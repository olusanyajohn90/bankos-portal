<?php
namespace App\Http\Controllers;

use App\Models\PortalBudget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BudgetController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $month    = now()->format('Y-m');

        $budgets  = PortalBudget::where('customer_id', $customer->id)
            ->where('month', $month)
            ->get()->keyBy('category');

        // Calculate spending per category this month
        $accountIds = $customer->accounts()->pluck('id');
        $txns = Transaction::whereIn('account_id', $accountIds)
            ->whereNotIn('type', ['deposit', 'credit', 'disbursement', 'interest', 'reversal'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get();

        $spending = [];
        foreach ($txns as $t) {
            $cat = PortalBudget::categorizeTransaction($t->description ?? '', $t->type ?? '');
            $spending[$cat] = ($spending[$cat] ?? 0) + (float)$t->amount;
        }

        $totalSpent  = array_sum($spending);
        $totalBudget = $budgets->sum('monthly_limit');

        return view('budget.index', compact('budgets', 'spending', 'totalSpent', 'totalBudget', 'month'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'category'      => 'required|string',
            'monthly_limit' => 'required|numeric|min:100',
        ]);

        $month = now()->format('Y-m');
        PortalBudget::updateOrCreate(
            ['customer_id' => $customer->id, 'category' => $request->category, 'month' => $month],
            ['id' => (string) Str::uuid(), 'tenant_id' => $customer->tenant_id, 'monthly_limit' => $request->monthly_limit]
        );

        return back()->with('success', 'Budget saved.');
    }

    public function destroy(string $id)
    {
        $customer = $this->customer();
        PortalBudget::where('customer_id', $customer->id)->findOrFail($id)->delete();
        return back()->with('success', 'Budget removed.');
    }
}
