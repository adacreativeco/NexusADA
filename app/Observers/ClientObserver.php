<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\ActivityService;

class ClientObserver
{
    public function created(Client $client): void
    {
        ActivityService::logUser(
            __('Yeni Müşteri Eklendi'),
            __(':name isimli yeni müşteri CRM sistemine kaydedildi.', ['name' => $client->name]),
            $client
        );
    }

    public function updated(Client $client): void
    {
        ActivityService::logUser(
            __('Müşteri Bilgileri Güncellendi'),
            __(':name isimli müşterinin detayları güncellendi.', ['name' => $client->name]),
            $client
        );
    }
}
