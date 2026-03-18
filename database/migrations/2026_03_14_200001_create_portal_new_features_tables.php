<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('portal_airtime_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['airtime','data']);
            $table->string('phone',20);
            $table->string('network',20);
            $table->string('data_plan',80)->nullable();
            $table->decimal('amount',15,2);
            $table->string('reference',30)->unique();
            $table->enum('status',['pending','completed','failed'])->default('completed');
            $table->timestamps();
        });
        Schema::create('portal_scheduled_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->string('beneficiary_name',120);
            $table->string('beneficiary_account',20);
            $table->string('bank_code',10);
            $table->string('bank_name',100)->nullable();
            $table->decimal('amount',15,2);
            $table->string('narration',255)->nullable();
            $table->timestamp('scheduled_at');
            $table->string('reference',30)->unique()->nullable();
            $table->enum('status',['pending','processed','failed','cancelled'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('portal_savings_challenges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('pocket_id')->nullable()->constrained('savings_pockets')->nullOnDelete();
            $table->string('name',120);
            $table->string('emoji',10)->default('🏆');
            $table->decimal('target_amount',15,2);
            $table->decimal('amount_per_save',15,2);
            $table->enum('frequency',['daily','weekly','monthly']);
            $table->decimal('current_amount',15,2)->default(0);
            $table->integer('streak_count')->default(0);
            $table->integer('total_saves')->default(0);
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->enum('status',['active','completed','paused','abandoned'])->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('portal_cheque_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
            $table->string('reference',30)->unique();
            $table->enum('book_type',['25_leaves','50_leaves','100_leaves'])->default('50_leaves');
            $table->enum('collection_method',['branch_pickup','home_delivery'])->default('branch_pickup');
            $table->text('delivery_address')->nullable();
            $table->string('branch_name',120)->nullable();
            $table->enum('status',['pending','processing','ready','collected','cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();
        });
        Schema::create('portal_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint',500);
            $table->text('p256dh')->nullable();
            $table->text('auth')->nullable();
            $table->string('user_agent',255)->nullable();
            $table->timestamps();
        });
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers','dark_mode')) {
                $table->boolean('dark_mode')->default(false)->after('portal_frozen');
            }
        });
    }
    public function down(): void {
        Schema::dropIfExists('portal_airtime_orders');
        Schema::dropIfExists('portal_scheduled_transfers');
        Schema::dropIfExists('portal_savings_challenges');
        Schema::dropIfExists('portal_cheque_requests');
        Schema::dropIfExists('portal_push_subscriptions');
        Schema::table('customers', function (Blueprint $table) { $table->dropColumn('dark_mode'); });
    }
};
