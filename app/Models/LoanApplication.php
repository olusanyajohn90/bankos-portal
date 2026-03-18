<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    protected $table = 'loan_applications';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['reviewed_at' => 'datetime', 'requested_amount' => 'decimal:2', 'monthly_income' => 'decimal:2'];

    public static $types = [
        'personal'  => ['label' => 'Personal Loan',    'icon' => '👤', 'desc' => 'For personal needs, school fees, medical bills'],
        'business'  => ['label' => 'Business Loan',    'icon' => '🏢', 'desc' => 'For business expansion or working capital'],
        'emergency' => ['label' => 'Emergency Loan',   'icon' => '🚨', 'desc' => 'Quick disbursement for urgent needs'],
        'mortgage'  => ['label' => 'Mortgage / Housing','icon' => '🏠', 'desc' => 'Buy or build your dream home'],
        'auto'      => ['label' => 'Auto Loan',        'icon' => '🚗', 'desc' => 'Finance your next vehicle'],
    ];

    public static $statusColors = [
        'submitted'    => ['#d97706', '#fffbeb', '#fde68a'],
        'under_review' => ['#2563eb', '#eff6ff', '#bfdbfe'],
        'approved'     => ['#16a34a', '#f0fdf4', '#bbf7d0'],
        'rejected'     => ['#dc2626', '#fef2f2', '#fecaca'],
        'disbursed'    => ['#059669', '#ecfdf5', '#a7f3d0'],
        'cancelled'    => ['#6b7280', '#f9fafb', '#e5e7eb'],
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function account() { return $this->belongsTo(Account::class); }
}
