@extends('layouts.app')
@section('title','Staff Attendance')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Attendance</h1>
    <a href="{{ route('attendance.staff.register') }}" class="btn btn-secondary btn-sm">Monthly Register</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('attendance.staff.save') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label class="label">Department</label>
          <select name="department_id" class="select">
            <option value="">All Departments</option>
            @foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="date" class="input" required value="{{ old('date',today()->toDateString()) }}">
        </div>
        <div class="flex items-end">
          <button type="submit" name="action" value="load" class="btn btn-secondary btn-sm">Load Staff</button>
        </div>
      </div>
      @if(isset($employees) && $employees->count())
      <div class="overflow-x-auto border border-slate-100 rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b"><tr>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Employee</th>
            <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">Dept</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium text-center">Status</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">In Time</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">Out Time</th>
            <th class="px-4 py-3 text-slate-500 text-xs uppercase font-medium">Remarks</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($employees as $emp)
            @php $existing = $attendances[$emp->id] ?? null; @endphp
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-2.5">
                <p class="font-medium text-slate-800 text-sm">{{ $emp->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $emp->employee_id }}</p>
                <input type="hidden" name="attendance[{{ $emp->id }}][employee_id]" value="{{ $emp->id }}">
              </td>
              <td class="px-4 py-2.5 text-xs text-slate-500">{{ $emp->department?->name }}</td>
              <td class="px-4 py-2.5">
                <select name="attendance[{{ $emp->id }}][status]" class="text-xs border border-slate-200 rounded px-2 py-1 w-24 mx-auto block">
                  @foreach(['present'=>'Present','absent'=>'Absent','half_day'=>'Half Day','late'=>'Late','holiday'=>'Holiday','leave'=>'Leave'] as $v=>$l)
                  <option value="{{ $v }}" @selected(($existing?->status??'present')===$v)>{{ $l }}</option>
                  @endforeach
                </select>
              </td>
              <td class="px-3 py-2.5"><input type="time" name="attendance[{{ $emp->id }}][in_time]" value="{{ $existing?->in_time }}" class="input text-xs w-28"></td>
              <td class="px-3 py-2.5"><input type="time" name="attendance[{{ $emp->id }}][out_time]" value="{{ $existing?->out_time }}" class="input text-xs w-28"></td>
              <td class="px-3 py-2.5"><input type="text" name="attendance[{{ $emp->id }}][remarks]" value="{{ $existing?->remarks }}" class="input text-xs" placeholder="Optional"></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="flex gap-3">
        <button type="submit" name="action" value="save" class="btn btn-primary">Save Attendance</button>
        <button type="button" onclick="markAll('present')" class="btn btn-secondary btn-sm">Mark All Present</button>
      </div>
      @endif
    </form>
  </div>
</div>
<script>
function markAll(val){document.querySelectorAll('select[name*="[status]"]').forEach(s=>s.value=val);}
</script>
@endsection
