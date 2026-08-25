@extends('layouts.app')
@section('title', 'Create Notice')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('academics.notices') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Create Notice / Circular</h1>
  </div>

  <form method="POST" action="{{ route('academics.notices.store') }}" class="card space-y-5">
    @csrf

    <div>
      <label class="label">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" value="{{ old('title') }}" class="input {{ (isset($errors) && $errors->has('title')) ? 'input-error' : '' }}" placeholder="Notice title">
      @if(isset($errors) && $errors->has('title')) <p class="field-error">{{ $errors->first('title') }}</p> @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="label">Notice Type <span class="text-red-500">*</span></label>
        <select name="notice_type" class="select">
          @foreach(['general'=>'General','circular'=>'Circular','academic'=>'Academic','exam'=>'Exam','fee'=>'Fee','event'=>'Event'] as $v => $l)
            <option value="{{ $v }}" @selected(old('notice_type') === $v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div x-data="{ audience: '{{ old('target_audience','all') }}' }">
        <label class="label">Target Audience <span class="text-red-500">*</span></label>
        <select name="target_audience" class="select" x-model="audience">
          @foreach(['all'=>'All','students'=>'Students','staff'=>'Staff','parents'=>'Parents','class_specific'=>'Specific Class'] as $v => $l)
            <option value="{{ $v }}">{{ $l }}</option>
          @endforeach
        </select>
        <div x-show="audience === 'class_specific'" x-transition class="mt-2">
          <select name="target_class_id" class="select">
            <option value="">Select class</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected(old('target_class_id') == $cls->id)>{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div>
        <label class="label">Publish Date <span class="text-red-500">*</span></label>
        <input type="date" name="publish_date" value="{{ old('publish_date', today()->toDateString()) }}" class="input">
      </div>
      <div>
        <label class="label">Expiry Date</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="input">
      </div>
    </div>

    <div>
      <label class="label">Content <span class="text-red-500">*</span></label>
      <textarea name="content" rows="6" class="input {{ (isset($errors) && $errors->has('content')) ? 'input-error' : '' }}" placeholder="Notice content…">{{ old('content') }}</textarea>
      @if(isset($errors) && $errors->has('content')) <p class="field-error">{{ $errors->first('content') }}</p> @endif
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_published" value="1" id="pub_check" class="rounded" @checked(old('is_published'))>
      <label for="pub_check" class="text-sm text-slate-700">Publish immediately</label>
    </div>

    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Create Notice
      </button>
      <a href="{{ route('academics.notices') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>

</div>
@endsection
