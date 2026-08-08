@extends('layouts.app')
@section('title', 'Fuel Expense Summary')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fuel Expense Summary</h1>
      <p class="page-subtitle">Monthly fuel consumption by vehicle</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('transport.fuel-summary.pdf', ['month'=>$month]) }}" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="{{ route('transport.fuel-summary.excel', ['month'=>$month]) }}" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="{{ route('transport.fuel') }}" class="btn btn-secondary btn-sm">← Fuel Log</a>
    </div>
  </div>

  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input" onchange="this.form.submit()">
      </div>
    </form>
  </div>

  @php [$yr,$mo] = explode('-',$month); $monthLabel = \Carbon\Carbon::createFromDate($yr,$mo,1)->format('F Y'); @endphp

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-slate-800">{{ number_format($grandTotals['litres'],1) }} L</p>
      <p class="text-sm text-slate-500 mt-1">Total Fuel — {{ $monthLabel }}</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-slate-800">₹{{ number_format($grandTotals['amount'],0) }}</p>
      <p class="text-sm text-slate-500 mt-1">Total Cost — {{ $monthLabel }}</p>
    </div>
  </div>

  @if($fuelByVehicle->isEmpty())
    <div class="card text-center py-12 text-slate-400">No fuel logs for {{ $monthLabel }}.</div>
  @else
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Vehicle</th>
        <th class="th text-center">Fill Ups</th>
        <th class="th text-center">Litres</th>
        <th class="th text-center">Amount (₹)</th>
        <th class="th text-center">KM Covered</th>
        <th class="th text-center">Efficiency (km/L)</th>
        <th class="th text-center">Cost/KM (₹)</th>
      </tr></thead>
      <tbody>
        @foreach($fuelByVehicle->sortByDesc('total_amount') as $row)
        <tr class="tr">
          <td class="td font-medium">
            {{ $row->vehicle?->vehicle_number ?? ('Vehicle #'.$row->vehicle_id) }}
            @if($row->vehicle?->vehicle_type)
              <span class="badge-slate ml-1 text-xs capitalize">{{ $row->vehicle->vehicle_type }}</span>
            @endif
          </td>
          <td class="td text-center">{{ $row->fill_count }}</td>
          <td class="td text-center font-medium">{{ number_format($row->total_litres, 1) }}</td>
          <td class="td text-center font-semibold text-slate-800">{{ number_format($row->total_amount, 0) }}</td>
          <td class="td text-center">{{ $row->km_covered ? number_format($row->km_covered,0) : '—' }}</td>
          <td class="td text-center">
            @if($row->total_litres > 0 && $row->km_covered)
              @php $efficiency = round($row->km_covered/$row->total_litres,2); @endphp
              <span class="{{ $efficiency >= 10 ? 'badge-green' : ($efficiency >= 7 ? 'badge-yellow' : 'badge-red') }}">{{ $efficiency }} km/L</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td class="td text-center">
            @if($row->km_covered > 0 && $row->total_amount > 0)
              @php $costPerKm = round($row->total_amount / $row->km_covered, 2); @endphp
              <span class="font-medium text-slate-700">₹{{ number_format($costPerKm, 2) }}</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
        </tr>
        @endforeach
        <tr class="tr bg-slate-50 font-semibold">
          <td class="td">Total</td>
          <td class="td text-center">{{ $fuelByVehicle->sum('fill_count') }}</td>
          <td class="td text-center">{{ number_format($grandTotals['litres'],1) }}</td>
          <td class="td text-center">{{ number_format($grandTotals['amount'],0) }}</td>
          <td class="td text-center">—</td>
          <td class="td text-center">—</td>
          <td class="td text-center">—</td>
        </tr>
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
