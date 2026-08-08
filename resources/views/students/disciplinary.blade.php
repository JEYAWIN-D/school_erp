@extends('layouts.app')
@section('title','Disciplinary Records — '.$student->full_name)
@section('content')
<div class="space-y-6">
  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="{{ route('students.index') }}" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="{{ route('students.show',$student->id) }}" class="hover:text-slate-600">{{ $student->full_name }}</a>
    <span>/</span>
    <span class="text-slate-600">Disciplinary</span>
  </nav>
  <div class="flex items-center gap-3">
    <a href="{{ route('students.show',$student->id) }}" class="text-slate-400 hover:text-slate-700">←</a>
    <h1 class="page-title">Disciplinary Records — {{ $student->full_name }}</h1>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('students.disciplinary.add',$student->id) }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Log Incident</h3>
      <div><label class="label">Incident Date</label><input type="date" name="incident_date" class="input" value="{{ today()->toDateString() }}"></div>
      <div><label class="label">Incident Type</label>
        <select name="incident_type" class="select">
          @foreach(['misconduct'=>'Misconduct','absenteeism'=>'Absenteeism','bullying'=>'Bullying','property_damage'=>'Property Damage','cheating'=>'Cheating','other'=>'Other'] as $k=>$v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div><label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="3" class="input" required></textarea>
      </div>
      <div><label class="label">Action Taken</label>
        <input type="text" name="action_taken" class="input" placeholder="Warning, suspension, etc.">
      </div>
      <div x-data="{type:''}" >
        <label class="label">Action Type</label>
        <select name="action_type" x-model="type" class="select w-full">
          <option value="">Select Type</option>
          <option value="warning">Warning</option>
          <option value="suspension">Suspension</option>
          <option value="expulsion">Expulsion</option>
          <option value="positive_award">Positive Award</option>
          <option value="other">Other</option>
        </select>
        <div x-show="type==='suspension'" x-transition class="grid grid-cols-2 gap-3 mt-3">
          <div><label class="label text-xs">Suspension From</label><input type="date" name="suspension_from" class="input"></div>
          <div><label class="label text-xs">Suspension To</label><input type="date" name="suspension_to" class="input"></div>
        </div>
        <div x-show="type==='positive_award'" x-transition class="mt-3">
          <label class="label text-xs">Award Name</label>
          <input type="text" name="award_name" class="input" placeholder="e.g. Best Student Award">
        </div>
      </div>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="parent_notified" value="1"> Parent Notified
      </label>
      <button type="submit" class="btn btn-primary">Save Record</button>
    </form>
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Incident History</h3>
      @forelse($incidents as $rec)
      <div class="py-2 border-b border-slate-100 last:border-0">
        <div class="flex items-start justify-between gap-2">
          <div class="flex gap-1 flex-wrap">
            @if($rec->action_type === 'positive_award')
              <span class="badge-green text-xs capitalize">{{ $rec->award_name ?: 'Award' }}</span>
            @elseif($rec->action_type === 'suspension')
              <span class="badge-red text-xs">Suspension</span>
            @elseif($rec->action_type === 'expulsion')
              <span class="badge-red text-xs font-bold">EXPULSION</span>
            @else
              <span class="badge-{{ in_array($rec->incident_type, ['misconduct','bullying']) ? 'red' : 'amber' }} capitalize text-xs">{{ str_replace('_',' ',$rec->incident_type) }}</span>
            @endif
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">{{ $rec->incident_date->format('d M Y') }}</span>
            @if($rec->action_type !== 'positive_award')
            <a href="{{ route('students.warning-letter', [$student->id, $rec->id]) }}" target="_blank"
               class="text-xs text-red-500 hover:text-red-700 font-medium" title="Warning Letter PDF">
              ⚠ Letter
            </a>
            @endif
          </div>
        </div>
        <p class="text-sm text-slate-600 mt-1">{{ $rec->description }}</p>
        @if($rec->action_taken)<p class="text-xs text-slate-400 mt-0.5">Action: {{ $rec->action_taken }}</p>@endif
        @if($rec->suspension_from)<p class="text-xs text-red-400 mt-0.5">Suspended: {{ $rec->suspension_from->format('d M') }} – {{ $rec->suspension_to?->format('d M Y') }}</p>@endif
        @if($rec->parent_notified)<span class="text-xs text-green-500">✓ Parent notified</span>@endif
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No disciplinary records.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
