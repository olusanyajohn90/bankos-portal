<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PortalSavingsChallenge extends Model {
    use HasUuids;
    protected $table = 'portal_savings_challenges';
    protected $guarded = ['id'];
    protected $casts = ['start_date'=>'date','target_date'=>'date','completed_at'=>'datetime'];
    public function account() { return $this->belongsTo(Account::class); }
    public function pocket() { return $this->belongsTo(SavingsPocket::class,'pocket_id'); }
    public function getProgressPctAttribute(): float {
        return $this->target_amount > 0 ? min(100, round(($this->current_amount / $this->target_amount) * 100, 1)) : 0;
    }
    public static array $templates = [
        ['name'=>'Emergency Fund',      'emoji'=>'🏥','desc'=>'Build 3-6 months of expenses'],
        ['name'=>'Vacation Fund',       'emoji'=>'✈️','desc'=>'Save up for that dream trip'],
        ['name'=>'New Phone',           'emoji'=>'📱','desc'=>'Upgrade your device'],
        ['name'=>'School Fees',         'emoji'=>'🎓','desc'=>'Plan ahead for education'],
        ['name'=>'Business Capital',    'emoji'=>'💼','desc'=>'Seed your next venture'],
        ['name'=>'House Rent',          'emoji'=>'🏠','desc'=>'Never scramble for rent again'],
    ];
}
