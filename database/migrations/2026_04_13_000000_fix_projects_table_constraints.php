<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Make client_name nullable since we use client_id now
            $table->string('client_name')->nullable()->change();
            
            // Add client_id if it doesn't exist (safety check)
            if (!Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('client_name')->nullable(false)->change();
            if (Schema::hasColumn('projects', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            }
        });
    }
};
