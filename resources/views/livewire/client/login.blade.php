<div class="login-card">
    <div class="login-card-brand">
        <span class="material-symbols-outlined" style="font-size: 32px;">vpn_key</span>
    </div>
    <h1>Müşteri Girişi</h1>
    <p>ADA Co-OS B2B Portalı'na bağlanarak projelerinizi takip edin, faturalarınızı yönetin ve sözleşmelerinizi onaylayın.</p>

    <form wire:submit.prevent="login" style="margin-top: 24px;">
        <div class="form-group">
            <label>E-posta Adresi</label>
            <input type="email" wire:model.defer="email" placeholder="ornek@sirket.com" autofocus required>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label>Giriş Parolası</label>
            <input type="password" wire:model.defer="password" placeholder="••••••••" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <button type="submit" class="btn-primary" style="margin-top: 30px;">
            <span wire:loading.remove style="display: flex; align-items: center; gap: 6px;">
                Güvenli Giriş Yap
                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
            </span>
            <span wire:loading style="display: flex; align-items: center; gap: 8px;">
                Bağlanılıyor...
            </span>
        </button>
    </form>
</div>
