<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\EvaluationResource;
use App\Jobs\AnalyzeEvaluationVideo;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\Media;
use App\Models\Post;
use App\Notifications\InternalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class EvaluationsController extends BaseApiController
{
    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin || ! $admin->can($permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    /**
     * Paginated list of evaluations.
     */
    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-evaluation')) {
            return $error;
        }

        $query = Evaluation::query()
            ->with('user')
            ->when($request->has('status'),function ($q) use ($request){
                $q->where('status',$request->get('status'));
            })
            ->with(['user', 'media', 'items.item']);


        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        $evaluations = $query
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['evaluations' => EvaluationResource::collection($evaluations)->resolve()],
                $this->paginationMeta($evaluations)
            ),
            'Evaluations retrieved successfully'
        );
    }

    /**
     * Single evaluation detail.
     */
    public function show(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-evaluation')) {
            return $error;
        }

        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation retrieved successfully'
        );
    }

    /**
     * Store admin evaluation comments and item scores.
     */
    public function store(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $validated = $request->validate([
            'pros_comment' => ['required', 'string'],
            'cons_comment' => ['required', 'string'],
            'suggestion' => ['required', 'string'],
            'summary' => ['required', 'string'],
            'score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(Evaluation::STATUSES)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'items.*.score' => ['required', 'integer', 'min:0'],
        ]);

        $newStatus = $validated['status'] ?? Evaluation::status_approved;
        $shouldNotify = $evaluation->status !== Evaluation::status_approved
            && $newStatus === Evaluation::status_approved;

        DB::transaction(function () use ($evaluation, $validated, $newStatus) {
            $items = collect($validated['items']);

            $evaluation->update([
                'pros_comment' => $validated['pros_comment'],
                'cons_comment' => $validated['cons_comment'],
                'suggestion' => $validated['suggestion'],
                'summary' => $validated['summary'],
                'score' => $validated['score'] ?? round($items->avg('score'), 2),
                'status' => $newStatus,
            ]);

            $evaluation->items()->delete();

            $items->each(fn (array $item) => EvaluationItem::create([
                'evaluation_id' => $evaluation->id,
                'item_id' => $item['item_id'],
                'score' => $item['score'],
            ]));
        });

        if ($shouldNotify) {
            $evaluation->user->notify(InternalNotification::evaluationApproved($evaluation));
        }

        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation stored successfully'
        );
    }

    /**
     * Update evaluation moderation status only.
     */
    public function updateStatus(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(Evaluation::STATUSES)],
        ]);

        $shouldNotifyEvaluation = $evaluation->status !== Evaluation::status_approved
            && $validated['status'] === Evaluation::status_approved;
        $publishedPostIds = collect();

        DB::transaction(function () use ($evaluation, $validated, &$publishedPostIds): void {
            $evaluation->update([
                'status' => $validated['status'],
            ]);

            if ($validated['status'] === Evaluation::status_approved) {
                $publishedPostIds = $evaluation->posts()
                    ->where('state', '!=', Post::STATE_PUBLISHED)
                    ->pluck('id');

                $evaluation->posts()->update([
                    'state' => Post::STATE_PUBLISHED,
                ]);
            }
        });

        if ($shouldNotifyEvaluation) {
            $evaluation->user->notify(InternalNotification::evaluationApproved($evaluation));
        }

        Post::query()
            ->whereIn('id', $publishedPostIds)
            ->get()
            ->each(fn (Post $post) => $post->user->notify(InternalNotification::postApproved($post)));

        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation status updated successfully'
        );
    }

    /**
     * Update evaluation summary fields and optionally replace item scores.
     */
    public function updateDetails(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $validated = $request->validate([
            'score' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'pros_comment' => ['sometimes', 'nullable', 'string'],
            'cons_comment' => ['sometimes', 'nullable', 'string'],
            'suggestion' => ['sometimes', 'nullable', 'string'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.item_id' => ['required_with:items', 'integer', 'distinct', 'exists:items,id'],
            'items.*.score' => ['required_with:items', 'integer', 'min:0'],
        ]);

        if ($validated === []) {
            return $this->errorResponse(
                'At least one of score, pros_comment, cons_comment, suggestion, summary, or items is required',
                422
            );
        }

        DB::transaction(function () use ($evaluation, $validated): void {
            $payload = collect($validated)->except(['items'])->all();

            if (array_key_exists('items', $validated)) {
                $items = collect($validated['items']);

                if (! array_key_exists('score', $payload)) {
                    $payload['score'] = round($items->avg('score'), 2);
                }

                $evaluation->items()->delete();

                $items->each(fn (array $item) => EvaluationItem::create([
                    'evaluation_id' => $evaluation->id,
                    'item_id' => $item['item_id'],
                    'score' => $item['score'],
                ]));
            }

            $evaluation->update($payload);
        });

        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation details updated successfully'
        );
    }

    public function retry(Request $request, Evaluation $evaluation)
    {
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $evaluation->update([
           'status' => Evaluation::status_pending,
        ]);

        AnalyzeEvaluationVideo::dispatch($evaluation->id)->afterCommit();

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation JObed successfully'
        );

    }

    /**
     * Delete an evaluation, its posts, and their stored media.
     */
    public function destroy(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-evaluation')) {
            return $error;
        }

        $evaluation->load(['media', 'posts.media']);

        $media = $evaluation->media
            ->concat($evaluation->posts->flatMap->media)
            ->unique('id')
            ->values();
        $mediaIds = $media->pluck('id');

        DB::transaction(function () use ($evaluation, $media, $mediaIds): void {
            foreach ($media->groupBy(fn (Media $item): string => ($item->disk ?: 's3').'|'.$item->path) as $fileMedia) {
                /** @var Media $file */
                $file = $fileMedia->first();
                $isUsedElsewhere = Media::query()
                    ->whereNotIn('id', $mediaIds)
                    ->where('disk', $file->disk)
                    ->where('path', $file->path)
                    ->exists();

                if (! $isUsedElsewhere) {
                    $disk = $file->disk ?: 's3';

                    if (! Storage::disk($disk)->delete($file->path)) {
                        throw new RuntimeException("Unable to delete evaluation media from disk [{$disk}].");
                    }
                }
            }

            foreach ($media as $item) {
                $item->forceDelete();
            }

            $evaluation->posts()->delete();
            $evaluation->items()->delete();
            $evaluation->delete();
        });

        return $this->successResponse(
            null,
            'Evaluation deleted successfully'
        );
    }
}
