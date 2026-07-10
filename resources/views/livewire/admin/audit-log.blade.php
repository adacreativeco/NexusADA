@section('title', 'Denetim Kaydı')
@section('breadcrumb', 'Denetim Kaydı')

<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">Denetim Kaydı</h1>
            <p class="nx-page-subtitle">Kim, ne zaman, neyi değiştirdi — tüm sistem logları</p>
        </div>
    </div>

    <div class="nx-table-container">
        {{-- Toolbar --}}
        <div class="nx-table-toolbar">
            <div class="nx-table-search">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Kullanıcı, URL veya IP ara..." class="nx-input" style="max-width: 240px;">
            </div>
            <div class="nx-table-filters" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <select wire:model.live="filterEvent" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
                    <option value="">Tüm İşlemler</option>
                    <option value="created">Oluşturma</option>
                    <option value="updated">Güncelleme</option>
                    <option value="deleted">Silme</option>
                </select>
                <select wire:model.live="filterModel" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
                    <option value="">Tüm Modeller</option>
                    @foreach($modelTypes as $type => $label)
                        <option value="{{ $type }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterUser" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
                    <option value="">Tüm Kullanıcılar</option>
                    @foreach($users as $uid => $uname)
                        <option value="{{ $uid }}">{{ $uname }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterDateRange" class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
                    <option value="">Tüm Zamanlar</option>
                    <option value="7">Son 7 gün</option>
                    <option value="30">Son 30 gün</option>
                    <option value="90">Son 90 gün</option>
                </select>
                <button wire:click="exportCsv" class="nx-btn nx-btn-secondary" style="font-size: 12px; padding: 6px 14px; display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    CSV İndir
                </button>
            </div>
        </div>

        {{-- Table --}}
        <table class="nx-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Tarih</th>
                    <th style="width: 120px;">Kullanıcı</th>
                    <th style="width: 100px;">İşlem</th>
                    <th style="width: 120px;">Model</th>
                    <th>Değişiklik Özeti</th>
                    <th style="width: 70px; text-align: right;">Detay</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                    <tr wire:key="audit-{{ $audit->id }}">
                        <td style="font-size: 12px; color: var(--nx-text-secondary);">
                            {{ $audit->created_at->diffForHumans() }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--nx-accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700;">
                                    {{ substr($audit->user?->name ?? 'S', 0, 1) }}
                                </div>
                                <span style="font-size: 12px;">{{ $audit->user?->name ?? 'Sistem' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $eventColors = ['created' => 'success', 'updated' => 'info', 'deleted' => 'danger'];
                                $eventLabels = ['created' => 'Oluşturma', 'updated' => 'Güncelleme', 'deleted' => 'Silme'];
                            @endphp
                            <span class="nx-badge nx-badge-{{ $eventColors[$audit->event] ?? 'gray' }}">
                                {{ $eventLabels[$audit->event] ?? $audit->event }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 500;">
                                {{ class_basename($audit->auditable_type) }}
                            </span>
                            <span style="font-size: 10px; color: var(--nx-text-secondary);">#{{ $audit->auditable_id }}</span>
                        </td>
                        <td style="font-size: 11px; color: var(--nx-text-secondary);">
                            @php
                                $changed = array_keys($audit->new_values ?? []);
                                $summary = implode(', ', array_slice($changed, 0, 3));
                                if (count($changed) > 3) $summary .= ' +' . (count($changed) - 3);
                            @endphp
                            {{ $summary ?: '—' }}
                        </td>
                        <td style="text-align: right;">
                            <button wire:click="showDetail({{ $audit->id }})" class="nx-btn-icon" title="Detay">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="nx-table-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                            </svg>
                            <p>Henüz denetim kaydı bulunmuyor.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($audits->hasPages())
            <div class="nx-table-footer">
                <span>{{ $audits->total() }} kayıttan {{ $audits->firstItem() }}–{{ $audits->lastItem() }} gösteriliyor</span>
                <div class="nx-pagination">
                    {{ $audits->links('livewire.admin.partials.pagination') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Detail Slide-Over --}}
    @if($detailId)
        <div class="nx-slide-over-backdrop" wire:click="closeDetail"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">
                    Denetim Detayı — {{ $detailData['auditable_type'] }} #{{ $detailData['auditable_id'] }}
                </h2>
                <button wire:click="closeDetail" class="nx-btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="nx-slide-over-body">
                {{-- Meta --}}
                <div class="nx-section" style="margin-bottom: 16px;">
                    <div class="nx-section-header"><h3 class="nx-section-title">Bilgi</h3></div>
                    <div class="nx-section-body" style="font-size: 12px;">
                        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px;">
                            <span style="font-weight: 600; color: var(--nx-text-secondary);">İşlem</span>
                            <span>
                                @php $ec = ['created'=>'success','updated'=>'info','deleted'=>'danger']; $el = ['created'=>'Oluşturma','updated'=>'Güncelleme','deleted'=>'Silme']; @endphp
                                <span class="nx-badge nx-badge-{{ $ec[$detailData['event']] ?? 'gray' }}">{{ $el[$detailData['event']] ?? $detailData['event'] }}</span>
                            </span>
                            <span style="font-weight: 600; color: var(--nx-text-secondary);">Kullanıcı</span>
                            <span>{{ $detailData['user_name'] }}</span>
                            <span style="font-weight: 600; color: var(--nx-text-secondary);">Tarih</span>
                            <span>{{ $detailData['created_at'] }}</span>
                            <span style="font-weight: 600; color: var(--nx-text-secondary);">IP</span>
                            <span style="font-family: var(--nx-font-mono);">{{ $detailData['ip_address'] ?? '—' }}</span>
                            <span style="font-weight: 600; color: var(--nx-text-secondary);">URL</span>
                            <span style="font-family: var(--nx-font-mono); word-break: break-all;">{{ $detailData['url'] ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Diff --}}
                <div class="nx-section">
                    <div class="nx-section-header"><h3 class="nx-section-title">Değişiklikler</h3></div>
                    <div class="nx-section-body" style="padding: 0;">
                        <table class="nx-table" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Alan</th>
                                    <th>Eski Değer</th>
                                    <th>Yeni Değer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allKeys = array_unique(array_merge(
                                        array_keys($detailData['old_values'] ?? []),
                                        array_keys($detailData['new_values'] ?? [])
                                    ));
                                @endphp
                                @forelse($allKeys as $key)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $key }}</td>
                                        <td style="color: var(--nx-danger); background: rgba(239,68,68,0.04);">
                                            {{ $detailData['old_values'][$key] ?? '—' }}
                                        </td>
                                        <td style="color: var(--nx-success); background: rgba(16,185,129,0.04);">
                                            {{ $detailData['new_values'][$key] ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: var(--nx-text-secondary); padding: 20px;">
                                            Değişiklik detayı mevcut değil.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
