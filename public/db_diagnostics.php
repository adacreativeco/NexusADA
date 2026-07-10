<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json; charset=utf-8');

try {
    $tables = [];
    $dbName = DB::connection()->getDatabaseName();
    
    // Get table sizes and row counts in MySQL
    $results = DB::select("
        SELECT table_name AS name, 
               table_rows AS row_count, 
               round(((data_length + index_length) / 1024 / 1024), 2) AS size_mb 
        FROM information_schema.TABLES 
        WHERE table_schema = ?
    ", [$dbName]);

    foreach ($results as $row) {
        $tables[] = [
            'name' => $row->name,
            'rows' => (int)$row->row_count,
            'size_mb' => (float)$row->size_mb
        ];
    }

    // Get current PHP memory limit and usage
    $memUsage = memory_get_usage(true) / 1024 / 1024;
    $memPeak = memory_get_peak_usage(true) / 1024 / 1024;
    $limit = ini_get('memory_limit');

    // System load
    $load = function_exists('sys_getloadavg') ? sys_getloadavg() : 'N/A';

    echo json_encode([
        'status' => 'success',
        'php' => [
            'version' => PHP_VERSION,
            'memory_limit' => $limit,
            'memory_usage_mb' => round($memUsage, 2),
            'memory_peak_usage_mb' => round($memPeak, 2),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status(false)['opcache_enabled'] ? 'Yes' : 'No',
        ],
        'system' => [
            'load_average' => $load,
        ],
        'database' => [
            'name' => $dbName,
            'tables' => $tables,
        ]
    ], JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}

@unlink(__FILE__);
