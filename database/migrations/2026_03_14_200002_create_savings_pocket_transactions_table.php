<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('savings_pocket_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pocket_id');
            $table->uuid('account_id');
            $table->uuid('customer_id');
            $table->enum('type', ['deposit', 'withdrawal', 'interest', 'round_up'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('note', 255)->nullable();
            $table->string('transaction_ref', 60)->nullable(); // linked main txn ref if any
            $table->timestamps();

            $table->index('pocket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_pocket_transactions');
    }
};
