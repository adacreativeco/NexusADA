<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('title');
            $table->string('proposal_number', 50)->unique();
            $table->string('status')->default('draft'); // draft, pending_approval, approved_internal, rejected_internal, sent, viewed, accepted, declined
            
            $table->json('items')->nullable(); // [{"description": "web design", "quantity": 1, "unit_price": 5000, "vat_rate": 20, "total": 6000}]
            
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            
            $table->string('currency', 3)->default('TRY');
            $table->date('valid_until')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
