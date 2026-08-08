@extends('layouts.app')

@section('title', 'New Enquiry')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('admissions.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">New Admission Enquiry</h1>
      <p class="page-subtitle">Academic Year: {{ $academicYear?->name ?? 'N/A' }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admissions.store') }}" class="space-y-6">
    @csrf

    {{-- Student Info --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" value="{{ old('student_name') }}" class="input @error('student_name') input-error @enderror" placeholder="Full name">
          @error('student_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Date of Birth</label>
          <input type="date" name="dob" value="{{ old('dob') }}" class="input @error('dob') input-error @enderror">
          @error('dob') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Gender</label>
          <select name="gender" class="select">
            <option value="">Select gender</option>
            <option value="male"   @selected(old('gender') === 'male')>Male</option>
            <option value="female" @selected(old('gender') === 'female')>Female</option>
            <option value="other"  @selected(old('gender') === 'other')>Other</option>
          </select>
        </div>
        <div>
          <label class="label">Applying for Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select @error('class_id') input-error @enderror">
            <option value="">Select class</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected(old('class_id') == $cls->id)>{{ $cls->name }}</option>
            @endforeach
          </select>
          @error('class_id') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Enquiry Source</label>
          <select name="source" class="select">
            <option value="">Select source</option>
            @foreach(['Walk-in', 'Phone Call', 'Website', 'Referral', 'Social Media', 'Newspaper', 'Other'] as $src)
              <option value="{{ strtolower(str_replace(' ', '-', $src)) }}" @selected(old('source') === strtolower(str_replace(' ', '-', $src)))>{{ $src }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Assigned Counsellor</label>
          <select name="assigned_to" class="select">
            <option value="">— Unassigned —</option>
            @foreach($users ?? [] as $u)
              <option value="{{ $u->id }}" @selected(old('assigned_to') == $u->id)>{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- Parent Info --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Parent Name <span class="text-red-500">*</span></label>
          <input type="text" name="parent_name" value="{{ old('parent_name') }}" class="input @error('parent_name') input-error @enderror" placeholder="Father / Mother / Guardian">
          @error('parent_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" name="parent_mobile" value="{{ old('parent_mobile') }}" class="input @error('parent_mobile') input-error @enderror" placeholder="10-digit mobile">
          @error('parent_mobile') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Email Address</label>
          <input type="email" name="parent_email" value="{{ old('parent_email') }}" class="input" placeholder="optional">
        </div>
        <div>
          <label class="label">Follow-up Date</label>
          <input type="date" name="follow_up_date" value="{{ old('follow_up_date') }}" class="input" min="{{ now()->toDateString() }}">
        </div>
        <div class="sm:col-span-2">
          <label class="label">Address</label>
          <input type="text" name="address" value="{{ old('address') }}" class="input" placeholder="Full address">
        </div>
      </div>
    </div>

    {{-- Previous School --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School (Optional)</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-1">
          <label class="label">Previous School</label>
          <input type="text" name="previous_school" value="{{ old('previous_school') }}" class="input" placeholder="School name">
        </div>
        <div>
          <label class="label">Previous Class</label>
          <input type="text" name="previous_class" value="{{ old('previous_class') }}" class="input" placeholder="e.g. Class 5">
        </div>
        <div>
          <label class="label">Percentage / CGPA</label>
          <input type="number" name="previous_percentage" value="{{ old('previous_percentage') }}" class="input" placeholder="0–100" min="0" max="100" step="0.01">
        </div>
      </div>
    </div>

    {{-- Notes --}}
    <div class="card">
      <label class="label">Additional Notes</label>
      <textarea name="notes" rows="3" class="input resize-none" placeholder="Any specific requirements or notes…">{{ old('notes') }}</textarea>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="{{ route('admissions.index') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Save Enquiry
      </button>
    </div>

  </form>
</div>
@endsection
