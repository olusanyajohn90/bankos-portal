<?php
namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationInboxController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $notifications = PortalNotification::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->paginate(20);
        $unread = PortalNotification::unreadCount($customer->id);
        return view('notifications.inbox', compact('notifications', 'unread'));
    }

    public function markRead(string $id)
    {
        $customer = $this->customer();
        PortalNotification::where('customer_id', $customer->id)
            ->where('id', $id)->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back();
    }

    public function markAllRead()
    {
        $customer = $this->customer();
        PortalNotification::where('customer_id', $customer->id)
            ->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id)
    {
        $customer = $this->customer();
        PortalNotification::where('customer_id', $customer->id)->where('id', $id)->delete();
        return back();
    }
}
