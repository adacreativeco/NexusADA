<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('source_type', 100);
            $table->unsignedBigInteger('source_id');
            $table->string('target_type', 100);
            $table->unsignedBigInteger('target_id');
            $table->string('relation_type', 100);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Unique constraint index with bounded column lengths
            $table->unique(
                ['tenant_id', 'source_type', 'source_id', 'target_type', 'target_id', 'relation_type'],
                'idx_entity_rels_unique'
            );
        });

        // Add MySQL FULLTEXT indexes for semantic/fulltext universal search
        try {
            DB::statement('ALTER TABLE clients ADD FULLTEXT fulltext_client_name (name)');
            DB::statement('ALTER TABLE works ADD FULLTEXT fulltext_work_title (title)');
            DB::statement('ALTER TABLE proposals ADD FULLTEXT fulltext_proposal_title (title)');
            DB::statement('ALTER TABLE tasks ADD FULLTEXT fulltext_task_title_desc (title, description)');
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE tasks DROP INDEX fulltext_task_title_desc');
            DB::statement('ALTER TABLE proposals DROP INDEX fulltext_proposal_title');
            DB::statement('ALTER TABLE works DROP INDEX fulltext_work_title');
            DB::statement('ALTER TABLE clients DROP INDEX fulltext_client_name');
        } catch (\Exception $e) {
            report($e);
        }

        Schema::dropIfExists('entity_relations');
    }
};
