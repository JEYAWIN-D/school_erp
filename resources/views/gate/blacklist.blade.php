@extends('layouts.app')
@section('title', 'Visitor Blacklist')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Visitor Blacklist</h1>
    <a href="{{ route('gate.index') }}" class="btn-sm btn-secondary">← Gate</a>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add form --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add to Blacklist</h3>
      <form method="POST" action="{{ route('gate.blacklist.store') }}" class="space-y-3">
        @csrf
        <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
        <div><label class="label">Phone</label><input type="tel" name="phone" class="input"></div>
        <div><label class="label">ID Number</label><input type="text" name="id_number" class="input"></div>
        <div><label class="label">Reason <span class="text-red-500">*</span></label><textarea name="reason" rows="2" class="input" required></textarea></div>
        <button type="submit" class="btn-primary w-full">Add to Blacklist</button>
      </form>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2">
      <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" class="input max-w-xs" placeholder="Search name, phone, or ID…">
        <button type="submit" class="btn-primary btn-sm">Search</button>
      </form>

      <div class="table-wrap">
        <table class="w-full text-sm">
          <thead><tr>
            <th class="th">Name</th>
            <th class="th">Phone</th>
            <th class="th">ID No.</th>
            <th class="th">Reason</th>
            <th class="th">Added By</th>
            <th class="th">Actions</th>
          </tr></thead>
          <tbody>
            @forelse($list as $b)
            <tr class="tr {{ !$b->is_active ? 'opacity-50' : '' }}">
              <td class="td font-medium">{{ $b->name }}</td>
              <td class="td">{{ $b->phone ?? '—' }}</td>
              <td class="td text-xs">{{ $b->id_number ?? '—' }}</td>
              <td class="td text-xs text-slate-500">{{ Str::limit($b->reason, 60) }}</td>
              <td class="td text-xs">{{ $b->addedBy?->name ?? '—' }}</td>
              <td class="td">
                @if($b->is_active)
                <form method="POST" action="{{ route('gate.blacklist.remove', $b->id) }}"
                      onsubmit="return confirm('Remove {{ addslashes($b->name) }} from blacklist?')">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-xs btn-secondary text-rose-600">Remove</button>
                </form>
                @else <span class="badge-slate text-xs">Removed</span> @endif
              </td>
            </tr>
            @empty
            <tr><td class="td text-center text-slate-400" colspan="6">No blacklist entries.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $list->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
