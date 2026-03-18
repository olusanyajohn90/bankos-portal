<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\TransactionAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPortalTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * Financial operations — never auto-retry. Manual review required on failure.
     */
    public int $tries = 0;

    public function __construct(
        public readonly string $customerId,
        public readonly string $fromAccountId,
        public readonly string $toAccountNumber,
        public readonly float  $amount,
        public readonly string $description,
        public readonly string $reference,
    ) {
        $this->onQueue('transfers');
    }

    /**
     * Execute the job.
     * Processes the portal transfer atomically and sends a TransactionAlert notification.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            // Lock source account
            $fromAccount = Account::where('id', $this->fromAccountId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($fromAccount->balance < $this->amount) {
                throw new \RuntimeException(
                    "Insufficient balance. Required: {$this->amount}, Available: {$fromAccount->balance}"
                );
            }

            // Find destination account by account number (within same tenant)
            $toAccount = Account::where('account_number', $this->toAccountNumber)
                ->where('tenant_id', $fromAccount->tenant_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Debit source
            $fromAccount->decrement('balance', $this->amount);

            $debitTxn = Transaction::create([
                'tenant_id'   => $fromAccount->tenant_id,
                'account_id'  => $fromAccount->id,
                'reference'   => $this->reference,
                'type'        => 'debit',
                'amount'      => $this->amount,
                'currency'    => $fromAccount->currency ?? 'NGN',
                'description' => $this->description,
                'status'      => 'completed',
                'performed_by'=> $this->customerId,
            ]);

            // Credit destination
            $toAccount->increment('balance', $this->amount);

            Transaction::create([
                'tenant_id'   => $toAccount->tenant_id,
                'account_id'  => $toAccount->id,
                'reference'   => $this->reference . '-CR',
                'type'        => 'credit',
                'amount'      => $this->amount,
                'currency'    => $toAccount->currency ?? 'NGN',
                'description' => $this->description,
                'status'      => 'completed',
            ]);

            // Send notification to the customer who initiated the transfer
            $customer = \App\Models\Customer::findOrFail($this->customerId);
            $customer->notify(new TransactionAlert($debitTxn, 'debit'));

            Log::info('Portal transfer processed successfully', [
                'reference'   => $this->reference,
                'customer_id' => $this->customerId,
                'amount'      => $this->amount,
                'from'        => $fromAccount->account_number,
                'to'          => $this->toAccountNumber,
            ]);
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessPortalTransfer: transfer failed', [
            'reference'       => $this->reference,
            'customer_id'     => $this->customerId,
            'from_account_id' => $this->fromAccountId,
            'to_account'      => $this->toAccountNumber,
            'amount'          => $this->amount,
            'error'           => $exception->getMessage(),
        ]);
    }
}
