<div>
    {{-- Breadcrumbs & Back link --}}
    <div style="margin-bottom: 20px;">
        <a href="/admin/works" style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: var(--nx-text-secondary); text-decoration: none; transition: color 0.15s;">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            İşler Listesine Dön
        </a>
    </div>

    {{-- Page Header --}}
    <div class="nx-page-header" style="align-items: flex-start; gap: 20px;">
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <span class="nx-badge nx-badge-{{ ['low'=>'gray','medium'=>'info','high'=>'warning','critical'=>'danger'][$work->priority] ?? 'gray' }}" style="font-size: 11px;">
                    {{ ['low'=>'Düşük','medium'=>'Orta','high'=>'Yüksek','critical'=>'Kritik'][$work->priority] ?? $work->priority }} Öncelik
                </span>
                <span style="font-size: 12px; color: var(--nx-text-secondary); font-weight: 500;">
                    Süreç: #{{ $work->id }}
                </span>
            </div>
            <h1 class="nx-page-title" style="font-size: 26px; font-weight: 700; color: var(--nx-text-primary); margin-bottom: 6px;">{{ $work->title }}</h1>
            <p class="nx-page-subtitle">{{ $work->client?->name ?? 'Müşterisiz' }} &bull; {{ $work->project?->title ?? 'Proje İlişkisi Yok' }}</p>
        </div>

        <div style="display: flex; gap: 8px;">
            <button class="nx-btn nx-btn-secondary" wire:click="openEditor">
                <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                İşi Düzenle
            </button>
        </div>
    </div>

    {{-- Status Horizontal Tracker Bar --}}
    <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        @php
            $statuses = [
                'lead' => 'Talep/Aday',
                'proposal' => 'Teklif',
                'contract' => 'Sözleşme',
                'in_progress' => 'Devam Eden',
                'completed' => 'Tamamlandı'
            ];
            $currentStatusIndex = array_search($work->status, array_keys($statuses));
        @endphp
        <div style="display: flex; align-items: center; gap: 8px; flex: 1; min-width: 300px;">
            @foreach(array_keys($statuses) as $index => $statusKey)
                @php
                    $isPastOrCurrent = $index <= $currentStatusIndex;
                    $isCurrent = $statusKey === $work->status;
                @endphp
                <div style="display: flex; align-items: center; flex: 1; position: relative;">
                    <button wire:click="changeStatus('{{ $statusKey }}')" 
                            style="border: none; background: none; cursor: pointer; padding: 0; display: flex; flex-direction: column; align-items: center; gap: 4px; z-index: 2; flex: 1; outline: none;">
                        <span style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;
                                     background: {{ $isPastOrCurrent ? 'var(--nx-accent)' : 'var(--nx-bg-tertiary)' }}; 
                                     color: {{ $isPastOrCurrent ? 'white' : 'var(--nx-text-secondary)' }};
                                     border: 2px solid {{ $isCurrent ? 'var(--nx-text-primary)' : 'transparent' }};
                                     transition: all 0.2s;">
                            @if($statusKey === 'completed' && $work->status === 'completed')
                                <span class="material-symbols-outlined" style="font-size: 14px; font-weight: bold;">check</span>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </span>
                        <span style="font-size: 11px; font-weight: {{ $isCurrent ? '600' : '500' }}; color: {{ $isCurrent ? 'var(--nx-text-primary)' : 'var(--nx-text-secondary)' }};">
                            {{ $statuses[$statusKey] }}
                        </span>
                    </button>
                    @if($index < count($statuses) - 1)
                        <div style="position: absolute; top: 12px; left: 50%; right: -50%; height: 2px; z-index: 1;
                                     background: {{ $index < $currentStatusIndex ? 'var(--nx-accent)' : 'var(--nx-border)' }};"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Main Layout Grid --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        {{-- Left Column: Timeline Stream & Creator Forms --}}
        <div>
            
            {{-- Creator Panel Bento Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                {{-- Form Selection Tabs --}}
                <div style="display: flex; gap: 8px; border-bottom: 1px solid var(--nx-border); padding-bottom: 12px; margin-bottom: 16px; overflow-x: auto;">
                    @foreach([
                        'note' => ['label' => 'Not Yaz', 'icon' => 'edit_note'],
                        'comment' => ['label' => 'Yorum Yap', 'icon' => 'comment'],
                        'task' => ['label' => 'Görev Planla', 'icon' => 'add_task'],
                        'event' => ['label' => 'Toplantı Ekle', 'icon' => 'calendar_today'],
                        'document' => ['label' => 'Dosya Yükle', 'icon' => 'cloud_upload']
                    ] as $formKey => $formMeta)
                        <button type="button" 
                                wire:click="$set('activeForm', '{{ $formKey }}')" 
                                style="border: none; background: none; cursor: pointer; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;
                                       background: {{ $activeForm === $formKey ? 'var(--nx-accent)' : 'transparent' }};
                                       color: {{ $activeForm === $formKey ? 'white' : 'var(--nx-text-secondary)' }};">
                            <span class="material-symbols-outlined" style="font-size: 16px;">{{ $formMeta['icon'] }}</span>
                            {{ $formMeta['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Tab Forms Contents --}}
                <div>
                    {{-- Form: Note --}}
                    @if($activeForm === 'note')
                        <form wire:submit.prevent="addNote">
                            <textarea wire:model="noteContent" class="nx-textarea" rows="3" placeholder="İş akışına özel notunuzu buraya yazın..." style="margin-bottom: 12px;"></textarea>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="nx-btn nx-btn-primary">Notu Ekle</button>
                            </div>
                        </form>
                    @endif

                    {{-- Form: Comment --}}
                    @if($activeForm === 'comment')
                        <form wire:submit.prevent="addComment">
                            <textarea wire:model="commentContent" class="nx-textarea" rows="3" placeholder="Görüş veya güncelleme yorumu bırakın..." style="margin-bottom: 12px;"></textarea>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="nx-btn nx-btn-primary">Gönder</button>
                            </div>
                        </form>
                    @endif

                    {{-- Form: Task --}}
                    @if($activeForm === 'task')
                        <form wire:submit.prevent="addTask">
                            <div class="nx-form-grid" style="grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                <div style="grid-column: span 1;">
                                    <input type="text" wire:model="taskTitle" class="nx-input" placeholder="Görev Başlığı" style="font-size: 13px;">
                                </div>
                                <div>
                                    <select wire:model="taskPriority" class="nx-select" style="font-size: 13px;">
                                        <option value="low">Düşük</option>
                                        <option value="medium">Orta</option>
                                        <option value="high">Yüksek</option>
                                        <option value="urgent">Acil</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="date" wire:model="taskDueDate" class="nx-input" style="font-size: 13px;">
                                </div>
                                <div>
                                    <select wire:model="taskAssignedTo" class="nx-select" style="font-size: 13px;">
                                        <option value="">Sorumlu Seç</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="nx-btn nx-btn-primary">Görevi Ata</button>
                            </div>
                        </form>
                    @endif

                    {{-- Form: Event --}}
                    @if($activeForm === 'event')
                        <form wire:submit.prevent="addEvent">
                            <div class="nx-form-grid" style="grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 12px;">
                                <div>
                                    <input type="text" wire:model="eventTitle" class="nx-input" placeholder="Toplantı Konusu / Başlığı" style="font-size: 13px;">
                                </div>
                                <div>
                                    <input type="datetime-local" wire:model="eventDate" class="nx-input" style="font-size: 13px;">
                                </div>
                                <div style="grid-column: span 2;">
                                    <textarea wire:model="eventDescription" class="nx-textarea" rows="2" placeholder="Gündem detayları..." style="font-size: 13px;"></textarea>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="nx-btn nx-btn-primary">Toplantıyı Ekle</button>
                            </div>
                        </form>
                    @endif

                    {{-- Form: Document --}}
                    @if($activeForm === 'document')
                        <form wire:submit.prevent="uploadDocument">
                            <div class="nx-form-grid" style="grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                <div>
                                    <input type="file" wire:model="docFile" class="nx-input" style="font-size: 13px;">
                                </div>
                                <div>
                                    <select wire:model="docCategory" class="nx-select" style="font-size: 13px;">
                                        <option value="brief">Brief / Talep</option>
                                        <option value="contract">Sözleşme</option>
                                        <option value="asset">Tasarım / Varlık</option>
                                        <option value="report">Rapor</option>
                                        <option value="other">Diğer</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="nx-btn nx-btn-primary">Dosyayı Yükle</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Timeline Stream list --}}
            <div style="position: relative; padding-left: 28px;">
                {{-- Timeline vertical connector --}}
                <div style="position: absolute; top: 8px; bottom: 8px; left: 10px; width: 2px; background: var(--nx-border); z-index: 1;"></div>

                @forelse($timeline as $item)
                    @php
                        $timestamp = $item->created_at ?? $item->updated_at;
                    @endphp
                    <div style="position: relative; margin-bottom: 24px; z-index: 2;">
                        
                        {{-- Timeline Icon Badge --}}
                        <div style="position: absolute; left: -28px; top: 0; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                                     background: var(--nx-bg-card); border: 2px solid var(--nx-border); box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--nx-text-secondary);">
                            @if($item instanceof \App\Models\Note)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #f59e0b;">edit_note</span>
                            @elseif($item instanceof \App\Models\Comment)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #3b82f6;">comment</span>
                            @elseif($item instanceof \App\Models\Task)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #10b981;">task_alt</span>
                            @elseif($item instanceof \App\Models\Event)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #8b5cf6;">calendar_today</span>
                            @elseif($item instanceof \App\Models\Document)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #ec4899;">cloud_upload</span>
                            @elseif($item instanceof \App\Models\Proposal)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #10b981;">description</span>
                            @elseif($item instanceof \App\Models\Contract)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #6366f1;">handshake</span>
                            @elseif($item instanceof \App\Models\Income)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #3b82f6;">trending_up</span>
                            @elseif($item instanceof \App\Models\Collection)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: #10b981;">price_check</span>
                            @elseif($item instanceof \OwenIt\Auditing\Models\Audit)
                                <span class="material-symbols-outlined" style="font-size: 13px; color: var(--nx-text-muted);">history</span>
                            @endif
                        </div>

                        {{-- Card Box --}}
                        <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 8px; padding: 12px 16px; margin-left: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            
                            {{-- Header metadata --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if($item instanceof \App\Models\Note)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Süreç Notu</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->user?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Comment)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Yorum</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->user?->name ?? 'Bilinmeyen' }}</span>
                                    @elseif($item instanceof \App\Models\Task)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Görev Ataması</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->creator?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Event)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Toplantı / Etkinlik</span>
                                    @elseif($item instanceof \App\Models\Document)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Dosya Paylaşımı</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->uploader?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Proposal)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Fiyat Teklifi</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->creator?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Contract)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Hizmet Sözleşmesi</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->creator?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Income)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Gelir Girişi / Fatura</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->creator?->name ?? 'Sistem' }}</span>
                                    @elseif($item instanceof \App\Models\Collection)
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">Ödeme / Tahsilat</span>
                                    @elseif($item instanceof \OwenIt\Auditing\Models\Audit)
                                        <span style="font-size: 11px; font-weight: 600; color: var(--nx-text-muted);">Sistem Güncellemesi</span>
                                        <span style="font-size: 11px; color: var(--nx-text-secondary);">&bull; {{ $item->user?->name ?? 'Sistem' }}</span>
                                    @endif
                                </div>
                                <span style="font-size: 11px; color: var(--nx-text-secondary);">
                                    {{ $timestamp ? $timestamp->diffForHumans() : '' }}
                                </span>
                            </div>

                            {{-- Card Contents --}}
                            <div>
                                @if($item instanceof \App\Models\Note)
                                    <div style="font-size: 13px; color: var(--nx-text-primary); background: rgba(245,158,11,0.05); border-left: 3px solid #f59e0b; padding: 8px 12px; border-radius: 4px; white-space: pre-wrap;">
                                        {{ $item->content }}
                                    </div>
                                @elseif($item instanceof \App\Models\Comment)
                                    <div style="font-size: 13px; color: var(--nx-text-primary); white-space: pre-wrap;">
                                        {{ $item->content }}
                                    </div>
                                @elseif($item instanceof \App\Models\Task)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div>
                                            <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->title }}</div>
                                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                                <span class="nx-badge nx-badge-{{ ['low'=>'gray','medium'=>'info','high'=>'warning','urgent'=>'danger'][$item->priority] ?? 'gray' }}" style="font-size: 9px; padding: 1px 6px;">
                                                    {{ ['low'=>'Düşük','medium'=>'Orta','high'=>'Yüksek','urgent'=>'Acil'][$item->priority] ?? $item->priority }}
                                                </span>
                                                @if($item->due_date)
                                                    <span style="font-size: 10px; color: var(--nx-text-secondary); display: inline-flex; align-items: center; gap: 2px;">
                                                        <span class="material-symbols-outlined" style="font-size: 12px;">calendar_today</span>
                                                        Vade: {{ $item->due_date->format('d.m.Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            @if($item->assignee)
                                                <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;" title="Sorumlu: {{ $item->assignee->name }}">
                                                    {{ strtoupper(substr($item->assignee->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="nx-badge nx-badge-{{ $item->status === 'done' ? 'success' : 'warning' }}" style="font-size: 10px;">
                                                {{ $item->status === 'done' ? 'Tamamlandı' : 'Bekliyor' }}
                                            </span>
                                        </div>
                                    </div>
                                @elseif($item instanceof \App\Models\Event)
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="background: rgba(139,92,246,0.1); border-radius: 6px; padding: 8px 12px; text-align: center; min-width: 60px;">
                                            <span style="display: block; font-size: 10px; text-transform: uppercase; color: #8b5cf6; font-weight: 700;">Toplantı</span>
                                            <span style="display: block; font-size: 14px; font-weight: 700; color: var(--nx-text-primary);">
                                                {{ $item->start_time ? now()->parse($item->start_time)->format('H:i') : '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->title }}</div>
                                            @if($item->description)
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">{{ $item->description }}</div>
                                            @endif
                                            <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 4px; display: inline-flex; align-items: center; gap: 2px;">
                                                <span class="material-symbols-outlined" style="font-size: 12px;">schedule</span>
                                                Tarih: {{ $item->start_time ? now()->parse($item->start_time)->format('d.m.Y') : '' }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item instanceof \App\Models\Document)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="material-symbols-outlined" style="font-size: 24px; color: #ec4899;">description</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->name }}</div>
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                                    Kategori: <span style="text-transform: capitalize;">{{ $item->category }}</span> &bull; Boyut: {{ $item->human_size }}
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $item->path) }}" target="_blank" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">download</span>
                                            İndir
                                        </a>
                                    </div>
                                @elseif($item instanceof \App\Models\Proposal)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="material-symbols-outlined" style="font-size: 24px; color: #10b981;">request_quote</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->title }}</div>
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                                    Teklif No: <strong>{{ $item->proposal_number }}</strong> &bull; Toplam: <strong>{{ number_format($item->grand_total, 2, ',', '.') }} {{ $item->currency }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="nx-badge nx-badge-{{ ['draft'=>'gray','pending_approval'=>'warning','approved_internal'=>'success','rejected_internal'=>'danger','sent'=>'info','accepted'=>'success','declined'=>'danger'][$item->status] ?? 'gray' }}" style="font-size: 10px;">
                                                {{ ['draft'=>'Taslak','pending_approval'=>'Onay Bekliyor','approved_internal'=>'Onaylandı','rejected_internal'=>'Reddedildi','sent'=>'Gönderildi','accepted'=>'Kabul Edildi','declined'=>'Reddedildi (Müşteri)'][$item->status] ?? $item->status }}
                                            </span>
                                            <a href="{{ route('admin.report.proposal', $item->id) }}" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">download</span>
                                                PDF
                                            </a>
                                        </div>
                                    </div>
                                @elseif($item instanceof \App\Models\Contract)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="material-symbols-outlined" style="font-size: 24px; color: #6366f1;">history_edu</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->title }}</div>
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                                    Sözleşme No: <strong>{{ $item->contract_number }}</strong> &bull; Değer: <strong>{{ number_format($item->value, 2, ',', '.') }} {{ $item->currency }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="nx-badge nx-badge-{{ ['draft'=>'gray','pending_approval'=>'warning','active'=>'success','rejected_internal'=>'danger','expired'=>'gray','terminated'=>'danger'][$item->status] ?? 'gray' }}" style="font-size: 10px;">
                                                {{ ['draft'=>'Taslak','pending_approval'=>'Onay Bekliyor','active'=>'Aktif','rejected_internal'=>'Reddedildi','expired'=>'Süresi Doldu','terminated'=>'Feshedildi'][$item->status] ?? $item->status }}
                                            </span>
                                            <a href="{{ route('admin.report.contract', $item->id) }}" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">download</span>
                                                PDF
                                            </a>
                                        </div>
                                    </div>
                                @elseif($item instanceof \App\Models\Income)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="material-symbols-outlined" style="font-size: 24px; color: #3b82f6;">receipt_long</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $item->title }}</div>
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                                    Fatura No: <strong>{{ $item->income_number }}</strong> &bull; Tutar: <strong>{{ number_format($item->grand_total, 2, ',', '.') }} {{ $item->currency }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="nx-badge nx-badge-{{ ['draft'=>'gray','sent'=>'info','paid'=>'success','cancelled'=>'danger','overdue'=>'danger'][$item->status] ?? 'gray' }}" style="font-size: 10px;">
                                                {{ ['draft'=>'Taslak','sent'=>'Gönderildi','paid'=>'Ödendi','cancelled'=>'İptal','overdue'=>'Gecikmiş'][$item->status] ?? $item->status }}
                                            </span>
                                        </div>
                                    </div>
                                @elseif($item instanceof \App\Models\Collection)
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span class="material-symbols-outlined" style="font-size: 24px; color: #10b981;">savings</span>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">Tahsilat Gerçekleşti</div>
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                                    Tutar: <strong>{{ number_format($item->amount, 2, ',', '.') }} {{ $item->currency }}</strong> &bull; Yöntem: {{ [
                                                        'bank_transfer' => 'Havale / EFT',
                                                        'credit_card' => 'Kredi Kartı',
                                                        'cash' => 'Nakit',
                                                        'check' => 'Çek / Senet',
                                                    ][$item->payment_method] ?? $item->payment_method }}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="nx-badge nx-badge-success" style="font-size: 10px;">ÖDENDİ</span>
                                        </div>
                                    </div>
                                @elseif($item instanceof \OwenIt\Auditing\Models\Audit)
                                    @php
                                        $old = $item->old_values;
                                        $new = $item->new_values;
                                    @endphp
                                    <div style="font-size: 12px; color: var(--nx-text-secondary);">
                                        Süreç üzerinde değişiklik yapıldı:
                                        <ul style="margin: 4px 0 0 16px; padding: 0; list-style-type: disc;">
                                            @foreach($new as $field => $val)
                                                @php
                                                    $oldVal = $old[$field] ?? 'Boş';
                                                    // Map statuses if possible
                                                    if ($field === 'status') {
                                                        $map = ['lead'=>'Talep','proposal'=>'Teklif','contract'=>'Sözleşme','in_progress'=>'Devam Eden','completed'=>'Tamamlandı'];
                                                        $val = $map[$val] ?? $val;
                                                        $oldVal = $map[$oldVal] ?? $oldVal;
                                                    }
                                                    if ($field === 'priority') {
                                                        $map = ['low'=>'Düşük','medium'=>'Orta','high'=>'Yüksek','critical'=>'Kritik'];
                                                        $val = $map[$val] ?? $val;
                                                        $oldVal = $map[$oldVal] ?? $oldVal;
                                                    }
                                                @endphp
                                                <li>
                                                    <strong style="text-transform: capitalize;">{{ $field }}</strong>: 
                                                    <span style="text-decoration: line-through; opacity: 0.6;">{{ is_array($oldVal) ? json_encode($oldVal) : $oldVal }}</span> &rarr; 
                                                    <span style="color: var(--nx-accent); font-weight: 600;">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 40px; text-align: center;">
                        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--nx-text-secondary); opacity: 0.5; margin-bottom: 12px;">timeline</span>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px;">Timeline Boş</h3>
                        <p style="font-size: 13px; color: var(--nx-text-secondary);">Yukarıdaki paneli kullanarak ilk notunuzu, dosyanızı veya görevinizi ekleyin.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column: Metrics & Information Bento Cards --}}
        <div>
            {{-- Client & Project Info Bento Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; color: var(--nx-accent);">business</span>
                    İlişkili Bağlantılar
                </h3>
                
                {{-- Client --}}
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; color: var(--nx-text-secondary); font-weight: 600; text-transform: uppercase;">Müşteri</div>
                    @if($work->client)
                        <a href="/admin/clients?search={{ urlencode($work->client->name) }}" style="font-size: 13px; font-weight: 600; color: var(--nx-accent); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                            {{ $work->client->name }}
                            <span class="material-symbols-outlined" style="font-size: 14px;">open_in_new</span>
                        </a>
                    @else
                        <div style="font-size: 13px; color: var(--nx-text-secondary); margin-top: 4px;">Atanmadı</div>
                    @endif
                </div>

                {{-- Project --}}
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; color: var(--nx-text-secondary); font-weight: 600; text-transform: uppercase;">Proje</div>
                    @if($work->project)
                        <a href="/admin/projects?search={{ urlencode($work->project->title) }}" style="font-size: 13px; font-weight: 600; color: var(--nx-accent); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                            {{ $work->project->title }}
                            <span class="material-symbols-outlined" style="font-size: 14px;">open_in_new</span>
                        </a>
                    @else
                        <div style="font-size: 13px; color: var(--nx-text-secondary); margin-top: 4px;">İlişkili Proje Yok</div>
                    @endif
                </div>

                {{-- Assignee --}}
                <div>
                    <div style="font-size: 11px; color: var(--nx-text-secondary); font-weight: 600; text-transform: uppercase;">Süreç Sahibi / Sorumlu</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px;">
                        @if($work->assignee)
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700;">
                                {{ strtoupper(substr($work->assignee->name, 0, 1)) }}
                            </div>
                            <span style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary);">{{ $work->assignee->name }}</span>
                        @else
                            <span style="font-size: 13px; color: var(--nx-text-secondary);">Atanmadı</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Financial Value Bento Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px; margin-bottom: 24px; border-left: 4px solid var(--nx-accent);">
                <div style="font-size: 12px; color: var(--nx-text-secondary); font-weight: 600; text-transform: uppercase;">İş / Süreç Değeri</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--nx-text-primary); margin-top: 6px; display: flex; align-items: baseline; gap: 4px;">
                    @php
                        $currencySymbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                        $sym = $currencySymbols[$work->currency] ?? '₺';
                    @endphp
                    <span style="color: var(--nx-accent);">{{ $sym }}</span>{{ number_format($work->value, 2, ',', '.') }}
                </div>
            </div>

            {{-- Summary Stats Bento Card --}}
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 16px;">Süreç Sağlığı & İstatistikler</h3>
                
                @php
                    $totalTasks = $work->tasks()->count();
                    $completedTasks = $work->tasks()->where('status', 'done')->count();
                    $taskPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    $docsCount = $work->documents()->count();
                    $eventsCount = $work->events()->count();
                @endphp

                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--nx-text-secondary); margin-bottom: 6px;">
                        <span>Görev İlerlemesi</span>
                        <span style="font-weight: 700; color: var(--nx-text-primary);">{{ $completedTasks }}/{{ $totalTasks }} ({{ $taskPercent }}%)</span>
                    </div>
                    <div style="height: 6px; background: var(--nx-bg-tertiary); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; background: var(--nx-accent); width: {{ $taskPercent }}%; transition: width 0.3s;"></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: var(--nx-bg-secondary); border: 1px solid var(--nx-border); border-radius: 8px; padding: 12px; text-align: center;">
                        <span class="material-symbols-outlined" style="color: #ec4899; font-size: 20px; margin-bottom: 4px;">description</span>
                        <div style="font-size: 18px; font-weight: 700; color: var(--nx-text-primary);">{{ $docsCount }}</div>
                        <div style="font-size: 10px; color: var(--nx-text-secondary); font-weight: 600;">DOSYALAR</div>
                    </div>
                    <div style="background: var(--nx-bg-secondary); border: 1px solid var(--nx-border); border-radius: 8px; padding: 12px; text-align: center;">
                        <span class="material-symbols-outlined" style="color: #8b5cf6; font-size: 20px; margin-bottom: 4px;">calendar_today</span>
                        <div style="font-size: 18px; font-weight: 700; color: var(--nx-text-primary);">{{ $eventsCount }}</div>
                        <div style="font-size: 10px; color: var(--nx-text-secondary); font-weight: 600;">TOPLANTILAR</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Info Edit Slideover --}}
    @if($editorOpen)
        <div class="nx-slide-over-backdrop" wire:click="closeEditor"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">Süreç Bilgilerini Güncelle</h2>
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
                                <input type="text" wire:model="editorData.title" class="nx-input">
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
                                <textarea wire:model="editorData.description" class="nx-textarea" rows="3"></textarea>
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
                                <label class="nx-label">Değer</label>
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
                                <label class="nx-label">Atanan Sorumlu</label>
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
            <div class="nx-slide-over-footer" style="display: flex; justify-content: flex-end; gap: 8px;">
                <button wire:click="closeEditor" class="nx-btn nx-btn-secondary" type="button">İptal</button>
                <button wire:click="saveWork" class="nx-btn nx-btn-primary" type="button">Kaydet</button>
            </div>
        </div>
    @endif
</div>
