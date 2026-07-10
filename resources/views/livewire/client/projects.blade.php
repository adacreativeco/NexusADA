<div style="animation: fadeSlideIn 0.3s ease-out;">
    <div style="margin-bottom: 32px;">
        <h1 style="font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.02em; font-family: 'Geist', sans-serif;">Projeleriniz</h1>
        <p style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">Firmanıza atanan aktif projeler, bütçe kullanımı ve teslim durumları.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 24px;">
        @forelse($projects as $project)
            <div class="client-card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 280px;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <h3 style="font-size: 16px; font-weight: 700; color: #fff; font-family: 'Geist', sans-serif;">{{ $project->title }}</h3>
                        
                        @php
                            $statusColors = [
                                'active' => ['#10b981', 'rgba(16, 185, 129, 0.1)'], 
                                'planning' => ['#3b82f6', 'rgba(59, 130, 246, 0.1)'], 
                                'on_hold' => ['#f59e0b', 'rgba(245, 158, 11, 0.1)'], 
                                'completed' => ['#9ca3af', 'rgba(156, 163, 175, 0.1)']
                            ];
                            $statusLabels = [
                                'active' => 'Aktif', 
                                'planning' => 'Planlama', 
                                'on_hold' => 'Beklemede', 
                                'completed' => 'Tamamlandı'
                            ];
                            $currentStatus = $statusColors[$project->status] ?? ['#9ca3af', 'rgba(156, 163, 175, 0.1)'];
                        @endphp
                        
                        <span style="font-size: 10px; padding: 4px 10px; border-radius: 6px; background: {{ $currentStatus[1] }}; color: {{ $currentStatus[0] }}; border: 1px solid {{ $currentStatus[0] }}30; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ $statusLabels[$project->status] ?? $project->status }}
                        </span>
                    </div>

                    @if($project->description)
                        <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                            {{ \Illuminate\Support\Str::limit($project->description, 140) }}
                        </p>
                    @endif
                </div>

                <div>
                    {{-- Stats Meta row --}}
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 16px; font-size: 12px; color: var(--text-secondary);">
                        @if($project->deadline)
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: var(--text-muted);">calendar_today</span>
                                {{ \Carbon\Carbon::parse($project->deadline)->format('d.m.Y') }}
                            </span>
                        @endif
                        @if($project->budget)
                            <span style="display: flex; align-items: center; gap: 4px; font-weight: 600; color: #fff;">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #10b981;">payments</span>
                                ₺{{ number_format($project->budget, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    {{-- Chart.js Project Graph --}}
                    @if($project->budget || isset($project->actual_revenue))
                        <div style="height: 120px; width: 100%; margin-top: 12px; position: relative; background: rgba(0,0,0,0.15); border-radius: 8px; padding: 10px;">
                            <canvas id="projectChart{{ $project->id }}"></canvas>
                        </div>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('projectChart{{ $project->id }}');
                            if (ctx) {
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Planlanan Bütçe', 'Gerçekleşen'],
                                        datasets: [{
                                            data: [{{ $project->budget ?? 0 }}, {{ $project->actual_revenue ?? 0 }}],
                                            backgroundColor: [
                                                'rgba(59, 130, 246, 0.4)', 
                                                'rgba(16, 185, 129, 0.4)'
                                            ],
                                            borderColor: [
                                                'rgba(59, 130, 246, 1)', 
                                                'rgba(16, 185, 129, 1)'
                                            ],
                                            borderWidth: 1,
                                            borderRadius: 4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { 
                                            legend: { display: false },
                                            tooltip: {
                                                backgroundColor: '#131b2e',
                                                titleColor: '#fff',
                                                bodyColor: '#e8eaf6',
                                                borderColor: 'rgba(42, 53, 80, 0.5)',
                                                borderWidth: 1
                                            }
                                        },
                                        scales: { 
                                            x: {
                                                grid: { display: false },
                                                ticks: { color: '#9ca3af', font: { size: 10 } }
                                            },
                                            y: { 
                                                beginAtZero: true, 
                                                display: false 
                                            } 
                                        }
                                    }
                                });
                            }
                        });
                        </script>
                    @endif
                </div>
            </div>
        @empty
            <div class="client-card" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <span class="material-symbols-outlined" style="font-size: 48px; color: var(--border); margin-bottom: 16px; display: inline-block;">folder_off</span>
                <p style="color: var(--text-secondary); font-weight: 600;">Henüz kayıtlı proje bulunmuyor.</p>
                <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Firmanıza ait projeler atandığında burada listelenecektir.</p>
            </div>
        @endforelse
    </div>
</div>
