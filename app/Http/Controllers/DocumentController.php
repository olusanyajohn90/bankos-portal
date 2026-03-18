<?php
namespace App\Http\Controllers;

use App\Mail\OfficialStatementMail;
use App\Models\CustomerDocument;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    private static array $types = [
        'confirmation_letter' => ['label' => 'Account Confirmation Letter', 'fee' => 0,  'desc' => 'Confirms your account exists with the bank. Accepted for ID verification, school enrollment, and KYC.'],
        'reference_letter'    => ['label' => 'Bank Reference Letter',       'fee' => 50, 'desc' => 'Official bank reference for tenancy agreements, business registration, embassy applications.'],
        'loan_clearance'      => ['label' => 'Loan Clearance Letter',       'fee' => 0,  'desc' => 'Confirms you have no outstanding loans or that all loans are fully repaid.'],
    ];

    public function index()
    {
        $customer  = $this->customer();
        $documents = CustomerDocument::where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get();

        return view('documents.index', compact('documents'), ['docTypes' => self::$types]);
    }

    public function requestForm(string $type)
    {
        if (!isset(self::$types[$type])) abort(404);

        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $docType  = self::$types[$type];

        return view('documents.request', compact('type', 'docType', 'accounts'));
    }

    public function generate(Request $request, string $type)
    {
        if (!isset(self::$types[$type])) abort(404);

        $customer = $this->customer();
        $request->validate(['account_id' => 'required|string']);

        $account  = $customer->accounts()->findOrFail($request->account_id);
        $tenant   = $customer->tenant;
        $docType  = self::$types[$type];
        $fee      = (float) $docType['fee'];
        $reference = strtoupper(Str::random(3)) . '-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));

        if ($fee > 0) {
            if ((float) $account->available_balance < $fee) {
                return back()->withErrors(['Insufficient balance. Document fee: NGN ' . number_format($fee, 2)]);
            }
            $account->decrement('available_balance', $fee);
            $account->decrement('ledger_balance', $fee);
            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => $reference . '-FEE',
                'type'         => 'fee',
                'amount'       => $fee,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => $docType['label'] . ' fee — Ref: ' . $reference,
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);
        }

        // Generate PDF
        $pdf = Pdf::loadView('documents.templates.' . $type, compact('account', 'customer', 'tenant', 'reference', 'fee'))
            ->setPaper('a4', 'portrait');

        // Log document
        CustomerDocument::create([
            'id'          => (string) Str::uuid(),
            'tenant_id'   => $customer->tenant_id,
            'account_id'  => $account->id,
            'customer_id' => $customer->id,
            'type'        => $type,
            'label'       => $docType['label'],
            'reference'   => $reference,
            'fee'         => $fee,
            'status'      => 'generated',
        ]);

        // Email to customer if they have an email
        if ($customer->email) {
            try {
                Mail::to($customer->email)->send(new OfficialStatementMail(
                    customer: $customer,
                    account: $account,
                    reference: $reference,
                    fee: $fee,
                    pdfBytes: $pdf->output(),
                ));
            } catch (\Throwable) {}
        }

        return $pdf->download($type . '-' . $reference . '.pdf');
    }

    public function download(string $docId)
    {
        $customer = $this->customer();
        $doc      = CustomerDocument::where('customer_id', $customer->id)->findOrFail($docId);
        $account  = $customer->accounts()->findOrFail($doc->account_id);
        $tenant   = $customer->tenant;
        $reference = $doc->reference;
        $fee       = $doc->fee;

        $pdf = Pdf::loadView('documents.templates.' . $doc->type, compact('account', 'customer', 'tenant', 'reference', 'fee'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($doc->type . '-' . $reference . '.pdf');
    }
}
