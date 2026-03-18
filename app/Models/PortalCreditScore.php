<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalCreditScore extends Model
{
    protected $table = 'portal_credit_scores';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = ['factors' => 'array'];

    public function getGradeColorAttribute(): array
    {
        return match(true) {
            $this->score >= 750 => ['#16a34a', '#f0fdf4', 'Excellent'],
            $this->score >= 670 => ['#2563eb', '#eff6ff', 'Good'],
            $this->score >= 580 => ['#d97706', '#fffbeb', 'Fair'],
            $this->score >= 300 => ['#dc2626', '#fef2f2', 'Poor'],
            default             => ['#6b7280', '#f9fafb', 'No Score'],
        };
    }

    public static function compute(Customer $customer): array
    {
        $loans = Loan::where('customer_id', $customer->id)->get();
        $accounts = $customer->accounts()->get();
        $totalTxns = Transaction::whereIn('account_id', $accounts->pluck('id'))->count();

        // 1. Payment history (35%) — based on on-time loan repayments
        $paymentScore = 350; // default full marks
        if ($loans->count() > 0) {
            $overdueCount = $loans->where('status', 'overdue')->count();
            $writtenOff   = $loans->where('status', 'written_off')->count();
            $paymentScore = max(0, 350 - ($overdueCount * 50) - ($writtenOff * 100));
        }

        // 2. Credit utilization (30%) — outstanding vs principal
        $utilizationScore = 300;
        if ($loans->whereIn('status', ['active', 'overdue'])->count() > 0) {
            $totalPrincipal = $loans->sum('principal_amount');
            $totalOutstanding = $loans->sum('outstanding_balance');
            $utilization = $totalPrincipal > 0 ? $totalOutstanding / $totalPrincipal : 0;
            $utilizationScore = max(0, (int) (300 * (1 - $utilization)));
        }

        // 3. Account age (15%) — years since oldest account
        $oldestAccount = $accounts->sortBy('created_at')->first();
        $ageYears = $oldestAccount ? now()->diffInYears($oldestAccount->created_at) : 0;
        $ageScore = min(150, $ageYears * 30);

        // 4. Account mix (10%) — variety of products
        $hasLoan     = $loans->whereIn('status', ['active', 'closed'])->count() > 0;
        $hasSavings  = $accounts->where('type', 'savings')->count() > 0;
        $hasCurrent  = $accounts->where('type', 'current')->count() > 0;
        $mixScore = 40 + ($hasLoan ? 25 : 0) + ($hasSavings ? 20 : 0) + ($hasCurrent ? 15 : 0);

        // 5. Activity (10%) — recent transactions
        $activityScore = min(100, $totalTxns * 2);

        $total = min(850, max(300, $paymentScore + $utilizationScore + $ageScore + $mixScore + $activityScore));

        $grade = match(true) {
            $total >= 750 => 'Excellent',
            $total >= 670 => 'Good',
            $total >= 580 => 'Fair',
            default       => 'Poor',
        };

        $factors = [];
        if ($paymentScore < 300) $factors[] = ['type' => 'negative', 'text' => 'Overdue or written-off loans impact your score'];
        if ($utilizationScore < 200) $factors[] = ['type' => 'negative', 'text' => 'High outstanding loan balance reduces your score'];
        if ($ageScore < 60) $factors[] = ['type' => 'tip', 'text' => 'Older accounts improve your score — keep them active'];
        if (!$hasLoan) $factors[] = ['type' => 'tip', 'text' => 'Responsibly repaying a loan would boost your score'];
        if ($activityScore < 50) $factors[] = ['type' => 'tip', 'text' => 'Regular account activity improves your score'];
        if ($total >= 670) $factors[] = ['type' => 'positive', 'text' => 'Good repayment history detected'];
        if ($hasSavings && $hasCurrent) $factors[] = ['type' => 'positive', 'text' => 'Healthy product mix across account types'];

        return compact('total', 'grade', 'paymentScore', 'utilizationScore', 'ageScore', 'mixScore', 'activityScore', 'factors');
    }
}
