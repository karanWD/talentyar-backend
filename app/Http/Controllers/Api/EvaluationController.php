<?php

namespace App\Http\Controllers\Api;

use App\Jobs\AnalyzeEvaluationVideo;
use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\MediaResource;
use App\Models\Evaluation;
use App\Models\Media;
use App\Models\Post;
use App\Models\Subscription;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $now = Jalalian::now();
        $year = $request->integer('year', $now->getYear());

        if ($year < 1000 || $year > 3000) {
            return $this->errorResponse('Invalid Jalali year', 422);
        }

        $startDate = (new Jalalian($year, 1, 1))->toCarbon()->startOfDay();
        $endDate = (new Jalalian($year + 1, 1, 1))->toCarbon()->startOfDay();

        $activeSubscription = $this->getActiveSubscription($request->user()->id);
        $subscriptionMonthState = $this->getSubscriptionMonthState($activeSubscription, $year);


        $evaluations = $request->user()
            ->evaluations()
            ->with(['media', 'items.item'])
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
            ->map(function (string $name, int $month) use ($evaluations, $subscriptionMonthState, $year) {
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
                    'evaluation' => $hasEvaluation
                        ? new EvaluationResource($evaluations->get($monthKey)->first())
                        : null,
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
        $mediaService = new MediaService();

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
        ]);

        $user = $request->user();
        $uploadAvailability = $this->getUploadAvailability($user->id);

        if (! $uploadAvailability['can_upload']) {
            return $this->errorResponse($uploadAvailability['message'], 422);
        }

        $evaluation = Evaluation::create([
            'user_id' => $user->id,
            'analyzer_status' => Evaluation::ANALYZER_STATUS_PENDING,
        ]);

        $videoMedia = Media::where('hash', $validated['video_hash'])->firstOrFail();
        $evaluation->assignMedia($videoMedia, 'video');


        $post = Post::create([
            'user_id' => $user->id,
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

        AnalyzeEvaluationVideo::dispatch($evaluation->id)->afterCommit();

        $evaluation->load('media');

        return $this->successResponse(
            ['post' => new EvaluationResource($evaluation)],
            'Evaluation created successfully',
            201
        );
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

    private function getActiveSubscription(int $userId): ?Subscription
    {
        return Subscription::query()
            ->with('plan')
            ->where('user_id', $userId)
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
     * @return array{can_upload: bool, message: string}
     */
    private function getUploadAvailability(int $userId): array
    {
        $subscription = $this->getActiveSubscription($userId);

        if ($subscription === null) {
            return [
                'can_upload' => false,
                'message' => 'User does not have an active subscription',
            ];
        }

        if ($this->getSubscriptionEvaluation($subscription) !== null) {
            return [
                'can_upload' => false,
                'message' => 'Evaluation already uploaded for this subscription',
            ];
        }

        return [
            'can_upload' => true,
            'message' => 'Evaluation upload is available',
        ];
    }

    private function getSubscriptionEvaluation(Subscription $subscription): ?Evaluation
    {
        return Evaluation::query()
            ->where('user_id', $subscription->user_id)
            ->where('created_at', '>=', $subscription->started_at)
            ->where('created_at', '<', $subscription->expires_at)
            ->oldest('created_at')
            ->first();
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
