<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->json('mentions')->nullable()->after('content');
            $table->foreignId('parent_id')->nullable()->after('mentions')
                  ->constrained('notes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['mentions', 'parent_id']);
        });
    }
};
