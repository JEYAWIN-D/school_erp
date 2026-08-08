@extends('layouts.app')
@section('title', 'Notice Read Receipts')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Read Receipts</h1>
      <p class="page-subtitle text-slate-500 text-sm">{{ $notice->title }}</p>
    </div>
    <a href="{{ route('academics.notices') }}" class="btn-sm btn-secondary">← Back to Notices</a>
  </div>

  {{-- Summary --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <div class="text-2xl font-bold text-green-700">{{ $reads->total() }}</div>
      <div class="text-xs text-slate-500 mt-1">Users Read</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-slate-700">{{ $totalUsers }}</div>
      <div class="text-xs text-slate-500 mt-1">Total Active Users</div>
    </div>
    <div class="card text-center">
      <div class="text-2xl font-bold text-indigo-700">
        {{ $totalUsers > 0 ? round($reads->total() / $totalUsers * 100) : 0 }}%
      </div>
      <div class="text-xs text-slate-500 mt-1">Read Rate</div>
    </div>
  </div>

  {{-- Notice details --}}
  <div class="card bg-slate-50 border border-slate-200">
    <div class="flex items-center gap-3 flex-wrap">
      <span class="badge-blue">{{ ucfirst($notice->notice_type) }}</span>
      <span class="badge-indigo capitalize">{{ $notice->target_audience }}</span>
      <span class="text-xs text-slate-500">Published: {{ \Carbon\Carbon::parse($notice->publish_date)->format('d M Y') }}</span>
      <span class="text-xs text-slate-400">By: {{ $notice->createdBy?->name }}</span>
    </div>
  </div>

  {{-- Read receipts table --}}
  <div class="card">
    <h2 class="text-sm font-semibold text-slate-700 mb-3">Who Read This Notice</h2>
    @if($reads->isEmpty())
      <p class="text-slate-400 text-sm">No read receipts yet.</p>
    @else
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">User</th>
            <th class="th">Role</th>
            <th class="th">Read At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reads as $i => $receipt)
          <tr class="tr">
            <td class="td text-slate-400 text-xs">{{ $reads->firstItem() + $i }}</td>
            <td class="td">
              <div class="font-medium">{{ $receipt->user?->name ?? '—' }}</div>
              <div class="text-xs text-slate-400">{{ $receipt->user?->email }}</div>
            </td>
            <td class="td">
              @foreach($receipt->user?->getRoleNames() ?? [] as $role)
                <span class="badge-indigo text-xs">{{ $role }}</span>
              @endforeach
            </td>
            <td class="td text-xs text-slate-500">{{ \Carbon\Carbon::parse($receipt->read_at)->format('d M Y, h:i A') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($reads->hasPages())<div class="mt-4">{{ $reads->links() }}</div>@endif
    @endif
  </div>
</div>
@endsection
