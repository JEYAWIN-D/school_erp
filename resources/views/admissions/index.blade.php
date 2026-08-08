@extends('layouts.app')

@section('title', 'Admission Enquiries')

@section('content')
<div class="space-y-5" x-data="bulkSelect()">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="page-title">Admission Enquiries</h1>
      <p class="page-subtitle">Manage and track all admission enquiries</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('admissions.form-builder') }}" class="btn btn-secondary btn-sm">Form Builder</a>
      <a href="{{ route('admissions.applications') }}" class="btn btn-secondary btn-sm">Applications</a>
      <a href="{{ route('admissions.application-form') }}" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Application Form
      </a>
      <a href="{{ route('admissions.export', request()->query()) }}" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
      </a>
      <a href="{{ route('admissions.bulk-import') }}" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
        Import
      </a>
      <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Enquiry
      </a>
    </div>
  </div>

  {{-- Stats Pipeline --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
    @foreach([
      ['label' => 'Total',     'value' => $stats['total'],     'badge' => 'badge-slate',  'href' => route('admissions.index')],
      ['label' => 'New',       'value' => $stats['new'],       'badge' => 'badge-blue',   'href' => route('admissions.index', ['status'=>'new'])],
      ['label' => 'Follow Up', 'value' => $stats['follow_up'], 'badge' => 'badge-amber',  'href' => route('admissions.index', ['status'=>'follow_up'])],
      ['label' => 'Converted', 'value' => $stats['converted'], 'badge' => 'badge-green',  'href' => route('admissions.index', ['status'=>'converted'])],
      ['label' => 'Lost',      'value' => $stats['lost'],      'badge' => 'badge-rose',   'href' => route('admissions.index', ['status'=>'lost'])],
    ] as $stat)
      <a href="{{ $stat['href'] }}"
         class="card-sm hover:shadow-sm hover:border-slate-300 transition-all text-center group">
        <p class="text-2xl font-bold text-slate-900 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
          {{ $stat['value'] }}
        </p>
        <span class="{{ $stat['badge'] }} mt-2">{{ $stat['label'] }}</span>
      </a>
    @endforeach
  </div>

  {{-- Filters --}}
  <form method="GET" class="filter-bar" id="filter-form">
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Name, mobile, enquiry no…" class="input w-52">
    </div>
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All Classes</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select w-32">
        <option value="">All Status</option>
        <option value="new"         @selected(request('status') === 'new')>New</option>
        <option value="follow_up"   @selected(request('status') === 'follow_up')>Follow Up</option>
        <option value="converted"   @selected(request('status') === 'converted')>Converted</option>
        <option value="lost"        @selected(request('status') === 'lost')>Lost</option>
        <option value="application" @selected(request('status') === 'application')>Application</option>
      </select>
    </div>
    <div>
      <label class="label">From</label>
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="input w-36">
    </div>
    <div>
      <label class="label">To</label>
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="input w-36">
    </div>
    <div class="flex items-end gap-2 pb-0">
      <button type="submit" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
        Filter
      </button>
      @if(request()->hasAny(['search', 'class_id', 'status', 'date_from', 'date_to']))
        <a href="{{ route('admissions.index') }}" class="btn btn-ghost btn-sm text-slate-500">Clear</a>
      @endif
    </div>
  </form>

  {{-- Bulk Actions --}}
  <div x-show="selected.length > 0"
       x-transition:enter="transition duration-150"
       x-transition:enter-start="opacity-0 -translate-y-1"
       x-transition:enter-end="opacity-100 translate-y-0"
       class="card-flat border-indigo-200 bg-indigo-50 flex flex-wrap items-center gap-3">
    <div class="flex items-center gap-2">
      <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center" x-text="selected.length"></span>
      <span class="text-sm font-semibold text-indigo-800" x-text="selected.length + ' enqu' + (selected.length === 1 ? 'iry' : 'iries') + ' selected'"></span>
    </div>
    <form method="POST" action="{{ route('admissions.bulk-status') }}" x-ref="bulkForm" @submit.prevent="submitBulk($refs.bulkForm)">
      @csrf
      <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
      </template>
      <div class="flex items-center gap-2">
        <select name="status" class="select-sm w-36">
          <option value="new">Mark: New</option>
          <option value="follow_up">Mark: Follow Up</option>
          <option value="converted">Mark: Converted</option>
          <option value="lost">Mark: Lost</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
      </div>
    </form>
    <button @click="selected = []" class="btn btn-ghost btn-sm text-indigo-700 ml-auto">Clear Selection</button>
  </div>

  {{-- Table --}}
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th w-10">
            <input type="checkbox" class="rounded border-slate-300 w-3.5 h-3.5"
                   @change="toggleAll($event)"
                   :checked="selected.length === {{ $enquiries->count() }} && {{ $enquiries->count() }} > 0">
          </th>
          <th class="th">Enquiry #</th>
          <th class="th">Student</th>
          <th class="th">Class</th>
          <th class="th">Parent / Mobile</th>
          <th class="th">Source</th>
          <th class="th">Status</th>
          <th class="th">Follow Up</th>
          <th class="th">Date</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $enq)
          <tr class="tr" :class="selected.includes({{ $enq->id }}) ? 'bg-indigo-50/50' : ''">
            <td class="td">
              <input type="checkbox" class="rounded border-slate-300 w-3.5 h-3.5" :value="{{ $enq->id }}" x-model="selected">
            </td>
            <td class="td">
              <span class="font-mono text-xs font-semibold text-indigo-600">{{ $enq->enquiry_number }}</span>
            </td>
            <td class="td">
              <p class="font-semibold text-slate-800 text-sm leading-tight">{{ $enq->student_name }}</p>
              @if($enq->gender)
                <span class="text-xs text-slate-400">{{ ucfirst($enq->gender) }}</span>
              @endif
              @if($enq->dob)
                <p class="text-xs text-slate-400">Age {{ $enq->dob->age }} yrs</p>
              @endif
            </td>
            <td class="td text-sm text-slate-700">{{ $enq->class?->name ?? '—' }}</td>
            <td class="td">
              <p class="font-semibold text-slate-800 text-sm leading-tight">{{ $enq->parent_name }}</p>
              <p class="text-xs text-slate-400 font-mono">{{ $enq->parent_mobile }}</p>
            </td>
            <td class="td">
              @if($enq->source)
                <span class="badge-slate capitalize">{{ str_replace('_', ' ', $enq->source) }}</span>
              @else
                <span class="text-slate-300 text-sm">—</span>
              @endif
            </td>
            <td class="td">
              <span class="{{ $enq->status_color }}">{{ $enq->status_label }}</span>
            </td>
            <td class="td text-xs {{ $enq->follow_up_date && $enq->follow_up_date->isPast() ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
              {{ $enq->follow_up_date?->format('d M Y') ?? '—' }}
            </td>
            <td class="td text-xs text-slate-400">{{ $enq->created_at->format('d M Y') }}</td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-0.5">
                <a href="{{ route('admissions.show', $enq->id) }}" class="btn-icon" title="View">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('admissions.edit', $enq->id) }}" class="btn-icon" title="Edit">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                @if(in_array($enq->status, ['converted', 'confirmed']))
                  <a href="{{ route('admissions.confirmation-letter', $enq->id) }}" target="_blank"
                     class="btn-icon text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50" title="Confirmation Letter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  </a>
                @endif
                <form method="POST" action="{{ route('admissions.destroy', $enq->id) }}"
                      onsubmit="return confirm('Delete this enquiry?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-icon text-red-400 hover:text-red-600 hover:bg-red-50" title="Delete">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="td">
              <div class="py-14 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 mb-1">No enquiries found</p>
                <p class="text-xs text-slate-400 mb-4">Try adjusting your filters or create a new enquiry.</p>
                <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                  New Enquiry
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($enquiries->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
      <span>
        Showing <span class="font-semibold text-slate-700">{{ $enquiries->firstItem() }}</span>–<span class="font-semibold text-slate-700">{{ $enquiries->lastItem() }}</span>
        of <span class="font-semibold text-slate-700">{{ $enquiries->total() }}</span> enquiries
      </span>
      <div>{{ $enquiries->links() }}</div>
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
function bulkSelect() {
  return {
    selected: [],
    toggleAll(e) {
      const ids = @json($enquiries->pluck('id'));
      this.selected = e.target.checked ? ids : [];
    },
    submitBulk(form) {
      if (!this.selected.length) return;
      form.submit();
    }
  }
}
</script>
@endpush
