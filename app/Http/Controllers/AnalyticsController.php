<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private $creditTypes = ['deposit', 'disbursement', 'interest', 'savings_withdrawal'];

    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $accountIds = $accounts->pluck('id');

        // Last 6 months income vs expense
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $creditList = implode("','", $this->creditTypes);

        $monthlyData = DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'success')
            ->whereIn(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"), $months->toArray())
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('type'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month', 'type')
            ->get();

        // Category spending (last 30 days)
        $categories = DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'success')
            ->whereNotIn('type', $this->creditTypes)
            ->where('created_at', '>=', now()->subDays(30))
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        // This month stats
        $thisMonth = DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'success')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->select(
                DB::raw("SUM(CASE WHEN type IN ('{$creditList}') THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type NOT IN ('{$creditList}') THEN amount ELSE 0 END) as expenses"),
                DB::raw('COUNT(*) as txn_count')
            )
            ->first();

        // Last month stats
        $lastMonth = DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'success')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->select(
                DB::raw("SUM(CASE WHEN type NOT IN ('{$creditList}') THEN amount ELSE 0 END) as expenses")
            )
            ->first();

        return view('analytics.index', compact(
            'accounts', 'monthlyData', 'categories', 'thisMonth', 'lastMonth', 'months'
        ));
    }

    public function data(Request $request)
    {
        $customer   = $this->customer();
        $accountIds = $customer->accounts()->pluck('id');

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $creditList = implode("','", $this->creditTypes);

        $rows = DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->where('status', 'success')
            ->whereIn(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"), $months->toArray())
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw("SUM(CASE WHEN type IN ('{$creditList}') THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type NOT IN ('{$creditList}') THEN amount ELSE 0 END) as expenses")
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $labels  = [];
        $income  = [];
        $expense = [];

        foreach ($months as $m) {
            $labels[]  = now()->createFromFormat('Y-m', $m)->format('M Y');
            $income[]  = (float) ($rows[$m]->income ?? 0);
            $expense[] = (float) ($rows[$m]->expenses ?? 0);
        }

        return response()->json(compact('labels', 'income', 'expense'));
    }
}
