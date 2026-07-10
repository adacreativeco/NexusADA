<?php

namespace App\Observers;

use App\Models\Income;
use App\Services\ActivityService;

class IncomeObserver
{
    public function created(Income $income): void
    {
        ActivityService::logUser(
            __('Gelir Kaydedildi'),
            __(':num numaralı fatura/gelir kaydı oluşturuldu: :title (:amount :curr)', [
                'num' => $income->income_number,
                'title' => $income->title,
                'amount' => number_format($income->grand_total, 2, ',', '.'),
                'curr' => $income->currency,
            ]),
            $income
        );

        if ($income->work_id) {
            $work = \App\Models\Work::find($income->work_id);
            if ($work) {
                \App\Models\EntityRelation::relate($work, $income, 'has_income');
            }
        }
        if ($income->client_id) {
            $client = \App\Models\Client::find($income->client_id);
            if ($client) {
                \App\Models\EntityRelation::relate($client, $income, 'billed_to');
            }
        }
    }

    public function updated(Income $income): void
    {
        if ($income->isDirty('status') && $income->status === 'paid') {
            ActivityService::logUser(
                __('Gelir Tahsil Edildi'),
                __(':num numaralı fatura/gelir tamamen tahsil edildi.', ['num' => $income->income_number]),
                $income
            );
        }
    }
}
