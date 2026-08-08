@extends('layouts.app')
@section('title','Archive — ' . $year->name)
@section('content')
<div class="space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('academics.year-archive', ['year_id' => $year->id]) }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="page-title">{{ $year->name }} — Student Archive</h1>
      <p class="text-sm text-slate-400">Read-only historical data</p>
    </div>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label text-xs">Filter by Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Class</th>
            <th class="th">Section</th>
            <th class="th">Roll No</th>
            <th class="th">Status</th>
            <th class="th">Promoted</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrollments as $i => $e)
          <tr class="tr">
            <td class="td text-slate-400">{{ $enrollments->firstItem() + $i }}</td>
            <td class="td font-medium">{{ $e->student?->full_name }}</td>
            <td class="td text-slate-500">{{ $e->student?->admission_number }}</td>
            <td class="td">{{ $e->class?->name }}</td>
            <td class="td">{{ $e->section?->name ?? '—' }}</td>
            <td class="td">{{ $e->roll_number ?? '—' }}</td>
            <td class="td">
              <span class="badge-{{ match($e->status ?? 'active'){ 'active'=>'green','transferred'=>'amber','left'=>'red',default=>'slate' } }} text-xs capitalize">
                {{ $e->status ?? 'active' }}
              </span>
            </td>
            <td class="td text-center">
              @if($e->promoted)
                <span class="badge-green text-xs">Yes</span>
              @elseif($e->promoted === false)
                <span class="badge-red text-xs">No</span>
              @else
                <span class="text-slate-300">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($enrollments->hasPages())
    <div class="px-4 pb-3 text-sm">{{ $enrollments->links() }}</div>
    @endif
  </div>
</div>
@endsection
