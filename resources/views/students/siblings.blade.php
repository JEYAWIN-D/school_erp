@extends('layouts.app')
@section('title', 'Sibling Group')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="{{ route('students.index') }}" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="{{ route('students.show', $student->id) }}" class="hover:text-slate-600">{{ $student->full_name }}</a>
    <span>/</span>
    <span class="text-slate-600">Siblings</span>
  </nav>
  <div class="flex items-center gap-4">
    <a href="{{ route('students.show', $student->id) }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Sibling Group</h1>
      <p class="page-subtitle">{{ $student->full_name }} &mdash; Group ID #{{ $student->sibling_group_id }}</p>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

  {{-- Link sibling form --}}
  <div class="card" x-data="{ open: false, query: '', results: [], loading: false, selected: null,
    async search() {
      if (this.query.length < 2) { this.results = []; return; }
      this.loading = true;
      const r = await fetch(`/admin/students/search-json?q=${encodeURIComponent(this.query)}&exclude={{ $student->id }}`);
      this.results = await r.json();
      this.loading = false;
    },
    select(s) { this.selected = s; this.query = s.admission_number + ' — ' + s.full_name; this.results = []; }
  }">
    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
      <h3 class="font-semibold text-slate-700">Link a Sibling</h3>
      <svg class="w-4 h-4 text-slate-400 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>
    <div x-show="open" x-transition class="mt-4 space-y-3">
      <div class="relative">
        <label class="label">Search Student by Name or Admission No.</label>
        <input type="text" x-model="query" @input.debounce.300ms="search()" class="input"
            placeholder="Type to search…">
        <div x-show="results.length > 0" class="absolute z-10 w-full bg-white border border-slate-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
          <template x-for="s in results" :key="s.id">
            <button type="button" @click="select(s)"
                class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm border-b border-slate-100 last:border-0">
              <span class="font-medium" x-text="s.full_name"></span>
              <span class="text-slate-400 ml-2" x-text="s.admission_number"></span>
            </button>
          </template>
        </div>
        <div x-show="loading" class="text-xs text-slate-400 mt-1">Searching…</div>
      </div>
      <form method="POST" action="{{ route('students.siblings.link', $student->id) }}" x-show="selected">
        @csrf
        <input type="hidden" name="sibling_id" :value="selected?.id">
        <p class="text-sm text-slate-600 mb-2">
          Link <strong x-text="selected?.full_name"></strong>
          <span class="text-slate-400" x-text="'(' + (selected?.admission_number ?? '') + ')'"></span>
          as sibling? This will also auto-apply any active sibling concession scheme.
        </p>
        <button type="submit" class="btn btn-primary btn-sm">Confirm Link</button>
      </form>
    </div>
  </div>

  @if(!$student->sibling_group_id)
  <div class="alert-warning">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>This student has not been assigned to a sibling group. Use the form above to link a sibling.</span>
  </div>
  @else

  {{-- Current student card --}}
  <div class="card border-l-4 border-blue-500">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
        <span class="text-blue-700 font-bold">{{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}</span>
      </div>
      <div class="flex-1">
        <p class="font-bold text-slate-800 text-lg">{{ $student->full_name }} <span class="text-sm font-normal text-blue-600">(Current)</span></p>
        <p class="text-sm text-slate-500">{{ $student->admission_number }} &middot; {{ $student->currentEnrollment?->class?->name }} {{ $student->currentEnrollment?->section?->name }}</p>
      </div>
      <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary btn-sm">View Profile</a>
    </div>
  </div>

  {{-- Siblings --}}
  @if($siblings->isEmpty())
    <div class="card text-center py-10 text-slate-400">
      <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <p class="font-medium text-slate-500">No other siblings found in this group.</p>
    </div>
  @else
    <div class="card overflow-hidden">
      <div class="px-4 pt-4 pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Siblings ({{ $siblings->count() }})</h3>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="th">Student</th>
            <th class="th">Adm. #</th>
            <th class="th">Class</th>
            <th class="th">Status</th>
            <th class="th text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($siblings as $sib)
          <tr class="tr">
            <td class="td">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-purple-700 text-xs font-bold">{{ strtoupper(substr($sib->first_name,0,1).substr($sib->last_name,0,1)) }}</span>
                </div>
                <div>
                  <p class="font-medium text-slate-800">{{ $sib->full_name }}</p>
                  <p class="text-xs text-slate-400">{{ $sib->gender ? ucfirst($sib->gender) : '' }}</p>
                </div>
              </div>
            </td>
            <td class="td font-mono text-xs text-blue-600">{{ $sib->admission_number }}</td>
            <td class="td">{{ $sib->currentEnrollment?->class?->name ?? '—' }}</td>
            <td class="td"><span class="{{ $sib->status === 'active' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($sib->status) }}</span></td>
            <td class="td text-right">
              <a href="{{ route('students.show', $sib->id) }}" class="btn-icon" title="View">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </a>
              <form method="POST" action="{{ route('students.siblings.unlink', [$student->id, $sib->id]) }}" class="inline"
                    onsubmit="return confirm('Remove {{ $sib->first_name }} from this sibling group?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon text-red-400 hover:text-red-600" title="Unlink">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  @endif
</div>
@endsection
