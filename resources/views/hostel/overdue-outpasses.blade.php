@extends('layouts.app')
@section('title','Overdue Gate Passes')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Late Return Alerts</h1>
      <p class="page-subtitle">Students with approved gate passes who have not yet returned (past expected return time)</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('hostel.outpass.workflow') }}" class="btn btn-secondary btn-sm">All Gate Passes</a>
      <a href="{{ route('hostel.index') }}" class="btn btn-secondary btn-sm">← Hostel</a>
    </div>
  </div>

  @if($overdue->isEmpty())
    <div class="card text-center py-16">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="text-slate-600 font-medium">All students have returned on time.</p>
      <p class="text-slate-400 text-sm mt-1">No overdue gate passes at this time.</p>
    </div>
  @else
    <div class="alert-red">
      <strong>{{ $overdue->count() }} student{{ $overdue->count() > 1 ? 's' : '' }}</strong> with overdue gate passes — immediate action required.
    </div>

    <div class="card overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Room</th>
          <th class="th">Hostel</th>
          <th class="th">Was Due Back</th>
          <th class="th">Overdue By</th>
          <th class="th">Destination</th>
          <th class="th">Purpose</th>
          <th class="th">Actions</th>
        </tr></thead>
        <tbody>
          @foreach($overdue as $op)
          @php $overdueHours = now()->diffInHours(\Carbon\Carbon::parse($op->to_datetime)); @endphp
          <tr class="tr {{ $overdueHours >= 24 ? 'bg-red-50' : '' }}">
            <td class="td">
              <p class="font-semibold text-slate-800">{{ $op->student?->full_name ?? '—' }}</p>
              <p class="text-xs text-slate-400">{{ $op->student?->admission_number }}</p>
            </td>
            <td class="td text-sm">{{ $op->allotment?->room?->room_number ?? '—' }}</td>
            <td class="td text-sm">{{ $op->allotment?->room?->hostel?->name ?? '—' }}</td>
            <td class="td">
              <p class="font-medium text-red-600">{{ \Carbon\Carbon::parse($op->to_datetime)->format('d M Y') }}</p>
              <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($op->to_datetime)->format('h:i A') }}</p>
            </td>
            <td class="td">
              @if($overdueHours >= 48)
                <span class="badge-red text-xs">{{ floor($overdueHours/24) }}d {{ $overdueHours % 24 }}h</span>
              @elseif($overdueHours >= 24)
                <span class="badge-red text-xs">{{ $overdueHours }}h</span>
              @else
                <span class="badge-amber text-xs">{{ $overdueHours }}h</span>
              @endif
            </td>
            <td class="td text-xs text-slate-500">{{ $op->destination ?? '—' }}</td>
            <td class="td text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($op->reason ?? '—', 30) }}</td>
            <td class="td">
              <form method="POST" action="{{ route('hostel.outpass.return', $op->id) }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary btn-xs text-green-600" onclick="return confirm('Mark student as returned?')">Mark Returned</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
