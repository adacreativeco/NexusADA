<?php

namespace App\Services\Email;

use App\Models\IncomingEmail;
use App\Models\Task;
use App\Services\AIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailIntelligenceService
{
    public static function processInboundEmail(IncomingEmail $email): array
    {
        $from = $email->from_address ?? $email->from ?? 'client@example.com';
        $body = strip_tags($email->body ?? '');
        $prompt = "Aşağıdaki gelen müşteri e-postasını analiz et.\nKimden: {$from}\nKonu: {$email->subject}\nİçerik:\n{$body}\n\nJSON formatında çıkar: sentiment (positive/neutral/negative/urgent), urgency (low/medium/high/critical), summary (kısa özet), should_create_task (true/false), suggested_task_title (görev başlığı).";

        $analysisJson = AIService::ask($prompt, 'Sen gelen e-posta analiz uzmanısın. Yalnızca geçerli JSON döndür.', 'email_analysis', $email->tenant_id);

        $data = json_decode($analysisJson, true);
        if (!$data || !isset($data['sentiment'])) {
            $isUrgent = (bool) preg_match('/(acil|önemli|kritik|derhal|hata|problem|urgent|critical|asap)/i', $email->subject . ' ' . $body);
            $data = [
                'sentiment' => $isUrgent ? 'urgent' : 'neutral',
                'urgency' => $isUrgent ? 'high' : 'medium',
                'summary' => Str::limit($body, 120),
                'should_create_task' => $isUrgent,
                'suggested_task_title' => "Müşteri Talebi: " . $email->subject,
            ];
        }

        $taskId = null;
        if (!empty($data['should_create_task'])) {
            $task = Task::create([
                'tenant_id' => $email->tenant_id ?? 1,
                'title' => $data['suggested_task_title'] ?? ("E-Posta Talebi: " . $email->subject),
                'description' => "Kimden: {$from}\nÖzet: {$data['summary']}\n\nE-posta ID: #{$email->id}",
                'status' => 'todo',
                'due_date' => $data['urgency'] === 'high' ? now()->addDay() : now()->addDays(3),
            ]);
            $taskId = $task->id;
        }

        return [
            'analysis' => $data,
            'task_id' => $taskId,
        ];
    }
}
