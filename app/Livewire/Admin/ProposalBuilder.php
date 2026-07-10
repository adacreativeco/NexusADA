<?php

namespace App\Livewire\Admin;

use App\Models\Proposal;
use App\Models\Client;
use App\Models\Work;
use App\Models\User;
use Livewire\Component;

class ProposalBuilder extends Component
{
    public ?int $proposalId = null;

    // Form fields
    public $clientId = '';
    public $workId = '';
    public string $title = '';
    public string $proposalNumber = '';
    public string $validUntil = '';
    public string $currency = 'TRY';
    public string $notes = '';
    public string $terms = '';

    // Line items
    public array $items = [];

    // Financial totals
    public float $subtotal = 0;
    public float $taxTotal = 0;
    public float $discountTotal = 0; // overall discount
    public float $grandTotal = 0;

    public function mount()
    {
        $proposalId = request()->query('proposal_id');
        
        if ($proposalId) {
            $proposal = Proposal::findOrFail($proposalId);
            $this->proposalId = $proposal->id;
            $this->clientId = $proposal->client_id;
            $this->workId = $proposal->work_id;
            $this->title = $proposal->title;
            $this->proposalNumber = $proposal->proposal_number;
            $this->validUntil = $proposal->valid_until ? $proposal->valid_until->format('Y-m-d') : '';
            $this->currency = $proposal->currency;
            $this->notes = $proposal->notes ?? '';
            $this->terms = $proposal->terms ?? '';
            $this->items = $proposal->items ?? [];
            $this->discountTotal = (float) $proposal->discount_total;
        } else {
            $this->proposalNumber = Proposal::generateNumber();
            $this->validUntil = now()->addDays(14)->format('Y-m-d');
            $this->items = [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0.00, 'vat_rate' => 20]
            ];
        }

        $this->calculateTotals();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'items') || $propertyName === 'discountTotal') {
            $this->calculateTotals();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0.00,
            'vat_rate' => 20
        ];
        $this->calculateTotals();
    }

    public function removeItem(int $index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        
        if (empty($this->items)) {
            $this->addItem();
        }
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $this->taxTotal = 0;

        foreach ($this->items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $vat = (float) ($item['vat_rate'] ?? 0);

            $lineSubtotal = $qty * $price;
            $lineTax = $lineSubtotal * ($vat / 100);

            $this->subtotal += $lineSubtotal;
            $this->taxTotal += $lineTax;
        }

        $this->grandTotal = ($this->subtotal + $this->taxTotal) - (float)$this->discountTotal;
        if ($this->grandTotal < 0) {
            $this->grandTotal = 0;
        }
    }

    public function saveProposal(string $targetStatus = 'draft')
    {
        $this->validate([
            'clientId' => 'required',
            'title' => 'required|string|max:255',
            'proposalNumber' => 'required|string',
            'validUntil' => 'required|date',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'clientId.required' => 'Müşteri seçimi zorunludur.',
            'title.required' => 'Teklif başlığı zorunludur.',
            'validUntil.required' => 'Geçerlilik tarihi zorunludur.',
            'items.*.description.required' => 'Kalem açıklaması boş bırakılamaz.',
            'items.*.quantity.required' => 'Miktar zorunludur.',
            'items.*.unit_price.required' => 'Birim fiyat zorunludur.',
        ]);

        $this->calculateTotals();

        $data = [
            'client_id' => $this->clientId,
            'work_id' => $this->workId ?: null,
            'title' => $this->title,
            'proposal_number' => $this->proposalNumber,
            'currency' => $this->currency,
            'valid_until' => $this->validUntil,
            'notes' => $this->notes ?: null,
            'terms' => $this->terms ?: null,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'tax_total' => $this->taxTotal,
            'discount_total' => $this->discountTotal,
            'grand_total' => $this->grandTotal,
            'created_by' => auth()->id(),
        ];

        if ($this->proposalId) {
            $proposal = Proposal::findOrFail($this->proposalId);
            $proposal->update($data);
        } else {
            // Check uniqueness of auto number
            if (Proposal::where('proposal_number', $this->proposalNumber)->exists()) {
                $this->proposalNumber = Proposal::generateNumber();
                $data['proposal_number'] = $this->proposalNumber;
            }
            
            // Resolve tenant_id dynamically
            $data['tenant_id'] = auth()->user()->tenant_id 
                ?? session('impersonating_tenant_id') 
                ?? \App\Models\Client::find($this->clientId)?->tenant_id 
                ?? \App\Models\Tenant::value('id') 
                ?? 3;

            $proposal = Proposal::create($data);
            $this->proposalId = $proposal->id;
        }

        if ($targetStatus === 'pending_approval') {
            $proposal->submitForApproval();
            $this->dispatch('notify', type: 'success', message: 'Teklif kaydedildi ve onay sürecine gönderildi.');
            return redirect()->to('/admin/proposals');
        }

        if ($targetStatus === 'approved_internal') {
            $proposal->approve(auth()->user(), 'Yönetici tarafından doğrudan onaylandı.');
            $this->dispatch('notify', type: 'success', message: 'Teklif doğrudan onaylandı.');
            return redirect()->to('/admin/proposals');
        }

        if ($targetStatus === 'sent') {
            if (in_array($proposal->status, ['draft', 'pending_approval'])) {
                $proposal->approve(auth()->user(), 'Müşteriye gönderilmeden önce sistem tarafından otomatik onaylandı.');
            }
            $proposal->update(['status' => 'sent']);
            $this->dispatch('notify', type: 'success', message: 'Teklif onaylandı ve müşteriye gönderildi.');
            return redirect()->to('/admin/proposals');
        }

        $proposal->update(['status' => 'draft']);
        
        $this->dispatch('notify', type: 'success', message: 'Teklif taslak olarak kaydedildi.');
        return redirect()->to('/admin/proposals');
    }

    public function render()
    {
        return view('livewire.admin.proposal-builder', [
            'clients' => Client::orderBy('name')->get(),
            'works' => Work::where('status', '!=', 'completed')->orderBy('title')->get(),
        ])->layout('layouts.admin', [
            'title' => 'Teklif Motoru',
            'breadcrumb' => 'Teklif Motoru',
        ]);
    }
}
