<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use Illuminate\Support\Str;

class MockGateway implements PaymentGatewayInterface
{
    public function createPaymentSession(Invoice $invoice, array $options = []): array
    {
        $token = 'mock_pay_' . Str::random(24);
        return [
            'status' => 'success',
            'gateway' => 'mock_sandbox',
            'session_id' => $token,
            'checkout_url' => url("/pay/mock/{$token}?invoice={$invoice->id}"),
            'amount' => $invoice->total_amount ?? $invoice->amount ?? 1000,
            'currency' => $invoice->currency ?? 'TRY',
        ];
    }

    public function verifyPayment(string $paymentId, array $payload = []): bool
    {
        return str_starts_with($paymentId, 'mock_pay_');
    }

    public function getGatewayName(): string
    {
        return 'mock';
    }
}
