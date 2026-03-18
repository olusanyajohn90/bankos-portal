<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsPocket extends Model
{
    protected $table = 'savings_pockets';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'auto_rule'     => 'array',
        'target_amount' => 'decimal:2',
        'balance'       => 'decimal:2',
        'target_date'   => 'date',
        'locked_until'  => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(SavingsPocketTransaction::class, 'pocket_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function progressPercent(): float
    {
        if (!$this->target_amount || $this->target_amount == 0) return 0;
        return min(100, round(($this->balance / $this->target_amount) * 100, 1));
    }
}
