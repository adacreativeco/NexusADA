@section('title', $config['title'] ?? 'Resource')
@section('breadcrumb', $config['title'] ?? 'Resource')

<div>
    <style>
        .nx-table-row {
            position: relative;
        }
        .nx-row-actions-hover {
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.15s ease-in-out;
        }
        .nx-table-row:hover .nx-row-actions-hover {
            opacity: 1;
            transform: scale(1);
        }
        @media (max-width: 1024px) {
            .nx-row-actions-hover {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>

    {{-- Statistics Panel --}}
    @php $stats = $this->getStats(); @endphp
    @if(!empty($stats))
        <div class="nx-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; animation: fadeSlideIn 0.3s ease;">
            @foreach($stats as $card)
                <div class="nx-section" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--nx-border-hover)'" onmouseout="this.style.borderColor='var(--nx-border)'">
                    <div style="min-width: 0; flex: 1;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--nx-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">{{ $card['label'] }}</span>
                        <div style="font-size: 20px; font-weight: 700; color: var(--nx-text-primary); font-family: var(--nx-font-mono);">{{ $card['value'] }}</div>
                        <span style="font-size: 10px; color: var(--nx-text-muted); display: block; margin-top: 2px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $card['desc'] }}</span>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--nx-radius-md); background: {{ $card['color'] }}12; display: flex; align-items: center; justify-content: center; color: {{ $card['color'] }}; flex-shrink: 0;">
                        <span class="material-symbols-outlined" style="font-size: 20px;">{{ $card['icon'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Page Header --}}
    <div class="nx-page-header">
        <div>
            <h1 class="nx-page-title">{{ $config['title'] ?? 'Resource' }}</h1>
            @if(isset($config['subtitle']))
                <p class="nx-page-subtitle">{{ $config['subtitle'] }}</p>
            @endif
        </div>
        <button class="nx-btn nx-btn-primary" wire:click="openEditor()" style="box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Yeni Ekle
        </button>
    </div>

    {{-- Toast Notification System --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 14px 20px; border-radius: 10px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); max-width: 400px;"
        :style="type === 'success' ? 'background: linear-gradient(135deg, #059669, #10b981); color: white;' : type === 'error' ? 'background: linear-gradient(135deg, #dc2626, #ef4444); color: white;' : 'background: linear-gradient(135deg, #2563eb, #3b82f6); color: white;'"
    >
        <svg x-show="type === 'success'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        <svg x-show="type === 'error'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
        <span x-text="message"></span>
        <button @click="show = false" style="margin-left: auto; opacity: 0.7; background: none; border: none; color: white; cursor: pointer; padding: 0;">&times;</button>
    </div>

    {{-- Flex Workspace layout (Alive & Adaptive) --}}
    <div class="nx-table-layout" style="display: flex; gap: 20px; align-items: flex-start; width: 100%;">
        <div class="nx-table-container" style="flex: 1; min-width: 0; margin-bottom: 0;">
        {{-- Toolbar --}}
        <div class="nx-table-toolbar">
            <div class="nx-table-search" style="position: relative;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--nx-text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Ara..."
                       class="nx-input" style="max-width: 280px; padding-left: 36px;">
            </div>

            <div class="nx-table-filters" style="display: flex; gap: 8px; align-items: center;">
                @foreach($config['filters'] ?? [] as $filter)
                    <select wire:model.live="activeFilters.{{ $filter['key'] }}"
                            class="nx-select" style="width: auto; min-width: 120px; padding: 6px 32px 6px 10px; font-size: 12px;">
                        <option value="">{{ $filter['label'] ?? ucfirst($filter['key']) }}</option>
                        @foreach($filter['options'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endforeach

                {{-- Per Page --}}
                <select wire:model.live="perPage" class="nx-select" style="width: auto; padding: 6px 32px 6px 10px; font-size: 12px;">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>

                {{-- CSV Export (direct download — bypasses Livewire) --}}
                <a href="{{ route('admin.export.csv', $resource) }}" class="nx-btn nx-btn-secondary" style="font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    CSV
                </a>

                {{-- Bulk Delete --}}
                @if(!empty($selectedIds))
                    <button wire:click="bulkDelete" wire:confirm="Seçili kayıtları silmek istediğinize emin misiniz?"
                            class="nx-btn nx-btn-danger" style="font-size: 12px; padding: 6px 12px;">
                        {{ count($selectedIds) }} seçili sil
                    </button>
                @endif
            </div>
        </div>

        {{-- Quick Entry Bar --}}
        @if(in_array($resource, ['tasks', 'clients', 'works']))
            <div style="background: var(--nx-bg-card); border-bottom: 1px solid var(--nx-border); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; font-weight: 700; color: var(--nx-accent); text-transform: uppercase; letter-spacing: 0.05em; background: rgba(59,130,246,0.1); padding: 3px 8px; border-radius: 4px; display: flex; align-items: center; gap: 4px;">
                        <span class="material-symbols-outlined" style="font-size: 14px;">bolt</span>
                        Hızlı Giriş
                    </span>
                </div>
                <form wire:submit.prevent="quickSave" style="display: flex; flex: 1; align-items: center; gap: 10px; flex-wrap: wrap; margin: 0;">
                    @if($resource === 'tasks')
                        <input type="text" wire:model="quickData.title" placeholder="Yeni görev başlığı..." class="nx-input" style="flex: 1; min-width: 200px; padding: 6px 12px; font-size: 12px;" required>
                        <select wire:model="quickData.priority" class="nx-select" style="width: auto; padding: 6px 24px 6px 10px; font-size: 12px;">
                            <option value="low">Düşük Öncelik</option>
                            <option value="medium">Orta Öncelik</option>
                            <option value="high">Yüksek Öncelik</option>
                            <option value="urgent">Acil Öncelik</option>
                        </select>
                        <select wire:model="quickData.project_id" class="nx-select" style="width: auto; padding: 6px 24px 6px 10px; font-size: 12px;">
                            <option value="">Proje Yok</option>
                            @foreach(\App\Models\Project::orderBy('title')->get() as $p)
                                <option value="{{ $p->id }}">{{ $p->title }}</option>
                            @endforeach
                        </select>
                    @elseif($resource === 'clients')
                        <input type="text" wire:model="quickData.name" placeholder="Firma Adı..." class="nx-input" style="flex: 1; min-width: 200px; padding: 6px 12px; font-size: 12px;" required>
                        <input type="text" wire:model="quickData.industry" placeholder="Sektör..." class="nx-input" style="width: auto; min-width: 150px; padding: 6px 12px; font-size: 12px;">
                    @elseif($resource === 'works')
                        <input type="text" wire:model="quickData.title" placeholder="Yeni iş başlığı..." class="nx-input" style="flex: 1; min-width: 200px; padding: 6px 12px; font-size: 12px;" required>
                        <select wire:model="quickData.client_id" class="nx-select" style="width: auto; padding: 6px 24px 6px 10px; font-size: 12px;">
                            <option value="">Müşteri Seçin...</option>
                            @foreach(\App\Models\Client::orderBy('name')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" wire:model="quickData.value" placeholder="Değer (₺)" class="nx-input" style="width: 120px; padding: 6px 12px; font-size: 12px;">
                    @endif
                    <button type="submit" class="nx-btn nx-btn-primary" style="padding: 6px 16px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                        Ekle
                    </button>
                </form>
            </div>
        @endif

        {{-- Table --}}
        <table class="nx-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" class="nx-checkbox"
                               wire:model.live="selectAll">
                    </th>
                    @foreach($config['columns'] as $col)
                        <th class="{{ $sortField === $col['key'] ? 'sorted' : '' }}"
                            @if($col['sortable'] ?? false) wire:click="sortBy('{{ $col['key'] }}')" @endif>
                            {{ $col['label'] ?? $col['key'] }}
                            @if($sortField === $col['key'])
                                <span class="sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                    @endforeach
                    <th style="width: 100px; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50" style="transition: opacity 0.2s;">
                @forelse($records as $record)
                    <tr wire:key="row-{{ $record->id }}" class="nx-table-row" style="transition: all 0.2s;">
                        <td>
                            <input type="checkbox" class="nx-checkbox"
                                   value="{{ $record->id }}"
                                   wire:model.live="selectedIds">
                        </td>
                        @foreach($config['columns'] as $col)
                            <td wire:click="openEditor({{ $record->id }})" style="cursor: pointer;">
                                @php
                                    $value = data_get($record, $col['key']);
                                @endphp

                                @if($col['avatar'] ?? false)
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--nx-gradient-accent); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                            {{ mb_substr(strval($value), 0, 1) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 500; color: var(--nx-text-primary);">{{ \Illuminate\Support\Str::limit(strval($value), 50) }}</div>
                                            @if(isset($col['sub_key']))
                                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">{{ data_get($record, $col['sub_key'], '') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($col['badge'] ?? false)
                                    @php
                                        $badgeColor = 'gray';
                                        if (isset($col['badge_colors']) && isset($col['badge_colors'][$value])) {
                                            $badgeColor = $col['badge_colors'][$value];
                                        } elseif (isset($col['badge_color'])) {
                                            $badgeColor = $col['badge_color'];
                                        }
                                        if (isset($col['badge_map']) && isset($col['badge_map'][$value])) {
                                            $badgeColor = $col['badge_map'][$value];
                                        }
                                        $dotColor = match($badgeColor) {
                                            'success' => '#10b981',
                                            'warning' => '#f59e0b',
                                            'danger' => '#ef4444',
                                            'info' => '#3b82f6',
                                            default => '#6b7280',
                                        };
                                        $label = $col['format_map'][$value] ?? $value;
                                    @endphp
                                    <span class="nx-badge nx-badge-{{ $badgeColor }}" style="display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; font-size: 10px; font-weight: 700; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 6px;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $dotColor }}; display: inline-block;"></span>
                                        {{ mb_strtoupper($label, 'UTF-8') }}
                                    </span>
                                @elseif($col['key'] === 'assignee.name' || $col['key'] === 'assigned_to')
                                    @if($value)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--nx-gradient-accent); color: white; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; text-transform: uppercase; border: 1.5px solid var(--nx-border);">
                                                {{ mb_substr(strval($value), 0, 1) }}
                                            </div>
                                            <span style="font-size: 12px; font-weight: 500; color: var(--nx-text-primary);">{{ $value }}</span>
                                        </div>
                                    @else
                                        <span style="font-size: 11px; color: var(--nx-text-muted); display: inline-flex; align-items: center; gap: 4px;">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">person_off</span>
                                            Atanmamış
                                        </span>
                                    @endif
                                @elseif($col['key'] === 'profitability_score' || ($col['suffix'] ?? '') === '%')
                                    @php
                                        $percent = intval($value);
                                        $barColor = '#10b981'; // green
                                        if ($percent < 30) {
                                            $barColor = '#ef4444'; // red
                                        } elseif ($percent < 70) {
                                            $barColor = '#f59e0b'; // yellow
                                        }
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 8px; min-width: 90px;">
                                        <div style="flex: 1; height: 6px; border-radius: 3px; background: var(--nx-bg-secondary); overflow: hidden; border: 1px solid var(--nx-border);">
                                            <div style="width: {{ $percent }}%; height: 100%; background: {{ $barColor }}; border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-family: var(--nx-font-mono); font-size: 11px; font-weight: 600; color: var(--nx-text-primary);">{{ $value }}{{ $col['suffix'] ?? '%' }}</span>
                                    </div>
                                @elseif($col['money'] ?? false)
                                    <span style="font-family: var(--nx-font-mono);">
                                        {{ $col['prefix'] ?? '' }}{{ number_format($value ?? 0, 0, ',', '.') }}
                                    </span>
                                @elseif($col['date'] ?? false)
                                    {{ $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : '-' }}
                                @elseif($col['datetime'] ?? false)
                                    {{ $value ? \Carbon\Carbon::parse($value)->format('d.m.Y H:i') : '-' }}
                                @elseif($col['numeric'] ?? false)
                                    <span style="font-family: var(--nx-font-mono);">
                                        {{ $value }}{{ $col['suffix'] ?? '' }}
                                    </span>
                                @elseif($col['relation'] ?? false)
                                    {{ data_get($record, $col['relation'] . '.name', '-') }}
                                @elseif($col['color_chips'] ?? false)
                                    @php
                                        $colors = array_map('trim', explode(',', strval($value)));
                                    @endphp
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        @foreach($colors as $color)
                                            @if(preg_match('/^#[0-9a-fA-F]{3,6}$/', $color))
                                                <div onclick="navigator.clipboard.writeText('{{ $color }}'); alert('{{ $color }} kopyalandı!'); event.stopPropagation();" 
                                                     style="width: 20px; height: 20px; border-radius: 4px; background: {{ $color }}; border: 1px solid var(--nx-border); cursor: pointer;" 
                                                     title="{{ $color }} - Kopyalamak için tıklayın">
                                                </div>
                                            @else
                                                <span style="font-size: 11px; color: var(--nx-text-secondary);">{{ $color }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div style="color: var(--nx-text-primary);">
                                        {{ \Illuminate\Support\Str::limit(strval($value), 50) }}
                                    </div>
                                    @if(isset($col['sub_key']) && !($col['avatar'] ?? false))
                                        <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">{{ data_get($record, $col['sub_key'], '') }}</div>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align: right;">
                            <div class="nx-row-actions-hover" style="display: flex; gap: 4px; justify-content: flex-end;">
                                {{-- Custom Row Actions --}}
                                @foreach($config['row_actions'] ?? [] as $action)
                                    @if(!isset($action['visible']) || call_user_func($action['visible'], $record))
                                        @if(isset($action['url']))
                                            {{-- URL-based action (e.g. PDF download) --}}
                                            <a href="{{ str_replace('{id}', $record->id, $action['url']) }}{{ str_contains($action['url'], '?') ? '&' : '?' }}t={{ time() }}"
                                               target="{{ $action['target'] ?? '_self' }}"
                                               class="nx-btn-icon" title="{{ $action['label'] }}"
                                               style="color: var(--nx-accent);">
                                                @if(isset($action['icon_path']))
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon_path'] }}"/>
                                                    </svg>
                                                @elseif(isset($action['icon']))
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">{{ $action['icon'] }}</span>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                                    </svg>
                                                @endif
                                            </a>
                                        @else
                                            {{-- Handler-based action --}}
                                            <button wire:click="executeAction('{{ $action['key'] }}', {{ $record->id }})"
                                                    class="nx-btn-icon" title="{{ $action['label'] }}">
                                                @if(isset($action['icon_path']))
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon_path'] }}"/>
                                                    </svg>
                                                @elseif(isset($action['icon']))
                                                    <span class="material-symbols-outlined" style="font-size: 16px;">{{ $action['icon'] }}</span>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        @endif
                                    @endif
                                @endforeach

                                {{-- Edit --}}
                                <button wire:click="openEditor({{ $record->id }})" class="nx-btn-icon" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>

                                {{-- Delete --}}
                                <button wire:click="confirmDelete({{ $record->id }})"
                                        class="nx-btn-icon" title="Sil" style="color: var(--nx-danger);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($config['columns']) + 2 }}" style="padding: 60px 24px; text-align: center;">
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--nx-bg-hover); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--nx-text-muted);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </div>
                            <h3 style="font-size: 15px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px;">Henüz Kayıt Yok</h3>
                            <p style="font-size: 13px; color: var(--nx-text-secondary); max-width: 300px; margin: 0 auto 16px;">Bu modülde henüz bir kayıt bulunmuyor. Yeni bir kayıt ekleyerek başlayabilirsiniz.</p>
                            <button wire:click="openEditor()" class="nx-btn nx-btn-primary" style="box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                                İlk Kaydı Ekle
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer / Pagination --}}
        @if($records->hasPages())
            <div class="nx-table-footer">
                <span>{{ $records->total() }} kayıttan {{ $records->firstItem() }}–{{ $records->lastItem() }} gösteriliyor</span>
                <div class="nx-pagination">
                    {{ $records->links('livewire.admin.partials.pagination') }}
                </div>
            </div>
        @endif
        </div>

        {{-- Right Side Live Activity & AI Insights Sidebar (Hidden on mobile) --}}
        <div class="nx-sidebar-feed" style="width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 16px;">
            <div class="nx-section" style="padding: 16px; display: flex; flex-direction: column; gap: 12px; margin-bottom: 0;">
                <div style="border-bottom: 1px solid var(--nx-border); padding-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="color: var(--nx-accent); font-size: 18px;">analytics</span>
                    <span style="font-weight: 600; font-size: 13px; color: var(--nx-text-primary);">Sistem Aktivite Akışı</span>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 8px; max-height: 380px; overflow-y: auto; padding-right: 4px;">
                    @php
                        $sidebarActivities = \App\Models\Activity::latest()->take(6)->get();
                    @endphp
                    @forelse($sidebarActivities as $act)
                        <div style="padding: 8px; border-radius: var(--nx-radius-sm); background: rgba(255,255,255,0.02); border: 1px solid var(--nx-border); display: flex; gap: 8px; align-items: flex-start; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(16,185,129,0.25)'" onmouseout="this.style.borderColor='var(--nx-border)'">
                            <div style="width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0;
                                {{ $act->type === 'user' ? 'background: rgba(59,130,246,0.12); color: #3b82f6;' : '' }}
                                {{ $act->type === 'ai' ? 'background: rgba(16,185,129,0.12); color: #10b981;' : '' }}
                                {{ $act->type === 'automation' ? 'background: rgba(139,92,246,0.12); color: #8b5cf6;' : '' }}
                                {{ $act->type === 'system' ? 'background: rgba(107,114,128,0.12); color: #6b7280;' : '' }}">
                                <span class="material-symbols-outlined" style="font-size: 12px;">
                                    {{ $act->type === 'user' ? 'person' : ($act->type === 'ai' ? 'psychology' : ($act->type === 'automation' ? 'bolt' : 'settings')) }}
                                </span>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--nx-text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $act->title }}
                                </div>
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 1px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3;">
                                    {{ $act->description }}
                                </div>
                                <div style="font-size: 9px; color: var(--nx-text-muted); margin-top: 2px;">
                                    {{ $act->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; color: var(--nx-text-muted); font-size: 11px;">
                            Henüz aktivite kaydı bulunmuyor.
                        </div>
                    @endforelse
                </div>

                {{-- Dynamic AI Contextual Advice --}}
                <div style="border-top: 1px solid var(--nx-border); padding-top: 12px; margin-top: 4px;">
                    <div style="display: flex; align-items: center; gap: 4px; color: #8b5cf6; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
                        <span class="material-symbols-outlined" style="font-size: 14px;">smart_toy</span>
                        AI Taktiksel Öneri
                    </div>
                    <div style="font-size: 11px; color: var(--nx-text-secondary); line-height: 1.4; background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,0.15); border-radius: var(--nx-radius-sm); padding: 8px; text-align: left;">
                        @if($this->resource === 'clients')
                            Stratejik önemi yüksek olan kurumsal firmaların proje/teklif durumlarını yakından izlemek için kayda tıklayıp "İlişkiler" sekmesine göz atın.
                        @elseif($this->resource === 'works')
                            İş süreçlerindeki finansal hacmi büyütmek için teklif durumlarını "Accepted" olarak güncelleyip otomatik sözleşmeleri tetikleyin.
                        @elseif($this->resource === 'tasks')
                            Overdue (gecikmiş) olan eylemleri gecikme bildirimleri gitmeden önce tamamlayın veya sorumlu ekip üyesine hatırlatma geçin.
                        @elseif($this->resource === 'proposals')
                            Taslak halindeki tekliflerin bütçe kalemlerini doldurup "Onaya Gönder" butonuyla yöneticilerinize bildirim fırlatın.
                        @elseif($this->resource === 'contracts')
                            Bitiş tarihi yaklaşan aktif sözleşmeler için otomatik yenileme scheduler otomasyonlarını konsol üzerinden takip edebilirsiniz.
                        @else
                            Bu modüldeki kayıtlar için detaylı analizleri ve NVIDIA AI raporlarını kayıt satırına tıklayıp "Yapay Zeka" sekmesinden üretebilirsiniz.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ SLIDE-OVER EDITOR ═══════════ --}}
    @if($editorOpen)
        <div class="nx-slide-over-backdrop" wire:click="closeEditor"></div>
        <div class="nx-slide-over">
            <div class="nx-slide-over-header">
                <h2 class="nx-slide-over-title">
                    {{ $editingId ? 'Düzenle' : 'Yeni Kayıt' }}
                </h2>
                <button wire:click="closeEditor" class="nx-btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="nx-slide-over-body">
                @if(!$editingId)
                    <form wire:submit.prevent="saveEditor">
                        @foreach($config['sections'] ?? [] as $section)
                            <div class="nx-section" style="margin-bottom: 20px;">
                                <div class="nx-section-header">
                                    <h3 class="nx-section-title">{{ $section['title'] }}</h3>
                                    @if(isset($section['description']))
                                        <p class="nx-section-desc">{{ $section['description'] }}</p>
                                    @endif
                                </div>
                                <div class="nx-section-body">
                                    <div class="nx-form-grid" style="grid-template-columns: repeat({{ $section['columns'] ?? 2 }}, 1fr);">
                                        @foreach($section['fields'] ?? [] as $field)
                                            <div class="nx-form-group {{ ($field['span'] ?? '') === 'full' ? 'col-span-full' : '' }}">
                                                <label class="nx-label">
                                                    {{ $field['label'] }}
                                                    @if($field['required'] ?? false)
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>

                                                @switch($field['type'] ?? 'text')
                                                    @case('text')
                                                        <input type="text"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input"
                                                               placeholder="{{ $field['placeholder'] ?? '' }}">
                                                        @break

                                                    @case('number')
                                                        @if(isset($field['prefix']))
                                                            <div class="nx-input-prefix">
                                                                <span class="prefix">{{ $field['prefix'] }}</span>
                                                                <input type="number"
                                                                       wire:model="editorData.{{ $field['key'] }}"
                                                                       class="nx-input"
                                                                       placeholder="{{ $field['placeholder'] ?? '0' }}">
                                                            </div>
                                                        @else
                                                            <input type="number"
                                                                   wire:model="editorData.{{ $field['key'] }}"
                                                                   class="nx-input"
                                                                   placeholder="{{ $field['placeholder'] ?? '0' }}">
                                                        @endif
                                                        @break

                                                    @case('textarea')
                                                        <textarea wire:model="editorData.{{ $field['key'] }}"
                                                                  class="nx-textarea"
                                                                  placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                  rows="{{ $field['rows'] ?? 3 }}"></textarea>
                                                        @break

                                                    @case('select')
                                                        <select wire:model="editorData.{{ $field['key'] }}" class="nx-select">
                                                            <option value="">Seçiniz...</option>
                                                            @foreach($field['options'] ?? [] as $val => $label)
                                                                <option value="{{ $val }}">{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('belongs_to')
                                                        <select wire:model="editorData.{{ $field['key'] }}" class="nx-select">
                                                            <option value="">Seçiniz...</option>
                                                            @foreach($field['model']::all() as $option)
                                                                <option value="{{ $option->id }}">{{ $option->{$field['display']} }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('date')
                                                        <input type="date"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                        @break

                                                    @case('datetime')
                                                        <input type="datetime-local"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                        @break

                                                    @case('toggle')
                                                        <label class="nx-toggle" style="margin-top: 4px;">
                                                            <div class="nx-toggle-track {{ ($editorData[$field['key']] ?? false) ? 'active' : '' }}"
                                                                 wire:click="$toggle('editorData.{{ $field['key'] }}')">
                                                                <div class="nx-toggle-thumb"></div>
                                                            </div>
                                                            <span style="font-size: 13px; color: var(--nx-text-secondary);">
                                                                {{ ($editorData[$field['key']] ?? false) ? 'Evet' : 'Hayır' }}
                                                            </span>
                                                        </label>
                                                        @break

                                                    @case('file')
                                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                                            <input type="file"
                                                                   wire:model="formFiles.{{ $field['key'] }}"
                                                                   class="nx-input" style="padding: 4px 8px; font-size: 12px;">
                                                            <div wire:loading wire:target="formFiles.{{ $field['key'] }}" style="font-size: 10px; color: var(--nx-accent); margin-top: 4px;">Dosya yükleniyor...</div>
                                                        </div>
                                                        @break

                                                    @default
                                                        <input type="text"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                @endswitch

                                                @error('formFiles.' . $field['key'])
                                                    <span class="nx-error-text">{{ $message }}</span>
                                                @enderror
                                                @error('editorData.' . $field['key'])
                                                    <span class="nx-error-text">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </form>
                @else
                    {{-- Tab Navigation (only when editing) --}}
                    <div style="display: flex; border-bottom: 1px solid var(--nx-border); padding: 0 16px;">
                        <button type="button" wire:click.prevent="setTab('info')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'info' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'info' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            Bilgiler
                        </button>
                        <button type="button" wire:click.prevent="setTab('notes')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'notes' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'notes' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            Notlar <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordNotes) }}</span>
                        </button>
                        <button type="button" wire:click.prevent="setTab('files')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'files' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'files' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            Dosyalar <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordDocuments) }}</span>
                        </button>
                        @if($config['has_interactions'] ?? false)
                            <button type="button" wire:click.prevent="setTab('interactions')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'interactions' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'interactions' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                                İletişim <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordInteractions) }}</span>
                            </button>
                        @endif
                        @if($resource === 'clients')
                            <button type="button" wire:click.prevent="setTab('activities')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'activities' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'activities' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                                📜 Zaman Tüneli <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordActivities) }}</span>
                            </button>
                        @endif
                        @if(in_array($resource, ['tasks', 'projects']))
                            <button type="button" wire:click.prevent="setTab('time')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'time' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'time' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                                ⏱ Zaman <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordTimeLogs) }}</span>
                            </button>
                        @endif
                        <button type="button" wire:click.prevent="setTab('comments')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'comments' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'comments' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            💬 Tartışma <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordComments) }}</span>
                        </button>
                        <button type="button" wire:click.prevent="setTab('relations')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'relations' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'relations' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            🔗 İlişkili Kayıtlar <span style="background: var(--nx-bg-tertiary); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px;">{{ count($recordRelations) }}</span>
                        </button>
                        <button type="button" wire:click.prevent="setTab('ai-actions')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'ai-actions' ? 'var(--nx-accent)' : 'transparent' }}; color: {{ $activeTab === 'ai-actions' ? 'var(--nx-accent)' : 'var(--nx-text-secondary)' }};">
                            🤖 Yapay Zekâ
                        </button>
                    </div>
                @endif

                {{-- TAB 1: Bilgiler (Form) --}}
                <div style="display: {{ $activeTab === 'info' ? 'block' : 'none' }};">
                    <form wire:submit.prevent="saveEditor">
                        @foreach($config['sections'] ?? [] as $section)
                            <div class="nx-section" style="margin-bottom: 0; border-radius: 0; border-left: 0; border-right: 0;">
                                <div class="nx-section-header">
                                    <h3 class="nx-section-title">{{ $section['title'] }}</h3>
                                </div>
                                <div class="nx-section-body">
                                    <div class="nx-form-grid">
                                        @foreach($section['fields'] ?? [] as $field)
                                            <div class="nx-form-group {{ ($field['span'] ?? 1) === 2 ? 'span-2' : '' }}">
                                                <label class="nx-label">
                                                    {{ $field['label'] }}
                                                    @if($field['required'] ?? false) <span class="required">*</span> @endif
                                                </label>

                                                @switch($field['type'])
                                                    @case('text')
                                                        <input type="text"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input"
                                                               placeholder="{{ $field['placeholder'] ?? '' }}">
                                                        @break

                                                    @case('number')
                                                        <input type="number"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input"
                                                               step="{{ $field['step'] ?? '1' }}">
                                                        @break

                                                    @case('textarea')
                                                        <textarea wire:model="editorData.{{ $field['key'] }}"
                                                                  class="nx-textarea"
                                                                  placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                  rows="{{ $field['rows'] ?? 3 }}"></textarea>
                                                        @break

                                                    @case('select')
                                                        <select wire:model="editorData.{{ $field['key'] }}" class="nx-select">
                                                            <option value="">Seçiniz...</option>
                                                            @foreach($field['options'] ?? [] as $val => $label)
                                                                <option value="{{ $val }}">{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('belongs_to')
                                                        <select wire:model="editorData.{{ $field['key'] }}" class="nx-select">
                                                            <option value="">Seçiniz...</option>
                                                            @foreach($field['model']::all() as $option)
                                                                <option value="{{ $option->id }}">{{ $option->{$field['display']} }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('date')
                                                        <input type="date"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                        @break

                                                    @case('datetime')
                                                        <input type="datetime-local"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                        @break

                                                    @case('toggle')
                                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                                            <input type="checkbox"
                                                                   wire:model="editorData.{{ $field['key'] }}"
                                                                   class="nx-input"
                                                                   style="width: auto;">
                                                            <span style="font-size: 12px; color: var(--nx-text-secondary);">{{ $field['description'] ?? 'Aktif' }}</span>
                                                        </label>
                                                        @break

                                                    @case('file')
                                                        <div style="display: flex; flex-direction: column; gap: 6px;">
                                                            @php
                                                                $fileVal = $editorData[$field['key']] ?? '';
                                                                if (is_array($fileVal)) {
                                                                    $fileVal = $fileVal[0] ?? '';
                                                                }
                                                            @endphp
                                                            @if(!empty($fileVal))
                                                                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; background: var(--nx-bg-tertiary); padding: 6px 12px; border-radius: 6px; border: 1px solid var(--nx-border);">
                                                                    <span class="material-symbols-outlined" style="font-size: 18px; color: var(--nx-accent);">attachment</span>
                                                                    <a href="{{ $fileVal }}" target="_blank" style="color: var(--nx-accent); font-weight: 500; text-decoration: none; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; flex: 1;">
                                                                        {{ basename($fileVal) }}
                                                                    </a>
                                                                    <button type="button" wire:click="$set('editorData.{{ $field['key'] }}', '')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 2px;">
                                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                            <input type="file"
                                                                   wire:model="formFiles.{{ $field['key'] }}"
                                                                   class="nx-input" style="padding: 4px 8px; font-size: 12px;">
                                                            <div wire:loading wire:target="formFiles.{{ $field['key'] }}" style="font-size: 10px; color: var(--nx-accent); margin-top: 4px;">Dosya yükleniyor...</div>
                                                        </div>
                                                        @break

                                                    @default
                                                        <input type="text"
                                                               wire:model="editorData.{{ $field['key'] }}"
                                                               class="nx-input">
                                                @endswitch

                                                @error('formFiles.' . $field['key'])
                                                    <span class="nx-error-text">{{ $message }}</span>
                                                @enderror
                                                @error('editorData.' . $field['key'])
                                                    <span class="nx-error-text">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>

                {{-- TAB 2: Notlar --}}
                @if($editingId)
                <div style="display: {{ $activeTab === 'notes' ? 'block' : 'none' }};">
                    <div style="padding: 12px; border-bottom: 1px solid var(--nx-border); display: flex; gap: 8px;">
                        <textarea wire:model="newNote" placeholder="Not ekle..." class="nx-textarea" rows="2" style="flex: 1; margin: 0;"></textarea>
                        <button wire:click="addNote" class="nx-btn nx-btn-primary" style="align-self: flex-end; padding: 6px 14px; font-size: 12px;">Ekle</button>
                    </div>
                    @forelse($recordNotes as $note)
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--nx-border); display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;" wire:key="note-{{ $note['id'] }}">
                            <div style="flex: 1;">
                                <div style="font-size: 12px; color: var(--nx-text-primary); line-height: 1.5;">{{ $note['content'] }}</div>
                                <div style="font-size: 10px; color: var(--nx-text-secondary); margin-top: 4px;">{{ $note['user'] }} · {{ $note['time'] }}</div>
                            </div>
                            <button wire:click="deleteNote({{ $note['id'] }})" wire:confirm="Bu notu silmek istediğinize emin misiniz?" class="nx-btn-icon" title="Sil" style="color: var(--nx-danger); flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz not eklenmemiş.</div>
                    @endforelse
                </div>

                {{-- TAB 3: Dosyalar --}}
                <div style="display: {{ $activeTab === 'files' ? 'block' : 'none' }};">
                    {{-- Upload --}}
                    <div style="padding: 12px; border-bottom: 1px solid var(--nx-border);">
                        <div style="display: flex; gap: 8px; align-items: flex-end;">
                            <div style="flex: 1;">
                                <label class="nx-label" style="font-size: 11px;">Dosya Seç (max 10MB)</label>
                                <input type="file" wire:model="uploadFile" style="font-size: 11px; color: var(--nx-text-secondary);">
                            </div>
                            <select wire:model="docCategory" class="nx-select" style="width: auto; min-width: 100px; padding: 4px 8px; font-size: 11px;">
                                <option value="other">Diğer</option>
                                <option value="brief">Brief</option>
                                <option value="contract">Sözleşme</option>
                                <option value="asset">Varlık</option>
                                <option value="report">Rapor</option>
                            </select>
                            <button wire:click="uploadDocument" class="nx-btn nx-btn-primary" style="padding: 6px 14px; font-size: 12px;" {{ !$uploadFile ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="uploadDocument">Yükle</span>
                                <span wire:loading wire:target="uploadDocument">...</span>
                            </button>
                        </div>
                        @error('uploadFile') <span class="nx-error-text" style="margin-top: 4px;">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="uploadFile" style="font-size: 10px; color: var(--nx-accent); margin-top: 4px;">Dosya hazırlanıyor...</div>
                    </div>

                    {{-- File List --}}
                    @forelse($recordDocuments as $doc)
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--nx-border); display: flex; align-items: center; gap: 10px;" wire:key="doc-{{ $doc['id'] }}">
                            {{-- Icon --}}
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--nx-bg-tertiary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                @if(str_contains($doc['mime'] ?? '', 'pdf'))
                                    <span style="font-size: 10px; font-weight: 700; color: #ef4444;">PDF</span>
                                @elseif(str_contains($doc['mime'] ?? '', 'image'))
                                    <span style="font-size: 10px; font-weight: 700; color: #8b5cf6;">IMG</span>
                                @elseif(str_contains($doc['mime'] ?? '', 'spreadsheet') || str_contains($doc['mime'] ?? '', 'csv'))
                                    <span style="font-size: 10px; font-weight: 700; color: #10b981;">XLS</span>
                                @else
                                    <span style="font-size: 10px; font-weight: 700; color: #3b82f6;">DOC</span>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 500; color: var(--nx-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $doc['name'] }}</div>
                                <div style="font-size: 10px; color: var(--nx-text-secondary);">{{ $doc['size'] }} · {{ $doc['uploader'] }} · {{ $doc['time'] }}</div>
                            </div>
                            <span style="font-size: 9px; padding: 1px 6px; border-radius: 3px; background: var(--nx-bg-tertiary); color: var(--nx-text-secondary); text-transform: uppercase;">{{ $doc['category'] }}</span>
                            <button wire:click="downloadDocument({{ $doc['id'] }})" class="nx-btn-icon" title="İndir" style="color: var(--nx-accent);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </button>
                            <button wire:click="deleteDocument({{ $doc['id'] }})" wire:confirm="Bu dosyayı silmek istediğinize emin misiniz?" class="nx-btn-icon" title="Sil" style="color: var(--nx-danger);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz dosya eklenmemiş.</div>
                    @endforelse
                </div>
                @endif

                {{-- TAB 4: İletişim (only for clients) --}}
                @if(($config['has_interactions'] ?? false) && $editingId)
                <div style="display: {{ $activeTab === 'interactions' ? 'block' : 'none' }};">
                    {{-- Add Interaction Form --}}
                    <div style="padding: 12px; border-bottom: 1px solid var(--nx-border);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <select wire:model="interactionForm.type" class="nx-select" style="padding: 6px 10px; font-size: 11px;">
                                <option value="call">📞 Telefon</option>
                                <option value="email">✉️ E-posta</option>
                                <option value="meeting">🤝 Toplantı</option>
                                <option value="note">📋 Not</option>
                                <option value="demo">🖥️ Demo</option>
                                <option value="proposal">📄 Teklif</option>
                            </select>
                            <input type="text" wire:model="interactionForm.subject" class="nx-input" placeholder="Konu *" style="padding: 6px 10px; font-size: 11px;">
                            <textarea wire:model="interactionForm.content" class="nx-textarea" placeholder="Detay..." rows="2" style="grid-column: span 2; font-size: 11px;"></textarea>
                            <input type="datetime-local" wire:model="interactionForm.interaction_date" class="nx-input" style="padding: 6px 10px; font-size: 11px;">
                            <select wire:model="interactionForm.outcome" class="nx-select" style="padding: 6px 10px; font-size: 11px;">
                                <option value="">Sonuç...</option>
                                <option value="positive">✅ Olumlu</option>
                                <option value="neutral">➖ Nötr</option>
                                <option value="negative">❌ Olumsuz</option>
                                <option value="pending">⏳ Beklemede</option>
                            </select>
                        </div>
                        <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                            <button wire:click="addInteraction" class="nx-btn nx-btn-primary" style="padding: 6px 14px; font-size: 12px;">Ekle</button>
                        </div>
                    </div>

                    {{-- Interaction Timeline --}}
                    @forelse($recordInteractions as $ix)
                        @php
                            $typeConfig = [
                                'call'=>['icon'=>'📞','color'=>'#3b82f6'], 'email'=>['icon'=>'✉️','color'=>'#10b981'],
                                'meeting'=>['icon'=>'🤝','color'=>'#8b5cf6'], 'note'=>['icon'=>'📋','color'=>'#6b7280'],
                                'demo'=>['icon'=>'🖥️','color'=>'#f59e0b'], 'proposal'=>['icon'=>'📄','color'=>'#ef4444'],
                            ];
                            $tc = $typeConfig[$ix['type']] ?? ['icon'=>'📋','color'=>'#6b7280'];
                            $outLabels = ['positive'=>'✅ Olumlu','neutral'=>'➖ Nötr','negative'=>'❌ Olumsuz','pending'=>'⏳ Beklemede'];
                        @endphp
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--nx-border); display: flex; gap: 10px;" wire:key="ix-{{ $ix['id'] }}">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $tc['color'] }}15; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">
                                {{ $tc['icon'] }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $ix['subject'] }}</div>
                                @if($ix['content'])
                                    <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px; line-height: 1.4;">{{ $ix['content'] }}</div>
                                @endif
                                <div style="display: flex; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                    <span style="font-size: 10px; color: var(--nx-text-tertiary);">{{ $ix['date'] }}</span>
                                    <span style="font-size: 10px; color: var(--nx-text-tertiary);">{{ $ix['user'] }}</span>
                                    @if($ix['duration'])<span style="font-size: 10px; color: var(--nx-text-tertiary);">{{ $ix['duration'] }} dk</span>@endif
                                    @if($ix['outcome'])<span style="font-size: 10px;">{{ $outLabels[$ix['outcome']] ?? $ix['outcome'] }}</span>@endif
                                    @if($ix['follow_up'])<span style="font-size: 10px; color: var(--nx-warning); font-weight: 600;">📅 Takip: {{ $ix['follow_up'] }}</span>@endif
                                </div>
                            </div>
                            <button wire:click="deleteInteraction({{ $ix['id'] }})" wire:confirm="Bu etkileşimi silmek istediğinize emin misiniz?" class="nx-btn-icon" title="Sil" style="color: var(--nx-danger); flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz etkileşim kaydı yok.</div>
                    @endforelse
                </div>
                @endif

                {{-- TAB: Client Activities Timeline --}}
                @if($resource === 'clients' && $editingId)
                <div style="display: {{ $activeTab === 'activities' ? 'block' : 'none' }};">
                    <div style="padding: 12px; background: rgba(59,130,246,0.02); border-bottom: 1px solid var(--nx-border); font-size: 11px; color: var(--nx-text-secondary);">
                        Müşteriye ve ilişkili proje, iş ve teklif süreçlerine ait son 10 aktivite
                    </div>
                    @forelse($recordActivities as $act)
                        @php
                            $actIcon = 'history';
                            $actColor = 'var(--nx-text-secondary)';
                            if ($act['type'] === 'user') {
                                $actIcon = 'person';
                                $actColor = '#3b82f6';
                            } elseif ($act['type'] === 'ai') {
                                $actIcon = 'psychology';
                                $actColor = '#10b981';
                            } elseif ($act['type'] === 'automation') {
                                $actIcon = 'bolt';
                                $actColor = '#f59e0b';
                            }
                        @endphp
                        <div style="padding: 12px; border-bottom: 1px solid var(--nx-border); display: flex; gap: 10px; align-items: start;">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background: {{ $actColor }}10; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: {{ $actColor }};">
                                <span class="material-symbols-outlined" style="font-size: 16px;">{{ $actIcon }}</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $act['title'] }}</div>
                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">{{ $act['description'] }}</div>
                                <div style="font-size: 10px; color: var(--nx-text-muted); margin-top: 4px; display: flex; gap: 8px;">
                                    <span>{{ $act['time'] }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $act['user'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz aktivite kaydı bulunmuyor.</div>
                    @endforelse
                </div>
                @endif

                {{-- TAB 5: Time Tracking (Tasks & Projects) --}}
                @if(in_array($resource, ['tasks', 'projects']) && $editingId)
                <div style="display: {{ $activeTab === 'time' ? 'block' : 'none' }};">
                    {{-- Timer Controls --}}
                    <div style="padding: 12px; border-bottom: 1px solid var(--nx-border);">
                        @php $hasRunning = collect($recordTimeLogs)->contains('is_running', true); @endphp
                        <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 10px;">
                            @if($hasRunning)
                                <button wire:click="stopTimer" class="nx-btn" style="background: #ef4444; color: #fff; border: none; padding: 8px 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; border-radius: var(--nx-radius-md);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="1"/></svg>
                                    Durdur
                                </button>
                                <span style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: pulse 1.5s infinite;"></span>
                                    Zamanlayıcı çalışıyor...
                                </span>
                            @else
                                <button wire:click="startTimer" class="nx-btn" style="background: #10b981; color: #fff; border: none; padding: 8px 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; border-radius: var(--nx-radius-md);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                                    Başlat
                                </button>
                            @endif
                        </div>

                        {{-- Manual Time Entry --}}
                        <div style="display: flex; gap: 8px; align-items: flex-end;">
                            <div style="flex-shrink: 0;">
                                <label class="nx-label" style="font-size: 10px;">Dakika</label>
                                <input type="number" wire:model="manualMinutes" class="nx-input" min="1" placeholder="30" style="width: 80px; padding: 6px 10px; font-size: 12px;">
                            </div>
                            <div style="flex: 1;">
                                <label class="nx-label" style="font-size: 10px;">Açıklama</label>
                                <input type="text" wire:model="manualTimeDescription" class="nx-input" placeholder="Ne üzerinde çalışıldı?" style="padding: 6px 10px; font-size: 12px;">
                            </div>
                            <button wire:click="addManualTime" class="nx-btn nx-btn-secondary" style="padding: 6px 14px; font-size: 12px;" {{ $manualMinutes <= 0 ? 'disabled' : '' }}>+ Ekle</button>
                        </div>
                    </div>

                    {{-- Time Logs List --}}
                    @forelse($recordTimeLogs as $tl)
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--nx-border); display: flex; align-items: center; gap: 10px;" wire:key="tl-{{ $tl['id'] }}">
                            <div style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; {{ $tl['is_running'] ? 'background: rgba(16,185,129,0.15); color: #10b981;' : ($tl['is_manual'] ? 'background: rgba(139,92,246,0.1); color: #8b5cf6;' : 'background: var(--nx-bg-tertiary); color: var(--nx-text-secondary);') }}">
                                @if($tl['is_running']) ⏱ @elseif($tl['is_manual']) ✍ @else ▶ @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">
                                    {{ $tl['duration'] }}
                                    @if($tl['billable'])
                                        <span style="font-size: 9px; padding: 1px 5px; border-radius: 3px; background: rgba(16,185,129,0.1); color: #10b981; margin-left: 4px;">₺</span>
                                    @endif
                                </div>
                                @if($tl['description'])
                                    <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 1px;">{{ $tl['description'] }}</div>
                                @endif
                                <div style="font-size: 10px; color: var(--nx-text-tertiary); margin-top: 2px;">{{ $tl['user'] }} · {{ $tl['started_at'] }}</div>
                            </div>
                            <button wire:click="deleteTimeLog({{ $tl['id'] }})" wire:confirm="Bu zaman kaydını silmek istediğinize emin misiniz?" class="nx-btn-icon" title="Sil" style="color: var(--nx-danger); flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz zaman kaydı yok.</div>
                    @endforelse
                </div>
                @endif

                {{-- TAB 6: Comments / Discussion --}}
                @if($editingId)
                <div style="display: {{ $activeTab === 'comments' ? 'block' : 'none' }};" wire:poll.15s="loadComments">
                    {{-- New Comment Form --}}
                    <div style="padding: 12px; border-bottom: 1px solid var(--nx-border);">
                        @if($replyingTo)
                            <div style="font-size: 11px; color: var(--nx-accent); margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                                <span>↩ Yanıtlanıyor...</span>
                                <button wire:click="setReplyingTo(null)" style="border: none; background: none; color: var(--nx-text-secondary); cursor: pointer; font-size: 11px;">İptal</button>
                            </div>
                        @endif
                        <div style="display: flex; gap: 8px;">
                            <textarea wire:model="newComment" placeholder="{{ $replyingTo ? 'Yanıtınız...' : 'Yorum yazın... (@isim ile etiketleyin)' }}" class="nx-textarea" rows="2" style="flex: 1; margin: 0; font-size: 12px;"></textarea>
                            <button wire:click="addComment" class="nx-btn nx-btn-primary" style="align-self: flex-end; padding: 6px 14px; font-size: 12px;">Gönder</button>
                        </div>
                    </div>

                    {{-- Comments List --}}
                    @forelse($recordComments as $comment)
                        <div style="padding: 10px 12px; border-bottom: 1px solid var(--nx-border);" wire:key="comment-{{ $comment['id'] }}">
                            <div style="display: flex; gap: 8px; align-items: flex-start;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--nx-accent-glow); color: var(--nx-accent); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">{{ $comment['user_initial'] }}</div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">{{ $comment['user'] }}</span>
                                        <span style="font-size: 10px; color: var(--nx-text-tertiary);">{{ $comment['time'] }}</span>
                                    </div>
                                    <div style="font-size: 12px; color: var(--nx-text-secondary); margin-top: 4px; line-height: 1.5;">{{ $comment['content'] }}</div>

                                    @if($comment['attachment_url'])
                                        <div style="margin-top: 6px;">
                                            @if(str_contains($comment['attachment_mime'] ?? '', 'image'))
                                                <img src="{{ $comment['attachment_url'] }}" alt="" style="max-width: 200px; border-radius: 6px; border: 1px solid var(--nx-border);">
                                            @else
                                                <a href="{{ $comment['attachment_url'] }}" target="_blank" style="font-size: 11px; color: var(--nx-accent);">📎 {{ $comment['attachment_name'] }}</a>
                                            @endif
                                        </div>
                                    @endif

                                    <div style="display: flex; gap: 12px; margin-top: 6px;">
                                        <button wire:click="setReplyingTo({{ $comment['id'] }})" style="font-size: 10px; color: var(--nx-text-tertiary); border: none; background: none; cursor: pointer; padding: 0;">↩ Yanıtla</button>
                                        <button wire:click="deleteComment({{ $comment['id'] }})" wire:confirm="Bu yorumu silmek istediğinize emin misiniz?" style="font-size: 10px; color: var(--nx-danger); border: none; background: none; cursor: pointer; padding: 0;">Sil</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Replies --}}
                            @foreach($comment['replies'] as $reply)
                                <div style="margin-left: 36px; margin-top: 8px; padding: 8px 10px; background: var(--nx-bg-elevated); border-radius: var(--nx-radius-md);" wire:key="reply-{{ $reply['id'] }}">
                                    <div style="display: flex; gap: 6px; align-items: flex-start;">
                                        <div style="width: 22px; height: 22px; border-radius: 50%; background: var(--nx-bg-tertiary); color: var(--nx-text-secondary); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; flex-shrink: 0;">{{ $reply['user_initial'] }}</div>
                                        <div style="flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="font-size: 11px; font-weight: 600; color: var(--nx-text-primary);">{{ $reply['user'] }}</span>
                                                <span style="font-size: 9px; color: var(--nx-text-tertiary);">{{ $reply['time'] }}</span>
                                            </div>
                                            <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px; line-height: 1.4;">{{ $reply['content'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">Henüz yorum yok. İlk yorumu siz yazın!</div>
                    @endforelse
                </div>
                @endif

                {{-- TAB 7: Related Records (İlişkili Kayıtlar) --}}
                @if($editingId)
                <div style="display: {{ $activeTab === 'relations' ? 'block' : 'none' }};">
                    <div style="padding: 12px; background: rgba(16,185,129,0.02); border-bottom: 1px solid var(--nx-border); font-size: 11px; color: var(--nx-text-secondary);">
                        Company Brain: Bu kayda bağlı diğer tüm finansal ve operasyonel süreçler
                    </div>
                    @forelse($recordRelations as $rel)
                        <div style="padding: 12px; border-bottom: 1px solid var(--nx-border); display: flex; gap: 10px; align-items: start;" wire:key="rel-{{ $rel['id'] }}">
                            <div style="width: 24px; height: 24px; border-radius: 6px; background: var(--nx-bg-tertiary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--nx-text-secondary);">
                                <span class="material-symbols-outlined" style="font-size: 16px;">link</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12px; font-weight: 600; color: var(--nx-text-primary);">
                                    <span style="color: var(--nx-accent);">[{{ $rel['type'] }}]</span> {{ $rel['name'] }}
                                </div>
                                <div style="font-size: 11px; color: var(--nx-text-secondary); margin-top: 2px;">
                                    Bu süreç {{ $rel['relation'] }} olarak işaretlendi.
                                </div>
                                <div style="font-size: 10px; color: var(--nx-text-muted); margin-top: 4px;">
                                    Bağlantı tarihi: {{ $rel['time'] }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 24px; text-align: center; font-size: 11px; color: var(--nx-text-secondary);">
                            Bu kayıt için kurulmuş herhangi bir ilişki bağı bulunmuyor.
                        </div>
                    @endforelse
                </div>
                @endif

                {{-- TAB 8: AI Actions --}}
                @if($editingId)
                <div style="display: {{ $activeTab === 'ai-actions' ? 'block' : 'none' }}; padding: 16px;">
                    <div style="margin-bottom: 16px; border-bottom: 1px solid var(--nx-border); padding-bottom: 12px;">
                        <h4 style="font-size: 13px; font-weight: 600; color: var(--nx-text-primary); margin-bottom: 4px;">🤖 ADA Co-OS AI Actions</h4>
                        <p style="font-size: 11px; color: var(--nx-text-secondary);">
                            Kayıt bağlamı otomatik derlenerek yapay zekâ analizine gönderilir. Lütfen çalıştırmak istediğiniz eylemi seçin:
                        </p>
                    </div>

                    {{-- Dynamic Buttons by Resource type --}}
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px;">
                        @if($resource === 'clients')
                            <button type="button" wire:click="runAiAction('client_analyze')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Müşteriyi Analiz Et</button>
                            <button type="button" wire:click="runAiAction('proposal_improve')" class="nx-btn nx-btn-secondary" style="padding: 6px 12px; font-size: 11px;">Teklif Önerisi Hazırla</button>
                        @elseif($resource === 'proposals')
                            <button type="button" wire:click="runAiAction('proposal_improve')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Fiyat/KDV Uygunluğu Kontrolü</button>
                            <button type="button" wire:click="runAiAction('proposal_improve')" class="nx-btn nx-btn-secondary" style="padding: 6px 12px; font-size: 11px;">Teklif Metnini İyileştir</button>
                        @elseif($resource === 'contracts')
                            <button type="button" wire:click="runAiAction('contract_risk')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Sözleşme Risk Analizi</button>
                            <button type="button" wire:click="runAiAction('contract_risk')" class="nx-btn nx-btn-secondary" style="padding: 6px 12px; font-size: 11px;">Önemli Maddeleri Çıkar</button>
                        @elseif($resource === 'works')
                            <button type="button" wire:click="runAiAction('work_summary')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Süreç Özetini Çıkar</button>
                            <button type="button" wire:click="runAiAction('work_summary')" class="nx-btn nx-btn-secondary" style="padding: 6px 12px; font-size: 11px;">Sonraki Adımı Öner</button>
                        @elseif($resource === 'events')
                            <button type="button" wire:click="runAiAction('event_summary')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Toplantıyı Özetle</button>
                            <button type="button" wire:click="runAiAction('event_summary')" class="nx-btn nx-btn-secondary" style="padding: 6px 12px; font-size: 11px;">Görevleri Listele</button>
                        @elseif($resource === 'expenses')
                            <button type="button" wire:click="runAiAction('expense_analyze')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Gider Bütçe Risk Analizi</button>
                        @else
                            <button type="button" wire:click="runAiAction('general')" class="nx-btn nx-btn-primary" style="padding: 6px 12px; font-size: 11px;">Genel Yapay Zeka Analizi</button>
                        @endif
                    </div>

                    {{-- AI Loading Indicator --}}
                    <div wire:loading wire:target="runAiAction" style="padding: 24px 0; text-align: center; color: var(--nx-accent); font-size: 12px; font-weight: 600;">
                        <span class="material-symbols-outlined" style="vertical-align: middle; animation: spin 1s linear infinite; margin-right: 6px; font-size: 18px;">autorenew</span>
                        Yapay zekâ verileri analiz ediyor ve rapor hazırlıyor, lütfen bekleyin...
                    </div>

                    {{-- AI Result Box --}}
                    @if($aiResult)
                        <div style="background: var(--nx-bg-elevated); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); padding: 14px; margin-top: 12px; font-size: 12px; line-height: 1.6; color: var(--nx-text-primary); white-space: pre-wrap;">
                            {!! $aiResult !!}
                        </div>

                        {{-- AI Result Actions --}}
                        <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px;">
                            <button type="button" onclick="navigator.clipboard.writeText(`{{ addslashes($aiResult) }}`); alert('Panoya kopyalandı!');" class="nx-btn nx-btn-secondary" style="padding: 4px 10px; font-size: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 13px;">content_copy</span>
                                Kopyala
                            </button>
                            <button type="button" wire:click="saveAiResultAsNote" class="nx-btn nx-btn-secondary" style="padding: 4px 10px; font-size: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 13px;">edit_note</span>
                                Süreç Notu Yap
                            </button>
                            <button type="button" wire:click="publishAiResultToFeed" class="nx-btn nx-btn-secondary" style="padding: 4px 10px; font-size: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 13px;">bolt</span>
                                Akışta Paylaş
                            </button>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="nx-slide-over-footer">
                <button wire:click="closeEditor" class="nx-btn nx-btn-secondary">{{ $editingId ? 'Kapat' : 'İptal' }}</button>
                @if(!$editingId || $activeTab === 'info')
                    <button wire:click="saveEditor" class="nx-btn nx-btn-primary">
                        <span wire:loading.remove wire:target="saveEditor">Kaydet</span>
                        <span wire:loading wire:target="saveEditor">Kaydediliyor...</span>
                    </button>
                @endif
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
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--nx-text, #e2e8f0);">Silme Onayı</h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--nx-text-muted, #94a3b8);">Bu kaydı silmek istediğinize emin misiniz?</p>
                </div>
            </div>
            <p style="font-size: 12px; color: var(--nx-text-muted, #94a3b8); margin-bottom: 20px;">Bu işlem geri alınamaz.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button wire:click="cancelDelete" class="nx-btn nx-btn-secondary" style="font-size: 13px; padding: 8px 16px;">Vazgeç</button>
                <button wire:click="deleteRecord" class="nx-btn" style="font-size: 13px; padding: 8px 16px; background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <span wire:loading.remove wire:target="deleteRecord">Evet, Sil</span>
                    <span wire:loading wire:target="deleteRecord">Siliniyor...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
