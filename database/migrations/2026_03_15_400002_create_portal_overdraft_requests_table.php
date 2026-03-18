<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_overdraft_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_id');
            $table->string('tenant_id');
            $table->string('account_id');
            $table->decimal('requested_limit', 15, 2);
            $table->string('purpose', 500)->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('employer', 200)->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('approved_limit', 15, 2)->nullable();
            $table->text('review_note')->nullable();
            $table->string('reference', 30);
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_overdraft_requests');
    }
};
