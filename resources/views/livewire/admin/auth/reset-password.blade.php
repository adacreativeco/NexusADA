<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-logo">
            <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="height: 40px; width: auto;">
            <p>Yeni Şifre Belirle</p>
        </div>

        @if(session('status'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <p style="color: #10b981; font-size: 14px; margin: 0; text-align: center;">
                    {{ session('status') }}
                </p>
            </div>
        @endif

        <form wire:submit="resetPassword">
            <div class="nx-form-group" style="margin-bottom: 16px;">
                <label class="nx-label">E-posta</label>
                <input type="email"
                       wire:model="email"
                       class="nx-input {{ $errors->has('email') ? 'has-error' : '' }}"
                       placeholder="admin@adacreative.co">
                @error('email')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="nx-form-group" style="margin-bottom: 16px;">
                <label class="nx-label">Yeni Şifre</label>
                <input type="password"
                       wire:model="password"
                       class="nx-input {{ $errors->has('password') ? 'has-error' : '' }}"
                       placeholder="••••••••">
                @error('password')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="nx-form-group" style="margin-bottom: 24px;">
                <label class="nx-label">Şifre Tekrar</label>
                <input type="password"
                       wire:model="password_confirmation"
                       class="nx-input"
                       placeholder="••••••••">
            </div>

            <button type="submit" class="nx-btn nx-btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                <span wire:loading.remove>Şifreyi Güncelle</span>
                <span wire:loading>Güncelleniyor...</span>
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--nx-text-secondary);">
            <a href="{{ route('admin.login') }}" style="color: var(--nx-accent); font-weight: 500; text-decoration: none;">← Giriş'e Dön</a>
        </p>
    </div>
</div>
