@extends('layouts.app')
@section('title', 'Competency Master — NEP 2020')
@section('content')
<div class="space-y-6" x-data="{ addOpen: false }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Master</h1>
      <p class="page-subtitle">NEP 2020 — Subject-wise competencies per class</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('examinations.competency-assessment') }}" class="btn btn-secondary btn-sm">Assess Students</a>
      <a href="{{ route('examinations.competency-report') }}" class="btn btn-secondary btn-sm">Progress Report</a>
      <a href="{{ route('examinations.coscholastic-nep') }}" class="btn btn-secondary btn-sm">Co-Scholastic</a>
      <a href="{{ route('examinations.activity-records') }}" class="btn btn-secondary btn-sm">Activity Records</a>
      <button @click="addOpen = !addOpen" class="btn btn-primary btn-sm">+ Add Competency</button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- Add Form --}}
  <div x-show="addOpen" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Add Competency</h3>
    <form method="POST" action="{{ route('examinations.competencies.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @csrf
      <div>
        <label class="label">Class <span class="text-red-500">*</span></label>
        <select name="class_id" class="select" required>
          <option value="">Select Class</option>
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <option value="">Select Subject</option>
          @foreach($subjects as $sub)
            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Competency Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" placeholder="e.g. Understands place value" required>
      </div>
      <div>
        <label class="label">Code</label>
        <input type="text" name="code" class="input" placeholder="e.g. MATH-G3-C1">
      </div>
      <div>
        <label class="label">Domain <span class="text-red-500">*</span></label>
        <select name="domain" class="select" required>
          <option value="cognitive">Cognitive</option>
          <option value="affective">Affective</option>
          <option value="psychomotor">Psychomotor</option>
          <option value="co_scholastic">Co-Scholastic</option>
        </select>
      </div>
      <div>
        <label class="label">Sort Order</label>
        <input type="number" name="sort_order" class="input" value="0" min="0">
      </div>
      <div class="sm:col-span-3">
        <label class="label">Description</label>
        <textarea name="description" class="input" rows="2" placeholder="Optional description…"></textarea>
      </div>
      <div class="sm:col-span-3 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Competency</button>
        <button type="button" @click="addOpen = false" class="btn btn-ghost btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
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
      <label class="label">Subject</label>
      <select name="subject_id" class="select w-40">
        <option value="">All Subjects</option>
        @foreach($subjects as $sub)
          <option value="{{ $sub->id }}" @selected(request('subject_id') == $sub->id)>{{ $sub->name }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['class_id','subject_id']))
      <a href="{{ route('examinations.competencies') }}" class="btn btn-ghost btn-sm">Clear</a>
    @endif
  </form>

  {{-- Table --}}
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Code</th>
          <th class="th">Competency</th>
          <th class="th">Class</th>
          <th class="th">Subject</th>
          <th class="th">Domain</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($competencies as $comp)
          <tr class="tr">
            <td class="td font-mono text-xs text-blue-600">{{ $comp->code ?? '—' }}</td>
            <td class="td font-medium text-slate-800">
              {{ $comp->name }}
              @if($comp->description)
                <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($comp->description, 80) }}</p>
              @endif
            </td>
            <td class="td">{{ $comp->class?->name }}</td>
            <td class="td">{{ $comp->subject?->name }}</td>
            <td class="td">
              @php $dc = ['cognitive'=>'badge-blue','affective'=>'badge-purple','psychomotor'=>'badge-green','co_scholastic'=>'badge-amber'] @endphp
              <span class="{{ $dc[$comp->domain] ?? 'badge-slate' }} capitalize">{{ str_replace('_', ' ', $comp->domain) }}</span>
            </td>
            <td class="td text-right">
              <form method="POST" action="{{ route('examinations.competencies.delete', $comp->id) }}" onsubmit="return confirm('Delete this competency?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No competencies defined yet. Add your first competency above.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($competencies->hasPages())
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing {{ $competencies->firstItem() }}–{{ $competencies->lastItem() }} of {{ $competencies->total() }}</span>
      {{ $competencies->links() }}
    </div>
  @endif

</div>
@endsection
