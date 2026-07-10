@section('title', 'Takvim')
@section('breadcrumb', 'Takvim')

<div>
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">Takvim</h1>
            <p class="nx-page-subtitle">Etkinlikler, kampanyalar ve içerik takvimi</p>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="nx-section" wire:ignore>
        <div class="nx-section-header" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--nx-text-secondary);">
                <span style="width: 8px; height: 8px; border-radius: 2px; background: #3b82f6;"></span> Etkinlik
            </span>
            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--nx-text-secondary);">
                <span style="width: 8px; height: 8px; border-radius: 2px; background: #10b981;"></span> Kampanya
            </span>
            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--nx-text-secondary);">
                <span style="width: 8px; height: 8px; border-radius: 2px; background: #f59e0b;"></span> İçerik
            </span>
            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--nx-text-secondary);">
                <span style="width: 8px; height: 8px; border-radius: 2px; background: #ef4444;"></span> Görev
            </span>
            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--nx-text-secondary);">
                <span style="width: 8px; height: 8px; border-radius: 2px; background: #ec4899;"></span> Sosyal
            </span>
        </div>
        <div class="nx-section-body" style="padding: 16px;">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- Slide-Over Detail Panel --}}
    @if($selectedDate)
        <div class="nx-slide-over-backdrop" wire:click="closeDetail"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y, l') }}
                </h2>
                <div style="display: flex; gap: 8px;">
                    <button wire:click="openCreate('{{ $selectedDate }}')" class="nx-btn-icon" title="Yeni Oluştur" style="background: rgba(16,185,129,0.1); color: #10b981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </button>
                    <button wire:click="closeDetail" class="nx-btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="nx-slide-over-body">
                @if(empty($selectedDateItems))
                    <div style="text-align: center; padding: 40px 20px; color: var(--nx-text-secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="margin: 0 auto 12px; opacity: 0.4;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        <p>Bu tarihte kayıt bulunmuyor.</p>
                        <button wire:click="openCreate('{{ $selectedDate }}')" class="nx-btn" style="margin-top: 12px; font-size: 12px; padding: 6px 14px; background: var(--nx-accent); color: white; border: none; border-radius: 8px; cursor: pointer;">
                            Hızlı Oluştur
                        </button>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($selectedDateItems as $item)
                            <div class="nx-card" style="padding: 14px; border-left: 3px solid {{ $item['color'] }};">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: {{ $item['color'] }}; background: {{ $item['color'] }}15; padding: 2px 8px; border-radius: 4px;">
                                        {{ $item['label'] }}
                                    </span>
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px;">
                                    {{ $item['title'] }}
                                </div>
                                @if($item['notes'])
                                    <div style="font-size: 12px; color: var(--nx-text-secondary); line-height: 1.5;">
                                        {{ $item['notes'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Quick Create Modal --}}
    @if($showCreateForm)
        <div style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div wire:click="closeCreateForm" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"></div>
            <div style="position: relative; background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: 14px; padding: 24px; max-width: 420px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
                <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px; color: var(--nx-text-primary);">
                    Hızlı Oluştur — {{ \Carbon\Carbon::parse($createDate)->translatedFormat('d F Y') }}
                </h3>
                <form wire:submit="saveQuickEvent" style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label class="nx-label">Tür</label>
                        <select wire:model="createType" class="nx-select">
                            <option value="event">Etkinlik</option>
                            <option value="task">Görev</option>
                        </select>
                    </div>
                    <div>
                        <label class="nx-label">Başlık</label>
                        <input type="text" wire:model="createTitle" class="nx-input" placeholder="Etkinlik veya görev başlığı..." autofocus>
                        @error('createTitle') <span class="nx-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="nx-label">Açıklama (opsiyonel)</label>
                        <textarea wire:model="createDescription" class="nx-input" rows="2" style="resize: vertical;"></textarea>
                    </div>
                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 4px;">
                        <button type="button" wire:click="closeCreateForm" class="nx-btn nx-btn-secondary" style="font-size: 13px; padding: 8px 16px;">İptal</button>
                        <button type="submit" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: var(--nx-accent); color: white; border: none; border-radius: 8px; cursor: pointer;">Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<style>
    .fc-theme-standard td, .fc-theme-standard th { border-color: var(--nx-border); }
    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600; color: var(--nx-text-primary); }
    .fc-button-primary { background-color: var(--nx-bg-tertiary) !important; border-color: var(--nx-border) !important; color: var(--nx-text-primary) !important; text-transform: capitalize; }
    .fc-button-active { background-color: var(--nx-accent) !important; border-color: var(--nx-accent) !important; color: white !important; }
    .fc-event { border: none !important; padding: 2px 4px; font-size: 11px; cursor: pointer; border-radius: 4px; }
    .fc-daygrid-day.fc-day-today { background-color: rgba(16, 185, 129, 0.05) !important; }
    .fc-daygrid-day-number { color: var(--nx-text-primary); font-size: 13px; font-weight: 500; text-decoration: none; padding: 4px; }
    .fc-col-header-cell-cushion { color: var(--nx-text-secondary); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px !important; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('calendar')) return;
    var calendarEl = document.getElementById('calendar');
    var calendar = new window.FullCalendar.Calendar(calendarEl, {
        plugins: [ window.FullCalendar.dayGridPlugin, window.FullCalendar.interactionPlugin, window.FullCalendar.listPlugin ],
        initialView: 'dayGridMonth',
        locale: window.FullCalendar.trLocale,
        events: @json($calendarData),
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        height: 700,
        editable: true,
        droppable: true,
        eventDrop: function(info) {
            var eventType = info.event.extendedProps.type;
            var newDate = info.event.startStr;
            @this.call('updateEventDate', eventType, parseInt(info.event.id), newDate);
        },
        dateClick: function(info) {
            @this.call('openCreate', info.dateStr);
        },
        eventClick: function(info) {
            @this.call('selectDate', info.event.startStr);
        }
    });
    calendar.render();
});
</script>
@endpush
