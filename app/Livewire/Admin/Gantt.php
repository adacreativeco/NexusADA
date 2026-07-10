<?php

namespace App\Livewire\Admin;

use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class Gantt extends Component
{
    public ?int $projectId = null;
    public string $zoomLevel = 'week'; // day, week, month

    public function mount($project = null)
    {
        $this->projectId = $project;
    }

    public function changeZoom(string $level)
    {
        $this->zoomLevel = $level;
    }

    public function updateTaskDates(int $taskId, string $startDate, string $endDate)
    {
        $task = Task::findOrFail($taskId);
        $task->update([
            'start_date' => $startDate,
            'due_date' => $endDate,
        ]);
    }

    protected function getGanttData(): array
    {
        $query = Task::with(['project', 'assignee'])
            ->where(function ($q) {
                $q->whereNotNull('start_date')
                  ->orWhereNotNull('due_date');
            });

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        $tasks = $query->orderBy('start_date')->orderBy('due_date')->get();

        if ($tasks->isEmpty()) {
            return ['tasks' => [], 'days' => [], 'minDate' => now()->format('Y-m-d'), 'maxDate' => now()->addMonth()->format('Y-m-d')];
        }

        // Determine date range
        $minDate = $tasks->min(fn($t) => $t->start_date ?? $t->created_at->format('Y-m-d'));
        $maxDate = $tasks->max(fn($t) => $t->due_date ?? Carbon::parse($t->start_date ?? $t->created_at)->addDays(7)->format('Y-m-d'));

        $minDate = Carbon::parse($minDate)->startOfWeek(Carbon::MONDAY);
        $maxDate = Carbon::parse($maxDate)->endOfWeek(Carbon::SUNDAY)->addDays(7);

        $days = collect(CarbonPeriod::create($minDate, $maxDate))->map(fn($d) => $d->format('Y-m-d'))->toArray();

        $taskData = $tasks->map(function ($task) use ($minDate) {
            $start = Carbon::parse($task->start_date ?? $task->created_at->format('Y-m-d'));
            $end = Carbon::parse($task->due_date ?? $start->copy()->addDays(3));

            // Logic check: ensure start is before end
            if ($start->gt($end)) {
                $temp = $start;
                $start = $end;
                $end = $temp;
            }

            // If they are the same, make it at least 1 day
            if ($start->isSameDay($end)) {
                $end = $end->copy()->addDay();
            }

            return [
                'id' => $task->id,
                'title' => $task->title,
                'project' => $task->project ? ($task->project->title . ($task->project->client ? " ({$task->project->client->name})" : "")) : 'Genel',
                'assignee' => $task->assignee?->name ?? '-',
                'status' => $task->status,
                'priority' => $task->priority,
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'offset' => $minDate->diffInDays($start),
                'duration' => max(1, $start->diffInDays($end) + 1),
                'depends_on' => $task->depends_on,
            ];
        })->toArray();

        return [
            'tasks' => $taskData,
            'days' => $days,
            'minDate' => $minDate->format('Y-m-d'),
            'maxDate' => $maxDate->format('Y-m-d'),
        ];
    }

    public function render()
    {
        $projects = Project::orderBy('title')->get();

        return view('livewire.admin.gantt', [
            'ganttData' => $this->getGanttData(),
            'projects' => $projects,
        ])->layout('layouts.admin', [
            'title' => 'Gantt Şeması',
            'breadcrumb' => 'Gantt',
        ]);
    }
}
