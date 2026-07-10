@section('title', 'E-posta Hesapları')
@section('breadcrumb', 'E-posta Hesapları')

<div>
    <div class="nx-page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="nx-page-title">E-posta Hesapları</h1>
            <p class="nx-page-subtitle">IMAP hesap yönetimi ve senkronizasyon durumu</p>
        </div>
        <button wire:click="openCreate" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: var(--nx-accent); color: white; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Hesap Ekle
        </button>
    </div>

    {{-- Accounts Table --}}
    <div class="nx-table-container">
        <table class="nx-table">
            <thead>
                <tr>
                    <th>E-posta</th>
                    <th>IMAP Sunucu</th>
                    <th style="width: 100px;">Durum</th>
                    <th style="width: 130px;">Son Sync</th>
                    <th style="width: 80px;">E-posta</th>
                    <th style="width: 160px; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr wire:key="account-{{ $account->id }}">
                        <td>
                            <div style="font-weight: 600; font-size: 13px;">{{ $account->email }}</div>
                            <div style="font-size: 11px; color: var(--nx-text-secondary);">{{ $account->folder }}</div>
                        </td>
                        <td style="font-size: 12px; font-family: var(--nx-font-mono);">
                            {{ $account->imap_host }}:{{ $account->imap_port }}
                            <span class="nx-badge" style="font-size: 9px; margin-left: 4px;">{{ strtoupper($account->imap_encryption) }}</span>
                        </td>
                        <td>
                            @if($account->is_active)
                                <span class="nx-badge nx-badge-success">Aktif</span>
                            @else
                                <span class="nx-badge nx-badge-gray">Devre Dışı</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: var(--nx-text-secondary);">
                            {{ $account->last_sync_at ? $account->last_sync_at->diffForHumans() : 'Hiç' }}
                        </td>
                        <td style="font-size: 12px; text-align: center;">
                            {{ $account->incomingEmails()->count() }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                <button wire:click="testConnection({{ $account->id }})" class="nx-btn-icon" title="Bağlantı Test">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                </button>
                                <button wire:click="syncNow({{ $account->id }})" class="nx-btn-icon" title="Şimdi Sync">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                </button>
                                <button wire:click="edit({{ $account->id }})" class="nx-btn-icon" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                </button>
                                <button wire:click="toggleActive({{ $account->id }})" class="nx-btn-icon" title="{{ $account->is_active ? 'Devre Dışı Bırak' : 'Aktifleştir' }}">
                                    @if($account->is_active)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#10b981"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                                    @endif
                                </button>
                                <button wire:click="delete({{ $account->id }})" wire:confirm="Bu hesabı silmek istediğinize emin misiniz?" class="nx-btn-icon" title="Sil">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="nx-table-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="opacity: 0.4; margin-bottom: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z"/>
                            </svg>
                            <p>Henüz e-posta hesabı eklenmemiş.</p>
                            <button wire:click="openCreate" class="nx-btn" style="margin-top: 12px; font-size: 12px; padding: 6px 14px;">İlk Hesabı Ekle</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create/Edit Slide-Over --}}
    @if($showForm)
        <div class="nx-slide-over-backdrop" wire:click="closeForm"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">{{ $editingId ? 'Hesap Düzenle' : 'Yeni E-posta Hesabı' }}</h2>
                <button wire:click="closeForm" class="nx-btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="nx-slide-over-body">
                <form wire:submit="save" style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label class="nx-label">E-posta Adresi</label>
                        <input type="email" wire:model="email" class="nx-input" placeholder="info@firma.com">
                        @error('email') <span class="nx-error">{{ $message }}</span> @enderror
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 100px 100px; gap: 12px;">
                        <div>
                            <label class="nx-label">IMAP Sunucu</label>
                            <input type="text" wire:model="imap_host" class="nx-input" placeholder="imap.gmail.com">
                            @error('imap_host') <span class="nx-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="nx-label">Port</label>
                            <input type="number" wire:model="imap_port" class="nx-input">
                        </div>
                        <div>
                            <label class="nx-label">Şifreleme</label>
                            <select wire:model="imap_encryption" class="nx-select">
                                <option value="ssl">SSL</option>
                                <option value="tls">TLS</option>
                                <option value="none">Yok</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="nx-label">Kullanıcı Adı</label>
                        <input type="text" wire:model="username" class="nx-input" placeholder="info@firma.com">
                        @error('username') <span class="nx-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="nx-label">Şifre {{ $editingId ? '(boş bırakırsanız değişmez)' : '' }}</label>
                        <input type="password" wire:model="password" class="nx-input">
                        @error('password') <span class="nx-error">{{ $message }}</span> @enderror
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label class="nx-label">Klasör</label>
                            <input type="text" wire:model="folder" class="nx-input">
                        </div>
                        <div>
                            <label class="nx-label">Sync Aralığı (dk)</label>
                            <input type="number" wire:model="sync_interval_minutes" class="nx-input" min="1" max="60">
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" wire:model="is_active" id="is_active" style="accent-color: var(--nx-accent);">
                        <label for="is_active" class="nx-label" style="margin: 0;">Hesap aktif</label>
                    </div>
                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 8px;">
                        <button type="button" wire:click="closeForm" class="nx-btn nx-btn-secondary" style="font-size: 13px; padding: 8px 16px;">İptal</button>
                        <button type="submit" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: var(--nx-accent); color: white; border: none; border-radius: 8px; cursor: pointer;">
                            {{ $editingId ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
