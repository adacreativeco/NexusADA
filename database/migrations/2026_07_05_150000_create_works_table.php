<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('lead'); // lead, proposal, approved, contract, started, in_progress, testing, delivery, support, completed, cancelled
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency', 3)->default('TRY');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();

            // Add foreign key constraint to tenant
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            // Add foreign key constraints to users
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });

        // Add work_id column to tasks table
        if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'work_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('work_id')->nullable()->constrained('works')->onDelete('set null');
            });
        }

        // Add work_id column to events table
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'work_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('work_id')->nullable()->constrained('works')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'work_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['work_id']);
                $table->dropColumn('work_id');
            });
        }

        if (Schema::hasColumn('tasks', 'work_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['work_id']);
                $table->dropColumn('work_id');
            });
        }

        Schema::dropIfExists('works');
    }
};
