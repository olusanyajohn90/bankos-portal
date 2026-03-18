<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    protected $table = 'bill_payments';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function categoryIcon(string $category): string
    {
        return match($category) {
            'airtime'     => '📱',
            'data'        => '🌐',
            'electricity' => '⚡',
            'tv'          => '📺',
            'water'       => '💧',
            default       => '💳',
        };
    }
}
