@extends('layouts.app')

@section('title', 'Staff ID Card Studio — ' . ($employee ? $employee->full_name : 'Employee Badges'))

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ activeSide: 'both' }">

  {{-- ── Top Navigation & Category Filter Tabs ────────────────── --}}
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs print:hidden space-y-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('hr.employees') }}" class="btn-icon w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Back to Employee List">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="page-title text-xl font-black text-slate-900">Staff ID Card Studio</h1>
            @if($employee && isset($employee->theme_meta))
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $employee->theme_meta['badge_class'] }}">
                {{ $employee->theme_meta['theme_badge'] }}
              </span>
            @endif
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            @if($employee)
              <span class="font-semibold text-slate-800">{{ $employee->full_name }}</span> ({{ $employee->employee_code ?? 'EMP-' . $employee->id }}) &bull; {{ $employee->category_label }} &bull; <span class="text-indigo-600 font-medium">{{ $employee->designation }}</span>
            @else
              Select an employee below to preview & print category-styled badge cards
            @endif
          </p>
        </div>
      </div>

      {{-- Action Controls & Side Toggle --}}
      <div class="flex flex-wrap items-center gap-2.5">
        {{-- View toggles --}}
        <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 text-xs font-bold shadow-2xs">
          <button @click="activeSide = 'both'" :class="activeSide === 'both' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Both Sides</button>
          <button @click="activeSide = 'front'" :class="activeSide === 'front' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Front Side Only</button>
          <button @click="activeSide = 'back'" :class="activeSide === 'back' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">Back Side Only</button>
        </div>

        {{-- Print Current Card --}}
        <button onclick="window.print()" class="btn btn-primary flex items-center gap-2 text-xs font-bold px-4 py-2 shadow-sm cursor-pointer" title="Print currently active ID card">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Print / Download Card
        </button>

        {{-- Bulk PDF Download --}}
        <a href="{{ route('hr.id-cards.download', ['category' => $categoryFilter, 'department_id' => $deptFilter]) }}" target="_blank" class="btn btn-secondary flex items-center gap-2 text-xs font-bold px-3.5 py-2">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          Download Bulk PDF
        </a>
      </div>
    </div>

    {{-- Category Filter Tabs --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
      <div class="flex flex-wrap items-center gap-1.5">
        <a href="{{ route('hr.id-card-studio', ['category' => 'all', 'department_id' => $deptFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $categoryFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
          All Staff ({{ $counts['all'] ?? 0 }})
        </a>
        <a href="{{ route('hr.id-card-studio', ['category' => 'hod', 'department_id' => $deptFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $categoryFilter === 'hod' ? 'bg-indigo-900 text-white shadow-xs' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
          HOD ({{ $counts['hod'] ?? 0 }})
        </a>
        <a href="{{ route('hr.id-card-studio', ['category' => 'senior_teacher', 'department_id' => $deptFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $categoryFilter === 'senior_teacher' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
          Senior Teacher ({{ $counts['senior_teacher'] ?? 0 }})
        </a>
        <a href="{{ route('hr.id-card-studio', ['category' => 'teacher', 'department_id' => $deptFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $categoryFilter === 'teacher' ? 'bg-blue-700 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
          Teacher ({{ $counts['teacher'] ?? 0 }})
        </a>
        <a href="{{ route('hr.id-card-studio', ['category' => 'non_teaching', 'department_id' => $deptFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $categoryFilter === 'non_teaching' ? 'bg-slate-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
          Non-Teaching Staff ({{ $counts['non_teaching'] ?? 0 }})
        </a>
      </div>

      {{-- Department & Staff Picker Form --}}
      <form method="GET" action="{{ route('hr.id-card-studio') }}" class="flex flex-wrap items-center gap-2">
        <input type="hidden" name="category" value="{{ $categoryFilter }}">
        <select name="department_id" onchange="this.form.submit()" class="select-sm text-xs bg-slate-50 border-slate-200 rounded-lg">
          <option value="">All Departments</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @selected($deptFilter == $dept->id)>{{ $dept->name }}</option>
          @endforeach
        </select>

        <select name="employee_id" onchange="this.form.submit()" class="select-sm text-xs bg-slate-50 border-slate-200 rounded-lg max-w-[200px]">
          @foreach($filteredEmployees as $emp)
            <option value="{{ $emp->id }}" @selected($employee && $employee->id === $emp->id)>
              {{ $emp->full_name }} ({{ $emp->employee_code }})
            </option>
          @endforeach
        </select>
      </form>
    </div>
  </div>

  {{-- ── Employee Photo Upload Box (Print Hidden) ─────────────── --}}
  @if($employee)
  <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border border-blue-200 p-4 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 print:hidden shadow-xs">
    <div class="flex items-center gap-3.5">
      <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
        📸
      </div>
      <div>
        <h4 class="text-xs font-bold text-blue-950">Employee Photo Upload for ID Card</h4>
        <p class="text-[11px] text-blue-700">Upload a crisp passport-size photo to instantly display on the Staff ID card.</p>
      </div>
    </div>

    <form action="{{ route('hr.employees.update-photo', $employee->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 w-full sm:w-auto">
      @csrf
      <input type="file" name="photo" accept="image/*" class="input text-xs bg-white py-1 text-slate-700 w-full sm:w-60 border-blue-200" required>
      <button type="submit" class="btn btn-secondary btn-xs font-bold px-3.5 py-1.5 whitespace-nowrap bg-white hover:bg-slate-50 border-slate-300 cursor-pointer">Upload Photo</button>
    </form>
  </div>
  @endif

  {{-- ── ID CARD DISPLAY STUDIO (Horizontal Badges) ──────────── --}}
  @if($employee)
  @php
    $theme = $employee->theme_meta ?? [
      'category_key'  => 'hod',
      'theme_label'   => 'HOD',
      'theme_badge'   => 'HOD THEME',
      'header_bg'     => 'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)',
      'header_color'  => '#1e1b4b',
      'accent_color'  => '#4338ca',
      'text_accent'   => 'text-indigo-600',
      'border_color'  => '#312e81',
    ];
  @endphp

  <div class="flex flex-col items-center justify-center gap-10 py-6">

    {{-- Cards Container --}}
    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 w-full">

      {{-- ── FRONT SIDE OF ID CARD (Landscape Standard 85x54 Ratio) ── --}}
      <div x-show="activeSide === 'both' || activeSide === 'front'" class="print-card-wrap flex flex-col items-center">
        <div class="id-card-landscape relative bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 275px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

          {{-- Curved Top Banner Graphic --}}
          <div class="relative h-14 overflow-hidden px-5 flex items-center justify-between text-white"
               style="background: {{ $theme['header_bg'] }};">
            {{-- School Brand --}}
            <div class="flex items-center gap-2 z-10">
              <div class="w-6 h-6 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center text-white border border-white/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              </div>
              <span class="font-extrabold text-xs tracking-wider uppercase drop-shadow-xs">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</span>
            </div>

            {{-- Institution Pill Badge --}}
            <div class="z-10 bg-white/15 backdrop-blur-xs border border-white/25 px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider uppercase text-white">
              INSTITUTION ID
            </div>

            {{-- Subtle overlay wave --}}
            <svg class="absolute bottom-0 left-0 right-0 w-full h-4 text-white opacity-10 pointer-events-none" viewBox="0 0 440 20" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,20 Q110,0 220,10 T440,0 L440,20 Z"/>
            </svg>
          </div>

          {{-- Card Body --}}
          <div class="px-5 py-3 flex items-center gap-4 flex-1">
            {{-- Staff Photo / Avatar --}}
            <div class="shrink-0">
              <div class="w-24 h-28 rounded-2xl bg-slate-50 border-2 overflow-hidden flex items-center justify-center shadow-xs"
                   style="border-color: {{ $theme['accent_color'] }}40;">
                @if($employee->photo)
                  <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full flex items-center justify-center font-black text-2xl"
                       style="background: {{ $theme['accent_color'] }}15; color: {{ $theme['accent_color'] }};">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                  </div>
                @endif
              </div>
            </div>

            {{-- Staff Details --}}
            <div class="min-w-0 flex-1 space-y-1.5">
              <div>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-tight leading-tight truncate">
                  {{ $employee->full_name }}
                </h2>
                <p class="text-xs font-bold truncate mt-0.5" style="color: {{ $theme['accent_color'] }};">
                  {{ $employee->designation ?? $employee->category_label }}
                </p>
              </div>

              {{-- Key-Value Box --}}
              <div class="bg-slate-50/90 rounded-xl p-2 border border-slate-200/80 text-[10px] space-y-0.5">
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-3 text-slate-500 font-bold">ID No</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-8 font-mono font-bold text-slate-900">{{ $employee->employee_code ?? 'EMP-' . $employee->id }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-3 text-slate-500 font-bold">DOB</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-8 font-medium text-slate-800">{{ $employee->dob ? $employee->dob->format('d/m/Y') : '15/08/1990' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1 items-center">
                  <span class="col-span-3 text-slate-500 font-bold">Phone</span>
                  <span class="col-span-1 text-slate-400 font-bold text-center">:</span>
                  <span class="col-span-8 font-mono font-medium text-slate-800">+91 {{ $employee->mobile ?? '9876543210' }}</span>
                </div>
              </div>

              {{-- Barcode Graphic --}}
              <div class="pt-0.5">
                <svg class="h-6 w-36 text-slate-800" viewBox="0 0 160 30" fill="currentColor">
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
          </div>

          {{-- Card Footer --}}
          <div class="px-5 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[9px] font-bold text-slate-400">
            <span>{{ $school->website ?? 'www.dasaeduerp.com' }}</span>
            <span class="tracking-wider text-slate-400">FRONT SIDE</span>
          </div>

        </div>
        <p class="text-center text-xs font-black text-slate-500 uppercase tracking-wider mt-3 print:hidden">
          FRONT SIDE PREVIEW ({{ $theme['theme_label'] }})
        </p>
      </div>


      {{-- ── BACK SIDE OF ID CARD (Landscape Standard 85x54 Ratio) ─── --}}
      <div x-show="activeSide === 'both' || activeSide === 'back'" class="print-card-wrap flex flex-col items-center">
        <div class="id-card-landscape relative bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 275px; font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;">

          {{-- Curved Top Banner Graphic --}}
          <div class="relative h-14 overflow-hidden px-5 flex items-center justify-between text-white"
               style="background: {{ $theme['header_bg'] }};">
            <div class="flex items-center gap-2 z-10">
              <div class="w-6 h-6 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center text-white border border-white/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <span class="font-extrabold text-xs tracking-wider uppercase drop-shadow-xs">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</span>
            </div>

            <div class="z-10 bg-white/15 backdrop-blur-xs border border-white/25 px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider uppercase text-white">
              AUTHORIZATION & ADDRESS
            </div>

            <svg class="absolute bottom-0 left-0 right-0 w-full h-4 text-white opacity-10 pointer-events-none" viewBox="0 0 440 20" fill="currentColor" preserveAspectRatio="none">
              <path d="M0,20 Q110,0 220,10 T440,0 L440,20 Z"/>
            </svg>
          </div>

          {{-- Card Body (Address Panels) --}}
          <div class="px-5 py-3 flex-1 flex flex-col justify-between">
            <div class="grid grid-cols-2 gap-3">
              {{-- Institution Address Box --}}
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 text-[10px] space-y-1">
                <div class="flex items-center gap-1.5 font-bold text-blue-900 text-[10px]">
                  <span>🏛</span>
                  <span class="uppercase tracking-wider">INSTITUTION ADDRESS</span>
                </div>
                <p class="text-slate-700 leading-snug font-medium">
                  {{ $school->school_name ?? 'Demo School Main Campus' }}<br>
                  {{ $school->address ?? '123, Main Street, Chennai - 600001' }}
                </p>
              </div>

              {{-- Employee Residential Address Box --}}
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 text-[10px] space-y-1">
                <div class="flex items-center gap-1.5 font-bold text-blue-900 text-[10px]">
                  <span>🏠</span>
                  <span class="uppercase tracking-wider">EMPLOYEE RESIDENTIAL ADDRESS</span>
                </div>
                <p class="text-slate-700 leading-snug font-medium">
                  {{ $employee->residential_address ?? $employee->address ?? 'Staff Quarters Road, Campus Block B - 600001' }}
                </p>
              </div>
            </div>

            {{-- Helpline & Authorization Note --}}
            <div class="flex items-center justify-between text-[10px] pt-1">
              <span class="text-slate-400 text-[9px]">Property of {{ $school->school_name ?? config('app.name') }}</span>
              <span class="font-bold text-slate-800 text-[10px]">
                Helpline: <span class="font-mono text-blue-900">+91 {{ $school->phone ?? $employee->emergency_contact_mobile ?? '9876543210' }}</span>
              </span>
            </div>
          </div>

          {{-- Card Footer --}}
          <div class="px-5 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[9px] font-bold text-slate-400">
            <span>{{ $school->website ?? 'www.dasaeduerp.com' }}</span>
            <span class="tracking-wider text-slate-400">BACK SIDE</span>
          </div>

        </div>
        <p class="text-center text-xs font-black text-slate-500 uppercase tracking-wider mt-3 print:hidden">
          BACK SIDE PREVIEW ({{ $theme['theme_label'] }})
        </p>
      </div>

    </div>

  </div>
  @else
  <div class="card text-center py-16">
    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">🪪</div>
    <h3 class="text-base font-bold text-slate-700">No Employee Found</h3>
    <p class="text-xs text-slate-500 mt-1">Please select another department or category filter from above.</p>
  </div>
  @endif

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
