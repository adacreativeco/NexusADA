<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-logo">
            <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="height: 40px; width: auto;">
            <p>Kurumsal Beyin — Giriş</p>
        </div>

        <form wire:submit="attempt">
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

            <div class="nx-form-group" style="margin-bottom: 16px;">
                <label class="nx-label">Şifre</label>
                <input type="password"
                       wire:model="password"
                       class="nx-input {{ $errors->has('password') ? 'has-error' : '' }}"
                       placeholder="••••••••">
                @error('password')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <label class="nx-toggle">
                    <div class="nx-toggle-track {{ $remember ? 'active' : '' }}"
                         wire:click="$toggle('remember')">
                        <div class="nx-toggle-thumb"></div>
                    </div>
                    <span style="font-size: 13px; color: var(--nx-text-secondary);">Beni Hatırla</span>
                </label>
                <a href="{{ route('admin.forgot-password') }}" style="font-size: 13px; color: var(--nx-accent); text-decoration: none; font-weight: 500;">Şifremi Unuttum</a>
            </div>

            <button type="submit" class="nx-btn nx-btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                <span wire:loading.remove>Giriş Yap</span>
                <span wire:loading>Doğrulanıyor...</span>
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--nx-text-secondary);">
            Hesabınız yok mu?
            <a href="{{ route('admin.register') }}" style="color: var(--nx-accent); font-weight: 500; text-decoration: none;">Ücretsiz Kayıt Ol</a>
        </p>
    </div>
</div>
