@extends('layouts.app')

@section('title', 'Add New Employee')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ category: '{{ old('employee_type', 'teaching') }}' }">

  {{-- Header --}}
  <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center gap-3">
      <a href="{{ route('hr.employees') }}" class="btn-icon" title="Back to Employee Directory">
        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-800">Add New Employee</h1>
        <p class="page-subtitle text-xs text-slate-500 mt-0.5">Fill details below and select employee role category</p>
      </div>
    </div>
  </div>

  @if ($errors->any())
  <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start gap-3 shadow-sm">
    <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
      <h4 class="font-bold text-red-900">Please correct the errors below</h4>
      <p class="text-xs text-red-700 mt-0.5">Some fields have validation errors. Check the highlighted inputs below.</p>
    </div>
  </div>
  @endif

  <form method="POST" action="{{ route('hr.employees.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- STEP 1: SELECT CATEGORY --}}
    <div class="card p-6 border-2 border-indigo-100 bg-gradient-to-r from-indigo-50/40 via-white to-purple-50/40">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">1</span>
            Select Staff Category / Role <span class="text-red-500">*</span>
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">The employee will be automatically placed into the corresponding card tab</p>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">

        {{-- Category 1: Teaching Staff --}}
        <label @click="category = 'teaching'"
               :class="category === 'teaching' ? 'border-indigo-600 bg-indigo-50/80 ring-2 ring-indigo-200' : 'border-slate-200 bg-white hover:border-indigo-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="teaching" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Teaching Staff</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Teachers</span>
        </label>

        {{-- Category 2: Non-Teaching Staff --}}
        <label @click="category = 'non_teaching'"
               :class="category === 'non_teaching' ? 'border-purple-600 bg-purple-50/80 ring-2 ring-purple-200' : 'border-slate-200 bg-white hover:border-purple-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="non_teaching" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Non-Teaching</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Admin & Accounts</span>
        </label>

        {{-- Category 3: Driver --}}
        <label @click="category = 'driver'"
               :class="category === 'driver' ? 'border-amber-600 bg-amber-50/80 ring-2 ring-amber-200' : 'border-slate-200 bg-white hover:border-amber-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="driver" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4 4h4m1 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Driver</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Bus & Van</span>
        </label>

        {{-- Category 4: Cleaner --}}
        <label @click="category = 'cleaner'"
               :class="category === 'cleaner' ? 'border-teal-600 bg-teal-50/80 ring-2 ring-teal-200' : 'border-slate-200 bg-white hover:border-teal-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="cleaner" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Cleaner</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Housekeeping</span>
        </label>

        {{-- Category 5: Nanny (Naani) --}}
        <label @click="category = 'nanny'"
               :class="category === 'nanny' ? 'border-rose-600 bg-rose-50/80 ring-2 ring-rose-200' : 'border-slate-200 bg-white hover:border-rose-300'"
               class="cursor-pointer p-3.5 rounded-xl border text-center transition-all duration-150 flex flex-col items-center">
          <input type="radio" name="employee_type" value="nanny" x-model="category" class="sr-only">
          <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <span class="text-xs font-bold text-slate-800">Nanny (Naani)</span>
          <span class="text-[10px] text-slate-500 mt-0.5">Caretaker</span>
        </label>

      </div>
    </div>

    {{-- CATEGORY-SPECIFIC EXTRA FIELDS BLOCK --}}
    {{-- Driver Fields --}}
    <div x-show="category === 'driver'" class="card p-5 border-amber-200 bg-amber-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-amber-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4 4h4m1 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
        Driver Specific Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Driving License Number</label>
          <input type="text" name="license_number" value="{{ old('license_number') }}" class="input uppercase @error('license_number') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. DL-042019881234">
          @error('license_number')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="label">License Expiry Date</label>
          <input type="date" name="license_expiry" value="{{ old('license_expiry') }}" class="input @error('license_expiry') border-red-500 ring-2 ring-red-200 @enderror">
          @error('license_expiry')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="label">Assigned Vehicle / Route</label>
          <input type="text" name="assigned_vehicle" value="{{ old('assigned_vehicle') }}" class="input @error('assigned_vehicle') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. Bus No. 1 / Route 4">
          @error('assigned_vehicle')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- Nanny (Naani) Fields --}}
    <div x-show="category === 'nanny'" class="card p-5 border-rose-200 bg-rose-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-rose-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        Nanny (Naani) Specific Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Assigned Section / Block</label>
          <input type="text" name="assigned_block" value="{{ old('assigned_block') }}" class="input @error('assigned_block') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. Nursery & LKG Section">
          @error('assigned_block')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="label">Shift Timings</label>
          <input type="text" name="shift_timing" value="{{ old('shift_timing') }}" class="input @error('shift_timing') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. School Hours (8:00 AM - 2:30 PM)">
          @error('shift_timing')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- Cleaner Fields --}}
    <div x-show="category === 'cleaner'" class="card p-5 border-teal-200 bg-teal-50/50 space-y-4" style="display:none">
      <h3 class="font-bold text-teal-900 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        Cleaner / Housekeeping Details
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label">Assigned Zone / Building</label>
          <input type="text" name="assigned_block" value="{{ old('assigned_block') }}" class="input @error('assigned_block') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. Academic Block A">
          @error('assigned_block')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="label">Work Shift</label>
          <input type="text" name="shift_timing" value="{{ old('shift_timing') }}" class="input @error('shift_timing') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. Morning Shift (7:00 AM - 3:30 PM)">
          @error('shift_timing')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- STEP 2: PERSONAL INFORMATION --}}
    <div class="card p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-slate-700 text-white text-xs font-bold flex items-center justify-center">2</span>
        Personal Information
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name') }}" class="input @error('first_name') border-red-500 ring-2 ring-red-200 @enderror" required placeholder="First name">
          @error('first_name')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name') }}" class="input @error('last_name') border-red-500 ring-2 ring-red-200 @enderror" required placeholder="Last name">
          @error('last_name')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Gender <span class="text-red-500">*</span></label>
          <select name="gender" class="select @error('gender') border-red-500 ring-2 ring-red-200 @enderror" required>
            <option value="male" @selected(old('gender')==='male')>Male</option>
            <option value="female" @selected(old('gender')==='female')>Female</option>
            <option value="other" @selected(old('gender')==='other')>Other</option>
          </select>
          @error('gender')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" name="mobile" value="{{ old('mobile') }}" class="input @error('mobile') border-red-500 ring-2 ring-red-200 @enderror" required placeholder="10-digit mobile" maxlength="10">
          @error('mobile')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Email Address</label>
          <input type="email" name="official_email" value="{{ old('official_email') }}" class="input @error('official_email') border-red-500 ring-2 ring-red-200 @enderror" placeholder="email@schoolerp.in">
          @error('official_email')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Date of Birth</label>
          <input type="date" name="dob" value="{{ old('dob') }}" class="input @error('dob') border-red-500 ring-2 ring-red-200 @enderror">
          @error('dob')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Aadhaar Number</label>
          <input type="text" name="aadhaar_no" value="{{ old('aadhaar_no') }}" class="input @error('aadhaar_no') border-red-500 ring-2 ring-red-200 @enderror" placeholder="12-digit Aadhaar" maxlength="14">
          @error('aadhaar_no')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">PAN Number</label>
          <input type="text" name="pan_no" value="{{ old('pan_no') }}" class="input uppercase @error('pan_no') border-red-500 ring-2 ring-red-200 @enderror" placeholder="ABCDE1234F" maxlength="15">
          @error('pan_no')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Profile Photo</label>
          <input type="file" name="photo" accept="image/*" class="input text-xs @error('photo') border-red-500 ring-2 ring-red-200 @enderror">
          @error('photo')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- STEP 3: EMPLOYMENT & SALARY DETAILS --}}
    <div class="card p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-slate-700 text-white text-xs font-bold flex items-center justify-center">3</span>
        Job & Salary Details
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">Designation <span class="text-red-500">*</span></label>
          <input type="text" name="designation" value="{{ old('designation') }}" class="input @error('designation') border-red-500 ring-2 ring-red-200 @enderror" required placeholder="e.g. Physics Teacher / Driver">
          @error('designation')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Department</label>
          <input type="text" name="department" value="{{ old('department') }}" class="input @error('department') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. Science / Transport / Housekeeping">
          @error('department')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Joining Date <span class="text-red-500">*</span></label>
          <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" class="input @error('joining_date') border-red-500 ring-2 ring-red-200 @enderror" required>
          @error('joining_date')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Basic Salary (₹/month)</label>
          <input type="number" name="basic_salary" value="{{ old('basic_salary') }}" class="input @error('basic_salary') border-red-500 ring-2 ring-red-200 @enderror" min="0" step="0.01" placeholder="e.g. 25000">
          @error('basic_salary')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Qualification</label>
          <input type="text" name="qualification" value="{{ old('qualification') }}" class="input @error('qualification') border-red-500 ring-2 ring-red-200 @enderror" placeholder="e.g. M.Sc., 10th Pass">
          @error('qualification')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="label">Emergency Contact Mobile</label>
          <input type="tel" name="emergency_contact_mobile" value="{{ old('emergency_contact_mobile') }}" class="input @error('emergency_contact_mobile') border-red-500 ring-2 ring-red-200 @enderror" placeholder="Emergency contact number">
          @error('emergency_contact_mobile')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-3">
          <label class="label">Residential Address</label>
          <textarea name="address" rows="2" class="input @error('address') border-red-500 ring-2 ring-red-200 @enderror" placeholder="Current address details">{{ old('address') }}</textarea>
          @error('address')<p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- SUBMIT BUTTONS --}}
    <div class="flex items-center justify-end gap-3 bg-white p-4 rounded-2xl border border-slate-200">
      <a href="{{ route('hr.employees') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary px-6 shadow-lg shadow-indigo-100 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Save & Add Employee
      </button>
    </div>

  </form>
</div>
@endsection
