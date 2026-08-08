@extends('layouts.app')
@section('title', 'Database Backup')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Database Backup</h1>
    <div class="flex gap-2">
      <a href="{{ route('system.audit-log') }}" class="btn-sm btn-secondary">Audit Log</a>
      <a href="{{ route('system.security') }}" class="btn-sm btn-secondary">Security</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert-danger">{{ session('error') }}</div> @endif

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3">Create New Backup</h3>
    <p class="text-sm text-slate-500 mb-4">
      Triggers a MySQL dump of the database. Requires <code>mysqldump</code> to be available on the server PATH.
    </p>
    <form method="POST" action="{{ route('system.backup.trigger') }}">
      @csrf
      <button type="submit" class="btn-primary" onclick="return confirm('Create database backup now?')">
        💾 Backup Database Now
      </button>
    </form>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Existing Backup Files</h3>
    @if($files->isEmpty())
      <p class="text-slate-400 text-sm">No backup files found in <code>storage/app/backups/</code>.</p>
    @else
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr><th class="th">Filename</th><th class="th">Size</th><th class="th">Date</th></tr></thead>
        <tbody>
          @foreach($files as $f)
          <tr class="tr">
            <td class="td font-mono text-xs">{{ $f['name'] }}</td>
            <td class="td text-xs">{{ $f['size'] }}</td>
            <td class="td text-xs">{{ $f['date'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection
