<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parent &amp; Guardian Visitor Pass — {{ $student->full_name }} ({{ $student->admission_no }})</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: radial-gradient(circle at 50% 0%, #1e293b 0%, #0f172a 100%);
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    @media print {
      body { background: #fff !important; color: #000 !important; }
      .no-print { display: none !important; }
      .print-card-wrapper { box-shadow: none !important; border: 2px solid #0f172a !important; }
    }
  </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col items-center justify-center p-4 sm:p-6">

  <div class="w-full max-w-lg mx-auto space-y-5">

    {{-- Top Action Bar (Mobile-friendly) --}}
    <div class="flex items-center justify-between no-print px-1">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
          Active Campus Escort Pass
        </span>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Print Pass
        </button>
      </div>
    </div>

    {{-- ── Physical/Digital PVC Badge Card ────────────────────────────── --}}
    <div id="visitorBadge" class="print-card-wrapper bg-white text-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 transition relative">

      {{-- Header Ribbon --}}
      <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-5 sm:p-6 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-10 w-36 h-36 bg-blue-500/10 rounded-full blur-2xl"></div>
        <div class="relative flex items-center justify-between gap-3">
          <div class="space-y-0.5">
            <div class="flex items-center gap-2">
              <span class="text-xs font-extrabold tracking-widest uppercase bg-amber-400 text-slate-950 px-2 py-0.5 rounded-md">OFFICIAL ESCORT PASS</span>
            </div>
            <h1 class="text-lg sm:text-xl font-black tracking-tight leading-snug">{{ $school->school_name ?? 'DASA EDUGROUP' }}</h1>
            <p class="text-[11px] text-blue-200 font-medium">Affiliation No: {{ $school->affiliation_no ?? 'CBSE/AFF/2026/089' }} &bull; Help: {{ $school->phone ?? '+91 98765 43210' }}</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 text-xl font-black text-amber-300 shadow-inner">
            🏫
          </div>
        </div>
      </div>

      {{-- Student Core Profile Strip --}}
      <div class="p-4 sm:p-5 bg-slate-50 border-b border-slate-200/80 flex items-center gap-4">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white border-2 border-blue-600/30 overflow-hidden shrink-0 shadow-sm relative">
          @if($student->photo)
            <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
          @else
            <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-black text-xl">
              {{ substr($student->first_name, 0, 1) }}
            </div>
          @endif
        </div>
        <div class="min-w-0 flex-1 space-y-1">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-base sm:text-lg font-black text-slate-900 truncate leading-tight">{{ $student->full_name }}</h2>
            @if($student->blood_group)
              <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 text-[11px] font-mono font-black border border-rose-200 shrink-0">
                {{ $student->blood_group }}
              </span>
            @endif
          </div>
          <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-xs text-slate-600">
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Admission No</span>
              <span class="font-mono font-black text-blue-700 text-xs sm:text-sm">{{ $student->admission_no }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Class &amp; Section</span>
              <span class="font-bold text-slate-800 text-xs sm:text-sm">
                Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }})
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- Authorized Visitors Section --}}
      <div class="p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Authorized Campus Visitors &amp; Escorts
          </h3>
          <span class="text-[10px] font-bold text-slate-400">Photo ID Verified</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

          {{-- Father Card --}}
          <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
              @if($student->father_photo)
                <img src="{{ asset('storage/' . $student->father_photo) }}" class="w-full h-full object-cover" alt="Father">
              @else
                <div class="w-full h-full flex items-center justify-center text-slate-400 text-lg">👨</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-blue-600 tracking-wider block">Father</span>
              <p class="text-xs font-bold text-slate-900 truncate leading-snug">{{ $student->father_name ?? $student->parent_name ?? 'Not Listed' }}</p>
              <p class="text-[11px] font-mono text-slate-500 truncate">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</p>
            </div>
          </div>

          {{-- Mother Card --}}
          <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-rose-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
              @if($student->mother_photo)
                <img src="{{ asset('storage/' . $student->mother_photo) }}" class="w-full h-full object-cover" alt="Mother">
              @else
                <div class="w-full h-full flex items-center justify-center text-slate-400 text-lg">👩</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-rose-600 tracking-wider block">Mother</span>
              <p class="text-xs font-bold text-slate-900 truncate leading-snug">{{ $student->mother_name ?? 'Not Listed' }}</p>
              <p class="text-[11px] font-mono text-slate-500 truncate">{{ $student->mother_mobile ?? '—' }}</p>
            </div>
          </div>

          {{-- Guardian Card (If applicable) --}}
          @if($student->guardian_name)
          <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-amber-300 transition flex items-center gap-3 sm:col-span-2">
            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
              @if($student->guardian_photo)
                <img src="{{ asset('storage/' . $student->guardian_photo) }}" class="w-full h-full object-cover" alt="Guardian">
              @else
                <div class="w-full h-full flex items-center justify-center text-slate-400 text-lg">👤</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-amber-600 tracking-wider block">Authorized Guardian ({{ $student->guardian_relation ?? 'Guardian' }})</span>
              <p class="text-xs font-bold text-slate-900 truncate leading-snug">{{ $student->guardian_name }}</p>
              <p class="text-[11px] font-mono text-slate-500 truncate">{{ $student->guardian_mobile ?? '—' }}</p>
            </div>
          </div>
          @endif

        </div>

        {{-- Security Gate QR Code Verification Box --}}
        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white flex items-center gap-4 border border-slate-800">
          <div class="bg-white p-2 rounded-xl shrink-0 shadow-md">
            @if(!empty($qrCodeSvg))
              <div class="w-24 h-24 flex items-center justify-center">
                {!! $qrCodeSvg !!}
              </div>
            @else
              <div class="w-24 h-24 bg-slate-100 flex items-center justify-center text-[10px] font-mono text-slate-600 text-center p-1">
                SCAN FOR GATE VERIFY
              </div>
            @endif
          </div>
          <div class="space-y-1.5 min-w-0">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              <h4 class="text-xs font-black tracking-tight text-white uppercase">Security Gate QR Verification</h4>
            </div>
            <p class="text-[11px] text-slate-300 leading-snug">
              Security officers scan this QR code at campus gates to verify authentic escort identity &amp; student pickup clearance.
            </p>
            <p class="text-[10px] font-mono text-blue-300">Valid Academic Year: {{ date('Y') }}–{{ date('Y') + 1 }}</p>
          </div>
        </div>

      </div>

      {{-- Card Bottom Footer Terms --}}
      <div class="bg-slate-100 px-5 py-3 border-t border-slate-200 text-center text-[10px] text-slate-500 leading-tight">
        ⚠️ This card must be presented for campus entry, Parent-Teacher Meetings (PTM), and student pickup. Non-transferable.
      </div>

    </div>

    {{-- Share & Download Buttons (No print) --}}
    <div class="space-y-2 no-print">
      @php
        $whatsappText = urlencode("Hello, here is the official DASA EduERP Campus Visitor & Escort Pass for {$student->full_name} (Adm: {$student->admission_no}):\n" . url()->current());
      @endphp
      <a href="https://api.whatsapp.com/send?text={{ $whatsappText }}" target="_blank"
         class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-900/30 transition flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        <span>Share Visitor Pass via WhatsApp</span>
      </a>

      <button onclick="navigator.clipboard.writeText(window.location.href); alert('Visitor Pass URL copied to clipboard!');"
              class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition border border-slate-700 cursor-pointer text-center">
        Copy Pass Link to Clipboard
      </button>
    </div>

  </div>

</body>
</html>
