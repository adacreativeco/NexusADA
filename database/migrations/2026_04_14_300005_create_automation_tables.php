<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('trigger_model'); // Task, Project, Client
            $table->string('trigger_event'); // created, updated, status_changed, deadline_passed
            $table->string('condition_field')->nullable();
            $table->string('condition_operator')->nullable(); // equals, not_equals, contains, greater_than
            $table->string('condition_value')->nullable();
            $table->string('action_type'); // notify, change_status, send_email, send_webhook, assign_user
            $table->json('action_config')->nullable();
            $table->integer('execution_count')->default(0);
            $table->datetime('last_executed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->string('trigger_model_type');
            $table->unsignedBigInteger('trigger_model_id');
            $table->string('action_type');
            $table->string('action_result'); // success, failed
            $table->text('error_message')->nullable();
            $table->datetime('executed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_rules');
    }
};
