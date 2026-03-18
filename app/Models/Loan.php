<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected $casts = [
        'principal_amount'    => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'disbursed_at'        => 'datetime',
    ];
}
