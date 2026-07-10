<?php

namespace App\Livewire\Admin;

use App\Models\TimeLog;
use App\Models\User;
use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class Timesheet extends Component
{
    public string $view = 'week'; // week or month
    public string $filterUser = '';
    public string $filterProject = '';
    public string $currentDate; // anchor date for navigation

    public function mount()
    {
        $this->currentDate = now()->format('Y-m-d');
    }

    public function previousPeriod()
    {
        $date = Carbon::parse($this->currentDate);
        $this->currentDate = $this->view === 'week'
            ? $date->subWeek()->format('Y-m-d')
            : $date->subMonth()->format('Y-m-d');
    }

    public function nextPeriod()
    {
        $date = Carbon::parse($this->currentDate);
        $this->currentDate = $this->view === 'week'
            ? $date->addWeek()->format('Y-m-d')
            : $date->addMonth()->format('Y-m-d');
    }

    public function goToToday()
    {
        $this->currentDate = now()->format('Y-m-d');
    }

    public function setView(string $view)
    {
        $this->view = $view;
    }

    protected function getDateRange(): array
    {
        $anchor = Carbon::parse($this->currentDate);

        if ($this->view === 'week') {
            $start = $anchor->copy()->startOfWeek(Carbon::MONDAY);
            $end = $anchor->copy()->endOfWeek(Carbon::SUNDAY);
        } else {
            $start = $anchor->copy()->startOfMonth();
            $end = $anchor->copy()->endOfMonth();
        }

        return [$start, $end];
    }

    protected function buildTimesheetData(): array
    {
        [$start, $end] = $this->getDateRange();

        $query = TimeLog::with(['user', 'task', 'project'])
            ->whereNotNull('stopped_at')
            ->whereBetween('started_at', [$start, $end]);

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }
        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }

        $logs = $query->get();

        // Group by user → date → sum minutes
        $data = [];
        $days = collect(CarbonPeriod::create($start, $end))->map(fn($d) => $d->format('Y-m-d'))->toArray();

        foreach ($logs as $log) {
            $userId = $log->user_id;
            $userName = $log->user?->name ?? 'Bilinmeyen';
            $day = $log->started_at->format('Y-m-d');

            if (!isset($data[$userId])) {
                $data[$userId] = [
                    'name' => $userName,
                    'days' => array_fill_keys($days, 0),
                    'total' => 0,
                ];
            }

            $minutes = $log->duration_minutes ?? 0;
            if (isset($data[$userId]['days'][$day])) {
                $data[$userId]['days'][$day] += $minutes;
            }
            $data[$userId]['total'] += $minutes;
        }

        // Calculate daily totals
        $dailyTotals = array_fill_keys($days, 0);
        foreach ($data as $user) {
            foreach ($user['days'] as $day => $minutes) {
                $dailyTotals[$day] += $minutes;
            }
        }

        return [
            'users' => $data,
            'days' => $days,
            'dailyTotals' => $dailyTotals,
            'grandTotal' => array_sum($dailyTotals),
        ];
    }

    public function render()
    {
        [$start, $end] = $this->getDateRange();

        return view('livewire.admin.timesheet', [
            'timesheetData' => $this->buildTimesheetData(),
            'periodLabel' => $this->view === 'week'
                ? $start->translatedFormat('d M') . ' – ' . $end->translatedFormat('d M Y')
                : $start->translatedFormat('F Y'),
            'users' => User::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ])->layout('layouts.admin', [
            'title' => 'Timesheet',
            'breadcrumb' => 'Timesheet',
        ]);
    }
}
