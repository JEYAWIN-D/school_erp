@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">

{{-- Header --}}
<div class="flex items-center justify-between flex-wrap gap-3">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Reports &amp; Analytics</h1>
    <p class="text-sm text-slate-500 mt-0.5">{{ $year?->name ?? 'All Years' }} — Last updated {{ now()->format('d M Y, h:i A') }}</p>
  </div>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('reports.fee') }}" class="btn btn-outline">Fee Report</a>
    <a href="{{ route('reports.attendance') }}" class="btn btn-outline">Attendance Report</a>
    <a href="{{ route('reports.fee.excel') }}" class="btn btn-secondary btn-sm">Fee Excel</a>
    <a href="{{ route('reports.attendance.excel') }}" class="btn btn-secondary btn-sm">Attendance Excel</a>
  </div>
</div>

{{-- KPI Row --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
  <div class="card text-center">
    <div class="text-3xl font-bold text-indigo-600">{{ number_format($totalStudents) }}</div>
    <div class="text-sm text-slate-500 mt-1">Active Students</div>
  </div>
  <div class="card text-center">
    <div class="text-3xl font-bold {{ $totalStaff > 0 ? 'text-emerald-600' : 'text-slate-300' }}">{{ $totalStaff > 0 ? number_format($totalStaff) : '—' }}</div>
    <div class="text-sm text-slate-500 mt-1">Active Staff</div>
    @if($totalStaff === 0)<div class="text-xs text-slate-400">Add staff in HR</div>@endif
  </div>
  <div class="card text-center">
    @if($todayTotal > 0)
      <div class="text-3xl font-bold {{ $todayPct >= 75 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $todayPct }}%</div>
      <div class="text-sm text-slate-500 mt-1">Today's Attendance</div>
      <div class="text-xs text-slate-400">{{ $todayPresent }}/{{ $todayTotal }}</div>
    @else
      <div class="text-3xl font-bold text-slate-300">—</div>
      <div class="text-sm text-slate-500 mt-1">Today's Attendance</div>
      <div class="text-xs text-slate-400">Not taken yet</div>
    @endif
  </div>
  <div class="card text-center">
    <div class="text-3xl font-bold text-amber-600">{{ $booksOverdue }}</div>
    <div class="text-sm text-slate-500 mt-1">Overdue Books</div>
    <div class="text-xs text-slate-400">{{ $booksIssued }} total issued</div>
  </div>
</div>

{{-- Fee Collection --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Fee Collection — {{ $year?->name ?? 'All Time' }}</h2>
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div class="bg-emerald-50 rounded-xl p-4 text-center">
        <div class="text-xl font-bold text-emerald-700">₹{{ number_format($totalCollected, 2) }}</div>
        <div class="text-xs text-emerald-600 mt-1">Collected</div>
      </div>
      <div class="bg-amber-50 rounded-xl p-4 text-center">
        <div class="text-xl font-bold text-amber-700">₹{{ number_format(max(0, $totalDemand - $totalCollected), 2) }}</div>
        <div class="text-xs text-amber-600 mt-1">Pending</div>
      </div>
    </div>
    @if($monthlyCollection->count())
    <canvas id="feeChart" height="120"></canvas>
    <div class="overflow-x-auto mt-4">
      <table class="table text-sm">
        <thead><tr><th>Month</th><th class="text-right">Collected (₹)</th></tr></thead>
        <tbody>
          @foreach($monthlyCollection as $m)
          <tr>
            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y') }}</td>
            <td class="text-right font-medium">{{ number_format($m->total, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
      <p class="text-slate-400 text-sm text-center py-4">No fee data yet.</p>
    @endif
  </div>

  {{-- Class Enrollment --}}
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Enrollment by Class</h2>
    @if($classSummary->count())
    <div class="space-y-2">
      @php $maxStudents = $classSummary->max('total') ?: 1; @endphp
      @foreach($classSummary as $row)
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-slate-700 font-medium">{{ $row->class_name }}</span>
          <span class="text-slate-500">{{ $row->total }}</span>
        </div>
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-2 bg-indigo-500 rounded-full" style="width: {{ round($row->total / $maxStudents * 100) }}%"></div>
        </div>
      </div>
      @endforeach
    </div>
    @else
      <p class="text-slate-400 text-sm text-center py-4">No enrollment data yet.</p>
    @endif
  </div>
</div>

{{-- Attendance Trend --}}
<div class="card">
  <h2 class="font-semibold text-slate-700 mb-4">Monthly Attendance Trend</h2>
  @if($attMonthly->count())
  <div class="overflow-x-auto">
    <table class="table text-sm">
      <thead>
        <tr><th>Month</th><th class="text-right">Present</th><th class="text-right">Absent</th><th class="text-right">Attendance %</th><th>Bar</th></tr>
      </thead>
      <tbody>
        @foreach($attMonthly as $row)
        <tr>
          <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $row['month'])->format('M Y') }}</td>
          <td class="text-right text-emerald-700 font-medium">{{ $row['present'] }}</td>
          <td class="text-right text-rose-600">{{ $row['absent'] }}</td>
          <td class="text-right font-semibold {{ $row['pct'] >= 75 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $row['pct'] }}%</td>
          <td class="w-32">
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-2 rounded-full {{ $row['pct'] >= 75 ? 'bg-emerald-500' : 'bg-rose-400' }}" style="width: {{ $row['pct'] }}%"></div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
    <p class="text-slate-400 text-sm text-center py-4">No attendance records yet.</p>
  @endif
</div>

{{-- Exam Results --}}
@if($latestExam)
<div class="card">
  <h2 class="font-semibold text-slate-700 mb-1">Exam Results — {{ $latestExam->name }}</h2>
  <p class="text-xs text-slate-400 mb-4">{{ $latestExam->start_date?->format('d M Y') }} – {{ $latestExam->end_date?->format('d M Y') }}</p>
  @if($examResults->count())
  <div class="overflow-x-auto">
    <table class="table text-sm">
      <thead><tr><th>Class</th><th class="text-right">Appeared</th><th class="text-right">Avg Marks</th></tr></thead>
      <tbody>
        @foreach($examResults as $r)
        <tr>
          <td class="font-medium">{{ $r->class_name }}</td>
          <td class="text-right">{{ $r->appeared }}</td>
          <td class="text-right font-semibold">{{ number_format($r->avg_marks, 1) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
    <p class="text-slate-400 text-sm text-center py-4">No marks entered yet for this exam.</p>
  @endif
</div>
@endif

</div>
@if($monthlyCollection->count())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const labels = @json($monthlyCollection->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y')));
  const data   = @json($monthlyCollection->pluck('total'));
  new Chart(document.getElementById('feeChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Fee Collected (₹)',
        data,
        backgroundColor: 'rgba(99,102,241,0.7)',
        borderColor: '#6366f1',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString('en-IN') } } }
    }
  });
})();
</script>
@endpush
@endif
@endsection
