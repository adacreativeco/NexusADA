<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // slack, discord, generic_webhook
            $table->string('webhook_url');
            $table->boolean('is_active')->default(true);
            $table->json('events')->nullable(); // ["task_created", "task_completed", ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
    }
};
