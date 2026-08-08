@extends('layouts.app')
@section('title', 'Assignments')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Assignments — {{ $course->title }}</h1>
    <a href="{{ route('lms.courses.show', $course->id) }}" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Create form --}}
    @if(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD']))
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">New Assignment</h3>
      <form method="POST" action="{{ route('lms.assignments.store', $course->id) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
          <label class="label">Title</label>
          <input type="text" name="title" value="{{ old('title') }}" class="input w-full" required>
        </div>
        <div>
          <label class="label">Instructions</label>
          <textarea name="instructions" rows="4" class="input w-full">{{ old('instructions') }}</textarea>
        </div>
        <div>
          <label class="label">Due Date &amp; Time</label>
          <input type="datetime-local" name="due_at" value="{{ old('due_at') }}" class="input w-full text-sm">
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label">Max Marks</label>
            <input type="number" name="max_marks" value="{{ old('max_marks', 100) }}" min="0" class="input w-full">
          </div>
        </div>
        <div>
          <label class="label">Attachment (optional)</label>
          <input type="file" name="attachment" class="input w-full text-sm py-1.5">
        </div>
        <button type="submit" class="btn-primary w-full">Create Assignment</button>
      </form>
    </div>
    @endif

    {{-- Assignment list --}}
    <div class="{{ auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD']) ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-4">
      @forelse($assignments as $assignment)
      <div class="card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              @if($assignment->isOverdue())
                <span class="badge-red text-xs">Overdue</span>
              @elseif($assignment->due_at)
                <span class="badge-blue text-xs">Open</span>
              @else
                <span class="badge-green text-xs">No Deadline</span>
              @endif
              <h3 class="font-semibold text-slate-800">{{ $assignment->title }}</h3>
            </div>
            @if($assignment->instructions)
              <p class="text-sm text-slate-600 mt-1">{{ Str::limit($assignment->instructions, 120) }}</p>
            @endif
            <div class="flex items-center gap-4 mt-2 text-xs text-slate-400 flex-wrap">
              @if($assignment->due_at)
                <span>Due: {{ $assignment->due_at->format('d M Y H:i') }}</span>
              @endif
              <span>Max: {{ $assignment->max_marks }} marks</span>
              <span>{{ $assignment->submissions_count ?? 0 }} submission(s)</span>
            </div>
          </div>
          <div class="flex gap-2 flex-shrink-0 flex-wrap">
            @if($assignment->attachment)
              <a href="{{ Storage::url($assignment->attachment) }}" target="_blank" class="btn-xs btn-secondary">Download</a>
            @endif
            @if(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD']))
              <a href="{{ route('lms.assignments.submissions', $assignment->id) }}" class="btn-xs btn-primary">Submissions</a>
              <form method="POST" action="{{ route('lms.assignments.delete', $assignment->id) }}">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Delete assignment?')" class="btn-xs text-red-500 border border-red-200 rounded px-1.5 py-0.5 hover:bg-red-50">Delete</button>
              </form>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="card text-center py-10">
        <p class="text-slate-400 text-sm">No assignments for this course yet.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
