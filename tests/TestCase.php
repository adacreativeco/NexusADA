<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();
        
        $conn = config('database.default', 'sqlite');
        if ($conn === 'sqlite') {
            return;
        }
        
        $db = (string) config("database.connections.{$conn}.database");
        
        if (!str_contains($db, 'test') && $db !== ':memory:') {
            throw new \RuntimeException("GÜVENLİK: Test DB ismi 'test' içermiyor. DB: {$db}");
        }

        if (str_contains($db, 'nexusada') && !str_contains($db, 'test')) {
            throw new \RuntimeException("GÜVENLİK: Production DB algılandı! DB: {$db}");
        }
    }
}
