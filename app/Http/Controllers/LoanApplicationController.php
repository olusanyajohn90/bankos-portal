<?php
namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoanApplicationController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $applications = LoanApplication::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->orderByDesc('created_at')->get();
        return view('loans.applications', compact('applications'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->where('status', 'active')->get();
        return view('loans.apply', compact('accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'loan_type'              => 'required|string',
            'requested_amount'       => 'required|numeric|min:1000',
            'requested_tenor_months' => 'required|integer|min:1|max:84',
            'purpose'                => 'required|string|max:500',
            'employment_status'      => 'required|string',
            'monthly_income'         => 'nullable|numeric|min:0',
            'account_id'             => 'nullable|string',
        ]);

        LoanApplication::create([
            'id'                     => (string) Str::uuid(),
            'customer_id'            => $customer->id,
            'tenant_id'              => $customer->tenant_id,
            'account_id'             => $request->account_id ?: null,
            'reference'              => 'APP-' . strtoupper(Str::random(10)),
            'loan_type'              => $request->loan_type,
            'requested_amount'       => $request->requested_amount,
            'requested_tenor_months' => $request->requested_tenor_months,
            'purpose'                => $request->purpose,
            'employment_status'      => $request->employment_status,
            'employer_name'          => $request->employer_name,
            'monthly_income'         => $request->monthly_income,
            'collateral_description' => $request->collateral_description,
            'collateral_value'       => $request->collateral_value,
            'status'                 => 'submitted',
        ]);

        return redirect()->route('loans.applications')
            ->with('success', 'Loan application submitted! Our team will review and contact you within 2–3 business days.');
    }

    public function cancel(string $id)
    {
        $customer = $this->customer();
        $app = LoanApplication::where('customer_id', $customer->id)
            ->whereIn('status', ['submitted', 'under_review'])
            ->findOrFail($id);
        $app->update(['status' => 'cancelled']);
        return back()->with('success', 'Application cancelled.');
    }
}
