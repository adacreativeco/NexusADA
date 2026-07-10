<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('type', 50); // logo, font, color, brandbook, template, prompt, file
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->json('metadata')->nullable(); // hex colors list, font family etc
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. knowledge_articles
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('type', 50); // sop, checklist, wiki, training, documentation
            $table->string('title');
            $table->longText('content'); // Markdown with checklist markup support
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('version')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('assets');
    }
};
