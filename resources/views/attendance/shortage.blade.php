@extends('layouts.app')
@section('title','Attendance Shortage')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Attendance Shortage (Below 75%)</h1>
  <form method="GET" class="card py-4"><div class="flex gap-3">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      @foreach($classes as $cls)<option value="{{ $cls->id }}" @selected(request('class_id')==$cls->id)>{{ $cls->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Check</button>
  </div></form>
  @if($students->count())
  <div class="table-wrap"><table class="w-full"><thead><tr><th class="th">Student</th><th class="th">Present Days</th><th class="th">%</th></tr></thead><tbody>
    @foreach($students as $r)<tr class="tr"><td class="td">{{ $r->student?->full_name }}</td><td class="td">{{ $r->present_days }}</td><td class="td text-red-600 font-semibold">{{ number_format($r->present_days / max(1,1) * 100,1) }}%</td></tr>@endforeach
  </tbody></table></div>
  @endif
</div>
@endsection
