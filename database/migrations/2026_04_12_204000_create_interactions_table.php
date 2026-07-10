<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'demo', 'proposal']);
            $table->string('subject');
            $table->text('content')->nullable();
            $table->datetime('interaction_date');
            $table->integer('duration_minutes')->nullable();
            $table->enum('outcome', ['positive', 'neutral', 'negative', 'pending'])->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
