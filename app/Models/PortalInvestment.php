<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalInvestment extends Model
{
    protected $table = 'portal_investments';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'principal' => 'decimal:2', 'expected_interest' => 'decimal:2', 'maturity_amount' => 'decimal:2',
        'start_date' => 'date', 'maturity_date' => 'date', 'matured_at' => 'datetime',
        'penalty_amount' => 'decimal:2',
    ];

    public static $durations = [
        30  => ['label' => '30 Days',  'rate' => 8.0],
        60  => ['label' => '60 Days',  'rate' => 9.5],
        90  => ['label' => '90 Days',  'rate' => 11.0],
        180 => ['label' => '180 Days', 'rate' => 12.5],
        365 => ['label' => '1 Year',   'rate' => 14.0],
    ];

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'active') return 0;
        return max(0, now()->diffInDays($this->maturity_date, false));
    }

    public function getAccruedInterestAttribute(): float
    {
        if ($this->status !== 'active') return 0;
        $daysElapsed = now()->diffInDays($this->start_date);
        $dailyRate = ($this->interest_rate / 100) / 365;
        return round((float)$this->principal * $dailyRate * $daysElapsed, 2);
    }

    public function account() { return $this->belongsTo(Account::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
