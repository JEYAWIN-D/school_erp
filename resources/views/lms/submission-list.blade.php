@extends('layouts.app')
@section('title', 'Submissions')
@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">{{ $assignment->title }} — Submissions</h1>
      <p class="text-sm text-slate-400">{{ $submissions->total() }} submission(s) &bull; Max: {{ $assignment->max_marks }} marks</p>
    </div>
    <a href="{{ route('lms.assignments.index', $assignment->course_id) }}" class="btn-secondary btn-sm">← Assignments</a>
  </div>

  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Submitted</th>
          <th class="th">Status</th>
          <th class="th text-center">Score</th>
          <th class="th">Feedback</th>
          <th class="th text-center">Actions</th>
        </tr></thead>
        <tbody>
          @forelse($submissions as $sub)
          <tr class="tr" x-data="{ open: false }">
            <td class="td">
              <div class="font-medium text-slate-700">{{ $sub->student_name }}</div>
              <div class="text-xs text-slate-400 font-mono">{{ $sub->admission_no }}</div>
            </td>
            <td class="td text-xs text-slate-500">
              {{ \Carbon\Carbon::parse($sub->created_at)->format('d M Y H:i') }}
              @if($sub->is_late) <span class="badge-red text-xs ml-1">Late</span> @endif
            </td>
            <td class="td">
              @if($sub->evaluated_at)
                <span class="badge-green text-xs">Evaluated</span>
              @else
                <span class="badge-amber text-xs">Pending</span>
              @endif
            </td>
            <td class="td text-center">
              @if($sub->evaluated_at)
                <span class="font-semibold {{ $sub->score >= ($assignment->max_marks * 0.5) ? 'text-green-600' : 'text-red-600' }}">
                  {{ $sub->score }}/{{ $assignment->max_marks }}
                </span>
              @else
                <span class="text-slate-400">—</span>
              @endif
            </td>
            <td class="td text-xs text-slate-500 max-w-xs truncate">
              {{ $sub->feedback ?? '—' }}
            </td>
            <td class="td text-center">
              <div class="flex items-center justify-center gap-2">
                <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="btn-xs btn-secondary">Download</a>
                <button @click="open = !open" class="btn-xs btn-primary">Evaluate</button>
              </div>
            </td>
          </tr>
          {{-- Inline evaluate form --}}
          <tr x-show="open" x-transition class="bg-blue-50">
            <td colspan="6" class="px-4 py-3">
              <form method="POST" action="{{ route('lms.assignments.evaluate', $sub->id) }}" class="flex flex-wrap items-end gap-3">
                @csrf @method('PATCH')
                <div>
                  <label class="label text-xs">Score (max {{ $assignment->max_marks }})</label>
                  <input type="number" name="score" value="{{ $sub->score }}" min="0" max="{{ $assignment->max_marks }}" step="0.5" class="input text-sm w-24" required>
                </div>
                <div class="flex-1 min-w-48">
                  <label class="label text-xs">Feedback</label>
                  <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Optional remarks..." class="input text-sm w-full">
                </div>
                <button type="submit" class="btn-sm btn-primary mb-0.5">Save Evaluation</button>
                <button type="button" @click="open = false" class="btn-sm btn-secondary mb-0.5">Cancel</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="td text-center text-slate-400 py-8">No submissions yet</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $submissions->links() }}</div>
  </div>
</div>
@endsection
