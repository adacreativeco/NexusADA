<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Services\ActivityService;

class CampaignObserver
{
    public function created(Campaign $campaign): void
    {
        ActivityService::logUser(
            __('Yeni Kampanya Başlatıldı'),
            __(':title isimli yeni pazarlama kampanyası oluşturuldu.', ['title' => $campaign->title]),
            $campaign
        );
    }

    public function updated(Campaign $campaign): void
    {
        if ($campaign->isDirty('status')) {
            ActivityService::logUser(
                __('Kampanya Durumu Değişti'),
                __(':title kampanyasının durumu :status yapıldı.', [
                    'title' => $campaign->title,
                    'status' => $campaign->status
                ]),
                $campaign
            );
        } else {
            ActivityService::logUser(
                __('Kampanya Güncellendi'),
                __(':title kampanyasının detayları güncellendi.', ['title' => $campaign->title]),
                $campaign
            );
        }
    }
}
