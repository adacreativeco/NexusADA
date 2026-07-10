<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('category');
            $table->foreignId('parent_document_id')->nullable()->after('version')->constrained('documents')->nullOnDelete();
            $table->boolean('is_current')->default(true)->after('parent_document_id');
            $table->string('approval_status')->nullable()->after('is_current'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['parent_document_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['version', 'parent_document_id', 'is_current', 'approval_status', 'approved_by', 'approved_at']);
        });
    }
};
