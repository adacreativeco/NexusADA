<?php

namespace App\Livewire\Admin;

use App\Models\Campaign;
use App\Models\ContentItem;
use App\Models\Event;
use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{
    public int $currentYear;
    public int $currentMonth;
    public ?string $selectedDate = null;
    public array $selectedDateItems = [];

    // Quick Event Creation
    public bool $showCreateForm = false;
    public string $createType = 'event';
    public string $createTitle = '';
    public string $createDescription = '';
    public ?string $createDate = null;

    public function mount()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->selectedDate = null;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->selectedDate = null;
    }

    public function goToToday()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = null;
    }

    public function selectDate(string $date)
    {
        $this->selectedDate = $date;
        $this->selectedDateItems = $this->getItemsForDate($date);
    }

    public function closeDetail()
    {
        $this->selectedDate = null;
        $this->selectedDateItems = [];
    }

    /**
     * Open quick event creation form for a given date.
     */
    public function openCreate(?string $date = null): void
    {
        $this->createDate = $date ?? $this->selectedDate ?? now()->format('Y-m-d');
        $this->createType = 'event';
        $this->createTitle = '';
        $this->createDescription = '';
        $this->showCreateForm = true;
    }

    /**
     * Save quick event.
     */
    public function saveQuickEvent(): void
    {
        $this->validate([
            'createTitle' => 'required|string|min:2|max:255',
            'createType' => 'required|in:event,task',
            'createDate' => 'required|date',
        ]);

        if ($this->createType === 'event') {
            Event::create([
                'title' => $this->createTitle,
                'description' => $this->createDescription,
                'start_date' => $this->createDate,
                'end_date' => $this->createDate,
                'tenant_id' => auth()->user()->tenant_id ?? session('impersonating_tenant_id'),
            ]);
            $this->dispatch('notify', message: 'Etkinlik oluşturuldu.', type: 'success');
        } else {
            \App\Models\Task::create([
                'title' => $this->createTitle,
                'description' => $this->createDescription,
                'due_date' => $this->createDate,
                'status' => 'todo',
                'tenant_id' => auth()->user()->tenant_id ?? session('impersonating_tenant_id'),
                'created_by' => auth()->id(),
            ]);
            $this->dispatch('notify', message: 'Görev oluşturuldu.', type: 'success');
        }

        $this->showCreateForm = false;
        $this->createTitle = '';
        $this->createDescription = '';
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
    }

    /**
     * Update event date (for drag-drop support).
     */
    public function updateEventDate(string $type, int $id, string $newDate): void
    {
        match ($type) {
            'event' => Event::where('id', $id)->update(['start_date' => $newDate]),
            'task' => \App\Models\Task::where('id', $id)->update(['due_date' => $newDate]),
            'campaign' => Campaign::where('id', $id)->update(['start_date' => $newDate]),
            default => null,
        };

        $this->dispatch('notify', message: 'Tarih güncellendi.', type: 'success');
    }

    protected function getItemsForDate(string $date): array
    {
        $items = [];

        // Events
        $events = Event::whereDate('start_date', $date)->get();
        foreach ($events as $event) {
            $items[] = [
                'type' => 'event',
                'label' => 'Etkinlik',
                'title' => $event->title,
                'notes' => $event->description,
                'color' => '#3b82f6',
            ];
        }

        // Campaigns (active on this date)
        $campaigns = Campaign::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();
        foreach ($campaigns as $campaign) {
            $items[] = [
                'type' => 'campaign',
                'label' => 'Kampanya',
                'title' => $campaign->title,
                'notes' => $campaign->goal,
                'color' => '#10b981',
            ];
        }

        // Campaign start dates
        $campaignStarts = Campaign::whereDate('start_date', $date)->get();
        foreach ($campaignStarts as $cs) {
            if (!$campaigns->contains('id', $cs->id)) {
                $items[] = [
                    'type' => 'campaign',
                    'label' => 'Kampanya Başlangıç',
                    'title' => $cs->title,
                    'notes' => $cs->goal,
                    'color' => '#10b981',
                ];
            }
        }

        // Content items created on this date
        $contents = ContentItem::whereDate('created_at', $date)->get();
        foreach ($contents as $content) {
            $items[] = [
                'type' => 'content',
                'label' => 'İçerik',
                'title' => $content->title,
                'notes' => $content->type,
                'color' => '#f59e0b',
            ];
        }

        // Tasks due on this date
        $tasks = \App\Models\Task::whereDate('due_date', $date)->get();
        foreach ($tasks as $task) {
            $items[] = [
                'type' => 'task',
                'label' => 'Görev',
                'title' => $task->title,
                'notes' => 'Durum: ' . $task->status,
                'color' => '#ef4444',
            ];
        }

        // Social Posts scheduled for this date
        $posts = \App\Models\SocialPost::whereDate('scheduled_at', $date)->get();
        foreach ($posts as $post) {
            $items[] = [
                'type' => 'social_post',
                'label' => 'Sosyal Medya',
                'title' => strtoupper($post->platform) . ': ' . \Illuminate\Support\Str::limit($post->content, 40),
                'notes' => 'Durum: ' . $post->status,
                'color' => '#ec4899',
            ];
        }

        return $items;
    }

    protected function getCalendarData(): array
    {
        $start = now()->subMonths(6)->startOfMonth();
        $end = now()->addMonths(6)->endOfMonth();

        $eventsArray = [];

        $events = Event::whereBetween('start_date', [$start, $end])->get();
        foreach ($events as $event) {
            $eventsArray[] = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => Carbon::parse($event->start_date)->format('Y-m-d'),
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#3b82f6',
                'extendedProps' => ['type' => 'event']
            ];
        }

        $campaigns = Campaign::where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->get();
        foreach ($campaigns as $campaign) {
            $eventsArray[] = [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'start' => Carbon::parse($campaign->start_date)->format('Y-m-d'),
                'end' => Carbon::parse($campaign->end_date)->addDay()->format('Y-m-d'),
                'backgroundColor' => '#10b981',
                'borderColor' => '#10b981',
                'extendedProps' => ['type' => 'campaign']
            ];
        }

        $contents = ContentItem::whereBetween('created_at', [$start, $end])->get();
        foreach ($contents as $content) {
            $eventsArray[] = [
                'id' => $content->id,
                'title' => $content->title,
                'start' => $content->created_at->format('Y-m-d'),
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'extendedProps' => ['type' => 'content']
            ];
        }

        $tasks = \App\Models\Task::whereBetween('due_date', [$start, $end])->get();
        foreach ($tasks as $task) {
            $eventsArray[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date->format('Y-m-d'),
                'backgroundColor' => '#ef4444',
                'borderColor' => '#ef4444',
                'extendedProps' => ['type' => 'task']
            ];
        }

        $posts = \App\Models\SocialPost::whereBetween('scheduled_at', [$start, $end])->get();
        foreach ($posts as $post) {
            $eventsArray[] = [
                'id' => $post->id,
                'title' => strtoupper($post->platform),
                'start' => $post->scheduled_at->format('Y-m-d'),
                'backgroundColor' => '#ec4899',
                'borderColor' => '#ec4899',
                'extendedProps' => ['type' => 'social_post']
            ];
        }

        return $eventsArray;
    }

    public function render()
    {
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $monthName = $start->translatedFormat('F Y');
        $daysInMonth = $start->daysInMonth;
        $startDayOfWeek = ($start->dayOfWeek + 6) % 7; // Monday = 0
        $calendarData = $this->getCalendarData();
        $today = now()->format('Y-m-d');

        return view('livewire.admin.calendar', [
            'monthName' => $monthName,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'calendarData' => $calendarData,
            'today' => $today,
            'yearMonth' => $start->format('Y-m'),
        ])->layout('layouts.admin', [
            'title' => 'Takvim',
            'breadcrumb' => 'Takvim',
        ]);
    }
}
