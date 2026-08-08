@extends('layouts.app')
@section('title', 'Hall Ticket Blocks')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Hall Ticket Blocks</h1>
  <p class="text-slate-500 text-sm">Check which students should be blocked from receiving hall tickets due to fee dues or attendance shortage.</p>

  <form method="GET" class="card">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="lg:col-span-2">
        <label class="label">Select Exam *</label>
        <select name="exam_id" required class="select w-full">
          <option value="">— Select Exam —</option>
          @foreach($exams as $exam)
            <option value="{{ $exam->id }}" @selected(request('exam_id')==$exam->id)>
              {{ $exam->name }} ({{ $exam->academicYear?->name }})
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Block Criteria</label>
        <div class="space-y-2 mt-1">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="block_fee_defaulters" value="1" @checked(request('block_fee_defaulters', '1')=='1')> Fee Defaulters
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="block_attendance_shortage" value="1" @checked(request('block_attendance_shortage', '1')=='1')> Attendance Shortage
          </label>
        </div>
      </div>
      <div class="flex items-end">
        <button type="submit" class="btn btn-primary btn-sm w-full">Check Blocks</button>
      </div>
    </div>
  </form>

  @if(request('exam_id'))
    @if($blocked->count())
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-800">Blocked Students <span class="badge-red ml-2">{{ $blocked->count() }}</span></h2>
        <p class="text-xs text-slate-500">These students will NOT receive hall tickets unless admin overrides.</p>
      </div>
      <div class="table-wrap">
        <table class="min-w-full text-sm">
          <thead><tr>
            <th class="th">Student</th>
            <th class="th">Adm. No.</th>
            <th class="th">Class</th>
            <th class="th">Section</th>
            <th class="th">Block Reason(s)</th>
          </tr></thead>
          <tbody>
            @foreach($blocked as $e)
            <tr class="tr">
              <td class="td font-medium">{{ $e->student?->full_name }}</td>
              <td class="td font-mono text-xs">{{ $e->student?->admission_number }}</td>
              <td class="td">{{ $e->class?->name }}</td>
              <td class="td">{{ $e->section?->name }}</td>
              <td class="td">
                @foreach($e->block_reasons as $reason)
                  <span class="badge-red text-xs mr-1">{{ $reason }}</span>
                @endforeach
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @else
    <div class="card text-center py-10">
      <div class="text-4xl mb-2">✓</div>
      <p class="text-slate-600 font-medium">No students are blocked for this exam.</p>
      <p class="text-slate-400 text-sm mt-1">All students meet the fee and attendance criteria.</p>
    </div>
    @endif
  @endif
</div>
@endsection
