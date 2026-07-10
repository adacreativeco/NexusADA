<?php

namespace App\Services;

use App\Models\AppNotification;

class NotificationService
{
    public static function send(int $userId, string $type, string $title, ?string $body = null, array $data = []): AppNotification
    {
        return AppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => !empty($data) ? $data : null,
        ]);
    }

    public static function taskAssigned(\App\Models\Task $task): void
    {
        if (!$task->assigned_to) return;
        if ($task->assigned_to === auth()->id()) return;

        self::send(
            $task->assigned_to,
            'task_assigned',
            'Yeni görev atandı',
            "\"{$task->title}\" görevi size atandı.",
            ['url' => '/admin/tasks/board', 'model_type' => 'Task', 'model_id' => $task->id]
        );
    }

    public static function taskCompleted(\App\Models\Task $task): void
    {
        if (!$task->created_by) return;
        if ($task->created_by === auth()->id()) return;

        self::send(
            $task->created_by,
            'task_completed',
            'Görev tamamlandı',
            "\"{$task->title}\" görevi tamamlandı.",
            ['url' => '/admin/tasks/board', 'model_type' => 'Task', 'model_id' => $task->id]
        );
    }

    public static function documentUploaded(string $modelType, int $modelId, string $docName, ?int $notifyUserId = null): void
    {
        if (!$notifyUserId || $notifyUserId === auth()->id()) return;

        self::send(
            $notifyUserId,
            'document_uploaded',
            'Yeni dosya yüklendi',
            "\"{$docName}\" dosyası yüklendi.",
            ['url' => '/admin/' . strtolower($modelType) . 's', 'model_type' => $modelType, 'model_id' => $modelId]
        );
    }

    public static function approvalRequired($model, int $managerUserId): void
    {
        $num = $model->proposal_number ?? $model->contract_number ?? $model->expense_number ?? $model->title ?? '';
        $typeLabel = ($model instanceof \App\Models\Proposal) ? 'Teklif' : (($model instanceof \App\Models\Expense) ? 'Gider' : 'Sözleşme');

        self::send(
            $managerUserId,
            'approval_required',
            'Onay Gerekiyor',
            "\"{$num}\" numaralı {$typeLabel} onayınızı bekliyor.",
            [
                'url' => '/admin',
                'model_type' => get_class($model),
                'model_id' => $model->id
            ]
        );
    }

    public static function contractExpiring(\App\Models\Contract $contract, int $notifyUserId, int $days): void
    {
        self::send(
            $notifyUserId,
            'contract_expiring',
            'Sözleşme Süresi Doluyor',
            "\"{$contract->contract_number}\" numaralı sözleşme {$days} gün içinde sona eriyor.",
            [
                'url' => '/admin/contracts',
                'model_type' => 'Contract',
                'model_id' => $contract->id
            ]
        );
    }
}
