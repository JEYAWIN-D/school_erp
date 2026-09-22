@extends('layouts.app')

@section('title', 'Fees Structure — Erode Public School (' . ($academicYear?->name ?? '2026–2027') . ')')

@section('content')
<div class="space-y-6" x-data="{
  feeViewMode: 'card',
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  standardFees: {{ json_encode($standardFees) }},
  selectedIndex: 0,
  get currentClass() {
    return this.classList[this.selectedIndex] || { id: '', name: 'Pre-KG' };
  },
  get currentFee() {
    return this.standardFees[this.currentClass.id] || {
      term1_fee: 21000, term2_fee: 10000, term3_fee: 10000, material_fee: 0,
      admission_fee: 2500, total_basic: 41000, tier: 'Pre-KG'
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
    <a href="{{ route('admissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold transition text-xs border border-slate-200 shadow-2xs">
      <svg class="w-3.5 h-3.5 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      <span>Back to Admissions</span>
    </a>

    <div class="flex items-center gap-2">
      <a href="{{ route('admissions.print-form', ['form' => 'admission']) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition">
        📄 Admission Form
      </a>
      <a href="{{ route('admissions.print-form', ['form' => 'grade11']) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition">
        🎓 Grade XI Form
      </a>
    </div>
  </div>

  {{-- ── Banner Card Header: Erode Public School Crimson Maroon ── --}}
  <div class="rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-lg relative overflow-hidden"
       style="background: linear-gradient(135deg, #731E1C 0%, #8C2826 55%, #A83230 100%);">
    <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

    <div class="flex items-center gap-4 z-10">
      <div class="w-14 h-14 rounded-2xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0 border border-white/30">
        <img src="{{ asset('images/school-seal-badge.png') }}" alt="Erode Public School" class="w-full h-full object-contain">
      </div>

      <div class="space-y-1.5 max-w-2xl">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-white/15 border border-white/20 text-amber-200">
            ERODE PUBLIC SCHOOL
          </span>
          <span class="text-xs text-rose-100/90 font-medium">&bull; Academic Year {{ $academicYear?->name ?? '2026–2027' }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Official Fees Structure</h1>
      </div>
    </div>

    <div class="flex-shrink-0 z-10">
      <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-white text-[#8C2826] hover:bg-rose-50 font-black px-5 py-3 rounded-2xl text-sm shadow-md transition">
        <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── Select Standard / Grade Tab Bar ──────────────────────── --}}
  <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <span class="text-xs font-extrabold text-[#8C2826] uppercase tracking-wider">SELECT STANDARD / GRADE</span>
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
                :class="selectedIndex === idx ? 'bg-[#8C2826] text-white shadow-xs font-black' : 'bg-slate-100 text-slate-700 hover:bg-rose-50 hover:text-[#8C2826]'">
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

  {{-- ── Selected Class Hero Card ──────────────────────────────── --}}
  <div class="rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md"
       style="background: linear-gradient(135deg, #731E1C 0%, #8C2826 100%);">
    <div class="space-y-2">
      <span class="inline-block px-3 py-1 bg-white/15 border border-white/20 rounded-full text-[11px] font-bold text-amber-200 uppercase tracking-wider"
            x-text="currentFee.tier ? currentFee.tier + ' Fee Structure Form' : 'Fee Structure Form'">
      </span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white" x-text="currentClass.name"></h2>
      <p class="text-rose-100 text-xs sm:text-sm">Official 3-term academic fee schedule for Academic Year 2026–2027</p>
    </div>

    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
      <div class="bg-black/20 border border-white/20 rounded-2xl p-4 sm:p-5 text-right min-w-[220px] flex flex-col items-end">
        <span class="text-[10px] font-bold text-amber-200 uppercase tracking-widest">TOTAL ANNUAL ACADEMIC FEE</span>
        <p class="text-3xl sm:text-4xl font-black text-white font-mono mt-1" x-text="formatMoney(currentFee.total_basic)"></p>
        <span class="text-[11px] text-rose-200 font-medium">per annum</span>
      </div>

      <a :href="'/admissions/fee-structure/' + currentClass.id + '/print?print=true'" target="_blank" class="px-5 py-3.5 rounded-2xl bg-white hover:bg-rose-50 text-[#8C2826] font-black text-xs shadow-md transition cursor-pointer flex items-center gap-2 shrink-0" title="Print Fee Structure">
        <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Fee Structure</span>
      </a>
    </div>
  </div>

  {{-- ── Studies & Academic Fees Section (Real 3-Term Breakdown) ──── --}}
  <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div class="flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-rose-50 text-[#8C2826] flex items-center justify-center font-bold text-sm border border-rose-200">📖</span>
        <div>
          <h3 class="font-black text-slate-900 text-base">Official Academic Fee Schedule (2026–2027)</h3>
          <p class="text-xs text-slate-500 font-medium">Class: <span class="font-bold text-[#8C2826]" x-text="currentClass.name"></span> &bull; 3 Terms Breakdown</p>
        </div>
      </div>

      {{-- View Mode Toggle --}}
      <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold">
        <button type="button" @click="feeViewMode = 'card'"
                :class="feeViewMode === 'card' ? 'bg-white text-[#8C2826] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer">
          Card View
        </button>
        <button type="button" @click="feeViewMode = 'table'"
                :class="feeViewMode === 'table' ? 'bg-white text-[#8C2826] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer">
          All Standards Table
        </button>
      </div>
    </div>

    {{-- 1. MODE 1: 4 Component Cards Grid --}}
    <div x-show="feeViewMode === 'card'" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-50/90 p-4 rounded-2xl border border-slate-200/80 space-y-1 hover:border-rose-200 transition">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">April I Term</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.term1_fee)"></p>
          <p class="text-[11px] text-[#8C2826] font-semibold">Due Date: 01.04.2026</p>
        </div>

        <div class="bg-slate-50/90 p-4 rounded-2xl border border-slate-200/80 space-y-1 hover:border-rose-200 transition">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Aug II Term</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.term2_fee)"></p>
          <p class="text-[11px] text-[#8C2826] font-semibold">Due Date: 05.08.2026</p>
        </div>

        <div class="bg-slate-50/90 p-4 rounded-2xl border border-slate-200/80 space-y-1 hover:border-rose-200 transition">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Dec III Term</p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="formatMoney(currentFee.term3_fee)"></p>
          <p class="text-[11px] text-[#8C2826] font-semibold">Due Date: 05.12.2026</p>
        </div>

        <div class="bg-slate-50/90 p-4 rounded-2xl border border-slate-200/80 space-y-1 hover:border-rose-200 transition">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide" x-text="currentFee.material_fee > 0 ? 'Material Fee' : 'New Admission Fee'"></p>
          <p class="text-2xl font-black text-slate-900 font-mono" x-text="currentFee.material_fee > 0 ? formatMoney(currentFee.material_fee) : formatMoney(currentFee.admission_fee)"></p>
          <p class="text-[11px] text-slate-500 font-medium" x-text="currentFee.material_fee > 0 ? 'Due Date: 10.01.2026' : '₹2,500 extra for new adm.'"></p>
        </div>
      </div>

      {{-- Integrated Coaching Option for Grade XI / XII --}}
      <div x-show="currentFee.has_integrated" class="p-5 rounded-2xl bg-gradient-to-r from-amber-50/80 to-rose-50/80 border border-amber-200 space-y-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-sm font-extrabold text-slate-900 uppercase">🎓 Grade XI (Integrated) NEET / JEE Coaching Fee</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#8C2826] text-white">Integrated</span>
          </div>
          <span class="text-base font-black font-mono text-[#8C2826]" x-text="formatMoney(currentFee.integrated_fee || 105000)"></span>
        </div>
        <div class="grid grid-cols-3 gap-3 text-xs">
          <div class="bg-white p-3 rounded-xl border border-amber-200/60">
            <span class="text-[10px] text-slate-500 block uppercase font-bold">April I Term</span>
            <span class="font-mono font-bold text-[#8C2826] text-sm" x-text="formatMoney(currentFee.integrated_term1 || 52500)"></span>
          </div>
          <div class="bg-white p-3 rounded-xl border border-amber-200/60">
            <span class="text-[10px] text-slate-500 block uppercase font-bold">Aug II Term</span>
            <span class="font-mono font-bold text-[#8C2826] text-sm" x-text="formatMoney(currentFee.integrated_term2 || 32500)"></span>
          </div>
          <div class="bg-white p-3 rounded-xl border border-amber-200/60">
            <span class="text-[10px] text-slate-500 block uppercase font-bold">Dec III Term</span>
            <span class="font-mono font-bold text-[#8C2826] text-sm" x-text="formatMoney(currentFee.integrated_term3 || 20000)"></span>
          </div>
        </div>
      </div>

      {{-- Verified Total Academic Fee Banner --}}
      <div class="bg-rose-50/70 border border-rose-200/90 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#8C2826] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs">
            ✓
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">Total Annual Academic Fee (2026–2027)</h4>
            <p class="text-xs text-slate-600 font-medium">Sum of April I + Aug II + Dec III Terms (plus Material Fee if Grade X)</p>
          </div>
        </div>

        <div class="text-right">
          <p class="text-2xl sm:text-3xl font-black text-[#8C2826] font-mono" x-text="formatMoney(currentFee.total_basic)"></p>
        </div>
      </div>
    </div>

    {{-- 2. MODE 2: All Standards Comparison Table --}}
    <div x-show="feeViewMode === 'table'" class="overflow-x-auto">
      <table class="w-full text-xs text-left">
        <thead class="bg-slate-50 text-slate-700 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
          <tr>
            <th class="py-3.5 px-4">Standard / Grade</th>
            <th class="py-3.5 px-4 text-right">April (Term I)</th>
            <th class="py-3.5 px-4 text-right">August (Term II)</th>
            <th class="py-3.5 px-4 text-right">December (Term III)</th>
            <th class="py-3.5 px-4 text-right">Material / Adm</th>
            <th class="py-3.5 px-4 text-right font-black">Total Annual Fee</th>
            <th class="py-3.5 px-4 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          <template x-for="(cls, idx) in classList" :key="cls.id">
            <tr class="hover:bg-rose-50/40 transition cursor-pointer"
                :class="selectedIndex === idx ? 'bg-rose-50/60 font-bold' : ''"
                @click="selectedIndex = idx; feeViewMode = 'card'">
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" :class="selectedIndex === idx ? 'bg-[#8C2826]' : 'bg-transparent'"></span>
                  <span class="font-bold text-slate-900" x-text="cls.name"></span>
                  <span class="text-[10px] text-slate-400 font-normal" x-text="standardFees[cls.id]?.tier ? '(' + standardFees[cls.id].tier + ')' : ''"></span>
                </div>
              </td>
              <td class="py-3.5 px-4 font-mono text-slate-700 text-right" x-text="formatMoney(standardFees[cls.id]?.term1_fee)"></td>
              <td class="py-3.5 px-4 font-mono text-slate-700 text-right" x-text="formatMoney(standardFees[cls.id]?.term2_fee)"></td>
              <td class="py-3.5 px-4 font-mono text-slate-700 text-right" x-text="formatMoney(standardFees[cls.id]?.term3_fee)"></td>
              <td class="py-3.5 px-4 font-mono text-slate-500 text-right" x-text="standardFees[cls.id]?.material_fee > 0 ? formatMoney(standardFees[cls.id]?.material_fee) + ' (Material)' : '₹2,500 (New Adm)'"></td>
              <td class="py-3.5 px-4 font-mono text-right font-black text-[#8C2826] text-sm" x-text="formatMoney(standardFees[cls.id]?.total_basic)"></td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold transition"
                      :class="selectedIndex === idx ? 'bg-[#8C2826] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                  Select &rarr;
                </span>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Important Notes & Due Dates Box --}}
  <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/80 space-y-2">
    <div class="flex items-center gap-2 text-amber-950 font-extrabold text-xs">
      <span class="w-5 h-5 rounded-full bg-amber-700 text-white flex items-center justify-center text-[10px]">i</span>
      <span class="uppercase tracking-wider">Important Notes &amp; Fee Due Dates (2026–2027)</span>
    </div>
    <ul class="text-xs text-slate-700 space-y-1 font-medium pl-6 list-disc">
      <li class="font-bold text-amber-950">New Admission Fee (Pre-KG to Class IX &amp; XI): ₹2,500/- extra one-time for new admissions.</li>
      <li><strong>Term I Due Date:</strong> 01.04.2026 &bull; <strong>Term II Due Date:</strong> 05.08.2026 &bull; <strong>Term III Due Date:</strong> 05.12.2026</li>
      <li><strong>Grade X Material Fee Due Date:</strong> 10.01.2026 (₹19,500)</li>
      <li>All fees are strictly payable on an annual or 3-term basis as scheduled.</li>
      <li>Fees once paid are non-refundable except as per school management policy.</li>
    </ul>
  </div>

  {{-- ── Online Payment Link Card ───────────────────────────── --}}
  <div class="bg-gradient-to-br from-[#8C2826] to-[#731E1C] rounded-2xl p-5 sm:p-6 text-white space-y-4 shadow-md">
    <div class="flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center text-xl flex-shrink-0">🔗</div>
        <div>
          <h3 class="font-extrabold text-white text-sm">Online Payment Link</h3>
          <p class="text-rose-200 text-xs mt-0.5">Share via WhatsApp or SMS — parents pay digitally</p>
        </div>
      </div>
      <span class="px-2.5 py-1 rounded-full bg-amber-400 text-slate-900 text-[10px] font-black uppercase tracking-wide">SECURE</span>
    </div>

    <div class="bg-white/10 border border-white/15 rounded-xl p-3 flex items-center gap-2">
      <span class="font-mono text-xs text-rose-100 flex-1 break-all" x-text="'{{ url('/pay/fee') }}/' + currentClass.id"></span>
      <button @click="navigator.clipboard.writeText('{{ url('/pay/fee') }}/' + currentClass.id).then(()=>{ $el.textContent='Copied!'; setTimeout(()=>$el.textContent='Copy',2000) })"
              class="px-3 py-1.5 bg-white/20 hover:bg-white/30 border border-white/25 rounded-lg text-xs font-bold text-white transition cursor-pointer">Copy</button>
    </div>

    <div class="flex items-center gap-3 flex-wrap">
      <a :href="'https://wa.me/?text=' + encodeURIComponent('Pay ' + currentClass.name + ' fees securely: {{ url('/pay/fee') }}/' + currentClass.id)"
         target="_blank"
         class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-xs">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Share on WhatsApp
      </a>
      <a :href="'/pay/fee/' + currentClass.id" target="_blank"
         class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">
        📱 Open Payment Page
      </a>
    </div>
  </div>

  {{-- ── Bottom Action Bar ────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
    <span class="text-xs text-slate-500 font-semibold">
      Selected Standard: <strong class="text-slate-900" x-text="currentClass.name"></strong>
    </span>

    <a :href="'{{ route('admissions.create') }}?class_id=' + currentClass.id"
       class="inline-flex items-center justify-center gap-2 bg-[#8C2826] hover:bg-[#731E1C] text-white font-black px-7 py-3 rounded-2xl text-sm shadow-md transition">
      <span x-text="'Apply for ' + currentClass.name + ' →'"></span>
    </a>
  </div>

</div>
@endsection
