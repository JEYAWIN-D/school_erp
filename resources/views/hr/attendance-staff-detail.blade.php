@extends('layouts.app')
@section('title', $employee->full_name . ' - Attendance Detail')

@section('content')
<div class="space-y-6">

  <style>
    .inspect-profile-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
    }
    @media (min-width: 640px) {
      .inspect-profile-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    @media (min-width: 1024px) {
      .inspect-profile-grid {
        grid-template-columns: repeat(6, 1fr);
      }
    }

    .inspect-cards-wrapper {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    @media (min-width: 1024px) {
      .inspect-cards-wrapper {
        flex-direction: row;
        align-items: stretch;
      }
      .inspect-card-attendance {
        flex: 1.8;
        min-width: 0;
      }
      .inspect-card-leave {
        flex: 1;
        min-width: 0;
      }
    }

    .attendance-metric-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.75rem;
    }
    @media (min-width: 640px) {
      .attendance-metric-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    @media (min-width: 1200px) {
      .attendance-metric-grid {
        grid-template-columns: repeat(6, 1fr);
      }
    }

    .leave-metric-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.75rem;
    }
  </style>

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.attendance.view', request()->except('id')) }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to View Attendance">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-blue-100 flex-shrink-0" style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Individual Staff Attendance</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Detailed attendance and leave history &bull; {{ $periodLabel }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-secondary btn-sm text-xs font-bold">
        Staff Profile
      </a>
    </div>
  </div>

  {{-- Staff Header Profile Card --}}
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="inspect-profile-grid">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Staff Name</span>
        <span class="font-extrabold text-sm text-slate-900 block mt-0.5">{{ $employee->full_name }}</span>
      </div>

      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Staff ID</span>
        <span class="font-mono font-bold text-sm text-slate-800 block mt-0.5">{{ $employee->employee_code ?? 'EMP-' . $employee->id }}</span>
      </div>

      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Category</span>
        <span class="{{ $employee->category_badge_class }} text-[9px] px-2 py-0.5 font-bold uppercase rounded mt-1 inline-block">
          {{ $employee->category_label }}
        </span>
      </div>

      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Department</span>
        <span class="font-semibold text-xs text-slate-800 block mt-0.5">{{ $employee->department_name }}</span>
      </div>

      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Designation</span>
        <span class="font-semibold text-xs text-slate-800 block mt-0.5">{{ $employee->designation_name }}</span>
      </div>

      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Joining Date</span>
        <span class="font-mono text-xs text-slate-700 block mt-0.5">
          {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : '—' }}
        </span>
      </div>
    </div>
  </div>

  {{-- Attendance Summary & Leave Summary Grids --}}
  <div class="inspect-cards-wrapper">

    {{-- ATTENDANCE SUMMARY --}}
    <div class="inspect-card-attendance bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h2 class="text-xs font-black uppercase tracking-wider text-slate-800">Attendance Summary</h2>
        <p class="text-[11px] text-slate-400">Attendance metrics for the selected period ({{ $periodLabel }})</p>
      </div>

      <div class="attendance-metric-grid">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Working Days</span>
          <span class="text-xl font-black font-mono text-slate-800 mt-0.5 block">{{ $workingDays }}</span>
        </div>

        <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 text-center">
          <span class="text-[10px] font-bold uppercase text-emerald-700 block">Present</span>
          <span class="text-xl font-black font-mono text-emerald-600 mt-0.5 block">{{ $presentCount }}</span>
        </div>

        <div class="p-3 bg-rose-50/50 rounded-xl border border-rose-100 text-center">
          <span class="text-[10px] font-bold uppercase text-rose-700 block">Absent</span>
          <span class="text-xl font-black font-mono text-rose-600 mt-0.5 block">{{ $absentCount }}</span>
        </div>

        <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100 text-center">
          <span class="text-[10px] font-bold uppercase text-amber-700 block">Leave</span>
          <span class="text-xl font-black font-mono text-amber-600 mt-0.5 block">{{ $leaveCount }}</span>
        </div>

        <div class="p-3 bg-orange-50/50 rounded-xl border border-orange-100 text-center">
          <span class="text-[10px] font-bold uppercase text-orange-700 block">Half Day</span>
          <span class="text-xl font-black font-mono text-orange-600 mt-0.5 block">{{ $halfDayCount }}</span>
        </div>

        <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 text-center">
          <span class="text-[10px] font-bold uppercase text-indigo-700 block">Attendance %</span>
          <span class="text-xl font-black font-mono text-indigo-600 mt-0.5 block">{{ $attendancePercentage }}%</span>
        </div>
      </div>

      @if(($onDutyCount ?? 0) > 0 || ($paidOffCount ?? 0) > 0 || ($permissionCount ?? 0) > 0)
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-[11px]">
          @if(($onDutyCount ?? 0) > 0)
            <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 font-bold">On Duty: {{ $onDutyCount }}</span>
          @endif
          @if(($paidOffCount ?? 0) > 0)
            <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 border border-purple-200 font-bold">Paid Off: {{ $paidOffCount }}</span>
          @endif
          @if(($permissionCount ?? 0) > 0)
            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-bold">Permission: {{ $permissionCount }}</span>
          @endif
        </div>
      @endif
    </div>

    {{-- LEAVE SUMMARY --}}
    <div class="inspect-card-leave bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h2 class="text-xs font-black uppercase tracking-wider text-slate-800">Leave Summary</h2>
        <p class="text-[11px] text-slate-400">Current annual leave balance</p>
      </div>

      <div class="leave-metric-grid text-center pt-1">
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Total Leave</span>
          <span class="text-lg font-black font-mono text-slate-800 mt-0.5 block">{{ $totalAllowedLeave }}</span>
        </div>

        <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100">
          <span class="text-[10px] font-bold uppercase text-amber-700 block">Taken</span>
          <span class="text-lg font-black font-mono text-amber-600 mt-0.5 block">{{ $approvedLeaveTaken }}</span>
        </div>

        <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
          <span class="text-[10px] font-bold uppercase text-emerald-700 block">Remaining</span>
          <span class="text-lg font-black font-mono text-emerald-600 mt-0.5 block">{{ $leaveRemaining }}</span>
        </div>
      </div>
    </div>

  </div>

  {{-- Detailed Attendance History Table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
    <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h2 class="text-xs font-black uppercase tracking-wider text-slate-800">Attendance History Records</h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Day-by-day attendance punches and recorded statuses</p>
      </div>
      <span class="text-xs font-bold text-slate-500 font-mono">{{ $history->count() }} records logged</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
            <th class="py-3.5 px-6 w-[40%]">Date</th>
            <th class="py-3.5 px-6 w-[35%]">Day</th>
            <th class="py-3.5 px-6 w-[25%] text-left">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs font-medium">
          @forelse($history as $rec)
            @php $cDate = \Carbon\Carbon::parse($rec->date); @endphp
            <tr class="hover:bg-slate-50/60 transition-colors">
              {{-- Date --}}
              <td class="py-3.5 px-6 font-mono font-bold text-slate-800">
                {{ $cDate->format('d-m-Y') }}
              </td>

              {{-- Day --}}
              <td class="py-3.5 px-6 font-semibold text-slate-700">
                {{ $cDate->format('l') }}
              </td>

              {{-- Status --}}
              <td class="py-3.5 px-6 text-left">
                @if($rec->status === 'present')
                  <span class="badge-green text-[10px] font-bold px-2.5 py-0.5 rounded-full">Present</span>
                @elseif($rec->status === 'absent')
                  <span class="badge-red text-[10px] font-bold px-2.5 py-0.5 rounded-full">Absent</span>
                @elseif($rec->status === 'late')
                  <span class="badge-amber text-[10px] font-bold px-2.5 py-0.5 rounded-full">Late</span>
                @elseif($rec->status === 'half_day')
                  <span class="badge-orange text-[10px] font-bold px-2.5 py-0.5 rounded-full">Half-Day</span>
                @elseif($rec->status === 'on_duty')
                  <span class="badge-blue text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 border border-sky-200">On Duty</span>
                @elseif($rec->status === 'paid_off')
                  <span class="badge-purple text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">Paid Off</span>
                @elseif($rec->status === 'permission')
                  <div class="inline-flex flex-col items-start gap-0.5">
                    <span class="badge-amber text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Permission</span>
                    @if($rec->check_out)
                      <span class="text-[10px] font-mono text-slate-500 mt-0.5">
                        Out: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($rec->check_out)->format('g:i A') }}</strong>
                        @if($rec->check_in)
                          &bull; In: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($rec->check_in)->format('g:i A') }}</strong>{{ $rec->in_time_auto_filled ? ' (Dispersal)' : '' }}
                        @else
                          &bull; <span class="text-amber-600 font-semibold italic">Awaiting return</span>
                        @endif
                      </span>
                    @endif
                  </div>
                @elseif($rec->status === 'leave' || $rec->status === 'on_leave')
                  <span class="badge-amber text-[10px] font-bold px-2.5 py-0.5 rounded-full">Leave</span>
                @elseif($rec->status === 'holiday')
                  <span class="badge-slate text-[10px] font-bold px-2.5 py-0.5 rounded-full">Holiday</span>
                @else
                  <span class="badge-blue text-[10px] font-bold px-2.5 py-0.5 rounded-full">{{ ucfirst(str_replace('_', ' ', $rec->status)) }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="py-12 text-center text-slate-400">
                <p class="text-sm font-semibold text-slate-500">No attendance records found for this period.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
