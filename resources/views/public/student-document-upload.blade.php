<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Upload Certificates & Documents — {{ $student->full_name }} | {{ $school->school_name ?? 'DASA EduERP' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="min-h-screen text-slate-800 antialiased selection:bg-indigo-500 selection:text-white pb-20">

  {{-- ── Top Navigation / Brand Bar ── --}}
  <header class="bg-gradient-to-r from-blue-900 via-indigo-900 to-indigo-800 text-white shadow-lg sticky top-0 z-30 border-b border-indigo-700/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white shadow-inner">
          <svg class="w-6 h-6 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white leading-tight">
            {{ $school->school_name ?? 'DASA EDUGROUP' }}
          </h1>
          <p class="text-[11px] text-indigo-200/90 font-medium">
            Student Document Verification &amp; Upload Portal
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 backdrop-blur-xs">
          <svg class="w-3.5 h-3.5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
          </svg>
          <span class="hidden sm:inline">Secure 256-Bit SSL</span>
          <span class="sm:hidden">Secure</span>
        </span>
      </div>
    </div>
  </header>

  {{-- ── Main Container ── --}}
  <main class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 space-y-6">

    {{-- Flash Notifications --}}
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs animate-fadeIn">
        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm font-semibold">
          {{ session('success') }}
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
          <p class="font-bold">Please check the errors below:</p>
          <ul class="list-disc list-inside mt-1 font-medium space-y-0.5">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- ── Student Verified Header Card ── --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 relative overflow-hidden">
      {{-- Gradient glow accent --}}
      <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

      <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 relative z-10">
        {{-- Student Photo / Avatar --}}
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shrink-0 overflow-hidden shadow-md ring-4 ring-slate-100 relative">
          @if($student->photo)
            <img src="{{ asset('storage/'.$student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
          @else
            <span class="text-white text-3xl font-extrabold tracking-wider">
              {{ strtoupper(substr($student->first_name,0,1) . substr($student->last_name,0,1)) }}
            </span>
          @endif
        </div>

        {{-- Student Details --}}
        <div class="flex-1 text-center sm:text-left space-y-2">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
              <div class="flex items-center justify-center sm:justify-start gap-2.5 flex-wrap">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                  {{ $student->full_name }}
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                  New Admission
                </span>
              </div>
              <p class="text-xs text-slate-500 font-medium mt-0.5">
                Official Student Record &bull; Academic Year: <span class="font-bold text-slate-700">{{ $student->currentEnrollment?->academicYear?->name ?? '2025-2026' }}</span>
              </p>
            </div>
          </div>

          {{-- Quick Key-Value Badges --}}
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 text-xs">
            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Admission No</span>
              <span class="font-mono font-extrabold text-indigo-700 text-sm">{{ $student->admission_number ?? 'Pending' }}</span>
            </div>

            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Standard / Class</span>
              <span class="font-bold text-slate-800 text-sm">
                {{ $student->currentEnrollment?->class?->name ?? 'Class ' . ($student->class_id ?? '5') }}
                @if($student->currentEnrollment?->section)
                  - {{ $student->currentEnrollment->section->name }}
                @endif
              </span>
            </div>

            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Roll Number</span>
              <span class="font-mono font-bold text-slate-800 text-sm">{{ $student->roll_number ?? 'Assigned' }}</span>
            </div>

            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Date of Birth</span>
              <span class="font-bold text-slate-800 text-sm">{{ $student->dob?->format('d M Y') ?? '—' }}</span>
            </div>
          </div>

          {{-- Parent Contact --}}
          <div class="pt-2 flex items-center justify-center sm:justify-start gap-4 text-xs text-slate-600 flex-wrap">
            @if($student->father_name)
              <span><strong>Parent / Guardian:</strong> {{ $student->father_name }}</span>
            @endif
            @if($student->father_mobile || $student->mobile)
              <span><strong>Mobile:</strong> {{ $student->father_mobile ?? $student->mobile }}</span>
            @endif
            @if($student->category)
              <span><strong>Category:</strong> <span class="capitalize">{{ $student->category }}</span></span>
            @endif
          </div>
        </div>
      </div>

      {{-- ── Live Upload Progress Tracker ── --}}
      <div class="mt-6 pt-5 border-t border-slate-100 space-y-2">
        @php
          $percent = $totalCategories > 0 ? round(($uploadedCount / $totalCategories) * 100) : 0;
        @endphp
        <div class="flex items-center justify-between text-xs font-bold">
          <div class="flex items-center gap-2">
            <span class="text-slate-700">Document Submission Progress</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $uploadedCount >= $mandatoryCount ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
              {{ $uploadedCount }} of {{ $totalCategories }} Uploaded
            </span>
          </div>
          <span class="font-mono text-indigo-700 font-extrabold text-sm">{{ $percent }}%</span>
        </div>

        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden shadow-inner p-0.5">
          <div class="h-full rounded-full transition-all duration-500 {{ $percent >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-600 to-indigo-600' }}"
               style="width: {{ $percent }}%"></div>
        </div>

        <p class="text-[11px] text-slate-500 font-medium">
          Please upload clear photos or PDF scans of the requested certificates. You can use your mobile camera directly.
        </p>
      </div>
    </div>

    {{-- ── Document Cards Grid ── --}}
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Certificates &amp; Soft Copies</h3>
          <p class="text-xs text-slate-500">Tap "Upload" on any document to take a photo or select a file</p>
        </div>
        <span class="text-xs font-semibold text-slate-400">PDF, JPG, PNG &bull; Max 10MB</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($categories as $key => $cat)
          @php
            $doc = $documents[$key] ?? null;
            $isUploaded = (bool) $doc;
            $status = $doc?->status ?? 'missing';
          @endphp

          <div x-data="{
                openUpload: false,
                isUploading: false,
                uploadSuccess: false,
                fileName: '',
                handleFileSelect(e) {
                  if (e.target.files.length > 0) {
                    this.fileName = e.target.files[0].name;
                  }
                }
               }"
               class="bg-white rounded-2xl border transition-all duration-200 p-5 flex flex-col justify-between shadow-2xs hover:shadow-sm
                      {{ $status === 'verified' ? 'border-emerald-200/90 bg-emerald-50/20' : ($status === 'rejected' ? 'border-rose-300 bg-rose-50/20' : ($isUploaded ? 'border-indigo-200/80' : 'border-slate-200/80')) }}">

            <div>
              {{-- Top Row: Icon + Title + Status Badge --}}
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs
                    {{ $status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($status === 'rejected' ? 'bg-rose-100 text-rose-700' : ($isUploaded ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600')) }}">
                    @if($key === 'aadhaar')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                    @elseif($key === 'birth_certificate')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @elseif($key === 'caste')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    @elseif($key === 'pan')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    @elseif($key === 'photo')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    @elseif($key === 'tc' || $key === 'marksheet')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @endif
                  </div>

                  <div>
                    <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $cat['title'] }}</h4>
                    <p class="text-[11px] text-slate-500 line-clamp-1">{{ $cat['subtitle'] }}</p>
                  </div>
                </div>

                {{-- Status Pill --}}
                <div>
                  @if($status === 'verified')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                      <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                      Verified
                    </span>
                  @elseif($status === 'rejected')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                      Needs Re-upload
                    </span>
                  @elseif($isUploaded)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                      Under Review
                    </span>
                  @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat['required'] ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                      {{ $cat['badge'] }}
                    </span>
                  @endif
                </div>
              </div>

              {{-- Description or Uploaded File Details --}}
              <div class="mt-3 text-xs">
                @if($isUploaded)
                  <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 overflow-hidden">
                      <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                      </svg>
                      <span class="font-mono text-slate-700 font-medium truncate" title="{{ $doc->original_name }}">
                        {{ $doc->original_name }}
                      </span>
                    </div>

                    <a href="{{ route('public.student.documents.download', [$student->document_token, $doc->id]) }}" target="_blank"
                       class="text-indigo-600 hover:text-indigo-800 font-bold shrink-0 text-[11px] underline">
                      View
                    </a>
                  </div>

                  @if($status === 'rejected' && $doc->remarks)
                    <div class="mt-2 text-[11px] p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 font-medium">
                      <strong>Office Note:</strong> {{ $doc->remarks }}
                    </div>
                  @endif
                @else
                  <p class="text-slate-500 text-[11px] leading-relaxed">
                    {{ $cat['description'] }}
                  </p>
                @endif
              </div>
            </div>

            {{-- Bottom Actions / Form Trigger --}}
            <div class="mt-4 pt-3 border-t border-slate-100">
              <div x-show="!openUpload" class="flex items-center justify-between">
                <button type="button" @click="openUpload = true"
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs cursor-pointer
                               {{ $isUploaded ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                  </svg>
                  <span>{{ $isUploaded ? 'Re-upload / Replace File' : 'Upload ' . $cat['title'] }}</span>
                </button>
              </div>

              {{-- Upload Form --}}
              <div x-show="openUpload" x-cloak class="space-y-3">
                <form method="POST" action="{{ route('public.student.documents.upload', $student->document_token) }}" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="document_type" value="{{ $key }}">

                  <div class="border-2 border-dashed border-indigo-200 rounded-xl p-3 bg-indigo-50/40 text-center hover:bg-indigo-50/80 transition">
                    <input type="file" name="file" id="file-{{ $key }}" required
                           accept=".pdf,.jpg,.jpeg,.png,.webp" capture="environment"
                           @change="handleFileSelect($event)"
                           class="hidden">

                    <label for="file-{{ $key }}" class="cursor-pointer block space-y-1">
                      <div class="w-8 h-8 mx-auto rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                      </div>
                      <p class="text-xs font-bold text-indigo-900" x-text="fileName ? fileName : 'Take Photo or Choose File'"></p>
                      <p class="text-[10px] text-slate-500">Camera snap supported on phone</p>
                    </label>
                  </div>

                  <div class="flex items-center gap-2 mt-2">
                    <button type="submit"
                            class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Submit File
                    </button>
                    <button type="button" @click="openUpload = false; fileName = ''"
                            class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition cursor-pointer">
                      Cancel
                    </button>
                  </div>
                </form>
              </div>

            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- ── Bottom Confirmation & Help Card ── --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-4 text-center">
      <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>

      <div class="max-w-md mx-auto">
        <h4 class="text-base font-bold text-slate-900">What happens after you upload?</h4>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Your soft copies are automatically uploaded to <strong>{{ $student->full_name }}'s</strong> official school profile.
          The school admissions and verification desk will review the documents and mark them as verified. You can return to this link anytime using your QR code.
        </p>
      </div>

      <div class="pt-2 border-t border-slate-100 flex items-center justify-center gap-6 text-xs text-slate-400 font-medium">
        <span>Help Desk: {{ $school->phone ?? '+91 98765 43210' }}</span>
        <span>&bull;</span>
        <span>Email: {{ $school->email ?? 'admissions@dasaedugroup.com' }}</span>
      </div>
    </div>

    {{-- Footer Copyright --}}
    <footer class="text-center text-xs text-slate-400 pt-4">
      <p>&copy; {{ date('Y') }} {{ $school->school_name ?? 'DASA EDUGROUP' }}. All rights reserved.</p>
    </footer>

  </main>

</body>
</html>
