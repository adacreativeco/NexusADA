<?php

use Illuminate\Support\Facades\Route;
use App\Models\Tenant;

/*
|--------------------------------------------------------------------------
| Platform (Webmaster) Routes
|--------------------------------------------------------------------------
| /platform prefix'li rotalar — sadece webmaster/super_admin rolüne sahip kullanıcılar.
|--------------------------------------------------------------------------
*/

Route::prefix('platform')->group(function () {

    // ── Protected Platform Routes ──────────────────────────────
    Route::middleware(['auth', 'role:webmaster|super_admin'])->group(function () {

        // Dashboard
        Route::get('/', \App\Livewire\Platform\Dashboard::class)
            ->name('platform.dashboard');

        // Tenant Management
        Route::get('tenants', \App\Livewire\Platform\TenantManager::class)
            ->name('platform.tenants');

        // Plan Management
        Route::get('plans', \App\Livewire\Platform\PlanManager::class)
            ->name('platform.plans');

        // Invoice Management
        Route::get('invoices', \App\Livewire\Platform\InvoiceManager::class)
            ->name('platform.invoices');

        // Announcements
        Route::get('announcements', \App\Livewire\Platform\AnnouncementManager::class)
            ->name('platform.announcements');

        // System Logs (Audit)
        Route::get('system-log', \App\Livewire\Platform\SystemLog::class)
            ->name('platform.system-log');

        // Access Request Management
        Route::get('access-requests', \App\Livewire\Platform\AccessRequestManager::class)
            ->name('platform.access-requests');

        // Platform Settings
        Route::get('settings', \App\Livewire\Platform\Settings::class)
            ->name('platform.settings');

        // Usage Stats (Super Admin Metrics)
        Route::get('usage-stats', \App\Livewire\Platform\UsageStats::class)
            ->name('platform.usage-stats');

        // Team Management (platform-level)
        Route::get('team', \App\Livewire\Admin\TeamManager::class)
            ->name('platform.team');

        // ── Impersonate Tenant ─────────────────────────────────
        Route::post('tenants/{tenant}/impersonate', function (Tenant $tenant) {
            logger()->info('🔍 Impersonate started', [
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()->name,
                'tenant_id'   => $tenant->id,
                'tenant_name' => $tenant->name,
                'ip'          => request()->ip(),
                'timestamp'   => now()->toIso8601String(),
            ]);

            session([
                'impersonating_tenant_id'   => $tenant->id,
                'impersonating_tenant_name' => $tenant->name,
            ]);

            return redirect()->route('admin.dashboard');
        })->name('platform.impersonate');

        Route::post('impersonate/stop', function () {
            logger()->info('🔍 Impersonate stopped', [
                'user_id'   => auth()->id(),
                'tenant_id' => session('impersonating_tenant_id'),
                'timestamp' => now()->toIso8601String(),
            ]);

            session()->forget([
                'impersonating_tenant_id',
                'impersonating_tenant_name',
            ]);

            return redirect()->route('platform.tenants');
        })->name('platform.impersonate.stop');
    });
});

