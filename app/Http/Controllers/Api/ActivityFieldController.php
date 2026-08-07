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
        $activityFieldIds = $validated['activity_field_ids'];

        $user->activityFields()->sync($activityFieldIds);

        if ($activityFieldIds === []) {
            $user->activityFieldSkills()->delete();
        } else {
            $user->activityFieldSkills()
                ->whereNotIn('activity_field_id', $activityFieldIds)
                ->delete();
        }

        $user->load(['activityFields', 'activityFieldSkills']);

        return $this->successResponse(
            ['activity_fields' => $this->serializeUserActivityFields($user, $request)],
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

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function serializeUserActivityFields($user, Request $request): array
    {
        $skillsByFieldId = $user->activityFieldSkills->keyBy('activity_field_id');

        return $user->activityFields->map(function (ActivityField $activityField) use ($request, $skillsByFieldId) {
            $data = (new ActivityFieldResource($activityField))->toArray($request);
            $data['skill_answers'] = $skillsByFieldId->get($activityField->id)?->answers;

            return $data;
        })->values()->all();
    }
}
