<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Services\ActivityService;

class ProposalObserver
{
    public function created(Proposal $proposal): void
    {
        ActivityService::logUser(
            __('Yeni Teklif Hazırlandı'),
            __(':num numaralı taslak teklif oluşturuldu.', ['num' => $proposal->proposal_number]),
            $proposal
        );

        if ($proposal->work_id) {
            $work = \App\Models\Work::find($proposal->work_id);
            if ($work) {
                \App\Models\EntityRelation::relate($work, $proposal, 'has_proposal');
            }
        }
        if ($proposal->client_id) {
            $client = \App\Models\Client::find($proposal->client_id);
            if ($client) {
                \App\Models\EntityRelation::relate($client, $proposal, 'received_proposal');
            }
        }
    }

    public function updated(Proposal $proposal): void
    {
        if ($proposal->isDirty('status')) {
            $num = $proposal->proposal_number;
            switch ($proposal->status) {
                case 'pending_approval':
                    ActivityService::logUser(
                        __('Teklif Onaya Sunuldu'),
                        __(':num numaralı teklif yönetici onayına sunuldu.', ['num' => $num]),
                        $proposal
                    );
                    break;
                case 'approved_internal':
                    ActivityService::logUser(
                        __('Teklif Onaylandı'),
                        __(':num numaralı teklif yönetici tarafından onaylandı.', ['num' => $num]),
                        $proposal
                    );
                    break;
                case 'rejected_internal':
                    ActivityService::logUser(
                        __('Teklif Reddedildi'),
                        __(':num numaralı teklif reddedildi.', ['num' => $num]),
                        $proposal
                    );
                    break;
                case 'sent':
                    ActivityService::logUser(
                        __('Teklif Gönderildi'),
                        __(':num numaralı teklif müşteriye iletildi.', ['num' => $num]),
                        $proposal
                    );
                    break;
                case 'accepted':
                    ActivityService::logUser(
                        __('Teklif Kabul Edildi'),
                        __(':num numaralı teklif müşteri tarafından kabul edildi.', ['num' => $num]),
                        $proposal
                    );
                    break;
                case 'declined':
                    ActivityService::logUser(
                        __('Teklif Reddedildi (Müşteri)'),
                        __(':num numaralı teklif müşteri tarafından reddedildi.', ['num' => $num]),
                        $proposal
                    );
                    break;
            }
        }
    }
}
