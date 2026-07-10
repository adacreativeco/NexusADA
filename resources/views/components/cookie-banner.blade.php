{{-- Cookie Banner — pure client-side (Laravel encrypts cookies, so JS-set cookies are invisible to request()->cookie()) --}}
<div id="cookie-banner" style="
    position: fixed; bottom: 20px; left: 20px; right: 20px; z-index: 99999;
    background: rgba(30, 30, 46, 0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 16px 24px;
    display: none; align-items: center; justify-content: space-between; gap: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    font-family: 'Inter', sans-serif;
">
    <div style="flex: 1;">
        <h4 style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #fff;">KVKK ve Çerez Politikası</h4>
        <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
            ADA Co-OS platformu, size daha iyi hizmet verebilmek ve oturum yönetimi sağlamak amacıyla çerez kullanmaktadır. 
            Detaylı bilgi için <a href="/kvkk" style="color: #3b82f6; text-decoration: none; font-weight: 500;">Aydınlatma Metni</a>'ni inceleyebilirsiniz.
        </p>
    </div>
    <button onclick="acceptCookies()" style="
        background: #3b82f6; color: white; border: none; 
        padding: 10px 20px; border-radius: 10px; 
        font-size: 13px; font-weight: 600; cursor: pointer;
        transition: all 0.2s; white-space: nowrap;
    " onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-1px)'" 
       onmouseout="this.style.background='#3b82f6'; this.style.transform='none'">
        Anladım, Kabul Et
    </button>
</div>

<script>
(function() {
    function hasCookieConsent() {
        return document.cookie.split(';').some(function(c) {
            return c.trim().startsWith('cookie_consent=');
        });
    }

    if (!hasCookieConsent()) {
        var banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.style.display = 'flex';
            banner.style.animation = 'nxSlideUp 0.5s ease-out';
        }
    }
})();

function acceptCookies() {
    document.cookie = "cookie_consent=1; max-age=31536000; path=/; SameSite=Lax";
    var banner = document.getElementById('cookie-banner');
    if (banner) {
        banner.style.transition = 'all 0.3s ease';
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(20px)';
        setTimeout(function() { banner.remove(); }, 300);
    }
}
</script>

<style>
@keyframes nxSlideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
