<div>
    @section('title', 'Sistem Logları')
    @section('breadcrumb', 'Sistem Logları')

    <div class="nx-card" style="padding: 0;">
        {{-- Header --}}
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0; font-size: 18px; font-weight: 600;">Denetim Kaydı</h2>
                <p style="margin: 4px 0 0; font-size: 13px; color: var(--nx-text-muted);">Tüm platform değişiklikleri</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Ara..."
                       class="nx-input" style="width: 240px;">
                <select wire:model.live="eventFilter" class="nx-input">
                    <option value="">Tüm Olaylar</option>
                    <option value="created">Oluşturma</option>
                    <option value="updated">Güncelleme</option>
                    <option value="deleted">Silme</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x: auto;">
            <table class="nx-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kullanıcı</th>
                        <th>Olay</th>
                        <th>Model</th>
                        <th>Değişiklikler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                        <tr>
                            <td style="white-space: nowrap; color: var(--nx-text-muted);">
                                {{ $audit->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td>{{ $audit->user?->name ?? 'Sistem' }}</td>
                            <td>
                                <span class="nx-badge nx-badge-{{ $audit->event === 'created' ? 'success' : ($audit->event === 'deleted' ? 'danger' : 'info') }}">
                                    {{ $audit->event === 'created' ? 'Oluşturma' : ($audit->event === 'deleted' ? 'Silme' : 'Güncelleme') }}
                                </span>
                            </td>
                            <td style="font-family: var(--nx-font-mono); font-size: 12px; color: var(--nx-text-secondary);">
                                {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                            </td>
                            <td style="max-width: 300px;">
                                @php
                                    $changes = $audit->new_values ?? [];
                                    $old = $audit->old_values ?? [];
                                @endphp
                                @if(!empty($changes))
                                    <div style="font-size: 12px; color: var(--nx-text-muted);">
                                        @foreach(array_slice($changes, 0, 3, true) as $key => $value)
                                            <div>
                                                <span style="color: var(--nx-text-secondary);">{{ $key }}:</span>
                                                @if(isset($old[$key]))
                                                    <span style="text-decoration: line-through; color: var(--nx-danger);">{{ \Illuminate\Support\Str::limit((string) $old[$key], 20) }}</span> →
                                                @endif
                                                <span style="color: var(--nx-success);">{{ \Illuminate\Support\Str::limit((string) $value, 20) }}</span>
                                            </div>
                                        @endforeach
                                        @if(count($changes) > 3)
                                            <span style="color: var(--nx-accent);">+{{ count($changes) - 3 }} alan daha</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: var(--nx-text-muted);">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: var(--nx-text-muted);">
                                Henüz denetim kaydı yok
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="padding: 16px 24px; border-top: 1px solid var(--nx-border);">
            {{ $audits->links() }}
        </div>
    </div>
</div>
