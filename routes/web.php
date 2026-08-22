<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/platforms', function () {
    return view('platforms');
});

// ── Health Check ────────────────────────────────
Route::get('/health', HealthController::class);

// ── Legal Pages ─────────────────────────────────
Route::get('/kvkk', fn() => view('legal.kvkk'))->name('kvkk');
Route::get('/gizlilik-politikasi', fn() => view('legal.gizlilik'))->name('privacy');
Route::get('/kullanim-kosullari', fn() => view('legal.kullanim-kosullari'))->name('terms');

// Legal aliases (/legal/* URL pattern — SEO + KVKK uyumu)
Route::get('/legal/aydinlatma-metni', fn() => view('legal.kvkk'))->name('legal.kvkk');
Route::get('/legal/gizlilik-politikasi', fn() => view('legal.gizlilik'))->name('legal.privacy');
Route::get('/legal/cerez-politikasi', fn() => view('legal.gizlilik'))->name('legal.cookies');
Route::get('/legal/kullanim-sartlari', fn() => view('legal.kullanim-kosullari'))->name('legal.terms');

Route::get('/system-status', function () {
    return response()->json([
        'status' => 'operational',
        'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'redis' => config('database.redis.client') ? 'configured' : 'none',
        'queue' => config('queue.default'),
        'timestamp' => now()->toIso8601String()
    ]);
});

// ── PWA & Static Service Worker Routes ──────────
Route::get('/manifest.json', function () {
    $path = public_path('manifest.json');
    if (!file_exists($path)) {
        return response()->json(['name' => 'ADA Co-OS', 'short_name' => 'NexusADA'], 200, ['Content-Type' => 'application/json']);
    }
    return response()->file($path, ['Content-Type' => 'application/json']);
});

Route::get('/sw.js', function () {
    $path = public_path('sw.js');
    if (!file_exists($path)) {
        return response("self.addEventListener('install', e => {});", 200, ['Content-Type' => 'application/javascript']);
    }
    return response()->file($path, ['Content-Type' => 'application/javascript']);
});
