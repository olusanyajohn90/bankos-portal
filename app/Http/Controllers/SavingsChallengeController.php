<?php
namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\PortalSavingsChallenge;
use App\Models\SavingsPocket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavingsChallengeController extends Controller {
    private function customer() { return Auth::guard('customer')->user(); }

    public function index() {
        $customer   = $this->customer();
        $challenges = PortalSavingsChallenge::where('customer_id',$customer->id)
            ->orderByDesc('created_at')->get();
        $accounts   = Account::where('customer_id',$customer->id)->where('status','active')->get();
        $pockets    = SavingsPocket::where('customer_id',$customer->id)->where('status','active')->get();
        $templates  = PortalSavingsChallenge::$templates;
        return view('savings-challenges.index',compact('challenges','accounts','pockets','templates'));
    }

    public function store(Request $request) {
        $customer = $this->customer();
        $request->validate([
            'name'           => 'required|string|max:120',
            'emoji'          => 'nullable|string|max:10',
            'target_amount'  => 'required|numeric|min:1000',
            'amount_per_save'=> 'required|numeric|min:100',
            'frequency'      => 'required|in:daily,weekly,monthly',
            'account_id'     => 'required|uuid',
            'pocket_id'      => 'nullable|uuid',
        ]);
        Account::where('id',$request->account_id)
            ->where('customer_id',$customer->id)->firstOrFail();
        PortalSavingsChallenge::create([
            'id'             => (string)Str::uuid(),
            'customer_id'    => $customer->id,
            'tenant_id'      => $customer->tenant_id,
            'account_id'     => $request->account_id,
            'pocket_id'      => $request->pocket_id ?: null,
            'name'           => $request->name,
            'emoji'          => $request->emoji ?: '🏆',
            'target_amount'  => $request->target_amount,
            'amount_per_save'=> $request->amount_per_save,
            'frequency'      => $request->frequency,
            'start_date'     => now()->toDateString(),
        ]);
        return back()->with('success','Challenge "'.$request->name.'" started! Stay consistent.');
    }

    public function save($id) {
        $customer  = $this->customer();
        $challenge = PortalSavingsChallenge::where('id',$id)
            ->where('customer_id',$customer->id)->where('status','active')->firstOrFail();
        $account   = Account::findOrFail($challenge->account_id);

        if($account->available_balance < $challenge->amount_per_save)
            return back()->with('error','Insufficient balance to make this challenge save.');

        DB::transaction(function() use($challenge,$account,$customer) {
            $account->decrement('available_balance',$challenge->amount_per_save);
            $account->decrement('ledger_balance',$challenge->amount_per_save);
            $account->refresh();
            Transaction::create([
                'id'           => (string)Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'type'         => 'debit',
                'amount'       => $challenge->amount_per_save,
                'balance_after'=> $account->available_balance,
                'reference'    => 'CHG-'.strtoupper(Str::random(8)),
                'description'  => 'Savings Challenge: '.$challenge->name,
                'status'       => 'completed',
                'channel'      => 'portal',
            ]);
            if($challenge->pocket_id) {
                SavingsPocket::where('id',$challenge->pocket_id)->increment('balance',$challenge->amount_per_save);
            }
            $newAmount  = $challenge->current_amount + $challenge->amount_per_save;
            $isComplete = $newAmount >= $challenge->target_amount;
            $challenge->update([
                'current_amount' => min($newAmount,$challenge->target_amount),
                'streak_count'   => $challenge->streak_count + 1,
                'total_saves'    => $challenge->total_saves + 1,
                'status'         => $isComplete ? 'completed' : 'active',
                'completed_at'   => $isComplete ? now() : null,
            ]);
        });
        $challenge->refresh();
        if($challenge->status === 'completed')
            return back()->with('success','🎉 Challenge completed! You saved ₦'.number_format($challenge->target_amount,0).'!');
        return back()->with('success','✅ Saved ₦'.number_format($challenge->amount_per_save,0).'! Streak: '.$challenge->streak_count.' 🔥');
    }

    public function pause($id) {
        $customer = $this->customer();
        PortalSavingsChallenge::where('id',$id)->where('customer_id',$customer->id)
            ->update(['status'=>'paused']);
        return back()->with('success','Challenge paused.');
    }

    public function resume($id) {
        $customer = $this->customer();
        PortalSavingsChallenge::where('id',$id)->where('customer_id',$customer->id)
            ->where('status','paused')->update(['status'=>'active']);
        return back()->with('success','Challenge resumed!');
    }

    public function destroy($id) {
        $customer = $this->customer();
        PortalSavingsChallenge::where('id',$id)->where('customer_id',$customer->id)
            ->update(['status'=>'abandoned']);
        return back()->with('success','Challenge abandoned.');
    }
}
