@extends('portal.layout')
@section('title', 'Academics')
@section('content')

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Academics</h2>

{{-- Homework --}}
<div class="portal-card" style="margin-bottom: 1rem;">
  <div class="section-title">
    <svg style="width:1rem;height:1rem;color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Homework & Assignments
  </div>
  @forelse($homework as $hw)
  @php
    $due = \Carbon\Carbon::parse($hw->due_date);
    $isToday    = $due->isToday();
    $isTomorrow = $due->isTomorrow();
    $isPast     = $due->isPast() && !$isToday;
    $dueColor   = $isPast ? '#dc2626' : ($isToday ? '#dc2626' : ($isTomorrow ? '#d97706' : '#64748b'));
  @endphp
  <div class="divider-row" style="display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; padding: .625rem 0;">
    <div style="flex: 1; min-width: 0;">
      <p style="font-size: .875rem; font-weight: 500; color: #1e293b; margin-bottom: .15rem;">{{ $hw->title }}</p>
      @if($hw->description ?? null)
        <p style="font-size: .75rem; color: #94a3b8; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $hw->description }}</p>
      @endif
      @if($hw->subject_name ?? null)
        <span class="badge-slate" style="margin-top: .25rem;">{{ $hw->subject_name }}</span>
      @endif
    </div>
    <div style="flex-shrink: 0; text-align: right;">
      <span style="font-size: .75rem; font-weight: 600; color: {{ $dueColor }}; white-space: nowrap;">
        @if($isPast) Overdue @elseif($isToday) Due Today @elseif($isTomorrow) Due Tomorrow @else Due {{ $due->format('d M') }} @endif
      </span>
    </div>
  </div>
  @empty
    <div style="text-align: center; padding: 1.5rem 0; color: #94a3b8;">
      <svg style="width:2.5rem;height:2.5rem;margin: 0 auto .5rem;opacity:.4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
      <p style="font-size: .875rem;">All caught up — no pending homework</p>
    </div>
  @endforelse
  @if($homework->hasPages())
    <div style="margin-top: .875rem;">{{ $homework->links() }}</div>
  @endif
</div>

{{-- LMS Assignments --}}
@if($assignments->count())
<div class="portal-card" style="margin-bottom: 1rem;">
  <div class="section-title" style="margin-bottom: .875rem;">
    <svg style="width:1rem;height:1rem;color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
    Assignment Submissions
  </div>

  @if(session('assignment_success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.625rem;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
      <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      {{ session('assignment_success') }}
    </div>
  @endif

  @foreach($assignments as $asgn)
  @php
    $due = \Carbon\Carbon::parse($asgn->due_at);
    $isOverdue = $due->isPast();
    $submitted = $asgn->submitted_at ?? null;
  @endphp
  <div class="divider-row" style="padding: .875rem 0;">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
      <div style="flex: 1; min-width: 200px;">
        <p style="font-size: .875rem; font-weight: 600; color: #1e293b; margin-bottom: .2rem;">{{ $asgn->title }}</p>
        <p style="font-size: .75rem; color: #94a3b8; margin-bottom: .375rem;">{{ $asgn->course_title ?? 'Course' }}</p>
        @if($asgn->instructions)
          <p style="font-size: .75rem; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $asgn->instructions }}</p>
        @endif
        <div style="margin-top: .375rem; display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;">
          <span style="font-size: .72rem; color: {{ $isOverdue && !$submitted ? '#dc2626' : '#94a3b8' }}; font-weight: 500;">
            Due: {{ $due->format('d M Y, g:i A') }}{{ $isOverdue ? ' (Overdue)' : '' }}
          </span>
          <span style="font-size: .72rem; color: #94a3b8;">Max: {{ $asgn->max_marks }}</span>
        </div>
      </div>
      <div style="flex-shrink: 0;">
        @if($submitted)
          <div style="text-align: right;">
            <span class="badge-green">Submitted</span>
            <p style="font-size: .72rem; color: #94a3b8; margin-top: .25rem;">{{ \Carbon\Carbon::parse($submitted)->format('d M, g:i A') }}</p>
            @if($asgn->score !== null) <p style="font-size: .75rem; font-weight: 700; color: #1e293b; margin-top: .2rem;">Score: {{ $asgn->score }}/{{ $asgn->max_marks }}</p> @endif
          </div>
        @else
          <button onclick="document.getElementById('asgn-form-{{ $asgn->id }}').style.display = document.getElementById('asgn-form-{{ $asgn->id }}').style.display === 'none' ? 'block' : 'none'"
                  style="padding:.4rem .875rem;background:#6366f1;color:#fff;border:none;border-radius:.5rem;font-size:.8125rem;font-weight:600;cursor:pointer; white-space:nowrap;">
            Submit
          </button>
        @endif
      </div>
    </div>
    @if(!$submitted)
    <div id="asgn-form-{{ $asgn->id }}" style="display: none; margin-top: .875rem; padding-top: .875rem; border-top: 1px solid #f1f5f9;">
      <form method="POST" action="{{ route('portal.academics.submit-assignment') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="assignment_id" value="{{ $asgn->id }}">
        <div style="display: grid; grid-template-columns: 1fr; gap: .625rem;">
          <div>
            <label style="display:block;font-size:.72rem;font-weight:600;color:#475569;margin-bottom:.3rem;">Note / Answer (optional)</label>
            <textarea name="note" rows="3"
                      style="width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.5rem .75rem;font-size:.8125rem;color:#1e293b;outline:none;resize:vertical;box-sizing:border-box;"
                      onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"
                      placeholder="Type your answer or notes here…"></textarea>
          </div>
          <div>
            <label style="display:block;font-size:.72rem;font-weight:600;color:#475569;margin-bottom:.3rem;">Attachment (PDF/image, max 10MB)</label>
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                   style="width:100%;font-size:.8125rem;color:#475569;">
          </div>
          <div>
            <button type="submit"
                    style="padding:.5rem 1.25rem;background:#6366f1;color:#fff;border:none;border-radius:.5rem;font-size:.8125rem;font-weight:600;cursor:pointer;"
                    onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
              Submit Assignment
            </button>
          </div>
        </div>
      </form>
    </div>
    @endif
  </div>
  @endforeach
</div>
@endif

{{-- LMS Courses --}}
@if($courses->count())
<h3 style="font-size: .9375rem; font-weight: 600; color: #1e293b; margin-bottom: .875rem; display: flex; align-items: center; gap: .5rem;">
  <svg style="width:1rem;height:1rem;color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
  My Courses
</h3>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
  @foreach($courses as $course)
  @php $progress = method_exists($course, 'progressFor') ? $course->progressFor($student->id) : 0; @endphp
  <a href="{{ route('portal.student.course', $course->id) }}" class="portal-card" style="text-decoration: none; display: block; transition: box-shadow .15s;"
     onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
    @if($course->thumbnail ?? null)
      <img src="{{ Storage::url($course->thumbnail) }}" style="width: 100%; height: 8rem; object-fit: cover; border-radius: .75rem; margin-bottom: .875rem;">
    @else
      <div style="width: 100%; height: 6rem; background: linear-gradient(135deg, #6366f1, #3b82f6); border-radius: .75rem; margin-bottom: .875rem; display: flex; align-items: center; justify-content: center;">
        <svg style="width:2rem;height:2rem;color:rgba(255,255,255,.6)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      </div>
    @endif
    <h4 style="font-size: .875rem; font-weight: 600; color: #1e293b; margin-bottom: .5rem; line-height: 1.4;">{{ $course->title }}</h4>
    <div style="display: flex; align-items: center; gap: .5rem;">
      <div class="progress-bar" style="flex: 1;">
        <div class="progress-fill" style="width: {{ $progress }}%; background: #3b82f6;"></div>
      </div>
      <span style="font-size: .72rem; color: #64748b; flex-shrink: 0;">{{ $progress }}%</span>
    </div>
  </a>
  @endforeach
</div>
@endif

@endsection
