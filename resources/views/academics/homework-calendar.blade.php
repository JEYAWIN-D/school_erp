@extends('layouts.app')
@section('title', 'Homework Calendar')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Homework Calendar — {{ $start->format('F Y') }}</h1>
    <div class="flex gap-2">
      <a href="{{ route('academics.homework') }}" class="btn-sm btn-secondary">List View</a>
    </div>
  </div>

  {{-- Month nav + filters --}}
  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <select name="month" class="select w-36">
          @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Year</label>
        <select name="year" class="select w-24">
          @foreach(range(now()->year-1, now()->year+1) as $y)
            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-40">
          <option value="">All Classes</option>
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-sm btn-primary">Apply</button>
      {{-- Prev / Next --}}
      @php
        $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
        $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
      @endphp
      <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
        class="btn-sm btn-secondary">← Prev</a>
      <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
        class="btn-sm btn-secondary">Next →</a>
    </div>
  </form>

  {{-- Calendar grid --}}
  <div class="card overflow-hidden">
    {{-- Day headers --}}
    <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
        <div class="px-2 py-2 text-center text-xs font-semibold text-slate-500 uppercase">{{ $day }}</div>
      @endforeach
    </div>

    {{-- Calendar cells --}}
    @php
      $firstDayOfWeek = $start->dayOfWeek; // 0=Sun, 6=Sat
      $totalCells = $firstDayOfWeek + count($calendar);
      $rows = ceil($totalCells / 7);
      $calKeys = array_keys($calendar);
      $cellIdx = 0;
    @endphp

    @for($row = 0; $row < $rows; $row++)
      <div class="grid grid-cols-7 divide-x divide-slate-100 border-b border-slate-100">
        @for($col = 0; $col < 7; $col++)
          @php
            $cellNumber = $row * 7 + $col;
            $dayOffset  = $cellNumber - $firstDayOfWeek;
          @endphp
          @if($dayOffset < 0 || $dayOffset >= count($calKeys))
            <div class="bg-slate-50 min-h-[80px] p-2"></div>
          @else
            @php
              $dateKey = $calKeys[$dayOffset];
              $cell    = $calendar[$dateKey];
              $isToday = $cell['date']->isToday();
              $hw      = $cell['homework'];
            @endphp
            <div class="min-h-[80px] p-2 {{ $isToday ? 'bg-blue-50' : '' }} hover:bg-slate-50 transition-colors">
              <div class="text-right mb-1">
                <span class="text-xs font-semibold {{ $isToday ? 'bg-blue-600 text-white rounded-full px-1.5 py-0.5' : 'text-slate-400' }}">
                  {{ $cell['date']->day }}
                </span>
              </div>
              <div class="space-y-1">
                @foreach($hw->take(3) as $h)
                <div class="text-xs rounded px-1.5 py-0.5 truncate
                  @if($h->subject?->name)
                    {{ ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-purple-100 text-purple-800', 'bg-orange-100 text-orange-800', 'bg-pink-100 text-pink-800'][crc32($h->subject->name) % 5] }}
                  @else
                    bg-slate-100 text-slate-700
                  @endif
                  " title="{{ $h->title }} — {{ $h->class?->name }}">
                  {{ $h->subject?->short_name ?? $h->subject?->name ?? '—' }}: {{ \Str::limit($h->title, 20) }}
                </div>
                @endforeach
                @if($hw->count() > 3)
                  <div class="text-xs text-slate-400 text-right">+{{ $hw->count() - 3 }} more</div>
                @endif
              </div>
            </div>
          @endif
        @endfor
      </div>
    @endfor
  </div>

  {{-- Upcoming due homework list --}}
  <div class="card">
    <h2 class="text-sm font-semibold text-slate-700 mb-3">All Homework This Month</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Due Date</th>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Title</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse(collect($calendar)->pluck('homework')->flatten()->sortBy('due_date') as $h)
          <tr class="tr {{ $h->due_date->isPast() ? 'opacity-60' : '' }}">
            <td class="td font-mono text-xs">{{ $h->due_date->format('d M Y') }}
              @if($h->due_date->isToday()) <span class="badge-blue ml-1">Today</span> @endif
            </td>
            <td class="td">{{ $h->class?->name }}</td>
            <td class="td">{{ $h->subject?->name }}</td>
            <td class="td">{{ $h->title }}</td>
            <td class="td">
              <a href="{{ route('academics.homework.submissions', $h->id) }}" class="btn-xs btn-secondary">Submissions</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="td text-center text-slate-400">No homework due this month.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
