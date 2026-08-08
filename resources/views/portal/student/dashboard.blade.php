@extends('portal.layout')
@section('title', 'Dashboard')
@section('content')
@php
  $statusVal  = $todayAtt?->status;
  $isPresent  = in_array($statusVal, ['present','late']);
  $isAbsent   = $statusVal === 'absent';
  $isLeave    = $statusVal === 'on_leave';
  $todayLabel = $isPresent ? ($statusVal === 'late' ? 'Late' : 'Present') : ($isAbsent ? 'Absent' : ($isLeave ? 'On Leave' : 'Not Marked'));
  $attBg      = $isPresent ? 'rgba(34,197,94,.22)' : ($isAbsent ? 'rgba(239,68,68,.22)' : ($isLeave ? 'rgba(59,130,246,.22)' : 'rgba(255,255,255,.1)'));
  $attFg      = $isPresent ? '#bbf7d0' : ($isAbsent ? '#fecaca' : ($isLeave ? '#bfdbfe' : 'rgba(255,255,255,.65)'));
  $attVal     = $attPct !== null ? (float) $attPct : null;
  $attBarClr  = ($attVal ?? 100) >= 75 ? '#22c55e' : (($attVal ?? 0) >= 50 ? '#f59e0b' : '#ef4444');
  $greeting   = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');
  $present    = (int)($attSummary['present'] ?? 0);
  $totalDays  = (int)($attSummary['total'] ?? 0);
  $hwCount    = $homework ? $homework->count() : 0;
  $nowTime    = now()->format('H:i:s');
  $pillColors = [
    ['#eff6ff','#2563eb'],['#f5f3ff','#7c3aed'],['#f0fdf4','#16a34a'],
    ['#fffbeb','#d97706'],['#fef2f2','#dc2626'],['#ecfeff','#0891b2'],
  ];
@endphp

<style>
  .db-hero{background:linear-gradient(135deg,#1d4ed8,#4f46e5 60%,#7c3aed);border-radius:1rem;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;margin-bottom:1.25rem;position:relative;overflow:hidden;box-shadow:0 8px 24px -8px rgba(79,70,229,.55);}
  .db-hero::before{content:'';position:absolute;right:-2rem;top:-2rem;width:9rem;height:9rem;border-radius:50%;background:rgba(255,255,255,.07);pointer-events:none;}
  .db-hero::after{content:'';position:absolute;right:4rem;bottom:-3.5rem;width:6rem;height:6rem;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;}
  .db-avatar{width:4rem;height:4rem;border-radius:50%;border:2.5px solid rgba(255,255,255,.35);flex-shrink:0;object-fit:cover;position:relative;z-index:1;}
  .db-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:.875rem;margin-bottom:1.25rem;}
  .db-stat{background:#fff;border-radius:.875rem;border:1px solid #e2e8f0;padding:1.125rem;display:flex;align-items:center;gap:.875rem;box-shadow:0 1px 3px rgba(0,0,0,.05);}
  .db-stat-icon{width:2.75rem;height:2.75rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .db-stat-val{font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;}
  .db-stat-lbl{font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-top:.2rem;}
  .db-grid{display:grid;grid-template-columns:1fr;gap:1rem;}
  .db-period{display:flex;align-items:center;gap:.75rem;padding:.625rem .5rem;border-radius:.625rem;border-left:3px solid transparent;}
  .db-period.now{background:#eff6ff;border-left-color:#2563eb;}
  @media(min-width:900px){.db-stats{grid-template-columns:repeat(4,1fr);}.db-grid{grid-template-columns:1fr 1fr;}}
  @media(min-width:640px){.db-hero-meta{display:flex !important;}}
</style>

{{-- HERO --}}
<div class="db-hero">
  @if($student->photo)
    <img src="{{ Storage::url($student->photo) }}" class="db-avatar">
  @else
    <div class="db-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;position:relative;z-index:1;">
      {{ strtoupper(substr($student->first_name ?? '?',0,1)) }}
    </div>
  @endif

  <div style="flex:1;min-width:0;position:relative;z-index:1;">
    <p style="color:rgba(255,255,255,.7);font-size:.8rem;margin-bottom:.1rem;">{{ $greeting }},</p>
    <h1 style="color:#fff;font-size:1.3rem;font-weight:800;line-height:1.2;letter-spacing:-.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->first_name }} {{ $student->last_name }}</h1>
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;margin-top:.4rem;">
      @if($enrollment)
        <span style="background:rgba(255,255,255,.16);color:#fff;font-size:.72rem;font-weight:600;padding:.2rem .625rem;border-radius:9999px;">
          {{ $enrollment->class_name }}{{ $enrollment->section_name ? ' · '.$enrollment->section_name : '' }}
        </span>
      @endif
      <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-size:.72rem;padding:.2rem .625rem;border-radius:9999px;">{{ $student->admission_no }}</span>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;flex-shrink:0;position:relative;z-index:1;" class="db-hero-meta">
    <span style="background:{{ $attBg }};color:{{ $attFg }};font-size:.8rem;font-weight:700;padding:.3rem .875rem;border-radius:9999px;">● {{ $todayLabel }}</span>
    <div style="text-align:right;">
      <p style="color:#fff;font-size:.9rem;font-weight:700;">{{ now()->format('l') }}</p>
      <p style="color:rgba(255,255,255,.6);font-size:.72rem;">{{ now()->format('d M Y') }}</p>
    </div>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="db-stats">

  <div class="db-stat">
    <div class="db-stat-icon" style="background:#eff6ff;">
      <svg style="width:1.375rem;height:1.375rem;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
    </div>
    <div style="flex:1;min-width:0;">
      <div class="db-stat-val">{{ $attVal !== null ? round($attVal).'%' : '—' }}</div>
      <div class="db-stat-lbl">Attendance</div>
      @if($attVal !== null)
        <div style="height:.3rem;border-radius:9999px;background:#e2e8f0;overflow:hidden;margin-top:.4rem;">
          <div style="height:100%;border-radius:9999px;width:{{ min(100,$attVal) }}%;background:{{ $attBarClr }};"></div>
        </div>
      @endif
    </div>
  </div>

  <div class="db-stat">
    <div class="db-stat-icon" style="background:{{ $feeBalance > 0 ? '#fef2f2' : '#f0fdf4' }};">
      <svg style="width:1.375rem;height:1.375rem;color:{{ $feeBalance > 0 ? '#dc2626' : '#16a34a' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    </div>
    <div style="min-width:0;">
      @if($feeBalance > 0)
        <div class="db-stat-val" style="color:#dc2626;">₹{{ number_format($feeBalance) }}</div>
        <div class="db-stat-lbl">Fee Due</div>
      @else
        <div class="db-stat-val" style="font-size:1.1rem;color:#16a34a;">All Clear</div>
        <div class="db-stat-lbl">Fee Status</div>
      @endif
    </div>
  </div>

  <div class="db-stat">
    <div class="db-stat-icon" style="background:#fffbeb;">
      <svg style="width:1.375rem;height:1.375rem;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
    </div>
    <div>
      <div class="db-stat-val">{{ $hwCount }}</div>
      <div class="db-stat-lbl">Homework Due</div>
    </div>
  </div>

  <div class="db-stat">
    <div class="db-stat-icon" style="background:#f5f3ff;">
      <svg style="width:1.375rem;height:1.375rem;color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <div>
      <div class="db-stat-val">{{ $present }}<span style="font-size:.875rem;font-weight:600;color:#94a3b8;">/{{ $totalDays }}</span></div>
      <div class="db-stat-lbl">Days Present</div>
    </div>
  </div>

</div>

{{-- CONTENT GRID --}}
<div class="db-grid">

  {{-- Today's Schedule --}}
  <div class="portal-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <div class="section-title" style="margin-bottom:0;">
        <svg style="width:1rem;height:1rem;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Today's Schedule
      </div>
      <span style="font-size:.75rem;color:#94a3b8;font-weight:500;">{{ now()->format('l, d M') }}</span>
    </div>
    @forelse($timetable as $period)
      @php
        $st = $period->start_time ? substr($period->start_time,0,8) : null;
        $et = $period->end_time   ? substr($period->end_time,0,8)   : null;
        $isCurrent = $st && $et && $nowTime >= $st && $nowTime <= $et;
      @endphp
      <div class="db-period {{ $isCurrent ? 'now' : '' }}">
        <div style="width:2.875rem;flex-shrink:0;text-align:right;">
          <div style="font-size:.75rem;font-weight:700;color:{{ $isCurrent ? '#2563eb' : '#475569' }};">{{ $period->start_time ? \Carbon\Carbon::parse($period->start_time)->format('H:i') : '' }}</div>
          <div style="font-size:.65rem;color:#94a3b8;">{{ $period->end_time ? \Carbon\Carbon::parse($period->end_time)->format('H:i') : '' }}</div>
        </div>
        <div style="width:2px;align-self:stretch;min-height:2rem;border-radius:9999px;background:{{ $isCurrent ? '#2563eb' : '#e2e8f0' }};flex-shrink:0;margin:0 .25rem;"></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
            <p style="font-size:.875rem;font-weight:600;color:#1e293b;">{{ $period->subject }}</p>
            @if($isCurrent)<span class="badge-blue">Now</span>@endif
          </div>
        </div>
      </div>
    @empty
      <div style="text-align:center;padding:2rem 0;color:#94a3b8;">
        <svg style="width:2rem;height:2rem;margin:0 auto .5rem;opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p style="font-size:.875rem;">No classes scheduled today</p>
      </div>
    @endforelse
  </div>

  {{-- Pending Homework --}}
  <div class="portal-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <div class="section-title" style="margin-bottom:0;">
        <svg style="width:1rem;height:1rem;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        Homework Due
      </div>
      <a href="{{ route('portal.student.academics') }}" class="link-sm">View all</a>
    </div>
    @forelse($homework as $hw)
      @php
        $due = \Carbon\Carbon::parse($hw->due_date);
        $urgBg  = $due->isToday() ? '#fef2f2' : ($due->isTomorrow() ? '#fffbeb' : '#f8fafc');
        $urgFg  = $due->isToday() ? '#dc2626' : ($due->isTomorrow() ? '#d97706' : '#64748b');
        $urgLbl = $due->isToday() ? 'Today' : ($due->isTomorrow() ? 'Tomorrow' : $due->format('d M'));
        $pc     = $pillColors[abs(crc32($hw->subject_name ?? 'x')) % count($pillColors)];
      @endphp
      <div class="divider-row" style="display:flex;align-items:flex-start;gap:.75rem;padding:.625rem 0;">
        <div style="flex:1;min-width:0;">
          @if($hw->subject_name)
            <span style="font-size:.67rem;font-weight:700;padding:.15rem .5rem;border-radius:9999px;background:{{ $pc[0] }};color:{{ $pc[1] }};display:inline-block;margin-bottom:.25rem;">{{ $hw->subject_name }}</span>
          @endif
          <p style="font-size:.875rem;font-weight:500;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $hw->title }}</p>
        </div>
        <span style="font-size:.72rem;font-weight:700;color:{{ $urgFg }};background:{{ $urgBg }};padding:.2rem .5rem;border-radius:.375rem;flex-shrink:0;white-space:nowrap;">{{ $urgLbl }}</span>
      </div>
    @empty
      <div style="text-align:center;padding:2rem 0;">
        <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;">
          <svg style="width:1.375rem;height:1.375rem;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p style="font-size:.875rem;font-weight:600;color:#16a34a;">All caught up!</p>
      </div>
    @endforelse
  </div>

  {{-- Recent Exam Results --}}
  <div class="portal-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <div class="section-title" style="margin-bottom:0;">
        <svg style="width:1rem;height:1rem;color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Recent Results
      </div>
      <a href="{{ route('portal.student.exams') }}" class="link-sm">All results</a>
    </div>
    @forelse($recentMarks->take(5) as $m)
      @php
        $pct = $m->max_marks > 0 ? round($m->marks_obtained / $m->max_marks * 100) : 0;
        $pctClr = $pct >= 75 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#dc2626');
        $pctBg  = $pct >= 75 ? '#f0fdf4' : ($pct >= 50 ? '#fffbeb' : '#fef2f2');
      @endphp
      <div class="divider-row" style="display:flex;align-items:center;gap:.75rem;padding:.625rem 0;">
        <div style="flex:1;min-width:0;">
          <p style="font-size:.875rem;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $m->subject }}</p>
          <p style="font-size:.72rem;color:#94a3b8;">{{ $m->exam_name }}</p>
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div style="font-size:.875rem;font-weight:700;color:#1e293b;">{{ $m->marks_obtained }}<span style="font-size:.75rem;color:#94a3b8;">/{{ $m->max_marks }}</span></div>
          <div style="font-size:.72rem;font-weight:700;color:{{ $pctClr }};background:{{ $pctBg }};padding:.1rem .375rem;border-radius:.375rem;display:inline-block;">{{ $pct }}%</div>
        </div>
      </div>
    @empty
      <p style="font-size:.875rem;color:#94a3b8;text-align:center;padding:2rem 0;">No results yet</p>
    @endforelse
  </div>

  {{-- Notices --}}
  <div class="portal-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <div class="section-title" style="margin-bottom:0;">
        <svg style="width:1rem;height:1rem;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        Latest Notices
      </div>
      <a href="{{ route('portal.student.notices') }}" class="link-sm">All notices</a>
    </div>
    @forelse($notices as $n)
      <div class="divider-row" style="padding:.625rem 0;display:flex;align-items:flex-start;gap:.625rem;">
        <div style="width:.375rem;height:.375rem;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:.4rem;"></div>
        <div style="flex:1;min-width:0;">
          @if(isset($n->priority) && $n->priority === 'urgent')
            <span class="badge-red" style="font-size:.65rem;margin-bottom:.2rem;">Urgent</span>
          @endif
          <p style="font-size:.875rem;font-weight:500;color:#1e293b;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $n->title }}</p>
          <p style="font-size:.72rem;color:#94a3b8;margin-top:.15rem;">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</p>
        </div>
      </div>
    @empty
      <p style="font-size:.875rem;color:#94a3b8;text-align:center;padding:2rem 0;">No notices right now</p>
    @endforelse
  </div>

</div>
@endsection
