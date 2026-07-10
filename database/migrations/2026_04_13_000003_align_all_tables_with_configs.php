<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // press_contacts: Config expects 'outlet', 'beat', 'title' but table has 'media_house'
        Schema::table('press_contacts', function (Blueprint $table) {
            $table->renameColumn('media_house', 'outlet');
        });
        Schema::table('press_contacts', function (Blueprint $table) {
            $table->string('beat')->nullable()->after('outlet');
            $table->string('title')->nullable()->after('name');
        });

        // incoming_emails: Config expects 'from' but table has 'from_address'
        Schema::table('incoming_emails', function (Blueprint $table) {
            $table->renameColumn('from_address', 'from');
        });

        // tools: Config expects 'cost' column
        Schema::table('tools', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn('cost');
        });

        Schema::table('incoming_emails', function (Blueprint $table) {
            $table->renameColumn('from', 'from_address');
        });

        Schema::table('press_contacts', function (Blueprint $table) {
            $table->dropColumn(['beat', 'title']);
        });
        Schema::table('press_contacts', function (Blueprint $table) {
            $table->renameColumn('outlet', 'media_house');
        });
    }
};
