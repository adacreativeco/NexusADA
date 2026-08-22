<?php

namespace App\Services;

use App\Models\WorkWorkflow;
use App\Models\Workflow;
use App\Models\Work;
use App\Models\Task;
use App\Services\Workflow\WorkflowStepExecutor;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    /**
     * Start a workflow template on a Work process
     */
    public static function start(Work $work, Workflow $workflow): WorkWorkflow
    {
        $tenantId = $work->tenant_id;

        $workWorkflow = WorkWorkflow::create([
            'tenant_id' => $tenantId,
            'work_id' => $work->id,
            'workflow_id' => $workflow->id,
            'current_step_index' => 0,
            'status' => 'running',
        ]);

        self::executeStep($workWorkflow, 0);

        return $workWorkflow;
    }

    /**
     * Execute a specific step index in the active workflow
     */
    public static function executeStep(WorkWorkflow $workWorkflow, int $stepIndex): void
    {
        $steps = $workWorkflow->workflow->steps ?? [];
        if (!isset($steps[$stepIndex])) {
            // Workflow complete!
            $workWorkflow->update(['status' => 'completed']);
            ActivityService::logSystem(
                'İş Akışı Tamamlandı',
                "{$workWorkflow->workflow->name} iş akışının tüm adımları başarıyla tamamlandı.",
                $workWorkflow->work
            );
            return;
        }

        $step = $steps[$stepIndex];
        $workWorkflow->update(['current_step_index' => $stepIndex]);

        ActivityService::logSystem(
            'İş Akışı Adımı Başlatıldı',
            "Akış adımı: {$step['label']} ({$step['action']})",
            $workWorkflow->work
        );

        try {
            $execution = WorkflowStepExecutor::execute($workWorkflow, $step, $stepIndex);

            // If step paused waiting for approval gate or manual task completion, don't advance automatically
            if (!empty($execution['waits_for_approval']) || ($step['action'] === 'create_task')) {
                return;
            }

            // Otherwise, advance immediately to next step
            self::executeStep($workWorkflow, $stepIndex + 1);

        } catch (\Exception $e) {
            Log::error("Workflow Step execution failed: " . $e->getMessage());
        }
    }

    /**
     * Move to next step if a task belonging to a workflow is completed
     */
    public static function checkTaskCompletion(Task $task): void
    {
        if (!$task->work_id || $task->status !== 'done') {
            return;
        }

        $activeWorkflow = WorkWorkflow::where('work_id', $task->work_id)
            ->where('status', 'running')
            ->first();

        if ($activeWorkflow) {
            $steps = $activeWorkflow->workflow->steps ?? [];
            $currentIndex = $activeWorkflow->current_step_index;

            if (isset($steps[$currentIndex]) && $steps[$currentIndex]['action'] === 'create_task') {
                if (trim($steps[$currentIndex]['label']) === trim($task->title)) {
                    self::executeStep($activeWorkflow, $currentIndex + 1);
                }
            }
        }
    }
}
