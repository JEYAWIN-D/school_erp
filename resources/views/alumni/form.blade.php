@extends('layouts.app')
@section('title', isset($alumni) ? 'Edit Alumni' : 'Add Alumni')
@section('content')
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">{{ isset($alumni) ? 'Edit Alumni' : 'Add Alumni Record' }}</h1>
    <a href="{{ route('alumni.index') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST"
    action="{{ isset($alumni) ? route('alumni.update', $alumni->id) : route('alumni.store') }}"
    enctype="multipart/form-data"
    class="card space-y-4">
    @csrf @if(isset($alumni)) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">First Name <span class="text-red-500">*</span></label><input type="text" name="first_name" value="{{ old('first_name', $alumni->first_name ?? '') }}" class="input" required></div>
      <div><label class="label">Last Name <span class="text-red-500">*</span></label><input type="text" name="last_name" value="{{ old('last_name', $alumni->last_name ?? '') }}" class="input" required></div>
      <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email', $alumni->email ?? '') }}" class="input"></div>
      <div><label class="label">Phone</label><input type="tel" name="phone" value="{{ old('phone', $alumni->phone ?? '') }}" class="input"></div>
      <div>
        <label class="label">Link Student Record</label>
        <select name="student_id" class="select">
          <option value="">None</option>
          @foreach($students as $s)
          <option value="{{ $s->id }}" @selected(old('student_id', $alumni->student_id ?? '')==$s->id)>
            {{ $s->first_name }} {{ $s->last_name }} ({{ $s->admission_no }})
          </option>
          @endforeach
        </select>
      </div>
      <div><label class="label">Passing Year <span class="text-red-500">*</span></label><input type="number" name="passing_year" value="{{ old('passing_year', $alumni->passing_year ?? '') }}" class="input" min="1990" max="{{ now()->year+1 }}" required></div>
      <div><label class="label">Last Class Studied</label><input type="text" name="last_class" value="{{ old('last_class', $alumni->last_class ?? '') }}" class="input"></div>
      <div><label class="label">Current Occupation</label><input type="text" name="current_occupation" value="{{ old('current_occupation', $alumni->current_occupation ?? '') }}" class="input"></div>
      <div><label class="label">Current Employer</label><input type="text" name="current_employer" value="{{ old('current_employer', $alumni->current_employer ?? '') }}" class="input"></div>
      <div><label class="label">Current City</label><input type="text" name="current_city" value="{{ old('current_city', $alumni->current_city ?? '') }}" class="input"></div>
      <div><label class="label">LinkedIn URL</label><input type="url" name="linkedin_url" value="{{ old('linkedin_url', $alumni->linkedin_url ?? '') }}" class="input"></div>
      <div class="col-span-2"><label class="label">Achievements / Notable Info</label><textarea name="achievements" rows="3" class="input">{{ old('achievements', $alumni->achievements ?? '') }}</textarea></div>
      <div class="col-span-2">
        <label class="label">Profile Photo</label>
        <input type="file" name="profile_photo" accept="image/*" class="input">
        @if(isset($alumni) && $alumni->profile_photo)
        <img src="{{ Storage::url($alumni->profile_photo) }}" class="h-16 rounded-full mt-2">
        @endif
      </div>
    </div>

    @if(isset($alumni))
    <div class="flex flex-wrap gap-4">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $alumni->is_verified ?? false)) class="rounded">
        Mark as Verified Alumni
      </label>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $alumni->is_active ?? true)) class="rounded">
        Active Record
      </label>
    </div>
    @endif

    @if($errors->any()) <div class="alert-danger text-sm">{{ $errors->first() }}</div> @endif

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">{{ isset($alumni) ? 'Update' : 'Add Alumni' }}</button>
      <a href="{{ route('alumni.index') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
