@extends('layouts.app')
@section('title', 'Marks Entry Progress')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Marks Entry Progress</h1>
            <p class="page-subtitle">Track % of marks entered per subject per class</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Exam</label>
                <select name="exam_id" class="select" required>
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Progress</button>
        </form>
    </div>

    @if(request('exam_id'))
    @if($data->isEmpty())
        <div class="card text-center py-8 text-slate-400">No exam schedules found for this exam.</div>
    @else
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Class</th>
                    <th class="th">Subject</th>
                    <th class="th">Exam Date</th>
                    <th class="th text-center">Students</th>
                    <th class="th text-center">Marks Entered</th>
                    <th class="th" style="min-width:200px">Progress</th>
                </tr></thead>
                <tbody>
                    @foreach($data as $row)
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['class'] }}</td>
                        <td class="td">{{ $row['subject'] }}</td>
                        <td class="td text-slate-500">{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d M Y') : '—' }}</td>
                        <td class="td text-center">{{ $row['total'] }}</td>
                        <td class="td text-center">{{ $row['entered'] }}</td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $row['pct'] >= 100 ? 'bg-green-500' : ($row['pct'] >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                         style="width: {{ $row['pct'] }}%"></div>
                                </div>
                                <span class="text-sm font-semibold {{ $row['pct'] >= 100 ? 'text-green-600' : ($row['pct'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ $row['pct'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
