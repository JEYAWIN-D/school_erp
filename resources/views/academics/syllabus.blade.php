@extends('layouts.app')
@section('title', 'Syllabus')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Syllabus</h1>
    <button x-data @click="$dispatch('open-modal','add-syllabus')" class="btn btn-primary btn-sm">Add Syllabus</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3">
    <select name="class_id" class="select w-36">
      <option value="">All Classes</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="subject_id" class="select w-44">
      <option value="">All Subjects</option>
      @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>
  <div class="space-y-3">
    @forelse($syllabus as $item)
    <div class="card" x-data="{ showUpload: false }">
      <div class="flex items-start justify-between">
        <div>
          <p class="font-semibold text-slate-800">{{ $item->topic ?? $item->chapter_title ?? '—' }}</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ $item->subject?->name }} &mdash; {{ $item->class?->name }}
            @if($item->term)<span class="ml-2 badge-blue text-xs">{{ $item->term }}</span>@endif
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span class="{{ $item->status === 'completed' ? 'badge-green' : ($item->status === 'in_progress' ? 'badge-amber' : 'badge-slate') }} capitalize">{{ str_replace('_',' ', $item->status) }}</span>
          @if($item->document_path)
            <a href="{{ asset('storage/' . $item->document_path) }}" target="_blank"
               class="btn btn-secondary btn-xs flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              PDF
            </a>
          @endif
          <button @click="showUpload = !showUpload" class="btn btn-secondary btn-xs">
            {{ $item->document_path ? 'Replace PDF' : 'Upload PDF' }}
          </button>
        </div>
      </div>
      @if($item->description)<p class="text-sm text-slate-500 mt-2">{{ $item->description }}</p>@endif
      <div x-show="showUpload" x-transition class="mt-3 pt-3 border-t border-slate-100">
        <form method="POST" action="{{ route('academics.syllabus.document', $item->id) }}"
              enctype="multipart/form-data" class="flex items-end gap-3 flex-wrap">
          @csrf
          <div class="flex-1 min-w-[200px]">
            <label class="label text-xs">PDF Document (max 10 MB)</label>
            <input type="file" name="document" accept=".pdf" class="input text-sm" required>
          </div>
          <div class="w-36">
            <label class="label text-xs">Term</label>
            <input type="text" name="term" value="{{ $item->term }}" placeholder="e.g. Term 1" class="input text-sm">
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Upload</button>
        </form>
      </div>
    </div>
    @empty
    <div class="card text-center py-10 text-slate-400">No syllabus entries found. Select a class and subject to view.</div>
    @endforelse
  </div>
  @if(method_exists($syllabus,'hasPages') && $syllabus->hasPages())<div class="text-sm">{{ $syllabus->links() }}</div>@endif
</div>
@endsection
