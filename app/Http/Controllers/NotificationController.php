<?php
namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function preferences()
    {
        $customer = $this->customer();
        $prefs    = NotificationPreference::forCustomer($customer->id, $customer->tenant_id);
        return view('notifications.preferences', compact('prefs'));
    }

    public function updatePreferences(Request $request)
    {
        $customer = $this->customer();
        $prefs    = NotificationPreference::forCustomer($customer->id, $customer->tenant_id);

        $request->validate([
            'low_balance_threshold' => 'nullable|numeric|min:0',
            'large_txn_threshold'   => 'nullable|numeric|min:0',
        ]);

        $prefs->update([
            'debit_alert'           => $request->boolean('debit_alert'),
            'credit_alert'          => $request->boolean('credit_alert'),
            'low_balance_alert'     => $request->boolean('low_balance_alert'),
            'low_balance_threshold' => $request->input('low_balance_threshold', 1000),
            'large_txn_alert'       => $request->boolean('large_txn_alert'),
            'large_txn_threshold'   => $request->input('large_txn_threshold', 50000),
            'loan_reminder'         => $request->boolean('loan_reminder'),
            'login_alert'           => $request->boolean('login_alert'),
            'monthly_summary'       => $request->boolean('monthly_summary'),
            'statement_ready'       => $request->boolean('statement_ready'),
            'weekly_statements'     => $request->boolean('weekly_statements'),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }

    public function pushSubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'nullable|string',
            'keys.auth'   => 'nullable|string',
        ]);

        $customer = $this->customer();

        // Upsert by endpoint so re-subscribing the same browser doesn't duplicate
        DB::table('portal_push_subscriptions')->upsert(
            [
                'customer_id' => $customer->id,
                'endpoint'    => $request->input('endpoint'),
                'p256dh'      => $request->input('keys.p256dh'),
                'auth'        => $request->input('keys.auth'),
                'user_agent'  => $request->userAgent(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            ['endpoint'],
            ['customer_id', 'p256dh', 'auth', 'user_agent', 'updated_at']
        );

        return response()->json(['success' => true]);
    }
}
