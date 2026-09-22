<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FeePayment;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

echo "--- FEE PAYMENTS SUMMARY ---\n";
$feeSummary = FeePayment::where('is_cancelled', false)
    ->select('payment_mode', DB::raw('count(*) as count'), DB::raw('sum(total_paid) as total'))
    ->groupBy('payment_mode')
    ->get();
echo json_encode($feeSummary, JSON_PRETTY_PRINT) . "\n\n";

echo "--- TODAY FEE PAYMENTS ---\n";
$todayFees = FeePayment::where('is_cancelled', false)
    ->whereDate('payment_date', date('Y-m-d'))
    ->select('id', 'receipt_number', 'payment_mode', 'total_paid', 'term_name', 'remarks')
    ->get();
echo json_encode($todayFees, JSON_PRETTY_PRINT) . "\n\n";

echo "--- EXPENSES SUMMARY ---\n";
$expSummary = Expense::select('category', 'approval_status', 'payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
    ->groupBy('category', 'approval_status', 'payment_method')
    ->get();
echo json_encode($expSummary, JSON_PRETTY_PRINT) . "\n\n";
