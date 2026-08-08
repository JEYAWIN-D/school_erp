@extends('layouts.app')
@section('title', isset($event) ? 'Edit Event' : 'New Event')
@section('content')
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">{{ isset($event) ? 'Edit Event' : 'Create Event' }}</h1>
    <a href="{{ route('events.index') }}" class="btn-sm btn-secondary">← Back</a>
  </div>

  <form method="POST"
        action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}"
        enctype="multipart/form-data"
        class="card space-y-4">
    @csrf
    @if(isset($event)) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="label">Event Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $event->name ?? '') }}" class="input" required>
      </div>
      <div>
        <label class="label">Type <span class="text-red-500">*</span></label>
        <select name="event_type" class="select" required>
          @foreach(['academic','cultural','sports','holiday','meeting','other'] as $t)
          <option value="{{ $t }}" @selected(old('event_type', $event->event_type ?? '')===$t)>{{ ucfirst($t) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Audience</label>
        <select name="audience" class="select">
          @foreach(['all','students','staff','parents'] as $a)
          <option value="{{ $a }}" @selected(old('audience', $event->audience ?? 'all')===$a)>{{ ucfirst($a) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Date <span class="text-red-500">*</span></label>
        <input type="date" name="event_date" value="{{ old('event_date', isset($event) ? $event->event_date->toDateString() : '') }}" class="input" required>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="label">Start Time</label>
          <input type="time" name="start_time" value="{{ old('start_time', $event->start_time ?? '') }}" class="input">
        </div>
        <div>
          <label class="label">End Time</label>
          <input type="time" name="end_time" value="{{ old('end_time', $event->end_time ?? '') }}" class="input">
        </div>
      </div>
      <div class="col-span-2">
        <label class="label">Venue</label>
        <input type="text" name="venue" value="{{ old('venue', $event->venue ?? '') }}" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Description</label>
        <div class="flex gap-1 mb-1">
          <button type="button" onclick="fmtDoc('bold')" class="btn-xs btn-secondary" title="Bold"><strong>B</strong></button>
          <button type="button" onclick="fmtDoc('italic')" class="btn-xs btn-secondary" title="Italic"><em>I</em></button>
          <button type="button" onclick="fmtDoc('underline')" class="btn-xs btn-secondary" title="Underline"><u>U</u></button>
          <button type="button" onclick="fmtDoc('insertUnorderedList')" class="btn-xs btn-secondary" title="Bullet list">• List</button>
          <button type="button" onclick="fmtDoc('insertOrderedList')" class="btn-xs btn-secondary" title="Numbered list">1. List</button>
        </div>
        <div id="desc-editor" contenteditable="true"
             class="input min-h-[100px] text-sm leading-relaxed"
             style="white-space:pre-wrap;">{!! old('description', $event->description ?? '') !!}</div>
        <textarea name="description" id="desc-hidden" class="hidden">{{ old('description', $event->description ?? '') }}</textarea>
        <script>
          function fmtDoc(cmd) { document.execCommand(cmd, false, null); document.getElementById('desc-editor').focus(); }
          document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('desc-hidden').value = document.getElementById('desc-editor').innerHTML;
          });
        </script>
      </div>
      <div class="col-span-2">
        <label class="label">Banner Image</label>
        <input type="file" name="banner_image" accept="image/*" class="input">
        @if(isset($event) && $event->banner_image)
        <img src="{{ Storage::url($event->banner_image) }}" class="h-20 mt-2 rounded-lg">
        @endif
      </div>
    </div>

    <div class="flex flex-wrap gap-4">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $event->is_published ?? false)) class="rounded">
        Publish Event
      </label>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="allow_rsvp" value="1" @checked(old('allow_rsvp', $event->allow_rsvp ?? false)) class="rounded">
        Allow RSVP
      </label>
      <div class="flex items-center gap-2">
        <label class="text-sm text-slate-600 whitespace-nowrap">Max RSVP</label>
        <input type="number" name="max_rsvp" value="{{ old('max_rsvp', $event->max_rsvp ?? '') }}"
               class="input w-24 text-sm" min="1" placeholder="No limit">
      </div>
    </div>

    @if($errors->any())
    <div class="alert-danger text-sm">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
    @endif

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">{{ isset($event) ? 'Update' : 'Create Event' }}</button>
      <a href="{{ route('events.index') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
