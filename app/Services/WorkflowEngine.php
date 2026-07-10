<?php

namespace App\Services;

use App\Models\WorkWorkflow;
use App\Models\Workflow;
use App\Models\Work;
use App\Models\Task;
use App\Models\Proposal;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    /**
     * Start a workflow template on a Work process
     */
    public static function start(Work $work, Workflow $workflow): WorkWorkflow
    {
        $tenantId = $work->tenant_id;

        // Create the active workflow tracker
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
            // No more steps -> completed!
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

        // Log starting of step
        ActivityService::logSystem(
            'İş Akışı Adımı Başlatıldı',
            "Akış adımı: {$step['label']} ({$step['action']}) sorumlusu rol: {$step['role']}",
            $workWorkflow->work
        );

        try {
            switch ($step['action']) {
                case 'create_task':
                    Task::create([
                        'tenant_id' => $workWorkflow->tenant_id,
                        'title' => $step['label'],
                        'description' => "İş Akışı adımı görevi. Sorumlu Rol: " . strtoupper($step['role']),
                        'status' => 'todo',
                        'work_id' => $workWorkflow->work_id,
                        'due_date' => now()->addDays(3),
                    ]);
                    break;

                case 'ai_analyze':
                    $work = $workWorkflow->work;
                    $prompt = "Aşağıdaki iş sürecini analiz et ve sonraki adımları çıkar:\n" . json_encode($work->toArray(), JSON_UNESCAPED_UNICODE);
                    $analysis = AIService::ask($prompt, 'Sen iş akışı analizörüsün.', 'work_summary', $workWorkflow->tenant_id);

                    if (method_exists($work, 'notes')) {
                        $work->notes()->create([
                            'content' => "🤖 YAPAY ZEKA SÜREÇ ANALİZİ (İş Akışı):\n\n" . $analysis,
                            'user_id' => $workWorkflow->workflow->created_by ?? auth()->id(),
                        ]);
                    }

                    // AI runs synchronously, trigger next step immediately!
                    self::executeStep($workWorkflow, $stepIndex + 1);
                    break;

                case 'create_proposal':
                    Proposal::create([
                        'tenant_id' => $workWorkflow->tenant_id,
                        'client_id' => $workWorkflow->work->client_id,
                        'work_id' => $workWorkflow->work_id,
                        'title' => "Otomatik Teklif - " . $workWorkflow->work->title,
                        'content' => "İş akışı tarafından otomatik oluşturulmuştur.",
                        'total_amount' => $workWorkflow->work->budget ?? 0,
                        'status' => 'draft',
                    ]);

                    // Trigger next step immediately
                    self::executeStep($workWorkflow, $stepIndex + 1);
                    break;

                case 'schedule_reminder':
                    AppNotification::create([
                        'tenant_id' => $workWorkflow->tenant_id,
                        'user_id' => auth()->id() ?? User::where('tenant_id', $workWorkflow->tenant_id)->first()?->id,
                        'title' => 'İş Akışı Hatırlatıcısı',
                        'body' => "Hatırlatıcı: {$step['label']} adımı için inceleme zamanı geldi.",
                        'type' => 'automation',
                    ]);

                    // Trigger next step immediately
                    self::executeStep($workWorkflow, $stepIndex + 1);
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Workflow Step execution failed: " . $e->getMessage());
        }
    }

    /**
     * Move to next step if a task belonging to a workflow is completed
     */
    public static function checkTaskCompletion(Task $task): void
    {
        if (!$task->work_id || $task->status !== 'done') return;

        // Check if there is a running workflow on this work
        $activeWorkflow = WorkWorkflow::where('work_id', $task->work_id)
            ->where('status', 'running')
            ->first();

        if ($activeWorkflow) {
            $steps = $activeWorkflow->workflow->steps ?? [];
            $currentIndex = $activeWorkflow->current_step_index;

            // Check if the current step generated this task
            if (isset($steps[$currentIndex]) && $steps[$currentIndex]['action'] === 'create_task') {
                if (trim($steps[$currentIndex]['label']) === trim($task->title)) {
                    // Proceed to next step!
                    self::executeStep($activeWorkflow, $currentIndex + 1);
                }
            }
        }
    }
}
