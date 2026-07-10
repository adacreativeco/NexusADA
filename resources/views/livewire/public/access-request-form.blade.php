<div>
    {{-- ═══ ERKEN ERİŞİM MODAL ═══ --}}
    @if($showModal)
    <div class="ar-overlay" wire:click.self="closeModal" style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;padding:20px;">
        <div class="ar-modal" style="background:var(--bg-card, #111118);border:1px solid var(--border, rgba(255,255,255,0.08));border-radius:16px;padding:40px 36px;max-width:520px;width:100%;position:relative;max-height:90vh;overflow-y:auto;">
            {{-- Close button --}}
            <button wire:click="closeModal" style="position:absolute;top:16px;right:16px;background:none;border:none;color:var(--text-muted, #555);cursor:pointer;font-size:20px;line-height:1;">&times;</button>

            @if($submitted)
                {{-- Success State --}}
                <div style="text-align:center;padding:20px 0;">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(16,185,129,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3 style="font-size:1.3rem;font-weight:700;margin-bottom:8px;color:var(--text-primary, #f0f0f5);">Talebiniz Alındı!</h3>
                    <p style="font-size:14px;color:var(--text-secondary, #8888a0);line-height:1.6;margin-bottom:24px;">
                        Ekibimiz en kısa sürede sizinle iletişime geçecektir. Onay e-postanızı kontrol etmeyi unutmayın.
                    </p>
                    <button wire:click="closeModal" style="padding:10px 28px;border-radius:8px;background:var(--accent, #10b981);color:#fff;border:none;font-weight:600;font-size:14px;cursor:pointer;font-family:var(--font-ui, 'Inter', sans-serif);">Tamam</button>
                </div>
            @else
                {{-- Form --}}
                <div style="margin-bottom:24px;">
                    <div style="display:inline-flex;padding:4px 12px;border-radius:100px;background:rgba(16,185,129,0.12);color:#10b981;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;">
                        Erken Erişim
                    </div>
                    <h3 style="font-size:1.25rem;font-weight:700;color:var(--text-primary, #f0f0f5);margin-bottom:4px;">
                        {{ $plan_interest === 'enterprise' ? 'Kurumsal' : 'Profesyonel' }} Plan Talebi
                    </h3>
                    <p style="font-size:13px;color:var(--text-secondary, #8888a0);">Bilgilerinizi bırakın, ekibimiz sizinle iletişime geçsin.</p>
                </div>

                <form wire:submit.prevent="submit" style="display:flex;flex-direction:column;gap:14px;">
                    {{-- Name --}}
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">Ad Soyad *</label>
                        <input type="text" wire:model="name" placeholder="Adınız Soyadınız" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;" />
                        @error('name') <span style="font-size:12px;color:#ef4444;margin-top:2px;display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Company --}}
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">Şirket / Ajans Adı *</label>
                        <input type="text" wire:model="company_name" placeholder="Şirket adınız" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;" />
                        @error('company_name') <span style="font-size:12px;color:#ef4444;margin-top:2px;display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email + Phone row --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">E-posta *</label>
                            <input type="email" wire:model="email" placeholder="ornek@sirket.com" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;" />
                            @error('email') <span style="font-size:12px;color:#ef4444;margin-top:2px;display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">Telefon</label>
                            <input type="tel" wire:model="phone" placeholder="+90 5xx xxx xx xx" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;" />
                        </div>
                    </div>

                    {{-- Expected users --}}
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">Beklenen Kullanıcı Sayısı *</label>
                        <input type="number" wire:model="expected_users" min="1" max="10000" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;" />
                        @error('expected_users') <span style="font-size:12px;color:#ef4444;margin-top:2px;display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Use case --}}
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary, #8888a0);margin-bottom:4px;">Kullanım Amacınız</label>
                        <textarea wire:model="use_case" rows="3" placeholder="Platformu nasıl kullanmayı düşünüyorsunuz?" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.08));background:var(--bg-elevated, #1a1a24);color:var(--text-primary, #f0f0f5);font-size:14px;font-family:var(--font-ui, 'Inter', sans-serif);outline:none;resize:vertical;"></textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" wire:loading.attr="disabled" style="padding:12px;border-radius:8px;background:var(--accent, #10b981);color:#fff;border:none;font-weight:600;font-size:14px;cursor:pointer;font-family:var(--font-ui, 'Inter', sans-serif);transition:all 0.3s ease;margin-top:4px;">
                        <span wire:loading.remove>Erken Erişim Talep Et →</span>
                        <span wire:loading>Gönderiliyor...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>
