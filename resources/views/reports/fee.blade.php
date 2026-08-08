@extends('layouts.app')
@section('title', 'Fee Collection Report')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Fee Collection Report</h1>
      <p class="text-sm text-slate-500 mt-0.5">{{ $year?->name ?? 'All Years' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('reports.index') }}" class="btn btn-outline">← Back</a>
      <a href="{{ route('reports.fee.excel', request()->query()) }}" class="btn btn-secondary btn-sm">Export Excel</a>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">From</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="input">
      </div>
      <div>
        <label class="label">To</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="input">
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Class-wise Collection — {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</h2>
    @if($classwise->count())
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>Class</th>
            <th class="text-right">Students Paid</th>
            <th class="text-right">Demand (₹)</th>
            <th class="text-right">Collected (₹)</th>
            <th class="text-right">Outstanding (₹)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($classwise as $row)
          <tr>
            <td class="font-medium">{{ $row->class_name }}</td>
            <td class="text-right">{{ $row->payers }}</td>
            <td class="text-right">{{ $row->demand > 0 ? number_format($row->demand, 2) : '—' }}</td>
            <td class="text-right font-semibold text-emerald-700">{{ number_format($row->collected, 2) }}</td>
            <td class="text-right {{ ($row->outstanding ?? 0) > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
              {{ ($row->outstanding ?? 0) > 0 ? number_format($row->outstanding, 2) : '—' }}
            </td>
          </tr>
          @endforeach
          <tr class="font-bold bg-slate-50">
            <td>Total</td>
            <td class="text-right">{{ $classwise->sum('payers') }}</td>
            <td class="text-right">{{ number_format($classwise->sum('demand'), 2) }}</td>
            <td class="text-right text-emerald-700">{{ number_format($classwise->sum('collected'), 2) }}</td>
            <td class="text-right text-rose-600">{{ number_format($classwise->sum('outstanding'), 2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    @else
      <p class="text-slate-400 text-center py-8">No fee payments in this date range.</p>
    @endif
  </div>
</div>
@endsection
