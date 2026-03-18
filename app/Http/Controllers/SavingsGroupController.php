<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SavingsGroupController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index()
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        // Groups customer is a member of (via portal_savings_group_members)
        $myGroupIds = DB::table('portal_savings_group_members')
            ->where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->pluck('group_id');

        $myGroups = DB::table('portal_savings_groups')
            ->whereIn('id', $myGroupIds)
            ->where('tenant_id', $customer->tenant_id)
            ->orderByDesc('created_at')
            ->get();

        // Enrich with member count
        $myGroups = $myGroups->map(function ($g) {
            $g->member_count = DB::table('portal_savings_group_members')
                ->where('group_id', $g->id)
                ->where('status', 'active')
                ->count();
            return $g;
        });

        // Available groups: forming, not full, not already joined
        $availableGroups = DB::table('portal_savings_groups')
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'forming')
            ->whereNotIn('id', $myGroupIds)
            ->orderByDesc('created_at')
            ->get();

        $availableGroups = $availableGroups->map(function ($g) {
            $g->member_count = DB::table('portal_savings_group_members')
                ->where('group_id', $g->id)
                ->where('status', 'active')
                ->count();
            return $g;
        })->filter(function ($g) {
            return $g->member_count < $g->max_members;
        })->values();

        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('savings-groups.index', compact('myGroups', 'availableGroups', 'accounts'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('savings-groups.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $validated = $request->validate([
            'name'                => 'required|string|max:120',
            'description'         => 'nullable|string|max:500',
            'contribution_amount' => 'required|numeric|min:100',
            'frequency'           => 'required|in:daily,weekly,monthly',
            'max_members'         => 'required|integer|min:2|max:20',
        ]);

        $groupId = (string) Str::uuid();
        $maxMembers = (int) $validated['max_members'];

        DB::transaction(function () use ($customer, $validated, $groupId, $maxMembers) {
            DB::table('portal_savings_groups')->insert([
                'id'                  => $groupId,
                'tenant_id'           => $customer->tenant_id,
                'creator_id'          => $customer->id,
                'name'                => $validated['name'],
                'description'         => $validated['description'] ?? null,
                'contribution_amount' => $validated['contribution_amount'],
                'frequency'           => $validated['frequency'],
                'max_members'         => $maxMembers,
                'current_cycle'       => 1,
                'total_cycles'        => $maxMembers,
                'status'              => 'forming',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Add creator as first member
            DB::table('portal_savings_group_members')->insert([
                'group_id'       => $groupId,
                'customer_id'    => $customer->id,
                'tenant_id'      => $customer->tenant_id,
                'payout_position'=> 1,
                'joined_at'      => now(),
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->route('savings-groups.show', $groupId)
            ->with('success', 'Group "' . $validated['name'] . '" created! Share it with members to join.');
    }

    public function show(string $groupId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $group = DB::table('portal_savings_groups')
            ->where('id', $groupId)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$group) {
            abort(404);
        }

        $members = DB::table('portal_savings_group_members as m')
            ->join('customers as c', 'c.id', '=', 'm.customer_id')
            ->where('m.group_id', $groupId)
            ->select(
                'm.id',
                'm.customer_id',
                'm.payout_position',
                'm.status',
                'm.account_id',
                'm.joined_at',
                'c.first_name',
                'c.last_name',
                'c.email'
            )
            ->orderBy('m.payout_position')
            ->get();

        // Contribution counts per member
        $contribCounts = DB::table('portal_savings_group_contributions')
            ->where('group_id', $groupId)
            ->where('status', 'paid')
            ->groupBy('member_id')
            ->selectRaw('member_id, count(*) as cnt')
            ->pluck('cnt', 'member_id');

        $members = $members->map(function ($m) use ($contribCounts) {
            $m->contributions_count = $contribCounts[$m->id] ?? 0;
            return $m;
        });

        $contributions = DB::table('portal_savings_group_contributions as c')
            ->join('customers as cu', 'cu.id', '=', 'c.customer_id')
            ->where('c.group_id', $groupId)
            ->select(
                'c.id',
                'c.amount',
                'c.cycle_number',
                'c.reference',
                'c.status',
                'c.paid_at',
                'c.created_at',
                'cu.first_name',
                'cu.last_name'
            )
            ->orderByDesc('c.created_at')
            ->limit(50)
            ->get();

        $myMembership = DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('customer_id', $customer->id)
            ->first();

        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('savings-groups.show', compact('group', 'members', 'contributions', 'myMembership', 'accounts'));
    }

    public function join(Request $request, string $groupId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $group = DB::table('portal_savings_groups')
            ->where('id', $groupId)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$group) {
            abort(404);
        }

        if ($group->status !== 'forming') {
            return back()->withErrors(['This group is no longer accepting new members.']);
        }

        // Check already a member
        $alreadyMember = DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('customer_id', $customer->id)
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors(['You are already a member of this group.']);
        }

        // Check not full
        $memberCount = DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('status', 'active')
            ->count();

        if ($memberCount >= $group->max_members) {
            return back()->withErrors(['This group is already full.']);
        }

        $newPosition = $memberCount + 1;

        DB::transaction(function () use ($customer, $groupId, $group, $newPosition, $memberCount) {
            DB::table('portal_savings_group_members')->insert([
                'group_id'        => $groupId,
                'customer_id'     => $customer->id,
                'tenant_id'       => $customer->tenant_id,
                'payout_position' => $newPosition,
                'joined_at'       => now(),
                'status'          => 'active',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // If group is now full, activate it
            if ($newPosition >= $group->max_members) {
                DB::table('portal_savings_groups')
                    ->where('id', $groupId)
                    ->update([
                        'status'     => 'active',
                        'updated_at' => now(),
                    ]);
            }
        });

        return redirect()->route('savings-groups.show', $groupId)
            ->with('success', 'You have joined the group!');
    }

    public function contribute(Request $request, string $groupId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $validated = $request->validate([
            'account_id' => 'required|string',
            'pin'        => 'required|digits:4',
        ]);

        // Verify PIN
        if (!$customer->portal_pin || !Hash::check($validated['pin'], $customer->portal_pin)) {
            return back()->withErrors(['pin' => 'Incorrect transaction PIN.']);
        }

        $group = DB::table('portal_savings_groups')
            ->where('id', $groupId)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$group || $group->status !== 'active') {
            return back()->withErrors(['This group is not accepting contributions right now.']);
        }

        $membership = DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            return back()->withErrors(['You are not an active member of this group.']);
        }

        // Check if already contributed this cycle
        $alreadyPaid = DB::table('portal_savings_group_contributions')
            ->where('group_id', $groupId)
            ->where('member_id', $membership->id)
            ->where('cycle_number', $group->current_cycle)
            ->where('status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return back()->withErrors(['You have already contributed for cycle ' . $group->current_cycle . '.']);
        }

        $account = $customer->accounts()
            ->where('status', 'active')
            ->find($validated['account_id']);

        if (!$account) {
            return back()->withErrors(['account_id' => 'Invalid account selected.']);
        }

        $amount = (float) $group->contribution_amount;

        if ((float) $account->available_balance < $amount) {
            return back()->withErrors(['Insufficient balance in selected account.']);
        }

        $reference = 'SGC-' . strtoupper(Str::random(10));
        $contributionId = (string) Str::uuid();

        DB::transaction(function () use (
            $customer, $group, $groupId, $membership, $account,
            $amount, $reference, $contributionId
        ) {
            // Debit account
            $account->decrement('available_balance', $amount);
            $account->decrement('ledger_balance', $amount);

            // Record contribution
            DB::table('portal_savings_group_contributions')->insert([
                'id'           => $contributionId,
                'group_id'     => $groupId,
                'member_id'    => $membership->id,
                'customer_id'  => $customer->id,
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'amount'       => $amount,
                'cycle_number' => $group->current_cycle,
                'reference'    => $reference,
                'status'       => 'paid',
                'paid_at'      => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Record transaction ledger
            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => $reference . '-DR',
                'type'         => 'transfer',
                'amount'       => $amount,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => 'Group savings contribution — ' . $group->name . ' (Cycle ' . $group->current_cycle . ')',
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);

            // Check if all active members paid this cycle
            $activeMembers = DB::table('portal_savings_group_members')
                ->where('group_id', $groupId)
                ->where('status', 'active')
                ->count();

            $paidThisCycle = DB::table('portal_savings_group_contributions')
                ->where('group_id', $groupId)
                ->where('cycle_number', $group->current_cycle)
                ->where('status', 'paid')
                ->count();

            if ($paidThisCycle >= $activeMembers) {
                // Find the recipient for this cycle (member with payout_position == current_cycle)
                $recipient = DB::table('portal_savings_group_members')
                    ->where('group_id', $groupId)
                    ->where('payout_position', $group->current_cycle)
                    ->where('status', 'active')
                    ->first();

                if ($recipient && $recipient->account_id) {
                    $payoutAmount = $amount * $activeMembers;
                    $payoutAccount = Account::find($recipient->account_id);

                    if ($payoutAccount) {
                        $payoutAccount->increment('available_balance', $payoutAmount);
                        $payoutAccount->increment('ledger_balance', $payoutAmount);

                        Transaction::create([
                            'id'           => (string) Str::uuid(),
                            'tenant_id'    => $customer->tenant_id,
                            'account_id'   => $payoutAccount->id,
                            'reference'    => 'SGP-' . strtoupper(Str::random(10)),
                            'type'         => 'transfer',
                            'amount'       => $payoutAmount,
                            'currency'     => $payoutAccount->currency ?? 'NGN',
                            'description'  => 'Group savings payout — ' . $group->name . ' (Cycle ' . $group->current_cycle . ')',
                            'status'       => 'success',
                            'performed_by' => $recipient->customer_id,
                        ]);
                    }
                }

                // Advance cycle or complete group
                $nextCycle = $group->current_cycle + 1;
                if ($nextCycle > $group->total_cycles) {
                    DB::table('portal_savings_groups')
                        ->where('id', $groupId)
                        ->update(['status' => 'completed', 'updated_at' => now()]);
                } else {
                    DB::table('portal_savings_groups')
                        ->where('id', $groupId)
                        ->update([
                            'current_cycle' => $nextCycle,
                            'updated_at'    => now(),
                        ]);
                }
            }
        });

        return back()->with('success', 'Contribution of NGN ' . number_format($amount, 2) . ' recorded. Ref: ' . $reference);
    }

    public function leave(string $groupId)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $group = DB::table('portal_savings_groups')
            ->where('id', $groupId)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$group) {
            abort(404);
        }

        if ($group->status !== 'forming') {
            return back()->withErrors(['You can only leave a group that is still forming.']);
        }

        $membership = DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$membership) {
            return back()->withErrors(['You are not a member of this group.']);
        }

        // Cannot leave if already contributed
        $hasContributed = DB::table('portal_savings_group_contributions')
            ->where('group_id', $groupId)
            ->where('member_id', $membership->id)
            ->exists();

        if ($hasContributed) {
            return back()->withErrors(['You cannot leave a group after making contributions.']);
        }

        DB::table('portal_savings_group_members')
            ->where('group_id', $groupId)
            ->where('customer_id', $customer->id)
            ->update(['status' => 'withdrawn', 'updated_at' => now()]);

        return redirect()->route('savings-groups.index')
            ->with('success', 'You have left the group.');
    }
}
