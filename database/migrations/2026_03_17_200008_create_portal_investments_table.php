<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_investments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->uuid('account_id');  // funding account
            $table->string('reference', 25)->unique();
            $table->string('name', 100);  // user-given name e.g. "Rainy Day Fund"
            $table->decimal('principal', 15, 2);
            $table->decimal('interest_rate', 5, 2); // annual rate %
            $table->integer('duration_days'); // 30, 60, 90, 180, 365
            $table->decimal('expected_interest', 15, 2);
            $table->decimal('maturity_amount', 15, 2);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->enum('status', ['active', 'matured', 'broken', 'pending'])->default('active');
            $table->timestamp('matured_at')->nullable();
            $table->timestamp('broken_at')->nullable();
            $table->decimal('penalty_amount', 15, 2)->default(0); // early liquidation penalty
            $table->timestamps();
            $table->index(['customer_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_investments'); }
};
