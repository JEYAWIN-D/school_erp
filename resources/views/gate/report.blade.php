@extends('layouts.app')
@section('title', 'Gate Visitor Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Gate Visitor Report</h1>
    <div class="flex gap-2">
      <a href="{{ route('gate.index') }}" class="btn-sm btn-secondary">← Gate</a>
      <a href="{{ route('gate.report', array_merge(request()->query(), ['export'=>'csv'])) }}" class="btn-sm btn-secondary">Export CSV</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div><label class="label">From</label><input type="date" name="from" value="{{ $from }}" class="input"></div>
    <div><label class="label">To</label><input type="date" name="to" value="{{ $to }}" class="input"></div>
    <button type="submit" class="btn-primary btn-sm">Generate</button>
    <div class="ml-auto flex gap-4 text-center">
      <div><p class="text-2xl font-bold text-indigo-600">{{ $totalIn }}</p><p class="text-xs text-slate-400">Total Visitors</p></div>
      <div><p class="text-2xl font-bold text-emerald-600">{{ $totalOut }}</p><p class="text-xs text-slate-400">Checked Out</p></div>
      <div><p class="text-2xl font-bold text-rose-600">{{ $stillInside }}</p><p class="text-xs text-slate-400">Still Inside</p></div>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">#</th>
        <th class="th">Visitor Name</th>
        <th class="th">Phone</th>
        <th class="th">Purpose</th>
        <th class="th">Whom to Meet</th>
        <th class="th">Vehicle</th>
        <th class="th">In Time</th>
        <th class="th">Out Time</th>
        <th class="th">Duration</th>
      </tr></thead>
      <tbody>
        @forelse($visitors as $i => $v)
        <tr class="tr">
          <td class="td text-slate-400">{{ $i+1 }}</td>
          <td class="td font-medium">{{ $v->visitor_name }}</td>
          <td class="td text-xs">{{ $v->visitor_phone ?? '—' }}</td>
          <td class="td text-xs">{{ $v->purpose }}</td>
          <td class="td text-xs">{{ $v->whom_to_meet ?? '—' }}</td>
          <td class="td text-xs">{{ $v->vehicle_number ?? '—' }}</td>
          <td class="td text-xs">{{ $v->in_time->format('d/m h:i A') }}</td>
          <td class="td text-xs">{{ $v->out_time ? $v->out_time->format('h:i A') : '—' }}</td>
          <td class="td text-xs">
            @if($v->out_time)
              {{ $v->in_time->diffForHumans($v->out_time, true) }}
            @else <span class="text-rose-500">Still inside</span> @endif
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="9">No visitors in this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
