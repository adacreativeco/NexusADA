<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bank Accounts
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('bank_name');
            $table->string('iban')->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('TRY');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 2. Incomes
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_id')->nullable()->constrained()->nullOnDelete();
            $table->string('income_number');
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->integer('tax_rate')->default(20);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('status')->default('draft'); // draft, sent, paid, cancelled, overdue
            $table->string('payment_method')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 3. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('vendor');
            $table->string('expense_number');
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->integer('tax_rate')->default(20);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('category');
            $table->boolean('is_recurring')->default(false);
            $table->string('status')->default('draft'); // draft, pending_approval, approved_internal, rejected_internal, paid
            $table->string('payment_method')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 4. Collections
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('income_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('payment_method')->nullable();
            $table->date('collected_at');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 5. Financial Instruments (Checks & Promissory Notes)
        Schema::create('financial_instruments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('type'); // check, promissory_note
            $table->string('direction'); // inbound, outbound
            $table->string('instrument_number');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TRY');
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending, paid, bounced, cancelled
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_instruments');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('bank_accounts');
    }
};
