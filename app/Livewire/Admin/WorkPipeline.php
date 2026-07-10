<?php

namespace App\Livewire\Admin;

use App\Models\Work;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Livewire\Component;

class WorkPipeline extends Component
{
    public string $filterClient = '';
    public string $filterPriority = '';

    // Editor state
    public bool $editorOpen = false;
    public ?int $editingId = null;
    public array $editorData = [];

    public function moveWork(int $workId, string $newStatus)
    {
        $work = Work::findOrFail($workId);
        $work->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Süreç durumu güncellendi.');
    }

    public function openEditor(?int $id = null)
    {
        if ($id) {
            $work = Work::findOrFail($id);
            $this->editorData = $work->toArray();
            $this->editingId = $id;
        } else {
            $this->editorData = [
                'title' => '',
                'client_id' => '',
                'project_id' => '',
                'description' => '',
                'status' => 'lead',
                'priority' => 'medium',
                'value' => 0,
                'currency' => 'TRY',
                'started_at' => '',
                'due_at' => '',
                'assigned_to' => '',
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

    public function saveWork()
    {
        $data = collect($this->editorData)->only([
            'title', 'client_id', 'project_id', 'description', 'status',
            'priority', 'value', 'currency', 'started_at', 'due_at', 'assigned_to'
        ])->toArray();

        foreach (['client_id', 'project_id', 'assigned_to', 'started_at', 'due_at'] as $field) {
            if (empty($data[$field])) $data[$field] = null;
        }

        if ($this->editingId) {
            Work::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'İş süreci güncellendi.');
        } else {
            $data['created_by'] = auth()->id();
            Work::create($data);
            $this->dispatch('notify', type: 'success', message: 'Yeni iş süreci eklendi.');
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

    public function deleteWork(?int $id = null)
    {
        $deleteId = $id ?? $this->confirmingDeleteId;
        if (!$deleteId) return;

        Work::findOrFail($deleteId)->delete();
        if ($this->editingId === $deleteId) {
            $this->closeEditor();
        }
        $this->confirmingDeleteId = null;
        $this->dispatch('notify', type: 'success', message: 'İş süreci başarıyla silindi.');
    }

    protected function getFilteredQuery()
    {
        $query = Work::with(['client', 'project', 'assignee']);

        if ($this->filterClient) {
            $query->where('client_id', $this->filterClient);
        }
        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $works = $this->getFilteredQuery()->get()->groupBy('status');

        $columns = [
            'lead' => ['label' => 'Talep/Aday', 'color' => '#6b7280', 'items' => $works->get('lead', collect())],
            'proposal' => ['label' => 'Teklif', 'color' => '#3b82f6', 'items' => $works->get('proposal', collect())],
            'contract' => ['label' => 'Sözleşme', 'color' => '#8b5cf6', 'items' => $works->get('contract', collect())],
            'in_progress' => ['label' => 'Devam Eden', 'color' => '#f59e0b', 'items' => $works->get('in_progress', collect())],
            'completed' => ['label' => 'Tamamlandı', 'color' => '#10b981', 'items' => $works->get('completed', collect())],
        ];

        return view('livewire.admin.work-pipeline', [
            'columns' => $columns,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.admin', [
            'title' => 'İş Pipeline',
            'breadcrumb' => 'İş Pipeline',
        ]);
    }
}
