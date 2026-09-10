<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitorCardController extends Controller
{
    /**
     * Public mobile view for parents to show or download their digital visitor card
     */
    public function viewPass(string $token)
    {
        $student = Student::with(['currentEnrollment.class', 'currentEnrollment.section'])
            ->where('parent_visitor_pass_token', $token)
            ->firstOrFail();

        $school = SchoolSetting::first() ?? (object)[
            'school_name'    => config('app.name', 'DASA EDUGROUP'),
            'phone'          => '+91 98765 43210',
            'email'          => 'info@schoolerp.in',
            'website'        => 'www.schoolerp.in',
            'address'        => 'Main Campus, Knowledge City',
            'affiliation_no' => 'CBSE/AFF/2026/089',
        ];

        // Verification QR code pointing to gate security verification URL
        $verifyUrl = route('public.visitor-card.verify', ['token' => $token]);
        
        $qrCodeSvg = '';
        try {
            $qrCodeSvg = QrCode::format('svg')->size(160)->margin(1)->generate($verifyUrl);
        } catch (\Throwable $e) {
            $qrCodeSvg = '';
        }

        return view('public.visitor-card-view', compact('student', 'school', 'qrCodeSvg', 'verifyUrl'));
    }

    /**
     * Security Gate QR Scan Verification Screen
     * When campus security guards scan the QR code on the parent's card, this verifies validity.
     */
    public function verifyPass(string $token)
    {
        $student = Student::with(['currentEnrollment.class', 'currentEnrollment.section'])
            ->where('parent_visitor_pass_token', $token)
            ->first();

        $school = SchoolSetting::first() ?? (object)[
            'school_name' => config('app.name', 'DASA EDUGROUP'),
            'phone'       => '+91 98765 43210',
            'address'     => 'Main Campus, Knowledge City',
        ];

        $isValid = $student && in_array($student->status, ['active', 'principal_approved']);

        return view('public.visitor-card-verify', compact('student', 'school', 'isValid', 'token'));
    }
}
