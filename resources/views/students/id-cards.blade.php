@extends('layouts.app')

@section('title', 'Student ID Cards — Wing-Wise Studio')

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="page-title text-xl font-black text-slate-900">Student ID Card Studio</h1>
        <span class="badge-blue text-[10px] font-bold">Wing-Wise Categorized</span>
      </div>
      <p class="text-xs text-slate-500 mt-0.5">Filter, preview wing-themed ID cards, and export bulk printable PDFs for {{ $currentYear?->name ?? 'Current Academic Year' }}</p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('students.id-card-template') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold">
        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        Template Designer
      </a>

      <a href="{{ route('students.id-cards.pdf', ['wing' => $wingFilter, 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}" target="_blank"
         class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Generate Bulk PDF ({{ $filteredEnrollments->count() }})
      </a>
    </div>
  </div>

  {{-- Wing Filter Tabs --}}
  <div class="card p-4 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap items-center gap-1.5">
        <a href="{{ route('students.id-cards', ['wing' => 'all', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
          All Wings ({{ $wingCounts['all'] ?? 0 }})
        </a>

        <a href="{{ route('students.id-cards', ['wing' => 'kg', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'kg' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
          Kindergarten ({{ $wingCounts['kg'] ?? 0 }})
        </a>

        <a href="{{ route('students.id-cards', ['wing' => 'primary', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'primary' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
          Primary ({{ $wingCounts['primary'] ?? 0 }})
        </a>

        <a href="{{ route('students.id-cards', ['wing' => 'middle', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'middle' ? 'bg-cyan-700 text-white shadow-xs' : 'bg-cyan-50 text-cyan-800 hover:bg-cyan-100 border border-cyan-200' }}">
          Middle Wing ({{ $wingCounts['middle'] ?? 0 }})
        </a>

        <a href="{{ route('students.id-cards', ['wing' => 'higher_secondary', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'higher_secondary' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200' }}">
          Higher Sec ({{ $wingCounts['higher_secondary'] ?? 0 }})
        </a>

        <a href="{{ route('students.id-cards', ['wing' => 'senior_secondary', 'class_id' => $classFilter, 'section_id' => $sectionFilter]) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $wingFilter === 'senior_secondary' ? 'bg-purple-700 text-white shadow-xs' : 'bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200' }}">
          Senior Sec ({{ $wingCounts['senior_secondary'] ?? 0 }})
        </a>
      </div>

      {{-- Class / Section Selectors --}}
      <form method="GET" action="{{ route('students.id-cards') }}" class="flex flex-wrap items-center gap-2">
        <input type="hidden" name="wing" value="{{ $wingFilter }}">
        <div>
          <select name="class_id" onchange="this.form.submit()" class="select-sm text-xs bg-slate-50 border-slate-200 rounded-lg">
            <option value="">All Classes</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" @selected($classFilter == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        @if($sections->count())
        <div>
          <select name="section_id" onchange="this.form.submit()" class="select-sm text-xs bg-slate-50 border-slate-200 rounded-lg">
            <option value="">All Sections</option>
            @foreach($sections as $s)
              <option value="{{ $s->id }}" @selected($sectionFilter == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        @if($classFilter || $sectionFilter || ($wingFilter && $wingFilter !== 'all'))
          <a href="{{ route('students.id-cards') }}" class="btn btn-ghost btn-xs text-slate-500">Reset</a>
        @endif
      </form>
    </div>
  </div>

  {{-- ── Preview Grid ────────────────────────────────────────── --}}
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-bold text-slate-800 text-sm">
        Displaying {{ $filteredEnrollments->count() }} Student ID Cards
        @if($wingFilter !== 'all')
          <span class="text-indigo-600">&bull; {{ ucfirst(str_replace('_', ' ', $wingFilter)) }} Wing</span>
        @endif
      </h3>
      <span class="text-xs text-slate-400">Click card for printable single view</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @forelse($filteredEnrollments as $enrollment)
      @php
        $s = $enrollment->student;
        $cls = $enrollment->class;
        $sec = $enrollment->section;
        $wing = $enrollment->wing_meta ?? [
          'key'             => 'primary',
          'name'            => 'Primary Wing',
          'tag'             => 'PRIMARY WING',
          'badge_color'     => 'bg-emerald-100 text-emerald-900 border-emerald-300 font-bold',
          'primary_color'   => '#059669',
          'header_gradient' => 'linear-gradient(135deg, #065f46 0%, #10b981 100%)',
          'accent'          => '#059669',
        ];
      @endphp
      @if($s)
      <a href="{{ route('students.id-card.single', $s->id) }}"
         class="group card p-0 border border-slate-200 hover:border-indigo-400 hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col justify-between block bg-white">

        {{-- Wing-Themed Header --}}
        <div class="p-3 text-white flex items-center justify-between"
             style="background: {{ $wing['header_gradient'] }};">
          <div>
            <p class="text-[10px] font-black uppercase tracking-wider leading-none drop-shadow-2xs">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</p>
            <span class="inline-block mt-1 text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-white/20 backdrop-blur-2xs text-white">
              {{ $wing['tag'] }}
            </span>
          </div>
          <span class="text-[9px] font-mono font-bold bg-black/20 px-2 py-0.5 rounded-full text-white/90">
            {{ $s->admission_number }}
          </span>
        </div>

        {{-- Body --}}
        <div class="p-3.5 flex items-center gap-3">
          {{-- Student Photo / Avatar --}}
          <div class="shrink-0">
            <div class="w-14 h-16 rounded-xl bg-slate-50 border overflow-hidden flex items-center justify-center shadow-2xs"
                 style="border-color: {{ $wing['primary_color'] }}40;">
              @if($s->photo)
                <img src="{{ asset('storage/' . $s->photo) }}" alt="{{ $s->full_name }}" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full flex items-center justify-center font-black text-sm"
                     style="background: {{ $wing['primary_color'] }}15; color: {{ $wing['primary_color'] }};">
                  {{ strtoupper(substr($s->first_name, 0, 1) . substr($s->last_name, 0, 1)) }}
                </div>
              @endif
            </div>
          </div>

          {{-- Info --}}
          <div class="min-w-0 flex-1 space-y-1">
            <p class="font-black text-sm text-slate-900 truncate group-hover:text-indigo-600 transition-colors">
              {{ $s->full_name }}
            </p>
            <p class="text-xs font-bold text-slate-700 truncate">
              Class {{ $cls?->name ?? '—' }} {{ $sec?->name ? '(' . $sec->name . ')' : '' }}
            </p>
            <div class="flex items-center gap-2 text-[10px] text-slate-500 font-medium">
              @if($s->blood_group)
                <span class="text-rose-600 font-bold bg-rose-50 px-1.5 py-0.2 rounded">{{ $s->blood_group }}</span>
              @endif
              @if($s->dob)
                <span>{{ $s->dob->format('d M Y') }}</span>
              @endif
            </div>
            <span class="inline-block mt-1 text-[10px] font-bold text-indigo-600 group-hover:underline">
              Print Single ID Card &rarr;
            </span>
          </div>
        </div>

        {{-- Footer --}}
        <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[9px] font-bold text-slate-400">
          <span>Valid: {{ $currentYear?->name ?? '2025-2026' }}</span>
          <span style="color: {{ $wing['primary_color'] }};">{{ $wing['short_name'] }}</span>
        </div>
      </a>
      @endif
      @empty
      <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-slate-200">
        <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-2">🎓</div>
        <p class="font-bold text-slate-700 text-sm">No Student ID Cards Found</p>
        <p class="text-xs text-slate-400 mt-0.5">Try selecting another wing or class from the filters above.</p>
      </div>
      @endforelse
    </div>
  </div>

</div>
@endsection
