@extends('layouts.app')

@section('title', 'New Enquiry')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
  selectedClassId: '{{ old('class_id', request('class_id', '')) }}',
  hostelSelected: false,
  hostelFee: 45000,
  transportSelected: false,
  transportFee: 18000,
  selectedActivities: [],
  classesData: {{ json_encode($standardFees ?? []) }},
  activitiesData: {{ json_encode($activities ?? []) }},

  getSelectedFee() {
    return this.classesData[this.selectedClassId] || null;
  },
  getSelectedClassName() {
    let fee = this.getSelectedFee();
    return fee ? ('Class ' + fee.class_name) : 'No Standard Selected';
  },
  getActivitiesTotal() {
    let sum = 0;
    this.selectedActivities.forEach(id => {
      let act = this.activitiesData.find(a => a.id === id);
      if (act) sum += act.annual_fee;
    });
    return sum;
  },
  getGrandTotal() {
    let fee = this.getSelectedFee();
    if (!fee) return 0;
    let base = fee.total_annual || 0;
    let hostel = this.hostelSelected ? this.hostelFee : 0;
    let transport = this.transportSelected ? this.transportFee : 0;
    return base + hostel + transport + this.getActivitiesTotal();
  }
}">

  <div class="flex items-center gap-4">
    <a href="{{ route('admissions.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="page-title">New Admission Enquiry</h1>
      <p class="page-subtitle">Academic Year: {{ $academicYear?->name ?? '2025-2026' }}</p>
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
          <input type="text" name="student_name" value="{{ old('student_name') }}" class="input @error('student_name') input-error @enderror" placeholder="Full name" required>
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
          <select name="class_id" x-model="selectedClassId" class="select @error('class_id') input-error @enderror" required>
            <option value="">Select class</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected(old('class_id', request('class_id')) == $cls->id)>{{ $cls->name }}</option>
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
          <input type="text" name="parent_name" value="{{ old('parent_name') }}" class="input @error('parent_name') input-error @enderror" placeholder="Father / Mother / Guardian" required>
          @error('parent_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" name="parent_mobile" value="{{ old('parent_mobile') }}" class="input @error('parent_mobile') input-error @enderror" placeholder="10-digit mobile" required>
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

    {{-- ── BASIC FEES BREAKDOWN & TOTAL CALCULATION (Screenshot 1 Matching) ── --}}
    <div class="card bg-white border border-slate-200 shadow-xs space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
          <span>💳 BASIC FEES BREAKDOWN & TOTAL CALCULATION</span>
        </h3>
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
              :class="selectedClassId ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-500'"
              x-text="getSelectedClassName()">
          No Standard Selected
        </span>
      </div>

      {{-- 3-Column Quick Breakdown --}}
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tuition Fee</span>
          <p class="text-base font-black font-mono text-slate-800 mt-0.5"
             x-text="getSelectedFee() ? ('₹' + Number(getSelectedFee().tuition_fee).toLocaleString('en-IN')) : '₹0'">
            ₹0
          </p>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Admission Fee (1st Yr)</span>
          <p class="text-base font-black font-mono text-slate-800 mt-0.5"
             x-text="getSelectedFee() ? ('₹' + Number(getSelectedFee().admission_fee).toLocaleString('en-IN')) : '₹0'">
            ₹0
          </p>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Activity & Lab Fee</span>
          <p class="text-base font-black font-mono text-slate-800 mt-0.5"
             x-text="getSelectedFee() ? ('₹' + Number(getSelectedFee().activity_fee).toLocaleString('en-IN')) : '₹0'">
            ₹0
          </p>
        </div>
      </div>

      {{-- Optional Add-ons Checkboxes --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
        <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/70 transition cursor-pointer text-xs">
          <input type="checkbox" x-model="hostelSelected" class="w-4 h-4 rounded text-indigo-600 focus:ring-0">
          <div class="flex-1">
            <span class="font-bold text-slate-800">Hostel & Boarding Facility</span>
            <span class="block text-[10px] text-slate-400">Dormitory & Meal Plan</span>
          </div>
          <span class="font-mono font-bold text-slate-700">+₹45,000</span>
        </label>

        <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/70 transition cursor-pointer text-xs">
          <input type="checkbox" x-model="transportSelected" class="w-4 h-4 rounded text-indigo-600 focus:ring-0">
          <div class="flex-1">
            <span class="font-bold text-slate-800">School Bus Transport</span>
            <span class="block text-[10px] text-slate-400">Doorstep AC Route</span>
          </div>
          <span class="font-mono font-bold text-slate-700">+₹18,000</span>
        </label>
      </div>

      {{-- Grand Total Annual Fee Banner (Screenshot 1 Matching) --}}
      <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div>
          <span class="text-[10px] font-black uppercase tracking-wider text-indigo-200">
            GRAND TOTAL ANNUAL ADMISSION FEE
          </span>
          <p class="text-[11px] text-slate-300">
            (Studies + Hostel + Extra Curricular Activities)
          </p>
        </div>
        <div class="text-right">
          <div class="text-2xl font-black font-mono text-emerald-400"
               x-text="'₹' + Number(getGrandTotal()).toLocaleString('en-IN')">
            ₹0
          </div>
          <span class="text-[10px] font-bold text-indigo-200">per annum total</span>
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
        Save Admission Enquiry
      </button>
    </div>

  </form>
</div>
@endsection
