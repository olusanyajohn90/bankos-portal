<?php
namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BeneficiaryController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer    = $this->customer();
        $beneficiaries = Beneficiary::where('customer_id', $customer->id)
            ->orderByDesc('transfer_count')
            ->get();

        return view('beneficiaries.index', compact('beneficiaries'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'nickname'       => 'required|string|max:100',
            'account_number' => 'required|string|max:20',
            'account_name'   => 'required|string|max:200',
            'is_intrabank'   => 'boolean',
            'bank_name'      => 'nullable|string|max:100',
        ]);

        // Prevent duplicates
        $exists = Beneficiary::where('customer_id', $customer->id)
            ->where('account_number', $request->account_number)
            ->exists();

        if ($exists) {
            return back()->withErrors(['This account number is already saved as a beneficiary.']);
        }

        Beneficiary::create([
            'id'             => (string) Str::uuid(),
            'customer_id'    => $customer->id,
            'tenant_id'      => $customer->tenant_id,
            'nickname'       => $request->nickname,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'is_intrabank'   => $request->boolean('is_intrabank', true),
            'bank_name'      => $request->bank_name,
        ]);

        return back()->with('success', $request->nickname . ' saved as beneficiary.');
    }

    public function destroy(string $id)
    {
        $customer = $this->customer();
        Beneficiary::where('customer_id', $customer->id)->findOrFail($id)->delete();
        return back()->with('success', 'Beneficiary removed.');
    }
}
