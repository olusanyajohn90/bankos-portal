<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $table = 'notification_preferences';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'debit_alert'           => 'boolean',
        'credit_alert'          => 'boolean',
        'low_balance_alert'     => 'boolean',
        'low_balance_threshold' => 'decimal:2',
        'large_txn_alert'       => 'boolean',
        'large_txn_threshold'   => 'decimal:2',
        'loan_reminder'         => 'boolean',
        'login_alert'           => 'boolean',
        'monthly_summary'       => 'boolean',
        'statement_ready'       => 'boolean',
        'weekly_statements'     => 'boolean',
    ];

    public static function forCustomer(string $customerId, string $tenantId): self
    {
        return self::firstOrCreate(
            ['customer_id' => $customerId],
            [
                'id'        => (string) \Illuminate\Support\Str::uuid(),
                'tenant_id' => $tenantId,
            ]
        );
    }
}
