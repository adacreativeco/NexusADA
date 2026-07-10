<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. workflows
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('steps'); // List of steps
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. work_workflows (Active workflows running on Works)
        Schema::create('work_workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('work_id');
            $table->unsignedBigInteger('workflow_id');
            $table->integer('current_step_index')->default(0);
            $table->string('status', 50)->default('running'); // running, completed, cancelled
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_workflows');
        Schema::dropIfExists('workflows');
    }
};
