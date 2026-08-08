@extends('layouts.app')
@section('title', 'Promotion Eligibility')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Promotion Eligibility</h1>
            <p class="page-subtitle">Identify pass/fail students from exam results</p>
        </div>
        <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Back to Exams</a>
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
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Check Eligibility</button>
        </form>
    </div>

    @if(request('exam_id') && request('class_id'))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Eligible --}}
        <div class="card">
            <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-success">Eligible for Promotion</span>
                <span class="text-sm text-slate-500">({{ $eligible->count() }} students)</span>
            </div>
            @if($eligible->isEmpty())
                <p class="text-slate-400 text-sm py-4 text-center">No students found.</p>
            @else
            <div class="table-wrap">
                <table class="w-full">
                    <thead><tr>
                        <th class="th">Student</th>
                        <th class="th">Admission No.</th>
                    </tr></thead>
                    <tbody>
                        @foreach($eligible as $student)
                        <tr class="tr">
                            <td class="td">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="td text-slate-500">{{ $student->admission_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('students.promotions', ['class_id' => request('class_id'), 'preselect_ids' => $eligible->pluck('id')->implode(',')]) }}"
                   class="btn btn-primary text-sm">
                    Promote These Students
                </a>
            </div>
            @endif
        </div>

        {{-- Not Eligible --}}
        <div class="card">
            <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-danger">Not Eligible (Failed)</span>
                <span class="text-sm text-slate-500">({{ $ineligible->count() }} students)</span>
            </div>
            @if($ineligible->isEmpty())
                <p class="text-slate-400 text-sm py-4 text-center">No failed students.</p>
            @else
            <div class="table-wrap">
                <table class="w-full">
                    <thead><tr>
                        <th class="th">Student</th>
                        <th class="th">Admission No.</th>
                    </tr></thead>
                    <tbody>
                        @foreach($ineligible as $student)
                        <tr class="tr">
                            <td class="td">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="td text-slate-500">{{ $student->admission_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Apply Merit Scholarships --}}
    @php $schemes = \App\Models\ScholarshipScheme::where('is_active',true)->where('criteria_type','merit')->whereNotNull('marks_threshold')->get(); @endphp
    @if($schemes->isNotEmpty())
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3">Merit Scholarship Auto-Apply</h3>
        <p class="text-sm text-slate-500 mb-4">
            Active schemes:
            @foreach($schemes as $s)
                <span class="badge badge-info">{{ $s->name }} ≥{{ $s->marks_threshold }}%</span>
            @endforeach
        </p>
        <form method="POST" action="{{ route('examinations.merit-scholarships') }}">
            @csrf
            <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
            <input type="hidden" name="class_id" value="{{ request('class_id') }}">
            <button type="submit" class="btn btn-primary">Apply Merit Scholarships to Eligible Students</button>
        </form>
    </div>
    @endif
    @endif
</div>
@endsection
