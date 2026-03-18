<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_known_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('customer_id');
            $table->string('device_fingerprint', 64);
            $table->string('device_name', 120)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->boolean('trusted')->default(true);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->unique(['customer_id', 'device_fingerprint']);
        });

        Schema::create('portal_login_otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('customer_id');
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_login_otps');
        Schema::dropIfExists('portal_known_devices');
    }
};
