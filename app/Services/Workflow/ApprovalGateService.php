<?php

namespace App\Services\Workflow;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;

class ApprovalGateService
{
    public static function createGate(Model $record, string $title, string $role = 'manager', ?int $assignedUserId = null): Approval
    {
        return Approval::create([
            'tenant_id' => $record->tenant_id ?? 1,
            'approvable_type' => get_class($record),
            'approvable_id' => $record->id,
            'action' => 'submitted',
            'user_id' => $assignedUserId,
            'notes' => $title,
        ]);
    }

    public static function resolveGate(Approval $approval, bool $approved, ?string $notes = null, ?int $userId = null): bool
    {
        $approval->update([
            'action' => $approved ? 'approved' : 'rejected',
            'user_id' => $userId ?? auth()->id() ?? $approval->user_id,
            'notes' => $notes ?? $approval->notes,
        ]);

        return true;
    }
}
