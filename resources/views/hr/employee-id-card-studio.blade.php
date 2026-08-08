@extends('layouts.app')

@section('title', 'Staff ID Card Studio — ' . $employee->full_name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ activeTab: 'both' }">

  {{-- Top Navigation & Action Bar --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn-icon" title="Back to Employee Profile">
        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-800">Staff ID Card Studio</h1>
        <p class="page-subtitle text-xs text-slate-500 mt-0.5">{{ $employee->full_name }} ({{ $employee->employee_code }}) &bull; {{ $employee->category_label }}</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      {{-- Tab view toggles --}}
      <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs font-semibold">
        <button @click="activeTab = 'both'" :class="activeTab === 'both' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-800'" class="px-3 py-1.5 rounded-md transition">Both Sides</button>
        <button @click="activeTab = 'front'" :class="activeTab === 'front' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-800'" class="px-3 py-1.5 rounded-md transition">Front Side Only</button>
        <button @click="activeTab = 'back'" :class="activeTab === 'back' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-800'" class="px-3 py-1.5 rounded-md transition">Back Side Only</button>
      </div>

      {{-- Print Button --}}
      <button onclick="window.print()" class="btn btn-primary shadow-lg shadow-indigo-100 flex items-center gap-2 text-xs font-bold px-4 py-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print / Download Card
      </button>
    </div>
  </div>

  {{-- Photo Upload Box (Print Hidden) --}}
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-4 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
        📸
      </div>
      <div>
        <h4 class="text-xs font-bold text-blue-950">Employee Photo Upload for ID Card</h4>
        <p class="text-[11px] text-blue-700">Upload a crisp passport-size photo to instantly display on the Staff ID card.</p>
      </div>
    </div>

    <form action="{{ route('hr.employees.update-photo', $employee->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 w-full sm:w-auto">
      @csrf
      <input type="file" name="photo" accept="image/*" class="input text-xs bg-white py-1 text-slate-700 w-full sm:w-60" required>
      <button type="submit" class="btn btn-secondary btn-xs font-bold px-3 py-1.5 whitespace-nowrap">Upload Photo</button>
    </form>
  </div>

  {{-- ID CARD DISPLAY STUDIO --}}
  <div class="flex flex-col lg:flex-row items-center justify-center gap-8 py-6">

    {{-- ── FRONT SIDE OF ID CARD ──────────────────────────── --}}
    <div x-show="activeTab === 'both' || activeTab === 'front'" class="print-card-wrap">
      <div class="id-card-vertical relative bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col justify-between"
           style="width: 320px; height: 500px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

        {{-- Top Smooth Curved Wave Header Graphic --}}
        <div class="absolute top-0 left-0 right-0 h-36 overflow-hidden pointer-events-none z-0">
          {{-- Darker Blue background wave --}}
          <svg class="absolute top-0 left-0 w-full h-full text-blue-600" viewBox="0 0 320 140" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,0 L320,0 L320,110 C240,140 160,80 0,110 Z"/>
          </svg>
          {{-- Lighter Blue overlay wave --}}
          <svg class="absolute top-0 left-0 w-full h-full text-blue-500 opacity-60" viewBox="0 0 320 140" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,0 L320,0 L320,90 C220,120 120,70 0,95 Z"/>
          </svg>
          {{-- Accent Pill Shape top left --}}
          <div class="absolute top-0 left-3 w-7 h-28 bg-blue-400 opacity-40 rounded-b-full"></div>
        </div>

        {{-- Header Organization Logo & Name (Crisp White Text) --}}
        <div class="relative z-10 pt-4 px-6 flex items-center justify-end">
          <div class="flex items-center gap-2 text-right">
            <div>
              <p class="text-xs font-black tracking-wider text-white uppercase leading-none drop-shadow-sm">{{ config('app.name', 'DASA EduERP') }}</p>
              <p class="text-[8px] font-bold text-blue-100 tracking-widest uppercase mt-0.5">INSTITUTION ID</p>
            </div>
            <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm text-white flex items-center justify-center shadow-sm border border-white/30">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
            </div>
          </div>
        </div>

        {{-- Center Photo Avatar Frame --}}
        <div class="relative z-10 mt-1 flex justify-center">
          <div class="w-32 h-32 rounded-full p-1 bg-white shadow-xl ring-4 ring-blue-100 flex items-center justify-center overflow-hidden">
            @if($employee->photo)
              <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full rounded-full object-cover">
            @else
              <div class="w-full h-full rounded-full bg-gradient-to-br from-slate-100 to-blue-50 flex items-center justify-center text-blue-600 font-black text-3xl border border-slate-200">
                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
              </div>
            @endif
          </div>
        </div>

        {{-- Employee Name & Designation --}}
        <div class="relative z-10 text-center px-4 mt-2">
          <h2 class="text-lg font-black tracking-tight text-blue-950 uppercase leading-tight">{{ $employee->full_name }}</h2>
          <p class="text-xs font-bold text-blue-600 capitalize mt-0.5">{{ $employee->designation }}</p>
        </div>

        {{-- Information Rows (Centered 3-column table block) --}}
        <div class="relative z-10 flex justify-center px-4 mt-2">
          <table style="border-collapse: collapse; font-size: 11px; font-weight: 600; color: #1e293b; margin: 0 auto;">
            <tbody>
              <tr>
                <td style="padding: 2px 6px 2px 0; font-weight: 700; color: #1e3a8a; white-space: nowrap; text-align: left;">ID No</td>
                <td style="padding: 2px 6px; color: #64748b; font-weight: 700; text-align: center;">:</td>
                <td style="padding: 2px 0 2px 6px; font-family: ui-monospace, monospace; font-weight: 700; color: #0f172a; text-align: left; white-space: nowrap;">{{ $employee->employee_code }}</td>
              </tr>
              <tr>
                <td style="padding: 2px 6px 2px 0; font-weight: 700; color: #1e3a8a; white-space: nowrap; text-align: left;">Email</td>
                <td style="padding: 2px 6px; color: #64748b; font-weight: 700; text-align: center;">:</td>
                <td style="padding: 2px 0 2px 6px; font-weight: 600; color: #334155; text-align: left; white-space: nowrap;">{{ $employee->official_email ?? $employee->email ?? 'staff@dasaeduerp.com' }}</td>
              </tr>
              <tr>
                <td style="padding: 2px 6px 2px 0; font-weight: 700; color: #1e3a8a; white-space: nowrap; text-align: left;">Phone</td>
                <td style="padding: 2px 6px; color: #64748b; font-weight: 700; text-align: center;">:</td>
                <td style="padding: 2px 0 2px 6px; font-family: ui-monospace, monospace; font-weight: 700; color: #0f172a; text-align: left; white-space: nowrap;">+91 {{ $employee->mobile }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- Barcode Graphic --}}
        <div class="relative z-10 flex flex-col items-center justify-center mt-2.5">
          <div class="bg-white px-3 py-1 rounded border border-slate-200 flex items-center justify-center shadow-sm">
            <svg class="h-8 w-48 text-slate-900" viewBox="0 0 160 30" fill="currentColor">
              <rect x="0" y="0" width="3" height="30"/>
              <rect x="5" y="0" width="1" height="30"/>
              <rect x="8" y="0" width="4" height="30"/>
              <rect x="15" y="0" width="2" height="30"/>
              <rect x="19" y="0" width="1" height="30"/>
              <rect x="22" y="0" width="5" height="30"/>
              <rect x="29" y="0" width="2" height="30"/>
              <rect x="33" y="0" width="3" height="30"/>
              <rect x="38" y="0" width="1" height="30"/>
              <rect x="41" y="0" width="4" height="30"/>
              <rect x="47" y="0" width="2" height="30"/>
              <rect x="51" y="0" width="1" height="30"/>
              <rect x="54" y="0" width="6" height="30"/>
              <rect x="62" y="0" width="2" height="30"/>
              <rect x="66" y="0" width="3" height="30"/>
              <rect x="71" y="0" width="1" height="30"/>
              <rect x="74" y="0" width="5" height="30"/>
              <rect x="81" y="0" width="2" height="30"/>
              <rect x="85" y="0" width="3" height="30"/>
              <rect x="90" y="0" width="1" height="30"/>
              <rect x="93" y="0" width="4" height="30"/>
              <rect x="99" y="0" width="2" height="30"/>
              <rect x="103" y="0" width="5" height="30"/>
              <rect x="110" y="0" width="1" height="30"/>
              <rect x="113" y="0" width="3" height="30"/>
              <rect x="118" y="0" width="2" height="30"/>
              <rect x="122" y="0" width="4" height="30"/>
              <rect x="128" y="0" width="1" height="30"/>
              <rect x="131" y="0" width="5" height="30"/>
              <rect x="138" y="0" width="2" height="30"/>
              <rect x="142" y="0" width="3" height="30"/>
              <rect x="147" y="0" width="1" height="30"/>
              <rect x="150" y="0" width="4" height="30"/>
              <rect x="156" y="0" width="2" height="30"/>
            </svg>
          </div>
        </div>

        {{-- Footer Website & Bottom Wave Curve Graphic --}}
        <div class="relative z-10 text-center pt-2">
          <p class="text-[10px] font-bold text-blue-900 tracking-wider mb-2">www.dasaeduerp.com</p>

          {{-- Bottom Wave Graphics --}}
          <div class="h-6 w-full relative overflow-hidden pointer-events-none">
            <svg class="absolute bottom-0 left-0 w-full h-full text-blue-600" viewBox="0 0 320 24" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,24 L320,24 L320,6 C220,20 100,2 0,18 Z"/>
            </svg>
            <svg class="absolute bottom-0 left-0 w-full h-full text-blue-400 opacity-60" viewBox="0 0 320 24" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,24 L320,24 L320,12 C180,22 80,8 0,20 Z"/>
            </svg>
          </div>
        </div>

      </div>
      <p class="text-center text-xs font-bold text-slate-500 mt-2 print:hidden">FRONT SIDE PREVIEW</p>
    </div>


    {{-- ── BACK SIDE OF ID CARD ───────────────────────────── --}}
    <div x-show="activeTab === 'both' || activeTab === 'back'" class="print-card-wrap">
      <div class="id-card-vertical relative bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col justify-between"
           style="width: 320px; height: 500px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

        {{-- Top Smooth Curved Wave Header Graphic --}}
        <div class="absolute top-0 left-0 right-0 h-36 overflow-hidden pointer-events-none z-0">
          <svg class="absolute top-0 left-0 w-full h-full text-blue-600" viewBox="0 0 320 140" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,0 L320,0 L320,110 C240,140 160,80 0,110 Z"/>
          </svg>
          <svg class="absolute top-0 left-0 w-full h-full text-blue-500 opacity-60" viewBox="0 0 320 140" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,0 L320,0 L320,90 C220,120 120,70 0,95 Z"/>
          </svg>
          <div class="absolute top-0 left-3 w-7 h-28 bg-blue-400 opacity-40 rounded-b-full"></div>
        </div>

        {{-- Header Organization Logo & Name (Crisp White Text) --}}
        <div class="relative z-10 pt-4 px-6 flex items-center justify-end">
          <div class="flex items-center gap-2 text-right">
            <div>
              <p class="text-xs font-black tracking-wider text-white uppercase leading-none drop-shadow-sm">{{ config('app.name', 'DASA EduERP') }}</p>
              <p class="text-[8px] font-bold text-blue-100 tracking-widest uppercase mt-0.5">AUTHORIZATION</p>
            </div>
            <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm text-white flex items-center justify-center shadow-sm border border-white/30">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
          </div>
        </div>

        {{-- Terms & Conditions Content --}}
        <div class="relative z-10 px-6 pt-14">
          <h3 class="text-base font-black text-blue-950 uppercase tracking-tight mb-2.5">TERMS & CONDITIONS</h3>

          <ul class="space-y-2 text-[10px] text-slate-700 leading-snug font-medium">
            <li class="flex items-start gap-1.5">
              <span class="text-blue-600 font-bold text-sm leading-none">&bull;</span>
              <span><strong class="text-blue-950">Possession:</strong> Employees must carry their ID card at all times within school premises for verification.</span>
            </li>
            <li class="flex items-start gap-1.5">
              <span class="text-blue-600 font-bold text-sm leading-none">&bull;</span>
              <span><strong class="text-blue-950">Usage Restriction:</strong> The ID card is school property and must only be used for authorized purposes.</span>
            </li>
            <li class="flex items-start gap-1.5">
              <span class="text-blue-600 font-bold text-sm leading-none">&bull;</span>
              <span><strong class="text-blue-950">Responsibility:</strong> Lost or stolen cards must be reported immediately to HR or security for deactivation.</span>
            </li>
            <li class="flex items-start gap-1.5">
              <span class="text-blue-600 font-bold text-sm leading-none">&bull;</span>
              <span><strong class="text-blue-950">Non-Transferable:</strong> The ID card is personal and non-transferable; lending or duplicating is strictly prohibited.</span>
            </li>
          </ul>
        </div>

        {{-- Emergency Contact Box --}}
        <div class="relative z-10 px-6 mt-2">
          <div class="bg-blue-50/80 p-2.5 rounded-xl border border-blue-100 text-[10px]">
            <p class="font-bold text-blue-900 uppercase text-[9px] tracking-wider">IF FOUND, PLEASE RETURN TO:</p>
            <p class="text-slate-800 font-bold mt-0.5">{{ config('app.name', 'DASA EduERP') }} Main Office</p>
            <p class="text-slate-600 mt-0.5">Emergency Helpline: <span class="font-mono font-bold text-blue-900">+91 {{ $employee->emergency_contact_mobile ?? $employee->mobile }}</span></p>
          </div>
        </div>

        {{-- Footer Website & Bottom Wave Curve Graphic --}}
        <div class="relative z-10 text-center pt-2">
          <p class="text-[10px] font-bold text-blue-900 tracking-wider mb-2">www.dasaeduerp.com</p>

          {{-- Bottom Wave Graphics --}}
          <div class="h-6 w-full relative overflow-hidden pointer-events-none">
            <svg class="absolute bottom-0 left-0 w-full h-full text-blue-600" viewBox="0 0 320 24" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,24 L320,24 L320,6 C220,20 100,2 0,18 Z"/>
            </svg>
            <svg class="absolute bottom-0 left-0 w-full h-full text-blue-400 opacity-60" viewBox="0 0 320 24" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,24 L320,24 L320,12 C180,22 80,8 0,20 Z"/>
            </svg>
          </div>
        </div>

      </div>
      <p class="text-center text-xs font-bold text-slate-500 mt-2 print:hidden">BACK SIDE PREVIEW</p>
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
      margin: 10px auto;
      page-break-after: always;
    }
    .id-card-vertical {
      box-shadow: none !important;
      border: 1px solid #cbd5e1 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
  }
</style>
@endsection
