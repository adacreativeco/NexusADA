<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public bool $sent = false;

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        $throttleKey = 'forgot-password|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Çok fazla deneme yaptınız. Lütfen {$seconds} saniye sonra tekrar deneyin.");
            return;
        }

        RateLimiter::hit($throttleKey);

        // Force synchronous sending — no queue worker dependency
        $originalConnection = config('queue.default');
        config(['queue.default' => 'sync']);

        $status = Password::sendResetLink(['email' => $this->email]);

        // Restore original queue connection
        config(['queue.default' => $originalConnection]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;
            session()->flash('status', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.');
        } else {
            $this->addError('email', 'Bu e-posta adresi kayıtlarımızda bulunamadı.');
        }
    }

    public function render()
    {
        return view('livewire.admin.auth.forgot-password')
            ->layout('livewire.admin.auth.login-layout');
    }
}
