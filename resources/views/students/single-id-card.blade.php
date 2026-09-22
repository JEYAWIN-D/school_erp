@extends('layouts.app')

@section('title', 'Student ID Card — ' . $student->full_name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ activeSide: 'both' }">

  {{-- ── Top Navigation & Action Bar ────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('students.id-cards') }}" class="btn-icon w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Back to All Student ID Cards">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="page-title text-xl font-black text-slate-900">Student ID Card Studio</h1>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $wingMeta['badge_color'] }}">
            {{ $wingMeta['tag'] }}
          </span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">
          <span class="font-bold text-slate-800">{{ $student->full_name }}</span> ({{ $student->admission_number }}) &bull; Class {{ $classModel?->name ?? '—' }} {{ $enrollment?->section?->name ? '(' . $enrollment->section->name . ')' : '' }} &bull; {{ $wingMeta['name'] }}
        </p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
      {{-- Side view toggles --}}
      <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 text-xs font-bold shadow-2xs">
        <button @click="activeSide = 'both'" :class="activeSide === 'both' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Both Sides</button>
        <button @click="activeSide = 'front'" :class="activeSide === 'front' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Front Side Only</button>
        <button @click="activeSide = 'back'" :class="activeSide === 'back' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Back Side Only</button>
      </div>

      {{-- Print Button --}}
      <button onclick="window.print()" class="btn btn-primary flex items-center gap-2 text-xs font-bold px-4 py-2 shadow-sm cursor-pointer" title="Print student ID card">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Student ID Card
      </button>
    </div>
  </div>

  {{-- ── Single Student ID Card Previews ─────────────────────── --}}
  <div class="flex flex-col items-center justify-center gap-10 py-6">

    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 w-full">

      {{-- ── FRONT SIDE (Landscape Badge 85x54 Ratio) ──────────── --}}
      <div x-show="activeSide === 'both' || activeSide === 'front'" class="print-card-wrap flex flex-col items-center">
        <div class="id-card-landscape relative bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 275px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

          {{-- Wing Themed Curved Header --}}
          <div class="relative h-14 overflow-hidden px-5 flex items-center justify-between text-white"
               style="background: {{ $wingMeta['header_gradient'] }};">
            <div class="flex items-center gap-2 z-10">
              <div class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-white shadow-xs p-0.5">
                <img src="{{ asset('images/school-seal-badge.png') }}" alt="Logo" class="w-full h-full object-contain">
              </div>
              <span class="font-extrabold text-xs tracking-wider uppercase drop-shadow-xs">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</span>
            </div>

            <div class="z-10 bg-white/15 backdrop-blur-xs border border-white/25 px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wider uppercase text-white">
              {{ $wingMeta['tag'] }}
            </div>

            <svg class="absolute bottom-0 left-0 right-0 w-full h-4 text-white opacity-10 pointer-events-none" viewBox="0 0 440 20" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,20 Q110,0 220,10 T440,0 L440,20 Z"/>
            </svg>
          </div>

          {{-- Card Body --}}
          <div class="px-5 py-3 flex items-center gap-4 flex-1">
            {{-- Student Photo --}}
            <div class="shrink-0">
              <div class="w-24 h-28 rounded-2xl bg-slate-50 border-2 overflow-hidden flex items-center justify-center shadow-xs"
                   style="border-color: {{ $wingMeta['primary_color'] }}40;">
                @if($student->photo)
                  <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full flex items-center justify-center font-black text-2xl"
                       style="background: {{ $wingMeta['primary_color'] }}15; color: {{ $wingMeta['primary_color'] }};">
                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                  </div>
                @endif
              </div>
            </div>

            {{-- Student Info --}}
            <div class="min-w-0 flex-1 space-y-1.5">
              <div>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-tight leading-tight truncate">
                  {{ $student->full_name }}
                </h2>
                <p class="text-xs font-bold truncate mt-0.5" style="color: {{ $wingMeta['primary_color'] }};">
                  Class {{ $classModel?->name ?? '—' }} {{ $enrollment?->section?->name ? '(' . $enrollment->section->name . ')' : '' }}
                </p>
              </div>

              {{-- Key-Value Box --}}
              <div class="bg-slate-50/90 rounded-xl p-2 border border-slate-200/80 text-[10px] space-y-0.5">
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-4 text-slate-500 font-bold">Roll No</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-7 font-mono font-bold text-slate-900">{{ $student->roll_number ?? $enrollment?->roll_number ?? $student->admission_number }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-4 text-slate-500 font-bold">DOB</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-7 font-medium text-slate-800">{{ $student->dob ? $student->dob->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-4 text-slate-500 font-bold">Blood Group</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-7 font-bold text-rose-600">{{ $student->blood_group ?? '—' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-4 text-slate-500 font-bold">Phone No</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-7 font-mono font-medium text-slate-800">+91 {{ $student->father_mobile ?? $student->mobile ?? '9876543210' }}</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Card Footer --}}
          <div class="px-5 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[9px] font-bold text-slate-400">
            <span>{{ $school->website ?? 'www.dasaeduerp.com' }}</span>
            <span class="tracking-wider text-slate-400">STUDENT IDENTITY CARD</span>
          </div>

        </div>
        <p class="text-center text-xs font-black text-slate-500 uppercase tracking-wider mt-3 print:hidden">
          FRONT SIDE PREVIEW ({{ $wingMeta['tag'] }})
        </p>
      </div>

      {{-- ── BACK SIDE (Landscape Badge 85x54 Ratio) ──────────── --}}
      <div x-show="activeSide === 'both' || activeSide === 'back'" class="print-card-wrap flex flex-col items-center">
        <div class="id-card-landscape relative bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 275px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

          {{-- Wing Themed Curved Header --}}
          <div class="relative h-14 overflow-hidden px-5 flex items-center justify-between text-white"
               style="background: {{ $wingMeta['header_gradient'] }};">
            <div class="flex items-center gap-2 z-10">
              <div class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-white shadow-xs p-0.5">
                <img src="{{ asset('images/school-seal-badge.png') }}" alt="Logo" class="w-full h-full object-contain">
              </div>
              <span class="font-extrabold text-xs tracking-wider uppercase drop-shadow-xs">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</span>
            </div>

            <div class="z-10 bg-white/15 backdrop-blur-xs border border-white/25 px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider uppercase text-white">
              AUTHORIZATION
            </div>

            <svg class="absolute bottom-0 left-0 right-0 w-full h-4 text-white opacity-10 pointer-events-none" viewBox="0 0 440 20" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,20 Q110,0 220,10 T440,0 L440,20 Z"/>
            </svg>
          </div>

          {{-- Card Body --}}
          <div class="px-5 py-3 flex-1 flex items-center gap-4">
            <div class="flex-1 space-y-2 text-[10px]">
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 space-y-1">
                <div class="flex items-center gap-1.5 font-bold text-blue-900 text-[10px]">
                  <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                  <span class="uppercase">RESIDENTIAL ADDRESS</span>
                </div>
                <p class="text-slate-700 leading-snug font-medium">
                  {{ $student->residential_address ?? $student->permanent_address ?? 'Campus Colony, Knowledge Park Block 4' }}
                </p>
              </div>

              <div class="text-[10px] space-y-0.5">
                <p class="font-bold text-slate-800">Father: <span class="font-medium text-slate-600">{{ $student->father_name ?? '—' }}</span></p>
                <p class="font-bold text-slate-800">Emergency Helpline: <span class="font-mono text-blue-900">+91 {{ $school->phone ?? $student->father_mobile ?? '9876543210' }}</span></p>
              </div>
            </div>

            {{-- QR Code --}}
            @if($qrCode)
            <div class="shrink-0 flex flex-col items-center">
              <div class="p-1.5 bg-white border border-slate-200 rounded-xl shadow-xs">
                <img src="data:image/png;base64,{{ $qrCode }}" class="w-20 h-20" alt="QR Code">
              </div>
              <span class="text-[8px] font-bold text-slate-400 mt-1">SCAN TO VERIFY</span>
            </div>
            @endif
          </div>

          {{-- Card Footer --}}
          <div class="px-5 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[9px] font-bold text-slate-400">
            <span>Valid: {{ $currentYear?->name ?? '2025-2026' }}</span>
            <span class="tracking-wider text-slate-400">BACK SIDE</span>
          </div>

        </div>
        <p class="text-center text-xs font-black text-slate-500 uppercase tracking-wider mt-3 print:hidden">
          BACK SIDE PREVIEW ({{ $wingMeta['tag'] }})
        </p>
      </div>

    </div>

  </div>

</div>

{{-- PRINT STYLING --}}
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    .print-card-wrap, .print-card-wrap * {
      visibility: visible;
    }
    .print-card-wrap {
      position: relative;
      left: 0;
      top: 0;
      margin: 15px auto;
      page-break-after: always;
    }
    .id-card-landscape {
      box-shadow: none !important;
      border: 1px solid #cbd5e1 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
  }
</style>
@endsection
