<?php

namespace App\Http\Controllers\Api;

use App\Enums\ChallengeActionType;
use App\Enums\ChallengeType;
use App\Http\Requests\UploadChallengeMediaRequest;
use App\Http\Resources\ChallengeParticipationResource;
use App\Http\Resources\ChallengeResource;
use App\Http\Resources\MediaResource;
use App\Models\Challenge;
use App\Models\ChallengeParticipation;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaService;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ChallengeController extends BaseApiController
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'activity_field_id' => ['nullable', 'integer', 'exists:activity_fields,id'],
            'type' => ['nullable', Rule::enum(ChallengeType::class)],
            'action_type' => ['nullable', Rule::enum(ChallengeActionType::class)],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $query = Challenge::query()
            ->currentlyActive()
            ->with(['activityField', 'trainingVideo'])
            ->withCount('participations')
            ->withExists([
                'participations as has_joined' => fn ($participations) => $participations
                    ->where('user_id', $user->id),
            ]);

        if (isset($filters['activity_field_id'])) {
            $query->where('activity_field_id', $filters['activity_field_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['action_type'])) {
            $query->where('action_type', $filters['action_type']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($nested) use ($search): void {
                $nested->where('title', 'like', '%'.$search.'%')
                    ->orWhere('prize', 'like', '%'.$search.'%');
            });
        }

        $challenges = $query
            ->latest('activated_at')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        $hasActiveSubscription = $user->activeSubscriptions()->exists();
        $challenges->getCollection()->each(
            fn (Challenge $challenge) => $challenge->setAttribute(
                'can_join',
                $hasActiveSubscription && ! $challenge->has_joined
            )
        );

        return $this->successResponse(
            array_merge(
                ['challenges' => ChallengeResource::collection($challenges)->resolve()],
                $this->paginationMeta($challenges)
            ),
            'Challenges retrieved successfully'
        );
    }

    public function show(Request $request, Challenge $challenge): JsonResponse
    {
        if (! $challenge->isCurrentlyActive()) {
            return $this->errorResponse('Challenge not found', 404);
        }

        /** @var User $user */
        $user = $request->user();
        $challenge->load(['activityField', 'trainingVideo'])
            ->loadCount('participations')
            ->loadExists([
                'participations as has_joined' => fn ($participations) => $participations
                    ->where('user_id', $user->id),
            ]);
        $challenge->setAttribute(
            'can_join',
            $user->activeSubscriptions()->exists() && ! $challenge->has_joined
        );

        return $this->successResponse(
            ['challenge' => new ChallengeResource($challenge)],
            'Challenge retrieved successfully'
        );
    }

    public function uploadParticipationMedia(UploadChallengeMediaRequest $request): JsonResponse
    {
        $media = $this->mediaService->addMediaFromUploadedFile(
            $request->file('file'),
            ChallengeParticipation::MEDIA_ENTITY_SLUG,
            Media::TYPE_FILE,
            ChallengeParticipation::MEDIA_COLLECTION,
            's3'
        );
        $media->alt = $request->validated('alt');
        $media->meta_data = [
            'uploaded_by_user_id' => $request->user()->id,
        ];
        $media->save();

        return $this->successResponse(
            [
                'media' => new MediaResource($media),
                'hash' => $media->hash,
            ],
            'Challenge participation media uploaded successfully',
            201
        );
    }

    public function participate(Request $request, Challenge $challenge): JsonResponse
    {
        if (! $challenge->isCurrentlyActive()) {
            return $this->errorResponse('Challenge is not active', 422);
        }

        /** @var User $user */
        $user = $request->user();

        if (! $user->activeSubscriptions()->exists()) {
            return $this->errorResponse('An active subscription is required to participate', 403);
        }

        if ($challenge->participations()->where('user_id', $user->id)->exists()) {
            return $this->errorResponse('User has already participated in this challenge', 409);
        }

        $validated = $challenge->isScoreType()
            ? $request->validate([
                'media_hash' => [
                    'bail',
                    'required',
                    'string',
                    'size:64',
                    function (string $attribute, mixed $value, Closure $fail) use ($user): void {
                        $media = $this->findAvailableParticipationMedia($value, $user->id);

                        if (! $media) {
                            $fail('The selected challenge media was not found or is already in use.');

                            return;
                        }

                        if (! str_starts_with($media->mime_type ?? '', 'image/')
                            && ! str_starts_with($media->mime_type ?? '', 'video/')) {
                            $fail('The selected media must be an image or video.');
                        }
                    },
                ],
            ])
            : $request->validate([
                'media_hash' => ['prohibited'],
            ]);

        try {
            $participation = DB::transaction(function () use ($challenge, $user, $validated): ChallengeParticipation {
                $media = null;

                if ($challenge->isScoreType()) {
                    $media = $this->findAvailableParticipationMedia(
                        $validated['media_hash'],
                        $user->id,
                        lockForUpdate: true
                    );

                    if (! $media) {
                        throw ValidationException::withMessages([
                            'media_hash' => ['The selected challenge media is no longer available.'],
                        ]);
                    }
                }

                $participation = $challenge->participations()->create([
                    'user_id' => $user->id,
                ]);

                if ($media) {
                    $participation->assignMedia($media, ChallengeParticipation::MEDIA_COLLECTION);
                }

                return $participation;
            });
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                return $this->errorResponse('User has already participated in this challenge', 409);
            }

            throw $exception;
        }

        $participation->load(['user.media', 'submission']);

        return $this->successResponse(
            ['participation' => new ChallengeParticipationResource($participation)],
            'Challenge participation registered successfully',
            201
        );
    }

    private function findAvailableParticipationMedia(
        string $hash,
        int $userId,
        bool $lockForUpdate = false
    ): ?Media {
        $query = Media::query()
            ->where('hash', $hash)
            ->where('entity_slug', ChallengeParticipation::MEDIA_ENTITY_SLUG)
            ->where('collection', ChallengeParticipation::MEDIA_COLLECTION)
            ->where('meta_data->uploaded_by_user_id', $userId)
            ->whereNull('entity_id')
            ->whereNull('entity_type');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array($exception->getCode(), ['23000', '23505'], true)
            || str_contains(strtolower($exception->getMessage()), 'unique constraint');
    }
}
