<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-logo">
            <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="height: 40px; width: auto;">
            <p>Şifre Sıfırlama</p>
        </div>

        @if($sent)
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <p style="color: #10b981; font-size: 14px; margin: 0; text-align: center;">
                    ✓ Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.
                </p>
            </div>
        @else
            <p style="font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 20px; text-align: center;">
                E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.
            </p>

            <form wire:submit="sendResetLink">
                <div class="nx-form-group" style="margin-bottom: 16px;">
                    <label class="nx-label">E-posta</label>
                    <input type="email"
                           wire:model="email"
                           class="nx-input {{ $errors->has('email') ? 'has-error' : '' }}"
                           placeholder="admin@adacreative.co"
                           autofocus>
                    @error('email')
                        <span class="nx-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="nx-btn nx-btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                    <span wire:loading.remove>Sıfırlama Bağlantısı Gönder</span>
                    <span wire:loading>Gönderiliyor...</span>
                </button>
            </form>
        @endif

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--nx-text-secondary);">
            Şifrenizi hatırladınız mı?
            <a href="{{ route('admin.login') }}" style="color: var(--nx-accent); font-weight: 500; text-decoration: none;">Giriş Yap</a>
        </p>
    </div>
</div>
