<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // brand_assets: already recreated by failed migration (table exists with new schema, no data)
        // Just ensure table is correct
        if (!Schema::hasColumn('brand_assets', 'type')) {
            $data = DB::table('brand_assets')->get();
            Schema::dropIfExists('brand_assets');
            Schema::create('brand_assets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->nullable();
                $table->string('version')->nullable();
                $table->text('notes')->nullable();
                $table->json('files')->nullable();
                $table->timestamps();
            });
            foreach ($data as $row) {
                $arr = (array) $row;
                $newRow = [
                    'id' => $arr['id'] ?? null,
                    'name' => $arr['name'] ?? '',
                    'type' => $arr['category'] ?? $arr['type'] ?? null,
                    'version' => $arr['version'] ?? null,
                    'notes' => $arr['guidelines'] ?? $arr['notes'] ?? null,
                    'files' => $arr['files'] ?? null,
                    'created_at' => $arr['created_at'] ?? now(),
                    'updated_at' => $arr['updated_at'] ?? now(),
                ];
                DB::table('brand_assets')->insert($newRow);
            }
        }

        // content_items: rebuild with all columns nullable
        if (Schema::hasColumn('content_items', 'type') && !Schema::hasColumn('content_items', 'campaign_id')) {
            // Old schema — needs rebuild
            $ciData = DB::table('content_items')->get();
            Schema::dropIfExists('content_items');
            Schema::create('content_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('type')->nullable();
                $table->longText('body')->nullable();
                $table->string('tone')->nullable();
                $table->string('status')->default('draft');
                $table->integer('usage_count')->default(0);
                $table->string('purpose')->nullable();
                $table->string('platform')->nullable();
                $table->string('attachments')->nullable();
                $table->text('internal_notes')->nullable();
                $table->text('production_reason')->nullable();
                $table->boolean('used_in_sales')->default(false);
                $table->string('team_used')->nullable();
                $table->boolean('is_reusable')->default(true);
                $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
            foreach ($ciData as $row) {
                $arr = (array) $row;
                // Remove keys that don't exist in new schema
                unset($arr['campaign_id'], $arr['department_id'], $arr['last_used_at']);
                DB::table('content_items')->insert($arr);
            }
        }

        // campaigns: make title and department_id nullable
        $campData = DB::table('campaigns')->get();
        Schema::dropIfExists('campaigns');
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('goal')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->string('status')->default('planned');
            $table->timestamps();
        });
        foreach ($campData as $row) {
            $arr = (array) $row;
            DB::table('campaigns')->insert($arr);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No rollback
    }
};
