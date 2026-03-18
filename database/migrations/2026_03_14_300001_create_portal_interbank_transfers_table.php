<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_interbank_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->string('beneficiary_name', 120);
            $table->string('beneficiary_account', 20);
            $table->string('beneficiary_bank', 100);
            $table->string('bank_code', 10);
            $table->decimal('amount', 15, 2);
            $table->string('narration', 255)->nullable();
            $table->string('reference', 40)->unique();
            $table->string('session_id', 40)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'reversed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->text('nip_response')->nullable();
            $table->timestamp('pin_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_interbank_transfers');
    }
};
