<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">Kanban Board</h1>
            <p class="nx-page-subtitle">Görevleri sürükle-bırak ile yönet</p>
        </div>
        <button class="nx-btn nx-btn-primary" wire:click="openEditor()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Görev Ekle
        </button>
    </div>

    {{-- Filters --}}
    <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
        <select wire:model.live="filterProject" class="nx-select" style="width: auto; min-width: 140px; padding: 6px 32px 6px 10px; font-size: 12px;">
            <option value="">Tüm Projeler</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterAssignee" class="nx-select" style="width: auto; min-width: 140px; padding: 6px 32px 6px 10px; font-size: 12px;">
            <option value="">Tüm Kişiler</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPriority" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
            <option value="">Tüm Öncelikler</option>
            <option value="urgent">Acil</option>
            <option value="high">Yüksek</option>
            <option value="medium">Orta</option>
            <option value="low">Düşük</option>
        </select>
    </div>

    {{-- Kanban Columns --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; min-height: 500px;">
        @foreach($columns as $status => $column)
            <div class="kanban-column"
                 data-status="{{ $status }}"
                 ondragover="event.preventDefault(); this.classList.add('kanban-drag-over');"
                 ondragleave="this.classList.remove('kanban-drag-over');"
                 ondrop="this.classList.remove('kanban-drag-over'); const taskId = event.dataTransfer.getData('task_id'); if(taskId) { @this.call('moveTask', parseInt(taskId), '{{ $status }}'); }"
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
                    @forelse($column['items'] as $task)
                        <div class="kanban-card"
                             draggable="true"
                             ondragstart="event.dataTransfer.setData('task_id', '{{ $task->id }}'); this.classList.add('kanban-dragging');"
                             ondragend="this.classList.remove('kanban-dragging');"
                             wire:click="openEditor({{ $task->id }})"
                             wire:key="task-{{ $task->id }}"
                             style="background: var(--nx-bg-secondary); border: 1px solid var(--nx-border); border-radius: 6px; padding: 10px 12px; cursor: grab;
                                    border-left: 3px solid {{ ['urgent'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#6b7280'][$task->priority] ?? '#6b7280' }};
                                    transition: transform 0.15s, box-shadow 0.15s;">

                            {{-- Title --}}
                            <div style="font-size: 13px; font-weight: 500; color: var(--nx-text-primary); margin-bottom: 6px; line-height: 1.4;">
                                {{ $task->title }}
                            </div>

                            {{-- Meta Row --}}
                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                @php
                                    $pColors = ['urgent'=>'danger','high'=>'warning','medium'=>'info','low'=>'gray'];
                                    $pLabels = ['urgent'=>'Acil','high'=>'Yüksek','medium'=>'Orta','low'=>'Düşük'];
                                @endphp
                                <span class="nx-badge nx-badge-{{ $pColors[$task->priority] ?? 'gray' }}" style="font-size: 9px; padding: 1px 6px;">
                                    {{ $pLabels[$task->priority] ?? $task->priority }}
                                </span>

                                @if($task->due_date)
                                    <span style="font-size: 10px; color: {{ $task->due_date->isPast() && $status !== 'done' ? 'var(--nx-danger)' : 'var(--nx-text-secondary)' }}; display: inline-flex; align-items: center; gap: 2px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                        </svg>
                                        {{ $task->due_date->format('d.m') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Assignee + Project --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
                                @if($task->assignee)
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <div style="width: 18px; height: 18px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 700;">
                                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                        </div>
                                        <span style="font-size: 10px; color: var(--nx-text-secondary);">{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span style="font-size: 10px; color: var(--nx-text-tertiary);">Atanmadı</span>
                                @endif

                                @if($task->project)
                                    <span style="font-size: 9px; color: var(--nx-text-secondary); background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 3px;">
                                        {{ \Illuminate\Support\Str::limit($task->project->name, 12) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px 12px; border: 2px dashed var(--nx-border); border-radius: 8px; background: rgba(0,0,0,0.1); color: var(--nx-text-muted); text-align: center; gap: 8px; transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span style="font-size: 12px; font-weight: 500;">Görev sürükleyin</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Task Editor Slide-Over --}}
    @if($editorOpen)
        <div class="nx-slide-over-backdrop" wire:click="closeEditor"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">{{ $editingId ? 'Görevi Düzenle' : 'Yeni Görev' }}</h2>
                <button wire:click="closeEditor" class="nx-btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="nx-slide-over-body">
                <div class="nx-section">
                    <div class="nx-section-body">
                        <div class="nx-form-grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="nx-form-group" style="grid-column: span 2;">
                                <label class="nx-label">Başlık <span class="required">*</span></label>
                                <input type="text" wire:model="editorData.title" class="nx-input">
                            </div>
                            <div class="nx-form-group" style="grid-column: span 2;">
                                <label class="nx-label">Açıklama</label>
                                <textarea wire:model="editorData.description" class="nx-textarea" rows="3"></textarea>
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Durum</label>
                                <select wire:model="editorData.status" class="nx-select">
                                    <option value="todo">Yapılacak</option>
                                    <option value="in_progress">Devam Ediyor</option>
                                    <option value="review">İnceleme</option>
                                    <option value="done">Tamamlandı</option>
                                </select>
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Öncelik</label>
                                <select wire:model="editorData.priority" class="nx-select">
                                    <option value="low">Düşük</option>
                                    <option value="medium">Orta</option>
                                    <option value="high">Yüksek</option>
                                    <option value="urgent">Acil</option>
                                </select>
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Atanan Kişi</label>
                                <select wire:model="editorData.assigned_to" class="nx-select">
                                    <option value="">Seçiniz</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Proje</label>
                                <select wire:model="editorData.project_id" class="nx-select">
                                    <option value="">Seçiniz</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Son Tarih</label>
                                <input type="date" wire:model="editorData.due_date" class="nx-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nx-slide-over-footer">
                @if($editingId)
                    <button wire:click="confirmDelete({{ $editingId }})" class="nx-btn nx-btn-danger" style="margin-right: auto;">Sil</button>
                @endif
                <button wire:click="closeEditor" class="nx-btn nx-btn-secondary">İptal</button>
                <button wire:click="saveTask" class="nx-btn nx-btn-primary">Kaydet</button>
            </div>
        </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($confirmingDeleteId)
    <div style="position: fixed; inset: 0; z-index: 9998; display: flex; align-items: center; justify-content: center;">
        <div wire:click="cancelDelete" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"></div>
        <div style="position: relative; background: var(--nx-bg-card, #1e1e2e); border: 1px solid var(--nx-border, #2e2e3e); border-radius: 14px; padding: 28px 32px; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--nx-text, #e2e8f0);">Silme Onayı</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--nx-text-muted, #94a3b8);">Bu görevi silmek istediğinize emin misiniz?</p>
                </div>
            </div>
            <p style="font-size: 12px; color: var(--nx-text-muted, #94a3b8); margin-bottom: 20px;">Bu işlem geri alınamaz.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button wire:click="cancelDelete" class="nx-btn nx-btn-secondary" style="font-size: 13px; padding: 8px 16px;">Vazgeç</button>
                <button wire:click="deleteTask" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <span wire:loading.remove wire:target="deleteTask">Evet, Sil</span>
                    <span wire:loading wire:target="deleteTask">Siliniyor...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
