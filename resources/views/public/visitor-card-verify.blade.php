<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Security Verification &bull; {{ $school->school_name ?? 'DASA EDUGROUP' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: radial-gradient(circle at 50% 0%, #0f172a 0%, #020617 100%);
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
  </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col items-center justify-center p-4">

  <div class="w-full max-w-md mx-auto space-y-4">

    @if($isValid && $student)
      {{-- Security Gate Pass: AUTHENTICATED --}}
      <div class="bg-white text-slate-900 rounded-3xl overflow-hidden shadow-2xl border-4 border-emerald-500">
        
        {{-- Status Header --}}
        <div class="bg-emerald-600 text-white p-5 text-center space-y-2">
          <div class="w-14 h-14 mx-auto rounded-full bg-white/20 flex items-center justify-center text-3xl shadow-inner">
            ✓
          </div>
          <h1 class="text-xl font-black tracking-tight uppercase">CAMPUS PASS AUTHENTICATED</h1>
          <p class="text-xs text-emerald-100 font-medium">Clearance Granted for Campus Entry &bull; {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        {{-- Student Details --}}
        <div class="p-5 border-b border-slate-100 flex items-center gap-4 bg-slate-50">
          <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 overflow-hidden shrink-0 shadow-xs">
            @if($student->photo)
              <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
            @else
              <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-bold text-xl">
                {{ substr($student->first_name, 0, 1) }}
              </div>
            @endif
          </div>
          <div class="min-w-0 flex-1 space-y-0.5">
            <h2 class="text-base font-black text-slate-900 truncate">{{ $student->full_name }}</h2>
            <p class="text-xs font-mono font-bold text-blue-700">Adm: {{ $student->admission_no }}</p>
            <p class="text-xs text-slate-600 font-medium">
              Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }}) &bull; Roll: {{ $student->roll_number ?? '—' }}
            </p>
          </div>
        </div>

        {{-- Authorized Escorts Verification --}}
        <div class="p-5 space-y-3">
          <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Authorized Escorts for this Student</h3>
          
          <div class="space-y-2.5">
            {{-- Father --}}
            <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 bg-slate-50/70">
              <div class="w-10 h-10 rounded-xl bg-slate-200 overflow-hidden shrink-0">
                @if($student->father_photo)
                  <img src="{{ asset('storage/' . $student->father_photo) }}" class="w-full h-full object-cover" alt="Father">
                @else
                  <div class="w-full h-full flex items-center justify-center text-sm">👨</div>
                @endif
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold uppercase text-blue-600 block">Father</span>
                <p class="text-xs font-bold text-slate-900 truncate">{{ $student->father_name ?? $student->parent_name ?? '—' }}</p>
                <p class="text-[11px] font-mono text-slate-500">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</p>
              </div>
              <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-bold">Authorized</span>
            </div>

            {{-- Mother --}}
            <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 bg-slate-50/70">
              <div class="w-10 h-10 rounded-xl bg-slate-200 overflow-hidden shrink-0">
                @if($student->mother_photo)
                  <img src="{{ asset('storage/' . $student->mother_photo) }}" class="w-full h-full object-cover" alt="Mother">
                @else
                  <div class="w-full h-full flex items-center justify-center text-sm">👩</div>
                @endif
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold uppercase text-rose-600 block">Mother</span>
                <p class="text-xs font-bold text-slate-900 truncate">{{ $student->mother_name ?? '—' }}</p>
                <p class="text-[11px] font-mono text-slate-500">{{ $student->mother_mobile ?? '—' }}</p>
              </div>
              <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-bold">Authorized</span>
            </div>

            {{-- Guardian (if present) --}}
            @if($student->guardian_name)
            <div class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 bg-slate-50/70">
              <div class="w-10 h-10 rounded-xl bg-slate-200 overflow-hidden shrink-0">
                @if($student->guardian_photo)
                  <img src="{{ asset('storage/' . $student->guardian_photo) }}" class="w-full h-full object-cover" alt="Guardian">
                @else
                  <div class="w-full h-full flex items-center justify-center text-sm">👤</div>
                @endif
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold uppercase text-amber-600 block">Guardian ({{ $student->guardian_relation ?? 'Guardian' }})</span>
                <p class="text-xs font-bold text-slate-900 truncate">{{ $student->guardian_name }}</p>
                <p class="text-[11px] font-mono text-slate-500">{{ $student->guardian_mobile ?? '—' }}</p>
              </div>
              <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-bold">Authorized</span>
            </div>
            @endif
          </div>
        </div>

        {{-- Verification Footer --}}
        <div class="bg-slate-100 p-4 text-center border-t border-slate-200">
          <p class="text-xs text-slate-600 font-semibold">{{ $school->school_name ?? 'DASA EDUGROUP' }} Security Gate Log</p>
          <p class="text-[10px] text-slate-400 font-mono mt-0.5">Token: {{ substr($token, 0, 16) }}... &bull; Status: ACTIVE</p>
        </div>

      </div>
    @else
      {{-- Invalid or Expired Pass --}}
      <div class="bg-white text-slate-900 rounded-3xl overflow-hidden shadow-2xl border-4 border-rose-500 p-6 text-center space-y-4">
        <div class="w-16 h-16 mx-auto rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-3xl font-black">
          ✕
        </div>
        <h1 class="text-xl font-black text-rose-600 tracking-tight uppercase">INVALID / UNAPPROVED PASS</h1>
        <p class="text-xs text-slate-600 leading-relaxed">
          This campus visitor pass could not be authenticated, or the student admission has not yet been fully approved. Please escort the visitor to the Main Admissions Office for assistance.
        </p>
        <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-xs font-mono text-rose-800">
          Security Alert: Do not grant unaccompanied student pickup.
        </div>
      </div>
    @endif

  </div>

</body>
</html>
