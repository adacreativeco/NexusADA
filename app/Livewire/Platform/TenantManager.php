<?php

namespace App\Livewire\Platform;

use App\Livewire\Admin\NexusTable;
use App\Models\Tenant;

class TenantManager extends NexusTable
{
    public function mount(string $resource = 'tenants')
    {
        parent::mount('tenants');
    }

    protected function getConfigs(): array
    {
        return array_merge(parent::getConfigs(), [
            'tenants' => \App\Admin\Resources\TenantConfig::class,
        ]);
    }

    public function executeAction(string $actionKey, int $recordId)
    {
        if ($actionKey === 'impersonate') {
            $tenant = Tenant::findOrFail($recordId);

            session([
                'impersonating_tenant_id' => $tenant->id,
                'impersonating_tenant_name' => $tenant->name,
            ]);

            logger()->info('🔍 Impersonate started', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'ip' => request()->ip(),
            ]);

            return redirect('/admin');
        }

        return parent::executeAction($actionKey, $recordId);
    }

    public function render()
    {
        $config = $this->getConfig();
        $records = $this->buildQuery()->paginate($this->perPage);

        return view('livewire.admin.nexus-table', [
            'config' => $config,
            'records' => $records,
        ])->layout('layouts.platform', [
            'title' => 'Kiracılar',
            'breadcrumb' => 'Kiracılar',
        ]);
    }
}
