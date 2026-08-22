<?php

namespace App\Services\Payment;

use App\Models\Invoice;

interface PaymentGatewayInterface
{
    public function createPaymentSession(Invoice $invoice, array $options = []): array;
    public function verifyPayment(string $paymentId, array $payload = []): bool;
    public function getGatewayName(): string;
}
