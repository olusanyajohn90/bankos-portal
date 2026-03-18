<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_savings_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->uuid('creator_id');
            $table->foreign('creator_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->decimal('contribution_amount', 15, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->tinyInteger('max_members')->default(10);
            $table->tinyInteger('current_cycle')->default(1);
            $table->tinyInteger('total_cycles');
            $table->enum('status', ['forming', 'active', 'completed', 'cancelled'])->default('forming');
            $table->date('next_collection_date')->nullable();
            $table->json('payout_order')->nullable();
            $table->timestamps();
        });

        Schema::create('portal_savings_group_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('group_id');
            $table->foreign('group_id')->references('id')->on('portal_savings_groups')->onDelete('cascade');
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->uuid('tenant_id');
            $table->tinyInteger('payout_position')->nullable();
            $table->uuid('account_id')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->enum('status', ['active', 'defaulted', 'withdrawn'])->default('active');
            $table->timestamps();
            $table->unique(['group_id', 'customer_id']);
        });

        Schema::create('portal_savings_group_contributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('group_id');
            $table->foreign('group_id')->references('id')->on('portal_savings_groups')->onDelete('cascade');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('portal_savings_group_members')->onDelete('cascade');
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->uuid('account_id');
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('cycle_number');
            $table->string('reference', 40)->unique();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_savings_group_contributions');
        Schema::dropIfExists('portal_savings_group_members');
        Schema::dropIfExists('portal_savings_groups');
    }
};
