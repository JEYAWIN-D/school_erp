@extends('layouts.app')
@section('title', 'Staff Events & Celebrations')

@section('content')
<div class="space-y-6">

  {{-- ── 1. Page Header ────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to HR & Payroll">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-rose-100 flex-shrink-0" style="background: linear-gradient(135deg, #E11D48 0%, #DB2777 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Staff Events & Celebrations</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Track birthdays, wedding anniversaries, joining milestones & school celebrations</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <button onclick="document.getElementById('addEventModal').classList.remove('hidden')"
              class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs bg-rose-600 hover:bg-rose-700 border-rose-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Staff Event
      </button>
    </div>
  </div>

  {{-- ── 2. Automatic Celebrations Alert Feed (Next 30 Days) ─────── --}}
  <div class="bg-gradient-to-r from-rose-50 via-pink-50 to-purple-50 p-5 rounded-2xl border border-rose-200/80 shadow-2xs space-y-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-xl">🎂</span>
        <h2 class="font-black text-sm text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Upcoming Birthdays & Work Anniversaries (Next 30 Days)
        </h2>
      </div>
      <span class="text-xs font-bold text-rose-700 bg-rose-100/80 px-2.5 py-0.5 rounded-full">
        {{ count($staffCelebrations) }} Milestone(s)
      </span>
    </div>

    @if(count($staffCelebrations) > 0)
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 pt-1">
        @foreach($staffCelebrations as $cel)
          <div class="bg-white/90 backdrop-blur-xs p-3.5 rounded-xl border border-rose-100 shadow-2xs hover:shadow-xs transition-shadow flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl {{ $cel['type'] === 'birthday' ? 'bg-rose-100 text-rose-700' : 'bg-purple-100 text-purple-700' }} flex items-center justify-center text-base shrink-0">
              {{ $cel['type'] === 'birthday' ? '🎂' : '🏆' }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="font-bold text-xs text-slate-900 truncate">{{ $cel['staff_name'] }}</p>
              <p class="text-[11px] text-slate-500 truncate">{{ $cel['title'] }}</p>
              <div class="flex items-center justify-between mt-1 pt-1 border-t border-slate-100">
                <span class="text-[11px] font-bold font-mono text-slate-700">{{ $cel['date_label'] }}</span>
                <span class="text-[10px] font-extrabold {{ $cel['days_left'] == 0 ? 'text-emerald-600' : ($cel['days_left'] == 1 ? 'text-amber-600' : 'text-slate-400') }}">
                  {{ $cel['days_left'] == 0 ? 'Today!' : ($cel['days_left'] == 1 ? 'Tomorrow' : 'in '.$cel['days_left'].'d') }}
                </span>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-500 italic py-1">No automatic staff milestones (birthdays or anniversaries) falling in the next 30 days.</p>
    @endif
  </div>

  {{-- ── 3. Filters & Tabs ──────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
    {{-- Tabs --}}
    <div class="flex items-center gap-2">
      <a href="{{ route('hr.events', ['tab' => 'upcoming', 'type' => $type]) }}"
         class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $tab === 'upcoming' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
        Upcoming Events
      </a>
      <a href="{{ route('hr.events', ['tab' => 'past', 'type' => $type]) }}"
         class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $tab === 'past' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
        Past Events
      </a>
      <a href="{{ route('hr.events', ['tab' => 'all', 'type' => $type]) }}"
         class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $tab === 'all' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
        All Events
      </a>
    </div>

    {{-- Type Filter --}}
    <form method="GET" action="{{ route('hr.events') }}" class="flex items-center gap-2">
      <input type="hidden" name="tab" value="{{ $tab }}">
      <label class="text-xs font-bold text-slate-500">Event Type:</label>
      <select name="type" onchange="this.form.submit()"
              class="form-select rounded-xl border-slate-200 text-xs font-bold text-slate-800 py-1.5 pl-3 pr-8 focus:border-rose-500 focus:ring-rose-500 bg-slate-50/50">
        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
        <option value="birthday" {{ $type === 'birthday' ? 'selected' : '' }}>Birthday</option>
        <option value="wedding" {{ $type === 'wedding' ? 'selected' : '' }}>Wedding</option>
        <option value="wedding_anniversary" {{ $type === 'wedding_anniversary' ? 'selected' : '' }}>Wedding Anniversary</option>
        <option value="joining_anniversary" {{ $type === 'joining_anniversary' ? 'selected' : '' }}>Joining Anniversary</option>
        <option value="retirement" {{ $type === 'retirement' ? 'selected' : '' }}>Retirement</option>
        <option value="other" {{ $type === 'other' ? 'selected' : '' }}>Other School Event</option>
      </select>
    </form>
  </div>

  {{-- ── 4. Events List / Table ─────────────────────────────────── --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Event Date</th>
            <th class="py-3 px-4">Staff Member</th>
            <th class="py-3 px-4">Event Type</th>
            <th class="py-3 px-4">Title</th>
            <th class="py-3 px-4">Description</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-medium">
          @forelse($events as $event)
            @php
              $date = \Carbon\Carbon::parse($event->event_date);
              $isPast = $date->isPast() && !$date->isToday();
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors {{ $isPast ? 'opacity-75' : '' }}">
              <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                <div>{{ $date->format('d M Y') }}</div>
                <div class="text-[11px] font-medium {{ $date->isToday() ? 'text-emerald-600 font-bold' : ($isPast ? 'text-slate-400' : 'text-indigo-600') }}">
                  {{ $date->isToday() ? 'Today!' : ($isPast ? $date->diffForHumans() : 'in ' . $date->diffInDays(today()) . ' days') }}
                </div>
              </td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900">{{ $event->employee?->full_name ?? '—' }}</div>
                <div class="text-[11px] text-slate-400 font-mono">{{ $event->employee?->employee_code }} &bull; {{ $event->employee?->department_name }}</div>
              </td>
              <td class="py-3.5 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $event->badge_color }}">
                  <span>
                    @if($event->event_type === 'birthday') 🎂
                    @elseif($event->event_type === 'wedding') 💍
                    @elseif($event->event_type === 'wedding_anniversary') 💐
                    @elseif($event->event_type === 'joining_anniversary') 🏆
                    @elseif($event->event_type === 'retirement') 🎓
                    @else 🎈
                    @endif
                  </span>
                  {{ $event->type_label }}
                </span>
              </td>
              <td class="py-3.5 px-4 font-bold text-slate-800">{{ $event->title }}</td>
              <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate">{{ $event->description ?? '—' }}</td>
              <td class="py-3.5 px-4 text-right">
                <form method="POST" action="{{ route('hr.events.delete', $event->id) }}" onsubmit="return confirm('Are you sure you want to delete this staff event?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-rose-600 hover:text-rose-800 p-1 rounded-lg hover:bg-rose-50 transition-colors" title="Delete event">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-12 text-slate-400">
                <div class="space-y-2">
                  <p class="text-3xl">🗓️</p>
                  <p class="font-bold text-xs text-slate-600">No events found in this category.</p>
                  <p class="text-[11px] text-slate-400">Click "Add Staff Event" above to record a new staff celebration.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($events->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $events->links() }}
      </div>
    @endif
  </div>

  {{-- ── 5. Add Event Modal ──────────────────────────────────────── --}}
  <div id="addEventModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-base font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Add Staff Event</h3>
        <button onclick="document.getElementById('addEventModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form method="POST" action="{{ route('hr.events.store') }}" class="space-y-4">
        @csrf

        {{-- Staff Member --}}
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Staff Member <span class="text-rose-500">*</span></label>
          <select name="employee_id" required class="form-select w-full rounded-xl border-slate-200 text-xs font-medium focus:border-rose-500 focus:ring-rose-500">
            <option value="">-- Select Staff Member --</option>
            @foreach($employees as $emp)
              <option value="{{ $emp->id }}">
                {{ $emp->full_name }} ({{ $emp->employee_code }}) - {{ $emp->category_label }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Event Type --}}
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Event Type <span class="text-rose-500">*</span></label>
          <select name="event_type" required class="form-select w-full rounded-xl border-slate-200 text-xs font-medium focus:border-rose-500 focus:ring-rose-500">
            <option value="birthday">Birthday</option>
            <option value="wedding">Wedding</option>
            <option value="wedding_anniversary">Wedding Anniversary</option>
            <option value="joining_anniversary">Joining Anniversary</option>
            <option value="retirement">Retirement</option>
            <option value="other">Other School Event</option>
          </select>
        </div>

        {{-- Event Date --}}
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Event Date <span class="text-rose-500">*</span></label>
          <input type="date" name="event_date" value="{{ today()->toDateString() }}" required
                 class="form-input w-full rounded-xl border-slate-200 text-xs font-medium focus:border-rose-500 focus:ring-rose-500">
        </div>

        {{-- Title --}}
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Title <span class="text-rose-500">*</span></label>
          <input type="text" name="title" placeholder="e.g. 5th Work Anniversary Celebration" required
                 class="form-input w-full rounded-xl border-slate-200 text-xs font-medium focus:border-rose-500 focus:ring-rose-500">
        </div>

        {{-- Description --}}
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Description (Optional)</label>
          <textarea name="description" rows="3" placeholder="Additional notes or celebration plans..."
                    class="form-textarea w-full rounded-xl border-slate-200 text-xs font-medium focus:border-rose-500 focus:ring-rose-500"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('addEventModal').classList.add('hidden')"
                  class="btn btn-secondary btn-sm text-xs font-bold">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm text-xs font-bold bg-rose-600 hover:bg-rose-700 border-rose-600">
            Save Event
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
