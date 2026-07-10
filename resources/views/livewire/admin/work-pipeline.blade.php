<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">İş Pipeline</h1>
            <p class="nx-page-subtitle">İş süreçlerini sürükle-bırak yöntemi ile yönetin ve izleyin</p>
        </div>
        <button class="nx-btn nx-btn-primary" wire:click="openEditor()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Yeni İş Başlat
        </button>
    </div>

    {{-- Filters --}}
    <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
        <select wire:model.live="filterClient" class="nx-select" style="width: auto; min-width: 140px; padding: 6px 32px 6px 10px; font-size: 12px;">
            <option value="">Tüm Müşteriler</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPriority" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
            <option value="">Tüm Öncelikler</option>
            <option value="critical">Kritik</option>
            <option value="high">Yüksek</option>
            <option value="medium">Orta</option>
            <option value="low">Düşük</option>
        </select>
    </div>

    {{-- Pipeline Columns --}}
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; min-height: 600px;">
        @foreach($columns as $status => $column)
            <div class="kanban-column"
                 data-status="{{ $status }}"
                 ondragover="event.preventDefault(); this.classList.add('kanban-drag-over');"
                 ondragleave="this.classList.remove('kanban-drag-over');"
                 ondrop="this.classList.remove('kanban-drag-over'); const workId = event.dataTransfer.getData('work_id'); if(workId) { @this.call('moveWork', parseInt(workId), '{{ $status }}'); }"
                 style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 8px; padding: 0; display: flex; flex-direction: column;">

                {{-- Column Header --}}
                <div style="padding: 12px 14px; border-bottom: 1px solid var(--nx-border); display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $column['color'] }};"></span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $column['label'] }}</span>
                    </div>
                    <span style="font-size: 11px; background: var(--nx-bg-tertiary); color: var(--nx-text-secondary); padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                        {{ $column['items']->count() }}
                    </span>
                </div>

                {{-- Cards --}}
                <div style="padding: 8px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                    @forelse($column['items'] as $work)
                        <div class="kanban-card"
                             draggable="true"
                             ondragstart="event.dataTransfer.setData('work_id', '{{ $work->id }}'); this.classList.add('kanban-dragging');"
                             ondragend="this.classList.remove('kanban-dragging');"
                             wire:click="openEditor({{ $work->id }})"
                             wire:key="work-{{ $work->id }}"
                             style="background: var(--nx-bg-secondary); border: 1px solid var(--nx-border); border-radius: 6px; padding: 10px 12px; cursor: grab;
                                    border-left: 3px solid {{ ['critical'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#6b7280'][$work->priority] ?? '#6b7280' }};
                                    transition: transform 0.15s, box-shadow 0.15s;">

                            {{-- Title --}}
                            <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px; line-height: 1.4;">
                                {{ $work->title }}
                            </div>

                            {{-- Client Name --}}
                            <div style="font-size: 11px; color: var(--nx-text-secondary); margin-bottom: 8px; font-weight: 500;">
                                {{ $work->client?->name ?? 'Müşterisiz' }}
                            </div>

                            {{-- Meta Row --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-bottom: 8px;">
                                @php
                                    $pColors = ['critical'=>'danger','high'=>'warning','medium'=>'info','low'=>'gray'];
                                    $pLabels = ['critical'=>'Kritik','high'=>'Yüksek','medium'=>'Orta','low'=>'Düşük'];
                                    $currencySymbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                                    $sym = $currencySymbols[$work->currency] ?? '₺';
                                @endphp
                                <span class="nx-badge nx-badge-{{ $pColors[$work->priority] ?? 'gray' }}" style="font-size: 9px; padding: 1px 6px;">
                                    {{ $pLabels[$work->priority] ?? $work->priority }}
                                </span>

                                @if($work->value > 0)
                                    <span style="font-size: 11px; font-weight: 600; color: var(--nx-accent);">
                                        {{ $sym }}{{ number_format($work->value, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Progress Timeline link / Assignee --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--nx-border); padding-top: 8px; margin-top: 4px;">
                                <a href="/admin/works/{{ $work->id }}/timeline" 
                                   onclick="event.stopPropagation();" 
                                   style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; color: var(--nx-text-secondary); hover:color: var(--nx-accent); text-decoration: none;">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">timeline</span>
                                    Timeline
                                </a>

                                @if($work->assignee)
                                    <div style="width: 18px; height: 18px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 700;" title="{{ $work->assignee->name }}">
                                        {{ strtoupper(substr($work->assignee->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px 12px; border: 2px dashed var(--nx-border); border-radius: 8px; background: rgba(0,0,0,0.1); color: var(--nx-text-muted); text-align: center; gap: 8px; transition: all 0.2s;">
                            <span class="material-symbols-outlined" style="opacity: 0.5;">drag_indicator</span>
                            <span style="font-size: 11px; font-weight: 500;">İş sürükleyin</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Editor Slide-Over --}}
    @if($editorOpen)
        <div class="nx-slide-over-backdrop" wire:click="closeEditor"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">{{ $editingId ? 'İş Sürecini Düzenle' : 'Yeni İş Başlat' }}</h2>
                <button wire:click="closeEditor" class="nx-btn-icon">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="nx-slide-over-body">
                <div class="nx-section">
                    <div class="nx-section-body">
                        <div class="nx-form-grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="nx-form-group" style="grid-column: span 2;">
                                <label class="nx-label">İş Başlığı <span class="required">*</span></label>
                                <input type="text" wire:model="editorData.title" class="nx-input" placeholder="Örn: E-Ticaret Kampanya Tasarımı">
                            </div>
                            
                            <div class="nx-form-group">
                                <label class="nx-label">Müşteri <span class="required">*</span></label>
                                <select wire:model="editorData.client_id" class="nx-select">
                                    <option value="">Seçin</option>
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Proje (Opsiyonel)</label>
                                <select wire:model="editorData.project_id" class="nx-select">
                                    <option value="">Seçin</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="nx-form-group" style="grid-column: span 2;">
                                <label class="nx-label">Açıklama</label>
                                <textarea wire:model="editorData.description" class="nx-textarea" rows="3" placeholder="Süreç gereksinimleri ve detaylar..."></textarea>
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Durum</label>
                                <select wire:model="editorData.status" class="nx-select">
                                    <option value="lead">Talep/Aday</option>
                                    <option value="proposal">Teklif</option>
                                    <option value="contract">Sözleşme</option>
                                    <option value="in_progress">Devam Eden</option>
                                    <option value="completed">Tamamlandı</option>
                                </select>
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Öncelik</label>
                                <select wire:model="editorData.priority" class="nx-select">
                                    <option value="low">Düşük</option>
                                    <option value="medium">Orta</option>
                                    <option value="high">Yüksek</option>
                                    <option value="critical">Kritik</option>
                                </select>
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Sözleşme / İş Değeri</label>
                                <input type="number" wire:model="editorData.value" class="nx-input">
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Para Birimi</label>
                                <select wire:model="editorData.currency" class="nx-select">
                                    <option value="TRY">TRY (₺)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Başlangıç Tarihi</label>
                                <input type="date" wire:model="editorData.started_at" class="nx-input">
                            </div>

                            <div class="nx-form-group">
                                <label class="nx-label">Bitiş Tarihi</label>
                                <input type="date" wire:model="editorData.due_at" class="nx-input">
                            </div>

                            <div class="nx-form-group" style="grid-column: span 2;">
                                <label class="nx-label">Sorumlu</label>
                                <select wire:model="editorData.assigned_to" class="nx-select">
                                    <option value="">Atanmadı</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nx-slide-over-footer">
                <div style="display: flex; justify-content: space-between; width: 100%;">
                    <div>
                        @if($editingId)
                            <button wire:click="confirmDelete({{ $editingId }})" class="nx-btn nx-btn-danger" type="button">Sil</button>
                        @endif
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button wire:click="closeEditor" class="nx-btn nx-btn-secondary" type="button">İptal</button>
                        <button wire:click="saveWork" class="nx-btn nx-btn-primary" type="button">Kaydet</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($confirmingDeleteId)
        <div class="nx-modal-backdrop"></div>
        <div class="nx-modal">
            <div class="nx-modal-header">
                <h3 class="nx-modal-title">İşi Sil</h3>
            </div>
            <div class="nx-modal-body">
                <p>Bu iş sürecini silmek istediğinize emin misiniz? Bu işlem geri alınamaz.</p>
            </div>
            <div class="nx-modal-footer">
                <button wire:click="cancelDelete" class="nx-btn nx-btn-secondary">İptal</button>
                <button wire:click="deleteWork" class="nx-btn nx-btn-danger">Sil</button>
            </div>
        </div>
    @endif
</div>
