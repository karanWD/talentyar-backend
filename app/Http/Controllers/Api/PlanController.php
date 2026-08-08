<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends BaseApiController
{
    /**
     * List active subscription plans (optional filters: type, duration).
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'activity_field_id' => ['required', 'integer', 'exists:activity_fields,id'],
            'type' => ['nullable', 'string', Rule::in([
                Plan::TYPE_BASIC,
                Plan::TYPE_PRO,
                Plan::TYPE_VIP,
            ])],
            'duration' => ['nullable', 'integer', Rule::in([1, 6, 12])],
        ]);

        $query = Plan::query()
            ->where('activity_field_id', $request->integer('activity_field_id'))
            ->where('status', Plan::STATUS_ACTIVE)
            ->orderBy('type')
            ->orderBy('duration');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->integer('duration'));
        }

        $plans = $query->get();

        return $this->successResponse(
            ['plans' => PlanResource::collection($plans)->resolve()],
            'Plans retrieved successfully'
        );
    }
}
