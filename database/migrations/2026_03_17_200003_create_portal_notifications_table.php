<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tenant_id');
            $table->string('type', 60)->default('info'); // info, success, warning, alert, promo
            $table->string('icon', 30)->nullable(); // emoji or icon name
            $table->string('title', 120);
            $table->text('body');
            $table->json('data')->nullable(); // extra payload: amount, reference, etc.
            $table->string('action_url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'read_at']);
            $table->index(['customer_id', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('portal_notifications'); }
};
