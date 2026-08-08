@extends('layouts.app')
@section('title', $lesson->title)
@section('content')
<div class="max-w-3xl space-y-5" x-data="{ completed: {{ $isCompleted ? 'true' : 'false' }} }">

  {{-- Breadcrumb --}}
  <div class="text-xs text-slate-400 flex items-center gap-1.5">
    <a href="{{ route('lms.index') }}" class="hover:text-slate-600">LMS</a>
    <span>/</span>
    <a href="{{ route('lms.courses.show', $lesson->unit->course_id) }}" class="hover:text-slate-600">{{ $lesson->unit->course->title }}</a>
    <span>/</span>
    <span>{{ $lesson->unit->title }}</span>
    <span>/</span>
    <span class="text-slate-600">{{ $lesson->title }}</span>
  </div>

  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-bold text-slate-800">{{ $lesson->title }}</h1>
      <span class="badge-{{ $lesson->is_published ? 'green' : 'blue' }}">{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>
    </div>

    {{-- Video --}}
    @if($lesson->type === 'video' && $lesson->video_url)
      @php $embedUrl = $lesson->youtubeEmbedUrl(); @endphp
      <div class="aspect-video bg-black rounded-xl overflow-hidden mb-4">
        <iframe src="{{ $embedUrl }}" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
      </div>
    @endif

    {{-- PDF --}}
    @if($lesson->type === 'pdf' && $lesson->file_path)
      <div class="mb-4 p-4 bg-slate-50 rounded-xl flex items-center gap-3">
        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM14 3.5L18.5 8H14V3.5zM12 17.5h-1V16h1v1.5zm0-2.5h-1v-4h1v4z"/></svg>
        <div class="flex-1">
          <p class="text-sm font-medium text-slate-700">PDF Document</p>
        </div>
        <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="btn-secondary btn-sm">Open PDF</a>
      </div>
    @endif

    {{-- Text / HTML --}}
    @if($lesson->body)
      <div class="prose prose-slate max-w-none text-sm leading-relaxed">{!! strip_tags($lesson->body, '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><blockquote><code><pre><a><img><table><thead><tbody><tr><th><td>') !!}</div>
    @endif

    {{-- Complete button (for students) --}}
    @if(auth()->user()->student_id)
      <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
        <span x-show="completed" class="flex items-center gap-2 text-green-600 text-sm font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Completed!
        </span>
        <button x-show="!completed"
                x-on:click="fetch('{{ route('lms.lessons.complete', $lesson->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}}); completed = true"
                class="btn-primary btn-sm">
          Mark as Complete
        </button>
      </div>
    @endif
  </div>

  {{-- Edit lesson (admin/teacher) --}}
  @if(!auth()->user()->student_id)
  <div class="card" x-data="{ editOpen: false }">
    <button @click="editOpen = !editOpen" class="text-sm font-medium text-blue-600 hover:underline">Edit this lesson</button>
    <div x-show="editOpen" x-transition class="mt-3">
      <form method="POST" action="{{ route('lms.lessons.update', $lesson->id) }}" enctype="multipart/form-data" class="space-y-3"
            x-data="{ lessonType: '{{ $lesson->type }}' }">
        @csrf @method('PUT')
        <input type="text" name="title" value="{{ $lesson->title }}" class="input w-full" required>
        <div>
          <label class="label">Lesson Type</label>
          <select name="type" x-model="lessonType" class="select w-full">
            <option value="video">Video</option>
            <option value="pdf">PDF</option>
            <option value="text">Text / Article</option>
          </select>
        </div>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="is_published" value="1" {{ $lesson->is_published ? 'checked' : '' }} class="rounded">
          Published
        </label>
        <div x-show="lessonType === 'video'">
          <label class="label">YouTube / Video URL</label>
          <input type="url" name="video_url" value="{{ $lesson->video_url }}" placeholder="https://youtube.com/..." class="input w-full">
        </div>
        <div x-show="lessonType === 'pdf'">
          <label class="label">PDF File</label>
          <input type="file" name="file" accept=".pdf" class="input w-full">
          @if($lesson->file_path)<p class="text-xs text-slate-400 mt-1">Current: {{ basename($lesson->file_path) }}</p>@endif
        </div>
        <div x-show="lessonType === 'text'">
          <label class="label">Content</label>
          <textarea name="body" rows="10" class="input w-full text-sm font-mono">{{ $lesson->body }}</textarea>
        </div>
        <button type="submit" class="btn-primary btn-sm">Save Changes</button>
      </form>
    </div>
  </div>
  @endif

</div>
@endsection
