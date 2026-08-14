@extends('layouts.admin')

@section('title', 'Classes & Timetables')

@section('content')
<div class="space-y-6" x-data="classesDashboard()" x-init="init()">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
    <div>
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Classes & Timetables
        </h1>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Academic Year {{ $currentYear->name ?? '2025-2026' }}
        </span>
      </div>
      <p class="text-xs text-slate-500 mt-1">
        Manage class sections, faculty assignments, substitutions, and live daily period schedules.
      </p>
    </div>

    {{-- Top Right Actions --}}
    <div class="flex items-center gap-2.5 self-end sm:self-auto flex-shrink-0">
      <button @click="showTimeSimulator = !showTimeSimulator"
              class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-bold border transition cursor-pointer"
              :class="showTimeSimulator || simulatedTime !== null
                ? 'bg-amber-500 hover:bg-amber-600 text-white border-amber-600 shadow-sm'
                : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 shadow-2xs'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="simulatedTime !== null ? 'Simulating: ' + displayClock : 'Time Simulator'"></span>
      </button>

      <button @click="openEditTimetableModal()"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md transition cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <span>Edit Timetable</span>
      </button>
    </div>
  </div>

  {{-- Interactive Time Simulator Panel (Collapsible) --}}
  <div x-show="showTimeSimulator"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 -translate-y-2"
       class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-indigo-500/10 border border-amber-300/80 rounded-2xl p-4 shadow-sm"
       style="display:none;">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Live Time Simulator</h4>
            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold"
                  :class="simulatedTime === null ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-200 text-amber-900 border border-amber-300'"
                  x-text="simulatedTime === null ? 'REAL SYSTEM CLOCK (' + liveTimeString + ')' : 'SIMULATING: ' + displayClock + (simulatedDayOfWeek === 6 ? ' (Saturday)' : '')">
            </span>
          </div>
          <p class="text-[11px] text-slate-600 mt-0.5">Click any period preset below to simulate live schedule transitions across all 48 class sections.</p>
        </div>
      </div>

      {{-- Presets --}}
      <div class="flex flex-wrap items-center gap-1.5">
        <button @click="resetToLiveTime()"
                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shadow-sm cursor-pointer"
                :class="simulatedTime === null ? 'bg-emerald-600 text-white ring-2 ring-emerald-400' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'">
          🔴 Live Time
        </button>
        <button @click="setTime('09:20', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '09:20' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          09:20 AM (P1)
        </button>
        <button @click="setTime('10:15', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '10:15' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          10:15 AM (P2)
        </button>
        <button @click="setTime('10:50', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '10:50' ? 'bg-amber-600 text-white font-black shadow-sm' : 'bg-amber-100 text-amber-900 hover:bg-amber-200 border border-amber-300'">
          10:50 AM (Morning Break)
        </button>
        <button @click="setTime('11:20', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '11:20' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          11:20 AM (P3)
        </button>
        <button @click="setTime('12:00', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '12:00' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          12:00 PM (P4)
        </button>
        <button @click="setTime('12:45', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '12:45' ? 'bg-amber-600 text-white font-black shadow-sm' : 'bg-amber-100 text-amber-900 hover:bg-amber-200 border border-amber-300'">
          12:45 PM (Lunch)
        </button>
        <button @click="setTime('13:30', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '13:30' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          01:30 PM (P5)
        </button>
        <button @click="setTime('14:15', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '14:15' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          02:15 PM (P6)
        </button>
        <button @click="setTime('14:50', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '14:50' ? 'bg-amber-600 text-white font-black shadow-sm' : 'bg-amber-100 text-amber-900 hover:bg-amber-200 border border-amber-300'">
          02:50 PM (Short Break)
        </button>
        <button @click="setTime('15:20', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '15:20' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          03:20 PM (P7)
        </button>
        <button @click="setTime('16:00', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '16:00' ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200'">
          04:00 PM (P8)
        </button>
        <button @click="setTime('16:45', 1)"
                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
                :class="simulatedTime === '16:45' ? 'bg-slate-700 text-white font-black shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300'">
          04:45 PM (After School)
        </button>
        <button @click="setTime('11:30', 6)"
                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shadow-sm cursor-pointer"
                :class="simulatedTime === '11:30' && simulatedDayOfWeek === 6 ? 'bg-emerald-700 text-white font-black ring-2 ring-emerald-400' : 'bg-emerald-600 text-white hover:bg-emerald-700'">
          🏆 Saturday
        </button>
      </div>
    </div>
  </div>

  {{-- ── 1. Top KPI Summary Cards (Styled exactly like user screenshot) ── --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

    {{-- Card 1: Total Standards --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-blue-glow">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">12</div>
        <div class="text-xs text-slate-500 font-medium mt-0.5">Total Standards (1st–12th)</div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-500 font-medium">1st – 12th Standard</span>
        <span class="text-blue-600 font-bold">Active ✓</span>
      </div>
    </div>

    {{-- Card 2: Total Sections --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $totalSections }}</div>
        <div class="text-xs text-slate-500 font-medium mt-0.5">Active Sections (ABCD)</div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-500 font-medium">Sections A, B, C, D</span>
        <span class="text-emerald-600 font-bold">4 per std ✓</span>
      </div>
    </div>

    {{-- Card 3: Total Enrolled Students --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <div class="w-11 h-11 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-sm">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">Capacity</span>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ number_format($totalStudents) }}</div>
        <div class="text-xs text-slate-500 font-medium mt-0.5">Enrolled Students</div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-purple-600 font-semibold hover:text-purple-700 cursor-pointer">
        <a href="{{ route('students.index') }}">Student Directory →</a>
      </div>
    </div>

    {{-- Card 4: Campus Period Live Status --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <div class="w-11 h-11 rounded-xl bg-cyan-600 text-white flex items-center justify-center shadow-sm">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-ping"></span>
          <span x-text="currentPeriod.badge_title">Live Now</span>
        </span>
      </div>
      <div class="mt-4">
        <div class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight truncate" x-text="currentPeriod.title">Period In Session</div>
        <div class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1.5">
          <span x-text="currentPeriod.timing">09:15 AM – 10:00 AM</span>
          <span class="opacity-70">•</span>
          <span class="text-cyan-700 font-semibold" x-text="currentPeriod.remaining_text">45m left</span>
        </div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-medium">
        <span class="text-slate-500" x-text="currentPeriod.day_type">Regular Day</span>
        <span class="text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-lg font-mono font-bold" x-text="displayClock"></span>
      </div>
    </div>

    {{-- Card 5: Timetable Coverage / Saturday Tracker --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Full Week</span>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">100%</div>
        <div class="text-xs text-slate-500 font-medium mt-0.5">Coverage (Mon–Sat)</div>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-500 font-medium">All periods assigned</span>
        <span class="text-emerald-600 font-bold">Mon–Sat ✓</span>
      </div>
    </div>

  </div>

  {{-- ── 2. Filter Bar & Search (Search at Top, Rectangle Cards in Line Below) ── --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
    
    {{-- Search Input at Top with Clear Icon Spacing --}}
    <div class="relative w-full flex items-center">
      <div class="absolute flex items-center pointer-events-none text-slate-400 z-10" style="left: 20px;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>
      <input type="text"
             x-model="searchQuery"
             placeholder="Search standard, section, subject, or class teacher..."
             style="padding-left: 52px !important; padding-right: 40px !important;"
             class="w-full h-11 bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs">
      <template x-if="searchQuery">
        <button @click="searchQuery = ''" class="absolute right-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer p-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </template>
    </div>

    {{-- Grade Category Cards in a Line Below --}}
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 no-scrollbar flex-wrap">
      <button @click="filterCategory = 'all'"
              :class="filterCategory === 'all'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex-shrink-0">
        All Standards (1st–12th)
      </button>

      <button @click="filterCategory = 'primary'"
              :class="filterCategory === 'primary'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex-shrink-0">
        Primary (1st–5th)
      </button>

      <button @click="filterCategory = 'middle'"
              :class="filterCategory === 'middle'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex-shrink-0">
        Middle School (6th–8th)
      </button>

      <button @click="filterCategory = 'secondary'"
              :class="filterCategory === 'secondary'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex items-center gap-1.5 flex-shrink-0">
        <span>Secondary (9th & 10th)</span>
        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
      </button>

      <button @click="filterCategory = 'higher-secondary'"
              :class="filterCategory === 'higher-secondary'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex items-center gap-1.5 flex-shrink-0">
        <span>Higher Secondary (11th & 12th)</span>
        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
      </button>

      <button @click="filterCategory = 'pre-primary'"
              :class="filterCategory === 'pre-primary'
                ? 'bg-blue-600 text-white font-bold shadow-xs'
                : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200'"
              class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition cursor-pointer flex-shrink-0">
        Pre-Primary (KG)
      </button>
    </div>

  </div>

@push('head')
<style>
.class-badge-primary { background: linear-gradient(135deg, #059669, #0d9488) !important; color: #ffffff !important; }
.class-badge-middle { background: linear-gradient(135deg, #4f46e5, #2563eb) !important; color: #ffffff !important; }
.class-badge-secondary { background: linear-gradient(135deg, #0284c7, #1d4ed8) !important; color: #ffffff !important; }
.class-badge-higher { background: linear-gradient(135deg, #7c3aed, #4338ca) !important; color: #ffffff !important; }
.class-badge-pre { background: linear-gradient(135deg, #ea580c, #d97706) !important; color: #ffffff !important; }

.live-card-active { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%) !important; color: #ffffff !important; border: 1px solid rgba(99, 102, 241, 0.5) !important; }
.live-card-saturday { background: linear-gradient(135deg, #064e3b 0%, #042f2e 100%) !important; color: #ffffff !important; border: 1px solid rgba(16, 185, 129, 0.5) !important; }
.live-card-break { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; color: #ffffff !important; border: 1px solid rgba(148, 163, 184, 0.3) !important; }
.live-card-idle { background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important; color: #ffffff !important; border: 1px solid #475569 !important; }
</style>
@endpush

  {{-- ── 3. Ultra-Premium Class Cards Grid (1st to 12th Standard) ── --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    <template x-for="cls in filteredClasses" :key="cls.id">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group hover:border-blue-400"
           x-data="{
             selectedSectionName: 'A',
             get currentSection() {
               return cls.sections.find(s => s.name === this.selectedSectionName) || cls.sections[0] || {};
             },
             get livePeriodForSection() {
               return $data.getLivePeriodForSection(cls, this.currentSection);
             }
           }">

        {{-- Card Header --}}
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-blue-50/40">
          <div class="flex items-start justify-between gap-3">
            {{-- Class Badge & Title --}}
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-white text-base shadow-md transition duration-300 group-hover:scale-105 flex-shrink-0"
                   :class="cls.numeric_value >= 11 ? 'class-badge-higher' :
                          (cls.numeric_value >= 9 ? 'class-badge-secondary' :
                          (cls.numeric_value >= 6 ? 'class-badge-middle' :
                          (cls.numeric_value >= 1 ? 'class-badge-primary' : 'class-badge-pre')))">
                <span class="text-white font-black text-base" x-text="cls.name || cls.numeric_value"></span>
              </div>
              <div>
                <h3 class="text-base font-extrabold text-slate-900 group-hover:text-blue-600 transition tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;"
                    x-text="cls.display_name"></h3>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[11px] font-semibold text-slate-500" x-text="'Standard ' + cls.name"></span>
                  <span class="text-slate-300">•</span>
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
                        :class="cls.category === 'higher-secondary' ? 'bg-purple-100 text-purple-800' :
                               (cls.category === 'secondary' ? 'bg-blue-100 text-blue-800' :
                               (cls.category === 'middle' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800'))"
                        x-text="cls.category.replace('-', ' ')">
                  </span>
                </div>
              </div>
            </div>

            {{-- Students Count --}}
            <div class="text-right">
              <span class="text-xs font-extrabold text-slate-900" x-text="cls.total_students + ' Students'"></span>
              <p class="text-[10px] text-slate-400">4 Sections (A–D)</p>
            </div>
          </div>

          {{-- Section Selection Cards (A, B, C, D) --}}
          <div class="mt-3.5 pt-3 border-t border-slate-100/90 flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5">
              <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1">Section:</span>
              <template x-for="sec in (cls.sections || [{name:'A'},{name:'B'},{name:'C'},{name:'D'}])" :key="sec.id || sec.name">
                <button @click="selectedSectionName = sec.name"
                        type="button"
                        class="w-8 h-8 rounded-xl font-extrabold text-xs transition-all duration-200 flex items-center justify-center border shadow-sm cursor-pointer"
                        :class="selectedSectionName === sec.name
                          ? 'bg-blue-600 text-white border-blue-600 shadow-blue-500/30 ring-2 ring-blue-400 scale-105 font-black'
                          : 'bg-slate-50 text-slate-700 border-slate-200/90 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700'">
                  <span x-text="sec.name"></span>
                </button>
              </template>
            </div>
            
            {{-- Selected Section Student Count Badge --}}
            <div class="text-right">
              <span class="text-[11px] font-extrabold text-blue-700 bg-blue-50/90 px-2.5 py-1 rounded-xl border border-blue-200/60 inline-flex items-center gap-1.5 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                <span x-text="'Sec ' + currentSection.name + ': ' + (currentSection.student_count || 40) + ' Students'"></span>
              </span>
            </div>
          </div>

          {{-- Class Teacher Line --}}
          <div class="mt-2.5 pt-2.5 border-t border-slate-100/80 flex items-center justify-between text-xs">
            <span class="text-slate-500 font-medium flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Class Teacher (Sec <span x-text="currentSection.name"></span>):
            </span>
            <span class="font-extrabold text-slate-900 bg-slate-100/80 px-2 py-0.5 rounded-md" x-text="currentSection.class_teacher || 'Senior Faculty'"></span>
          </div>
        </div>

        {{-- Card Body --}}
        <div class="p-5 space-y-4">

          {{-- Live Period Glow Box --}}
          <div class="rounded-2xl p-4 transition-all duration-300 shadow-md relative overflow-hidden"
               :class="livePeriodForSection.is_active_period
                 ? (livePeriodForSection.is_saturday ? 'live-card-saturday' : 'live-card-active')
                 : (livePeriodForSection.is_break ? 'live-card-break' : 'live-card-idle')">

            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                        :class="livePeriodForSection.is_active_period ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5"
                        :class="livePeriodForSection.is_active_period ? 'bg-emerald-500' : 'bg-slate-500'"></span>
                </span>
                <span class="text-[11px] font-extrabold uppercase tracking-wider"
                      :style="livePeriodForSection.is_active_period ? 'color: #34d399 !important;' : 'color: #94a3b8 !important;'"
                      x-text="livePeriodForSection.badge_text">LIVE NOW</span>
              </div>
              <span class="text-[11px] font-mono font-bold" style="color: #e2e8f0 !important;" x-text="livePeriodForSection.timing">09:15 AM – 10:00 AM</span>
            </div>

            <div class="mt-2.5">
              <h4 class="text-lg font-black tracking-tight truncate" style="color: #ffffff !important;" x-text="livePeriodForSection.subject_name">Mathematics</h4>
              <div class="flex items-center justify-between text-xs mt-1" style="color: #cbd5e1 !important;">
                <span class="truncate font-medium" x-text="livePeriodForSection.teacher_name">Faculty In-charge</span>
                <span class="font-mono text-[11px] px-2 py-0.5 rounded font-bold" style="background: rgba(255,255,255,0.18); color: #ffffff !important;" x-text="currentSection.room">Room 1-A</span>
              </div>
            </div>

            {{-- Progress Bar - only for live periods --}}
            <template x-if="livePeriodForSection.is_active_period && livePeriodForSection.progress_percent !== undefined">
              <div class="mt-3">
                <div class="w-full rounded-full h-1.5 overflow-hidden" style="background: rgba(255,255,255,0.25);">
                  <div class="h-1.5 rounded-full transition-all duration-500"
                       style="background: linear-gradient(90deg, #34d399, #38bdf8);"
                       :style="'width: ' + livePeriodForSection.progress_percent + '%'"></div>
                </div>
                <div class="flex justify-between text-[10px] mt-1 font-mono" style="color: #cbd5e1 !important;">
                  <span x-text="livePeriodForSection.remaining_mins + ' mins remaining'"></span>
                  <span x-text="livePeriodForSection.progress_percent + '% elapsed'"></span>
                </div>
              </div>
            </template>

            {{-- Upcoming Period Indicator (Without 'Next:' label) --}}
            <template x-if="livePeriodForSection.next_period_text">
              <div class="mt-2.5 pt-2.5 text-[11px] flex items-center gap-1.5 font-medium" style="border-top: 1px solid rgba(255,255,255,0.15); color: #cbd5e1 !important;">
                <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                <span class="truncate font-semibold text-white" x-text="livePeriodForSection.next_period_text"></span>
              </div>
            </template>
          </div>

          {{-- Today's 8-Period Preview --}}
          <div>
            <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1.5">
              <span>Today's Schedule (Periods 1 to 8):</span>
              <span class="text-blue-600 font-semibold" x-text="simulatedDayOfWeek === 6 ? 'Saturday' : 'Regular'"></span>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5">
              <template x-for="slot in getTodayPeriods(cls, currentSection)" :key="slot.period">
                <div class="p-1.5 rounded-xl text-center border transition"
                     :class="slot.is_current
                       ? 'bg-blue-600 text-white border-blue-700 shadow-sm ring-2 ring-blue-400 ring-offset-1 font-extrabold'
                       : 'bg-slate-50 text-slate-700 border-slate-200 font-semibold'"
                     :title="slot.full_title">
                  <div class="text-[9px] font-bold opacity-80" x-text="'P' + slot.period"></div>
                  <div class="text-[10px] truncate mt-0.5" x-text="slot.abbr"></div>
                </div>
              </template>
            </div>
          </div>
        </div>

        {{-- Card Footer --}}
        <div class="p-4 bg-slate-50/80 border-t border-slate-100 flex items-center gap-2">
          <button @click="openTimetableModal(cls, currentSection)"
                  class="flex-1 inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            View Timetable
          </button>
          <button @click="openEditTimetableModal(cls, currentSection)"
                  class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 hover:border-slate-300 shadow-2xs transition cursor-pointer"
                  title="Edit Timetable for this Class & Section">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Edit</span>
          </button>
        </div>
      </div>
    </template>
  </div>

  <template x-teleport="body">
    <div x-show="modalOpen"
         @keydown.window.escape="modalOpen = false"
         @click.self="modalOpen = false"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="display:none; padding:28px 20px; box-sizing:border-box; background:rgba(15, 23, 42, 0.65);"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-98"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-98">

      {{-- Modal panel --}}
      <div class="bg-white"
           style="max-width:1200px; max-height:calc(100vh - 56px); min-height:0; overflow:hidden; display:flex; flex-direction:column; width:100%; border-radius:20px; box-shadow:0 25px 60px -15px rgba(0,0,0,0.3), 0 0 0 1px rgba(0,0,0,0.06);">

        {{-- ── 1. CLEAN MODERN LIGHT HEADER ── --}}
        <div class="px-6 py-3.5 flex-shrink-0 flex items-center justify-between gap-4 bg-white border-b border-slate-200/80">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 font-black text-sm tracking-wide"
                 x-text="modalClass?.name || 'Class'">
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-extrabold text-slate-900 tracking-tight"
                    x-text="modalClass?.display_name || ''"></h3>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60 uppercase tracking-wide">
                  Weekly Timetable
                </span>
              </div>
              <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                8 Periods Daily &nbsp;•&nbsp; 09:15 AM – 04:30 PM
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            {{-- Edit Timetable shortcut --}}
            <button @click="modalOpen = false; openEditTimetableModal(modalClass, modalSection)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 shadow-2xs transition cursor-pointer"
                    title="Edit Timetable for this Class & Section">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              <span>Edit</span>
            </button>
            {{-- PDF --}}
            <a :href="'{{ url('classes') }}/' + (modalClass?.id || '') + '/timetable/pdf?section_id=' + (modalSection?.id || '')"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-xs transition">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              PDF
            </a>
            {{-- ✕ Close Button --}}
            <button @click="modalOpen = false"
                    class="w-8 h-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition shadow-md hover:scale-105 cursor-pointer"
                    title="Close (Esc)">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        {{-- ── 2. SECTION & TEACHER BAR (CLEAN LIGHT THEME) ── --}}
        <div class="px-6 py-2.5 flex-shrink-0 flex items-center justify-between flex-wrap gap-3 bg-slate-50/90 border-b border-slate-200/80">
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Section</span>
            <template x-for="sec in (modalClass?.sections || [])" :key="sec.id">
              <button @click="modalSection = sec"
                      class="px-3.5 py-1 rounded-lg text-xs font-bold transition-all"
                      :class="modalSection?.id === sec.id
                        ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25'
                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-900'">
                <span x-text="'Sec ' + sec.name"></span>
              </button>
            </template>
          </div>
          <div class="text-xs text-slate-600 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-slate-400 font-medium">Class Teacher:</span>
            <strong class="text-slate-800 font-bold" x-text="modalSection?.class_teacher"></strong>
            <span class="text-slate-300">•</span>
            <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 font-mono text-[11px] font-semibold border border-blue-200/60" x-text="modalSection?.room"></span>
          </div>
        </div>

        {{-- ── 3. DAY TABS BAR ── --}}
        <div class="px-6 py-2 flex-shrink-0 flex items-center gap-1.5 flex-wrap bg-white border-b border-slate-200">
          <button @click="modalDay = 0"
                  :class="modalDay === 0 ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                  class="px-3.5 py-1 rounded-full text-xs font-semibold transition">All Week</button>
          <template x-for="[num, label] in [[1,'Mon'],[2,'Tue'],[3,'Wed'],[4,'Thu'],[5,'Fri']]" :key="num">
            <button @click="modalDay = num"
                    :class="modalDay === num ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                    class="px-3.5 py-1 rounded-full text-xs font-semibold transition"
                    x-text="label"></button>
          </template>
          <button @click="modalDay = 6"
                  :class="modalDay === 6 ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/80 hover:bg-emerald-100'"
                  class="px-3.5 py-1 rounded-full text-xs font-semibold transition flex items-center gap-1.5">
            Saturday <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full" :class="modalDay === 6 ? 'bg-emerald-700 text-white' : 'bg-emerald-200 text-emerald-900'">CLUBS</span>
          </button>
        </div>

        {{-- ── 4. FIXED PERIOD HEADERS (DEDICATED ROW - NO OVERLAPPING!) ── --}}
        <div class="px-6 pt-3 pb-2 bg-slate-50/80 border-b border-slate-200/80 flex-shrink-0">
          <div style="min-width: 900px; display: grid; grid-template-columns: 92px repeat(8, minmax(0, 1fr)); gap: 10px; align-items: center;">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 text-center">
              Day
            </div>
            <template x-for="[p, times] in [
              [1,'09:15–10:00'],
              [2,'10:00–10:45'],
              [3,'11:00–11:45'],
              [4,'11:45–12:30'],
              [5,'01:15–02:00'],
              [6,'02:00–02:45'],
              [7,'03:00–03:45'],
              [8,'03:45–04:30']
            ]" :key="p">
              <div class="bg-white border border-slate-200/90 rounded-xl py-2 px-1 text-center shadow-xs flex flex-col items-center justify-center">
                <span class="text-xs font-black text-blue-600 tracking-tight" x-text="'Period ' + p"></span>
                <span class="text-[9.5px] text-slate-400 font-medium tracking-tight mt-0.5" x-text="times"></span>
              </div>
            </template>
          </div>
        </div>

        {{-- ── 5. SCROLLABLE TIMETABLE CARDS (SPACIOUS & CLEAN) ── --}}
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30" style="min-height:0;">
          <div style="min-width: 900px; display: flex; flex-direction: column; gap: 10px;">
            <template x-for="d in getModalDays()" :key="d.dayNum">
              <div style="display: grid; grid-template-columns: 92px repeat(8, minmax(0, 1fr)); gap: 10px; align-items: stretch;">
                
                {{-- Day Title Card --}}
                <div class="rounded-xl border flex flex-col items-center justify-center text-center p-2.5 shadow-xs"
                     style="height: 86px;"
                     :class="isModalDayHoliday(d.dayNum) ? 'bg-amber-50 border-amber-300 text-amber-900' : (d.dayNum === 6 ? 'bg-emerald-50/90 border-emerald-200 text-emerald-950' : 'bg-white border-slate-200 text-slate-800')">
                  <span class="font-extrabold text-xs tracking-tight uppercase" x-text="d.name"></span>
                  <template x-if="isModalDayHoliday(d.dayNum)">
                    <span class="text-[8.5px] font-black bg-amber-200 text-amber-900 px-2 py-0.5 rounded-full mt-1.5 tracking-wider uppercase">HOLIDAY</span>
                  </template>
                  <template x-if="!isModalDayHoliday(d.dayNum) && d.dayNum === 6">
                    <span class="text-[8.5px] font-black bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded-full mt-1.5 tracking-wider uppercase">CLUBS</span>
                  </template>
                </div>

                {{-- Full-row Holiday Banner if Day is Holiday in View Modal --}}
                <template x-if="isModalDayHoliday(d.dayNum)">
                  <div class="rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 px-6 py-3 flex items-center justify-between shadow-xs"
                       style="grid-column: 2 / span 8; height: 86px; min-width: 0;">
                    <div class="flex items-center gap-3.5">
                      <div class="w-10 h-10 rounded-xl bg-amber-200/90 text-amber-900 flex items-center justify-center font-bold text-xl shadow-xs flex-shrink-0">
                        🏖️
                      </div>
                      <div>
                        <div class="flex items-center gap-2">
                          <h4 class="font-extrabold text-sm text-amber-950" x-text="d.name + ' — School Holiday'"></h4>
                          <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-200/70 text-amber-900 uppercase tracking-wider">No Classes</span>
                        </div>
                        <p class="text-xs text-amber-800/80 mt-0.5 font-medium">Entire day marked as holiday. All 8 academic periods suspended.</p>
                      </div>
                    </div>
                  </div>
                </template>

                {{-- 8 Period Cards when not holiday --}}
                <template x-if="!isModalDayHoliday(d.dayNum)">
                  <template x-for="p in [1,2,3,4,5,6,7,8]" :key="p">
                    <div class="rounded-xl border transition-all p-3 flex flex-col justify-between overflow-hidden"
                         style="height: 86px; max-height: 86px;"
                         :class="d.dayNum === 6
                           ? 'bg-emerald-50/40 hover:bg-emerald-50 border-emerald-200 hover:border-emerald-300 hover:shadow-sm'
                           : (getPeriodEntry(d.dayNum, p)?.subject_name
                               ? 'bg-white hover:bg-blue-50/30 border-slate-200/90 hover:border-blue-300 shadow-xs hover:shadow-md'
                               : 'bg-slate-100/40 border-dashed border-slate-200 items-center justify-center')">

                      <template x-if="getPeriodEntry(d.dayNum, p)?.subject_name">
                        <div class="flex flex-col justify-between h-full w-full">
                          <div>
                            <h4 class="font-extrabold text-xs leading-snug truncate"
                                :class="d.dayNum === 6 ? 'text-emerald-950' : 'text-slate-900'"
                                x-text="getPeriodEntry(d.dayNum, p)?.subject_name"
                                :title="getPeriodEntry(d.dayNum, p)?.subject_name"></h4>
                          </div>
                          <div class="mt-auto pt-1 flex items-center justify-between">
                            <p class="text-[10.5px] text-slate-500 truncate font-medium"
                               x-text="getPeriodEntry(d.dayNum, p)?.teacher_name || 'Faculty'"
                               :title="getPeriodEntry(d.dayNum, p)?.teacher_name"></p>
                            <template x-if="getPeriodEntry(d.dayNum, p)?.period_type === 'substitution'">
                              <span class="text-[8.5px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 flex-shrink-0">
                                🔄 Sub
                              </span>
                            </template>
                          </div>
                        </div>
                      </template>

                      <template x-if="!getPeriodEntry(d.dayNum, p)?.subject_name">
                        <span class="text-[11px] text-slate-300 font-medium italic">Free Period</span>
                      </template>
                    </div>
                  </template>
                </template>

              </div>
            </template>
          </div>
        </div>

        {{-- ── 6. FOOTER ── --}}
        <div class="px-6 py-2.5 flex-shrink-0 flex items-center justify-between bg-white border-t border-slate-200">
          <p class="text-[11px] text-slate-500 font-medium">🔔 &nbsp;09:15 Start · 10:45 Break · 12:30 Lunch · 02:45 Break · 04:30 Dispersal</p>
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">DASA EduERP</p>
        </div>

      </div>
    </div>
  </template>

  {{-- ── 5. Full Interactive "Edit Timetable" Modal (Teleported to Body) ── --}}
  <template x-teleport="body">
    <div x-show="editModalOpen"
         @keydown.window.escape="editModalOpen = false"
         @click.self="editModalOpen = false"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="display:none; padding:24px 16px; box-sizing:border-box; background:rgba(15, 23, 42, 0.7);"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-98"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-98">

      {{-- Modal Panel --}}
      <div class="bg-white"
           style="max-width:1220px; max-height:calc(100vh - 48px); min-height:0; overflow:hidden; display:flex; flex-direction:column; width:100%; border-radius:20px; box-shadow:0 25px 60px -15px rgba(0,0,0,0.35), 0 0 0 1px rgba(0,0,0,0.06);">

        {{-- ── Top Header ── --}}
        <div class="px-6 py-3.5 flex-shrink-0 flex items-center justify-between gap-4 bg-white border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm font-bold text-base">
              ✏️
            </div>
            <div class="flex items-center gap-2.5">
              <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Edit Timetable</h3>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200/70 uppercase tracking-wider">
                Live Editor
              </span>
            </div>
          </div>

          <button @click="editModalOpen = false"
                  class="w-8 h-8 rounded-full bg-slate-100 hover:bg-red-500 hover:text-white text-slate-500 flex items-center justify-center transition shadow-xs cursor-pointer"
                  title="Close (Esc)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        {{-- ── Filter & Action Bar (Neat, Aligned Order) ── --}}
        <div class="px-6 py-3 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between flex-wrap gap-4 flex-shrink-0">
          <div class="flex items-center gap-3 flex-wrap">
            {{-- Class Selector --}}
            <div class="flex items-center gap-2">
              <label class="text-xs font-bold text-slate-600">Class:</label>
              <select x-model="editSelectedClassId"
                      @change="onEditClassChange()"
                      class="h-9 pl-3 pr-8 min-w-[135px] bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition cursor-pointer">
                <template x-for="c in classesData" :key="c.id">
                  <option :value="c.id" x-text="c.display_name"></option>
                </template>
              </select>
            </div>

            {{-- Section Selector --}}
            <div class="flex items-center gap-2">
              <label class="text-xs font-bold text-slate-600">Section:</label>
              <select x-model="editSelectedSectionId"
                      @change="onEditSectionChange()"
                      class="h-9 pl-3 pr-8 min-w-[125px] bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition cursor-pointer">
                <template x-for="s in (editClass?.sections || [])" :key="s.id">
                  <option :value="s.id" x-text="'Section ' + s.name"></option>
                </template>
              </select>
            </div>

            {{-- Search / Load Button --}}
            <button @click="loadEditTimetable()"
                    class="h-9 inline-flex items-center gap-1.5 px-3.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition cursor-pointer">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <span>Search & Load</span>
            </button>

            {{-- Day Holiday Action (No vertical divider line) --}}
            <div class="flex items-center gap-2">
              <label class="text-xs font-bold text-slate-600">Day:</label>
              <select x-model.number="selectedHolidayDay"
                      class="h-9 pl-3 pr-8 min-w-[115px] bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-xs cursor-pointer">
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
              </select>

              <button @click="toggleDayHoliday(selectedHolidayDay)"
                      class="h-9 inline-flex items-center gap-1.5 px-3.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs cursor-pointer"
                      :disabled="isDayHoliday(selectedHolidayDay)"
                      :class="isDayHoliday(selectedHolidayDay) ? 'opacity-60 cursor-not-allowed bg-slate-400' : ''">
                <span>🏖️ Mark as Holiday</span>
              </button>
            </div>
          </div>

          {{-- Active Class & Teacher Display Card --}}
          <div class="text-right">
            <div class="text-xs font-extrabold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200/60"
                 x-text="(editClass?.display_name || 'Class') + ' — Section ' + (editSection?.name || 'A')"></div>
            <div class="text-[11px] text-slate-500 mt-0.5 font-medium">
              Teacher: <strong class="text-slate-700" x-text="editSection?.class_teacher || 'Assigned Teacher'"></strong>
              &nbsp;•&nbsp; Room: <strong class="text-slate-700" x-text="editSection?.room || 'Room'"></strong>
            </div>
          </div>
        </div>

        {{-- ── Day Filter Bar ── --}}
        <div class="px-6 py-2 bg-white border-b border-slate-200 flex items-center justify-between flex-wrap gap-2 flex-shrink-0">
          <div class="flex items-center gap-1.5 flex-wrap">
            <button @click="editDay = 0"
                    :class="editDay === 0 ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer">All Week</button>
            <template x-for="[num, label] in [[1,'Mon'],[2,'Tue'],[3,'Wed'],[4,'Thu'],[5,'Fri'],[6,'Sat']]" :key="num">
              <button @click="editDay = num"
                      :class="editDay === num ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                      class="px-3.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer"
                      x-text="label"></button>
            </template>
          </div>
          <div class="text-[11px] text-slate-400 font-medium">
            💡 Click on any period card below to change subject, teacher, or assign substitution.
          </div>
        </div>

        {{-- ── Fixed Period Headers (Period 1 to 8 Timings) ── --}}
        <div class="px-6 pt-3 pb-2 bg-slate-50/80 border-b border-slate-200/80 flex-shrink-0">
          <div style="min-width: 900px; display: grid; grid-template-columns: 100px repeat(8, minmax(0, 1fr)); gap: 10px; align-items: center;">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 text-center">
              Day / Status
            </div>
            <template x-for="[p, times] in [
              [1,'09:15–10:00'],
              [2,'10:00–10:45'],
              [3,'11:00–11:45'],
              [4,'11:45–12:30'],
              [5,'01:15–02:00'],
              [6,'02:00–02:45'],
              [7,'03:00–03:45'],
              [8,'03:45–04:30']
            ]" :key="p">
              <div class="bg-white border border-slate-200/90 rounded-xl py-2 px-1 text-center shadow-xs flex flex-col items-center justify-center">
                <span class="text-xs font-black text-blue-600 tracking-tight" x-text="'Period ' + p"></span>
                <span class="text-[9.5px] text-slate-400 font-medium tracking-tight mt-0.5" x-text="times"></span>
              </div>
            </template>
          </div>
        </div>

        {{-- ── Scrollable Editable Timetable Cards Grid ── --}}
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30" style="min-height:0;">
          <div style="min-width: 900px; display: flex; flex-direction: column; gap: 12px;">
            <template x-for="d in getEditDays()" :key="d.dayNum">
              <div style="display: grid; grid-template-columns: 100px repeat(8, minmax(0, 1fr)); gap: 10px; align-items: stretch;">
                
                {{-- Day Card with Holiday Toggle & Undo --}}
                <div class="rounded-xl border flex flex-col items-center justify-between text-center p-2.5 shadow-xs"
                     style="height: 86px;"
                     :class="isDayHoliday(d.dayNum) ? 'bg-amber-50 border-amber-300 text-amber-900' : (d.dayNum === 6 ? 'bg-emerald-50/90 border-emerald-200 text-emerald-950' : 'bg-white border-slate-200 text-slate-800')">
                  <span class="font-extrabold text-xs tracking-tight uppercase" x-text="d.name"></span>
                  
                  <template x-if="!isDayHoliday(d.dayNum)">
                    <button @click.stop="toggleDayHoliday(d.dayNum)"
                            class="text-[9.5px] font-bold px-2 py-0.5 rounded-md transition cursor-pointer bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800"
                            title="Mark entire day as holiday">
                      + Holiday
                    </button>
                  </template>
                  <template x-if="isDayHoliday(d.dayNum)">
                    <span class="text-[9.5px] font-extrabold px-1.5 py-0.5 rounded bg-amber-200/90 text-amber-900">
                      🏖️ Holiday
                    </span>
                  </template>
                </div>

                {{-- Full-row Holiday Banner if Day is Marked as Holiday (Explicit grid-column: 2 / span 8 across all 8 periods) --}}
                <template x-if="isDayHoliday(d.dayNum)">
                  <div class="rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 px-6 py-3 flex items-center justify-between shadow-xs"
                       style="grid-column: 2 / span 8; height: 86px; min-width: 0;">
                    <div class="flex items-center gap-3.5">
                      <div class="w-10 h-10 rounded-xl bg-amber-200/90 text-amber-900 flex items-center justify-center font-bold text-xl shadow-xs flex-shrink-0">
                        🏖️
                      </div>
                      <div>
                        <div class="flex items-center gap-2">
                          <h4 class="font-extrabold text-sm text-amber-950" x-text="d.name + ' — School Holiday'"></h4>
                          <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-200/70 text-amber-900 uppercase tracking-wider">No Classes</span>
                        </div>
                        <p class="text-xs text-amber-800/80 mt-0.5 font-medium">Entire day marked as holiday. All 8 academic periods suspended.</p>
                      </div>
                    </div>
                    <button @click.stop="toggleDayHoliday(d.dayNum)"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-amber-900 border border-amber-300 hover:bg-amber-100 hover:border-amber-400 shadow-sm transition-all cursor-pointer flex items-center gap-1.5 flex-shrink-0">
                      <span>↩️</span> Undo Holiday (Restore Classes)
                    </button>
                  </div>
                </template>

                {{-- 8 Interactive Clean Period Cards if Day is Active --}}
                <template x-if="!isDayHoliday(d.dayNum)">
                  <template x-for="p in [1,2,3,4,5,6,7,8]" :key="p">
                    <div @click.stop="openSlotEditor(d.dayNum, p)"
                         class="rounded-xl border transition-all p-2.5 flex flex-col justify-between overflow-hidden cursor-pointer group hover:shadow-md hover:border-blue-400 bg-white"
                         style="height: 86px; max-height: 86px;"
                         :class="getSlotCardClass(d.dayNum, p)">

                      <div class="flex flex-col justify-between h-full w-full">
                        {{-- Top line: Subject Name alone --}}
                        <div class="flex items-start justify-between gap-1">
                          <h4 class="font-extrabold text-xs leading-snug truncate"
                              :class="getSlotTextClass(d.dayNum, p)"
                              x-text="getPeriodEntryDisplay(d.dayNum, p)?.subject_name"
                              :title="getPeriodEntryDisplay(d.dayNum, p)?.subject_name"></h4>
                          <span class="opacity-0 group-hover:opacity-100 text-[10px] text-blue-600 bg-blue-50 px-1 py-0.5 rounded transition">✏️ Edit</span>
                        </div>

                        {{-- Bottom line: Teacher Name alone (or Free Period / Substitution tag) --}}
                        <div class="mt-auto pt-1 flex items-center justify-between">
                          <p class="text-[10.5px] text-slate-500 truncate font-medium"
                             x-text="getPeriodEntryDisplay(d.dayNum, p)?.teacher_name"
                             :title="getPeriodEntryDisplay(d.dayNum, p)?.teacher_name"></p>
                          
                          <template x-if="getPeriodEntryDisplay(d.dayNum, p)?.period_type === 'substitution'">
                            <span class="text-[8.5px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-purple-100 text-purple-800">
                              🔄 Sub
                            </span>
                          </template>
                          <template x-if="getPeriodEntryDisplay(d.dayNum, p)?.period_type === 'free'">
                            <span class="text-[8.5px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">
                              ⚡ Free
                            </span>
                          </template>
                        </div>
                      </div>
                    </div>
                  </template>
                </template>

              </div>
            </template>
          </div>
        </div>

        {{-- ── Footer with Save Button ── --}}
        <div class="px-6 py-3 flex-shrink-0 flex items-center justify-between bg-white border-t border-slate-200">
          <p class="text-[11px] text-slate-500 font-medium">🔔 &nbsp;09:15 Start · 10:45 Break · 12:30 Lunch · 02:45 Break · 04:30 Dispersal</p>
          <div class="flex items-center gap-3">
            <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
              Close
            </button>
            <button type="button" @click="saveAndApplyTimetable()" class="px-5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer">
              <span>💾</span> Save Timetable
            </button>
          </div>
        </div>

      </div>

      {{-- ── Slot Editor Sub-Modal Overlay (Directly inside Edit Modal) ── --}}
      <div x-show="editSlotModalOpen"
           class="fixed inset-0 z-[10010] flex items-center justify-center p-4 overflow-y-auto"
           style="display:none;"
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 scale-95"
           x-transition:enter-end="opacity-100 scale-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 scale-100"
           x-transition:leave-end="opacity-0 scale-95">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs" @click="editSlotModalOpen = false"></div>

        {{-- Dialog Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden z-10 my-auto"
             @click.stop>
          
          {{-- Editor Header --}}
          <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-blue-500 text-white text-[10px] font-black uppercase" x-text="'Period ' + editSlotData.period_number"></span>
                <h3 class="text-base font-extrabold text-white">Edit Period Slot</h3>
              </div>
              <p class="text-xs text-slate-300 mt-1" x-text="editSlotData.dayName + ' • ' + (editClass?.display_name || '') + ' (' + (editSection?.name || '') + ')'"></p>
            </div>
            <button @click="editSlotModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
              ✕
            </button>
          </div>

          {{-- Editor Form Body --}}
          <div class="p-6 space-y-4">
            {{-- Period Type Selector (Pills) --}}
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Period Type</label>
              <div class="grid grid-cols-4 gap-2">
                <button type="button" @click="editSlotData.period_type = 'class'"
                        class="px-2.5 py-2 rounded-xl text-xs font-bold border text-center transition cursor-pointer"
                        :class="editSlotData.period_type === 'class' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                  📚 Class
                </button>
                <button type="button" @click="editSlotData.period_type = 'free'"
                        class="px-2.5 py-2 rounded-xl text-xs font-bold border text-center transition cursor-pointer"
                        :class="editSlotData.period_type === 'free' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                  ⚡ Free
                </button>
                <button type="button" @click="editSlotData.period_type = 'substitution'"
                        class="px-2.5 py-2 rounded-xl text-xs font-bold border text-center transition cursor-pointer"
                        :class="editSlotData.period_type === 'substitution' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                  🔄 Sub
                </button>
                <button type="button" @click="editSlotData.period_type = 'activity'"
                        class="px-2.5 py-2 rounded-xl text-xs font-bold border text-center transition cursor-pointer"
                        :class="editSlotData.period_type === 'activity' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                  🎨 Club
                </button>
              </div>
            </div>

            {{-- Subject Selection --}}
            <div x-show="editSlotData.period_type !== 'free'">
              <label class="block text-xs font-bold text-slate-700 mb-1">Subject</label>
              <select x-model="editSlotData.subject_id"
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition cursor-pointer">
                <template x-for="subj in allSubjects" :key="subj.id">
                  <option :value="subj.id" x-text="subj.name + ' (' + subj.code + ')'"></option>
                </template>
              </select>
            </div>

            {{-- Teacher Selection --}}
            <div x-show="editSlotData.period_type !== 'free'">
              <label class="block text-xs font-bold text-slate-700 mb-1" x-text="editSlotData.period_type === 'substitution' ? 'Regular Assigned Teacher' : 'Assigned Teacher'"></label>
              <select x-model="editSlotData.teacher_id"
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition cursor-pointer">
                <option value="">-- No Teacher --</option>
                <template x-for="t in allTeachers" :key="t.id">
                  <option :value="t.id" x-text="t.first_name + ' ' + (t.last_name || '') + ' (' + (t.employee_code || 'EMP') + ')'"></option>
                </template>
              </select>
            </div>

            {{-- Substitute Teacher Selection (when Sub is selected) --}}
            <div x-show="editSlotData.period_type === 'substitution'" class="p-3.5 bg-purple-50 rounded-xl border border-purple-200 space-y-2">
              <div class="flex items-center gap-1.5 text-xs font-bold text-purple-900">
                <span>🔄</span> Substitute Faculty Member
              </div>
              <select x-model="editSlotData.substitute_teacher_id"
                      class="w-full px-3.5 py-2 bg-white border border-purple-300 rounded-xl text-xs font-bold text-purple-950 focus:ring-2 focus:ring-purple-500 transition cursor-pointer">
                <option value="">-- Select Substitute Teacher --</option>
                <template x-for="t in allTeachers" :key="t.id">
                  <option :value="t.id" x-text="t.first_name + ' ' + (t.last_name || '') + ' (' + (t.employee_code || 'EMP') + ')'"></option>
                </template>
              </select>
            </div>

            {{-- Room / Lab Location (Optional) --}}
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Room / Lab (Optional)</label>
              <input type="text"
                     x-model="editSlotData.room"
                     placeholder="e.g. Room 1-A / Physics Lab"
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>
          </div>

          {{-- Editor Footer with Undo Option --}}
          <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <button type="button" @click="resetSlotToDefault()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 border border-slate-300 transition cursor-pointer flex items-center gap-1">
              <span>↩️</span> Undo / Reset Slot
            </button>
            <div class="flex items-center gap-2">
              <button type="button" @click="editSlotModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                Cancel
              </button>
              <button type="button" @click="saveSlotEdit()" :disabled="isSavingSlot" class="px-5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md transition disabled:opacity-50 flex items-center gap-2 cursor-pointer">
                <span x-show="isSavingSlot" class="animate-spin">🌀</span>
                <span x-text="isSavingSlot ? 'Saving...' : '💾 Save Changes'"></span>
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>
  </template>

</div>

@push('head')
<script>
function classesDashboard() {
  return {
    classesData: @json($classesData),
    allSubjects: @json($allSubjects),
    allTeachers: @json($allTeachers),
    filterCategory: 'all',
    searchQuery: '',

    // Time Simulator State
    showTimeSimulator: false,
    simulatedTime: null,
    simulatedDayOfWeek: null,
    displayClock: '',
    liveTimeString: '',

    
    // View Timetable Modal State
    modalOpen: false,
    modalClass: null,
    modalSection: null,
    modalDay: 0,

    // Edit Timetable Modal State
    editModalOpen: false,
    editSelectedClassId: null,
    editSelectedSectionId: null,
    editClass: null,
    editSection: null,
    editDay: 0,
    selectedHolidayDay: 6,
    holidayDays: {},

    // Slot Editor Modal State
    editSlotModalOpen: false,
    isSavingSlot: false,
    editSlotData: {
      class_id: null,
      section_id: null,
      day_of_week: 1,
      dayName: 'Monday',
      period_number: 1,
      subject_id: null,
      teacher_id: null,
      substitute_teacher_id: null,
      room: '',
      period_type: 'class',
      notes: ''
    },

    currentPeriod: {
      title: 'Period 1 In Session',
      badge_title: 'Live Now',
      timing: '09:15 AM – 10:00 AM',
      remaining_text: '45m remaining',
      day_type: 'Regular Academic Day',
      period_number: 1,
      is_live: true
    },

    init() {
      this.updateClock();
      setInterval(() => {
        this.updateClock();
      }, 5000);

      // Initialize edit defaults with 1st standard or first class
      if (this.classesData.length > 0) {
        const firstCls = this.classesData.find(c => c.numeric_value === 1) || this.classesData[0];
        this.editSelectedClassId = firstCls.id;
        this.editClass = firstCls;
        if (firstCls.sections.length > 0) {
          const firstSec = firstCls.sections.find(s => s.name === 'A') || firstCls.sections[0];
          this.editSelectedSectionId = firstSec.id;
          this.editSection = firstSec;
        }
      }
    },

    updateClock() {
      const now = new Date();
      let hours = now.getHours();
      let minutes = now.getMinutes();
      let ampm = hours >= 12 ? 'PM' : 'AM';
      let h12 = hours % 12 || 12;
      this.liveTimeString = `${h12}:${minutes < 10 ? '0' + minutes : minutes} ${ampm}`;

      let currentH = hours;
      let currentM = minutes;
      let dayOfWeek = now.getDay() === 0 ? 7 : now.getDay(); // 1=Mon, 6=Sat, 7=Sun

      if (this.simulatedTime) {
        const parts = this.simulatedTime.split(':');
        currentH = parseInt(parts[0], 10);
        currentM = parseInt(parts[1], 10);
        if (this.simulatedDayOfWeek !== null) {
          dayOfWeek = this.simulatedDayOfWeek;
        }
      }

      let simAmpm = currentH >= 12 ? 'PM' : 'AM';
      let simH12 = currentH % 12 || 12;
      this.displayClock = `${simH12}:${currentM < 10 ? '0' + currentM : currentM} ${simAmpm}`;

      this.evaluateLivePeriod(currentH, currentM, dayOfWeek);
    },

    setTime(timeStr, dayOfWeek) {
      this.simulatedTime = timeStr;
      this.simulatedDayOfWeek = dayOfWeek;
      this.updateClock();
    },

    resetToLiveTime() {
      this.simulatedTime = null;
      this.simulatedDayOfWeek = null;
      this.updateClock();
    },

    evaluateLivePeriod(hours, minutes, dayOfWeek) {
      const timeInMins = hours * 60 + minutes;
      const isSat = (dayOfWeek === 6);
      const isSun = (dayOfWeek === 7);

      if (isSun) {
        this.currentPeriod = {
          title: 'Sunday — Campus Holiday',
          badge_title: 'Weekend',
          timing: 'Campus Closed',
          remaining_text: 'Classes resume Monday 09:15 AM',
          day_type: 'Sunday Holiday',
          period_number: 0,
          is_live: false,
          is_holiday: true
        };
        return;
      }

      const dayLabel = isSat ? 'Saturday Extracurricular' : 'Regular Schedule';

      if (timeInMins < 555) {
        this.currentPeriod = {
          title: 'School Starts at 09:15 AM',
          badge_title: 'Pre-Session',
          timing: 'Starts in ' + (555 - timeInMins) + ' mins',
          remaining_text: 'Period 1 begins at 09:15 AM',
          day_type: dayLabel,
          period_number: 0,
          is_live: false
        };
      } else if (timeInMins < 600) {
        this.setPeriodState(1, 'Period 1', '09:15 AM – 10:00 AM', 555, 600, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 645) {
        this.setPeriodState(2, 'Period 2', '10:00 AM – 10:45 AM', 600, 645, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 660) {
        this.currentPeriod = {
          title: 'Morning Interval Break',
          badge_title: '☕ Interval',
          timing: '10:45 AM – 11:00 AM',
          remaining_text: (660 - timeInMins) + 'm left',
          day_type: dayLabel,
          period_number: 0,
          is_live: true,
          is_break: true,
          next_period_num: 3
        };
      } else if (timeInMins < 705) {
        this.setPeriodState(3, 'Period 3', '11:00 AM – 11:45 AM', 660, 705, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 750) {
        this.setPeriodState(4, 'Period 4', '11:45 AM – 12:30 PM', 705, 750, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 795) {
        this.currentPeriod = {
          title: '🍱 Lunch Break',
          badge_title: 'Lunch',
          timing: '12:30 PM – 01:15 PM',
          remaining_text: (795 - timeInMins) + 'm left',
          day_type: dayLabel,
          period_number: 0,
          is_live: true,
          is_break: true,
          next_period_num: 5
        };
      } else if (timeInMins < 840) {
        this.setPeriodState(5, 'Period 5', '01:15 PM – 02:00 PM', 795, 840, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 885) {
        this.setPeriodState(6, 'Period 6', '02:00 PM – 02:45 PM', 840, 885, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 900) {
        this.currentPeriod = {
          title: 'Afternoon Refreshment Break',
          badge_title: 'Break',
          timing: '02:45 PM – 03:00 PM',
          remaining_text: (900 - timeInMins) + 'm left',
          day_type: dayLabel,
          period_number: 0,
          is_live: true,
          is_break: true,
          next_period_num: 7
        };
      } else if (timeInMins < 945) {
        this.setPeriodState(7, 'Period 7', '03:00 PM – 03:45 PM', 900, 945, timeInMins, dayLabel, isSat);
      } else if (timeInMins < 990) {
        this.setPeriodState(8, 'Period 8', '03:45 PM – 04:30 PM', 945, 990, timeInMins, dayLabel, isSat);
      } else {
        this.currentPeriod = {
          title: 'School Dispersed for the Day',
          badge_title: 'Dismissed',
          timing: 'Ended at 04:30 PM',
          remaining_text: 'Campus re-opens 09:15 AM tomorrow',
          day_type: dayLabel,
          period_number: 0,
          is_live: false
        };
      }
    },

    setPeriodState(periodNum, title, timing, startMin, endMin, curMin, dayLabel, isSat) {
      const elapsed = curMin - startMin;
      const total = endMin - startMin;
      const remaining = endMin - curMin;
      const percent = Math.round((elapsed / total) * 100);

      this.currentPeriod = {
        title: title + ' In Session',
        badge_title: 'Live Now',
        timing: timing,
        remaining_text: remaining + 'm left',
        day_type: dayLabel,
        period_number: periodNum,
        elapsed_mins: elapsed,
        remaining_mins: remaining,
        progress_percent: percent,
        is_live: true,
        is_saturday: isSat
      };
    },

    getLivePeriodForSection(cls, sec) {
      const pNum = this.currentPeriod.period_number;
      const day = this.simulatedDayOfWeek !== null ? this.simulatedDayOfWeek : (new Date().getDay() === 0 ? 7 : new Date().getDay());

      if (day === 7) {
        return {
          is_active_period: false,
          badge_text: 'Weekend Holiday',
          subject_name: 'Sunday Campus Holiday',
          teacher_name: 'No sessions scheduled',
          timing: 'Resumes Monday 09:15 AM'
        };
      }

      if (pNum === 0) {
        return {
          is_active_period: false,
          is_break: this.currentPeriod.is_break || false,
          badge_text: this.currentPeriod.badge_title || 'Campus Interval',
          subject_name: this.currentPeriod.title,
          teacher_name: 'Supervised Interval',
          timing: this.currentPeriod.timing,
          next_period_text: this.currentPeriod.next_period_num ? ('Period ' + this.currentPeriod.next_period_num + ' begins next') : null
        };
      }

      const weekSchedule = sec.weekly_schedule || {};
      const dayEntries = weekSchedule[day] || weekSchedule[String(day)] || weekSchedule[1] || weekSchedule['1'] || [];
      const entry = dayEntries.find(e => parseInt(e.period, 10) === pNum) || dayEntries[pNum - 1] || null;
      const nextEntry = dayEntries.find(e => parseInt(e.period, 10) === pNum + 1) || dayEntries[pNum] || null;

      // Extract real ongoing subject name & teacher name
      let subjectName = entry?.subject_name;
      let teacherName = entry?.teacher_name || sec.class_teacher || 'Faculty';

      if (!subjectName || subjectName === 'Class in Session' || subjectName === 'Study Period') {
        const todaySlots = this.getTodayPeriods(cls, sec);
        const currentSlot = todaySlots.find(s => s.period === pNum);
        if (currentSlot && currentSlot.full_title) {
          subjectName = currentSlot.full_title.split(' (')[0];
        } else {
          const defaultSubjects = ['Tamil', 'English', 'Mathematics', 'Science', 'Social Science', 'Computer Science', 'Hindi', 'Physical Education'];
          subjectName = defaultSubjects[(pNum - 1) % defaultSubjects.length];
        }
      }

      const timing = (entry && entry.start_time && entry.end_time)
        ? (entry.start_time + ' – ' + entry.end_time)
        : this.currentPeriod.timing;

      const nextSlot = pNum < 8 ? (dayEntries.find(e => parseInt(e.period, 10) === pNum + 1) || dayEntries[pNum] || null) : null;
      let nextPeriodText = null;

      if (pNum < 8) {
        const nextSubject = nextSlot?.subject_name || (this.getTodayPeriods(cls, sec)[pNum]?.full_title?.split(' (')[0]) || ('Period ' + (pNum + 1));
        nextPeriodText = 'Period ' + (pNum + 1) + ': ' + nextSubject;
      } else {
        nextPeriodText = 'End of Day (04:30 PM)';
      }

      return {
        is_active_period: true,
        is_saturday: day === 6,
        badge_text: 'LIVE NOW • PERIOD ' + pNum,
        subject_name: subjectName,
        teacher_name: teacherName,
        timing: timing,
        progress_percent: this.currentPeriod.progress_percent || 45,
        remaining_mins: this.currentPeriod.remaining_mins || 20,
        next_period_text: nextPeriodText
      };
    },

    getTodayPeriods(cls, sec) {
      const day = this.simulatedDayOfWeek !== null ? this.simulatedDayOfWeek : (new Date().getDay() === 0 ? 7 : new Date().getDay());
      const weekSchedule = sec.weekly_schedule || {};
      const dayEntries = weekSchedule[day] || weekSchedule[String(day)] || weekSchedule[1] || weekSchedule['1'] || [];

      const currentPeriodNum = this.currentPeriod.period_number;

      return [1, 2, 3, 4, 5, 6, 7, 8].map(p => {
        const entry = dayEntries.find(e => parseInt(e.period, 10) === p) || dayEntries[p - 1];
        let name = entry ? entry.subject_name : 'Period ' + p;
        let abbr = name.split(' ')[0].substring(0, 4);
        if (abbr.length < 3) abbr = name.substring(0, 4);

        return {
          period: p,
          full_title: name + ' (' + (entry ? entry.teacher_name : '') + ')',
          abbr: abbr,
          is_current: (p === currentPeriodNum && day !== 7)
        };
      });
    },

    // ── View Timetable Modal Methods ──
    async openTimetableModal(cls, sec) {
      this.modalClass = cls;
      this.modalSection = sec || (cls.sections && cls.sections[0]) || null;
      this.modalDay = 0;
      this.modalOpen = true;

      // Always fetch freshest timetable data from backend
      if (this.modalClass && this.modalSection) {
        try {
          const res = await fetch(`/classes/${this.modalClass.id}/timetable-data?section_id=${this.modalSection.id}`);
          const data = await res.json();
          if (data && data.timetable) {
            this.modalSection.weekly_schedule = data.timetable;
          }
        } catch (err) {
          console.warn('Using local cache for timetable:', err);
        }
      }
    },

    isModalDayHoliday(dayNum) {
      if (!this.modalSection || !this.modalSection.weekly_schedule) return false;
      const dayEntries = this.modalSection.weekly_schedule[dayNum] || this.modalSection.weekly_schedule[String(dayNum)] || [];
      return dayEntries.length > 0 && dayEntries.every(e => e.subject_type === 'holiday' || e.period_type === 'holiday');
    },

    getModalDays() {
      const allDays = [
        { dayNum: 1, name: 'Monday' },
        { dayNum: 2, name: 'Tuesday' },
        { dayNum: 3, name: 'Wednesday' },
        { dayNum: 4, name: 'Thursday' },
        { dayNum: 5, name: 'Friday' },
        { dayNum: 6, name: 'Saturday' }
      ];

      if (this.modalDay === 0) {
        return allDays;
      }
      return allDays.filter(d => d.dayNum === this.modalDay);
    },

    getPeriodEntry(dayNum, periodNum) {
      if (!this.modalSection) return null;
      const weekSchedule = this.modalSection.weekly_schedule || {};
      const dayEntries = weekSchedule[dayNum] || weekSchedule[String(dayNum)] || [];
      return dayEntries.find(e => parseInt(e.period, 10) === periodNum) || dayEntries[periodNum - 1] || null;
    },

    // ── Edit Timetable Modal Methods ──
    openEditTimetableModal(cls, sec) {
      const targetClass = cls || (this.classesData.find(c => c.id === this.editSelectedClassId) || this.classesData[0]);
      if (!targetClass) return;

      this.editClass = targetClass;
      this.editSelectedClassId = targetClass.id;

      const targetSection = sec || (targetClass.sections.find(s => s.id === this.editSelectedSectionId) || targetClass.sections[0] || null);
      this.editSection = targetSection;
      this.editSelectedSectionId = targetSection ? targetSection.id : null;

      this.editDay = 0;
      this.editModalOpen = true;
    },

    onEditClassChange() {
      const cid = parseInt(this.editSelectedClassId, 10);
      const cls = this.classesData.find(c => c.id === cid);
      if (cls) {
        this.editClass = cls;
        if (cls.sections.length > 0) {
          this.editSelectedSectionId = cls.sections[0].id;
          this.editSection = cls.sections[0];
        } else {
          this.editSelectedSectionId = null;
          this.editSection = null;
        }
      }
    },

    onEditSectionChange() {
      if (!this.editClass) return;
      const sid = parseInt(this.editSelectedSectionId, 10);
      const sec = this.editClass.sections.find(s => s.id === sid);
      if (sec) {
        this.editSection = sec;
      }
    },

    loadEditTimetable() {
      this.onEditClassChange();
      this.onEditSectionChange();
    },

    syncCurrentEditToClassesData() {
      if (this.editClass && this.editSection) {
        const cls = this.classesData.find(c => c.id === this.editClass.id);
        if (cls) {
          const sec = cls.sections.find(s => s.id === this.editSection.id);
          if (sec) {
            sec.weekly_schedule = JSON.parse(JSON.stringify(this.editSection.weekly_schedule || {}));
          }
        }
      }
    },

    saveAndApplyTimetable() {
      this.syncCurrentEditToClassesData();
      this.editModalOpen = false;
    },

    getEditDays() {
      const allDays = [
        { dayNum: 1, name: 'Monday' },
        { dayNum: 2, name: 'Tuesday' },
        { dayNum: 3, name: 'Wednesday' },
        { dayNum: 4, name: 'Thursday' },
        { dayNum: 5, name: 'Friday' },
        { dayNum: 6, name: 'Saturday' }
      ];

      if (this.editDay === 0) {
        return allDays;
      }
      return allDays.filter(d => d.dayNum === this.editDay);
    },

    isDayHoliday(dayNum) {
      if (this.holidayDays[dayNum] !== undefined) {
        return this.holidayDays[dayNum];
      }
      if (this.editSection && this.editSection.weekly_schedule) {
        const dayEntries = this.editSection.weekly_schedule[dayNum] || this.editSection.weekly_schedule[String(dayNum)] || [];
        if (dayEntries.length > 0 && dayEntries.every(e => e.subject_type === 'holiday' || e.period_type === 'holiday')) {
          return true;
        }
      }
      return false;
    },

    async toggleDayHoliday(dayNum) {
      const newStatus = !this.isDayHoliday(dayNum);
      this.holidayDays[dayNum] = newStatus;

      // Local state update
      if (this.editSection && this.editSection.weekly_schedule) {
        if (!this.editSection.weekly_schedule[dayNum]) {
          this.editSection.weekly_schedule[dayNum] = [];
        }
        for (let p = 1; p <= 8; p++) {
          const entryIdx = this.editSection.weekly_schedule[dayNum].findIndex(e => parseInt(e.period, 10) === p);
          const holidaySlot = {
            period: p,
            subject_name: newStatus ? 'Holiday / No Class' : 'Study Period',
            teacher_name: newStatus ? 'Holiday' : 'Faculty',
            room: newStatus ? 'Holiday' : (this.editSection.room || 'Room'),
            subject_type: newStatus ? 'holiday' : 'class',
            period_type: newStatus ? 'holiday' : 'class'
          };
          if (entryIdx >= 0) {
            this.editSection.weekly_schedule[dayNum][entryIdx] = Object.assign({}, this.editSection.weekly_schedule[dayNum][entryIdx], holidaySlot);
          } else {
            this.editSection.weekly_schedule[dayNum].push(holidaySlot);
          }
        }
      }

      this.syncCurrentEditToClassesData();

      // Backend sync
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        await fetch('{{ route("classes.timetable.toggle-holiday") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            class_id: this.editClass.id,
            section_id: this.editSection.id,
            day_of_week: dayNum,
            is_holiday: newStatus
          })
        });
      } catch (e) {
        console.error('Holiday toggle sync error:', e);
      }
    },

    getPeriodEntryDisplay(dayNum, periodNum) {
      if (!this.editSection) return { subject_name: '—', teacher_name: 'Faculty', room: 'Room', period_type: 'class' };
      const weekSchedule = this.editSection.weekly_schedule || {};
      const dayEntries = weekSchedule[dayNum] || weekSchedule[String(dayNum)] || [];
      const entry = dayEntries.find(e => parseInt(e.period, 10) === periodNum) || dayEntries[periodNum - 1] || null;
      if (!entry) {
        return { subject_name: 'Study Period', teacher_name: 'Subject Faculty', room: this.editSection.room || 'Room', period_type: 'class' };
      }
      return entry;
    },

    getSlotCardClass(dayNum, p) {
      const entry = this.getPeriodEntryDisplay(dayNum, p);
      const type = entry.period_type || entry.subject_type || 'class';
      if (type === 'free') {
        return 'bg-amber-50/50 hover:bg-amber-50 border-amber-200/80 hover:border-amber-300';
      }
      if (type === 'substitution') {
        return 'bg-purple-50/50 hover:bg-purple-50 border-purple-200/80 hover:border-purple-300';
      }
      if (type === 'activity' || dayNum === 6) {
        return 'bg-emerald-50/50 hover:bg-emerald-50 border-emerald-200/80 hover:border-emerald-300';
      }
      return 'bg-white hover:bg-blue-50/30 border-slate-200/90 hover:border-blue-300 shadow-xs';
    },

    getSlotTextClass(dayNum, p) {
      const entry = this.getPeriodEntryDisplay(dayNum, p);
      const type = entry.period_type || entry.subject_type || 'class';
      if (type === 'free') return 'text-amber-900';
      if (type === 'substitution') return 'text-purple-900';
      if (type === 'activity' || dayNum === 6) return 'text-emerald-950';
      return 'text-slate-900';
    },

    // ── Open Slot Editor ──
    openSlotEditor(dayNum, periodNum) {
      if (!this.editClass || !this.editSection) return;
      const dayNames = { 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };
      const currentEntry = this.getPeriodEntryDisplay(dayNum, periodNum);

      this.editSlotData = {
        class_id: this.editClass.id,
        section_id: this.editSection.id,
        day_of_week: dayNum,
        dayName: dayNames[dayNum] || 'Monday',
        period_number: periodNum,
        subject_id: currentEntry?.subject_id || (this.allSubjects && this.allSubjects[0]?.id) || 1,
        teacher_id: currentEntry?.teacher_id || (this.allTeachers && this.allTeachers[0]?.id) || null,
        substitute_teacher_id: null,
        room: currentEntry?.room || this.editSection.room || 'Room 1-A',
        period_type: currentEntry?.period_type || currentEntry?.subject_type || 'class',
        notes: ''
      };

      this.editSlotModalOpen = true;
    },

    setSlotAsFree() {
      this.editSlotData.period_type = 'free';
      this.saveSlotEdit();
    },

    resetSlotToDefault() {
      this.editSlotData.period_type = 'class';
      this.editSlotData.substitute_teacher_id = null;
      this.saveSlotEdit();
    },

    async saveSlotEdit() {
      this.isSavingSlot = true;
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('{{ route("classes.timetable.update-slot") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.editSlotData)
        });

        const data = await res.json();
        if (data.success && data.slot) {
          const dayNum = this.editSlotData.day_of_week;
          const periodNum = this.editSlotData.period_number;

          if (!this.editSection.weekly_schedule) {
            this.editSection.weekly_schedule = {};
          }
          if (!this.editSection.weekly_schedule[dayNum]) {
            this.editSection.weekly_schedule[dayNum] = [];
          }

          const dayEntries = this.editSection.weekly_schedule[dayNum];
          const existingIdx = dayEntries.findIndex(e => parseInt(e.period, 10) === periodNum);

          if (existingIdx >= 0) {
            dayEntries[existingIdx] = data.slot;
          } else {
            dayEntries.push(data.slot);
          }

          this.syncCurrentEditToClassesData();
          this.editSlotModalOpen = false;
        } else {
          alert(data.message || 'Failed to update slot.');
        }
      } catch (err) {
        alert('An error occurred while saving the slot: ' + err.message);
      } finally {
        this.isSavingSlot = false;
      }
    },

    get filteredClasses() {
      return this.classesData.filter(c => {
        if (this.filterCategory === 'all') {
          if (c.numeric_value < 1 || c.numeric_value > 12) {
            return false;
          }
        } else if (this.filterCategory !== 'all') {
          if (c.category !== this.filterCategory) {
            return false;
          }
        }

        if (this.searchQuery.trim()) {
          const q = this.searchQuery.toLowerCase();
          const matchName = (c.name || '').toLowerCase().includes(q);
          const matchDisplayName = (c.display_name || '').toLowerCase().includes(q);
          const matchCategory = (c.category || '').toLowerCase().includes(q);
          const matchSection = (c.sections || []).some(s =>
            (s.stream_name || '').toLowerCase().includes(q) ||
            (s.class_teacher || '').toLowerCase().includes(q)
          );
          return matchName || matchDisplayName || matchCategory || matchSection;
        }

        return true;
      });
    }
  };
}
</script>
@endpush
@endsection

