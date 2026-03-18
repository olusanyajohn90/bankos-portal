<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('customers', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            }
            if (!Schema::hasColumn('customers', 'two_factor_method')) {
                $table->enum('two_factor_method', ['totp', 'sms'])->nullable()->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('customers', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_method');
            }
            if (!Schema::hasColumn('customers', 'portal_frozen')) {
                $table->boolean('portal_frozen')->default(false)->after('two_factor_confirmed_at');
            }
            if (!Schema::hasColumn('customers', 'portal_frozen_at')) {
                $table->timestamp('portal_frozen_at')->nullable()->after('portal_frozen');
            }
        });
    }
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $cols = ['two_factor_secret', 'two_factor_enabled', 'two_factor_method', 'two_factor_confirmed_at', 'portal_frozen', 'portal_frozen_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) $table->dropColumn($col);
            }
        });
    }
};
