<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admission Enquiry — ERODE PUBLIC SCHOOL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #f1f5f9 100%);
    }
    .font-school-title {
      font-family: 'Playfair Display', Georgia, serif;
    }
    .input-field {
      width: 100%;
      border-radius: 0.75rem;
      border: 1.5px solid #cbd5e1;
      padding: 0.65rem 1rem;
      font-size: 0.875rem;
      background-color: #ffffff;
      color: #0f172a;
      transition: all 0.2s ease;
    }
    .input-field:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .field-label {
      display: block;
      font-size: 0.75rem;
      font-weight: 700;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 0.35rem;
    }
  </style>
</head>
<body class="min-h-screen py-8 px-4 sm:px-6 flex flex-col justify-between">

  {{-- Top Navigation Bar --}}
  <div class="max-w-3xl mx-auto w-full mb-6 flex items-center justify-between text-xs font-semibold text-slate-600">
    <a href="{{ route('home') }}" class="flex items-center gap-1.5 hover:text-blue-600 transition">
      &larr; Back to School Home
    </a>
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.print-form', ['form' => 'enquiry']) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-blue-500 hover:text-blue-600 shadow-sm transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Blank Enquiry Form (PDF)</span>
      </a>
      <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition">
        Staff Login &rarr;
      </a>
    </div>
  </div>

  {{-- Main Container Card --}}
  <div class="max-w-3xl mx-auto w-full bg-white rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden mb-12">
    
    {{-- School Header --}}
    <div class="bg-gradient-to-b from-slate-900 to-slate-950 text-white p-6 sm:p-8 text-center relative border-b-4 border-[#b91c1c]">
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
        <div class="w-20 h-20 bg-white/10 rounded-2xl p-2 flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-lg flex-shrink-0">
          <img src="{{ asset('images/school-crest-transparent.png') }}" alt="Erode Public School" class="w-16 h-16 object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
        </div>
        <div class="text-center sm:text-left">
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-wide uppercase font-school-title text-amber-400">
            ERODE PUBLIC SCHOOL
          </h1>
          <p class="text-xs font-semibold text-slate-300 mt-1">
            Senior Secondary Affiliated to CBSE, New Delhi. (Aff.No.1931585)
          </p>
          <p class="text-[11px] text-slate-400 mt-0.5">
            NO.420, Vakkil Thottam, Manickampalayam, Erode - 638 004.
          </p>
          <p class="text-[11px] text-slate-400">
            Ph: +91 93677 54654 &bull; Email: calleps22@gmail.com
          </p>
        </div>
      </div>

      {{-- Form Title Badge --}}
      <div class="inline-block mt-6 bg-[#b91c1c] text-white px-6 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-md">
        Official Admission Enquiry Form
      </div>
    </div>

    {{-- Form Content Area --}}
    <div class="p-6 sm:p-10">
      
      @if(session('success'))
      <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-5 mb-8 text-emerald-900 shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
          <h3 class="font-extrabold text-sm">Enquiry Submitted Successfully!</h3>
          <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
          <p class="text-xs text-emerald-600 mt-2 font-semibold">Our Admissions Counselor will contact you shortly.</p>
        </div>
      </div>
      @endif

      @if($errors->any())
      <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 mb-6 text-rose-800 text-xs">
        <p class="font-bold mb-1">Please fix the following issues:</p>
        <ul class="list-disc list-inside space-y-0.5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form method="POST" action="{{ route('enquiry.submit') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- ── 1. Student Information ─────────────────────────────── --}}
        <div>
          <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-200">
            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">1</span>
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Student Information</h2>
          </div>

          <div class="space-y-4">
            <div>
              <label class="field-label">Name of the Student (In Capital Letter) <span class="text-red-500">*</span></label>
              <input type="text" name="student_name" value="{{ old('student_name') }}" required
                     class="input-field uppercase font-bold tracking-wide" placeholder="E.G. AARAV KUMAR">
              @error('student_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="field-label">Admission Required for Class <span class="text-red-500">*</span></label>
                <select name="class_id" required class="input-field font-semibold">
                  <option value="">-- Select Class --</option>
                  @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected(old('class_id') == $c->id)>{{ $c->name }}</option>
                  @endforeach
                </select>
                @error('class_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
              </div>

              <div>
                <label class="field-label">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob') }}" class="input-field">
                @error('dob')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
            </div>

            <div>
              <label class="field-label">School Last Studied</label>
              <input type="text" name="last_school_studied" value="{{ old('last_school_studied') }}"
                     class="input-field" placeholder="Name of previous school (if applicable)">
            </div>
          </div>
        </div>

        {{-- ── 2. Parent / Guardian Details ────────────────────────── --}}
        <div>
          <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-200">
            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">2</span>
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Parent / Guardian Information</h2>
          </div>

          {{-- Father Info --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 mb-4">
            <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <span>Father's Details</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="field-label">Father's Name <span class="text-red-500">*</span></label>
                <input type="text" name="father_name" value="{{ old('father_name') }}" required
                       class="input-field font-semibold" placeholder="Father's full name">
              </div>
              <div>
                <label class="field-label">Father's Qualification</label>
                <input type="text" name="father_qualification" value="{{ old('father_qualification') }}"
                       class="input-field" placeholder="E.g. B.Tech, MBA, M.Com, Schooling">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="field-label">Father's Occupation</label>
                <input type="text" name="father_occupation" value="{{ old('father_occupation') }}"
                       class="input-field" placeholder="E.g. Business, Engineer, Doctor">
              </div>
              <div>
                <label class="field-label">Father's Monthly / Annual Income</label>
                <input type="text" name="father_income" value="{{ old('father_income') }}"
                       class="input-field" placeholder="E.g. 5,00,000 / year">
              </div>
            </div>

            <div>
              <label class="field-label">Father's Mobile No. <span class="text-red-500">*</span></label>
              <input type="tel" name="father_mobile" value="{{ old('father_mobile') }}" required
                     inputmode="numeric" maxlength="10" minlength="10" pattern="[6-9][0-9]{9}"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                     class="input-field font-mono font-bold" placeholder="10-digit mobile number">
              @error('father_mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Mother Info --}}
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
            <h3 class="text-xs font-bold text-pink-900 uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <span>Mother's Details</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="field-label">Mother's Name</label>
                <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                       class="input-field font-semibold" placeholder="Mother's full name">
              </div>
              <div>
                <label class="field-label">Mother's Qualification</label>
                <input type="text" name="mother_qualification" value="{{ old('mother_qualification') }}"
                       class="input-field" placeholder="E.g. M.Sc, B.Ed, Graduate">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="field-label">Mother's Occupation</label>
                <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}"
                       class="input-field" placeholder="E.g. Homemaker, Teacher, IT">
              </div>
              <div>
                <label class="field-label">Mother's Monthly / Annual Income</label>
                <input type="text" name="mother_income" value="{{ old('mother_income') }}"
                       class="input-field" placeholder="E.g. Optional">
              </div>
            </div>

            <div>
              <label class="field-label">Mother's Mobile No.</label>
              <input type="tel" name="mother_mobile" value="{{ old('mother_mobile') }}"
                     inputmode="numeric" maxlength="10"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                     class="input-field font-mono" placeholder="10-digit mobile number (optional)">
              @error('mother_mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>

        {{-- ── 3. Address & Referral ───────────────────────────────── --}}
        <div>
          <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-200">
            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">3</span>
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Address &amp; Referral Details</h2>
          </div>

          <div class="space-y-4">
            <div>
              <label class="field-label">Residential Address</label>
              <textarea name="address" rows="3" class="input-field" placeholder="Full residential street address, locality, city, pincode">{{ old('address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="field-label">Referred By</label>
                <input type="text" name="referred_by" value="{{ old('referred_by') }}"
                       class="input-field" placeholder="Staff, Friend, Relative, Social Media, etc.">
              </div>
              <div>
                <label class="field-label">Email Address (Optional)</label>
                <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                       class="input-field" placeholder="For admission updates and notifications">
              </div>
            </div>
          </div>
        </div>

        {{-- ── 4. Document Attachments (Optional) ──────────────────── --}}
        <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-200">
          <label class="field-label text-blue-950">Supporting Documents (Optional)</label>
          <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                 class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-blue-200 rounded-xl p-1 bg-white">
          <p class="text-[11px] text-slate-500 mt-1.5">You may attach Birth Certificate, Previous Marksheet, or Transfer Certificate (Max 2MB per file).</p>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
          <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-extrabold text-sm shadow-xl transition transform active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
            <span>Submit Official Admission Enquiry</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </button>
        </div>

      </form>
    </div>

    {{-- Footer Info --}}
    <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 text-center text-xs text-slate-500">
      <p>Have questions? Call school admissions desk directly at <a href="tel:+919367754654" class="font-bold text-blue-700 hover:underline">+91 93677 54654</a> or email <a href="mailto:calleps22@gmail.com" class="font-bold text-blue-700 hover:underline">calleps22@gmail.com</a></p>
    </div>

  </div>

</body>
</html>
