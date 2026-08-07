<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserActivityFieldSkillRequest;
use App\Http\Requests\UpdateUserActivityFieldSkillRequest;
use App\Http\Resources\UserActivityFieldSkillResource;
use App\Http\Resources\UserResource;
use App\Models\ActivityField;
use App\Models\User;
use App\Models\UserActivityFieldSkill;
use Illuminate\Http\JsonResponse;

class UserActivityFieldSkillController extends BaseApiController
{
    public function store(StoreUserActivityFieldSkillRequest $request, ActivityField $activityField): JsonResponse
    {
        $user = $request->user();

        if ($response = $this->validateActivityFieldAccess($user, $activityField)) {
            return $response;
        }

        $existingSkill = $this->findUserSkill($user, $activityField);

        if ($existingSkill) {
            return $this->errorResponse('Skills for this activity field already exist for the user', 422);
        }

        $skill = UserActivityFieldSkill::create([
            'user_id' => $user->id,
            'activity_field_id' => $activityField->id,
            'answers' => $request->validatedAnswers(),
        ]);

        $skill->load('activityField');
        $this->loadUserProfileRelations($user);

        return $this->successResponse(
            [
                'skill' => new UserActivityFieldSkillResource($skill),
                'user' => new UserResource($user),
            ],
            'Activity field skills stored successfully',
            201
        );
    }

    public function update(UpdateUserActivityFieldSkillRequest $request, ActivityField $activityField): JsonResponse
    {
        $user = $request->user();

        if ($response = $this->validateActivityFieldAccess($user, $activityField)) {
            return $response;
        }

        $skill = $this->findUserSkill($user, $activityField);

        if (! $skill) {
            return $this->errorResponse('Skills for this activity field were not found for the user', 404);
        }

        $skill->update([
            'answers' => array_replace($skill->answers ?? [], $request->validatedAnswers()),
        ]);

        $skill->load('activityField');
        $this->loadUserProfileRelations($user);

        return $this->successResponse(
            [
                'skill' => new UserActivityFieldSkillResource($skill),
                'user' => new UserResource($user),
            ],
            'Activity field skills updated successfully'
        );
    }

    public function destroy(ActivityField $activityField, \Illuminate\Http\Request $request): JsonResponse
    {
        $user = $request->user();

        if ($response = $this->validateActivityFieldAccess($user, $activityField)) {
            return $response;
        }

        $skill = $this->findUserSkill($user, $activityField);

        if (! $skill) {
            return $this->errorResponse('Skills for this activity field were not found for the user', 404);
        }

        $skill->delete();
        $this->loadUserProfileRelations($user);

        return $this->successResponse(
            [
                'activity_field_id' => $activityField->id,
                'user' => new UserResource($user),
            ],
            'Activity field skills deleted successfully'
        );
    }

    protected function validateActivityFieldAccess(User $user, ActivityField $activityField): ?JsonResponse
    {
        if ($activityField->specialtyQuestions() === []) {
            return $this->errorResponse('No specialty config found for this activity field', 422);
        }

        $userHasActivityField = $user->activityFields()
            ->whereKey($activityField->id)
            ->exists();

        if (! $userHasActivityField) {
            return $this->errorResponse('The user does not have access to this activity field', 422);
        }

        return null;
    }

    protected function findUserSkill(User $user, ActivityField $activityField): ?UserActivityFieldSkill
    {
        return $user->activityFieldSkills()
            ->where('activity_field_id', $activityField->id)
            ->first();
    }

    protected function loadUserProfileRelations(User $user): void
    {
        $user->load(['media', 'activityFields', 'activityFieldSkills']);
    }
}
