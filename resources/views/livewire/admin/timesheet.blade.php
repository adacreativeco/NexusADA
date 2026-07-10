<div>
    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button wire:click="previousPeriod" class="nx-btn-icon" title="Önceki">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </button>
            <h2 style="font-size: 18px; font-weight: 700; letter-spacing: -0.02em; margin: 0;">{{ $periodLabel }}</h2>
            <button wire:click="nextPeriod" class="nx-btn-icon" title="Sonraki">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </button>
            <button wire:click="goToToday" class="nx-btn-ghost" style="font-size: 12px;">Bugün</button>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            {{-- View Toggle --}}
            <div style="display: flex; border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); overflow: hidden;">
                <button wire:click="setView('week')" style="padding: 6px 14px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; font-family: var(--nx-font-ui); {{ $view === 'week' ? 'background: var(--nx-accent); color: #fff;' : 'background: var(--nx-bg-card); color: var(--nx-text-secondary);' }}">Hafta</button>
                <button wire:click="setView('month')" style="padding: 6px 14px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; font-family: var(--nx-font-ui); {{ $view === 'month' ? 'background: var(--nx-accent); color: #fff;' : 'background: var(--nx-bg-card); color: var(--nx-text-secondary);' }}">Ay</button>
            </div>

            {{-- Filters --}}
            <select wire:model.live="filterUser" class="nx-input" style="width: 160px; font-size: 12px; padding: 6px 10px;">
                <option value="">Tüm Kullanıcılar</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterProject" class="nx-input" style="width: 160px; font-size: 12px; padding: 6px 10px;">
                <option value="">Tüm Projeler</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->client_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Timesheet Table --}}
    <div style="background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-lg); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: var(--nx-bg-elevated);">
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--nx-text-secondary); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; left: 0; background: var(--nx-bg-elevated); z-index: 1; min-width: 160px;">Kullanıcı</th>
                    @foreach($timesheetData['days'] as $day)
                        @php
                            $dayCarbon = \Carbon\Carbon::parse($day);
                            $isToday = $day === now()->format('Y-m-d');
                            $isWeekend = $dayCarbon->isWeekend();
                        @endphp
                        <th style="padding: 12px 8px; text-align: center; font-weight: 500; min-width: 64px; {{ $isToday ? 'color: var(--nx-accent); background: rgba(16,185,129,0.06);' : ($isWeekend ? 'color: var(--nx-text-muted);' : 'color: var(--nx-text-secondary);') }}">
                            <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">{{ $dayCarbon->translatedFormat('D') }}</div>
                            <div style="font-size: 14px; font-weight: 700;">{{ $dayCarbon->format('d') }}</div>
                        </th>
                    @endforeach
                    <th style="padding: 12px 16px; text-align: center; font-weight: 700; color: var(--nx-text-primary); font-size: 11px; text-transform: uppercase;">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timesheetData['users'] as $userId => $userData)
                    <tr style="border-top: 1px solid var(--nx-border);">
                        <td style="padding: 10px 16px; font-weight: 500; position: sticky; left: 0; background: var(--nx-bg-card); z-index: 1;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--nx-accent-glow); color: var(--nx-accent); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">{{ substr($userData['name'], 0, 1) }}</div>
                                {{ $userData['name'] }}
                            </div>
                        </td>
                        @foreach($timesheetData['days'] as $day)
                            @php
                                $minutes = $userData['days'][$day] ?? 0;
                                $hours = $minutes > 0 ? number_format($minutes / 60, 1) : '';
                                $isToday = $day === now()->format('Y-m-d');
                                $intensity = min($minutes / 480, 1); // 8 saat = max intensity
                            @endphp
                            <td style="padding: 10px 8px; text-align: center; {{ $isToday ? 'background: rgba(16,185,129,0.04);' : '' }} {{ $minutes > 0 ? 'color: var(--nx-text-primary); font-weight: 600;' : 'color: var(--nx-text-muted);' }}">
                                @if($minutes > 0)
                                    <div style="background: rgba(16,185,129,{{ 0.08 + $intensity * 0.2 }}); border-radius: var(--nx-radius-sm); padding: 4px 0; font-size: 13px;">
                                        {{ $hours }}s
                                    </div>
                                @else
                                    <span style="font-size: 12px;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="padding: 10px 16px; text-align: center; font-weight: 700; color: var(--nx-accent);">
                            {{ number_format($userData['total'] / 60, 1) }}s
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($timesheetData['days']) + 2 }}" style="padding: 40px; text-align: center; color: var(--nx-text-muted);">
                            Bu dönem için zaman kaydı bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($timesheetData['users']) > 0)
            <tfoot>
                <tr style="border-top: 2px solid var(--nx-border); background: var(--nx-bg-elevated);">
                    <td style="padding: 10px 16px; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.03em; position: sticky; left: 0; background: var(--nx-bg-elevated); z-index: 1;">Günlük Toplam</td>
                    @foreach($timesheetData['days'] as $day)
                        @php $dayTotal = $timesheetData['dailyTotals'][$day] ?? 0; @endphp
                        <td style="padding: 10px 8px; text-align: center; font-weight: 600; color: {{ $dayTotal > 0 ? 'var(--nx-text-primary)' : 'var(--nx-text-muted)' }};">
                            {{ $dayTotal > 0 ? number_format($dayTotal / 60, 1) . 's' : '—' }}
                        </td>
                    @endforeach
                    <td style="padding: 10px 16px; text-align: center; font-weight: 800; color: var(--nx-accent); font-size: 15px;">
                        {{ number_format($timesheetData['grandTotal'] / 60, 1) }}s
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
