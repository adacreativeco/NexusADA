<?php

namespace App\Livewire\Platform;

use App\Livewire\Admin\NexusTable;

class InvoiceManager extends NexusTable
{
    public function mount(string $resource = 'invoices')
    {
        parent::mount('invoices');
    }

    protected function getConfigs(): array
    {
        return array_merge(parent::getConfigs(), [
            'invoices' => \App\Admin\Resources\InvoiceConfig::class,
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
            'title' => 'Faturalar',
            'breadcrumb' => 'Faturalar',
        ]);
    }
}
