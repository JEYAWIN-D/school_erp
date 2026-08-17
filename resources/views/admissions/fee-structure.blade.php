@extends('layouts.app')

@section('title', 'Admissions Fee Structure — ' . ($academicYear?->name ?? 'Academic Year'))

@section('content')
<div class="space-y-6" x-data="{
  selectedClassId: '{{ $classes->first()?->id ?? '' }}',
  hostelSelected: false,
  hostelFee: 45000,
  transportSelected: false,
  transportFee: 18000,
  selectedActivities: [],
  classesData: {{ json_encode($standardFees) }},
  activitiesData: {{ json_encode($activities) }},

  getSelectedFee() {
    return this.classesData[this.selectedClassId] || { tuition_fee: 0, admission_fee: 0, activity_fee: 0, exam_fee: 0, library_fee: 0, total_annual: 0 };
  },
  getActivitiesTotal() {
    let sum = 0;
    this.selectedActivities.forEach(id => {
      let act = this.activitiesData.find(a => a.id === id);
      if (act) sum += act.annual_fee;
    });
    return sum;
  },
  getGrandTotal() {
    let base = this.getSelectedFee().total_annual || 0;
    let hostel = this.hostelSelected ? this.hostelFee : 0;
    let transport = this.transportSelected ? this.transportFee : 0;
    return base + hostel + transport + this.getActivitiesTotal();
  }
}">

  {{-- ── Top Navigation & Actions ────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.index') }}" class="btn-icon w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Back to Admissions Overview">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="page-title text-xl font-black text-slate-900">Standard Fee Structure</h1>
          <span class="badge-blue text-[10px] font-bold">{{ $academicYear?->name ?? '2025-2026' }}</span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Standard Grade Tuition, Activity, Examination, and Extra-Curricular fee schedule</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold">
        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Fee Schedule
      </button>

      <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        + New Enquiry
      </a>
    </div>
  </div>

  {{-- ── Grade-Wise Standard Fee Grid ────────────────────────── --}}
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
        <span>🏫 Grade-Wise Annual Standard Fees</span>
        <span class="text-xs text-slate-400 font-normal">({{ count($standardFees) }} Standards)</span>
      </h3>
      <span class="text-xs text-indigo-600 font-bold hidden sm:inline">Tuition + Lab + Library + Examination</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($classes as $c)
      @php $fee = $standardFees[$c->id] ?? null; @endphp
      @if($fee)
      <div class="card p-5 bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 rounded-2xl flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
              {{ $fee['tier'] }}
            </span>
            <span class="text-[10px] font-bold text-slate-400">
              {{ $fee['enquiries_count'] }} Enquiries
            </span>
          </div>

          <h4 class="text-lg font-black text-slate-900 leading-tight">Class {{ $c->name }}</h4>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-slate-900 font-mono">₹{{ number_format($fee['total_annual']) }}</span>
            <span class="text-xs text-slate-400 font-medium">/ year</span>
            <span class="text-[11px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full ml-auto">₹{{ number_format($fee['term_fee']) }}/term</span>
          </div>
        </div>

        {{-- Fee Breakdown Table --}}
        <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-200/60 text-xs space-y-1.5 font-medium">
          <div class="flex justify-between items-center text-slate-600">
            <span>Tuition Fee</span>
            <span class="font-mono font-bold text-slate-900">₹{{ number_format($fee['tuition_fee']) }}</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Admission / Reg Fee (1st Yr)</span>
            <span class="font-mono font-bold text-slate-900">₹{{ number_format($fee['admission_fee']) }}</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Activity & Lab Fee</span>
            <span class="font-mono font-bold text-slate-900">₹{{ number_format($fee['activity_fee']) }}</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Library & Resources</span>
            <span class="font-mono font-bold text-slate-900">₹{{ number_format($fee['library_fee']) }}</span>
          </div>
          <div class="flex justify-between items-center text-slate-600 pt-1 border-t border-slate-200">
            <span>Examination Fee</span>
            <span class="font-mono font-bold text-slate-900">₹{{ number_format($fee['exam_fee']) }}</span>
          </div>
        </div>

        <a href="{{ route('admissions.create', ['class_id' => $c->id]) }}" class="btn btn-secondary btn-xs w-full text-center font-bold text-indigo-700 hover:bg-indigo-50 border-indigo-200">
          Create Enquiry for {{ $c->name }} &rarr;
        </a>
      </div>
      @endif
      @endforeach
    </div>
  </div>

  {{-- ── Extra-Curricular Activities & Specialized Clubs ─────── --}}
  <div class="space-y-3 pt-2">
    <div class="flex items-center justify-between">
      <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
        <span>🌟 Extra-Curricular Clubs & Specialized Academies</span>
      </h3>
      <span class="text-xs text-slate-400">Optional Add-On Activities</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      @foreach($activities as $act)
      <div class="card p-4 bg-white border border-slate-200 rounded-2xl flex flex-col justify-between space-y-3 hover:border-slate-300 transition">
        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <span class="text-2xl">{{ $act['icon'] }}</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
              {{ $act['category'] }}
            </span>
          </div>
          <h4 class="font-bold text-slate-900 text-sm">{{ $act['name'] }}</h4>
          <p class="text-xs text-slate-500 line-clamp-2">{{ $act['description'] }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
          <div>
            <span class="text-xs text-slate-400">Monthly</span>
            <p class="text-xs font-mono font-bold text-slate-800">₹{{ number_format($act['monthly_fee']) }}/mo</p>
          </div>
          <div class="text-right">
            <span class="text-xs text-slate-400">Annual</span>
            <p class="text-sm font-mono font-black text-indigo-600">₹{{ number_format($act['annual_fee']) }}/yr</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── Live Interactive Fee Calculator Card ───────────────── --}}
  <div class="card p-6 bg-gradient-to-br from-indigo-900 via-slate-900 to-blue-950 text-white rounded-3xl shadow-xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-white/10">
      <div>
        <h3 class="text-lg font-black tracking-tight text-white flex items-center gap-2">
          <span>🧮 Interactive Admission Fee Estimator</span>
        </h3>
        <p class="text-xs text-indigo-200">Select grade, hostel, transport, and extra-curriculars to calculate instant total fee</p>
      </div>

      <div class="text-right bg-white/10 px-4 py-2 rounded-2xl backdrop-blur-xs border border-white/15">
        <span class="text-[10px] uppercase font-bold text-indigo-200 tracking-wider">Estimated Grand Total</span>
        <div class="text-2xl font-black font-mono text-emerald-400" x-text="'₹' + Number(getGrandTotal()).toLocaleString('en-IN') + ' / yr'">
          ₹0 / yr
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Grade Picker --}}
      <div class="space-y-2">
        <label class="text-xs font-bold text-indigo-200 uppercase tracking-wider">1. Select Standard / Grade</label>
        <select x-model="selectedClassId" class="w-full text-xs font-bold bg-white/10 border border-white/20 text-white rounded-xl p-2.5 focus:bg-slate-800 focus:outline-none">
          @foreach($classes as $c)
            <option value="{{ $c->id }}" class="text-slate-900">Class {{ $c->name }} (Standard Base Fee)</option>
          @endforeach
        </select>

        <div class="bg-white/5 rounded-xl p-3 border border-white/10 text-xs space-y-1 text-indigo-200 font-mono mt-3">
          <div class="flex justify-between">
            <span>Base Tuition:</span>
            <span class="text-white font-bold" x-text="'₹' + Number(getSelectedFee().tuition_fee || 0).toLocaleString('en-IN')"></span>
          </div>
          <div class="flex justify-between">
            <span>Admission / Reg:</span>
            <span class="text-white font-bold" x-text="'₹' + Number(getSelectedFee().admission_fee || 0).toLocaleString('en-IN')"></span>
          </div>
          <div class="flex justify-between">
            <span>Activity & Lab:</span>
            <span class="text-white font-bold" x-text="'₹' + Number(getSelectedFee().activity_fee || 0).toLocaleString('en-IN')"></span>
          </div>
        </div>
      </div>

      {{-- Boarding & Transport --}}
      <div class="space-y-2">
        <label class="text-xs font-bold text-indigo-200 uppercase tracking-wider">2. Boarding & Transport</label>
        <div class="space-y-2.5">
          <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition cursor-pointer">
            <input type="checkbox" x-model="hostelSelected" class="w-4 h-4 rounded text-indigo-500 focus:ring-0">
            <div class="flex-1 text-xs">
              <p class="font-bold text-white">Residential Hostel & Mess</p>
              <p class="text-[11px] text-indigo-200">AC Dormitory, 4 meals, laundry</p>
            </div>
            <span class="font-mono font-bold text-emerald-400 text-xs">+₹45,000</span>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition cursor-pointer">
            <input type="checkbox" x-model="transportSelected" class="w-4 h-4 rounded text-indigo-500 focus:ring-0">
            <div class="flex-1 text-xs">
              <p class="font-bold text-white">School Bus Transport</p>
              <p class="text-[11px] text-indigo-200">GPS-tracked AC bus service</p>
            </div>
            <span class="font-mono font-bold text-emerald-400 text-xs">+₹18,000</span>
          </label>
        </div>
      </div>

      {{-- Extra Curricular Clubs --}}
      <div class="space-y-2">
        <label class="text-xs font-bold text-indigo-200 uppercase tracking-wider">3. Specialized Clubs</label>
        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
          @foreach($activities as $act)
          <label class="flex items-center gap-2.5 p-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition cursor-pointer text-xs">
            <input type="checkbox" value="{{ $act['id'] }}" x-model="selectedActivities" class="w-3.5 h-3.5 rounded text-indigo-500 focus:ring-0">
            <span class="text-base">{{ $act['icon'] }}</span>
            <span class="text-white truncate flex-1">{{ $act['name'] }}</span>
            <span class="font-mono text-emerald-400 text-[11px]">+₹{{ number_format($act['annual_fee']) }}</span>
          </label>
          @endforeach
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
