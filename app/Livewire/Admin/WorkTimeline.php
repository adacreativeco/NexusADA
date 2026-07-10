<?php

namespace App\Livewire\Admin;

use App\Models\Work;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\Note;
use App\Models\Comment;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;

class WorkTimeline extends Component
{
    use WithFileUploads;

    public $workId;
    public Work $work;

    // Quick Action Active Tabs
    public string $activeForm = 'note'; // note, comment, task, event, document

    // Form inputs
    public string $noteContent = '';
    
    public string $commentContent = '';
    
    public string $taskTitle = '';
    public string $taskPriority = 'medium';
    public string $taskDueDate = '';
    public string $taskAssignedTo = '';

    public string $eventTitle = '';
    public string $eventDate = '';
    public string $eventDescription = '';

    public $docFile = null;
    public string $docCategory = 'other';

    // Edit Slideover
    public bool $editorOpen = false;
    public array $editorData = [];

    public function mount($id)
    {
        $this->workId = $id;
        $this->work = Work::with(['client', 'project', 'assignee'])->findOrFail($id);
    }

    public function changeStatus(string $status)
    {
        $this->work->update([
            'status' => $status,
            'completed_at' => $status === 'completed' ? now() : null,
        ]);
        $this->work->refresh();
        $this->dispatch('notify', type: 'success', message: 'İş durumu güncellendi.');
    }

    public function changePriority(string $priority)
    {
        $this->work->update([
            'priority' => $priority,
        ]);
        $this->work->refresh();
        $this->dispatch('notify', type: 'success', message: 'İş önceliği güncellendi.');
    }

    public function addNote()
    {
        $this->validate(['noteContent' => 'required']);

        $this->work->notes()->create([
            'tenant_id' => $this->work->tenant_id,
            'user_id' => auth()->id(),
            'content' => $this->noteContent,
        ]);

        $this->noteContent = '';
        $this->dispatch('notify', type: 'success', message: 'Not başarıyla eklendi.');
    }

    public function addComment()
    {
        $this->validate(['commentContent' => 'required']);

        $this->work->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->commentContent,
        ]);

        $this->commentContent = '';
        $this->dispatch('notify', type: 'success', message: 'Yorum eklendi.');
    }

    public function addTask()
    {
        $this->validate(['taskTitle' => 'required']);

        Task::create([
            'tenant_id' => $this->work->tenant_id,
            'project_id' => $this->work->project_id,
            'work_id' => $this->work->id,
            'title' => $this->taskTitle,
            'priority' => $this->taskPriority,
            'due_date' => $this->taskDueDate ?: null,
            'assigned_to' => $this->taskAssignedTo ?: null,
            'status' => 'todo',
            'created_by' => auth()->id(),
        ]);

        $this->taskTitle = '';
        $this->taskPriority = 'medium';
        $this->taskDueDate = '';
        $this->taskAssignedTo = '';
        $this->dispatch('notify', type: 'success', message: 'Görev eklendi.');
    }

    public function addEvent()
    {
        $this->validate([
            'eventTitle' => 'required',
            'eventDate' => 'required',
        ]);

        Event::create([
            'tenant_id' => $this->work->tenant_id,
            'project_id' => $this->work->project_id,
            'work_id' => $this->work->id,
            'title' => $this->eventTitle,
            'description' => $this->eventDescription ?: null,
            'start_time' => $this->eventDate,
            'end_time' => now()->parse($this->eventDate)->addHour()->toDateTimeString(),
        ]);

        $this->eventTitle = '';
        $this->eventDate = '';
        $this->eventDescription = '';
        $this->dispatch('notify', type: 'success', message: 'Toplantı/Etkinlik eklendi.');
    }

    public function uploadDocument()
    {
        $this->validate([
            'docFile' => 'required|max:10240', // 10MB Limit
        ]);

        $path = $this->docFile->store('documents/' . $this->work->tenant_id, 'public');

        $this->work->documents()->create([
            'uploaded_by' => auth()->id(),
            'name' => $this->docFile->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $this->docFile->getMimeType(),
            'size' => $this->docFile->getSize(),
            'category' => $this->docCategory,
        ]);

        $this->docFile = null;
        $this->docCategory = 'other';
        $this->dispatch('notify', type: 'success', message: 'Dosya başarıyla yüklendi.');
    }

    public function openEditor()
    {
        $this->editorData = $this->work->toArray();
        $this->editorOpen = true;
    }

    public function closeEditor()
    {
        $this->editorOpen = false;
        $this->editorData = [];
    }

    public function saveWork()
    {
        $data = collect($this->editorData)->only([
            'title', 'client_id', 'project_id', 'description', 'status',
            'priority', 'value', 'currency', 'started_at', 'due_at', 'assigned_to'
        ])->toArray();

        foreach (['client_id', 'project_id', 'assigned_to', 'started_at', 'due_at'] as $field) {
            if (empty($data[$field])) $data[$field] = null;
        }

        $this->work->update($data);
        $this->work->refresh();
        $this->closeEditor();
        $this->dispatch('notify', type: 'success', message: 'İş bilgileri güncellendi.');
    }

    public function render()
    {
        // Gather all timeline elements
        $tasks = $this->work->tasks()->with('assignee')->get();
        $events = $this->work->events()->get();
        $notes = $this->work->notes()->with('user')->get();
        $documents = $this->work->documents()->with('uploader')->get();
        $comments = $this->work->comments()->with(['user', 'replies.user'])->topLevel()->get();
        $audits = $this->work->audits()->with('user')->get();
        $proposals = $this->work->proposals()->with('creator')->get();
        $contracts = $this->work->contracts()->with('creator')->get();
        $incomes = $this->work->incomes()->with('creator')->get();
        $collections = $this->work->collections()->get();

        // Merge all into a chronological timeline
        $timeline = collect()
            ->merge($tasks)
            ->merge($events)
            ->merge($notes)
            ->merge($documents)
            ->merge($comments)
            ->merge($audits)
            ->merge($proposals)
            ->merge($contracts)
            ->merge($incomes)
            ->merge($collections)
            ->sortByDesc(function ($item) {
                return $item->created_at ?? $item->updated_at;
            });

        return view('livewire.admin.work-timeline', [
            'timeline' => $timeline,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.admin', [
            'title' => $this->work->title . ' - Timeline',
            'breadcrumb' => $this->work->title,
        ]);
    }
}
