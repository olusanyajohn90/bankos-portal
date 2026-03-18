<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->string('nickname', 100);
            $table->string('account_number', 20);
            $table->string('account_name', 200);
            $table->boolean('is_intrabank')->default(true);
            $table->string('bank_code', 10)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->unsignedInteger('transfer_count')->default(0);
            $table->timestamp('last_transfer_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
