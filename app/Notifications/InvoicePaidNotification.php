<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvoicePaidNotification extends Notification
{
    use Queueable;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Faturanız Tahsil Edildi: #" . ($this->invoice->invoice_number ?? $this->invoice->id))
            ->greeting("Merhaba " . ($notifiable->name ?? 'Müşterimiz') . ",")
            ->line("Faturanıza ait ödeme başarıyla onaylanmış ve kayıtlara işlenmiştir.")
            ->line("Ödenen Tutar: " . number_format($this->invoice->amount ?? 0, 2) . " " . ($this->invoice->currency ?? 'TRY'))
            ->line("Ödeme Yöntemi: " . strtoupper($this->invoice->payment_method ?? 'Kredi Kartı'))
            ->action("Faturayı İnceleyin", url('/client/invoices'))
            ->line("ADA Co-OS sistemini kullandığınız için teşekkür ederiz.");
    }

    public function toArray($notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'amount' => $this->invoice->amount,
            'status' => 'paid',
        ];
    }
}
