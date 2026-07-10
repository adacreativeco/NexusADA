<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // 1. Database connection
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'ok',
                'driver' => DB::connection()->getDriverName(),
            ];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'fail', 'error' => $e->getMessage()];
            $healthy = false;
        }

        // 2. Storage write test
        try {
            $testFile = 'health_check_' . time() . '.tmp';
            Storage::put($testFile, 'ok');
            Storage::delete($testFile);
            $checks['storage'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $checks['storage'] = ['status' => 'fail', 'error' => $e->getMessage()];
            $healthy = false;
        }

        // 3. Cache
        try {
            Cache::put('health_check', true, 10);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            $checks['cache'] = ['status' => $value ? 'ok' : 'fail'];
            if (!$value) $healthy = false;
        } catch (\Throwable $e) {
            $checks['cache'] = ['status' => 'fail', 'error' => $e->getMessage()];
            $healthy = false;
        }

        // 4. Queue table exists
        try {
            $jobCount = DB::table('jobs')->count();
            $failedCount = DB::table('failed_jobs')->count();
            $checks['queue'] = [
                'status'      => 'ok',
                'pending'     => $jobCount,
                'failed_24h'  => $failedCount,
            ];
        } catch (\Throwable $e) {
            $checks['queue'] = ['status' => 'fail', 'error' => $e->getMessage()];
            $healthy = false;
        }

        return response()->json([
            'status'    => $healthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'version'   => config('app.version', 'unknown'),
            'checks'    => $checks,
        ], $healthy ? 200 : 503);
    }
}
