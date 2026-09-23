<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Structure — {{ $selectedClass?->name ?? 'Standard' }} ({{ $academicYear?->name ?? '2026–2027' }})</title>
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
      <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-[#8C2826] hover:bg-[#731E1C] text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Document</span>
      </button>
    </div>
  </div>

  {{-- ── Main Printable Fee Structure Page ─────────────────────── --}}
  <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden page-card space-y-6 pb-6">

    {{-- Top Curved School Header: Erode Public School Crimson Maroon & Gold --}}
    <div class="relative text-white px-8 py-8 flex items-center justify-between overflow-hidden"
         style="background: linear-gradient(135deg, #731E1C 0%, #8C2826 55%, #A83230 100%);">
      <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-amber-400/15 blur-2xl"></div>

      <div class="flex items-center gap-5 z-10">
        <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shrink-0 shadow-lg p-1 border border-white/30">
          <img src="{{ asset('images/school-seal-badge.png') }}" alt="Erode Public School" class="w-full h-full object-contain">
        </div>

        <div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white drop-shadow-sm">
            {{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}
          </h1>
          <p class="text-xs sm:text-sm font-semibold text-rose-100 tracking-wider mt-0.5">
            {{ $school->tagline ?? 'Affiliated to CBSE, New Delhi (Affiliation No: 1930965)' }}
          </p>
        </div>
      </div>

      {{-- Academic Year Pill --}}
      <div class="z-10 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl px-5 py-2.5 text-center">
        <span class="block text-[10px] font-black tracking-widest text-amber-200 uppercase">ACADEMIC YEAR</span>
        <span class="block text-base font-black text-white font-mono mt-0.5">{{ $academicYear?->name ?? '2026–2027' }}</span>
      </div>
    </div>

    <div class="px-8 space-y-6">

      {{-- Document Title & Subtitle --}}
      <div class="text-center space-y-1.5 pt-2">
        <div class="inline-flex items-center gap-3 justify-center">
          <span class="h-0.5 w-12 bg-rose-200 rounded-full"></span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#8C2826] uppercase tracking-wider">OFFICIAL FEE SCHEDULE</h2>
          <span class="h-0.5 w-12 bg-rose-200 rounded-full"></span>
        </div>
        <p class="text-base font-extrabold text-slate-800">
          {{ $selectedClass?->name ?? 'Standard' }} ({{ $currentFee['tier'] ?? 'Academic Fee Structure' }})
        </p>
        <p class="text-xs text-slate-500 font-medium max-w-xl mx-auto">
          Official 3-term academic fee schedule for Academic Year {{ $academicYear?->name ?? '2026–2027' }}.
          All amounts in Indian Rupees (₹).
        </p>
      </div>

      {{-- ── Academic Fee Breakdown Card ── --}}
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-[#8C2826] text-white flex items-center justify-center font-bold text-xs">1</span>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
              ACADEMIC FEE SCHEDULE — {{ $currentFee['official_name'] ?? ($selectedClass?->name ?? 'STANDARD') }}
            </h3>
          </div>
          <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-[#8C2826] font-extrabold text-xs border border-rose-200">2026–2027</span>
        </div>

        {{-- 4 Component Cards Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          {{-- Term 1 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-8 h-8 rounded-lg bg-rose-100 text-[#8C2826] mx-auto flex items-center justify-center font-bold text-xs">T1</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">APRIL I TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term1_fee'] ?? 21000) }}</p>
            <p class="text-[10px] text-[#8C2826] font-bold">Due Date: 01.04.2026</p>
          </div>

          {{-- Term 2 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-8 h-8 rounded-lg bg-rose-100 text-[#8C2826] mx-auto flex items-center justify-center font-bold text-xs">T2</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">AUG II TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term2_fee'] ?? 10000) }}</p>
            <p class="text-[10px] text-[#8C2826] font-bold">Due Date: 05.08.2026</p>
          </div>

          {{-- Term 3 --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-8 h-8 rounded-lg bg-rose-100 text-[#8C2826] mx-auto flex items-center justify-center font-bold text-xs">T3</div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">DEC III TERM</p>
            <p class="text-xl font-black text-slate-900 font-mono">₹{{ number_format($currentFee['term3_fee'] ?? 10000) }}</p>
            <p class="text-[10px] text-[#8C2826] font-bold">Due Date: 05.12.2026</p>
          </div>

          {{-- Material or Admission Fee --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-1.5">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 mx-auto flex items-center justify-center font-bold text-xs">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
              {{ ($currentFee['material_fee'] ?? 0) > 0 ? 'MATERIAL FEE' : 'ADMISSION FEE' }}
            </p>
            <p class="text-xl font-black text-slate-900 font-mono">
              ₹{{ number_format(($currentFee['material_fee'] ?? 0) > 0 ? $currentFee['material_fee'] : ($currentFee['admission_fee'] ?? 2500)) }}
            </p>
            <p class="text-[10px] text-slate-500 font-semibold">
              {{ ($currentFee['material_fee'] ?? 0) > 0 ? 'Due: 10.01.2026' : '₹2,500 extra (New Adm)' }}
            </p>
          </div>
        </div>

        @if(!empty($currentFee['has_integrated']))
        {{-- Integrated Coaching Option --}}
        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-slate-900 uppercase tracking-wide">Grade XI (Integrated Coaching) NEET / JEE</span>
            <span class="font-mono font-black text-[#8C2826] text-sm">Total: ₹{{ number_format($currentFee['integrated_fee'] ?? 105000) }}</span>
          </div>
          <div class="grid grid-cols-3 gap-2 text-xs text-center font-mono">
            <div class="bg-white p-2 rounded-xl border border-amber-200/60">
              <span class="text-[10px] text-slate-500 block font-sans">April I Term</span>
              <span class="font-bold text-[#8C2826]">₹{{ number_format($currentFee['integrated_term1'] ?? 52500) }}</span>
            </div>
            <div class="bg-white p-2 rounded-xl border border-amber-200/60">
              <span class="text-[10px] text-slate-500 block font-sans">Aug II Term</span>
              <span class="font-bold text-[#8C2826]">₹{{ number_format($currentFee['integrated_term2'] ?? 32500) }}</span>
            </div>
            <div class="bg-white p-2 rounded-xl border border-amber-200/60">
              <span class="text-[10px] text-slate-500 block font-sans">Dec III Term</span>
              <span class="font-bold text-[#8C2826]">₹{{ number_format($currentFee['integrated_term3'] ?? 20000) }}</span>
            </div>
          </div>
        </div>
        @endif

        {{-- Total Academic Banner --}}
        <div class="bg-rose-50/80 border border-rose-200 rounded-2xl p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#8C2826] text-white flex items-center justify-center font-extrabold text-sm shadow-xs">✓</div>
            <div>
              <p class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">TOTAL ANNUAL ACADEMIC FEE (2026–2027)</p>
              <p class="text-[11px] text-[#8C2826] font-semibold">April I + Aug II + Dec III Terms (plus Material Fee if Grade X)</p>
            </div>
          </div>
          <p class="text-3xl font-black text-[#8C2826] font-mono">₹{{ number_format($currentFee['total_basic'] ?? 41000) }}</p>
        </div>
      </div>

      {{-- ── Important Notes & Authorizations Grid ────────────────────────────── --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        {{-- Important Notes Box --}}
        <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/80 space-y-2">
          <div class="flex items-center gap-2 text-amber-950 font-extrabold text-xs">
            <span class="w-5 h-5 rounded-full bg-amber-700 text-white flex items-center justify-center text-[10px]">i</span>
            <span class="uppercase tracking-wider">Important Notes &amp; Fee Due Dates</span>
          </div>
          <ul class="text-[11px] text-slate-700 space-y-1 font-medium pl-6 list-disc">
            <li class="font-bold text-amber-950">New Admission Fee (Pre-KG to Class IX &amp; XI): ₹2,500/- extra one-time.</li>
            <li><strong>Term Due Dates:</strong> Term I: 01.04.2026 &bull; Term II: 05.08.2026 &bull; Term III: 05.12.2026</li>
            <li>Grade X Material Fee Due Date: 10.01.2026 (₹19,500)</li>
            <li>All fees are payable on an annual or 3-term schedule as notified.</li>
            <li>Fees once paid are non-refundable except as per school management policy.</li>
          </ul>
        </div>

        {{-- Formal Authorization Box --}}
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between">
          <div>
            <p class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Official Authorization</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Erode Public School Administration</p>
          </div>
          <div class="grid grid-cols-2 gap-6 pt-8 text-center text-[10px] font-bold text-slate-600">
            <div>
              <div class="border-b border-slate-400 border-dashed pb-1 mb-1"></div>
              <span>Accounts Officer</span>
            </div>
            <div>
              <div class="border-b border-slate-400 border-dashed pb-1 mb-1"></div>
              <span>Principal / Seal</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Thank You Box --}}
      <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs">
          <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="space-y-0.5">
          <p class="text-xs font-extrabold text-slate-900">Thank you for choosing {{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}.</p>
          <p class="text-[11px] text-slate-600 font-medium">
            Chennimalai Road, Erode, Tamil Nadu &bull; CBSE Affiliation No: 1930965
          </p>
        </div>
      </div>

      {{-- ── Footer Contact Bar ───────────────────────────────── --}}
      <div class="pt-4 border-t border-slate-200 text-[10px] text-slate-600 flex flex-wrap items-center justify-between gap-3 font-semibold">
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          <span>{{ $school->phone ?? '+91 98427 88888' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <span>{{ $school->email ?? 'info@erodepublicschool.edu.in' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
          <span>{{ $school->website ?? 'www.erodepublicschool.edu.in' }}</span>
        </div>
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span>{{ $school->address ?? 'Chennimalai Road, Erode, Tamil Nadu' }}</span>
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
