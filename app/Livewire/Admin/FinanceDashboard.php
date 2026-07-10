<?php

namespace App\Livewire\Admin;

use App\Models\BankAccount;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Collection;
use App\Models\FinancialInstrument;
use Livewire\Component;

class FinanceDashboard extends Component
{
    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        // 1. Core Financial Aggregations
        $totalBalance = BankAccount::where('tenant_id', $tenantId)->sum('balance');
        
        $monthlyIncomes = Income::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');

        $monthlyExpenses = Expense::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'rejected_internal')
            ->sum('grand_total');

        $monthlyCollections = Collection::where('tenant_id', $tenantId)
            ->whereMonth('collected_at', now()->month)
            ->whereYear('collected_at', now()->year)
            ->sum('amount');

        // Burn Rate / Profit Margin
        $burnRate = 0.0;
        if ($monthlyIncomes > 0) {
            $burnRate = round(($monthlyExpenses / $monthlyIncomes) * 100, 1);
        }

        // 2. Data Lists
        $bankAccounts = BankAccount::where('tenant_id', $tenantId)->get();
        
        $pendingCollections = Income::where('tenant_id', $tenantId)
            ->where('status', '!=', 'paid')
            ->with('client')
            ->latest()
            ->limit(5)
            ->get();

        $recentExpenses = Expense::where('tenant_id', $tenantId)
            ->latest()
            ->limit(5)
            ->get();

        $pendingInstruments = FinancialInstrument::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('livewire.admin.finance-dashboard', [
            'totalBalance' => $totalBalance,
            'monthlyIncomes' => $monthlyIncomes,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyCollections' => $monthlyCollections,
            'burnRate' => $burnRate,
            'bankAccounts' => $bankAccounts,
            'pendingCollections' => $pendingCollections,
            'recentExpenses' => $recentExpenses,
            'pendingInstruments' => $pendingInstruments,
        ])->layout('layouts.admin', [
            'title' => 'Finans Paneli',
            'breadcrumb' => 'Finans',
        ]);
    }
}
