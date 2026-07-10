<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── ADA Co-OS Scheduled Tasks ──────────────────────
Schedule::command('nexus:process-recurring')->daily()->description('Tekrar eden görev şablonlarını işle');
Schedule::command('nexus:process-deadlines')->dailyAt('08:00')->description('Gecikmiş görev otomasyonlarını tetikle');
Schedule::command('imap:sync')->everyFiveMinutes()->description('IMAP e-posta senkronizasyonu');

Schedule::call(function () {
    $contracts = \App\Models\Contract::where('status', 'active')
        ->whereNotNull('end_date')
        ->get();

    foreach ($contracts as $contract) {
        $daysLeft = (int) now()->diffInDays($contract->end_date, false);
        if ($daysLeft === (int) $contract->reminder_days) {
            $users = \App\Models\User::role(['webmaster', 'pm'])
                ->where('tenant_id', $contract->tenant_id)
                ->get();

            foreach ($users as $user) {
                \App\Services\NotificationService::contractExpiring($contract, $user->id, $daysLeft);
            }
        }
    }
})->daily()->description('Süresi yaklaşan sözleşmeler için bildirim gönder');

Schedule::call(function () {
    $recurringExpenses = \App\Models\Expense::where('is_recurring', true)->get();
    foreach ($recurringExpenses as $expense) {
        $exists = \App\Models\Expense::where('tenant_id', $expense->tenant_id)
            ->where('vendor', $expense->vendor)
            ->where('title', $expense->title)
            ->where('amount', $expense->amount)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('id', '!=', $expense->id)
            ->exists();

        if (!$exists) {
            $newExpense = $expense->replicate();
            $newExpense->status = 'draft';
            $newExpense->expense_number = \App\Models\Expense::generateExpenseNumber();
            $newExpense->save();
        }
    }
})->daily()->description('Tekrarlayan gider şablonlarını otomatik kopyala');

Schedule::call(function () {
    // 1. Overdue Incomes (sent status, older than 15 days)
    $overdueIncomes = \App\Models\Income::where('status', 'sent')
        ->where('created_at', '<', now()->subDays(15))
        ->get();

    foreach ($overdueIncomes as $income) {
        $users = \App\Models\User::role(['webmaster', 'pm'])
            ->where('tenant_id', $income->tenant_id)
            ->get();

        foreach ($users as $user) {
            \App\Services\NotificationService::send(
                $user->id,
                'collection_overdue',
                'Geciken Tahsilat Uyarısı',
                "\"{$income->income_number}\" numaralı faturanın vadesi geçti. Lütfen tahsilatı takip edin.",
                ['url' => '/admin/incomes', 'model_type' => 'Income', 'model_id' => $income->id]
            );
        }
    }

    // 2. Financial instruments due date (due_date in next 3 days, status: pending)
    $dueInstruments = \App\Models\FinancialInstrument::where('status', 'pending')
        ->whereBetween('due_date', [now()->toDateString(), now()->addDays(3)->toDateString()])
        ->get();

    foreach ($dueInstruments as $inst) {
        $users = \App\Models\User::role(['webmaster', 'pm'])
            ->where('tenant_id', $inst->tenant_id)
            ->get();

        foreach ($users as $user) {
            $typeLabel = $inst->type === 'check' ? 'Çek' : 'Senet';
            $dirLabel = $inst->direction === 'inbound' ? 'tahsil' : 'ödeme';
            \App\Services\NotificationService::send(
                $user->id,
                'instrument_due',
                'Kıymetli Evrak Vade Uyarısı',
                "\"{$inst->instrument_number}\" numaralı {$typeLabel} evrağının vade tarihi yaklaşıyor ({$inst->due_date->format('d.m.Y')}). Lütfen {$dirLabel} işlemini kontrol edin.",
                ['url' => '/admin/financial-instruments', 'model_type' => 'FinancialInstrument', 'model_id' => $inst->id]
            );
        }
    }
})->daily()->description('Vadesi yaklaşan çek/senet ve geciken tahsilatlar için uyarı gönder');

