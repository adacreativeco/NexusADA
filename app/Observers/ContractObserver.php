<?php

namespace App\Observers;

use App\Models\Contract;
use App\Services\ActivityService;

class ContractObserver
{
    public function created(Contract $contract): void
    {
        ActivityService::logUser(
            __('Yeni Sözleşme Taslağı'),
            __(':num numaralı sözleşme taslağı oluşturuldu.', ['num' => $contract->contract_number]),
            $contract
        );

        if ($contract->work_id) {
            $work = \App\Models\Work::find($contract->work_id);
            if ($work) {
                \App\Models\EntityRelation::relate($work, $contract, 'has_contract');
            }
        }
        if ($contract->client_id) {
            $client = \App\Models\Client::find($contract->client_id);
            if ($client) {
                \App\Models\EntityRelation::relate($client, $contract, 'signed_contract');
            }
        }
    }

    public function updated(Contract $contract): void
    {
        if ($contract->isDirty('status')) {
            $num = $contract->contract_number;
            switch ($contract->status) {
                case 'pending_approval':
                    ActivityService::logUser(
                        __('Sözleşme Onaya Sunuldu'),
                        __(':num numaralı sözleşme yönetici onayına sunuldu.', ['num' => $num]),
                        $contract
                    );
                    break;
                case 'active':
                    ActivityService::logUser(
                        __('Sözleşme Yürürlüğe Girdi'),
                        __(':num numaralı sözleşme imzalanarak aktif hale getirildi.', ['num' => $num]),
                        $contract
                    );
                    break;
                case 'rejected_internal':
                    ActivityService::logUser(
                        __('Sözleşme Taslağı Reddedildi'),
                        __(':num numaralı sözleşme taslağı yönetici tarafından reddedildi.', ['num' => $num]),
                        $contract
                    );
                    break;
                case 'expired':
                    ActivityService::logUser(
                        __('Sözleşme Süresi Doldu'),
                        __(':num numaralı sözleşmenin süresi sona erdi.', ['num' => $num]),
                        $contract
                    );
                    break;
                case 'terminated':
                    ActivityService::logUser(
                        __('Sözleşme Feshedildi'),
                        __(':num numaralı sözleşme feshedildi.', ['num' => $num]),
                        $contract
                    );
                    break;
            }
        }
    }
}
