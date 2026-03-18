<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_bulk_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->uuid('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->uuid('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->string('reference', 30)->unique();
            $table->string('label', 120)->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('recipient_count')->default(0);
            $table->integer('processed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['draft', 'pending', 'processing', 'completed', 'partial', 'failed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('portal_bulk_transfer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bulk_transfer_id');
            $table->foreign('bulk_transfer_id')->references('id')->on('portal_bulk_transfers')->onDelete('cascade');
            $table->integer('row_number');
            $table->string('beneficiary_name', 120);
            $table->string('account_number', 20);
            $table->string('bank_code', 10)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('narration', 255)->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('reference', 40)->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_bulk_transfer_items');
        Schema::dropIfExists('portal_bulk_transfers');
    }
};
