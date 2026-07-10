<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->string('purpose')->nullable()->after('tone');
            $table->string('platform')->nullable()->after('purpose');
            $table->string('attachments')->nullable()->after('platform');
            $table->text('internal_notes')->nullable()->after('attachments');
            $table->text('production_reason')->nullable();
            $table->boolean('used_in_sales')->default(false);
            $table->string('team_used')->nullable();
            $table->boolean('is_reusable')->default(true);
        });

        Schema::table('market_feedback', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('id');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photos')->nullable();
            $table->boolean('is_reference_eligible')->default(false);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('photos')->nullable();
            $table->string('usage_area')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'platform', 'attachments', 'internal_notes', 'production_reason', 'used_in_sales', 'team_used', 'is_reusable']);
        });

        Schema::table('market_feedback', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['project_name', 'project_id', 'photos', 'is_reference_eligible']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['photos', 'usage_area']);
        });
    }
};
