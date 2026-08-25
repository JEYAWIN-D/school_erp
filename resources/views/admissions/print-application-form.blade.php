<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Admission Application Form — {{ $school->school_name ?? 'DASA EDUGROUP' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
    }
    .print-page {
      background-color: #ffffff;
      page-break-after: always;
      break-after: page;
    }
    .print-page:last-child {
      page-break-after: auto;
      break-after: auto;
    }
    .digit-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 26px;
      border: 1px solid #64748b;
      border-radius: 4px;
      font-weight: 700;
      font-size: 11px;
    }
    .paper-line {
      border-bottom: 1.5px solid #94a3b8;
      min-height: 24px;
      display: inline-block;
    }
    .checkbox-box {
      width: 15px;
      height: 15px;
      border: 1.5px solid #475569;
      border-radius: 3px;
      display: inline-block;
      vertical-align: middle;
      margin-right: 4px;
    }
    @media print {
      @page {
        size: A4 portrait;
        margin: 8mm 10mm;
      }
      body {
        background-color: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print {
        display: none !important;
      }
      .print-page {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
    }
  </style>
</head>
<body class="py-6 px-4 sm:px-6">

  {{-- ── Floating Web Control Bar ──────────────────────────────── --}}
  <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-xl">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.create') }}" class="text-xs font-bold text-slate-300 hover:text-white flex items-center gap-1.5">
        &larr; Back to Admission Form
      </a>
      <span class="text-slate-600">|</span>
      <span class="text-xs font-semibold text-slate-300">Blank Parent Application Form Preview</span>
    </div>

    <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      <span>Print Blank Application Form</span>
    </button>
  </div>

  {{-- ── PAGE 1: STUDENT INFORMATION ─────────────────────────── --}}
  <div class="max-w-4xl mx-auto print-page rounded-3xl shadow-xl border border-slate-200 p-8 space-y-5">
    
    {{-- Header --}}
    <div class="flex items-center justify-between border-b-2 border-blue-900 pb-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-blue-900 text-white flex items-center justify-center font-black text-2xl shadow-md">
          🎓
        </div>
        <div>
          <h1 class="text-2xl font-black text-blue-950 uppercase tracking-tight">{{ $school->school_name ?? 'DASA EDUGROUP' }}</h1>
          <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mt-0.5">OFFICIAL STUDENT ADMISSION APPLICATION FORM</p>
          <p class="text-[11px] text-slate-500 font-semibold">Academic Year: {{ $academicYear?->name ?? '2025-2026' }}</p>
        </div>
      </div>

      <div class="text-right space-y-1">
        <div class="text-xs font-mono font-bold text-slate-700">App No: <span class="paper-line w-28"></span></div>
        <div class="text-xs font-mono font-bold text-slate-700">Date: <span class="paper-line w-28"></span></div>
      </div>
    </div>

    {{-- Section 1 Title --}}
    <div class="bg-blue-900 text-white px-4 py-2 rounded-xl flex items-center justify-between">
      <h2 class="text-xs font-black uppercase tracking-wider">SECTION 1: STUDENT PERSONAL DETAILS</h2>
      <span class="text-[10px] font-mono font-bold uppercase text-blue-200">Page 1 of 3</span>
    </div>

    {{-- Form Fields Grid --}}
    <div class="space-y-4 text-xs">
      
      {{-- Student Names --}}
      <div class="grid grid-cols-2 gap-6">
        <div>
          <span class="font-bold text-slate-800">Student First Name <span class="text-rose-600">*</span>:</span>
          <div class="paper-line w-full mt-1"></div>
        </div>
        <div>
          <span class="font-bold text-slate-800">Student Last Name:</span>
          <div class="paper-line w-full mt-1"></div>
        </div>
      </div>

      {{-- Email & DOB & Gender --}}
      <div class="grid grid-cols-3 gap-4">
        <div>
          <span class="font-bold text-slate-800">Student Email Address:</span>
          <div class="paper-line w-full mt-1"></div>
        </div>

        <div>
          <span class="block font-bold text-slate-800 mb-1">Date of Birth <span class="text-rose-600">*</span>:</span>
          <div class="flex items-center gap-1 font-mono">
            <span class="digit-box">D</span><span class="digit-box">D</span>
            <span class="text-slate-400 font-bold mx-0.5">/</span>
            <span class="digit-box">M</span><span class="digit-box">M</span>
            <span class="text-slate-400 font-bold mx-0.5">/</span>
            <span class="digit-box">Y</span><span class="digit-box">Y</span><span class="digit-box">Y</span><span class="digit-box">Y</span>
          </div>
        </div>

        <div>
          <span class="block font-bold text-slate-800 mb-1.5">Gender <span class="text-rose-600">*</span>:</span>
          <div class="flex items-center gap-4 font-semibold text-slate-700">
            <label><span class="checkbox-box"></span> Male</label>
            <label><span class="checkbox-box"></span> Female</label>
            <label><span class="checkbox-box"></span> Other</label>
          </div>
        </div>
      </div>

      {{-- Standard --}}
      <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
        <span class="font-bold text-slate-900 block mb-1">Applying for Standard / Class <span class="text-rose-600">*</span>:</span>
        <div class="paper-line w-full"></div>
      </div>

      {{-- Source --}}
      <div>
        <span class="block font-bold text-slate-800 mb-1.5">Enquiry Source:</span>
        <div class="flex flex-wrap items-center gap-6 text-slate-700">
          <label><span class="checkbox-box"></span> Walk-in</label>
          <label><span class="checkbox-box"></span> Online / Website</label>
          <label><span class="checkbox-box"></span> Referral</label>
          <label><span class="checkbox-box"></span> Advertisement</label>
        </div>
      </div>

      {{-- Demographics --}}
      <div class="space-y-3 pt-2">
        <h3 class="font-extrabold text-blue-900 uppercase text-[11px] border-b border-slate-200 pb-1">Demographic Details</h3>

        {{-- Blood Group --}}
        <div>
          <span class="font-bold text-slate-800 block mb-1">Blood Group:</span>
          <div class="flex flex-wrap items-center gap-4 text-slate-700">
            <label><span class="checkbox-box"></span> A+</label>
            <label><span class="checkbox-box"></span> A-</label>
            <label><span class="checkbox-box"></span> B+</label>
            <label><span class="checkbox-box"></span> B-</label>
            <label><span class="checkbox-box"></span> O+</label>
            <label><span class="checkbox-box"></span> O-</label>
            <label><span class="checkbox-box"></span> AB+</label>
            <label><span class="checkbox-box"></span> AB-</label>
          </div>
        </div>

        {{-- Category --}}
        <div>
          <span class="font-bold text-slate-800 block mb-1">Category / Community:</span>
          <div class="flex flex-wrap items-center gap-4 text-slate-700">
            <label><span class="checkbox-box"></span> General</label>
            <label><span class="checkbox-box"></span> OBC</label>
            <label><span class="checkbox-box"></span> SC</label>
            <label><span class="checkbox-box"></span> ST</label>
            <label><span class="checkbox-box"></span> EWS</label>
            <label><span class="checkbox-box"></span> Minority</label>
          </div>
        </div>

        {{-- Religion & Mother Tongue --}}
        <div class="grid grid-cols-2 gap-6">
          <div>
            <span class="font-bold text-slate-800">Religion:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Mother Tongue:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>

        {{-- Aadhaar & Pincode --}}
        <div class="grid grid-cols-2 gap-6 pt-1">
          <div>
            <span class="block font-bold text-slate-800 mb-1">Aadhaar Number (12 Digits):</span>
            <div class="flex items-center gap-1 font-mono">
              <span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span>
              <span class="text-slate-400 font-bold mx-1">-</span>
              <span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span>
              <span class="text-slate-400 font-bold mx-1">-</span>
              <span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span>
            </div>
          </div>

          <div>
            <span class="block font-bold text-slate-800 mb-1">Pincode (6 Digits):</span>
            <div class="flex items-center gap-1 font-mono">
              <span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span>
              <span class="digit-box"></span><span class="digit-box"></span><span class="digit-box"></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- ── PAGE 2: PARENT & GUARDIAN DETAILS & ADDRESS ──────────── --}}
  <div class="max-w-4xl mx-auto print-page rounded-3xl shadow-xl border border-slate-200 p-8 space-y-5">
    
    {{-- Section 2 Title --}}
    <div class="bg-blue-900 text-white px-4 py-2 rounded-xl flex items-center justify-between">
      <h2 class="text-xs font-black uppercase tracking-wider">SECTION 2: PARENT &amp; GUARDIAN INFORMATION</h2>
      <span class="text-[10px] font-mono font-bold uppercase text-blue-200">Page 2 of 3</span>
    </div>

    <div class="space-y-5 text-xs">
      
      {{-- Father Details --}}
      <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
        <h3 class="font-extrabold text-indigo-900 uppercase text-xs border-b border-slate-200 pb-1">Father / Primary Parent Details</h3>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="font-bold text-slate-800">Father / Primary Parent Name <span class="text-rose-600">*</span>:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Father Mobile Number <span class="text-rose-600">*</span>:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="font-bold text-slate-800">Father Email Address:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Father Occupation:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
      </div>

      {{-- Mother Details --}}
      <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
        <h3 class="font-extrabold text-rose-900 uppercase text-xs border-b border-slate-200 pb-1">Mother Details</h3>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="font-bold text-slate-800">Mother Name:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Mother Mobile Number:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="font-bold text-slate-800">Mother Email Address:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Mother Occupation:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
      </div>

      {{-- Guardian Details --}}
      <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
        <h3 class="font-extrabold text-amber-900 uppercase text-xs border-b border-slate-200 pb-1">Guardian Details (If Applicable)</h3>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <span class="font-bold text-slate-800">Guardian Name:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Relation to Student:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Guardian Mobile Number:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
      </div>

      {{-- Address & Income --}}
      <div class="space-y-3 pt-2">
        <h3 class="font-extrabold text-blue-900 uppercase text-xs border-b border-slate-200 pb-1">Address &amp; Annual Family Income</h3>

        <div>
          <span class="font-bold text-slate-800">Residential Address:</span>
          <div class="paper-line w-full mt-1"></div>
          <div class="paper-line w-full mt-2"></div>
        </div>

        <div>
          <span class="font-bold text-slate-800">Permanent Address (If different from Residential):</span>
          <div class="paper-line w-full mt-1"></div>
          <div class="paper-line w-full mt-2"></div>
        </div>

        <div class="w-1/2 pt-2">
          <span class="font-bold text-slate-800">Annual Family Income (₹):</span>
          <div class="paper-line w-full mt-1"></div>
        </div>
      </div>

    </div>
  </div>

  {{-- ── PAGE 3: ACADEMICS, HOSTEL, PAYMENT TERMS & OFFICE USE ──── --}}
  <div class="max-w-4xl mx-auto print-page rounded-3xl shadow-xl border border-slate-200 p-8 space-y-5">
    
    {{-- Section 3 Title --}}
    <div class="bg-blue-900 text-white px-4 py-2 rounded-xl flex items-center justify-between">
      <h2 class="text-xs font-black uppercase tracking-wider">SECTION 3: FACILITIES, ACTIVITIES, DECLARATION &amp; OFFICE USE</h2>
      <span class="text-[10px] font-mono font-bold uppercase text-blue-200">Page 3 of 3</span>
    </div>

    <div class="space-y-4 text-xs">
      
      {{-- Hostel & Extra Curricular Activities --}}
      <div class="grid grid-cols-1 gap-4">
        <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200">
          <span class="font-bold text-amber-950 block mb-1">Hostel Facility Required:</span>
          <div class="flex items-center gap-6 font-bold text-amber-900">
            <label><span class="checkbox-box"></span> Yes (Requires boarding lodging &amp; dining mess)</label>
            <label><span class="checkbox-box"></span> No (Day Scholar)</label>
          </div>
        </div>

        <div>
          <span class="font-bold text-slate-900 block mb-2">Extra-Curricular Activities (Select Preferred Activities):</span>
          <div class="grid grid-cols-4 gap-2 font-medium text-slate-800 bg-slate-50 p-3 rounded-2xl border border-slate-200">
            @foreach($activities as $act)
              <label class="truncate"><span class="checkbox-box"></span> {{ $act['name'] }}</label>
            @endforeach
            <label><span class="checkbox-box"></span> Western Dance</label>
            <label><span class="checkbox-box"></span> Classical Dance</label>
            <label><span class="checkbox-box"></span> Yoga Class</label>
            <label><span class="checkbox-box"></span> Skating</label>
            <label><span class="checkbox-box"></span> School Band</label>
            <label><span class="checkbox-box"></span> Keyboard Class</label>
            <label><span class="checkbox-box"></span> Kungfu / Karate</label>
            <label><span class="checkbox-box"></span> Swimming Class</label>
          </div>
        </div>
      </div>

      {{-- Previous School Details --}}
      <div class="space-y-2 pt-1">
        <h3 class="font-extrabold text-blue-900 uppercase text-xs border-b border-slate-200 pb-1">Previous Academic History (Optional)</h3>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <span class="font-bold text-slate-800">Previous School Name:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Previous Class Passed:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span class="font-bold text-slate-800">Marks Percentage / CGPA:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
      </div>

      {{-- Payment Terms & Mode Preferences --}}
      <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
        <h3 class="font-extrabold text-slate-900 uppercase text-[11px]">Fee Payment Preference</h3>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="font-bold text-slate-800 block mb-1">Preferred Payment Terms:</span>
            <div class="flex items-center gap-4 font-semibold text-slate-700">
              <label><span class="checkbox-box"></span> Single Payment</label>
              <label><span class="checkbox-box"></span> 2 Terms</label>
              <label><span class="checkbox-box"></span> 3 Terms</label>
            </div>
          </div>
          <div>
            <span class="font-bold text-slate-800 block mb-1">Preferred Payment Mode:</span>
            <div class="flex items-center gap-4 font-semibold text-slate-700">
              <label><span class="checkbox-box"></span> UPI</label>
              <label><span class="checkbox-box"></span> Net Banking</label>
              <label><span class="checkbox-box"></span> Cash</label>
            </div>
          </div>
        </div>
      </div>

      {{-- Additional Notes --}}
      <div>
        <span class="font-bold text-slate-800">Additional Notes / Medical / Special Instructions:</span>
        <div class="paper-line w-full mt-1"></div>
        <div class="paper-line w-full mt-2"></div>
      </div>

      {{-- Parent Declaration --}}
      <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-200 space-y-2">
        <h3 class="font-extrabold text-blue-950 uppercase text-[11px]">Parent / Guardian Declaration</h3>
        <p class="text-[10px] text-slate-600 leading-snug font-medium">
          I hereby declare that all details provided in this admission form are true and accurate to the best of my knowledge. I agree to abide by the rules, regulations, and fee policies of {{ $school->school_name ?? 'DASA EDUGROUP' }}.
        </p>
        <div class="grid grid-cols-2 gap-8 pt-6">
          <div>
            <span class="font-bold text-slate-800">Date:</span> <span class="paper-line w-36"></span>
          </div>
          <div class="text-right">
            <span class="font-bold text-slate-800">Parent / Guardian Signature:</span> <span class="paper-line w-44"></span>
          </div>
        </div>
      </div>

      {{-- OFFICE USE ONLY BLOCK --}}
      <div class="p-4 bg-slate-100 rounded-2xl border-2 border-slate-400 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-300 pb-1">
          <h3 class="font-black text-slate-900 uppercase text-xs tracking-wider">OFFICE USE ONLY (STAFF / ADMISSIONS VERIFICATION)</h3>
          <span class="text-[9px] font-mono font-bold text-slate-500">FOR SCHOOL STAFF ONLY</span>
        </div>

        <div class="grid grid-cols-4 gap-3 text-[11px] font-bold text-slate-800">
          <div>Adm No: <span class="paper-line w-20"></span></div>
          <div>Roll No: <span class="paper-line w-20"></span></div>
          <div>Class Allotted: <span class="paper-line w-16"></span></div>
          <div>Sec: <span class="paper-line w-10"></span></div>
        </div>

        <div class="grid grid-cols-3 gap-3 text-[11px] font-bold text-slate-800">
          <div>Total Fee Fixed: ₹ <span class="paper-line w-20"></span></div>
          <div>Amount Collected: ₹ <span class="paper-line w-20"></span></div>
          <div>Receipt No: <span class="paper-line w-20"></span></div>
        </div>

        <div class="grid grid-cols-3 gap-4 pt-3 text-[10px] font-bold text-slate-700">
          <div>
            <span>Counselor Verification:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span>Accounts Verification:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
          <div>
            <span>Principal Approval:</span>
            <div class="paper-line w-full mt-1"></div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Auto-trigger print dialog when opened in standalone mode
    window.addEventListener('load', () => {
      if (window.location.search.includes('print=true')) {
        setTimeout(() => window.print(), 300);
      }
    });
  </script>
</body>
</html>
