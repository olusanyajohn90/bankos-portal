<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_cards', function (Blueprint $table) {
            $table->string('card_pin', 255)->nullable()->after('status');
            $table->timestamp('pin_set_at')->nullable()->after('card_pin');
            $table->tinyInteger('pin_attempts')->default(0)->after('pin_set_at');
            $table->timestamp('pin_locked_until')->nullable()->after('pin_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_cards', function (Blueprint $table) {
            $table->dropColumn(['card_pin', 'pin_set_at', 'pin_attempts', 'pin_locked_until']);
        });
    }
};
