@extends('layouts.app')
@section('title', 'Attendance Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Monthly Attendance Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      @if($classId)
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
      @endif
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Class <span class="text-red-500">*</span></label>
      <select name="class_id" class="select" required>
        <option value="">Select Class</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected($classId==$cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Month</label>
      <select name="month" class="select">
        @foreach(range(1,12) as $m)
          <option value="{{ $m }}" @selected($month==$m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Year</label>
      <select name="year" class="select">
        @foreach(range(now()->year-2, now()->year+1) as $y)
          <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Load</button>
  </form>

  @if($classId && $students->isNotEmpty())
  @php
    $statusMap = ['present'=>'P','absent'=>'A','late'=>'L','half_day'=>'H/D','holiday'=>'','sunday'=>''];
    $colorMap  = ['present'=>'text-emerald-600','absent'=>'text-rose-600','late'=>'text-amber-600','half_day'=>'text-blue-600'];
  @endphp
  <div class="overflow-x-auto">
    <div class="card p-0 min-w-max">
      <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
        <h3 class="font-semibold text-slate-700">
          {{ $students->first()->class_name ?? '' }}
          — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </h3>
        <div class="flex gap-3 text-xs text-slate-500">
          <span><span class="font-bold text-emerald-600">P</span> Present</span>
          <span><span class="font-bold text-rose-600">A</span> Absent</span>
          <span><span class="font-bold text-amber-600">L</span> Late</span>
          <span><span class="font-bold text-blue-600">H/D</span> Half Day</span>
        </div>
      </div>
      <table class="w-full text-xs">
        <thead>
          <tr class="bg-slate-50">
            <th class="px-3 py-2 text-left font-semibold text-slate-600 whitespace-nowrap sticky left-0 bg-slate-50 z-10">#</th>
            <th class="px-3 py-2 text-left font-semibold text-slate-600 whitespace-nowrap sticky left-8 bg-slate-50 z-10">Name</th>
            @foreach($days as $day)
            <th class="px-1.5 py-2 text-center font-semibold text-slate-500 w-8">{{ $day }}</th>
            @endforeach
            <th class="px-3 py-2 text-center font-semibold text-slate-600">P</th>
            <th class="px-3 py-2 text-center font-semibold text-slate-600">A</th>
            <th class="px-3 py-2 text-center font-semibold text-slate-600">%</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $i => $s)
          @php
            $presentCount = 0; $absentCount = 0;
          @endphp
          <tr class="tr">
            <td class="px-3 py-1.5 text-slate-400 sticky left-0 bg-white z-10">{{ $i+1 }}</td>
            <td class="px-3 py-1.5 font-medium text-slate-700 whitespace-nowrap sticky left-8 bg-white z-10">
              {{ $s->first_name }} {{ $s->last_name }}
            </td>
            @foreach($days as $day)
            @php
              $key = $s->id . '_' . $day;
              $rec = $records->get($key)?->first();
              $status = $rec?->status ?? '';
              $label  = $statusMap[$status] ?? '';
              $color  = $colorMap[$status] ?? 'text-slate-300';
              if ($status === 'present' || $status === 'half_day') $presentCount++;
              elseif ($status === 'absent') $absentCount++;
            @endphp
            <td class="px-1.5 py-1.5 text-center font-semibold {{ $color }}">{{ $label ?: '·' }}</td>
            @endforeach
            <td class="px-3 py-1.5 text-center font-semibold text-emerald-600">{{ $presentCount }}</td>
            <td class="px-3 py-1.5 text-center font-semibold text-rose-600">{{ $absentCount }}</td>
            <td class="px-3 py-1.5 text-center font-semibold text-slate-600">
              @php $total = $presentCount + $absentCount; @endphp
              {{ $total > 0 ? round($presentCount/$total*100) : '—' }}{{ $total > 0 ? '%' : '' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif($classId)
    <div class="card text-center py-10 text-slate-400">No students enrolled in the selected class.</div>
  @else
    <div class="card text-center py-10 text-slate-400">Select a class and month to view the attendance register.</div>
  @endif
</div>
@endsection
