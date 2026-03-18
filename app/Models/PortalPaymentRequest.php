<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalPaymentRequest extends Model
{
    protected $table = 'portal_payment_requests';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['expires_at' => 'datetime', 'paid_at' => 'datetime', 'amount' => 'decimal:2'];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === 'pending';
    }

    public function account() { return $this->belongsTo(Account::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
