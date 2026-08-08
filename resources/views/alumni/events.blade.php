@extends('layouts.app')
@section('title', 'Alumni Events')
@section('content')
<div class="space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('alumni.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Alumni Events</h1>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Create form --}}
    <div class="lg:col-span-1">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">New Event</h3>
        <form method="POST" action="{{ route('alumni.events.store') }}" class="space-y-3">
          @csrf
          <div>
            <label class="label">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" class="input" value="{{ old('title') }}" required>
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="label">Event Date <span class="text-red-500">*</span></label>
            <input type="date" name="event_date" class="input" value="{{ old('event_date') }}" required>
            @error('event_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="label">Venue</label>
            <input type="text" name="venue" class="input" value="{{ old('venue') }}" placeholder="Location / Online">
          </div>
          <div>
            <label class="label">Description</label>
            <textarea name="description" rows="4" class="input">{{ old('description') }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary w-full">Create Event</button>
        </form>
      </div>
    </div>

    {{-- Events list --}}
    <div class="lg:col-span-2">
      @forelse($events as $event)
      <div class="card mb-4">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <h3 class="font-semibold text-slate-800">{{ $event->title }}</h3>
              @if($event->is_published)
                <span class="badge-green">Published</span>
              @else
                <span class="badge-slate">Draft</span>
              @endif
            </div>
            <p class="text-sm text-slate-500 mb-1">
              <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
              @if($event->venue)
                &bull;
                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $event->venue }}
              @endif
            </p>
            @if($event->description)
              <p class="text-sm text-slate-500 line-clamp-2">{{ $event->description }}</p>
            @endif
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <form method="POST" action="{{ route('alumni.events.toggle', $event->id) }}">
              @csrf
              <button type="submit" class="btn btn-secondary btn-sm">
                {{ $event->is_published ? 'Unpublish' : 'Publish' }}
              </button>
            </form>
            <form method="POST" action="{{ route('alumni.events.destroy', $event->id) }}" onsubmit="return confirm('Delete this event?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-secondary btn-sm text-red-600 hover:bg-red-50">Delete</button>
            </form>
          </div>
        </div>
      </div>
      @empty
      <div class="card text-center py-12 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p class="text-sm">No alumni events yet. Create one to get started.</p>
      </div>
      @endforelse

      @if($events->hasPages())
        <div class="mt-4">{{ $events->links() }}</div>
      @endif
    </div>

  </div>
</div>
@endsection
