<?php
namespace App\Http\Controllers;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountOpeningController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function index() {
        $customer = $this->customer();
        $existing = Account::where('customer_id',$customer->id)->where('status','active')->get();
        $accountTypes = [
            ['type'=>'savings',     'label'=>'Savings Account',      'icon'=>'savings',    'desc'=>'Earn interest on deposits. Daily interest computation.','min_bal'=>0,'interest'=>'4% p.a.'],
            ['type'=>'current',     'label'=>'Current Account',      'icon'=>'current',    'desc'=>'Chequebook, unlimited transactions, no withdrawal limits.','min_bal'=>5000,'interest'=>'0%'],
            ['type'=>'domiciliary', 'label'=>'Domiciliary Account',  'icon'=>'domiciliary','desc'=>'Hold USD, GBP, EUR. Receive foreign currency transfers.','min_bal'=>0,'interest'=>'0%'],
            ['type'=>'kids',        'label'=>'Kids / Minor Account', 'icon'=>'kids',       'desc'=>'Save for your child\'s future. Parent-controlled.','min_bal'=>0,'interest'=>'5% p.a.'],
        ];
        return view('account-opening.index',compact('existing','accountTypes'));
    }

    public function store(Request $request) {
        $customer = $this->customer();
        $request->validate([
            'account_type' => 'required|in:savings,current,domiciliary,kids',
            'purpose'      => 'nullable|string|max:255',
        ]);
        $alreadyHas = Account::where('customer_id',$customer->id)
            ->where('type',$request->account_type)->where('status','active')->exists();
        if($alreadyHas)
            return back()->with('error','You already have an active '.ucfirst($request->account_type).' account.');

        DB::transaction(function() use($request,$customer) {
            $number = '20'.str_pad(mt_rand(0,99999999),8,'0',STR_PAD_LEFT);
            Account::create([
                'id'                => (string)Str::uuid(),
                'tenant_id'         => $customer->tenant_id,
                'customer_id'       => $customer->id,
                'account_number'    => $number,
                'account_name'      => strtoupper($customer->first_name.' '.$customer->last_name),
                'type'              => $request->account_type,
                'currency'          => $request->account_type === 'domiciliary' ? 'USD' : 'NGN',
                'available_balance' => 0,
                'ledger_balance'    => 0,
                'status'            => 'active',
            ]);
        });
        $label = ['savings'=>'Savings','current'=>'Current','domiciliary'=>'Domiciliary','kids'=>'Kids'][$request->account_type];
        return back()->with('success','Your new '.$label.' Account has been opened successfully!');
    }
}
