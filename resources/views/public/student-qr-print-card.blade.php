<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Submission Pass — {{ $student->full_name }} ({{ $student->admission_number }})</title>
  @vite(['resources/css/app.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
    h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    @media print {
      body { background: #fff !important; }
      .no-print { display: none !important; }
      .print-card {
        box-shadow: none !important;
        border: 2px solid #0f172a !important;
        margin: 0 auto !important;
        page-break-inside: avoid !important;
      }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

  {{-- Top Actions Bar (Hidden on Print) --}}
  <div class="max-w-md w-full mb-4 flex items-center justify-between no-print">
    <a href="javascript:window.history.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
      &larr; Back to Profile
    </a>
    <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-2 cursor-pointer">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print Document Slip
    </button>
  </div>

  {{-- The Printable Card Slip --}}
  <div class="print-card max-w-md w-full bg-white rounded-3xl border-2 border-slate-300 shadow-xl p-6 sm:p-8 text-slate-900 space-y-6">

    {{-- School Header --}}
    <div class="text-center border-b-2 border-slate-900 pb-4">
      <h2 class="text-2xl font-black text-blue-900 tracking-wider uppercase">
        {{ $school->school_name ?? 'DASA EDUGROUP' }}
      </h2>
      <p class="text-[11px] font-bold text-slate-600 uppercase tracking-widest mt-0.5">
        Official Student Admission &bull; Certificate Submission Pass
      </p>
      <p class="text-[10px] text-slate-400 font-medium">
        Academic Year: {{ $student->currentEnrollment?->academicYear?->name ?? '2025-2026' }} &bull; Date: {{ date('d M Y') }}
      </p>
    </div>

    {{-- Student Credentials Info --}}
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Student Name</span>
        <span class="text-base font-extrabold text-slate-900">{{ $student->full_name }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Admission No</span>
        <span class="text-sm font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">{{ $student->admission_number }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Class &amp; Section</span>
        <span class="text-sm font-bold text-slate-800">
          {{ $student->currentEnrollment?->class?->name ?? 'Class ' . ($student->class_id ?? '5') }}
          @if($student->currentEnrollment?->section)
            - {{ $student->currentEnrollment->section->name }}
          @endif
        </span>
      </div>
      @if($student->father_name)
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Parent / Guardian</span>
        <span class="text-xs font-bold text-slate-800">{{ $student->father_name }}</span>
      </div>
      @endif
      @if($student->father_mobile ?? $student->mobile)
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contact Mobile</span>
        <span class="text-xs font-mono font-bold text-slate-800">{{ $student->father_mobile ?? $student->mobile }}</span>
      </div>
      @endif
    </div>

    {{-- QR Code Section --}}
    <div class="text-center space-y-3 py-2">
      <div class="inline-block p-4 rounded-2xl bg-white border-2 border-slate-900 shadow-md">
        <div class="w-48 h-48 mx-auto flex items-center justify-center">
          {!! $qrSvg !!}
        </div>
      </div>

      <div class="space-y-1">
        <p class="text-xs font-extrabold text-indigo-900 tracking-wide uppercase">
          Scan to Upload Student Certificates
        </p>
        <p class="text-[11px] text-slate-600 max-w-xs mx-auto leading-tight">
          Open your smartphone camera &amp; scan the QR code above to upload soft copies of <strong>Aadhaar, Birth Certificate, Community Certificate, PAN, TC, &amp; Photo</strong>.
        </p>
      </div>
    </div>

    {{-- Checklist Notice --}}
    <div class="border-t border-slate-200 pt-3 text-[10px] text-slate-500 space-y-1">
      <p class="font-bold text-slate-700 uppercase tracking-wider">Required Soft Copies Checklist:</p>
      <div class="grid grid-cols-2 gap-1 text-[10px] text-slate-600">
        <span>&bull; Student Aadhaar Card</span>
        <span>&bull; Birth Certificate</span>
        <span>&bull; Community / Caste Certificate</span>
        <span>&bull; Parent PAN Card</span>
        <span>&bull; Previous Marksheet / TC</span>
        <span>&bull; Passport Size Photo</span>
      </div>
    </div>

    {{-- Footer Stamp & Signature --}}
    <div class="border-t-2 border-slate-900 pt-3 flex items-center justify-between text-[10px] text-slate-500">
      <div>
        <p class="font-bold text-slate-700">DASA EduERP Verification Portal</p>
        <p>{{ $school->website ?? 'www.dasaedugroup.com' }}</p>
      </div>
      <div class="text-right">
        <p class="font-bold text-slate-700">Admissions Desk Seal / Sign</p>
        <div class="h-6 w-24 border-b border-dashed border-slate-400 mt-1"></div>
      </div>
    </div>

  </div>

</body>
</html>
