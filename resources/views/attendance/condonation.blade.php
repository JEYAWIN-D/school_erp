@extends('layouts.admin')
@section('title', 'Attendance Condonation')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Attendance Condonation</h1>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Grant Condonation Form --}}
        <div class="lg:col-span-1">
            <div class="card">
                <h2 class="font-semibold text-slate-700 mb-4">Grant Condonation</h2>
                <form method="POST" action="{{ route('attendance.condonation.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">Class</label>
                            <select name="class_id" class="select" onchange="this.form.submit()">
                                <option value="">Select Class</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($students->count())
                        <div>
                            <label class="label">Student</label>
                            <select name="student_id" class="select" required>
                                <option value="">Select Student</option>
                                @foreach($students as $enrollment)
                                    <option value="{{ $enrollment->student?->id }}">{{ $enrollment->student?->full_name }} ({{ $enrollment->student?->admission_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div>
                            <label class="label">Days to Condone</label>
                            <input name="days_condoned" type="number" min="1" max="365" class="input" required>
                        </div>
                        <div>
                            <label class="label">Reason / Justification</label>
                            <textarea name="reason" rows="3" class="input" required placeholder="Medical grounds, principal's discretion..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Grant Condonation</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent condonations --}}
        <div class="lg:col-span-2">
            <div class="card p-0 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <p class="font-semibold text-slate-700">Recent Condonations — {{ $currentYear?->name }}</p>
                </div>
                @if($recentCondonations->count())
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th class="th">Student</th>
                            <th class="th text-center">Days</th>
                            <th class="th">Reason</th>
                            <th class="th">Condoned By</th>
                            <th class="th">Date</th>
                        </tr></thead>
                        <tbody>
                            @foreach($recentCondonations as $c)
                            <tr class="tr">
                                <td class="td">
                                    <div class="font-medium text-slate-800">{{ $c->student?->full_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $c->student?->admission_number }}</div>
                                </td>
                                <td class="td text-center">
                                    <span class="badge-blue font-semibold">{{ $c->days_condoned }}</span>
                                </td>
                                <td class="td text-sm text-slate-600">{{ Str::limit($c->reason, 60) }}</td>
                                <td class="td text-sm">{{ $c->condonedBy?->name }}</td>
                                <td class="td text-sm">{{ $c->condoned_on?->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-slate-400">No condonations recorded yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
