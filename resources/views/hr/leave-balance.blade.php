@extends('layouts.app')
@section('title', 'Leave Balance Tracker')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Leave Balance Tracker</h1>
      <p class="page-subtitle">Remaining leave entitlements for {{ $year }}</p>
    </div>
    <a href="{{ route('hr.leaves') }}" class="btn btn-secondary">All Leave Requests</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Year</label>
        <select name="year" class="select">
          @for($y = now()->year; $y >= now()->year - 3; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>
      <div>
        <label class="label">Department</label>
        <select name="department" class="select">
          <option value="">All Departments</option>
          @foreach($departments as $dept)
          <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  {{-- Carry Forward Form --}}
  <div class="card" x-data="{ showCF: false }">
    <button @click="showCF = !showCF" class="text-sm font-semibold text-indigo-600 flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
      Carry Forward Leaves
    </button>
    <div x-show="showCF" x-transition class="mt-4 pt-4 border-t">
      <form method="POST" action="{{ route('hr.leaves.carry-forward') }}" class="flex flex-wrap gap-4 items-end">
        @csrf
        <div>
          <label class="label">From Year</label>
          <input type="number" name="from_year" class="input w-28" value="{{ $year }}" required>
        </div>
        <div>
          <label class="label">To Year</label>
          <input type="number" name="to_year" class="input w-28" value="{{ $year + 1 }}" required>
        </div>
        <button type="submit" class="btn btn-warning" onclick="return confirm('This will create carry-forward leave entries for all active employees. Continue?')">
          Run Carry-Forward
        </button>
      </form>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="w-full min-w-max">
      <thead>
        <tr>
          <th class="th">Employee</th>
          <th class="th">Dept</th>
          @foreach($leaveTypes as $lt)
          <th class="th text-center" colspan="2">{{ $lt->name }}<br><span class="text-xs font-normal opacity-70">({{ $lt->days_allowed }} days)</span></th>
          @endforeach
        </tr>
        <tr>
          <th class="th" colspan="2"></th>
          @foreach($leaveTypes as $lt)
          <th class="th text-center text-xs">Used</th>
          <th class="th text-center text-xs">Balance</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($employees as $emp)
        <tr class="tr">
          <td class="td">
            <a href="{{ route('hr.employees.show', $emp->id) }}" class="font-medium text-indigo-600 hover:underline">
              {{ $emp->first_name }} {{ $emp->last_name }}
            </a>
            <div class="text-xs text-slate-400">{{ $emp->employee_number }}</div>
          </td>
          <td class="td text-slate-500 text-sm">{{ $emp->department ?? '—' }}</td>
          @foreach($leaveTypes as $lt)
          @php
            $used    = $usedMap[$emp->id][$lt->id]->total_days ?? 0;
            $balance = max(0, $lt->days_allowed - $used);
          @endphp
          <td class="td text-center text-sm {{ $used > $lt->days_allowed ? 'text-red-600 font-bold' : '' }}">{{ $used }}</td>
          <td class="td text-center">
            <span class="text-sm font-semibold {{ $balance <= 2 ? 'text-red-500' : ($balance <= 5 ? 'text-amber-500' : 'text-green-600') }}">{{ $balance }}</span>
          </td>
          @endforeach
        </tr>
        @endforeach
        @if($employees->isEmpty())
        <tr><td colspan="{{ 2 + $leaveTypes->count() * 2 }}" class="td text-center py-8 text-slate-400">No employees found.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
</div>
@endsection
