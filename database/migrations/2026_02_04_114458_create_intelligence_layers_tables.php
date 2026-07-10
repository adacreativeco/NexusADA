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
        // 1. CRM: İlişki Zekası Katmanı (Clients)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            
            // Stratejik Bağlam (Context)
            $table->string('decision_style')->nullable(); // Hızlı, Analitik, Konsensüs vb.
            $table->string('risk_level')->nullable(); // Düşük, Orta, Yüksek
            $table->string('presentation_language')->nullable(); // Teknik, Vizyoner, Finansal odaklı vb.
            $table->integer('strategic_importance')->default(3); // 1-5 scale
            $table->text('behavioral_notes')->nullable(); // "Revizyon sever", "Prestij odaklı" vb.
            
            $table->timestamps();
        });

        // 2. Değer ERP'si Katmanı (Projects Genişletme)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            
            // Değer Takibi (Value Tracking)
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('actual_revenue', 15, 2)->nullable();
            $table->integer('team_hours')->default(0); // Harcanan toplam iş gücü
            
            // Zeka Göstergeleri
            $table->integer('profitability_score')->nullable(); // 1-100
            $table->integer('sustainability_index')->nullable(); // 1-100
            
            $table->text('strategic_value_notes')->nullable();
        });

        // 3. Pazar Geri Bildirimi (Clients ile İlişkilendirme)
        Schema::table('market_feedback', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_feedback', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'budget', 'actual_revenue', 'team_hours', 'profitability_score', 'sustainability_index', 'strategic_value_notes']);
        });

        Schema::dropIfExists('clients');
    }
};
