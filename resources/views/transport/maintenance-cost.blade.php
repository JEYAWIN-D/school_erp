@extends('layouts.app')
@section('title','Vehicle Maintenance Cost Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Maintenance Cost per Vehicle</h1>
      <p class="page-subtitle">Annual breakdown of maintenance expenses across fleet</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('transport.maintenance-cost.pdf', ['year' => $year]) }}" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="{{ route('transport.maintenance-cost.excel', ['year' => $year]) }}" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="{{ route('transport.maintenance') }}" class="btn btn-secondary btn-sm">← Maintenance Logs</a>
    </div>
  </div>

  <form method="GET" class="card-flat py-3"><div class="flex gap-3 items-end">
    <div>
      <label class="label">Year</label>
      <select name="year" class="select w-32" onchange="this.form.submit()">
        @foreach(range(now()->year, now()->year - 4) as $y)
        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
        @endforeach
      </select>
    </div>
  </div></form>

  {{-- Grand total summary --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-700">₹{{ number_format($grandTotal, 0) }}</p>
      <p class="text-xs text-slate-400 mt-1">Total Maintenance Cost {{ $year }}</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-indigo-600">{{ $perVehicle->count() }}</p>
      <p class="text-xs text-slate-400 mt-1">Vehicles in Fleet</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-red-600">{{ $perVehicle->sum('breakdown_count') }}</p>
      <p class="text-xs text-slate-400 mt-1">Breakdown Incidents</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600">{{ $perVehicle->sum('scheduled_count') }}</p>
      <p class="text-xs text-slate-400 mt-1">Scheduled Services</p>
    </div>
  </div>

  {{-- Per-vehicle breakdown --}}
  @forelse($perVehicle->filter(fn($v) => $v['total'] > 0) as $row)
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-semibold text-slate-800">{{ $row['vehicle']->vehicle_number }}</h3>
        <p class="text-xs text-slate-400">{{ $row['vehicle']->make ?? '' }} {{ $row['vehicle']->model ?? '' }} &nbsp;|&nbsp; {{ $row['vehicle']->vehicle_type ?? '' }}</p>
      </div>
      <div class="text-right">
        <p class="text-xl font-bold text-indigo-600">₹{{ number_format($row['total'], 0) }}</p>
        <p class="text-xs text-slate-400">Total for {{ $year }}</p>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4 text-sm text-center">
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-red-600">{{ $row['breakdown_count'] }}</p>
        <p class="text-xs text-slate-400">Breakdowns</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-green-600">{{ $row['scheduled_count'] }}</p>
        <p class="text-xs text-slate-400">Scheduled</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-slate-600">{{ $row['logs']->count() }}</p>
        <p class="text-xs text-slate-400">Total Jobs</p>
      </div>
    </div>

    {{-- Monthly cost sparkline bar --}}
    @if($row['by_month']->isNotEmpty())
    <div class="mb-4">
      <p class="text-xs text-slate-400 mb-2">Monthly cost distribution</p>
      <div class="flex items-end gap-1 h-10">
        @php $maxMonth = $row['by_month']->max() ?: 1; @endphp
        @foreach($months as $num => $name)
          @php $val = $row['by_month'][$num] ?? 0; $h = round(($val/$maxMonth)*40); @endphp
          <div class="flex-1 flex flex-col items-center gap-0.5" title="{{ $name }}: ₹{{ number_format($val,0) }}">
            <div class="w-full rounded-sm bg-indigo-400" style="height: {{ $h > 0 ? max($h, 2) : 0 }}px"></div>
            <span class="text-slate-400" style="font-size:7px">{{ $name }}</span>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Logs table --}}
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Type</th>
          <th class="th">Work Done</th>
          <th class="th">Vendor</th>
          <th class="th text-right">Cost</th>
          <th class="th">Next Service</th>
        </tr></thead>
        <tbody>
          @foreach($row['logs']->sortByDesc('maintenance_date') as $log)
          <tr class="tr">
            <td class="td text-xs">{{ \Carbon\Carbon::parse($log->maintenance_date)->format('d M Y') }}</td>
            <td class="td">
              <span class="badge-{{ $log->maintenance_type==='breakdown'?'red':'green' }} text-xs capitalize">
                {{ ucfirst($log->maintenance_type) }}
              </span>
            </td>
            <td class="td text-xs text-slate-600 max-w-xs truncate">{{ $log->work_done }}</td>
            <td class="td text-xs text-slate-500">{{ $log->vendor ?? '—' }}</td>
            <td class="td text-right font-semibold text-sm">₹{{ number_format($log->cost, 0) }}</td>
            <td class="td text-xs text-slate-400">
              @if($log->next_service_date)
                @php $next = \Carbon\Carbon::parse($log->next_service_date); @endphp
                <span class="{{ $next->isPast() ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
                  {{ $next->format('d M Y') }}
                  @if($next->isPast()) (overdue)@endif
                </span>
              @else
                —
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-slate-50">
            <td colspan="4" class="td font-semibold">Vehicle Total</td>
            <td class="td text-right font-bold text-indigo-700">₹{{ number_format($row['total'], 0) }}</td>
            <td class="td"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">
    No maintenance records found for {{ $year }}.
  </div>
  @endforelse

  {{-- Vehicles with no maintenance this year --}}
  @if($perVehicle->filter(fn($v) => $v['total'] == 0)->count() > 0)
  <div class="card">
    <h3 class="font-semibold text-slate-600 mb-3 text-sm">No Maintenance Recorded in {{ $year }}</h3>
    <div class="flex flex-wrap gap-2">
      @foreach($perVehicle->filter(fn($v) => $v['total'] == 0) as $row)
      <span class="badge-slate text-xs">{{ $row['vehicle']->vehicle_number }}</span>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
