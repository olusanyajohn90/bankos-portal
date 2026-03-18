<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InterbankTransferController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();

        $recentTransfers = DB::table('portal_interbank_transfers')
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('interbank-transfer.index', compact('accounts', 'recentTransfers'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();

        $request->validate([
            'from_account_id'    => 'required|string',
            'beneficiary_name'   => 'required|string|max:120',
            'beneficiary_account'=> 'required|digits:10',
            'bank_code'          => 'required|string|max:10',
            'bank_name'          => 'required|string|max:100',
            'amount'             => 'required|numeric|min:100',
            'narration'          => 'nullable|string|max:255',
            'pin'                => 'required|digits:4',
        ]);

        // Verify transaction PIN
        if (!$customer->portal_pin || !Hash::check($request->pin, $customer->portal_pin)) {
            return back()->withErrors(['pin' => 'Incorrect transaction PIN.'])->withInput($request->except('pin'));
        }

        $account = $customer->accounts()->findOrFail($request->from_account_id);
        $amount  = (float) $request->amount;

        // Check sufficient balance
        if ((float) $account->available_balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient account balance. Available: NGN ' . number_format((float) $account->available_balance, 2)])->withInput($request->except('pin'));
        }

        $reference = 'NIP-' . strtoupper(Str::random(12));

        DB::transaction(function () use ($customer, $account, $amount, $request, $reference) {
            // Deduct from source account
            $account->decrement('available_balance', $amount);
            $account->decrement('ledger_balance', $amount);

            // Create Transaction record
            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => $reference,
                'type'         => 'transfer',
                'amount'       => $amount,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => 'NIP Transfer to ' . $request->beneficiary_name . ' (' . $request->beneficiary_account . ' — ' . $request->bank_name . ')',
                'status'       => 'pending',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);

            // Create interbank transfer record
            DB::table('portal_interbank_transfers')->insert([
                'id'                  => (string) Str::uuid(),
                'customer_id'         => $customer->id,
                'tenant_id'           => $customer->tenant_id,
                'account_id'          => $account->id,
                'beneficiary_name'    => $request->beneficiary_name,
                'beneficiary_account' => $request->beneficiary_account,
                'beneficiary_bank'    => $request->bank_name,
                'bank_code'           => $request->bank_code,
                'amount'              => $amount,
                'narration'           => $request->narration,
                'reference'           => $reference,
                'session_id'          => null,
                'status'              => 'pending',
                'pin_verified_at'     => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        });

        return redirect()->route('interbank-transfer.index')
            ->with('success', 'Transfer queued. Reference: ' . $reference . '. Processing within 10 minutes.');
    }

    public function banks()
    {
        return response()->json([
            ['code' => '000014', 'name' => 'ACCESS BANK'],
            ['code' => '000005', 'name' => 'ACCESS BANK (DIAMOND)'],
            ['code' => '035A',   'name' => 'ALAT BY WEMA'],
            ['code' => '000035', 'name' => 'CBN'],
            ['code' => '060001', 'name' => 'CORONATION MERCHANT BANK'],
            ['code' => '050006', 'name' => 'EKONDO MFB'],
            ['code' => '070010', 'name' => 'ECOBANK NIGERIA'],
            ['code' => '000003', 'name' => 'FIRST BANK OF NIGERIA'],
            ['code' => '011',    'name' => 'FIRST CITY MONUMENT BANK'],
            ['code' => '00103',  'name' => 'FINCORP MFB'],
            ['code' => '070009', 'name' => 'FIRST INLAND BANK'],
            ['code' => '058',    'name' => 'GUARANTY TRUST BANK'],
            ['code' => '070006', 'name' => 'JAIZ BANK'],
            ['code' => '030',    'name' => 'HERITAGE BANK'],
            ['code' => '082',    'name' => 'KEYSTONE BANK'],
            ['code' => '50211',  'name' => 'KUDA BANK'],
            ['code' => '000002', 'name' => 'KEYSTONE BANK'],
            ['code' => '076',    'name' => 'POLARIS BANK'],
            ['code' => '101',    'name' => 'PROVIDUS BANK'],
            ['code' => '000023', 'name' => 'STERLING BANK'],
            ['code' => '232',    'name' => 'STERLING BANK'],
            ['code' => '032',    'name' => 'UNION BANK'],
            ['code' => '033',    'name' => 'UNITED BANK FOR AFRICA'],
            ['code' => '215',    'name' => 'UNITY BANK'],
            ['code' => '035',    'name' => 'WEMA BANK'],
            ['code' => '057',    'name' => 'ZENITH BANK'],
        ]);
    }
}
