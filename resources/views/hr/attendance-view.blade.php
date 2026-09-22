@extends('layouts.app')
@section('title', 'View Staff Attendance')

@section('content')
<div class="space-y-6" x-data="{
  searchQuery: '{{ addslashes($search ?? '') }}',
  staff: [
    @foreach($staffSummary as $row)
      @php $e = $row['employee']; @endphp
      {
        id: {{ $e->id }},
        text: '{{ strtolower(addslashes($e->full_name . ' ' . ($e->employee_code ?? 'EMP-' . $e->id) . ' ' . ($e->category_label ?? ''))) }}'
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

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to HR & Payroll">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-blue-100 flex-shrink-0" style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">View Attendance</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Staff category & period attendance register &bull; {{ $periodLabel }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('hr.attendance.mark') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Mark Attendance
      </a>
    </div>
  </div>

  {{-- Filter Toolbar: Staff Category, Period & Live Quick Search --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
    <form method="GET" action="{{ route('hr.attendance.view') }}" id="viewFilterForm" class="space-y-4">
      <input type="hidden" name="category" value="{{ $category }}" id="selectedCategoryInput">

      {{-- 1. Staff Category Selection (Primary Selection) --}}
      <div>
        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2">
          Select Staff Category
        </label>
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
          @foreach($categories as $cat)
            @php $isCatActive = ($category === $cat['key']); @endphp
            <button type="button"
                    onclick="document.getElementById('selectedCategoryInput').value='{{ $cat['key'] }}'; document.getElementById('viewFilterForm').submit();"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer
                    {{ $isCatActive ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
              <span>{{ $cat['label'] }}</span>
            </button>
          @endforeach
        </div>
      </div>

      <div class="border-t border-slate-100 pt-3 flex flex-wrap items-end justify-between gap-4">
        {{-- 2. Period Selection --}}
        <div class="flex flex-wrap items-center gap-3">
          <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Period</label>
            <select name="period_type" onchange="document.getElementById('viewFilterForm').submit()"
                    class="select select-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
              <option value="daily" @selected($periodType === 'daily')>Daily</option>
              <option value="weekly" @selected($periodType === 'weekly')>Weekly</option>
              <option value="monthly" @selected($periodType === 'monthly')>Monthly</option>
              <option value="term" @selected($periodType === 'term')>Term</option>
              <option value="yearly" @selected($periodType === 'yearly')>Yearly</option>
            </select>
          </div>

          {{-- Period specific inputs --}}
          @if($periodType === 'daily' || $periodType === 'weekly')
            <div>
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                {{ $periodType === 'daily' ? 'Date' : 'Week of Date' }}
              </label>
              <input type="date" name="date" value="{{ $date }}" onchange="document.getElementById('viewFilterForm').submit()"
                     class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
            </div>
          @elseif($periodType === 'monthly')
            <div>
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Month</label>
              <input type="month" name="month" value="{{ $month }}" onchange="document.getElementById('viewFilterForm').submit()"
                     class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
            </div>
          @elseif($periodType === 'term')
            <div>
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Academic Term</label>
              <select name="term_id" onchange="document.getElementById('viewFilterForm').submit()"
                      class="select select-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
                @foreach($academicTerms as $t)
                  <option value="{{ $t->id }}" @selected($termId == $t->id)>
                    {{ $t->name }} ({{ $t->start_date->format('d M') }} - {{ $t->end_date->format('d M Y') }})
                  </option>
                @endforeach
              </select>
            </div>
          @elseif($periodType === 'yearly')
            <div>
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Academic Year</label>
              <select name="year_id" onchange="document.getElementById('viewFilterForm').submit()"
                      class="select select-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800">
                @foreach($academicYears as $y)
                  <option value="{{ $y->id }}" @selected($yearId == $y->id)>
                    {{ $y->name }} {{ $y->is_current ? '(Current)' : '' }}
                  </option>
                @endforeach
              </select>
            </div>
          @endif
        </div>

        {{-- 3. Quick Search (Live keystroke filtering like Mark Attendance) --}}
        <div class="w-full sm:w-72">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Quick Search</label>
          <div class="relative">
            <input type="text"
                   name="search"
                   x-model="searchQuery"
                   @keydown.enter.prevent
                   placeholder="Search staff by name or ID..."
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

  {{-- Category Attendance Table (Without Attendance Percentage Column) --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">

    {{-- Category Header Title --}}
    <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-xs font-black uppercase tracking-wider text-slate-800 flex flex-wrap items-center gap-2">
          <span>
            @if($category === 'teaching') Teaching Staff Attendance
            @elseif($category === 'non_teaching') Non-Teaching Staff Attendance
            @elseif($category === 'driver') Drivers Attendance
            @elseif($category === 'cleaner') Cleaners Attendance
            @elseif($category === 'nanny') Nannies Attendance
            @else All Staff Attendance
            @endif
          </span>
          @if(!empty($statusFilter))
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold normal-case">
              Status: {{ ucfirst(str_replace('_', ' ', $statusFilter)) }}
              <a href="{{ route('hr.attendance.view', request()->except('status')) }}" class="text-indigo-900 hover:text-rose-600 font-extrabold ml-0.5 leading-none" title="Clear status filter">&times;</a>
            </span>
          @endif
          <template x-if="searchQuery && searchQuery.trim()">
            <span class="normal-case text-indigo-600 font-bold ml-1" x-text="`&bull; Filter: '${searchQuery.trim()}'`"></span>
          </template>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Showing <strong class="text-slate-700" x-text="matchCount"></strong> staff members &bull; Click any staff member to inspect detailed individual attendance history</p>
      </div>
      <div class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-xl">
        Working Days in Period: <strong class="text-indigo-600">{{ $totalWorkingDays }}</strong>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
            <th class="py-3 px-4">Staff Name</th>
            <th class="py-3 px-3">Category</th>
            <th class="py-3 px-2.5 text-center">Present</th>
            <th class="py-3 px-2.5 text-center">Absent</th>
            <th class="py-3 px-2.5 text-center">Half Day</th>
            <th class="py-3 px-2.5 text-center text-sky-700">On Duty</th>
            <th class="py-3 px-2.5 text-center text-purple-700">Paid Off</th>
            <th class="py-3 px-2.5 text-center text-amber-700">Permission</th>
            <th class="py-3 px-2.5 text-center">Leave</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
          @forelse($staffSummary as $row)
            @php $emp = $row['employee']; @endphp
            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer"
                x-show="matches({{ $emp->id }})"
                onclick="window.location='{{ route('hr.attendance.staff-detail', array_merge(['id' => $emp->id], request()->query())) }}'">
              
              {{-- Staff Name --}}
              <td class="py-3.5 px-4 font-bold text-slate-900">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                    {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}
                  </div>
                  <div>
                    <span class="text-slate-900 hover:text-indigo-600 font-bold block">{{ $emp->full_name }}</span>
                    <span class="text-[10px] font-mono text-slate-400 block">{{ $emp->employee_code ?? 'EMP-' . $emp->id }}</span>
                  </div>
                </div>
              </td>

              {{-- Category --}}
              <td class="py-3.5 px-3">
                <span class="{{ $emp->category_badge_class }} text-[9px] px-2.5 py-0.5 font-bold uppercase rounded">
                  {{ $emp->category_label }}
                </span>
              </td>

              {{-- Present Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-bold text-emerald-600 text-sm">
                {{ $row['present'] }}
              </td>

              {{-- Absent Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-bold text-rose-600 text-sm">
                {{ $row['absent'] }}
              </td>

              {{-- Half Day Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-medium text-slate-700 text-xs">
                {{ $row['half_day'] }}
              </td>

              {{-- On Duty Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-medium text-sky-700 text-xs">
                {{ $row['on_duty'] ?? 0 }}
              </td>

              {{-- Paid Off Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-medium text-purple-700 text-xs">
                {{ $row['paid_off'] ?? 0 }}
              </td>

              {{-- Permission Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-medium text-amber-700 text-xs">
                {{ $row['permission'] ?? 0 }}
              </td>

              {{-- Leave Count --}}
              <td class="py-3.5 px-2.5 text-center font-mono font-bold text-amber-600 text-sm">
                {{ $row['leave'] }}
              </td>

              {{-- Action Link --}}
              <td class="py-3.5 px-4 text-right">
                <a href="{{ route('hr.attendance.staff-detail', array_merge(['id' => $emp->id], request()->query())) }}"
                   class="btn btn-secondary btn-xs font-bold text-indigo-600 hover:text-indigo-700"
                   onclick="event.stopPropagation()">
                  Inspect &rarr;
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="py-12 text-center text-slate-400">
                <p class="text-sm font-semibold text-slate-500">No attendance records found for this selection.</p>
              </td>
            </tr>
          @endforelse

          {{-- Empty state when quick search has no matches --}}
          <tr x-show="matchCount === 0 && staff.length > 0" style="display: none;">
            <td colspan="10" class="py-12 text-center text-slate-400">
              <div class="max-w-xs mx-auto text-center space-y-2">
                <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 14a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
                <p class="text-sm font-bold text-slate-600">No staff members found.</p>
                <p class="text-xs text-slate-400">No staff matches "<span class="font-semibold text-slate-600" x-text="searchQuery"></span>" in this category.</p>
                <button type="button" @click="searchQuery = ''" class="btn btn-secondary btn-xs mt-2 text-indigo-600 font-bold">
                  Clear search
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
