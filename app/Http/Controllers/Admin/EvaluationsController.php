<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        DB::transaction(function () use ($evaluation, $validated) {
            $items = collect($validated['items']);

            $evaluation->update([
                'pros_comment' => $validated['pros_comment'],
                'cons_comment' => $validated['cons_comment'],
                'suggestion' => $validated['suggestion'],
                'summary' => $validated['summary'],
                'score' => $validated['score'] ?? round($items->avg('score'), 2),
                'status' => $validated['status'] ?? Evaluation::status_approved,
            ]);

            $evaluation->items()->delete();

            $items->each(fn (array $item) => EvaluationItem::create([
                'evaluation_id' => $evaluation->id,
                'item_id' => $item['item_id'],
                'score' => $item['score'],
            ]));
        });

        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation stored successfully'
        );
    }
}
