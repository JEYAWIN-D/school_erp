@extends('layouts.app')
@section('title', $alumni->full_name)
@section('content')
<div class="max-w-2xl space-y-6">
  <div class="flex items-center justify-between">
    <a href="{{ route('alumni.index') }}" class="btn-sm btn-secondary">← Alumni</a>
    <div class="flex gap-2">
      <a href="{{ route('alumni.edit', $alumni->id) }}" class="btn-sm btn-secondary">Edit</a>
      <form method="POST" action="{{ route('alumni.destroy', $alumni->id) }}" onsubmit="return confirm('Delete this alumni record permanently?')">
        @csrf @method('DELETE')
        <button class="btn-sm btn-danger">Delete</button>
      </form>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="card">
    <div class="flex items-start gap-5">
      @if($alumni->profile_photo)
      <img src="{{ Storage::url($alumni->profile_photo) }}" class="w-24 h-24 rounded-2xl object-cover">
      @else
      <div class="w-24 h-24 rounded-2xl bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-600">
        {{ strtoupper(substr($alumni->first_name,0,1)) }}
      </div>
      @endif
      <div class="flex-1">
        <h1 class="text-2xl font-bold text-slate-800">{{ $alumni->full_name }}</h1>
        <p class="text-indigo-600 font-medium">Class of {{ $alumni->passing_year }} @if($alumni->last_class) — {{ $alumni->last_class }}@endif</p>
        <div class="flex gap-2 mt-2 flex-wrap">
          @if($alumni->is_verified)
            <span class="badge-green relative group cursor-default">
              ✓ Verified
              <span class="absolute hidden group-hover:flex -top-9 left-0 bg-slate-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">Identity verified by school administration</span>
            </span>
          @endif
          @if($alumni->current_city) <span class="badge-slate">📍 {{ $alumni->current_city }}</span> @endif
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-5 text-sm">
      @if($alumni->current_occupation)
      <div><p class="text-slate-400 text-xs">Occupation</p><p class="font-medium text-slate-700">{{ $alumni->current_occupation }}</p></div>
      @endif
      @if($alumni->current_employer)
      <div><p class="text-slate-400 text-xs">Employer</p><p class="font-medium text-slate-700">{{ $alumni->current_employer }}</p></div>
      @endif
      @if($alumni->email)
      <div><p class="text-slate-400 text-xs">Email</p><a href="mailto:{{ $alumni->email }}" class="font-medium text-indigo-600 hover:underline">{{ $alumni->email }}</a></div>
      @endif
      @if($alumni->phone)
      <div><p class="text-slate-400 text-xs">Phone</p><a href="tel:{{ $alumni->phone }}" class="font-medium text-blue-600 hover:underline font-mono">{{ $alumni->phone }}</a></div>
      @endif
      @if($alumni->linkedin_url)
      <div class="col-span-2"><p class="text-slate-400 text-xs">LinkedIn</p><a href="{{ $alumni->linkedin_url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $alumni->linkedin_url }}</a></div>
      @endif
    </div>

    @if($alumni->achievements)
    <div class="mt-4 pt-4 border-t border-slate-100">
      <p class="text-xs text-slate-400 mb-1">Achievements</p>
      <p class="text-sm text-slate-600">{{ $alumni->achievements }}</p>
    </div>
    @endif
  </div>
</div>
@endsection
