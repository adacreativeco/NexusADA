<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->integer('expected_users')->nullable();
            $table->string('plan_interest')->default('pro'); // pro, enterprise
            $table->text('use_case')->nullable();
            $table->string('status')->default('new');        // new, contacted, converted, rejected
            $table->text('notes')->nullable();               // admin notes
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
