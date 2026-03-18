<?php
if (!function_exists('pushNotify')) {
    function pushNotify(string $customerId, string $title, string $body, string $url = '/notifications'): void {
        app(\App\Services\PushNotificationService::class)->send($customerId, $title, $body, $url);
    }
}
