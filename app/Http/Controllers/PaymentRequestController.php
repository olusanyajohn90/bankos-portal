<?php
namespace App\Http\Controllers;

use App\Models\PortalPaymentRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentRequestController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $requests = PortalPaymentRequest::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->get();
        $accounts = $customer->accounts()->where('status', 'active')->get();
        return view('pay-requests.index', compact('requests', 'accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'account_id'     => 'required|string',
            'amount'         => 'required|numeric|min:100',
            'description'    => 'nullable|string|max:200',
            'recipient_name' => 'nullable|string|max:100',
            'recipient_email'=> 'nullable|email',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);
        PortalPaymentRequest::create([
            'id'              => (string) Str::uuid(),
            'customer_id'     => $customer->id,
            'tenant_id'       => $customer->tenant_id,
            'account_id'      => $account->id,
            'reference'       => strtoupper(Str::random(12)),
            'amount'          => $request->amount,
            'currency'        => $account->currency ?? 'NGN',
            'description'     => $request->description,
            'recipient_name'  => $request->recipient_name,
            'recipient_email' => $request->recipient_email,
            'status'          => 'pending',
            'expires_at'      => now()->addDays(7),
        ]);

        return back()->with('success', 'Payment request created. Share the link with your payer.');
    }

    public function destroy(string $id)
    {
        $customer = $this->customer();
        PortalPaymentRequest::where('customer_id', $customer->id)
            ->where('status', 'pending')->findOrFail($id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Request cancelled.');
    }

    // Public — no auth
    public function publicShow(string $reference)
    {
        $req = PortalPaymentRequest::where('reference', $reference)->firstOrFail();
        if ($req->status === 'paid') return view('pay-requests.paid', compact('req'));
        if ($req->status !== 'pending' || ($req->expires_at && $req->expires_at->isPast())) {
            return view('pay-requests.expired', compact('req'));
        }
        return view('pay-requests.pay', compact('req'));
    }

    public function publicPay(Request $request, string $reference)
    {
        $req = PortalPaymentRequest::where('reference', $reference)->where('status', 'pending')->firstOrFail();
        if ($req->expires_at && $req->expires_at->isPast()) return back()->withErrors(['This payment request has expired.']);

        $request->validate([
            'payer_name'    => 'required|string|max:100',
            'payer_account' => 'nullable|string',
        ]);

        // Simulate payment — in production this would go through a payment gateway
        DB::transaction(function () use ($req, $request) {
            $account = $req->account;
            if ($account) {
                $account->increment('available_balance', $req->amount);
                $account->increment('ledger_balance', $req->amount);
                Transaction::create([
                    'id' => (string) Str::uuid(), 'tenant_id' => $req->tenant_id,
                    'account_id' => $account->id, 'reference' => 'PAY-' . strtoupper(Str::random(10)),
                    'type' => 'deposit', 'amount' => $req->amount, 'currency' => $req->currency,
                    'description' => 'Payment from ' . $request->payer_name . ($req->description ? ' — ' . $req->description : ''),
                    'status' => 'success', 'performed_by' => null, // portal-initiated
                ]);
            }
            $req->update(['status' => 'paid', 'paid_at' => now(), 'paid_by_name' => $request->payer_name]);
        });

        return redirect()->route('pay-request.public', $reference);
    }
}
