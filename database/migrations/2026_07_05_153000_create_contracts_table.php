<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('title');
            $table->string('contract_number', 50)->unique();
            $table->string('status')->default('draft'); // draft, pending_approval, approved_internal, rejected_internal, active, expired, terminated
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            $table->decimal('value', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('TRY');
            
            $table->text('terms')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->integer('reminder_days')->default(30);
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
