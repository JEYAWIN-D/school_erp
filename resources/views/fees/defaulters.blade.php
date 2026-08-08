@extends('layouts.app')
@section('title','Fee Defaulters')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fee Defaulters</h1>
      <p class="page-subtitle">Students with no fee payment in {{ $currentYear?->name }}</p>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select text-sm w-32">
          <option value="">All Classes</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Portal Status</label>
        <select name="blocked" class="select text-sm w-36">
          <option value="">All</option>
          <option value="1" @selected(request('blocked') === '1')>Blocked</option>
          <option value="0" @selected(request('blocked') === '0')>Not Blocked</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="{{ route('fees.defaulters') }}" class="btn btn-secondary btn-sm text-slate-500">Reset</a>
    </div>
  </form>

  {{-- Stats --}}
  @php
    $total   = $defaulters instanceof \Illuminate\Pagination\LengthAwarePaginator ? $defaulters->total() : $defaulters->count();
    $blocked = $defaulters->where('portal_blocked', true)->count();
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-red-600">{{ $total }}</p>
      <p class="text-xs text-slate-500 mt-0.5">Total Defaulters</p>
    </div>
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-orange-600">{{ $blocked }}</p>
      <p class="text-xs text-slate-500 mt-0.5">Portal Blocked</p>
    </div>
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-slate-600">{{ $total - $blocked }}</p>
      <p class="text-xs text-slate-500 mt-0.5">Portal Active</p>
    </div>
  </div>

  <div class="card overflow-x-auto" x-data="{ blockId: null, blockName: '' }">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Student</th>
          <th class="th">Adm #</th>
          <th class="th">Class</th>
          <th class="th">Parent Mobile</th>
          <th class="th text-center">Portal</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($defaulters as $s)
        <tr class="tr {{ $s->portal_blocked ? 'bg-red-50/30' : '' }}">
          <td class="td font-medium text-slate-800">
            {{ $s->full_name }}
            @if($s->portal_blocked)
              <p class="text-xs text-red-500 mt-0.5">{{ $s->portal_block_reason }}</p>
            @endif
          </td>
          <td class="td font-mono text-xs">{{ $s->admission_number }}</td>
          <td class="td">{{ $s->currentEnrollment?->class?->name ?? '—' }}</td>
          <td class="td font-mono text-sm">{{ $s->father_mobile ?? $s->mobile ?? '—' }}</td>
          <td class="td text-center">
            @if($s->portal_blocked)
              <span class="badge-red">Blocked</span>
              @if($s->portal_blocked_at)
                <p class="text-xs text-slate-400 mt-0.5">{{ $s->portal_blocked_at->format('d M Y') }}</p>
              @endif
            @else
              <span class="badge-green">Active</span>
            @endif
          </td>
          <td class="td">
            @if($s->portal_blocked)
              <form method="POST" action="{{ route('fees.defaulters.unblock', $s->id) }}"
                    onsubmit="return confirm('Restore portal access for {{ $s->full_name }}?')">
                @csrf
                <button type="submit" class="btn-xs bg-green-50 text-green-700 hover:bg-green-100 rounded px-2 py-0.5 text-xs">
                  Unblock
                </button>
              </form>
            @else
              <button type="button"
                      @click="blockId = {{ $s->id }}; blockName = '{{ addslashes($s->full_name) }}'"
                      class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 rounded px-2 py-0.5 text-xs">
                Block Portal
              </button>
            @endif
          </td>
        </tr>
        @empty
        <tr class="tr">
          <td colspan="6" class="td text-center py-10 text-slate-400">No defaulters found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    @if($defaulters instanceof \Illuminate\Pagination\LengthAwarePaginator && $defaulters->hasPages())
      <div class="p-4 text-sm text-slate-500">{{ $defaulters->links() }}</div>
    @endif

    {{-- Block confirmation modal --}}
    <div x-show="blockId !== null" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         style="display:none">
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm" @click.away="blockId = null">
        <h3 class="font-semibold text-slate-700 mb-1">Block Portal Access</h3>
        <p class="text-sm text-slate-500 mb-3">Block student portal for <span class="font-medium text-slate-800" x-text="blockName"></span>?</p>
        <form method="POST" :action="'/fees/defaulters/' + blockId + '/block-portal'" class="space-y-3">
          @csrf
          <div>
            <label class="label text-xs">Reason (shown to student)</label>
            <input type="text" name="reason" class="input text-sm"
                   placeholder="e.g. Fee dues pending since Term 1"
                   value="Long-term fee defaulter – portal access restricted">
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white btn-sm flex-1">Block Access</button>
            <button type="button" @click="blockId = null" class="btn btn-secondary btn-sm">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Info note --}}
  <div class="card-flat p-4 text-xs text-slate-500">
    <strong class="text-slate-700">How portal blocking works:</strong>
    Blocked students cannot log in to the student/parent portal until access is restored.
    Check the portal login middleware to enforce <code>portal_blocked</code> flag.
    You can also automate blocking by running a scheduled command that queries students with overdue fees beyond a threshold.
  </div>

</div>
@endsection
