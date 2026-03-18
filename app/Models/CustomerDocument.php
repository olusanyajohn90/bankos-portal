<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $table = 'customer_documents';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'fee' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'account_statement'    => 'Account Statement',
            'confirmation_letter'  => 'Account Confirmation Letter',
            'reference_letter'     => 'Bank Reference Letter',
            'loan_clearance'       => 'Loan Clearance Letter',
            default                => ucwords(str_replace('_', ' ', $type)),
        };
    }
}
