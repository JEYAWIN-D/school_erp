@extends('layouts.app')
@section('title', 'Defaulter Aging Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Defaulter Aging Report</h1>
      <p class="page-subtitle">Outstanding fee analysis by days since last payment</p>
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

  @php
    $bands = [
      '0-30'  => ['label' => '0–30 Days',  'color' => 'amber',  'count' => count($agingBands['0-30'])],
      '31-60' => ['label' => '31–60 Days', 'color' => 'orange', 'count' => count($agingBands['31-60'])],
      '61-90' => ['label' => '61–90 Days', 'color' => 'red',    'count' => count($agingBands['61-90'])],
      '90+'   => ['label' => '90+ Days',   'color' => 'rose',   'count' => count($agingBands['90+'])],
    ];
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    @foreach($bands as $key => $band)
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-{{ $band['color'] }}-600">{{ $band['count'] }}</p>
      <p class="text-sm text-slate-500 mt-1">{{ $band['label'] }}</p>
    </div>
    @endforeach
  </div>

  @foreach(['0-30' => ['0–30 Days','amber'], '31-60' => ['31–60 Days','orange'], '61-90' => ['61–90 Days','red'], '90+' => ['90+ Days','rose']] as $key => [$label, $color])
  @if(count($agingBands[$key]) > 0)
  <div class="card">
    <h2 class="font-semibold text-{{ $color }}-700 mb-4">{{ $label }} — {{ count($agingBands[$key]) }} Students</h2>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Class</th>
            <th class="th">Total Fee</th>
            <th class="th">Paid</th>
            <th class="th">Balance</th>
            <th class="th">Days Since Payment</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($agingBands[$key] as $d)
          <tr class="tr">
            <td class="td font-medium">
              <a href="{{ route('students.show', $d['student']->id) }}" class="text-indigo-600 hover:underline">
                {{ $d['student']->first_name }} {{ $d['student']->last_name }}
              </a>
            </td>
            <td class="td text-slate-500">{{ $d['student']->currentEnrollment?->class?->name ?? '—' }}</td>
            <td class="td">₹{{ number_format($d['totalFee'], 2) }}</td>
            <td class="td text-green-600">₹{{ number_format($d['totalPaid'], 2) }}</td>
            <td class="td font-semibold text-{{ $color }}-600">₹{{ number_format($d['balance'], 2) }}</td>
            <td class="td">{{ $d['daysSince'] }} days</td>
            <td class="td">
              <a href="{{ route('fees.demand-notice', $d['student']->id) }}" class="btn btn-secondary btn-sm">Demand Notice</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
  @endforeach

  @if($defaulterList->isEmpty())
  <div class="card text-center py-12 text-slate-400">No defaulters found for the current academic year.</div>
  @endif
</div>
@endsection
