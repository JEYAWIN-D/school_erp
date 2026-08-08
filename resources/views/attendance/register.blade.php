@extends('layouts.app')
@section('title','Attendance Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Attendance Register</h1>
    <form method="GET" action="{{ route('attendance.register.pdf') }}" class="flex gap-2">
      @foreach(request()->query() as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
      <button type="submit" class="btn btn-secondary btn-sm">Download PDF</button>
    </form>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      @foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>
  @if(isset($students) && $students->count())
  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b"><tr>
        <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium min-w-[160px]">Student</th>
        @foreach($dates as $d)
        <th class="px-2 py-3 text-center font-medium text-slate-500 w-8">
          {{ $d->format('d') }}<br><span class="text-slate-300">{{ $d->format('D')[0] }}</span>
        </th>
        @endforeach
        <th class="px-3 py-3 text-center font-medium text-slate-500">P</th>
        <th class="px-3 py-3 text-center font-medium text-slate-500">A</th>
        <th class="px-3 py-3 text-center font-medium text-slate-500">%</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($students as $s)
        @php $rec = $attendanceMap[$s->student_id] ?? []; @endphp
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800">{{ $s->student?->full_name }}</td>
          @foreach($dates as $d)
          @php $status = $rec[$d->toDateString()] ?? null; @endphp
          <td class="px-1 py-2 text-center">
            @if($status === 'present')<span class="text-green-600 font-bold">P</span>
            @elseif($status === 'absent')<span class="text-red-500 font-bold">A</span>
            @elseif($status === 'late')<span class="text-amber-500 font-bold">L</span>
            @elseif($status === 'holiday')<span class="text-slate-300">H</span>
            @else<span class="text-slate-200">—</span>@endif
          </td>
          @endforeach
          @php
            $p = collect($rec)->filter(fn($v)=>in_array($v,['present','late']))->count();
            $a = collect($rec)->filter(fn($v)=>$v==='absent')->count();
            $total = $p + $a;
            $pct = $total > 0 ? round($p/$total*100) : 0;
          @endphp
          <td class="px-3 py-2 text-center font-semibold text-green-700">{{ $p }}</td>
          <td class="px-3 py-2 text-center font-semibold text-red-500">{{ $a }}</td>
          <td class="px-3 py-2 text-center font-semibold {{ $pct < 75 ? 'text-red-600' : 'text-slate-700' }}">{{ $pct }}%</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">Select class, section and month to load register.</div>
  @endif
</div>
@endsection
