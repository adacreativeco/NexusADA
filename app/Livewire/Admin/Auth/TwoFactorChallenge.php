<?php

namespace App\Livewire\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallenge extends Component
{
    public string $code = '';
    public string $recoveryCode = '';
    public bool $showRecovery = false;

    public function mount()
    {
        if (!session()->has('login.id')) {
            return redirect()->route('admin.login');
        }
    }

    public function verify()
    {
        if ($this->showRecovery) {
            return $this->verifyRecovery();
        }

        $this->validate([
            'code' => 'required|digits:6',
        ]);

        $userId = session('login.id');
        $user = User::findOrFail($userId);

        try {
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);
            
            $valid = $google2fa->verifyKey($secret, $this->code);

            if ($valid) {
                return $this->completeLogin($user);
            }

            $this->addError('code', 'Geçersiz doğrulama kodu.');
        } catch (\Exception $e) {
            $this->addError('code', 'Doğrulama sırasında bir hata oluştu.');
        }
    }

    protected function verifyRecovery()
    {
        $this->validate([
            'recoveryCode' => 'required|string',
        ]);

        $userId = session('login.id');
        $user = User::findOrFail($userId);

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];

        if (in_array($this->recoveryCode, $recoveryCodes)) {
            // Kullanılan kodu listeden sil (tek kullanımlık)
            $newCodes = array_diff($recoveryCodes, [$this->recoveryCode]);
            $user->update(['two_factor_recovery_codes' => array_values($newCodes)]);

            return $this->completeLogin($user);
        }

        $this->addError('recoveryCode', 'Geçersiz kurtarma kodu.');
    }

    protected function completeLogin($user)
    {
        Auth::login($user, session('login.remember', false));
        
        session()->forget(['login.id', 'login.remember']);
        session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function toggleRecovery()
    {
        $this->showRecovery = !$this->showRecovery;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.auth.two-factor-challenge')
            ->layout('livewire.admin.auth.login-layout');
    }
}
