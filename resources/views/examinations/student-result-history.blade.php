@extends('layouts.app')
@section('title', 'Student Result History')
@section('content')
<div class="space-y-6" x-data="{ search: '{{ request('search') }}' }">
    <h1 class="page-title">Student-wise Result History</h1>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-64">
                <label class="label">Search Student</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Name, admission number…" class="input w-full">
            </div>
            <button class="btn-primary">Search</button>
        </form>

        @if(isset($students) && $students->count() > 0 && !$student)
        <div class="mt-4 divide-y border rounded-lg overflow-hidden">
            @foreach($students as $s)
            <a href="{{ route('examinations.student-result-history', ['student_id' => $s->id]) }}"
               class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition">
                <div>
                    <div class="font-medium text-gray-800">{{ $s->full_name }}</div>
                    <div class="text-sm text-gray-500">Adm# {{ $s->admission_number }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    @if($student)
    <div class="card bg-blue-50 border-blue-200">
        <div class="font-semibold text-blue-900 text-lg">{{ $student->full_name }}</div>
        <div class="text-sm text-blue-700">Admission No: {{ $student->admission_number }}</div>
    </div>

    @if($history->count())
    @foreach($history as $item)
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
            <div>
                <span class="font-semibold text-gray-800">{{ $item['exam']->name }}</span>
                <span class="ml-2 text-sm text-gray-500">{{ $item['exam']->start_date?->format('M Y') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold">{{ $item['total'] }}/{{ $item['max'] }} ({{ $item['pct'] }}%)</span>
                <span class="badge-{{ $item['passed'] ? 'success' : 'danger' }}">
                    {{ $item['passed'] ? 'PASS' : 'FAIL' }}
                </span>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Subject</th>
                        <th class="th text-center">Max</th>
                        <th class="th text-center">Pass</th>
                        <th class="th text-center">Obtained</th>
                        <th class="th text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['marks'] as $m)
                    <tr class="tr">
                        <td class="td">{{ $m->examSchedule?->subject?->name }}</td>
                        <td class="td text-center">{{ $m->examSchedule?->max_marks }}</td>
                        <td class="td text-center">{{ $m->examSchedule?->pass_marks }}</td>
                        <td class="td text-center font-semibold">
                            @if($m->is_absent) <span class="badge-warning">ABS</span>
                            @else {{ $m->marks_obtained ?? '—' }}
                            @endif
                        </td>
                        <td class="td text-center">
                            @if(!$m->is_absent && $m->marks_obtained !== null)
                                @if($m->marks_obtained >= $m->examSchedule?->pass_marks)
                                    <span class="badge-success">P</span>
                                @else
                                    <span class="badge-danger">F</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
    @else
    <div class="alert-info">No exam results found for this student.</div>
    @endif
    @endif
</div>
@endsection
