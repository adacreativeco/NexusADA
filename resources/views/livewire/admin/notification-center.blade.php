<div>
    {{-- Page Header --}}
    <div class="nx-page-header" style="margin-bottom: 24px;">
        <div>
            <h1 class="nx-page-title" style="font-size: 24px; font-weight: 700; color: var(--nx-text-primary); display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 28px; color: var(--nx-accent);">notifications</span>
                Bildirim Merkezi
            </h1>
            <p class="nx-page-subtitle">Süreç onayları, atanan görevler ve sistem güncellemeleri</p>
        </div>
        <div style="display: flex; gap: 8px;">
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">done_all</span>
                    Tümünü Oku
                </button>
            @endif
            <button wire:click="clearAll" onclick="confirm('Tüm bildirim geçmişini silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()" class="nx-btn nx-btn-secondary" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px; color: #ef4444; border-color: rgba(239,68,68,0.2);">
                <span class="material-symbols-outlined" style="font-size: 16px;">delete_sweep</span>
                Tümünü Temizle
            </button>
        </div>
    </div>

    {{-- Filters Tab Bar --}}
    <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 8px; margin-bottom: 20px; display: flex; gap: 6px; overflow-x: auto;">
        @foreach([
            'all' => ['label' => 'Tümü', 'icon' => 'list'],
            'unread' => ['label' => 'Okunmamış', 'icon' => 'mark_chat_unread'],
            'approval' => ['label' => 'Onaylar', 'icon' => 'rule'],
            'task' => ['label' => 'Görevler', 'icon' => 'task_alt'],
            'system' => ['label' => 'Sistem / Dosya', 'icon' => 'settings']
        ] as $key => $meta)
            <button wire:click="$set('filter', '{{ $key }}')" 
                    style="border: none; background: none; cursor: pointer; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;
                           background: {{ $filter === $key ? 'var(--nx-accent)' : 'transparent' }};
                           color: {{ $filter === $key ? 'white' : 'var(--nx-text-secondary)' }};">
                <span class="material-symbols-outlined" style="font-size: 16px;">{{ $meta['icon'] }}</span>
                {{ $meta['label'] }}
                @if($key === 'unread' && $unreadCount > 0)
                    <span style="background: var(--nx-danger); color: white; font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 10px; margin-left: 4px;">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Notification List Feed --}}
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @forelse($notifications as $notif)
            @php
                $url = $notif->data['url'] ?? '#';
                $isUnread = !$notif->read_at;
                
                // Color and Icon definitions
                $icon = 'notifications';
                $color = 'var(--nx-text-secondary)';
                $bgColor = 'rgba(255, 255, 255, 0.01)';
                
                switch ($notif->type) {
                    case 'approval_required':
                        $icon = 'check_box';
                        $color = '#f59e0b';
                        $bgColor = $isUnread ? 'rgba(245, 158, 11, 0.04)' : $bgColor;
                        break;
                    case 'task_assigned':
                        $icon = 'task_alt';
                        $color = '#3b82f6';
                        $bgColor = $isUnread ? 'rgba(59, 130, 246, 0.04)' : $bgColor;
                        break;
                    case 'task_completed':
                        $icon = 'check_circle';
                        $color = '#10b981';
                        $bgColor = $isUnread ? 'rgba(16, 185, 129, 0.04)' : $bgColor;
                        break;
                    case 'contract_expiring':
                        $icon = 'warning';
                        $color = '#ef4444';
                        $bgColor = $isUnread ? 'rgba(239, 68, 68, 0.04)' : $bgColor;
                        break;
                    case 'document_uploaded':
                        $icon = 'cloud_upload';
                        $color = '#ec4899';
                        $bgColor = $isUnread ? 'rgba(236, 72, 153, 0.04)' : $bgColor;
                        break;
                }
            @endphp
            <div style="background: var(--nx-bg-card); border: 1px solid {{ $isUnread ? 'rgba(16,185,129,0.2)' : 'var(--nx-border)' }}; border-radius: 12px; padding: 16px; display: flex; align-items: start; gap: 16px; transition: all 0.2s;
                        box-shadow: {{ $isUnread ? '0 2px 8px rgba(16,185,129,0.05)' : 'none' }};
                        background-color: {{ $bgColor }};">
                
                {{-- Icon Badge --}}
                <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--nx-bg-secondary); border: 1px solid var(--nx-border); color: {{ $color }}; flex-shrink: 0;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">{{ $icon }}</span>
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 4px;">
                        <h4 style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary); margin: 0;">
                            {{ $notif->title }}
                            @if($isUnread)
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981; margin-left: 6px;" title="Okunmadı"></span>
                            @endif
                        </h4>
                        <span style="font-size: 11px; color: var(--nx-text-secondary);">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    @if($notif->body)
                        <p style="font-size: 12px; color: var(--nx-text-secondary); line-height: 1.5; margin: 0 0 8px 0;">
                            {{ $notif->body }}
                        </p>
                    @endif

                    {{-- Actions block --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        {{-- Left side link --}}
                        <div>
                            @if($url && $url !== '#')
                                <a href="{{ $url }}" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                    Detayı Gör
                                    <span class="material-symbols-outlined" style="font-size: 14px;">open_in_new</span>
                                </a>
                            @endif
                        </div>

                        {{-- Right side inline actions --}}
                        <div style="display: flex; align-items: center; gap: 8px;">
                            {{-- Inline Approval Block --}}
                            @if($notif->type === 'approval_required' && isset($notif->data['model_type']) && isset($notif->data['model_id']))
                                <button wire:click="rejectItem({{ $notif->id }}, '{{ addslashes($notif->data['model_type']) }}', {{ $notif->data['model_id'] }})" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 10px; color: #ef4444; border-color: rgba(239,68,68,0.2);">
                                    Reddet
                                </button>
                                <button wire:click="approveItem({{ $notif->id }}, '{{ addslashes($notif->data['model_type']) }}', {{ $notif->data['model_id'] }})" class="nx-btn nx-btn-primary" style="font-size: 11px; padding: 4px 10px;">
                                    Onayla
                                </button>
                            @endif

                            @if($isUnread)
                                <button wire:click="markRead({{ $notif->id }})" class="nx-btn nx-btn-secondary" style="font-size: 11px; padding: 4px 8px;" title="Okundu İşaretle">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">done</span>
                                </button>
                            @endif
                            
                            <button wire:click="deleteNotification({{ $notif->id }})" class="nx-btn-icon" style="color: #ef4444; border: 1px solid var(--nx-border); border-radius: 4px; padding: 4px;" title="Bildirimi Sil">
                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 12px; padding: 48px; text-align: center;">
                <span class="material-symbols-outlined" style="font-size: 48px; color: var(--nx-text-secondary); opacity: 0.4; margin-bottom: 12px;">notifications_off</span>
                <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px;">Bildirim Bulunmuyor</h3>
                <p style="font-size: 12px; color: var(--nx-text-secondary);">Seçilen filtre için herhangi bir bildirim kaydı mevcut değil.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div style="margin-top: 16px;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
