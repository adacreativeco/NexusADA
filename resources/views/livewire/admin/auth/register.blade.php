<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-logo">
            <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="height: 40px; width: auto;">
            <p>Ücretsiz Hesap Oluştur</p>
        </div>

        <form wire:submit="register">
            {{-- Ad Soyad --}}
            <div class="nx-form-group" style="margin-bottom: 14px;">
                <label class="nx-label">Ad Soyad</label>
                <input type="text"
                       wire:model="name"
                       class="nx-input {{ $errors->has('name') ? 'has-error' : '' }}"
                       placeholder="Adınız Soyadınız"
                       autofocus>
                @error('name')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- E-posta --}}
            <div class="nx-form-group" style="margin-bottom: 14px;">
                <label class="nx-label">E-posta</label>
                <input type="email"
                       wire:model="email"
                       class="nx-input {{ $errors->has('email') ? 'has-error' : '' }}"
                       placeholder="siz@sirketiniz.com">
                @error('email')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Şirket/Ajans Adı --}}
            <div class="nx-form-group" style="margin-bottom: 14px;">
                <label class="nx-label">Şirket / Ajans Adı</label>
                <input type="text"
                       wire:model="company_name"
                       class="nx-input {{ $errors->has('company_name') ? 'has-error' : '' }}"
                       placeholder="Ada Creative Co.">
                @error('company_name')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Şifre --}}
            <div class="nx-form-group" style="margin-bottom: 14px;">
                <label class="nx-label">Şifre</label>
                <input type="password"
                       wire:model="password"
                       class="nx-input {{ $errors->has('password') ? 'has-error' : '' }}"
                       placeholder="••••••••">
                @error('password')
                    <span class="nx-error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Şifre Tekrar --}}
            <div class="nx-form-group" style="margin-bottom: 16px;">
                <label class="nx-label">Şifre Tekrar</label>
                <input type="password"
                       wire:model="password_confirmation"
                       class="nx-input"
                       placeholder="••••••••">
            </div>

            {{-- KVKK / Kullanım Koşulları --}}
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                    <input type="checkbox"
                           wire:model="terms"
                           style="width: 16px; height: 16px; margin-top: 2px; accent-color: var(--nx-accent); cursor: pointer; flex-shrink: 0;">
                    <span style="font-size: 12.5px; line-height: 1.5; color: var(--nx-text-secondary);">
                        <a href="#" style="color: var(--nx-accent); text-decoration: none;">Kullanım Koşulları</a> ve
                        <a href="#" style="color: var(--nx-accent); text-decoration: none;">KVKK Aydınlatma Metni</a>'ni
                        okudum ve kabul ediyorum.
                    </span>
                </label>
                @error('terms')
                    <span class="nx-error-text" style="margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="nx-btn nx-btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                <span wire:loading.remove>Hesap Oluştur →</span>
                <span wire:loading>Hesabınız oluşturuluyor...</span>
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--nx-text-secondary);">
            Zaten hesabınız var mı?
            <a href="{{ route('admin.login') }}" style="color: var(--nx-accent); font-weight: 500; text-decoration: none;">Giriş Yap</a>
        </p>
    </div>
</div>
