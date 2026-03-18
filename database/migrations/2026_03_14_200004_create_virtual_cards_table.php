<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('virtual_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->string('card_last4', 4);
            $table->string('card_number_masked', 19); // **** **** **** 1234
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 4);
            $table->string('card_name', 100);
            $table->string('card_type', 20)->default('visa'); // visa/mastercard
            $table->enum('status', ['active', 'frozen', 'cancelled'])->default('active');
            $table->decimal('spending_limit', 15, 2)->nullable();
            $table->decimal('spent_this_month', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_cards');
    }
};
