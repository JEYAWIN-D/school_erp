@extends('layouts.app')
@section('title','Examinations')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Examinations</h1>
      <p class="page-subtitle">{{ $currentYear?->name }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('examinations.marks-progress') }}" class="btn btn-secondary btn-sm">Marks Progress</a>
      <a href="{{ route('examinations.tabulation') }}" class="btn btn-secondary btn-sm">Tabulation Sheet</a>
      <a href="{{ route('examinations.class-result') }}" class="btn btn-secondary btn-sm">Result Summary</a>
      <a href="{{ route('examinations.marks-import-template') }}" class="btn btn-secondary btn-sm">Import Template</a>
      <a href="{{ route('examinations.create') }}" class="btn btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Exam
      </a>
    </div>
  </div>

  {{-- KPI Stats --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['total'] }}</p>
      <p class="text-sm text-slate-500 mt-1">Total Exams</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-blue-600" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['upcoming'] }}</p>
      <p class="text-sm text-slate-500 mt-1">Upcoming</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-amber-500" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['ongoing'] }}</p>
      <p class="text-sm text-slate-500 mt-1">Ongoing</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['results'] }}</p>
      <p class="text-sm text-slate-500 mt-1">Results Published</p>
    </div>
  </div>

  {{-- Analysis Quick Links --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach([
      ['Result Summary',      'examinations.class-result',          'from-blue-500 to-indigo-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ['Failed Students',     'examinations.failed',                'from-red-500 to-rose-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
      ['Subject Performance', 'examinations.subject-performance',   'from-purple-500 to-indigo-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
      ['Student History',     'examinations.student-result-history','from-teal-500 to-cyan-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
      ['Marks Progress',      'examinations.marks-progress',        'from-green-500 to-emerald-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
    ] as [$label,$route,$color,$icon])
    <a href="{{ route($route) }}" class="card-flat flex items-center gap-3 py-4 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $color }} flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
      </div>
      <span class="font-semibold text-sm text-slate-700">{{ $label }}</span>
    </a>
    @endforeach
  </div>

  {{-- Upcoming Schedule (next 7 days) --}}
  @if($upcomingSchedules->count())
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">Upcoming Exams — Next 7 Days</h3>
      <span class="badge-blue">{{ $upcomingSchedules->count() }} schedule{{ $upcomingSchedules->count() > 1 ? 's' : '' }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Date</th>
            <th class="th">Exam</th>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Time</th>
          </tr>
        </thead>
        <tbody>
          @foreach($upcomingSchedules as $s)
          <tr class="tr">
            <td class="td">
              <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($s->date)->format('d M') }}</span>
              <span class="text-xs text-slate-400 ml-1">{{ \Carbon\Carbon::parse($s->date)->format('D') }}</span>
            </td>
            <td class="td text-slate-600">{{ $s->exam_name }}</td>
            <td class="td"><span class="badge-slate">{{ $s->class }}</span></td>
            <td class="td font-medium text-slate-800">{{ $s->subject }}</td>
            <td class="td text-xs text-slate-500">{{ $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '—' }} @if($s->end_time) – {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }} @endif</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- Exams List --}}
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">All Exams — {{ $currentYear?->name }}</h3>
    @if($exams->isEmpty())
      <div class="text-center py-12 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        No exams created yet. <a href="{{ route('examinations.create') }}" class="text-blue-600 hover:underline">Create the first exam</a>.
      </div>
    @else
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Exam Name</th>
            <th class="th">Type</th>
            <th class="th">Period</th>
            <th class="th">Pass %</th>
            <th class="th">Status</th>
            <th class="th text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($exams as $exam)
          @php
            $started = \Carbon\Carbon::parse($exam->start_date)->isPast();
            $ended   = \Carbon\Carbon::parse($exam->end_date)->isPast();
            $state   = $exam->result_published ? 'results' : ($ended ? 'ended' : ($started ? 'ongoing' : 'upcoming'));
          @endphp
          <tr class="tr">
            <td class="td">
              <p class="font-semibold text-slate-800">{{ $exam->name }}</p>
              @if($exam->term_label)
              <p class="text-xs text-slate-400">{{ $exam->term_label }}</p>
              @endif
            </td>
            <td class="td">
              <span class="badge-slate capitalize">{{ str_replace('_',' ',$exam->type) }}</span>
              @if($exam->is_external)
              <span class="badge-amber text-xs ml-1">External</span>
              @endif
            </td>
            <td class="td text-sm">
              <p>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</p>
              <p class="text-xs text-slate-400">to {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</p>
            </td>
            <td class="td text-sm font-mono">{{ $exam->passing_percentage ? $exam->passing_percentage.'%' : '—' }}</td>
            <td class="td">
              @if($state === 'results')
                <span class="badge-green">Results Out</span>
              @elseif($state === 'ongoing')
                <span class="badge-amber">Ongoing</span>
              @elseif($state === 'ended')
                <span class="badge-slate">Ended</span>
              @elseif($state === 'upcoming')
                <span class="badge-blue">Upcoming</span>
              @elseif($exam->is_published)
                <span class="badge-slate">Published</span>
              @else
                <span class="badge-amber text-xs font-semibold">Draft</span>
              @endif
            </td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-1">
                <a href="{{ route('examinations.marks', $exam->id) }}" class="btn-icon" title="Enter Marks">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <a href="{{ route('examinations.results', $exam->id) }}" class="btn-icon" title="Results">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <a href="{{ route('examinations.hall-tickets', $exam->id) }}" class="btn-icon text-amber-500 hover:text-amber-700 hover:bg-amber-50" title="Hall Tickets">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </a>
                <a href="{{ route('examinations.edit', $exam->id) }}" class="btn-icon" title="Edit Exam">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form method="POST" action="{{ route('examinations.destroy', $exam->id) }}" onsubmit="return confirm('Delete exam {{ addslashes($exam->name) }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-icon text-red-400 hover:text-red-600 hover:bg-red-50" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

</div>
@endsection
