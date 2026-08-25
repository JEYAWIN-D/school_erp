@extends('layouts.app')

@section('title', 'Fees Structure — ' . ($academicYear?->name ?? '2025-2026'))

@section('content')
<div class="space-y-6" x-data="{
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  standardFees: {{ json_encode($standardFees) }},
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

  {{-- Top Action Header Bar (Back button) --}}
  <div class="flex items-center justify-between gap-3">
    <a href="{{ route('admissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-xs border border-slate-200 shadow-2xs">
      <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      <span>Back</span>
    </a>
  </div>

  {{-- ── Banner Card Header ───────────────────────────────────── --}}
  <div class="bg-blue-600 rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md relative overflow-hidden">
    <div class="space-y-1.5 max-w-2xl">
      <div class="flex items-center gap-2">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-blue-500/60 border border-blue-400/40 text-blue-100">
          ADMISSION MODULE
        </span>
        <span class="text-xs text-blue-200 font-medium">&bull; Academic Year {{ $academicYear?->name ?? '2025–2026' }}</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Fees Structure</h1>
      <p class="text-blue-100 text-xs sm:text-sm leading-relaxed">
        View studies fee structure and hostel fees for each standard from Pre-KG to Class 12. Click + New Admission to register.
      </p>
    </div>

    <div class="flex-shrink-0">
      <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-blue-50 font-bold px-5 py-3 rounded-2xl text-sm shadow-sm transition">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── Select Standard / Grade Tab Bar ──────────────────────── --}}
  <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
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
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 text-xs font-bold transition">
        &larr; &mdash; Previous Class
      </button>

      <button type="button"
              @click="nextClass()"
              :disabled="selectedIndex === classList.length - 1"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 text-xs font-bold transition">
        Next Class &mdash; &rarr;
      </button>
    </div>
  </div>

  {{-- ── Selected Class Hero Card ──────────────────────────────── --}}
  <div class="bg-blue-600 rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md">
    <div class="space-y-2">
      <span class="inline-block px-3 py-1 bg-blue-500/60 border border-blue-400/40 rounded-full text-[11px] font-bold text-blue-100 uppercase tracking-wider"
            x-text="currentFee.tier ? currentFee.tier + ' Fee Structure Form' : 'Basic Fee Structure Form'">
      </span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white" x-text="currentClass.name"></h2>
      <p class="text-blue-100 text-xs sm:text-sm">Official annual fees breakdown for studies & hostel</p>
    </div>

    <div class="bg-blue-700/80 border border-blue-400/30 rounded-2xl p-4 sm:p-5 text-right min-w-[240px] flex flex-col items-end">
      <span class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">TOTAL BASIC ACADEMIC FEE</span>
      <p class="text-3xl sm:text-4xl font-black text-white font-mono mt-1" x-text="formatMoney(currentFee.total_basic)"></p>
      <span class="text-[11px] text-blue-200 font-medium">per annum</span>
    </div>
  </div>

  {{-- ── Studies & Academic Fees Section ─────────────────────── --}}
  <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-6">
    <div class="flex items-center justify-between">
      <h3 class="font-bold text-slate-900 text-base flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm border border-blue-100">📖</span>
        <span>Studies & Academic Fees (Basic Form)</span>
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
        <p class="text-[11px] text-slate-400 font-medium">Textbooks & learning materials</p>
      </div>

      <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Exam Fees</p>
        <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.exam_fee)"></p>
        <p class="text-[11px] text-slate-400 font-medium">Term assessments & exams</p>
      </div>

      <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-1">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Lab / Computer Fees</p>
        <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.lab_fee)"></p>
        <p class="text-[11px] text-slate-400 font-medium">Lab maintenance & computer</p>
      </div>
    </div>

    {{-- Total Basic Banner --}}
    <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs">
          ✓
        </div>
        <div>
          <h4 class="text-sm font-bold text-slate-900">Total Basic Academic Fees</h4>
          <p class="text-xs text-blue-600 font-medium">Includes Tuition, Books, Exam & Lab charges</p>
        </div>
      </div>

      <div class="text-right">
        <p class="text-2xl sm:text-3xl font-black text-blue-600 font-mono" x-text="formatMoney(currentFee.total_basic)"></p>
      </div>
    </div>
  </div>

  {{-- ── Hostel Fees Section (Yellow/Amber Card) ─────────────── --}}
  <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm border border-amber-200">🛏️</span>
        <div>
          <h3 class="font-bold text-amber-950 text-base">Hostel Fees (Below Total Fees)</h3>
          <p class="text-xs text-amber-700">Residential lodging & mess charges for <span class="font-semibold" x-text="currentClass.name"></span></p>
        </div>
      </div>

      <span class="px-3 py-1 rounded-full bg-white/90 border border-amber-300 text-amber-800 text-xs font-bold self-start sm:self-auto">Per Annum</span>
    </div>

    {{-- Hostel Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="bg-white p-5 rounded-2xl border border-amber-200/80 flex items-center justify-between">
        <div>
          <p class="text-xs font-bold text-slate-500">Hostel & Dining Mess Fee</p>
          <p class="text-2xl font-black text-amber-950 font-mono mt-1" x-text="formatMoney(currentFee.hostel_annual)"></p>
        </div>
        <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-xs font-bold font-mono" x-text="'₹' + (currentFee.hostel_monthly || 2500) + '/mo'"></span>
      </div>

      <div class="bg-white/60 p-5 rounded-2xl border border-amber-200/60 flex items-center justify-between">
        <div>
          <p class="text-xs font-bold text-slate-400">Total (Academic + Hostel)</p>
          <p class="text-2xl font-black text-amber-900/40 font-mono mt-1" x-text="formatMoney(currentFee.combined_total)"></p>
        </div>
        <span class="text-xs font-medium text-amber-700/60">Combined Total</span>
      </div>
    </div>
  </div>

  {{-- ── Bottom Action Bar ────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
    <span class="text-xs text-slate-500 font-semibold">
      Standard: <strong class="text-slate-900" x-text="currentClass.name"></strong>
    </span>

    <a :href="'{{ route('admissions.create') }}?class_id=' + currentClass.id"
       class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-7 py-3 rounded-2xl text-sm shadow-xs transition">
      <span x-text="'Apply for ' + currentClass.name + ' →'"></span>
    </a>
  </div>

</div>
@endsection
