@section('title', 'Workflow Builder')
@section('breadcrumb', 'Workflow Builder')

<div style="animation: fadeSlideIn 0.3s ease; opacity: 1;">
    {{-- Toast Alerts --}}
    @if(session()->has('message'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--nx-radius-md); color: #10b981; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                {{ session('message') }}
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 18px;">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--nx-radius-md); color: #ef4444; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
                {{ session('error') }}
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 18px;">&times;</button>
        </div>
    @endif

    {{-- Main Workspace --}}
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px; align-items: start;">
        
        {{-- Left Form Panel --}}
        <div class="nx-section" style="padding: 20px;">
            <div class="nx-section-header" style="margin-bottom: 20px; border-bottom: 1px solid var(--nx-border); padding-bottom: 12px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--nx-text-primary); display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent);">settings_suggest</span>
                    Akış Ayarları & Adımlar
                </h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: var(--nx-text-secondary); margin-bottom: 6px;">İŞ AKIŞI ADI</label>
                    <input type="text" wire:model.defer="name" class="nx-input" style="width: 100%;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: var(--nx-text-secondary); margin-bottom: 6px;">AÇIKLAMA</label>
                    <textarea wire:model.defer="description" class="nx-input" style="width: 100%; height: 60px;"></textarea>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--nx-border); margin: 8px 0;">

                {{-- Add Step Subsection --}}
                <div style="background: var(--nx-bg-elevated); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); padding: 14px;">
                    <h4 style="font-size: 12px; font-weight: 700; color: var(--nx-text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <span class="material-symbols-outlined" style="font-size: 16px; color: var(--nx-accent);">add_circle</span>
                        Yeni Adım Ekle
                    </h4>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <label style="display: block; font-size: 10px; font-weight: 600; color: var(--nx-text-secondary); margin-bottom: 4px;">ADIM ETİKETİ (örn: Tasarım Onayı)</label>
                            <input type="text" wire:model="newStepLabel" class="nx-input" style="width: 100%;" placeholder="Adım adı girin...">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="display: block; font-size: 10px; font-weight: 600; color: var(--nx-text-secondary); margin-bottom: 4px;">SORUMLU ROL</label>
                                <select wire:model="newStepRole" class="nx-input" style="width: 100%;">
                                    <option value="pm">Project Manager</option>
                                    <option value="designer">Designer</option>
                                    <option value="developer">Developer</option>
                                    <option value="client_contact">Müşteri Sorumlusu</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 10px; font-weight: 600; color: var(--nx-text-secondary); margin-bottom: 4px;">EYLEM TİPİ</label>
                                <select wire:model="newStepAction" class="nx-input" style="width: 100%;">
                                    <option value="create_task">Görev Oluştur</option>
                                    <option value="ai_analyze">Yapay Zekâ Analizi Yap</option>
                                    <option value="create_proposal">Teklif Taslağı Oluştur</option>
                                    <option value="schedule_reminder">Hatırlatıcı Planla</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" wire:click="addStep" class="nx-btn nx-btn-secondary" style="width: 100%; justify-content: center; font-size: 11px;">
                            Akışa Adım Ekle
                        </button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 12px;">
                    <a href="{{ route('admin.resource.index', 'workflows') }}" class="nx-btn nx-btn-secondary">Geri Dön</a>
                    <button type="button" wire:click="saveWorkflow" class="nx-btn nx-btn-primary">Akışı Kaydet</button>
                </div>
            </div>
        </div>

        {{-- Right Visual Canvas Panel --}}
        <div class="nx-section" style="padding: 20px; min-height: 480px; display: flex; flex-direction: column;">
            <div class="nx-section-header" style="margin-bottom: 20px; border-bottom: 1px solid var(--nx-border); padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--nx-text-primary); display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent);">schema</span>
                    Görsel Akış Şeması (SVG Preview)
                </h3>
                <span style="font-size: 10px; background: rgba(59,130,246,0.1); color: #3b82f6; padding: 2px 8px; border-radius: 12px; font-weight: 600;">
                    {{ count($steps) }} Adım Aktif
                </span>
            </div>

            {{-- Flow Canvas Container --}}
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: start; gap: 16px; padding: 20px; background: var(--nx-bg-elevated); border: 1px dashed var(--nx-border); border-radius: var(--nx-radius-lg); overflow-y: auto; max-height: 500px;">
                @forelse($steps as $index => $step)
                    {{-- Flow Node --}}
                    <div style="width: 100%; max-width: 360px; background: var(--nx-bg-input); border: 1px solid var(--nx-border); border-left: 4px solid var(--nx-accent); border-radius: var(--nx-radius-md); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--nx-bg-tertiary); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--nx-text-secondary); flex-shrink: 0;">
                                {{ $index + 1 }}
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $step['label'] }}
                                </div>
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 2px;">
                                    Rol: <strong style="color: var(--nx-text-primary);">{{ strtoupper($step['role']) }}</strong> | Eylem: <strong style="color: var(--nx-text-primary);">{{ str_replace('_', ' ', $step['action']) }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Node Controls --}}
                        <div style="display: flex; gap: 4px;">
                            <button type="button" wire:click="moveStepUp({{ $index }})" style="background: none; border: none; color: var(--nx-text-secondary); cursor: pointer;" title="Yukarı Taşı">
                                <span class="material-symbols-outlined" style="font-size: 18px;">keyboard_arrow_up</span>
                            </button>
                            <button type="button" wire:click="moveStepDown({{ $index }})" style="background: none; border: none; color: var(--nx-text-secondary); cursor: pointer;" title="Aşağı Taşı">
                                <span class="material-symbols-outlined" style="font-size: 18px;">keyboard_arrow_down</span>
                            </button>
                            <button type="button" wire:click="removeStep({{ $index }})" style="background: none; border: none; color: #ef4444; cursor: pointer;" title="Sil">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                            </button>
                        </div>
                    </div>

                    {{-- Connecting Line (if not last step) --}}
                    @if($index < count($steps) - 1)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--nx-border)" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                            <line x1="12" y1="0" x2="12" y2="24"></line>
                            <polyline points="8,16 12,20 16,16"></polyline>
                        </svg>
                    @endif
                @empty
                    <div style="text-align: center; color: var(--nx-text-secondary); margin-top: 60px;">
                        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--nx-border); margin-bottom: 12px;">route</span>
                        <p style="font-size: 12px;">Henüz bu iş akışına ait bir adım bulunmuyor.</p>
                        <p style="font-size: 11px; margin-top: 4px;">Soldaki panelden adımlar ekleyerek ilk akışınızı oluşturun.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
