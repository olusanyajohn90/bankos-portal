<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountOpeningController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\DeviceVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BulkTransferController;
use App\Http\Controllers\InterbankTransferController;
use App\Http\Controllers\SavingsGroupController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ChequeRequestController;
use App\Http\Controllers\CreditScoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FxRatesController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\KycUpgradeController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationInboxController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SavingsChallengeController;
use App\Http\Controllers\ScheduledTransferController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\StandingOrderController;
use App\Http\Controllers\TransactionReceiptController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\VirtualCardController;
use App\Http\Controllers\LoanCalculatorController;
use App\Http\Controllers\QrPaymentController;
use App\Http\Controllers\SplitBillController;
use App\Http\Controllers\PhysicalCardController;
use App\Http\Controllers\PortalOverdraftController;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', fn() => view('landing'))->name('home');

// Self-onboarding wizard (public — no auth required)
Route::prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/',        [OnboardingController::class, 'start'])->name('start');
    Route::post('/step1',  [OnboardingController::class, 'storeStep1'])->name('step1');
    Route::get('/step2',   [OnboardingController::class, 'step2'])->name('step2');
    Route::post('/step2',  [OnboardingController::class, 'storeStep2'])->name('step2.store');
    Route::get('/step3',   [OnboardingController::class, 'step3'])->name('step3');
    Route::post('/step3',  [OnboardingController::class, 'storeStep3'])->name('step3.store');
    Route::get('/step4',   [OnboardingController::class, 'step4'])->name('step4');
    Route::post('/step4',  [OnboardingController::class, 'storeStep4'])->name('step4.store');
    Route::get('/step5',   [OnboardingController::class, 'step5'])->name('step5');
    Route::post('/step5',  [OnboardingController::class, 'storeStep5'])->name('step5.store');
    Route::get('/review',  [OnboardingController::class, 'review'])->name('review');
    Route::post('/submit', [OnboardingController::class, 'submit'])->name('submit');
});

// Auth routes
Route::middleware('guest:customer')->group(function () {
    Route::get('/login',            [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',           [LoginController::class, 'login']);
    Route::get('/login/2fa',         [TwoFactorController::class, 'show'])->name('login.2fa');
    Route::post('/login/2fa',        [TwoFactorController::class, 'verify'])->name('login.2fa.verify');
    Route::get('/login/verify-device',  [DeviceVerificationController::class, 'show'])->name('login.device-verify');
    Route::post('/login/verify-device', [DeviceVerificationController::class, 'verify'])->name('login.device-verify.submit');
    Route::post('/login/resend-otp',    [DeviceVerificationController::class, 'resend'])->name('login.device-verify.resend');
    Route::get('/forgot-password',  [PasswordResetController::class, 'showForgot'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendReset'])->name('password.forgot.send');
    Route::get('/reset-password',   [PasswordResetController::class, 'showReset'])->name('password.reset.form');
    Route::post('/reset-password',  [PasswordResetController::class, 'reset'])->name('password.reset');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public payment request page (no auth needed — anyone can pay)
Route::get('/p/{reference}', [PaymentRequestController::class, 'publicShow'])->name('pay-request.public');
Route::post('/p/{reference}/pay', [PaymentRequestController::class, 'publicPay'])->name('pay-request.public.pay');

// Authenticated customer routes
Route::middleware(['auth:customer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Accounts
    Route::get('/accounts/{accountId}', [AccountController::class, 'show'])->name('accounts.show');
    Route::get('/accounts/{accountId}/transactions', [DashboardController::class, 'transactions'])->name('transactions');
    Route::get('/accounts/{accountId}/statement', [AccountController::class, 'statementRequest'])->name('accounts.statement');
    Route::get('/accounts/{accountId}/statement/pdf', [AccountController::class, 'statementDownloadPdf'])->name('accounts.statement.pdf');
    Route::get('/accounts/{accountId}/statement/excel', [AccountController::class, 'statementDownloadExcel'])->name('accounts.statement.excel');
    Route::post('/accounts/{accountId}/statement/signed', [AccountController::class, 'statementSigned'])->name('accounts.statement.signed');
    Route::post('/accounts/{accountId}/settings', [AccountSettingsController::class, 'update'])->name('accounts.settings');
    Route::post('/accounts/{accountId}/freeze', [AccountSettingsController::class, 'freeze'])->name('accounts.freeze');
    Route::post('/accounts/{accountId}/unfreeze', [AccountSettingsController::class, 'unfreeze'])->name('accounts.unfreeze');

    // Transfer
    Route::get('/transfer', [TransferController::class, 'create'])->name('transfer');
    Route::post('/transfer', [TransferController::class, 'store'])->name('transfer.store');
    Route::get('/transfer/lookup', [TransferController::class, 'lookup'])->name('transfer.lookup');

    // Savings Pockets
    Route::get('/savings', [SavingsController::class, 'index'])->name('savings')->middleware('feature:portal_savings_pockets');
    Route::get('/savings/create', [SavingsController::class, 'create'])->name('savings.create')->middleware('feature:portal_savings_pockets');
    Route::post('/savings', [SavingsController::class, 'store'])->name('savings.store')->middleware('feature:portal_savings_pockets');
    Route::get('/savings/{pocketId}', [SavingsController::class, 'show'])->name('savings.show')->middleware('feature:portal_savings_pockets');
    Route::post('/savings/{pocketId}/deposit', [SavingsController::class, 'deposit'])->name('savings.deposit')->middleware('feature:portal_savings_pockets');
    Route::post('/savings/{pocketId}/withdraw', [SavingsController::class, 'withdraw'])->name('savings.withdraw')->middleware('feature:portal_savings_pockets');
    Route::delete('/savings/{pocketId}', [SavingsController::class, 'destroy'])->name('savings.destroy')->middleware('feature:portal_savings_pockets');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');

    // Beneficiaries
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::delete('/beneficiaries/{id}', [BeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');

    // Virtual Cards
    Route::get('/cards', [VirtualCardController::class, 'index'])->name('cards')->middleware('feature:portal_virtual_cards');
    Route::post('/cards', [VirtualCardController::class, 'store'])->name('cards.store')->middleware('feature:portal_virtual_cards');
    Route::post('/cards/{cardId}/freeze', [VirtualCardController::class, 'freeze'])->name('cards.freeze')->middleware('feature:portal_virtual_cards');
    Route::post('/cards/{cardId}/unfreeze', [VirtualCardController::class, 'unfreeze'])->name('cards.unfreeze')->middleware('feature:portal_virtual_cards');
    Route::post('/cards/{cardId}/limit', [VirtualCardController::class, 'setLimit'])->name('cards.limit')->middleware('feature:portal_virtual_cards');
    Route::delete('/cards/{cardId}', [VirtualCardController::class, 'destroy'])->name('cards.destroy')->middleware('feature:portal_virtual_cards');

    // Document Centre
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
    Route::get('/documents/request/{type}', [DocumentController::class, 'requestForm'])->name('documents.request');
    Route::post('/documents/request/{type}', [DocumentController::class, 'generate'])->name('documents.generate');
    Route::get('/documents/{docId}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Security Centre
    Route::get('/security', [SecurityController::class, 'index'])->name('security');
    Route::post('/security/logout-all', [SecurityController::class, 'logoutAll'])->name('security.logout-all');
    Route::post('/security/pin', [SecurityController::class, 'updatePin'])->name('security.pin');
    Route::post('/security/2fa/enable', [SecurityController::class, 'enable2fa'])->name('security.2fa.enable');
    Route::post('/security/2fa/confirm', [SecurityController::class, 'confirm2fa'])->name('security.2fa.confirm');
    Route::post('/security/2fa/disable', [SecurityController::class, 'disable2fa'])->name('security.2fa.disable');

    // Bill Payments
    Route::get('/bills', [BillController::class, 'index'])->name('bills')->middleware('feature:portal_bills');
    Route::get('/bills/{category}', [BillController::class, 'category'])->name('bills.category')->middleware('feature:portal_bills');
    Route::post('/bills/{category}', [BillController::class, 'pay'])->name('bills.pay')->middleware('feature:portal_bills');

    // Standing Orders
    Route::get('/standing-orders', [StandingOrderController::class, 'index'])->name('standing-orders')->middleware('feature:portal_standing_orders');
    Route::get('/standing-orders/create', [StandingOrderController::class, 'create'])->name('standing-orders.create')->middleware('feature:portal_standing_orders');
    Route::post('/standing-orders', [StandingOrderController::class, 'store'])->name('standing-orders.store')->middleware('feature:portal_standing_orders');
    Route::post('/standing-orders/{id}/pause', [StandingOrderController::class, 'pause'])->name('standing-orders.pause')->middleware('feature:portal_standing_orders');
    Route::post('/standing-orders/{id}/resume', [StandingOrderController::class, 'resume'])->name('standing-orders.resume')->middleware('feature:portal_standing_orders');
    Route::delete('/standing-orders/{id}', [StandingOrderController::class, 'destroy'])->name('standing-orders.destroy')->middleware('feature:portal_standing_orders');

    // Loans
    Route::get('/loans', [LoanController::class, 'index'])->name('loans');
    Route::get('/loans/calculator', [LoanCalculatorController::class, 'index'])->name('loans.calculator');

    // Loan Applications — must be before /loans/{loanId} to avoid "apply" being matched as a UUID
    Route::get('/loans/apply', [LoanApplicationController::class, 'create'])->name('loans.apply')->middleware('feature:portal_loan_apply');
    Route::post('/loans/apply', [LoanApplicationController::class, 'store'])->name('loans.apply.store')->middleware('feature:portal_loan_apply');
    Route::get('/loans/applications', [LoanApplicationController::class, 'index'])->name('loans.applications')->middleware('feature:portal_loan_apply');
    Route::delete('/loans/applications/{id}', [LoanApplicationController::class, 'cancel'])->name('loans.applications.cancel')->middleware('feature:portal_loan_apply');

    Route::get('/loans/{loanId}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loanId}/repay', [LoanController::class, 'repay'])->name('loans.repay');

    // Budget Planner
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget')->middleware('feature:portal_budget');
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store')->middleware('feature:portal_budget');
    Route::delete('/budget/{id}', [BudgetController::class, 'destroy'])->name('budget.destroy')->middleware('feature:portal_budget');

    // Disputes
    Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes')->middleware('feature:portal_disputes');
    Route::get('/disputes/create', [DisputeController::class, 'create'])->name('disputes.create')->middleware('feature:portal_disputes');
    Route::post('/disputes', [DisputeController::class, 'store'])->name('disputes.store')->middleware('feature:portal_disputes');
    Route::get('/disputes/{id}', [DisputeController::class, 'show'])->name('disputes.show')->middleware('feature:portal_disputes');

    // Notification Inbox
    Route::get('/notifications', [NotificationInboxController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationInboxController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationInboxController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationInboxController::class, 'destroy'])->name('notifications.destroy');

    // Notification Preferences & Push
    Route::get('/notifications/preferences',  [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::post('/notifications/subscribe',   [NotificationController::class, 'pushSubscribe'])->name('notifications.push-subscribe');

    // KYC Upgrade
    Route::get('/kyc/upgrade', [KycUpgradeController::class, 'index'])->name('kyc.upgrade')->middleware('feature:portal_kyc_upgrade');
    Route::post('/kyc/upgrade', [KycUpgradeController::class, 'store'])->name('kyc.upgrade.store')->middleware('feature:portal_kyc_upgrade');

    // Investments (Fixed Deposits)
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments')->middleware('feature:portal_investments');
    Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create')->middleware('feature:portal_investments');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store')->middleware('feature:portal_investments');
    Route::get('/investments/{id}', [InvestmentController::class, 'show'])->name('investments.show')->middleware('feature:portal_investments');
    Route::post('/investments/{id}/liquidate', [InvestmentController::class, 'liquidate'])->name('investments.liquidate')->middleware('feature:portal_investments');

    // Payment Requests
    Route::get('/pay-requests', [PaymentRequestController::class, 'index'])->name('pay-requests')->middleware('feature:portal_pay_requests');
    Route::post('/pay-requests', [PaymentRequestController::class, 'store'])->name('pay-requests.store')->middleware('feature:portal_pay_requests');
    Route::delete('/pay-requests/{id}', [PaymentRequestController::class, 'destroy'])->name('pay-requests.destroy')->middleware('feature:portal_pay_requests');

    // Credit Score
    Route::get('/credit-score', [CreditScoreController::class, 'index'])->name('credit-score')->middleware('feature:portal_credit_score');

    // FX Rates
    Route::get('/rates', [FxRatesController::class, 'index'])->name('rates')->middleware('feature:portal_fx_rates');

    // Referral Programme
    Route::get('/referral', [ReferralController::class, 'index'])->name('referral')->middleware('feature:portal_referral');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/dark-mode', [ProfileController::class, 'toggleDarkMode'])->name('profile.dark-mode');

    // Transaction Receipt PDF
    Route::get('/transactions/{id}/receipt', [TransactionReceiptController::class, 'download'])->name('transactions.receipt');

    // Airtime & Data
    Route::get('/airtime', [AirtimeController::class, 'index'])->name('airtime');
    Route::post('/airtime', [AirtimeController::class, 'store'])->name('airtime.store');

    // Scheduled Transfers
    Route::get('/scheduled-transfers', [ScheduledTransferController::class, 'index'])->name('scheduled-transfers');
    Route::post('/scheduled-transfers', [ScheduledTransferController::class, 'store'])->name('scheduled-transfers.store');
    Route::delete('/scheduled-transfers/{id}', [ScheduledTransferController::class, 'destroy'])->name('scheduled-transfers.destroy');

    // Savings Challenges
    Route::get('/savings-challenges', [SavingsChallengeController::class, 'index'])->name('savings-challenges');
    Route::post('/savings-challenges', [SavingsChallengeController::class, 'store'])->name('savings-challenges.store');
    Route::post('/savings-challenges/{id}/save', [SavingsChallengeController::class, 'save'])->name('savings-challenges.save');
    Route::post('/savings-challenges/{id}/pause', [SavingsChallengeController::class, 'pause'])->name('savings-challenges.pause');
    Route::post('/savings-challenges/{id}/resume', [SavingsChallengeController::class, 'resume'])->name('savings-challenges.resume');

    // Chequebook Requests
    Route::get('/cheque-requests', [ChequeRequestController::class, 'index'])->name('cheque-requests');
    Route::post('/cheque-requests', [ChequeRequestController::class, 'store'])->name('cheque-requests.store');
    Route::delete('/cheque-requests/{id}', [ChequeRequestController::class, 'destroy'])->name('cheque-requests.destroy');

    // Account Opening
    Route::get('/account-opening', [AccountOpeningController::class, 'index'])->name('account-opening');
    Route::post('/account-opening', [AccountOpeningController::class, 'store'])->name('account-opening.store');

    // Inter-bank Transfer (NIP)
    Route::get('/interbank-transfer',        [InterbankTransferController::class, 'index'])->name('interbank-transfer');
    Route::post('/interbank-transfer',       [InterbankTransferController::class, 'store'])->name('interbank-transfer.store');
    Route::get('/interbank-transfer/banks',  [InterbankTransferController::class, 'banks'])->name('interbank-transfer.banks');

    // Bulk / Payroll Transfer
    Route::get('/bulk-transfer',             [BulkTransferController::class, 'index'])->name('bulk-transfer');
    Route::get('/bulk-transfer/create',      [BulkTransferController::class, 'create'])->name('bulk-transfer.create');
    Route::post('/bulk-transfer/upload',     [BulkTransferController::class, 'upload'])->name('bulk-transfer.upload');
    Route::get('/bulk-transfer/preview',     [BulkTransferController::class, 'preview'])->name('bulk-transfer.preview');
    Route::post('/bulk-transfer/submit',     [BulkTransferController::class, 'submit'])->name('bulk-transfer.submit');
    Route::get('/bulk-transfer/{id}',        [BulkTransferController::class, 'show'])->name('bulk-transfer.show');
    Route::get('/bulk-transfer/{id}/download', [BulkTransferController::class, 'download'])->name('bulk-transfer.download');

    // Card PIN Management
    Route::post('/cards/{cardId}/pin',       [\App\Http\Controllers\VirtualCardController::class, 'setPin'])->name('cards.pin.set');
    Route::post('/cards/{cardId}/pin/change',[\App\Http\Controllers\VirtualCardController::class, 'changePin'])->name('cards.pin.change');

    // Loan Top-Up
    Route::get('/loans/{loanId}/topup',      [\App\Http\Controllers\LoanController::class, 'topupForm'])->name('loans.topup');
    Route::post('/loans/{loanId}/topup',     [\App\Http\Controllers\LoanController::class, 'topupStore'])->name('loans.topup.store');

    // Group Savings (Ajo)
    Route::get('/savings-groups',            [SavingsGroupController::class, 'index'])->name('savings-groups');
    Route::get('/savings-groups/create',     [SavingsGroupController::class, 'create'])->name('savings-groups.create');
    Route::post('/savings-groups',           [SavingsGroupController::class, 'store'])->name('savings-groups.store');
    Route::get('/savings-groups/{id}',       [SavingsGroupController::class, 'show'])->name('savings-groups.show');
    Route::post('/savings-groups/{id}/join', [SavingsGroupController::class, 'join'])->name('savings-groups.join');
    Route::post('/savings-groups/{id}/contribute', [SavingsGroupController::class, 'contribute'])->name('savings-groups.contribute');
    Route::post('/savings-groups/{id}/leave',[SavingsGroupController::class, 'leave'])->name('savings-groups.leave');

    // QR Payment
    Route::get('/qr-payment', [QrPaymentController::class, 'index'])->name('qr-payment');

    // Split Bills
    Route::get('/split-bills',                                     [SplitBillController::class, 'index'])->name('split-bills');
    Route::get('/split-bills/create',                              [SplitBillController::class, 'create'])->name('split-bills.create');
    Route::post('/split-bills',                                    [SplitBillController::class, 'store'])->name('split-bills.store');
    Route::get('/split-bills/{id}',                                [SplitBillController::class, 'show'])->name('split-bills.show');
    Route::post('/split-bills/{billId}/participants/{pid}/paid',   [SplitBillController::class, 'markPaid'])->name('split-bills.mark-paid');
    Route::delete('/split-bills/{id}',                            [SplitBillController::class, 'cancel'])->name('split-bills.cancel');

    // Physical Debit Cards
    Route::get('/physical-cards',                          [PhysicalCardController::class, 'index'])->name('physical-cards');
    Route::post('/physical-cards/{cardId}/block',          [PhysicalCardController::class, 'block'])->name('physical-cards.block');
    Route::post('/physical-cards/{cardId}/unblock',        [PhysicalCardController::class, 'unblock'])->name('physical-cards.unblock');
    Route::get('/physical-cards/request/{accountId}',      [PhysicalCardController::class, 'requestCard'])->name('physical-cards.request');
    Route::post('/physical-cards/request',                 [PhysicalCardController::class, 'storeRequest'])->name('physical-cards.store-request');
    Route::post('/physical-cards/requests/{id}/cancel',    [PhysicalCardController::class, 'cancelRequest'])->name('physical-cards.cancel-request');

    // Overdraft
    Route::get('/overdraft',        [PortalOverdraftController::class, 'index'])->name('overdraft');
    Route::get('/overdraft/apply',  [PortalOverdraftController::class, 'create'])->name('overdraft.create');
    Route::post('/overdraft',       [PortalOverdraftController::class, 'store'])->name('overdraft.store');
    Route::delete('/overdraft/{id}',[PortalOverdraftController::class, 'cancel'])->name('overdraft.cancel');
});
