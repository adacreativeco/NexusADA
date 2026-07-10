<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterEvent = '';
    public string $filterModel = '';
    public string $filterDateRange = '';
    public string $filterIp = '';
    public string $filterUser = '';
    public ?int $detailId = null;
    public array $detailData = [];

    protected $queryString = ['search', 'filterEvent', 'filterModel'];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterEvent() { $this->resetPage(); }
    public function updatedFilterModel() { $this->resetPage(); }
    public function updatedFilterDateRange() { $this->resetPage(); }
    public function updatedFilterIp() { $this->resetPage(); }
    public function updatedFilterUser() { $this->resetPage(); }

    /**
     * CSV export — streams all matching audit records.
     */
    public function exportCsv(): StreamedResponse
    {
        $query = $this->buildQuery();

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compat
            fwrite($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'Tarih', 'Kullanıcı', 'İşlem', 'Model', 'Model ID',
                'Değişen Alanlar', 'IP', 'URL',
            ], ';');

            // Chunked iteration
            $query->with('user')->latest()->chunk(500, function ($audits) use ($handle) {
                foreach ($audits as $audit) {
                    $changed = implode(', ', array_keys($audit->new_values ?? []));
                    fputcsv($handle, [
                        $audit->created_at->format('d.m.Y H:i:s'),
                        $audit->user?->name ?? 'Sistem',
                        $audit->event,
                        class_basename($audit->auditable_type),
                        $audit->auditable_id,
                        $changed,
                        $audit->ip_address ?? '-',
                        $audit->url ?? '-',
                    ], ';');
                }
            });

            fclose($handle);
        }, 'denetim-kaydi-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function showDetail(int $id)
    {
        $audit = Audit::find($id);
        if ($audit) {
            $this->detailId = $id;
            $this->detailData = [
                'event' => $audit->event,
                'auditable_type' => class_basename($audit->auditable_type),
                'auditable_id' => $audit->auditable_id,
                'old_values' => $audit->old_values ?? [],
                'new_values' => $audit->new_values ?? [],
                'url' => $audit->url,
                'ip_address' => $audit->ip_address,
                'user_agent' => $audit->user_agent,
                'created_at' => $audit->created_at->format('d.m.Y H:i:s'),
                'user_name' => $audit->user?->name ?? 'Sistem',
            ];
        }
    }

    public function closeDetail()
    {
        $this->detailId = null;
        $this->detailData = [];
    }

    protected function getModelTypes(): array
    {
        return Audit::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->mapWithKeys(fn($type) => [$type => class_basename($type)])
            ->toArray();
    }

    protected function getUsers(): array
    {
        return Audit::with('user')
            ->whereNotNull('user_id')
            ->select('user_id')
            ->distinct()
            ->get()
            ->mapWithKeys(fn($a) => [$a->user_id => $a->user?->name ?? 'ID:' . $a->user_id])
            ->toArray();
    }

    protected function buildQuery()
    {
        $query = Audit::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('url', 'like', "%{$this->search}%")
                  ->orWhere('ip_address', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        if ($this->filterModel) {
            $query->where('auditable_type', $this->filterModel);
        }

        if ($this->filterIp) {
            $query->where('ip_address', 'like', "%{$this->filterIp}%");
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterDateRange) {
            match ($this->filterDateRange) {
                '7' => $query->where('created_at', '>=', now()->subDays(7)),
                '30' => $query->where('created_at', '>=', now()->subDays(30)),
                '90' => $query->where('created_at', '>=', now()->subDays(90)),
                default => null,
            };
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildQuery()->with('user');

        return view('livewire.admin.audit-log', [
            'audits' => $query->paginate(25),
            'modelTypes' => $this->getModelTypes(),
            'users' => $this->getUsers(),
        ])->layout('layouts.admin', [
            'title' => 'Denetim Kaydı',
            'breadcrumb' => 'Denetim Kaydı',
        ]);
    }
}
