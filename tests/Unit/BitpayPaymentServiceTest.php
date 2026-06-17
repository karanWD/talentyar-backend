<?php

use App\Services\BitpayPaymentService;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

it('requests a bitpay payment and returns gateway url', function () {
    config()->set('services.bitpay.api', 'test-api');
    config()->set('services.bitpay.gateway_send_url', 'https://bitpay.ir/payment/gateway-send');
    config()->set('services.bitpay.gateway_url', 'https://bitpay.ir/payment/gateway-%s-get');

    Http::fake([
        'https://bitpay.ir/payment/gateway-send' => Http::response('12345'),
    ]);

    $result = app(BitpayPaymentService::class)->requestPayment(
        amount: 10000,
        redirect: 'https://example.test/payment/callback',
        factorId: 10,
        name: 'Test User',
        email: 'test@example.test',
        description: 'Plan purchase'
    );

    expect($result)->toMatchArray([
        'id_get' => 12345,
        'gateway_url' => 'https://bitpay.ir/payment/gateway-12345-get',
    ]);

    Http::assertSent(fn ($request) => $request->data()['api'] === 'test-api'
        && $request->data()['amount'] === 10000
        && $request->data()['factorId'] === 10);
});

it('verifies a successful bitpay payment', function () {
    config()->set('services.bitpay.api', 'test-api');
    config()->set('services.bitpay.gateway_result_url', 'https://bitpay.ir/payment/gateway-result-second');

    Http::fake([
        'https://bitpay.ir/payment/gateway-result-second' => Http::response([
            'status' => 1,
            'amount' => 10000,
            'factorId' => 10,
            'cardNum' => '603799******1234',
        ]),
    ]);

    $result = app(BitpayPaymentService::class)->verifyPayment('tx-123', '12345');

    expect($result)->toMatchArray([
        'verified' => true,
        'amount' => 10000,
        'factor_id' => 10,
        'card_number' => '603799******1234',
        'trans_id' => 'tx-123',
        'id_get' => '12345',
    ]);
});

it('throws when bitpay api key is missing', function () {
    config()->set('services.bitpay.api', null);

    app(BitpayPaymentService::class)->requestPayment(
        amount: 10000,
        redirect: 'https://example.test/payment/callback'
    );
})->throws(RuntimeException::class, 'Bitpay API key is not configured.');
