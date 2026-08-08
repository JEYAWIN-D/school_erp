@extends('layouts.app')
@section('title','Hall Tickets')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Hall Tickets — {{ $exam->name }}</h1>
    <p class="text-sm text-slate-500">{{ $exam->start_date?->format('d M Y') }} to {{ $exam->end_date?->format('d M Y') }}</p>
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
    <button type="submit" class="btn btn-secondary btn-sm">Load</button>
  </div></form>

  @if($enrollments->count())
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <span class="font-semibold text-slate-700">{{ $enrollments->count() }} students</span>
      <a href="{{ route('examinations.hall-tickets.bulk', $exam->id) }}?{{ http_build_query(request()->only('class_id','section_id')) }}"
         class="btn btn-primary btn-sm">
        ↓ Download All (ZIP)
      </a>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['#','Student','Admission No','Class','Section','Hall Ticket'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($enrollments as $i=>$enr)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $i+1 }}</td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $enr->student?->full_name }}</td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $enr->student?->admission_number }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $enr->class?->name }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $enr->section?->name ?? '—' }}</td>
          <td class="px-4 py-3">
            <a href="{{ route('examinations.hall-ticket.pdf', $enr->id) }}?exam_id={{ $exam->id }}" target="_blank" class="btn btn-secondary btn-sm text-xs">Download PDF</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">Select class and section to load students.</div>
  @endif
</div>
@endsection
