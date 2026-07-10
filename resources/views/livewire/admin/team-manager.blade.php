<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">Ekip Yönetimi</h1>
            <p class="nx-page-subtitle">
                @if(session('impersonating_tenant_id') && $isPlatformAdmin)
                    {{ session('impersonating_tenant_name') }} ekibi — {{ count($users) }} kişi
                @elseif($isPlatformAdmin)
                    Platform ekibi — {{ count($users) }} kişi
                @else
                    Ajans ekibi — {{ count($users) }} kişi
                @endif
            </p>
        </div>
        <button class="nx-btn nx-btn-primary" wire:click="openEditor()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Kullanıcı Ekle
        </button>
    </div>

    <div class="nx-table-container">
        <table class="nx-table">
            <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th>Ad</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    @if($isPlatformAdmin)
                        <th>Katman</th>
                    @endif
                    <th>Kayıt Tarihi</th>
                    <th style="width: 100px; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td style="font-weight: 600;">{{ $user->name }}</td>
                        <td style="font-size: 12px; color: var(--nx-text-secondary);">{{ $user->email }}</td>
                        <td>
                            @php $roleName = $user->roles->first()?->name ?? 'none'; @endphp
                            <span class="nx-badge nx-badge-{{ $roleBadgeColors[$roleName] ?? 'gray' }}">
                                {{ $roleBadgeLabels[$roleName] ?? ucfirst($roleName) }}
                            </span>
                        </td>
                        @if($isPlatformAdmin)
                            <td>
                                @if(in_array($roleName, $platformRoles))
                                    <span style="font-size: 11px; color: var(--nx-accent); font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                                        </svg>
                                        Platform
                                    </span>
                                @elseif($user->tenant)
                                    <span style="font-size: 11px; color: var(--nx-text-secondary);">
                                        🏢 {{ $user->tenant->name ?? 'Ajans #' . $user->tenant_id }}
                                    </span>
                                @else
                                    <span style="font-size: 11px; color: var(--nx-text-muted);">—</span>
                                @endif
                            </td>
                        @endif
                        <td style="font-size: 12px; color: var(--nx-text-secondary);">
                            {{ $user->created_at?->format('d.m.Y') }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                <button wire:click="openEditor({{ $user->id }})" class="nx-btn-icon" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})" class="nx-btn-icon" title="Sil" style="color: var(--nx-danger);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Editor Slide-Over --}}
    @if($editorOpen)
        <div class="nx-slide-over-backdrop" wire:click="closeEditor"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">{{ $editingId ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı' }}</h2>
                <button wire:click="closeEditor" class="nx-btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="nx-slide-over-body">
                <div class="nx-section">
                    <div class="nx-section-body">
                        <div class="nx-form-grid" style="grid-template-columns: 1fr;">
                            <div class="nx-form-group">
                                <label class="nx-label">Ad Soyad <span class="required">*</span></label>
                                <input type="text" wire:model="name" class="nx-input" placeholder="Ad Soyad">
                                @error('name') <span class="nx-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">E-posta <span class="required">*</span></label>
                                <input type="email" wire:model="email" class="nx-input" placeholder="email@ornek.com">
                                @error('email') <span class="nx-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">
                                    Şifre {{ $editingId ? '(boş bırakılırsa değişmez)' : '' }}
                                    @if(!$editingId) <span class="required">*</span> @endif
                                </label>
                                <input type="password" wire:model="password" class="nx-input" placeholder="••••••••">
                                @error('password') <span class="nx-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="nx-form-group">
                                <label class="nx-label">Rol <span class="required">*</span></label>
                                <select wire:model="selectedRole" class="nx-select">
                                    <option value="">Seçiniz...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $roleLabels[$role->name] ?? ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                @error('selectedRole') <span class="nx-error-text">{{ $message }}</span> @enderror

                                @if($isPlatformAdmin)
                                    <p style="font-size: 11px; color: var(--nx-text-muted); margin-top: 4px; line-height: 1.5;">
                                        ℹ️ Platform rolleri burada listelenir. Ajans rolleri ilgili ajans panelinden yönetilir.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nx-slide-over-footer">
                <button wire:click="closeEditor" class="nx-btn nx-btn-secondary">İptal</button>
                <button wire:click="save" class="nx-btn nx-btn-primary">
                    <span wire:loading.remove wire:target="save">Kaydet</span>
                    <span wire:loading wire:target="save">Kaydediliyor...</span>
                </button>
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
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--nx-text, #e2e8f0);">Kullanıcı Silme</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--nx-text-muted, #94a3b8);">Bu kullanıcıyı silmek istediğinize emin misiniz?</p>
                </div>
            </div>
            <p style="font-size: 12px; color: var(--nx-text-muted, #94a3b8); margin-bottom: 20px;">Bu işlem geri alınamaz.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button wire:click="cancelDelete" class="nx-btn nx-btn-secondary" style="font-size: 13px; padding: 8px 16px;">Vazgeç</button>
                <button wire:click="deleteUser" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <span wire:loading.remove wire:target="deleteUser">Evet, Sil</span>
                    <span wire:loading wire:target="deleteUser">Siliniyor...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
