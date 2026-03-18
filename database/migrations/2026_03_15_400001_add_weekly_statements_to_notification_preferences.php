<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_preferences') && !Schema::hasColumn('notification_preferences', 'weekly_statements')) {
            Schema::table('notification_preferences', function (Blueprint $table) {
                $table->boolean('weekly_statements')->default(true)->after('monthly_summary');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notification_preferences', 'weekly_statements')) {
            Schema::table('notification_preferences', function (Blueprint $table) {
                $table->dropColumn('weekly_statements');
            });
        }
    }
};
