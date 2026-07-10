<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('press_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('media_house')->nullable();
            $table->string('category')->nullable(); // Journalist, Influencer, Agency
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('press_contacts');
    }
};
