<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MinimalMediaResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UserResource;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends BaseApiController
{

    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::find($request->plan_id);
        if (!$plan) {
            return $this->errorResponse('Plan not found', 404);
        }

        $user = $request->user();
        if ($user->activeSubscription()->exists()) {
            return $this->errorResponse('User already has an active subscription', 400);
        }


        // todo:: add active for test
        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'price_paid' => $plan->price ?? 0,
            'discount_price' => $plan->discount_price ?? 0,
            'started_at' => now(),
            'expires_at' => now()->addDays($plan->duration),
        ]);

        return $this->successResponse([
            'subscription' => new SubscriptionResource($subscription),
        ], 'Subscription purchased successfully', 201);
    }

    public function getActiveSubscription(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->activeSubscription()->first();
        if (!$subscription) {
            return $this->errorResponse('User does not have an active subscription', 404);
        }
        return $this->successResponse([
            'subscription' => new SubscriptionResource($subscription),
        ], 'Active subscription retrieved successfully');
    }



}
