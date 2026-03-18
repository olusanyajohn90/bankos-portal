<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrder extends Model
{
    protected $table = 'standing_orders';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'amount'      => 'decimal:2',
        'start_date'  => 'date',
        'end_date'    => 'date',
        'next_run_at' => 'date',
        'last_run_at' => 'date',
        'is_intrabank'=> 'boolean',
    ];

    // Map portal columns to the existing standing_orders schema from the main app
    // The existing table uses: source_account_id, beneficiary_account_number, etc.
    public function account()
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }
}
