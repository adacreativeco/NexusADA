<?php

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProposalSentNotification extends Notification
{
    use Queueable;

    public Proposal $proposal;

    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("📄 Yeni Teklif Hazırlandı: " . $this->proposal->title)
            ->greeting("Merhaba " . ($notifiable->name ?? 'Yetkili') . ",")
            ->line("Firmanız adına yeni bir kurumsal teklif hazırlanmış ve onayınıza sunulmuştur.")
            ->line("Teklif Tutarı: " . number_format($this->proposal->grand_total ?? 0, 2) . " " . ($this->proposal->currency ?? 'TRY'))
            ->action("Teklifi İncele & Onayla", url('/client'))
            ->line("Sorularınız veya revizyon talepleriniz için bu e-postayı yanıtlayabilirsiniz.");
    }

    public function toArray($notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'title' => $this->proposal->title,
            'amount' => $this->proposal->grand_total,
        ];
    }
}
