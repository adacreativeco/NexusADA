<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    public function dispatch(string $event, Model $model): void
    {
        $integrations = IntegrationSetting::active()->forEvent($event)->get();

        foreach ($integrations as $integration) {
            try {
                $payload = $this->formatPayload($integration->provider, $event, $model);
                Http::timeout(10)->post($integration->webhook_url, $payload);
            } catch (\Exception $e) {
                Log::error("Webhook dispatch failed [{$integration->provider}]: " . $e->getMessage());
            }
        }
    }

    protected function formatPayload(string $provider, string $event, Model $model): array
    {
        $baseData = [
            'event' => $event,
            'model' => class_basename($model),
            'id' => $model->id,
            'title' => $model->title ?? $model->name ?? "#{$model->id}",
            'timestamp' => now()->toIso8601String(),
        ];

        return match ($provider) {
            'slack' => [
                'text' => "🔔 *{$event}*: {$baseData['title']}",
                'blocks' => [
                    ['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => "🔔 *{$event}*\n{$baseData['model']}: {$baseData['title']}"]],
                ],
            ],
            'discord' => [
                'content' => "🔔 **{$event}**: {$baseData['title']}",
                'embeds' => [
                    ['title' => $event, 'description' => "{$baseData['model']}: {$baseData['title']}", 'color' => 5814783],
                ],
            ],
            default => $baseData,
        };
    }
}
