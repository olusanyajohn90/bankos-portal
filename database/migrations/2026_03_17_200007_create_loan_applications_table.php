<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->uuid('account_id')->nullable(); // disbursement account
            $table->string('reference', 25)->unique();
            $table->string('loan_type', 60)->default('personal'); // personal, business, emergency, mortgage, auto
            $table->decimal('requested_amount', 15, 2);
            $table->integer('requested_tenor_months');
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('employment_status', 40)->nullable(); // employed, self_employed, business_owner, retired, student
            $table->string('employer_name', 100)->nullable();
            $table->text('purpose');
            $table->text('collateral_description')->nullable();
            $table->decimal('collateral_value', 15, 2)->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'disbursed', 'cancelled'])->default('submitted');
            $table->text('officer_notes')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->uuid('resulting_loan_id')->nullable(); // link to actual loan if approved
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('loan_applications'); }
};
