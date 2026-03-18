<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class FeatureFlag
{
    /**
     * Check whether a feature is enabled for the given customer.
     *
     * Resolution order:
     *  1. Customer-specific override (customer_id = $customer->id)
     *  2. Tenant-wide flag          (customer_id IS NULL)
     *  3. Hard-coded default        (true — all features default to enabled)
     *
     * @param  string  $feature   Feature key, e.g. "portal_loans"
     * @param  object  $customer  Customer model (must have id, tenant_id)
     * @return bool
     */
    public static function check(string $feature, object $customer): bool
    {
        // 1. Customer-specific override
        $customerRow = DB::table('tenant_feature_flags')
            ->where('tenant_id', $customer->tenant_id)
            ->where('customer_id', $customer->id)
            ->where('feature_key', $feature)
            ->first();

        if ($customerRow !== null) {
            return (bool) $customerRow->is_enabled;
        }

        // 2. Tenant-wide flag
        $tenantRow = DB::table('tenant_feature_flags')
            ->where('tenant_id', $customer->tenant_id)
            ->whereNull('customer_id')
            ->where('feature_key', $feature)
            ->first();

        if ($tenantRow !== null) {
            return (bool) $tenantRow->is_enabled;
        }

        // 3. Default: enabled
        return true;
    }
}
