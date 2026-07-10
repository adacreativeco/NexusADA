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
        Schema::create('incoming_emails', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->string('from_address');
            $table->string('from_name')->nullable();
            $table->longText('body');
            $table->string('status')->default('unread'); // unread, read, replied
            $table->string('folder')->default('inbox');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_emails');
    }
};
