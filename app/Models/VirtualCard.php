<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualCard extends Model
{
    protected $table = 'virtual_cards';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'spending_limit'    => 'decimal:2',
        'spent_this_month'  => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function isFrozen(): bool
    {
        return $this->status === 'frozen';
    }
}
