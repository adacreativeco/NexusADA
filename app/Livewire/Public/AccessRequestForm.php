<?php

namespace App\Livewire\Public;

use App\Models\AccessRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;

class AccessRequestForm extends Component
{
    public string $name = '';
    public string $company_name = '';
    public string $email = '';
    public string $phone = '';
    public int $expected_users = 5;
    public string $plan_interest = 'pro';
    public string $use_case = '';

    public bool $showModal = false;
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|min:2|max:255',
            'company_name'   => 'required|string|min:2|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'expected_users' => 'required|integer|min:1|max:10000',
            'plan_interest'  => 'required|in:pro,enterprise',
            'use_case'       => 'nullable|string|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'         => 'Ad Soyad zorunludur.',
            'company_name.required' => 'Şirket adı zorunludur.',
            'email.required'        => 'E-posta adresi zorunludur.',
            'email.email'           => 'Geçerli bir e-posta adresi girin.',
            'expected_users.min'    => 'En az 1 kullanıcı belirtmelisiniz.',
        ];
    }

    #[On('openAccessModal')]
    public function openModal(string $plan = 'pro'): void
    {
        $this->plan_interest = $plan;
        $this->showModal = true;
        $this->submitted = false;
        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function submit(): void
    {
        // Rate limiting: 3 requests per 10 minutes per IP
        $key = 'access_request:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Çok fazla deneme. Lütfen {$seconds} saniye sonra tekrar deneyin.");
            return;
        }
        RateLimiter::hit($key, 600);

        $this->validate();

        $accessRequest = AccessRequest::create([
            'name'           => $this->name,
            'company_name'   => $this->company_name,
            'email'          => $this->email,
            'phone'          => $this->phone ?: null,
            'expected_users' => $this->expected_users,
            'plan_interest'  => $this->plan_interest,
            'use_case'       => $this->use_case ?: null,
            'status'         => 'new',
        ]);

        // Send notification to ADA Creative team
        try {
            Mail::raw(
                "Yeni Erken Erişim Talebi\n\n" .
                "Ad Soyad: {$this->name}\n" .
                "Şirket: {$this->company_name}\n" .
                "E-posta: {$this->email}\n" .
                "Telefon: " . ($this->phone ?: 'Belirtilmedi') . "\n" .
                "Plan: " . ($this->plan_interest === 'pro' ? 'Profesyonel' : 'Kurumsal') . "\n" .
                "Beklenen Kullanıcı: {$this->expected_users}\n" .
                "Kullanım Amacı: " . ($this->use_case ?: 'Belirtilmedi') . "\n\n" .
                "Tarih: " . now()->format('d.m.Y H:i') . "\n" .
                "Panel: " . url('/admin'),
                function ($message) {
                    $message->to('hello@adacreative.co')
                            ->subject('ADA Co-OS — Yeni Erken Erişim Talebi: ' . $this->company_name);
                }
            );
        } catch (\Throwable $e) {
            // Log but don't fail — the request is saved in DB
            \Log::warning('Access request notification email failed: ' . $e->getMessage());
        }

        // Send thank-you email to requester
        try {
            Mail::raw(
                "Merhaba {$this->name},\n\n" .
                "ADA Co-OS erken erişim talebiniz alınmıştır. Ekibimiz en kısa sürede sizinle iletişime geçecektir.\n\n" .
                "Talep Detayları:\n" .
                "Plan: " . ($this->plan_interest === 'pro' ? 'Profesyonel' : 'Kurumsal') . "\n" .
                "Beklenen Kullanıcı: {$this->expected_users}\n\n" .
                "Teşekkür ederiz,\nADA Co-OS Ekibi\nhttps://nexus.adacreative.co",
                function ($message) {
                    $message->to($this->email)
                            ->subject('ADA Co-OS — Erken Erişim Talebiniz Alındı');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Access request thank-you email failed: ' . $e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'company_name', 'email', 'phone', 'expected_users', 'use_case']);
    }

    public function render()
    {
        return view('livewire.public.access-request-form');
    }
}
