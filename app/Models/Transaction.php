<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
