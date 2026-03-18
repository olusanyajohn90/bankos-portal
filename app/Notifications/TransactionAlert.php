<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactionAlert extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public $transaction,
        public $account,
        public $tenant
    ) {}

    /**
     * Check notification preferences before sending via mail.
     * The customer ID is resolved from the transaction's performed_by field.
     */
    public function via($notifiable): array
    {
        // Resolve the customer_id from the transaction (performed_by = customer uuid)
        $customerId = $this->transaction->performed_by ?? null;

        if ($customerId) {
            /** @var object|null $pref */
            $pref = DB::table('notification_preferences')
                ->where('customer_id', $customerId)
                ->first();

            if ($pref) {
                $type     = strtolower((string) ($this->transaction->type ?? ''));
                $isCredit = \in_array($type, ['credit', 'deposit', 'transfer_in'], true);
                $isDebit  = \in_array($type, ['debit', 'withdrawal', 'transfer', 'transfer_out'], true);

                /** @var bool $creditAlert */
                $creditAlert = (bool) ($pref->credit_alert ?? true);
                /** @var bool $debitAlert */
                $debitAlert  = (bool) ($pref->debit_alert ?? true);

                if ($isCredit && !$creditAlert) {
                    return [];
                }
                if ($isDebit && !$debitAlert) {
                    return [];
                }
            }
        }

        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $txn     = $this->transaction;
        $account = $this->account;
        $tenant  = $this->tenant;

        // Determine direction label
        $type      = strtolower((string) ($txn->type ?? ''));
        $isCredit  = \in_array($type, ['credit', 'deposit', 'transfer_in'], true);
        $direction = $isCredit ? 'Credit' : 'Debit';

        $amount      = number_format((float) $txn->amount, 2);
        $balance     = number_format((float) $account->available_balance, 2);
        $currency    = (string) ($txn->currency ?? 'NGN');
        $description = $txn->description ?? $txn->narration ?? null;
        $reference   = (string) ($txn->reference ?? 'N/A');
        $dateTime    = $txn->created_at
            ? Carbon::parse($txn->created_at)->format('d M Y, h:i A')
            : now()->format('d M Y, h:i A');

        $firstName = (string) ($notifiable->first_name ?? '');
        $lastName  = (string) ($notifiable->last_name ?? '');
        $customerName = trim("{$firstName} {$lastName}");
        if ($customerName === '') {
            $customerName = (string) ($notifiable->name ?? 'Valued Customer');
        }

        $mail = (new MailMessage)
            ->subject("Transaction Alert — {$account->account_name}")
            ->greeting("Hello {$customerName},")
            ->line("{$direction} of {$currency} {$amount} on your account **{$account->account_name}**.");

        if ($description) {
            $mail->line("Description: {$description}");
        }

        $mail->line("Your new balance: {$currency} {$balance}")
             ->line("Reference: {$reference}")
             ->line("Date & Time: {$dateTime}")
             ->salutation("{$tenant->name} — This is an automated alert. Do not reply to this email.");

        return $mail;
    }
}
