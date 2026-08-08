@extends('layouts.app')
@section('title','Academic Year Archive')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Academic Year Archive</h1>
    <span class="badge-amber text-xs">Read-Only — Historical Data</span>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end flex-wrap">
      <div>
        <label class="label text-xs">Academic Year</label>
        <select name="year_id" class="select w-48">
          @foreach($academicYears as $yr)
          <option value="{{ $yr->id }}" @selected($selectedYear && $selectedYear->id === $yr->id)>
            {{ $yr->name }}{{ $yr->is_current ? ' (Current)' : '' }}
          </option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">View Archive</button>
    </div>
  </form>

  @if($selectedYear)
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-semibold text-slate-800">{{ $selectedYear->name }}</h2>
        <p class="text-sm text-slate-400">
          {{ $selectedYear->start_date?->format('d M Y') }} — {{ $selectedYear->end_date?->format('d M Y') }}
        </p>
      </div>
      <a href="{{ route('academics.year-archive.students', $selectedYear->id) }}" class="btn btn-secondary btn-sm">
        View All Students →
      </a>
    </div>

    @if($classSummary->count())
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Class</th>
            <th class="th">Total Students</th>
            <th class="th">Promoted</th>
            <th class="th">Detained / Held</th>
            <th class="th">Promotion %</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($classSummary as $row)
          <tr class="tr">
            <td class="td font-medium">{{ $row['class']->name }}</td>
            <td class="td text-center">{{ $row['total'] }}</td>
            <td class="td text-center text-green-600 font-semibold">{{ $row['promoted'] }}</td>
            <td class="td text-center {{ $row['detained'] > 0 ? 'text-red-500' : 'text-slate-300' }}">
              {{ $row['detained'] }}
            </td>
            <td class="td text-center">
              @if($row['total'] > 0)
                @php $pct = round($row['promoted'] / $row['total'] * 100, 1); @endphp
                <span class="{{ $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-amber-500' : 'text-red-500') }} font-semibold">
                  {{ $pct }}%
                </span>
              @else
                <span class="text-slate-300">—</span>
              @endif
            </td>
            <td class="td">
              <a href="{{ route('academics.year-archive.students', [$selectedYear->id, 'class_id' => $row['class']->id]) }}"
                class="btn btn-ghost btn-xs">View Students</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
    <div class="text-center py-8 text-slate-400">No student enrollment data found for this year.</div>
    @endif
  </div>
  @endif
</div>
@endsection
