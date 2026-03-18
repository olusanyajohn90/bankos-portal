<?php
namespace App\Http\Controllers;

use App\Models\StandingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StandingOrderController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();

        // The existing standing_orders table uses source_account_id
        $accountIds = $customer->accounts()->pluck('id');
        $orders = StandingOrder::whereIn('source_account_id', $accountIds)
            ->orderByDesc('created_at')
            ->get();

        $accounts = $customer->accounts()->get();

        return view('standing-orders.index', compact('orders', 'accounts'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        return view('standing-orders.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'account_id'               => 'required|string',
            'nickname'                 => 'required|string|max:100',
            'beneficiary_account_number' => 'required|string|max:20',
            'beneficiary_account_name'   => 'required|string|max:200',
            'amount'                   => 'required|numeric|min:1',
            'frequency'                => 'required|in:daily,weekly,monthly',
            'start_date'               => 'required|date|after_or_equal:today',
            'end_date'                 => 'nullable|date|after:start_date',
            'day_of_week'              => 'nullable|integer|between:0,6',
            'day_of_month'             => 'nullable|integer|between:1,28',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);

        StandingOrder::create([
            'id'                         => (string) Str::uuid(),
            'tenant_id'                  => $customer->tenant_id,
            'source_account_id'          => $account->id,
            'beneficiary_account_number' => $request->beneficiary_account_number,
            'beneficiary_name'           => $request->beneficiary_account_name,
            'transfer_type'              => 'intrabank',
            'amount'                     => $request->amount,
            'narration'                  => $request->nickname,
            'frequency'                  => $request->frequency,
            'start_date'                 => $request->start_date,
            'end_date'                   => $request->end_date,
            'next_run_date'              => $request->start_date,
            'status'                     => 'active',
            'created_by'                 => $customer->id,
        ]);

        return redirect()->route('standing-orders')->with('success', 'Standing order "' . $request->nickname . '" created.');
    }

    public function pause(string $id)
    {
        $this->ownerOrder($id)->update(['status' => 'paused']);
        return back()->with('success', 'Standing order paused.');
    }

    public function resume(string $id)
    {
        $this->ownerOrder($id)->update(['status' => 'active']);
        return back()->with('success', 'Standing order resumed.');
    }

    public function destroy(string $id)
    {
        $this->ownerOrder($id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Standing order cancelled.');
    }

    private function ownerOrder(string $id): StandingOrder
    {
        $customer   = $this->customer();
        $accountIds = $customer->accounts()->pluck('id');
        return StandingOrder::whereIn('source_account_id', $accountIds)->findOrFail($id);
    }
}
