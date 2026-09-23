<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Official CBSE Admission Forms — Erode Public School</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800;900&family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: #f1f5f9;
      color: #0f172a;
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
    }
    .font-school-title {
      font-family: 'Playfair Display', Georgia, serif;
    }

    /* Standard A4 Paper Layout */
    .a4-sheet {
      width: 210mm;
      min-height: 297mm;
      padding: 14mm 16mm;
      margin: 20px auto;
      background: #ffffff;
      box-sizing: border-box;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 10px 25px -5px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .page-break {
      page-break-after: always;
      break-after: page;
    }

    /* Fillable inputs that look like official paper lines */
    .fillable-input {
      background: transparent;
      border: none;
      outline: none;
      font-family: inherit;
      color: #0f172a;
      padding: 1px 4px;
      transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .fillable-input:hover {
      background-color: rgba(239, 246, 255, 0.5);
    }
    .fillable-input:focus {
      background-color: #eff6ff;
      border-radius: 2px;
    }

    .dotted-line-wrap {
      border-bottom: 1.2px dotted #334155;
      display: inline-flex;
      align-items: flex-end;
      min-height: 22px;
      vertical-align: bottom;
    }

    .solid-line-wrap {
      border-bottom: 1.2px solid #1e293b;
      display: inline-flex;
      align-items: flex-end;
      min-height: 22px;
      vertical-align: bottom;
    }

    .digit-cell {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 21px;
      height: 24px;
      border: 1.2px solid #0f172a;
      font-weight: 700;
      font-size: 11px;
      font-family: monospace;
      color: #0f172a;
      background: #fff;
      text-align: center;
      outline: none;
      padding: 0;
      margin: 0;
    }
    .digit-cell:focus {
      background: #eff6ff;
      border-color: #2563eb;
    }

    .custom-check-box {
      width: 16px;
      height: 16px;
      border: 1.5px solid #0f172a;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 900;
      vertical-align: middle;
      background: #fff;
      cursor: pointer;
      user-select: none;
      transition: all 0.15s ease;
    }
    .custom-check-box:hover {
      border-color: #1e3a8a;
      background: #f8fafc;
    }

    /* Print Specific Media Queries */
    @media print {
      @page {
        size: A4 portrait;
        margin: 0;
      }
      body {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print {
        display: none !important;
      }
      .a4-sheet {
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        width: 100% !important;
        min-height: 297mm !important;
        padding: 12mm 15mm !important;
      }
      .fillable-input {
        background: transparent !important;
        outline: none !important;
        box-shadow: none !important;
        color: #000000 !important;
        padding: 0 !important;
      }
      input::placeholder, textarea::placeholder {
        color: transparent !important;
      }
      .photo-upload-hint {
        display: none !important;
      }
    }
  </style>
</head>
<body class="text-slate-900 pb-20">

  {{-- ── Properly Arranged Clean Top Toolbar (No Clutter) ────── --}}
  <header class="no-print sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
      
      {{-- Left: Brand & Back Navigation --}}
      <div class="flex items-center gap-3">
        <a href="{{ route('admissions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 transition" title="Back to Admissions">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          <span>Back</span>
        </a>
        
        <div class="h-6 w-[1px] bg-slate-200"></div>

        <div class="flex items-center gap-2.5">
          <img src="{{ asset('images/school-crest-transparent.png') }}" alt="Crest" class="w-8 h-8 object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
          <div>
            <div class="flex items-center gap-1.5">
              <span class="text-xs sm:text-sm font-black tracking-wide text-slate-900 leading-tight">ERODE PUBLIC SCHOOL</span>
              <span class="px-1.5 py-0.2 text-[9px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 rounded">CBSE</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Official Printout &amp; Application Forms</p>
          </div>
        </div>
      </div>

      {{-- Center: The 3 Main Form Tabs (Spacious, No Scrollbar, Perfectly Arranged) --}}
      <nav class="flex items-center bg-slate-100 p-1.5 rounded-xl border border-slate-200">
        <button type="button" onclick="switchForm('admission')" id="tab-admission" class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-[#1e3a8a] text-white shadow-xs">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Admission Form (LKG–X)</span>
        </button>
        
        <button type="button" onclick="switchForm('grade11')" id="tab-grade11" class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-slate-200/60">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
          <span>Grade XI Form</span>
        </button>
        
        <button type="button" onclick="switchForm('enquiry')" id="tab-enquiry" class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-slate-200/60">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          <span>Enquiry Form</span>
        </button>
      </nav>

      {{-- Right: Single Clean Action Button --}}
      <div class="flex items-center">
        <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] active:scale-95 text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center gap-2 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          <span>Print / Save as PDF</span>
        </button>
      </div>

    </div>
  </header>

  {{-- Hidden Photo Input for interactive upload --}}
  <input type="file" id="photo-upload-input" accept="image/*" class="hidden" onchange="handlePhotoUpload(this)">


  {{-- ══════════════════════════════════════════════════════════════════════════
       FORM 1: GENERAL ADMISSION FORM (PDF 1) — 2 Pages
       Fillable in-place with authentic styling:
       - Header inside Red Rounded Rectangle (#b91c1c)
       - Royal Blue Pill "ADMISSION FORM"
       - DOB: 8 digit boxes [D][D] [M][M] [Y][Y][Y][Y]
       - Aadhaar: 12 digit boxes
       - Clickable checkboxes (MALE / FEMALE, BC, CC, AC)
       - Previous school records table
       - Declaration and Principal signature office box
  ══════════════════════════════════════════════════════════════════════════ --}}
  <main id="form-admission" class="form-container">
    
    {{-- PAGE 1 of Admission Form --}}
    <div class="a4-sheet page-break">
      
      {{-- Red Rounded Header Border Box matching PDF 1 --}}
      <div class="border-2 border-[#b91c1c] rounded-2xl p-3 sm:p-4 flex items-center justify-between mb-3">
        <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
          <img src="{{ asset('images/school-crest-transparent.png') }}" alt="Erode Public School Crest" class="w-18 h-18 object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
        </div>
        <div class="text-center flex-1 px-3">
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-wide text-[#b91c1c] uppercase font-school-title leading-tight">
            ERODE PUBLIC SCHOOL
          </h1>
          <p class="text-[11px] font-bold text-[#b91c1c] tracking-tight mt-0.5">
            SENIOR SECONDARY Affiliated to CBSE, New Delhi. (Aff.No.1931585)
          </p>
          <p class="text-[10px] font-semibold text-[#b91c1c] mt-0.5">
            NO.420, VAKKIL THOTTAM, MANICKAMPALAYAM, ERODE - 638 004.
          </p>
          <p class="text-[10px] font-semibold text-[#b91c1c]">
            Ph : + 91 93677 54654, E-MAIL : calleps22@gmail.com
          </p>
        </div>
        <div class="w-12"></div>
      </div>

      {{-- Royal Blue Pill Title: ADMISSION FORM --}}
      <div class="flex justify-center mb-3">
        <div class="bg-[#1e3a8a] text-white px-8 py-1 rounded-full text-xs sm:text-sm font-extrabold uppercase tracking-widest shadow-xs">
          ADMISSION FORM
        </div>
      </div>

      {{-- Top Meta Info & Photo Box --}}
      <div class="flex justify-between items-start mb-2">
        <div class="space-y-3 pt-2 text-xs font-semibold text-slate-800">
          <div class="flex items-center">
            <span class="w-28 font-bold">Form No. :</span>
            <div class="dotted-line-wrap w-44">
              <input type="text" data-sync="form_no" class="fillable-input font-mono font-bold text-blue-900 w-full" value="{{ $student?->id ? 'EPS-F' . str_pad($student->id, 4, '0', STR_PAD_LEFT) : '' }}" placeholder="EPS-F....">
            </div>
          </div>
          <div class="flex items-center">
            <span class="w-28 font-bold">Date :</span>
            <div class="dotted-line-wrap w-44">
              <input type="text" data-sync="date" class="fillable-input font-mono w-full" value="{{ $student?->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : date('d/m/Y') }}" placeholder="DD/MM/YYYY">
            </div>
          </div>
          <div class="flex items-center">
            <span class="w-28 font-bold">Admission No. :</span>
            <div class="dotted-line-wrap w-44">
              <input type="text" data-sync="admission_no" class="fillable-input font-mono font-extrabold text-blue-900 w-full" value="{{ $student?->admission_no ?? '' }}" placeholder="Admission No.">
            </div>
          </div>
        </div>

        {{-- Passport Photo Box (Click to Upload / View) --}}
        <div onclick="triggerPhotoUpload()" class="w-[95px] h-[115px] border-2 border-slate-800 flex flex-col items-center justify-center text-center p-1 bg-slate-50 relative group cursor-pointer" title="Click to upload student photo">
          <img id="preview-photo-admission" src="{{ !empty($student?->photo) ? asset('storage/' . $student->photo) : '' }}" class="{{ !empty($student?->photo) ? '' : 'hidden' }} w-full h-full object-cover">
          <div id="placeholder-photo-admission" class="{{ !empty($student?->photo) ? 'hidden' : '' }} flex flex-col items-center justify-center">
            <span class="text-[10px] font-bold text-slate-700 uppercase leading-tight">Pass Port<br>Size<br>Photo</span>
            <span class="photo-upload-hint text-[8px] text-blue-600 font-semibold mt-1 opacity-0 group-hover:opacity-100 transition">Click to add</span>
          </div>
        </div>
      </div>

      {{-- Student Main Details --}}
      <div class="space-y-3 text-xs text-slate-900 mt-1">
        
        <div>
          <span class="font-bold">Name of the Pupil</span> <span class="text-[10px] font-medium text-slate-600">(In Capital Letter)</span> :
          <div class="solid-line-wrap w-full">
            <input type="text" data-sync="student_name" class="fillable-input font-bold uppercase tracking-wider text-sm w-full text-slate-950" value="{{ $student?->full_name ?? ($enquiry?->student_name ?? '') }}" placeholder="ENTER PUPIL'S FULL NAME">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold whitespace-nowrap mr-2">Admission sought for the class</span>
            <div class="solid-line-wrap flex-1">
              <select data-sync="class_id" class="fillable-input font-bold text-blue-900 w-full text-center cursor-pointer">
                <option value="">Select Class</option>
                @foreach($classes as $cls)
                  <option value="{{ $cls->id }}" @selected(($student?->currentEnrollment?->class_id ?? ($enquiry?->class_id ?? 0)) == $cls->id)>{{ $cls->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold whitespace-nowrap mr-2">Academic Year</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="academic_year" class="fillable-input font-bold text-center w-full" value="{{ $academicYear?->name ?? '2026-2027' }}">
            </div>
          </div>
        </div>

        {{-- Date of Birth Grid (2 + 2 + 4) & Aadhaar No Grid (12 boxes) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div class="flex items-center gap-2">
            <span class="font-bold whitespace-nowrap text-xs">Date of Birth</span>
            @php
              $dobDigits = str_pad(preg_replace('/[^0-9]/', '', ($student?->dob ? \Carbon\Carbon::parse($student->dob)->format('dmY') : ($enquiry?->dob ? \Carbon\Carbon::parse($enquiry->dob)->format('dmY') : ''))), 8, ' ', STR_PAD_RIGHT);
            @endphp
            <div class="inline-flex items-center gap-0.5" id="dob-container-admission">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[0] ?? '') }}" data-dob-idx="0">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[1] ?? '') }}" data-dob-idx="1">
              <span class="mx-0.5 font-bold text-slate-400">/</span>
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[2] ?? '') }}" data-dob-idx="2">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[3] ?? '') }}" data-dob-idx="3">
              <span class="mx-0.5 font-bold text-slate-400">/</span>
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[4] ?? '') }}" data-dob-idx="4">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[5] ?? '') }}" data-dob-idx="5">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[6] ?? '') }}" data-dob-idx="6">
              <input type="text" maxlength="1" class="digit-cell" value="{{ trim($dobDigits[7] ?? '') }}" data-dob-idx="7">
            </div>
            <input type="hidden" data-sync="dob" value="{{ $student?->dob ?? ($enquiry?->dob ?? '') }}">
          </div>

          <div class="flex items-center gap-2">
            <span class="font-bold whitespace-nowrap text-xs">Aadhar No .</span>
            @php
              $aadhaarClean = str_pad(preg_replace('/[^0-9]/', '', (string)($student?->aadhaar_no ?? '')), 12, ' ', STR_PAD_RIGHT);
            @endphp
            <div class="inline-flex items-center" id="aadhaar-container-admission">
              @for($i = 0; $i < 12; $i++)
                <input type="text" maxlength="1" class="digit-cell" value="{{ trim($aadhaarClean[$i] ?? '') }}" data-aadhaar-idx="{{ $i }}">
                @if(($i + 1) % 4 == 0 && $i < 11)
                  <span class="mx-0.5"></span>
                @endif
              @endfor
            </div>
            <input type="hidden" data-sync="aadhaar_no" value="{{ $student?->aadhaar_no ?? '' }}">
          </div>
        </div>

        {{-- Gender --}}
        <div class="flex items-center gap-4 py-0.5">
          <span class="font-bold">Gender :</span>
          @php $gen = strtolower($student?->gender ?? ($enquiry?->gender ?? '')); @endphp
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-1.5 font-bold cursor-pointer" onclick="selectGender('male')">
              <span class="custom-check-box" id="check-gender-male">{{ ($gen === 'male' || $gen === 'boy') ? '✓' : '' }}</span> MALE
            </label>
            <label class="flex items-center gap-1.5 font-bold cursor-pointer" onclick="selectGender('female')">
              <span class="custom-check-box" id="check-gender-female">{{ ($gen === 'female' || $gen === 'girl') ? '✓' : '' }}</span> FEMALE
            </label>
          </div>
          <input type="hidden" data-sync="gender" value="{{ $gen }}">
        </div>

        {{-- Religion, Caste, Nationality, Blood Group --}}
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-24">Religion</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="religion" class="fillable-input font-semibold w-full" value="{{ $student?->religion ?? '' }}" placeholder="Religion">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-24">Caste</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="caste" class="fillable-input font-semibold w-full" value="{{ $student?->caste ?? '' }}" placeholder="Caste / Community">
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-24">Nationality</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="nationality" class="fillable-input font-semibold w-full" value="{{ $student?->nationality ?? 'Indian' }}">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-24">Blood Group</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="blood_group" class="fillable-input font-bold text-red-700 w-full" value="{{ $student?->blood_group ?? '' }}" placeholder="e.g. O+ve">
            </div>
          </div>
        </div>

        {{-- Father Details --}}
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-28">Father's Name</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="father_name" class="fillable-input font-semibold w-full" value="{{ $student?->father_name ?? ($enquiry?->father_name ?? '') }}" placeholder="Father's full name">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-28">Qualification</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="father_qualification" class="fillable-input font-semibold w-full" value="{{ $student?->father_qualification ?? ($enquiry?->father_qualification ?? '') }}" placeholder="Degree / Qualification">
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-28">Occupation</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="father_occupation" class="fillable-input font-semibold w-full" value="{{ $student?->father_occupation ?? ($enquiry?->father_occupation ?? '') }}" placeholder="Business / Profession">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-28">Income</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="father_income" class="fillable-input font-semibold w-full" value="{{ $student?->father_income ?? ($student?->annual_family_income ? '₹ ' . number_format($student->annual_family_income, 0) : ($enquiry?->father_income ?? '')) }}" placeholder="Annual Income">
            </div>
          </div>
        </div>

        {{-- Mother Details --}}
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-28">Mother's Name</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="mother_name" class="fillable-input font-semibold w-full" value="{{ $student?->mother_name ?? ($enquiry?->mother_name ?? '') }}" placeholder="Mother's full name">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-28">Qualification</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="mother_qualification" class="fillable-input font-semibold w-full" value="{{ $student?->mother_qualification ?? ($enquiry?->mother_qualification ?? '') }}" placeholder="Degree / Qualification">
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-end">
            <span class="font-bold w-28">Occupation</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="mother_occupation" class="fillable-input font-semibold w-full" value="{{ $student?->mother_occupation ?? ($enquiry?->mother_occupation ?? '') }}" placeholder="Occupation / Homemaker">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-28">Income</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="mother_income" class="fillable-input font-semibold w-full" value="{{ $student?->mother_income ?? ($enquiry?->mother_income ?? '') }}" placeholder="Annual Income (if any)">
            </div>
          </div>
        </div>

        {{-- Residential Address --}}
        <div>
          <span class="font-bold">Residential address</span>
          <div class="solid-line-wrap w-full mt-0.5">
            <input type="text" data-sync="address_line1" class="fillable-input font-medium text-xs w-full" value="{{ $student?->residential_address ?? ($enquiry?->address ?? '') }}" placeholder="Door No, Street name, Area, City, Pincode">
          </div>
          <div class="solid-line-wrap w-full mt-2">
            <input type="text" data-sync="address_line2" class="fillable-input font-medium text-xs w-full" placeholder="(Additional address details if required)">
          </div>
        </div>

        {{-- Contact Numbers --}}
        <div class="flex items-end gap-6 pt-1">
          <span class="font-bold">Contact number :</span>
          <div class="flex items-end flex-1">
            <span class="font-bold mr-2">Father</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="father_mobile" class="fillable-input font-mono font-bold text-slate-900 w-full" value="{{ $student?->father_mobile ?? ($enquiry?->father_mobile ?? ($enquiry?->parent_mobile ?? '')) }}" placeholder="10-digit mobile">
            </div>
          </div>
          <div class="flex items-end flex-1">
            <span class="font-bold mr-2">Mother</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="mother_mobile" class="fillable-input font-mono font-bold text-slate-900 w-full" value="{{ $student?->mother_mobile ?? ($enquiry?->mother_mobile ?? '') }}" placeholder="10-digit mobile">
            </div>
          </div>
        </div>

        {{-- Personal Identification Marks --}}
        <div class="pt-1">
          <span class="font-bold">Personal Identification Marks :</span>
          <div class="flex items-end mt-1.5">
            <span class="font-bold mr-2">1.</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="id_mark_1" class="fillable-input font-medium text-xs w-full" value="{{ $student?->identification_mark_1 ?? '' }}" placeholder="A mole or visible mark on...">
            </div>
          </div>
          <div class="flex items-end mt-2">
            <span class="font-bold mr-2">2.</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="id_mark_2" class="fillable-input font-medium text-xs w-full" value="{{ $student?->identification_mark_2 ?? '' }}" placeholder="A scar or visible mark on...">
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- PAGE 2 of Admission Form --}}
    <div class="a4-sheet">
      
      {{-- Previous School Records Table --}}
      <div class="mb-6">
        <h3 class="font-bold text-xs text-slate-900 mb-2">Previous School Records</h3>
        <table class="w-full border-2 border-slate-700 text-xs text-slate-900">
          <thead>
            <tr class="border-b-2 border-slate-700 bg-slate-50 font-bold">
              <th class="py-2 px-3 border-r-2 border-slate-700 text-center w-1/2">Name of the School</th>
              <th class="py-2 px-3 border-r-2 border-slate-700 text-center w-1/4">Class Studied</th>
              <th class="py-2 px-3 text-center w-1/4">Year of Study</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b border-slate-400 h-14">
              <td class="px-2 border-r-2 border-slate-700 align-middle">
                <input type="text" data-sync="prev_school" class="fillable-input font-semibold w-full" value="{{ $student?->previous_school_name ?? ($enquiry?->last_school_studied ?? ($enquiry?->previous_school ?? '')) }}" placeholder="School name & city">
              </td>
              <td class="px-2 border-r-2 border-slate-700 text-center align-middle">
                <input type="text" data-sync="prev_class" class="fillable-input font-semibold text-center w-full" value="{{ $enquiry?->previous_class ?? '' }}" placeholder="e.g. Class 5">
              </td>
              <td class="px-2 text-center align-middle">
                <input type="text" data-sync="prev_year" class="fillable-input font-semibold text-center w-full" value="{{ $student?->year_of_passing ?? ($enquiry?->year_of_passing ?? '') }}" placeholder="e.g. 2024-2025">
              </td>
            </tr>
            <tr class="h-14">
              <td class="px-2 border-r-2 border-slate-700 align-middle">
                <input type="text" class="fillable-input font-semibold w-full">
              </td>
              <td class="px-2 border-r-2 border-slate-700 text-center align-middle">
                <input type="text" class="fillable-input font-semibold text-center w-full">
              </td>
              <td class="px-2 text-center align-middle">
                <input type="text" class="fillable-input font-semibold text-center w-full">
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Promotion & TC Enclosure --}}
      <div class="space-y-4 text-xs text-slate-900 mb-6">
        <div class="flex items-end">
          <span class="font-bold whitespace-nowrap mr-2">Whether qualified for promotion</span>
          <div class="solid-line-wrap flex-1">
            <input type="text" data-sync="promotion" class="fillable-input font-semibold w-full" value="{{ $student?->is_qualified_promotion ?? 'Yes' }}" placeholder="Yes / No">
          </div>
        </div>

        <div class="flex items-end">
          <span class="font-bold whitespace-nowrap mr-2">Whether original TC is enclosed</span>
          <div class="solid-line-wrap flex-1">
            <input type="text" data-sync="tc_enclosed" class="fillable-input font-semibold w-full" value="{{ $student?->is_tc_enclosed ? 'Yes' : '' }}" placeholder="Yes / No">
          </div>
        </div>

        {{-- Enclosed Xerox Copies Checklist (BC, CC, AC) --}}
        <div class="flex items-center justify-between pt-1">
          <span class="font-bold">Whether xerox copy of Birth, community, Aadhar are enclosed</span>
          <div class="flex items-center gap-8">
            <label class="flex items-center gap-2 font-bold cursor-pointer" onclick="toggleCustomCheck(this)">
              BC <span class="custom-check-box">{{ !empty($student?->documents_submitted['birth_certificate']) ? '✓' : '' }}</span>
            </label>
            <label class="flex items-center gap-2 font-bold cursor-pointer" onclick="toggleCustomCheck(this)">
              CC <span class="custom-check-box">{{ !empty($student?->documents_submitted['community_certificate']) ? '✓' : '' }}</span>
            </label>
            <label class="flex items-center gap-2 font-bold cursor-pointer" onclick="toggleCustomCheck(this)">
              AC <span class="custom-check-box">{{ !empty($student?->documents_submitted['aadhaar_card']) ? '✓' : '' }}</span>
            </label>
          </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="pt-3">
          <span class="font-bold">In case of emergency :</span>
          <div class="flex items-end mt-2">
            <span class="font-bold w-36">Contact Person :</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="emergency_name" class="fillable-input font-semibold w-full" value="{{ $student?->emergency_contact_name ?? '' }}" placeholder="Name of guardian / relative">
            </div>
          </div>
          <div class="flex items-end mt-3">
            <span class="font-bold w-36">Contact Number :</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="emergency_mobile" class="fillable-input font-mono font-semibold w-full" value="{{ $student?->emergency_contact_mobile ?? '' }}" placeholder="Emergency mobile number">
            </div>
          </div>
        </div>
      </div>

      {{-- Declaration --}}
      <div class="text-center my-10 px-4 text-xs font-semibold leading-relaxed text-slate-800">
        <p>I hereby, declare that the details furnished above are correct</p>
        <p>that I will not demand any change and I shall abide by</p>
        <p>the rules of the school</p>

        <div class="flex justify-end mt-12 pt-4">
          <div class="text-center">
            <div class="w-64 border-b border-slate-700 mb-1"></div>
            <span class="text-xs font-bold text-slate-800">Full Signature of the Parent / Guardian</span>
          </div>
        </div>
      </div>

      {{-- FOR OFFICE USE ONLY BOX --}}
      <div class="border-2 border-slate-800 p-5 rounded-lg mt-12 bg-slate-50/50">
        <div class="text-center mb-4">
          <span class="font-extrabold text-xs tracking-wider text-slate-900 uppercase border-b-2 border-slate-900 pb-0.5">
            FOR OFFICE USE ONLY
          </span>
        </div>

        <div class="space-y-4 text-xs">
          <div class="flex items-end">
            <span class="font-bold w-32">Admission No</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="office_adm_no" class="fillable-input font-mono font-extrabold text-blue-900 w-full" value="{{ $student?->admission_no ?? '' }}" placeholder="Office assigned admission number">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-32">Class</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="office_class" class="fillable-input font-bold w-full" value="{{ $student?->currentEnrollment?->class?->name ?? '' }}" placeholder="Class admitted into">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-32">Emis Number</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="office_emis" class="fillable-input font-mono font-semibold w-full" value="{{ $student?->emis_no ?? '' }}" placeholder="EMIS registration code">
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-10 pt-4">
          <div class="text-center">
            <div class="w-52 border-b border-slate-700 mb-1"></div>
            <span class="text-xs font-bold text-slate-800">Signature of the Principal</span>
          </div>
        </div>
      </div>

    </div>

  </main>


  {{-- ══════════════════════════════════════════════════════════════════════════
       FORM 2: GRADE XI APPLICATION FORM (PDF 2) — 2 Pages
       Fillable in-place with authentic styling:
       - Header inside Black Rounded Rectangle (#000000)
       - Black Inverted Pill "APPLICATION FORM GRADE XI (2026–2027)"
       - Student Details, Parent Details, Academic Info
       - Page 2: Stream Selection (Science Group A/B, Commerce C, Humanities D)
       - Documents Checklist
       - Declaration and Admission In-Charge Office box
  ══════════════════════════════════════════════════════════════════════════ --}}
  <main id="form-grade11" class="form-container hidden">
    
    {{-- PAGE 1 of Grade XI Form --}}
    <div class="a4-sheet page-break">
      
      {{-- Black Rounded Header Border Box matching PDF 2 --}}
      <div class="border-2 border-black rounded-2xl p-3 sm:p-4 flex items-center justify-between mb-3">
        <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
          <img src="{{ asset('images/school-crest-transparent.png') }}" alt="Erode Public School Crest" class="w-18 h-18 object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
        </div>
        <div class="text-center flex-1 px-3">
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-wide text-slate-950 uppercase font-school-title leading-tight">
            ERODE PUBLIC SCHOOL
          </h1>
          <p class="text-[10px] font-bold text-slate-800 mt-1">
            NO.420, VAKKIL THOTTAM, MANICKAMPALAYAM, ERODE - 638 004.
          </p>
          <p class="text-[10px] font-bold text-slate-800">
            Ph : + 91 93677 54654, E-MAIL : calleps22@gmail.com
          </p>
        </div>
        <div class="w-12"></div>
      </div>

      {{-- Black Inverted Pill: APPLICATION FORM GRADE XI (2026–2027) --}}
      <div class="flex justify-center my-3">
        <div class="bg-black text-white px-8 py-1.5 rounded-lg text-xs sm:text-sm font-black uppercase tracking-wider shadow-xs">
          APPLICATION FORM GRADE XI ({{ $academicYear?->name ?? '2026–2027' }})
        </div>
      </div>

      {{-- Section 1: STUDENT DETAILS --}}
      <div class="flex justify-between items-start mb-2">
        <div class="space-y-3 pt-2 text-xs font-semibold text-slate-800">
          <h3 class="font-black text-xs uppercase tracking-wider text-slate-950">STUDENT DETAILS</h3>
          <div class="flex items-center">
            <span class="w-28 font-bold">Form No :</span>
            <div class="solid-line-wrap w-44">
              <input type="text" data-sync="form_no" class="fillable-input font-mono font-bold text-blue-900 w-full" value="{{ $student?->id ? 'XI-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) : '' }}" placeholder="XI-....">
            </div>
          </div>
          <div class="flex items-center">
            <span class="w-28 font-bold">Date :</span>
            <div class="solid-line-wrap w-44">
              <input type="text" data-sync="date" class="fillable-input font-mono w-full" value="{{ $student?->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : date('d/m/Y') }}" placeholder="DD/MM/YYYY">
            </div>
          </div>
          <div class="flex items-center">
            <span class="w-28 font-bold">Admission No :</span>
            <div class="solid-line-wrap w-44">
              <input type="text" data-sync="admission_no" class="fillable-input font-mono font-bold text-blue-900 w-full" value="{{ $student?->admission_no ?? '' }}" placeholder="Admission No.">
            </div>
          </div>
        </div>

        {{-- Passport Photo Box --}}
        <div onclick="triggerPhotoUpload()" class="w-[95px] h-[115px] border-2 border-black flex flex-col items-center justify-center text-center p-1 bg-slate-50 relative group cursor-pointer" title="Click to upload student photo">
          <img id="preview-photo-grade11" src="{{ !empty($student?->photo) ? asset('storage/' . $student->photo) : '' }}" class="{{ !empty($student?->photo) ? '' : 'hidden' }} w-full h-full object-cover">
          <div id="placeholder-photo-grade11" class="{{ !empty($student?->photo) ? 'hidden' : '' }} flex flex-col items-center justify-center">
            <span class="text-[10px] font-bold text-slate-700 uppercase leading-tight">Pass port<br>Size<br>Photo</span>
            <span class="photo-upload-hint text-[8px] text-blue-600 font-semibold mt-1 opacity-0 group-hover:opacity-100 transition">Click to add</span>
          </div>
        </div>
      </div>

      {{-- Student Main Details --}}
      <div class="space-y-3 text-xs text-slate-900 mt-2">
        <div class="flex items-end justify-between gap-4">
          <div class="flex-1">
            <span class="font-bold">Name of the Pupil :</span>
            <span class="text-[10px] text-slate-500 block">(In Capital Letters)</span>
            <div class="solid-line-wrap w-full">
              <input type="text" data-sync="student_name" class="fillable-input font-bold uppercase tracking-wider text-sm w-full" value="{{ $student?->full_name ?? ($enquiry?->student_name ?? '') }}" placeholder="ENTER PUPIL'S FULL NAME">
            </div>
          </div>
          <div class="flex items-end gap-3 whitespace-nowrap pb-1">
            <span class="font-bold">Gender:</span>
            <label class="flex items-center gap-1 font-bold cursor-pointer" onclick="selectGender('male')">
              <span class="custom-check-box" id="check-gender-male-g11">{{ ($gen === 'male' || $gen === 'boy') ? '✓' : '' }}</span> MALE
            </label>
            <label class="flex items-center gap-1 font-bold cursor-pointer" onclick="selectGender('female')">
              <span class="custom-check-box" id="check-gender-female-g11">{{ ($gen === 'female' || $gen === 'girl') ? '✓' : '' }}</span> FEMALE
            </label>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div class="flex items-end">
            <span class="font-bold w-28">Date of Birth:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="dob_display" class="fillable-input font-semibold w-full" value="{{ $student?->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : ($enquiry?->dob ? \Carbon\Carbon::parse($enquiry->dob)->format('d/m/Y') : '') }}" placeholder="DD/MM/YYYY">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-24">Nationality:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="nationality" class="fillable-input font-semibold w-full" value="{{ $student?->nationality ?? 'Indian' }}">
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div class="flex items-end">
            <span class="font-bold w-28">Blood Group:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="blood_group" class="fillable-input font-bold text-red-700 w-full" value="{{ $student?->blood_group ?? '' }}" placeholder="e.g. A+ve">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-24">Religion :</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="religion" class="fillable-input font-semibold w-full" value="{{ $student?->religion ?? '' }}" placeholder="Religion">
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div class="flex items-end">
            <span class="font-bold w-28">Aadhaar No.:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="aadhaar_no" class="fillable-input font-mono font-semibold w-full" value="{{ $student?->aadhaar_no ?? '' }}" placeholder="12-digit Aadhaar number">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-24">Caste :</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="caste" class="fillable-input font-semibold w-full" value="{{ $student?->caste ?? '' }}" placeholder="Caste / Community">
            </div>
          </div>
        </div>

        <div>
          <span class="font-bold">Residential Address:</span>
          <div class="solid-line-wrap w-full mt-0.5">
            <input type="text" data-sync="address_line1" class="fillable-input font-medium text-xs w-full" value="{{ $student?->residential_address ?? ($enquiry?->address ?? '') }}" placeholder="Door No, Street name, Area, City, Pincode">
          </div>
          <div class="solid-line-wrap w-full mt-2">
            <input type="text" data-sync="address_line2" class="fillable-input font-medium text-xs w-full">
          </div>
        </div>

        <div class="flex items-end">
          <span class="font-bold w-24">Email ID:</span>
          <div class="solid-line-wrap flex-1">
            <input type="email" data-sync="email" class="fillable-input font-mono font-medium w-full" value="{{ $student?->email ?? ($enquiry?->parent_email ?? '') }}" placeholder="parent.email@domain.com">
          </div>
        </div>

        {{-- Section 2: PARENT / GUARDIAN DETAILS --}}
        <div class="pt-2">
          <h3 class="font-black text-xs uppercase tracking-wider text-slate-950 mb-2">PARENT / GUARDIAN DETAILS</h3>
          
          <div class="grid grid-cols-3 gap-3 mb-2">
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Father’s Name:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="father_name" class="fillable-input font-semibold w-full" value="{{ $student?->father_name ?? ($enquiry?->father_name ?? '') }}">
              </div>
            </div>
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Occupation:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="father_occupation" class="fillable-input font-semibold w-full" value="{{ $student?->father_occupation ?? ($enquiry?->father_occupation ?? '') }}">
              </div>
            </div>
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Mobile No:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="father_mobile" class="fillable-input font-mono font-bold w-full" value="{{ $student?->father_mobile ?? ($enquiry?->father_mobile ?? ($enquiry?->parent_mobile ?? '')) }}">
              </div>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3 mb-2">
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Mother’s Name:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="mother_name" class="fillable-input font-semibold w-full" value="{{ $student?->mother_name ?? ($enquiry?->mother_name ?? '') }}">
              </div>
            </div>
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Occupation:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="mother_occupation" class="fillable-input font-semibold w-full" value="{{ $student?->mother_occupation ?? ($enquiry?->mother_occupation ?? '') }}">
              </div>
            </div>
            <div class="flex items-end col-span-1">
              <span class="font-bold mr-2 whitespace-nowrap">Mobile No.</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="mother_mobile" class="fillable-input font-mono font-bold w-full" value="{{ $student?->mother_mobile ?? ($enquiry?->mother_mobile ?? '') }}">
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex items-end">
              <span class="font-bold mr-2 whitespace-nowrap">Guardian (if any):</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="guardian_name" class="fillable-input font-semibold w-full" value="{{ $student?->guardian_name ?? '' }}">
              </div>
            </div>
            <div class="flex items-end">
              <span class="font-bold mr-2 whitespace-nowrap">Relationship with Student:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="guardian_relation" class="fillable-input font-semibold w-full" value="{{ $student?->guardian_relation ?? '' }}">
              </div>
            </div>
          </div>
        </div>

        {{-- Section 3: ACADEMIC INFORMATION --}}
        <div class="pt-2">
          <h3 class="font-black text-xs uppercase tracking-wider text-slate-950 mb-2">ACADEMIC INFORMATION</h3>
          
          <div class="space-y-3">
            <div class="flex items-end">
              <span class="font-bold w-52">Previous School Attended:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="prev_school" class="fillable-input font-semibold w-full" value="{{ $student?->previous_school_attended ?? ($student?->previous_school_name ?? ($enquiry?->last_school_studied ?? ($enquiry?->previous_school ?? ''))) }}">
              </div>
            </div>
            <div class="flex items-end">
              <span class="font-bold w-52">Board (CBSE / ICSE / State):</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="board" class="fillable-input font-semibold w-full" value="{{ $student?->board ?? 'CBSE' }}">
              </div>
            </div>
            <div class="flex items-end">
              <span class="font-bold w-52">Class Last Studied:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" class="fillable-input font-semibold w-full" value="Class X (10th Standard)">
              </div>
            </div>
            <div class="flex items-end">
              <span class="font-bold w-52">Year of Passing:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="prev_year" class="fillable-input font-semibold w-full" value="{{ $student?->year_of_passing ?? (date('Y')) }}">
              </div>
            </div>
            <div class="flex items-end">
              <span class="font-bold w-52">Percentage / Grade Obtained:</span>
              <div class="solid-line-wrap flex-1">
                <input type="text" data-sync="previous_marks" class="fillable-input font-semibold w-full" value="{{ !empty($student?->previous_percentage) ? $student->previous_percentage . '%' : '' }}" placeholder="e.g. 92% or A1 Grade">
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

    {{-- PAGE 2 of Grade XI Form --}}
    <div class="a4-sheet">
      
      {{-- Section 4: STREAM / GROUP APPLIED FOR --}}
      <div class="mb-5">
        <h3 class="font-black text-xs uppercase tracking-wider text-slate-950">STREAM / GROUP APPLIED FOR</h3>
        <p class="text-xs italic text-slate-700 mb-3">Select ONE group by ticking &#10003;</p>

        @php
          $stGroup = strtoupper($student?->stream_group ?? ($enquiry?->stream_group ?? ''));
        @endphp

        {{-- Science Stream --}}
        <div class="mb-3">
          <h4 class="font-extrabold text-xs uppercase tracking-wide text-slate-950 mb-1.5">SCIENCE STREAM</h4>
          <div class="space-y-2 pl-2 text-xs">
            <label class="flex items-start gap-2 cursor-pointer" onclick="selectStreamGroup('GROUP A')">
              <span class="custom-check-box mt-0.5" id="check-group-a">{{ str_contains($stGroup, 'GROUP A') || $stGroup === 'A' ? '✓' : '' }}</span>
              <span><strong>Group A:</strong> English, Mathematics, Physics, Chemistry, Biology / Computer Science</span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer" onclick="selectStreamGroup('GROUP B')">
              <span class="custom-check-box mt-0.5" id="check-group-b">{{ str_contains($stGroup, 'GROUP B') || $stGroup === 'B' ? '✓' : '' }}</span>
              <span><strong>Group B:</strong> English, Physics, Chemistry, Biology, Mathematics / Computer Science</span>
            </label>
          </div>
        </div>

        {{-- Commerce Stream --}}
        <div class="mb-3">
          <h4 class="font-extrabold text-xs uppercase tracking-wide text-slate-950 mb-1.5">COMMERCE STREAM</h4>
          <div class="space-y-2 pl-2 text-xs">
            <label class="flex items-start gap-2 cursor-pointer" onclick="selectStreamGroup('GROUP C')">
              <span class="custom-check-box mt-0.5" id="check-group-c">{{ str_contains($stGroup, 'GROUP C') || $stGroup === 'C' ? '✓' : '' }}</span>
              <span><strong>Group C:</strong> English, Business Studies / Entrepreneurship, Accountancy, Economics, Computer Science</span>
            </label>
          </div>
        </div>

        {{-- Humanities Stream --}}
        <div class="mb-3">
          <h4 class="font-extrabold text-xs uppercase tracking-wide text-slate-950 mb-1.5">HUMANITIES STREAM</h4>
          <div class="space-y-2 pl-2 text-xs">
            <label class="flex items-start gap-2 cursor-pointer" onclick="selectStreamGroup('GROUP D')">
              <span class="custom-check-box mt-0.5" id="check-group-d">{{ str_contains($stGroup, 'GROUP D') || $stGroup === 'D' ? '✓' : '' }}</span>
              <span><strong>Group D:</strong> English, History, Political Science / Legal Studies, Psychology, Entrepreneurship / Economics</span>
            </label>
          </div>
        </div>
        <input type="hidden" data-sync="stream_group" value="{{ $stGroup }}">
      </div>

      {{-- Section 5: DOCUMENTS TO BE ATTACHED --}}
      <div class="mb-5 pt-1">
        <h3 class="font-black text-xs uppercase tracking-wider text-slate-950 mb-2.5">DOCUMENTS TO BE ATTACHED</h3>
        <div class="grid grid-cols-2 gap-y-2.5 gap-x-6 text-xs pl-2">
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->documents_submitted['marksheet_10']) ? '✓' : '' }}</span>
            <span>Copy of Class 10 Marksheet / Result</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->is_tc_enclosed) ? '✓' : '' }}</span>
            <span>Transfer Certificate (TC)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->documents_submitted['conduct_certificate']) ? '✓' : '' }}</span>
            <span>Conduct Certificate</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->documents_submitted['aadhaar_card']) ? '✓' : '' }}</span>
            <span>Aadhaar Card Copy</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->photo) ? '✓' : '' }}</span>
            <span>Two Passport-size Photographs</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->documents_submitted['community_certificate']) ? '✓' : '' }}</span>
            <span>Community Certificate</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer" onclick="toggleCustomCheck(this)">
            <span class="custom-check-box">{{ !empty($student?->documents_submitted['birth_certificate']) ? '✓' : '' }}</span>
            <span>Birth Certificate</span>
          </label>
        </div>
      </div>

      {{-- Section 6: DECLARATION --}}
      <div class="mb-6 pt-2">
        <h3 class="font-black text-xs uppercase tracking-wider text-slate-950 mb-2">DECLARATION</h3>
        <p class="text-xs leading-relaxed text-slate-800 mb-6">
          I hereby declare that the information provided above is true to the best of my knowledge. I understand that providing false information may lead to cancellation of my admission.
        </p>

        <div class="space-y-4 text-xs">
          <div class="flex items-end">
            <span class="font-bold w-48">Signature of Student:</span>
            <span class="solid-line-wrap flex-1"></span>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-48">Signature of Parent / Guardian:</span>
            <span class="solid-line-wrap flex-1"></span>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-48">Date:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="date" class="fillable-input font-mono font-medium w-full" value="{{ date('d/m/Y') }}">
            </div>
          </div>
        </div>
      </div>

      {{-- Section 7: FOR OFFICE USE ONLY --}}
      <div class="border-2 border-black p-5 rounded-lg mt-6 bg-slate-50/50">
        <h3 class="font-black text-xs uppercase tracking-wider text-slate-950 mb-3">FOR OFFICE USE ONLY</h3>
        
        <div class="space-y-3 text-xs">
          <div class="flex items-end">
            <span class="font-bold w-48">Application No.:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="app_no_g11" class="fillable-input font-mono font-bold w-full" value="{{ $student?->id ? 'APP-XI-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) : '' }}" placeholder="APP-XI-....">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-48">Date Received:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="date" class="fillable-input font-mono w-full" value="{{ date('d/m/Y') }}">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-48">Group Allotted:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="stream_allotted" class="fillable-input font-bold text-blue-900 w-full" value="{{ $student?->stream_group_allotted ?? ($student?->stream_group ?? '') }}" placeholder="e.g. Science Group A">
            </div>
          </div>
          <div class="flex items-end">
            <span class="font-bold w-48">Admission Confirmed On:</span>
            <div class="solid-line-wrap flex-1">
              <input type="text" data-sync="confirmed_on" class="fillable-input font-mono w-full" value="{{ $student?->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : '' }}" placeholder="DD/MM/YYYY">
            </div>
          </div>
          <div class="flex items-end mt-4">
            <span class="font-bold w-48">Signature (Admission In-Charge):</span>
            <span class="solid-line-wrap flex-1"></span>
          </div>
        </div>
      </div>

    </div>

  </main>


  {{-- ══════════════════════════════════════════════════════════════════════════
       FORM 3: ADMISSION ENQUIRY FORM (PDF 3) — 1 Single Page
       Fillable in-place with authentic styling:
       - Double Outer Border Frame
       - Inner Rounded Header Box with CBSE details
       - Centered Underlined Title "ADMISSION ENQUIRY FORM"
       - Exactly 12 clean rows with colons and dotted underlines
       - Date & Parent Signature footer
  ══════════════════════════════════════════════════════════════════════════ --}}
  <main id="form-enquiry" class="form-container hidden">
    
    <div class="a4-sheet" style="padding: 10mm 12mm;">
      
      {{-- Outer Double Border Frame matching PDF 3 exactly --}}
      <div class="border-[2.5px] border-slate-900 rounded-2xl p-6 min-h-[268mm] flex flex-col justify-between" style="outline: 1.5px solid #0f172a; outline-offset: -5px;">
        
        <div>
          {{-- Header Box with rounded inner border --}}
          <div class="border-[1.5px] border-slate-800 rounded-2xl p-3 sm:p-4 flex items-center justify-between mb-4">
            <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
              <img src="{{ asset('images/school-crest-transparent.png') }}" alt="Erode Public School Crest" class="w-18 h-18 object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
            </div>
            <div class="text-center flex-1 px-3">
              <h1 class="text-2xl sm:text-3xl font-extrabold tracking-wide text-slate-950 uppercase font-school-title leading-tight">
                ERODE PUBLIC SCHOOL
              </h1>
              <p class="text-[11px] font-bold text-slate-800 tracking-tight mt-0.5">
                SENIOR SECONDARY Affiliated to CBSE, New Delhi. (Aff.No.1931585)
              </p>
              <p class="text-[10px] font-semibold text-slate-700 mt-0.5">
                NO.420, VAKKIL THOTTAM, MANICKAMPALAYAM, ERODE - 638 004.
              </p>
              <p class="text-[10px] font-semibold text-slate-700">
                Ph : + 91 93677 54654, E-MAIL : calleps22@gmail.com
              </p>
            </div>
            <div class="w-12"></div>
          </div>

          {{-- Title: ADMISSION ENQUIRY FORM --}}
          <div class="text-center my-6">
            <h2 class="text-lg sm:text-xl font-extrabold uppercase tracking-wider text-slate-900 underline underline-offset-4 decoration-2">
              ADMISSION ENQUIRY FORM
            </h2>
          </div>

          {{-- 12 Clean Lined Fields with colons matching PDF 3 --}}
          <div class="space-y-4 text-xs sm:text-sm text-slate-900 px-3 mt-6 font-medium">
            
            <div class="flex items-end">
              <span class="w-64 font-bold">Name of the Student</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="student_name" class="fillable-input font-bold uppercase tracking-wider text-slate-950 w-full" value="{{ $enquiry?->student_name ?? ($student?->full_name ?? '') }}" placeholder="STUDENT FULL NAME">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Admission Required for class</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <select data-sync="class_id" class="fillable-input font-bold text-blue-900 w-full cursor-pointer">
                  <option value="">Select Class</option>
                  @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" @selected(($enquiry?->class_id ?? ($student?->currentEnrollment?->class_id ?? 0)) == $cls->id)>{{ $cls->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">School Last Studied</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="prev_school" class="fillable-input font-semibold w-full" value="{{ $enquiry?->last_school_studied ?? ($enquiry?->previous_school ?? ($student?->previous_school_name ?? '')) }}" placeholder="School name and location">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Date of Birth</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="dob_display" class="fillable-input font-semibold w-full" value="{{ ($enquiry?->dob ? \Carbon\Carbon::parse($enquiry->dob)->format('d/m/Y') : ($student?->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '')) }}" placeholder="DD/MM/YYYY">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Father's Name &amp; Qualification</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="father_name_qual" class="fillable-input font-semibold w-full" value="{{ $enquiry?->father_name ?? ($student?->father_name ?? '') }}{{ !empty($enquiry?->father_qualification ?? $student?->father_qualification) ? ' (' . ($enquiry?->father_qualification ?? $student?->father_qualification) . ')' : '' }}" placeholder="Father Name (Qualification)">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Mother's Name &amp; Qualification</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="mother_name_qual" class="fillable-input font-semibold w-full" value="{{ $enquiry?->mother_name ?? ($student?->mother_name ?? '') }}{{ !empty($enquiry?->mother_qualification ?? $student?->mother_qualification) ? ' (' . ($enquiry?->mother_qualification ?? $student?->mother_qualification) . ')' : '' }}" placeholder="Mother Name (Qualification)">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Father's Occupation &amp; Income</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="father_occ_inc" class="fillable-input font-semibold w-full" value="{{ $enquiry?->father_occupation ?? ($student?->father_occupation ?? '') }}{{ !empty($enquiry?->father_income ?? $student?->father_income) ? ' — ' . ($enquiry?->father_income ?? $student?->father_income) : '' }}" placeholder="Occupation — Annual Income">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Mother's Occupation &amp; Income</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="mother_occ_inc" class="fillable-input font-semibold w-full" value="{{ $enquiry?->mother_occupation ?? ($student?->mother_occupation ?? '') }}{{ !empty($enquiry?->mother_income ?? $student?->mother_income) ? ' — ' . ($enquiry?->mother_income ?? $student?->mother_income) : '' }}" placeholder="Occupation — Annual Income">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Father's Mobile No.</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="father_mobile" class="fillable-input font-mono font-bold text-slate-900 w-full" value="{{ $enquiry?->father_mobile ?? ($enquiry?->parent_mobile ?? ($student?->father_mobile ?? '')) }}" placeholder="10-digit mobile number">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Mother's Mobile No.</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="mother_mobile" class="fillable-input font-mono font-bold text-slate-900 w-full" value="{{ $enquiry?->mother_mobile ?? ($student?->mother_mobile ?? '') }}" placeholder="10-digit mobile number">
              </div>
            </div>

            <div class="flex items-end">
              <span class="w-64 font-bold">Referred by</span>
              <span class="font-bold mr-3">:</span>
              <div class="dotted-line-wrap flex-1">
                <input type="text" data-sync="referred_by" class="fillable-input font-semibold w-full" value="{{ $enquiry?->referred_by ?? ($enquiry?->referral_name ?? '') }}" placeholder="Parent / Staff name or source">
              </div>
            </div>

            <div>
              <div class="flex items-end">
                <span class="w-64 font-bold">Address</span>
                <span class="font-bold mr-3">:</span>
                <div class="dotted-line-wrap flex-1">
                  <input type="text" data-sync="address_line1" class="fillable-input font-semibold w-full" value="{{ $enquiry?->address ?? ($student?->residential_address ?? '') }}" placeholder="House No., Street, City, Pincode">
                </div>
              </div>
              <div class="dotted-line-wrap w-full mt-3">
                <input type="text" data-sync="address_line2" class="fillable-input font-semibold w-full" placeholder="(Additional address lines)">
              </div>
            </div>

          </div>
        </div>

        {{-- Footer Date & Parent Signature --}}
        <div class="flex items-end justify-between px-3 pt-14 pb-2 text-xs font-bold text-slate-900">
          <div>
            <span>DATE:</span>
            <div class="dotted-line-wrap w-36 ml-2">
              <input type="text" data-sync="date" class="fillable-input font-mono font-medium w-full text-center" value="{{ date('d/m/Y') }}">
            </div>
          </div>

          <div class="text-right">
            <span class="tracking-wide">PARENT / GUARDIAN'S SIGNATURE</span>
          </div>
        </div>

      </div>

    </div>

  </main>

  {{-- Interactive Script for Tabs, Auto-sync & Form Switching --}}
  <script>

    // Tab Switching
    function switchForm(formName) {
      document.querySelectorAll('.form-container').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-[#1e3a8a]', 'text-white', 'shadow-xs');
        btn.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/60');
        const badge = btn.querySelector('span:nth-child(2)');
        if (badge) {
          badge.classList.remove('bg-white/20', 'text-white');
          badge.classList.add('bg-slate-200', 'text-slate-700');
        }
      });

      const targetForm = document.getElementById('form-' + formName);
      const targetTab  = document.getElementById('tab-' + formName);

      if (targetForm) targetForm.classList.remove('hidden');
      if (targetTab) {
        targetTab.classList.add('bg-[#1e3a8a]', 'text-white', 'shadow-xs');
        targetTab.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/60');
        const badge = targetTab.querySelector('span:nth-child(2)');
        if (badge) {
          badge.classList.add('bg-white/20', 'text-white');
          badge.classList.remove('bg-slate-200', 'text-slate-700');
        }
      }

      const url = new URL(window.location);
      url.searchParams.set('form', formName);
      window.history.replaceState({}, '', url);
    }

    // Auto-sync synchronized input fields across all 3 forms
    document.addEventListener('input', (e) => {
      const syncKey = e.target.getAttribute('data-sync');
      if (!syncKey) return;
      const val = e.target.value;

      document.querySelectorAll(`[data-sync="${syncKey}"]`).forEach(el => {
        if (el !== e.target && el.tagName === e.target.tagName) {
          el.value = val;
        }
      });

      // Handle Compound fields for Enquiry
      if (syncKey === 'father_name' || syncKey === 'father_qualification') {
        const fn = getSyncVal('father_name');
        const fq = getSyncVal('father_qualification');
        setSyncVal('father_name_qual', fn + (fq ? ` (${fq})` : ''));
      }
      if (syncKey === 'mother_name' || syncKey === 'mother_qualification') {
        const mn = getSyncVal('mother_name');
        const mq = getSyncVal('mother_qualification');
        setSyncVal('mother_name_qual', mn + (mq ? ` (${mq})` : ''));
      }
      if (syncKey === 'father_occupation' || syncKey === 'father_income') {
        const fo = getSyncVal('father_occupation');
        const fi = getSyncVal('father_income');
        setSyncVal('father_occ_inc', fo + (fi ? ` — ${fi}` : ''));
      }
      if (syncKey === 'mother_occupation' || syncKey === 'mother_income') {
        const mo = getSyncVal('mother_occupation');
        const mi = getSyncVal('mother_income');
        setSyncVal('mother_occ_inc', mo + (mi ? ` — ${mi}` : ''));
      }
    });

    function getSyncVal(key) {
      const el = document.querySelector(`[data-sync="${key}"]`);
      return el ? el.value : '';
    }

    function setSyncVal(key, val) {
      document.querySelectorAll(`[data-sync="${key}"]`).forEach(el => {
        el.value = val || '';
      });
    }

    // DOB Digit Cells Auto-Advance
    document.querySelectorAll('#dob-container-admission .digit-cell').forEach((input, index, list) => {
      input.addEventListener('input', (e) => {
        if (input.value.length === 1 && index < list.length - 1) {
          list[index + 1].focus();
        }
        collectDobDigits();
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          list[index - 1].focus();
        }
      });
    });

    function collectDobDigits() {
      const digits = Array.from(document.querySelectorAll('#dob-container-admission .digit-cell')).map(el => el.value || '').join('');
      if (digits.length === 8) {
        const formatted = `${digits.substring(0,2)}/${digits.substring(2,4)}/${digits.substring(4,8)}`;
        setSyncVal('dob_display', formatted);
        const iso = `${digits.substring(4,8)}-${digits.substring(2,4)}-${digits.substring(0,2)}`;
        setSyncVal('dob', iso);
      }
    }

    // Aadhaar Digit Cells Auto-Advance & Paste
    const aadhaarCells = Array.from(document.querySelectorAll('#aadhaar-container-admission .digit-cell'));
    aadhaarCells.forEach((input, index) => {
      input.addEventListener('input', () => {
        if (input.value.length === 1 && index < aadhaarCells.length - 1) {
          aadhaarCells[index + 1].focus();
        }
        collectAadhaarDigits();
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          aadhaarCells[index - 1].focus();
        }
      });
      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 12);
        for (let i = 0; i < text.length; i++) {
          if (aadhaarCells[i]) aadhaarCells[i].value = text[i];
        }
        collectAadhaarDigits();
        if (aadhaarCells[Math.min(text.length, 11)]) {
          aadhaarCells[Math.min(text.length, 11)].focus();
        }
      });
    });

    function collectAadhaarDigits() {
      const full = aadhaarCells.map(el => el.value || '').join('');
      setSyncVal('aadhaar_no', full);
    }

    function setAadhaarBoxes(str) {
      const clean = (str || '').replace(/\D/g, '').substring(0, 12);
      aadhaarCells.forEach((cell, idx) => {
        cell.value = clean[idx] || '';
      });
      setSyncVal('aadhaar_no', clean);
    }

    function setDobBoxes(dmyStr) {
      if (!dmyStr) return;
      const clean = dmyStr.replace(/\D/g, '');
      const cells = document.querySelectorAll('#dob-container-admission .digit-cell');
      cells.forEach((cell, idx) => {
        cell.value = clean[idx] || '';
      });
      setSyncVal('dob_display', dmyStr);
    }

    // Gender Selection
    function selectGender(gen) {
      setSyncVal('gender', gen);
      const isMale = gen === 'male';
      document.getElementById('check-gender-male').textContent = isMale ? '✓' : '';
      document.getElementById('check-gender-female').textContent = !isMale ? '✓' : '';
      const m11 = document.getElementById('check-gender-male-g11');
      const f11 = document.getElementById('check-gender-female-g11');
      if (m11) m11.textContent = isMale ? '✓' : '';
      if (f11) f11.textContent = !isMale ? '✓' : '';
    }

    // Stream Selection for Grade XI
    function selectStreamGroup(grp) {
      setSyncVal('stream_group', grp);
      setSyncVal('stream_allotted', grp);
      document.getElementById('check-group-a').textContent = grp === 'GROUP A' ? '✓' : '';
      document.getElementById('check-group-b').textContent = grp === 'GROUP B' ? '✓' : '';
      document.getElementById('check-group-c').textContent = grp === 'GROUP C' ? '✓' : '';
      document.getElementById('check-group-d').textContent = grp === 'GROUP D' ? '✓' : '';
    }

    // Toggle Generic Custom Checkboxes
    function toggleCustomCheck(label) {
      const box = label.querySelector('.custom-check-box');
      if (box) {
        box.textContent = box.textContent.trim() === '✓' ? '' : '✓';
      }
    }

    // Photo Upload Trigger & Preview
    function triggerPhotoUpload() {
      document.getElementById('photo-upload-input').click();
    }

    function handlePhotoUpload(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          ['admission', 'grade11'].forEach(type => {
            const preview = document.getElementById('preview-photo-' + type);
            const placeholder = document.getElementById('placeholder-photo-' + type);
            if (preview && placeholder) {
              preview.src = e.target.result;
              preview.classList.remove('hidden');
              placeholder.classList.add('hidden');
            }
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Auto-select tab based on URL query parameter (?form=admission | grade11 | enquiry)
    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const formParam = params.get('form') || '{{ $formType ?? "admission" }}';
      if (formParam && ['admission', 'grade11', 'enquiry'].includes(formParam)) {
        switchForm(formParam);
      }
    });
  </script>

</body>
</html>
