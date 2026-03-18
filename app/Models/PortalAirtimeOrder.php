<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PortalAirtimeOrder extends Model {
    use HasUuids;
    protected $table = 'portal_airtime_orders';
    protected $guarded = ['id'];
    public function account() { return $this->belongsTo(Account::class); }
    public static array $networks = [
        'mtn'     => ['label'=>'MTN',     'color'=>'#f59e0b','prefixes'=>['0703','0706','0803','0806','0810','0813','0814','0816','0903','0906','0913','0916']],
        'airtel'  => ['label'=>'Airtel',  'color'=>'#dc2626','prefixes'=>['0701','0708','0802','0808','0812','0901','0902','0904','0907','0912']],
        'glo'     => ['label'=>'Glo',     'color'=>'#16a34a','prefixes'=>['0705','0805','0807','0811','0815','0905','0915']],
        '9mobile' => ['label'=>'9mobile', 'color'=>'#059669','prefixes'=>['0809','0817','0818','0908','0909']],
    ];
    public static array $dataPlans = [
        'mtn'     => [['plan'=>'500MB · 1 day','amount'=>100],['plan'=>'1GB · 7 days','amount'=>300],['plan'=>'2GB · 30 days','amount'=>500],['plan'=>'5GB · 30 days','amount'=>1000],['plan'=>'10GB · 30 days','amount'=>2000],['plan'=>'20GB · 30 days','amount'=>3500]],
        'airtel'  => [['plan'=>'750MB · 1 day','amount'=>100],['plan'=>'1.5GB · 7 days','amount'=>300],['plan'=>'3GB · 30 days','amount'=>500],['plan'=>'6GB · 30 days','amount'=>1000],['plan'=>'12GB · 30 days','amount'=>2000],['plan'=>'24GB · 30 days','amount'=>3500]],
        'glo'     => [['plan'=>'1GB · 1 day','amount'=>100],['plan'=>'2GB · 7 days','amount'=>300],['plan'=>'4GB · 30 days','amount'=>500],['plan'=>'7.7GB · 30 days','amount'=>1000],['plan'=>'15GB · 30 days','amount'=>2000],['plan'=>'30GB · 30 days','amount'=>3500]],
        '9mobile' => [['plan'=>'500MB · 1 day','amount'=>100],['plan'=>'1GB · 7 days','amount'=>300],['plan'=>'2.5GB · 30 days','amount'=>500],['plan'=>'5GB · 30 days','amount'=>1000],['plan'=>'11.5GB · 30 days','amount'=>2000],['plan'=>'22GB · 30 days','amount'=>3500]],
    ];
}
