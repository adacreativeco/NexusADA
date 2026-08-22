<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Payment\PaymentService;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Stripe Webhooks
     */
    public function handleStripe(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['type'] ?? 'unknown';

        Log::info("Stripe Webhook Received: {$event}");

        if ($event === 'checkout.session.completed' || $event === 'payment_intent.succeeded') {
            $session = $payload['data']['object'] ?? [];
            $invoiceId = $session['metadata']['invoice_id'] ?? null;
            $paymentRef = $session['id'] ?? 'stripe_txn_' . time();

            if ($invoiceId) {
                $invoice = Invoice::find($invoiceId);
                if ($invoice && $invoice->status !== 'paid') {
                    PaymentService::markInvoicePaid($invoice, $paymentRef, 'stripe');
                }
            }
        }

        return response()->json(['status' => 'handled', 'event' => $event]);
    }

    /**
     * Handle Iyzico Webhooks
     */
    public function handleIyzico(Request $request): JsonResponse
    {
        $payload = $request->all();
        $status = $payload['status'] ?? 'FAILURE';
        $paymentId = $payload['paymentId'] ?? null;
        $invoiceId = $payload['invoiceId'] ?? null;

        Log::info("Iyzico Webhook Received: Status={$status}, PaymentId={$paymentId}");

        if ($status === 'SUCCESS' && $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->status !== 'paid') {
                PaymentService::markInvoicePaid($invoice, $paymentId ?? 'iyzi_txn_' . time(), 'iyzico');
            }
        }

        return response()->json(['status' => 'handled']);
    }
}
