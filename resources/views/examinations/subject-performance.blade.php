@extends('layouts.app')
@section('title', 'Subject-wise Performance Report')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Subject-wise Performance Report</h1>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">Exam</label>
                <select name="exam_id" class="select" required>
                    <option value="">— Select Exam —</option>
                    @foreach($exams as $e)
                        <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" required>
                    <option value="">— Select Class —</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary">Generate Report</button>
        </form>
    </div>

    @if(!empty($data))
    <div class="card p-0 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">{{ $exam->name ?? '' }} — {{ $class->name ?? '' }}</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Subject</th>
                        <th class="th text-center">Max Marks</th>
                        <th class="th text-center">Pass Marks</th>
                        <th class="th text-center">Students Appeared</th>
                        <th class="th text-center">Passed</th>
                        <th class="th text-center">Failed</th>
                        <th class="th text-center">Pass %</th>
                        <th class="th text-center">Avg Marks</th>
                        <th class="th text-center">Highest</th>
                        <th class="th text-center">Lowest</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['subject'] }}</td>
                        <td class="td text-center">{{ $row['max_marks'] }}</td>
                        <td class="td text-center">{{ $row['pass_marks'] }}</td>
                        <td class="td text-center">{{ $row['total'] }}</td>
                        <td class="td text-center text-green-600 font-semibold">{{ $row['passed'] }}</td>
                        <td class="td text-center text-red-600 font-semibold">{{ $row['failed'] }}</td>
                        <td class="td text-center">
                            <span class="inline-flex items-center gap-1">
                                <span class="font-semibold {{ $row['pass_pct'] >= 75 ? 'text-green-600' : ($row['pass_pct'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $row['pass_pct'] }}%
                                </span>
                            </span>
                        </td>
                        <td class="td text-center">{{ $row['avg'] }}</td>
                        <td class="td text-center text-green-700">{{ $row['highest'] }}</td>
                        <td class="td text-center text-red-700">{{ $row['lowest'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(request('exam_id'))
    <div class="alert-info">No data found for the selected exam and class.</div>
    @endif
</div>
@endsection
