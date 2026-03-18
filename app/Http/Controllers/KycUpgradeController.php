<?php
namespace App\Http\Controllers;

use App\Models\KycUpgradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KycUpgradeController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $pending = KycUpgradeRequest::where('customer_id', $customer->id)
            ->whereIn('status', ['submitted', 'under_review'])->first();
        $history = KycUpgradeRequest::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->get();

        $tierLimits = [
            'level_1' => ['daily_transfer' => 50000,    'single_txn' => 20000,    'balance' => 300000],
            'level_2' => ['daily_transfer' => 200000,   'single_txn' => 100000,   'balance' => 500000],
            'level_3' => ['daily_transfer' => 5000000,  'single_txn' => 1000000,  'balance' => 'Unlimited'],
        ];

        return view('kyc.upgrade', compact('customer', 'pending', 'history', 'tierLimits'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();

        // Block if pending request exists
        if (KycUpgradeRequest::where('customer_id', $customer->id)->whereIn('status', ['submitted', 'under_review'])->exists()) {
            return back()->withErrors(['You already have a pending upgrade request.']);
        }

        $currentTier = $customer->kyc_tier ?? 'level_1';
        $targetTier  = $currentTier === 'level_1' ? 'level_2' : 'level_3';

        $request->validate([
            'bvn'     => 'nullable|string|size:11',
            'nin'     => 'nullable|string|size:11',
            'id_type' => 'required|string',
            'id_number' => 'required|string',
        ]);

        $paths = [];
        foreach (['id_document', 'selfie', 'address_proof'] as $field) {
            if ($request->hasFile($field)) {
                $paths[$field . '_path'] = $request->file($field)->store('kyc-docs/' . $customer->id, 'local');
            }
        }

        KycUpgradeRequest::create(array_merge([
            'id'           => (string) Str::uuid(),
            'customer_id'  => $customer->id,
            'tenant_id'    => $customer->tenant_id,
            'current_tier' => $currentTier,
            'target_tier'  => $targetTier,
            'bvn'          => $request->bvn,
            'nin'          => $request->nin,
            'id_type'      => $request->id_type,
            'id_number'    => $request->id_number,
            'status'       => 'submitted',
        ], $paths));

        return redirect()->route('kyc.upgrade')
            ->with('success', 'KYC upgrade request submitted! Our compliance team will review within 1–2 business days.');
    }
}
