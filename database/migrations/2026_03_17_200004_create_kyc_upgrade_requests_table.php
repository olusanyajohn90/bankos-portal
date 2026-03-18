<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kyc_upgrade_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->enum('current_tier', ['level_1', 'level_2', 'level_3'])->default('level_1');
            $table->enum('target_tier', ['level_2', 'level_3']);
            $table->string('bvn', 11)->nullable();
            $table->string('nin', 11)->nullable();
            $table->enum('id_type', ['national_id', 'drivers_license', 'voters_card', 'international_passport', 'nin_slip'])->nullable();
            $table->string('id_number', 50)->nullable();
            $table->string('id_document_path')->nullable();  // stored file path
            $table->string('selfie_path')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->text('reviewer_notes')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('kyc_upgrade_requests'); }
};
