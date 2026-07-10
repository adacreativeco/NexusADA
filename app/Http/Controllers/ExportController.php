<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Map of resource slugs to config classes.
     * Must mirror NexusTable::getConfigs()
     */
    private function getConfigs(): array
    {
        return [
            'clients' => \App\Admin\Resources\ClientConfig::class,
            'projects' => \App\Admin\Resources\ProjectConfig::class,
            'campaigns' => \App\Admin\Resources\CampaignConfig::class,
            'invoices' => \App\Admin\Resources\InvoiceConfig::class,
            'content-items' => \App\Admin\Resources\ContentItemConfig::class,
            'events' => \App\Admin\Resources\EventConfig::class,
            'press-contacts' => \App\Admin\Resources\PressContactConfig::class,
            'media-insights' => \App\Admin\Resources\MediaInsightConfig::class,
            'emails' => \App\Admin\Resources\EmailConfig::class,
            'brand-assets' => \App\Admin\Resources\BrandAssetConfig::class,
            'departments' => \App\Admin\Resources\DepartmentConfig::class,
            'documents' => \App\Admin\Resources\DocumentConfig::class,
            'tools' => \App\Admin\Resources\ToolConfig::class,
            'tasks' => \App\Admin\Resources\TaskConfig::class,
            'social-posts' => \App\Admin\Resources\SocialPostConfig::class,
            'email-templates' => \App\Admin\Resources\EmailTemplateConfig::class,
            'automations' => \App\Admin\Resources\AutomationRuleConfig::class,
            'integrations' => \App\Admin\Resources\IntegrationSettingConfig::class,
        ];
    }

    /**
     * Export resource as CSV — direct HTTP response (no Livewire)
     */
    public function csv(string $resource): StreamedResponse
    {
        $configs = $this->getConfigs();
        $configClass = $configs[$resource] ?? null;

        if (!$configClass || !class_exists($configClass)) {
            abort(404, "Resource '{$resource}' bulunamadı.");
        }

        $config = $configClass::config();
        $columns = $config['columns'];
        $model = $config['model'];

        // Build query with default sort
        $query = $model::query();
        $sortable = collect($columns)->firstWhere('sortable', true);
        $defaultSort = $sortable['key'] ?? $columns[0]['key'];
        $query->orderBy($defaultSort);
        $records = $query->get();

        $filename = $resource . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($columns, $records) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            $headers = array_map(fn($col) => $col['label'] ?? $col['key'], $columns);
            fputcsv($handle, $headers, ';');

            // Rows
            foreach ($records as $record) {
                $row = [];
                foreach ($columns as $col) {
                    $value = data_get($record, $col['key']);
                    if ($col['money'] ?? false) {
                        $row[] = number_format($value ?? 0, 2, ',', '.');
                    } elseif ($col['date'] ?? false) {
                        $row[] = $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : '';
                    } elseif (isset($col['format_map']) && isset($col['format_map'][$value])) {
                        $row[] = $col['format_map'][$value];
                    } else {
                        $row[] = $value ?? '';
                    }
                }
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
