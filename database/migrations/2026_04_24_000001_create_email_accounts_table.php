<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Email Accounts (IMAP credentials) ──────────────────
        if (!Schema::hasTable('email_accounts')) {
            Schema::create('email_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('email');
                $table->string('imap_host');                    // imap.gmail.com, outlook.office365.com
                $table->integer('imap_port')->default(993);
                $table->string('imap_encryption')->default('ssl'); // ssl, tls, none
                $table->string('username');                     // usually same as email
                $table->text('password');                       // stored encrypted via cast
                $table->string('folder')->default('INBOX');
                $table->timestamp('last_sync_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sync_interval_minutes')->default(5);
                $table->text('last_error')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        // ── Enhance incoming_emails table ──────────────────────
        if (Schema::hasTable('incoming_emails')) {
            Schema::table('incoming_emails', function (Blueprint $table) {
                if (!Schema::hasColumn('incoming_emails', 'tenant_id')) {
                    $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
                }
                if (!Schema::hasColumn('incoming_emails', 'email_account_id')) {
                    $table->foreignId('email_account_id')->nullable()->constrained()->cascadeOnDelete();
                }
                if (!Schema::hasColumn('incoming_emails', 'message_id')) {
                    $table->string('message_id')->nullable();
                }
                if (!Schema::hasColumn('incoming_emails', 'uid')) {
                    $table->unsignedInteger('uid')->nullable();
                }
                if (!Schema::hasColumn('incoming_emails', 'to')) {
                    $table->string('to')->nullable();
                }
                if (!Schema::hasColumn('incoming_emails', 'cc')) {
                    $table->string('cc')->nullable();
                }
                if (!Schema::hasColumn('incoming_emails', 'body_html')) {
                    $table->longText('body_html')->nullable();
                }
                if (!Schema::hasColumn('incoming_emails', 'has_attachments')) {
                    $table->boolean('has_attachments')->default(false);
                }
            });

            // Unique constraint: prevent duplicate imports (may already exist)
            try {
                Schema::table('incoming_emails', function (Blueprint $table) {
                    $table->unique(['email_account_id', 'message_id'], 'unique_email_per_account');
                });
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        }
    }

    public function down(): void
    {
        Schema::table('incoming_emails', function (Blueprint $table) {
            $table->dropUnique('unique_email_per_account');
            $table->dropIndex(['tenant_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['email_account_id']);
            $table->dropColumn([
                'tenant_id', 'email_account_id', 'message_id', 'uid',
                'to', 'cc', 'body_html', 'has_attachments',
            ]);
        });

        Schema::dropIfExists('email_accounts');
    }
};
