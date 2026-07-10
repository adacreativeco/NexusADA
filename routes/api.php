<?php

use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Desktop App — Version Check (no auth required)
Route::get('desktop/version', function () {
    $settings = cache()->remember('desktop_version', 300, function () {
        return \App\Models\PlatformSetting::getDesktopVersion();
    });

    return response()->json($settings);
})->name('api.desktop.version');

/*
|--------------------------------------------------------------------------
| API v1 — Sanctum Protected
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // ── Auth ────────────────────────────────────────
    Route::get('me', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'user'   => $request->user()->only('id', 'name', 'email'),
            'tenant' => $request->user()->tenant?->only('id', 'name'),
        ]);
    })->name('api.v1.me');

    // ── Projects ────────────────────────────────────
    Route::apiResource('projects', ProjectController::class);

    // ── Tasks ───────────────────────────────────────
    Route::apiResource('tasks', TaskController::class);

    // ── Clients ─────────────────────────────────────
    Route::apiResource('clients', \App\Http\Controllers\Api\V1\ClientController::class);

    // ── Campaigns ───────────────────────────────────
    Route::apiResource('campaigns', \App\Http\Controllers\Api\V1\CampaignController::class)->only(['index', 'show']);

    // ── Reports ─────────────────────────────────────
    Route::get('reports', [\App\Http\Controllers\Api\V1\ReportController::class, 'index']);
});

