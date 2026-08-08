@extends('layouts.app')
@section('title','Mess Rebate Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Rebate for Absentees</h1>
      <p class="page-subtitle">Grant mess fee rebates to students who were absent for a period</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('hostel.mess-attendance-summary') }}" class="btn btn-secondary btn-sm">Attendance Summary</a>
      <a href="{{ route('hostel.index') }}" class="btn btn-secondary btn-sm">← Hostel</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Grant Rebate Form --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Grant New Rebate</h3>
      <form method="POST" action="{{ route('hostel.mess-rebates.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Student (Hostel Resident) <span class="text-red-500">*</span></label>
          <select name="allotment_id" class="select" required>
            <option value="">Select student</option>
            @foreach($allotments as $a)
            <option value="{{ $a->id }}">{{ $a->student?->full_name }} — {{ $a->room?->room_number }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Absent From <span class="text-red-500">*</span></label>
            <input type="date" name="from_date" class="input" required>
          </div>
          <div>
            <label class="label">Absent To <span class="text-red-500">*</span></label>
            <input type="date" name="to_date" class="input" required>
          </div>
        </div>
        <div>
          <label class="label">Rebate per Day (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="rebate_per_day" class="input" step="0.50" min="0" placeholder="e.g. 80" required>
        </div>
        <div>
          <label class="label">Reason / Remarks</label>
          <input type="text" name="reason" class="input" placeholder="e.g. Medical leave, home visit...">
        </div>
        <button type="submit" class="btn btn-primary w-full">Create Rebate</button>
      </form>
    </div>

    {{-- Rebates List --}}
    <div class="lg:col-span-2">
      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-700">Rebate Records</h3>
          <form method="GET" class="flex gap-2">
            <select name="status" class="select text-sm py-1 w-36" onchange="this.form.submit()">
              <option value="">All Status</option>
              <option value="pending" @selected(request('status')==='pending')>Pending</option>
              <option value="approved" @selected(request('status')==='approved')>Approved</option>
              <option value="applied" @selected(request('status')==='applied')>Applied</option>
            </select>
          </form>
        </div>

        @if($rebates->isEmpty())
          <p class="text-center py-8 text-slate-400">No rebate records yet.</p>
        @else
        <div class="table-wrap">
          <table class="w-full text-sm">
            <thead><tr>
              <th class="th">Student</th>
              <th class="th">Room</th>
              <th class="th">Period</th>
              <th class="th">Days</th>
              <th class="th">Per Day</th>
              <th class="th">Total Rebate</th>
              <th class="th">Reason</th>
              <th class="th">Status</th>
              <th class="th"></th>
            </tr></thead>
            <tbody>
              @foreach($rebates as $r)
              <tr class="tr">
                <td class="td font-medium">{{ $r->allotment?->student?->full_name ?? '—' }}</td>
                <td class="td text-xs">{{ $r->allotment?->room?->room_number ?? '—' }}</td>
                <td class="td text-xs text-slate-500">
                  {{ $r->from_date?->format('d M') }} – {{ $r->to_date?->format('d M Y') }}
                </td>
                <td class="td text-center font-semibold">{{ $r->days_absent }}</td>
                <td class="td text-right">₹{{ number_format($r->rebate_per_day, 0) }}</td>
                <td class="td text-right font-semibold text-green-700">₹{{ number_format($r->total_rebate, 0) }}</td>
                <td class="td text-xs text-slate-500">{{ $r->reason ?? '—' }}</td>
                <td class="td">
                  <span class="badge-{{ $r->status==='approved'?'green':($r->status==='applied'?'indigo':'amber') }} text-xs capitalize">
                    {{ $r->status }}
                  </span>
                </td>
                <td class="td">
                  <div class="flex gap-1">
                    @if($r->status === 'pending')
                    <form method="POST" action="{{ route('hostel.mess-rebates.approve', $r->id) }}">
                      @csrf
                      <button class="btn btn-secondary btn-xs text-green-600">Approve</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('hostel.mess-rebates.delete', $r->id) }}" onsubmit="return confirm('Delete this rebate?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-secondary btn-xs text-red-400">Del</button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($rebates->hasPages())<div class="p-4">{{ $rebates->links() }}</div>@endif
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
