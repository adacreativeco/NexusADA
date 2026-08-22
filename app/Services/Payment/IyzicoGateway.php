<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use Illuminate\Support\Str;

class IyzicoGateway implements PaymentGatewayInterface
{
    protected ?string $apiKey;
    protected ?string $secretKey;

    public function __construct()
    {
        $this->apiKey = config('services.iyzico.api_key', env('IYZICO_API_KEY'));
        $this->secretKey = config('services.iyzico.secret_key', env('IYZICO_SECRET_KEY'));
    }

    public function createPaymentSession(Invoice $invoice, array $options = []): array
    {
        if (empty($this->apiKey)) {
            $mock = new MockGateway();
            return $mock->createPaymentSession($invoice, $options);
        }

        $token = 'iyzi_' . Str::random(20);
        return [
            'status' => 'success',
            'gateway' => 'iyzico',
            'token' => $token,
            'checkout_url' => "https://sandbox-api.iyzipay.com/payment/iyziconnect/{$token}",
        ];
    }

    public function verifyPayment(string $paymentId, array $payload = []): bool
    {
        return true;
    }

    public function getGatewayName(): string
    {
        return 'iyzico';
    }
}
