<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\AutomationEngine;
use App\Services\ActivityService;

class TaskObserver
{
    public function created(Task $task): void
    {
        ActivityService::logUser(
            __('Yeni Görev Eklendi'),
            __(':title isimli görev oluşturuldu.', ['title' => $task->title]),
            $task
        );

        app(AutomationEngine::class)->evaluate('Task', 'created', $task);

        // Notify if assigned to someone else
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $assignee = \App\Models\User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new \App\Notifications\TaskNotification($task, 'assigned', auth()->user()));
            }
        }
    }

    public function updated(Task $task): void
    {
        if ($task->isDirty('status')) {
            if ($task->status === 'done') {
                ActivityService::logUser(
                    __('Görev Tamamlandı'),
                    __(':title görevi başarıyla tamamlandı.', ['title' => $task->title]),
                    $task
                );

                \App\Services\WorkflowEngine::checkTaskCompletion($task);
            } else {
                ActivityService::logUser(
                    __('Görev Durumu Değişti'),
                    __(':title görevinin durumu :status yapıldı.', [
                        'title' => $task->title,
                        'status' => $task->status
                    ]),
                    $task
                );
            }

            app(AutomationEngine::class)->evaluate('Task', 'status_changed', $task);
        } else {
            ActivityService::logUser(
                __('Görev Güncellendi'),
                __(':title görevinin detayları güncellendi.', ['title' => $task->title]),
                $task
            );
        }

        app(AutomationEngine::class)->evaluate('Task', 'updated', $task);

        // Check if assigned_to changed
        if ($task->isDirty('assigned_to') && $task->assigned_to && $task->assigned_to !== auth()->id()) {
            $assignee = \App\Models\User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new \App\Notifications\TaskNotification($task, 'assigned', auth()->user()));
            }
        } 
        // Else check if other significant fields changed and it's assigned to someone else
        elseif ($task->isDirty(['title', 'description', 'status', 'priority', 'due_date']) 
                && $task->assigned_to && $task->assigned_to !== auth()->id()) {
            $assignee = \App\Models\User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new \App\Notifications\TaskNotification($task, 'updated', auth()->user()));
            }
        }
    }
}
