<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\ChallengeParticipationResource;
use App\Http\Resources\ChallengeResource;
use App\Http\Resources\MediaResource;
use App\Models\Admin;
use App\Models\Challenge;
use App\Models\Media;
use App\Services\AdminActivityFieldService;
use App\Services\AdminPermissionService;
use App\Services\MediaService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChallengeController extends BaseApiController
{
    public function __construct(
        private readonly AdminPermissionService $permissions,
        private readonly AdminActivityFieldService $activityFields,
        private readonly MediaService $mediaService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-challenges')) {
            return $error;
        }

        $filters = $request->validate([
            'activity_field_id' => ['nullable', 'integer', 'exists:activity_fields,id'],
            'status' => ['nullable', 'string', Rule::in(Challenge::STATUSES)],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        /** @var Admin $admin */
        $admin = $request->user('api-admin');
        $query = $this->activityFields
            ->scopeChallenges(Challenge::query(), $admin)
            ->with(['activityField', 'trainingVideo'])
            ->withCount('participations');

        if (isset($filters['activity_field_id'])) {
            $query->where('activity_field_id', $filters['activity_field_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($nested) use ($search): void {
                $nested->where('title', 'like', '%'.$search.'%')
                    ->orWhere('prize', 'like', '%'.$search.'%');
            });
        }

        $challenges = $query
            ->latest()
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['challenges' => ChallengeResource::collection($challenges)->resolve()],
                $this->paginationMeta($challenges)
            ),
            'Challenges retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create-challenges')) {
            return $error;
        }

        $validated = $request->validate($this->rules(creating: true));

        if ($error = $this->authorizeActivityFieldOrFail($request, $validated['activity_field_id'])) {
            return $error;
        }

        $videoHash = Arr::pull($validated, 'training_video_hash');
        $validated['status'] ??= Challenge::STATUS_INACTIVE;

        $challenge = DB::transaction(function () use ($validated, $videoHash): Challenge {
            $challenge = new Challenge($validated);
            $challenge->synchronizeActivationWindow(
                restart: $challenge->status === Challenge::STATUS_ACTIVE
            );
            $challenge->save();

            if ($videoHash !== null) {
                $this->syncTrainingVideo($challenge, $videoHash);
            }

            return $challenge;
        });

        return $this->successResponse(
            ['challenge' => new ChallengeResource($this->loadChallenge($challenge))],
            'Challenge created successfully',
            201
        );
    }

    public function show(Request $request, Challenge $challenge): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-challenges', $challenge)) {
            return $error;
        }

        return $this->successResponse(
            ['challenge' => new ChallengeResource($this->loadChallenge($challenge))],
            'Challenge retrieved successfully'
        );
    }

    public function update(Request $request, Challenge $challenge): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-challenges', $challenge)) {
            return $error;
        }

        $validated = $request->validate($this->rules(creating: false, challenge: $challenge));

        if (isset($validated['activity_field_id'])
            && ($error = $this->authorizeActivityFieldOrFail($request, $validated['activity_field_id']))) {
            return $error;
        }

        $hasVideoUpdate = array_key_exists('training_video_hash', $validated);
        $videoHash = Arr::pull($validated, 'training_video_hash');

        DB::transaction(function () use ($challenge, $validated, $hasVideoUpdate, $videoHash): void {
            $wasExpired = $challenge->status === Challenge::STATUS_ACTIVE
                && $challenge->expires_at?->lte(now());
            $wasInactive = $challenge->status !== Challenge::STATUS_ACTIVE;

            $challenge->fill($validated);

            $explicitActivation = array_key_exists('status', $validated)
                && $validated['status'] === Challenge::STATUS_ACTIVE;
            $challenge->synchronizeActivationWindow(
                restart: $explicitActivation && ($wasInactive || $wasExpired)
            );
            $challenge->save();

            if ($hasVideoUpdate) {
                $this->syncTrainingVideo($challenge, $videoHash);
            }
        });

        return $this->successResponse(
            ['challenge' => new ChallengeResource($this->loadChallenge($challenge))],
            'Challenge updated successfully'
        );
    }

    public function destroy(Request $request, Challenge $challenge): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-challenges', $challenge)) {
            return $error;
        }

        DB::transaction(function () use ($challenge): void {
            $challenge->unAssignMedia(Challenge::TRAINING_VIDEO_COLLECTION);
            $challenge->delete();
        });

        return $this->successResponse(null, 'Challenge deleted successfully');
    }

    public function participants(Request $request, Challenge $challenge): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-challenges', $challenge)) {
            return $error;
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = $challenge->participations()
            ->with('user.media');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($users) use ($search): void {
                $users->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%');
            });
        }

        $participations = $query
            ->latest()
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                [
                    'challenge' => [
                        'id' => $challenge->id,
                        'title' => $challenge->title,
                    ],
                    'participants' => ChallengeParticipationResource::collection($participations)->resolve(),
                ],
                $this->paginationMeta($participations)
            ),
            'Challenge participants retrieved successfully'
        );
    }

    public function uploadVideo(Request $request): JsonResponse
    {
        if ($error = $this->authorizeUploadOrFail($request)) {
            return $error;
        }

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:'.UploadMediaRequest::VIDEO_MIMES,
                'max:'.(int) ceil(UploadMediaRequest::maxUploadBytes() / 1024),
            ],
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $media = $this->mediaService->addMediaFromUploadedFile(
            $request->file('file'),
            'challenge',
            Media::TYPE_FILE,
            Challenge::TRAINING_VIDEO_COLLECTION,
            's3',
            Arr::only($validated, ['alt'])
        );

        return $this->successResponse(
            [
                'media' => new MediaResource($media),
                'hash' => $media->hash,
            ],
            'Challenge video uploaded successfully',
            201
        );
    }

    private function authorizeOrFail(
        Request $request,
        string $permission,
        ?Challenge $challenge = null
    ): ?JsonResponse {
        $admin = $request->user('api-admin');

        if (! $admin instanceof Admin
            || ! $this->permissions->can($admin, $permission)
            || ($challenge && ! $this->activityFields->canAccessChallenge($admin, $challenge))) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    private function authorizeUploadOrFail(Request $request): ?JsonResponse
    {
        $admin = $request->user('api-admin');

        if (! $admin instanceof Admin
            || (! $this->permissions->can($admin, 'create-challenges')
                && ! $this->permissions->can($admin, 'update-challenges'))) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    private function authorizeActivityFieldOrFail(Request $request, int $activityFieldId): ?JsonResponse
    {
        $admin = $request->user('api-admin');

        if (! $admin instanceof Admin
            || ! $this->activityFields->canAccessActivityField($admin, $activityFieldId)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $creating, ?Challenge $challenge = null): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return [
            'activity_field_id' => [$required, 'integer', 'exists:activity_fields,id'],
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string'],
            'about' => [$required, 'string'],
            'conditions' => [$required, 'string'],
            'rules' => [$required, 'string'],
            'prize' => [$required, 'string', 'max:255'],
            'status' => [$creating ? 'nullable' : 'sometimes', 'string', Rule::in(Challenge::STATUSES)],
            'active_duration_days' => [$required, 'integer', 'min:1', 'max:3650'],
            'training_video_hash' => [
                $creating ? 'nullable' : 'sometimes',
                'nullable',
                'string',
                'size:64',
                function (string $attribute, mixed $value, Closure $fail) use ($challenge): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $media = $this->findAvailableTrainingVideo($value, $challenge);

                    if (! $media) {
                        $fail('The selected challenge video was not found or is already in use.');

                        return;
                    }

                    if (! str_starts_with($media->mime_type ?? '', 'video/')) {
                        $fail('The selected media is not a video.');
                    }
                },
            ],
        ];
    }

    private function syncTrainingVideo(Challenge $challenge, ?string $hash): void
    {
        $currentVideo = $challenge->trainingVideo()->first();

        if ($hash === null) {
            $challenge->unAssignMedia(Challenge::TRAINING_VIDEO_COLLECTION);

            return;
        }

        $media = $this->findAvailableTrainingVideo($hash, $challenge);

        if (! $media) {
            abort(422, 'The selected challenge video is no longer available.');
        }

        if ($currentVideo?->is($media)) {
            return;
        }

        $challenge->unAssignMedia(Challenge::TRAINING_VIDEO_COLLECTION);
        $challenge->assignMedia($media, Challenge::TRAINING_VIDEO_COLLECTION);
    }

    private function findAvailableTrainingVideo(string $hash, ?Challenge $challenge): ?Media
    {
        return Media::query()
            ->where('hash', $hash)
            ->where('entity_slug', 'challenge')
            ->where(function ($query) use ($challenge): void {
                $query->whereNull('entity_id');

                if ($challenge) {
                    $query->orWhere(function ($attached) use ($challenge): void {
                        $attached->where('entity_type', $challenge->getMorphClass())
                            ->where('entity_id', $challenge->getKey())
                            ->where('collection', Challenge::TRAINING_VIDEO_COLLECTION);
                    });
                }
            })
            ->first();
    }

    private function loadChallenge(Challenge $challenge): Challenge
    {
        return $challenge
            ->fresh(['activityField', 'trainingVideo'])
            ->loadCount('participations');
    }
}
