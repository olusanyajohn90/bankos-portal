<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('referrer_customer_id');     // who shared the link
            $table->uuid('tenant_id');
            $table->string('referee_name', 100)->nullable();
            $table->string('referee_email', 150)->nullable();
            $table->string('referee_phone', 20)->nullable();
            $table->uuid('referee_customer_id')->nullable(); // filled when they register
            $table->string('referral_code', 12)->index();
            $table->enum('status', ['pending', 'registered', 'activated', 'rewarded'])->default('pending');
            $table->decimal('reward_amount', 15, 2)->default(500); // NGN 500
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();
            $table->index(['referrer_customer_id', 'status']);
        });

        // Add referral_code column to customers if not exists
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'referral_code')) {
                $table->string('referral_code', 12)->nullable()->unique()->after('last_login_at');
            }
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('portal_referrals');
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });
    }
};
