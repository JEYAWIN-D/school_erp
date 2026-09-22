<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Structure — {{ $selectedClass?->name ?? 'Standard' }} ({{ $academicYear?->name ?? '2025-2026' }})</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
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
      .page-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
      }
    }
  </style>
</head>
<body class="py-6 px-4 sm:px-6">

  {{-- ── Floating Action Bar (Web Only) ────────────────────────── --}}
  <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-xl">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.index') }}" class="text-xs font-bold text-slate-300 hover:text-white flex items-center gap-1.5">
        &larr; Back to Admissions
      </a>
      <span class="text-slate-600">|</span>
      <span class="text-xs font-semibold text-slate-300">Fee Structure Document Preview</span>
    </div>

    <div class="flex items-center gap-3">
      <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Document</span>
      </button>
    </div>
  </div>

  {{-- ── Main Printable Fee Structure Page ─────────────────────── --}}
  <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden page-card space-y-6 pb-6">

    {{-- Top Curved School Header --}}
    <div class="relative bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white px-8 py-8 flex items-center justify-between overflow-hidden">
      {{-- Background Decorative Shape --}}
      <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>

      <div class="flex items-center gap-5 z-10">
        {{-- Crest Logo --}}
        <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shrink-0 shadow-lg p-1 border border-white/30">
          <img src="{{ asset('images/school-seal-badge.png') }}" alt="Erode Public School" class="w-full h-full object-contain">
        </div>

        <div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white drop-shadow-sm">
            {{ $school->school_name ?? 'DASA EDUGROUP' }}
          </h1>
          <p class="text-xs sm:text-sm font-semibold text-blue-200 tracking-wider mt-0.5">Learn &bull; Grow &bull; Excel</p>
        </div>
      </div>

      {{-- Academic Year Pill --}}
      <div class="z-10 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-5 py-2.5 text-center">
        <span class="block text-[10px] font-black tracking-widest text-blue-200 uppercase">ACADEMIC YEAR</span>
        <span class="block text-base font-extrabold text-white font-mono mt-0.5">{{ $academicYear?->name ?? '2025-2026' }}</span>
      </div>
    </div>

    <div class="px-8 space-y-6">

      {{-- Document Title & Subtitle --}}
      <div class="text-center space-y-1.5 pt-2">
        <div class="inline-flex items-center gap-3 justify-center">
          <span class="h-0.5 w-12 bg-blue-300 rounded-full"></span>
          <h2 class="text-2xl sm:text-3xl font-black text-blue-950 uppercase tracking-wider">FEE STRUCTURE</h2>
          <span class="h-0.5 w-12 bg-blue-300 rounded-full"></span>
        </div>
        <p class="text-base font-extrabold text-indigo-700">
          {{ $selectedClass?->name ?? 'Standard' }} ({{ $currentFee['tier'] ?? 'Basic Fee Structure' }})
        </p>
        <p class="text-xs text-slate-500 font-medium max-w-xl mx-auto">
          The following is the official annual fee structure for the academic year {{ $academicYear?->name ?? '2025-2026' }}.
          All fees are mentioned in Indian Rupees (₹).
        </p>
      </div>

      {{-- ── Card 1: Official Studies & Academic Fees (2026-2027 Schedule) ── --}}
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs">1</span>
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
              OFFICIAL FEE STRUCTURE — {{ $currentFee['official_name'] ?? ($selectedClass?->name ?? 'STANDARD') }}
            </h3>
          </div>
          <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200">2026–2027</span>
        </div>

        {{-- 4 Grid Component Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          {{-- Term 1 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 mx-auto flex items-center justify-center text-base">🌱</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">APRIL I TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term1_fee'] ?? 25000) }}</p>
            <p class="text-[10px] text-blue-700 font-bold">Due Date: 01.04.2026</p>
          </div>

          {{-- Term 2 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 mx-auto flex items-center justify-center text-base">🌿</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">AUG II TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term2_fee'] ?? 11000) }}</p>
            <p class="text-[10px] text-blue-700 font-bold">Due Date: 05.08.2026</p>
          </div>

          {{-- Term 3 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 mx-auto flex items-center justify-center text-base">🌳</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">DEC. III TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term3_fee'] ?? 11000) }}</p>
            <p class="text-[10px] text-blue-700 font-bold">Due Date: 05.12.2026</p>
          </div>

          {{-- Material or Admission Fee --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 mx-auto flex items-center justify-center text-base">📋</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
              {{ ($currentFee['material_fee'] ?? 0) > 0 ? 'MATERIAL FEE' : 'ADMISSION FEE' }}
            </p>
            <p class="text-xl font-black text-slate-900 font-mono">
              ₹{{ number_format(($currentFee['material_fee'] ?? 0) > 0 ? $currentFee['material_fee'] : ($currentFee['admission_fee'] ?? 2500)) }}
            </p>
            <p class="text-[10px] text-slate-500 font-semibold">
              {{ ($currentFee['material_fee'] ?? 0) > 0 ? 'Due: 10.01.2026' : 'Rs.2,500/- extra' }}
            </p>
          </div>
        </div>

        @if(!empty($currentFee['has_integrated']))
        {{-- Integrated Coaching Option --}}
        <div class="p-4 rounded-2xl bg-indigo-50/80 border border-indigo-200 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-indigo-950 uppercase tracking-wide">Grade XI (Integrated Coaching) NEET / JEE</span>
            <span class="font-mono font-black text-indigo-900 text-sm">Total: ₹{{ number_format($currentFee['integrated_fee'] ?? 105000) }}</span>
          </div>
          <div class="grid grid-cols-3 gap-2 text-xs text-center font-mono">
            <div class="bg-white p-2 rounded-xl border border-indigo-100">
              <span class="text-[10px] text-slate-500 block font-sans">April I Term</span>
              <span class="font-bold text-indigo-900">₹{{ number_format($currentFee['integrated_term1'] ?? 52500) }}</span>
            </div>
            <div class="bg-white p-2 rounded-xl border border-indigo-100">
              <span class="text-[10px] text-slate-500 block font-sans">Aug II Term</span>
              <span class="font-bold text-indigo-900">₹{{ number_format($currentFee['integrated_term2'] ?? 32500) }}</span>
            </div>
            <div class="bg-white p-2 rounded-xl border border-indigo-100">
              <span class="text-[10px] text-slate-500 block font-sans">Dec III Term</span>
              <span class="font-bold text-indigo-900">₹{{ number_format($currentFee['integrated_term3'] ?? 20000) }}</span>
            </div>
          </div>
        </div>
        @endif

        {{-- Blue Academic Total Banner --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-extrabold text-sm">✓</div>
            <div>
              <p class="text-xs font-extrabold text-blue-950 uppercase tracking-wider">TOTAL ACADEMIC FEES (2026–2027)</p>
              <p class="text-[11px] text-blue-700 font-medium">April I + Aug II + Dec III Terms (plus Material Fee if applicable)</p>
            </div>
          </div>
          <p class="text-2xl font-black text-blue-800 font-mono">₹{{ number_format($currentFee['total_basic'] ?? 47000) }}</p>
        </div>
      </div>

      {{-- ── Card 2: Hostel Fees ────────────────────────────────── --}}
      <div class="bg-amber-50/50 rounded-3xl border border-amber-200/80 p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-amber-200/60 pb-3">
          <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-amber-800 text-white flex items-center justify-center font-bold text-xs">2</span>
            <div>
              <h3 class="text-sm font-extrabold text-amber-950 uppercase tracking-wider">HOSTEL FEES (BELOW TOTAL FEES)</h3>
              <p class="text-[11px] text-amber-800 font-medium">Residential lodging &amp; mess charges for {{ $selectedClass?->name ?? 'Standard' }}</p>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full bg-amber-100 border border-amber-300 text-amber-900 text-[10px] font-extrabold uppercase">Per Annum</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-white p-4 rounded-2xl border border-amber-200 flex items-center justify-between">
            <div>
              <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">HOSTEL &amp; DINING MESS FEE</p>
              <p class="text-2xl font-black text-slate-900 font-mono mt-0.5">₹{{ number_format($currentFee['hostel_annual'] ?? 30000) }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 text-xs font-bold font-mono">₹{{ number_format($currentFee['hostel_monthly'] ?? 2500) }}/mo</span>
          </div>

          <div class="bg-white p-4 rounded-2xl border border-amber-200 flex items-center justify-between">
            <div>
              <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">TOTAL (ACADEMIC + HOSTEL)</p>
              <p class="text-2xl font-black text-blue-900 font-mono mt-0.5">₹{{ number_format(($currentFee['total_basic'] ?? 22000) + ($currentFee['hostel_annual'] ?? 30000)) }}</p>
            </div>
            <span class="text-xs font-extrabold text-slate-400">Combined Total</span>
          </div>
        </div>
      </div>

      {{-- ── Royal Blue Grand Total Highlight Banner ────────────── --}}
      <div class="bg-gradient-to-r from-blue-950 via-blue-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl border border-blue-800">
        <div>
          <h3 class="text-xs font-black tracking-widest text-blue-200 uppercase">GRAND TOTAL ANNUAL FEE</h3>
          <p class="text-sm font-semibold text-blue-100 mt-1">(Academic + Hostel)</p>
        </div>
        <div class="text-center sm:text-right">
          <p class="text-3xl sm:text-4xl font-black text-white font-mono tracking-tight">₹{{ number_format(($currentFee['total_basic'] ?? 22000) + ($currentFee['hostel_annual'] ?? 30000)) }}</p>
          <p class="text-xs text-blue-200 font-medium mt-0.5">per annum</p>
        </div>
      </div>

      {{-- ── Bottom Info Boxes Grid ────────────────────────────── --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        {{-- Important Notes Box --}}
        <div class="bg-blue-50/70 rounded-2xl p-5 border border-blue-200/80 space-y-2">
          <div class="flex items-center gap-2 text-blue-950 font-extrabold text-xs">
            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px]">i</span>
            <span class="uppercase tracking-wider">Important Notes</span>
          </div>
          <ul class="text-[11px] text-slate-700 space-y-1 font-medium pl-6 list-disc">
            <li class="font-bold text-blue-900">ADMISSION FEES FOR PRE KG TO IX STD &amp; XI: Rs.2500/- extra</li>
            <li class="font-semibold text-slate-800">TERM DUE DATES: Term I: 01.04.2026 &bull; Term II: 05.08.2026 &bull; Term III: 05.12.2026</li>
            <li>Grade IX / X Material Fee Due Date: 10.01.2026</li>
            <li>All fees are payable on an annual or 3-term basis.</li>
            <li>Hostel facility is optional and available on request.</li>
            <li>Fees once paid are non-refundable except as per school policy.</li>
          </ul>
        </div>

        {{-- Thank You Box --}}
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex items-center gap-4">
          <div class="w-12 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-2xl shrink-0 shadow-xs">
            🧾
          </div>
          <div class="space-y-1">
            <p class="text-xs font-extrabold text-slate-900">Thank you for choosing {{ $school->school_name ?? 'DASA EDUGROUP' }}.</p>
            <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
              We look forward to a successful academic journey with your child.
            </p>
          </div>
        </div>
      </div>

      {{-- ── Footer Contact Bar ───────────────────────────────── --}}
      <div class="pt-4 border-t border-slate-200 text-[10px] text-slate-600 flex flex-wrap items-center justify-between gap-3 font-semibold">
        <div class="flex items-center gap-1.5">
          <span>📞</span>
          <span>{{ $school->phone ?? '+91 98765 43210' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span>✉️</span>
          <span>{{ $school->email ?? 'info@dasaedugroup.com' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span>🌐</span>
          <span>{{ $school->website ?? 'www.dasaedugroup.com' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span>📍</span>
          <span>{{ $school->address ?? '123, Education City Campus, India' }}</span>
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

