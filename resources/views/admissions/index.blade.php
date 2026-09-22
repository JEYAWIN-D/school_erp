@extends('layouts.app')

@section('title', 'Admissions Module — Erode Public School (' . ($academicYear?->name ?? '2026–2027') . ')')

@section('content')
<div class="space-y-6" x-data="{
  activeTab: '{{ request('status') || request('search') || request('class_id') ? 'enquiries' : 'fee-structure' }}',
  feeViewMode: 'card',
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  standardFees: {{ json_encode($standardFees ?? []) }},
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

  {{-- ── Banner Card Header: Erode Public School Crimson Maroon ── --}}
  <div class="rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-lg relative overflow-hidden print:hidden"
       style="background: linear-gradient(135deg, #731E1C 0%, #8C2826 55%, #A83230 100%);">
    {{-- Decorative subtle glow --}}
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
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight" x-text="activeTab === 'fee-structure' ? 'Fees Structure' : 'Admission Enquiries'"></h1>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap z-10">
      {{-- 3 Official School Forms Quick Links (PDF 1, 2, 3) --}}
      <div class="flex items-center gap-1.5 bg-black/20 p-1.5 rounded-2xl border border-white/15 backdrop-blur-xs">
        <a href="{{ route('admissions.print-form', ['form' => 'admission']) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition flex items-center gap-1" title="Print Official General Admission Form (PDF 1)">
          <span>📄 Adm Form</span>
        </a>
        <a href="{{ route('admissions.print-form', ['form' => 'grade11']) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition flex items-center gap-1" title="Print Official Grade XI Form (PDF 2)">
          <span>🎓 Grade XI</span>
        </a>
        <a href="{{ route('admissions.print-form', ['form' => 'enquiry']) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition flex items-center gap-1" title="Print Official Enquiry Form (PDF 3)">
          <span>📝 Enquiry</span>
        </a>
      </div>

      <a href="{{ route('admissions.approvals') }}" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm shadow-xs transition">
        <i class="fas fa-check-double text-xs text-amber-300"></i>
        <span>Approvals Desk</span>
      </a>
      <a href="{{ route('admissions.create') }}" class="inline-flex items-center gap-2 bg-white text-[#8C2826] hover:bg-rose-50 font-black px-4 py-2.5 rounded-2xl text-xs sm:text-sm shadow-md transition">
        <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>+ New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── Module View Navigation Tabs ────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3 print:hidden">
    <div class="flex items-center gap-2">
      <button type="button"
              @click="activeTab = 'fee-structure'"
              class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer"
              :class="activeTab === 'fee-structure' ? 'bg-[#8C2826] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
        <span>Fees Structure</span>
      </button>

      <button type="button"
              @click="activeTab = 'enquiries'"
              class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer"
              :class="activeTab === 'enquiries' ? 'bg-[#8C2826] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
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

    {{-- Selected Class Hero Card --}}
    <div class="rounded-3xl p-6 sm:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md print:hidden"
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

        <a :href="'/admissions/fee-structure/' + currentClass.id + '/print?print=true'" target="_blank" class="px-5 py-3.5 rounded-2xl bg-white hover:bg-rose-50 text-[#8C2826] font-black text-xs shadow-md transition cursor-pointer flex items-center gap-2 shrink-0 print:hidden" title="Print Fee Structure">
          <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          <span>Print Fee Structure</span>
        </a>
      </div>
    </div>

    {{-- Official Academic Fees Section (Real 3-Term Breakdown) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-6 print:hidden">
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
    <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/80 space-y-2 print:hidden">
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

    {{-- Online Payment Link Card --}}
    <div class="bg-gradient-to-br from-[#8C2826] to-[#731E1C] rounded-2xl p-5 sm:p-6 text-white space-y-4 print:hidden shadow-md">
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

    {{-- Bottom Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 print:hidden">
      <span class="text-xs text-slate-500 font-semibold">
        Selected Standard: <strong class="text-slate-900" x-text="currentClass.name"></strong>
      </span>

      <a :href="'{{ route('admissions.create') }}?class_id=' + currentClass.id"
         class="inline-flex items-center justify-center gap-2 bg-[#8C2826] hover:bg-[#731E1C] text-white font-black px-7 py-3 rounded-2xl text-sm shadow-md transition">
        <span x-text="'Apply for ' + currentClass.name + ' →'"></span>
      </a>
    </div>

    {{-- ── FORMAL PRINT FORMAT FOR FEES STRUCTURE (ERODE PUBLIC SCHOOL) ── --}}
    <div class="hidden print:block print-fee-document space-y-6 bg-white text-slate-900 font-sans p-4">
      <div class="flex items-center justify-between border-b-2 border-[#8C2826] pb-4">
        <div class="flex items-center gap-3">
          <img src="{{ asset('images/school-seal-badge.png') }}" class="w-16 h-16 object-contain">
          <div>
            <h1 class="text-2xl font-black text-[#8C2826] tracking-wider uppercase">ERODE PUBLIC SCHOOL</h1>
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest mt-0.5">Affiliated to CBSE, New Delhi &bull; Official Fee Structure</p>
            <p class="text-xs text-slate-500 font-medium">Academic Year: {{ $academicYear?->name ?? '2026–2027' }}</p>
          </div>
        </div>
        <div class="text-right">
          <div class="inline-block px-4 py-1.5 bg-rose-50 text-[#8C2826] font-black text-base rounded-xl border border-rose-200 uppercase">
            Standard: <span x-text="currentClass.name"></span>
          </div>
          <p class="text-xs text-slate-500 font-mono mt-1">Date: {{ date('d-m-Y') }}</p>
        </div>
      </div>

      <div class="space-y-3">
        <h2 class="text-sm font-extrabold text-[#8C2826] uppercase tracking-wider border-b border-slate-300 pb-1.5">Academic Fee Schedule Breakdown</h2>
        <table class="w-full text-xs text-left border-collapse border border-slate-300">
          <thead>
            <tr class="bg-rose-50/80 text-slate-900 border-b border-slate-300 font-bold uppercase text-[10px]">
              <th class="py-2.5 px-4 border border-slate-300">Term / Component</th>
              <th class="py-2.5 px-4 border border-slate-300">Due Date / Details</th>
              <th class="py-2.5 px-4 text-right border border-slate-300">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 font-medium">
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">April (Term I)</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Due Date: 01.04.2026</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.term1_fee)"></td>
            </tr>
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">August (Term II)</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Due Date: 05.08.2026</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.term2_fee)"></td>
            </tr>
            <tr>
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">December (Term III)</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Due Date: 05.12.2026</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.term3_fee)"></td>
            </tr>
            <tr x-show="currentFee.material_fee > 0">
              <td class="py-2.5 px-4 border border-slate-300 font-bold text-slate-900">Material Fee (Grade X)</td>
              <td class="py-2.5 px-4 border border-slate-300 text-slate-600">Due Date: 10.01.2026</td>
              <td class="py-2.5 px-4 border border-slate-300 text-right font-mono font-bold text-slate-900" x-text="formatMoney(currentFee.material_fee)"></td>
            </tr>
            <tr class="bg-rose-50/80 font-bold">
              <td colspan="2" class="py-3 px-4 border border-slate-300 text-[#8C2826] uppercase text-xs">Total Annual Academic Fee</td>
              <td class="py-3 px-4 border border-slate-300 text-right font-mono text-sm text-[#8C2826]" x-text="formatMoney(currentFee.total_basic)"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="grid grid-cols-2 gap-12 pt-16 text-xs">
        <div>
          <p class="font-bold text-slate-800">Accounts &amp; Admissions Officer</p>
          <div class="h-12 border-b border-slate-400 border-dashed"></div>
          <p class="text-[10px] text-slate-500 mt-1">Erode Public School</p>
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
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or mobile..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-[#8C2826]">
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
          <button type="submit" class="w-full py-2.5 rounded-xl bg-[#8C2826] hover:bg-[#731E1C] text-white font-bold text-xs shadow-xs transition">
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
                <td class="py-3.5 px-4 font-mono font-bold text-[#8C2826] whitespace-nowrap">
                  {{ $enquiry->enquiry_number }}
                </td>
                <td class="py-3.5 px-4">
                  <p class="font-bold text-slate-900">{{ $enquiry->student_name }}</p>
                  @if($enquiry->last_school_studied || $enquiry->last_class_studied)
                    <p class="text-[11px] text-slate-500 mt-0.5">
                      <span class="text-slate-400">Prev:</span> {{ $enquiry->last_school_studied ?: 'Prior School' }}
                      @if($enquiry->last_class_studied)
                        <span class="text-[#8C2826] font-semibold">({{ $enquiry->last_class_studied }})</span>
                      @endif
                    </p>
                  @endif
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-700 whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-[#8C2826] border border-rose-200 text-xs">
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
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
