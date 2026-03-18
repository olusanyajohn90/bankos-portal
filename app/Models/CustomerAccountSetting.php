<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerAccountSetting extends Model
{
    protected $table = 'customer_account_settings';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['is_frozen' => 'boolean', 'hide_balance' => 'boolean', 'frozen_at' => 'datetime'];

    public static function forAccount(string $customerId, string $accountId): self
    {
        return static::firstOrNew(
            ['customer_id' => $customerId, 'account_id' => $accountId],
            ['id' => (string) \Illuminate\Support\Str::uuid(), 'color_hex' => '#2563eb']
        );
    }
}
