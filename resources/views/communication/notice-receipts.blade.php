@extends('layouts.app')
@section('title', 'Notice Read Receipts')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Read Receipts</h1>
    <a href="{{ route('communication.staff-notices') }}" class="btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-2">{{ $notice->title }}</h3>
    <p class="text-sm text-slate-500 mb-4">{{ $reads->count() }} staff have read this notice</p>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr><th class="th">Staff Name</th><th class="th">Read At</th></tr></thead>
        <tbody>
          @forelse($reads as $r)
          <tr class="tr">
            <td class="td font-medium text-slate-700">{{ $r->user?->name ?? '—' }}</td>
            <td class="td text-slate-500">{{ \Carbon\Carbon::parse($r->read_at)->format('d M Y H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="2" class="td text-center text-slate-400 py-6">No read receipts yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
