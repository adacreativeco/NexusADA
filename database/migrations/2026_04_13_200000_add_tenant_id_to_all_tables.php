<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tenant-scoped olması gereken tüm tablolar.
     * Her tabloya nullable tenant_id FK eklenir.
     * Nullable çünkü: webmaster tarafından oluşturulan kayıtlar tenant'a ait olmayabilir.
     */
    protected array $tables = [
        'clients',
        'projects',
        'campaigns',
        'content_items',
        'events',
        'media_insights',
        'brand_assets',
        'departments',
        'press_contacts',
        'incoming_emails',
        'tools',
        'tasks',
        'social_posts',
        'email_templates',
        'notes',
        'documents',
        'interactions',
        'app_notifications',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('tenant_id')->nullable()->after('id');
                    $t->index('tenant_id');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['tenant_id']);
                    $t->dropColumn('tenant_id');
                });
            }
        }
    }
};
