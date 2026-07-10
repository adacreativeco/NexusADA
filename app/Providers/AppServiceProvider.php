<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\Project::observe(\App\Observers\ProjectObserver::class);
        \App\Models\Client::observe(\App\Observers\ClientObserver::class);
        \App\Models\Campaign::observe(\App\Observers\CampaignObserver::class);
        \App\Models\Work::observe(\App\Observers\WorkObserver::class);
        \App\Models\Proposal::observe(\App\Observers\ProposalObserver::class);
        \App\Models\Contract::observe(\App\Observers\ContractObserver::class);
        \App\Models\Income::observe(\App\Observers\IncomeObserver::class);
        \App\Models\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Models\Collection::observe(\App\Observers\CollectionObserver::class);

        // Usage Metrics: Logins
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                // owen-it/laravel-auditing formatında manuel audit kaydı
                \DB::table('audits')->insert([
                    'user_type' => get_class($event->user),
                    'user_id' => $event->user->id,
                    'event' => 'login',
                    'auditable_type' => get_class($event->user),
                    'auditable_id' => $event->user->id,
                    'url' => request()->fullUrl(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        );

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA journal_mode=WAL;');
            DB::statement('PRAGMA synchronous=NORMAL;');
            DB::statement('PRAGMA busy_timeout=5000;');
        }
    }
}
