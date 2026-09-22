@extends('layouts.app')
@section('title', 'Attendance & HR Reports')

@section('content')
<div class="space-y-6">

  {{-- ── 1. Page Header ────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to HR & Payroll">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-purple-100 flex-shrink-0" style="background: linear-gradient(135deg, #9333EA 0%, #4F46E5 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Attendance & HR Reports</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Comprehensive staff attendance analytics, category breakdown & individual audits</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      @if($reportMode === 'individual' && $selectedStaff)
        <button type="button" onclick="window.print()" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs bg-indigo-600 hover:bg-indigo-700 border-indigo-600 text-white cursor-pointer" id="btnPrintReport">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Print Report
        </button>
      @else
        <a href="{{ route('hr.reports.export', request()->all()) }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs bg-emerald-600 hover:bg-emerald-700 border-emerald-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Export CSV
        </a>
      @endif
    </div>
  </div>

  {{-- ── 2. Navigation Tabs (Overall Category vs Individual Staff Report) ── --}}
  <div class="flex border-b border-slate-200 gap-6 no-print">
    <a href="{{ route('hr.reports', array_merge(request()->query(), ['mode' => 'overall'])) }}"
       class="pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 transition-colors {{ $reportMode === 'overall' ? 'border-purple-600 text-purple-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      Overall Category Report
    </a>
    <a href="{{ route('hr.reports', array_merge(request()->query(), ['mode' => 'individual'])) }}"
       class="pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 transition-colors {{ $reportMode === 'individual' ? 'border-purple-600 text-purple-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      Individual Staff Report
    </a>
  </div>

  {{-- ── 3. Filter Bar ─────────────────────────────────────────── --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs no-print">
    <form method="GET" action="{{ route('hr.reports') }}" class="flex flex-wrap items-end gap-3.5">
      <input type="hidden" name="mode" value="{{ $reportMode }}">

      {{-- Period Type Selector --}}
      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Period Type</label>
        <select name="period_type" id="periodTypeSelect" onchange="this.form.submit()"
                class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 pl-3 pr-8 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
          <option value="daily" {{ $periodType === 'daily' ? 'selected' : '' }}>Daily</option>
          <option value="weekly" {{ $periodType === 'weekly' ? 'selected' : '' }}>Weekly</option>
          <option value="monthly" {{ $periodType === 'monthly' ? 'selected' : '' }}>Monthly</option>
          <option value="term" {{ $periodType === 'term' ? 'selected' : '' }}>Academic Term</option>
          <option value="yearly" {{ $periodType === 'yearly' ? 'selected' : '' }}>Yearly (Academic Year)</option>
        </select>
      </div>

      {{-- Dynamic Period Filter Input --}}
      @if($periodType === 'daily' || $periodType === 'weekly')
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
            {{ $periodType === 'daily' ? 'Select Date' : 'Select Week (Date)' }}
          </label>
          <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                 class="form-input rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 px-3 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
        </div>
      @elseif($periodType === 'monthly')
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Month</label>
          <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                 class="form-input rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 px-3 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
        </div>
      @elseif($periodType === 'term')
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Academic Term</label>
          <select name="term_id" onchange="this.form.submit()"
                  class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 pl-3 pr-8 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
            @foreach($academicTerms as $term)
              <option value="{{ $term->id }}" {{ (string)$termId === (string)$term->id ? 'selected' : '' }}>
                {{ $term->name }} ({{ \Carbon\Carbon::parse($term->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($term->end_date)->format('d M Y') }})
              </option>
            @endforeach
          </select>
        </div>
      @elseif($periodType === 'yearly')
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Academic Year</label>
          <select name="year_id" onchange="this.form.submit()"
                  class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 pl-3 pr-8 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
            @foreach($academicYears as $year)
              <option value="{{ $year->id }}" {{ (string)$yearId === (string)$year->id ? 'selected' : '' }}>
                {{ $year->name }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      @if($reportMode === 'overall')
        {{-- Category Filter --}}
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Staff Category</label>
          <select name="category" onchange="this.form.submit()"
                  class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 pl-3 pr-8 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50">
            @foreach($categories as $cat)
              <option value="{{ $cat['key'] }}" {{ $category === $cat['key'] ? 'selected' : '' }}>
                {{ $cat['label'] }}
              </option>
            @endforeach
          </select>
        </div>
      @else
        {{-- Employee Selector for Individual Mode --}}
        <div class="min-w-[260px]">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Staff Member</label>
          <select name="employee_id" onchange="this.form.submit()"
                  class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-2 pl-3 pr-8 focus:border-purple-500 focus:ring-purple-500 bg-slate-50/50 w-full">
            @foreach($allActiveStaff as $emp)
              <option value="{{ $emp->id }}" {{ $selectedStaff && $selectedStaff->id === $emp->id ? 'selected' : '' }}>
                {{ $emp->full_name }} ({{ $emp->employee_code }}) - {{ $emp->category_label }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="flex items-center gap-2">
        <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold">Apply Filter</button>
        <a href="{{ route('hr.reports', ['mode' => $reportMode]) }}" class="btn btn-ghost btn-sm text-xs text-slate-500 hover:text-slate-700">Reset</a>
      </div>

      <div class="ml-auto text-right">
        <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
          Period: {{ $periodLabel }} &bull; {{ $workingDays }} Working Day(s)
        </span>
      </div>
    </form>
  </div>

  {{-- ── 4. REPORT MODE CONTENT ────────────────────────────────── --}}
  @if($reportMode === 'overall')
    {{-- OVERALL CATEGORY REPORT --}}

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center">
        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Filtered Staff</p>
        <p class="text-2xl font-black text-slate-800 mt-1 font-mono">{{ count($overallRows) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $category === 'all' ? 'All categories' : $selectedCategoryLabel }}</p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-2xs text-center">
        <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Present Staff</p>
        <p class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ $presentStaffCount }}</p>
        <p class="text-[11px] text-emerald-600/70 mt-0.5 font-semibold">{{ $totalPresentSum }} total present days</p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-rose-200 bg-rose-50/20 shadow-2xs text-center">
        <p class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700">Absent Staff</p>
        <p class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ $absentStaffCount }}</p>
        <p class="text-[11px] text-rose-600/70 mt-0.5 font-semibold">{{ $totalAbsentSum }} total absent days</p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-amber-200 bg-amber-50/20 shadow-2xs text-center">
        <p class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Staff On Leave</p>
        <p class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ $leaveStaffCount }}</p>
        <p class="text-[11px] text-amber-600/70 mt-0.5 font-semibold">{{ $totalLeaveSum }} total leave days</p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-purple-200 bg-purple-50/20 shadow-2xs text-center col-span-2 sm:col-span-1">
        <p class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700">Avg Attendance</p>
        <p class="text-2xl font-black text-purple-700 mt-1 font-mono">{{ $avgPercentage }}%</p>
        <p class="text-[11px] text-purple-600/70 mt-0.5 font-semibold">Across category</p>
      </div>
    </div>

    {{-- Overall Breakdown Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="font-extrabold text-sm text-slate-800">Category Attendance Register</h3>
          <p class="text-xs text-slate-400 mt-0.5">{{ $selectedCategoryLabel }} &bull; Period: {{ $periodLabel }}</p>
        </div>
        <span class="text-xs font-bold text-slate-400 font-mono">{{ count($overallRows) }} Staff Members</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-4">Staff ID</th>
              <th class="py-3 px-4">Employee</th>
              <th class="py-3 px-4">Category</th>
              <th class="py-3 px-4 text-center">Present</th>
              <th class="py-3 px-4 text-center">Absent</th>
              <th class="py-3 px-4 text-center">Leave</th>
              <th class="py-3 px-4 text-center">Attendance %</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($overallRows as $row)
              @php $emp = $row['employee']; @endphp
              <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ $emp->employee_code }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">
                  {{ $emp->full_name }}
                  <span class="block text-[11px] font-normal text-slate-400">{{ $emp->designation_name }} &bull; {{ $emp->department_name }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="text-[11px] font-semibold text-slate-600">{{ $row['category'] }}</span>
                </td>
                <td class="py-3 px-4 text-center font-mono font-bold text-emerald-600">{{ $row['present'] }}</td>
                <td class="py-3 px-4 text-center font-mono font-bold text-rose-600">{{ $row['absent'] }}</td>
                <td class="py-3 px-4 text-center font-mono font-bold text-amber-600">{{ $row['leave'] }}</td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden hidden sm:block">
                      <div class="h-1.5 rounded-full {{ $row['percentage'] >= 75 ? 'bg-emerald-500' : ($row['percentage'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                           style="width: {{ min(100, $row['percentage']) }}%"></div>
                    </div>
                    <span class="font-mono font-bold text-xs {{ $row['percentage'] >= 75 ? 'text-emerald-700' : ($row['percentage'] >= 50 ? 'text-amber-700' : 'text-rose-700') }}">
                      {{ $row['percentage'] }}%
                    </span>
                  </div>
                </td>
                <td class="py-3 px-4 text-right">
                  <a href="{{ route('hr.reports', ['mode' => 'individual', 'employee_id' => $emp->id, 'period_type' => $periodType, 'date' => $date, 'month' => $month, 'term_id' => $termId, 'year_id' => $yearId]) }}"
                     class="btn btn-secondary btn-xs font-bold text-purple-600 hover:text-purple-700 hover:border-purple-300">
                    Individual View &rarr;
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-10 text-slate-400 text-xs">
                  No staff members found matching the selected filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  @else
    {{-- ── INDIVIDUAL STAFF REPORT (PRINTABLE SINGLE-PAGE REPORT) ── --}}
    @if($selectedStaff && $individualSummary)

      {{-- Action Bar on Screen --}}
      <div class="flex items-center justify-between gap-3 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 no-print">
        <div class="flex items-center gap-2 text-indigo-900 text-xs font-semibold">
          <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Showing print-ready single-page attendance report for <span class="font-bold underline">{{ $selectedStaff->full_name }}</span> ({{ $selectedStaff->employee_code }}).
        </div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="window.print()" class="btn btn-primary btn-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold flex items-center gap-1.5 cursor-pointer shadow-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Report
          </button>
          <a href="{{ route('hr.attendance.staff-detail', $selectedStaff->id) }}" class="btn btn-secondary btn-xs text-slate-700 font-bold">
            Full Attendance Profile &rarr;
          </a>
        </div>
      </div>

      {{-- Printable Report Container --}}
      <div id="printable-staff-report" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8 max-w-4xl mx-auto text-slate-800">

        {{-- 1. School & Report Header --}}
        <div class="report-header text-center border-b-2 border-slate-800 pb-4 mb-5">
          <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
            {{ $school?->school_name ?? config('app.name', 'DASA EduERP') }}
          </h1>
          @if(!empty($school?->address) || !empty($school?->phone) || !empty($school?->affiliation_no))
            <p class="text-[11px] text-slate-600 mt-0.5">
              @if(!empty($school?->address)) {{ $school->address }} @endif
              @if(!empty($school?->affiliation_no)) &bull; Affiliation No: {{ $school->affiliation_no }} @endif
              @if(!empty($school?->school_code)) &bull; School Code: {{ $school->school_code }} @endif
              @if(!empty($school?->phone)) &bull; Tel: {{ $school->phone }} @endif
            </p>
          @endif
          <div class="mt-2.5 inline-block border-y border-slate-700 py-1 px-6">
            <h2 class="text-xs sm:text-sm font-black tracking-widest uppercase text-slate-900">
              STAFF ATTENDANCE REPORT
            </h2>
          </div>
        </div>

        {{-- 2. Staff Details Section --}}
        <div class="report-section mb-5">
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2.5 text-xs">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Staff Name</span>
                <span class="font-extrabold text-slate-900 block truncate">{{ $selectedStaff->full_name }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Staff ID</span>
                <span class="font-extrabold font-mono text-slate-800 block">{{ $selectedStaff->employee_code }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Category</span>
                <span class="font-bold text-slate-800 block">{{ $selectedStaff->category_label }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Department</span>
                <span class="font-bold text-slate-800 block truncate">{{ $selectedStaff->department_name }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Designation</span>
                <span class="font-bold text-slate-800 block truncate">{{ $selectedStaff->designation_name }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Academic Year</span>
                <span class="font-bold text-slate-800 block">{{ $academicYearName }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Reporting Period</span>
                <span class="font-extrabold text-indigo-900 block">{{ $periodLabel }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- 3. Attendance Summary & Leave Summary Grids (Side-by-Side for Single-Page Layout) --}}
        <div class="report-section grid grid-cols-1 md:grid-cols-12 gap-4 mb-5">

          {{-- ATTENDANCE SUMMARY --}}
          <div class="md:col-span-7 bg-white border border-slate-200 rounded-xl p-3.5">
            <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-1.5 mb-2.5 flex items-center justify-between">
              <span>Attendance Summary</span>
              <span class="text-[10px] text-slate-500 font-semibold normal-case">Period: {{ $periodLabel }}</span>
            </h3>
            <table class="w-full text-xs">
              <tbody class="divide-y divide-slate-100">
                <tr>
                  <td class="py-1 text-slate-600 font-medium">Total Working Days</td>
                  <td class="py-1 text-right font-mono font-bold text-slate-900">{{ $individualSummary['working_days'] }}</td>
                </tr>
                <tr>
                  <td class="py-1 text-slate-600 font-medium">Present Days</td>
                  <td class="py-1 text-right font-mono font-bold text-emerald-700">{{ $individualSummary['present'] }}</td>
                </tr>
                <tr>
                  <td class="py-1 text-slate-600 font-medium">Absent Days</td>
                  <td class="py-1 text-right font-mono font-bold text-rose-700">{{ $individualSummary['absent'] }}</td>
                </tr>
                <tr>
                  <td class="py-1 text-slate-600 font-medium">Half Day</td>
                  <td class="py-1 text-right font-mono font-bold text-blue-700">{{ $individualSummary['half_day'] }}</td>
                </tr>
                @if(($individualSummary['on_duty'] ?? 0) > 0)
                  <tr>
                    <td class="py-1 text-slate-600 font-medium">On Duty</td>
                    <td class="py-1 text-right font-mono font-bold text-sky-700">{{ $individualSummary['on_duty'] }}</td>
                  </tr>
                @endif
                @if(($individualSummary['paid_off'] ?? 0) > 0)
                  <tr>
                    <td class="py-1 text-slate-600 font-medium">Paid Off</td>
                    <td class="py-1 text-right font-mono font-bold text-purple-700">{{ $individualSummary['paid_off'] }}</td>
                  </tr>
                @endif
                @if(($individualSummary['permission'] ?? 0) > 0)
                  <tr>
                    <td class="py-1 text-slate-600 font-medium">Permission</td>
                    <td class="py-1 text-right font-mono font-bold text-amber-700">{{ $individualSummary['permission'] }}</td>
                  </tr>
                @endif
                <tr>
                  <td class="py-1 text-slate-600 font-medium">Approved Leave (In Period)</td>
                  <td class="py-1 text-right font-mono font-bold text-amber-700">{{ $individualSummary['leave'] }}</td>
                </tr>
                <tr class="border-t-2 border-slate-300 bg-slate-50/60">
                  <td class="py-1.5 text-slate-900 font-extrabold">Attendance Rate</td>
                  <td class="py-1.5 text-right font-mono font-black text-indigo-700 text-sm">{{ $individualSummary['percentage'] }}%</td>
                </tr>
              </tbody>
            </table>
          </div>

          {{-- LEAVE SUMMARY --}}
          <div class="md:col-span-5 bg-white border border-slate-200 rounded-xl p-3.5">
            <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-1.5 mb-2.5 flex items-center justify-between">
              <span>Leave Summary</span>
              <span class="text-[10px] text-slate-500 font-semibold normal-case">Annual Balance</span>
            </h3>
            @if(count($leaveSummaryBreakdown) > 0)
              <table class="w-full text-[11px]">
                <thead>
                  <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[9px]">
                    <th class="py-1 text-left">Leave Type</th>
                    <th class="py-1 text-center">Alloc.</th>
                    <th class="py-1 text-center">Taken</th>
                    <th class="py-1 text-right">Bal.</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach($leaveSummaryBreakdown as $lt)
                    <tr>
                      <td class="py-1 text-slate-700 font-semibold truncate max-w-[110px]" title="{{ $lt['type'] }}">{{ $lt['type'] }}</td>
                      <td class="py-1 text-center font-mono text-slate-600">{{ $lt['allocated'] }}</td>
                      <td class="py-1 text-center font-mono font-bold text-amber-700">{{ $lt['taken'] }}</td>
                      <td class="py-1 text-right font-mono font-extrabold text-emerald-700">{{ $lt['remaining'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr class="border-t-2 border-slate-200 bg-slate-50 font-bold text-slate-800 text-[10px]">
                    <td class="py-1">Total</td>
                    <td class="py-1 text-center font-mono">{{ $individualSummary['total_allowed'] }}</td>
                    <td class="py-1 text-center font-mono text-amber-700">{{ $individualSummary['leave_taken'] }}</td>
                    <td class="py-1 text-right font-mono text-emerald-700">{{ max(0, $individualSummary['total_allowed'] - $individualSummary['leave_taken']) }}</td>
                  </tr>
                </tfoot>
              </table>
            @else
              <div class="py-4 text-center text-slate-400 text-xs">
                No leave categories configured.
              </div>
            @endif
          </div>

        </div>

        {{-- 4. Attendance History (DATE, DAY, STATUS ONLY — No Check-In, Check-Out, Remarks) --}}
        <div class="report-section mb-6">
          <div class="border border-slate-200 rounded-xl overflow-hidden">
            <div class="bg-slate-100 px-3.5 py-2 border-b border-slate-200 flex items-center justify-between">
              <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-900">Attendance History</h3>
              <span class="text-[10px] font-mono font-bold text-slate-500">{{ $individualHistory->count() }} Recorded Days</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                    <th class="py-2 px-3.5 w-1/3">Date</th>
                    <th class="py-2 px-3.5 w-1/3">Day</th>
                    <th class="py-2 px-3.5 w-1/3 text-left">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                  @forelse($individualHistory as $att)
                    @php
                      $cDate = \Carbon\Carbon::parse($att->date);
                      $statusLower = strtolower($att->status ?? '');
                    @endphp
                    <tr class="{{ $cDate->isSunday() ? 'bg-slate-50/70 text-slate-400' : '' }}">
                      <td class="py-1.5 px-3.5 font-mono font-bold text-slate-900">{{ $cDate->format('d-m-Y') }}</td>
                      <td class="py-1.5 px-3.5 text-slate-600">{{ $cDate->format('l') }}</td>
                      <td class="py-1.5 px-3.5">
                        @if(in_array($statusLower, ['present', 'late']))
                          <span class="status-badge status-present font-bold text-emerald-800">
                            {{ ucfirst($statusLower) }}
                          </span>
                        @elseif($statusLower === 'absent')
                          <span class="status-badge status-absent font-bold text-rose-800">
                            Absent
                          </span>
                        @elseif($statusLower === 'half_day')
                          <span class="status-badge status-half-day font-bold text-blue-800">
                            Half Day
                          </span>
                        @elseif($statusLower === 'on_duty')
                          <span class="status-badge font-bold text-sky-800 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded">
                            On Duty
                          </span>
                        @elseif($statusLower === 'paid_off')
                          <span class="status-badge font-bold text-purple-800 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">
                            Paid Off
                          </span>
                        @elseif($statusLower === 'permission')
                          <span class="status-badge font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                            Permission
                          </span>
                        @elseif(in_array($statusLower, ['leave', 'on_leave']))
                          <span class="status-badge status-leave font-bold text-amber-800">
                            Approved Leave
                          </span>
                        @else
                          <span class="text-slate-500 font-medium">{{ ucfirst(str_replace('_', ' ', $att->status ?? '—')) }}</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center py-6 text-slate-400 text-xs">
                        No attendance records logged for this staff member in the selected period.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- 5. Report Metadata & Signatures --}}
        <div class="report-footer pt-3 border-t border-slate-300">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[10px] text-slate-500 mb-8 gap-2">
            <div>
              <span class="font-bold">Generated on:</span> {{ now()->format('d F Y, h:i A') }}
            </div>
            <div>
              <span class="font-bold">Generated by:</span> {{ auth()->user()->name ?? 'Administrator' }}
            </div>
          </div>

          {{-- Signature Lines --}}
          <div class="grid grid-cols-3 gap-6 text-center text-xs pt-4">
            <div class="border-t border-dashed border-slate-400 pt-2">
              <p class="font-bold text-slate-700">Prepared By</p>
              <p class="text-[10px] text-slate-400 mt-0.5">Staff / HR Executive</p>
            </div>
            <div class="border-t border-dashed border-slate-400 pt-2">
              <p class="font-bold text-slate-700">HR / Administrator</p>
              <p class="text-[10px] text-slate-400 mt-0.5">Verified</p>
            </div>
            <div class="border-t border-dashed border-slate-400 pt-2">
              <p class="font-bold text-slate-700">Principal / Authorized</p>
              <p class="text-[10px] text-slate-400 mt-0.5">Official Stamp & Signature</p>
            </div>
          </div>
        </div>

      </div>

    @else
      <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
        <p class="text-slate-400 text-sm font-semibold">Please select a staff member to view and print their individual attendance report.</p>
      </div>
    @endif
  @endif
</div>

{{-- ── 5. Print Styling for Professional Single-Page A4 Report ── --}}
<style>
@media print {
  @page {
    size: A4 portrait;
    margin: 8mm 10mm;
  }

  /* Reset body and html for printing */
  html, body {
    background: #ffffff !important;
    color: #000000 !important;
    font-size: 11px !important;
    line-height: 1.3 !important;
    margin: 0 !important;
    padding: 0 !important;
    height: auto !important;
    overflow: visible !important;
  }

  /* Hide entire application layout except the printable report */
  body * {
    visibility: hidden;
  }

  #printable-staff-report,
  #printable-staff-report * {
    visibility: visible;
  }

  #printable-staff-report {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
  }

  .no-print,
  #sidebar,
  header,
  nav,
  .btn,
  #toast-container,
  button,
  .alert-success,
  .alert-error {
    display: none !important;
  }

  /* Compact sections for single page fit */
  .report-header {
    border-bottom: 2px solid #0f172a !important;
    padding-bottom: 6px !important;
    margin-bottom: 8px !important;
  }
  .report-section {
    margin-bottom: 8px !important;
  }
  .report-footer {
    padding-top: 6px !important;
    margin-top: 8px !important;
    page-break-inside: avoid;
  }

  /* Table styling */
  table {
    border-collapse: collapse !important;
    width: 100% !important;
  }
  th, td {
    padding: 2.5px 5px !important;
  }

  /* Status Badges in Print */
  .status-badge {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 4px;
    font-size: 9.5px;
    font-weight: 700;
  }
  .status-present {
    color: #15803d !important;
    border: 1px solid #86efac !important;
    background-color: #f0fdf4 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .status-absent {
    color: #b91c1c !important;
    border: 1px solid #fca5a5 !important;
    background-color: #fef2f2 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .status-half-day {
    color: #1d4ed8 !important;
    border: 1px solid #93c5fd !important;
    background-color: #eff6ff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .status-leave {
    color: #b45309 !important;
    border: 1px solid #fcd34d !important;
    background-color: #fffbeb !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
}
</style>
@endsection
