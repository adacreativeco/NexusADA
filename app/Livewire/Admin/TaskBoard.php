<?php

namespace App\Livewire\Admin;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;

class TaskBoard extends Component
{
    public string $filterProject = '';
    public string $filterAssignee = '';
    public string $filterPriority = '';

    // Edit slide-over
    public bool $editorOpen = false;
    public ?int $editingId = null;
    public array $editorData = [];

    public function moveTask(int $taskId, string $newStatus)
    {
        $task = Task::findOrFail($taskId);
        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'done' ? now() : null,
        ]);
    }

    public function openEditor(?int $id = null)
    {
        if ($id) {
            $task = Task::findOrFail($id);
            $this->editorData = $task->toArray();
            $this->editingId = $id;
        } else {
            $this->editorData = [
                'title' => '',
                'description' => '',
                'status' => 'todo',
                'priority' => 'medium',
                'assigned_to' => '',
                'project_id' => '',
                'campaign_id' => '',
                'due_date' => '',
            ];
            $this->editingId = null;
        }
        $this->editorOpen = true;
    }

    public function closeEditor()
    {
        $this->editorOpen = false;
        $this->editingId = null;
        $this->editorData = [];
    }

    public function saveTask()
    {
        $data = collect($this->editorData)->only([
            'title', 'description', 'status', 'priority',
            'assigned_to', 'project_id', 'campaign_id', 'due_date',
        ])->toArray();

        // Clean empty strings to null
        foreach (['assigned_to', 'project_id', 'campaign_id', 'due_date'] as $field) {
            if (empty($data[$field])) $data[$field] = null;
        }

        if ($this->editingId) {
            Task::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Görev güncellendi.');
        } else {
            $data['created_by'] = auth()->id();
            Task::create($data);
            $this->dispatch('notify', type: 'success', message: 'Yeni görev eklendi.');
        }

        $this->closeEditor();
    }

    public ?int $confirmingDeleteId = null;

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteTask(?int $id = null)
    {
        $deleteId = $id ?? $this->confirmingDeleteId;
        if (!$deleteId) return;

        Task::findOrFail($deleteId)->delete();
        if ($this->editingId === $deleteId) {
            $this->closeEditor();
        }
        $this->confirmingDeleteId = null;
        $this->dispatch('notify', type: 'success', message: 'Görev başarıyla silindi.');
    }

    // ── Time Tracking ──────────────────────────────────

    public function startTimer(int $taskId)
    {
        $task = Task::findOrFail($taskId);

        // Stop any existing running timer for this user
        \App\Models\TimeLog::running()
            ->where('user_id', auth()->id())
            ->each(fn($log) => $log->stop());

        \App\Models\TimeLog::create([
            'task_id' => $taskId,
            'project_id' => $task->project_id,
            'user_id' => auth()->id(),
            'started_at' => now(),
        ]);
    }

    public function stopTimer(int $taskId)
    {
        $timer = \App\Models\TimeLog::running()
            ->where('task_id', $taskId)
            ->where('user_id', auth()->id())
            ->first();

        if ($timer) {
            $timer->stop();
        }
    }

    public function addManualTime(int $taskId, int $minutes, string $description = '')
    {
        $task = Task::findOrFail($taskId);

        \App\Models\TimeLog::create([
            'task_id' => $taskId,
            'project_id' => $task->project_id,
            'user_id' => auth()->id(),
            'description' => $description ?: null,
            'started_at' => now()->subMinutes($minutes),
            'stopped_at' => now(),
            'duration_minutes' => $minutes,
            'is_manual' => true,
        ]);
    }

    protected function getFilteredQuery()
    {
        $query = Task::with(['assignee', 'project']);

        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }
        if ($this->filterAssignee) {
            $query->where('assigned_to', $this->filterAssignee);
        }
        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        return $query->orderBy('position')->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $tasks = $this->getFilteredQuery()->get()->groupBy('status');

        $columns = [
            'todo' => ['label' => 'Yapılacak', 'color' => '#6b7280', 'items' => $tasks->get('todo', collect())],
            'in_progress' => ['label' => 'Devam Eden', 'color' => '#3b82f6', 'items' => $tasks->get('in_progress', collect())],
            'review' => ['label' => 'İnceleme', 'color' => '#f59e0b', 'items' => $tasks->get('review', collect())],
            'done' => ['label' => 'Tamamlandı', 'color' => '#10b981', 'items' => $tasks->get('done', collect())],
        ];

        return view('livewire.admin.task-board', [
            'columns' => $columns,
            'users' => User::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ])->layout('layouts.admin', [
            'title' => 'Kanban Board',
            'breadcrumb' => 'Kanban Board',
        ]);
    }
}
