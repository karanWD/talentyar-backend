<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MinimalMediaResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UserResource;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\BitpayPaymentService;
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
            //todo:: handle exteded plan
            return $this->errorResponse('User already has an active subscription', 400);
        }

        $response = null;
        $amount = (! is_null($plan->discount_price) && (float) $plan->discount_price !== 0.0)
            ? $plan->discount_price
            : ($plan->price ?? 0);

        if($amount == 0) {
            return $this->errorResponse('amount is 0', 400);
        }

        $wallet = Wallet::query()->firstOrCreate([
            'entity_type' => $user->getMorphClass(),
            'entity_id' => $user->id,
        ], [
            'balance' => 0,
            'currency' => 'IRR',
        ]);

        $transaction  = Transaction::query()
            ->create([
                'wallet_id' => $wallet->id,
                'payment' => Transaction::PAYMENT_SUBSCRIPTION,
                'type' => Transaction::TYPE_IN,
                'status' => Transaction::STATUS_PENDING,
                'amount' => $amount,
                'currency' => $wallet->currency ?? 'IRR',
                'description' => 'Subscription purchase for plan: ' . $plan->name,
                'plan_id' => $plan->id,
            ]);

        $payment = new BitpayPaymentService();
        $response = $payment->requestPayment($amount, 'https://api.talentyar.com/api/v1/callback' , $transaction->id,$user->phone,$user->username,'description');
        $url = $response['gateway_url'] ?? null;

        $transaction->update([
           'metadata' => json_encode([
               'id_get' => $response['id_get'] ?? null,
               'raw' => $response['raw'] ?? null,

           ])
        ]);


        if (is_null($url)){
            return $this->errorResponse('payment gateway down!', 400);
        }

        return $this->successResponse([
            'url' => $url,
        ], 'go to payment gate', 200);

        // todo:: add active for test
//        $subscription = $user->subscriptions()->create([
//            'plan_id' => $plan->id,
//            'status' => Subscription::STATUS_ACTIVE,
//            'price_paid' => $amount,
//            'discount_price' => $plan->discount_price ?? 0,
//            'started_at' => now(),
//            'expires_at' => now()->addDays($plan->duration),
//        ]);
//
//        return $this->successResponse([
//            'subscription' => new SubscriptionResource($subscription),
//        ], 'Subscription purchased successfully', 201);
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

    public function callback(Request $request)
    {
        $transaction = Transaction::query()
            ->where('id', $request->get('factorId'))
            ->first();
        if (!$transaction) {
            return $this->errorResponse('Transaction not found', 404);
        }

        $response = null;
        $payment = new BitpayPaymentService();
        $response = $payment->verifyPayment($request->get('trans_id'), $request->get('id_get'));

        dd($response);
    }



}
