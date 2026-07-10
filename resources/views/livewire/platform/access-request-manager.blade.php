<div>
    <div class="nx-page-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 class="nx-page-title">Erken Erişim Talepleri</h1>
            <p class="nx-page-subtitle">Landing page'den gelen Pro/Enterprise talepleri</p>
        </div>
        <div style="display:flex;gap:12px;">
            <div class="nx-stat-card" style="padding:12px 16px;min-width:auto;">
                <div style="font-size:10px;color:var(--nx-text-secondary);">Toplam</div>
                <div style="font-size:18px;font-weight:700;">{{ $stats['total'] }}</div>
            </div>
            <div class="nx-stat-card accent-warning" style="padding:12px 16px;min-width:auto;">
                <div style="font-size:10px;color:var(--nx-text-secondary);">Bekleyen</div>
                <div style="font-size:18px;font-weight:700;">{{ $stats['pending'] }}</div>
            </div>
            <div class="nx-stat-card accent-success" style="padding:12px 16px;min-width:auto;">
                <div style="font-size:10px;color:var(--nx-text-secondary);">Onaylı</div>
                <div style="font-size:18px;font-weight:700;">{{ $stats['approved'] }}</div>
            </div>
        </div>
    </div>

    <div class="nx-table-container">
        <div class="nx-table-toolbar">
            <div class="nx-table-search">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ad, şirket veya e-posta ara..." class="nx-input" style="max-width:240px;">
            </div>
            <div class="nx-table-filters" style="display:flex;gap:8px;align-items:center;">
                <select wire:model.live="filterStatus" class="nx-select" style="width:auto;min-width:120px;padding:6px 32px 6px 10px;font-size:12px;">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending">Bekliyor</option>
                    <option value="reviewed">İncelendi</option>
                    <option value="approved">Onaylandı</option>
                    <option value="contacted">İletişime Geçildi</option>
                    <option value="rejected">Reddedildi</option>
                </select>
                <select wire:model.live="filterPlan" class="nx-select" style="width:auto;min-width:120px;padding:6px 32px 6px 10px;font-size:12px;">
                    <option value="">Tüm Planlar</option>
                    <option value="pro">Pro</option>
                    <option value="enterprise">Enterprise</option>
                </select>
                <button wire:click="exportCsv" class="nx-btn nx-btn-secondary" style="font-size:12px;padding:6px 14px;">CSV İndir</button>
            </div>
        </div>

        <table class="nx-table">
            <thead>
                <tr>
                    <th>Tarih</th><th>Ad Soyad</th><th>Şirket</th><th>E-posta</th><th>Plan</th><th style="width:100px;">Durum</th><th style="width:60px;">Detay</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr wire:key="ar-{{ $req->id }}">
                        <td style="font-size:12px;color:var(--nx-text-secondary);">{{ $req->created_at->format('d.m.Y') }}</td>
                        <td style="font-weight:500;">{{ $req->name }}</td>
                        <td>{{ $req->company_name }}</td>
                        <td style="font-size:12px;">{{ $req->email }}</td>
                        <td><span class="nx-badge nx-badge-{{ $req->plan_interest === 'enterprise' ? 'info' : 'success' }}">{{ ucfirst($req->plan_interest) }}</span></td>
                        <td>
                            @php $sc = ['pending'=>'warning','reviewed'=>'info','approved'=>'success','rejected'=>'danger','contacted'=>'info']; @endphp
                            <span class="nx-badge nx-badge-{{ $sc[$req->status] ?? 'gray' }}">{{ ucfirst($req->status) }}</span>
                        </td>
                        <td style="text-align:right;">
                            <button wire:click="showDetail({{ $req->id }})" class="nx-btn-icon" title="Detay">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="nx-table-empty"><p>Henüz talep yok.</p></td></tr>
                @endforelse
            </tbody>
        </table>
        @if($requests->hasPages())
            <div class="nx-table-footer">{{ $requests->links() }}</div>
        @endif
    </div>

    @if($detailId)
        <div class="nx-slide-over-backdrop" wire:click="closeDetail"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">Talep #{{ $detailData['id'] }}</h2>
                <button wire:click="closeDetail" class="nx-btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="nx-slide-over-body">
                <div class="nx-section" style="margin-bottom:16px;">
                    <div class="nx-section-body" style="font-size:13px;display:grid;grid-template-columns:120px 1fr;gap:8px;">
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Ad Soyad</span><span>{{ $detailData['name'] }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Şirket</span><span>{{ $detailData['company_name'] }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">E-posta</span><span>{{ $detailData['email'] }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Telefon</span><span>{{ $detailData['phone'] ?? '-' }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Kullanıcı</span><span>{{ $detailData['expected_users'] ?? '-' }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Plan</span><span>{{ ucfirst($detailData['plan_interest']) }}</span>
                        <span style="font-weight:600;color:var(--nx-text-secondary);">Tarih</span><span>{{ $detailData['created_at'] }}</span>
                    </div>
                </div>
                @if($detailData['use_case'])
                    <div class="nx-section" style="margin-bottom:16px;">
                        <div class="nx-section-header"><h3 class="nx-section-title">Kullanım Alanı</h3></div>
                        <div class="nx-section-body" style="font-size:13px;line-height:1.6;">{{ $detailData['use_case'] }}</div>
                    </div>
                @endif
                <div class="nx-section" style="margin-bottom:16px;">
                    <div class="nx-section-header"><h3 class="nx-section-title">Admin Notu</h3></div>
                    <div class="nx-section-body">
                        <textarea wire:model="adminNote" class="nx-input" rows="3" style="width:100%;resize:vertical;" placeholder="İç not..."></textarea>
                        <button wire:click="saveNote({{ $detailData['id'] }})" class="nx-btn nx-btn-secondary" style="font-size:12px;padding:6px 12px;margin-top:8px;">Notu Kaydet</button>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button wire:click="updateStatus({{ $detailData['id'] }}, 'reviewed')" class="nx-btn nx-btn-secondary" style="font-size:12px;padding:6px 12px;">İncelendi</button>
                    <button wire:click="updateStatus({{ $detailData['id'] }}, 'contacted')" class="nx-btn nx-btn-secondary" style="font-size:12px;padding:6px 12px;">İletişime Geçildi</button>
                    <button wire:click="updateStatus({{ $detailData['id'] }}, 'approved')" class="nx-btn" style="font-size:12px;padding:6px 12px;background:#10b981;color:white;border:none;border-radius:8px;cursor:pointer;">Onayla</button>
                    <button wire:click="updateStatus({{ $detailData['id'] }}, 'rejected')" class="nx-btn" style="font-size:12px;padding:6px 12px;background:#ef4444;color:white;border:none;border-radius:8px;cursor:pointer;">Reddet</button>
                </div>
            </div>
        </div>
    @endif
</div>
