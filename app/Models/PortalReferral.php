<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalReferral extends Model
{
    protected $table = 'portal_referrals';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['rewarded_at' => 'datetime', 'reward_amount' => 'decimal:2'];
}
