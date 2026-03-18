<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('notification_preferences')) return;
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->unique();
            $table->uuid('tenant_id');
            $table->boolean('debit_alert')->default(true);
            $table->boolean('credit_alert')->default(true);
            $table->boolean('low_balance_alert')->default(true);
            $table->decimal('low_balance_threshold', 10, 2)->default(1000);
            $table->boolean('large_txn_alert')->default(true);
            $table->decimal('large_txn_threshold', 10, 2)->default(50000);
            $table->boolean('loan_reminder')->default(true);
            $table->boolean('login_alert')->default(true);
            $table->boolean('monthly_summary')->default(true);
            $table->boolean('statement_ready')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
