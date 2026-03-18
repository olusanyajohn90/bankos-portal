<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class QrPaymentController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $accounts = $customer->accounts()->where('status', 'active')->get();
        return view('qr-payment.index', compact('accounts'));
    }
}
