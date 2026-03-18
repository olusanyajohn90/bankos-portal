<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BulkTransferController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index()
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $transfers = DB::table('portal_bulk_transfers')
            ->where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bulk-transfer.index', compact('transfers'));
    }

    public function create()
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('bulk-transfer.create', compact('accounts'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:2048|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Could not read the uploaded file.']);
        }

        $rows = [];
        $errors = [];
        $lineNum = 0;

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            $lineNum++;

            if ($lineNum > 100) {
                $errors[] = 'File exceeds 100 rows. Only the first 100 rows will be processed.';
                break;
            }

            // Pad row to at least 6 columns
            while (count($data) < 6) {
                $data[] = '';
            }

            [$name, $accountNumber, $bankCode, $bankName, $amount, $narration] = $data;

            $name          = trim($name);
            $accountNumber = trim($accountNumber);
            $bankCode      = trim($bankCode);
            $bankName      = trim($bankName);
            $amount        = trim($amount);
            $narration     = trim($narration);

            // Preserve leading zeros: if purely numeric and < 10 digits, pad to 10 (NUBAN standard)
            if ($accountNumber !== '' && ctype_digit($accountNumber) && strlen($accountNumber) < 10) {
                $accountNumber = str_pad($accountNumber, 10, '0', STR_PAD_LEFT);
            }

            $rowErrors = [];

            if (empty($name)) {
                $rowErrors[] = 'Name is required';
            }
            if (empty($accountNumber)) {
                $rowErrors[] = 'Account number is required';
            }
            if (empty($amount) || !is_numeric($amount) || (float) $amount <= 0) {
                $rowErrors[] = 'Amount must be a positive number';
            }

            if (!empty($rowErrors)) {
                $errors[] = 'Row ' . $lineNum . ': ' . implode(', ', $rowErrors);
                continue;
            }

            $rows[] = [
                'row_number'       => $lineNum,
                'beneficiary_name' => $name,
                'account_number'   => $accountNumber,
                'bank_code'        => $bankCode,
                'bank_name'        => $bankName,
                'amount'           => (float) $amount,
                'narration'        => $narration,
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'No valid rows found in the CSV file. ' . implode(' ', $errors)]);
        }

        $totalAmount = array_sum(array_column($rows, 'amount'));

        // Store in session for preview
        session([
            'bulk_transfer_rows'  => $rows,
            'bulk_transfer_total' => $totalAmount,
            'bulk_transfer_count' => count($rows),
            'bulk_transfer_errors'=> $errors,
        ]);

        return redirect()->route('bulk-transfer.preview');
    }

    public function preview()
    {
        $rows        = session('bulk_transfer_rows');
        $totalAmount = session('bulk_transfer_total');
        $rowCount    = session('bulk_transfer_count');
        $parseErrors = session('bulk_transfer_errors', []);

        if (empty($rows)) {
            return redirect()->route('bulk-transfer.create')
                ->withErrors(['No preview data found. Please upload a CSV file first.']);
        }

        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('bulk-transfer.preview', compact('rows', 'totalAmount', 'rowCount', 'parseErrors', 'accounts'));
    }

    public function submit(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $validated = $request->validate([
            'account_id' => 'required|string',
            'label'      => 'nullable|string|max:120',
            'pin'        => 'required|digits:4',
        ]);

        // Verify PIN
        if (!$customer->portal_pin || !Hash::check($validated['pin'], $customer->portal_pin)) {
            return back()->withErrors(['pin' => 'Incorrect transaction PIN.']);
        }

        $rows = session('bulk_transfer_rows');

        if (empty($rows)) {
            return redirect()->route('bulk-transfer.create')
                ->withErrors(['Session expired. Please upload the CSV again.']);
        }

        $account = $customer->accounts()
            ->where('status', 'active')
            ->find($validated['account_id']);

        if (!$account) {
            return back()->withErrors(['account_id' => 'Invalid source account.']);
        }

        $totalAmount = (float) array_sum(array_column($rows, 'amount'));

        if ((float) $account->available_balance < $totalAmount) {
            return back()->withErrors([
                'Insufficient balance. Required: NGN ' . number_format($totalAmount, 2) .
                ', Available: NGN ' . number_format($account->available_balance, 2)
            ]);
        }

        $reference    = 'BLK' . strtoupper(Str::random(10));
        $bulkId       = (string) Str::uuid();
        $rowCount     = count($rows);
        $label        = $validated['label'] ?? null;

        DB::transaction(function () use (
            $customer, $account, $rows, $totalAmount,
            $reference, $bulkId, $rowCount, $label
        ) {
            // Create bulk transfer record
            DB::table('portal_bulk_transfers')->insert([
                'id'              => $bulkId,
                'customer_id'     => $customer->id,
                'tenant_id'       => $customer->tenant_id,
                'account_id'      => $account->id,
                'reference'       => $reference,
                'label'           => $label,
                'total_amount'    => $totalAmount,
                'recipient_count' => $rowCount,
                'processed_count' => $rowCount,
                'failed_count'    => 0,
                'status'          => 'completed',
                'submitted_at'    => now(),
                'completed_at'    => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Deduct total from source account
            $account->decrement('available_balance', $totalAmount);
            $account->decrement('ledger_balance', $totalAmount);

            // Create each item + individual transaction
            foreach ($rows as $row) {
                $itemId   = (string) Str::uuid();
                $itemRef  = $reference . '-' . str_pad($row['row_number'], 3, '0', STR_PAD_LEFT);

                DB::table('portal_bulk_transfer_items')->insert([
                    'id'               => $itemId,
                    'bulk_transfer_id' => $bulkId,
                    'row_number'       => $row['row_number'],
                    'beneficiary_name' => $row['beneficiary_name'],
                    'account_number'   => $row['account_number'],
                    'bank_code'        => $row['bank_code'],
                    'bank_name'        => $row['bank_name'],
                    'amount'           => $row['amount'],
                    'narration'        => $row['narration'],
                    'status'           => 'processed',
                    'reference'        => $itemRef,
                    'processed_at'     => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                Transaction::create([
                    'id'           => (string) Str::uuid(),
                    'tenant_id'    => $customer->tenant_id,
                    'account_id'   => $account->id,
                    'reference'    => $itemRef,
                    'type'         => 'transfer',
                    'amount'       => $row['amount'],
                    'currency'     => $account->currency ?? 'NGN',
                    'description'  => ($row['narration'] ?: 'Bulk transfer') .
                                      ' — ' . $row['beneficiary_name'] .
                                      ' (' . $row['account_number'] . ')',
                    'status'       => 'success',
                    'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
                ]);
            }
        });

        // Clear session
        session()->forget(['bulk_transfer_rows', 'bulk_transfer_total', 'bulk_transfer_count', 'bulk_transfer_errors']);

        return redirect()->route('bulk-transfer.show', $bulkId)
            ->with('success', 'Bulk transfer processed successfully. Reference: ' . $reference);
    }

    public function show(string $id)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $transfer = DB::table('portal_bulk_transfers')
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$transfer) {
            abort(404);
        }

        $items = DB::table('portal_bulk_transfer_items')
            ->where('bulk_transfer_id', $id)
            ->orderBy('row_number')
            ->get();

        // Source account
        $account = Account::find($transfer->account_id);

        return view('bulk-transfer.show', compact('transfer', 'items', 'account'));
    }

    public function download(string $id)
    {
        /** @var \App\Models\Customer $customer */
        $customer = $this->customer();

        $transfer = DB::table('portal_bulk_transfers')
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->first();

        if (!$transfer) {
            abort(404);
        }

        $items = DB::table('portal_bulk_transfer_items')
            ->where('bulk_transfer_id', $id)
            ->orderBy('row_number')
            ->get();

        $filename = 'bulk-transfer-' . $transfer->reference . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Row', 'Name', 'Account Number', 'Bank Code', 'Bank Name', 'Amount', 'Narration', 'Status', 'Reference', 'Failure Reason']);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->row_number,
                    $item->beneficiary_name,
                    $item->account_number,
                    $item->bank_code,
                    $item->bank_name,
                    $item->amount,
                    $item->narration,
                    $item->status,
                    $item->reference,
                    $item->failure_reason,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
