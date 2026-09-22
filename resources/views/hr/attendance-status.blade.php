@extends('layouts.app')
@section('title', $title . ' - Staff Attendance')

@section('content')
<div class="space-y-6" x-data="{
  searchQuery: '{{ addslashes($search ?? '') }}',
  staff: [
    @foreach($records as $rec)
      @php $e = $rec->employee; @endphp
      {
        id: {{ $rec->id }},
        employeeId: {{ $e ? $e->id : 0 }},
        text: '{{ strtolower(addslashes(($e ? $e->full_name : '') . ' ' . ($e ? ($e->employee_code ?? 'EMP-' . $e->id) : '') . ' ' . ($e ? ($e->category_label ?? '') : ''))) }}'
      },
    @endforeach
  ],
  matches(id) {
    if (!this.searchQuery || !this.searchQuery.trim()) return true;
    const q = this.searchQuery.toLowerCase().trim();
    const item = this.staff.find(s => s.id === id);
    return item ? item.text.includes(q) : true;
  },
  get matchCount() {
    if (!this.searchQuery || !this.searchQuery.trim()) return this.staff.length;
    const q = this.searchQuery.toLowerCase().trim();
    return this.staff.filter(s => s.text.includes(q)).length;
  }
}">

  {{-- Flash Alerts --}}
  @if(session('success'))
    <div class="alert alert-success bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3 shadow-2xs">
      <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span class="text-xs font-bold">{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error') || $errors->any())
    <div class="alert alert-error bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-center gap-3 shadow-2xs">
      <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div class="text-xs font-bold">
        {{ session('error') ?? $errors->first() }}
      </div>
    </div>
  @endif

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition shrink-0" title="Back to HR Dashboard">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </a>

      @php
        $iconColors = [
          'absent'     => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'grad' => 'linear-gradient(135deg, #E11D48 0%, #BE123C 100%)'],
          'on_duty'    => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'grad' => 'linear-gradient(135deg, #0284C7 0%, #0369A1 100%)'],
          'paid_off'   => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'grad' => 'linear-gradient(135deg, #9333EA 0%, #7E22CE 100%)'],
          'permission' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'grad' => 'linear-gradient(135deg, #D97706 0%, #B45309 100%)'],
        ];
        $colorConfig = $iconColors[$status] ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'grad' => 'linear-gradient(135deg, #4F46E5 0%, #4338CA 100%)'];
      @endphp

      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md flex-shrink-0" style="background: {{ $colorConfig['grad'] }};">
        @if($status === 'absent')
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        @elseif($status === 'on_duty')
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        @elseif($status === 'paid_off')
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        @elseif($status === 'permission')
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        @endif
      </div>

      <div>
        <h1 class="page-title text-xl font-black text-slate-800 uppercase tracking-wide" style="font-family:'Plus Jakarta Sans',sans-serif;">
          {{ $title }}
        </h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">
          Filtered attendance for {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }} &bull;
          <span class="text-slate-700 font-bold" x-text="matchCount"></span> marked {{ str_replace('_', ' ', $status) }}
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('hr.attendance.mark', ['date' => $date]) }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Mark Attendance
      </a>
      <a href="{{ route('hr.attendance.view', ['date' => $date]) }}" class="btn btn-outline btn-sm text-xs font-bold border-slate-200 text-slate-600 hover:bg-slate-100">
        View All Attendance
      </a>
    </div>
  </div>

  {{-- Filter Toolbar: Staff Category & Live Quick Search --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
    <form method="GET" action="{{ url()->current() }}" id="statusFilterForm" class="space-y-4">
      <input type="hidden" name="category" value="{{ $category }}" id="selectedCategoryInput">
      <input type="hidden" name="date" value="{{ $date }}">

      {{-- 1. Staff Category Selection --}}
      <div>
        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2">
          Staff Category
        </label>
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
          @foreach($categories as $cat)
            @php $isCatActive = ($category === $cat['key']); @endphp
            <button type="button"
                    onclick="document.getElementById('selectedCategoryInput').value='{{ $cat['key'] }}'; document.getElementById('statusFilterForm').submit();"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer
                    {{ $isCatActive ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
              <span>{{ $cat['label'] }}</span>
            </button>
          @endforeach
        </div>
      </div>

      <div class="border-t border-slate-100 pt-3 flex flex-wrap items-end justify-between gap-4">
        {{-- Date Picker --}}
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Attendance Date</label>
          <input type="date" name="date" value="{{ $date }}" onchange="document.getElementById('statusFilterForm').submit()"
                 class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
        </div>

        {{-- Quick Search (Live keystroke filtering within this status) --}}
        <div class="w-full sm:w-80">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
            Quick Search (in {{ ucfirst(str_replace('_', ' ', $status)) }})
          </label>
          <div class="relative">
            <input type="text"
                   name="search"
                   x-model="searchQuery"
                   @keydown.enter.prevent
                   placeholder="Type staff name or ID..."
                   class="input input-sm w-full bg-slate-50 border-slate-200 text-xs rounded-xl focus:bg-white px-3.5 pr-8 font-medium">
            <button type="button"
                    x-show="searchQuery && searchQuery.length > 0"
                    @click="searchQuery = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer leading-none"
                    title="Clear search"
                    style="display: none;">
              &times;
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  {{-- Status-Specific Staff Table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">

    {{-- Category & Summary Sub-Header --}}
    <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
          <span>
            @if($category === 'teaching') Teaching Staff
            @elseif($category === 'non_teaching') Non-Teaching Staff
            @elseif($category === 'driver') Drivers
            @elseif($category === 'cleaner') Cleaners / Support
            @elseif($category === 'nanny') Nannies
            @else All Staff
            @endif
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
            @if($status === 'absent') bg-rose-100 text-rose-800
            @elseif($status === 'on_duty') bg-sky-100 text-sky-800
            @elseif($status === 'paid_off') bg-purple-100 text-purple-800
            @elseif($status === 'permission') bg-amber-100 text-amber-800
            @endif">
            Status: {{ ucfirst(str_replace('_', ' ', $status)) }}
          </span>
          <template x-if="searchQuery && searchQuery.trim()">
            <span class="normal-case text-indigo-600 font-bold ml-1" x-text="`&bull; Filter: '${searchQuery.trim()}'`"></span>
          </template>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">
          Showing <strong class="text-slate-700" x-text="matchCount"></strong> staff member(s) &bull;
          @if($status === 'permission')
            Review Out Time and update Return In Time when staff returns
          @else
            Specific attendance records for selected date
          @endif
        </p>
      </div>
      <div class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-xl">
        Date: <strong class="text-indigo-600">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
      </div>
    </div>

    @if($records->isEmpty())
      {{-- Professional Empty State --}}
      <div class="p-12 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl {{ $colorConfig['bg'] }} {{ $colorConfig['text'] }} flex items-center justify-center mb-3.5 shadow-2xs">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">No Records Found</h3>
        <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto mt-1">{{ $emptyMessage }}</p>
        <div class="mt-4 flex items-center justify-center gap-2">
          <a href="{{ route('hr.attendance.mark', ['date' => $date]) }}" class="btn btn-primary btn-sm text-xs font-bold">
            Mark Attendance Now
          </a>
        </div>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-black uppercase tracking-wider text-slate-400">
              <th class="py-3 px-4">Staff Name</th>
              <th class="py-3 px-3">Staff ID</th>
              <th class="py-3 px-3">Category</th>
              @if($status === 'permission')
                <th class="py-3 px-4 text-center">Out Time</th>
                <th class="py-3 px-4 text-center">In Time</th>
                <th class="py-3 px-4 text-center">Status</th>
              @else
                <th class="py-3 px-3">Date</th>
                <th class="py-3 px-4 text-center">Status</th>
              @endif
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            @foreach($records as $rec)
              @php $emp = $rec->employee; @endphp
              <tr x-show="matches({{ $rec->id }})" class="hover:bg-slate-50/70 transition-colors">
                {{-- Staff Name --}}
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 font-black text-[11px] flex items-center justify-center shrink-0 border border-slate-200 uppercase">
                      {{ substr($emp->first_name ?? 'S', 0, 1) }}{{ substr($emp->last_name ?? '', 0, 1) }}
                    </div>
                    <div>
                      <a href="{{ route('hr.attendance.staff-detail', $emp->id) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition">
                        {{ $emp->full_name ?? 'Unknown Staff' }}
                      </a>
                      <div class="text-[11px] text-slate-400 font-medium">
                        {{ $emp->designation->name ?? ($emp->department->name ?? 'General Staff') }}
                      </div>
                    </div>
                  </div>
                </td>

                {{-- Staff ID --}}
                <td class="py-3 px-3 font-mono font-bold text-slate-600 text-[11px]">
                  {{ $emp->employee_code ?? ('EMP-' . str_pad($emp->id, 3, '0', STR_PAD_LEFT)) }}
                </td>

                {{-- Category --}}
                <td class="py-3 px-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $emp->category_label ?? 'Staff' }}
                  </span>
                </td>

                @if($status === 'permission')
                  {{-- Out Time --}}
                  <td class="py-3 px-4 text-center">
                    @if(!empty($rec->check_out))
                      <span class="inline-flex items-center gap-1 font-mono font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 text-xs">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($rec->check_out)->format('h:i A') }}
                      </span>
                    @else
                      <span class="text-slate-400 italic font-medium">—</span>
                    @endif
                  </td>

                  {{-- In Time (Manual Enter workflow or Displayed Completed/Auto-filled) --}}
                  <td class="py-3 px-4 text-center">
                    @if(!empty($rec->check_in))
                      {{-- Already has In Time: display completed time without Enter In Time button --}}
                      <div class="inline-flex flex-col items-center gap-0.5">
                        <span class="inline-flex items-center gap-1 font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                          <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                          </svg>
                          {{ \Carbon\Carbon::parse($rec->check_in)->format('h:i A') }}
                        </span>
                        @if($rec->in_time_auto_filled)
                          <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold text-slate-500 bg-slate-100 border border-slate-200" title="Auto-filled at school dispersal">
                            Auto-filled
                          </span>
                        @endif
                      </div>
                    @else
                      {{-- Blank In Time: provide Enter In Time workflow --}}
                      <div x-data="{ editing: false, inTimeVal: '' }" class="inline-flex flex-col items-center">
                        <div x-show="!editing" class="flex items-center gap-2">
                          <span class="text-slate-400 italic text-xs font-semibold">Not Entered</span>
                          <button type="button"
                                  @click="editing = true; $nextTick(() => $refs.inTimeInput.focus())"
                                  class="btn btn-xs rounded-lg font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Enter In Time
                          </button>
                        </div>

                        {{-- Inline Form for In Time --}}
                        <div x-show="editing" style="display: none;" class="bg-amber-50/80 p-1.5 rounded-xl border border-amber-200">
                          <form method="POST" action="{{ route('hr.attendance.permission.update-in-time') }}" class="flex items-center gap-1.5">
                            @csrf
                            <input type="hidden" name="attendance_id" value="{{ $rec->id }}">
                            <div class="flex items-center gap-1">
                              <label class="text-[10px] font-bold uppercase text-amber-800">In Time:</label>
                              <input type="time"
                                     name="in_time"
                                     x-ref="inTimeInput"
                                     x-model="inTimeVal"
                                     required
                                     class="input input-xs border-amber-300 rounded-lg text-xs font-mono font-bold text-slate-800 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-28">
                            </div>
                            <button type="submit" class="btn btn-xs btn-primary text-[11px] font-bold px-2 py-0.5 shadow-2xs">
                              Save
                            </button>
                            <button type="button" @click="editing = false" class="btn btn-xs btn-ghost text-[11px] font-semibold text-slate-500 px-1.5">
                              Cancel
                            </button>
                          </form>
                        </div>
                      </div>
                    @endif
                  </td>

                  {{-- Status --}}
                  <td class="py-3 px-4 text-center">
                    @if(!empty($rec->check_in))
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completed
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        On Permission
                      </span>
                    @endif
                  </td>
                @else
                  {{-- Date for other statuses --}}
                  <td class="py-3 px-3 text-slate-600 font-medium">
                    {{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}
                  </td>

                  {{-- Status Badge --}}
                  <td class="py-3 px-4 text-center">
                    @if($status === 'absent')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Absent
                      </span>
                    @elseif($status === 'on_duty')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        On Duty
                      </span>
                    @elseif($status === 'paid_off')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Paid Off
                      </span>
                    @endif
                  </td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>

</div>
@endsection
