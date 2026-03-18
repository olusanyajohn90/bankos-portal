<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->string('category', 50); // airtime, data, electricity, tv, water
            $table->string('biller', 100);  // MTN, AEDC, DSTV...
            $table->string('recipient', 100); // phone, meter, smartcard
            $table->decimal('amount', 10, 2);
            $table->string('reference', 100)->unique();
            $table->string('external_reference', 100)->nullable();
            $table->string('token', 300)->nullable(); // electricity token
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['customer_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
