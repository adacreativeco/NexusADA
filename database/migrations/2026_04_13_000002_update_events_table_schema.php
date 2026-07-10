<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->renameColumn('date', 'start_date');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('location')->nullable()->after('title');
            $table->text('description')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['end_date', 'location', 'description']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->renameColumn('start_date', 'date');
        });
    }
};
