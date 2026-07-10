<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function attempt()
    {
        $this->validate();

        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Çok fazla giriş denemesi. Lütfen {$seconds} saniye sonra tekrar deneyin.");
            return;
        }

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            $user = auth()->user();

            // ── 2FA Kontrolü ────────────────────────────────
            if ($user->two_factor_enabled) {
                // Kullanıcıyı oturumdan çıkar, sadece ID'sini session'da tut
                Auth::logout();
                session([
                    'login.id' => $user->id,
                    'login.remember' => $this->remember,
                ]);

                return redirect()->route('admin.two-factor-challenge');
            }

            session()->regenerate();

            $user = auth()->user();

            // Platform sahibi → /admin/ (günlük çalışma alanı)
            // /platform/ paneline sidebar'dan erişir
            if ($user->isWebmaster()) {
                return redirect()->route('admin.dashboard');
            }

            // Tenant kullanıcısı → /admin/
            if ($user->tenant_id) {
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
                return redirect()->route('admin.dashboard');
            }

            // Ne platform ne tenant → erişim yok
            auth()->logout();
            $this->addError('email', 'Hesabınız bir ajansa atanmamış. Lütfen yöneticinizle iletişime geçin.');
            return;
        }

        RateLimiter::hit($throttleKey);
        $this->addError('email', 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.');
    }

    public function render()
    {
        return view('livewire.admin.auth.login')
            ->layout('livewire.admin.auth.login-layout');
    }
}
