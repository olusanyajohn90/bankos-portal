<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_account_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('account_id');
            $table->string('nickname', 60)->nullable();
            $table->string('color_hex', 7)->default('#2563eb');
            $table->boolean('is_frozen')->default(false);
            $table->string('frozen_reason', 200)->nullable();
            $table->timestamp('frozen_at')->nullable();
            $table->boolean('hide_balance')->default(false);
            $table->timestamps();
            $table->unique(['customer_id', 'account_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('customer_account_settings'); }
};
