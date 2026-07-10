<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $db = config('database.connections.mysql.database');
        
        if (!str_contains($db, 'test')) {
            throw new \RuntimeException("GÜVENLİK: Test DB ismi 'test' içermiyor. DB: {$db}");
        }

        if (str_contains($db, 'nexusada') && !str_contains($db, 'test')) {
            throw new \RuntimeException("GÜVENLİK: Production DB algılandı! DB: {$db}");
        }
    }
}
