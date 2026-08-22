<?php

namespace App\Services\Workflow;

use App\Models\WorkWorkflow;
use App\Models\Task;
use App\Models\Proposal;
use App\Models\AppNotification;
use App\Services\AIService;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class WorkflowStepExecutor
{
    public static function execute(WorkWorkflow $workWorkflow, array $step, int $stepIndex): array
    {
        $action = $step['action'] ?? '';
        $work = $workWorkflow->work;
        $result = ['status' => 'success', 'action' => $action];

        // 1. Check condition if step is conditional
        if (!empty($step['condition'])) {
            $meetsCondition = ConditionEvaluator::evaluate($step['condition'], $work);
            if (!$meetsCondition) {
                // Skip or jump to alternate branch
                $result['skipped'] = true;
                return $result;
            }
        }

        switch ($action) {
            case 'create_task':
                $task = Task::create([
                    'tenant_id' => $workWorkflow->tenant_id,
                    'title' => $step['label'] ?? 'İş Akışı Görevi',
                    'description' => $step['description'] ?? ("Sorumlu Rol: " . strtoupper($step['role'] ?? 'Ekip')),
                    'status' => 'todo',
                    'work_id' => $workWorkflow->work_id,
                    'due_date' => now()->addDays($step['due_in_days'] ?? 3),
                ]);
                $result['task_id'] = $task->id;
                break;

            case 'ai_analyze':
                $prompt = "Aşağıdaki iş sürecini analiz et ve sonraki adımları çıkar:\n" . json_encode($work->toArray(), JSON_UNESCAPED_UNICODE);
                $analysis = AIService::ask($prompt, 'Sen iş akışı analizörüsün.', 'work_summary', $workWorkflow->tenant_id);

                if (method_exists($work, 'notes')) {
                    $work->notes()->create([
                        'content' => "🤖 YAPAY ZEKA SÜREÇ ANALİZİ (İş Akışı):\n\n" . $analysis,
                        'user_id' => $workWorkflow->workflow->created_by ?? 1,
                    ]);
                }
                $result['analysis'] = $analysis;
                break;

            case 'require_approval':
                $gate = ApprovalGateService::createGate($work, $step['label'] ?? 'Yönetici Onayı Gerekli', $step['role'] ?? 'manager');
                $result['approval_id'] = $gate->id;
                $result['waits_for_approval'] = true;
                break;

            case 'create_proposal':
                $proposal = Proposal::create([
                    'tenant_id' => $workWorkflow->tenant_id,
                    'client_id' => $work->client_id,
                    'work_id' => $workWorkflow->work_id,
                    'title' => "Otomatik Teklif — " . $work->title,
                    'content' => "İş akışı tarafından otomatik oluşturulmuştur.",
                    'total_amount' => $work->budget ?? ($step['amount'] ?? 10000),
                    'status' => 'draft',
                ]);
                $result['proposal_id'] = $proposal->id;
                break;

            case 'schedule_reminder':
                AppNotification::create([
                    'tenant_id' => $workWorkflow->tenant_id,
                    'user_id' => auth()->id() ?? 1,
                    'title' => 'İş Akışı Hatırlatıcısı',
                    'body' => "Hatırlatıcı: " . ($step['label'] ?? 'İnceleme zamanı geldi.'),
                    'type' => 'automation',
                ]);
                break;
        }

        return $result;
    }
}
