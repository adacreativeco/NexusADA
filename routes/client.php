<?php

use Illuminate\Support\Facades\Route;

Route::prefix('client')->group(function () {
    // Guest routes
    Route::middleware('guest:client')->group(function () {
        Route::get('login', \App\Livewire\Client\Login::class)->name('client.login');
    });

    // Authenticated client routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/', \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
        Route::get('projects', \App\Livewire\Client\Projects::class)->name('client.projects');
        Route::get('invoices', \App\Livewire\Client\Invoices::class)->name('client.invoices');
        Route::post('logout', function () {
            auth('client')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('client.login');
        })->name('client.logout');
    });
});
