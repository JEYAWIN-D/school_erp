@extends('layouts.app')
@section('title', 'Teacher Workload Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Teacher Workload Report</h1>
            <p class="page-subtitle">Periods per week per teacher — max limit: {{ $maxPerWeek }} periods</p>
        </div>
        <a href="{{ route('academics.timetable') }}" class="btn btn-secondary">Class Timetable</a>
    </div>

    @php $overAllocated = $workload->where('over', true)->count(); @endphp
    @if($overAllocated > 0)
    <div class="alert-warning">
        {{ $overAllocated }} teacher(s) exceed the maximum workload of {{ $maxPerWeek }} periods/week.
    </div>
    @endif

    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Teacher</th>
                    <th class="th">Employee No.</th>
                    <th class="th text-center">Periods / Week</th>
                    <th class="th text-center">Max Allowed</th>
                    <th class="th" style="min-width:180px">Load</th>
                    <th class="th text-center">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($workload as $row)
                    <tr class="tr {{ $row['over'] ? 'bg-red-50' : '' }}">
                        <td class="td font-medium">{{ $row['teacher']->first_name }} {{ $row['teacher']->last_name }}</td>
                        <td class="td text-slate-500">{{ $row['teacher']->employee_number }}</td>
                        <td class="td text-center font-semibold {{ $row['over'] ? 'text-red-600' : '' }}">{{ $row['periods'] }}</td>
                        <td class="td text-center text-slate-400">{{ $row['max'] }}</td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $row['over'] ? 'bg-red-500' : ($row['pct'] >= 80 ? 'bg-amber-400' : 'bg-green-500') }}"
                                         style="width: {{ min(100, $row['pct']) }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500">{{ $row['pct'] }}%</span>
                            </div>
                        </td>
                        <td class="td text-center">
                            @if($row['over'])
                                <span class="badge badge-danger">Over-allocated</span>
                            @elseif($row['pct'] >= 80)
                                <span class="badge badge-warning">Near Limit</span>
                            @elseif($row['periods'] == 0)
                                <span class="badge badge-secondary">Unassigned</span>
                            @else
                                <span class="badge badge-success">OK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card text-sm text-slate-500">
        <p>Max periods/week is configured in <a href="{{ route('settings.index') }}" class="text-indigo-600 hover:underline">School Settings</a> → <code>teacher_max_periods_per_week</code> (default: 30).</p>
    </div>
</div>
@endsection
