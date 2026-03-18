<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->string('category', 60); // food, transport, bills, entertainment, health, shopping, education, others
            $table->decimal('monthly_limit', 15, 2);
            $table->string('month', 7); // YYYY-MM
            $table->string('color_hex', 7)->default('#2563eb');
            $table->timestamps();
            $table->unique(['customer_id', 'category', 'month']);
            $table->index(['customer_id', 'month']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_budgets'); }
};
