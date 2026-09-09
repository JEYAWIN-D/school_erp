@extends('layouts.app')
@section('title', 'Staff Attendance Register — ' . \Carbon\Carbon::parse($month . '-01')->format('F Y'))

@section('content')
<div class="space-y-5 w-full">

  {{-- ── Page Header ───────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('attendance.staff') }}" class="btn-icon w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="page-title text-xl font-extrabold text-slate-900">Staff Attendance Register</h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-200">
            {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
          </span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Statutory monthly attendance register with approved permissions tracking</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      {{-- Daily Staff Marking Kiosk --}}
      <a href="{{ route('attendance.staff') }}"
         class="btn bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 btn-sm flex items-center gap-1.5 text-xs font-bold shadow-2xs transition-all hover:border-slate-300">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Daily Attendance Terminal
      </a>

      {{-- Export Excel --}}
      <a href="{{ route('attendance.staff.export.month', ['month' => $month, 'category' => $category, 'department_id' => $deptId]) }}"
         class="btn bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 btn-sm flex items-center gap-1.5 text-xs font-bold shadow-2xs transition-all">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
      </a>

      {{-- Print Slip --}}
      <button type="button" onclick="window.print()"
              class="btn bg-indigo-600 hover:bg-indigo-700 text-white btn-sm flex items-center gap-1.5 text-xs font-bold shadow-2xs transition-all cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Register
      </button>
    </div>
  </div>

  {{-- ── Filter & Search Toolbar ───────────────────────────────── --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
    <form method="GET" action="{{ route('attendance.staff.register') }}" class="flex flex-wrap items-center justify-between gap-3">
      <input type="hidden" name="category" value="{{ $category }}">

      <div class="flex flex-wrap items-center gap-3">
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Month & Year</label>
          <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                 class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
        </div>

        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Department</label>
          <select name="department_id" onchange="this.form.submit()"
                  class="select select-sm border-slate-200 rounded-xl font-semibold text-xs bg-slate-50 text-slate-800">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected($deptId == $dept->id)>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="self-end">
          <button type="submit" class="btn btn-primary btn-sm rounded-xl text-xs font-bold px-4">Load Register</button>
        </div>
      </div>

      {{-- Legend Badges --}}
      <div class="flex items-center gap-1.5 flex-wrap text-[10px] font-mono">
        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">P = Present</span>
        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold border border-amber-200">L = Late</span>
        <span class="px-2 py-0.5 rounded bg-orange-100 text-orange-800 font-bold border border-orange-200">HD = Half-Day</span>
        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-900 font-black border border-purple-300 ring-1 ring-purple-400/30">PRM = Permission</span>
        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold border border-rose-200">A = Absent</span>
        <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-900 font-bold border border-yellow-200">OT = Overtime</span>
        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">H = Holiday</span>
      </div>
    </form>
  </div>

  {{-- ── Category Filter Tabs ──────────────────────────────────── --}}
  <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
    @foreach($categories as $cat)
      @php $isActive = ($category === $cat['key'] || (!$category && $cat['key'] === 'all')); @endphp
      <a href="{{ route('attendance.staff.register', array_merge(request()->query(), ['category' => $cat['key']])) }}"
         class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2
         {{ $isActive
            ? 'bg-purple-600 text-white shadow-sm shadow-purple-200'
            : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
        <span>{{ $cat['label'] }}</span>
      </a>
    @endforeach
  </div>

  {{-- ── Main Matrix Register Table ────────────────────────────── --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
    <div class="overflow-x-auto max-w-full">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-black uppercase text-slate-500">
            <th class="sticky left-0 z-20 bg-slate-100/95 backdrop-blur px-4 py-3 min-w-[220px] shadow-r">
              Staff Member & Designation
            </th>
            @foreach($days as $d)
              @php
                $isSun = $d->isSunday();
                $isHol = isset($holidays[$d->toDateString()]);
              @endphp
              <th class="px-1.5 py-2.5 text-center min-w-[34px] border-r border-slate-100 {{ $isSun ? 'bg-slate-200/60 text-slate-400' : ($isHol ? 'bg-amber-50 text-amber-700' : '') }}">
                <div class="font-mono text-xs font-black leading-none">{{ $d->format('d') }}</div>
                <div class="text-[9px] font-bold uppercase text-slate-400 mt-0.5">{{ $d->format('D')[0] }}</div>
              </th>
            @endforeach
            {{-- Summary Columns --}}
            <th class="px-2.5 py-3 text-center bg-emerald-50 text-emerald-800 font-extrabold border-l border-slate-200">P</th>
            <th class="px-2.5 py-3 text-center bg-amber-50 text-amber-800 font-extrabold">L</th>
            <th class="px-2.5 py-3 text-center bg-orange-50 text-orange-800 font-extrabold">HD</th>
            <th class="px-2.5 py-3 text-center bg-purple-100 text-purple-900 font-black border-x border-purple-300">PRM</th>
            <th class="px-2.5 py-3 text-center bg-yellow-50 text-amber-800 font-extrabold">OT</th>
            <th class="px-2.5 py-3 text-center bg-teal-50 text-teal-800 font-extrabold">LV</th>
            <th class="px-2.5 py-3 text-center bg-rose-50 text-rose-800 font-extrabold">A</th>
            <th class="px-3 py-3 text-center bg-indigo-50 text-indigo-900 font-black border-l border-slate-200">Rate%</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-[11px]">
          @forelse($employees as $emp)
            @php
              $recs = $attendanceMap[$emp->id] ?? [];
              $presentCount    = 0;
              $lateCount       = 0;
              $halfDayCount    = 0;
              $permissionCount = 0;
              $overtimeCount   = 0;
              $leaveCount      = 0;
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors">
              {{-- Sticky Employee Info --}}
              <td class="sticky left-0 z-10 bg-white px-4 py-2.5 font-medium text-slate-800 shadow-r">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 font-bold text-indigo-700 flex items-center justify-center text-[10px] shrink-0">
                    {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 truncate">{{ $emp->full_name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono truncate">{{ $emp->employee_code ?? 'EMP-' . $emp->id }} &bull; {{ $emp->department?->name ?? 'General' }}</div>
                  </div>
                </div>
              </td>

              {{-- Day Columns --}}
              @foreach($days as $d)
                @php
                  $dateStr = $d->toDateString();
                  $isSun = $d->isSunday();
                  $isHol = isset($holidays[$dateStr]);
                  $rec = $recs[$dateStr] ?? null;
                  $st = $rec?->status;
                  $isPerm = ($st === 'permission') || ($rec?->is_permission);

                  if ($isPerm) {
                      $permissionCount++;
                  } elseif ($st === 'present') {
                      $presentCount++;
                  } elseif ($st === 'late') {
                      $lateCount++;
                  } elseif ($st === 'half_day') {
                      $halfDayCount++;
                  } elseif ($st === 'overtime') {
                      $overtimeCount++;
                  } elseif ($st === 'leave') {
                      $leaveCount++;
                  }
                @endphp
                <td class="px-1 py-2 text-center border-r border-slate-100 {{ $isSun ? 'bg-slate-100/40' : '' }}">
                  @if($isSun)
                    <span class="text-slate-300 font-bold text-[9px]">OFF</span>
                  @elseif($isHol && empty($rec?->check_in))
                    <span class="text-slate-400 font-bold text-[9px]" title="{{ $holidays[$dateStr]->name ?? 'Holiday' }}">H</span>
                  @elseif($isPerm)
                    <span class="inline-flex items-center justify-center w-6 h-5 rounded font-black text-[9px] bg-purple-100 text-purple-900 border border-purple-300"
                          title="Approved Permission: {{ $rec->permission_hours ?? 1.5 }} hrs ({{ $rec->permission_reason ?? 'Approved' }})">
                      PRM
                    </span>
                  @elseif($st === 'present')
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">P</span>
                  @elseif($st === 'late')
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">L</span>
                  @elseif($st === 'half_day')
                    <span class="inline-flex items-center justify-center w-6 h-5 rounded font-bold text-[9px] bg-orange-100 text-orange-800">HD</span>
                  @elseif($st === 'overtime')
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded font-black text-[9px] bg-yellow-100 text-amber-900">OT</span>
                  @elseif($st === 'leave')
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded font-bold text-[9px] bg-teal-100 text-teal-800">LV</span>
                  @elseif($st === 'absent')
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded font-black text-[10px] bg-rose-100 text-rose-700">A</span>
                  @else
                    <span class="text-slate-300">—</span>
                  @endif
                </td>
              @endforeach

              {{-- Summary Calculation --}}
              @php
                $attendedUnits = $presentCount + $lateCount + ($halfDayCount * 0.5) + $permissionCount + $overtimeCount;
                $absentCount = max(0, $workingDaysCount - ($presentCount + $lateCount + $halfDayCount + $permissionCount + $leaveCount));
                $rate = $workingDaysCount > 0 ? round(($attendedUnits / $workingDaysCount) * 100, 1) : 0;
              @endphp
              <td class="px-2.5 py-2 text-center font-bold text-emerald-700 bg-emerald-50/40 border-l border-slate-200">{{ $presentCount }}</td>
              <td class="px-2.5 py-2 text-center font-bold text-amber-700 bg-amber-50/40">{{ $lateCount }}</td>
              <td class="px-2.5 py-2 text-center font-bold text-orange-700 bg-orange-50/40">{{ $halfDayCount }}</td>
              <td class="px-2.5 py-2 text-center font-black text-purple-900 bg-purple-100/70 border-x border-purple-200">{{ $permissionCount }}</td>
              <td class="px-2.5 py-2 text-center font-bold text-amber-800 bg-yellow-50/40">{{ $overtimeCount }}</td>
              <td class="px-2.5 py-2 text-center font-bold text-teal-700 bg-teal-50/40">{{ $leaveCount }}</td>
              <td class="px-2.5 py-2 text-center font-bold text-rose-600 bg-rose-50/40">{{ $absentCount }}</td>
              <td class="px-3 py-2 text-center font-black border-l border-slate-200 {{ $rate >= 90 ? 'text-emerald-700' : ($rate >= 75 ? 'text-blue-700' : 'text-amber-700') }}">
                {{ $rate }}%
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="40" class="py-12 text-center text-slate-400">
                No staff records found for the selected department.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
