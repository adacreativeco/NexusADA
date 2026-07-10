<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email : Target email address}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info("Sending test email to {$email}...");

        try {
            Mail::raw(
                "Bu bir ADA Co-OS test e-postasıdır.\n\n" .
                "Tarih: " . now()->format('d.m.Y H:i:s') . "\n" .
                "Sunucu: " . config('app.url') . "\n" .
                "Mail Driver: " . config('mail.default') . "\n\n" .
                "Bu e-postayı aldıysanız, e-posta gönderim sistemi başarıyla çalışıyor demektir.",
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('ADA Co-OS — SMTP Test (' . now()->format('H:i:s') . ')');
                }
            );

            $this->info('✓ Test e-postası başarıyla gönderildi!');
            $this->info("  Alıcı: {$email}");
            $this->info('  Driver: ' . config('mail.default'));
            $this->info('  Host: ' . config('mail.mailers.' . config('mail.default') . '.host', 'N/A'));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ E-posta gönderilemedi!');
            $this->error('  Hata: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
