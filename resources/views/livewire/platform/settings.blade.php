<div>
    @section('title', 'Platform Ayarları')
    @section('breadcrumb', 'Ayarlar')

    @if(session()->has('message'))
        <div style="padding: 12px 16px; background: var(--nx-badge-success-bg); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--nx-radius-md); color: var(--nx-success); font-size: 14px; margin-bottom: 20px;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        {{-- General Settings --}}
        <div class="nx-card" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Genel Ayarlar</h3>
            </div>
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Platform Adı</label>
                    <input type="text" wire:model="platformName" class="nx-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Destek E-postası</label>
                    <input type="email" wire:model="supportEmail" class="nx-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Varsayılan Para Birimi</label>
                    <select wire:model="defaultCurrency" class="nx-input" style="width: 100%;">
                        <option value="TRY">TRY (₺)</option>
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Trial & Registration --}}
        <div class="nx-card" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Kayıt & Deneme</h3>
            </div>
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Deneme Süresi (gün)</label>
                    <input type="number" wire:model="defaultTrialDays" class="nx-input" style="width: 100%;">
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" wire:model="registrationEnabled" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                        <span style="margin-left: 8px; font-size: 14px; color: var(--nx-text-primary);">Self-service kayıt açık</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Desktop Version Management --}}
        <div class="nx-card" style="padding: 0; grid-column: 1 / -1;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border); display: flex; align-items: center; gap: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#10b981">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z"/>
                </svg>
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Masaüstü Uygulama Sürüm Yönetimi</h3>
            </div>
            <div style="padding: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Güncel Sürüm</label>
                    <input type="text" wire:model="desktopLatestVersion" class="nx-input" style="width: 100%;" placeholder="1.0.0">
                    <p style="margin: 4px 0 0; font-size: 11px; color: var(--nx-text-muted);">Semver formatında: 1.0.0, 1.2.0, 2.0.0</p>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">İndirme Bağlantısı (URL)</label>
                    <input type="url" wire:model="desktopDownloadUrl" class="nx-input" style="width: 100%;" placeholder="https://drive.google.com/...">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--nx-text-secondary);">Sürüm Notları</label>
                    <textarea wire:model="desktopReleaseNotes" class="nx-textarea" rows="3" style="width: 100%;" placeholder="Bu sürümdeki yenilikler..."></textarea>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" wire:model="desktopUpdateMandatory" style="width: 18px; height: 18px; accent-color: #ef4444;">
                        <span style="margin-left: 8px; font-size: 14px; color: var(--nx-text-primary);">Zorunlu güncelleme</span>
                    </label>
                    <span style="font-size: 11px; color: var(--nx-text-muted);">Aktif edilirse kullanıcı kapatana kadar kalıcı bildirim görür</span>
                </div>
                <div style="grid-column: 1 / -1; padding: 14px 16px; background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(59,130,246,0.06)); border: 1px solid rgba(16,185,129,0.15); border-radius: var(--nx-radius-md); font-size: 12px; color: var(--nx-text-secondary);">
                    <strong>API Endpoint:</strong>
                    <code style="background: var(--nx-bg-tertiary); padding: 2px 6px; border-radius: 4px; font-size: 11px;">GET /api/desktop/version</code>
                    — Masaüstü uygulamaları bu endpoint'i kontrol ederek güncelleme olup olmadığını öğrenir.
                </div>
            </div>
        </div>
        {{-- Module Management --}}
        <div class="nx-card" style="padding: 0; grid-column: 1 / -1;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border); display: flex; align-items: center; gap: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8b5cf6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/>
                </svg>
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Modül Yönetimi</h3>
            </div>
            <div style="padding: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                
                {{-- Settings & Core --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Settings & Core</span>
                        <input type="checkbox" wire:model="cat_settings" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Müşteriler</span> <input type="checkbox" wire:model="mod_clients" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Departmanlar</span> <input type="checkbox" wire:model="mod_departments" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

                {{-- Project Management --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Project Management</span>
                        <input type="checkbox" wire:model="cat_project" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Projeler</span> <input type="checkbox" wire:model="mod_projects" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Görevler</span> <input type="checkbox" wire:model="mod_tasks" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Kanban</span> <input type="checkbox" wire:model="mod_kanban" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Timesheet</span> <input type="checkbox" wire:model="mod_timesheet" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Gantt</span> <input type="checkbox" wire:model="mod_gantt" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

                {{-- Marketing & Content --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Marketing & Content</span>
                        <input type="checkbox" wire:model="cat_marketing" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Kampanyalar</span> <input type="checkbox" wire:model="mod_campaigns" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>İçerikler</span> <input type="checkbox" wire:model="mod_content" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Sosyal Medya</span> <input type="checkbox" wire:model="mod_social" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

                {{-- Media & Insights --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Media & Insights</span>
                        <input type="checkbox" wire:model="cat_media" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">

                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Etkinlikler</span> <input type="checkbox" wire:model="mod_events" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Basın İletişimi</span> <input type="checkbox" wire:model="mod_press" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Takvim</span> <input type="checkbox" wire:model="mod_calendar" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

                {{-- Internal Tools --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Internal Tools</span>
                        <input type="checkbox" wire:model="cat_internal" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Gelen Kutusu</span> <input type="checkbox" wire:model="mod_inbox" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Araçlar</span> <input type="checkbox" wire:model="mod_tools" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Marka Varlıkları</span> <input type="checkbox" wire:model="mod_brand" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>E-posta Şablonları</span> <input type="checkbox" wire:model="mod_emails" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>E-posta Hesapları</span> <input type="checkbox" wire:model="mod_email_acc" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

                {{-- Sistem --}}
                <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 12px; cursor: pointer;">
                        <span>Sistem</span>
                        <input type="checkbox" wire:model="cat_system" style="width: 18px; height: 18px; accent-color: var(--nx-accent);">
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Otomasyonlar</span> <input type="checkbox" wire:model="mod_automations" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Entegrasyonlar</span> <input type="checkbox" wire:model="mod_integrations" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Teklif Motoru</span> <input type="checkbox" wire:model="mod_proposal" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Ekip</span> <input type="checkbox" wire:model="mod_team" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>2FA Güvenlik</span> <input type="checkbox" wire:model="mod_2fa" style="width: 16px; height: 16px;"></label>
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; cursor: pointer;"><span>Denetim Kaydı</span> <input type="checkbox" wire:model="mod_audit" style="width: 16px; height: 16px;"></label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
        <button wire:click="save" class="nx-btn nx-btn-primary">
            Kaydet
        </button>
    </div>
</div>
