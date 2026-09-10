@extends('layouts.app')

@section('title', 'Parent & Guardian Visitor Card — ' . $student->full_name)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

  {{-- Top Navigation & Action Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('students.show', $student->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200 shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-black text-slate-900">Parent &amp; Guardian Campus Visitor Card</h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Official Pass</span>
        </div>
        <p class="text-xs text-slate-500 font-medium">Student: {{ $student->full_name }} &bull; Adm: {{ $student->admission_no }} &bull; Class: {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }})</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      @php
        $whatsappMsg = urlencode("Dear Parent, Here is your official DASA EduERP Campus Visitor & Escort Pass for {$student->full_name} (Adm: {$student->admission_no}):\n" . route('public.visitor-card.view', ['token' => $student->parent_visitor_pass_token]));
      @endphp
      <a href="https://api.whatsapp.com/send?text={{ $whatsappMsg }}" target="_blank"
         class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        Send to Parent via WhatsApp
      </a>

      <button onclick="window.print()" class="btn btn-sm btn-primary flex items-center gap-1.5 shadow-xs cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Physical Pass
      </button>

      <a href="{{ route('public.visitor-card.view', ['token' => $student->parent_visitor_pass_token]) }}" target="_blank"
         class="btn btn-sm btn-secondary flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Open Mobile View
      </a>
    </div>
  </div>

  {{-- ── Printable Visitor Pass Card Container ──────────────────────── --}}
  <div class="flex justify-center p-2 sm:p-4">
    <div id="physicalPassCard" class="w-full max-w-xl bg-white rounded-3xl border-2 border-slate-300 overflow-hidden shadow-xl print:shadow-none print:border-2 print:border-black print:rounded-none">

      {{-- Card Header --}}
      <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-5 print:bg-black print:text-black">
        <div class="flex items-center justify-between gap-3">
          <div class="space-y-0.5">
            <span class="text-[10px] font-black uppercase tracking-widest bg-amber-400 text-slate-950 px-2 py-0.5 rounded">
              CAMPUS VISITOR &amp; ESCORT PASS
            </span>
            <h2 class="text-lg font-black tracking-tight uppercase leading-snug">{{ $school->school_name ?? 'DASA EDUGROUP' }}</h2>
            <p class="text-[11px] text-blue-200 print:text-slate-600 font-medium">
              Affiliation No: {{ $school->affiliation_no ?? 'CBSE/AFF/2026/089' }} &bull; Help: {{ $school->phone ?? '+91 98765 43210' }}
            </p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-xl font-bold shrink-0">
            🏫
          </div>
        </div>
      </div>

      {{-- Student Identity Banner --}}
      <div class="bg-slate-50 p-4 border-b border-slate-200 flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-blue-500 overflow-hidden shrink-0 shadow-sm">
          @if($student->photo)
            <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
          @else
            <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-black text-xl">
              {{ substr($student->first_name, 0, 1) }}
            </div>
          @endif
        </div>
        <div class="min-w-0 flex-1 space-y-1">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 truncate">{{ $student->full_name }}</h3>
            @if($student->blood_group)
              <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[11px] font-mono font-black border border-rose-200">
                {{ $student->blood_group }}
              </span>
            @endif
          </div>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div>
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Admission No</span>
              <span class="font-mono font-black text-blue-700 text-sm">{{ $student->admission_no }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Class &amp; Roll</span>
              <span class="font-bold text-slate-800 text-xs">
                Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }}) &bull; #{{ $student->roll_number ?? '—' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- Authorized Parents & Guardian Escorts --}}
      <div class="p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
          <h4 class="text-xs font-black uppercase tracking-wider text-slate-600">Authorized Escorts &bull; Parents &amp; Guardian</h4>
          <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Security Cleared</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          {{-- Father --}}
          <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-200 border border-slate-300 overflow-hidden shrink-0">
              @if($student->father_photo)
                <img src="{{ asset('storage/' . $student->father_photo) }}" class="w-full h-full object-cover" alt="Father">
              @else
                <div class="w-full h-full flex items-center justify-center text-base">👨</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-blue-600 block">Father</span>
              <p class="text-xs font-bold text-slate-900 truncate">{{ $student->father_name ?? $student->parent_name ?? '—' }}</p>
              <p class="text-[11px] font-mono text-slate-500">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</p>
            </div>
          </div>

          {{-- Mother --}}
          <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-200 border border-slate-300 overflow-hidden shrink-0">
              @if($student->mother_photo)
                <img src="{{ asset('storage/' . $student->mother_photo) }}" class="w-full h-full object-cover" alt="Mother">
              @else
                <div class="w-full h-full flex items-center justify-center text-base">👩</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-rose-600 block">Mother</span>
              <p class="text-xs font-bold text-slate-900 truncate">{{ $student->mother_name ?? '—' }}</p>
              <p class="text-[11px] font-mono text-slate-500">{{ $student->mother_mobile ?? '—' }}</p>
            </div>
          </div>

          {{-- Guardian --}}
          @if($student->guardian_name)
          <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center gap-3 sm:col-span-2">
            <div class="w-12 h-12 rounded-xl bg-slate-200 border border-slate-300 overflow-hidden shrink-0">
              @if($student->guardian_photo)
                <img src="{{ asset('storage/' . $student->guardian_photo) }}" class="w-full h-full object-cover" alt="Guardian">
              @else
                <div class="w-full h-full flex items-center justify-center text-base">👤</div>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-extrabold uppercase text-amber-600 block">Guardian ({{ $student->guardian_relation ?? 'Guardian' }})</span>
              <p class="text-xs font-bold text-slate-900 truncate">{{ $student->guardian_name }}</p>
              <p class="text-[11px] font-mono text-slate-500">{{ $student->guardian_mobile ?? '—' }}</p>
            </div>
          </div>
          @endif
        </div>

        {{-- Gate Security QR Code Box --}}
        <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center gap-4 border border-slate-800 print:bg-white print:text-black print:border-black">
          <div class="bg-white p-2 rounded-xl shrink-0">
            @if(!empty($qrCodeSvg))
              <div class="w-24 h-24 flex items-center justify-center">
                {!! $qrCodeSvg !!}
              </div>
            @else
              <div class="w-24 h-24 bg-slate-100 flex items-center justify-center text-[10px] font-mono text-slate-700 text-center">
                SECURITY QR CODE
              </div>
            @endif
          </div>
          <div class="space-y-1 min-w-0">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              <h5 class="text-xs font-black uppercase text-white print:text-black">Security Gate Checkpoint</h5>
            </div>
            <p class="text-[11px] text-slate-300 print:text-slate-700 leading-snug">
              Scan QR code with smartphone to verify authorized escort profiles and grant campus entry clearance.
            </p>
            <p class="text-[10px] font-mono text-blue-300 print:text-blue-700">Valid Academic Year: {{ $currentYear?->name ?? (date('Y') . '–' . (date('Y')+1)) }}</p>
          </div>
        </div>

      </div>

      {{-- Card Bottom Disclaimer --}}
      <div class="bg-slate-100 px-5 py-3 border-t border-slate-200 text-center text-[10px] text-slate-500">
        Non-transferable &bull; Strictly for campus visits, PTM meetings, and authorized child pickup &bull; Issued by Admissions Office
      </div>

    </div>
  </div>

</div>

<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #physicalPassCard, #physicalPassCard * {
      visibility: visible;
    }
    #physicalPassCard {
      position: absolute;
      left: 50%;
      top: 20px;
      transform: translateX(-50%);
      width: 100% !important;
      max-width: 520px !important;
    }
  }
</style>
@endsection
