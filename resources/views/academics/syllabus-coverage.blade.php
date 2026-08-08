@extends('layouts.app')
@section('title','Syllabus Coverage Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Syllabus Coverage Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class <span class="text-red-500">*</span></label>
        <select name="class_id" class="select w-36" required>
          <option value="">Select Class</option>
          @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject (all if blank)</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if($coverage->count())
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Subject</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Total Topics</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Completed</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Pending</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Coverage %</th>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">Progress</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($coverage as $row)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $row['subject']?->name }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $row['total'] }}</td>
          <td class="px-4 py-3 text-green-600 font-semibold">{{ $row['completed'] }}</td>
          <td class="px-4 py-3 text-amber-600">{{ $row['pending'] }}</td>
          <td class="px-4 py-3">
            <span class="font-semibold {{ $row['percentage'] >= 75 ? 'text-green-600' : ($row['percentage'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">
              {{ $row['percentage'] }}%
            </span>
          </td>
          <td class="px-4 py-3 w-40">
            <div class="w-full bg-slate-200 rounded h-2">
              <div class="h-2 rounded {{ $row['percentage'] >= 75 ? 'bg-green-500' : ($row['percentage'] >= 50 ? 'bg-amber-400' : 'bg-red-500') }}"
                style="width:{{ $row['percentage'] }}%"></div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @elseif(request('class_id'))
  <div class="card text-center py-8 text-slate-400">No syllabus configured for this class.</div>
  @else
  <div class="card text-center py-8 text-slate-400">Select a class to view coverage.</div>
  @endif
</div>
@endsection
