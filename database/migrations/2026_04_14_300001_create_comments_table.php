<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('content');
            $table->json('mentions')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_type', 'commentable_id']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
