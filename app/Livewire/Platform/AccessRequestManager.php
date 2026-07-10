<?php

namespace App\Livewire\Platform;

use App\Models\AccessRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccessRequestManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPlan = '';
    public ?int $detailId = null;
    public array $detailData = [];
    public string $adminNote = '';

    protected $queryString = ['search', 'filterStatus', 'filterPlan'];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterPlan() { $this->resetPage(); }

    public function showDetail(int $id): void
    {
        $ar = AccessRequest::findOrFail($id);
        $this->detailId = $id;
        $this->adminNote = $ar->notes ?? '';
        $this->detailData = [
            'id' => $ar->id,
            'name' => $ar->name,
            'company_name' => $ar->company_name,
            'email' => $ar->email,
            'phone' => $ar->phone,
            'expected_users' => $ar->expected_users,
            'plan_interest' => $ar->plan_interest,
            'use_case' => $ar->use_case,
            'status' => $ar->status,
            'notes' => $ar->notes,
            'created_at' => $ar->created_at->format('d.m.Y H:i'),
        ];
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
        $this->detailData = [];
        $this->adminNote = '';
    }

    public function updateStatus(int $id, string $status): void
    {
        $ar = AccessRequest::findOrFail($id);
        $ar->update([
            'status' => $status,
            'notes' => $this->adminNote,
        ]);

        $statusLabels = [
            'pending' => 'Bekliyor',
            'reviewed' => 'İncelendi',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'contacted' => 'İletişime Geçildi',
        ];

        $label = $statusLabels[$status] ?? $status;

        $this->dispatch('notify',
            message: "Talep durumu güncellendi: {$label}",
            type: 'success'
        );

        $this->closeDetail();
    }

    public function saveNote(int $id): void
    {
        AccessRequest::findOrFail($id)->update(['notes' => $this->adminNote]);
        $this->dispatch('notify', message: 'Not kaydedildi.', type: 'success');
    }

    public function exportCsv(): StreamedResponse
    {
        $query = $this->buildQuery();

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Tarih', 'Ad Soyad', 'Şirket', 'E-posta', 'Telefon',
                'Kullanıcı Sayısı', 'Plan', 'Kullanım Alanı', 'Durum', 'Not',
            ], ';');

            $query->latest()->chunk(500, function ($items) use ($handle) {
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->created_at->format('d.m.Y H:i'),
                        $item->name,
                        $item->company_name,
                        $item->email,
                        $item->phone ?? '-',
                        $item->expected_users ?? '-',
                        $item->plan_interest,
                        $item->use_case ?? '-',
                        $item->status,
                        $item->notes ?? '-',
                    ], ';');
                }
            });

            fclose($handle);
        }, 'erken-erisim-talepleri-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildQuery()
    {
        $query = AccessRequest::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('company_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPlan) {
            $query->where('plan_interest', $this->filterPlan);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildQuery()->latest();

        return view('livewire.platform.access-request-manager', [
            'requests' => $query->paginate(20),
            'stats' => [
                'total' => AccessRequest::count(),
                'pending' => AccessRequest::where('status', 'pending')->count(),
                'approved' => AccessRequest::where('status', 'approved')->count(),
            ],
        ])->layout('layouts.platform', [
            'title' => 'Erken Erişim Talepleri',
        ]);
    }
}
