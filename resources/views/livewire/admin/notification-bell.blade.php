<div style="position: relative;" wire:poll.30s>
    {{-- Bell Icon --}}
    <button wire:click="toggleDropdown" class="nx-btn-icon" style="position: relative;" title="Bildirimler">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>
        @if($unreadCount > 0)
            <span style="position: absolute; top: -2px; right: -2px; width: 14px; height: 14px; border-radius: 50%; background: var(--nx-danger); color: white; font-size: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid var(--nx-bg-base);">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($dropdownOpen)
        <div class="nx-notif-dropdown">
            <div style="padding: 12px 16px; border-bottom: 1px solid var(--nx-border); display: flex; justify-content: space-between; align-items: center; background: var(--nx-bg-card);">
                <a href="/admin/notifications" wire:click="$set('dropdownOpen', false)" style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Bildirimler
                    <span class="material-symbols-outlined" style="font-size: 14px; opacity: 0.6;">open_in_new</span>
                </a>
                @if($unreadCount > 0)
                    <button wire:click="markAllRead" style="font-size: 11px; color: var(--nx-accent); background: none; border: none; cursor: pointer; font-weight: 500;">
                        Tümünü oku
                    </button>
                @endif
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                @forelse($notifications as $notif)
                    @php
                        $url = $notif->data['url'] ?? '#';
                        $icons = ['task_assigned'=>'var(--nx-info)','task_completed'=>'var(--nx-success)','document_uploaded'=>'#8b5cf6','mention'=>'var(--nx-warning)'];
                    @endphp
                    <a href="{{ $url }}" wire:click="markRead({{ $notif->id }})"
                       class="nx-notif-item {{ !$notif->read_at ? 'unread' : '' }}"
                       style="text-decoration: none;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0;
                                    background: {{ !$notif->read_at ? ($icons[$notif->type] ?? 'var(--nx-accent)') : 'rgba(255,255,255,0.1)' }};"></div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 500; color: var(--nx-text-primary);">{{ $notif->title }}</div>
                            @if($notif->body)
                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px; line-height: 1.4;">{{ $notif->body }}</div>
                            @endif
                            <div style="font-size: 10px; color: var(--nx-text-muted); margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div style="padding: 40px 20px; text-align: center; color: var(--nx-text-muted); font-size: 12px;">
                        Henüz bildiriminiz yok.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
