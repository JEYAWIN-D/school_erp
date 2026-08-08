@extends('layouts.app')
@section('title', 'Outstanding Balance Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Outstanding Balance Report</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} — Students with pending dues</p>
    </div>
    <a href="{{ route('fees.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          @foreach($classes as $class)
          <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  @if($outstanding->isNotEmpty())
  @php $totalOutstanding = $outstanding->sum('balance'); @endphp
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600">{{ $outstanding->count() }}</p>
      <p class="text-sm text-slate-500 mt-1">Students with Dues</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600">₹{{ number_format($totalOutstanding / 100000, 2) }}L</p>
      <p class="text-sm text-slate-500 mt-1">Total Outstanding</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-amber-600">₹{{ number_format($outstanding->avg('balance'), 0) }}</p>
      <p class="text-sm text-slate-500 mt-1">Average Due Per Student</p>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student</th>
            <th class="th">Class</th>
            <th class="th">Total Fee</th>
            <th class="th">Paid</th>
            <th class="th">Outstanding</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($outstanding as $i => $row)
          <tr class="tr">
            <td class="td text-slate-400">{{ $i + 1 }}</td>
            <td class="td">
              <a href="{{ route('students.show', $row['student']->id) }}" class="font-medium text-indigo-600 hover:underline">
                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
              </a>
              <div class="text-xs text-slate-400">{{ $row['student']->admission_no ?? $row['student']->admission_number }}</div>
            </td>
            <td class="td text-slate-500">{{ $row['student']->currentEnrollment?->class?->name ?? '—' }}</td>
            <td class="td">₹{{ number_format($row['totalFee'], 2) }}</td>
            <td class="td text-green-600">₹{{ number_format($row['totalPaid'], 2) }}</td>
            <td class="td">
              <span class="font-semibold text-red-600">₹{{ number_format($row['balance'], 2) }}</span>
              <div class="mt-1 bg-slate-200 rounded-full h-1.5 w-24">
                <div class="h-1.5 rounded-full bg-red-500" style="width:{{ $row['totalFee'] > 0 ? round($row['balance']/$row['totalFee']*100) : 0 }}%"></div>
              </div>
            </td>
            <td class="td">
              <a href="{{ route('fees.collect', ['student_id' => $row['student']->id]) }}" class="btn btn-primary btn-sm">Collect</a>
              <a href="{{ route('fees.demand-notice', $row['student']->id) }}" class="btn btn-secondary btn-sm mt-1">Notice</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request()->hasAny(['class_id']))
  <div class="alert-success">No outstanding dues found for the selected filters.</div>
  @else
  <div class="card text-center py-12 text-slate-400">Select a class or click Filter to see outstanding balances.</div>
  @endif
</div>
@endsection
