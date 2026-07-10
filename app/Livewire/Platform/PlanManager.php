<?php

namespace App\Livewire\Platform;

use App\Livewire\Admin\NexusTable;

class PlanManager extends NexusTable
{
    public function mount(string $resource = 'plans')
    {
        parent::mount('plans');
    }

    protected function getConfigs(): array
    {
        return array_merge(parent::getConfigs(), [
            'plans' => \App\Admin\Resources\PlanConfig::class,
        ]);
    }

    public function render()
    {
        $config = $this->getConfig();
        $records = $this->buildQuery()->paginate($this->perPage);

        return view('livewire.admin.nexus-table', [
            'config' => $config,
            'records' => $records,
        ])->layout('layouts.platform', [
            'title' => 'Planlar',
            'breadcrumb' => 'Planlar',
        ]);
    }
}
