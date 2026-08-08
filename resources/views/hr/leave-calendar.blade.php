@extends('layouts.app')
@section('title','Leave Calendar')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Staff Leave Calendar</h1>
    <form method="GET" class="flex gap-2">
      <select name="department_id" class="select w-40">
        <option value="">All Depts</option>
        @foreach($departments as $d)<option value="{{ $d->id }}" @selected(request('department_id')==$d->id)>{{ $d->name }}</option>@endforeach
      </select>
      <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="input w-36">
      <button type="submit" class="btn btn-primary btn-sm">Load</button>
    </form>
  </div>

  <div class="card overflow-x-auto">
    <table class="text-xs min-w-max">
      <thead class="bg-slate-50 border-b">
        <tr>
          <th class="sticky left-0 bg-slate-50 text-left px-4 py-3 text-slate-500 uppercase font-medium min-w-[160px]">Employee</th>
          @foreach($dates as $d)
          <th class="px-1.5 py-3 text-center font-medium text-slate-500 w-8 {{ $d->isWeekend() ? 'bg-slate-100' : '' }}">
            {{ $d->format('d') }}<br><span class="{{ $d->isWeekend() ? 'text-red-300' : 'text-slate-300' }}">{{ $d->format('D')[0] }}</span>
          </th>
          @endforeach
          <th class="px-3 py-3 text-center font-medium text-slate-500">Leave</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($employees as $emp)
        @php $empLeaves = $leaveMap[$emp->id] ?? []; @endphp
        <tr class="hover:bg-slate-50">
          <td class="sticky left-0 bg-white px-4 py-2 font-medium text-slate-800">
            {{ $emp->full_name }}<br>
            <span class="text-xs text-slate-400">{{ $emp->department?->name }}</span>
          </td>
          @foreach($dates as $d)
          @php
            $status = $empLeaves[$d->toDateString()] ?? null;
            $isWeekend = $d->isWeekend();
          @endphp
          <td class="px-1 py-2 text-center {{ $isWeekend ? 'bg-slate-50' : '' }}">
            @if($isWeekend)<span class="text-slate-200">W</span>
            @elseif($status === 'approved')<span class="text-red-500 font-bold" title="{{ $status }}">L</span>
            @elseif($status === 'half_day')<span class="text-amber-500 font-bold">H</span>
            @elseif($status === 'holiday')<span class="text-blue-300">H</span>
            @else<span class="text-slate-100">·</span>@endif
          </td>
          @endforeach
          @php $leaveCount = collect($empLeaves)->filter(fn($v)=>$v==='approved')->count(); @endphp
          <td class="px-3 py-2 text-center font-semibold {{ $leaveCount > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $leaveCount ?: '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="flex gap-4 text-xs text-slate-500">
    <span><span class="font-bold text-red-500">L</span> = Leave</span>
    <span><span class="font-bold text-amber-500">H</span> = Half Day</span>
    <span><span class="font-bold text-blue-300">H</span> = Holiday</span>
    <span><span class="text-slate-200 font-bold">W</span> = Weekend</span>
  </div>
</div>
@endsection
