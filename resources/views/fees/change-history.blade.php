@extends('layouts.app')
@section('title', 'Fee Change History')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fee Change History</h1>
      <p class="page-subtitle">Audit log of all fee revisions, concessions, and overrides</p>
    </div>
    <a href="{{ route('fees.revision') }}" class="btn btn-secondary btn-sm">Fee Revision</a>
  </div>

  {{-- Filters --}}
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select text-sm">
          <option value="">All Classes</option>
          @foreach($classes as $class)
            <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Change Type</label>
        <select name="change_type" class="select text-sm">
          <option value="">All Types</option>
          <option value="revision" @selected(request('change_type') === 'revision')>Revision</option>
          <option value="concession" @selected(request('change_type') === 'concession')>Concession</option>
          <option value="override" @selected(request('change_type') === 'override')>Override</option>
          <option value="cancellation" @selected(request('change_type') === 'cancellation')>Cancellation</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="{{ route('fees.change-history') }}" class="btn btn-secondary btn-sm">Reset</a>
    </form>
  </div>

  <div class="card">
    @if($logs->isEmpty())
      <div class="text-center py-12 text-slate-400">No fee change records found.</div>
    @else
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Date & Time</th>
            <th class="th">Class</th>
            <th class="th">Fee Head</th>
            <th class="th text-center">Type</th>
            <th class="th text-right">Old Amount</th>
            <th class="th text-right">New Amount</th>
            <th class="th text-right">Change</th>
            <th class="th">Reason</th>
            <th class="th">Changed By</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
          @php
            $diff = ($log->new_amount ?? 0) - ($log->old_amount ?? 0);
            $isIncrease = $diff > 0;
          @endphp
          <tr class="tr">
            <td class="td text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</td>
            <td class="td font-medium">{{ $log->class?->name ?? '—' }}</td>
            <td class="td">{{ $log->feeHead?->name ?? '—' }}</td>
            <td class="td text-center">
              <span class="text-xs px-2 py-0.5 rounded-full font-medium
                {{ $log->change_type === 'revision' ? 'bg-blue-100 text-blue-700' :
                   ($log->change_type === 'concession' ? 'bg-green-100 text-green-700' :
                   ($log->change_type === 'cancellation' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                {{ ucfirst($log->change_type) }}
              </span>
            </td>
            <td class="td text-right text-slate-500">{{ $log->old_amount ? '₹'.number_format($log->old_amount, 2) : '—' }}</td>
            <td class="td text-right font-semibold">{{ $log->new_amount ? '₹'.number_format($log->new_amount, 2) : '—' }}</td>
            <td class="td text-right font-semibold {{ $isIncrease ? 'text-red-500' : 'text-green-600' }}">
              {{ $diff != 0 ? ($isIncrease ? '+' : '').number_format($diff, 2) : '—' }}
            </td>
            <td class="td text-xs text-slate-500 max-w-xs truncate">{{ $log->reason ?? '—' }}</td>
            <td class="td text-xs">{{ $log->changedBy?->name ?? 'System' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
    @endif
  </div>
</div>
@endsection
