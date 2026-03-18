<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_split_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_id');
            $table->string('tenant_id');
            $table->string('account_id');        // account to receive contributions
            $table->string('reference', 30)->unique();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('per_person_amount', 15, 2);
            $table->unsignedSmallInteger('participant_count');
            $table->decimal('collected_amount', 15, 2)->default(0);
            $table->unsignedSmallInteger('paid_count')->default(0);
            $table->enum('status', ['open', 'completed', 'cancelled'])->default('open');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'status']);
        });

        Schema::create('portal_split_bill_participants', function (Blueprint $table) {
            $table->id();
            $table->string('split_bill_id');
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'paid', 'skipped'])->default('pending');
            $table->string('reference', 30)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('split_bill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_split_bill_participants');
        Schema::dropIfExists('portal_split_bills');
    }
};
