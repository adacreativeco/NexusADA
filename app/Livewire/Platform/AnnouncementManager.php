<?php

namespace App\Livewire\Platform;

use App\Livewire\Admin\NexusTable;

class AnnouncementManager extends NexusTable
{
    public function mount(string $resource = 'announcements')
    {
        parent::mount('announcements');
    }

    protected function getConfigs(): array
    {
        return array_merge(parent::getConfigs(), [
            'announcements' => \App\Admin\Resources\AnnouncementConfig::class,
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
            'title' => 'Duyurular',
            'breadcrumb' => 'Duyurular',
        ]);
    }
}
