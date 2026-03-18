<?php

namespace App\Http\Middleware;

use App\Support\FeatureFlag;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * Usage on routes:  ->middleware('feature:portal_loan_apply')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $customer = auth('customer')->user();

        if ($customer && !FeatureFlag::check($feature, $customer)) {
            return redirect()->route('dashboard')
                ->with('error', 'This feature is not available for your account.');
        }

        return $next($request);
    }
}
