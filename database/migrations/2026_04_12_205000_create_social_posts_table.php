<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('platform', ['instagram', 'facebook', 'linkedin', 'twitter', 'youtube', 'tiktok']);
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->datetime('scheduled_at');
            $table->enum('status', ['draft', 'scheduled', 'published', 'cancelled'])->default('draft');
            $table->string('published_url')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
