<?php

namespace App\Livewire\Admin\Auth;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $company_name = '';
    public bool $terms = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:10|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'company_name' => 'required|string|min:2|max:255',
            'terms' => 'accepted',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'name.min' => 'Ad Soyad en az 2 karakter olmalıdır.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 10 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'password.regex' => 'Şifre en az 1 büyük harf, 1 rakam ve 1 özel karakter içermelidir.',
            'company_name.required' => 'Şirket/Ajans adı zorunludur.',
            'company_name.min' => 'Şirket adı en az 2 karakter olmalıdır.',
            'terms.accepted' => 'Kullanım koşullarını kabul etmeniz gerekmektedir.',
        ];
    }

    public function register()
    {
        // ── Rate Limiting ────────────────────────────────
        $key = 'register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Çok fazla deneme. Lütfen {$seconds} saniye sonra tekrar deneyin.");
            return;
        }
        RateLimiter::hit($key, 60);

        // ── Validasyon ───────────────────────────────────
        $this->validate();

        // ── Kayıt İşlemi (Atomik) ───────────────────────
        DB::transaction(function () {
            // 1. Starter planı bul
            $starterPlan = Plan::where('slug', 'starter')->first();

            // 2. Benzersiz slug oluştur
            $baseSlug = Str::slug($this->company_name);
            $slug = $baseSlug;
            $counter = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // 3. Tenant oluştur
            $tenant = Tenant::create([
                'name' => $this->company_name,
                'slug' => $slug,
                'email' => $this->email,
                'plan_id' => $starterPlan?->id,
                'status' => 'pending',
                'max_users' => $starterPlan?->max_users ?? 1,
                'max_projects' => $starterPlan?->max_projects ?? 3,
                'max_storage_mb' => $starterPlan?->max_storage_mb ?? 512,
            ]);

            // 4. Kullanıcı oluştur
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'tenant_id' => $tenant->id,
            ]);

            // 5. tenant_owner rolü ata
            $user->assignRole('tenant_owner');

            // 6. Otomatik giriş
            Auth::login($user);
            session()->regenerate();

            // 7. Doğrulama e-postası gönder (senkron — queue worker bağımsız)
            $originalConnection = config('queue.default');
            config(['queue.default' => 'sync']);
            $user->sendEmailVerificationNotification();
            config(['queue.default' => $originalConnection]);
        });

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.admin.auth.register')
            ->layout('livewire.admin.auth.login-layout');
    }
}
