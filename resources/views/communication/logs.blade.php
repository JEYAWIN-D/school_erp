@extends('layouts.app')
@section('title', 'Communication Logs')
@section('content')
<div class="space-y-6" x-data="{ showBody: false, bodyHtml: '', bodySubject: '' }">

  {{-- Body modal --}}
  <div x-show="showBody" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showBody=false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col" @click.stop>
      <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-semibold text-slate-700 truncate" x-text="bodySubject"></h3>
        <button @click="showBody=false" class="text-slate-400 hover:text-slate-600">&times;</button>
      </div>
      <div class="overflow-y-auto p-4 text-sm text-slate-700 prose max-w-none" x-html="bodyHtml"></div>
    </div>
  </div>
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Communication Logs</h1>
    <a href="{{ route('communication.index') }}" class="btn-secondary btn-sm">← Back</a>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Type</label>
      <select name="type" class="select">
        <option value="">All Types</option>
        <option value="email"           {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
        <option value="internal_notice" {{ request('type') === 'internal_notice' ? 'selected' : '' }}>Internal Notice</option>
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select">
        <option value="">All Statuses</option>
        <option value="sent"    {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
        <option value="failed"  {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>Sending</option>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('communication.logs') }}" class="btn-secondary btn-sm">Reset</a>
  </form>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Type</th>
          <th class="th">Subject</th>
          <th class="th">Audience</th>
          <th class="th text-center">Sent</th>
          <th class="th text-center">Failed</th>
          <th class="th">Sent By</th>
          <th class="th">Status</th>
          <th class="th"></th>
        </tr></thead>
        <tbody>
          @forelse($logs as $log)
          <tr class="tr">
            <td class="td text-xs text-slate-500">{{ \Carbon\Carbon::parse($log->sent_at ?? $log->created_at)->format('d M Y H:i') }}</td>
            <td class="td"><span class="badge-{{ $log->type === 'email' ? 'blue' : 'purple' }}">{{ ucfirst(str_replace('_', ' ', $log->type)) }}</span></td>
            <td class="td font-medium text-slate-700">{{ $log->subject }}</td>
            <td class="td text-xs text-slate-500">
              {{ ucfirst(str_replace('_', ' ', $log->audience_meta['type'] ?? '—')) }}
              @if(isset($log->audience_meta['class_id'])) (Class {{ $log->audience_meta['class_id'] }}) @endif
            </td>
            <td class="td text-center text-green-600 font-medium">{{ $log->sent_count }}</td>
            <td class="td text-center {{ $log->failed_count > 0 ? 'text-red-600 font-medium' : 'text-slate-400' }}">{{ $log->failed_count }}</td>
            <td class="td text-xs">{{ $log->sender?->name ?? '—' }}</td>
            <td class="td">
              <span class="badge-{{ match($log->status) { 'sent' => 'green', 'failed' => 'red', default => 'blue' } }}">
                {{ ucfirst($log->status) }}
              </span>
            </td>
            <td class="td">
              @if($log->body)
              <button type="button" class="btn-xs btn-secondary"
                @click="bodyHtml = {{ Js::from($log->body) }}; bodySubject = {{ Js::from($log->subject) }}; showBody = true">
                View
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="td text-center text-slate-400 py-6">No communication logs found</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
  </div>
</div>
@endsection
