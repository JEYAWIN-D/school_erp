@extends('layouts.app')

@section('title', 'New Student Admission Form — DASA EDUGROUP (' . ($academicYear?->name ?? '2025-2026') . ')')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
  classList: {{ json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
  sectionsList: {{ json_encode($sections->map(fn($s) => ['id' => $s->id, 'class_id' => $s->class_id, 'name' => $s->name])) }},
  standardFees: {{ json_encode($standardFees) }},
  activitiesData: {{ json_encode($activities) }},
  admissionKitsData: {{ json_encode($admissionKits ?? []) }},
  transportRoutesData: {{ json_encode($transportRoutes ?? []) }},
  selectedClassId: '{{ request("class_id", "") }}',
  selectedSectionId: '',

  // After School Program (ASP) State — replaces Hostel per school requirement
  aspRequired: false,
  aspFee: 15000,

  // School Transport Facility State (Stopping, Km, Fee)
  transportRequired: false,
  selectedRouteId: '',
  selectedStopId: '',

  // Concession Categories State (Staff kid, Topper, Sports, Single shot, Referral, Sibling)
  concessionType: 'none',
  concessionAmountInput: '',
  concessionRemarks: '',

  // Sibling in Same School
  hasSibling: false,

  // Extra Curricular Activities (ECA) — Complimentary per school requirement
  selectedActivities: [],
  additionalItems: {},

  // Live Student & Parent Photo Previews
  photoPreview: null,
  handlePhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.photoPreview = URL.createObjectURL(file);
    }
  },
  fatherPhotoPreview: null,
  handleFatherPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.fatherPhotoPreview = URL.createObjectURL(file);
    }
  },
  motherPhotoPreview: null,
  handleMotherPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.motherPhotoPreview = URL.createObjectURL(file);
    }
  },
  guardianPhotoPreview: null,
  handleGuardianPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.guardianPhotoPreview = URL.createObjectURL(file);
    }
  },
  docUploadMode: 'desk',

  // Payment Terms & Account Destination State
  paymentTerms: 'single',
  paymentMode: 'UPI',
  paymentAccount: 'upi',
  upiRefNo: '',
  setAccount(account, mode) {
    this.paymentAccount = account;
    this.paymentMode = mode;
  },
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
  get aspFeeTotal() {
    return this.aspRequired ? Number(this.aspFee || 15000) : 0;
  },
  get currentRoute() {
    if (!this.transportRequired || !this.selectedRouteId) return null;
    return this.transportRoutesData.find(r => r.id == this.selectedRouteId) || null;
  },
  get currentRouteStops() {
    return this.currentRoute?.stops || [];
  },
  get currentStop() {
    if (!this.currentRoute || !this.selectedStopId) return null;
    return (this.currentRoute.stops || []).find(s => s.id == this.selectedStopId) || null;
  },
  get transportFee() {
    if (!this.transportRequired) return 0;
    if (this.currentStop && this.currentStop.fare !== null) return Number(this.currentStop.fare);
    if (this.currentRoute) return Number(this.currentRoute.fee || 0);
    return 0;
  },
  get transportDistanceKm() {
    if (!this.transportRequired) return 0;
    if (this.currentStop && this.currentStop.distance_km !== null) return Number(this.currentStop.distance_km);
    if (this.currentRoute) return Number(this.currentRoute.distance_km || 0);
    return 0;
  },
  get subTotal() {
    return this.basicFeeTotal + this.aspFeeTotal + this.transportFee + this.totalKitFee;
  },
  get calculatedConcession() {
    if (this.concessionType === 'none') return 0;
    if (this.concessionType === 'staff_kid') return Math.round(this.basicFeeTotal * 0.50);
    if (this.concessionType === 'single_shot') return Math.round(this.subTotal * 0.05);
    if (this.concessionType === 'topper') return Math.round(this.basicFeeTotal * 0.25);
    if (this.concessionType === 'sports') return Math.round(this.basicFeeTotal * 0.20);
    if (this.concessionType === 'sibling') return Math.round(this.basicFeeTotal * 0.15);
    return 0;
  },
  get concessionAmount() {
    if (this.concessionAmountInput !== '' && !isNaN(Number(this.concessionAmountInput))) {
      return Number(this.concessionAmountInput);
    }
    return this.calculatedConcession;
  },
  get grandTotal() {
    return Math.max(0, this.subTotal - this.concessionAmount);
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

  {{-- Clean Section Navigator --}}
  <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-2 overflow-x-auto print:hidden sticky top-3 z-30 backdrop-blur-md bg-white/95">
    <a href="#section-student" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-4 py-2 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 group">
      <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition">1</span>
      <div>
        <span class="font-extrabold text-slate-800 text-xs block">Student Info</span>
        <span class="text-[10px] text-slate-400 font-medium">Class, Identity, Kit</span>
      </div>
    </a>
    <div class="text-slate-300 text-xs font-bold">&rarr;</div>
    <a href="#section-parents" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-4 py-2 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 group">
      <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">2</span>
      <div>
        <span class="font-extrabold text-slate-800 text-xs block">Parents &amp; Facilities</span>
        <span class="text-[10px] text-slate-400 font-medium">Family, ASP, Bus</span>
      </div>
    </a>
    <div class="text-slate-300 text-xs font-bold">&rarr;</div>
    <a href="#section-billing" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-4 py-2 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 group">
      <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">3</span>
      <div>
        <span class="font-extrabold text-slate-800 text-xs block">Fees &amp; Payment</span>
        <span class="text-[10px] text-slate-400 font-medium">UPI / Cash Box 1 &amp; 2</span>
      </div>
    </a>
  </div>

  <form id="admissionCreateForm" method="POST" action="{{ route('admissions.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
    @csrf

    {{-- Server Validation Error Banner --}}
    @if ($errors->any())
      <div id="serverValidationAlert" class="p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-md flex items-start gap-4">
        <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-extrabold text-sm text-rose-900">Please review and fix the following ({{ $errors->count() }}) errors:</h4>
          <ul class="list-disc list-inside text-xs mt-1.5 space-y-1 font-medium text-rose-800">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 font-bold text-sm p-1">✕</button>
      </div>
    @endif

    {{-- Client Validation Error Banner --}}
    <div id="clientValidationAlert" class="hidden p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-md flex items-start gap-4">
      <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <div class="flex-1 min-w-0">
        <h4 class="font-extrabold text-sm text-rose-900" id="clientValidationTitle">Please correct the highlighted fields before submitting:</h4>
        <ul id="clientValidationList" class="list-disc list-inside text-xs mt-1.5 space-y-1 font-medium text-rose-800"></ul>
      </div>
      <button type="button" onclick="document.getElementById('clientValidationAlert').classList.add('hidden')" class="text-rose-400 hover:text-rose-700 font-bold text-sm p-1">✕</button>
    </div>

    {{-- ── Step 1: Student Information ──────────────────────────── --}}
    <div id="section-student" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card scroll-mt-20">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900">Student Information</h2>
        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Step 1 of 3</span>
      </div>

      <div class="space-y-4">
        {{-- Student Passport Photograph Upload with Live Preview --}}
        <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-2xl bg-blue-50/40 border border-blue-100/80">
          <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white border-2 border-dashed border-blue-300 flex items-center justify-center overflow-hidden shrink-0 relative shadow-inner">
            <template x-if="photoPreview">
              <img :src="photoPreview" class="w-full h-full object-cover" alt="Student Photo Preview">
            </template>
            <template x-if="!photoPreview">
              <div class="text-center p-2 text-slate-400">
                <svg class="w-8 h-8 mx-auto text-blue-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Student Photo</span>
              </div>
            </template>
          </div>
          <div class="flex-1 space-y-1.5 text-center sm:text-left">
            <label class="block text-xs font-bold text-slate-800">Student Passport Photograph</label>
            <p class="text-[11px] text-slate-500 font-medium">Upload recent formal photograph of the student (JPEG, PNG, max 3MB). Will appear on the student profile, ID card, and registers.</p>
            <input type="file" name="photo" accept="image/*" @change="handlePhotoChange($event)" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
            @error('photo') <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- First Name & Last Name in same line with gaps --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
            <input type="text" id="first_name" name="first_name" required maxlength="50" value="{{ old('first_name') }}"
                   placeholder="e.g. Akash"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('first_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="first_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name</label>
            <input type="text" id="last_name" name="last_name" maxlength="50" value="{{ old('last_name') }}"
                   placeholder="e.g. A R"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('last_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="last_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Student Email Address</label>
            <input type="email" id="email" name="email" maxlength="100" value="{{ old('email') }}"
                   placeholder="student@example.com"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="email_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Date of Birth</label>
            <input type="date" id="dob" name="dob" max="{{ date('Y-m-d') }}" value="{{ old('dob') }}"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
            @error('dob') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="dob_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender</label>
            <select id="gender" name="gender" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">Select gender</option>
              <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Standard & Section Selector --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Applying for Standard / Class <span class="text-rose-500">*</span></label>
            <select id="class_id" name="class_id" required x-model="selectedClassId" @change="selectedSectionId = ''"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700">
              <option value="">-- Select Standard (Pre-KG to Class 12) --</option>
              @foreach($classes as $c)
                <option value="{{ $c->id }}">Class {{ $c->name }}</option>
              @endforeach
            </select>
            @error('class_id') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="class_id_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Section</label>
            <select id="section_id" name="section_id" x-model="selectedSectionId"
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
            @error('section_id') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- EMIS / PEN Number and Identification Marks --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">EMIS / PEN Number</label>
              <span class="text-[9px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">State EMIS</span>
            </div>
            <input type="text" name="emis_no" value="{{ old('emis_no') }}" placeholder="e.g. 33020100101234"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium font-mono transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Identification Mark 1</label>
            <input type="text" name="identification_mark_1" value="{{ old('identification_mark_1') }}" placeholder="e.g. Mole on right cheek"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Identification Mark 2</label>
            <input type="text" name="identification_mark_2" value="{{ old('identification_mark_2') }}" placeholder="e.g. Scar on left forehead"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
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
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Aadhaar Number</label>
              <span id="aadhaar_no_badge" class="text-[10px] font-mono font-bold text-slate-400">12 digits (Optional)</span>
            </div>
            <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{ old('aadhaar_no') }}"
                   inputmode="numeric" maxlength="12" pattern="[0-9]{12}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,12); updateAadhaarBadge(this, 'aadhaar_no_badge')"
                   placeholder="12-digit Aadhaar"
                   class="w-full px-4 py-3 rounded-xl border @error('aadhaar_no') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono">
            @error('aadhaar_no') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="aadhaar_no_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Pincode</label>
              <span id="pincode_badge" class="text-[10px] font-mono font-bold text-slate-400">6 digits (Optional)</span>
            </div>
            <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}"
                   inputmode="numeric" maxlength="6" pattern="[1-9][0-9]{5}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,6); updatePincodeBadge(this, 'pincode_badge')"
                   placeholder="6-digit PIN"
                   class="w-full px-4 py-3 rounded-xl border @error('pincode') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono">
            @error('pincode') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="pincode_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Step 2: Parent & Guardian Information ───────────────── --}}
    <div id="section-parents" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card scroll-mt-20">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900">Parent &amp; Guardian Details</h2>
        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Step 2 of 3</span>
      </div>

      {{-- Father Details --}}
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider">Father Details</h3>
          <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Campus Visitor Pass Escort #1</span>
        </div>

        {{-- Father Photo Upload for Visitor Pass --}}
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-indigo-50/50 border border-indigo-100">
          <div class="w-14 h-14 rounded-xl bg-white border border-indigo-200 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
            <template x-if="fatherPhotoPreview">
              <img :src="fatherPhotoPreview" class="w-full h-full object-cover" alt="Father Preview">
            </template>
            <template x-if="!fatherPhotoPreview">
              <span class="text-2xl text-indigo-400">👨</span>
            </template>
          </div>
          <div class="flex-1 min-w-0">
            <label class="block text-xs font-bold text-slate-800">Father Photograph (For Campus Visitor Pass)</label>
            <p class="text-[11px] text-slate-500">Will appear on the physical &amp; digital Parent Visitor Pass</p>
            <input type="file" name="father_photo" accept="image/*" @change="handleFatherPhotoChange($event)"
                   class="text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father / Primary Parent Name <span class="text-rose-500">*</span></label>
            <input type="text" id="parent_name" name="parent_name" required maxlength="100" value="{{ old('parent_name') }}"
                   placeholder="Father Name"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('parent_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="parent_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Father Mobile Number <span class="text-rose-500">*</span></label>
              <span id="parent_mobile_badge" class="text-[10px] font-mono font-bold text-slate-400">10 digits required</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none font-mono">+91</span>
              <input type="tel" id="parent_mobile" name="parent_mobile" required
                     inputmode="numeric" maxlength="10" minlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('parent_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'parent_mobile_badge', true)"
                     placeholder="9876543210"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('parent_mobile') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold font-mono transition text-slate-900 tracking-wider">
            </div>
            @error('parent_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="parent_mobile_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Email Address</label>
            <input type="email" id="parent_email" name="parent_email" maxlength="100" value="{{ old('parent_email') }}"
                   placeholder="father@example.com"
                   class="w-full px-4 py-3 rounded-xl border @error('parent_email') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('parent_email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="parent_email_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Occupation</label>
            <input type="text" id="father_occupation" name="father_occupation" maxlength="100" value="{{ old('father_occupation') }}"
                   placeholder="e.g. Engineer / Business"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Father Aadhaar Number</label>
              <span id="father_aadhaar_badge" class="text-[10px] font-mono font-bold text-slate-400">12 digits (Optional)</span>
            </div>
            <input type="text" id="father_aadhaar" name="father_aadhaar" value="{{ old('father_aadhaar') }}"
                   inputmode="numeric" maxlength="12" pattern="[0-9]{12}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,12); updateAadhaarBadge(this, 'father_aadhaar_badge')"
                   placeholder="12-digit Aadhaar"
                   class="w-full px-4 py-3 rounded-xl border @error('father_aadhaar') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition font-mono text-slate-900">
            @error('father_aadhaar') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="father_aadhaar_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>
      </div>

      {{-- Mother Details --}}
      <div class="space-y-4 pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-extrabold text-rose-600 uppercase tracking-wider">Mother Details</h3>
          <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Campus Visitor Pass Escort #2</span>
        </div>

        {{-- Mother Photo Upload for Visitor Pass --}}
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-rose-50/50 border border-rose-100">
          <div class="w-14 h-14 rounded-xl bg-white border border-rose-200 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
            <template x-if="motherPhotoPreview">
              <img :src="motherPhotoPreview" class="w-full h-full object-cover" alt="Mother Preview">
            </template>
            <template x-if="!motherPhotoPreview">
              <span class="text-2xl text-rose-400">👩</span>
            </template>
          </div>
          <div class="flex-1 min-w-0">
            <label class="block text-xs font-bold text-slate-800">Mother Photograph (For Campus Visitor Pass)</label>
            <p class="text-[11px] text-slate-500">Will appear on the physical &amp; digital Parent Visitor Pass</p>
            <input type="file" name="mother_photo" accept="image/*" @change="handleMotherPhotoChange($event)"
                   class="text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-rose-600 file:text-white hover:file:bg-rose-700 cursor-pointer">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Name</label>
            <input type="text" id="mother_name" name="mother_name" maxlength="100" value="{{ old('mother_name') }}"
                   placeholder="Mother Name"
                   class="w-full px-4 py-3 rounded-xl border @error('mother_name') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('mother_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="mother_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Mother Mobile Number</label>
              <span id="mother_mobile_badge" class="text-[10px] font-mono font-bold text-slate-400">10 digits (Optional)</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none font-mono">+91</span>
              <input type="tel" id="mother_mobile" name="mother_mobile"
                     inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('mother_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'mother_mobile_badge', false)"
                     placeholder="10-digit mobile"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('mother_mobile') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold font-mono transition text-slate-900 tracking-wider">
            </div>
            @error('mother_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="mother_mobile_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Occupation</label>
            <input type="text" id="mother_occupation" name="mother_occupation" maxlength="100" value="{{ old('mother_occupation') }}"
                   placeholder="e.g. Teacher / Homemaker / Engineer"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Email Address</label>
            <input type="email" id="mother_email" name="mother_email" maxlength="100" value="{{ old('mother_email') }}"
                   placeholder="mother@example.com"
                   class="w-full px-4 py-3 rounded-xl border @error('mother_email') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
            @error('mother_email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="mother_email_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>
      </div>

      {{-- Guardian Details --}}
      <div class="space-y-4 pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-extrabold text-amber-600 uppercase tracking-wider">Guardian Details (If Applicable)</h3>
          <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Optional Escort #3</span>
        </div>

        {{-- Guardian Photo Upload for Visitor Pass --}}
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-amber-50/50 border border-amber-100">
          <div class="w-14 h-14 rounded-xl bg-white border border-amber-200 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
            <template x-if="guardianPhotoPreview">
              <img :src="guardianPhotoPreview" class="w-full h-full object-cover" alt="Guardian Preview">
            </template>
            <template x-if="!guardianPhotoPreview">
              <span class="text-2xl text-amber-400">👤</span>
            </template>
          </div>
          <div class="flex-1 min-w-0">
            <label class="block text-xs font-bold text-slate-800">Guardian Photograph (For Campus Visitor Pass)</label>
            <p class="text-[11px] text-slate-500">Will appear on the Parent &amp; Guardian Visitor Pass</p>
            <input type="file" name="guardian_photo" accept="image/*" @change="handleGuardianPhotoChange($event)"
                   class="text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Guardian Name</label>
            <input type="text" id="guardian_name" name="guardian_name" maxlength="100" value="{{ old('guardian_name') }}"
                   placeholder="e.g. Grandparent / Local Guardian"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Guardian Mobile Number</label>
              <span id="guardian_mobile_badge" class="text-[10px] font-mono font-bold text-slate-400">10 digits (Optional)</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none font-mono">+91</span>
              <input type="tel" id="guardian_mobile" name="guardian_mobile"
                     inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('guardian_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'guardian_mobile_badge', false)"
                     placeholder="10-digit mobile"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('guardian_mobile') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold font-mono transition text-slate-900 tracking-wider">
            </div>
            @error('guardian_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="guardian_mobile_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Relationship to Student</label>
            <input type="text" id="guardian_relation" name="guardian_relation" maxlength="50" value="{{ old('guardian_relation') }}"
                   placeholder="e.g. Uncle / Aunt / Grandfather"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
          </div>
        </div>
      </div>

      {{-- Sibling Information (School Requirement) --}}
      <div class="space-y-3 pt-4 border-t border-slate-100">
        <div class="flex items-center gap-3">
          <input type="checkbox" id="has_sibling" x-model="hasSibling" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
          <label for="has_sibling" class="text-xs font-bold text-slate-800 cursor-pointer select-none flex items-center gap-1.5">
            <span>Does the student have a sibling currently studying in this school?</span>
            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">Sibling Concession Eligible</span>
          </label>
        </div>

        <div x-show="hasSibling" x-transition class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Sibling Student Name</label>
            <input type="text" name="sibling_name" value="{{ old('sibling_name') }}" placeholder="e.g. Priya Sharma"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 text-xs font-medium text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Sibling Admission No.</label>
            <input type="text" name="sibling_admission_no" value="{{ old('sibling_admission_no') }}" placeholder="e.g. ADM25-0142"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 text-xs font-mono font-bold text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Sibling Class &amp; Section</label>
            <input type="text" name="sibling_class" value="{{ old('sibling_class') }}" placeholder="e.g. Class 7 - Section A"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 text-xs font-medium text-slate-900">
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

    {{-- ── Special School Facilities: ASP & Transport (School Requirements) ── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card">
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900">School Facilities &amp; Programs</h2>
          <p class="text-xs text-slate-500 font-medium">After School Program (ASP) and Transport Facility</p>
        </div>
        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-200">Optional Facilities</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- 1. After School Program (ASP) — Replaces Hostel --}}
        <div class="p-5 rounded-2xl border transition-all"
             :class="aspRequired ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-100 shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70'">
          <div class="flex items-start gap-3.5">
            <input type="checkbox" id="asp_check" name="is_asp" value="1" x-model="aspRequired"
                   class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500 cursor-pointer">
            <div class="space-y-2 flex-1">
              <label for="asp_check" class="cursor-pointer select-none block">
                <span class="text-sm font-extrabold text-amber-950 block">After School Program (ASP)</span>
                <span class="text-xs text-amber-800 font-medium block mt-0.5 leading-relaxed">
                  Special academic tutoring, homework guidance, supervised athletic activities, and evening refreshments.
                </span>
              </label>

              <div x-show="aspRequired" x-transition class="pt-2 border-t border-amber-200/70 flex items-center justify-between gap-3">
                <span class="text-xs font-bold text-amber-900">Annual ASP Fee:</span>
                <div class="flex items-center gap-1">
                  <span class="text-xs font-bold text-slate-500">₹</span>
                  <input type="number" name="asp_fee" x-model.number="aspFee" min="0" step="500"
                         class="w-28 px-3 py-1.5 rounded-xl border border-amber-300 bg-white font-mono font-bold text-xs text-amber-900 text-right focus:ring-amber-500">
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- 2. School Transport Facility (Stopping, Km, Fee) --}}
        <div class="p-5 rounded-2xl border transition-all"
             :class="transportRequired ? 'bg-blue-50/70 border-blue-300 ring-2 ring-blue-100 shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70'">
          <div class="flex items-start gap-3.5">
            <input type="checkbox" id="transport_check" x-model="transportRequired"
                   class="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
            <div class="space-y-3 flex-1">
              <label for="transport_check" class="cursor-pointer select-none block">
                <span class="text-sm font-extrabold text-blue-950 block">School Transport Facility</span>
                <span class="text-xs text-blue-800 font-medium block mt-0.5 leading-relaxed">
                  GPS-monitored school bus/van transit with fixed stopping points and distance-based annual fare.
                </span>
              </label>

              {{-- Hidden Transport Form Inputs for Database --}}
              <input type="hidden" name="transport_route_id" :value="transportRequired ? selectedRouteId : ''">
              <input type="hidden" name="transport_stop_id" :value="transportRequired ? selectedStopId : ''">
              <input type="hidden" name="transport_distance_km" :value="transportRequired ? transportDistanceKm : 0">
              <input type="hidden" name="transport_fee" :value="transportRequired ? transportFee : 0">

              <div x-show="transportRequired" x-transition class="space-y-3 pt-2 border-t border-blue-200/70">
                <div>
                  <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Transport Route</label>
                  <select x-model="selectedRouteId" @change="selectedStopId = ''"
                          class="w-full px-3 py-2 rounded-xl border border-blue-200 bg-white text-xs font-semibold text-slate-800 focus:border-blue-500">
                    <option value="">-- Choose School Route --</option>
                    <template x-for="route in transportRoutesData" :key="route.id">
                      <option :value="route.id" x-text="route.route_name + ' (' + route.distance_km + ' km total)'"></option>
                    </template>
                  </select>
                </div>

                <div x-show="selectedRouteId">
                  <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Boarding / Stopping Point</label>
                  <select x-model="selectedStopId"
                          class="w-full px-3 py-2 rounded-xl border border-blue-200 bg-white text-xs font-semibold text-slate-800 focus:border-blue-500">
                    <option value="">-- Choose Stop / Landmark --</option>
                    <template x-for="stop in currentRouteStops" :key="stop.id">
                      <option :value="stop.id" x-text="stop.name + ' • ' + stop.distance_km + ' km • ' + (stop.landmark ? '(' + stop.landmark + ') • ' : '') + formatMoney(stop.fare)"></option>
                    </template>
                  </select>
                </div>

                {{-- Live Stopping, Km & Fee Summary Badge --}}
                <div x-show="currentStop" class="p-3 bg-white rounded-xl border border-blue-200 flex items-center justify-between text-xs">
                  <div>
                    <span class="font-extrabold text-blue-950 block" x-text="currentStop?.name"></span>
                    <span class="text-[11px] text-slate-500" x-text="'Distance: ' + currentStop?.distance_km + ' km' + (currentStop?.landmark ? ' • Landmark: ' + currentStop.landmark : '')"></span>
                  </div>
                  <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Transport Fee</span>
                    <span class="font-mono font-black text-blue-700 text-sm" x-text="formatMoney(transportFee)"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Concessions & Scholarships (School Requirements) ───── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card">
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900">Fee Concession &amp; Discounts</h2>
          <p class="text-xs text-slate-500 font-medium">Staff Kid, Academic Topper, Sports Quota, Single Shot Payment, Referral, Sibling</p>
        </div>
        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">Fee Concessions</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Concession Category</label>
          <select name="concession_type" x-model="concessionType"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-xs font-bold text-slate-800 transition">
            @foreach($concessionTypes as $k => $label)
              <option value="{{ $k }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Discount Amount (₹)</label>
          <input type="number" step="0.01" min="0" name="concession_amount"
                 :value="concessionAmount"
                 @input="concessionAmountInput = $event.target.value"
                 placeholder="0.00"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-bold font-mono transition text-emerald-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Concession Approval Remarks</label>
          <input type="text" name="concession_remarks" x-model="concessionRemarks"
                 placeholder="e.g. Approved by Principal / Correspondent"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-xs font-medium transition text-slate-800">
        </div>
      </div>

      {{-- Concession Notice Pill --}}
      <div x-show="concessionType !== 'none'" x-transition class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
        <span class="text-emerald-800 font-medium">
          Concession Applied: <strong class="capitalize" x-text="concessionType.replace('_', ' ')"></strong>
        </span>
        <span class="font-mono font-black text-emerald-700 text-sm" x-text="'- ' + formatMoney(concessionAmount)"></span>
      </div>
    </div>

    {{-- ── Official Document Submission & Home QR Upload ────────── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card">
      <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-bold text-slate-900">Student Documents &amp; Certificates</h2>
          <p class="text-xs text-slate-500 font-medium">Upload certificates now at the desk, OR generate a QR code for parent to scan &amp; upload from home</p>
        </div>
        
        {{-- Mode Selector Tabs --}}
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-2xl shrink-0">
          <button type="button" @click="docUploadMode = 'desk'"
                  :class="docUploadMode === 'desk' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                  class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>📁 Upload Now</span>
          </button>
          <button type="button" @click="docUploadMode = 'qr_home'"
                  :class="docUploadMode === 'qr_home' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                  class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>📱 Scan QR from Home</span>
          </button>
        </div>
      </div>

      {{-- Mode A: Direct Document Uploads Now --}}
      <div x-show="docUploadMode === 'desk'" x-transition class="space-y-4">
        <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-100 flex items-center gap-2.5 text-xs text-blue-800">
          <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>Attach digital scans or photos directly (PDF, JPG, PNG &bull; max 5MB each). Documents will be verified automatically.</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {{-- Birth Certificate --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Birth Certificate</span>
              <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">Mandatory</span>
            </div>
            <input type="file" name="doc_birth_certificate" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>

          {{-- Student / Parent Aadhaar --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Student Aadhaar Card</span>
              <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">Mandatory</span>
            </div>
            <input type="file" name="doc_aadhaar" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>

          {{-- Community / Caste Certificate --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Community Certificate</span>
              <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">If Applicable</span>
            </div>
            <input type="file" name="doc_caste" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>

          {{-- Transfer Certificate (TC) --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Transfer Certificate (TC)</span>
              <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded">Std 1 &amp; Above</span>
            </div>
            <input type="file" name="doc_tc" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>

          {{-- Previous Marksheet --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Previous Marksheet / Progress Card</span>
              <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded">Academic</span>
            </div>
            <input type="file" name="doc_marksheet" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>

          {{-- Parent ID Proof --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Parent PAN / ID Proof</span>
              <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded">Finance / 80G</span>
            </div>
            <input type="file" name="doc_pan_id" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
          </div>
        </div>
      </div>

      {{-- Mode B: Scan QR Code from Home (Upload Later) --}}
      <div x-show="docUploadMode === 'qr_home'" x-transition class="p-5 rounded-3xl bg-gradient-to-br from-indigo-900 via-blue-900 to-slate-900 text-white space-y-4 shadow-inner">
        <div class="flex flex-col sm:flex-row items-center gap-5">
          <div class="w-24 h-24 bg-white p-2 rounded-2xl shrink-0 flex flex-col items-center justify-center text-slate-800 shadow-md">
            <svg class="w-16 h-16 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            <span class="text-[8px] font-mono font-black text-indigo-900">INSTANT QR SLIP</span>
          </div>
          <div class="space-y-1.5 min-w-0 text-center sm:text-left">
            <span class="text-[10px] font-black uppercase tracking-widest bg-amber-400 text-slate-950 px-2 py-0.5 rounded-md">
              PARENT HOME SELF-SERVICE
            </span>
            <h3 class="text-base font-black text-white">Scan QR Code &amp; Upload from Home</h3>
            <p class="text-xs text-blue-200 leading-relaxed">
              If the parent does not have physical certificates today, finish this admission form now. The system will automatically generate an official QR Code slip and WhatsApp upload link. The parent can scan the QR code using their phone camera at home anytime and upload their documents directly!
            </p>
          </div>
        </div>
      </div>

      {{-- Physical Verification Checklist --}}
      <div class="space-y-2 pt-2 border-t border-slate-100">
        <span class="text-xs font-extrabold text-slate-600 uppercase tracking-wider block">Physical Documents Received at Desk</span>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
          @foreach($officialDocChecklist as $docKey => $docTitle)
            <label class="p-3 rounded-2xl border border-slate-200 hover:bg-slate-50 flex items-center gap-3 cursor-pointer transition select-none">
              <input type="checkbox" name="documents_submitted[]" value="{{ $docKey }}"
                     class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
              <span class="text-xs font-bold text-slate-800 leading-snug">{{ $docTitle }}</span>
            </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── Extra Curricular Activities Section (Complimentary) ──── --}}
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5 print-card">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <span>✨ Extra Curricular Activities (ECA)</span>
          </h2>
          <p class="text-xs text-slate-500 font-medium">Select student hobby preferences. <strong class="text-blue-600">Complimentary with admission (No extra fee added).</strong></p>
        </div>
        <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">No Extra Charge</span>
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
          <span class="text-[11px] font-bold text-emerald-600 shrink-0">Included</span>
        </label>
        @endforeach
      </div>
    </div>

    {{-- ── Step 3: Payment Terms & Fee Collection Card ─────────── --}}
    <div id="section-billing" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card scroll-mt-20">
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
          <div class="flex justify-between items-center text-amber-800 pt-1 border-t border-slate-200/60" x-show="aspRequired">
            <span>After School Program (ASP) Fee</span>
            <span class="font-mono font-bold text-amber-900" x-text="formatMoney(aspFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-blue-800 pt-1 border-t border-slate-200/60" x-show="transportRequired && transportFee > 0">
            <span>School Transport Facility Fee</span>
            <span class="font-mono font-bold text-blue-900" x-text="formatMoney(transportFee)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="standardKitFeeTotal > 0">
            <span>Student Admission Kit (Standard Kit)</span>
            <span class="font-mono font-bold" x-text="formatMoney(standardKitFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="additionalInventoryFeeTotal > 0">
            <span>Additional Inventory &amp; Kit Items Fee</span>
            <span class="font-mono font-bold" x-text="formatMoney(additionalInventoryFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-emerald-700 pt-1 border-t border-slate-200/60 font-bold" x-show="concessionAmount > 0">
            <span>Concession Discount (<span class="capitalize" x-text="concessionType.replace('_', ' ')"></span>)</span>
            <span class="font-mono" x-text="'- ' + formatMoney(concessionAmount)"></span>
          </div>
        </div>
      </div>

      {{-- Grand Total Highlight Banner --}}
      <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-xs font-extrabold text-blue-950 uppercase tracking-wider">NET GRAND TOTAL ADMISSION FEE</h3>
          <p class="text-xs text-blue-600 font-medium mt-0.5">Studies + Facilities + Admission Kit (After Concessions)</p>
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

      {{-- Payment Destination Account & Mode Selector --}}
      <div class="space-y-4 pt-2">
        <div>
          <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1">
            Payment Destination Account &amp; Mode <span class="text-rose-500">*</span>
          </label>
          <p class="text-xs text-slate-500 font-medium">Select whether the fee was paid via UPI QR or physical cash (automatically added to the respective box in Account Management):</p>
        </div>

        {{-- Hidden fields for form submission --}}
        <input type="hidden" name="payment_mode" :value="paymentMode">
        <input type="hidden" name="payment_account" :value="paymentAccount">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          {{-- Option 1: UPI Account --}}
          <div @click="setAccount('upi', 'UPI')"
               class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between relative select-none"
               :class="paymentAccount === 'upi' ? 'border-purple-500 bg-purple-50/70 ring-2 ring-purple-100 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
            <div class="flex items-start justify-between">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center transition"
                   :class="paymentAccount === 'upi' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-600'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              </div>
              <span x-show="paymentAccount === 'upi'" class="text-[10px] font-extrabold uppercase tracking-wide text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full border border-purple-200">Selected</span>
            </div>
            <div class="mt-3">
              <h4 class="text-xs font-bold text-slate-900">UPI Digital Account</h4>
              <p class="text-[11px] text-slate-500 mt-0.5 font-medium">PhonePe, GPay, Paytm &amp; QR</p>
              <span class="text-[10px] text-purple-700 font-bold block mt-2">&rarr; Deposited to UPI Ledger</span>
            </div>
          </div>

          {{-- Option 2: Cash Box 1 (Front Office) --}}
          <div @click="setAccount('cash_box_1', 'Cash')"
               class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between relative select-none"
               :class="paymentAccount === 'cash_box_1' ? 'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-100 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
            <div class="flex items-start justify-between">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center transition"
                   :class="paymentAccount === 'cash_box_1' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-600'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </div>
              <span x-show="paymentAccount === 'cash_box_1'" class="text-[10px] font-extrabold uppercase tracking-wide text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-200">Selected</span>
            </div>
            <div class="mt-3">
              <h4 class="text-xs font-bold text-slate-900">Front Office Cash (Box 1)</h4>
              <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Front Desk &amp; Admission Counter</p>
              <span class="text-[10px] text-emerald-700 font-bold block mt-2">&rarr; Deposited to Cash Box 1</span>
            </div>
          </div>

          {{-- Option 3: Cash Box 2 (Accounts Dept) --}}
          <div @click="setAccount('cash_box_2', 'Cash')"
               class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between relative select-none"
               :class="paymentAccount === 'cash_box_2' ? 'border-blue-500 bg-blue-50/70 ring-2 ring-blue-100 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
            <div class="flex items-start justify-between">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center transition"
                   :class="paymentAccount === 'cash_box_2' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-600'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              </div>
              <span x-show="paymentAccount === 'cash_box_2'" class="text-[10px] font-extrabold uppercase tracking-wide text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full border border-blue-200">Selected</span>
            </div>
            <div class="mt-3">
              <h4 class="text-xs font-bold text-slate-900">Accounts Vault (Box 2)</h4>
              <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Accounts Department Vault</p>
              <span class="text-[10px] text-blue-700 font-bold block mt-2">&rarr; Deposited to Cash Box 2</span>
            </div>
          </div>
        </div>

        {{-- UPI UTR / Reference ID Field (Visible when UPI is active) --}}
        <div x-show="paymentAccount === 'upi'" x-transition class="p-4 bg-purple-50/70 rounded-2xl border border-purple-200/80 space-y-1.5">
          <label class="block text-xs font-bold text-slate-800">UPI Transaction ID / UTR Number <span class="text-slate-400 font-normal">(Optional)</span></label>
          <input type="text" id="transaction_id" name="transaction_id" x-model="upiRefNo" placeholder="e.g. 202612345678 or UPI UTR No."
                 class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-medium text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition outline-none">
          <p class="text-[11px] text-slate-500 font-medium">This transaction ID will be stored with the student fee receipt and matched in Account Management.</p>
        </div>

        {{-- Payment Date & Amount Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Date <span class="text-rose-500">*</span></label>
            <input type="date" id="payment_date" name="payment_date" x-model="paymentDate" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-700 font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Amount Collected Today (₹) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" id="amount_collected" name="amount_collected" required
                   :value="userAmountCollected !== null ? userAmountCollected : term1Amount"
                   @input="userAmountCollected = $event.target.value"
                   placeholder="0.00"
                   class="w-full px-4 py-3 rounded-xl border @error('amount_collected') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold font-mono transition text-slate-900">
            @error('amount_collected') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="amount_collected_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
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
          <input type="text" id="previous_school" name="previous_school" value="{{ old('previous_school') }}"
                 placeholder="School name"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Previous Class</label>
          <input type="text" id="previous_class" name="previous_class" value="{{ old('previous_class') }}"
                 placeholder="e.g. Class 5"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Percentage / CGPA</label>
          <input type="number" step="0.1" min="0" max="100" id="previous_percentage" name="previous_percentage" value="{{ old('previous_percentage') }}"
                 placeholder="0–100"
                 class="w-full px-4 py-3 rounded-xl border @error('previous_percentage') border-rose-400 @else border-slate-200 @enderror focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition text-slate-900 font-mono">
          @error('previous_percentage') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          <p id="previous_percentage_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
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

    {{-- ── Form Action Buttons & Approval Notice Footer ──────────────────────────── --}}
    <div class="bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-slate-50 p-6 rounded-3xl border border-blue-100/80 shadow-xs space-y-4 print:hidden">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <i class="fas fa-shield-alt text-base"></i>
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
              <span>2-Tier Institutional Admission Workflow</span>
              <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700">Governance Enabled</span>
            </h4>
            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
              Upon submission, this application will be sent to the <strong>Principal's Desk</strong> for initial verification. Once approved by the Principal, <strong>Admin final confirmation</strong> will enroll the student, activate their register entry, and unlock official ID Cards &amp; Transfer Certificates.
            </p>
          </div>
        </div>
      </div>

      <div class="pt-3 border-t border-blue-100/60 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <i class="fas fa-info-circle text-blue-500"></i>
          <span>Home upload QR &amp; Parents Visitor Card link will be generated immediately upon submission.</span>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
          <a href="{{ route('admissions.index') }}" class="px-5 h-11 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold border border-slate-300 text-sm transition shadow-xs flex items-center justify-center">
            Cancel
          </a>
          <button type="submit" class="px-6 h-11 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fas fa-paper-plane text-xs"></i>
            <span>Submit &amp; Request Principal Approval</span>
          </button>
        </div>
      </div>
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

<script>
  // Real-time phone badge updater
  function updatePhoneBadge(input, badgeId, isRequired = false) {
    const badge = document.getElementById(badgeId);
    if (!badge) return;
    const val = input.value.trim();
    const len = val.length;

    if (len === 0) {
      if (isRequired) {
        badge.innerHTML = '<span class="text-amber-600 font-bold">10 digits required</span>';
      } else {
        badge.innerHTML = '<span class="text-slate-400">10 digits (Optional)</span>';
      }
      input.classList.remove('border-rose-400', 'border-emerald-500', 'ring-2', 'ring-rose-100', 'ring-emerald-100');
      return;
    }

    if (len < 10) {
      badge.innerHTML = `<span class="text-blue-600 font-bold">${len}/10 digits</span>`;
      input.classList.remove('border-emerald-500', 'ring-emerald-100');
    } else if (len === 10) {
      if (/^[6-9]\d{9}$/.test(val)) {
        badge.innerHTML = '<span class="text-emerald-600 font-bold flex items-center gap-1"><i class="fas fa-check-circle"></i> Valid 10-digit number</span>';
        input.classList.remove('border-rose-400', 'ring-rose-100');
        input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-100');
      } else {
        badge.innerHTML = '<span class="text-rose-600 font-bold flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> Must start with 6, 7, 8, or 9</span>';
        input.classList.remove('border-emerald-500', 'ring-emerald-100');
        input.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
      }
    }
  }

  // Real-time Aadhaar badge updater
  function updateAadhaarBadge(input, badgeId) {
    const badge = document.getElementById(badgeId);
    if (!badge) return;
    const val = input.value.trim();
    const len = val.length;

    if (len === 0) {
      badge.innerHTML = '<span class="text-slate-400">12 digits (Optional)</span>';
      input.classList.remove('border-rose-400', 'border-emerald-500', 'ring-2', 'ring-rose-100', 'ring-emerald-100');
      return;
    }

    if (len < 12) {
      badge.innerHTML = `<span class="text-blue-600 font-bold">${len}/12 digits</span>`;
      input.classList.remove('border-emerald-500', 'ring-emerald-100');
    } else if (len === 12) {
      badge.innerHTML = '<span class="text-emerald-600 font-bold flex items-center gap-1"><i class="fas fa-check-circle"></i> Valid 12-digit Aadhaar</span>';
      input.classList.remove('border-rose-400', 'ring-rose-100');
      input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-100');
    }
  }

  // Real-time Pincode badge updater
  function updatePincodeBadge(input, badgeId) {
    const badge = document.getElementById(badgeId);
    if (!badge) return;
    const val = input.value.trim();
    const len = val.length;

    if (len === 0) {
      badge.innerHTML = '<span class="text-slate-400">6 digits (Optional)</span>';
      input.classList.remove('border-rose-400', 'border-emerald-500', 'ring-2', 'ring-rose-100', 'ring-emerald-100');
      return;
    }

    if (len < 6) {
      badge.innerHTML = `<span class="text-blue-600 font-bold">${len}/6 digits</span>`;
      input.classList.remove('border-emerald-500', 'ring-emerald-100');
    } else if (len === 6) {
      badge.innerHTML = '<span class="text-emerald-600 font-bold flex items-center gap-1"><i class="fas fa-check-circle"></i> Valid 6-digit PIN</span>';
      input.classList.remove('border-rose-400', 'ring-rose-100');
      input.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-100');
    }
  }

  // Initialize badges on page load if old inputs exist
  document.addEventListener('DOMContentLoaded', function() {
    const parentMobile = document.getElementById('parent_mobile');
    if (parentMobile && parentMobile.value) updatePhoneBadge(parentMobile, 'parent_mobile_badge', true);

    const motherMobile = document.getElementById('mother_mobile');
    if (motherMobile && motherMobile.value) updatePhoneBadge(motherMobile, 'mother_mobile_badge', false);

    const guardianMobile = document.getElementById('guardian_mobile');
    if (guardianMobile && guardianMobile.value) updatePhoneBadge(guardianMobile, 'guardian_mobile_badge', false);

    const aadhaarNo = document.getElementById('aadhaar_no');
    if (aadhaarNo && aadhaarNo.value) updateAadhaarBadge(aadhaarNo, 'aadhaar_no_badge');

    const fatherAadhaar = document.getElementById('father_aadhaar');
    if (fatherAadhaar && fatherAadhaar.value) updateAadhaarBadge(fatherAadhaar, 'father_aadhaar_badge');

    const pincode = document.getElementById('pincode');
    if (pincode && pincode.value) updatePincodeBadge(pincode, 'pincode_badge');

    // Comprehensive client-side form validation interceptor
    const form = document.getElementById('admissionCreateForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        let errors = [];
        let firstInvalidElement = null;

        // Reset previous client error highlights
        document.querySelectorAll('[id$="_client_err"]').forEach(el => {
          el.innerText = '';
          el.classList.add('hidden');
        });
        document.querySelectorAll('.border-rose-400').forEach(el => {
          el.classList.remove('border-rose-400', 'ring-2', 'ring-rose-100');
        });

        function markInvalid(inputEl, errElId, message) {
          errors.push(message);
          if (inputEl) {
            inputEl.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
            if (!firstInvalidElement) firstInvalidElement = inputEl;
          }
          const errEl = document.getElementById(errElId);
          if (errEl) {
            errEl.innerText = message;
            errEl.classList.remove('hidden');
          }
        }

        // 1. Student First Name
        const firstName = document.getElementById('first_name');
        if (!firstName || !firstName.value.trim()) {
          markInvalid(firstName, 'first_name_client_err', 'Student First Name is required.');
        }

        // 2. Class
        const classId = document.getElementById('class_id');
        if (!classId || !classId.value.trim()) {
          markInvalid(classId, 'class_id_client_err', 'Please select the Standard / Class applying for.');
        }

        // 3. Date of Birth (cannot be in the future)
        const dob = document.getElementById('dob');
        if (dob && dob.value) {
          const selectedDate = new Date(dob.value);
          const today = new Date();
          today.setHours(23, 59, 59, 999);
          if (selectedDate > today) {
            markInvalid(dob, 'dob_client_err', 'Date of Birth cannot be in the future.');
          }
        }

        // 4. Student Email (if filled)
        const email = document.getElementById('email');
        if (email && email.value.trim()) {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(email.value.trim())) {
            markInvalid(email, 'email_client_err', 'Please enter a valid student email address.');
          }
        }

        // 5. Student Aadhaar (if filled, must be 12 digits)
        const aadhaarNo = document.getElementById('aadhaar_no');
        if (aadhaarNo && aadhaarNo.value.trim()) {
          if (!/^\d{12}$/.test(aadhaarNo.value.trim())) {
            markInvalid(aadhaarNo, 'aadhaar_no_client_err', 'Student Aadhaar number must be exactly 12 numeric digits.');
          }
        }

        // 6. Pincode (if filled, must be 6 digits)
        const pincode = document.getElementById('pincode');
        if (pincode && pincode.value.trim()) {
          if (!/^[1-9]\d{5}$/.test(pincode.value.trim())) {
            markInvalid(pincode, 'pincode_client_err', 'Pincode must be a valid 6-digit postal code (e.g. 600001).');
          }
        }

        // 7. Father / Primary Parent Name
        const parentName = document.getElementById('parent_name');
        if (!parentName || !parentName.value.trim()) {
          markInvalid(parentName, 'parent_name_client_err', 'Father / Primary Parent Name is required.');
        }

        // 8. Father Mobile (REQUIRED: exactly 10 digits starting with 6-9)
        const parentMobile = document.getElementById('parent_mobile');
        if (!parentMobile || !parentMobile.value.trim()) {
          markInvalid(parentMobile, 'parent_mobile_client_err', 'Father Mobile Number is required (10 digits).');
        } else {
          const cleanMobile = parentMobile.value.replace(/\D/g, '');
          if (!/^[6-9]\d{9}$/.test(cleanMobile)) {
            markInvalid(parentMobile, 'parent_mobile_client_err', 'Father Mobile number must be a valid 10-digit Indian mobile starting with 6, 7, 8, or 9.');
          }
        }

        // 9. Father Email (if filled)
        const parentEmail = document.getElementById('parent_email');
        if (parentEmail && parentEmail.value.trim()) {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(parentEmail.value.trim())) {
            markInvalid(parentEmail, 'parent_email_client_err', 'Please enter a valid Father email address.');
          }
        }

        // 10. Father Aadhaar (if filled, must be 12 digits)
        const fatherAadhaar = document.getElementById('father_aadhaar');
        if (fatherAadhaar && fatherAadhaar.value.trim()) {
          if (!/^\d{12}$/.test(fatherAadhaar.value.trim())) {
            markInvalid(fatherAadhaar, 'father_aadhaar_client_err', 'Father Aadhaar number must be exactly 12 numeric digits.');
          }
        }

        // 11. Mother Mobile (if filled, must be 10 digits starting with 6-9)
        const motherMobile = document.getElementById('mother_mobile');
        if (motherMobile && motherMobile.value.trim()) {
          const cleanMother = motherMobile.value.replace(/\D/g, '');
          if (!/^[6-9]\d{9}$/.test(cleanMother)) {
            markInvalid(motherMobile, 'mother_mobile_client_err', 'Mother Mobile number must be a valid 10-digit mobile starting with 6, 7, 8, or 9.');
          }
        }

        // 12. Mother Email (if filled)
        const motherEmail = document.getElementById('mother_email');
        if (motherEmail && motherEmail.value.trim()) {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(motherEmail.value.trim())) {
            markInvalid(motherEmail, 'mother_email_client_err', 'Please enter a valid Mother email address.');
          }
        }

        // 13. Guardian Mobile (if filled, must be 10 digits starting with 6-9)
        const guardianMobile = document.getElementById('guardian_mobile');
        if (guardianMobile && guardianMobile.value.trim()) {
          const cleanGuardian = guardianMobile.value.replace(/\D/g, '');
          if (!/^[6-9]\d{9}$/.test(cleanGuardian)) {
            markInvalid(guardianMobile, 'guardian_mobile_client_err', 'Guardian Mobile number must be a valid 10-digit mobile starting with 6, 7, 8, or 9.');
          }
        }

        // 14. Amount Collected (must be non-negative)
        const amountCollected = document.getElementById('amount_collected');
        if (!amountCollected || amountCollected.value === '' || Number(amountCollected.value) < 0) {
          markInvalid(amountCollected, 'amount_collected_client_err', 'Amount Collected Today is required and cannot be negative (enter 0 if paying later).');
        }

        // 15. Previous Percentage (if filled, 0 to 100)
        const prevPct = document.getElementById('previous_percentage');
        if (prevPct && prevPct.value.trim() !== '') {
          const val = Number(prevPct.value);
          if (isNaN(val) || val < 0 || val > 100) {
            markInvalid(prevPct, 'previous_percentage_client_err', 'Previous Percentage / CGPA must be between 0 and 100.');
          }
        }

        // If any error exists:
        if (errors.length > 0) {
          e.preventDefault();

          // Render client error banner at top of form
          const clientAlert = document.getElementById('clientValidationAlert');
          const clientList = document.getElementById('clientValidationList');
          const clientTitle = document.getElementById('clientValidationTitle');
          if (clientAlert && clientList) {
            clientTitle.innerText = `Please correct the following (${errors.length}) issues before submitting:`;
            clientList.innerHTML = errors.map(msg => `<li>${msg}</li>`).join('');
            clientAlert.classList.remove('hidden');
          }

          // Smoothly scroll to the first invalid field
          if (firstInvalidElement) {
            firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
              firstInvalidElement.focus();
            }, 300);
          } else if (clientAlert) {
            clientAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    }
  });
</script>
@endsection
