<?php

namespace App\Models\Traits;

use App\Models\Approval;
use App\Models\User;

trait Approvable
{
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getStatusColumn(): string
    {
        return 'status';
    }

    public function getPendingStatus(): string
    {
        return 'pending_approval';
    }

    public function getApprovedStatus(): string
    {
        return 'approved_internal';
    }

    public function getRejectedStatus(): string
    {
        return 'rejected_internal';
    }

    public function submitForApproval(?User $by = null, ?string $notes = null): void
    {
        $statusCol = $this->getStatusColumn();
        $this->update([
            $statusCol => $this->getPendingStatus()
        ]);

        $this->approvals()->create([
            'tenant_id' => $this->tenant_id,
            'action' => 'submitted',
            'user_id' => $by ? $by->id : auth()->id(),
            'notes' => $notes,
        ]);

        // Notify managers/PMs in the same tenant
        try {
            $managers = User::role(['webmaster', 'pm'])
                ->where('tenant_id', $this->tenant_id)
                ->get();

            foreach ($managers as $manager) {
                \App\Services\NotificationService::approvalRequired($this, $manager->id);
            }
        } catch (\Exception $e) {
            \Log::warning("Notification failed during submitForApproval: " . $e->getMessage());
        }
    }

    public function approve(User $by, ?string $notes = null): void
    {
        $statusCol = $this->getStatusColumn();
        
        $updateData = [
            $statusCol => $this->getApprovedStatus(),
            'approved_by' => $by->id,
            'approved_at' => now(),
        ];

        $this->update($updateData);

        $this->approvals()->create([
            'tenant_id' => $this->tenant_id,
            'action' => 'approved',
            'user_id' => $by->id,
            'notes' => $notes,
        ]);
    }

    public function reject(User $by, string $notes): void
    {
        $statusCol = $this->getStatusColumn();
        
        $this->update([
            $statusCol => $this->getRejectedStatus(),
        ]);

        $this->approvals()->create([
            'tenant_id' => $this->tenant_id,
            'action' => 'rejected',
            'user_id' => $by->id,
            'notes' => $notes,
        ]);
    }

    public function requestRevision(User $by, string $notes): void
    {
        $statusCol = $this->getStatusColumn();
        
        $this->update([
            $statusCol => 'draft',
        ]);

        $this->approvals()->create([
            'tenant_id' => $this->tenant_id,
            'action' => 'revision_requested',
            'user_id' => $by->id,
            'notes' => $notes,
        ]);
    }

    public function isPendingApproval(): bool
    {
        $statusCol = $this->getStatusColumn();
        return $this->{$statusCol} === $this->getPendingStatus();
    }
}
