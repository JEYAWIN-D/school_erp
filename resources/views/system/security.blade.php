@extends('layouts.app')
@section('title', 'Security Settings')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Security Settings</h1>
    <div class="flex gap-2">
      <a href="{{ route('system.audit-log') }}" class="btn-sm btn-secondary">Audit Log</a>
      <a href="{{ route('system.backup') }}" class="btn-sm btn-secondary">Backup</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-3xl font-bold text-rose-600">{{ $failedLogins }}</p>
      <p class="text-sm text-slate-500 mt-1">Failed Logins (24h)</p>
    </div>
    <div class="card text-center">
      <p class="text-3xl font-bold text-indigo-600">{{ $whitelist->count() }}</p>
      <p class="text-sm text-slate-500 mt-1">Whitelisted IPs</p>
    </div>
    <div class="card text-center">
      <p class="text-3xl font-bold text-emerald-600">{{ $recentAttempts->where('success',true)->count() }}</p>
      <p class="text-sm text-slate-500 mt-1">Successful Logins (50 recent)</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- IP Whitelist --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">IP Whitelist</h3>
      <form method="POST" action="{{ route('system.whitelist.add') }}" class="flex gap-2 mb-4">
        @csrf
        <input type="text" name="ip_address" class="input flex-1" placeholder="e.g. 192.168.1.1" required>
        <input type="text" name="description" class="input flex-1" placeholder="Description">
        <button type="submit" class="btn-primary btn-sm">Add</button>
      </form>
      @if($whitelist->isEmpty())
        <p class="text-slate-400 text-sm">No IPs whitelisted. All IPs can access.</p>
      @else
      <div class="space-y-2">
        @foreach($whitelist as $ip)
        <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
          <div>
            <p class="font-mono text-sm font-medium">{{ $ip->ip_address }}</p>
            <p class="text-xs text-slate-400">{{ $ip->description ?? '—' }}</p>
          </div>
          <form method="POST" action="{{ route('system.whitelist.remove', $ip->id) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn-xs btn-secondary text-rose-600">Remove</button>
          </form>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Recent Login Attempts --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Recent Login Attempts</h3>
      <div class="space-y-1 max-h-80 overflow-y-auto">
        @foreach($recentAttempts as $att)
        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100">
          <div>
            <p class="font-medium text-slate-700">{{ $att->email }}</p>
            <p class="text-slate-400">{{ \Carbon\Carbon::parse($att->attempted_at)->format('d M H:i') }} · {{ $att->ip_address }}</p>
          </div>
          @if($att->success) <span class="badge-green">OK</span>
          @else <span class="badge-red">Failed</span> @endif
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
