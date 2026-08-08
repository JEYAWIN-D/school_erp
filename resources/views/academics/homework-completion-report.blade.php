@extends('layouts.app')
@section('title', 'Homework Completion Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Homework Completion Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Class *</label>
        <select name="class_id" required class="select w-36">
          <option value="">Select Class</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Subject</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)
            <option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if(isset($homeworks))
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Title / Description','Subject','Due Date','Assigned By','Submitted','Not Submitted','Evaluated','% Completion','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($homeworks as $hw)
        @php
          $sub       = $hw->submissions;
          $submitted = $sub->whereIn('status', ['submitted','late','evaluated'])->count();
          $evaluated = $sub->where('status', 'evaluated')->count();
          $notSub    = $totalStudents - $submitted;
          $pct       = $totalStudents > 0 ? round($submitted / $totalStudents * 100) : 0;
        @endphp
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">
            {{ $hw->title ?: Str::limit($hw->description, 50) }}
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $hw->subject?->name }}</td>
          <td class="px-4 py-3 text-xs {{ $hw->due_date?->isPast() ? 'text-red-500' : 'text-slate-500' }}">{{ $hw->due_date?->format('d M Y') }}</td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $hw->teacher?->full_name ?? '—' }}</td>
          <td class="px-4 py-3 text-center">
            <span class="badge-green text-xs">{{ $submitted }}</span>
          </td>
          <td class="px-4 py-3 text-center">
            <span class="badge-red text-xs">{{ max(0,$notSub) }}</span>
          </td>
          <td class="px-4 py-3 text-center">
            <span class="badge-blue text-xs">{{ $evaluated }}</span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <div class="flex-1 bg-slate-100 rounded-full h-2 min-w-16">
                <div class="h-2 rounded-full bg-green-500" style="width:{{ $pct }}%"></div>
              </div>
              <span class="text-xs text-slate-500 w-8">{{ $pct }}%</span>
            </div>
          </td>
          <td class="px-4 py-3">
            <a href="{{ route('academics.homework.submissions', $hw->id) }}" class="btn btn-secondary btn-xs">Manage</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No homework found for this class.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if(isset($homeworks) && method_exists($homeworks, 'hasPages') && $homeworks->hasPages())
    <div class="px-4 pb-3 text-sm">{{ $homeworks->links() }}</div>
    @endif
  </div>
  @endif
</div>
@endsection
