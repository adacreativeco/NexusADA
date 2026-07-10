<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('projects', 'start_date')) {
                $table->date('start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('projects', 'due_date')) {
                $table->date('due_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('projects', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
        });

        // Data migration: Set title from client_name if title is empty
        \DB::table('projects')->whereNull('title')->update([
            'title' => \DB::raw('client_name')
        ]);
        
        // Data migration: Set description from summary if description is empty
        \DB::table('projects')->whereNull('description')->update([
            'description' => \DB::raw('summary')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'start_date', 'due_date']);
            // tenant_id usually we don't drop in down if it's critical
        });
    }
};
