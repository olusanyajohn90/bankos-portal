<?php
namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\PortalChequeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChequeRequestController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function index() {
        $customer = $this->customer();
        $accounts = Account::where('customer_id',$customer->id)
            ->whereIn('type',['current','corporate'])->where('status','active')->get();
        $requests = PortalChequeRequest::where('customer_id',$customer->id)
            ->orderByDesc('created_at')->get();
        return view('cheque-requests.index',compact('accounts','requests'));
    }

    public function store(Request $request) {
        $customer = $this->customer();
        $request->validate([
            'account_id'        => 'required|uuid',
            'book_type'         => 'required|in:25_leaves,50_leaves,100_leaves',
            'collection_method' => 'required|in:branch_pickup,home_delivery',
            'branch_name'       => 'required_if:collection_method,branch_pickup|nullable|string|max:120',
            'delivery_address'  => 'required_if:collection_method,home_delivery|nullable|string|max:500',
        ]);
        $pending = PortalChequeRequest::where('customer_id',$customer->id)
            ->whereIn('status',['pending','processing'])->count();
        if($pending >= 2)
            return back()->with('error','You already have a pending chequebook request. Please wait for it to be processed.');

        Account::where('id',$request->account_id)
            ->where('customer_id',$customer->id)->firstOrFail();
        PortalChequeRequest::create([
            'id'                => (string)Str::uuid(),
            'customer_id'       => $customer->id,
            'tenant_id'         => $customer->tenant_id,
            'account_id'        => $request->account_id,
            'reference'         => 'CHQ-'.strtoupper(Str::random(8)),
            'book_type'         => $request->book_type,
            'collection_method' => $request->collection_method,
            'branch_name'       => $request->branch_name,
            'delivery_address'  => $request->delivery_address,
        ]);
        return back()->with('success','Chequebook request submitted. We will notify you when ready (3-5 working days).');
    }

    public function destroy($id) {
        $customer = $this->customer();
        PortalChequeRequest::where('id',$id)->where('customer_id',$customer->id)
            ->where('status','pending')->firstOrFail()->update(['status'=>'cancelled']);
        return back()->with('success','Request cancelled.');
    }
}
