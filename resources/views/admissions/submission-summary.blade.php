@extends('layouts.app')

@section('title', 'Admission Submitted — Awaiting Principal Approval')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Top Navigation --}}
  <div class="flex items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('admissions.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200 shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Admission Application Received</h1>
        <p class="text-xs text-slate-500 font-medium">Application Reference: {{ $student->admission_no }} &bull; Class {{ $student->currentEnrollment?->class?->name ?? '—' }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admissions.approvals') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4 text-[#8C2826]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span>View Approval Queue</span>
      </a>
      <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── 3-Stage Approval Progress Tracker ────────────────────────────── --}}
  <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="w-3 h-3 rounded-full bg-[#8C2826] animate-ping"></span>
        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Admission Approval Pipeline</h2>
      </div>
      <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
        Stage 2 of 3: Principal Review Pending
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
      {{-- Stage 1: Done --}}
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
          ✓
        </div>
        <div class="space-y-0.5 min-w-0">
          <p class="text-xs font-black text-emerald-900">1. Admin Desk Submission</p>
          <p class="text-[11px] text-emerald-700">Data, photos &amp; fees logged</p>
          <span class="text-[10px] font-semibold text-emerald-600">Completed {{ now()->format('d M, h:i A') }}</span>
        </div>
      </div>

      {{-- Stage 2: Pending --}}
      <div class="p-4 rounded-2xl bg-[#FFF5F5] border-2 border-[#FECACA] flex items-start gap-3 relative shadow-xs">
        <div class="w-8 h-8 rounded-xl bg-[#8C2826] text-white flex items-center justify-center font-bold text-sm shrink-0 animate-pulse">
          ⏳
        </div>
        <div class="space-y-0.5 min-w-0">
          <p class="text-xs font-black text-[#380E0D]">2. Principal Review</p>
          <p class="text-[11px] text-[#8C2826] font-medium">Pending Principal endorsement</p>
          <span class="text-[10px] font-bold text-[#5C1210] bg-[#FFF5F5] px-2 py-0.5 rounded">Sent to principal@schoolerp.in</span>
        </div>
      </div>

      {{-- Stage 3: Awaiting --}}
      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3 opacity-70">
        <div class="w-8 h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm shrink-0">
          3
        </div>
        <div class="space-y-0.5 min-w-0">
          <p class="text-xs font-black text-slate-800">3. Admin Final Confirmation</p>
          <p class="text-[11px] text-slate-500">Activates ID Card, TC &amp; Directory</p>
          <span class="text-[10px] text-slate-400">Locked until Step 2 completes</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── 2 Columns: Applicant Dossier + Home QR Document Slip ──────────── --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left: Student & Parent Summary (7 cols) --}}
    <div class="lg:col-span-7 space-y-6">
      
      {{-- Student Profile Card --}}
      <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
        <div class="flex items-center gap-4">
          <div class="w-20 h-20 rounded-2xl bg-slate-100 border-2 border-[#FECACA] overflow-hidden shrink-0">
            @if($student->photo)
              <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
            @else
              <div class="w-full h-full flex items-center justify-center bg-[#FFF5F5] text-[#8C2826] font-black text-2xl">
                {{ substr($student->first_name, 0, 1) }}
              </div>
            @endif
          </div>
          <div class="min-w-0 flex-1 space-y-1">
            <div class="flex items-center gap-2">
              <h2 class="text-lg font-black text-slate-900 truncate">{{ $student->full_name }}</h2>
              <span class="px-2 py-0.5 rounded bg-[#FFF5F5] text-[#8C2826] text-xs font-bold border border-[#FECACA]">
                Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }})
              </span>
            </div>
            <p class="text-xs font-bold text-[#8C2826]">Admission No: {{ $student->admission_no }}</p>
            <p class="text-xs text-slate-500">Roll No: {{ $student->roll_number ?? 'Auto-Allocated' }} &bull; Gender: {{ ucfirst($student->gender ?? '—') }}</p>
          </div>
        </div>

        {{-- Parents & Guardian Strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
          {{-- Father --}}
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 overflow-hidden shrink-0">
              @if($student->father_photo)
                <img src="{{ asset('storage/' . $student->father_photo) }}" class="w-full h-full object-cover" alt="Father">
              @else
                <div class="w-full h-full flex items-center justify-center text-sm">👨</div>
              @endif
            </div>
            <div class="min-w-0 flex-1 text-xs">
              <span class="text-[10px] uppercase font-bold text-[#8C2826] block">Father</span>
              <p class="font-bold text-slate-800 truncate">{{ $student->father_name ?? $student->parent_name }}</p>
              <p class="text-slate-500 font-medium text-[11px]">{{ $student->father_mobile ?? $student->parent_mobile }}</p>
            </div>
          </div>

          {{-- Mother --}}
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 overflow-hidden shrink-0">
              @if($student->mother_photo)
                <img src="{{ asset('storage/' . $student->mother_photo) }}" class="w-full h-full object-cover" alt="Mother">
              @else
                <div class="w-full h-full flex items-center justify-center text-sm">👩</div>
              @endif
            </div>
            <div class="min-w-0 flex-1 text-xs">
              <span class="text-[10px] uppercase font-bold text-rose-600 block">Mother</span>
              <p class="font-bold text-slate-800 truncate">{{ $student->mother_name ?? '—' }}</p>
              <p class="text-slate-500 font-medium text-[11px]">{{ $student->mother_mobile ?? '—' }}</p>
            </div>
          </div>
        </div>

        {{-- Fee Status Summary --}}
        <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="text-slate-400 font-medium">Total Admission Fee Billed:</span>
            <span class="font-bold text-base text-amber-300 tabular-nums">₹{{ number_format($student->total_admission_fee ?? 0, 2) }}</span>
          </div>
          <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-800">
            <span class="text-emerald-400 font-bold">Initial Amount Collected:</span>
            <span class="font-bold text-emerald-400 tabular-nums">₹{{ number_format($student->admission_paid_amount ?? 0, 2) }}</span>
          </div>
          @if(($student->admission_pending_amount ?? 0) > 0)
          <div class="flex items-center justify-between text-xs">
            <span class="text-slate-400">Remaining Balance:</span>
            <span class="font-bold text-rose-300 tabular-nums">₹{{ number_format($student->admission_pending_amount, 2) }}</span>
          </div>
          @endif
        </div>

        {{-- Action Links --}}
        <div class="flex items-center gap-3 pt-1">
          <a href="{{ route('students.visitor-card', $student->id) }}" class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Parent Visitor Card
          </a>
          <a href="{{ route('students.show', $student->id) }}" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 transition flex items-center justify-center gap-2">
            View Student Record
          </a>
        </div>

      </div>

    </div>

    {{-- Right: Scan QR to Upload from Home Slip (5 cols) --}}
    <div class="lg:col-span-5 space-y-4">
      <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white p-6 rounded-3xl shadow-xl border border-indigo-800 space-y-5">
        
        <div class="text-center space-y-1">
          <span class="text-[10px] font-black uppercase tracking-widest bg-amber-400 text-slate-950 px-2.5 py-0.5 rounded-full">
            FOR PARENT USE AT HOME
          </span>
          <h3 class="text-lg font-black tracking-tight text-white">Scan QR Code &amp; Upload Documents</h3>
          <p class="text-xs text-indigo-200 leading-snug">
            Give this slip to the parent. They can scan the QR code with their mobile phone camera at home anytime to upload certificates!
          </p>
        </div>

        {{-- QR Code Visual Box --}}
        <div class="bg-white p-4 rounded-2xl shadow-lg mx-auto w-fit flex flex-col items-center justify-center">
          <div class="w-40 h-40 flex items-center justify-center">
            {!! $docUploadQrSvg !!}
          </div>
          <p class="text-[10px] font-bold text-slate-500 mt-1 tracking-wide">SCAN TO UPLOAD CERTIFICATES</p>
        </div>

        {{-- WhatsApp & Print Action Buttons --}}
        <div class="space-y-2 pt-2">
          @php
            $docPortalUrl = route('public.student.documents', ['token' => $student->document_token]);
            $waDocText = urlencode("Dear Parent, Here is the secure link to upload remaining documents for {$student->full_name} (Admission #{$student->admission_no}):\n" . $docPortalUrl . "\nYou can click the link and take photos of Birth Certificate, Aadhaar, Community Certificate, TC, etc.");
          @endphp

          <a href="https://api.whatsapp.com/send?text={{ $waDocText }}" target="_blank"
             class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            <span>Send Upload Link via WhatsApp</span>
          </a>

          <button onclick="navigator.clipboard.writeText('{{ $docPortalUrl }}'); alert('Upload link copied to clipboard!');"
                  class="w-full py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition border border-white/20 cursor-pointer flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
            <span>Copy Upload Link</span>
          </button>
        </div>

      </div>
    </div>

  </div>

</div>
@endsection
