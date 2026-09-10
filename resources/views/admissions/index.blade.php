@extends('layouts.app')

@section('title', 'Admissions Module — DASA EDUGROUP (' . ($academicYear?->name ?? '2025-2026') . ')')

@section('content')
<div class="space-y-6" x-data="{
  activeTab: '{{ request('status') || request('search') || request('class_id') ? 'enquiries' : 'fee-structure' }}',
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  standardFees: {{ json_encode($standardFees ?? []) }},
  selectedIndex: 0,
  get currentClass() {
    return this.classList[this.selectedIndex] || { id: '', name: 'Pre-KG' };
  },
  get currentFee() {
    return this.standardFees[this.currentClass.id] || {
      tuition_fee: 18000, book_fee: 2500, exam_fee: 1500, lab_fee: 0,
      total_basic: 22000, hostel_annual: 30000, hostel_monthly: 2500, combined_total: 52000, tier: 'Basic Form'
    };
  },
  prevClass() {
    if (this.selectedIndex > 0) this.selectedIndex--;
  },
  nextClass() {
    if (this.selectedIndex < this.classList.length - 1) this.selectedIndex++;
  },
  formatMoney(num) {
    return '₹' + Number(num || 0).toLocaleString('en-IN');
  }
}">

  {{-- ── Banner Card Header ───────────────────────────────────── --}}
  <div class="bg-blue-600 rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md relative overflow-hidden print:hidden">
    <div class="space-y-1.5 max-w-2xl">
      <div class="flex items-center gap-2">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-blue-500/60 border border-blue-400/40 text-blue-100">
          DASA EDUGROUP ADMISSION MODULE
        </span>
        <span class="text-xs text-blue-200 font-medium">&bull; Academic Year {{ $academicYear?->name ?? '2025–2026' }}</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight" x-text="activeTab === 'fee-structure' ? 'Fees Structure' : 'Admission Enquiries'"></h1>
      <p class="text-blue-100 text-xs sm:text-sm leading-relaxed">
        View studies fee structure and hostel fees for each standard from Pre-KG to Class 12. Click + New Admission to register.
      </p>
    </div>

    <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
      <a href="{{ route('admissions.approvals') }}" class="inline-flex items-center gap-2 bg-blue-700/70 hover:bg-blue-700 text-white border border-blue-400/40 font-bold px-4 py-3 rounded-2xl text-sm shadow-sm transition">
        <i class="fas fa-check-double text-xs"></i>
        <span>Approvals Desk</span>
      </a>
      <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-blue-50 font-bold px-5 py-3 rounded-2xl text-sm shadow-sm transition">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── Module View Navigation Tabs ────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3 print:hidden">
    <div class="flex items-center gap-2">
      <button type="button"
              @click="activeTab = 'fee-structure'"
              class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer"
              :class="activeTab === 'fee-structure' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
        <span>Fees Structure</span>
      </button>

      <button type="button"
              @click="activeTab = 'enquiries'"
              class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer"
              :class="activeTab === 'enquiries' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
        <span>Enquiry List</span>
      </button>
    </div>

    <div>
      <a href="{{ route('admissions.approvals') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100 transition shadow-2xs">
        <i class="fas fa-stamp text-amber-600"></i>
        <span>2-Tier Approval Desk (Principal &amp; Admin)</span>
      </a>
    </div>
  </div>

  {{-- ── TAB 1: FEES STRUCTURE VIEW ────────────────────────────── --}}
  <div x-show="activeTab === 'fee-structure'" class="space-y-6">

    {{-- Select Standard / Grade Tab Bar --}}
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4 print:hidden">
      <div class="flex items-center justify-between">
        <span class="text-xs font-extrabold text-blue-600 uppercase tracking-wider">SELECT STANDARD / GRADE</span>
        <span class="text-xs font-semibold text-slate-500">
          Standard <span class="font-bold text-slate-800" x-text="selectedIndex + 1"></span> of <span class="font-bold text-slate-800" x-text="classList.length"></span>
        </span>
      </div>

      {{-- Pills Row --}}
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
        <template x-for="(cls, idx) in classList" :key="cls.id">
          <button type="button"
                  @click="selectedIndex = idx"
                  class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer"
                  :class="selectedIndex === idx ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
            <span x-text="cls.name"></span>
          </button>
        </template>
      </div>

      {{-- Previous / Next Controls --}}
      <div class="flex items-center justify-between pt-2 border-t border-slate-100">
        <button type="button"
                @click="prevClass()"
                :disabled="selectedIndex === 0"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 text-xs font-bold transition cursor-pointer">
          &larr; &mdash; Previous Class
        </button>

        <button type="button"
                @click="nextClass()"
                :disabled="selectedIndex === classList.length - 1"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 text-xs font-bold transition cursor-pointer">
          Next Class &mdash; &rarr;
        </button>
      </div>
    </div>

    {{-- Selected Class Hero Card (Second Header Box with Print Button) --}}
    <div class="bg-blue-600 rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md print:hidden">
      <div class="space-y-2">
        <span class="inline-block px-3 py-1 bg-blue-500/60 border border-blue-400/40 rounded-full text-[11px] font-bold text-blue-100 uppercase tracking-wider"
              x-text="currentFee.tier ? currentFee.tier + ' Fee Structure Form' : 'Basic Fee Structure Form'">
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white" x-text="currentClass.name"></h2>
        <p class="text-blue-100 text-xs sm:text-sm">Official annual fees breakdown for studies &amp; hostel</p>
      </div>

      <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
        <div class="bg-blue-700/80 border border-blue-400/30 rounded-2xl p-4 sm:p-5 text-right min-w-[220px] flex flex-col items-end">
          <span class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">TOTAL BASIC ACADEMIC FEE</span>
          <p class="text-3xl sm:text-4xl font-black text-white font-mono mt-1" x-text="formatMoney(currentFee.total_basic)"></p>
          <span class="text-[11px] text-blue-200 font-medium">per annum</span>
        </div>

        <a :href="'/admissions/fee-structure/' + currentClass.id + '/print?print=true'" target="_blank" class="px-5 py-3.5 rounded-2xl bg-white hover:bg-blue-50 text-blue-700 font-extrabold text-xs shadow-md transition cursor-pointer flex items-center gap-2 shrink-0 print:hidden" title="Print Fee Structure">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          <span>Print Fee Structure</span>
        </a>
      </div>
    </div>

    {{-- Web View: Studies & Academic Fees Section --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-6 print:hidden">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2.5">
          <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm border border-blue-100">📖</span>
          <span>Studies &amp; Academic Fees (Basic Form)</span>
        </h3>
        <span class="px-3 py-1 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold">Basic Form</span>
      </div>

      {{-- 4 Component Cards Grid --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tuition Fees</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.tuition_fee)"></p>
          <p class="text-[11px] text-slate-400 font-medium">Class teaching fee per annum</p>
        </div>

        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Book Fees</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.book_fee)"></p>
          <p class="text-[11px] text-slate-400 font-medium">Textbooks &amp; learning materials</p>
        </div>

        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Exam Fees</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.exam_fee)"></p>
          <p class="text-[11px] text-slate-400 font-medium">Term assessments &amp; exams</p>
        </div>

        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Lab / Computer Fees</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.lab_fee)"></p>
          <p class="text-[11px] text-slate-400 font-medium">Lab maintenance &amp; computer</p>
        </div>
      </div>

      {{-- Total Academic Banner --}}
      <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">✓</div>
          <div>
            <p class="text-xs font-extrabold text-blue-950 uppercase">Total Basic Academic Fees</p>
            <p class="text-[11px] text-blue-700 font-medium">Includes Tuition, Books, Exam &amp; Lab charges</p>
          </div>
        </div>
        <p class="text-2xl font-black text-blue-700 font-mono" x-text="formatMoney(currentFee.total_basic)"></p>
      </div>
    </div>

    {{-- Web View: Hostel Fees Section --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-6 print:hidden">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2.5">
          <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-100">🏡</span>
          <div>
            <span>Hostel Fees (Below Total Fees)</span>
            <p class="text-xs text-amber-700 font-normal">Residential lodging &amp; mess charges for <span class="font-bold" x-text="currentClass.name"></span></p>
          </div>
        </h3>
        <span class="px-3 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-700 text-xs font-bold">Per Annum</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Hostel &amp; Dining Mess Fee</p>
            <p class="text-2xl font-black text-slate-900 font-mono mt-1" x-text="formatMoney(currentFee.hostel_annual || 30000)"></p>
          </div>
          <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-xs font-bold font-mono">₹2500/mo</span>
        </div>

        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total (Academic + Hostel)</p>
            <p class="text-2xl font-black text-blue-700 font-mono mt-1" x-text="formatMoney((currentFee.total_basic || 0) + (currentFee.hostel_annual || 30000))"></p>
          </div>
          <span class="text-xs font-extrabold text-slate-400">Combined Total</span>
        </div>
      </div>
    </div>

    {{-- ── FORMAL PRINT FORMAT FOR FEES STRUCTURE (DASA EDUGROUP) ── --}}
    <div class="hidden print:block print-fee-document space-y-6 bg-white text-slate-900 font-sans p-4">
      {{-- Header --}}
      <div class="flex items-center justify-between border-b-2 border-blue-900 pb-4">
        <div>
          <h1 class="text-3xl font-black text-blue-900 tracking-wider uppercase">DASA EDUGROUP</h1>
          <p class="text-xs font-bold text-slate-600 uppercase tracking-widest mt-0.5">Official Annual Fees Structure Statement</p>
          <p class="text-xs text-slate-500 font-medium">Academic Year: {{ $academicYear?->name ?? '2025-2026' }}</p>
        </div>
        <div class="text-right">
          <div class="inline-block px-4 py-1.5 bg-blue-100 text-blue-950 font-black text-base rounded-xl border border-blue-300 uppercase">
            Standard: <span x-text="currentClass.name"></span>
          </div>
          <p class="text-xs text-slate-500 font-mono mt-1">Date: {{ date('d-m-Y') }}</p>
        </div>
      </div>

      {{-- Academic Fees Table --}}
      <div class="space-y-3">
        <h2 class="text-sm font-extrabold text-blue-950 uppercase tracking-wider border-b border-slate-300 pb-1.5">1. Academic &amp; Studies Fees Breakdown</h2>
        <table class="w-full text-xs text-left border-collapse border border-slate-300">
          <thead>
            <tr class="bg-blue-50 text-blue-950 border-b border-slate-300 font-bold uppercase text-[10px]">
              <th class="py-2.5 px-4 border border-slate-300">Fee Component</th>
              <th class="py-2.5 px-4 border border-slate-300">Description</th>
              <th class="py-2.5 px-4 text-right border border-slate-300">Annual Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 font-medium">
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Tuition Fees</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Class teaching fee per annum</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.tuition_fee)"></td>
            </tr>
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Book Fees</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Textbooks, workbooks &amp; learning materials</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.book_fee)"></td>
            </tr>
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Exam Fees</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Term assessments, evaluation &amp; report cards</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.exam_fee)"></td>
            </tr>
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Lab / Computer Fees</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Science lab maintenance &amp; computer laboratory access</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.lab_fee)"></td>
            </tr>
            <tr class="bg-blue-50/80 font-bold">
              <td colspan="2" class="py-3 px-4 border border-slate-300 text-blue-950 uppercase text-xs">Total Basic Academic Fees</td>
              <td class="py-3 px-4 border border-slate-300 text-right font-mono text-sm text-blue-900" x-text="formatMoney(currentFee.total_basic)"></td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Hostel Fees Table --}}
      <div class="space-y-3 pt-2">
        <h2 class="text-sm font-extrabold text-amber-950 uppercase tracking-wider border-b border-slate-300 pb-1.5">2. Hostel &amp; Boarding Fees (Optional)</h2>
        <table class="w-full text-xs text-left border-collapse border border-slate-300">
          <tbody class="font-medium">
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Hostel &amp; Dining Mess Fee (Annual)</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Residential lodging &amp; dining mess charges</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.hostel_annual || 30000)"></td>
            </tr>
            <tr class="bg-slate-100 font-black">
              <td colspan="2" class="py-3 px-4 border border-slate-300 text-slate-900 uppercase text-xs">Combined Total (Academic + Hostel Fees)</td>
              <td class="py-3 px-4 border border-slate-300 text-right font-mono text-sm text-blue-900" x-text="formatMoney((currentFee.total_basic || 0) + (currentFee.hostel_annual || 30000))"></td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Formal Authorization Footer --}}
      <div class="grid grid-cols-2 gap-12 pt-16 text-xs">
        <div>
          <p class="font-bold text-slate-800">Accounts &amp; Admissions Officer</p>
          <div class="h-12 border-b border-slate-400 border-dashed"></div>
          <p class="text-[10px] text-slate-500 mt-1">DASA EduGroup Admissions Office</p>
        </div>
        <div class="text-right">
          <p class="font-bold text-slate-800">Authorized Signatory</p>
          <div class="h-12 border-b border-slate-400 border-dashed"></div>
          <p class="text-[10px] text-slate-500 mt-1">School Seal / Principal Signature</p>
        </div>
      </div>
    </div>

  </div>

  {{-- ── TAB 2: ENQUIRY LIST VIEW ─────────────────────────────── --}}
  <div x-show="activeTab === 'enquiries'" class="space-y-6 print:hidden">
    {{-- Search & Filters --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
      <form method="GET" action="{{ route('admissions.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or mobile..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500">
        </div>
        <div>
          <select name="class_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700">
            <option value="">All Standards</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>Class {{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
            Filter Enquiries
          </button>
        </div>
      </form>
    </div>

    {{-- Enquiries Data Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3.5 px-4">Enquiry No</th>
              <th class="py-3.5 px-4">Student Name &amp; Prior School</th>
              <th class="py-3.5 px-4">Standard</th>
              <th class="py-3.5 px-4">Parent Details</th>
              <th class="py-3.5 px-4">Referral</th>
              <th class="py-3.5 px-4 text-right">Total Fee</th>
              <th class="py-3.5 px-4 text-right">Collected</th>
              <th class="py-3.5 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($enquiries as $enquiry)
              <tr class="hover:bg-slate-50/60 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                  {{ $enquiry->enquiry_number }}
                </td>
                <td class="py-3.5 px-4">
                  <p class="font-bold text-slate-900">{{ $enquiry->student_name }}</p>
                  @if($enquiry->last_school_studied || $enquiry->last_class_studied)
                    <p class="text-[11px] text-slate-500 mt-0.5">
                      <span class="text-slate-400">Prev:</span> {{ $enquiry->last_school_studied ?: 'Prior School' }}
                      @if($enquiry->last_class_studied)
                        <span class="text-indigo-600 font-semibold">({{ $enquiry->last_class_studied }})</span>
                      @endif
                    </p>
                  @endif
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-700 whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-100 text-xs">
                    Class {{ $enquiry->class?->name ?? '—' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 space-y-0.5">
                  <p class="font-bold text-slate-800">
                    {{ $enquiry->father_name ?: ($enquiry->parent_name ?: '—') }}
                    @if($enquiry->father_occupation)
                      <span class="text-[10px] font-normal text-slate-500">({{ $enquiry->father_occupation }})</span>
                    @endif
                  </p>
                  @if($enquiry->father_mobile || $enquiry->parent_mobile)
                    <p class="font-mono text-[11px] text-slate-500">{{ $enquiry->father_mobile ?: $enquiry->parent_mobile }}</p>
                  @endif
                  @if($enquiry->mother_name)
                    <p class="text-[11px] text-slate-500 pt-0.5">
                      <span class="text-slate-400">Mother:</span> {{ $enquiry->mother_name }}
                      @if($enquiry->mother_occupation)
                        <span class="text-[10px]">({{ $enquiry->mother_occupation }})</span>
                      @endif
                    </p>
                  @endif
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">
                  @if($enquiry->referred_by)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                      {{ $enquiry->referred_by }}
                    </span>
                  @else
                    <span class="text-slate-400 text-xs">—</span>
                  @endif
                </td>
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-right whitespace-nowrap">
                  ₹{{ number_format($enquiry->total_admission_fee ?? 0) }}
                </td>
                <td class="py-3.5 px-4 font-mono font-bold text-emerald-600 text-right whitespace-nowrap">
                  ₹{{ number_format($enquiry->amount_collected ?? 0) }}
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border bg-emerald-100 text-emerald-700 border-emerald-200">
                    {{ ucfirst($enquiry->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="py-12 text-center text-slate-400 font-medium">
                  No admission enquiries recorded yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

{{-- Print Styling --}}
<style>
  @media print {
    @page {
      size: A4;
      margin: 10mm 15mm;
    }
    body * {
      visibility: hidden;
    }
    .print-fee-document, .print-fee-document * {
      visibility: visible;
    }
    .print-fee-document {
      position: absolute;
      left: 0;
      top: 0;
      width: 100% !important;
      max-width: 100% !important;
    }
    .print\:hidden {
      display: none !important;
    }
  }
</style>
@endsection
