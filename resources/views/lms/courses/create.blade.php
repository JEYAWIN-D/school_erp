@extends('layouts.app')
@section('title', 'New Course')
@section('content')
<div class="space-y-5 max-w-2xl">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Create New Course</h1>
    <a href="{{ route('lms.index') }}" class="btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('lms.courses.store') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div>
        <label class="label">Course Title</label>
        <input type="text" name="title" value="{{ old('title') }}" class="input w-full" required>
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Class</label>
          <select name="class_id" class="select w-full">
            <option value="">— All Classes —</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Subject</label>
          <select name="subject_id" class="select w-full">
            <option value="">— Any Subject —</option>
            @foreach($subjects as $sub)
              <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div>
        <label class="label">Description</label>
        <textarea name="description" rows="3" class="input w-full">{{ old('description') }}</textarea>
      </div>
      <div>
        <label class="label">Thumbnail (optional)</label>
        <input type="file" name="thumbnail" accept="image/*" class="input w-full">
      </div>
      <div>
        <label class="label">Status</label>
        <select name="status" class="select w-full">
          <option value="draft"     {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
      </div>
      <button type="submit" class="btn-primary w-full">Create Course</button>
    </form>
  </div>
</div>
@endsection
