@extends('layouts.app')
@section('title', 'Activity-Based Learning Records')
@section('content')
<div class="space-y-6" x-data="{ addOpen: false }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Activity Learning Records</h1>
      <p class="page-subtitle">NEP 2020 — Project, experiment, field trip, arts, sports, community service</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Student ID</label>
      <input type="text" name="student_id" value="{{ request('student_id') }}" placeholder="Student ID…" class="input w-44">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Load</button>
  </form>

  @if($student)
    <div class="flex items-center justify-between">
      <p class="font-semibold text-slate-700">{{ $student->full_name }} — Activity Records ({{ $academicYear?->name }})</p>
      <button @click="addOpen = !addOpen" class="btn btn-primary btn-sm">+ Add Activity</button>
    </div>

    {{-- Add Form --}}
    <div x-show="addOpen" x-transition class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">New Activity Record</h3>
      <form method="POST" action="{{ route('examinations.activity-records.store') }}" enctype="multipart/form-data"
            class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <div>
          <label class="label">Activity Name <span class="text-red-500">*</span></label>
          <input type="text" name="activity_name" class="input" required placeholder="e.g. Science Fair Project">
        </div>
        <div>
          <label class="label">Type <span class="text-red-500">*</span></label>
          <select name="activity_type" class="select" required>
            <option value="">Select Type</option>
            @foreach(['project' => 'Project', 'experiment' => 'Experiment', 'field_trip' => 'Field Trip', 'art' => 'Art', 'sports' => 'Sports', 'community_service' => 'Community Service', 'other' => 'Other'] as $val => $label)
              <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Date <span class="text-red-500">*</span></label>
          <input type="date" name="activity_date" class="input" required max="{{ today()->toDateString() }}">
        </div>
        <div class="sm:col-span-2">
          <label class="label">Description</label>
          <textarea name="description" class="input" rows="2" placeholder="What did the student do?"></textarea>
        </div>
        <div>
          <label class="label">Outcome / Learning</label>
          <input type="text" name="outcome" class="input" placeholder="Key learning or result…">
        </div>
        <div>
          <label class="label">Attachment (optional)</label>
          <input type="file" name="attachment" class="input" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <div class="sm:col-span-3 flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm">Save Record</button>
          <button type="button" @click="addOpen = false" class="btn btn-ghost btn-sm">Cancel</button>
        </div>
      </form>
    </div>

    {{-- Records list --}}
    @if($records->count())
      <div class="space-y-3">
        @foreach($records as $rec)
          <div class="card-flat py-3 flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-slate-800 text-sm">{{ $rec->activity_name }}</span>
                <span class="badge-slate capitalize text-xs">{{ str_replace('_', ' ', $rec->activity_type) }}</span>
                <span class="text-xs text-slate-400">{{ $rec->activity_date->format('d M Y') }}</span>
              </div>
              @if($rec->description)
                <p class="text-sm text-slate-500 mt-0.5">{{ $rec->description }}</p>
              @endif
              @if($rec->outcome)
                <p class="text-xs text-green-600 mt-1 font-medium">Outcome: {{ $rec->outcome }}</p>
              @endif
            </div>
            @if($rec->attachment)
              <a href="{{ Storage::url($rec->attachment) }}" target="_blank" class="btn btn-secondary btn-xs">View</a>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="card text-center py-10 text-slate-400">No activity records yet. Add the first one above.</div>
    @endif
  @else
    <div class="card text-center py-10 text-slate-400">Enter a Student ID to view or add activity records.</div>
  @endif

</div>
@endsection
