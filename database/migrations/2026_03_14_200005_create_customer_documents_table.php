<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->string('type', 60); // account_statement, confirmation_letter, reference_letter, loan_clearance
            $table->string('label', 150); // human-readable label
            $table->string('reference', 60)->unique();
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('period_from', 20)->nullable();
            $table->string('period_to', 20)->nullable();
            $table->enum('status', ['generated', 'pending', 'failed'])->default('generated');
            $table->timestamps();

            $table->index(['customer_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
