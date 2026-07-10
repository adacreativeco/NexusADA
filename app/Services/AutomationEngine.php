<?php

namespace App\Services;

use App\Models\AutomationRule;
use App\Models\AutomationLog;
use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutomationEngine
{
    /**
     * Evaluate all active rules for a given model event.
     */
    public function evaluate(string $modelType, string $event, Model $record): void
    {
        $rules = AutomationRule::active()
            ->where('trigger_model', $modelType)
            ->where('trigger_event', $event)
            ->get();

        foreach ($rules as $rule) {
            try {
                if ($this->checkCondition($rule, $record)) {
                    $this->executeAction($rule, $record);

                    AutomationLog::create([
                        'rule_id' => $rule->id,
                        'trigger_model_type' => get_class($record),
                        'trigger_model_id' => $record->id,
                        'action_type' => $rule->action_type,
                        'action_result' => 'success',
                        'executed_at' => now(),
                    ]);

                    $rule->increment('execution_count');
                    $rule->update(['last_executed_at' => now()]);
                }
            } catch (\Exception $e) {
                AutomationLog::create([
                    'rule_id' => $rule->id,
                    'trigger_model_type' => get_class($record),
                    'trigger_model_id' => $record->id,
                    'action_type' => $rule->action_type,
                    'action_result' => 'failed',
                    'error_message' => $e->getMessage(),
                    'executed_at' => now(),
                ]);

                Log::error("Automation #{$rule->id} failed: " . $e->getMessage());
            }
        }
    }

    protected function checkCondition(AutomationRule $rule, Model $record): bool
    {
        if (!$rule->condition_field) {
            return true; // No condition = always match
        }

        $value = data_get($record, $rule->condition_field);
        $target = $rule->condition_value;

        return match ($rule->condition_operator) {
            'equals' => $value == $target,
            'not_equals' => $value != $target,
            'contains' => str_contains((string) $value, (string) $target),
            'greater_than' => $value > $target,
            'less_than' => $value < $target,
            default => true,
        };
    }

    protected function executeAction(AutomationRule $rule, Model $record): void
    {
        $config = $rule->action_config ?? [];

        match ($rule->action_type) {
            'notify' => $this->actionNotify($config, $record, $rule),
            'change_status' => $this->actionChangeStatus($config, $record),
            'send_email' => $this->actionSendEmail($config, $record, $rule),
            'assign_user' => $this->actionAssignUser($config, $record),
            'send_webhook' => $this->actionSendWebhook($config, $record, $rule),
            'ai_analyze' => $this->actionAiAnalyze($config, $record, $rule),
            'create_proposal' => $this->actionCreateProposal($config, $record, $rule),
            'create_task' => $this->actionCreateTask($config, $record, $rule),
            'schedule_reminder' => $this->actionScheduleReminder($config, $record, $rule),
            default => throw new \Exception("Unknown action type: {$rule->action_type}"),
        };
    }

    protected function actionNotify(array $config, Model $record, AutomationRule $rule): void
    {
        $userId = $config['user_id'] ?? null;
        $message = $config['message'] ?? "Otomasyon tetiklendi: {$rule->name}";

        if ($userId) {
            AppNotification::create([
                'user_id' => $userId,
                'title' => $rule->name,
                'body' => $message,
                'type' => 'automation',
            ]);
        }
    }

    protected function actionChangeStatus(array $config, Model $record): void
    {
        $status = $config['status'] ?? null;
        if ($status && $record->hasAttribute('status')) {
            $record->update(['status' => $status]);
        }
    }

    protected function actionSendEmail(array $config, Model $record, AutomationRule $rule): void
    {
        $to = $config['email'] ?? null;
        $subject = $config['subject'] ?? $rule->name;
        $body = $config['body'] ?? "Otomasyon tetiklendi.";

        if ($to) {
            Mail::raw($body, function ($msg) use ($to, $subject) {
                $msg->to($to)->subject($subject);
            });
        }
    }

    protected function actionAssignUser(array $config, Model $record): void
    {
        $userId = $config['user_id'] ?? null;
        if ($userId && $record->hasAttribute('assigned_to')) {
            $record->update(['assigned_to' => $userId]);
        }
    }

    protected function actionSendWebhook(array $config, Model $record, AutomationRule $rule): void
    {
        $url = $config['webhook_url'] ?? null;
        if (!$url) return;

        \Illuminate\Support\Facades\Http::timeout(10)->post($url, [
            'event' => $rule->trigger_event,
            'model' => $rule->trigger_model,
            'record_id' => $record->id,
            'rule_name' => $rule->name,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    protected function actionAiAnalyze(array $config, Model $record, AutomationRule $rule): void
    {
        $prompt = "Aşağıdaki kaydı analiz et:\n" . json_encode($record->toArray(), JSON_UNESCAPED_UNICODE);
        $analysis = AIService::ask($prompt, 'Sen bir otomasyon analizörüsün.', 'general', $record->tenant_id);

        if (method_exists($record, 'notes')) {
            $record->notes()->create([
                'content' => "🤖 YAPAY ZEKA OTOMATİK ANALİZİ:\n\n" . $analysis,
                'user_id' => $rule->created_by ?? auth()->id(),
            ]);
        }
    }

    protected function actionCreateProposal(array $config, Model $record, AutomationRule $rule): void
    {
        \App\Models\Proposal::create([
            'tenant_id' => $record->tenant_id,
            'client_id' => $record->client_id ?? $record->id,
            'work_id' => $record->work_id ?? ($record instanceof \App\Models\Work ? $record->id : null),
            'title' => "Otomatik Teklif — " . ($record->title ?? $record->name ?? 'Yeni Kayıt'),
            'content' => "Otomatik kural tetiklendi: " . $rule->name,
            'total_amount' => $config['amount'] ?? 5000,
            'status' => 'draft',
        ]);
    }

    protected function actionCreateTask(array $config, Model $record, AutomationRule $rule): void
    {
        \App\Models\Task::create([
            'tenant_id' => $record->tenant_id,
            'title' => $config['title'] ?? "Otomatik Görev — " . $rule->name,
            'description' => $config['description'] ?? "Otomasyon tetiklemesiyle oluşturulmuştur.",
            'status' => 'todo',
            'work_id' => $record->work_id ?? ($record instanceof \App\Models\Work ? $record->id : null),
            'due_date' => now()->addDays(5),
        ]);
    }

    protected function actionScheduleReminder(array $config, Model $record, AutomationRule $rule): void
    {
        AppNotification::create([
            'tenant_id' => $record->tenant_id,
            'user_id' => $config['user_id'] ?? auth()->id(),
            'title' => "Hatırlatıcı: " . $rule->name,
            'body' => $config['message'] ?? "Lütfen bu kaydı inceleyin.",
            'type' => 'automation',
        ]);
    }
}
