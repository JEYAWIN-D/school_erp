@extends('layouts.app')
@section('title', 'Homework Submissions')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Homework Submissions</h1>
      <p class="text-slate-500 text-sm mt-0.5">
        {{ $homework->subject?->name }} — {{ $homework->class?->name }}{{ $homework->section ? ' / '.$homework->section?->name : '' }}
        <span class="ml-2 text-slate-400">Due: {{ $homework->due_date?->format('d M Y') }}</span>
      </p>
      @if($homework->title)<p class="font-medium text-slate-700 text-sm mt-0.5">{{ $homework->title }}</p>@endif
    </div>
    <a href="{{ route('academics.homework') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  @php
    $total     = $enrollments->count();
    $submitted = collect($submissions)->where('status','submitted')->count() + collect($submissions)->where('status','late')->count() + collect($submissions)->where('status','evaluated')->count();
    $pct       = $total > 0 ? round($submitted/$total*100) : 0;
  @endphp
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-slate-700">Submission Rate</span>
      <span class="font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ $submitted }}/{{ $total }} ({{ $pct }}%)</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-2">
      <div class="h-2 rounded-full bg-green-500" style="width:{{ $pct }}%"></div>
    </div>
  </div>

  <form method="POST" action="{{ route('academics.homework.submissions.save', $homework->id) }}">
    @csrf
    <div class="card overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100"><tr>
          <th class="th">Student</th>
          <th class="th">Adm. No.</th>
          <th class="th">Status</th>
          <th class="th">Submitted On</th>
          <th class="th">Score @if($homework->max_score)(/ {{ $homework->max_score }})@endif</th>
          <th class="th">Feedback</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($enrollments as $enrollment)
          @php $s = $submissions[$enrollment->student_id] ?? null; @endphp
          <tr class="hover:bg-slate-50">
            <td class="td font-medium">{{ $enrollment->student?->full_name }}</td>
            <td class="td text-xs font-mono text-slate-500">{{ $enrollment->student?->admission_number }}</td>
            <td class="td">
              <select name="submissions[{{ $enrollment->student_id }}][status]" class="select text-xs py-1 w-32">
                <option value="not_submitted" @selected(($s?->status ?? 'not_submitted')==='not_submitted')>Not Submitted</option>
                <option value="submitted" @selected($s?->status==='submitted')>Submitted</option>
                <option value="late" @selected($s?->status==='late')>Late</option>
                <option value="evaluated" @selected($s?->status==='evaluated')>Evaluated</option>
              </select>
            </td>
            <td class="td">
              <input type="date" name="submissions[{{ $enrollment->student_id }}][submitted_at]"
                value="{{ $s?->submitted_at?->format('Y-m-d') }}"
                class="input text-xs py-1 w-36">
            </td>
            <td class="td">
              <input type="number" name="submissions[{{ $enrollment->student_id }}][score]"
                value="{{ $s?->score }}" step="0.5" min="0"
                @if($homework->max_score) max="{{ $homework->max_score }}" @endif
                class="input text-xs py-1 w-20">
            </td>
            <td class="td">
              <input type="text" name="submissions[{{ $enrollment->student_id }}][teacher_feedback]"
                value="{{ $s?->teacher_feedback }}" placeholder="Remarks"
                class="input text-xs py-1 w-full min-w-32">
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <button type="submit" class="btn btn-primary">Save Submission Status</button>
    </div>
  </form>
</div>
@endsection
