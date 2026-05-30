<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\PostResource;
use App\Models\Evaluation;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Paginated list of posts (optional filters: state, user_id, search).
     */
    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-evaluation')) {
            return $error;
        }

        $query = Evaluation::query()
            ->with(['user', 'media']);


        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        $evaluations = $query
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['posts' => EvaluationResource::collection($evaluations)->resolve()],
                $this->paginationMeta($evaluations)
            ),
            'Evaluations retrieved successfully'
        );
    }

    /**
     * Single post detail.
     */
    public function show(Request $request, Evaluation $evaluation): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-evaluation')) {
            return $error;
        }

        $evaluation->load(['user', 'media']);

        return $this->successResponse(
            ['post' => new EvaluationResource($evaluation)],
            'Evaluation retrieved successfully'
        );
    }

    /**
     * Update post moderation state (published, draft, rejected).
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $validated = $request->validate([
            'pros_comment' => ['required', 'string'],
            'cons_comment' => ['required', 'string'],
        ]);

    }
}
