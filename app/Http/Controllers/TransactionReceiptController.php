<?php
namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionReceiptController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function download($id) {
        $customer    = $this->customer();
        $accountIds  = Account::where('customer_id',$customer->id)->pluck('id');
        $transaction = Transaction::whereIn('account_id',$accountIds)->findOrFail($id);
        $account     = Account::find($transaction->account_id);
        $tenant      = $customer->tenant;
        $pdf = Pdf::loadView('receipts.transaction',compact('transaction','account','customer','tenant'))
            ->setPaper([0,0,227,340],'portrait'); // thermal-style receipt
        return $pdf->download('receipt-'.$transaction->reference.'.pdf');
    }
}
