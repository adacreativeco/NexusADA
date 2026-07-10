<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$domains = [
    'google' => 'https://www.google.com',
    'httpbin' => 'https://httpbin.org/delay/1',
    'github' => 'https://api.github.com',
    'nvidia' => 'https://integrate.api.nvidia.com/v1/chat/completions'
];

$results = [];
foreach ($domains as $name => $url) {
    try {
        $start = microtime(true);
        $response = Illuminate\Support\Facades\Http::timeout(5)->get($url);
        $results[$name] = [
            'status' => $response->status(),
            'time' => round((microtime(true) - $start) * 1000, 2) . 'ms'
        ];
    } catch (\Exception $e) {
        $results[$name] = [
            'error' => $e->getMessage()
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
