@extends('layouts.app')
@section('title', 'Annual Collection Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Annual Collection Report</h1>
      <p class="page-subtitle">Year-on-year fee collection summary</p>
    </div>
    <a href="{{ route('fees.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-{{ min(count($data), 4) }} gap-4">
    @foreach($data as $year => $amount)
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-indigo-600">₹{{ number_format($amount / 100000, 2) }}L</p>
      <p class="text-sm text-slate-500 mt-1">{{ $year }}</p>
    </div>
    @endforeach
  </div>

  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4">Year-wise Collection</h2>
    @php $maxVal = max($data ?: [1]); @endphp
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Year</th>
            <th class="th text-right">Total Collection</th>
            <th class="th">Growth</th>
          </tr>
        </thead>
        <tbody>
          @php $prevAmount = null; @endphp
          @foreach($data as $year => $amount)
          @php
            $growth = $prevAmount > 0 ? round(($amount - $prevAmount) / $prevAmount * 100, 1) : null;
            $prevAmount = $amount;
          @endphp
          <tr class="tr">
            <td class="td font-medium">{{ $year }}</td>
            <td class="td text-right font-semibold">₹{{ number_format($amount, 2) }}</td>
            <td class="td">
              @if($growth !== null)
                <span class="text-sm font-medium {{ $growth >= 0 ? 'text-green-600' : 'text-red-500' }}">
                  {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                </span>
              @else
                <span class="text-slate-400 text-sm">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
