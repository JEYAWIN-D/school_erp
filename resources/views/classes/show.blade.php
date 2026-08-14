@extends('layouts.admin')

@section('title', $class->display_name . ' — Classes')

@section('content')
<div class="space-y-6" x-data="{
  activeSectionId: {{ $class->sections->first()?->id ?? 'null' }},
  activeDay: 1,
  printTimetable() { window.print(); }
}">

  {{-- Top Navigation & Breadcrumb --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('classes.index') }}"
         class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:border-blue-300 hover:shadow-sm transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2.5">
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">
            {{ $class->display_name }}
          </h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
            Class {{ $class->name }}
          </span>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
            {{ ucfirst(str_replace('-', ' ', $class->category)) }}
          </span>
        </div>
        <p class="text-xs text-slate-500 mt-1">Academic Session {{ $currentYear->name ?? '2025-2026' }} • School Timing: 09:15 AM - 04:30 PM</p>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-2">
      <a :href="'{{ route('classes.timetable.pdf', $class->id) }}?section_id=' + activeSectionId"
         class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow-md transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Download Timetable PDF
      </a>
      <a :href="'{{ route('classes.timetable.csv', $class->id) }}?section_id=' + activeSectionId"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-xl transition">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
      </a>
    </div>
  </div>

  {{-- Section Selector Tabs --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-sm">
    <div class="flex flex-wrap items-center gap-2">
      @foreach($class->sections as $sec)
        @php
          $streamLabel = '';
          if ($class->numeric_value >= 11) {
            if ($sec->name === 'A') $streamLabel = ' (Computer Science)';
            elseif ($sec->name === 'B') $streamLabel = ' (Biology)';
            elseif ($sec->name === 'C') $streamLabel = ' (Commerce - BM)';
            else $streamLabel = ' (Commerce - CA)';
          }
        @endphp
        <button @click="activeSectionId = {{ $sec->id }}"
                :class="activeSectionId === {{ $sec->id }}
                  ? 'bg-blue-600 text-white shadow-sm font-semibold'
                  : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'"
                class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 transition duration-150">
          <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                :class="activeSectionId === {{ $sec->id }} ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
            {{ $sec->name }}
          </span>
          <span>Section {{ $sec->name }}{{ $streamLabel }}</span>
          <span class="text-[10px] opacity-80">({{ $sec->enrollments->count() > 0 ? $sec->enrollments->count() : 35 }} students)</span>
        </button>
      @endforeach
    </div>
  </div>

  {{-- Selected Section Content --}}
  @foreach($class->sections as $sec)
    @php
      $streamTitle = null;
      $streamDesc = null;
      if ($class->numeric_value >= 11) {
        if ($sec->name === 'A') {
          $streamTitle = 'Group 1 — Computer Science Stream';
          $streamDesc = 'Curriculum: Tamil, English, Mathematics, Physics (Theory+Lab), Chemistry (Theory+Lab), Computer Science (Python+SQL).';
        } elseif ($sec->name === 'B') {
          $streamTitle = 'Group 2 — Biology & Life Sciences Stream';
          $streamDesc = 'Curriculum: Tamil, English, Physics (Theory+Lab), Chemistry (Theory+Lab), Biology (Botany & Zoology), Mathematics.';
        } elseif ($sec->name === 'C') {
          $streamTitle = 'Group 3 — Commerce & Business Mathematics Stream';
          $streamDesc = 'Curriculum: Tamil, English, Commerce & Business Studies, Accountancy, Economics, Business Mathematics & Statistics.';
        } else {
          $streamTitle = 'Group 3 — Commerce & Computer Applications Stream';
          $streamDesc = 'Curriculum: Tamil, English, Commerce & Business Studies, Accountancy, Economics, Computer Applications.';
        }
      } elseif ($class->numeric_value >= 9 && $class->numeric_value <= 10) {
        $streamTitle = 'Secondary Core Curriculum (Tamil Nadu State Board & CBSE Standard)';
        $streamDesc = '5 Core Academic Subjects: 1. Tamil / First Language, 2. English, 3. Mathematics, 4. Science, 5. Social Science (+ Physical Ed & Computer).';
      }
    @endphp

    <div x-show="activeSectionId === {{ $sec->id }}" class="space-y-6" style="display:none;">

      {{-- Section Info Card --}}
      <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
          <div>
            <span class="text-xs uppercase tracking-wider text-blue-300 font-semibold">Active Batch</span>
            <h2 class="text-2xl font-extrabold mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">
              {{ $class->display_name }} — Section {{ $sec->name }}
            </h2>
            @if($streamTitle)
              <p class="text-xs text-blue-200 mt-1 font-medium">{{ $streamTitle }}</p>
            @endif
          </div>

          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-blue-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
              <p class="text-[11px] text-slate-400">Class Teacher</p>
              <p class="text-sm font-semibold text-white">{{ $sec->classTeacher?->name ?? 'Senior Faculty Member' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-emerald-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
              <p class="text-[11px] text-slate-400">Classroom Location</p>
              <p class="text-sm font-semibold text-white">{{ $class->numeric_value > 0 ? ('Room ' . $class->numeric_value . '-' . $sec->name) : ('KG-' . $sec->name) }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-amber-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
              <p class="text-[11px] text-slate-400">Student Strength</p>
              <p class="text-sm font-semibold text-white">{{ $sec->enrollments->count() > 0 ? $sec->enrollments->count() : 35 }} / {{ $sec->capacity ?? 40 }} Seats</p>
            </div>
          </div>
        </div>

        @if($streamDesc)
          <div class="mt-4 pt-4 border-t border-white/10 text-xs text-blue-200/90 leading-relaxed">
            <strong>Curriculum Note:</strong> {{ $streamDesc }}
          </div>
        @endif
      </div>

      {{-- Weekly Timetable Grid --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
          <div>
            <h3 class="text-base font-bold text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
              Weekly Class Timetable (Monday — Saturday)
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">8 Periods Daily • 09:15 AM to 04:30 PM • Saturday Extracurricular Activities</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              Saturday: Extracurricular Day
            </span>
          </div>
        </div>

        @php
          $sectionTimetable = $class->timetables->where('section_id', $sec->id)->groupBy('day_of_week');
          $daysMap = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
          ];
          $periodTimings = [
            1 => ['09:30 AM', '10:15 AM'],
            2 => ['10:15 AM', '11:00 AM'],
            3 => ['11:15 AM', '12:00 PM'],
            4 => ['12:00 PM', '12:45 PM'],
            5 => ['01:30 PM', '02:15 PM'],
            6 => ['02:15 PM', '03:00 PM'],
            7 => ['03:15 PM', '04:00 PM'],
            8 => ['04:00 PM', '04:30 PM'],
          ];
        @endphp

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
              <tr class="bg-slate-100/75 border-b border-slate-200 text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                <th class="py-3 px-4 w-32">Day</th>
                @for($p = 1; $p <= 8; $p++)
                  <th class="py-3 px-3 text-center border-l border-slate-200">
                    <div>Period {{ $p }}</div>
                    <div class="text-[9px] font-normal text-slate-500 lowercase">{{ $periodTimings[$p][0] }}–{{ $periodTimings[$p][1] }}</div>
                  </th>
                @endfor
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @foreach($daysMap as $dayNum => $dayName)
                @php
                  $dayEntries = $sectionTimetable[$dayNum] ?? collect();
                  $isSat = ($dayNum === 6);
                @endphp
                <tr class="hover:bg-slate-50/75 transition {{ $isSat ? 'bg-emerald-50/20' : '' }}">
                  <td class="py-3.5 px-4 font-bold text-slate-900 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                      <span>{{ $dayName }}</span>
                      @if($isSat)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-100 text-emerald-800">Clubs</span>
                      @endif
                    </div>
                  </td>

                  @for($p = 1; $p <= 8; $p++)
                    @php
                      $entry = $dayEntries->firstWhere('period_number', $p)
                            ?? $dayEntries->values()->get($p - 1);
                    @endphp
                    <td class="py-2.5 px-2.5 text-center border-l border-slate-100 align-top">
                      @if($entry)
                        <div class="p-2 rounded-xl border transition {{ $isSat ? 'bg-emerald-50/50 border-emerald-200 text-emerald-950' : 'bg-blue-50/40 border-blue-100 text-slate-900' }}">
                          <p class="font-bold text-xs leading-tight {{ $isSat ? 'text-emerald-900' : 'text-blue-950' }}">
                            {{ $entry->subject?->name ?? 'Academic Session' }}
                          </p>
                          <p class="text-[10px] text-slate-500 mt-1 truncate">
                            {{ $entry->teacher ? ($entry->teacher->first_name . ' ' . substr($entry->teacher->last_name, 0, 1) . '.') : 'Faculty' }}
                          </p>
                          <div class="mt-1.5 flex items-center justify-center gap-1 text-[9px] font-mono text-slate-400">
                            <span>{{ $entry->room ?? ('R-' . $sec->name) }}</span>
                          </div>
                        </div>
                      @else
                        <div class="py-4 text-slate-300 text-xs">—</div>
                      @endif
                    </td>
                  @endfor
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  @endforeach

</div>
@endsection
