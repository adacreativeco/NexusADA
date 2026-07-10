<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // tools: url and icon are NOT NULL but should be nullable
        $toolsData = DB::table('tools')->get();
        Schema::dropIfExists('tools');
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('category')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        foreach ($toolsData as $row) {
            DB::table('tools')->insert((array) $row);
        }

        // brand_assets: category is NOT NULL but should be nullable
        $brandData = DB::table('brand_assets')->get();
        Schema::dropIfExists('brand_assets');
        Schema::create('brand_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('guidelines')->nullable();
            $table->json('files')->nullable();
            $table->timestamps();
        });
        foreach ($brandData as $row) {
            DB::table('brand_assets')->insert((array) $row);
        }
    }

    public function down(): void
    {
        // No rollback needed — original constraints were too strict
    }
};
