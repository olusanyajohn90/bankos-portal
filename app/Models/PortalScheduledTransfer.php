<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PortalScheduledTransfer extends Model {
    use HasUuids;
    protected $table = 'portal_scheduled_transfers';
    protected $guarded = ['id'];
    protected $casts = ['scheduled_at'=>'datetime','processed_at'=>'datetime'];
    public function account() { return $this->belongsTo(Account::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
