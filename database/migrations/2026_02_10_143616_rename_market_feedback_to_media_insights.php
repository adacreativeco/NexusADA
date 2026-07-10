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
        Schema::rename('market_feedback', 'media_insights');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('media_insights', 'market_feedback');
    }
};
