@extends('layouts.app')
@section('title', 'Email Result Notifications')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Email Result Notifications — {{ $exam->name }}</h1>
    <a href="{{ route('examinations.index') }}" class="btn-secondary btn-sm">← Back</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif

  <div class="card max-w-2xl">
    <h2 class="text-base font-semibold text-slate-700 mb-4">Send Report Card via Email</h2>
    <p class="text-sm text-slate-600 mb-6">
      This will generate a PDF report card for each student and email it to the parent/guardian.
      Only students with marks entered and a registered email address will receive notifications.
    </p>
    <form method="POST" action="{{ route('examinations.email-results.send', $exam->id) }}"
      onsubmit="return confirm('Send result emails to all eligible students? This may take a while.')">
      @csrf
      <div class="space-y-4">
        <div>
          <label class="label">Filter by Class <span class="text-slate-400 text-xs">(optional — leave blank for all)</span></label>
          <select name="class_id" class="select">
            <option value="">All Classes</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}">{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
          <p class="text-sm text-amber-800 font-medium">Before sending:</p>
          <ul class="text-sm text-amber-700 mt-1 space-y-1 list-disc list-inside">
            <li>Ensure SMTP mail settings are configured in <code>.env</code></li>
            <li>All marks must be entered and confirmed</li>
            <li>Parents must have email addresses registered in the system</li>
          </ul>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="btn-primary">Send Result Notifications</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
