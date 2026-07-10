<?php

namespace App\Livewire\Platform;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Invoice;

class Dashboard extends Component
{
    public function render()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trial')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();

        // MRR hesaplama
        $mrr = Tenant::where('status', 'active')
            ->whereNotNull('plan_id')
            ->get()
            ->sum(function ($tenant) {
                return $tenant->plan->price_monthly ?? 0;
            });

        // Yaklaşan abonelik bitişleri (7 gün)
        $expiringSoon = Tenant::expiring(7)->with('plan')->get();

        // Son kayıt olan kiracılar
        $recentTenants = Tenant::with('plan')->latest()->take(5)->get();

        // Son faturalar
        $recentInvoices = Invoice::with(['tenant', 'plan'])->latest()->take(5)->get();

        // Plan dağılımı
        $planDistribution = Plan::withCount('tenants')->active()->get();

        return view('livewire.platform.dashboard', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'trialTenants' => $trialTenants,
            'suspendedTenants' => $suspendedTenants,
            'mrr' => $mrr,
            'expiringSoon' => $expiringSoon,
            'recentTenants' => $recentTenants,
            'recentInvoices' => $recentInvoices,
            'planDistribution' => $planDistribution,
        ])->layout('layouts.platform');
    }
}
