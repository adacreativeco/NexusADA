<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Türkçeleştirilmiş doğrulama e-postası.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('ADA Co-OS — E-posta Adresinizi Doğrulayın')
            ->greeting('Merhaba!')
            ->line('ADA Co-OS hesabınız başarıyla oluşturuldu. Platformu kullanmaya başlamak için lütfen e-posta adresinizi doğrulayın.')
            ->action('E-postamı Doğrula', $url)
            ->line('Bu bağlantı 60 dakika içinde geçerliliğini yitirecektir.')
            ->line('Eğer bu hesabı siz oluşturmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation('— ADA Co-OS Ekibi');
    }
}
