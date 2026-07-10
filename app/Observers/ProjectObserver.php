<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\AutomationEngine;
use App\Services\ActivityService;

class ProjectObserver
{
    public function created(Project $project): void
    {
        ActivityService::logUser(
            __('Yeni Proje Başlatıldı'),
            __(':title isimli proje başlatıldı.', ['title' => $project->title]),
            $project
        );

        app(AutomationEngine::class)->evaluate('Project', 'created', $project);
    }

    public function updated(Project $project): void
    {
        if ($project->isDirty('status')) {
            ActivityService::logUser(
                __('Proje Durumu Değişti'),
                __(':title projesinin durumu :status olarak güncellendi.', [
                    'title' => $project->title,
                    'status' => $project->status
                ]),
                $project
            );

            app(AutomationEngine::class)->evaluate('Project', 'status_changed', $project);
        } else {
            ActivityService::logUser(
                __('Proje Güncellendi'),
                __(':title projesinin detayları güncellendi.', ['title' => $project->title]),
                $project
            );
        }

        app(AutomationEngine::class)->evaluate('Project', 'updated', $project);
    }
}
