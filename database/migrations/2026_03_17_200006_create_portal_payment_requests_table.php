<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_payment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');   // who is requesting payment
            $table->uuid('tenant_id');
            $table->uuid('account_id');    // account to receive payment
            $table->string('reference', 20)->unique();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('description', 200)->nullable();
            $table->string('recipient_name', 100)->nullable();   // who to send request to
            $table->string('recipient_email', 100)->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->uuid('paid_by_account_id')->nullable();
            $table->string('paid_by_name', 100)->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_payment_requests'); }
};
