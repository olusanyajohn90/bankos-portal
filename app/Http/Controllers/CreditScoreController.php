<?php
namespace App\Http\Controllers;

use App\Models\PortalCreditScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreditScoreController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        // Get or compute score
        $latest = PortalCreditScore::where('customer_id', $customer->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderByDesc('created_at')->first();

        if (!$latest) {
            $data = PortalCreditScore::compute($customer);
            $latest = PortalCreditScore::create([
                'id'                    => (string) Str::uuid(),
                'customer_id'           => $customer->id,
                'tenant_id'             => $customer->tenant_id,
                'score'                 => $data['total'],
                'grade'                 => $data['grade'],
                'payment_history_score' => $data['paymentScore'],
                'utilization_score'     => $data['utilizationScore'],
                'account_age_score'     => $data['ageScore'],
                'account_mix_score'     => $data['mixScore'],
                'activity_score'        => $data['activityScore'],
                'factors'               => $data['factors'],
            ]);
        }

        $history = PortalCreditScore::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->limit(6)->get();

        return view('credit-score.index', compact('latest', 'history'));
    }
}
