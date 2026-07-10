<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_user_id');
            $table->string('email_hash', 64);
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('deleted_at');

            $table->index('email_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_users');
    }
};
