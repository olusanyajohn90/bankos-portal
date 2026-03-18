<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PortalChequeRequest extends Model {
    use HasUuids;
    protected $table = 'portal_cheque_requests';
    protected $guarded = ['id'];
    protected $casts = ['ready_at'=>'datetime','collected_at'=>'datetime'];
    public function account() { return $this->belongsTo(Account::class); }
    public static array $statusColors = [
        'pending'    =>['#d97706','#fffbeb'],
        'processing' =>['#2563eb','#eff6ff'],
        'ready'      =>['#059669','#ecfdf5'],
        'collected'  =>['#16a34a','#f0fdf4'],
        'cancelled'  =>['#6b7280','#f9fafb'],
    ];
}
