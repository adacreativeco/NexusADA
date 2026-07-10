<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\ActivityService;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        ActivityService::logUser(
            __('Gider Kaydedildi'),
            __(':num numaralı gider kaydı oluşturuldu: :title (:amount :curr)', [
                'num' => $expense->expense_number,
                'title' => $expense->title,
                'amount' => number_format($expense->grand_total, 2, ',', '.'),
                'curr' => $expense->currency,
            ]),
            $expense
        );
    }

    public function updated(Expense $expense): void
    {
        if ($expense->isDirty('status')) {
            $num = $expense->expense_number;
            switch ($expense->status) {
                case 'pending_approval':
                    ActivityService::logUser(
                        __('Gider Onaya Sunuldu'),
                        __(':num numaralı gider onay için yöneticilere iletildi.', ['num' => $num]),
                        $expense
                    );
                    break;
                case 'approved_internal':
                    ActivityService::logUser(
                        __('Gider Onaylandı'),
                        __(':num numaralı gider yöneticiler tarafından onaylandı.', ['num' => $num]),
                        $expense
                    );
                    break;
                case 'rejected_internal':
                    ActivityService::logUser(
                        __('Gider Reddedildi'),
                        __(':num numaralı gider yöneticiler tarafından reddedildi.', ['num' => $num]),
                        $expense
                    );
                    break;
            }
        }
    }
}
