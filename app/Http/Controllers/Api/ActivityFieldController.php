<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ActivityFieldResource;
use App\Models\ActivityField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityFieldController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $activityFields = ActivityField::query()
            ->orderBy('id')
            ->get();

        return $this->successResponse(
            ['activity_fields' => ActivityFieldResource::collection($activityFields)->resolve()],
            'Activity fields retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_field_ids' => ['required', 'array'],
            'activity_field_ids.*' => ['integer', 'distinct', 'exists:activity_fields,id'],
        ]);

        $user = $request->user();
        $user->activityFields()->sync($validated['activity_field_ids']);
        $user->load('activityFields');

        return $this->successResponse(
            ['activity_fields' => ActivityFieldResource::collection($user->activityFields)->resolve()],
            'User activity fields stored successfully'
        );
    }

    public function destroy(Request $request, ActivityField $activityField): JsonResponse
    {
        $user = $request->user();
        $user->activityFields()->detach($activityField->id);
        $user->load('activityFields');

        return $this->successResponse(
            ['activity_fields' => ActivityFieldResource::collection($user->activityFields)->resolve()],
            'User activity field deleted successfully'
        );
    }
}
