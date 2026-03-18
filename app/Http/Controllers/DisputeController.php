<?php
namespace App\Http\Controllers;

use App\Models\PortalDispute;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DisputeController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $disputes = PortalDispute::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->get();
        return view('disputes.index', compact('disputes'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $recentTxns = Transaction::whereIn('account_id', $accounts->pluck('id'))
            ->orderByDesc('created_at')->limit(30)->get();
        return view('disputes.create', compact('accounts', 'recentTxns'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'type'            => 'required|string',
            'description'     => 'required|string|min:20|max:1000',
            'account_id'      => 'nullable|string',
            'transaction_id'  => 'nullable|string',
            'disputed_amount' => 'nullable|numeric|min:1',
        ]);

        PortalDispute::create([
            'id'              => (string) Str::uuid(),
            'customer_id'     => $customer->id,
            'tenant_id'       => $customer->tenant_id,
            'account_id'      => $request->account_id ?: null,
            'transaction_id'  => $request->transaction_id ?: null,
            'reference'       => 'DSP-' . strtoupper(Str::random(8)),
            'type'            => $request->type,
            'description'     => $request->description,
            'disputed_amount' => $request->disputed_amount,
            'status'          => 'open',
        ]);

        return redirect()->route('disputes')
            ->with('success', 'Dispute logged. Reference number sent. Expected resolution: 5–7 business days.');
    }

    public function show(string $id)
    {
        $customer = $this->customer();
        $dispute = PortalDispute::where('customer_id', $customer->id)->findOrFail($id);
        return view('disputes.show', compact('dispute'));
    }
}
