<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class LowBalanceAlert extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public $account,
        public $tenant,
        public float $threshold = 1000.0
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $account  = $this->account;
        $tenant   = $this->tenant;
        $balance  = number_format((float) $account->available_balance, 2);
        $currency = $account->currency ?? 'NGN';

        $customerName = trim(($notifiable->first_name ?? '') . ' ' . ($notifiable->last_name ?? ''));
        if (empty(trim($customerName))) {
            $customerName = $notifiable->name ?? 'Valued Customer';
        }

        return (new MailMessage)
            ->subject("Low Balance Alert — {$account->account_name}")
            ->greeting("Hello {$customerName},")
            ->line("Your account **{$account->account_name}** (No: {$account->account_number}) has a low balance.")
            ->line("Current available balance: **{$currency} {$balance}**")
            ->line("Your balance has fallen below the threshold of {$currency} " . number_format($this->threshold, 2) . ".")
            ->line("To avoid service interruptions, please fund your account at your earliest convenience.")
            ->salutation("{$tenant->name} — This is an automated alert. Do not reply to this email.");
    }
}
