@extends('layouts.app')
@section('title', $course->title)
@section('content')
<div class="space-y-5" x-data="{ addUnitOpen: false, editCourse: false }">

  {{-- Header --}}
  <div class="flex items-start justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">{{ $course->title }}</h1>
      <p class="text-sm text-slate-400 mt-0.5">
        <span class="badge-{{ $course->status === 'published' ? 'green' : 'blue' }}">{{ ucfirst($course->status) }}</span>
        &nbsp;&bull; {{ $course->units->count() }} units &bull; {{ $course->lessons->count() }} lessons
      </p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('lms.quiz.builder', $course->id) }}"    class="btn-secondary btn-sm">Quiz Builder</a>
      <a href="{{ route('lms.assignments', $course->id) }}"     class="btn-secondary btn-sm">Assignments</a>
      <a href="{{ route('lms.forum', $course->id) }}"           class="btn-secondary btn-sm">Forum</a>
      <a href="{{ route('lms.progress', $course->id) }}"        class="btn-secondary btn-sm">Progress</a>
      <button @click="editCourse = !editCourse" class="btn-primary btn-sm">Edit Course</button>
    </div>
  </div>

  {{-- Edit course form --}}
  <div x-show="editCourse" x-transition class="card border-blue-200">
    <h3 class="font-semibold text-slate-700 mb-3">Edit Course Details</h3>
    <form method="POST" action="{{ route('lms.courses.update', $course->id) }}" enctype="multipart/form-data"
          class="grid grid-cols-2 gap-3">
      @csrf @method('PUT')
      <div class="col-span-2">
        <label class="label">Title</label>
        <input type="text" name="title" value="{{ $course->title }}" class="input w-full" required>
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-full">
          <option value="">— Any —</option>
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}" {{ $course->class_id == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Status</label>
        <select name="status" class="select w-full">
          <option value="draft"     {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
          <option value="archived"  {{ $course->status === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
      </div>
      <div class="col-span-2">
        <label class="label">Description</label>
        <textarea name="description" rows="2" class="input w-full">{{ $course->description }}</textarea>
      </div>
      <div class="col-span-2">
        <label class="label">Thumbnail</label>
        <input type="file" name="thumbnail" accept="image/*" class="input w-full">
      </div>
      <div class="col-span-2 flex gap-2">
        <button type="submit" class="btn-primary btn-sm">Save Changes</button>
        <button type="button" @click="editCourse = false" class="btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Course content tree --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Units + Lessons --}}
    <div class="lg:col-span-2 space-y-4">

      <div id="units-list" data-course="{{ $course->id }}" data-reorder-url="{{ route('lms.units.reorder', $course->id) }}">
      @foreach($course->units as $unit)
      <div class="card mb-4 unit-card" data-unit-id="{{ $unit->id }}" x-data="{ addLesson: false, editUnit: false }">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <span class="unit-drag-handle cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-500 select-none" title="Drag to reorder">⠿</span>
            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
              {{ $loop->iteration }}
            </span>
            <h3 class="font-semibold text-slate-700">{{ $unit->title }}</h3>
          </div>
          <div class="flex gap-2">
            <button @click="addLesson = !addLesson" class="btn-xs btn-primary">+ Lesson</button>
            <button @click="editUnit = !editUnit" class="btn-xs btn-secondary">Edit</button>
            <form method="POST" action="{{ route('lms.units.delete', $unit->id) }}" class="inline">
              @csrf @method('DELETE')
              <button type="submit" onclick="return confirm('Delete unit and all its lessons?')" class="btn-xs text-red-500 hover:bg-red-50 border border-red-200 rounded px-1.5 py-0.5">×</button>
            </form>
          </div>
        </div>

        {{-- Edit unit --}}
        <div x-show="editUnit" x-transition class="mb-3">
          <form method="POST" action="{{ route('lms.units.update', $unit->id) }}" class="flex gap-2">
            @csrf @method('PUT')
            <input type="text" name="title" value="{{ $unit->title }}" class="input flex-1 text-sm">
            <button type="submit" class="btn-sm btn-primary">Save</button>
          </form>
        </div>

        {{-- Lessons list --}}
        <div class="lessons-list" data-unit="{{ $unit->id }}" data-reorder-url="{{ route('lms.lessons.reorder', $unit->id) }}">
        @foreach($unit->lessons as $lesson)
        <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0 lesson-row" data-lesson-id="{{ $lesson->id }}">
          <span class="lesson-drag-handle cursor-grab active:cursor-grabbing text-slate-200 hover:text-slate-400 select-none text-sm" title="Drag to reorder">⠿</span>
          <div class="w-2 h-2 rounded-full {{ $lesson->is_published ? 'bg-green-400' : 'bg-slate-300' }} flex-shrink-0"></div>
          <div class="flex-1 min-w-0">
            <a href="{{ route('lms.lessons.show', $lesson->id) }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
              {{ $lesson->title }}
            </a>
            <span class="ml-2 text-xs text-slate-400 capitalize">{{ str_replace('_', ' ', $lesson->type) }}</span>
          </div>
          <form method="POST" action="{{ route('lms.lessons.delete', $lesson->id) }}">
            @csrf @method('DELETE')
            <button type="submit" onclick="return confirm('Delete lesson?')" class="text-xs text-red-400 hover:text-red-600">×</button>
          </form>
        </div>
        @endforeach
        </div>

        {{-- Add lesson form --}}
        <div x-show="addLesson" x-transition class="mt-3 pt-3 border-t border-slate-100">
          <form method="POST" action="{{ route('lms.lessons.store', $unit->id) }}" enctype="multipart/form-data" x-data="{ type: 'text' }" class="space-y-2">
            @csrf
            <div class="grid grid-cols-2 gap-2">
              <input type="text" name="title" placeholder="Lesson title" class="input col-span-2 text-sm" required>
              <select name="type" x-model="type" class="select text-sm">
                <option value="text">Text / HTML</option>
                <option value="video">Video (YouTube)</option>
                <option value="pdf">PDF Upload</option>
                <option value="quiz">Quiz</option>
                <option value="assignment">Assignment</option>
              </select>
              <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_published" value="1" class="rounded"> Published
              </label>
            </div>
            <textarea x-show="type === 'text'" name="body" rows="3" placeholder="Lesson content (HTML allowed)..." class="input w-full text-sm"></textarea>
            <input x-show="type === 'video'" type="url" name="video_url" placeholder="YouTube URL..." class="input w-full text-sm">
            <input x-show="type === 'pdf'" type="file" name="file" accept=".pdf" class="input w-full text-sm">
            <div class="flex gap-2">
              <button type="submit" class="btn-sm btn-primary">Add Lesson</button>
              <button type="button" @click="addLesson = false" class="btn-sm btn-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>
      @endforeach
      </div>{{-- /units-list --}}

      {{-- Add unit --}}
      <div class="card border-dashed" x-data="{ open: false }">
        <button @click="open = !open" class="w-full text-center text-sm text-blue-600 font-medium py-2">
          + Add Unit
        </button>
        <div x-show="open" x-transition class="mt-2">
          <form method="POST" action="{{ route('lms.units.store', $course->id) }}" class="flex gap-2">
            @csrf
            <input type="text" name="title" placeholder="Unit title..." class="input flex-1 text-sm" required>
            <button type="submit" class="btn-sm btn-primary">Add</button>
          </form>
        </div>
      </div>
    </div>

    {{-- Side info --}}
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3">Course Info</h3>
        <dl class="space-y-2 text-sm">
          <div><dt class="text-xs text-slate-400">Status</dt><dd class="font-medium">{{ ucfirst($course->status) }}</dd></div>
          <div><dt class="text-xs text-slate-400">Units</dt><dd class="font-medium">{{ $course->units->count() }}</dd></div>
          <div><dt class="text-xs text-slate-400">Lessons</dt><dd class="font-medium">{{ $course->lessons->count() }}</dd></div>
          <div><dt class="text-xs text-slate-400">Quizzes</dt><dd class="font-medium">{{ $course->quizzes->count() }}</dd></div>
          <div><dt class="text-xs text-slate-400">Assignments</dt><dd class="font-medium">{{ $course->assignments->count() }}</dd></div>
        </dl>
      </div>

      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3">Delete Course</h3>
        <p class="text-xs text-slate-400 mb-3">This will delete the course and all its units and lessons.</p>
        <form method="POST" action="{{ route('lms.courses.destroy', $course->id) }}">
          @csrf @method('DELETE')
          <button type="submit" onclick="return confirm('Delete this course permanently?')" class="w-full py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
            Delete Course
          </button>
        </form>
      </div>
    </div>

  </div>
</div>
@push('scripts')
<script>
(function() {
  function makeDraggable(container, itemSelector, handleSelector, onReorder) {
    let dragging = null;
    container.querySelectorAll(itemSelector).forEach(item => {
      const handle = handleSelector ? item.querySelector(handleSelector) : item;
      if (!handle) return;
      handle.addEventListener('mousedown', () => { item.draggable = true; });
      item.addEventListener('dragstart', e => { dragging = item; item.style.opacity = '.4'; e.dataTransfer.effectAllowed = 'move'; });
      item.addEventListener('dragend', () => { item.style.opacity = ''; item.draggable = false; dragging = null; onReorder(container, itemSelector); });
      item.addEventListener('dragover', e => { e.preventDefault(); if (dragging && dragging !== item) { const rect = item.getBoundingClientRect(); const mid = rect.top + rect.height / 2; item.parentNode.insertBefore(dragging, e.clientY < mid ? item : item.nextSibling); } });
    });
  }

  function sendReorder(container, itemSelector, url, attr) {
    const ids = Array.from(container.querySelectorAll(itemSelector)).map(el => el.dataset[attr]);
    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ order: ids }) });
  }

  // Units reorder
  const unitsList = document.getElementById('units-list');
  if (unitsList) {
    makeDraggable(unitsList, '.unit-card', '.unit-drag-handle', (c, sel) => {
      sendReorder(c, sel, unitsList.dataset.reorderUrl, 'unitId');
    });
  }

  // Lessons reorder (per unit)
  document.querySelectorAll('.lessons-list').forEach(list => {
    makeDraggable(list, '.lesson-row', '.lesson-drag-handle', (c, sel) => {
      sendReorder(c, sel, list.dataset.reorderUrl, 'lessonId');
    });
  });
})();
</script>
@endpush
@endsection
