<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        return view('profile.index', compact('customer'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function toggleDarkMode(Request $request)
    {
        // Dark mode is managed client-side via localStorage.
        // This endpoint exists for future server-side persistence.
        return response()->json(['ok' => true]);
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $customer->portal_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $customer->update(['portal_password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
