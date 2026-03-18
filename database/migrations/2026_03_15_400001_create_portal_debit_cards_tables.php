<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_debit_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_id');
            $table->string('tenant_id');
            $table->string('account_id');
            $table->string('card_last4', 4)->nullable();
            $table->enum('card_scheme', ['verve', 'visa', 'mastercard'])->default('verve');
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_at')->nullable();
            $table->string('blocked_reason', 300)->nullable();
            $table->date('expires_at')->nullable();
            $table->enum('status', ['active', 'blocked', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['account_id']);
        });

        Schema::create('portal_debit_card_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_id');
            $table->string('tenant_id');
            $table->string('account_id');
            $table->enum('request_type', ['new', 'replacement', 'lost_stolen'])->default('new');
            $table->string('reason', 500)->nullable();
            $table->text('delivery_address')->nullable();
            $table->enum('collection_method', ['branch_pickup', 'home_delivery'])->default('branch_pickup');
            $table->string('branch_name', 200)->nullable();
            $table->enum('status', ['pending', 'processing', 'dispatched', 'delivered', 'cancelled'])->default('pending');
            $table->string('reference', 30);
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_debit_card_requests');
        Schema::dropIfExists('portal_debit_cards');
    }
};
