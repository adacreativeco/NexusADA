<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/platform.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/client.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant' => \App\Http\Middleware\SetTenantContext::class,
            'ensure_tenant' => \App\Http\Middleware\EnsureTenantAccess::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\SetTenantContext::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackUsage::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        if (class_exists(\Sentry\Laravel\Integration::class)) {
            \Sentry\Laravel\Integration::handles($exceptions);
        }
    })->create();
