@extends('layouts.app')
@section('title','Admission Analytics')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Admission Analytics</h1>
      <p class="page-subtitle">{{ $currentYear?->name ?? 'Current Year' }} — Enquiry & conversion insights</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admissions.analytics.pdf') }}" target="_blank" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Export PDF
      </a>
      <a href="{{ route('admissions.analytics.excel') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
      </a>
      <a href="{{ route('admissions.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Enquiries
      </a>
    </div>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['label' => 'Total Enquiries', 'value' => $byStatus->sum(),           'color' => 'text-indigo-600'],
      ['label' => 'Converted',       'value' => $byStatus['converted'] ?? 0, 'color' => 'text-green-600'],
      ['label' => 'Follow Up',       'value' => $byStatus['follow_up'] ?? 0, 'color' => 'text-amber-600'],
      ['label' => 'Lost',            'value' => $byStatus['lost'] ?? 0,      'color' => 'text-red-600'],
    ] as $s)
    <div class="card text-center py-5">
      <p class="text-3xl font-bold {{ $s['color'] }}" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $s['value'] }}</p>
      <p class="text-sm text-slate-500 mt-1">{{ $s['label'] }}</p>
    </div>
    @endforeach
  </div>

  @if($yoyData)
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Year-over-Year Comparison</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      @foreach([$yoyData['current'], $yoyData['prev']] as $yr)
      <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
        <p class="text-sm font-semibold text-slate-500 mb-3">{{ $yr['year'] }}</p>
        <div class="flex gap-6">
          <div>
            <p class="text-2xl font-bold text-indigo-600" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $yr['enquiries'] }}</p>
            <p class="text-xs text-slate-400">Enquiries</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-green-600" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $yr['converted'] }}</p>
            <p class="text-xs text-slate-400">Converted</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-slate-600" style="font-family:'Plus Jakarta Sans',sans-serif;">
              {{ $yr['enquiries'] > 0 ? round(($yr['converted'] / $yr['enquiries']) * 100) : 0 }}%
            </p>
            <p class="text-xs text-slate-400">Conversion Rate</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Source-wise Breakdown</h3>
      <canvas id="sourceChart" height="220"></canvas>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Monthly Enquiry Trend</h3>
      <canvas id="trendChart" height="220"></canvas>
    </div>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Status Breakdown</h3>
    <div class="space-y-3">
      @php
        $total = $byStatus->sum() ?: 1;
        $statusColors = ['new'=>'bg-blue-500','follow_up'=>'bg-amber-500','converted'=>'bg-green-500','lost'=>'bg-red-500','application'=>'bg-purple-500','confirmed'=>'bg-cyan-500','enrolled'=>'bg-teal-500'];
        $statusLabels = ['new'=>'New','follow_up'=>'Follow Up','converted'=>'Converted','lost'=>'Lost','application'=>'Application','confirmed'=>'Confirmed','enrolled'=>'Enrolled'];
      @endphp
      @foreach($byStatus as $status => $count)
      <div class="flex items-center gap-3">
        <span class="w-28 text-xs text-slate-600 text-right">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
        <div class="flex-1 bg-slate-100 rounded-full h-2">
          <div class="{{ $statusColors[$status] ?? 'bg-slate-400' }} h-2 rounded-full" style="width: {{ round(($count / $total) * 100) }}%"></div>
        </div>
        <span class="w-10 text-xs font-semibold text-slate-700 text-right">{{ $count }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card overflow-hidden">
      <h3 class="font-semibold text-slate-700 px-4 pt-4 pb-3">Class-wise Enquiry Count</h3>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="th">Class</th>
            <th class="th text-right">Enquiries</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($byClass->sortByDesc('total') as $row)
          <tr class="tr">
            <td class="td">{{ $row->class?->name ?? 'Unknown' }}</td>
            <td class="td text-right font-semibold text-indigo-600">{{ $row->total }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Counsellor-wise Conversion --}}
    @if($byCounsellor->isNotEmpty())
    <div class="card overflow-hidden">
      <h3 class="font-semibold text-slate-700 px-4 pt-4 pb-3">Counsellor-wise Conversion</h3>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="th">Counsellor</th>
            <th class="th text-right">Enquiries</th>
            <th class="th text-right">Converted</th>
            <th class="th text-right">Rate</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($byCounsellor->sortByDesc('total') as $row)
          @php $rate = $row->total > 0 ? round(($row->converted / $row->total) * 100) : 0; @endphp
          <tr class="tr">
            <td class="td">{{ $row->assignedTo?->name ?? 'Unassigned' }}</td>
            <td class="td text-right">{{ $row->total }}</td>
            <td class="td text-right text-green-600 font-semibold">{{ $row->converted }}</td>
            <td class="td text-right">
              <span class="px-2 py-0.5 rounded text-xs {{ $rate >= 50 ? 'bg-green-100 text-green-700' : ($rate >= 25 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                {{ $rate }}%
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

</div>
@push('scripts')
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const sourceRaw = @json($bySource);
const trendRaw  = @json($monthlyTrend);

const sourceLabels = Object.keys(sourceRaw).map(k => k ? k.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Unknown');
new Chart(document.getElementById('sourceChart'), {
  type: 'doughnut',
  data: { labels: sourceLabels, datasets: [{ data: Object.values(sourceRaw), backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#06b6d4'] }] },
  options: { plugins: { legend: { position: 'bottom' } }, cutout: '60%' }
});

const trendValues = Array.from({length: 12}, (_, i) => trendRaw[i + 1] ?? 0);
new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: { labels: months, datasets: [{ label: 'Enquiries', data: trendValues, backgroundColor: '#6366f1', borderRadius: 4 }] },
  options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
});
</script>
@endpush
@endsection
