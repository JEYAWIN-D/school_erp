@extends('layouts.app')
@section('title','Report Cards — ' . $exam->name)
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="{{ route('examinations.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <h1 class="page-title">{{ $exam->name }} — Report Cards</h1>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">Select Class</option>
          @foreach(\App\Models\Classes::active()->get() as $cls)
          <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
      @if(request('class_id'))
      <a href="{{ route('examinations.report-cards.bulk-pdf', [$exam->id, 'class_id' => request('class_id'), 'section_id' => request('section_id')]) }}" class="btn btn-secondary">
        Download All PDFs
      </a>
      @endif
    </form>
  </div>

  {{-- Principal remarks for this exam --}}
  @php $principalRemarks = \App\Models\SchoolSetting::get('principal_remarks_exam_' . $exam->id, \App\Models\SchoolSetting::get('principal_remarks', '')); @endphp
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3">Principal's Remarks <span class="text-xs font-normal text-slate-400">(printed on every report card for this exam)</span></h3>
    <form method="POST" action="{{ route('examinations.principal-remarks', $exam->id) }}" class="flex gap-3 items-end">
      @csrf
      <div class="flex-1">
        <textarea name="principal_remarks" rows="2" class="input w-full" placeholder="e.g. Congratulations to all students for their hard work and dedication this term.">{{ $principalRemarks }}</textarea>
      </div>
      <button type="submit" class="btn btn-primary">Save Remarks</button>
    </form>
  </div>

  @if(isset($enrollments) && $enrollments->count())
  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Roll</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($enrollments as $enrollment)
          <tr class="tr">
            <td class="td font-medium">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
            <td class="td text-slate-500">{{ $enrollment->student->admission_no ?? $enrollment->student->admission_number ?? '—' }}</td>
            <td class="td">{{ $enrollment->roll_number }}</td>
            <td class="td">
              <a href="{{ route('examinations.report-card.pdf', [$enrollment->id, 'exam_id' => $exam->id]) }}" class="btn btn-secondary btn-sm" target="_blank">
                PDF
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">Select a class to view report cards.</div>
  @endif
</div>
@endsection
