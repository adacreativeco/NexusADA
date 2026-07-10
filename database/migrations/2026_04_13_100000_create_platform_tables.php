<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Plans ────────────────────────────────────────────────────
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // "Starter", "Pro", "Enterprise"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->string('currency', 3)->default('TRY');

            // Limitler
            $table->integer('max_users')->default(5);
            $table->integer('max_projects')->default(50);
            $table->integer('max_storage_mb')->default(1024);
            $table->integer('max_campaigns')->nullable();    // NULL = sınırsız

            // Özellik Bayrakları
            $table->json('features')->nullable();            // {"kanban":true, "social_media":true, ...}
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Tenants ──────────────────────────────────────────────────
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // "Ada Creative Co."
            $table->string('slug', 100)->unique();           // "ada-creative"
            $table->string('domain')->nullable();            // "panel.adacreative.co"
            $table->string('logo_url', 500)->nullable();
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('industry', 100)->nullable();

            // Abonelik
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('trial');      // trial, active, past_due, suspended, cancelled
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamp('last_payment_at')->nullable();

            // Limitler (plan'dan override)
            $table->integer('max_users')->default(5);
            $table->integer('max_projects')->default(50);
            $table->integer('max_storage_mb')->default(1024);

            // Meta
            $table->json('settings')->nullable();
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamps();
        });

        // ── Invoices ─────────────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('status')->default('pending');    // pending, paid, failed, refunded
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Platform Announcements ───────────────────────────────────
        Schema::create('platform_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('info');         // info, warning, critical, feature
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // ── Users → tenant_id ────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::dropIfExists('platform_announcements');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('plans');
    }
};
