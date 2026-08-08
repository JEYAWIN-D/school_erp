@extends('layouts.app')
@section('title', 'Subject-wise Attendance Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Subject-wise Attendance</h1>
            <p class="page-subtitle">Period-wise attendance per subject per student</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Student</label>
                <select name="student_id" class="select">
                    <option value="">Select Student</option>
                    @foreach($students as $en)
                        <option value="{{ $en->student_id }}" @selected(request('student_id') == $en->student_id)>
                            {{ $en->student?->first_name }} {{ $en->student?->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" class="input" value="{{ request('from_date') }}">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" class="input" value="{{ request('to_date') }}">
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>
    </div>

    @if($student)
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">
            {{ $student->first_name }} {{ $student->last_name }}
            <span class="text-slate-400 font-normal text-sm ml-2">{{ $student->admission_number }}</span>
        </h3>
        @if($report->isEmpty())
            <p class="text-slate-400 text-sm text-center py-8">No period-wise attendance data found for the selected range.</p>
        @else
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Subject</th>
                    <th class="th text-center">Total Periods</th>
                    <th class="th text-center">Present</th>
                    <th class="th text-center">Absent</th>
                    <th class="th text-center">Attendance %</th>
                    <th class="th text-center">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($report as $row)
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['subject'] }}</td>
                        <td class="td text-center">{{ $row['total'] }}</td>
                        <td class="td text-center text-green-600">{{ $row['present'] }}</td>
                        <td class="td text-center text-red-500">{{ $row['absent'] }}</td>
                        <td class="td text-center font-semibold {{ $row['percentage'] < 75 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $row['percentage'] }}%
                        </td>
                        <td class="td text-center">
                            @if($row['percentage'] >= 75)
                                <span class="badge badge-success">OK</span>
                            @elseif($row['percentage'] >= 60)
                                <span class="badge badge-warning">Low</span>
                            @else
                                <span class="badge badge-danger">Shortage</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @elseif(request('class_id'))
        <div class="card text-center py-8 text-slate-400">Select a student to view subject-wise report.</div>
    @endif
</div>
@endsection
