@extends('layouts.app')

@section('title', 'Edit Enquiry — ' . $enquiry->enquiry_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('admissions.show', $enquiry->id) }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Edit Enquiry</h1>
      <p class="page-subtitle font-mono">{{ $enquiry->enquiry_number }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admissions.update', $enquiry->id) }}" class="space-y-6">
    @csrf @method('PUT')

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" value="{{ old('student_name', $enquiry->student_name) }}" class="input @error('student_name') input-error @enderror">
          @error('student_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Date of Birth</label>
          <input type="date" name="dob" value="{{ old('dob', $enquiry->dob?->toDateString()) }}" class="input">
        </div>
        <div>
          <label class="label">Gender</label>
          <select name="gender" class="select">
            <option value="">Select gender</option>
            <option value="male"   @selected(old('gender', $enquiry->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $enquiry->gender) === 'female')>Female</option>
            <option value="other"  @selected(old('gender', $enquiry->gender) === 'other')>Other</option>
          </select>
        </div>
        <div>
          <label class="label">Applying for Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select @error('class_id') input-error @enderror">
            <option value="">Select class</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected(old('class_id', $enquiry->class_id) == $cls->id)>{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Source</label>
          <select name="source" class="select">
            <option value="">Select source</option>
            @foreach(['walk-in' => 'Walk-in', 'phone-call' => 'Phone Call', 'website' => 'Website', 'referral' => 'Referral', 'social-media' => 'Social Media', 'newspaper' => 'Newspaper', 'other' => 'Other'] as $val => $label)
              <option value="{{ $val }}" @selected(old('source', $enquiry->source) === $val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Assigned Counsellor</label>
          <select name="assigned_to" class="select">
            <option value="">— Unassigned —</option>
            @foreach($users ?? [] as $u)
              <option value="{{ $u->id }}" @selected(old('assigned_to', $enquiry->assigned_to) == $u->id)>{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Parent Name <span class="text-red-500">*</span></label>
          <input type="text" name="parent_name" value="{{ old('parent_name', $enquiry->parent_name) }}" class="input @error('parent_name') input-error @enderror">
        </div>
        <div>
          <label class="label">Mobile <span class="text-red-500">*</span></label>
          <input type="tel" name="parent_mobile" value="{{ old('parent_mobile', $enquiry->parent_mobile) }}" class="input">
        </div>
        <div>
          <label class="label">Email</label>
          <input type="email" name="parent_email" value="{{ old('parent_email', $enquiry->parent_email) }}" class="input">
        </div>
        <div>
          <label class="label">Follow-up Date</label>
          <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $enquiry->follow_up_date?->toDateString()) }}" class="input">
        </div>
        <div class="sm:col-span-2">
          <label class="label">Address</label>
          <input type="text" name="address" value="{{ old('address', $enquiry->address) }}" class="input">
        </div>
      </div>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">School Name</label>
          <input type="text" name="previous_school" value="{{ old('previous_school', $enquiry->previous_school) }}" class="input">
        </div>
        <div>
          <label class="label">Class</label>
          <input type="text" name="previous_class" value="{{ old('previous_class', $enquiry->previous_class) }}" class="input">
        </div>
        <div>
          <label class="label">Percentage</label>
          <input type="number" name="previous_percentage" value="{{ old('previous_percentage', $enquiry->previous_percentage) }}" class="input" min="0" max="100" step="0.01">
        </div>
      </div>
    </div>

    <div class="card">
      <label class="label">Notes</label>
      <textarea name="notes" rows="3" class="input resize-none">{{ old('notes', $enquiry->notes) }}</textarea>
    </div>

    <div class="flex items-center gap-3 justify-end">
      <a href="{{ route('admissions.show', $enquiry->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>

  </form>
</div>
@endsection
