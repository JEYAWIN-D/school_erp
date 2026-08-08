@extends('layouts.app')
@section('title','Attendance vs Leave Reconciliation')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Staff Attendance vs Leave Reconciliation</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" class="input" value="{{ $month }}">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Employee','Dept','Total Days Marked','Present','Absent','Approved Leave','LOP Days','Status'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($reconciliation as $row)
        @php $emp = $row['employee']; @endphp
        <tr class="hover:bg-slate-50 {{ $row['lop_days'] > 0 ? 'bg-red-50/30' : '' }}">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800">{{ $emp->full_name }}</p>
            <p class="text-xs text-slate-400">{{ $emp->employee_id }}</p>
          </td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $emp->department?->name ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $row['total_days'] }}</td>
          <td class="px-4 py-3 text-green-600 font-semibold">{{ $row['present_days'] }}</td>
          <td class="px-4 py-3 {{ $row['absent_days'] > 0 ? 'text-red-600' : 'text-slate-300' }}">
            {{ $row['absent_days'] }}
          </td>
          <td class="px-4 py-3 text-blue-600">{{ $row['approved_leave'] }}</td>
          <td class="px-4 py-3">
            @if($row['lop_days'] > 0)
            <span class="badge-red text-xs">{{ $row['lop_days'] }} LOP</span>
            @else
            <span class="text-slate-300">0</span>
            @endif
          </td>
          <td class="px-4 py-3">
            @if($row['total_days'] === 0)
            <span class="badge-amber text-xs">Not Marked</span>
            @elseif($row['lop_days'] > 0)
            <span class="badge-red text-xs">LOP Deduction</span>
            @else
            <span class="badge-green text-xs">Reconciled</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No data found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
