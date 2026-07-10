<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migrate data from SQLite to MySQL.
 *
 * Usage:
 *   1. Set up .env with MySQL credentials (DB_CONNECTION=mysql)
 *   2. Run: php artisan migrate (creates tables in MySQL)
 *   3. Run: php artisan db:migrate-to-mysql --sqlite-path=/path/to/database.sqlite
 *
 * The command reads from the SQLite file and writes to the configured MySQL connection.
 */
class MigrateSqliteToMysql extends Command
{
    protected $signature = 'db:migrate-to-mysql
                            {--sqlite-path= : Path to the SQLite database file}
                            {--chunk=500 : Number of records to insert per batch}
                            {--dry-run : Show what would be migrated without actually doing it}';

    protected $description = 'Migrate all data from SQLite database to MySQL (configured connection)';

    /**
     * Table order respecting foreign key dependencies.
     * Tables without FK dependencies come first.
     */
    private array $tableOrder = [
        // 1. Independent tables (no FK)
        'plans',
        'departments',
        'sectors',
        'products',
        'use_cases',

        // 2. Tables with single FK dependencies
        'tenants',              // FK: plan_id
        'users',                // FK: tenant_id
        'clients',              // FK: tenant_id
        'campaigns',            // FK: department_id

        // 3. Tables depending on users/tenants
        'projects',             // FK: client_id, tenant_id
        'tasks',                // FK: project_id, assigned_to
        'events',               // FK: tenant_id
        'social_posts',         // FK: campaign_id
        'content_items',        // FK: campaign_id, department_id
        'brand_assets',
        'media_items',
        'incoming_emails',

        // 4. Pivot / junction tables
        'content_item_product',
        'content_item_sector',
        'content_item_use_case',

        // 5. Activity / log tables
        'comments',
        'documents',
        'time_entries',
        'app_notifications',
        'automation_rules',
        'automation_logs',
        'integration_settings',
        'platform_announcements',
        'invoices',
        'audits',

        // 6. Auth / system tables
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',

        // 7. Spatie permission tables
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
    ];

    public function handle(): int
    {
        $sqlitePath = $this->option('sqlite-path')
            ?? database_path('database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error("SQLite file not found: {$sqlitePath}");
            return self::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        // Configure temporary SQLite connection
        config(['database.connections.sqlite_source' => [
            'driver'   => 'sqlite',
            'database' => $sqlitePath,
            'prefix'   => '',
            'foreign_key_constraints' => false,
        ]]);

        $mysqlConnection = DB::connection();
        $sqliteConnection = DB::connection('sqlite_source');

        $driverName = $mysqlConnection->getDriverName();
        if (!in_array($driverName, ['mysql', 'mariadb'])) {
            $this->error("Default connection is '{$driverName}', expected 'mysql' or 'mariadb'. Check .env DB_CONNECTION.");
            return self::FAILURE;
        }

        $this->info("Source: {$sqlitePath}");
        $this->info("Target: {$driverName} ({$mysqlConnection->getDatabaseName()})");
        $this->newLine();

        if ($isDryRun) {
            $this->warn('DRY RUN — no data will be written.');
            $this->newLine();
        }

        // Get all tables from SQLite
        $sqliteTables = collect($sqliteConnection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations'"))
            ->pluck('name')
            ->toArray();

        // Build ordered list: known order first, then any remaining tables
        $orderedTables = [];
        foreach ($this->tableOrder as $table) {
            if (in_array($table, $sqliteTables)) {
                $orderedTables[] = $table;
            }
        }
        // Add any tables not in our predefined order
        foreach ($sqliteTables as $table) {
            if (!in_array($table, $orderedTables)) {
                $orderedTables[] = $table;
            }
        }

        // Disable FK checks during import
        if (!$isDryRun) {
            $mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=0');
        }

        $totalMigrated = 0;

        foreach ($orderedTables as $table) {
            // Check if table exists in MySQL
            if (!Schema::connection($mysqlConnection->getName())->hasTable($table)) {
                $this->warn("  ⏭ {$table} — not in MySQL schema, skipping");
                continue;
            }

            $count = $sqliteConnection->table($table)->count();

            if ($count === 0) {
                $this->line("  ○ {$table} — empty, skipping");
                continue;
            }

            if ($isDryRun) {
                $this->info("  ✓ {$table} — {$count} rows would be migrated");
                $totalMigrated += $count;
                continue;
            }

            // Truncate target table first
            $mysqlConnection->table($table)->truncate();

            // Chunk read from SQLite, bulk insert to MySQL
            $migrated = 0;
            $sqliteConnection->table($table)->orderBy(
                Schema::connection('sqlite_source')->hasColumn($table, 'id') ? 'id' : $sqliteConnection->raw('rowid')
            )->chunk($chunkSize, function ($rows) use ($mysqlConnection, $table, &$migrated) {
                $data = collect($rows)->map(fn($row) => (array) $row)->toArray();
                $mysqlConnection->table($table)->insert($data);
                $migrated += count($data);
            });

            $this->info("  ✓ {$table} — {$migrated} rows migrated");
            $totalMigrated += $migrated;
        }

        // Re-enable FK checks
        if (!$isDryRun) {
            $mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->newLine();
        $this->info("Migration complete. Total rows: {$totalMigrated}");

        if (!$isDryRun) {
            $this->info('Verifying FK integrity...');
            // Quick integrity check on critical relationships
            $orphanUsers = $mysqlConnection->table('users')
                ->whereNotNull('tenant_id')
                ->whereNotIn('tenant_id', fn($q) => $q->select('id')->from('tenants'))
                ->count();

            if ($orphanUsers > 0) {
                $this->warn("  ⚠ {$orphanUsers} users with orphaned tenant_id found!");
            } else {
                $this->info('  ✓ tenant_id integrity OK');
            }
        }

        return self::SUCCESS;
    }
}
