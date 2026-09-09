<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicFeePaymentController extends Controller
{
    public function show(Request $request, int $classId)
    {
        $class = Classes::findOrFail($classId);
        $academicYear = AcademicYear::current() ?? AcademicYear::first();

        // Compute standard fees by class tier
        $tier = match(true) {
            in_array($class->name, ['Pre-KG', 'LKG', 'UKG']) => 'Kindergarten',
            in_array($class->name, ['I', 'II', 'III', 'IV', 'V']) => 'Primary',
            in_array($class->name, ['VI', 'VII', 'VIII']) => 'Middle',
            default => 'High School',
        };

        $baseTuition = match($tier) {
            'Kindergarten' => 18000,
            'Primary'      => 24000,
            'Middle'       => 30000,
            default        => 38000,
        };

        $bookFee  = match($tier) {
            'Kindergarten' => 2500,
            'Primary'      => 3500,
            'Middle'       => 4500,
            default        => 5500,
        };

        $examFee    = 1500;
        $labFee     = in_array($tier, ['Middle', 'High School']) ? 3000 : 0;
        $totalBasic = $baseTuition + $bookFee + $examFee + $labFee;

        $feeBreakdown = [
            'tier'          => $tier,
            'tuition_fee'   => $baseTuition,
            'book_fee'      => $bookFee,
            'exam_fee'      => $examFee,
            'lab_fee'       => $labFee,
            'total_basic'   => $totalBasic,
            'hostel_annual' => 30000,
            'combined'      => $totalBasic + 30000,
        ];

        // UPI details
        $upiId     = config('school.upi_id', 'schoolerp@upi');
        $schoolName = config('app.name', 'DASA EduGroup');
        $upiUrl    = "upi://pay?pa={$upiId}&pn=" . urlencode($schoolName)
                   . "&am={$totalBasic}&cu=INR&tn=" . urlencode("Fee Class {$class->name}");

        // Generate QR code server-side as inline SVG (no external API)
        $qrSvg = null;
        try {
            $qrSvg = QrCode::size(180)->margin(1)->generate($upiUrl);
        } catch (\Throwable $e) {
            // Silently fail — blade will show a fallback
        }

        // Current page URL for sharing
        $pageUrl = url()->current();

        return view('public.fee-payment', compact(
            'class', 'academicYear', 'feeBreakdown',
            'upiUrl', 'upiId', 'schoolName', 'qrSvg', 'pageUrl'
        ));
    }

    public function processPayment(Request $request, int $classId)
    {
        $request->validate([
            'student_name'    => 'required|string|max:100',
            'admission_no'    => 'nullable|string|max:50',
            'parent_phone'    => 'required|string|max:20',
            'payment_type'    => 'required|in:term_1,full_year,custom',
            'amount'          => 'required|numeric|min:1',
            'payment_mode'    => 'required|in:upi,net_banking,card,qr',
            'transaction_ref' => 'nullable|string|max:100',
        ]);

        $class      = Classes::findOrFail($classId);
        $receiptNo  = 'REC-ONL-' . strtoupper(Str::random(6)) . '-' . date('Ymd');
        $transactionId = $request->transaction_ref ?: ('TXN' . strtoupper(Str::random(8)));

        $paymentData = [
            'receipt_number' => $receiptNo,
            'student_name'   => $request->student_name,
            'admission_no'   => $request->admission_no ?: 'NEW-ADMISSION',
            'parent_phone'   => $request->parent_phone,
            'class_name'     => $class->name,
            'amount'         => $request->amount,
            'payment_type'   => $request->payment_type,
            'payment_mode'   => strtoupper($request->payment_mode),
            'transaction_id' => $transactionId,
            'paid_at'        => now()->format('d M Y, h:i A'),
            'status'         => 'SUCCESS',
        ];

        session()->flash('payment_success', $paymentData);

        return redirect()->route('public.fee.pay', $classId)->with('receipt', $paymentData);
    }
}
