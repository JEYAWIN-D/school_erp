@extends('layouts.app')
@section('title','Period-wise Attendance')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Period-wise Attendance</h1>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      @foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}" class="input w-36">
    <button type="submit" class="btn btn-primary btn-sm">Load</button>
  </div></form>
  @if(isset($students) && $students->count())
  <form method="POST" action="{{ route('attendance.period.save') }}" class="card overflow-hidden">
    @csrf
    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
    <input type="hidden" name="section_id" value="{{ request('section_id') }}">
    <input type="hidden" name="date" value="{{ request('date') }}">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b"><tr>
          <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Student</th>
          @foreach(range(1,8) as $p)<th class="px-3 py-3 text-slate-500 text-xs font-medium text-center">P{{ $p }}</th>@endforeach
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($students as $s)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $s->student?->full_name }}</td>
            @foreach(range(1,8) as $p)
            <td class="px-2 py-2 text-center">
              <select name="periods[{{ $s->student_id }}][{{ $p }}]" class="text-xs border border-slate-200 rounded px-1 py-0.5 w-16">
                <option value="present">P</option>
                <option value="absent">A</option>
                <option value="late">L</option>
              </select>
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-100">
      <button type="submit" class="btn btn-primary">Save Period Attendance</button>
    </div>
  </form>
  @else
  <div class="card text-center py-12 text-slate-400">Select class, section and date to load students.</div>
  @endif
</div>
@endsection
