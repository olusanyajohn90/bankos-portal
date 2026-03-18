<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('standing_orders')) return;
        Schema::create('standing_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->string('nickname', 100);
            $table->string('beneficiary_account_number', 20);
            $table->string('beneficiary_account_name', 200);
            $table->boolean('is_intrabank')->default(true);
            $table->string('beneficiary_bank_name', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->tinyInteger('day_of_week')->nullable(); // 0=Mon ... 6=Sun
            $table->tinyInteger('day_of_month')->nullable(); // 1-28
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_at')->nullable();
            $table->date('last_run_at')->nullable();
            $table->unsignedInteger('run_count')->default(0);
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index('next_run_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standing_orders');
    }
};
