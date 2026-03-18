<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsPocketTransaction extends Model
{
    protected $table = 'savings_pocket_transactions';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function pocket()
    {
        return $this->belongsTo(SavingsPocket::class, 'pocket_id');
    }
}
