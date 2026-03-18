<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): mixed
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = count($parts) >= 2 ? $parts[0] : 'demo';

        $tenant = DB::table('tenants')->where('slug', $subdomain)->first();

        if (!$tenant) {
            // Fallback: use first tenant for local dev
            $tenant = DB::table('tenants')->first();
        }

        if ($tenant) {
            app()->instance('current_tenant', $tenant);
            config(['app.tenant_id' => $tenant->id]);
        }

        return $next($request);
    }
}
