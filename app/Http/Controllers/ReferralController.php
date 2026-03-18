<?php
namespace App\Http\Controllers;

use App\Models\PortalReferral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        // Generate referral code if not set
        if (!$customer->referral_code) {
            $code = strtoupper(Str::random(8));
            $customer->update(['referral_code' => $code]);
        }

        $referrals = PortalReferral::where('referrer_customer_id', $customer->id)
            ->orderByDesc('created_at')->get();

        $totalEarned   = $referrals->where('status', 'rewarded')->sum('reward_amount');
        $pendingCount  = $referrals->whereIn('status', ['pending', 'registered'])->count();

        return view('referral.index', compact('customer', 'referrals', 'totalEarned', 'pendingCount'));
    }
}
