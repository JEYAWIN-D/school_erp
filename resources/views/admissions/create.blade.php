@extends('layouts.app')

@section('title', 'New Student Admission Form — DASA EDUGROUP (' . ($academicYear?->name ?? '2025-2026') . ')')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  sectionsList: {{ json_encode($sections->map(fn($s) => ['id' => $s->id, 'class_id' => $s->class_id, 'name' => $s->name])) }},
  standardFees: {{ json_encode($standardFees) }},
  activitiesData: {{ json_encode($activities) }},
  admissionKitsData: {{ json_encode($admissionKits ?? []) }},
  selectedClassId: '{{ request("class_id", "") }}',
  selectedSectionId: '',
  hostelRequired: false,
  selectedActivities: [],
  additionalItems: {},

  // Payment Terms State
  paymentTerms: 'single',
  paymentMode: 'UPI',
  paymentDate: '{{ date("Y-m-d") }}',
  term2DueDate: '{{ date("Y-m-d", strtotime("+90 days")) }}',
  term3DueDate: '{{ date("Y-m-d", strtotime("+180 days")) }}',
  userAmountCollected: null,

  get availableSections() {
    if (!this.selectedClassId) return [];
    return this.sectionsList.filter(s => s.class_id == this.selectedClassId);
  },
  get selectedFee() {
    return this.standardFees[this.selectedClassId] || null;
  },
  get currentClassKit() {
    if (!this.selectedClassId || !this.admissionKitsData[this.selectedClassId]) return [];
    return this.admissionKitsData[this.selectedClassId];
  },
  get standardKitFeeTotal() {
    let sum = 0;
    this.currentClassKit.forEach(cfg => {
      let defaultQty = Number(cfg.default_quantity || 0);
      if (defaultQty > 0) {
        let price = Number(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0));
        sum += defaultQty * price;
      }
    });
    return sum;
  },
  get additionalInventoryFeeTotal() {
    let sum = 0;
    this.currentClassKit.forEach(cfg => {
      let addQty = Number(this.additionalItems[cfg.item_id] || 0);
      if (addQty > 0) {
        let price = Number(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0));
        sum += addQty * price;
      }
    });
    return sum;
  },
  get totalKitFee() {
    return this.standardKitFeeTotal + this.additionalInventoryFeeTotal;
  },
  get hasLowStockWarning() {
    return this.currentClassKit.some(cfg => {
      let addQty = Number(this.additionalItems[cfg.item_id] || 0);
      let totalQty = cfg.default_quantity + addQty;
      return (cfg.item?.current_stock || 0) < totalQty;
    });
  },
  get selectedClassName() {
    let c = this.classList.find(x => x.id == this.selectedClassId);
    return c ? c.name : '';
  },
  get basicFeeTotal() {
    return this.selectedFee ? (this.selectedFee.total_basic || 0) : 0;
  },
  get hostelFeeTotal() {
    return (this.hostelRequired && this.selectedFee) ? (this.selectedFee.hostel_annual || 30000) : 0;
  },
  get activitiesFeeTotal() {
    let sum = 0;
    this.selectedActivities.forEach(id => {
      let act = this.activitiesData.find(a => a.id === id);
      if (act) sum += act.annual_fee;
    });
    return sum;
  },
  get grandTotal() {
    return this.basicFeeTotal + this.hostelFeeTotal + this.activitiesFeeTotal + this.totalKitFee;
  },
  get remainingFeesTotal() {
    return Math.max(0, this.grandTotal - this.totalKitFee);
  },

  // Term amounts calculations: Admission Kit + Additional Kit directly added to Term 1, remaining tuition fees divided evenly
  get term1Amount() {
    if (this.paymentTerms === 'single') return this.grandTotal;
    if (this.paymentTerms === '2_terms') {
      let remT1 = Math.round(this.remainingFeesTotal / 2);
      return this.totalKitFee + remT1;
    }
    if (this.paymentTerms === '3_terms') {
      let remT1 = Math.round(this.remainingFeesTotal / 3);
      return this.totalKitFee + remT1;
    }
    return this.grandTotal;
  },
  get term2Amount() {
    if (this.paymentTerms === '2_terms') return this.grandTotal - this.term1Amount;
    if (this.paymentTerms === '3_terms') return Math.round(this.remainingFeesTotal / 3);
    return 0;
  },
  get term3Amount() {
    if (this.paymentTerms === '3_terms') return this.grandTotal - (this.term1Amount + this.term2Amount);
    return 0;
  },

  get amountCollected() {
    if (this.userAmountCollected !== null && this.userAmountCollected !== '') {
      let val = Number(this.userAmountCollected);
      return Math.min(val, this.grandTotal);
    }
    return this.term1Amount;
  },
  get pendingAmount() {
    return Math.max(0, this.grandTotal - this.amountCollected);
  },
  get paymentStatus() {
    if (this.amountCollected >= this.grandTotal && this.grandTotal > 0) return 'Paid';
    if (this.amountCollected > 0) return 'Partially Paid';
    return 'Pending';
  },

  formatMoney(num) {
    return '₹' + Number(num || 0).toLocaleString('en-IN');
  }
}">

  {{-- ── Top Navigation / Title Header ────────────────────────── --}}
  <div class="flex items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200 shadow-2xs" title="Back">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">New Student Admission Form</h1>
        <p class="text-xs text-slate-500 font-medium">DASA EDUGROUP &bull; Academic Year: {{ $academicYear?->name ?? '2025-2026' }}</p>
      </div>
    </div>

    {{-- Print Form Button --}}
    <a href="{{ route('admissions.print-form') }}?print=true" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-2 shadow-xs cursor-pointer">
      <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      <span>Print Form</span>
    </a>
  </div>

  {{-- Print Header --}}
  <div class="hidden print:block border-b-2 border-blue-900 pb-3 mb-6">
    <h1 class="text-3xl font-black text-blue-900 tracking-wider uppercase">DASA EDUGROUP</h1>
    <p class="text-xs font-bold text-slate-600 uppercase tracking-widest mt-0.5">Official Student Admission Application Form</p>
    <p class="text-xs text-slate-500 font-medium mt-0.5">Academic Year: {{ $academicYear?->name ?? '2025-2026' }} &bull; Date: {{ date('d-m-Y') }}</p>
  </div>

  <form method="POST" action="{{ route('admissions.store') }}" class="space-y-6">
    @csrf

    {{-- ── Step 1: Student Information ──────────────────────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900">Student Information</h2>
        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Step 1 of 3</span>
      </div>

      <div class="space-y-4">
        {{-- First Name & Last Name in same line with gaps --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
            <input type="text" name="first_name" required value="{{ old('first_name') }}"
                   placeholder="e.g. Akash"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('first_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}"
                   placeholder="e.g. A R"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('last_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Student Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="student@example.com"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Date of Birth</label>
            <input type="date" name="dob" value="{{ old('dob') }}"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender</label>
            <select name="gender" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select gender</option>
              <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>

        {{-- Standard & Section Selector --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Applying for Standard / Class <span class="text-rose-500">*</span></label>
            <select name="class_id" required x-model="selectedClassId" @change="selectedSectionId = ''"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">-- Select Standard (Pre-KG to Class 12) --</option>
              @foreach($classes as $c)
                <option value="{{ $c->id }}">Class {{ $c->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Section</label>
            <select name="section_id" x-model="selectedSectionId"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select Section (Default Section A)</option>
              <template x-for="sec in availableSections" :key="sec.id">
                <option :value="sec.id" x-text="sec.name.toLowerCase().includes('section') ? sec.name : ('Section ' + sec.name)"></option>
              </template>
              <template x-if="availableSections.length === 0">
                <template x-for="secName in ['Section A', 'Section B', 'Section C', 'Section D']" :key="secName">
                  <option :value="secName" x-text="secName"></option>
                </template>
              </template>
            </select>
          </div>
        </div>

        {{-- Standard-wise Student Admission Kit & Textbook Inventory --}}
        <div x-show="selectedClassId" x-transition class="pt-4 border-t border-slate-100 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-extrabold text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Student Admission Kit &amp; Textbooks (Standard Kit)</span>
              </h3>
              <p class="text-[11px] text-slate-500 font-medium">Items automatically issued from Warehouse for <span class="font-bold text-slate-900" x-text="'Class ' + selectedClassName"></span></p>
            </div>
            <span class="text-xs font-extrabold font-mono text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg" x-text="currentClassKit.length + ' Items'"></span>
          </div>

          {{-- Stock Warning Alert --}}
          <div x-show="hasLowStockWarning" class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs font-medium text-amber-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Notice: One or more kit items have low or insufficient stock in the Warehouse. Please verify stock before completing admission.</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <template x-for="cfg in currentClassKit" :key="cfg.id">
              <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3">
                <div>
                  <span class="font-extrabold text-slate-900 text-xs block" x-text="cfg.item?.name || 'Item'"></span>
                  <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5 flex-wrap">
                    <span>Default: <strong class="text-slate-800 font-mono" x-text="cfg.default_quantity + ' ' + (cfg.item?.unit || '')"></strong></span>
                    <span>&bull;</span>
                    <span>Stock: <strong :class="(cfg.item?.current_stock || 0) < (cfg.default_quantity + Number(additionalItems[cfg.item_id]||0)) ? 'text-rose-600 font-mono' : 'text-emerald-700 font-mono'" x-text="(cfg.item?.current_stock || 0) + ' ' + (cfg.item?.unit || '')"></strong></span>
                    <span>&bull;</span>
                    <span class="text-slate-700 font-medium">Price: <strong class="font-mono text-blue-700 font-bold" x-text="formatMoney(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0)) + '/' + (cfg.item?.unit || 'unit')"></strong></span>
                  </div>
                </div>

                {{-- Additional Paid Qty Selector --}}
                <div x-show="cfg.allow_additional_qty" class="flex flex-col items-end gap-1">
                  <div class="flex items-center gap-1.5 shrink-0 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 pl-1">+ Extra:</span>
                    <button type="button" @click="additionalItems[cfg.item_id] = Math.max(0, Number(additionalItems[cfg.item_id]||0) - 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-xs cursor-pointer flex items-center justify-center">-</button>
                    <input type="number" :name="'additional_items[' + cfg.item_id + ']'" x-model.number="additionalItems[cfg.item_id]" min="0" readonly class="w-8 text-center text-xs font-mono font-bold text-blue-700 bg-transparent border-0 p-0 focus:ring-0">
                    <button type="button" @click="additionalItems[cfg.item_id] = Number(additionalItems[cfg.item_id]||0) + 1" class="w-6 h-6 rounded-lg bg-blue-100 hover:bg-blue-200 font-bold text-blue-800 text-xs cursor-pointer flex items-center justify-center">+</button>
                  </div>
                  <span class="text-[10px] font-bold text-blue-700 font-mono pr-1" x-show="Number(additionalItems[cfg.item_id]||0) > 0" x-text="'+ ' + formatMoney((Number(additionalItems[cfg.item_id]||0)) * Number(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0)))"></span>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Enquiry Source</label>
            <select name="source" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="Walk-in">Walk-in</option>
              <option value="Online">Online / Website</option>
              <option value="Referral">Parent Referral</option>
              <option value="Advertisement">Newspaper / Social Media</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Assigned Counselor / Staff</label>
            <select name="assigned_to" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select Counselor</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Demographic & Identification Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Blood Group</label>
            <select name="blood_group" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select Blood Group</option>
              <option value="A+" {{ old('blood_group') === 'A+' ? 'selected' : '' }}>A+</option>
              <option value="A-" {{ old('blood_group') === 'A-' ? 'selected' : '' }}>A-</option>
              <option value="B+" {{ old('blood_group') === 'B+' ? 'selected' : '' }}>B+</option>
              <option value="B-" {{ old('blood_group') === 'B-' ? 'selected' : '' }}>B-</option>
              <option value="O+" {{ old('blood_group') === 'O+' ? 'selected' : '' }}>O+</option>
              <option value="O-" {{ old('blood_group') === 'O-' ? 'selected' : '' }}>O-</option>
              <option value="AB+" {{ old('blood_group') === 'AB+' ? 'selected' : '' }}>AB+</option>
              <option value="AB-" {{ old('blood_group') === 'AB-' ? 'selected' : '' }}>AB-</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Category</label>
            <select name="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select Category</option>
              <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
              <option value="obc" {{ old('category') === 'obc' ? 'selected' : '' }}>OBC</option>
              <option value="sc" {{ old('category') === 'sc' ? 'selected' : '' }}>SC</option>
              <option value="st" {{ old('category') === 'st' ? 'selected' : '' }}>ST</option>
              <option value="ews" {{ old('category') === 'ews' ? 'selected' : '' }}>EWS</option>
              <option value="minority" {{ old('category') === 'minority' ? 'selected' : '' }}>Minority</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Religion</label>
            <input type="text" name="religion" value="{{ old('religion') }}" placeholder="e.g. Hindu, Christian, Muslim"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Tongue</label>
            <input type="text" name="mother_tongue" value="{{ old('mother_tongue') }}" placeholder="e.g. Tamil, English, Hindi"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Aadhaar Number</label>
            <input type="text" name="aadhaar_no" value="{{ old('aadhaar_no') }}" placeholder="12-digit Aadhaar"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pincode</label>
            <input type="text" name="pincode" value="{{ old('pincode') }}" placeholder="6-digit PIN"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono">
          </div>
        </div>
      </div>
    </div>

    {{-- ── Step 2: Parent & Guardian Information ───────────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900">Parent &amp; Guardian Details</h2>
        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Step 2 of 3</span>
      </div>

      {{-- Father Details --}}
      <div class="space-y-4">
        <h3 class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider">Father Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father / Primary Parent Name <span class="text-rose-500">*</span></label>
            <input type="text" name="parent_name" required value="{{ old('parent_name') }}"
                   placeholder="Father Name"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
            @error('parent_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Mobile Number <span class="text-rose-500">*</span></label>
            <input type="text" name="parent_mobile" required value="{{ old('parent_mobile') }}"
                   placeholder="10-digit mobile"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
            @error('parent_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Email Address</label>
            <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                   placeholder="father@example.com"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Occupation</label>
            <input type="text" name="father_occupation" value="{{ old('father_occupation') }}"
                   placeholder="e.g. Engineer / Business / Govt. Service"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>
        </div>
      </div>

      {{-- Mother Details --}}
      <div class="space-y-4 pt-4 border-t border-slate-100">
        <h3 class="text-xs font-extrabold text-rose-600 uppercase tracking-wider">Mother Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Name</label>
            <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                   placeholder="Mother Name"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Mobile Number</label>
            <input type="text" name="mother_mobile" value="{{ old('mother_mobile') }}"
                   placeholder="10-digit mobile"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Occupation</label>
            <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}"
                   placeholder="e.g. Teacher / Homemaker / Engineer"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Email Address</label>
            <input type="email" name="mother_email" value="{{ old('mother_email') }}"
                   placeholder="mother@example.com"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>
        </div>
      </div>

      {{-- Address & Income Details --}}
      <div class="space-y-4 pt-4 border-t border-slate-100">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Residential Address</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   placeholder="Full residential address"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Annual Family Income (₹)</label>
            <input type="number" step="0.01" name="annual_family_income" value="{{ old('annual_family_income') }}"
                   placeholder="e.g. 500000"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono">
          </div>
        </div>
      </div>
    </div>

    {{-- ── Hostel Facility Required Section ───────────────────── --}}
    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 flex items-start gap-3.5 transition print-card">
      <input type="checkbox" id="hostel_check" name="hostel_required" value="1" x-model="hostelRequired"
             class="mt-1 w-4 h-4 text-blue-600 rounded border-amber-300 focus:ring-blue-500 cursor-pointer">
      <label for="hostel_check" class="cursor-pointer select-none">
        <span class="block text-sm font-extrabold text-amber-950">Hostel Facility Required</span>
        <span class="block text-xs text-amber-700 font-medium mt-0.5">Includes residential lodging &amp; dining mess charges in total fee.</span>
      </label>
    </div>

    {{-- ── Extra Curricular Activities Section ──────────────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <span>✨ Extra Curricular Activities</span>
        </h2>
        <span class="text-xs text-slate-400 font-medium">Select activities for fixed per annum fees</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($activities as $act)
        <label class="p-3.5 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-3 select-none"
               :class="selectedActivities.includes('{{ $act['id'] }}') ? 'bg-blue-50/70 border-blue-400 shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/80'">
          <div class="flex items-center gap-2.5 min-w-0">
            <input type="checkbox" name="activities[]" value="{{ $act['id'] }}" x-model="selectedActivities"
                   class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
            <span class="text-xs font-bold text-slate-800 truncate">{{ $act['name'] }}</span>
          </div>
          <span class="text-xs font-extrabold font-mono text-blue-600 shrink-0">{{ $act['label'] }}</span>
        </label>
        @endforeach
      </div>
    </div>

    {{-- ── Step 3: Payment Terms & Fee Collection Card ─────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">💳</span>
          <h2 class="text-base font-bold text-slate-900">Payment Terms &amp; Fee Collection</h2>
        </div>
        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Step 3 of 3</span>
      </div>

      {{-- Basic Fees Breakdown --}}
      <div x-show="selectedClassId" x-transition class="space-y-3">
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/70 text-xs space-y-2 font-medium">
          <div class="flex justify-between items-center text-slate-600">
            <span>Tuition Fee</span>
            <span class="font-mono font-bold text-slate-900" x-text="formatMoney(selectedFee?.tuition_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Book Fee</span>
            <span class="font-mono font-bold text-slate-900" x-text="formatMoney(selectedFee?.book_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Exam Fee</span>
            <span class="font-mono font-bold text-slate-900" x-text="formatMoney(selectedFee?.exam_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Lab / Computer Fee</span>
            <span class="font-mono font-bold text-slate-900" x-text="formatMoney(selectedFee?.lab_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-amber-700 pt-1 border-t border-slate-200/60" x-show="hostelRequired">
            <span>Hostel &amp; Mess Fee</span>
            <span class="font-mono font-bold" x-text="formatMoney(selectedFee?.hostel_annual || 30000)"></span>
          </div>
          <div class="flex justify-between items-center text-blue-700 pt-1 border-t border-slate-200/60" x-show="activitiesFeeTotal > 0">
            <span>Extra Curricular Activities</span>
            <span class="font-mono font-bold" x-text="formatMoney(activitiesFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="standardKitFeeTotal > 0">
            <span>Student Admission Kit (Standard Kit)</span>
            <span class="font-mono font-bold" x-text="formatMoney(standardKitFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="additionalInventoryFeeTotal > 0">
            <span>Additional Inventory &amp; Kit Items Fee</span>
            <span class="font-mono font-bold" x-text="formatMoney(additionalInventoryFeeTotal)"></span>
          </div>
        </div>
      </div>

      {{-- Grand Total Highlight Banner --}}
      <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-xs font-extrabold text-blue-950 uppercase tracking-wider">GRAND TOTAL ANNUAL ADMISSION FEE</h3>
          <p class="text-xs text-blue-600 font-medium mt-0.5">Studies + Hostel + Extra Curricular Activities</p>
        </div>
        <div class="text-left sm:text-right">
          <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono" x-text="formatMoney(grandTotal)"></span>
        </div>
      </div>

      {{-- Payment Terms Selector --}}
      <div class="space-y-3 pt-2">
        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">Payment Terms Selection</label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === 'single' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-100 shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">Single Payment</span>
              <input type="radio" name="payment_terms" value="single" x-model="paymentTerms" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">100% annual fee paid at admission</p>
            <p class="text-sm font-black font-mono text-blue-700" x-text="formatMoney(grandTotal)"></p>
          </label>

          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === '2_terms' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-100 shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">2 Terms</span>
              <input type="radio" name="payment_terms" value="2_terms" x-model="paymentTerms" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">100% Kit + 50% tuition in T1, 50% in T2</p>
            <p class="text-sm font-black font-mono text-blue-700" x-text="formatMoney(term1Amount) + ' (T1) &bull; ' + formatMoney(term2Amount) + ' (T2)'"></p>
          </label>

          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === '3_terms' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-100 shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">3 Terms</span>
              <input type="radio" name="payment_terms" value="3_terms" x-model="paymentTerms" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">100% Kit + 1/3 tuition in T1, equal in T2 &amp; T3</p>
            <p class="text-sm font-black font-mono text-blue-700" x-text="formatMoney(term1Amount) + ' (T1) &bull; ' + formatMoney(term2Amount) + ' (T2/T3)'"></p>
          </label>
        </div>
      </div>

      {{-- Dynamic Schedule Breakdown Table --}}
      <div class="space-y-3 pt-2">
        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">Term Payment Schedule</label>
        <div class="overflow-hidden border border-slate-200/80 rounded-2xl">
          <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
              <tr>
                <th class="py-3 px-4">Term</th>
                <th class="py-3 px-4 text-right">Amount</th>
                <th class="py-3 px-4">Due Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr>
                <td class="py-3 px-4 font-bold text-slate-900">Term 1 (At Admission)</td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900" x-text="formatMoney(term1Amount)"></td>
                <td class="py-3 px-4 text-slate-500 font-mono" x-text="paymentDate"></td>
              </tr>
              <tr x-show="paymentTerms === '2_terms' || paymentTerms === '3_terms'">
                <td class="py-3 px-4 font-bold text-slate-900">Term 2</td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900" x-text="formatMoney(term2Amount)"></td>
                <td class="py-3 px-4">
                  <input type="date" name="term_2_due_date" x-model="term2DueDate" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-mono">
                </td>
              </tr>
              <tr x-show="paymentTerms === '3_terms'">
                <td class="py-3 px-4 font-bold text-slate-900">Term 3</td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900" x-text="formatMoney(term3Amount)"></td>
                <td class="py-3 px-4">
                  <input type="date" name="term_3_due_date" x-model="term3DueDate" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-mono">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Payment Collection Controls --}}
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Mode <span class="text-rose-500">*</span></label>
          <select name="payment_mode" required x-model="paymentMode" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
            <option value="Cash">Cash</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Date</label>
          <input type="date" name="payment_date" x-model="paymentDate" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Amount Collected Today (₹) <span class="text-rose-500">*</span></label>
          <input type="number" step="0.01" name="amount_collected" required
                 :value="userAmountCollected !== null ? userAmountCollected : term1Amount"
                 @input="userAmountCollected = $event.target.value"
                 placeholder="0.00"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold font-mono transition text-slate-900">
        </div>
      </div>

      {{-- Live Collection Status Summary Tiles --}}
      <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
        <div>
          <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Collected Today</span>
          <p class="text-xl font-black text-emerald-700 font-mono mt-0.5" x-text="formatMoney(amountCollected)"></p>
        </div>
        <div>
          <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pending Amount</span>
          <p class="text-xl font-black text-amber-600 font-mono mt-0.5" x-text="formatMoney(pendingAmount)"></p>
        </div>
        <div>
          <span class="text-[10px] font-bold text-blue-900 uppercase tracking-wider">Status</span>
          <p class="text-sm font-extrabold font-mono mt-1"
             :class="paymentStatus === 'Paid' ? 'text-emerald-600' : (paymentStatus === 'Partially Paid' ? 'text-amber-600' : 'text-slate-600')"
             x-text="paymentStatus">
          </p>
        </div>
      </div>
    </div>

    {{-- ── Previous School (Optional) ──────────────────────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4 print-card">
      <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Previous School (Optional)</h2>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Previous School</label>
          <input type="text" name="previous_school" value="{{ old('previous_school') }}"
                 placeholder="School name"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Previous Class</label>
          <input type="text" name="previous_class" value="{{ old('previous_class') }}"
                 placeholder="e.g. Class 5"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Percentage / CGPA</label>
          <input type="number" step="0.1" name="previous_percentage" value="{{ old('previous_percentage') }}"
                 placeholder="0–100"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">
        </div>
      </div>
    </div>

    {{-- ── Additional Notes ────────────────────────────────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 print-card">
      <h2 class="text-base font-bold text-slate-900">Additional Notes</h2>
      <textarea name="notes" rows="3"
                placeholder="Any specific requirements or notes..."
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition">{{ old('notes') }}</textarea>
    </div>

    {{-- Signature Box on Print --}}
    <div class="hidden print:grid grid-cols-2 gap-12 pt-8 text-xs">
      <div>
        <p class="font-bold text-slate-800">Parent / Guardian Signature</p>
        <div class="h-10 border-b border-slate-400 border-dashed"></div>
      </div>
      <div class="text-right">
        <p class="font-bold text-slate-800">Admissions Officer Signature</p>
        <div class="h-10 border-b border-slate-400 border-dashed"></div>
      </div>
    </div>

    {{-- ── Form Action Buttons Footer ──────────────────────────── --}}
    <div class="flex items-center justify-end gap-3 pt-4 print:hidden">
      <a href="{{ route('admissions.index') }}" class="w-32 h-11 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold border border-slate-300 text-sm transition shadow-xs flex items-center justify-center">
        Cancel
      </a>
      <button type="submit" class="w-32 h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold border border-blue-600 text-sm transition shadow-xs cursor-pointer flex items-center justify-center">
        Finish
      </button>
    </div>

  </form>
</div>

{{-- Multi-page Clean Print Styling --}}
<style>
  @media print {
    @page {
      size: A4;
      margin: 8mm 12mm;
    }
    body * {
      visibility: hidden;
    }
    form, form *, .print-card, .print-card * {
      visibility: visible;
    }
    form {
      position: absolute;
      left: 0;
      top: 0;
      width: 100% !important;
      max-width: 100% !important;
    }
    .print\:hidden {
      display: none !important;
    }
    .print-card {
      break-inside: avoid !important;
      page-break-inside: avoid !important;
      border: 1px solid #cbd5e1 !important;
      box-shadow: none !important;
      border-radius: 12px !important;
      margin-bottom: 12px !important;
      padding: 16px !important;
    }
    input, select, textarea {
      border: 1px solid #94a3b8 !important;
      background-color: #f8fafc !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      padding: 6px 10px !important;
      font-size: 11px !important;
    }
  }
</style>
@endsection
