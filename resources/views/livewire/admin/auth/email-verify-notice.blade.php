<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-logo">
            <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="height: 40px; width: auto;">
            <p>E-posta Doğrulama</p>
        </div>

        {{-- Mail Icon --}}
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; margin: 0 auto; border-radius: var(--nx-radius-lg); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--nx-accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </div>
        </div>

        <p style="text-align: center; font-size: 14px; color: var(--nx-text-primary); margin-bottom: 8px; font-weight: 500;">
            E-posta adresinize bir doğrulama bağlantısı gönderdik.
        </p>
        <p style="text-align: center; font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 24px; line-height: 1.6;">
            <strong style="color: var(--nx-accent);">{{ auth()->user()->email }}</strong> adresine gelen
            e-postadaki bağlantıya tıklayarak hesabınızı doğrulayın.
        </p>

        {{-- Başarı mesajı --}}
        @if ($sent)
            <div style="padding: 10px 14px; border-radius: var(--nx-radius-sm); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); margin-bottom: 16px; text-align: center;">
                <span style="font-size: 13px; color: var(--nx-accent); font-weight: 500;">✓ Doğrulama bağlantısı tekrar gönderildi.</span>
            </div>
        @endif

        {{-- Tekrar Gönder --}}
        <button wire:click="resend"
                class="nx-btn nx-btn-primary"
                style="width: 100%; justify-content: center; padding: 10px; margin-bottom: 12px;">
            <span wire:loading.remove wire:target="resend">Tekrar Gönder</span>
            <span wire:loading wire:target="resend">Gönderiliyor...</span>
        </button>

        {{-- Çıkış --}}
        <form method="POST" action="{{ route('admin.logout') }}" style="text-align: center;">
            @csrf
            <button type="submit"
                    style="background: none; border: none; color: var(--nx-text-muted); font-size: 13px; cursor: pointer; font-family: var(--nx-font-ui); transition: color 0.15s;"
                    onmouseover="this.style.color='var(--nx-text-secondary)'"
                    onmouseout="this.style.color='var(--nx-text-muted)'">
                Farklı bir hesapla giriş yap
            </button>
        </form>
    </div>
</div>
