@extends('layouts.app')
@section('title','Grading Schemes')
@section('content')
<div class="space-y-6" x-data="{addOpen:false}">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Grading Schemes</h1>
    <button @click="addOpen=true" class="btn btn-primary btn-sm">+ Add Scheme</button>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($schemes as $scheme)
    <div class="card space-y-3">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <div>
          <p class="font-semibold text-slate-800">{{ $scheme->name }}</p>
          <p class="text-xs text-slate-400">{{ $scheme->type === 'percentage' ? 'Percentage-based' : 'Grade Point' }}</p>
        </div>
        @if($scheme->is_default)<span class="badge-green text-xs">Default</span>@endif
      </div>
      <div class="space-y-1">
        @foreach($scheme->ranges->sortByDesc('min_marks') as $r)
        <div class="flex items-center justify-between text-sm px-2 py-1 rounded {{ $r->grade === 'F' ? 'bg-red-50' : 'bg-slate-50' }}">
          <span class="font-bold text-slate-700 w-8">{{ $r->grade }}</span>
          <span class="text-slate-500 text-xs">{{ $r->min_marks }}—{{ $r->max_marks }}%</span>
          <span class="text-slate-400 text-xs">{{ $r->description }}</span>
          @if($r->gpa_points)<span class="font-semibold text-indigo-600 text-xs">{{ $r->gpa_points }} pts</span>@endif
        </div>
        @endforeach
      </div>
      <div class="flex gap-2 pt-1">
        <a href="{{ route('examinations.grading.edit',$scheme->id) }}" class="btn btn-secondary btn-sm flex-1 text-center">Edit Ranges</a>
        @if(!$scheme->is_default)
        <form method="POST" action="{{ route('examinations.grading.default',$scheme->id) }}">@csrf
          <button type="submit" class="btn btn-secondary btn-sm">Set Default</button>
        </form>
        @endif
      </div>
    </div>
    @empty
    <div class="col-span-3 card text-center py-12 text-slate-400">No grading schemes configured.</div>
    @endforelse
  </div>
</div>

{{-- Add Scheme Modal --}}
<div x-data="{addOpen:false}" x-on:open-modal.window="addOpen=($event.detail==='add-scheme')" x-show="addOpen" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="addOpen=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">New Grading Scheme</h3>
    <form method="POST" action="{{ route('examinations.grading.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Scheme Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="e.g. CBSE 10-point Scale">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Type</label>
          <select name="type" class="select">
            <option value="percentage">Percentage</option>
            <option value="gpa">GPA</option>
          </select>
        </div>
        <div><label class="label">Pass Mark (%)</label>
          <input type="number" name="pass_percentage" class="input" value="35" min="0" max="100">
        </div>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1" id="sch_default">
        <label for="sch_default" class="text-sm text-slate-600">Set as default</label>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Create</button>
        <button type="button" @click="addOpen=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
