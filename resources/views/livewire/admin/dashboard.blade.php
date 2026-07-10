@section('title', 'Home')
@section('breadcrumb', 'Home')

<div wire:init="loadDailyBriefing">
    {{-- Toast/Alert Messages --}}
    @if(session()->has('message'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--nx-radius-md); color: #10b981; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: space-between; animation: fadeSlideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                {{ session('message') }}
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 18px; line-height: 1;">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--nx-radius-md); color: #ef4444; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: space-between; animation: fadeSlideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
                {{ session('error') }}
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 18px; line-height: 1;">&times;</button>
        </div>
    @endif

    {{-- Bento Grid --}}
    <div class="nx-bento-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; animation: fadeSlideIn 0.5s var(--nx-ease-spring) forwards; opacity: 0;">
        
        {{-- Card 1: Welcome & Quick Greetings (Span 2) --}}
        <div class="nx-section" style="grid-column: span 2; background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(59,130,246,0.05) 100%); border: 1px solid rgba(16,185,129,0.15); display: flex; flex-direction: column; justify-content: space-between; padding: 24px;">
            <div>
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--nx-accent); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">token</span>
                    ADA Co-OS · Agency Command Center
                </div>
                <h2 style="font-size: 26px; font-weight: 700; color: var(--nx-text-primary); margin-bottom: 6px; letter-spacing: -0.02em; display: flex; align-items: center; gap: 8px;">
                    {{ now()->hour < 12 ? 'Günaydın' : (now()->hour < 18 ? 'İyi Günler' : 'İyi Akşamlar') }}, {{ auth()->user()->name ?? 'Kullanıcı' }} 👋
                </h2>
                <p style="font-size: 14px; color: var(--nx-text-secondary); max-width: 85%; line-height: 1.5;">
                    İşletmenizi takip eden değil, işinizi ilerleten platforma hoş geldiniz. Bugün tamamlamanız gereken eylemleri aşağıda görebilirsiniz.
                </p>

                {{-- AI Daily Briefing Box --}}
                <div style="margin-top: 20px; background: rgba(255,255,255,0.02); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); padding: 16px; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        @php
                            $hour = now()->hour;
                            $isEvening = ($hour >= 18 || $hour < 5);
                            $briefingTitle = $isEvening ? __('Günün Değerlendirmesi (Yapay Zekâ Brifingi)') : __('Güne Başlarken (Yapay Zekâ Brifingi)');
                        @endphp
                        <span style="font-size: 11px; font-weight: 700; color: var(--nx-accent); display: flex; align-items: center; gap: 4px; text-transform: uppercase;">
                            <span class="material-symbols-outlined" style="font-size: 16px;">psychology</span>
                            {{ $briefingTitle }}
                        </span>
                        <button type="button" wire:click="loadDailyBriefing(true)" wire:loading.attr="disabled" style="background: none; border: none; color: var(--nx-text-secondary); cursor: pointer; display: flex; align-items: center;" title="Yenile">
                            <span class="material-symbols-outlined" style="font-size: 15px;">sync</span>
                        </button>
                    </div>
                    <div wire:loading wire:target="loadDailyBriefing" style="font-size: 11px; color: var(--nx-text-secondary);">
                        Günlük brifing verileri derleniyor ve özetleniyor...
                    </div>
                    <div wire:loading.remove wire:target="loadDailyBriefing" style="font-size: 12px; color: var(--nx-text-secondary); line-height: 1.6; white-space: pre-wrap; font-family: var(--nx-font-sans);">
                        {!! $dailyBriefingText !!}
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 24px; flex-wrap: wrap;">
                <a href="{{ route('admin.resource.index', 'tasks') }}?action=create" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--nx-accent); color: #fff; font-size: 12px; font-weight: 600; border-radius: var(--nx-radius-md); text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(16,185,129,0.2);" onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
                    <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                    Yeni Görev Ekle
                </a>
                <a href="{{ route('admin.resource.index', 'clients') }}?action=create" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--nx-bg-input); border: 1px solid var(--nx-border); color: var(--nx-text-primary); font-size: 12px; font-weight: 600; border-radius: var(--nx-radius-md); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--nx-border)'" onmouseout="this.style.background='var(--nx-bg-input)'">
                    <span class="material-symbols-outlined" style="font-size: 16px;">person_add</span>
                    Müşteri Ekle
                </a>
                <a href="{{ route('admin.proposal') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--nx-bg-input); border: 1px solid var(--nx-border); color: var(--nx-text-primary); font-size: 12px; font-weight: 600; border-radius: var(--nx-radius-md); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--nx-border)'" onmouseout="this.style.background='var(--nx-bg-input)'">
                    <span class="material-symbols-outlined" style="font-size: 16px;">description</span>
                    Teklif Oluştur
                </a>
            </div>
        </div>

        {{-- Card 2: Aktif Zamanlayıcı Tracker --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: #10b981; animation: pulse 1.5s infinite;">play_circle</span>
                    Zaman İzleyici
                </h3>
                <a href="{{ route('admin.timesheet') }}" style="font-size: 11px; color: var(--nx-accent); text-decoration: none;">Timesheet →</a>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 12px;">
                @if($activeTimer)
                    <div>
                        <div style="font-size: 11px; color: var(--nx-text-muted);">Şu an çalışılıyor:</div>
                        <div style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 2px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            {{ $activeTimer->task?->title ?? 'Genel Çalışma' }}
                        </div>
                        <div style="font-size: 11px; color: var(--nx-text-secondary);">
                            {{ $activeTimer->project?->title ?? 'Projesiz' }}
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
                        <div style="font-size: 24px; font-weight: 700; font-family: var(--nx-font-mono); color: #10b981;">
                            {{ $activeTimer->duration_formatted }}
                        </div>
                        <button wire:click="stopTimer({{ $activeTimer->id }})" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #ef4444; color: #fff; font-size: 11px; font-weight: 600; border-radius: var(--nx-radius-sm); border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                            <span class="material-symbols-outlined" style="font-size: 14px;">stop</span>
                            Durdur
                        </button>
                    </div>
                @else
                    <div style="margin-bottom: 8px;">
                        <label style="font-size: 11px; color: var(--nx-text-secondary); display: block; margin-bottom: 4px;">Başlamak için Görev Seçin</label>
                        <select wire:model.live="selectedTaskIdForTimer" style="width: 100%; font-size: 12px; background: var(--nx-bg-input); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-sm); color: var(--nx-text-primary); padding: 6px;">
                            <option value="">-- Görev Seçin --</option>
                            @foreach($availableTasksForTimer as $task)
                                <option value="{{ $task->id }}">{{ $task->title }} ({{ $task->project?->title ?? 'Projesiz' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button wire:click="startTimer" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px; background: #10b981; color: #fff; font-size: 12px; font-weight: 600; border-radius: var(--nx-radius-sm); border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                        <span class="material-symbols-outlined" style="font-size: 14px;">play_arrow</span>
                        Zamanlayıcı Başlat
                    </button>
                @endif
            </div>
        </div>

        {{-- Card 3: Geciken Görevler (Overdue Tasks) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: #ef4444;">warning</span>
                    Geciken Eylemler
                </h3>
                <span style="font-size: 11px; padding: 2px 6px; border-radius: 10px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">
                    {{ $overdueTasks->count() }} Görev
                </span>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 8px; overflow-y: auto; max-height: 180px;">
                @forelse($overdueTasks as $task)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-sm);">
                        <div style="min-width: 0; flex: 1; margin-right: 8px;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $task->title }}
                            </div>
                            <div style="font-size: 10px; color: #ef4444; margin-top: 2px; display: flex; align-items: center; gap: 2px;">
                                <span class="material-symbols-outlined" style="font-size: 12px;">calendar_today</span>
                                {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                            </div>
                        </div>
                        
                        <button wire:click="completeTask({{ $task->id }})" style="display: inline-flex; align-items: center; gap: 2px; padding: 4px 8px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                            <span class="material-symbols-outlined" style="font-size: 12px;">done</span>
                            Tamamla
                        </button>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px;">
                        Gecikmiş göreviniz bulunmuyor! 🥳
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 4: Onay Bekleyenler (Awaiting Approvals) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: #f59e0b;">check_box</span>
                    Onay Bekleyenler
                </h3>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 8px; overflow-y: auto; max-height: 180px;">
                @forelse($pendingApprovals as $app)
                    <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-sm);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">
                                    {{ $app['title'] }}
                                </div>
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 1px;">
                                    {{ $app['desc'] }}
                                </div>
                            </div>
                            @if(isset($app['amount']))
                                <div style="font-size: 12px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">
                                    ₺{{ number_format($app['amount'], 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            @if($app['type'] === 'proposal')
                                <button wire:click="rejectProposal({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.05); color: #ef4444; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='rgba(239,68,68,0.05)'">
                                    Reddet
                                </button>
                                <button wire:click="approveProposal({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(16,185,129,0.3); background: rgba(16,185,129,0.1); color: #10b981; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                    Onayla
                                </button>
                            @elseif($app['type'] === 'contract')
                                <button wire:click="rejectContract({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.05); color: #ef4444; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='rgba(239,68,68,0.05)'">
                                    Reddet
                                </button>
                                <button wire:click="approveContract({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(16,185,129,0.3); background: rgba(16,185,129,0.1); color: #10b981; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                    Onayla
                                </button>
                            @elseif($app['type'] === 'expense')
                                <button wire:click="rejectExpense({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.05); color: #ef4444; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='rgba(239,68,68,0.05)'">
                                    Reddet
                                </button>
                                <button wire:click="approveExpense({{ $app['id'] }})" style="padding: 3px 8px; border: 1px solid rgba(16,185,129,0.3); background: rgba(16,185,129,0.1); color: #10b981; font-size: 10px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                    Onayla
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding: 32px 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 28px; color: var(--nx-text-muted);">verified</span>
                        <span>Tüm onaylar tamamlandı!</span>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 5: Bugünün Toplantıları (Today's Meetings) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: #3b82f6;">calendar_month</span>
                    Bugünün Toplantıları
                </h3>
                <a href="{{ route('admin.calendar') }}" style="font-size: 11px; color: var(--nx-accent); text-decoration: none;">Takvime Git →</a>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 8px; overflow-y: auto; max-height: 180px;">
                @forelse($todayMeetings as $meeting)
                    <div style="display: flex; align-items: flex-start; gap: 10px; padding: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-sm);">
                        <div style="font-size: 11px; font-weight: 700; color: #3b82f6; background: rgba(59,130,246,0.1); padding: 4px 6px; border-radius: 4px; font-family: var(--nx-font-mono);">
                            {{ $meeting['time'] }}
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">
                                {{ $meeting['name'] }}
                            </div>
                            @if($meeting['notes'])
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 2px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $meeting['notes'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px;">
                        Bugün planlanmış toplantı yok.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 6: Ortalama Kârlılık ve Marj (SVG Gauge Widget) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
            <div class="nx-section-header" style="width: 100%; border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 8px;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: #10b981;">bar_chart</span>
                    Kârlılık & Hedef İlerlemesi
                </h3>
            </div>
            
            <div style="display: flex; justify-content: space-around; width: 100%; align-items: center; padding: 10px 0;">
                {{-- Gauge 1: Profitability --}}
                <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
                    <svg width="80" height="80" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="var(--nx-border)" stroke-width="8" />
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#10b981" stroke-width="8" 
                                stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $avgProfit) / 100 }}" 
                                stroke-linecap="round" style="transition: stroke-dashoffset 1s ease-out;" />
                    </svg>
                    <div style="position: absolute; top: 20px; text-align: center; width: 80px;">
                        <span style="font-size: 16px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">{{ $avgProfit }}%</span>
                        <div style="font-size: 8px; color: var(--nx-text-muted); margin-top: -2px;">Kârlılık</div>
                    </div>
                </div>

                {{-- Gauge 2: Target Progress --}}
                <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
                    <svg width="80" height="80" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="var(--nx-border)" stroke-width="8" />
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#3b82f6" stroke-width="8" 
                                stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $targetProgress) / 100 }}" 
                                stroke-linecap="round" style="transition: stroke-dashoffset 1s ease-out;" />
                    </svg>
                    <div style="position: absolute; top: 20px; text-align: center; width: 80px;">
                        <span style="font-size: 16px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">{{ $targetProgress }}%</span>
                        <div style="font-size: 8px; color: var(--nx-text-muted); margin-top: -2px;">Ciro Hedefi</div>
                    </div>
                </div>
            </div>
            
            <div style="width: 100%; text-align: center; font-size: 11px; color: var(--nx-text-secondary); margin-top: 4px;">
                Cari ay gerçekleşen ciro: ₺{{ number_format($thisMonthRevenue, 0, ',', '.') }}
            </div>
        </div>

        {{-- Card 7: Nakit Akışı ve Bakiye (Finance Summary) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent);">account_balance_wallet</span>
                    Nakit Akışı & Kasa
                </h3>
                <a href="{{ route('admin.finance') }}" style="font-size: 11px; color: var(--nx-accent); text-decoration: none;">Finans Takibi →</a>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 10px; justify-content: center;">
                <div>
                    <span style="font-size: 10px; color: var(--nx-text-secondary); text-transform: uppercase; font-weight: 600;">Toplam Banka / Kasa</span>
                    <div style="font-size: 18px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono); margin-top: 2px;">
                        ₺{{ number_format($totalBalance, 2, ',', '.') }}
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div>
                        <span style="font-size: 9px; color: var(--nx-text-secondary); text-transform: uppercase;">Bu Ay Gelir</span>
                        <div style="font-size: 12px; font-weight: 600; color: #10b981; font-family: var(--nx-font-mono); margin-top: 1px;">
                            +₺{{ number_format($thisMonthIncomes, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 9px; color: var(--nx-text-secondary); text-transform: uppercase;">Bu Ay Gider</span>
                        <div style="font-size: 12px; font-weight: 600; color: #ef4444; font-family: var(--nx-font-mono); margin-top: 1px;">
                            -₺{{ number_format($thisMonthExpenses, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 4px;">
                        <span style="font-weight: 600; color: var(--nx-text-primary);">Harcama Oranı (Burn Rate)</span>
                        <span style="font-family: var(--nx-font-mono); font-weight: 700; color: {{ $burnRate > 90 ? '#ef4444' : ($burnRate > 50 ? '#f59e0b' : '#10b981') }}">{{ $burnRate }}%</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: var(--nx-border); border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ min($burnRate, 100) }}%; height: 100%; background: {{ $burnRate > 90 ? '#ef4444' : ($burnRate > 50 ? '#f59e0b' : '#10b981') }}; border-radius: 3px; transition: width 0.5s ease;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 8: Bugün Senin İçin (Akıllı Öneriler & AI Actions) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent);">insights</span>
                    Bugün Senin İçin (AI Asistanı)
                </h3>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                @forelse($recommendations as $rec)
                    <div style="padding: 10px; border-radius: var(--nx-radius-sm); border: 1px solid {{ $rec['type'] === 'warning' ? 'rgba(239,68,68,0.2)' : 'rgba(59,130,246,0.2)' }}; background: {{ $rec['type'] === 'warning' ? 'rgba(239,68,68,0.02)' : 'rgba(59,130,246,0.02)' }}; display: flex; gap: 8px; align-items: flex-start;">
                        <span class="material-symbols-outlined" style="font-size: 16px; color: {{ $rec['type'] === 'warning' ? '#ef4444' : '#3b82f6' }}; margin-top: 2px;">
                            {{ $rec['type'] === 'warning' ? 'warning' : 'info' }}
                        </span>
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $rec['title'] }}</div>
                            <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px; line-height: 1.4;">{{ $rec['text'] }}</div>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 24px; color: #10b981;">task_alt</span>
                        <span>Mükemmel! Yapay zeka bugün için her şeyin yolunda olduğunu söylüyor.</span>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 9: Unified Activity Feed (Son Aktiviteler) --}}
        <div class="nx-section" style="padding: 20px; display: flex; flex-direction: column;">
            <div class="nx-section-header" style="border-bottom: 1px solid var(--nx-border); padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="nx-section-title" style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent);">rss_feed</span>
                    Akış (User + AI + Automation)
                </h3>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column; gap: 8px; overflow-y: auto; max-height: 200px;">
                @forelse($recentActivity as $act)
                    <div style="display: flex; gap: 10px; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                        <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;
                            {{ $act['type'] === 'user' ? 'background: rgba(59,130,246,0.12); color: #3b82f6;' : '' }}
                            {{ $act['type'] === 'ai' ? 'background: rgba(16,185,129,0.12); color: #10b981;' : '' }}
                            {{ $act['type'] === 'automation' ? 'background: rgba(139,92,246,0.12); color: #8b5cf6;' : '' }}
                            {{ $act['type'] === 'system' ? 'background: rgba(107,114,128,0.12); color: #6b7280;' : '' }}">
                            <span class="material-symbols-outlined" style="font-size: 14px;">
                                {{ $act['type'] === 'user' ? 'person' : ($act['type'] === 'ai' ? 'psychology' : ($act['type'] === 'automation' ? 'bolt' : 'settings')) }}
                            </span>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-size: 11px; font-weight: 600; color: var(--nx-text-primary);">
                                {{ $act['title'] }}
                            </div>
                            @if($act['desc'])
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 1px;">
                                    {{ $act['desc'] }}
                                </div>
                            @endif
                            <div style="font-size: 9px; color: var(--nx-text-muted); margin-top: 2px;">
                                {{ $act['user'] }} · {{ $act['time'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--nx-text-secondary); font-size: 12px;">
                        Henüz aktivite kaydı yok.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
