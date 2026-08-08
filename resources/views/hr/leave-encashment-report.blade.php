@extends('layouts.app')
@section('title', 'Leave Encashment Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Leave Encashment Report</h1>
    <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  {{-- Date filter --}}
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">From</label>
        <input type="date" name="from" value="{{ $fyStart->toDateString() }}" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">To</label>
        <input type="date" name="to" value="{{ $fyEnd->toDateString() }}" class="input text-sm">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
      <a href="{{ route('hr.leave-encashment-report.pdf', request()->only('from','to')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="{{ route('hr.leave-encashment-report.excel', request()->only('from','to')) }}"
         class="btn btn-secondary btn-sm">Excel</a>
    </form>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700">{{ $encashments->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Encashments</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalPaid, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Paid</p>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Encashment Date</th>
          <th class="th">Days Encashed</th>
          <th class="th">Per Day (₹)</th>
          <th class="th">Amount (₹)</th>
          <th class="th">Year</th>
          <th class="th">Remarks</th>
        </tr>
      </thead>
      <tbody>
        @forelse($encashments as $enc)
        <tr class="tr">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td font-medium">{{ $enc->employee->name }}</td>
          <td class="td text-slate-500">{{ $enc->employee->department?->name ?? '—' }}</td>
          <td class="td">{{ $enc->encashment_date->format('d M Y') }}</td>
          <td class="td text-center">{{ $enc->days_encashed ?? '—' }}</td>
          <td class="td text-right">{{ $enc->basic_per_day ? '₹'.number_format($enc->basic_per_day, 2) : '—' }}</td>
          <td class="td text-right font-semibold text-green-700">₹{{ number_format($enc->amount, 2) }}</td>
          <td class="td text-center">{{ $enc->year }}</td>
          <td class="td text-slate-500 text-xs">{{ $enc->remarks ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="td text-center text-slate-400 py-8">No leave encashment records found.</td></tr>
        @endforelse
      </tbody>
      @if($encashments->count())
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="6" class="td text-right">Total:</td>
          <td class="td text-right text-green-700">₹{{ number_format($totalPaid, 2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
