@section('title', 'İki Adımlı Doğrulama')
@section('breadcrumb', 'İki Adımlı Doğrulama')

<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">İki Adımlı Doğrulama (2FA)</h1>
            <p class="nx-page-subtitle">Hesabınızı bir kimlik doğrulama uygulaması (Google Authenticator, Authy, vb.) ile koruyun</p>
        </div>
    </div>

    <div class="nx-section" style="max-width: 600px;">
        <div class="nx-section-header"><h3 class="nx-section-title">Durum</h3></div>
        <div class="nx-section-body" style="padding: 24px;">
            @if($isEnabled)
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#10b981"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:15px;font-weight:600;color:#10b981;">2FA Aktif</div>
                        <div style="font-size:12px;color:var(--nx-text-secondary);">Hesabınız korunuyor.</div>
                    </div>
                </div>

                @if($showRecoveryCodes)
                    <div style="margin-top:24px; padding:16px; background:rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.2); border-radius:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <h4 style="font-size:14px; font-weight:600; color:var(--nx-warning); margin:0;">⚠️ Kurtarma Kodlarınız</h4>
                            <button wire:click="hideCodes" style="background:none; border:none; color:var(--nx-text-muted); font-size:11px; cursor:pointer;">Gizle</button>
                        </div>
                        <p style="font-size:12px; color:var(--nx-text-secondary); margin-bottom:12px;">Kalan {{ count($recoveryCodes) }} kodunuz aşağıdadır. Lütfen güvenli bir yerde saklayın.</p>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px;">
                            @foreach($recoveryCodes as $code)
                                <code style="font-size:11px; background:var(--nx-bg-tertiary); padding:4px 8px; border-radius:4px; font-family:var(--nx-font-mono);">{{ $code }}</code>
                            @endforeach
                        </div>
                        <button wire:click="regenerateCodes" wire:confirm="Mevcut tüm kurtarma kodlarını iptal edip yenilerini oluşturmak istediğinize emin misiniz?" style="font-size:11px; color:var(--nx-accent); background:none; border:none; cursor:pointer; padding:0; text-decoration:underline;">Yeni Kodlar Üret</button>
                    </div>
                @else
                    <button wire:click="showCodes" class="nx-btn nx-btn-secondary" style="font-size:12px; padding:6px 12px; margin-top:16px;">Kurtarma Kodlarını Görüntüle</button>
                @endif

                <div style="margin-top:24px; border-top:1px solid var(--nx-border); padding-top:20px;">
                    <button wire:click="disable" wire:confirm="2FA'yı devre dışı bırakmak istediğinize emin misiniz?" class="nx-btn nx-btn-secondary" style="font-size:13px;padding:8px 16px;color:#ef4444;">2FA'yı Devre Dışı Bırak</button>
                </div>
            @elseif($showSetup)
                <p style="font-size:13px;color:var(--nx-text-secondary);line-height:1.6;margin-bottom:16px;">Kimlik doğrulama uygulamanızla (Google Authenticator, Authy, Microsoft Authenticator vb.) aşağıdaki QR kodu tarayın.</p>
                <div style="background:white;border-radius:12px;padding:16px;display:inline-block;margin-bottom:16px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR" style="width:200px;height:200px;">
                </div>
                <div style="margin-bottom:16px;">
                    <p style="font-size:11px;color:var(--nx-text-secondary);margin-bottom:4px;">Manuel anahtar:</p>
                    <code style="font-family:var(--nx-font-mono);font-size:14px;color:var(--nx-accent);background:var(--nx-bg-tertiary);padding:8px 12px;border-radius:6px;display:inline-block;letter-spacing:2px;">{{ $secretKey }}</code>
                </div>

                <form wire:submit="verify" style="display:flex;flex-direction:column;gap:16px;max-width:450px;">
                    <div style="background:rgba(255,255,255,0.03); border:1px solid var(--nx-border); border-radius:12px; padding:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <h4 style="font-size:13px; font-weight:600; color:var(--nx-warning); margin:0;">🔐 Kurtarma Kodlarınız</h4>
                            <button type="button" 
                                    onclick="downloadRecoveryCodes()" 
                                    wire:click="markAsDownloaded"
                                    class="nx-btn nx-btn-secondary" 
                                    style="font-size:11px; padding:4px 10px; height:auto;">
                                💾 Kodları İndir (.txt)
                            </button>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                            @foreach($recoveryCodes as $code)
                                <code style="font-size:11px; background:var(--nx-bg-tertiary); padding:4px 8px; border-radius:4px; font-family:var(--nx-font-mono);">{{ $code }}</code>
                            @endforeach
                        </div>
                        <p style="font-size:11px; color:var(--nx-text-secondary); line-height:1.4;">Bu kodları güvenli bir yerde saklayın. Telefonunuzu kaybetmeniz durumunda hesabınıza erişmenin <strong>tek yolu</strong> budur.</p>
                    </div>

                    <div>
                        <label class="nx-label">6 Haneli Doğrulama Kodu</label>
                        <input type="text" wire:model="verifyCode" class="nx-input" placeholder="000000" maxlength="6" style="font-size:20px;text-align:center;letter-spacing:8px;font-family:var(--nx-font-mono);" autofocus>
                        @error('verifyCode') <span class="nx-error" style="display:block; margin-top:8px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="nx-btn" style="flex:1; font-size:13px;padding:10px 16px;background:var(--nx-accent);color:white;border:none;border-radius:8px;cursor:pointer;">
                            @if($forceConfirm) Kodları Kaydettim, Etkinleştir @else Doğrula ve Etkinleştir @endif
                        </button>
                        <button type="button" wire:click="cancelSetup" class="nx-btn nx-btn-secondary" style="font-size:13px;padding:10px 16px;">İptal</button>
                    </div>
                </form>
            @else
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(239,68,68,0.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.572-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:15px;font-weight:600;">2FA Devre Dışı</div>
                        <div style="font-size:12px;color:var(--nx-text-secondary);">Güvenliği artırmak için 2FA etkinleştirin.</div>
                    </div>
                </div>
                
                <div style="margin:20px 0; padding:16px; background:rgba(59,130,246,0.05); border-left:4px solid var(--nx-accent); border-radius:4px;">
                    <h5 style="font-size:13px; font-weight:600; color:var(--nx-accent); margin-bottom:8px;">Önemli Bilgilendirme</h5>
                    <p style="font-size:12px; color:var(--nx-text-secondary); line-height:1.5;">
                        İki adımlı doğrulama (2FA) etkinleştirildiğinde, giriş yapmak için şifrenizin yanı sıra telefonunuzdaki kimlik doğrulama uygulamasından alacağınız koda ihtiyacınız olacaktır. 
                        <strong>Telefonunuza erişiminizi kaybetmeniz durumunda hesabınıza erişmek için size özel üretilecek kurtarma kodlarını kullanmanız gerekecektir.</strong>
                    </p>
                </div>

                <button wire:click="startSetup" class="nx-btn" style="font-size:13px;padding:8px 16px;background:var(--nx-accent);color:white;border:none;border-radius:8px;cursor:pointer;">2FA Kurulumunu Başlat</button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function downloadRecoveryCodes() {
        const codes = @json($recoveryCodes);
        const text = "ADA CO-OS - 2FA KURTARMA KODLARI\n" + 
                     "Tarih: " + new Date().toLocaleString() + "\n" +
                     "----------------------------------\n\n" + 
                     codes.join("\n") + 
                     "\n\n----------------------------------\n" + 
                     "DİKKAT: Bu kodlar tek kullanımlıktır. Lütfen güvenli bir yerde saklayın.";
        
        const blob = new Blob([text], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const element = document.createElement('a');
        element.setAttribute('href', url);
        element.setAttribute('download', 'nexus-ada-kurtarma-kodlari.txt');
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
        window.URL.revokeObjectURL(url);
    }
</script>
@endpush
