@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Audit Trail</h1>
    <div class="flex gap-2">
      <a href="{{ route('system.roles') }}" class="btn-sm btn-secondary">Roles & Permissions</a>
      <a href="{{ route('system.security') }}" class="btn-sm btn-secondary">Security</a>
      <a href="{{ route('system.backup') }}" class="btn-sm btn-secondary">Backup</a>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">User</label>
      <select name="user_id" class="select">
        <option value="">All Users</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Action</label>
      <select name="action" class="select">
        <option value="">All Actions</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>
        @endforeach
      </select>
    </div>
    <div><label class="label">From</label><input type="date" name="from" value="{{ request('from') }}" class="input"></div>
    <div><label class="label">To</label><input type="date" name="to" value="{{ request('to') }}" class="input"></div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead><tr>
        <th class="th">Time</th>
        <th class="th">User</th>
        <th class="th">Action</th>
        <th class="th">Model</th>
        <th class="th">IP</th>
        <th class="th">Details</th>
      </tr></thead>
      <tbody>
        @forelse($logs as $log)
        <tr class="tr" x-data="{ open: false }">
          <td class="td text-xs text-slate-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
          <td class="td text-xs">{{ $log->user?->name ?? 'System' }}</td>
          <td class="td">
            @php $ac=['login'=>'badge-blue','logout'=>'badge-slate','created'=>'badge-green','updated'=>'badge-amber','deleted'=>'badge-red']; @endphp
            <span class="{{ $ac[$log->action] ?? 'badge-slate' }} text-xs">{{ $log->action }}</span>
          </td>
          <td class="td text-xs text-slate-500">
            @if($log->model_type)
              {{ class_basename($log->model_type) }} #{{ $log->model_id }}
            @else — @endif
          </td>
          <td class="td font-mono text-xs">{{ $log->ip_address ?? '—' }}</td>
          <td class="td">
            @if($log->old_values || $log->new_values)
            <button @click="open=!open" class="btn-xs btn-secondary">Changes</button>
            @endif
          </td>
        </tr>
        @if($log->old_values || $log->new_values)
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <div class="grid grid-cols-2 gap-4 text-xs">
              @if($log->old_values)
              <div>
                <p class="font-semibold text-slate-500 mb-1">Before:</p>
                <pre class="text-slate-600 whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
              </div>
              @endif
              @if($log->new_values)
              <div>
                <p class="font-semibold text-slate-500 mb-1">After:</p>
                <pre class="text-slate-600 whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
              </div>
              @endif
            </div>
          </td>
        </tr>
        @endif
        @empty
        <tr><td class="td text-center text-slate-400" colspan="6">No audit logs found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
