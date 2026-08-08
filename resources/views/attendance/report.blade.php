@extends('layouts.app')
@section('title','Attendance Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Attendance Report</h1>
  <form method="GET" class="card py-4"><div class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">Select Class</option>
        @foreach($classes as $cls)<option value="{{ $cls->id }}" @selected(request('class_id')==$cls->id)>{{ $cls->name }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="label">Month</label>
      <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="input w-44">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    @if($data->count() && request('class_id') && request('month'))
    <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn btn-secondary btn-sm">Export Excel</a>
    @endif
  </div></form>

  @if($data->count())
  @php
    [$year, $mon] = explode('-', request('month'));
    $daysInMonth  = \Carbon\Carbon::create($year, $mon, 1)->daysInMonth;
    $dates        = range(1, $daysInMonth);
  @endphp
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">
        {{ $classes->firstWhere('id', request('class_id'))?->name }} — {{ \Carbon\Carbon::create($year, $mon, 1)->format('F Y') }}
      </h3>
      <span class="text-xs text-slate-400">{{ $data->count() }} students</span>
    </div>
    <div class="overflow-x-auto">
      <table class="text-xs min-w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-3 py-2 font-medium text-slate-500 uppercase tracking-wide sticky left-0 bg-slate-50 z-10 min-w-[140px]">Student</th>
            @foreach($dates as $d)
            @php $weekday = \Carbon\Carbon::create($year, $mon, $d)->dayOfWeek; @endphp
            <th class="text-center px-1 py-2 font-medium {{ in_array($weekday,[0,6]) ? 'text-slate-300' : 'text-slate-500' }} w-6">{{ $d }}</th>
            @endforeach
            <th class="text-center px-2 py-2 font-medium text-green-600 uppercase tracking-wide">P</th>
            <th class="text-center px-2 py-2 font-medium text-red-500 uppercase tracking-wide">A</th>
            <th class="text-center px-2 py-2 font-medium text-amber-500 uppercase tracking-wide">L</th>
            <th class="text-center px-2 py-2 font-medium text-slate-500 uppercase tracking-wide">%</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($data as $studentId => $records)
          @php
            $student  = $records->first()?->student;
            $byDate   = $records->keyBy(fn($r) => \Carbon\Carbon::parse($r->date)->day);
            $present  = $records->whereIn('status', ['present', 'late'])->count();
            $absent   = $records->where('status', 'absent')->count();
            $leave    = $records->where('status', 'leave')->count();
            $total    = $present + $absent + $leave;
            $pct      = $total > 0 ? round($present / $total * 100, 1) : 0;
          @endphp
          <tr class="hover:bg-slate-50">
            <td class="px-3 py-2 sticky left-0 bg-white z-10">
              <p class="font-medium text-slate-800">{{ $student?->first_name }} {{ $student?->last_name }}</p>
              <p class="text-slate-400">{{ $student?->admission_no }}</p>
            </td>
            @foreach($dates as $d)
            @php
              $rec     = $byDate[$d] ?? null;
              $status  = $rec?->status;
              $cell    = match($status) {
                'present' => ['P', 'text-green-600 font-semibold'],
                'late'    => ['L8', 'text-amber-500'],
                'absent'  => ['A', 'text-red-500 font-semibold'],
                'leave'   => ['LV', 'text-blue-500'],
                default   => ['·', 'text-slate-200'],
              };
              $weekday = \Carbon\Carbon::create($year, $mon, $d)->dayOfWeek;
            @endphp
            <td class="text-center w-6 {{ in_array($weekday,[0,6]) ? 'bg-slate-50' : '' }}">
              <span class="{{ $cell[1] }}">{{ $cell[0] }}</span>
            </td>
            @endforeach
            <td class="text-center font-semibold text-green-600">{{ $present }}</td>
            <td class="text-center font-semibold text-red-500">{{ $absent }}</td>
            <td class="text-center text-blue-500">{{ $leave }}</td>
            <td class="text-center font-semibold {{ $pct < 75 ? 'text-red-600' : 'text-slate-700' }}">{{ $pct }}%</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('class_id') && request('month'))
  <div class="card text-center py-8 text-slate-400">No attendance records found for the selected class and month.</div>
  @endif
</div>
@endsection
