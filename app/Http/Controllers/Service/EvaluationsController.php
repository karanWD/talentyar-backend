<?php

namespace App\Http\Controllers\Service;

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
    public function index(Request $request): JsonResponse
    {
        $query = Evaluation::query()
            ->with(['user', 'media', 'items.item']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('analyzer_status')) {
            $query->where('analyzer_status', $request->string('analyzer_status')->toString());
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

    public function show(Evaluation $evaluation): JsonResponse
    {
        $evaluation->load(['user', 'media', 'items.item']);

        return $this->successResponse(
            ['evaluation' => new EvaluationResource($evaluation)],
            'Evaluation retrieved successfully'
        );
    }

    public function store(Request $request, Evaluation $evaluation): JsonResponse
    {
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

    public function analysisStatus(Evaluation $evaluation): JsonResponse
    {
        return $this->successResponse(
            [
                'evaluation_id' => $evaluation->id,
                'evaluation_status' => $evaluation->status,
                'analyzer' => [
                    'status' => $evaluation->analyzer_status ?? Evaluation::ANALYZER_STATUS_PENDING,
                    'attempts' => (int) $evaluation->analyzer_attempts,
                    'configured_max_tries' => (new \App\Jobs\AnalyzeEvaluationVideo($evaluation->id))->tries,
                    'failure_count' => (int) $evaluation->analyzer_failure_count,
                    'success_count' => (int) $evaluation->analyzer_success_count,
                    'last_error' => $evaluation->analyzer_last_error,
                    'last_attempt_at' => $evaluation->analyzer_last_attempt_at?->toIso8601String(),
                    'succeeded_at' => $evaluation->analyzer_succeeded_at?->toIso8601String(),
                ],
            ],
            'Evaluation analyzer status retrieved successfully'
        );
    }

    public function fail(Evaluation $evaluation)
    {
        $evaluation->update([
            'analyzer_status' => Evaluation::ANALYZER_STATUS_FAILED,
        ]);

        return $this->successResponse(
            [
                'evaluation_id' => $evaluation->id,
                'evaluation_status' => $evaluation->status,
                'analyzer' => [
                    'status' => $evaluation->analyzer_status ?? Evaluation::ANALYZER_STATUS_PENDING,
                    'attempts' => (int) $evaluation->analyzer_attempts,
                    'configured_max_tries' => (new \App\Jobs\AnalyzeEvaluationVideo($evaluation->id))->tries,
                    'failure_count' => (int) $evaluation->analyzer_failure_count,
                    'success_count' => (int) $evaluation->analyzer_success_count,
                    'last_error' => $evaluation->analyzer_last_error,
                    'last_attempt_at' => $evaluation->analyzer_last_attempt_at?->toIso8601String(),
                    'succeeded_at' => $evaluation->analyzer_succeeded_at?->toIso8601String(),
                ],
            ],
            'Evaluation analyzer status retrieved successfully'
        );
    }
}
