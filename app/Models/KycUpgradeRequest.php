<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KycUpgradeRequest extends Model
{
    protected $table = 'kyc_upgrade_requests';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['reviewed_at' => 'datetime'];
}
