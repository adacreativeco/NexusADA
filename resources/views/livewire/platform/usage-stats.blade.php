<div>
    {{-- Dashboard kendi CSS'ini kullanır, layout token'larına tam uyumlu --}}
    <style>
        .us-page { padding: 1.5rem 2rem; }
        .us-header { margin-bottom: 2rem; }
        .us-header h1 { font-size: 1.5rem; font-weight: 700; color: var(--nx-text-primary); display: flex; align-items: center; gap: 0.5rem; }
        .us-header p { font-size: 0.8125rem; color: var(--nx-text-muted); margin-top: 0.25rem; }

        /* ── Stat Cards ── */
        .us-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .us-card {
            background: var(--nx-bg-card);
            border: 1px solid var(--nx-border);
            border-radius: var(--nx-radius-lg);
            padding: 1.25rem;
            transition: border-color 0.2s, transform 0.2s;
        }
        .us-card:hover { border-color: var(--nx-border-hover); transform: translateY(-2px); }
        .us-card-label { font-size: 0.75rem; font-weight: 500; color: var(--nx-text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.5rem; }
        .us-card-value { font-size: 1.75rem; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono); }
        .us-card-value.danger { color: var(--nx-badge-danger-text); }
        .us-card-value.success { color: var(--nx-accent); }
        .us-card-value.muted { font-size: 1rem; color: var(--nx-text-muted); font-family: var(--nx-font-ui); font-weight: 400; font-style: italic; }

        /* ── Sections ── */
        .us-section {
            background: var(--nx-bg-card);
            border: 1px solid var(--nx-border);
            border-radius: var(--nx-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .us-section-title {
            font-size: 0.8125rem; font-weight: 600; color: var(--nx-text-secondary);
            text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;
        }

        /* ── Two-column row ── */
        .us-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }

        /* ── Content totals mini grid ── */
        .us-totals { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.75rem; }
        .us-total-item { text-align: center; }
        .us-total-item .label { font-size: 0.6875rem; color: var(--nx-text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .us-total-item .value { font-size: 1.5rem; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono); margin-top: 0.25rem; }

        /* ── Recent production badges ── */
        .us-recent { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .us-recent-badge {
            background: var(--nx-bg-elevated);
            border: 1px solid var(--nx-border);
            border-radius: var(--nx-radius-sm);
            padding: 0.5rem 0.875rem;
            font-size: 0.8125rem; color: var(--nx-text-secondary);
            display: flex; align-items: center; gap: 0.375rem;
        }
        .us-recent-badge strong { color: var(--nx-accent); font-family: var(--nx-font-mono); }

        /* ── Health panel ── */
        .us-health-row { display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; }
        .us-health-item { flex: 1; min-width: 120px; }
        .us-health-item .label { font-size: 0.6875rem; color: var(--nx-text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .us-health-item .value { font-size: 1.25rem; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono); margin-top: 0.25rem; }
        .us-health-item .value.warn { color: var(--nx-badge-warning-text); }

        /* ── Table ── */
        .us-table { width: 100%; border-collapse: collapse; }
        .us-table th {
            text-align: left; padding: 0.625rem 1rem;
            font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--nx-text-muted); border-bottom: 1px solid var(--nx-border);
        }
        .us-table td { padding: 0.625rem 1rem; font-size: 0.8125rem; color: var(--nx-text-secondary); border-bottom: 1px solid var(--nx-border); }
        .us-table tr:last-child td { border-bottom: none; }
        .us-table tr:hover td { background: var(--nx-bg-hover); }
        .us-table .mono { font-family: var(--nx-font-mono); font-size: 0.75rem; color: var(--nx-text-muted); }

        /* ── Progress bar ── */
        .us-bar { background: var(--nx-bg-elevated); width: 100%; max-width: 120px; height: 6px; border-radius: 3px; overflow: hidden; }
        .us-bar-fill { height: 100%; border-radius: 3px; background: var(--nx-accent); transition: width 0.3s; }

        /* ── Chart container ── */
        .us-chart-wrap { position: relative; height: 200px; }

        /* ── No data ── */
        .us-empty { text-align: center; padding: 2.5rem; color: var(--nx-text-muted); font-style: italic; font-size: 0.8125rem; }

        /* ── Responsive ── */
        @media (max-width: 1024px) { .us-grid { grid-template-columns: repeat(2, 1fr); } .us-row { grid-template-columns: 1fr; } .us-totals { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px) { .us-grid { grid-template-columns: 1fr; } .us-totals { grid-template-columns: repeat(2, 1fr); } .us-page { padding: 1rem; } }
    </style>

    <div class="us-page">

        {{-- Header --}}
        <div class="us-header">
            <h1>📊 Kullanım Metrikleri</h1>
            <p>Platform geneli gerçek zamanlı kullanım verileri ve sistem sağlığı.</p>
        </div>

        {{-- ═══ Top Stat Cards ═══ --}}
        <div class="us-grid">
            <div class="us-card">
                <div class="us-card-label">Toplam Login (30 gün)</div>
                @if($activity['total_logins'] > 0)
                    <div class="us-card-value">{{ number_format($activity['total_logins']) }}</div>
                @else
                    <div class="us-card-value muted">Henüz veri yok</div>
                @endif
            </div>
            <div class="us-card">
                <div class="us-card-label">Haftalık Aktif (WAU)</div>
                @if($activity['wau'] > 0)
                    <div class="us-card-value success">{{ $activity['wau'] }}</div>
                @else
                    <div class="us-card-value muted">Henüz veri yok</div>
                @endif
            </div>
            <div class="us-card">
                <div class="us-card-label">Ort. Session / Kullanıcı</div>
                @if($activity['avg_sessions'] > 0)
                    <div class="us-card-value">{{ $activity['avg_sessions'] }}</div>
                @else
                    <div class="us-card-value muted">Henüz veri yok</div>
                @endif
            </div>
            <div class="us-card">
                <div class="us-card-label">500 Hatası (30 gün)</div>
                <div class="us-card-value {{ $health['errors_30_days'] > 0 ? 'danger' : 'success' }}">
                    {{ $health['errors_30_days'] }}
                </div>
            </div>
        </div>

        {{-- ═══ DAU Chart ═══ --}}
        <div class="us-section">
            <div class="us-section-title">📈 Günlük Aktif Kullanıcı (DAU) Trendi — Son 30 Gün</div>
            @if(count($activity['dau_data']) > 0)
                <div class="us-chart-wrap">
                    <canvas id="dauChart"></canvas>
                </div>
            @else
                <div class="us-empty">Grafik için henüz yeterli veri yok. Kullanıcılar giriş yaptıkça veriler burada görünecektir.</div>
            @endif
        </div>

        {{-- ═══ Two-column: Content + Health ═══ --}}
        <div class="us-row">
            {{-- Content --}}
            <div class="us-section" style="margin-bottom: 0;">
                <div class="us-section-title">📦 İçerik Dağılımı</div>
                <div class="us-totals">
                    <div class="us-total-item">
                        <div class="label">Müşteri</div>
                        <div class="value">{{ $content['totals']['clients'] }}</div>
                    </div>
                    <div class="us-total-item">
                        <div class="label">Proje</div>
                        <div class="value">{{ $content['totals']['projects'] }}</div>
                    </div>
                    <div class="us-total-item">
                        <div class="label">Görev</div>
                        <div class="value">{{ $content['totals']['tasks'] }}</div>
                    </div>
                    <div class="us-total-item">
                        <div class="label">Kampanya</div>
                        <div class="value">{{ $content['totals']['campaigns'] }}</div>
                    </div>
                    <div class="us-total-item">
                        <div class="label">Etkinlik</div>
                        <div class="value">{{ $content['totals']['events'] }}</div>
                    </div>
                </div>
                <div class="us-recent">
                    <div class="us-recent-badge">Yeni Proje <strong>{{ $content['recent']['projects'] }}</strong></div>
                    <div class="us-recent-badge">Yeni Görev <strong>{{ $content['recent']['tasks'] }}</strong></div>
                    <div class="us-recent-badge">Yeni Yorum <strong>{{ $content['recent']['comments'] }}</strong></div>
                    <div class="us-recent-badge">Yeni Dosya <strong>{{ $content['recent']['files'] }}</strong></div>
                </div>
            </div>

            {{-- Health & Storage --}}
            <div class="us-section" style="margin-bottom: 0;">
                <div class="us-section-title">🛡️ Sistem Sağlığı & Depolama</div>
                <div class="us-health-row">
                    <div class="us-health-item">
                        <div class="label">DB Boyutu</div>
                        <div class="value">{{ $storage['db_size_mb'] }} <span style="font-size:0.75rem;color:var(--nx-text-muted);">MB</span></div>
                    </div>
                    <div class="us-health-item">
                        <div class="label">Dosya Depolama</div>
                        <div class="value">{{ $storage['file_size_mb'] }} <span style="font-size:0.75rem;color:var(--nx-text-muted);">MB</span></div>
                    </div>
                    <div class="us-health-item">
                        <div class="label">Ort. Yanıt Süresi</div>
                        <div class="value warn">{{ $health['avg_response_time'] }}</div>
                    </div>
                </div>
                <div style="margin-top:1.25rem; display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; background:var(--nx-bg-elevated); border-radius:var(--nx-radius-sm); border:1px solid var(--nx-border);">
                    <span style="font-size:0.8125rem; color:var(--nx-text-secondary);">PDF Raporlar (Toplam / Son 30 gün)</span>
                    <span style="font-family:var(--nx-font-mono); font-weight:700; color:var(--nx-text-primary);">{{ $content['pdf']['total'] }} / {{ $content['pdf']['last_30_days'] }}</span>
                </div>
            </div>
        </div>

        {{-- ═══ Module Hit Table ═══ --}}
        <div class="us-section">
            <div class="us-section-title">🚀 Modül Kullanım Yoğunluğu</div>
            @if(count($modules['top_modules']) > 0)
                <table class="us-table">
                    <thead>
                        <tr>
                            <th style="width:50%;">Modül / Rota</th>
                            <th>Hit</th>
                            <th style="width:30%;">Yoğunluk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules['top_modules'] as $mod)
                            <tr>
                                <td class="mono">{{ $mod->url }}</td>
                                <td style="font-family:var(--nx-font-mono); font-weight:600; color:var(--nx-text-primary);">{{ number_format($mod->hits) }}</td>
                                <td>
                                    <div class="us-bar">
                                        <div class="us-bar-fill" style="width: {{ min(100, ($mod->hits / max(1, $modules['top_modules'][0]->hits)) * 100) }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="us-empty">Modül kullanım verileri henüz toplanmadı. Sayfa ziyaretleri otomatik olarak kaydedilecektir.</div>
            @endif
        </div>

    </div>

    {{-- ═══ Chart.js (only if data exists) ═══ --}}
    @if(count($activity['dau_data']) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('dauChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($activity['dau_data']->pluck('date')),
                    datasets: [{
                        label: 'Aktif Kullanıcı',
                        data: @json($activity['dau_data']->pluck('count')),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111118',
                            borderColor: 'rgba(255,255,255,0.08)',
                            borderWidth: 1,
                            titleFont: { family: "'Inter', sans-serif", size: 12 },
                            bodyFont: { family: "'JetBrains Mono', monospace", size: 13 },
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            ticks: { color: '#555568', font: { family: "'JetBrains Mono', monospace", size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#555568', font: { family: "'JetBrains Mono', monospace", size: 10 }, maxRotation: 45 }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</div>
