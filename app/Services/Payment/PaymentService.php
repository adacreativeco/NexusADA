<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Services\ActivityService;

class PaymentService
{
    public static function getGateway(?string $preferred = null): PaymentGatewayInterface
    {
        return match ($preferred) {
            'stripe' => new StripeGateway(),
            'iyzico' => new IyzicoGateway(),
            default => new MockGateway(),
        };
    }

    public static function initiateInvoicePayment(Invoice $invoice, ?string $gatewayName = null): array
    {
        $gateway = self::getGateway($gatewayName);
        $session = $gateway->createPaymentSession($invoice);

        ActivityService::logSystem(
            'Ödeme Başlatıldı',
            "Fatura #{$invoice->id} için {$gateway->getGatewayName()} üzerinden ödeme oturumu oluşturuldu.",
            $invoice
        );

        return $session;
    }

    public static function markInvoicePaid(Invoice $invoice, string $paymentRef, string $gateway): void
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $paymentRef,
            'payment_method' => $gateway,
        ]);

        ActivityService::logSystem(
            'Fatura Ödendi',
            "Fatura #{$invoice->id} {$gateway} ({$paymentRef}) üzerinden tahsil edildi.",
            $invoice
        );
    }
}
