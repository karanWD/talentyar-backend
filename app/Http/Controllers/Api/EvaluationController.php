<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\MediaResource;
use App\Jobs\AnalyzeEvaluationVideo;
use App\Models\Evaluation;
use App\Models\Media;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BitpayPaymentService;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class EvaluationController extends BaseApiController
{
    private const JALALI_MONTHS = [
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];

    public function months(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_fields_id' => ['required', 'integer', 'exists:activity_fields,id'],
        ]);

        $now = Jalalian::now();
        $year = $request->integer('year', $now->getYear());

        if ($year < 1000 || $year > 3000) {
            return $this->errorResponse('Invalid Jalali year', 422);
        }

        $startDate = (new Jalalian($year, 1, 1))->toCarbon()->startOfDay();
        $endDate = (new Jalalian($year + 1, 1, 1))->toCarbon()->startOfDay();

        $activityFieldId = $validated['activity_fields_id'];
        $activeSubscription = $this->getActiveSubscription($request->user()->id, $activityFieldId);
        $subscriptionMonthState = $this->getSubscriptionMonthState($activeSubscription, $year);

        $evaluations = $request->user()
            ->evaluations()
            ->with(['media', 'items.item'])
            ->where('activity_fields_id', $activityFieldId)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->latest()
            ->get()
            ->groupBy(fn ($evaluation) => $this->monthKey(Jalalian::fromCarbon($evaluation->created_at)));

        $baseLastVisibleMonth = match (true) {
            $year < $now->getYear() => 12,
            $year === $now->getYear() => $now->getMonth(),
            default => 0,
        };
        $lastVisibleMonth = max($baseLastVisibleMonth, $subscriptionMonthState['last_visible_month']);

        $months = collect(self::JALALI_MONTHS)
            ->filter(fn (string $name, int $month) => $month <= $lastVisibleMonth)
            ->map(function (string $name, int $month) use ($evaluations, $subscriptionMonthState, $year, $activeSubscription) {
                $monthKey = $this->monthKey(new Jalalian($year, $month, 1));
                $hasEvaluation = $evaluations->has($monthKey);

                return [
                    'month' => $month,
                    'name' => $name,
                    'has_evaluation' => $hasEvaluation,
                    'can_upload' => ($subscriptionMonthState['can_upload'][$monthKey] ?? false)
                        && ! $hasEvaluation,
                    'uploaded_before' => ($subscriptionMonthState['uploaded_before'][$monthKey] ?? false)
                        && ! $hasEvaluation,
                    'is_expiration_month' => ($subscriptionMonthState['expiration'][$monthKey] ?? false),
                    're_evaluation_quote' => $activeSubscription->re_evaluation_credits ?? 0,
                    'evaluation' => $hasEvaluation
                        ? new EvaluationResource($evaluations->get($monthKey)->first())
                        : null,
                    'evaluations' => $hasEvaluation
                        ? EvaluationResource::collection($evaluations->get($monthKey))->resolve()
                        : [],
                ];
            })
            ->values();

        return $this->successResponse([
            'year' => $year,
            'months' => $months,
        ], 'Evaluation months retrieved successfully');
    }

    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $mediaService = new MediaService;

        $media = $mediaService->addMediaFromUploadedFile(
            $file,
            'user',
            $request->validated('type', Media::TYPE_EVALUATION),
            'default',
            's3',
            $request->only(['alt'])
        );

        return $this->successResponse(
            [
                'media' => new MediaResource($media),
                'hash' => $media->hash,
            ],
            'Media uploaded successfully',
            201
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'video_hash' => ['required', 'string', 'exists:media,hash'],
            'activity_fields_id' => ['required', 'integer', 'exists:activity_fields,id'],
        ]);

        return DB::transaction(function () use ($request, $validated): JsonResponse {

            $user = $request->user();
            $uploadAvailability = $this->getUploadAvailability(
                $user->id,
                $validated['activity_fields_id']
            );

            if (! $uploadAvailability['can_upload']) {
                return $this->errorResponse($uploadAvailability['message'], 422);
            }

            $evaluation = Evaluation::create([
                'user_id' => $user->id,
                'activity_fields_id' => $validated['activity_fields_id'],
                'analyzer_status' => Evaluation::ANALYZER_STATUS_PENDING,
            ]);

            $videoMedia = Media::where('hash', $validated['video_hash'])->firstOrFail();
            $evaluation->assignMedia($videoMedia, 'video');

            $post = Post::create([
                'user_id' => $user->id,
                'activity_fields_id' => $validated['activity_fields_id'],
                'evaluation_id' => $evaluation->id,
                'caption' => ' ',
                'state' => Post::STATE_DRAFT,
            ]);

            $postMedia = $videoMedia->replicate([
                'entity_id',
                'entity_type',
            ]);

            $postMedia->entity_id = $post->id;
            $postMedia->entity_type = get_class($post);
            $postMedia->save();

            if ($uploadAvailability['uses_re_evaluation_credit']) {
                $uploadAvailability['subscription']->decrement('re_evaluation_credits');
            }

            AnalyzeEvaluationVideo::dispatch($evaluation->id)->afterCommit();

            $evaluation->load('media');

            return $this->successResponse(
                ['post' => new EvaluationResource($evaluation)],
                'Evaluation created successfully',
                201
            );
        });
    }

    public function purchaseReEvaluation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_fields_id' => ['required', 'integer', 'exists:activity_fields,id'],
        ]);

        $user = $request->user();
        $subscription = $this->getActiveSubscription($user->id, $validated['activity_fields_id']);

        if ($subscription === null) {
            return $this->errorResponse('User does not have an active subscription', 422);
        }

//        if ($this->getSubscriptionEvaluationCount($subscription) === 0) {
//            return $this->errorResponse('User has no evaluation to re-evaluate', 422);
//        }

        $amount = (int) $subscription->plan?->re_evaluation_price;

        if ($amount <= 0) {
            return $this->errorResponse('Re-evaluation price is not configured', 500);
        }

        $wallet = Wallet::query()->firstOrCreate([
            'entity_type' => $user->getMorphClass(),
            'entity_id' => $user->id,
        ], [
            'balance' => 0,
            'currency' => 'IRR',
        ]);

        $transaction = Transaction::query()->create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'payment' => Transaction::PAYMENT_RE_EVALUATION,
            'type' => Transaction::TYPE_IN,
            'status' => Transaction::STATUS_PENDING,
            'amount' => $amount,
            'currency' => $wallet->currency ?? 'IRR',
            'description' => 'Re-evaluation purchase',
            'subscription_id' => $subscription->id,
        ]);

        $payment = (new BitpayPaymentService)->requestPayment(
            $amount,
            'https://api.talentyar.com/api/v1/callback',
            $transaction->id,
            $user->phone,
            $user->username,
            'Re-evaluation purchase'
        );

        $transaction->update([
            'meta_data' => [
                'id_get' => $payment['id_get'] ?? null,
                'raw' => $payment['raw'] ?? null,
            ],
        ]);

        $url = $payment['gateway_url'] ?? null;

        if ($url === null) {
            return $this->errorResponse('payment gateway down!', 400);
        }

        return $this->successResponse([
            'url' => $url,
            'amount' => $amount,
            'currency' => 'TOMAN',
        ], 'go to payment gate');
    }

    public function reEvaluationPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_fields_id' => ['required', 'integer', 'exists:activity_fields,id'],
        ]);

        $subscription = $this->getActiveSubscription(
            $request->user()->id,
            $validated['activity_fields_id']
        );

        if ($subscription === null) {
            return $this->errorResponse('User does not have an active subscription', 422);
        }

        $price = (int) $subscription->plan?->re_evaluation_price;

        if ($price <= 0) {
            return $this->errorResponse('Re-evaluation price is not configured', 500);
        }

        return $this->successResponse([
            'price' => $price,
            'currency' => 'TOMAN',
        ], 'Re-evaluation price retrieved successfully');
    }

    /**
     * @return array{
     *     can_upload: array<string, bool>,
     *     uploaded_before: array<string, bool>,
     *     expiration: array<string, bool>,
     *     last_visible_month: int
     * }
     */
    private function getSubscriptionMonthState(?Subscription $subscription, int $year): array
    {
        if ($subscription === null) {
            return [
                'can_upload' => [],
                'uploaded_before' => [],
                'expiration' => [],
                'last_visible_month' => 0,
            ];
        }

        $coveredMonthKeys = $this->getCoveredMonthKeysForSubscription($subscription);
        $uploadedBefore = [];
        $canUpload = [];
        $expiration = [];
        $lastVisibleMonth = 0;
        $subscriptionEvaluation = $this->getSubscriptionEvaluation($subscription);
        $evaluationMonthKey = $subscriptionEvaluation?->created_at
            ? $this->monthKey(Jalalian::fromCarbon($subscriptionEvaluation->created_at))
            : null;
        $currentMonthKey = $this->monthKey(Jalalian::now());

        foreach ($coveredMonthKeys as $monthKey) {
            if ($this->monthKeyYear($monthKey) !== $year) {
                continue;
            }

            $monthNumber = $this->monthKeyMonth($monthKey);
            $lastVisibleMonth = max($lastVisibleMonth, $monthNumber);

            if ($monthKey < $currentMonthKey) {
                continue;
            }

            if ($evaluationMonthKey === null) {
                $canUpload[$monthKey] = true;

                continue;
            }

            if ($monthKey > $evaluationMonthKey) {
                $uploadedBefore[$monthKey] = true;
            }
        }

        $expirationMonthKey = $this->getExpirationMonthKey($subscription);
        if ($expirationMonthKey !== null && $this->monthKeyYear($expirationMonthKey) === $year) {
            $expiration[$expirationMonthKey] = true;
            $lastVisibleMonth = max($lastVisibleMonth, $this->monthKeyMonth($expirationMonthKey));
        }

        return [
            'can_upload' => $canUpload,
            'uploaded_before' => $uploadedBefore,
            'expiration' => $expiration,
            'last_visible_month' => $lastVisibleMonth,
        ];
    }

    private function getActiveSubscription(int $userId, int $activityFieldId): ?Subscription
    {
        return Subscription::query()
            ->with('plan')
            ->where('user_id', $userId)
            ->where('activity_field_id', $activityFieldId)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereNotNull('started_at')
            ->whereNotNull('expires_at')
            ->where('started_at', '<=', now())
            ->where('expires_at', '>', now())
            ->latest('started_at')
            ->first();
    }

    /**
     * @return array<int, string>
     */
    private function getCoveredMonthKeysForSubscription(Subscription $subscription): array
    {
        if ($subscription->started_at === null || $subscription->expires_at === null) {
            return [];
        }

        $rangeStart = $subscription->started_at->greaterThan(now())
            ? $subscription->started_at->copy()
            : now()->copy();
        $rangeEnd = $subscription->expires_at->copy()->subSecond();

        if ($rangeEnd->lessThan($rangeStart)) {
            return [];
        }

        $startMonth = Jalalian::fromCarbon($rangeStart);
        $endMonth = Jalalian::fromCarbon($rangeEnd);
        $keys = [];
        $currentMonth = $startMonth;

        while (true) {
            $keys[] = $this->monthKey($currentMonth);

            if ($this->monthKey($currentMonth) === $this->monthKey($endMonth)) {
                break;
            }

            $currentMonth = $currentMonth->addMonths(1);
        }

        return $keys;
    }

    /**
     * @return array{
     *     can_upload: bool,
     *     message: string,
     *     subscription?: Subscription,
     *     uses_re_evaluation_credit?: bool
     * }
     */
    private function getUploadAvailability(int $userId, int $activityFieldId): array
    {
        $subscription = $this->getActiveSubscription($userId, $activityFieldId);

        if ($subscription === null) {
            return [
                'can_upload' => false,
                'message' => 'User does not have an active subscription',
            ];
        }

        $subscription = Subscription::query()->lockForUpdate()->findOrFail($subscription->id);

        $hasEvaluation = $this->getSubscriptionEvaluation($subscription) !== null;

        if ($hasEvaluation && $subscription->re_evaluation_credits < 1) {
            return [
                'can_upload' => false,
                'message' => 'Evaluation already uploaded for this subscription',
            ];
        }

        return [
            'can_upload' => true,
            'message' => 'Evaluation upload is available',
            'subscription' => $subscription,
            'uses_re_evaluation_credit' => $hasEvaluation,
        ];
    }

    private function getSubscriptionEvaluation(Subscription $subscription): ?Evaluation
    {
        return Evaluation::query()
            ->where('user_id', $subscription->user_id)
            ->where('activity_fields_id', $subscription->activity_field_id)
            ->where('created_at', '>=', $subscription->started_at)
            ->where('created_at', '<', $subscription->expires_at)
            ->oldest('created_at')
            ->first();
    }

    private function getSubscriptionEvaluationCount(Subscription $subscription): int
    {
        return Evaluation::query()
            ->where('user_id', $subscription->user_id)
            ->where('activity_fields_id', $subscription->activity_field_id)
            ->where('created_at', '>=', $subscription->started_at)
            ->where('created_at', '<', $subscription->expires_at)
            ->count();
    }

    private function getExpirationMonthKey(Subscription $subscription): ?string
    {
        if ($subscription->expires_at === null) {
            return null;
        }

        $expirationBoundary = $subscription->expires_at->copy()->subSecond();

        if ($subscription->started_at !== null && $expirationBoundary->lessThan($subscription->started_at)) {
            $expirationBoundary = $subscription->started_at->copy();
        }

        return $this->monthKey(Jalalian::fromCarbon($expirationBoundary));
    }

    private function monthKey(Jalalian $date): string
    {
        return sprintf('%d-%02d', $date->getYear(), $date->getMonth());
    }

    private function monthKeyYear(string $monthKey): int
    {
        return (int) explode('-', $monthKey, 2)[0];
    }

    private function monthKeyMonth(string $monthKey): int
    {
        return (int) explode('-', $monthKey, 2)[1];
    }
}
