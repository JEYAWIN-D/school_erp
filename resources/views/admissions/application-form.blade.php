@extends('layouts.app')

@section('title', 'Online Application Form')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center gap-4">
    <a href="{{ route('admissions.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">Online Application Form</h1>
      <p class="page-subtitle">{{ $academicYear?->name }} — Share this page link with parents</p>
    </div>
  </div>

  {{-- Public link notice --}}
  <div class="alert-info">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>This form is accessible from: <strong>{{ url('/admissions/application-form') }}</strong> — share with prospective parents.</span>
  </div>

  <div class="card">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <h2 class="text-lg font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Admission Application Form</h2>
      <p class="text-sm text-slate-500 mt-1">{{ $academicYear?->name ?? date('Y') . '–' . (date('Y')+1) }}</p>
    </div>

    <form method="POST" action="{{ route('admissions.application-form.store') }}" class="space-y-6">
      @csrf

      {{-- Student Details --}}
      <div>
        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs flex items-center justify-center font-bold">1</span>
          Student Details
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Student Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="student_name" value="{{ old('student_name') }}" class="input @error('student_name') input-error @enderror" placeholder="First Middle Last Name">
            @error('student_name') <p class="field-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="label">Date of Birth</label>
            <input type="date" name="dob" value="{{ old('dob') }}" class="input">
          </div>
          <div>
            <label class="label">Gender</label>
            <select name="gender" class="select">
              <option value="">Select</option>
              <option value="male"   @selected(old('gender') === 'male')>Male</option>
              <option value="female" @selected(old('gender') === 'female')>Female</option>
              <option value="other"  @selected(old('gender') === 'other')>Other</option>
            </select>
          </div>
          <div>
            <label class="label">Class Applying For <span class="text-red-500">*</span></label>
            <select name="class_id" class="select @error('class_id') input-error @enderror">
              <option value="">Select class</option>
              @foreach($classes as $cls)
                <option value="{{ $cls->id }}" @selected(old('class_id') == $cls->id)>{{ $cls->name }}</option>
              @endforeach
            </select>
            @error('class_id') <p class="field-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="label">Previous School</label>
            <input type="text" name="previous_school" value="{{ old('previous_school') }}" class="input" placeholder="Name of last school attended">
          </div>
        </div>
      </div>

      <hr class="border-slate-100">

      {{-- Parent Details --}}
      <div>
        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs flex items-center justify-center font-bold">2</span>
          Parent / Guardian Details
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Parent / Guardian Name <span class="text-red-500">*</span></label>
            <input type="text" name="parent_name" value="{{ old('parent_name') }}" class="input @error('parent_name') input-error @enderror">
            @error('parent_name') <p class="field-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="label">Mobile Number <span class="text-red-500">*</span></label>
            <input type="tel" name="parent_mobile" value="{{ old('parent_mobile') }}"
                   class="input @error('parent_mobile') input-error @enderror"
                   inputmode="numeric" maxlength="10" minlength="10" pattern="[6-9][0-9]{9}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                   placeholder="10-digit mobile (e.g. 9876543210)">
            @error('parent_mobile') <p class="field-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="label">Email Address</label>
            <input type="email" name="parent_email" value="{{ old('parent_email') }}" class="input" placeholder="For communication">
          </div>
          <div>
            <label class="label">Residential Address</label>
            <input type="text" name="address" value="{{ old('address') }}" class="input" placeholder="Full address">
          </div>
        </div>
      </div>

      <div class="pt-2">
        <button type="submit" class="btn btn-primary w-full justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          Submit Application
        </button>
      </div>
    </form>
  </div>

  {{-- Recent applications --}}
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-800">Recent Applications</h3>
      <a href="{{ route('admissions.index', ['status' => 'application']) }}" class="text-xs text-blue-600 hover:underline">View all</a>
    </div>
    @php
      $recent = \App\Models\Enquiry::where('status', 'application')->latest()->take(5)->get();
    @endphp
    @forelse($recent as $r)
      <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800">{{ $r->student_name }}</p>
          <p class="text-xs text-slate-400">{{ $r->enquiry_number }} · {{ $r->class?->name }}</p>
        </div>
        <span class="text-xs text-slate-400">{{ $r->created_at->diffForHumans() }}</span>
      </div>
    @empty
      <p class="text-sm text-slate-400 text-center py-4">No applications yet</p>
    @endforelse
  </div>

</div>
@endsection
