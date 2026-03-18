<?php
// Run: php artisan tinker --execute="require base_path('zainab_seed.php');"

$custId      = 'a14acc23-13da-444e-839c-b8266f9f08e7';
$tenantId    = 'a14a96f2-006d-4232-973e-9683ef578737';
$accCurrent  = '727815ab-e519-47d9-bdf5-483f0cb608a3'; // 1003853933 ₦2,617,000
$accSavings  = '978696b7-60b9-4f4d-963e-238c6bb6447a'; // 1005609646 ₦1,451,000
$accSavings2 = 'a14acc23-1574-4348-aa27-8bb67fa1c8a3'; // 1008796608 ₦73,434

// ── 1. PORTAL LOGIN CREDENTIALS ────────────────────────────────────
\DB::table('customers')->where('id', $custId)->update([
    'portal_password' => bcrypt('demo1234'),
    'portal_active'   => 1,
    'portal_pin'      => hash('sha256', '1234'),
    'kyc_tier'        => 'level_2',
    'kyc_status'      => 'approved',
    'status'          => 'active',
    'notify_email'    => 1, 'notify_sms' => 1, 'notify_push' => 1,
]);
echo "✓ Portal password: demo1234, PIN: 1234\n";

// ── 2. TRUSTED DEVICE (bypass OTP on login) ─────────────────────────
\DB::table('portal_known_devices')->where('customer_id', $custId)->delete();
\DB::table('portal_known_devices')->insert([
    'customer_id'        => $custId,
    'device_fingerprint' => hash('sha256', 'demo-device-for-client-meeting-2026'),
    'device_name'        => 'Chrome on Windows',
    'ip_address'         => '127.0.0.1',
    'first_seen_at'      => now()->subDays(30),
    'last_seen_at'       => now(),
    'trusted'            => true,
    'created_at'         => now()->subDays(30),
    'updated_at'         => now(),
]);
echo "✓ Trusted device seeded\n";

// ── 3. BENEFICIARIES ───────────────────────────────────────────────
\DB::table('beneficiaries')->where('customer_id', $custId)->delete();
$bens = [
    ['Emeka Okonkwo',            '0123456789', true,  null,  'Bank One',                5, 7],
    ['Fatima Bello',             '0987654321', true,  null,  'Bank One',                3, 10],
    ['Tunde Adeyemi',            '0234567890', false, '058', 'Guaranty Trust Bank',     8, 2],
    ['Kemi Oladele',             '1234567890', false, '044', 'Access Bank',             2, 20],
    ['Nonso Eze',                '5678901234', false, '057', 'Zenith Bank',             1, 45],
    ['Mum - Hajiya Amina Ahmed', '0891234567', true,  null,  'Bank One',               12, 1],
];
foreach ($bens as [$name, $acc, $intra, $code, $bank, $count, $daysAgo]) {
    \DB::table('beneficiaries')->insert([
        'id'               => \Str::uuid(),
        'customer_id'      => $custId,
        'tenant_id'        => $tenantId,
        'nickname'         => $name,
        'account_number'   => $acc,
        'account_name'     => $name,
        'is_intrabank'     => $intra,
        'bank_code'        => $code,
        'bank_name'        => $bank,
        'transfer_count'   => $count,
        'last_transfer_at' => now()->subDays($daysAgo),
        'created_at'       => now()->subDays(60),
        'updated_at'       => now()->subDays($daysAgo),
    ]);
}
echo "✓ 6 beneficiaries seeded\n";

// ── 4. VIRTUAL CARD ────────────────────────────────────────────────
\DB::table('virtual_cards')->where('customer_id', $custId)->delete();
\DB::table('virtual_cards')->insert([
    'id'                 => \Str::uuid(),
    'tenant_id'          => $tenantId,
    'account_id'         => $accCurrent,
    'customer_id'        => $custId,
    'card_last4'         => '4821',
    'card_number_masked' => '5399 **** **** 4821',
    'expiry_month'       => '09',
    'expiry_year'        => '28',
    'card_name'          => 'ZAINAB AHMED',
    'card_type'          => 'mastercard',
    'status'             => 'active',
    'card_pin'           => hash('sha256', '1234'),
    'pin_set_at'         => now()->subDays(20),
    'spending_limit'     => 500000.00,
    'spent_this_month'   => 87500.00,
    'created_at'         => now()->subDays(45),
    'updated_at'         => now(),
]);
echo "✓ Virtual card (Mastercard ***4821) seeded\n";

// ── 5. TRANSACTIONS ────────────────────────────────────────────────
\DB::table('transactions')
    ->whereIn('account_id', [$accCurrent, $accSavings, $accSavings2])
    ->whereNull('performed_by')
    ->delete();

$txns = [
    [$accCurrent, 'deposit',    450000,  'Salary Credit — March 2026 (Bank One MFB)',         1,  9, 0],
    [$accCurrent, 'transfer',   85000,   'NIP Transfer to GTB — Tunde Adeyemi',               2, 14, 30],
    [$accCurrent, 'withdrawal', 35000,   'Bill Pay — DSTV Compact Plus',                      3, 11, 0],
    [$accCurrent, 'withdrawal', 12500,   'Airtime — MTN 08012345678',                         4, 10, 15],
    [$accSavings, 'deposit',    50000,   'Standing Order Auto-Save',                           5,  8, 0],
    [$accCurrent, 'transfer',   220000,  'Transfer — Emeka Okonkwo (Rent)',                    7, 16, 45],
    [$accCurrent, 'deposit',    15000,   'Credit Alert — Freelance Payment',                  8, 13, 0],
    [$accCurrent, 'withdrawal', 8500,    'Bill Pay — IKEDC Electricity Prepaid',               9, 10, 0],
    [$accCurrent, 'transfer',   45000,   'Transfer — Fatima Bello',                           10, 11, 20],
    [$accSavings, 'deposit',    100000,  'Transfer from Current — Savings top-up',            11,  9, 0],
    [$accCurrent, 'transfer',   62000,   'NIP Transfer — Kemi Oladele (Access Bank)',         12, 15, 30],
    [$accCurrent, 'deposit',    25000,   'Credit Alert — Client Retainer',                   14, 10, 0],
    [$accCurrent, 'withdrawal', 18000,   'Bill Pay — Lagos State Water Corporation',          15, 11, 0],
    [$accCurrent, 'repayment',  55000,   'Loan Repayment — LNJUCZX8ZW',                      16, 12, 0],
    [$accSavings, 'deposit',    50000,   'Standing Order Auto-Save',                          20,  8, 0],
    [$accCurrent, 'deposit',    450000,  'Salary Credit — February 2026 (Bank One MFB)',      30,  9, 0],
    [$accCurrent, 'transfer',   125000,  'Transfer — School Fees BUA Academy',                32, 10, 0],
    [$accCurrent, 'withdrawal', 9200,    'Airtime — Airtel Data Bundle 10GB',                 33, 14, 0],
    [$accCurrent, 'transfer',   75000,   'NIP Transfer — Nonso Eze (Zenith Bank)',            35, 16, 30],
    [$accCurrent, 'deposit',    50000,   'Credit Alert — Freelance Design Project',           38, 11, 0],
    [$accCurrent, 'withdrawal', 32000,   'Bill Pay — Multichoice GOtv',                       40, 10, 0],
    [$accCurrent, 'deposit',    450000,  'Salary Credit — January 2026 (Bank One MFB)',       60,  9, 0],
    [$accSavings, 'deposit',    50000,   'Standing Order Auto-Save',                          50,  8, 0],
    [$accCurrent, 'withdrawal', 15000,   'ATM Withdrawal — GTB ATM Victoria Island',          42, 13, 0],
];
foreach ($txns as [$accId, $type, $amount, $desc, $daysAgo, $hour, $min]) {
    \DB::table('transactions')->insert([
        'id'           => \Str::uuid(),
        'tenant_id'    => $tenantId,
        'account_id'   => $accId,
        'reference'    => strtoupper(substr(md5(uniqid()), 0, 3)) . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        'type'         => $type,
        'amount'       => $amount,
        'currency'     => 'NGN',
        'description'  => $desc,
        'status'       => 'success',
        'performed_by' => null,
        'created_at'   => now()->subDays($daysAgo)->setTime($hour, $min),
        'updated_at'   => now()->subDays($daysAgo),
    ]);
}
echo "✓ 24 realistic transactions seeded\n";

// ── 6. BILL PAYMENTS ──────────────────────────────────────────────
\DB::table('bill_payments')->where('customer_id', $custId)->delete();
$bills = [
    ['electricity', 'IKEDC',         '54382901234', 8500,  'success', 9],
    ['electricity', 'IKEDC',         '54382901234', 8500,  'success', 40],
    ['cable_tv',    'DSTV',          'IUC-0098765', 35000, 'success', 3],
    ['cable_tv',    'GOtv',          'IUC-0043218', 32000, 'success', 40],
    ['water',       'Lagos Water',   'LWC-5553321', 18000, 'success', 15],
    ['internet',    'Smile Telecom', '07012345678', 22000, 'success', 22],
    ['internet',    'Smile Telecom', '07012345678', 22000, 'success', 52],
];
foreach ($bills as [$cat, $biller, $recip, $amount, $status, $daysAgo]) {
    \DB::table('bill_payments')->insert([
        'id'                 => \Str::uuid(),
        'tenant_id'          => $tenantId,
        'account_id'         => $accCurrent,
        'customer_id'        => $custId,
        'category'           => $cat,
        'biller'             => $biller,
        'recipient'          => $recip,
        'amount'             => $amount,
        'reference'          => 'BILL-' . strtoupper(\Str::random(8)),
        'external_reference' => 'EXT' . rand(1000000, 9999999),
        'token'              => $cat === 'electricity' ? rand(1000000000000000000, PHP_INT_MAX) : null,
        'status'             => $status,
        'created_at'         => now()->subDays($daysAgo)->setTime(rand(9,17), rand(0,59)),
        'updated_at'         => now()->subDays($daysAgo),
    ]);
}
echo "✓ 7 bill payments seeded\n";

// ── 7. AIRTIME ORDERS ─────────────────────────────────────────────
\DB::table('portal_airtime_orders')->where('customer_id', $custId)->delete();
$airtime = [
    ['airtime', '08012345678', 'MTN',   null,           12500, 'completed', 4],
    ['data',    '08012345678', 'MTN',   '10GB - 30 days', 5000, 'completed', 4],
    ['airtime', '09087654321', 'Airtel',null,           5000,  'completed', 18],
    ['data',    '09087654321', 'Airtel','5GB - 30 days',  3500, 'completed', 33],
    ['airtime', '08012345678', 'MTN',   null,           12500, 'completed', 35],
];
foreach ($airtime as [$type, $phone, $network, $plan, $amount, $status, $daysAgo]) {
    \DB::table('portal_airtime_orders')->insert([
        'id'          => \Str::uuid(),
        'customer_id' => $custId,
        'tenant_id'   => $tenantId,
        'account_id'  => $accCurrent,
        'type'        => $type,
        'phone'       => $phone,
        'network'     => $network,
        'data_plan'   => $plan,
        'amount'      => $amount,
        'reference'   => 'AIR-' . strtoupper(\Str::random(8)),
        'status'      => $status,
        'created_at'  => now()->subDays($daysAgo)->setTime(rand(8,20), rand(0,59)),
        'updated_at'  => now()->subDays($daysAgo),
    ]);
}
echo "✓ 5 airtime/data orders seeded\n";

// ── 8. INTERBANK TRANSFERS ────────────────────────────────────────
\DB::table('portal_interbank_transfers')->where('customer_id', $custId)->delete();
$ibTxns = [
    ['Tunde Adeyemi', '0234567890', 'Guaranty Trust Bank', '058', 85000, 'Transfer — Rent contribution',      'completed', 2],
    ['Kemi Oladele',  '1234567890', 'Access Bank',         '044', 62000, 'Business payment',                  'completed', 12],
    ['Nonso Eze',     '5678901234', 'Zenith Bank',         '057', 75000, 'School fees — BUA Academy balance', 'completed', 35],
];
foreach ($ibTxns as [$name, $acc, $bank, $code, $amount, $narration, $status, $daysAgo]) {
    \DB::table('portal_interbank_transfers')->insert([
        'id'                  => \Str::uuid(),
        'customer_id'         => $custId,
        'tenant_id'           => $tenantId,
        'account_id'          => $accCurrent,
        'beneficiary_name'    => $name,
        'beneficiary_account' => $acc,
        'beneficiary_bank'    => $bank,
        'bank_code'           => $code,
        'amount'              => $amount,
        'narration'           => $narration,
        'reference'           => 'NIP' . date('Ymd') . strtoupper(\Str::random(6)),
        'session_id'          => strtoupper(\Str::random(16)),
        'status'              => $status,
        'pin_verified_at'     => now()->subDays($daysAgo),
        'created_at'          => now()->subDays($daysAgo)->setTime(rand(9,17), rand(0,59)),
        'updated_at'          => now()->subDays($daysAgo),
    ]);
}
echo "✓ 3 interbank transfers seeded\n";

// ── 9. FIXED DEPOSIT INVESTMENT ───────────────────────────────────
\DB::table('portal_investments')->where('customer_id', $custId)->delete();
\DB::table('portal_investments')->insert([
    [
        'id'               => \Str::uuid(),
        'customer_id'      => $custId,
        'tenant_id'        => $tenantId,
        'account_id'       => $accSavings,
        'reference'        => 'INV-' . strtoupper(\Str::random(8)),
        'name'             => 'Emergency Fund FD',
        'principal'        => 500000.00,
        'interest_rate'    => 12.50,
        'duration_days'    => 90,
        'expected_interest'=> 15411.00,
        'maturity_amount'  => 515411.00,
        'start_date'       => now()->subDays(45)->toDateString(),
        'maturity_date'    => now()->addDays(45)->toDateString(),
        'status'           => 'active',
        'penalty_amount'   => 0,
        'created_at'       => now()->subDays(45),
        'updated_at'       => now()->subDays(45),
    ],
    [
        'id'               => \Str::uuid(),
        'customer_id'      => $custId,
        'tenant_id'        => $tenantId,
        'account_id'       => $accSavings,
        'reference'        => 'INV-' . strtoupper(\Str::random(8)),
        'name'             => 'Holiday Fund — Dec 2026',
        'principal'        => 200000.00,
        'interest_rate'    => 11.00,
        'duration_days'    => 180,
        'expected_interest'=> 10849.00,
        'maturity_amount'  => 210849.00,
        'start_date'       => now()->subDays(10)->toDateString(),
        'maturity_date'    => now()->addDays(170)->toDateString(),
        'status'           => 'active',
        'penalty_amount'   => 0,
        'created_at'       => now()->subDays(10),
        'updated_at'       => now()->subDays(10),
    ],
]);
echo "✓ 2 fixed deposit investments seeded\n";

// ── 10. BUDGETS ───────────────────────────────────────────────────
\DB::table('portal_budgets')->where('customer_id', $custId)->delete();
$budgets = [
    ['Food & Groceries', 80000,  '#16a34a'],
    ['Transport',        40000,  '#2563eb'],
    ['Utilities',        35000,  '#d97706'],
    ['Entertainment',    25000,  '#9333ea'],
    ['School/Education', 150000, '#0891b2'],
    ['Savings',          100000, '#059669'],
];
foreach ($budgets as [$cat, $limit, $color]) {
    \DB::table('portal_budgets')->insert([
        'id'            => \Str::uuid(),
        'customer_id'   => $custId,
        'tenant_id'     => $tenantId,
        'category'      => $cat,
        'monthly_limit' => $limit,
        'month'         => now()->format('Y-m'),
        'color_hex'     => $color,
        'created_at'    => now()->subDays(15),
        'updated_at'    => now()->subDays(15),
    ]);
}
echo "✓ 6 budget categories seeded\n";

// ── 11. SAVINGS CHALLENGE ─────────────────────────────────────────
\DB::table('portal_savings_challenges')->where('customer_id', $custId)->delete();
\DB::table('portal_savings_challenges')->insert([
    [
        'id'              => \Str::uuid(),
        'customer_id'     => $custId,
        'tenant_id'       => $tenantId,
        'account_id'      => $accSavings,
        'pocket_id'       => null,
        'name'            => 'New Laptop Fund',
        'emoji'           => '💻',
        'target_amount'   => 350000.00,
        'amount_per_save' => 25000.00,
        'frequency'       => 'weekly',
        'current_amount'  => 125000.00,
        'streak_count'    => 5,
        'total_saves'     => 5,
        'start_date'      => now()->subWeeks(6)->toDateString(),
        'target_date'     => now()->addWeeks(8)->toDateString(),
        'status'          => 'active',
        'created_at'      => now()->subWeeks(6),
        'updated_at'      => now()->subDays(3),
    ],
    [
        'id'              => \Str::uuid(),
        'customer_id'     => $custId,
        'tenant_id'       => $tenantId,
        'account_id'      => $accSavings,
        'pocket_id'       => null,
        'name'            => 'Eid Celebration Fund',
        'emoji'           => '🌙',
        'target_amount'   => 200000.00,
        'amount_per_save' => 20000.00,
        'frequency'       => 'monthly',
        'current_amount'  => 60000.00,
        'streak_count'    => 3,
        'total_saves'     => 3,
        'start_date'      => now()->subMonths(3)->toDateString(),
        'target_date'     => now()->addMonths(5)->toDateString(),
        'status'          => 'active',
        'created_at'      => now()->subMonths(3),
        'updated_at'      => now()->subDays(10),
    ],
]);
echo "✓ 2 savings challenges seeded\n";

// ── 12. STANDING ORDER (Scheduled Transfer) ───────────────────────
\DB::table('standing_orders')->where('source_account_id', $accCurrent)->delete();
\DB::table('standing_orders')->insert([
    'id'                        => \Str::uuid(),
    'tenant_id'                 => $tenantId,
    'source_account_id'         => $accCurrent,
    'beneficiary_account_number'=> '0987654321',
    'beneficiary_bank_code'     => null,
    'beneficiary_name'          => 'Savings Auto-Save',
    'internal_dest_account_id'  => $accSavings,
    'transfer_type'             => 'internal',
    'amount'                    => 50000.00,
    'narration'                 => 'Monthly Auto-Save to Savings Account',
    'frequency'                 => 'monthly',
    'start_date'                => now()->subMonths(3)->toDateString(),
    'end_date'                  => null,
    'next_run_date'             => now()->addDays(14)->toDateString(),
    'max_runs'                  => null,
    'runs_completed'            => 3,
    'last_run_at'               => now()->subDays(20),
    'status'                    => 'active',
    'created_at'                => now()->subMonths(3),
    'updated_at'                => now()->subDays(20),
]);
echo "✓ Standing order seeded\n";

// ── 13. CREDIT SCORE ──────────────────────────────────────────────
\DB::table('portal_credit_scores')->where('customer_id', $custId)->delete();
\DB::table('portal_credit_scores')->insert([
    'id'                   => \Str::uuid(),
    'customer_id'          => $custId,
    'tenant_id'            => $tenantId,
    'score'                => 718,
    'grade'                => 'B',
    'payment_history_score'=> 85,
    'utilization_score'    => 72,
    'account_age_score'    => 68,
    'account_mix_score'    => 75,
    'activity_score'       => 80,
    'factors'              => json_encode([
        'positive' => ['Consistent salary credits', 'Regular savings habit', 'No missed loan payments in 6 months'],
        'negative' => ['One overdue loan (LNJUCZX8ZW)', 'High credit utilization on card'],
    ]),
    'created_at'           => now()->subDays(7),
    'updated_at'           => now()->subDays(7),
]);
echo "✓ Credit score (718/B) seeded\n";

// ── 14. PORTAL DISPUTES ───────────────────────────────────────────
\DB::table('portal_disputes')->where('customer_id', $custId)->delete();
\DB::table('portal_disputes')->insert([
    [
        'id'                  => \Str::uuid(),
        'customer_id'         => $custId,
        'tenant_id'           => $tenantId,
        'account_id'          => $accCurrent,
        'transaction_id'      => null,
        'reference'           => 'DSP-' . strtoupper(\Str::random(8)),
        'type'                => 'double_charge',
        'description'         => 'Duplicate debit of ₦8,500 for IKEDC bill payment on March 9. Transaction appeared twice on my statement but power unit was only credited once.',
        'disputed_amount'     => 8500.00,
        'status'              => 'resolved',
        'admin_response'      => 'We investigated and confirmed a duplicate debit occurred. A reversal of ₦8,500 has been processed and will reflect in your account within 24 hours. We apologise for the inconvenience.',
        'resolution_notes'    => 'Reversal processed — duplicate debit confirmed.',
        'admin_responded_at'  => now()->subDays(8),
        'admin_responded_by'  => null,
        'resolved_at'         => now()->subDays(8),
        'created_at'          => now()->subDays(12),
        'updated_at'          => now()->subDays(8),
    ],
    [
        'id'                  => \Str::uuid(),
        'customer_id'         => $custId,
        'tenant_id'           => $tenantId,
        'account_id'          => $accCurrent,
        'transaction_id'      => null,
        'reference'           => 'DSP-' . strtoupper(\Str::random(8)),
        'type'                => 'failed_transfer',
        'description'         => 'Transferred ₦45,000 to Fatima Bello (Account: 0987654321) on March 6. The money was debited from my account but recipient has not received it. Please investigate urgently.',
        'disputed_amount'     => 45000.00,
        'status'              => 'investigating',
        'admin_response'      => null,
        'resolution_notes'    => null,
        'admin_responded_at'  => null,
        'admin_responded_by'  => null,
        'resolved_at'         => null,
        'created_at'          => now()->subDays(10),
        'updated_at'          => now()->subDays(10),
    ],
]);
echo "✓ 2 portal disputes seeded\n";

// ── 15. CHEQUE REQUEST ────────────────────────────────────────────
\DB::table('portal_cheque_requests')->where('customer_id', $custId)->delete();
\DB::table('portal_cheque_requests')->insert([
    'id'                => \Str::uuid(),
    'customer_id'       => $custId,
    'tenant_id'         => $tenantId,
    'account_id'        => $accCurrent,
    'reference'         => 'CHQ-' . strtoupper(\Str::random(8)),
    'book_type'         => '25_leaves',
    'collection_method' => 'branch_pickup',
    'delivery_address'  => null,
    'branch_name'       => 'Victoria Island Branch',
    'status'            => 'collected',
    'admin_notes'       => 'Collected by customer on ' . now()->subDays(25)->format('d M Y'),
    'ready_at'          => now()->subDays(27),
    'collected_at'      => now()->subDays(25),
    'created_at'        => now()->subDays(30),
    'updated_at'        => now()->subDays(25),
]);
echo "✓ Cheque book request seeded\n";

// ── 16. PORTAL NOTIFICATIONS ──────────────────────────────────────
\DB::table('portal_notifications')->where('customer_id', $custId)->delete();
$notifs = [
    ['credit_alert',    '💰', 'Salary Received',              'Your account 1003853933 has been credited with ₦450,000.00. New balance: ₦2,617,000.00',                  null,              1],
    ['debit_alert',     '💸', 'Transfer Successful',           'You sent ₦85,000.00 to Tunde Adeyemi (GTBank). Reference: NIP' . strtoupper(\Str::random(8)),             null,              2],
    ['investment',      '📈', 'Investment Update',             'Your Emergency Fund FD is performing well. Accrued interest: ₦3,082.00. Matures in 45 days.',             '/investments',    3],
    ['kyc',             '✅', 'KYC Tier 2 Approved',           'Congratulations! Your KYC upgrade to Tier 2 has been approved. You can now transact up to ₦500,000/day.', '/kyc/upgrade',    5],
    ['dispute',         '🔍', 'Dispute Under Investigation',   'Your dispute DSP reference for ₦45,000 transfer to Fatima Bello is being investigated by our team.',      '/disputes',       10],
    ['dispute',         '✅', 'Dispute Resolved',              'Your duplicate IKEDC debit dispute has been resolved. ₦8,500 reversal is being processed.',               '/disputes',       8],
    ['savings',         '🎯', 'Savings Milestone!',            'Great job! You\'ve saved ₦125,000 towards your Laptop Fund. You\'re 36% of the way there! Keep going!',   '/budget',         3],
    ['bill_payment',    '⚡', 'Electricity Token Delivered',   'Your IKEDC prepaid token has been sent to your registered email. Units: 45.2 kWh.',                       null,              9],
    ['card',            '💳', 'Virtual Card Activated',        'Your Mastercard virtual card ***4821 is now active and ready to use for online payments.',                 '/cards',          20],
    ['general',         '📢', 'New Feature: Savings Challenges','Set personal savings goals and track your progress. Try it now — create your first challenge!',           '/budget',         14],
    ['debit_alert',     '💸', 'Standing Order Executed',       'Monthly auto-save of ₦50,000 transferred from Current (1003853933) to Savings (1005609646).',             null,              20],
    ['credit_alert',    '💰', 'Salary Received',               'Your account 1003853933 has been credited with ₦450,000.00 (February Salary).',                          null,              30],
];
foreach ($notifs as [$type, $icon, $title, $body, $url, $daysAgo]) {
    \DB::table('portal_notifications')->insert([
        'id'          => \Str::uuid(),
        'customer_id' => $custId,
        'tenant_id'   => $tenantId,
        'type'        => $type,
        'icon'        => $icon,
        'title'       => $title,
        'body'        => $body,
        'data'        => null,
        'action_url'  => $url,
        'read_at'     => $daysAgo > 3 ? now()->subDays($daysAgo - 1) : null,
        'created_at'  => now()->subDays($daysAgo)->setTime(rand(8,20), rand(0,59)),
        'updated_at'  => now()->subDays($daysAgo),
    ]);
}
echo "✓ 12 portal notifications seeded\n";

// ── 17. LOAN TOP-UP REQUEST ───────────────────────────────────────
$loan = \DB::table('loans')->where('customer_id', $custId)->first();
if ($loan) {
    \DB::table('portal_loan_topup_requests')->where('customer_id', $custId)->delete();
    \DB::table('portal_loan_topup_requests')->insert([
        'id'               => \Str::uuid(),
        'customer_id'      => $custId,
        'tenant_id'        => $tenantId,
        'loan_id'          => $loan->id,
        'requested_amount' => 100000.00,
        'purpose'          => 'Business working capital — purchasing inventory for upcoming Eid season',
        'status'           => 'pending',
        'created_at'       => now()->subDays(3),
        'updated_at'       => now()->subDays(3),
    ]);
    echo "✓ Loan top-up request seeded\n";
} else {
    echo "⚠ No loan found — skipping top-up request\n";
}

echo "\n========================================\n";
echo "✅ ALL DEMO DATA SEEDED FOR ZAINAB AHMED\n";
echo "========================================\n";
echo "Login email: zainabahmed@gmail.com\n";
echo "Password:    demo1234\n";
echo "PIN:         1234\n";
echo "========================================\n";
