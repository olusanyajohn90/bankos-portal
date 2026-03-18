<?php
namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\PortalScheduledTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScheduledTransferController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function index() {
        $customer  = $this->customer();
        $accounts  = Account::where('customer_id',$customer->id)->where('status','active')->get();
        $scheduled = PortalScheduledTransfer::where('customer_id',$customer->id)
            ->orderByDesc('scheduled_at')->paginate(20);
        return view('scheduled-transfers.index',compact('accounts','scheduled'));
    }

    public function store(Request $request) {
        $customer = $this->customer();
        $request->validate([
            'account_id'          => 'required|uuid',
            'beneficiary_name'    => 'required|string|max:120',
            'beneficiary_account' => 'required|string|size:10',
            'bank_name'           => 'required|string|max:100',
            'amount'              => 'required|numeric|min:100',
            'narration'           => 'nullable|string|max:255',
            'scheduled_at'        => 'required|date|after:now',
        ]);
        Account::where('id',$request->account_id)
            ->where('customer_id',$customer->id)->where('status','active')->firstOrFail();

        PortalScheduledTransfer::create([
            'id'                   => (string)Str::uuid(),
            'customer_id'          => $customer->id,
            'tenant_id'            => $customer->tenant_id,
            'account_id'           => $request->account_id,
            'beneficiary_name'     => $request->beneficiary_name,
            'beneficiary_account'  => $request->beneficiary_account,
            'bank_code'            => '999',
            'bank_name'            => $request->bank_name,
            'amount'               => $request->amount,
            'narration'            => $request->narration,
            'scheduled_at'         => $request->scheduled_at,
            'status'               => 'pending',
        ]);
        return back()->with('success','Transfer scheduled for '.date('d M Y, H:i',strtotime($request->scheduled_at)).'.');
    }

    public function destroy($id) {
        $customer = $this->customer();
        PortalScheduledTransfer::where('id',$id)->where('customer_id',$customer->id)
            ->where('status','pending')->firstOrFail()->update(['status'=>'cancelled']);
        return back()->with('success','Scheduled transfer cancelled.');
    }
}
