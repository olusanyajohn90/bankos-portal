<?php
namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\PortalAirtimeOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AirtimeController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function index() {
        $customer = $this->customer();
        $accounts = Account::where('customer_id',$customer->id)->where('status','active')->get();
        $history  = PortalAirtimeOrder::where('customer_id',$customer->id)
            ->orderByDesc('created_at')->limit(20)->get();
        $networks = PortalAirtimeOrder::$networks;
        $dataPlans= PortalAirtimeOrder::$dataPlans;
        return view('airtime.index',compact('accounts','history','networks','dataPlans'));
    }

    public function store(Request $request) {
        $customer = $this->customer();
        $request->validate([
            'type'      => 'required|in:airtime,data',
            'account_id'=> 'required|uuid',
            'phone'     => 'required|string|min:11|max:14',
            'network'   => 'required|in:mtn,airtel,glo,9mobile',
            'amount'    => 'required|numeric|min:50|max:50000',
            'data_plan' => 'nullable|string|max:80',
        ]);
        $account = Account::where('id',$request->account_id)
            ->where('customer_id',$customer->id)->where('status','active')->firstOrFail();

        if($account->available_balance < $request->amount)
            return back()->with('error','Insufficient balance in selected account.');

        DB::transaction(function() use($request,$account,$customer) {
            $ref = 'AIR-'.strtoupper(Str::random(10));
            $account->decrement('available_balance',$request->amount);
            $account->decrement('ledger_balance',$request->amount);
            $account->refresh();
            Transaction::create([
                'id'           => (string)Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'type'         => 'debit',
                'amount'       => $request->amount,
                'balance_after'=> $account->available_balance,
                'reference'    => $ref,
                'description'  => ($request->type==='data' ? 'Data: '.$request->data_plan.' ' : 'Airtime ').
                                   'for '.$request->phone.' ('.strtoupper($request->network).')',
                'status'       => 'completed',
                'channel'      => 'portal',
            ]);
            PortalAirtimeOrder::create([
                'id'          => (string)Str::uuid(),
                'customer_id' => $customer->id,
                'tenant_id'   => $customer->tenant_id,
                'account_id'  => $account->id,
                'type'        => $request->type,
                'phone'       => $request->phone,
                'network'     => $request->network,
                'data_plan'   => $request->data_plan,
                'amount'      => $request->amount,
                'reference'   => $ref,
                'status'      => 'completed',
            ]);
        });
        return back()->with('success','✅ '.ucfirst($request->type).' of ₦'.number_format($request->amount,0).' sent to '.$request->phone.'!');
    }
}
