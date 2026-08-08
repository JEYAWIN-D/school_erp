@extends('layouts.app')
@section('title', 'Chronic Absentees')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Chronic Absentee Report</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} — Students below attendance threshold</p>
    </div>
  </div>

  <form method="GET" class="card-flat py-4">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-40">
          <option value="">All Classes</option>
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Below % Threshold</label>
        <input type="number" name="threshold" value="{{ request('threshold', 75) }}" min="1" max="100" class="input w-24">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
      @if(request()->hasAny(['class_id']))
        <a href="{{ route('attendance.chronic') }}" class="btn btn-ghost btn-sm">Clear</a>
      @endif
    </div>
  </form>

  @if($students->isNotEmpty())
    <div class="alert-warning">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span><strong>{{ $students->count() }}</strong> students with attendance below {{ $threshold }}%</span>
    </div>
  @endif

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Student</th>
          <th class="th">Class</th>
          <th class="th text-center">Present</th>
          <th class="th text-center">Total Days</th>
          <th class="th text-center">Attendance %</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $i => $row)
          @php $cls = $row->student?->currentEnrollment?->class ?? null; @endphp
          <tr class="tr">
            <td class="td text-slate-400 text-xs">{{ $i + 1 }}</td>
            <td class="td">
              <p class="font-medium text-slate-800">{{ $row->student?->full_name }}</p>
              <p class="text-xs text-slate-400 font-mono">{{ $row->student?->admission_number }}</p>
            </td>
            <td class="td">{{ $cls?->name ?? '—' }}</td>
            <td class="td text-center font-semibold text-green-600">{{ $row->present_days }}</td>
            <td class="td text-center">{{ $row->total_days }}</td>
            <td class="td text-center">
              <span class="font-bold text-{{ $row->percentage < 50 ? 'red' : 'amber' }}-600">{{ $row->percentage }}%</span>
              <div class="w-16 bg-slate-200 rounded-full h-1.5 mx-auto mt-1">
                <div class="h-1.5 rounded-full {{ $row->percentage < 50 ? 'bg-red-500' : 'bg-amber-500' }}" style="width:{{ $row->percentage }}%"></div>
              </div>
            </td>
            <td class="td text-right">
              @if($row->student)
                <a href="{{ route('students.show', $row->student->id) }}" class="btn-icon" title="View Profile">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="td text-center py-12 text-slate-400">
              @if(request('class_id') || request()->has('threshold'))
                No students found below {{ $threshold }}% attendance.
              @else
                Select a class and click Generate Report.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
