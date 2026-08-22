<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StripeGateway implements PaymentGatewayInterface
{
    protected ?string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret', env('STRIPE_SECRET_KEY'));
    }

    public function createPaymentSession(Invoice $invoice, array $options = []): array
    {
        if (empty($this->secretKey)) {
            $mock = new MockGateway();
            return $mock->createPaymentSession($invoice, $options);
        }

        $res = Http::withToken($this->secretKey)->asForm()->post('https://api.stripe.com/v1/checkout/sessions', [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($invoice->currency ?? 'try'),
                    'unit_amount' => (int)(($invoice->total_amount ?? 100) * 100),
                    'product_data' => ['name' => "Fatura: " . ($invoice->invoice_number ?? $invoice->id)],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url("/pay/success?session_id={CHECKOUT_SESSION_ID}"),
            'cancel_url' => url("/pay/cancel"),
        ]);

        if ($res->successful()) {
            return [
                'status' => 'success',
                'gateway' => 'stripe',
                'session_id' => $res->json('id'),
                'checkout_url' => $res->json('url'),
            ];
        }

        return ['status' => 'error', 'message' => $res->body()];
    }

    public function verifyPayment(string $paymentId, array $payload = []): bool
    {
        if (empty($this->secretKey)) {
            return true;
        }
        $res = Http::withToken($this->secretKey)->get("https://api.stripe.com/v1/checkout/sessions/{$paymentId}");
        return $res->successful() && $res->json('payment_status') === 'paid';
    }

    public function getGatewayName(): string
    {
        return 'stripe';
    }
}
