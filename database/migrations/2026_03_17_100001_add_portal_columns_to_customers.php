<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'portal_password')) {
                $table->string('portal_password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'portal_pin')) {
                $table->string('portal_pin', 6)->nullable()->after('portal_password');
            }
            if (!Schema::hasColumn('customers', 'portal_active')) {
                $table->boolean('portal_active')->default(false)->after('portal_pin');
            }
            if (!Schema::hasColumn('customers', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('portal_active');
            }
            if (!Schema::hasColumn('customers', 'remember_token')) {
                $table->rememberToken()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $cols = ['portal_password', 'portal_pin', 'portal_active', 'last_login_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
