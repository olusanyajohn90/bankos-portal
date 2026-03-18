<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send a push notification to all subscribed devices for a customer.
     *
     * Note: Real delivery requires VAPID keys and minishlink/web-push library.
     * Install: composer require minishlink/web-push
     * Set in .env: VAPID_PUBLIC_KEY=... VAPID_PRIVATE_KEY=... VAPID_SUBJECT=mailto:...
     *
     * This implementation queues the notification and logs it.
     * When minishlink/web-push is installed, uncomment the actual sending code.
     */
    public function send(string $customerId, string $title, string $body, string $url = '/notifications', ?string $tag = null): void
    {
        $subscriptions = DB::table('portal_push_subscriptions')
            ->where('customer_id', $customerId)
            ->get();

        if ($subscriptions->isEmpty()) return;

        foreach ($subscriptions as $sub) {
            Log::info('PushNotification', [
                'customer_id' => $customerId,
                'title'       => $title,
                'body'        => $body,
                'endpoint'    => substr($sub->endpoint, 0, 60) . '...',
            ]);

            // Uncomment when minishlink/web-push is installed:
            // try {
            //     $webPush = new \Minishlink\WebPush\WebPush([
            //         'VAPID' => [
            //             'subject'    => config('app.vapid_subject'),
            //             'publicKey'  => config('app.vapid_public_key'),
            //             'privateKey' => config('app.vapid_private_key'),
            //         ],
            //     ]);
            //     $subscription = \Minishlink\WebPush\Subscription::create([
            //         'endpoint' => $sub->endpoint,
            //         'keys'     => ['p256dh' => $sub->p256dh, 'auth' => $sub->auth],
            //     ]);
            //     $webPush->queueNotification($subscription, json_encode([
            //         'title'   => $title,
            //         'body'    => $body,
            //         'url'     => $url,
            //         'tag'     => $tag ?? 'bankos-' . time(),
            //         'icon'    => '/icons/icon-192.png',
            //     ]));
            //     foreach ($webPush->flush() as $report) {
            //         if (!$report->isSuccess()) {
            //             DB::table('portal_push_subscriptions')->where('endpoint', $report->getEndpoint())->delete();
            //         }
            //     }
            // } catch (\Exception $e) {
            //     Log::error('PushNotification failed: ' . $e->getMessage());
            // }
        }
    }

    /**
     * Helper: notify customer of a transaction.
     */
    public function notifyTransaction(string $customerId, string $type, float $amount, string $currency = 'NGN'): void
    {
        $isCredit = in_array($type, ['deposit', 'disbursement', 'interest']);
        $sign     = $isCredit ? '+' : '-';
        $this->send(
            $customerId,
            $isCredit ? 'Credit Alert' : 'Debit Alert',
            "{$sign}{$currency} " . number_format($amount, 2) . " " . ($isCredit ? 'received' : 'debited'),
            '/notifications',
            'transaction-alert'
        );
    }

    /**
     * Helper: notify customer of a login from new device.
     */
    public function notifyNewDevice(string $customerId, string $deviceName): void
    {
        $this->send(
            $customerId,
            'New Device Login',
            "Your account was accessed from {$deviceName}. If this wasn't you, contact us immediately.",
            '/security',
            'security-alert'
        );
    }
}
