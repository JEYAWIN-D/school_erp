<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\AccountTransfer;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        $todayStr = today()->toDateString();

        // ── 1. Real-Time Cumulative Balances per Account ───────────────────────
        $balances = $this->calculateAccountBalances();

        // ── 2. Today's Inflow, Outflow & Cash Flow ─────────────────────────────
        $todayFeePayments = FeePayment::where('is_cancelled', false)
            ->whereDate('payment_date', $todayStr)
            ->get();

        $todayExpenses = Expense::where('approval_status', 'approved')
            ->whereDate('expense_date', $todayStr)
            ->get();

        $todayTransfers = AccountTransfer::whereDate('transfer_date', $todayStr)->get();

        $todayInflow = (float) $todayFeePayments->sum('total_paid');
        $todayOutflow = (float) $todayExpenses->sum('amount');
        $todayNet = $todayInflow - $todayOutflow;

        // Inflow breakdown today
        $todayAdmissionFees = (float) $todayFeePayments->filter(function($p) {
            return str_contains(strtolower($p->term_name ?? ''), 'admission') ||
                   str_contains(strtolower($p->remarks ?? ''), 'admission');
        })->sum('total_paid');

        $todayTransportFees = (float) $todayFeePayments->filter(function($p) {
            return str_contains(strtolower($p->term_name ?? ''), 'transport') ||
                   str_contains(strtolower($p->term_name ?? ''), 'bus') ||
                   str_contains(strtolower($p->remarks ?? ''), 'transport') ||
                   str_contains(strtolower($p->remarks ?? ''), 'bus');
        })->sum('total_paid');

        $todayTuitionFees = max(0, $todayInflow - ($todayAdmissionFees + $todayTransportFees));

        // Outflow breakdown today
        $todayAcademicExpenses = (float) $todayExpenses->where('category', 'academic')->sum('amount');
        $todayMaintenanceExpenses = (float) $todayExpenses->where('category', 'maintenance')->sum('amount');

        // Today per account collections
        $todayUpiInflow = (float) $todayFeePayments->where('payment_account', 'upi')->sum('total_paid');
        $todayUpiOutflow = (float) $todayExpenses->where('payment_account', 'upi')->sum('amount');

        $todayBox1Inflow = (float) $todayFeePayments->where('payment_account', 'cash_box_1')->sum('total_paid');
        $todayBox1Outflow = (float) $todayExpenses->where('payment_account', 'cash_box_1')->sum('amount');

        $todayBox2Inflow = (float) $todayFeePayments->where('payment_account', 'cash_box_2')->sum('total_paid');
        $todayBox2Outflow = (float) $todayExpenses->where('payment_account', 'cash_box_2')->sum('amount');

        // ── 3. Filtered Unified Transaction Ledger / Logs ──────────────────────
        $dateFilter = $request->input('date_filter', 'today');
        $accountFilter = $request->input('account', 'all');
        $flowFilter = $request->input('flow', 'all');
        $categoryFilter = $request->input('category', 'all');
        $search = trim((string) $request->input('search', ''));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Build merged ledger
        $ledger = $this->getUnifiedLedger($dateFilter, $accountFilter, $flowFilter, $categoryFilter, $search, $dateFrom, $dateTo);

        // Paginate in-memory collection
        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $totalItems = $ledger->count();
        $paginatedItems = $ledger->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $totalItems,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('accounts.index', compact(
            'currentYear',
            'balances',
            'todayInflow',
            'todayOutflow',
            'todayNet',
            'todayAdmissionFees',
            'todayTransportFees',
            'todayTuitionFees',
            'todayAcademicExpenses',
            'todayMaintenanceExpenses',
            'todayUpiInflow',
            'todayUpiOutflow',
            'todayBox1Inflow',
            'todayBox1Outflow',
            'todayBox2Inflow',
            'todayBox2Outflow',
            'paginator',
            'dateFilter',
            'accountFilter',
            'flowFilter',
            'categoryFilter',
            'search',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Calculate cumulative balances for UPI, Cash Box 1, Cash Box 2, and Total
     */
    private function calculateAccountBalances(): array
    {
        // Opening cash reserves / petty cash floats
        $box1Float = 200000.00; // Front office counter cash float
        $box2Float = 150000.00; // Accounts office vault opening reserve
        $upiFloat  = 0.00;

        // UPI
        $upiInflow = (float) FeePayment::where('is_cancelled', false)->where('payment_account', 'upi')->sum('total_paid');
        $upiOutflow = (float) Expense::where('approval_status', 'approved')->where('payment_account', 'upi')->sum('amount');
        $upiTransferIn = (float) AccountTransfer::where('to_account', 'upi')->sum('amount');
        $upiTransferOut = (float) AccountTransfer::where('from_account', 'upi')->sum('amount');
        $upiBalance = $upiFloat + $upiInflow - $upiOutflow + $upiTransferIn - $upiTransferOut;

        // Cash Box 1 (Front Office)
        $box1Inflow = (float) FeePayment::where('is_cancelled', false)->where('payment_account', 'cash_box_1')->sum('total_paid');
        $box1Outflow = (float) Expense::where('approval_status', 'approved')->where('payment_account', 'cash_box_1')->sum('amount');
        $box1TransferIn = (float) AccountTransfer::where('to_account', 'cash_box_1')->sum('amount');
        $box1TransferOut = (float) AccountTransfer::where('from_account', 'cash_box_1')->sum('amount');
        $box1Balance = $box1Float + $box1Inflow - $box1Outflow + $box1TransferIn - $box1TransferOut;

        // Cash Box 2 (Accounts Office)
        $box2Inflow = (float) FeePayment::where('is_cancelled', false)->where('payment_account', 'cash_box_2')->sum('total_paid');
        $box2Outflow = (float) Expense::where('approval_status', 'approved')->where('payment_account', 'cash_box_2')->sum('amount');
        $box2TransferIn = (float) AccountTransfer::where('to_account', 'cash_box_2')->sum('amount');
        $box2TransferOut = (float) AccountTransfer::where('from_account', 'cash_box_2')->sum('amount');
        $box2Balance = $box2Float + $box2Inflow - $box2Outflow + $box2TransferIn - $box2TransferOut;

        $totalBalance = $upiBalance + $box1Balance + $box2Balance;

        return [
            'upi' => [
                'name'          => 'UPI Account',
                'subtitle'      => 'PhonePe, Google Pay, Paytm & QR Code',
                'badge'         => 'Digital UPI',
                'balance'       => $upiBalance,
                'opening_float' => $upiFloat,
                'total_inflow'  => $upiInflow,
                'total_outflow' => $upiOutflow,
                'transfers_in'  => $upiTransferIn,
                'transfers_out' => $upiTransferOut,
            ],
            'cash_box_1' => [
                'name'          => 'Cash Box 1 (Front Office)',
                'subtitle'      => 'Reception desk & admission counter collections',
                'badge'         => 'Front Office',
                'balance'       => $box1Balance,
                'opening_float' => $box1Float,
                'total_inflow'  => $box1Inflow,
                'total_outflow' => $box1Outflow,
                'transfers_in'  => $box1TransferIn,
                'transfers_out' => $box1TransferOut,
            ],
            'cash_box_2' => [
                'name'          => 'Cash Box 2 (Accounts Office)',
                'subtitle'      => 'Main office vault & official institutional reserve',
                'badge'         => 'Accounts Vault',
                'balance'       => $box2Balance,
                'opening_float' => $box2Float,
                'total_inflow'  => $box2Inflow,
                'total_outflow' => $box2Outflow,
                'transfers_in'  => $box2TransferIn,
                'transfers_out' => $box2TransferOut,
            ],
            'total_balance' => $totalBalance,
        ];
    }

    /**
     * Build unified transaction collection from fee_payments, expenses, and transfers
     */
    private function getUnifiedLedger($dateFilter, $accountFilter, $flowFilter, $categoryFilter, $search, $dateFrom = null, $dateTo = null)
    {
        // ── 1. Inflows: Fee Payments ──────────────────────────────────────────
        $feeQuery = FeePayment::with(['student.currentEnrollment.class', 'feeHead', 'collectedBy'])
            ->where('is_cancelled', false);

        $this->applyDateFilter($feeQuery, 'payment_date', $dateFilter, $dateFrom, $dateTo);

        if ($accountFilter !== 'all') {
            $feeQuery->where('payment_account', $accountFilter);
        }

        if ($flowFilter === 'outflow' || $flowFilter === 'transfer') {
            $feePayments = collect();
        } else {
            $feePayments = $feeQuery->get()->map(function($f) {
                $isAdmission = str_contains(strtolower($f->term_name ?? ''), 'admission') ||
                               str_contains(strtolower($f->remarks ?? ''), 'admission');
                $isTransport = str_contains(strtolower($f->term_name ?? ''), 'transport') ||
                               str_contains(strtolower($f->term_name ?? ''), 'bus') ||
                               str_contains(strtolower($f->remarks ?? ''), 'transport') ||
                               str_contains(strtolower($f->remarks ?? ''), 'bus');

                $category = $isAdmission ? 'admission' : ($isTransport ? 'bus' : 'fees');
                $categoryLabel = $isAdmission ? 'Admission Fee' : ($isTransport ? 'Bus / Transport Fee' : ($f->term_name ?: ($f->feeHead?->name ?: 'Tuition Fee')));

                $student = $f->student;
                $studentName = $student ? trim($student->first_name . ' ' . ($student->last_name ?? '')) : 'Student';
                $className = $student?->currentEnrollment?->class?->name ? 'Class ' . $student->currentEnrollment->class->name : '';
                $admNo = $student?->admission_no ? "Adm: {$student->admission_no}" : '';

                return (object) [
                    'id'              => 'fee_' . $f->id,
                    'record_id'       => $f->id,
                    'type'            => 'inflow',
                    'flow_label'      => 'INFLOW',
                    'flow_color'      => 'emerald',
                    'date'            => $f->payment_date ? $f->payment_date->format('Y-m-d') : $f->created_at->format('Y-m-d'),
                    'formatted_date'  => $f->payment_date ? $f->payment_date->format('d M Y') : $f->created_at->format('d M Y'),
                    'time'            => $f->created_at ? $f->created_at->format('h:i A') : '',
                    'timestamp'       => $f->created_at ? $f->created_at->timestamp : strtotime($f->payment_date),
                    'voucher_no'      => $f->receipt_number,
                    'party_name'      => $studentName,
                    'party_subtitle'  => trim($className . ($admNo ? " • {$admNo}" : '')),
                    'party_mobile'    => $student?->mobile ?: ($student?->father_mobile ?: ''),
                    'category'        => $category,
                    'category_label'  => $categoryLabel,
                    'description'     => $f->remarks ?: "Fee Collection ({$categoryLabel})",
                    'account'         => $f->payment_account ?: 'cash_box_1',
                    'account_label'   => $this->getAccountLabel($f->payment_account),
                    'payment_mode'    => strtoupper($f->payment_mode ?: 'Cash'),
                    'amount'          => (float) $f->total_paid,
                    'reference_no'    => $f->transaction_id ?: ($f->cheque_number ?: '—'),
                    'staff_name'      => $f->collectedBy?->name ?: 'Staff',
                    'receipt_url'     => route('fees.receipt', $f->id),
                ];
            });
        }

        // ── 2. Outflows: Approved Expenses ────────────────────────────────────
        $expQuery = Expense::with(['creator', 'approver'])
            ->where('approval_status', 'approved');

        $this->applyDateFilter($expQuery, 'expense_date', $dateFilter, $dateFrom, $dateTo);

        if ($accountFilter !== 'all') {
            $expQuery->where('payment_account', $accountFilter);
        }

        if ($flowFilter === 'inflow' || $flowFilter === 'transfer') {
            $expenses = collect();
        } else {
            $expenses = $expQuery->get()->map(function($e) {
                $category = $e->category === 'academic' ? 'academic_expense' : 'maintenance_expense';
                $categoryLabel = $e->category === 'academic' ? 'Academic Expense' : 'Maintenance Expense';

                return (object) [
                    'id'              => 'exp_' . $e->id,
                    'record_id'       => $e->id,
                    'type'            => 'outflow',
                    'flow_label'      => 'OUTFLOW',
                    'flow_color'      => 'rose',
                    'date'            => $e->expense_date ? $e->expense_date->format('Y-m-d') : $e->created_at->format('Y-m-d'),
                    'formatted_date'  => $e->expense_date ? $e->expense_date->format('d M Y') : $e->created_at->format('d M Y'),
                    'time'            => $e->created_at ? $e->created_at->format('h:i A') : '',
                    'timestamp'       => $e->created_at ? $e->created_at->timestamp : strtotime($e->expense_date),
                    'voucher_no'      => $e->expense_number,
                    'party_name'      => $e->vendor_name ?: ($e->title ?: 'Expense Voucher'),
                    'party_subtitle'  => $e->subcategory_label ?: ucfirst($e->category),
                    'party_mobile'    => '',
                    'category'        => $category,
                    'category_label'  => $categoryLabel . ' (' . ($e->subcategory_label ?: 'General') . ')',
                    'description'     => $e->title . ($e->description ? ' — ' . $e->description : ''),
                    'account'         => $e->payment_account ?: 'cash_box_1',
                    'account_label'   => $this->getAccountLabel($e->payment_account),
                    'payment_mode'    => $e->payment_method ?: 'Cash',
                    'amount'          => (float) $e->amount,
                    'reference_no'    => $e->vendor_invoice_no ?: '—',
                    'staff_name'      => $e->creator?->name ?: 'Staff',
                    'receipt_url'     => route('expenses.show', $e->id),
                ];
            });
        }

        // ── 3. Internal Fund Transfers ────────────────────────────────────────
        $trfQuery = AccountTransfer::with('transferredBy');
        $this->applyDateFilter($trfQuery, 'transfer_date', $dateFilter, $dateFrom, $dateTo);

        if ($accountFilter !== 'all') {
            $trfQuery->where(function($q) use ($accountFilter) {
                $q->where('from_account', $accountFilter)->orWhere('to_account', $accountFilter);
            });
        }

        if ($flowFilter === 'inflow' || $flowFilter === 'outflow') {
            $transfers = collect();
        } else {
            $transfers = $trfQuery->get()->map(function($t) {
                $fromLabel = $this->getAccountLabel($t->from_account);
                $toLabel = $this->getAccountLabel($t->to_account);

                return (object) [
                    'id'              => 'trf_' . $t->id,
                    'record_id'       => $t->id,
                    'type'            => 'transfer',
                    'flow_label'      => 'TRANSFER',
                    'flow_color'      => 'indigo',
                    'date'            => $t->transfer_date ? $t->transfer_date->format('Y-m-d') : $t->created_at->format('Y-m-d'),
                    'formatted_date'  => $t->transfer_date ? $t->transfer_date->format('d M Y') : $t->created_at->format('d M Y'),
                    'time'            => $t->created_at ? $t->created_at->format('h:i A') : '',
                    'timestamp'       => $t->created_at ? $t->created_at->timestamp : strtotime($t->transfer_date),
                    'voucher_no'      => $t->transfer_number,
                    'party_name'      => "Fund Movement: {$fromLabel} → {$toLabel}",
                    'party_subtitle'  => "Internal Box Clearance",
                    'party_mobile'    => '',
                    'category'        => 'transfer',
                    'category_label'  => "Internal Transfer",
                    'description'     => $t->remarks ?: "Cash Transfer from {$fromLabel} to {$toLabel}",
                    'account'         => $t->to_account,
                    'account_label'   => "{$fromLabel} → {$toLabel}",
                    'payment_mode'    => 'Internal',
                    'amount'          => (float) $t->amount,
                    'reference_no'    => $t->reference_no ?: '—',
                    'staff_name'      => $t->transferredBy?->name ?: 'Staff',
                    'receipt_url'     => '#',
                ];
            });
        }

        // Merge all three sources
        $merged = $feePayments->concat($expenses)->concat($transfers);

        // Filter by category
        if ($categoryFilter !== 'all') {
            $merged = $merged->filter(fn($item) => $item->category === $categoryFilter);
        }

        // Filter by search string
        if ($search !== '') {
            $q = strtolower($search);
            $merged = $merged->filter(function($item) use ($q) {
                return str_contains(strtolower($item->party_name), $q) ||
                       str_contains(strtolower($item->voucher_no), $q) ||
                       str_contains(strtolower($item->category_label), $q) ||
                       str_contains(strtolower($item->description), $q) ||
                       str_contains(strtolower($item->party_subtitle), $q) ||
                       str_contains(strtolower($item->reference_no), $q);
            });
        }

        // Sort descending by timestamp / date
        return $merged->sortByDesc('timestamp')->values();
    }

    private function applyDateFilter($query, $dateCol, $dateFilter, $dateFrom = null, $dateTo = null)
    {
        $today = today()->toDateString();

        match($dateFilter) {
            'today'      => $query->whereDate($dateCol, $today),
            'yesterday'  => $query->whereDate($dateCol, now()->subDay()->toDateString()),
            'this_week'  => $query->whereBetween($dateCol, [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]),
            'this_month' => $query->whereMonth($dateCol, now()->month)->whereYear($dateCol, now()->year),
            'custom'     => $query->when($dateFrom, fn($q) => $q->whereDate($dateCol, '>=', $dateFrom))
                                  ->when($dateTo, fn($q) => $q->whereDate($dateCol, '<=', $dateTo)),
            default      => null, // 'all' -> no date filter
        };
    }

    private function getAccountLabel(?string $account): string
    {
        return match(strtolower((string) $account)) {
            'upi'         => 'UPI Account',
            'cash_box_1'  => 'Cash Box 1 (Front Office)',
            'cash_box_2'  => 'Cash Box 2 (Accounts Office)',
            'bank', 'bank_transfer' => 'Bank Account',
            default       => 'Cash Box 1',
        };
    }

    /**
     * Store internal fund transfer between accounts (e.g. Cash Box 1 -> Cash Box 2 or Cash Box 1 -> UPI)
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_account'  => 'required|in:upi,cash_box_1,cash_box_2,bank',
            'to_account'    => 'required|in:upi,cash_box_1,cash_box_2,bank|different:from_account',
            'amount'        => 'required|numeric|min:1',
            'transfer_date' => 'required|date|before_or_equal:today',
            'reference_no'  => 'nullable|string|max:100',
            'remarks'       => 'nullable|string|max:500',
        ], [
            'to_account.different' => 'Destination account must be different from the source account.',
            'amount.min'           => 'Transfer amount must be at least ₹1.',
        ]);

        $transfer = AccountTransfer::create([
            'transfer_number' => AccountTransfer::generateNumber(),
            'from_account'    => $request->from_account,
            'to_account'      => $request->to_account,
            'amount'          => $request->amount,
            'transfer_date'   => $request->transfer_date,
            'reference_no'    => $request->reference_no,
            'remarks'         => $request->remarks,
            'transferred_by'  => Auth::id(),
        ]);

        $fromLabel = $this->getAccountLabel($request->from_account);
        $toLabel = $this->getAccountLabel($request->to_account);
        $formattedAmount = '₹' . number_format($request->amount, 2);

        return redirect()->route('accounts.index')
            ->with('success', "Transferred {$formattedAmount} from {$fromLabel} to {$toLabel} successfully (Ref: {$transfer->transfer_number}).");
    }

    /**
     * Export unified ledger to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $dateFilter = $request->input('date_filter', 'all');
        $accountFilter = $request->input('account', 'all');
        $flowFilter = $request->input('flow', 'all');
        $categoryFilter = $request->input('category', 'all');
        $search = trim((string) $request->input('search', ''));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $ledger = $this->getUnifiedLedger($dateFilter, $accountFilter, $flowFilter, $categoryFilter, $search, $dateFrom, $dateTo);

        $filename = 'School_Account_Ledger_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function() use ($ledger) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Date',
                'Time',
                'Voucher / Receipt No',
                'Type',
                'Party / Student / Payee',
                'Class / Department',
                'Category',
                'Description / What For',
                'Account / Destination',
                'Payment Mode',
                'Amount (INR)',
                'Reference / UTR',
                'Recorded By',
            ]);

            foreach ($ledger as $row) {
                fputcsv($handle, [
                    $row->date,
                    $row->time,
                    $row->voucher_no,
                    $row->flow_label,
                    $row->party_name,
                    $row->party_subtitle,
                    $row->category_label,
                    $row->description,
                    $row->account_label,
                    $row->payment_mode,
                    number_format($row->amount, 2, '.', ''),
                    $row->reference_no,
                    $row->staff_name,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Daily Account Day Book printable sheet
     */
    public function daybook(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $school = \App\Models\SchoolSetting::first() ?? (object)[
            'school_name' => 'DASA EDUGROUP',
            'phone'       => '+91 98765 43210',
            'email'       => 'info@dasaedugroup.com',
            'address'     => '123, Education City Campus, India'
        ];

        $ledger = $this->getUnifiedLedger('custom', 'all', 'all', 'all', '', $date, $date);

        $totalInflow = (float) $ledger->where('type', 'inflow')->sum('amount');
        $totalOutflow = (float) $ledger->where('type', 'outflow')->sum('amount');
        $netAmount = $totalInflow - $totalOutflow;

        $upiInflow = (float) $ledger->where('type', 'inflow')->where('account', 'upi')->sum('amount');
        $upiOutflow = (float) $ledger->where('type', 'outflow')->where('account', 'upi')->sum('amount');

        $box1Inflow = (float) $ledger->where('type', 'inflow')->where('account', 'cash_box_1')->sum('amount');
        $box1Outflow = (float) $ledger->where('type', 'outflow')->where('account', 'cash_box_1')->sum('amount');

        $box2Inflow = (float) $ledger->where('type', 'inflow')->where('account', 'cash_box_2')->sum('amount');
        $box2Outflow = (float) $ledger->where('type', 'outflow')->where('account', 'cash_box_2')->sum('amount');

        return view('accounts.daybook', compact(
            'date',
            'school',
            'ledger',
            'totalInflow',
            'totalOutflow',
            'netAmount',
            'upiInflow',
            'upiOutflow',
            'box1Inflow',
            'box1Outflow',
            'box2Inflow',
            'box2Outflow'
        ));
    }
}
