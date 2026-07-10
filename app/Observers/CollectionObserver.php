<?php

namespace App\Observers;

use App\Models\Collection;
use App\Services\ActivityService;

class CollectionObserver
{
    public function created(Collection $collection): void
    {
        ActivityService::logUser(
            __('Tahsilat Alındı'),
            __(':amount :curr tutarında ödeme tahsil edildi. Ödeme yöntemi: :method.', [
                'amount' => number_format($collection->amount, 2, ',', '.'),
                'curr' => $collection->currency,
                'method' => $collection->payment_method ?: 'Bilinmeyen',
            ]),
            $collection
        );

        if ($collection->work_id) {
            $work = \App\Models\Work::find($collection->work_id);
            if ($work) {
                \App\Models\EntityRelation::relate($work, $collection, 'collected_for');
            }
        }
        if ($collection->client_id) {
            $client = \App\Models\Client::find($collection->client_id);
            if ($client) {
                \App\Models\EntityRelation::relate($client, $collection, 'paid_by');
            }
        }
    }
}
