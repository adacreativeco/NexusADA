<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ai_usage_logs
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('model');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 2. ai_memories
        Schema::create('ai_memories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('category', 50); // style, preference, rule, fact, context
            $table->text('content');
            $table->string('source', 50)->default('admin_set'); // user_input, ai_learned, admin_set
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_memories');
        Schema::dropIfExists('ai_usage_logs');
    }
};
