<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Nexus Admin Routes
|--------------------------------------------------------------------------
| Tüm /admin prefix'li rotalar burada tanımlanır.
| Auth middleware ile korunur.
|--------------------------------------------------------------------------
*/

// ── Public Auth Routes ──────────────────────────────────────────
Route::prefix('admin')->group(function () {

    // Login
    Route::get('login', \App\Livewire\Admin\Auth\Login::class)
        ->name('admin.login')
        ->middleware('guest');

    // 2FA Challenge
    Route::get('login/two-factor', \App\Livewire\Admin\Auth\TwoFactorChallenge::class)
        ->name('admin.two-factor-challenge')
        ->middleware('guest');

    // Register (Self-Registration)
    Route::get('register', \App\Livewire\Admin\Auth\Register::class)
        ->name('admin.register')
        ->middleware('guest');

    // Forgot Password
    Route::get('forgot-password', \App\Livewire\Admin\Auth\ForgotPassword::class)
        ->name('admin.forgot-password')
        ->middleware('guest');

    // Reset Password
    Route::get('reset-password/{token}', \App\Livewire\Admin\Auth\ResetPassword::class)
        ->name('password.reset')
        ->middleware('guest');

    // Logout
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('admin.logout');

    // ── E-posta Doğrulama ───────────────────────────────────────
    Route::get('email/verify', \App\Livewire\Admin\Auth\EmailVerifyNotice::class)
        ->name('verification.notice')
        ->middleware('auth');

    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('admin.dashboard');
    })->name('verification.verify')
      ->middleware(['auth', 'signed']);

    // ── Onay Bekleniyor ─────────────────────────────────────────
    Route::get('waiting-approval', \App\Livewire\Admin\Auth\WaitingApproval::class)
        ->name('admin.waiting-approval')
        ->middleware(['auth', 'verified']);

    // ── Protected Admin Routes ──────────────────────────────────
    Route::middleware(['auth', 'verified', 'ensure_tenant'])->group(function () {

        // Dashboard
        Route::get('/', \App\Livewire\Admin\Dashboard::class)
            ->name('admin.dashboard');

        // Proposal Builder (custom page)
        Route::get('proposal', \App\Livewire\Admin\ProposalBuilder::class)
            ->name('admin.proposal');

        // PDF Reports
        Route::get('reports/project/{project}', [\App\Http\Controllers\ReportController::class, 'projectPdf'])
            ->name('admin.report.project');
        Route::get('reports/client/{client}', [\App\Http\Controllers\ReportController::class, 'clientPdf'])
            ->name('admin.report.client');
        Route::get('reports/campaign/{campaign}', [\App\Http\Controllers\ReportController::class, 'campaignPdf'])
            ->name('admin.report.campaign');
        Route::get('reports/proposal/{proposal}', [\App\Http\Controllers\ReportController::class, 'proposalPdf'])
            ->name('admin.report.proposal');
        Route::get('proposals/{proposal}/view', [\App\Http\Controllers\ReportController::class, 'proposalView'])
            ->name('admin.proposal.view');
        Route::get('reports/contract/{contract}', [\App\Http\Controllers\ReportController::class, 'contractPdf'])
            ->name('admin.report.contract');

        // Calendar
        Route::get('calendar', \App\Livewire\Admin\Calendar::class)
            ->name('admin.calendar');

        // Audit Log
        Route::get('audit-log', \App\Livewire\Admin\AuditLog::class)
            ->name('admin.audit-log');

        // Team Manager
        Route::get('team', \App\Livewire\Admin\TeamManager::class)
            ->name('admin.team');

        // Task Board (Kanban)
        Route::get('tasks/board', \App\Livewire\Admin\TaskBoard::class)
            ->name('admin.tasks.board');

        // Timesheet
        Route::get('timesheet', \App\Livewire\Admin\Timesheet::class)
            ->name('admin.timesheet');

        // Gantt Chart
        Route::get('gantt/{project?}', \App\Livewire\Admin\Gantt::class)
            ->name('admin.gantt');

        // CSV Export (direct HTTP — bypasses Livewire)
        Route::get('export/{resource}/csv', [\App\Http\Controllers\ExportController::class, 'csv'])
            ->name('admin.export.csv');

        // Email Account Management
        Route::get('email-accounts', \App\Livewire\Admin\EmailAccountManager::class)
            ->name('admin.email-accounts');

        // Two-Factor Authentication Setup
        Route::get('two-factor', \App\Livewire\Admin\TwoFactorSetup::class)
            ->name('admin.two-factor');

        // KVKK & GDPR
        Route::get('kvkk/export', [\App\Http\Controllers\KvkkController::class, 'export'])
            ->name('admin.kvkk.export');
        Route::post('kvkk/anonymize', [\App\Http\Controllers\KvkkController::class, 'anonymize'])
            ->name('admin.kvkk.anonymize');

        // Account
        Route::delete('account', [\App\Http\Controllers\AccountController::class, 'destroy'])
            ->name('admin.account.destroy');
        Route::get('profile', \App\Livewire\Admin\Profile::class)
            ->name('admin.profile');

        // Work Module Custom Routes
        Route::get('works/pipeline', \App\Livewire\Admin\WorkPipeline::class)
            ->name('admin.works.pipeline');
        Route::get('works/{id}/timeline', \App\Livewire\Admin\WorkTimeline::class)
            ->name('admin.works.timeline');
        Route::get('notifications', \App\Livewire\Admin\NotificationCenter::class)
            ->name('admin.notifications');
        Route::get('finance', \App\Livewire\Admin\FinanceDashboard::class)
            ->name('admin.finance');
        Route::get('workflows/{id}/design', \App\Livewire\Admin\WorkflowDesigner::class)
            ->name('admin.workflows.design');

        // Universal Search
        Route::get('search/universal', [\App\Http\Controllers\Admin\SearchController::class, 'search'])
            ->name('admin.search.universal');

        // Generic Resource Routes
        Route::get('{resource}', \App\Livewire\Admin\NexusTable::class)
            ->name('admin.resource.index');
    });
});
