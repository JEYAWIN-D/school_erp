@extends('layouts.app')
@section('title', 'Increment History')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Increment History Report</h1>
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
      <a href="{{ route('hr.increment-history.pdf', request()->only('from','to')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF</a>
      <a href="{{ route('hr.increment-history.excel', request()->only('from','to')) }}"
         class="btn btn-secondary btn-sm">Excel</a>
    </form>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700">{{ $increments->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Employees Incremented</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalIncrement, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Increment Amount</p>
    </div>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Employee</th>
          <th class="th">Department</th>
          <th class="th">Appraisal Year</th>
          <th class="th">Increment (₹)</th>
          <th class="th">Increment %</th>
          <th class="th">Effective From</th>
          <th class="th">Rating</th>
        </tr>
      </thead>
      <tbody>
        @forelse($increments as $inc)
        <tr class="tr">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td font-medium">{{ $inc->employee->name }}</td>
          <td class="td text-slate-500">{{ $inc->employee->department?->name ?? '—' }}</td>
          <td class="td">{{ $inc->appraisal_year }}</td>
          <td class="td text-right font-semibold text-green-700">₹{{ number_format($inc->increment_amount, 2) }}</td>
          <td class="td text-center">{{ $inc->increment_percent ? number_format($inc->increment_percent, 1).'%' : '—' }}</td>
          <td class="td">{{ $inc->increment_effective_date?->format('d M Y') ?? '—' }}</td>
          <td class="td">
            @if($inc->rating_label)
              <span class="badge-green text-xs">{{ $inc->rating_label }}</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No increment records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
