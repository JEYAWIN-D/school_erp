@extends('layouts.app')
@section('title', 'Student Outpass')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Student Outpass</h1>
    <div class="flex gap-2">
      <a href="{{ route('gate.index') }}" class="btn-sm btn-secondary">← Gate</a>
      <a href="{{ route('gate.outpass.create') }}" class="btn-primary btn-sm">+ Issue Outpass</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <form method="GET" class="flex gap-3">
    <select name="status" class="select">
      <option value="">All Status</option>
      <option value="active" @selected(request('status')==='active')>Active</option>
      <option value="returned" @selected(request('status')==='returned')>Returned</option>
      <option value="overdue" @selected(request('status')==='overdue')>Overdue</option>
    </select>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Pass No.</th>
        <th class="th">Student</th>
        <th class="th">Reason</th>
        <th class="th">Out Time</th>
        <th class="th">Expected Return</th>
        <th class="th">Actual Return</th>
        <th class="th">Authorized By</th>
        <th class="th">Status</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($outpasses as $op)
        <tr class="tr">
          <td class="td font-mono font-semibold text-indigo-600">{{ $op->pass_number }}</td>
          <td class="td">
            <p class="font-medium">{{ $op->student?->first_name }} {{ $op->student?->last_name }}</p>
            <p class="text-xs text-slate-400">{{ $op->student?->admission_number }}</p>
          </td>
          <td class="td text-xs">{{ $op->reason }}</td>
          <td class="td text-xs">{{ $op->out_time?->format('d M, h:i A') }}</td>
          <td class="td text-xs">{{ $op->expected_return?->format('d M, h:i A') ?? '—' }}</td>
          <td class="td text-xs">{{ $op->actual_return?->format('d M, h:i A') ?? '—' }}</td>
          <td class="td text-xs">{{ $op->authorized_by }}</td>
          <td class="td">
            @if($op->status==='active') <span class="badge-blue">Active</span>
            @elseif($op->status==='returned') <span class="badge-green">Returned</span>
            @else <span class="badge-red">Overdue</span> @endif
          </td>
          <td class="td">
            @if($op->status !== 'returned')
            <form method="POST" action="{{ route('gate.outpass.return', $op->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn-xs btn-primary">Mark Returned</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="9">No outpasses found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $outpasses->withQueryString()->links() }}</div>
</div>
@endsection
