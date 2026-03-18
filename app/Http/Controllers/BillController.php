<?php
namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    private static array $categories = [
        'airtime' => [
            'label' => 'Airtime',
            'icon'  => '📱',
            'color' => '#7c3aed',
            'billers' => ['MTN', 'Airtel', 'Glo', '9mobile'],
            'fields' => ['phone' => 'Phone Number'],
            'amounts' => [100, 200, 500, 1000, 2000, 5000],
        ],
        'data' => [
            'label' => 'Data Bundle',
            'icon'  => '🌐',
            'color' => '#2563eb',
            'billers' => ['MTN', 'Airtel', 'Glo', '9mobile'],
            'fields' => ['phone' => 'Phone Number'],
            'amounts' => [200, 500, 1000, 2000, 3000, 5000],
        ],
        'electricity' => [
            'label' => 'Electricity',
            'icon'  => '⚡',
            'color' => '#d97706',
            'billers' => ['IKEDC (Ikeja Electric)', 'EKEDC (Eko Electric)', 'AEDC (Abuja Electric)', 'BEDC (Benin Electric)', 'PHED (Port Harcourt Electric)', 'KEDCO (Kano Electric)', 'JEDC (Jos Electric)'],
            'fields' => ['meter' => 'Meter Number'],
            'amounts' => [1000, 2000, 5000, 10000, 20000],
        ],
        'tv' => [
            'label' => 'Cable TV',
            'icon'  => '📺',
            'color' => '#059669',
            'billers' => ['DSTV', 'GOtv', 'Startimes', 'ShowMax'],
            'fields' => ['smartcard' => 'Smartcard / IUC Number'],
            'amounts' => [1800, 2500, 3800, 5000, 8000, 15800, 21000],
        ],
        'water' => [
            'label' => 'Water Bill',
            'icon'  => '💧',
            'color' => '#0ea5e9',
            'billers' => ['Lagos Water Corp', 'Abuja Water Board', 'FCT Water Board'],
            'fields' => ['meter' => 'Meter / Account Number'],
            'amounts' => [500, 1000, 2000, 5000],
        ],
    ];

    public function index()
    {
        $customer = $this->customer();
        $recent   = BillPayment::where('customer_id', $customer->id)
            ->where('status', 'success')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('bills.index', ['categories' => self::$categories, 'recent' => $recent]);
    }

    public function category(string $category)
    {
        if (!isset(self::$categories[$category])) abort(404);

        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $cat      = self::$categories[$category];
        $history  = BillPayment::where('customer_id', $customer->id)
            ->where('category', $category)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('bills.category', compact('category', 'cat', 'accounts', 'history'));
    }

    public function pay(Request $request, string $category)
    {
        if (!isset(self::$categories[$category])) abort(404);

        $customer = $this->customer();
        $cat      = self::$categories[$category];

        $request->validate([
            'account_id' => 'required|string',
            'biller'     => 'required|string|max:100',
            'recipient'  => 'required|string|max:100',
            'amount'     => 'required|numeric|min:50',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);
        $amount  = (float) $request->amount;

        if ((float) $account->available_balance < $amount) {
            return back()->withErrors(['Insufficient account balance.']);
        }

        $reference = 'BILL-' . strtoupper(Str::random(10));

        // Deduct from account
        $account->decrement('available_balance', $amount);
        $account->decrement('ledger_balance', $amount);

        // Log transaction
        Transaction::create([
            'id'           => (string) Str::uuid(),
            'tenant_id'    => $customer->tenant_id,
            'account_id'   => $account->id,
            'reference'    => $reference,
            'type'         => 'bill_payment',
            'amount'       => $amount,
            'currency'     => $account->currency ?? 'NGN',
            'description'  => $cat['label'] . ' — ' . $request->biller . ' (' . $request->recipient . ')',
            'status'       => 'success',
            'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
        ]);

        // Simulate token for electricity
        $token = null;
        if ($category === 'electricity') {
            $token = implode('-', str_split(str_pad(random_int(0, 99999999999999999), 20, '0', STR_PAD_LEFT), 4));
        }

        BillPayment::create([
            'id'          => (string) Str::uuid(),
            'tenant_id'   => $customer->tenant_id,
            'account_id'  => $account->id,
            'customer_id' => $customer->id,
            'category'    => $category,
            'biller'      => $request->biller,
            'recipient'   => $request->recipient,
            'amount'      => $amount,
            'reference'   => $reference,
            'token'       => $token,
            'status'      => 'success',
        ]);

        $msg = 'Payment of NGN ' . number_format($amount, 2) . ' to ' . $request->biller . ' was successful.';
        if ($token) $msg .= ' Token: ' . $token;

        return redirect()->route('bills.category', $category)->with('success', $msg);
    }
}
