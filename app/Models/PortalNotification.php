<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $table = 'portal_notifications';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];

    public function isRead(): bool { return !is_null($this->read_at); }

    public static function send(string $customerId, string $tenantId, string $type, string $title, string $body, array $data = [], ?string $actionUrl = null): void
    {
        static::create([
            'id'         => (string) \Illuminate\Support\Str::uuid(),
            'customer_id' => $customerId,
            'tenant_id'   => $tenantId,
            'type'        => $type,
            'title'       => $title,
            'body'        => $body,
            'data'        => $data,
            'action_url'  => $actionUrl,
        ]);
    }

    public static function unreadCount(string $customerId): int
    {
        return static::where('customer_id', $customerId)->whereNull('read_at')->count();
    }
}
