@extends('layouts.app')

@section('title', 'New Student Admission Form — Erode Public School (' . ($academicYear?->name ?? '2026-2027') . ')')

@push('head')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --school-maroon: #8C2826;
      --school-maroon-dark: #731E1C;
      --school-maroon-light: #FFF5F5;
      --school-maroon-border: #FECACA;
      --school-gold: #C8973A;
      --school-gold-light: #FEF9EE;
      --school-gold-border: #FDE68A;
    }
    .admission-form-wrapper {
      font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
    }
    .admission-form-wrapper input,
    .admission-form-wrapper select,
    .admission-form-wrapper textarea,
    .admission-form-wrapper button {
      font-family: 'Inter', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
      -webkit-font-smoothing: antialiased;
    }
    .admission-form-wrapper h1,
    .admission-form-wrapper h2,
    .admission-form-wrapper h3,
    .admission-form-wrapper h4,
    .admission-form-wrapper .step-badge {
      font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
      letter-spacing: -0.015em;
      -webkit-font-smoothing: antialiased;
    }
    .admission-form-wrapper .tabular-nums {
      font-variant-numeric: tabular-nums;
      font-feature-settings: "tnum" 1;
    }
    .step-badge {
      display: inline-flex !important;
      align-items: center !important;
      gap: 0.35rem !important;
      font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif !important;
      font-size: 0.75rem !important;
      font-weight: 700 !important;
      letter-spacing: 0.01em !important;
      background-color: #FFF5F5 !important;
      color: #8C2826 !important;
      border: 1px solid #FECACA !important;
      padding: 0.35rem 0.85rem !important;
      border-radius: 9999px !important;
      box-shadow: 0 1px 2px rgba(140, 40, 38, 0.05) !important;
    }
    .admission-form-wrapper input:focus,
    .admission-form-wrapper select:focus,
    .admission-form-wrapper textarea:focus {
      border-color: #8C2826 !important;
      --tw-ring-color: rgba(140, 40, 38, 0.18) !important;
    }
  </style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6 admission-form-wrapper" x-data="{
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
  hasSibling: {{ old('has_sibling') || old('sibling_name') || old('sibling_admission_no') || old('sibling_roll_no') ? 'true' : 'false' }},
  siblingRollNo: '{{ old("sibling_roll_no", old("sibling_admission_no", "")) }}',
  siblingName: '{{ old("sibling_name", "") }}',
  siblingClass: '{{ old("sibling_class", "") }}',
  siblingAdmissionNo: '{{ old("sibling_admission_no", "") }}',
  siblingLookupStatus: '', // '', 'loading', 'found', 'not_found', 'error'
  siblingLookupMessage: '',

  async fetchSiblingDetails() {
    let roll = (this.siblingRollNo || '').trim();
    if (!roll) {
      this.siblingName = '';
      this.siblingClass = '';
      this.siblingAdmissionNo = '';
      this.siblingLookupStatus = '';
      this.siblingLookupMessage = '';
      return;
    }
    this.siblingLookupStatus = 'loading';
    this.siblingLookupMessage = 'Fetching student details...';
    try {
      let res = await fetch(`{{ route('admissions.sibling-lookup') }}?roll_number=${encodeURIComponent(roll)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      let data = await res.json();
      if (data.found && data.student) {
        this.siblingName = data.student.name;
        this.siblingClass = data.student.class_section;
        this.siblingAdmissionNo = data.student.admission_no || data.student.roll_number;
        this.siblingLookupStatus = 'found';
        this.siblingLookupMessage = `Found: ${data.student.name} (${data.student.class_section})`;
      } else {
        this.siblingName = '';
        this.siblingClass = '';
        this.siblingAdmissionNo = '';
        this.siblingLookupStatus = 'not_found';
        this.siblingLookupMessage = data.message || 'No student found with this Roll No.';
      }
    } catch (e) {
      this.siblingLookupStatus = 'error';
      this.siblingLookupMessage = 'Error connecting to student database.';
    }
  },

  // Extra Curricular Activities (ECA) — Complimentary per school requirement
  selectedActivities: [],
  additionalItems: {},

  // Live Student & Parent Photo Previews
  formsDropdownOpen: false,
  photoPreview: null,
  photoFileName: '',
  handlePhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.photoPreview = URL.createObjectURL(file);
      this.photoFileName = file.name;
    }
  },
  clearPhoto() {
    this.photoPreview = null;
    this.photoFileName = '';
    const input = document.getElementById('student_photo_input');
    if (input) input.value = '';
  },
  fatherPhotoPreview: null,
  fatherPhotoFileName: '',
  handleFatherPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.fatherPhotoPreview = URL.createObjectURL(file);
      this.fatherPhotoFileName = file.name;
    }
  },
  motherPhotoPreview: null,
  motherPhotoFileName: '',
  handleMotherPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.motherPhotoPreview = URL.createObjectURL(file);
      this.motherPhotoFileName = file.name;
    }
  },
  guardianPhotoPreview: null,
  guardianPhotoFileName: '',
  handleGuardianPhotoChange(event) {
    const file = event.target.files[0];
    if (file) {
      this.guardianPhotoPreview = URL.createObjectURL(file);
      this.guardianPhotoFileName = file.name;
    }
  },
  docUploadMode: 'desk',

  // Multi-Format Date of Birth State (Day, Month, Year selectors & DD/MM/YYYY text entry)
  dobMode: 'dropdown',
  dobDay: '{{ old("dob") ? date("d", strtotime(old("dob"))) : "" }}',
  dobMonth: '{{ old("dob") ? date("m", strtotime(old("dob"))) : "" }}',
  dobYear: '{{ old("dob") ? date("Y", strtotime(old("dob"))) : "" }}',
  dobText: '{{ old("dob") ? date("d/m/Y", strtotime(old("dob"))) : "" }}',
  handleDobTextInput(val) {
    let clean = val.replace(/\D/g, '').slice(0, 8);
    let formatted = clean;
    if (clean.length >= 3 && clean.length <= 4) {
      formatted = clean.slice(0, 2) + '/' + clean.slice(2);
    } else if (clean.length > 4) {
      formatted = clean.slice(0, 2) + '/' + clean.slice(2, 4) + '/' + clean.slice(4);
    }
    this.dobText = formatted;
    if (clean.length === 8) {
      this.dobDay = clean.slice(0, 2);
      this.dobMonth = clean.slice(2, 4);
      this.dobYear = clean.slice(4, 8);
    }
  },
  get computedDob() {
    if (this.dobDay && this.dobMonth && this.dobYear) {
      let d = String(this.dobDay).padStart(2, '0');
      let m = String(this.dobMonth).padStart(2, '0');
      let y = String(this.dobYear);
      return `${y}-${m}-${d}`;
    }
    return '';
  },
  get calculatedAge() {
    if (!this.computedDob) return null;
    let parts = this.computedDob.split('-');
    if (parts.length !== 3) return null;
    let birth = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    if (isNaN(birth.getTime())) return null;
    let now = new Date();
    let years = now.getFullYear() - birth.getFullYear();
    let months = now.getMonth() - birth.getMonth();
    if (months < 0 || (months === 0 && now.getDate() < birth.getDate())) {
      years--;
      months += 12;
    }
    if (years < 0) return null;
    return `${years} yrs ${months} mos`;
  },

  // Religion with Custom Options
  selectedReligion: '{{ in_array(old("religion"), ["Hindu", "Christian", "Muslim", "Jain", "Sikh", "Buddhist", "Parsi"]) ? old("religion") : (old("religion") ? "Other" : "") }}',
  customReligion: '{{ !in_array(old("religion"), ["Hindu", "Christian", "Muslim", "Jain", "Sikh", "Buddhist", "Parsi", ""]) ? old("religion") : "" }}',
  get effectiveReligion() {
    if (this.selectedReligion === 'Other') return this.customReligion;
    return this.selectedReligion;
  },

  // Mother Tongue with Custom Options
  selectedMotherTongue: '{{ in_array(old("mother_tongue"), ["Tamil", "English", "Hindi", "Telugu", "Malayalam", "Kannada", "Urdu", "Gujarati", "Marathi", "Bengali"]) ? old("mother_tongue") : (old("mother_tongue") ? "Other" : "") }}',
  customMotherTongue: '{{ !in_array(old("mother_tongue"), ["Tamil", "English", "Hindi", "Telugu", "Malayalam", "Kannada", "Urdu", "Gujarati", "Marathi", "Bengali", ""]) ? old("mother_tongue") : "" }}',
  get effectiveMotherTongue() {
    if (this.selectedMotherTongue === 'Other') return this.customMotherTongue;
    return this.selectedMotherTongue;
  },

  // Student Wearable Sizes (Dress & Shoe)
  selectedDressSize: '{{ old("dress_size", "") }}',
  customDressSize: '',
  get effectiveDressSize() {
    if (this.selectedDressSize === 'Other') return this.customDressSize;
    return this.selectedDressSize;
  },
  selectedShoeSize: '{{ old("shoe_size", "") }}',
  customShoeSize: '',
  get effectiveShoeSize() {
    if (this.selectedShoeSize === 'Other') return this.customShoeSize;
    return this.selectedShoeSize;
  },

  // Second Language Textbook & Curriculum Pack
  secondLanguage: '{{ old("second_language", "Tamil") }}',
  customSecondLanguage: '',
  get effectiveSecondLanguage() {
    if (this.secondLanguage === 'Other') return this.customSecondLanguage;
    return this.secondLanguage;
  },
  textbookPack: 'CBSE Standard NCERT Package',
  textbookNotes: '',

  // Custom Admission Kit & Textbooks Dynamic Items
  customKitItems: [],
  addCustomKitItem() {
    this.customKitItems.push({
      id: Date.now(),
      name: '',
      category: 'Textbook',
      specification: '',
      quantity: 1,
      unit_price: 0
    });
  },
  removeCustomKitItem(idx) {
    this.customKitItems.splice(idx, 1);
  },

  // Payment Terms & Account Destination State
  paymentTerms: '3_terms',
  paymentMode: 'UPI',
  paymentAccount: 'upi',
  upiRefNo: '',
  setAccount(account, mode) {
    this.paymentAccount = account;
    this.paymentMode = mode;
  },
  paymentDate: '{{ date("Y-m-d") }}',
  term2DueDate: '2026-08-05',
  term3DueDate: '2026-12-05',
  userAmountCollected: null,
  isIntegrated: false,
  includeAdmissionFee: true,

  get availableSections() {
    if (!this.selectedClassId) return [];
    return this.sectionsList.filter(s => s.class_id == this.selectedClassId);
  },
  get selectedFee() {
    return this.standardFees[this.selectedClassId] || null;
  },
  get activeFeeSchedule() {
    if (!this.selectedFee) return null;
    if (this.isIntegrated && this.selectedFee.has_integrated) {
      return {
        term1_fee: Number(this.selectedFee.integrated_term1 || 52500),
        term2_fee: Number(this.selectedFee.integrated_term2 || 32500),
        term3_fee: Number(this.selectedFee.integrated_term3 || 20000),
        material_fee: 0,
        admission_fee: Number(this.selectedFee.admission_fee || 2500),
        total_basic: Number(this.selectedFee.integrated_fee || 105000),
        tier: this.selectedFee.class_name + ' (Integrated Coaching)',
        official_name: this.selectedFee.official_name + ' (INTEGRATED)',
        has_integrated: true
      };
    }
    return this.selectedFee;
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
  get customKitFeeTotal() {
    return this.customKitItems.reduce((sum, item) => {
      let q = Number(item.quantity) || 0;
      let p = Number(item.unit_price) || 0;
      return sum + (q * p);
    }, 0);
  },
  get totalKitFee() {
    return this.standardKitFeeTotal + this.additionalInventoryFeeTotal + this.customKitFeeTotal;
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
  selectedStreamGroup: '{{ old("stream_group", "Group A: English, Mathematics, Physics, Chemistry, Biology / CS") }}',
  get classStage() {
    if (!this.selectedClassId) return '';
    let name = (this.selectedClassName || '').toUpperCase().trim();
    if (name.includes('PRE') || name.includes('LKG') || name.includes('UKG') || name.includes('KG')) {
      return 'kindergarten';
    }
    if (['XI', 'XII', '11', '12', 'GRADE XI', 'GRADE XII', 'CLASS XI', 'CLASS XII', '11TH', '12TH'].includes(name) || name.includes('11') || name.includes('12')) {
      return 'senior_secondary';
    }
    if (['IX', 'X', '9', '10', 'GRADE IX', 'GRADE X', 'CLASS IX', 'CLASS X', '9TH', '10TH'].includes(name)) {
      return 'secondary';
    }
    if (['VI', 'VII', 'VIII', '6', '7', '8', 'GRADE VI', 'GRADE VII', 'GRADE VIII'].includes(name)) {
      return 'middle';
    }
    if (['I', 'II', 'III', 'IV', 'V', '1', '2', '3', '4', '5', 'GRADE I', 'GRADE II', 'GRADE III', 'GRADE IV', 'GRADE V'].includes(name)) {
      return 'primary';
    }
    let numMatch = name.match(/\d+/);
    if (numMatch) {
      let n = parseInt(numMatch[0]);
      if (n === 11 || n === 12) return 'senior_secondary';
      if (n >= 9 && n <= 10) return 'secondary';
      if (n >= 6 && n <= 8) return 'middle';
      if (n >= 1 && n <= 5) return 'primary';
    }
    if (this.selectedFee && this.selectedFee.has_integrated) return 'senior_secondary';
    return 'general';
  },
  get isSeniorSecondary() {
    return this.classStage === 'senior_secondary' || (this.selectedFee && this.selectedFee.has_integrated);
  },
  get isSecondary() {
    return this.classStage === 'secondary';
  },
  get isMiddle() {
    return this.classStage === 'middle';
  },
  get isPrimary() {
    return this.classStage === 'primary';
  },
  get isKindergarten() {
    return this.classStage === 'kindergarten';
  },
  get basicFeeTotal() {
    return this.activeFeeSchedule ? (Number(this.activeFeeSchedule.total_basic) || 0) : 0;
  },
  get admissionFeeTotal() {
    if (!this.includeAdmissionFee || !this.activeFeeSchedule) return 0;
    return Number(this.activeFeeSchedule.admission_fee || 0);
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
    return this.basicFeeTotal + this.admissionFeeTotal + this.aspFeeTotal + this.transportFee + this.totalKitFee;
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

  // Term amounts calculations: follows official Term 1, Term 2, Term 3 fee breakdown
  get term1Amount() {
    if (this.paymentTerms === 'single') return this.grandTotal;
    if (this.paymentTerms === '2_terms') {
      let remT1 = Math.round(this.remainingFeesTotal / 2);
      return this.totalKitFee + remT1;
    }
    if (this.paymentTerms === '3_terms') {
      let baseT1 = Number(this.activeFeeSchedule?.term1_fee || 0) + this.admissionFeeTotal;
      let facilityShare = Math.round((this.aspFeeTotal + this.transportFee) / 3);
      let discShare = Math.round(this.concessionAmount / 3);
      return Math.max(0, baseT1 + this.totalKitFee + facilityShare - discShare);
    }
    return this.grandTotal;
  },
  get term2Amount() {
    if (this.paymentTerms === 'single') return 0;
    if (this.paymentTerms === '2_terms') return this.grandTotal - this.term1Amount;
    if (this.paymentTerms === '3_terms') {
      let baseT2 = Number(this.activeFeeSchedule?.term2_fee || 0);
      let facilityShare = Math.round((this.aspFeeTotal + this.transportFee) / 3);
      let discShare = Math.round(this.concessionAmount / 3);
      return Math.max(0, baseT2 + facilityShare - discShare);
    }
    return 0;
  },
  get term3Amount() {
    if (this.paymentTerms === '3_terms') {
      return Math.max(0, this.grandTotal - (this.term1Amount + this.term2Amount));
    }
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
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 transition border border-slate-200/90 shadow-2xs group" title="Back to Admissions">
        <svg class="w-4 h-4 text-slate-600 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">New Student Admission Form</h1>
        <p class="text-xs text-slate-500 font-medium flex flex-wrap items-center gap-1.5 mt-0.5">
          <span class="font-semibold text-slate-700">Erode Public School</span>
          <span>&bull;</span>
          <span class="text-slate-500">CBSE Affiliated 1931585</span>
          <span>&bull;</span>
          <span class="text-[#8C2826] font-bold bg-[#FFF5F5] px-2.5 py-0.5 rounded-lg border border-[#FECACA]">Academic Year: {{ $academicYear?->name ?? '2026-2027' }}</span>
        </p>
      </div>
    </div>

    {{-- Consolidated Official Blank Application Forms Dropdown (PDF 1, 2, 3) --}}
    <div class="relative" @click.away="formsDropdownOpen = false">
      <button type="button" @click="formsDropdownOpen = !formsDropdownOpen"
              class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/90 text-slate-700 text-xs font-bold shadow-2xs hover:border-slate-300 transition cursor-pointer">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Blank Forms</span>
        <svg class="w-3.5 h-3.5 text-slate-400 transition transform" :class="formsDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>

      <div x-show="formsDropdownOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
           class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-200/90 shadow-xl p-1.5 z-40 space-y-1">
        <a href="{{ route('admissions.print-form', ['form' => 'admission']) }}" target="_blank"
           class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition group">
          <span class="w-6 h-6 rounded-lg bg-[#FFF5F5] text-[#8C2826] flex items-center justify-center text-xs group-hover:bg-[#8C2826] group-hover:text-white transition">📄</span>
          <div>
            <div class="font-bold text-slate-900">General Admission Form</div>
            <div class="text-[10px] text-slate-400 font-normal">Pre-KG to Class X (PDF 1)</div>
          </div>
        </a>
        <a href="{{ route('admissions.print-form', ['form' => 'grade11']) }}" target="_blank"
           class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition group">
          <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs group-hover:bg-[#8C2826] group-hover:text-white transition">🎓</span>
          <div>
            <div class="font-bold text-slate-900">Grade XI Application Form</div>
            <div class="text-[10px] text-slate-400 font-normal">Streams &amp; Board Norms (PDF 2)</div>
          </div>
        </a>
        <a href="{{ route('admissions.print-form', ['form' => 'enquiry']) }}" target="_blank"
           class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition group">
          <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover:bg-[#C8973A] group-hover:text-white transition">📝</span>
          <div>
            <div class="font-bold text-slate-900">Walk-in Enquiry Form</div>
            <div class="text-[10px] text-slate-400 font-normal">Counseling &amp; Visit Log (PDF 3)</div>
          </div>
        </a>
      </div>
    </div>
  </div>

  {{-- Print Header --}}
  <div class="hidden print:block border-b-2 border-red-700 pb-3 mb-6">
    <h1 class="text-3xl font-black text-red-700 tracking-wider uppercase font-school-title">ERODE PUBLIC SCHOOL</h1>
    <p class="text-xs font-bold text-slate-800 uppercase tracking-widest mt-0.5">Senior Secondary Affiliated to CBSE, New Delhi (Aff.No.1931585)</p>
    <p class="text-xs text-slate-600 font-medium mt-0.5">Academic Year: {{ $academicYear?->name ?? '2026-2027' }} &bull; Date: {{ date('d-m-Y') }}</p>
  </div>

  {{-- Modern Section Navigator --}}
  <div class="bg-white/95 backdrop-blur-md px-3 py-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-2 overflow-x-auto print:hidden sticky top-3 z-30">
    <a href="#section-student" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-3.5 py-2 rounded-xl hover:bg-[#FFF5F5] transition group border border-transparent hover:border-[#FECACA]">
      <span class="w-7 h-7 rounded-xl bg-[#8C2826] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">1</span>
      <div class="min-w-0">
        <span class="font-bold text-slate-800 group-hover:text-[#8C2826] text-xs block truncate transition">Student &amp; Class</span>
        <span class="text-[10px] text-slate-400 block truncate">Profile, Kit &amp; Identity</span>
      </div>
    </a>

    <div class="h-4 w-px bg-slate-200 shrink-0"></div>

    <a href="#section-parents" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-3.5 py-2 rounded-xl hover:bg-[#FFF5F5] transition group border border-transparent hover:border-[#FECACA]">
      <span class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 group-hover:bg-[#8C2826] group-hover:text-white font-bold text-xs flex items-center justify-center shrink-0 transition">2</span>
      <div class="min-w-0">
        <span class="font-bold text-slate-700 group-hover:text-[#8C2826] text-xs block truncate transition">Parents &amp; Transport</span>
        <span class="text-[10px] text-slate-400 block truncate">Family, ASP &amp; Bus</span>
      </div>
    </a>

    <div class="h-4 w-px bg-slate-200 shrink-0"></div>

    <a href="#section-billing" class="flex-1 min-w-[150px] flex items-center gap-2.5 px-3.5 py-2 rounded-xl hover:bg-[#FEF9EE] transition group border border-transparent hover:border-[#FDE68A]">
      <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-[#C8973A] group-hover:text-white font-bold text-xs flex items-center justify-center shrink-0 transition">3</span>
      <div class="min-w-0">
        <span class="font-bold text-slate-700 group-hover:text-[#C8973A] text-xs block truncate transition">Fees &amp; Payment</span>
        <span class="text-[10px] text-slate-400 block truncate">Fee Schedule &amp; Billing</span>
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
        <div>
          <h2 class="text-base font-bold text-slate-900">Student Information</h2>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Personal profile, date of birth, and class enrollment</p>
        </div>
        <span class="step-badge text-xs font-bold text-[#8C2826] bg-[#FFF5F5] px-3.5 py-1 rounded-full border border-[#FECACA] shadow-2xs">Step 1 of 3</span>
      </div>

      <div class="space-y-4">
        {{-- Student Passport Photograph Upload --}}
        <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-2xl bg-gradient-to-r from-[#FFF5F5] via-slate-50/70 to-white border border-[#FECACA] shadow-2xs">
          <div class="relative group shrink-0">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white border-2 border-dashed border-[#FCA5A5] flex items-center justify-center overflow-hidden shadow-inner transition group-hover:border-[#8C2826]">
              <template x-if="photoPreview">
                <img :src="photoPreview" class="w-full h-full object-cover" alt="Student Photo Preview">
              </template>
              <template x-if="!photoPreview">
                <div class="text-center p-2 text-slate-400">
                  <svg class="w-8 h-8 mx-auto text-[#8C2826] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <span class="text-[10px] font-bold uppercase tracking-wider block">Student Photo</span>
                </div>
              </template>
            </div>
            <template x-if="photoPreview">
              <button type="button" @click="clearPhoto()" title="Remove photo"
                      class="absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold flex items-center justify-center shadow-xs transition cursor-pointer">
                ✕
              </button>
            </template>
          </div>

          <div class="flex-1 space-y-2 text-center sm:text-left">
            <div>
              <div class="flex items-center justify-center sm:justify-start gap-2">
                <label class="block text-xs font-bold text-slate-800">Student Passport Photograph</label>
                <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Optional</span>
              </div>
              <p class="text-[11px] text-slate-500 font-medium mt-0.5">Upload recent formal photograph of the student (JPEG, PNG, max 3MB). Will appear on the student profile, ID card, and registers.</p>
            </div>

            {{-- Hidden native file input --}}
            <input type="file" id="student_photo_input" x-ref="studentPhotoInput" name="photo" accept="image/*"
                   @change="handlePhotoChange($event)" class="hidden">

            {{-- Custom Upload Trigger & File Pill --}}
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-0.5">
              <button type="button" @click="$refs.studentPhotoInput.click()"
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#8C2826] hover:bg-[#731E1C] active:bg-[#5C1210] text-white font-bold text-xs shadow-xs hover:shadow transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span x-text="photoPreview ? 'Change Photo' : 'Choose Photo'"></span>
              </button>

              <template x-if="photoFileName">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-medium shadow-2xs max-w-[240px]">
                  <span class="truncate" x-text="photoFileName"></span>
                  <button type="button" @click="clearPhoto()" class="text-slate-400 hover:text-rose-600 font-bold ml-1 cursor-pointer" title="Remove">✕</button>
                </div>
              </template>
            </div>

            @error('photo') <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Row 1: First Name & Last Name (2 columns) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
            <input type="text" id="first_name" name="first_name" required maxlength="50" value="{{ old('first_name') }}"
                   placeholder="Student First Name (e.g. Akash)"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900 bg-white">
            @error('first_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="first_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name / Initial</label>
            <input type="text" id="last_name" name="last_name" maxlength="50" value="{{ old('last_name') }}"
                   placeholder="Surname or Initial (e.g. A R)"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900 bg-white">
            @error('last_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="last_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>

        {{-- Row 2: Date of Birth, Gender, Enquiry Source (3 balanced columns) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Date of Birth <span class="text-rose-500">*</span></label>
              <div class="flex items-center gap-1.5">
                <span x-show="calculatedAge" class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-2 py-0.5 rounded-full border border-[#FECACA] shadow-2xs" x-text="'Age: ' + calculatedAge"></span>
                <button type="button" @click="dobMode = (dobMode === 'dropdown' ? 'text' : 'dropdown')"
                        class="text-[10px] text-[#8C2826] hover:text-[#731E1C] font-semibold cursor-pointer underline">
                  <span x-text="dobMode === 'dropdown' ? 'Type DD/MM/YYYY' : 'Select Day/Mo/Yr'"></span>
                </button>
              </div>
            </div>

            {{-- Mode 1: 3 Dropdown Selectors (Day, Month, Year) --}}
            <div x-show="dobMode === 'dropdown'" class="grid grid-cols-3 gap-1.5">
              <div>
                <select x-model="dobDay"
                        class="w-full px-2 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-semibold text-slate-700 bg-white">
                  <option value="">Day</option>
                  @for($d = 1; $d <= 31; $d++)
                    @php $dStr = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $dStr }}">{{ $dStr }}</option>
                  @endfor
                </select>
              </div>

              <div>
                <select x-model="dobMonth"
                        class="w-full px-2 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-semibold text-slate-700 bg-white">
                  <option value="">Month</option>
                  @foreach([
                    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
                    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                  ] as $mNum => $mName)
                    <option value="{{ $mNum }}">{{ $mName }} ({{ $mNum }})</option>
                  @endforeach
                </select>
              </div>

              <div>
                <select x-model="dobYear"
                        class="w-full px-2 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-semibold text-slate-700 bg-white">
                  <option value="">Year</option>
                  @for($y = 2025; $y >= 2004; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                  @endfor
                </select>
              </div>
            </div>

            {{-- Mode 2: Direct Text Input (DD/MM/YYYY) --}}
            <div x-show="dobMode === 'text'" class="relative">
              <input type="text" :value="dobText" @input="handleDobTextInput($event.target.value)"
                     placeholder="DD/MM/YYYY (e.g. 15/08/2018)" maxlength="10"
                     class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition bg-white">
            </div>

            {{-- Hidden synchronised input for server validation --}}
            <input type="hidden" id="dob" name="dob" :value="computedDob">
            @error('dob') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="dob_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender <span class="text-rose-500">*</span></label>
            <select id="gender" name="gender" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
              <option value="">Select gender</option>
              <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Enquiry Source</label>
            <select name="source" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
              <option value="Walk-in">Walk-in</option>
              <option value="Online">Online / Website</option>
              <option value="Referral">Parent Referral</option>
              <option value="Advertisement">Newspaper / Social Media</option>
            </select>
          </div>
        </div>

        {{-- Row 3: Standard & Section Selector --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Applying for Standard / Class <span class="text-rose-500">*</span></label>
            <select id="class_id" name="class_id" required x-model="selectedClassId" @change="selectedSectionId = ''"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
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
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
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

        {{-- Live Dynamic Official 2026-2027 Fee Card for Selected Class --}}
        <div x-show="selectedClassId && selectedFee" x-transition class="p-4 rounded-2xl bg-gradient-to-r from-[#FFF5F5] via-[#FEF9EE]/70 to-slate-50 border border-[#FECACA]/90 space-y-3 shadow-2xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-[#8C2826] text-white shadow-xs flex items-center justify-center font-black text-sm shadow-xs">₹</span>
              <div>
                <div class="flex items-center gap-2">
                  <span class="text-xs font-black text-slate-900 uppercase tracking-wide" x-text="'Official Fees for ' + (selectedFee?.official_name || ('Class ' + selectedClassName))"></span>
                  <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-[#FFF5F5] text-[#5C1210] border border-[#FECACA]">2026–2027</span>
                </div>
                <span class="text-[11px] text-slate-500 font-medium block">Approved term-wise fee schedule</span>
              </div>
            </div>

            {{-- Integrated Coaching Toggle for Grade XI / XII --}}
            <div x-show="selectedFee?.has_integrated" class="flex items-center gap-1 p-1 bg-white rounded-xl border border-[#FECACA] shadow-2xs">
              <button type="button" @click="isIntegrated = false"
                      :class="!isIntegrated ? 'bg-[#8C2826] text-white shadow-xs shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                      class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                Standard (₹80,000)
              </button>
              <button type="button" @click="isIntegrated = true"
                      :class="isIntegrated ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                      class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-1">
                <span>Integrated (₹1,05,000)</span>
                <span class="text-[9px] bg-amber-400 text-slate-950 font-black px-1 rounded">NEET/JEE</span>
              </button>
            </div>
          </div>

          {{-- 4 Term Breakdown Stat Cards --}}
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            <div class="bg-white p-2.5 rounded-xl border border-[#FECACA]">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">April I Term</span>
              <span class="font-bold text-slate-900 text-sm tabular-nums" x-text="formatMoney(activeFeeSchedule?.term1_fee)"></span>
            </div>
            <div class="bg-white p-2.5 rounded-xl border border-[#FECACA]">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Aug II Term</span>
              <span class="font-bold text-slate-900 text-sm tabular-nums" x-text="formatMoney(activeFeeSchedule?.term2_fee)"></span>
            </div>
            <div class="bg-white p-2.5 rounded-xl border border-[#FECACA]">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Dec III Term</span>
              <span class="font-bold text-slate-900 text-sm tabular-nums" x-text="formatMoney(activeFeeSchedule?.term3_fee)"></span>
            </div>
            <div class="bg-[#8C2826] text-white shadow-xs p-2.5 rounded-xl shadow-xs">
              <span class="text-[10px] uppercase font-bold text-amber-200 block">Total Academic Fee</span>
              <span class="font-extrabold text-white text-sm tabular-nums" x-text="formatMoney(activeFeeSchedule?.total_basic)"></span>
            </div>
          </div>

          {{-- Extra Notes: Admission fee & Material fee --}}
          <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-[#FECACA]/50 text-[11px] text-slate-600">
            <div class="flex items-center gap-3">
              <span class="font-bold text-[#8C2826]" x-show="activeFeeSchedule?.admission_fee > 0">
                &bull; Admission Fee: Rs.2,500/- extra for new admissions
              </span>
              <span class="font-bold text-purple-700" x-show="activeFeeSchedule?.material_fee > 0">
                &bull; Material Fee: Rs.19,500/- (Due: 10.01.2026)
              </span>
            </div>
            <span class="text-slate-500 font-medium text-[10px]">
              Due Dates: T1: 01.04.2026 &bull; T2: 05.08.2026 &bull; T3: 05.12.2026
            </span>
          </div>
        </div>

        {{-- Hidden Inputs for Server Submission --}}
        <input type="hidden" name="is_integrated" :value="isIntegrated ? 1 : 0">
        <input type="hidden" name="include_admission_fee" :value="includeAdmissionFee ? 1 : 0">
        <input type="hidden" name="admission_fee_amount" :value="admissionFeeTotal">

        {{-- Standard-Specific Section 1: Senior Secondary Stream / Group (Grade XI & XII) --}}
        <div x-show="isSeniorSecondary" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 rounded-3xl bg-gradient-to-br from-[#FFF5F5] via-[#FEF9EE]/70 to-slate-50 border-2 border-[#FECACA] space-y-4 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#FECACA] pb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-[#8C2826] text-white shadow-xs flex items-center justify-center font-black text-sm shadow-xs">🎓</span>
              <div>
                <label class="block text-xs font-black text-[#380E0D] uppercase tracking-wide">
                  Senior Secondary Stream / Group (Grade XI &amp; XII)
                </label>
                <p class="text-[11px] text-slate-500 font-medium">Official CBSE curriculum specialization group for Class XI / XII admission</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-bold text-[#8C2826] bg-white px-2.5 py-1 rounded-full border border-[#FECACA] shadow-2xs">Official CBSE Curriculum</span>
              <a href="{{ route('admissions.print-form', ['form' => 'grade11']) }}" target="_blank"
                 class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-[#8C2826] hover:bg-[#731E1C] text-white font-bold text-[11px] shadow-xs transition" title="Print Official Grade XI Admission Form (PDF 2)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Grade XI Form</span>
              </a>
            </div>
          </div>

          {{-- The 4 Stream Options with interactive radio cards --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            {{-- Group A --}}
            <label class="p-3.5 rounded-2xl border transition flex items-start gap-3 cursor-pointer shadow-2xs"
                   :class="selectedStreamGroup.includes('Group A') ? 'border-[#8C2826] bg-white ring-2 ring-[#8C2826]/20 shadow-xs' : 'border-slate-200 bg-white/80 hover:border-[#FECACA]'">
              <input type="radio" name="stream_group" x-model="selectedStreamGroup" value="Group A: English, Mathematics, Physics, Chemistry, Biology / CS" class="mt-1 text-[#8C2826] focus:ring-[#8C2826]">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                  <span class="font-extrabold text-slate-900 text-xs">Science Stream (Group A)</span>
                  <span class="text-[9px] font-bold bg-[#FFF5F5] text-[#8C2826] border border-[#FECACA] px-2 py-0.5 rounded tracking-wide">ENGG / RESEARCH</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">English, Mathematics, Physics, Chemistry, Biology / Computer Science</p>
              </div>
            </label>

            {{-- Group B --}}
            <label class="p-3.5 rounded-2xl border transition flex items-start gap-3 cursor-pointer shadow-2xs"
                   :class="selectedStreamGroup.includes('Group B') ? 'border-slate-300 bg-white ring-2 ring-[#8C2826]/20 shadow-xs' : 'border-slate-200 bg-white/80 hover:border-[#FECACA]'">
              <input type="radio" name="stream_group" x-model="selectedStreamGroup" value="Group B: English, Physics, Chemistry, Biology, Mathematics / CS" class="mt-1 text-[#8C2826] focus:ring-[#8C2826]">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                  <span class="font-extrabold text-slate-900 text-xs">Science Stream (Group B)</span>
                  <span class="text-[9px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded tracking-wide">NEET / MEDICAL</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">English, Physics, Chemistry, Biology, Mathematics / Computer Science</p>
              </div>
            </label>

            {{-- Group C --}}
            <label class="p-3.5 rounded-2xl border transition flex items-start gap-3 cursor-pointer shadow-2xs"
                   :class="selectedStreamGroup.includes('Group C') ? 'border-slate-300 bg-white ring-2 ring-[#8C2826]/20 shadow-xs' : 'border-slate-200 bg-white/80 hover:border-[#FECACA]'">
              <input type="radio" name="stream_group" x-model="selectedStreamGroup" value="Group C: English, Business Studies / Entr, Accountancy, Economics, CS" class="mt-1 text-[#8C2826] focus:ring-[#8C2826]">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                  <span class="font-extrabold text-slate-900 text-xs">Commerce Stream (Group C)</span>
                  <span class="text-[9px] font-bold bg-amber-100 text-amber-900 px-2 py-0.5 rounded tracking-wide">CA / BUSINESS / CS</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">English, Business Studies / Entr, Accountancy, Economics, Computer Science</p>
              </div>
            </label>

            {{-- Group D --}}
            <label class="p-3.5 rounded-2xl border transition flex items-start gap-3 cursor-pointer shadow-2xs"
                   :class="selectedStreamGroup.includes('Group D') ? 'border-slate-300 bg-white ring-2 ring-[#8C2826]/20 shadow-xs' : 'border-slate-200 bg-white/80 hover:border-[#FECACA]'">
              <input type="radio" name="stream_group" x-model="selectedStreamGroup" value="Group D: English, History, Pol Sci / Legal, Psych, Entr / Economics" class="mt-1 text-[#8C2826] focus:ring-[#8C2826]">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                  <span class="font-extrabold text-slate-900 text-xs">Humanities Stream (Group D)</span>
                  <span class="text-[9px] font-bold bg-purple-100 text-purple-800 px-2 py-0.5 rounded tracking-wide">LAW / CIVIL SERVICES</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">English, History, Political Science / Legal, Psychology, Entr / Economics</p>
              </div>
            </label>
          </div>

          {{-- Class X Board Examination Qualifying Information for 11th Admission --}}
          <div class="pt-3 border-t border-[#FECACA] space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                <span>📝 Class X Qualifying Board Exam Information</span>
              </span>
              <span class="text-[10px] font-semibold text-slate-500">As per Grade XI CBSE Admission Norms</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Class X Board</label>
                <select name="previous_school_board" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-semibold text-slate-700 bg-white">
                  <option value="CBSE" selected>CBSE (New Delhi)</option>
                  <option value="ICSE">ICSE / ISC</option>
                  <option value="Tamil Nadu State Board">Tamil Nadu State Board</option>
                  <option value="Other State Board">Other State Board</option>
                  <option value="International / IGCSE">International / IGCSE</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Class X Roll / Reg No.</label>
                <input type="text" name="previous_class" placeholder="e.g. 193158524" value="{{ old('previous_class') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium text-slate-900 bg-white">
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Percentage / CGPA Obtained</label>
                <div class="relative">
                  <input type="number" step="0.01" min="0" max="100" name="previous_percentage" placeholder="e.g. 91.5" value="{{ old('previous_percentage') }}"
                         class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-bold text-slate-900 bg-white">
                  <span class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-xs font-bold text-slate-400">%</span>
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Year of Passing Class X</label>
                <select name="year_of_passing" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-semibold text-slate-700 bg-white">
                  <option value="2026" selected>2026</option>
                  <option value="2025">2025</option>
                  <option value="2024">2024</option>
                  <option value="2023">2023</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Previous School Attended (Class X)</label>
                <input type="text" name="previous_school_name" placeholder="School name & city (e.g. Erode Public School)" value="{{ old('previous_school_name') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium text-slate-900 bg-white">
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">TC &amp; Enclosures Verification</label>
                <div class="flex items-center gap-4 pt-1.5 flex-wrap">
                  <label class="flex items-center gap-1.5 text-slate-700 font-bold cursor-pointer">
                    <input type="checkbox" name="is_tc_enclosed" value="1" checked class="rounded text-[#8C2826] focus:ring-[#8C2826]">
                    <span>Original TC Enclosed</span>
                  </label>
                  <label class="flex items-center gap-1.5 text-slate-700 font-bold cursor-pointer">
                    <input type="checkbox" name="is_qualified_promotion" value="Yes" checked class="rounded text-[#8C2826] focus:ring-[#8C2826]">
                    <span>Qualified for Promotion</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Standard-Specific Section 2: Secondary Stage (Classes IX & X) --}}
        <div x-show="isSecondary" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 rounded-3xl bg-gradient-to-br from-indigo-50/90 via-slate-50 to-white border-2 border-indigo-200/90 space-y-4 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-200/70 pb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-xs">📖</span>
              <div>
                <label class="block text-xs font-black text-indigo-950 uppercase tracking-wide">
                  Secondary Stage Academic Details (Class IX &amp; X)
                </label>
                <p class="text-[11px] text-slate-500 font-medium">All India Secondary School Examination (AISSE) curriculum &amp; subject alignment</p>
              </div>
            </div>
            <span class="text-[10px] font-bold text-indigo-700 bg-white px-2.5 py-1 rounded-full border border-indigo-200 shadow-2xs">CBSE AISSE Stage</span>
          </div>

          {{-- Class X Material Fee Notice if Grade X --}}
          <div x-show="selectedFee?.material_fee > 0" class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5 shadow-2xs">
            <span class="text-base shrink-0">⚠️</span>
            <div>
              <span class="font-extrabold block">Class X Board Coaching &amp; Material Package Fee: Rs. 19,500/-</span>
              <p class="text-[11px] text-amber-800 mt-0.5">Includes board exam question bank sets, laboratory practical journals, special revision sessions, and pre-board examinations. Due on or before 10.01.2026.</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Mathematics Track</label>
              <select class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 text-xs font-semibold text-slate-700 bg-white">
                <option value="standard" selected>Mathematics Standard (Code 041 - For Science/Commerce with Math in 11th)</option>
                <option value="basic">Mathematics Basic (Code 241 - For Commerce without Math / Humanities)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Previous School Name</label>
              <input type="text" name="previous_school_name" placeholder="Previous school &amp; city" value="{{ old('previous_school_name') }}"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 text-xs font-medium text-slate-900 bg-white">
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Class Last Studied &amp; TC</label>
              <div class="flex items-center gap-2">
                <input type="text" name="previous_class" placeholder="e.g. Class 8 / Class 9" value="{{ old('previous_class') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 text-xs font-medium text-slate-900 bg-white">
                <label class="flex items-center gap-1 text-slate-700 font-bold whitespace-nowrap cursor-pointer">
                  <input type="checkbox" name="is_tc_enclosed" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                  <span>TC</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Standard-Specific Section 3: Middle Stage (Classes VI – VIII) --}}
        <div x-show="isMiddle" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 rounded-3xl bg-gradient-to-br from-emerald-50/90 via-teal-50/70 to-slate-50 border-2 border-emerald-200/90 space-y-4 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-200/70 pb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-xs">🔬</span>
              <div>
                <label class="block text-xs font-black text-emerald-950 uppercase tracking-wide">
                  Middle Stage Academic Details (Class VI – VIII)
                </label>
                <p class="text-[11px] text-slate-500 font-medium">Experiential learning, 3-language formula, and practical science foundations</p>
              </div>
            </div>
            <span class="text-[10px] font-bold text-emerald-700 bg-white px-2.5 py-1 rounded-full border border-emerald-200 shadow-2xs">NEP Middle Stage</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Third Language (L3)</label>
              <select class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-emerald-500 text-xs font-semibold text-slate-700 bg-white">
                <option value="Hindi" selected>Hindi (Foundation / Introductory)</option>
                <option value="Sanskrit">Sanskrit (Classical Indian Language)</option>
                <option value="Tamil">Tamil (Basic Language)</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Previous School Attended</label>
              <input type="text" name="previous_school_name" placeholder="School name &amp; city" value="{{ old('previous_school_name') }}"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-emerald-500 text-xs font-medium text-slate-900 bg-white">
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Previous Class &amp; TC Status</label>
              <div class="flex items-center gap-2">
                <input type="text" name="previous_class" placeholder="e.g. Class 5 / Class 6" value="{{ old('previous_class') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-emerald-500 text-xs font-medium text-slate-900 bg-white">
                <label class="flex items-center gap-1 text-slate-700 font-bold whitespace-nowrap cursor-pointer">
                  <input type="checkbox" name="is_tc_enclosed" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                  <span>TC</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- Standard-Specific Section 4: Primary Stage (Classes I – V) --}}
        <div x-show="isPrimary" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 rounded-3xl bg-gradient-to-br from-amber-50/90 via-orange-50/70 to-slate-50 border-2 border-amber-200/90 space-y-4 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200/70 pb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center font-black text-sm shadow-xs">🎒</span>
              <div>
                <label class="block text-xs font-black text-amber-950 uppercase tracking-wide">
                  Primary / Preparatory Stage Details (Class I – V)
                </label>
                <p class="text-[11px] text-slate-500 font-medium">Foundational literacy, numeracy, discovery-based learning, and creative arts</p>
              </div>
            </div>
            <span class="text-[10px] font-bold text-amber-800 bg-white px-2.5 py-1 rounded-full border border-amber-200 shadow-2xs">Preparatory Stage</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Previous School / Kindergarten Attended</label>
              <input type="text" name="previous_school_name" placeholder="Nursery / Kindergarten / School name" value="{{ old('previous_school_name') }}"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-amber-500 text-xs font-medium text-slate-900 bg-white">
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Learning &amp; Activity Interests</label>
              <input type="text" placeholder="e.g. Drawing, Music, Sports, Mental Math"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-amber-500 text-xs font-medium text-slate-900 bg-white">
            </div>
          </div>
        </div>

        {{-- Standard-Specific Section 5: Kindergarten / Pre-Primary (Pre-KG, LKG, UKG) --}}
        <div x-show="isKindergarten" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/90 via-pink-50/70 to-slate-50 border-2 border-rose-200/90 space-y-4 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-rose-200/70 pb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-black text-sm shadow-xs">🧸</span>
              <div>
                <label class="block text-xs font-black text-rose-950 uppercase tracking-wide">
                  Kindergarten / Early Childhood Care &amp; Education (Pre-KG, LKG, UKG)
                </label>
                <p class="text-[11px] text-slate-500 font-medium">Child comfort, habits, routines, and authorized child escort details</p>
              </div>
            </div>
            <span class="text-[10px] font-bold text-rose-700 bg-white px-2.5 py-1 rounded-full border border-rose-200 shadow-2xs">Foundational ECCE</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Toilet Training Status</label>
              <select class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-rose-500 text-xs font-semibold text-slate-700 bg-white">
                <option value="trained" selected>Fully Toilet Trained</option>
                <option value="training">In Training / Needs Occasional Help</option>
                <option value="diapers">Requires Diaper Assistance</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Food / Eating Habits</label>
              <input type="text" placeholder="e.g. Vegetarian, Eats independently"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-rose-500 text-xs font-medium text-slate-900 bg-white">
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Authorized Escort for Child Pickup</label>
              <input type="text" placeholder="Name &amp; Phone of authorized person"
                     class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-rose-500 text-xs font-medium text-slate-900 bg-white">
            </div>
          </div>
        </div>

        {{-- Student Email Address (Optional) --}}
        <div class="pt-2 border-t border-slate-100">
          <div class="flex items-center justify-between mb-1.5">
            <label for="email" class="block text-xs font-bold text-slate-700">Student Email Address</label>
            <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Optional</span>
          </div>
          <input type="email" id="email" name="email" maxlength="100" value="{{ old('email') }}"
                 placeholder="student@example.com (optional — for Class 9 and above)"
                 class="w-full px-4 py-3 rounded-xl border @error('email') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900 bg-white">
          @error('email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          <p id="email_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
        </div>

        {{-- EMIS / PEN Number and Identification Marks --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">EMIS / PEN Number</label>
              <span class="text-[9px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded border border-[#FECACA]">State EMIS</span>
            </div>
            <input type="text" name="emis_no" value="{{ old('emis_no') }}" placeholder="e.g. 33020100101234"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Identification Mark 1</label>
            <input type="text" name="identification_mark_1" value="{{ old('identification_mark_1') }}" placeholder="e.g. Mole on right cheek"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Identification Mark 2</label>
            <input type="text" name="identification_mark_2" value="{{ old('identification_mark_2') }}" placeholder="e.g. Scar on left forehead"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>
        </div>

        {{-- Standard-wise Student Admission Kit & Textbook Inventory --}}
        <div x-show="selectedClassId" x-transition class="pt-4 border-t border-slate-100 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-extrabold text-[#380E0D] uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Student Admission Kit &amp; Textbooks (Standard Kit)</span>
              </h3>
              <p class="text-[11px] text-slate-500 font-medium">Items automatically issued from Warehouse for <span class="font-bold text-slate-900" x-text="'Class ' + selectedClassName"></span></p>
            </div>
            <span class="text-xs font-bold text-[#8C2826] bg-[#FFF5F5] px-2.5 py-1 rounded-lg border border-[#FECACA]" x-text="currentClassKit.length + ' Items'"></span>
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
                    <span>Default: <strong class="text-slate-800 font-semibold tabular-nums" x-text="cfg.default_quantity + ' ' + (cfg.item?.unit || '')"></strong></span>
                    <span>&bull;</span>
                    <span>Stock: <strong :class="(cfg.item?.current_stock || 0) < (cfg.default_quantity + Number(additionalItems[cfg.item_id]||0)) ? 'text-rose-600 font-bold tabular-nums' : 'text-emerald-700 font-bold tabular-nums'" x-text="(cfg.item?.current_stock || 0) + ' ' + (cfg.item?.unit || '')"></strong></span>
                    <span>&bull;</span>
                    <span class="text-slate-700 font-medium">Price: <strong class="text-[#8C2826] font-bold tabular-nums" x-text="formatMoney(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0)) + '/' + (cfg.item?.unit || 'unit')"></strong></span>
                  </div>
                </div>

                {{-- Additional Paid Qty Selector --}}
                <div x-show="cfg.allow_additional_qty" class="flex flex-col items-end gap-1">
                  <div class="flex items-center gap-1.5 shrink-0 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 pl-1">+ Extra:</span>
                    <button type="button" @click="additionalItems[cfg.item_id] = Math.max(0, Number(additionalItems[cfg.item_id]||0) - 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-xs cursor-pointer flex items-center justify-center">-</button>
                    <input type="number" :name="'additional_items[' + cfg.item_id + ']'" x-model.number="additionalItems[cfg.item_id]" min="0" readonly class="w-8 text-center text-xs font-bold text-[#8C2826] bg-transparent border-0 p-0 focus:ring-0 tabular-nums">
                    <button type="button" @click="additionalItems[cfg.item_id] = Number(additionalItems[cfg.item_id]||0) + 1" class="w-6 h-6 rounded-lg bg-[#FFF5F5] hover:bg-[#FEE2E2] font-bold text-[#8C2826] text-xs cursor-pointer flex items-center justify-center">+</button>
                  </div>
                  <span class="text-[10px] font-bold text-[#8C2826] tabular-nums pr-1" x-show="Number(additionalItems[cfg.item_id]||0) > 0" x-text="'+ ' + formatMoney((Number(additionalItems[cfg.item_id]||0)) * Number(cfg.student_charge > 0 ? cfg.student_charge : (cfg.item?.student_price || 0)))"></span>
                </div>
              </div>
            </template>
          </div>

          {{-- Custom Fields for Student Admission Kit & Textbooks --}}
          <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50/70 via-slate-50 to-white border border-indigo-200/80 space-y-4 shadow-2xs mt-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-100 pb-3">
              <div>
                <h4 class="text-xs font-extrabold text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                  <span>📚 Custom Fields for Admission Kit &amp; Textbooks</span>
                </h4>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Customize textbook curriculum, language books, and add custom kit items for this student</p>
              </div>
              <button type="button" @click="addCustomKitItem()"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition cursor-pointer self-start sm:self-auto">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>+ Add Custom Kit Item / Textbook</span>
              </button>
            </div>

            {{-- Row 1: Second Language Textbook & Textbook Pack --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
              {{-- Second Language Textbook Choice --}}
              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label class="block text-xs font-bold text-slate-700">Second Language Textbook</label>
                  <span x-show="secondLanguage === 'Other'" class="text-[10px] font-bold text-indigo-600">Custom</span>
                </div>
                <select x-model="secondLanguage"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-xs font-semibold text-slate-700 bg-white">
                  <option value="Tamil">Tamil (State Board / CBSE)</option>
                  <option value="Hindi">Hindi (Course A / B)</option>
                  <option value="French">French (Foreign Lang)</option>
                  <option value="Sanskrit">Sanskrit (Classical)</option>
                  <option value="Other">Other (Custom Language Book)</option>
                </select>
                <div x-show="secondLanguage === 'Other'" x-transition class="mt-1.5">
                  <input type="text" x-model="customSecondLanguage" placeholder="Enter custom language..."
                         class="w-full px-3 py-2 rounded-xl border border-indigo-300 text-xs bg-white">
                </div>
                <input type="hidden" name="second_language" :value="effectiveSecondLanguage">
              </div>

              {{-- Textbook Package Type --}}
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Textbook Curriculum Pack</label>
                <select name="textbook_pack" x-model="textbookPack"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-xs font-semibold text-slate-700 bg-white">
                  <option value="CBSE Standard NCERT Package">CBSE Standard NCERT Package</option>
                  <option value="State Board Textbook Pack">State Board Textbook Pack</option>
                  <option value="Integrated NEET/JEE Modules Pack">Integrated NEET/JEE Modules Pack</option>
                  <option value="Foundation Workbook Package">Foundation Workbook Package</option>
                </select>
              </div>

              {{-- Special Textbook / Kit Notes --}}
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Textbook &amp; Kit Notes</label>
                <input type="text" name="textbook_notes" x-model="textbookNotes"
                       placeholder="e.g. Includes full cover set &amp; lab manual"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-xs font-medium text-slate-700 bg-white">
              </div>
            </div>

            {{-- Row 2: Dynamic Custom Kit & Textbook Items List --}}
            <div class="space-y-2 pt-1">
              <div class="flex items-center justify-between text-xs">
                <span class="font-extrabold text-slate-700 uppercase tracking-wide text-[11px]">
                  Custom Kit &amp; Textbook Items Added (<span x-text="customKitItems.length"></span>)
                </span>
                <span class="font-bold text-indigo-700 text-xs tabular-nums" x-show="customKitFeeTotal > 0" x-text="'Custom Fee Total: ' + formatMoney(customKitFeeTotal)"></span>
              </div>

              <template x-if="customKitItems.length === 0">
                <div class="p-3 bg-white/70 rounded-xl border border-dashed border-indigo-200 text-center text-xs text-slate-500 font-medium">
                  <span>No custom items added yet. Click <strong>"+ Add Custom Kit Item / Textbook"</strong> above to add specialized books, drawing sets, lab coats, or custom school items.</span>
                </div>
              </template>

              <div class="space-y-2">
                <template x-for="(cItem, cIdx) in customKitItems" :key="cItem.id">
                  <div class="p-3 rounded-xl bg-white border border-indigo-100 shadow-2xs grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                    {{-- Item Name --}}
                    <div class="sm:col-span-4">
                      <input type="text" x-model="cItem.name" placeholder="Item / Book Name (e.g. Lab Coat, Drawing Kit)"
                             class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200">
                    </div>

                    {{-- Category --}}
                    <div class="sm:col-span-3">
                      <select x-model="cItem.category"
                              class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 bg-white">
                        <option value="Textbook">Textbook</option>
                        <option value="Notebook Set">Notebook Set</option>
                        <option value="Uniform / Wearable">Uniform / Wearable</option>
                        <option value="Stationery Kit">Stationery Kit</option>
                        <option value="Lab Equipment">Lab Equipment</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>

                    {{-- Specification / Size --}}
                    <div class="sm:col-span-2">
                      <input type="text" x-model="cItem.specification" placeholder="Size / Spec"
                             class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700">
                    </div>

                    {{-- Quantity --}}
                    <div class="sm:col-span-1">
                      <input type="number" min="1" x-model.number="cItem.quantity" placeholder="Qty"
                             class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-center tabular-nums">
                    </div>

                    {{-- Unit Price --}}
                    <div class="sm:col-span-1">
                      <input type="number" min="0" step="10" x-model.number="cItem.unit_price" placeholder="₹ Price"
                             class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-center text-indigo-700 tabular-nums">
                    </div>

                    {{-- Delete Button --}}
                    <div class="sm:col-span-1 text-right">
                      <button type="button" @click="removeCustomKitItem(cIdx)"
                              class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold flex items-center justify-center transition cursor-pointer mx-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </div>
                  </div>
                </template>
              </div>
            </div>

            {{-- Hidden Submission for Custom Kit Items JSON --}}
            <input type="hidden" name="custom_kit_items" :value="JSON.stringify(customKitItems)">
          </div>
        </div>

        {{-- Demographic & Identification Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Blood Group</label>
            <select name="blood_group" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700">
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
            <select name="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700">
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
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Religion</label>
              <span x-show="selectedReligion === 'Other'" class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded">Custom</span>
            </div>
            <select x-model="selectedReligion"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
              <option value="">-- Select Religion --</option>
              <option value="Hindu">Hindu</option>
              <option value="Christian">Christian</option>
              <option value="Muslim">Muslim</option>
              <option value="Jain">Jain</option>
              <option value="Sikh">Sikh</option>
              <option value="Buddhist">Buddhist</option>
              <option value="Parsi">Parsi</option>
              <option value="Other">Other (Custom Religion)</option>
            </select>
            <div x-show="selectedReligion === 'Other'" x-transition class="mt-2">
              <input type="text" x-model="customReligion" placeholder="Type custom religion here..."
                     class="w-full px-4 py-2.5 rounded-xl border border-[#FECACA] focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium bg-[#FFF5F5]">
            </div>
            <input type="hidden" name="religion" :value="effectiveReligion">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Mother Tongue</label>
              <span x-show="selectedMotherTongue === 'Other'" class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded">Custom</span>
            </div>
            <select x-model="selectedMotherTongue"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
              <option value="">-- Select Mother Tongue --</option>
              <option value="Tamil">Tamil</option>
              <option value="English">English</option>
              <option value="Hindi">Hindi</option>
              <option value="Telugu">Telugu</option>
              <option value="Malayalam">Malayalam</option>
              <option value="Kannada">Kannada</option>
              <option value="Urdu">Urdu</option>
              <option value="Gujarati">Gujarati</option>
              <option value="Marathi">Marathi</option>
              <option value="Bengali">Bengali</option>
              <option value="Other">Other (Custom Language)</option>
            </select>
            <div x-show="selectedMotherTongue === 'Other'" x-transition class="mt-2">
              <input type="text" x-model="customMotherTongue" placeholder="Type custom mother tongue here..."
                     class="w-full px-4 py-2.5 rounded-xl border border-[#FECACA] focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium bg-[#FFF5F5]">
            </div>
            <input type="hidden" name="mother_tongue" :value="effectiveMotherTongue">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Aadhaar Number</label>
              <span id="aadhaar_no_badge" class="text-[10px] font-semibold text-slate-400">12 digits (Optional)</span>
            </div>
            <input type="text" id="aadhaar_no" name="aadhaar_no" value="{{ old('aadhaar_no') }}"
                   inputmode="numeric" maxlength="12" pattern="[0-9]{12}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,12); updateAadhaarBadge(this, 'aadhaar_no_badge')"
                   placeholder="12-digit Aadhaar"
                   class="w-full px-4 py-3 rounded-xl border @error('aadhaar_no') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition tracking-wider">
            @error('aadhaar_no') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="aadhaar_no_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Pincode</label>
              <span id="pincode_badge" class="text-[10px] font-semibold text-slate-400">6 digits (Optional)</span>
            </div>
            <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}"
                   inputmode="numeric" maxlength="6" pattern="[1-9][0-9]{5}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,6); updatePincodeBadge(this, 'pincode_badge')"
                   placeholder="6-digit PIN"
                   class="w-full px-4 py-3 rounded-xl border @error('pincode') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition tracking-wider">
            @error('pincode') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="pincode_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>

        {{-- Student Wearable Sizes: Uniform Dress & Shoes --}}
        <div class="p-4 rounded-2xl bg-gradient-to-r from-[#FFF5F5] via-slate-50 to-slate-50 border border-[#FECACA]/80 space-y-3">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold text-[#380E0D] uppercase tracking-wider flex items-center gap-2">
              <span>👕 Student Uniform Dress &amp; Shoe Sizing</span>
            </h4>
            <span class="text-[10px] text-[#8C2826] font-bold bg-white border border-[#FECACA] px-2 py-0.5 rounded-full shadow-2xs">For Warehouse Kit Issue</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Dress / Uniform Size --}}
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700">Dress / Uniform Size</label>
                <span x-show="selectedDressSize === 'Other'" class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded">Custom Size</span>
              </div>
              <select x-model="selectedDressSize"
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
                <option value="">-- Select Dress / Uniform Size --</option>
                <option value="Size 20 (Pre-KG)">Size 20 (Pre-KG / Age 2–3)</option>
                <option value="Size 22 (LKG)">Size 22 (LKG / Age 3–4)</option>
                <option value="Size 24 (UKG)">Size 24 (UKG / Age 4–5)</option>
                <option value="Size 26 (Class 1-2)">Size 26 (Class 1–2 / Age 5–7)</option>
                <option value="Size 28 (Class 3-4)">Size 28 (Class 3–4 / Age 7–9)</option>
                <option value="Size 30 (Class 5-6)">Size 30 (Class 5–6 / Age 9–11)</option>
                <option value="Size 32 (Class 7-8)">Size 32 (Class 7–8 / Age 11–13)</option>
                <option value="Size 34 (Class 9-10)">Size 34 (Class 9–10 / Age 13–15)</option>
                <option value="Size 36 (Class 11 / Small)">Size 36 (Class 11 / Small)</option>
                <option value="Size 38 (Class 12 / Medium)">Size 38 (Class 12 / Medium)</option>
                <option value="Size 40 (Large)">Size 40 (Large)</option>
                <option value="Size 42 (Extra Large)">Size 42 (Extra Large)</option>
                <option value="Other">Other (Custom Measurement)</option>
              </select>
              <div x-show="selectedDressSize === 'Other'" x-transition class="mt-2">
                <input type="text" x-model="customDressSize" placeholder="Enter custom measurement (e.g. Chest 30, Length 32)"
                       class="w-full px-4 py-2.5 rounded-xl border border-[#FECACA] focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium bg-white">
              </div>
              <input type="hidden" name="dress_size" :value="effectiveDressSize">
            </div>

            {{-- Shoe Size --}}
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700">Shoe Size (UK / India)</label>
                <span x-show="selectedShoeSize === 'Other'" class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded">Custom Size</span>
              </div>
              <select x-model="selectedShoeSize"
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700 bg-white">
                <option value="">-- Select Shoe Size --</option>
                <optgroup label="Kids Sizing (Pre-KG to Class 3)">
                  <option value="Size 8 (Kids)">Size 8 (Kids)</option>
                  <option value="Size 9 (Kids)">Size 9 (Kids)</option>
                  <option value="Size 10 (Kids)">Size 10 (Kids)</option>
                  <option value="Size 11 (Kids)">Size 11 (Kids)</option>
                  <option value="Size 12 (Kids)">Size 12 (Kids)</option>
                  <option value="Size 13 (Kids)">Size 13 (Kids)</option>
                </optgroup>
                <optgroup label="Junior Sizing (Class 4 to 6)">
                  <option value="Size 1 (Junior)">Size 1 (Junior)</option>
                  <option value="Size 2 (Junior)">Size 2 (Junior)</option>
                  <option value="Size 3 (Junior)">Size 3 (Junior)</option>
                </optgroup>
                <optgroup label="Adult Sizing (Class 7 to 12)">
                  <option value="Size 4 (Adult)">Size 4 (Adult)</option>
                  <option value="Size 5 (Adult)">Size 5 (Adult)</option>
                  <option value="Size 6 (Adult)">Size 6 (Adult)</option>
                  <option value="Size 7 (Adult)">Size 7 (Adult)</option>
                  <option value="Size 8 (Adult)">Size 8 (Adult)</option>
                  <option value="Size 9 (Adult)">Size 9 (Adult)</option>
                  <option value="Size 10 (Adult)">Size 10 (Adult)</option>
                </optgroup>
                <option value="Other">Other (Custom Size)</option>
              </select>
              <div x-show="selectedShoeSize === 'Other'" x-transition class="mt-2">
                <input type="text" x-model="customShoeSize" placeholder="Enter custom shoe size (e.g. Size 10.5 Wide)"
                       class="w-full px-4 py-2.5 rounded-xl border border-[#FECACA] focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-xs font-medium bg-white">
              </div>
              <input type="hidden" name="shoe_size" :value="effectiveShoeSize">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Step 2: Parent & Guardian Information ───────────────── --}}
    <div id="section-parents" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 print-card scroll-mt-20">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="text-base font-bold text-slate-900">Parent &amp; Guardian Details</h2>
        <span class="step-badge text-xs font-bold text-[#8C2826] bg-[#FFF5F5] px-3.5 py-1 rounded-full border border-[#FECACA] shadow-2xs">Step 2 of 3</span>
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
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
            @error('parent_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="parent_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Father Mobile Number <span class="text-rose-500">*</span></label>
              <span id="parent_mobile_badge" class="text-[10px] font-semibold text-slate-400">10 digits required</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none">+91</span>
              <input type="tel" id="parent_mobile" name="parent_mobile" required
                     inputmode="numeric" maxlength="10" minlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('parent_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'parent_mobile_badge', true)"
                     placeholder="9876543210"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('parent_mobile') border-rose-400 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-semibold transition text-slate-900 tracking-wider">
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
                   class="w-full px-4 py-3 rounded-xl border @error('parent_email') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
            @error('parent_email') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="parent_email_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Occupation</label>
            <input type="text" id="father_occupation" name="father_occupation" maxlength="100" value="{{ old('father_occupation') }}"
                   placeholder="e.g. Engineer / Business"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Qualification</label>
            <input type="text" id="father_qualification" name="father_qualification" maxlength="100" value="{{ old('father_qualification') }}"
                   placeholder="e.g. B.Tech / MBA / Post Graduate"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Father Income</label>
            <input type="text" id="father_income" name="father_income" maxlength="100" value="{{ old('father_income') }}"
                   placeholder="e.g. 6,00,000 / year"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Father Aadhaar Number</label>
              <span id="father_aadhaar_badge" class="text-[10px] font-semibold text-slate-400">12 digits (Optional)</span>
            </div>
            <input type="text" id="father_aadhaar" name="father_aadhaar" value="{{ old('father_aadhaar') }}"
                   inputmode="numeric" maxlength="12" pattern="[0-9]{12}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,12); updateAadhaarBadge(this, 'father_aadhaar_badge')"
                   placeholder="12-digit Aadhaar"
                   class="w-full px-4 py-3 rounded-xl border @error('father_aadhaar') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900 tracking-wider">
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
                   class="w-full px-4 py-3 rounded-xl border @error('mother_name') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
            @error('mother_name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="mother_name_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Mother Mobile Number</label>
              <span id="mother_mobile_badge" class="text-[10px] font-semibold text-slate-400">10 digits (Optional)</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none">+91</span>
              <input type="tel" id="mother_mobile" name="mother_mobile"
                     inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('mother_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'mother_mobile_badge', false)"
                     placeholder="10-digit mobile"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('mother_mobile') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-semibold transition text-slate-900 tracking-wider">
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
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Qualification</label>
            <input type="text" id="mother_qualification" name="mother_qualification" maxlength="100" value="{{ old('mother_qualification') }}"
                   placeholder="e.g. M.Sc / B.Ed / Graduate"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Income</label>
            <input type="text" id="mother_income" name="mother_income" maxlength="100" value="{{ old('mother_income') }}"
                   placeholder="e.g. Optional"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mother Email Address</label>
            <input type="email" id="mother_email" name="mother_email" maxlength="100" value="{{ old('mother_email') }}"
                   placeholder="mother@example.com"
                   class="w-full px-4 py-3 rounded-xl border @error('mother_email') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
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
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Guardian Mobile Number</label>
              <span id="guardian_mobile_badge" class="text-[10px] font-semibold text-slate-400">10 digits (Optional)</span>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400 select-none">+91</span>
              <input type="tel" id="guardian_mobile" name="guardian_mobile"
                     inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}"
                     value="{{ old('guardian_mobile') }}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10); updatePhoneBadge(this, 'guardian_mobile_badge', false)"
                     placeholder="10-digit mobile"
                     class="w-full pl-12 pr-4 py-3 rounded-xl border @error('guardian_mobile') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-semibold transition text-slate-900 tracking-wider">
            </div>
            @error('guardian_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="guardian_mobile_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Relationship to Student</label>
            <input type="text" id="guardian_relation" name="guardian_relation" maxlength="50" value="{{ old('guardian_relation') }}"
                   placeholder="e.g. Uncle / Aunt / Grandfather"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
          </div>
        </div>
      </div>

      {{-- Sibling Information (School Requirement) --}}
      <div class="space-y-3 pt-4 border-t border-slate-100">
        <div class="flex items-center gap-3">
          <input type="checkbox" id="has_sibling" x-model="hasSibling" class="w-4 h-4 text-[#8C2826] rounded border-slate-300 focus:ring-[#8C2826] cursor-pointer">
          <label for="has_sibling" class="text-xs font-bold text-slate-800 cursor-pointer select-none flex items-center gap-1.5">
            <span>Does the student have a sibling currently studying in this school?</span>
            <span class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-2 py-0.5 rounded border border-[#FECACA]">Sibling Concession Eligible</span>
          </label>
        </div>

        <div x-show="hasSibling" x-transition class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Field 1: Sibling Roll No. (The ONLY editable input field) --}}
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700">Sibling Roll No. <span class="text-rose-500">*</span></label>
                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200">Auto-Fetch</span>
              </div>
              <div class="relative">
                <input type="text" id="sibling_roll_no" name="sibling_roll_no" x-model="siblingRollNo"
                       @input.debounce.400ms="fetchSiblingDetails()"
                       @change="fetchSiblingDetails()"
                       placeholder="Enter Roll No. (e.g. PKGA004)"
                       class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-xs font-bold text-slate-900 bg-white transition shadow-2xs tracking-wider">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <template x-if="siblingLookupStatus === 'loading'">
                    <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                  </template>
                  <template x-if="siblingLookupStatus === 'found'">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </template>
                  <template x-if="siblingLookupStatus === 'not_found' || siblingLookupStatus === 'error'">
                    <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </template>
                </div>
              </div>

              {{-- Hidden Input for admission_no to retain database compatibility --}}
              <input type="hidden" name="sibling_admission_no" :value="siblingAdmissionNo || siblingRollNo">
            </div>

            {{-- Field 2: Sibling Student Name (Visible as textfield, auto-filled) --}}
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Sibling Student Name</label>
              <div class="relative">
                <input type="text" name="sibling_name" x-model="siblingName" readonly
                       placeholder="Auto-filled from Roll No."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/80 text-xs font-semibold text-slate-800 cursor-default select-none shadow-2xs">
              </div>
            </div>

            {{-- Field 3: Sibling Class & Section (Visible as textfield, auto-filled) --}}
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Sibling Class &amp; Section</label>
              <div class="relative">
                <input type="text" name="sibling_class" x-model="siblingClass" readonly
                       placeholder="Auto-filled from Roll No."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/80 text-xs font-semibold text-slate-800 cursor-default select-none shadow-2xs">
              </div>
            </div>
          </div>

          {{-- Lookup Status Feedback Message --}}
          <div x-show="siblingLookupMessage" x-transition class="text-[11px] font-semibold flex items-center gap-1.5 pt-0.5"
               :class="{
                 'text-emerald-700': siblingLookupStatus === 'found',
                 'text-rose-600': siblingLookupStatus === 'not_found' || siblingLookupStatus === 'error',
                 'text-indigo-600': siblingLookupStatus === 'loading'
               }">
            <span x-show="siblingLookupStatus === 'found'">✓</span>
            <span x-show="siblingLookupStatus === 'not_found' || siblingLookupStatus === 'error'">⚠</span>
            <span x-text="siblingLookupMessage"></span>
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
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Annual Family Income (₹)</label>
            <input type="number" step="0.01" name="annual_family_income" value="{{ old('annual_family_income') }}"
                   placeholder="e.g. 500000"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition tabular-nums">
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
                         class="w-28 px-3 py-1.5 rounded-xl border border-amber-300 bg-white font-bold text-xs text-amber-900 text-right focus:ring-amber-500 tabular-nums">
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- 2. School Transport Facility (Stopping, Km, Fee) --}}
        <div class="p-5 rounded-2xl border transition-all"
             :class="transportRequired ? 'bg-[#FFF5F5]/80 border-[#FECACA] ring-2 ring-[#FEE2E2] shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70'">
          <div class="flex items-start gap-3.5">
            <input type="checkbox" id="transport_check" x-model="transportRequired"
                   class="mt-1 w-4 h-4 text-[#8C2826] rounded border-slate-300 focus:ring-[#8C2826] cursor-pointer">
            <div class="space-y-3 flex-1">
              <label for="transport_check" class="cursor-pointer select-none block">
                <span class="text-sm font-extrabold text-[#380E0D] block">School Transport Facility</span>
                <span class="text-xs text-slate-600 font-medium block mt-0.5 leading-relaxed">
                  GPS-monitored school bus/van transit with fixed stopping points and distance-based annual fare.
                </span>
              </label>

              {{-- Hidden Transport Form Inputs for Database --}}
              <input type="hidden" name="transport_route_id" :value="transportRequired ? selectedRouteId : ''">
              <input type="hidden" name="transport_stop_id" :value="transportRequired ? selectedStopId : ''">
              <input type="hidden" name="transport_distance_km" :value="transportRequired ? transportDistanceKm : 0">
              <input type="hidden" name="transport_fee" :value="transportRequired ? transportFee : 0">

              <div x-show="transportRequired" x-transition class="space-y-3 pt-2 border-t border-[#FECACA]">
                <div>
                  <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Transport Route</label>
                  <select x-model="selectedRouteId" @change="selectedStopId = ''"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#8C2826]">
                    <option value="">-- Choose School Route --</option>
                    <template x-for="route in transportRoutesData" :key="route.id">
                      <option :value="route.id" x-text="route.route_name + ' (' + route.distance_km + ' km total)'"></option>
                    </template>
                  </select>
                </div>

                <div x-show="selectedRouteId">
                  <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Boarding / Stopping Point</label>
                  <select x-model="selectedStopId"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#8C2826]">
                    <option value="">-- Choose Stop / Landmark --</option>
                    <template x-for="stop in currentRouteStops" :key="stop.id">
                      <option :value="stop.id" x-text="stop.name + ' • ' + stop.distance_km + ' km • ' + (stop.landmark ? '(' + stop.landmark + ') • ' : '') + formatMoney(stop.fare)"></option>
                    </template>
                  </select>
                </div>

                {{-- Live Stopping, Km & Fee Summary Badge --}}
                <div x-show="currentStop" class="p-3 bg-white rounded-xl border border-[#FECACA] flex items-center justify-between text-xs">
                  <div>
                    <span class="font-extrabold text-[#380E0D] block" x-text="currentStop?.name"></span>
                    <span class="text-[11px] text-slate-500" x-text="'Distance: ' + currentStop?.distance_km + ' km' + (currentStop?.landmark ? ' • Landmark: ' + currentStop.landmark : '')"></span>
                  </div>
                  <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Transport Fee</span>
                    <span class="font-bold text-[#8C2826] text-sm tabular-nums" x-text="formatMoney(transportFee)"></span>
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
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-sm font-bold transition text-emerald-700 tabular-nums">
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
        <span class="font-bold text-emerald-700 text-sm tabular-nums" x-text="'- ' + formatMoney(concessionAmount)"></span>
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
                  :class="docUploadMode === 'desk' ? 'bg-white text-[#8C2826] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                  class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>📁 Upload Now</span>
          </button>
          <button type="button" @click="docUploadMode = 'qr_home'"
                  :class="docUploadMode === 'qr_home' ? 'bg-white text-[#8C2826] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                  class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>📱 Scan QR from Home</span>
          </button>
        </div>
      </div>

      {{-- Mode A: Direct Document Uploads Now --}}
      <div x-show="docUploadMode === 'desk'" x-transition class="space-y-4">
        <div class="p-3 bg-[#FFF5F5] rounded-2xl border border-[#FECACA] flex items-center gap-2.5 text-xs text-[#5C1210]">
          <svg class="w-4 h-4 text-[#8C2826] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>

          {{-- Student / Parent Aadhaar --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Student Aadhaar Card</span>
              <span class="text-[10px] font-bold text-[#8C2826] bg-[#FFF5F5] px-1.5 py-0.5 rounded">Mandatory</span>
            </div>
            <input type="file" name="doc_aadhaar" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>

          {{-- Community / Caste Certificate --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Community Certificate</span>
              <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">If Applicable</span>
            </div>
            <input type="file" name="doc_caste" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>

          {{-- Transfer Certificate (TC) --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Transfer Certificate (TC)</span>
              <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded">Std 1 &amp; Above</span>
            </div>
            <input type="file" name="doc_tc" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>

          {{-- Previous Marksheet --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Previous Marksheet / Progress Card</span>
              <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded">Academic</span>
            </div>
            <input type="file" name="doc_marksheet" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>

          {{-- Parent ID Proof --}}
          <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800">Parent PAN / ID Proof</span>
              <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded">Finance / 80G</span>
            </div>
            <input type="file" name="doc_pan_id" accept=".pdf,image/*"
                   class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8C2826] file:text-white hover:file:bg-[#731E1C] cursor-pointer">
          </div>
        </div>
      </div>

      {{-- Mode B: Scan QR Code from Home (Upload Later) --}}
      <div x-show="docUploadMode === 'qr_home'" x-transition class="p-5 rounded-3xl bg-gradient-to-br from-[#380E0D] via-[#1A0505] to-slate-900 text-white space-y-4 shadow-inner">
        <div class="flex flex-col sm:flex-row items-center gap-5">
          <div class="w-24 h-24 bg-white p-2 rounded-2xl shrink-0 flex flex-col items-center justify-center text-slate-800 shadow-md">
            <svg class="w-16 h-16 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            <span class="text-[8px] font-bold text-indigo-900 tracking-wider">INSTANT QR SLIP</span>
          </div>
          <div class="space-y-1.5 min-w-0 text-center sm:text-left">
            <span class="text-[10px] font-black uppercase tracking-widest bg-amber-400 text-slate-950 px-2 py-0.5 rounded-md">
              PARENT HOME SELF-SERVICE
            </span>
            <h3 class="text-base font-black text-white">Scan QR Code &amp; Upload from Home</h3>
            <p class="text-xs text-amber-200 leading-relaxed">
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
                     class="w-4 h-4 text-[#8C2826] rounded border-slate-300 focus:ring-[#8C2826] cursor-pointer">
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
          <p class="text-xs text-slate-500 font-medium">Select student hobby preferences. <strong class="text-[#8C2826]">Complimentary with admission (No extra fee added).</strong></p>
        </div>
        <span class="text-xs font-bold text-[#8C2826] bg-[#FFF5F5] px-2.5 py-1 rounded-lg border border-[#FECACA]">No Extra Charge</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($activities as $act)
        <label class="p-3.5 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-3 select-none"
               :class="selectedActivities.includes('{{ $act['id'] }}') ? 'bg-[#FFF5F5] border-[#8C2826] shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/80'">
          <div class="flex items-center gap-2.5 min-w-0">
            <input type="checkbox" name="activities[]" value="{{ $act['id'] }}" x-model="selectedActivities"
                   class="w-4 h-4 text-[#8C2826] rounded border-slate-300 focus:ring-[#8C2826] cursor-pointer">
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
          <span class="w-7 h-7 rounded-lg bg-[#FFF5F5] text-[#8C2826] flex items-center justify-center font-bold text-sm">💳</span>
          <h2 class="text-base font-bold text-slate-900">Payment Terms &amp; Fee Collection</h2>
        </div>
        <span class="step-badge text-xs font-bold text-[#8C2826] bg-[#FFF5F5] px-3.5 py-1 rounded-full border border-[#FECACA] shadow-2xs">Step 3 of 3</span>
      </div>

      {{-- Official 2026-2027 Fees Breakdown --}}
      <div x-show="selectedClassId" x-transition class="space-y-3">
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/70 text-xs space-y-2.5 font-medium">
          <div class="flex justify-between items-center text-slate-700">
            <span class="font-bold flex items-center gap-1.5">
              <span>April I Term Fee</span>
              <span class="text-[10px] text-slate-400 font-medium">(Due: 01.04.2026)</span>
            </span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(activeFeeSchedule?.term1_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-slate-700">
            <span class="font-bold flex items-center gap-1.5">
              <span>Aug II Term Fee</span>
              <span class="text-[10px] text-slate-400 font-medium">(Due: 05.08.2026)</span>
            </span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(activeFeeSchedule?.term2_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-slate-700">
            <span class="font-bold flex items-center gap-1.5">
              <span>Dec III Term Fee</span>
              <span class="text-[10px] text-slate-400 font-medium">(Due: 05.12.2026)</span>
            </span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(activeFeeSchedule?.term3_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-purple-900 pt-1 border-t border-slate-200/60" x-show="activeFeeSchedule?.material_fee > 0">
            <span class="font-bold flex items-center gap-1.5">
              <span>Grade X Material Fee</span>
              <span class="text-[10px] text-purple-600 font-medium">(Due: 10.01.2026)</span>
            </span>
            <span class="font-bold text-purple-900 tabular-nums" x-text="formatMoney(activeFeeSchedule?.material_fee)"></span>
          </div>
          <div class="flex justify-between items-center text-[#380E0D] pt-1 border-t border-slate-200/60" x-show="activeFeeSchedule?.admission_fee > 0">
            <div class="flex items-center gap-2">
              <input type="checkbox" id="inc_adm_fee" x-model="includeAdmissionFee" class="w-3.5 h-3.5 text-[#8C2826] rounded">
              <label for="inc_adm_fee" class="cursor-pointer font-bold select-none">
                Admission Fee (Pre-KG to IX &amp; XI Extra)
              </label>
            </div>
            <span class="font-bold text-[#8C2826] tabular-nums" x-text="formatMoney(admissionFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-amber-800 pt-1 border-t border-slate-200/60" x-show="aspRequired">
            <span>After School Program (ASP) Fee</span>
            <span class="font-bold text-amber-900 tabular-nums" x-text="formatMoney(aspFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-[#5C1210] pt-1 border-t border-slate-200/60" x-show="transportRequired && transportFee > 0">
            <span>School Transport Facility Fee</span>
            <span class="font-bold text-[#8C2826] tabular-nums" x-text="formatMoney(transportFee)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="standardKitFeeTotal > 0">
            <span>Student Admission Kit (Standard Kit)</span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(standardKitFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="additionalInventoryFeeTotal > 0">
            <span>Additional Inventory &amp; Kit Items Fee</span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(additionalInventoryFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-indigo-700 pt-1 border-t border-slate-200/60" x-show="customKitFeeTotal > 0">
            <span>Custom Admission Kit &amp; Textbooks Fee</span>
            <span class="font-bold text-slate-900 tabular-nums" x-text="formatMoney(customKitFeeTotal)"></span>
          </div>
          <div class="flex justify-between items-center text-emerald-700 pt-1 border-t border-slate-200/60 font-bold" x-show="concessionAmount > 0">
            <span>Concession Discount (<span class="capitalize" x-text="concessionType.replace('_', ' ')"></span>)</span>
            <span class="font-bold tabular-nums" x-text="'- ' + formatMoney(concessionAmount)"></span>
          </div>
        </div>
      </div>

      {{-- Grand Total Highlight Banner --}}
      <div class="bg-[#FFF5F5] border border-[#FECACA] rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-xs font-extrabold text-[#380E0D] uppercase tracking-wider">NET GRAND TOTAL ADMISSION FEE</h3>
          <p class="text-xs text-[#8C2826] font-medium mt-0.5">Studies + Facilities + Admission Kit (After Concessions)</p>
        </div>
        <div class="text-left sm:text-right">
          <span class="text-2xl sm:text-3xl font-extrabold text-[#8C2826] tabular-nums tracking-tight" x-text="formatMoney(grandTotal)"></span>
        </div>
      </div>

      {{-- Payment Terms Selector --}}
      <div class="space-y-3 pt-2">
        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">Payment Terms Selection</label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === 'single' ? 'bg-[#FFF5F5] border-[#8C2826] ring-2 ring-[#FEE2E2] shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">Single Payment</span>
              <input type="radio" name="payment_terms" value="single" x-model="paymentTerms" class="w-4 h-4 text-[#8C2826] focus:ring-[#8C2826]">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">100% annual fee paid at admission</p>
            <p class="text-sm font-bold text-[#8C2826] tabular-nums" x-text="formatMoney(grandTotal)"></p>
          </label>

          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === '2_terms' ? 'bg-[#FFF5F5] border-[#8C2826] ring-2 ring-[#FEE2E2] shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">2 Terms</span>
              <input type="radio" name="payment_terms" value="2_terms" x-model="paymentTerms" class="w-4 h-4 text-[#8C2826] focus:ring-[#8C2826]">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">100% Kit + 50% tuition in T1, 50% in T2</p>
            <p class="text-sm font-bold text-[#8C2826] tabular-nums" x-text="formatMoney(term1Amount) + ' (T1) &bull; ' + formatMoney(term2Amount) + ' (T2)'"></p>
          </label>

          <label class="p-4 rounded-2xl border transition cursor-pointer flex flex-col justify-between space-y-2 select-none"
                 :class="paymentTerms === '3_terms' ? 'bg-[#FFF5F5] border-[#8C2826] ring-2 ring-[#FEE2E2] shadow-xs' : 'bg-slate-50/70 border-slate-200 hover:bg-slate-100'">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold text-slate-900">3 Terms</span>
              <input type="radio" name="payment_terms" value="3_terms" x-model="paymentTerms" class="w-4 h-4 text-[#8C2826] focus:ring-[#8C2826]">
            </div>
            <p class="text-[11px] text-slate-500 font-medium">Official 3 Terms: April (T1), August (T2), December (T3)</p>
            <p class="text-sm font-bold text-[#8C2826] tabular-nums" x-text="formatMoney(term1Amount) + ' (T1) &bull; ' + formatMoney(term2Amount) + ' (T2) &bull; ' + formatMoney(term3Amount) + ' (T3)'"></p>
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
                <td class="py-3 px-4 text-right font-bold text-slate-900 tabular-nums" x-text="formatMoney(term1Amount)"></td>
                <td class="py-3 px-4 text-slate-500 font-medium" x-text="paymentDate"></td>
              </tr>
              <tr x-show="paymentTerms === '2_terms' || paymentTerms === '3_terms'">
                <td class="py-3 px-4 font-bold text-slate-900">Term 2</td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 tabular-nums" x-text="formatMoney(term2Amount)"></td>
                <td class="py-3 px-4">
                  <input type="date" name="term_2_due_date" x-model="term2DueDate" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-medium">
                </td>
              </tr>
              <tr x-show="paymentTerms === '3_terms'">
                <td class="py-3 px-4 font-bold text-slate-900">Term 3</td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 tabular-nums" x-text="formatMoney(term3Amount)"></td>
                <td class="py-3 px-4">
                  <input type="date" name="term_3_due_date" x-model="term3DueDate" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-medium">
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
               :class="paymentAccount === 'cash_box_2' ? 'border-[#8C2826] bg-[#FFF5F5] ring-2 ring-[#FEE2E2] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
            <div class="flex items-start justify-between">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center transition"
                   :class="paymentAccount === 'cash_box_2' ? 'bg-[#8C2826] text-white shadow-xs shadow-xs' : 'bg-[#FFF5F5] text-[#8C2826]'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              </div>
              <span x-show="paymentAccount === 'cash_box_2'" class="text-[10px] font-extrabold uppercase tracking-wide text-[#8C2826] bg-[#FFF5F5] px-2 py-0.5 rounded-full border border-[#FECACA]">Selected</span>
            </div>
            <div class="mt-3">
              <h4 class="text-xs font-bold text-slate-900">Accounts Vault (Box 2)</h4>
              <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Accounts Department Vault</p>
              <span class="text-[10px] text-[#8C2826] font-bold block mt-2">&rarr; Deposited to Cash Box 2</span>
            </div>
          </div>
        </div>

        {{-- UPI UTR / Reference ID Field (Visible when UPI is active) --}}
        <div x-show="paymentAccount === 'upi'" x-transition class="p-4 bg-purple-50/70 rounded-2xl border border-purple-200/80 space-y-1.5">
          <label class="block text-xs font-bold text-slate-800">UPI Transaction ID / UTR Number <span class="text-slate-400 font-normal">(Optional)</span></label>
          <input type="text" id="transaction_id" name="transaction_id" x-model="upiRefNo" placeholder="e.g. 202612345678 or UPI UTR No."
                 class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition outline-none tracking-wider">
          <p class="text-[11px] text-slate-500 font-medium">This transaction ID will be stored with the student fee receipt and matched in Account Management.</p>
        </div>

        {{-- Payment Date & Amount Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Date <span class="text-rose-500">*</span></label>
            <input type="date" id="payment_date" name="payment_date" x-model="paymentDate" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Amount Collected Today (₹) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" id="amount_collected" name="amount_collected" required
                   :value="userAmountCollected !== null ? userAmountCollected : term1Amount"
                   @input="userAmountCollected = $event.target.value"
                   placeholder="0.00"
                   class="w-full px-4 py-3 rounded-xl border @error('amount_collected') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-bold transition text-slate-900 tabular-nums">
            @error('amount_collected') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            <p id="amount_collected_client_err" class="text-xs text-rose-500 mt-1 font-semibold hidden"></p>
          </div>
        </div>
      </div>

      {{-- Live Collection Status Summary Tiles --}}
      <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
        <div>
          <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Collected Today</span>
          <p class="text-xl font-bold text-emerald-700 tabular-nums mt-0.5" x-text="formatMoney(amountCollected)"></p>
        </div>
        <div>
          <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pending Amount</span>
          <p class="text-xl font-bold text-amber-600 tabular-nums mt-0.5" x-text="formatMoney(pendingAmount)"></p>
        </div>
        <div>
          <span class="text-[10px] font-bold text-[#380E0D] uppercase tracking-wider">Status</span>
          <p class="text-sm font-extrabold mt-1"
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
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Previous Class</label>
          <input type="text" id="previous_class" name="previous_class" value="{{ old('previous_class') }}"
                 placeholder="e.g. Class 5"
                 class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Percentage / CGPA</label>
          <input type="number" step="0.1" min="0" max="100" id="previous_percentage" name="previous_percentage" value="{{ old('previous_percentage') }}"
                 placeholder="0–100"
                 class="w-full px-4 py-3 rounded-xl border @error('previous_percentage') border-rose-400 @else border-slate-200 @enderror focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition text-slate-900">
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
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#8C2826] focus:ring-2 focus:ring-[#FEE2E2] text-sm font-medium transition">{{ old('notes') }}</textarea>
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

    {{-- ── Form Action Buttons (Approval request temporarily disabled per request) ──────────────────────────── --}}
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4 print:hidden">
      <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <svg class="w-4 h-4 text-[#8C2826] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Please review student &amp; billing details before submitting. Admission slip and register entry will be created.</span>
      </div>
      <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
        <a href="{{ route('admissions.index') }}" class="px-5 h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-200 text-xs transition flex items-center justify-center shadow-2xs">
          Cancel
        </a>
        <button type="submit" class="px-7 h-11 rounded-xl bg-gradient-to-r from-[#8C2826] to-[#A13431] hover:from-[#731E1C] hover:to-[#8C2826] text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span>Submit &amp; Enroll Student</span>
        </button>
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
      badge.innerHTML = `<span class="text-[#8C2826] font-bold">${len}/10 digits</span>`;
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
      badge.innerHTML = `<span class="text-[#8C2826] font-bold">${len}/12 digits</span>`;
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
      badge.innerHTML = `<span class="text-[#8C2826] font-bold">${len}/6 digits</span>`;
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
