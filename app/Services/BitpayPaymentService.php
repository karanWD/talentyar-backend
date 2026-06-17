<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class BitpayPaymentService
{
    public function requestPayment(
        int $amount,
        string $redirect,
        int|string|null $factorId = null,
        ?string $name = null,
        ?string $email = null,
        ?string $description = null
    ): array {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        $response = $this->client()
            ->asForm()
            ->post($this->config('gateway_send_url'), [
                'api' => $this->apiKey(),
                'amount' => $amount,
                'redirect' => $redirect,
                'factorId' => $factorId,
                'name' => $name,
                'email' => $email,
                'description' => $description,
            ]);

        $response->throw();

        $gatewayId = trim($response->body());

        if (! is_numeric($gatewayId) || (int) $gatewayId <= 0) {
            throw new RuntimeException("Bitpay payment request failed with response: {$gatewayId}");
        }

        return [
            'id_get' => (int) $gatewayId,
            'gateway_url' => sprintf($this->config('gateway_url'), $gatewayId),
            'raw' => $gatewayId,
        ];
    }

    public function verifyPayment(string $transId, string $idGet): array
    {
        if ($transId === '' || $idGet === '') {
            throw new InvalidArgumentException('Transaction id and gateway id are required.');
        }

        $response = $this->client()
            ->asForm()
            ->post($this->config('gateway_result_url'), [
                'api' => $this->apiKey(),
                'id_get' => $idGet,
                'trans_id' => $transId,
                'json' => 1,
            ]);

        $response->throw();

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException('Bitpay verification response is not valid JSON.');
        }

        return [
            'verified' => (int) ($payload['status'] ?? 0) === 1,
            'status' => $payload['status'] ?? null,
            'amount' => $payload['amount'] ?? null,
            'factor_id' => $payload['factorId'] ?? null,
            'card_number' => $payload['cardNum'] ?? null,
            'trans_id' => $transId,
            'id_get' => $idGet,
            'raw' => $payload,
        ];
    }

    private function client(): PendingRequest
    {
        $request = Http::timeout((int) $this->config('timeout'));

        if (! (bool) $this->config('verify_ssl')) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function apiKey(): string
    {
        $apiKey = $this->config('api');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('Bitpay API key is not configured.');
        }

        return $apiKey;
    }

    private function config(string $key): mixed
    {
        return config("services.bitpay.{$key}");
    }
}
