<?php

namespace App\Observers;

use App\Models\Work;
use App\Services\ActivityService;

class WorkObserver
{
    public function created(Work $work): void
    {
        ActivityService::logUser(
            __('Yeni İş Başlatıldı'),
            __(':title isimli iş süreci başlatıldı.', ['title' => $work->title]),
            $work
        );

        if ($work->client_id) {
            $client = \App\Models\Client::find($work->client_id);
            if ($client) {
                \App\Models\EntityRelation::relate($client, $work, 'created_for');
            }
        }
        if ($work->project_id) {
            $project = \App\Models\Project::find($work->project_id);
            if ($project) {
                \App\Models\EntityRelation::relate($project, $work, 'associated_with');
            }
        }
    }

    public function updated(Work $work): void
    {
        if ($work->isDirty('status')) {
            $statusLabels = [
                'lead' => 'Talep/Aday',
                'proposal' => 'Teklif',
                'contract' => 'Sözleşme',
                'in_progress' => 'Devam Eden',
                'completed' => 'Tamamlandı'
            ];
            $newStatus = $statusLabels[$work->status] ?? $work->status;
            
            ActivityService::logUser(
                __('Süreç Aşaması Değişti'),
                __(':title iş sürecinin aşaması :status olarak güncellendi.', [
                    'title' => $work->title,
                    'status' => $newStatus
                ]),
                $work
            );
        } else {
            ActivityService::logUser(
                __('İş Bilgileri Güncellendi'),
                __(':title iş sürecinin bilgileri güncellendi.', ['title' => $work->title]),
                $work
            );
        }
    }
}
