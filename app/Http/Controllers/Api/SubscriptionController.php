<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubscriptionResource;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BitpayPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends BaseApiController
{
    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'activity_field_id' => 'required|integer|exists:activity_fields,id',
        ]);

        $activityFieldId = $request->integer('activity_field_id');
        $plan = Plan::query()
            ->whereKey($request->integer('plan_id'))
            ->where('activity_field_id', $activityFieldId)
            ->where('status', Plan::STATUS_ACTIVE)
            ->first();

        if (! $plan) {
            return $this->errorResponse('Plan not found for this activity field', 404);
        }

        $user = $request->user();
        if ($user->activeSubscriptions()->where('activity_field_id', $activityFieldId)->exists()) {
            // todo:: handle exteded plan
            return $this->errorResponse('User already has an active subscription for this activity field', 400);
        }

        $response = null;
        $amount = (! is_null($plan->discount_price) && (float) $plan->discount_price !== 0.0)
            ? $plan->discount_price
            : ($plan->price ?? 0);

        if ($amount == 0) {
            return $this->errorResponse('amount is 0', 400);
        }

        $wallet = Wallet::query()->firstOrCreate([
            'entity_type' => $user->getMorphClass(),
            'entity_id' => $user->id,
        ], [
            'balance' => 0,
            'currency' => 'IRR',
        ]);

        $transaction = Transaction::query()
            ->create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'payment' => Transaction::PAYMENT_SUBSCRIPTION,
                'type' => Transaction::TYPE_IN,
                'status' => Transaction::STATUS_PENDING,
                'amount' => $amount,
                'currency' => $wallet->currency ?? 'IRR',
                'description' => 'Subscription purchase for plan: '.$plan->name,
                'plan_id' => $plan->id,
            ]);

        $payment = new BitpayPaymentService;
        $response = $payment->requestPayment($amount, 'https://api.talentyar.com/api/v1/callback', $transaction->id, $user->phone, $user->username, 'description');
        $url = $response['gateway_url'] ?? null;

        $transaction->update([
            'meta_data' => [
                'id_get' => $response['id_get'] ?? null,
                'raw' => $response['raw'] ?? null,
            ],
        ]);

        if (is_null($url)) {
            return $this->errorResponse('payment gateway down!', 400);
        }

        return $this->successResponse([
            'url' => $url,
        ], 'go to payment gate', 200);

        // todo:: add active for test
        //        $subscription = $user->subscriptions()->create([
        //            'activity_field_id' => $plan->activity_field_id,
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
        $request->validate([
            'activity_field_id' => 'nullable|integer|exists:activity_fields,id',
        ]);

        $user = $request->user();
        $query = $user->activeSubscriptions()->with('plan');

        if ($request->filled('activity_field_id')) {
            $query->where('activity_field_id', $request->integer('activity_field_id'));
        }

        $subscriptions = $query
            ->orderBy('activity_field_id')
            ->latest('started_at')
            ->get();

        return $this->successResponse([
            'subscriptions' => SubscriptionResource::collection($subscriptions)->resolve(),
        ], 'Active subscriptions retrieved successfully');
    }

    public function callback(Request $request)
    {
        $transaction = Transaction::query()
            ->with(['plan', 'user'])
            ->where('id', $request->get('factorId'))
            ->first();
        if (! $transaction) {
            return $this->paymentResultRedirect('failed');
        }

        if ($transaction->status != Transaction::STATUS_PENDING) {
            return $this->paymentResultRedirect('failed');
        }

        $user = $transaction->user;
        $plan = $transaction->plan;

        if (! $user
            || ($transaction->payment !== Transaction::PAYMENT_RE_EVALUATION && ! $plan)) {
            return $this->paymentResultRedirect('failed');
        }

        $response = null;
        $payment = new BitpayPaymentService;
        $response = $payment->verifyPayment($request->get('trans_id'), $request->get('id_get'));

        if (is_null($response)) {
            return $this->paymentResultRedirect('failed');
        }

        $transaction->update([
            'meta_data' => array_merge($transaction->meta_data ?? [], [
                'verify_response' => $response,
            ]),
        ]);

        if ($response['verified'] == true) {

            if ($transaction->payment === Transaction::PAYMENT_RE_EVALUATION) {
                return DB::transaction(function () use ($transaction): RedirectResponse {
                    $lockedTransaction = Transaction::query()
                        ->lockForUpdate()
                        ->find($transaction->id);

                    if (! $lockedTransaction
                        || $lockedTransaction->status !== Transaction::STATUS_PENDING) {
                        return $this->paymentResultRedirect('failed');
                    }

                    $subscription = Subscription::query()
                        ->lockForUpdate()
                        ->find($lockedTransaction->subscription_id);

                    if (! $subscription) {
                        return $this->paymentResultRedirect('failed');
                    }

                    $subscription->increment('re_evaluation_credits');
                    $lockedTransaction->update([
                        'status' => Transaction::STATUS_SUCCESS,
                    ]);

                    return $this->paymentResultRedirect('success');
                });
            }

            $subscription = $user->subscriptions()->create([
                'activity_field_id' => $plan->activity_field_id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'price_paid' => $transaction->amount,
                'discount_price' => $plan->discount_price ?? 0,
                'started_at' => now(),
                'expires_at' => now()->addDays($plan->duration),
            ]);

            $transaction->update([
                'status' => Transaction::STATUS_SUCCESS,
                'subscription_id' => $subscription->id,
            ]);

            return $this->paymentResultRedirect('success');
        } else {
            $transaction->update([
                'status' => Transaction::STATUS_FAIL,
            ]);

            return $this->paymentResultRedirect('failed');
        }

    }

    private function paymentResultRedirect(string $status): RedirectResponse
    {
        return redirect()->away('https://talentyar.com/payment/result?status='.urlencode($status));
    }
}
