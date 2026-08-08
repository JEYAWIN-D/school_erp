@extends('layouts.app')
@section('title','Lesson Plans')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Lesson Plans</h1>
    <button x-data @click="$dispatch('open-modal','add-lesson-plan')" class="btn btn-primary btn-sm">+ New Plan</button>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject</label>
        <select name="subject_id" class="select w-36">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Status</label>
        <select name="status" class="select w-32">
          <option value="">All</option>
          @foreach(['draft'=>'Draft','submitted'=>'Submitted','approved'=>'Approved','rejected'=>'Rejected'] as $v=>$l)
          <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Topic','Class/Subject','Date','Teacher','Status','HOD Remarks','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($plans as $plan)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800">{{ $plan->topic }}</p>
            @if($plan->learning_objectives)
            <p class="text-xs text-slate-400 truncate max-w-xs">{{ Str::limit($plan->learning_objectives, 60) }}</p>
            @endif
            @if($plan->is_completed)<span class="badge-green text-xs">Completed</span>@endif
          </td>
          <td class="px-4 py-3 text-xs text-slate-600">
            {{ $plan->class?->name }}<br>{{ $plan->subject?->name }}
          </td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $plan->plan_date?->format('d M Y') }}</td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $plan->createdBy?->name }}</td>
          <td class="px-4 py-3">
            @php $statusColors = ['draft'=>'badge-slate','submitted'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red']; @endphp
            <span class="{{ $statusColors[$plan->status] ?? 'badge-slate' }} text-xs capitalize">{{ $plan->status }}</span>
          </td>
          <td class="px-4 py-3 text-xs text-slate-500 max-w-xs">{{ $plan->hod_remarks ?? '—' }}</td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1" x-data="{review:false}">
              @if($plan->status === 'submitted')
              <button @click="review=!review" class="btn btn-secondary btn-xs">Review</button>
              <div x-show="review" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="review=false">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
                  <h3 class="font-semibold">HOD Review — {{ $plan->topic }}</h3>
                  <form method="POST" action="{{ route('academics.lesson-plans.review', $plan->id) }}" class="space-y-3">
                    @csrf
                    <div>
                      <label class="label">Decision</label>
                      <select name="decision" class="select" required>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                      </select>
                    </div>
                    <div>
                      <label class="label">Remarks</label>
                      <textarea name="hod_remarks" rows="3" class="input" placeholder="Feedback for teacher..."></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                      <button type="button" @click="review=false" class="btn btn-secondary btn-sm">Cancel</button>
                      <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                    </div>
                  </form>
                </div>
              </div>
              @endif
              @if($plan->status === 'approved' && !$plan->is_completed)
              <form method="POST" action="{{ route('academics.lesson-plans.complete', $plan->id) }}" class="inline">
                @csrf <button class="btn btn-ghost btn-xs text-green-600">✓ Done</button>
              </form>
              @endif
              <a href="{{ route('academics.lesson-plans.pdf', $plan->id) }}" target="_blank" class="btn btn-ghost btn-xs" title="Print PDF">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No lesson plans found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($plans->hasPages())
    <div class="px-4 pb-3">{{ $plans->links() }}</div>
    @endif
  </div>
</div>

{{-- New Lesson Plan Modal --}}
<div x-data="{open:false}" @open-modal.window="if($event.detail==='add-lesson-plan')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">New Lesson Plan</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="{{ route('academics.lesson-plans.store') }}" class="space-y-3">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label text-xs">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select Class</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select Subject</option>
            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
          </select>
        </div>
        @if($syllabusTopics->count())
        <div class="col-span-2">
          <label class="label text-xs">Link to Syllabus Topic</label>
          <select name="syllabus_id" class="select">
            <option value="">— None —</option>
            @foreach($syllabusTopics as $st)
            <option value="{{ $st->id }}">{{ $st->topic }}</option>
            @endforeach
          </select>
        </div>
        @endif
        <div class="col-span-2">
          <label class="label text-xs">Topic <span class="text-red-500">*</span></label>
          <input type="text" name="topic" class="input" required placeholder="Lesson topic">
        </div>
        <div>
          <label class="label text-xs">Plan Date <span class="text-red-500">*</span></label>
          <input type="date" name="plan_date" class="input" value="{{ today()->toDateString() }}" required>
        </div>
        <div>
          <label class="label text-xs">Duration (mins)</label>
          <input type="number" name="duration_minutes" class="input" value="45" min="1">
        </div>
        <div>
          <label class="label text-xs">Period No.</label>
          <input type="text" name="period_number" class="input" placeholder="e.g. 3">
        </div>
        <div>
          <label class="label text-xs">Teaching Method</label>
          <input type="text" name="teaching_method" class="input" placeholder="e.g. Discussion, Demo">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Learning Objectives</label>
          <textarea name="learning_objectives" rows="2" class="input"></textarea>
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Resources Required</label>
          <input type="text" name="resources_required" class="input" placeholder="Textbook, chart, lab equipment...">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Teacher Notes</label>
          <textarea name="teacher_notes" rows="2" class="input" placeholder="Internal notes (not shown in review)"></textarea>
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit for Review</button>
      </div>
    </form>
  </div>
</div>
@endsection
