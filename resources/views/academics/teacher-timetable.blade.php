@extends('layouts.app')
@section('title', 'Teacher-wise Timetable')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Teacher Timetable</h1>
            <p class="page-subtitle">View period schedule for individual teachers</p>
        </div>
        <a href="{{ route('academics.timetable') }}" class="btn btn-secondary">Class Timetable</a>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Select Teacher</label>
                <select name="teacher_id" class="select" required>
                    <option value="">Choose Teacher</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(request('teacher_id') == $t->id)>
                            {{ $t->first_name }} {{ $t->last_name }} ({{ $t->employee_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Timetable</button>
        </form>
    </div>

    @if($teacher)
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">
            {{ $teacher->first_name }} {{ $teacher->last_name }} — Weekly Schedule
        </h3>
        @if($timetable->isEmpty())
            <p class="text-slate-400 text-sm text-center py-6">No timetable entries found for this teacher.</p>
        @else
        @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
        <div class="space-y-4">
            @foreach($days as $dayNum => $dayName)
            @php $periods = $timetable[$dayNum + 1] ?? collect(); @endphp
            @if($periods->isNotEmpty())
            <div>
                <h4 class="text-sm font-semibold text-indigo-600 mb-2">{{ $dayName }}</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($periods as $p)
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-3 py-2 text-sm">
                        <p class="font-medium text-slate-800">{{ $p->subject?->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">Period {{ $p->period_number }}</p>
                        <p class="text-xs text-indigo-600">
                            {{ $p->class?->name ?? '' }} {{ $p->section?->name ?? '' }}
                        </p>
                        @if($p->start_time && $p->end_time)
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($p->end_time)->format('h:i A') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Workload Summary --}}
        <div class="mt-6 pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                <strong>Total periods/week:</strong> {{ $timetable->flatten()->count() }}
            </p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
