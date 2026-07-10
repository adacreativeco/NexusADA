<div class="nx-login-page">
    <div class="nx-login-card">
        <div class="nx-login-header">
            <h1 class="nx-login-title">İki Adımlı Doğrulama</h1>
            <p class="nx-login-subtitle">
                Güvenliğiniz için lütfen kimlik doğrulama uygulamanızdaki 6 haneli kodu girin.
            </p>
        </div>

        <form wire:submit="verify" class="nx-login-form">
            @if(!$showRecovery)
                <div class="nx-form-group">
                    <label class="nx-label">Doğrulama Kodu</label>
                    <input type="text" 
                           wire:model="code" 
                           class="nx-input" 
                           placeholder="000000" 
                           maxlength="6" 
                           style="font-size: 24px; text-align: center; letter-spacing: 12px; font-family: var(--nx-font-mono);" 
                           autofocus>
                    @error('code') <span class="nx-error">{{ $message }}</span> @enderror
                </div>
            @else
                <div class="nx-form-group">
                    <label class="nx-label">Kurtarma Kodu</label>
                    <input type="text" 
                           wire:model="recoveryCode" 
                           class="nx-input" 
                           placeholder="xxxx-xxxx" 
                           style="font-size: 14px; text-align: center; font-family: var(--nx-font-mono);" 
                           autofocus>
                    <p style="font-size:11px; color:var(--nx-text-secondary); margin-top:8px;">8 haneli kurtarma kodlarınızdan birini girin.</p>
                    @error('recoveryCode') <span class="nx-error">{{ $message }}</span> @enderror
                </div>
            @endif

            <button type="submit" class="nx-btn nx-btn-primary" style="width: 100%; margin-top: 16px;">
                {{ $showRecovery ? 'Kurtarma Kodu ile Giriş Yap' : 'Giriş Yap' }}
            </button>
        </form>

        <div class="nx-login-footer">
            <button type="button" wire:click="toggleRecovery" class="nx-login-link" style="background:none; border:none; width:100%; cursor:pointer; margin-bottom:12px;">
                {{ $showRecovery ? 'Uygulama kodunu kullan' : 'Cihazınıza erişemiyor musunuz?' }}
            </button>
            
            <a href="{{ route('admin.login') }}" class="nx-login-link" style="display: block; margin-top: 12px; font-size: 12px; color: var(--nx-text-muted);">
                Giriş sayfasına dön
            </a>
        </div>
    </div>
</div>
