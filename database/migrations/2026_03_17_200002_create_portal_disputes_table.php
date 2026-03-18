<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->uuid('account_id')->nullable();
            $table->uuid('transaction_id')->nullable();
            $table->string('reference', 30)->unique();
            $table->enum('type', ['unauthorized_transaction', 'wrong_amount', 'double_charge', 'failed_transfer', 'atm_dispute', 'card_fraud', 'other'])->default('other');
            $table->text('description');
            $table->decimal('disputed_amount', 15, 2)->nullable();
            $table->enum('status', ['open', 'investigating', 'resolved', 'rejected', 'escalated'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_disputes'); }
};
