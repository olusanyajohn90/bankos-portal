<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExecuteStandingOrders extends Command
{
    protected $signature = 'banking:execute-standing-orders {--tenant= : Limit to a specific tenant ID}';
    protected $description = 'Execute due standing orders for portal customers';

    public function handle(): int
    {
        $now = Carbon::now()->toDateString();

        // Base query: active orders whose next_run_at is today or in the past
        $query = DB::table('standing_orders')
            ->where('status', 'active')
            ->where('next_run_at', '<=', $now);

        if ($this->option('tenant')) {
            $query->where('tenant_id', $this->option('tenant'));
        }

        $orders = $query->get();

        $executed = 0;
        $paused   = 0;

        foreach ($orders as $order) {
            try {
                $result = $this->executeOrder($order);
                if ($result === 'executed') {
                    $executed++;
                } else {
                    $paused++;
                }
            } catch (\Throwable $e) {
                Log::error("ExecuteStandingOrders: Unexpected error on order {$order->id} — {$e->getMessage()}");
                $paused++;
            }
        }

        $msg = "Executed {$executed} standing order(s), paused {$paused}.";
        Log::info("ExecuteStandingOrders: {$msg}");
        $this->info($msg);

        return self::SUCCESS;
    }

    private function executeOrder(object $order): string
    {
        // Look up source account
        $sourceAccount = DB::table('accounts')
            ->where('id', $order->account_id)
            ->where('tenant_id', $order->tenant_id)
            ->first();

        if (!$sourceAccount || $sourceAccount->status !== 'active') {
            $reason = !$sourceAccount ? 'Source account not found' : 'Source account is not active';
            $this->pauseOrder($order->id, $reason);
            Log::warning("ExecuteStandingOrders: Paused order {$order->id} — {$reason}");
            return 'paused';
        }

        if ((float) $sourceAccount->available_balance < (float) $order->amount) {
            $this->pauseOrder($order->id, 'Insufficient balance');
            Log::warning("ExecuteStandingOrders: Paused order {$order->id} for account {$order->account_id} — Insufficient balance");
            return 'paused';
        }

        // Find destination account by account_number within the same tenant (intrabank only)
        $destAccount = null;
        if ($order->is_intrabank) {
            $destAccount = DB::table('accounts')
                ->where('tenant_id', $order->tenant_id)
                ->where('account_number', $order->beneficiary_account_number)
                ->first();

            if (!$destAccount) {
                $this->pauseOrder($order->id, "Destination account {$order->beneficiary_account_number} not found");
                Log::warning("ExecuteStandingOrders: Paused order {$order->id} — destination account not found");
                return 'paused';
            }
        }

        DB::transaction(function () use ($order, $sourceAccount, $destAccount) {
            $amount   = (float) $order->amount;
            $currency = $sourceAccount->currency ?? 'NGN';
            $narration = $order->nickname ?? 'Standing order transfer';

            // Debit source
            DB::table('accounts')
                ->where('id', $sourceAccount->id)
                ->decrement('available_balance', $amount);
            DB::table('accounts')
                ->where('id', $sourceAccount->id)
                ->decrement('ledger_balance', $amount);

            $refBase = 'SO-' . now()->format('Ymd') . '-' . strtoupper(substr(str_replace('-', '', $order->id), 0, 8));

            DB::table('transactions')->insert([
                'id'          => (string) Str::uuid(),
                'tenant_id'   => $order->tenant_id,
                'account_id'  => $sourceAccount->id,
                'reference'   => $refBase . '-DR',
                'type'        => 'transfer',
                'amount'      => $amount,
                'currency'    => $currency,
                'description' => "Standing order debit: {$narration}",
                'status'      => 'success',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Credit destination (intrabank only)
            if ($destAccount) {
                DB::table('accounts')
                    ->where('id', $destAccount->id)
                    ->increment('available_balance', $amount);
                DB::table('accounts')
                    ->where('id', $destAccount->id)
                    ->increment('ledger_balance', $amount);

                DB::table('transactions')->insert([
                    'id'          => (string) Str::uuid(),
                    'tenant_id'   => $order->tenant_id,
                    'account_id'  => $destAccount->id,
                    'reference'   => $refBase . '-CR',
                    'type'        => 'transfer',
                    'amount'      => $amount,
                    'currency'    => $currency,
                    'description' => "Standing order credit: {$narration}",
                    'status'      => 'success',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Advance next_run_at and update run count
            $nextRunAt = $this->calculateNextRunDate(Carbon::parse($order->next_run_at), $order->frequency);

            DB::table('standing_orders')->where('id', $order->id)->update([
                'next_run_at'  => $nextRunAt->toDateString(),
                'last_run_at'  => now()->toDateString(),
                'run_count'    => DB::raw('run_count + 1'),
                'updated_at'   => now(),
            ]);
        });

        return 'executed';
    }

    private function pauseOrder(string $orderId, string $reason): void
    {
        DB::table('standing_orders')->where('id', $orderId)->update([
            'status'     => 'paused',
            'updated_at' => now(),
        ]);
    }

    private function calculateNextRunDate(Carbon $current, string $frequency): Carbon
    {
        return match ($frequency) {
            'daily'     => $current->addDay(),
            'weekly'    => $current->addWeek(),
            'quarterly' => $current->addMonths(3),
            default     => $current->addMonth(), // monthly
        };
    }
}
