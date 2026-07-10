<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSetup extends Component
{
    public bool $showSetup = false;
    public string $verifyCode = '';
    public ?string $qrCodeUrl = null;
    public ?string $secretKey = null;
    public bool $isEnabled = false;
    public array $recoveryCodes = [];
    public bool $showRecoveryCodes = false;
    public bool $hasDownloaded = false;
    public bool $forceConfirm = false;

    public function mount(): void
    {
        $this->isEnabled = (bool) auth()->user()->two_factor_enabled;
    }

    public function startSetup(): void
    {
        $google2fa = new Google2FA();
        $this->secretKey = $google2fa->generateSecretKey();

        $this->qrCodeUrl = $google2fa->getQRCodeUrl(
            'NexusADA',
            auth()->user()->email,
            $this->secretKey
        );

        $this->qrCodeUrl .= '&image=' . urlencode('https://nexus.adacreative.co/images/logo.png');

        // Kurulum başlarken geçici kurtarma kodları üret (görsel için)
        $this->recoveryCodes = collect(range(1, 8))->map(fn() => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10))->toArray();

        $this->showSetup = true;
    }

    public function verify(): void
    {
        $this->validate([
            'verifyCode' => 'required|digits:6',
        ]);

        // İndirme kontrolü
        if (!$this->hasDownloaded && !$this->forceConfirm) {
            $this->forceConfirm = true;
            $this->addError('verifyCode', 'Lütfen devam etmeden önce kurtarma kodlarını indirdiğinizden veya güvenli bir yere kaydettiğinizden emin olun.');
            return;
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secretKey, $this->verifyCode);

        if ($valid) {
            auth()->user()->update([
                'two_factor_secret' => encrypt($this->secretKey),
                'two_factor_enabled' => true,
                'two_factor_recovery_codes' => $this->recoveryCodes,
                'two_factor_confirmed_at' => now(),
            ]);

            $this->showRecoveryCodes = true;
            $this->isEnabled = true;
            $this->showSetup = false;
            $this->secretKey = null;
            $this->qrCodeUrl = null;
            $this->verifyCode = '';
            $this->forceConfirm = false;

            $this->dispatch('notify', message: '2FA başarıyla etkinleştirildi!', type: 'success');
        } else {
            $this->addError('verifyCode', 'Geçersiz doğrulama kodu. Tekrar deneyin.');
        }
    }

    public function markAsDownloaded(): void
    {
        $this->hasDownloaded = true;
        $this->forceConfirm = false; // İndirince uyarıyı kaldır
    }

    public function disable(): void
    {
        auth()->user()->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $this->isEnabled = false;
        $this->recoveryCodes = [];
        $this->dispatch('notify', message: '2FA devre dışı bırakıldı.', type: 'info');
    }

    public function showCodes(): void
    {
        $this->recoveryCodes = auth()->user()->two_factor_recovery_codes ?? [];
        $this->showRecoveryCodes = true;
    }

    public function regenerateCodes(): void
    {
        $codes = collect(range(1, 8))->map(fn() => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10))->toArray();
        
        auth()->user()->update([
            'two_factor_recovery_codes' => $codes,
        ]);

        $this->recoveryCodes = $codes;
        $this->showRecoveryCodes = true;
        $this->dispatch('notify', message: 'Yeni kurtarma kodları üretildi.', type: 'success');
    }

    public function hideCodes(): void
    {
        $this->showRecoveryCodes = false;
        $this->recoveryCodes = [];
    }

    public function cancelSetup(): void
    {
        $this->showSetup = false;
        $this->secretKey = null;
        $this->qrCodeUrl = null;
        $this->verifyCode = '';
    }

    public function render()
    {
        return view('livewire.admin.two-factor-setup')
            ->layout('layouts.admin', [
                'title' => 'İki Adımlı Doğrulama',
                'breadcrumb' => 'İki Adımlı Doğrulama',
            ]);
    }
}
