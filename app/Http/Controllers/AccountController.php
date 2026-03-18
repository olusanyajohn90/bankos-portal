<?php
namespace App\Http\Controllers;

use App\Mail\OfficialStatementMail;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function show(string $accountId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->findOrFail($accountId);
        $recent   = $account->transactions()->latest()->limit(10)->get();

        return view('accounts.show', compact('account', 'recent'));
    }

    /**
     * Statement request page — choose PDF, Excel (free) or signed (fee, emailed).
     */
    public function statementRequest(string $accountId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->findOrFail($accountId);
        $fee      = config('portal.signed_statement_fee', 20);

        return view('accounts.statement', compact('account', 'fee'));
    }

    /**
     * Download unsigned PDF statement (free, instant).
     */
    public function statementDownloadPdf(Request $request, string $accountId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->findOrFail($accountId);
        $tenant   = $customer->tenant;

        $transactions = $account->transactions()
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
            ->get();

        $reference   = 'STMT-FREE-' . strtoupper(Str::random(6));
        $from        = $request->from ? \Carbon\Carbon::parse($request->from) : null;
        $to          = $request->to   ? \Carbon\Carbon::parse($request->to)   : null;
        $creditTypes = ['deposit', 'disbursement', 'interest'];
        $totalCredit = $transactions->whereIn('type', $creditTypes)->sum('amount');
        $totalDebit  = $transactions->whereNotIn('type', $creditTypes)->sum('amount');
        $official    = false;
        $fee         = 0;

        $pdf = Pdf::loadView('accounts.statement_print', compact(
            'account', 'customer', 'transactions', 'tenant',
            'reference', 'fee', 'from', 'to', 'totalCredit', 'totalDebit', 'official'
        ))->setPaper('a4', 'portrait');

        $filename = 'statement-' . $account->account_number . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download unsigned Excel statement (free, instant).
     */
    public function statementDownloadExcel(Request $request, string $accountId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->findOrFail($accountId);

        $transactions = $account->transactions()
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
            ->get();

        $filename = 'statement-' . $account->account_number . '-' . now()->format('Ymd') . '.xlsx';

        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($account, $transactions, $customer) {
            $tenant = $customer->tenant;
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [$tenant?->name ?? 'Account Statement']);
            fputcsv($handle, ['ACCOUNT STATEMENT (UNSIGNED)']);
            fputcsv($handle, ['Account Name',   $account->account_name]);
            fputcsv($handle, ['Account Number', $account->account_number]);
            fputcsv($handle, ['Currency',       $account->currency ?? 'NGN']);
            fputcsv($handle, ['Generated',      now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Date', 'Description', 'Type', 'Amount', 'CR/DR', 'Status', 'Reference']);

            $creditTypes = ['deposit', 'disbursement', 'interest'];
            foreach ($transactions as $txn) {
                $isCredit = in_array($txn->type, $creditTypes);
                fputcsv($handle, [
                    $txn->created_at?->format('d/m/Y H:i') ?? '',
                    $txn->description ?? '',
                    strtoupper($txn->type ?? ''),
                    number_format((float) $txn->amount, 2),
                    $isCredit ? 'CR' : 'DR',
                    $txn->status ?? '',
                    $txn->reference ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate bank-signed official statement (deducts fee, emails PDF to customer).
     */
    public function statementSigned(Request $request, string $accountId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->findOrFail($accountId);

        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $fee = config('portal.signed_statement_fee', 20);

        if ((float) $account->available_balance < $fee) {
            return back()->withErrors(['Insufficient balance. Statement fee: NGN ' . number_format($fee, 2)]);
        }

        if (empty($customer->email)) {
            return back()->withErrors(['No email address on file. Please update your profile before requesting an official statement.']);
        }

        $reference = 'STMT' . strtoupper(Str::random(8));

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
            'description'  => 'Account statement fee — Ref: ' . $reference,
            'status'       => 'success',
            'performed_by' => null, // portal-initiated; no admin user
        ]);

        $account->refresh();

        $transactions = $account->transactions()
            ->where('reference', '!=', $reference . '-FEE')
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
            ->get();

        $tenant      = $customer->tenant;
        $from        = $request->from ? \Carbon\Carbon::parse($request->from) : null;
        $to          = $request->to   ? \Carbon\Carbon::parse($request->to)   : null;
        $creditTypes = ['deposit', 'disbursement', 'interest'];
        $totalCredit = $transactions->whereIn('type', $creditTypes)->sum('amount');
        $totalDebit  = $transactions->whereNotIn('type', $creditTypes)->sum('amount');
        $official    = true;
        $forPdf      = true;

        $pdf = Pdf::loadView('accounts.statement_print', compact(
            'account', 'customer', 'transactions', 'tenant',
            'reference', 'fee', 'from', 'to', 'totalCredit', 'totalDebit', 'official', 'forPdf'
        ))->setPaper('a4', 'portrait');

        $pdfBytes = $pdf->output();

        Mail::to($customer->email)->send(new OfficialStatementMail(
            customer: $customer,
            account: $account,
            reference: $reference,
            fee: (float) $fee,
            pdfBytes: $pdfBytes,
        ));

        return redirect()
            ->route('accounts.statement', $accountId)
            ->with('success', 'Official statement emailed to ' . $customer->email . '. Reference: ' . $reference);
    }
}
