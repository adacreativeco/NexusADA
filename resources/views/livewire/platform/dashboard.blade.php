<div>
    @section('title', 'Platform Dashboard')
    @section('breadcrumb', 'Dashboard')

    {{-- KPI Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">
        {{-- Total Tenants --}}
        <div class="nx-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 8px;">Toplam Kiracı</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--nx-text-primary);">{{ $totalTenants }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: var(--nx-radius-lg); background: var(--nx-badge-info-bg); display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--nx-badge-info-text)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 12px; color: var(--nx-text-muted);">
                <span style="color: var(--nx-success);">{{ $activeTenants }} aktif</span> · 
                <span style="color: var(--nx-warning);">{{ $trialTenants }} deneme</span>
            </div>
        </div>

        {{-- MRR --}}
        <div class="nx-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 8px;">Aylık Gelir (MRR)</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--nx-accent);">₺{{ number_format($mrr, 0, ',', '.') }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: var(--nx-radius-lg); background: var(--nx-badge-success-bg); display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--nx-accent)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 12px; color: var(--nx-text-muted);">
                Yıllık: ₺{{ number_format($mrr * 12, 0, ',', '.') }}
            </div>
        </div>

        {{-- Active Subscriptions --}}
        <div class="nx-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 8px;">Aktif Abonelik</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--nx-success);">{{ $activeTenants }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: var(--nx-radius-lg); background: var(--nx-badge-success-bg); display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--nx-badge-success-text)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Suspended --}}
        <div class="nx-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 13px; color: var(--nx-text-secondary); margin-bottom: 8px;">Askıda / Uyarı</div>
                    <div style="font-size: 32px; font-weight: 700; color: {{ $suspendedTenants > 0 ? 'var(--nx-danger)' : 'var(--nx-text-primary)' }};">{{ $suspendedTenants }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: var(--nx-radius-lg); background: var(--nx-badge-danger-bg); display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--nx-badge-danger-text)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                </div>
            </div>
            @if($expiringSoon->count() > 0)
                <div style="margin-top: 12px; font-size: 12px; color: var(--nx-warning);">
                    {{ $expiringSoon->count() }} abonelik 7 gün içinde bitiyor
                </div>
            @endif
        </div>
    </div>

    {{-- Two Column Grid --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        {{-- Recent Tenants --}}
        <div class="nx-card" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Son Kayıt Olan Kiracılar</h3>
            </div>
            <div style="padding: 8px;">
                @forelse($recentTenants as $tenant)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: var(--nx-radius-md); transition: background var(--nx-transition);" onmouseover="this.style.background='var(--nx-bg-hover)'" onmouseout="this.style.background='transparent'">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: var(--nx-radius-md); background: var(--nx-badge-info-bg); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--nx-badge-info-text); font-size: 13px;">
                                {{ strtoupper(substr($tenant->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 500;">{{ $tenant->name }}</div>
                                <div style="font-size: 12px; color: var(--nx-text-muted);">{{ $tenant->email }}</div>
                            </div>
                        </div>
                        <div>
                            <span class="nx-badge nx-badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'trial' ? 'warning' : 'danger') }}">
                                {{ $tenant->status_label }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 40px; text-align: center; color: var(--nx-text-muted); font-size: 14px;">
                        Henüz kiracı kaydı yok
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Plan Distribution --}}
        <div class="nx-card" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--nx-border);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Plan Dağılımı</h3>
            </div>
            <div style="padding: 24px;">
                @forelse($planDistribution as $plan)
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 500;">{{ $plan->name }}</span>
                            <span style="font-size: 14px; color: var(--nx-text-secondary);">{{ $plan->tenants_count }} kiracı</span>
                        </div>
                        <div style="height: 6px; background: var(--nx-bg-hover); border-radius: 3px; overflow: hidden;">
                            @php $pct = $totalTenants > 0 ? ($plan->tenants_count / $totalTenants) * 100 : 0; @endphp
                            <div style="width: {{ $pct }}%; height: 100%; background: var(--nx-accent); border-radius: 3px; transition: width 0.5s;"></div>
                        </div>
                        <div style="font-size: 12px; color: var(--nx-text-muted); margin-top: 4px;">
                            ₺{{ number_format($plan->price_monthly, 0, ',', '.') }}/ay
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--nx-text-muted); padding: 20px;">
                        Plan verisi yükleniyor...
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
