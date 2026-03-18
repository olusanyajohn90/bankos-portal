<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('savings_pockets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->string('name', 100);
            $table->string('emoji', 10)->default('💰');
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('type', ['manual', 'round_up', 'scheduled'])->default('manual');
            $table->json('auto_rule')->nullable(); // {frequency, day_of_week, day_of_month, amount, percent}
            $table->date('locked_until')->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'locked', 'completed', 'broken'])->default('active');
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_pockets');
    }
};
