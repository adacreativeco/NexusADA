<div class="nx-login-card">
    <div class="nx-login-header">
        <h1 class="nx-login-title">Hesabınız İnceleniyor</h1>
        <p class="nx-login-subtitle">
            E-posta doğrulamanız başarıyla tamamlandı. ADA Co-OS kalitesini korumak adına tüm yeni kayıtları manuel olarak onaylıyoruz.
        </p>
    </div>

    <div style="text-align: center; margin: 32px 0;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; color: var(--nx-accent); margin-bottom: 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 32px; height: 32px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        
        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-lg); padding: 20px; text-align: left;">
            <p style="font-size: 14px; color: var(--nx-text-primary); margin-bottom: 12px; font-weight: 600;">Bundan sonra ne olacak?</p>
            <ul style="font-size: 13px; color: var(--nx-text-secondary); padding-left: 18px; margin: 0;">
                <li style="margin-bottom: 8px;">Ekibimiz başvurunuzu 24 saat içinde inceleyecektir.</li>
                <li style="margin-bottom: 8px;">Onaylandığında size bir bilgilendirme e-postası göndereceğiz.</li>
                <li>Onay sonrası bu ekran otomatik olarak güncellenecek ve panelinize erişebileceksiniz.</li>
            </ul>
        </div>
    </div>

    <div class="nx-login-footer" style="border-top: 1px solid var(--nx-border); padding-top: 20px;">
        <p class="nx-login-footer-text">
            Bir hata olduğunu mu düşünüyorsunuz? 
            <a href="mailto:hello@adacreative.co" class="nx-login-link">Destekle İletişime Geçin</a>
        </p>
        
        <form method="POST" action="{{ route('admin.logout') }}" style="margin-top: 16px;">
            @csrf
            <button type="submit" style="background: none; border: none; color: var(--nx-text-muted); font-size: 12px; cursor: pointer; text-decoration: underline;">
                Farklı bir hesapla giriş yap
            </button>
        </form>
    </div>
</div>
