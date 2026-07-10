<?php

namespace App\Livewire\Platform;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

class SystemLog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $eventFilter = '';
    public int $perPage = 25;

    protected $queryString = ['search', 'eventFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Audit::with('user')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('auditable_type', 'like', '%' . $this->search . '%')
                  ->orWhere('old_values', 'like', '%' . $this->search . '%')
                  ->orWhere('new_values', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->eventFilter) {
            $query->where('event', $this->eventFilter);
        }

        return view('livewire.platform.system-log', [
            'audits' => $query->paginate($this->perPage),
        ])->layout('layouts.platform', [
            'title' => 'Sistem Logları',
            'breadcrumb' => 'Sistem Logları',
        ]);
    }
}
