<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_credit_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->integer('score'); // 300 - 850
            $table->string('grade', 10); // Excellent, Good, Fair, Poor
            $table->integer('payment_history_score');   // 35% weight
            $table->integer('utilization_score');       // 30% weight
            $table->integer('account_age_score');       // 15% weight
            $table->integer('account_mix_score');       // 10% weight
            $table->integer('activity_score');          // 10% weight
            $table->json('factors')->nullable();         // tips / factors array
            $table->timestamps();
            $table->index(['customer_id', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_credit_scores'); }
};
