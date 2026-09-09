@extends('layouts.app')
@section('title', 'Syllabus & Curriculum Management')

@push('head')
<style>
  /* ── Ultra-Premium Syllabus Design System ────────── */
  .syllabus-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(79, 70, 229, 0.3);
  }
  .syllabus-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
  }

  /* Class Selector Pills */
  .class-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1rem;
    border-radius: 1rem;
    background: white;
    border: 2px solid #e2e8f0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 90px;
    text-align: center;
    position: relative;
  }
  .class-pill:hover {
    border-color: #818cf8;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.12);
  }
  .class-pill.active {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border-color: #4338ca;
    color: white !important;
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    transform: translateY(-2px);
  }
  .class-pill.active .class-sub-text { color: rgba(255, 255, 255, 0.8) !important; }
  .class-pill.active .class-pct-badge { background: rgba(255, 255, 255, 0.25); color: white; }

  /* Term Selection Cards */
  .term-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
  }
  .term-card:hover {
    border-color: #a5b4fc;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.08);
  }
  .term-card.active {
    border-color: #6366f1;
    background: #f8faff;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
  }
  .term-card.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #8b5cf6);
  }

  /* Chapter Card */
  .chapter-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    transition: all 0.2s ease;
    position: relative;
  }
  .chapter-card:hover {
    border-color: #c7d2fe;
    box-shadow: 0 6px 22px rgba(79, 70, 229, 0.07);
    transform: translateY(-1px);
  }
  .chapter-card.status-completed { border-left: 4.5px solid #16a34a; }
  .chapter-card.status-in_progress { border-left: 4.5px solid #f59e0b; }
  .chapter-card.status-pending { border-left: 4.5px solid #94a3b8; }

  /* Status badge cycle */
  .status-badge {
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    border: none;
    padding: 0.3rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: capitalize;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
  }
  .status-badge:hover { transform: scale(1.05); }
  .status-completed { background: #dcfce7; color: #15803d; }
  .status-in_progress { background: #fef3c7; color: #b45309; }
  .status-pending { background: #f1f5f9; color: #475569; }

  /* Subject Tile Card */
  .subject-tile {
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 1rem;
    padding: 0.9rem 1rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    overflow: hidden;
    min-height: 105px;
  }
  .subject-tile:hover {
    border-color: #a5b4fc;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(99, 102, 241, 0.1);
  }
  .subject-tile.active {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border-color: #4338ca;
    color: white !important;
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.28);
    transform: translateY(-2px);
  }
  .subject-tile.active .tile-text-muted { color: rgba(255, 255, 255, 0.75) !important; }
  .subject-tile.active .tile-badge { background: rgba(255, 255, 255, 0.22); color: white !important; }
  .subject-tile.active .tile-dot { background-color: #34d399 !important; }
  .subject-tile.active .tile-track { background-color: rgba(255, 255, 255, 0.25) !important; }
  .subject-tile.active .tile-bar { background-color: #34d399 !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="syllabusApp()">

  {{-- ── 1. Top Standard / Class Selector Bar ────────────── --}}
  <div class="card p-4">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Select Standard / Class</h2>
      </div>
      <span class="text-xs text-slate-400 font-medium">Curriculum organized standard-wise</span>
    </div>

    <!-- Scrollable Class Badges -->
    <div class="flex items-center gap-2.5 overflow-x-auto pb-2 pt-1 no-scrollbar">
      @foreach($classes as $c)
        @php
          $isActive = ($selectedClass && $selectedClass->id === $c->id);
          $pct = $c->progress_pct ?? 0;
          $pctColor = $pct >= 75 ? 'text-green-600 bg-green-50' : ($pct >= 40 ? 'text-amber-600 bg-amber-50' : 'text-slate-500 bg-slate-100');
        @endphp
        <a href="{{ route('academics.syllabus', ['class_id' => $c->id]) }}"
           class="class-pill {{ $isActive ? 'active' : '' }} flex-shrink-0">
          <span class="font-extrabold text-sm tracking-tight {{ $isActive ? 'text-white' : 'text-slate-800' }}">
            {{ $c->name }}
          </span>
          <span class="class-sub-text text-[11px] {{ $isActive ? 'text-indigo-100' : 'text-slate-400' }} mt-0.5 font-medium">
            {{ $c->total_chapters }} chaps
          </span>
          <span class="class-pct-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full mt-1.5 {{ $isActive ? '' : $pctColor }}">
            {{ $pct }}%
          </span>
        </a>
      @endforeach
    </div>
  </div>

  @if($selectedClass)
    {{-- ── 2. Standard Hero & Progress Header ──────────────── --}}
    <div class="syllabus-hero">
      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-bold backdrop-blur-sm">
              Standard: {{ $selectedClass->name }}
            </span>
            <span class="text-indigo-200 text-xs font-medium">
              {{ $classSubjects->count() }} Subjects Configured
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            {{ $selectedClass->display_name ?? 'Class ' . $selectedClass->name }} Curriculum
          </h1>
          <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-xl">
            Term-wise chapter planning, learning outcomes & syllabus completion tracking
          </p>

          <!-- Overall Standard Progress Bar -->
          <div class="mt-4 max-w-md">
            <div class="flex items-center justify-between text-xs font-bold text-white mb-1.5">
              <span>Overall Completion Progress</span>
              <span>{{ $classOverallStats['completed'] }} / {{ $classOverallStats['total'] }} Chapters ({{ $classOverallStats['pct'] }}%)</span>
            </div>
            <div class="h-2.5 bg-white/20 rounded-full overflow-hidden flex backdrop-blur-sm p-0.5">
              <div class="bg-emerald-400 h-full rounded-full transition-all duration-500"
                   style="width: {{ $classOverallStats['pct'] }}%"></div>
              <div class="bg-amber-300 h-full transition-all duration-500"
                   style="width: {{ $classOverallStats['total'] > 0 ? round(($classOverallStats['in_progress'] / $classOverallStats['total']) * 100) : 0 }}%"></div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Batch Add Topics -->
          <button type="button" @click="openBatchModal()"
                  class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold backdrop-blur-sm border border-white/20 transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Batch Add Topics
          </button>

          <!-- Print Sheet -->
          <a href="{{ route('academics.syllabus.print', ['class_id' => $selectedClass->id, 'term' => request('term'), 'subject_id' => request('subject_id')]) }}"
             target="_blank"
             class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold backdrop-blur-sm border border-white/20 transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Sheet
          </a>

          <!-- Coverage Report -->
          <a href="{{ route('academics.syllabus-coverage', ['class_id' => $selectedClass->id]) }}"
             class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold backdrop-blur-sm border border-white/20 transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Coverage Report
          </a>
        </div>
      </div>
    </div>

    {{-- ── 3. Term Division Cards (Term 1 & Term 2 — Official School Structure) ── --}}
    <div>
      <div class="flex items-center justify-between mb-2.5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          2-Term Academic Portion Breakdown
        </h3>
        @if(request('term'))
          <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'subject_id' => request('subject_id')]) }}"
             class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
            View Complete Year
          </a>
        @endif
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        @php
          $termMeta = [
            'Term 1' => ['label' => 'Term 1', 'subtitle' => 'Jun – Nov (Half-Yearly)', 'color' => 'indigo'],
            'Term 2' => ['label' => 'Term 2', 'subtitle' => 'Nov – Apr (Annual / Final)', 'color' => 'emerald'],
          ];
        @endphp

        <!-- All Terms Overview Card -->
        <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'subject_id' => request('subject_id')]) }}"
           class="term-card {{ !request('term') ? 'active' : '' }}">
          <div class="flex items-start justify-between">
            <div>
              <p class="font-extrabold text-sm text-slate-800">Complete Academic Year</p>
              <p class="text-[11px] text-slate-400 mt-0.5">All Portions (Term 1 &amp; Term 2)</p>
            </div>
            <span class="text-xs font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
              {{ $classOverallStats['pct'] }}%
            </span>
          </div>
          <div class="mt-3">
            <div class="flex justify-between text-[11px] text-slate-500 font-medium mb-1">
              <span>{{ $classOverallStats['completed'] }}/{{ $classOverallStats['total'] }} Chaps</span>
              <span>{{ $classOverallStats['total'] - $classOverallStats['completed'] }} Remaining</span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $classOverallStats['pct'] }}%"></div>
            </div>
          </div>
        </a>

        <!-- Term 1 and Term 2 Cards -->
        @foreach(['Term 1', 'Term 2'] as $t)
          @php
            $stats = $termStats[$t] ?? ['total' => 0, 'completed' => 0, 'in_progress' => 0, 'pct' => 0];
            $meta = $termMeta[$t];
            $isSelectedTerm = (request('term') === $t);
          @endphp
          <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'term' => $t, 'subject_id' => request('subject_id')]) }}"
             class="term-card {{ $isSelectedTerm ? 'active' : '' }}">
            <div class="flex items-start justify-between">
              <div>
                <p class="font-extrabold text-sm text-slate-800">{{ $meta['label'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $meta['subtitle'] }}</p>
              </div>
              <span class="text-xs font-extrabold text-{{ $meta['color'] }}-600 bg-{{ $meta['color'] }}-50 px-2 py-0.5 rounded-full">
                {{ $stats['pct'] }}%
              </span>
            </div>
            <div class="mt-3">
              <div class="flex justify-between text-[11px] text-slate-500 font-medium mb-1">
                <span>{{ $stats['completed'] }}/{{ $stats['total'] }} Done</span>
                <span>{{ $stats['in_progress'] }} In-Progress</span>
              </div>
              <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden flex">
                <div class="h-full bg-emerald-500" style="width: {{ $stats['pct'] }}%"></div>
                <div class="h-full bg-amber-400" style="width: {{ $stats['total'] > 0 ? round(($stats['in_progress'] / $stats['total']) * 100) : 0 }}%"></div>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>

    {{-- ── 4. Standard-Specific Subject Grid & Actions ── --}}
    <div class="card p-5 sm:p-6">
      <div class="flex items-center justify-between mb-4 flex-wrap gap-3 pb-3.5 border-b border-slate-100">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">
              Subjects in {{ $selectedClass->name }}
            </h3>
            <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
              {{ $classSubjects->count() }} Subjects
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">Click any subject card to filter chapters or view full curriculum</p>
        </div>

        <div class="flex items-center gap-2.5">
          <!-- Add Subject Button -->
          <button type="button" @click="openAddSubjectModal()"
                  class="text-xs font-bold text-slate-700 hover:text-indigo-700 flex items-center gap-1.5 bg-slate-50 hover:bg-indigo-50 px-3.5 py-2 rounded-xl border border-slate-200 hover:border-indigo-200 transition shadow-2xs">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Subject
          </button>

          <!-- Add Chapter Button -->
          <button type="button" @click="openAddModal('{{ request('subject_id') }}')"
                  class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 flex items-center gap-1.5 px-4 py-2 rounded-xl shadow-xs transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Chapter
          </button>
        </div>
      </div>

      <!-- Spacious Responsive Subject Tiles Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3.5">
        <!-- All Subjects Tile -->
        @php $isAllActive = !request('subject_id'); @endphp
        <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'term' => request('term')]) }}"
           class="subject-tile {{ $isAllActive ? 'active' : '' }}">
          <div class="flex items-center justify-between gap-1 mb-2">
            <span class="text-[10px] font-bold uppercase tracking-wider tile-badge px-2 py-0.5 rounded-full {{ $isAllActive ? '' : 'bg-slate-100 text-slate-500' }}">
              Overview
            </span>
            <span class="text-[11px] font-extrabold tile-badge px-2 py-0.5 rounded-full {{ $isAllActive ? '' : 'bg-indigo-50 text-indigo-700' }}">
              {{ $syllabus->count() }} ch
            </span>
          </div>

          <div class="my-auto py-1">
            <h4 class="font-extrabold text-sm tracking-tight leading-snug">
              All Subjects
            </h4>
            <p class="text-[11px] tile-text-muted text-slate-400 mt-0.5">Full Standard</p>
          </div>

          <div class="mt-2.5 pt-2 border-t {{ $isAllActive ? 'border-white/20' : 'border-slate-100' }}">
            <div class="flex justify-between items-center text-[10px] font-bold mb-1">
              <span class="tile-text-muted text-slate-400">Total Progress</span>
              <span>{{ $classOverallStats['pct'] }}%</span>
            </div>
            <div class="h-1.5 rounded-full overflow-hidden tile-track {{ $isAllActive ? '' : 'bg-slate-100' }}">
              <div class="h-full rounded-full tile-bar {{ $isAllActive ? '' : 'bg-indigo-600' }}" style="width: {{ $classOverallStats['pct'] }}%"></div>
            </div>
          </div>
        </a>

        <!-- Individual Class Subject Tiles -->
        @foreach($classSubjects as $sub)
          @php
            $isSubActive = (request('subject_id') == $sub->id);
            $dotColor = $sub->progress_pct >= 80 ? 'bg-emerald-500' : ($sub->progress_pct >= 40 ? 'bg-amber-400' : 'bg-slate-300');
          @endphp
          <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'subject_id' => $sub->id, 'term' => request('term')]) }}"
             class="subject-tile {{ $isSubActive ? 'active' : '' }}">
            <div class="flex items-center justify-between gap-1 mb-2">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full tile-dot {{ $dotColor }}"></span>
                <span class="text-[10px] font-bold uppercase tracking-wider tile-text-muted text-slate-400">
                  {{ $sub->type ?? 'Subject' }}
                </span>
              </span>
              <span class="text-[11px] font-extrabold tile-badge px-2 py-0.5 rounded-full {{ $isSubActive ? '' : 'bg-slate-100 text-slate-600' }}">
                {{ $sub->total_chapters }} ch
              </span>
            </div>

            <div class="my-auto py-1">
              <h4 class="font-extrabold text-sm tracking-tight leading-snug line-clamp-2" title="{{ $sub->name }}">
                {{ $sub->name }}
              </h4>
            </div>

            <div class="mt-2.5 pt-2 border-t {{ $isSubActive ? 'border-white/20' : 'border-slate-100' }}">
              <div class="flex justify-between items-center text-[10px] font-bold mb-1">
                <span class="tile-text-muted text-slate-400">{{ $sub->total_chapters }} Chapters</span>
                <span>{{ $sub->progress_pct }}%</span>
              </div>
              <div class="h-1.5 rounded-full overflow-hidden tile-track {{ $isSubActive ? '' : 'bg-slate-100' }}">
                <div class="h-full rounded-full tile-bar {{ $isSubActive ? '' : 'bg-emerald-500' }}" style="width: {{ $sub->progress_pct }}%"></div>
              </div>
            </div>
          </a>
        @endforeach
      </div>

      @if(request('subject_id'))
        @php
          $currentSubject = $classSubjects->firstWhere('id', request('subject_id'));
        @endphp
        @if($currentSubject)
          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 flex-wrap gap-2">
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-800">{{ $currentSubject->name }}</span>
              <span>•</span>
              <span>{{ $currentSubject->total_chapters }} Chapters in {{ $selectedClass->name }}</span>
              <span>•</span>
              <span class="text-indigo-600 font-bold">{{ $currentSubject->progress_pct }}% Completed</span>
            </div>
            <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'term' => request('term')]) }}"
               class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
              ← View All Subjects
            </a>
          </div>
        @endif
      @endif
    </div>

    {{-- ── 5. Chapter Cards List with Rich Metadata & Status Cycler ── --}}
    <div class="space-y-3.5">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <h3 class="text-sm font-bold text-slate-700">
            @if(request('term') && request('subject_id'))
              {{ request('term') }} • {{ $classSubjects->firstWhere('id', request('subject_id'))?->name }} Chapters
            @elseif(request('term'))
              {{ request('term') }} Chapters
            @elseif(request('subject_id'))
              {{ $classSubjects->firstWhere('id', request('subject_id'))?->name }} Chapters
            @else
              All Curriculum Chapters ({{ $syllabus->count() }})
            @endif
          </h3>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
          <span>Click status badge to toggle</span>
        </div>
      </div>

      <!-- Chapter Cards Loop -->
      <div id="chapterList" class="space-y-3">
        @forelse($syllabus as $item)
          <div class="chapter-card status-{{ $item->status }}" id="chapter-{{ $item->id }}">
            
            {{-- VIEW MODE --}}
            <div id="view-{{ $item->id }}">
              <div class="flex items-start gap-3.5">
                <!-- Chapter Number Box -->
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center text-indigo-700 font-extrabold text-sm shadow-xs">
                  <span class="text-[10px] font-semibold text-indigo-400 uppercase leading-none">Ch</span>
                  <span>{{ $item->chapter_number ?: ($loop->index + 1) }}</span>
                </div>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h4 class="font-bold text-slate-800 text-sm leading-snug">
                      {{ $item->chapter_title }}
                    </h4>
                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[11px] font-bold">
                      {{ $item->term ?: 'Term 1' }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-semibold">
                      {{ $item->subject?->name }}
                    </span>
                  </div>

                  @if($item->topics)
                    <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed">
                      <strong class="text-slate-700">Topics:</strong> {{ $item->topics }}
                    </p>
                  @endif

                  @if($item->description && $item->description !== $item->topics)
                    <p class="text-xs text-slate-500 mt-1 italic flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      {{ $item->description }}
                    </p>
                  @endif

                  <!-- Dates & PDF Info -->
                  <div class="flex items-center gap-4 mt-2.5 text-[11px] text-slate-400 font-medium flex-wrap">
                    @if($item->planned_date)
                      <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Planned: {{ \Carbon\Carbon::parse($item->planned_date)->format('d M Y') }}
                      </span>
                    @endif
                    @if($item->completed_date)
                      <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Completed: {{ \Carbon\Carbon::parse($item->completed_date)->format('d M Y') }}
                      </span>
                    @endif
                    @if($item->document_path)
                      <a href="{{ asset('storage/' . $item->document_path) }}" target="_blank"
                         class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Syllabus PDF
                      </a>
                    @endif
                  </div>
                </div>

                <!-- Right Actions: Status & Quick Buttons -->
                <div class="flex items-center gap-2 flex-shrink-0">
                  <!-- 1-Click Interactive Status Badge -->
                  <button type="button"
                          class="status-badge status-{{ $item->status }}"
                          onclick="cycleStatus({{ $item->id }}, '{{ $item->status }}', this)"
                          title="Click to toggle status (Pending -> In Progress -> Completed)">
                    @if($item->status === 'completed')
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Completed
                    @elseif($item->status === 'in_progress')
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> In Progress
                    @else
                      <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Pending
                    @endif
                  </button>

                  <!-- Edit Button -->
                  <button type="button" onclick="showEdit({{ $item->id }})"
                          class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 transition"
                          title="Edit Chapter Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>

                  <!-- Delete Button -->
                  <form method="POST" action="{{ route('academics.syllabus.delete', $item->id) }}"
                        onsubmit="return confirm('Remove this chapter from syllabus?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                </div>
              </div>

              <!-- Inline Attach PDF Toggle -->
              <div x-data="{ showPdf: false }" class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <button type="button" @click="showPdf = !showPdf" class="text-slate-400 hover:text-indigo-600 font-semibold flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                  {{ $item->document_path ? 'Replace Attached PDF' : 'Attach Lesson PDF' }}
                </button>

                <div x-show="showPdf" x-cloak class="w-full mt-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                  <form method="POST" action="{{ route('academics.syllabus.document', $item->id) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                      <label class="block text-[11px] font-bold text-slate-500 mb-1">Upload PDF (Max 10MB)</label>
                      <input type="file" name="document" accept=".pdf" class="input text-xs py-1" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Upload File</button>
                  </form>
                </div>
              </div>
            </div>

            {{-- EDIT MODE (Inline Form) --}}
            <div id="edit-{{ $item->id }}" class="hidden">
              <form method="POST" action="{{ route('academics.syllabus.update', $item->id) }}" class="space-y-3">
                @csrf @method('PUT')
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                  <span class="text-xs font-bold text-indigo-700 uppercase tracking-wide">Edit Chapter #{{ $item->chapter_number }}</span>
                  <button type="button" onclick="hideEdit({{ $item->id }})" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <div>
                    <label class="label text-xs">Term <span class="text-red-500">*</span></label>
                    <select name="term" class="select text-xs" required>
                      <option value="Term 1" @selected($item->term === 'Term 1')>Term 1 (Jun–Nov)</option>
                      <option value="Term 2" @selected($item->term === 'Term 2')>Term 2 (Nov–Apr)</option>
                    </select>
                  </div>
                  <div>
                    <label class="label text-xs">Chapter No.</label>
                    <input type="text" name="chapter_number" value="{{ $item->chapter_number }}" class="input text-xs">
                  </div>
                  <div>
                    <label class="label text-xs">Status</label>
                    <select name="status" class="select text-xs">
                      <option value="pending" @selected($item->status === 'pending')>Pending</option>
                      <option value="in_progress" @selected($item->status === 'in_progress')>In Progress</option>
                      <option value="completed" @selected($item->status === 'completed')>Completed</option>
                    </select>
                  </div>
                  <div class="sm:col-span-3">
                    <label class="label text-xs">Chapter Title <span class="text-red-500">*</span></label>
                    <input type="text" name="chapter_title" value="{{ $item->chapter_title }}" class="input text-xs" required>
                  </div>
                  <div class="sm:col-span-3">
                    <label class="label text-xs">Key Topics / Concepts Covered</label>
                    <textarea name="topics" rows="2" class="input text-xs">{{ $item->topics }}</textarea>
                  </div>
                  <div class="sm:col-span-3">
                    <label class="label text-xs">Teaching Notes & Learning Outcomes</label>
                    <textarea name="description" rows="2" class="input text-xs">{{ $item->description }}</textarea>
                  </div>
                  <div>
                    <label class="label text-xs">Planned Date</label>
                    <input type="date" name="planned_date" value="{{ $item->planned_date?->format('Y-m-d') }}" class="input text-xs">
                  </div>
                  <div>
                    <label class="label text-xs">Completed Date</label>
                    <input type="date" name="completed_date" value="{{ $item->completed_date?->format('Y-m-d') }}" class="input text-xs">
                  </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                  <button type="button" onclick="hideEdit({{ $item->id }})" class="btn btn-secondary btn-sm">Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
              </form>
            </div>

          </div>
        @empty
          <div class="card text-center py-12 text-slate-400">
            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <p class="font-bold text-slate-600 text-sm">No chapters found for the selected criteria</p>
            <p class="text-xs text-slate-400 mt-1">Use the "+ Add Chapter" button to create curriculum chapters.</p>
            <button type="button" @click="openAddModal('{{ request('subject_id') }}')" class="btn btn-primary btn-sm mt-3 font-bold">
              + Add First Chapter
            </button>
          </div>
        @endforelse
      </div>

      <!-- Quick Add Chapter Bottom Card -->
      @if($syllabus->count() > 0)
        <div class="card p-3.5 border-2 border-dashed border-slate-200 hover:border-indigo-300 transition text-center cursor-pointer bg-slate-50/50 hover:bg-indigo-50/30"
             @click="openAddModal('{{ request('subject_id') }}')">
          <p class="text-xs font-bold text-indigo-600 flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Another Chapter @if(request('subject_id') && $classSubjects->firstWhere('id', request('subject_id'))) under {{ $classSubjects->firstWhere('id', request('subject_id'))->name }} @endif
          </p>
        </div>
      @endif
    </div>
  @else
    <div class="card text-center py-16">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5"/></svg>
      <h3 class="text-lg font-bold text-slate-700">Please Select a Standard to View Syllabus</h3>
      <p class="text-xs text-slate-400 mt-1">Choose from Pre-KG, LKG, UKG, or Class I - XII in the top bar.</p>
    </div>
  @endif

</div>

{{-- ── MODAL 1: Add Subject to Standard Modal ────────────── --}}
<div id="addSubjectModal" class="fixed inset-0 z-50 hidden items-center justify-center" x-data="{ mode: 'existing' }">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" onclick="closeAddSubjectModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto z-10 border border-slate-100">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-5 rounded-t-2xl text-white">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-extrabold text-white">Add Subject to {{ $selectedClass?->name }}</h3>
          <p class="text-xs text-indigo-100 mt-0.5">Configure a new subject or assign from master list</p>
        </div>
        <button onclick="closeAddSubjectModal()" class="text-white/80 hover:text-white text-2xl font-bold">&times;</button>
      </div>

      <!-- Mode Toggle -->
      <div class="flex gap-2 mt-3 bg-black/15 p-1 rounded-xl">
        <button type="button" @click="mode = 'existing'"
                :class="mode === 'existing' ? 'bg-white text-indigo-700 font-bold shadow-xs' : 'text-white/80 font-medium'"
                class="flex-1 py-1.5 rounded-lg text-xs transition">
          From Master List
        </button>
        <button type="button" @click="mode = 'new'"
                :class="mode === 'new' ? 'bg-white text-indigo-700 font-bold shadow-xs' : 'text-white/80 font-medium'"
                class="flex-1 py-1.5 rounded-lg text-xs transition">
          Create New Subject
        </button>
      </div>
    </div>

    <div class="p-5">
      <form method="POST" action="{{ route('academics.subjects.store') }}" class="space-y-3.5">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClass?->id }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="sm:col-span-2">
            <label class="label text-xs font-bold text-slate-700">Subject Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="input text-xs" placeholder="e.g. Storytelling, Tamil, Art & Craft, Hindi" required>
          </div>
          <div>
            <label class="label text-xs font-bold text-slate-700">Subject Type <span class="text-red-500">*</span></label>
            <select name="type" class="select text-xs font-semibold" required>
              <option value="language">Language</option>
              <option value="activity" selected>Activity / Co-curricular</option>
              <option value="theory">Theory / Core</option>
              <option value="practical">Practical / Lab</option>
            </select>
          </div>
          <div>
            <label class="label text-xs font-bold text-slate-700">Assign to Term</label>
            <select name="term" class="select text-xs font-semibold">
              <option value="Term 1">Term 1 (Jun–Nov)</option>
              <option value="Term 2">Term 2 (Nov–Apr)</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="label text-xs">First Chapter / Topic (Optional)</label>
            <input type="text" name="first_topic" class="input text-xs" placeholder="e.g. Chapter 1: Introduction & Basic Sounds">
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" onclick="closeAddSubjectModal()" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm font-bold">+ Add Subject to {{ $selectedClass?->name }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── MODAL 2: Single Add Chapter Modal ─────────────────── --}}
<div id="addChapterModal" class="fixed inset-0 z-50 hidden items-center justify-center">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" onclick="closeAddModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto z-10 border border-slate-100">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-5 rounded-t-2xl text-white">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-extrabold text-white" id="addChapterModalTitle">Add Curriculum Chapter</h3>
          <p class="text-xs text-indigo-100 mt-0.5">
            Standard: {{ $selectedClass?->name ?? 'Select Class' }}
          </p>
        </div>
        <button onclick="closeAddModal()" class="text-white/80 hover:text-white text-2xl font-bold">&times;</button>
      </div>
    </div>

    <form method="POST" action="{{ route('academics.syllabus.save') }}" class="p-5 space-y-3.5">
      @csrf
      <input type="hidden" name="class_id" value="{{ $selectedClass?->id }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="sm:col-span-2">
          <label class="label text-xs font-bold text-slate-700">Select Subject in {{ $selectedClass?->name }} <span class="text-red-500">*</span></label>
          <select name="subject_id" id="addModalSubjectSelect" class="select text-xs font-semibold" required onchange="updateModalHeader(this)">
            <option value="">— Select Subject in {{ $selectedClass?->name }} —</option>
            @foreach($classSubjects as $s)
              <option value="{{ $s->id }}" @selected(request('subject_id') == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs">Term Division <span class="text-red-500">*</span></label>
          <select name="term" class="select text-xs" required>
            <option value="Term 1" @selected(request('term') === 'Term 1' || !request('term'))>Term 1 (Jun–Nov)</option>
            <option value="Term 2" @selected(request('term') === 'Term 2')>Term 2 (Nov–Apr)</option>
          </select>
        </div>
        <div>
          <label class="label text-xs">Chapter Number / Code</label>
          <input type="text" name="chapter_number" class="input text-xs" placeholder="e.g. 1, 2A, etc.">
        </div>
        <div>
          <label class="label text-xs">Status</label>
          <select name="status" class="select text-xs">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div>
          <label class="label text-xs">Planned Target Date</label>
          <input type="date" name="planned_date" class="input text-xs">
        </div>
        <div class="sm:col-span-2">
          <label class="label text-xs">Chapter Title <span class="text-red-500">*</span></label>
          <input type="text" name="chapter_title" class="input text-xs" placeholder="e.g. Phonics & Vowel Sounds / Force and Motion" required>
        </div>
        <div class="sm:col-span-2">
          <label class="label text-xs">Topics Covered</label>
          <textarea name="topics" rows="2" class="input text-xs" placeholder="Key sub-topics, experiments, or exercises..."></textarea>
        </div>
        <div class="sm:col-span-2">
          <label class="label text-xs">Learning Outcomes / Teaching Activities</label>
          <textarea name="description" rows="2" class="input text-xs" placeholder="Specific learning goals, practical activities..."></textarea>
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
        <button type="button" onclick="closeAddModal()" class="btn btn-secondary btn-sm">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm font-bold">Add Chapter</button>
      </div>
    </form>
  </div>
</div>

{{-- ── MODAL 3: Batch Add Topics Modal ──────────────────── --}}
<div id="batchModal" class="fixed inset-0 z-50 hidden items-center justify-center">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" onclick="closeBatchModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto z-10 border border-slate-100">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-5 rounded-t-2xl text-white">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-extrabold text-white">Batch Add Topics</h3>
          <p class="text-xs text-emerald-100 mt-0.5">Paste multiple chapter titles at once (one per line)</p>
        </div>
        <button onclick="closeBatchModal()" class="text-white/80 hover:text-white text-2xl font-bold">&times;</button>
      </div>
    </div>

    <form method="POST" action="{{ route('academics.syllabus.batch') }}" class="p-5 space-y-3.5">
      @csrf
      <input type="hidden" name="class_id" value="{{ $selectedClass?->id }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="label text-xs font-bold text-slate-700">Subject in {{ $selectedClass?->name }} <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select text-xs font-semibold" required>
            <option value="">— Select Subject in {{ $selectedClass?->name }} —</option>
            @foreach($classSubjects as $s)
              <option value="{{ $s->id }}" @selected(request('subject_id') == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs font-bold text-slate-700">Target Term <span class="text-red-500">*</span></label>
          <select name="term" class="select text-xs font-semibold" required>
            <option value="Term 1" @selected(request('term') === 'Term 1' || !request('term'))>Term 1 (Jun–Nov)</option>
            <option value="Term 2" @selected(request('term') === 'Term 2')>Term 2 (Nov–Apr)</option>
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="label text-xs">List of Chapter Titles (One per line) <span class="text-red-500">*</span></label>
          <textarea name="topics_list" rows="6" class="input text-xs font-mono" placeholder="1. Introduction to Numbers&#10;2. Shapes and Colors&#10;3. Addition Basics" required></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Tip: You can include numbers like "1. Title" and it will extract the chapter number automatically.</p>
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
        <button type="button" onclick="closeBatchModal()" class="btn btn-secondary btn-sm">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm font-bold bg-emerald-600 hover:bg-emerald-700">Add All Chapters</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function syllabusApp() {
    return {};
}

function openAddSubjectModal() {
    const m = document.getElementById('addSubjectModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeAddSubjectModal() {
    const m = document.getElementById('addSubjectModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function openAddModal(subjectId = '', subjectName = '') {
    const titleEl = document.getElementById('addChapterModalTitle');
    const select = document.getElementById('addModalSubjectSelect');

    if (subjectId && select) {
        select.value = subjectId;
        if (subjectName && titleEl) {
            titleEl.innerText = 'Add Chapter under ' + subjectName;
        } else if (select.selectedOptions[0] && titleEl) {
            titleEl.innerText = 'Add Chapter under ' + select.selectedOptions[0].text;
        }
    } else if (titleEl) {
        titleEl.innerText = 'Add Curriculum Chapter';
    }

    const m = document.getElementById('addChapterModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function updateModalHeader(select) {
    const titleEl = document.getElementById('addChapterModalTitle');
    if (select.value && select.selectedOptions[0] && titleEl) {
        titleEl.innerText = 'Add Chapter under ' + select.selectedOptions[0].text.replace(/^\+\s*/, '');
    } else if (titleEl) {
        titleEl.innerText = 'Add Curriculum Chapter';
    }
}

function closeAddModal() {
    const m = document.getElementById('addChapterModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function openBatchModal() {
    const m = document.getElementById('batchModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeBatchModal() {
    const m = document.getElementById('batchModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function showEdit(id) {
    document.getElementById('view-' + id).classList.add('hidden');
    document.getElementById('edit-' + id).classList.remove('hidden');
}
function hideEdit(id) {
    document.getElementById('view-' + id).classList.remove('hidden');
    document.getElementById('edit-' + id).classList.add('hidden');
}

const statusCycle = {
    'pending': 'in_progress',
    'in_progress': 'completed',
    'completed': 'pending'
};
const statusBadges = {
    'pending': '<span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span> Pending',
    'in_progress': '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span> In Progress',
    'completed': '<span class="w-1.5 h-1.5 rounded-full bg-emerald-600 inline-block"></span> Completed'
};

function cycleStatus(id, currentStatus, btn) {
    const nextStatus = statusCycle[currentStatus];

    btn.className = 'status-badge status-' + nextStatus;
    btn.innerHTML = statusBadges[nextStatus];
    btn.setAttribute('onclick', `cycleStatus(${id}, '${nextStatus}', this)`);

    const card = document.getElementById('chapter-' + id);
    card.className = card.className.replace(/status-\w+/, 'status-' + nextStatus);

    fetch('{{ url("/academics/syllabus") }}/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: nextStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Status updated to ' + nextStatus.replace('_', ' '));
        }
    })
    .catch(() => showToast('Failed to update status', 'error'));
}

function showToast(msg, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl shadow-xl text-xs font-bold flex items-center gap-2 transition-all ${type === 'error' ? 'bg-rose-600 text-white' : 'bg-slate-900 text-white'}`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAddSubjectModal();
        closeAddModal();
        closeBatchModal();
    }
});
</script>
@endpush
@endsection
