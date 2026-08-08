<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
.header { text-align: center; border-bottom: 2px solid #2c5282; padding-bottom: 8px; margin-bottom: 8px; }
.school-name { font-size: 16px; font-weight: bold; color: #2c5282; }
.report-title { font-size: 13px; margin-top: 3px; }
.info-grid { display: table; width: 100%; margin: 8px 0; }
.info-row { display: table-row; }
.info-cell { display: table-cell; padding: 3px 8px; border: 1px solid #ddd; }
.label { font-weight: bold; background: #f0f4ff; width: 25%; }
table { width: 100%; border-collapse: collapse; margin: 8px 0; }
th { background: #2c5282; color: white; padding: 5px; text-align: center; font-size: 10px; }
td { padding: 4px 6px; border: 1px solid #ddd; text-align: center; font-size: 11px; }
tr:nth-child(even) td { background: #f7faff; }
.pass { color: #059669; font-weight: bold; }
.fail { color: #dc2626; font-weight: bold; }
.section-title { background: #e8f0fe; padding: 4px 8px; font-weight: bold; margin: 8px 0 4px; border-left: 3px solid #2c5282; }
.footer { margin-top: 20px; display: flex; justify-content: space-between; }
.sign-box { text-align: center; width: 30%; }
.sign-line { border-top: 1px solid #333; margin-top: 30px; padding-top: 4px; font-size: 10px; }
.summary-box { background: #f0fff4; border: 1px solid #a7f3d0; padding: 8px; margin: 8px 0; display: flex; justify-content: space-around; }
.summary-item { text-align: center; }
.summary-value { font-size: 18px; font-weight: bold; color: #065f46; }
.summary-label { font-size: 10px; color: #6b7280; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school->school_name ?? 'DASA EduERP' }}</div>
  <div style="font-size:11px;color:#666">{{ $school->address ?? '' }}</div>
  <div class="report-title">PROGRESS REPORT — {{ $exam->name }}</div>
  <div style="font-size:11px">Academic Year: {{ $academicYear->name ?? '' }}</div>
</div>

<table style="border:none;margin:0">
  <tr style="border:none">
    <td style="border:none;padding:0;vertical-align:top;width:75%">
      <table style="margin:0">
        <tr><td class="label">Student Name</td><td>{{ $student->full_name }}</td><td class="label">Admission No</td><td>{{ $student->admission_number }}</td></tr>
        <tr><td class="label">Class</td><td>{{ $enrollment?->class?->name }} {{ $enrollment?->section?->name }}</td><td class="label">Roll No</td><td>{{ $enrollment?->roll_number }}</td></tr>
        <tr><td class="label">Date of Birth</td><td>{{ $student->date_of_birth?->format('d/m/Y') }}</td><td class="label">Attendance</td><td>{{ $attendancePct ?? '--' }}% ({{ $presentDays ?? '--' }}/{{ $totalDays ?? '--' }} days)</td></tr>
      </table>
    </td>
  </tr>
</table>

<div class="section-title">Academic Performance</div>
<table>
  <thead><tr>
    <th>Subject</th><th>Max Marks</th><th>Marks Obtained</th><th>Pass Marks</th><th>Grade</th><th>Status</th>
  </tr></thead>
  <tbody>
    @foreach($marks as $m)
    <tr>
      <td style="text-align:left">{{ $m->examSchedule?->subject?->name }}</td>
      <td>{{ $m->examSchedule?->max_marks }}</td>
      <td>{{ $m->is_absent ? 'Absent' : $m->marks_obtained }}</td>
      <td>{{ $m->examSchedule?->pass_marks }}</td>
      <td>{{ $m->grade ?? '--' }}</td>
      <td class="{{ (!$m->is_absent && $m->marks_obtained >= $m->examSchedule?->pass_marks) ? 'pass' : 'fail' }}">
        {{ $m->is_absent ? 'Absent' : (($m->marks_obtained >= $m->examSchedule?->pass_marks) ? 'Pass' : 'Fail') }}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="summary-box">
  <div class="summary-item"><div class="summary-value">{{ $totalMarks }}</div><div class="summary-label">Total Marks</div></div>
  <div class="summary-item"><div class="summary-value">{{ $percentage }}%</div><div class="summary-label">Percentage</div></div>
  <div class="summary-item"><div class="summary-value">{{ $overallGrade }}</div><div class="summary-label">Grade</div></div>
  <div class="summary-item"><div class="summary-value {{ $result === 'Pass' ? 'pass' : 'fail' }}">{{ $result }}</div><div class="summary-label">Result</div></div>
  <div class="summary-item"><div class="summary-value">{{ $rank }}</div><div class="summary-label">Class Rank</div></div>
</div>

@if(isset($coscholasticMarks) && $coscholasticMarks->count())
<div class="section-title">Co-Scholastic Activities</div>
<table>
  <thead><tr><th style="text-align:left">Activity / Subject</th><th>Grade</th><th>Remarks</th></tr></thead>
  <tbody>
    @foreach($coscholasticMarks as $cm)
    <tr>
      <td style="text-align:left">{{ $cm->examSchedule?->subject?->name }}</td>
      <td><strong>{{ $cm->grade ?? '—' }}</strong></td>
      <td>{{ $cm->remarks ?? '' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif

@if(isset($cumulativeData) && $cumulativeData->count())
<div class="section-title">Cumulative / Term-wise Performance</div>
<table>
  <thead><tr><th>Term</th><th>Weightage %</th><th>Term %</th><th>Weighted Score</th></tr></thead>
  <tbody>
    @foreach($cumulativeData as $row)
    <tr>
      <td style="text-align:left">{{ $row['label'] }}</td>
      <td>{{ $row['weightage'] }}%</td>
      <td>{{ $row['percentage'] }}%</td>
      <td>{{ $row['weighted'] }}</td>
    </tr>
    @endforeach
    <tr style="background:#e8f0fe;font-weight:bold">
      <td colspan="3" style="text-align:right">Cumulative Total</td>
      <td>{{ number_format($cumulativeTotal ?? 0, 2) }}</td>
    </tr>
  </tbody>
</table>
@endif

@if($teacherRemarks ?? null)
<div style="margin:8px 0;padding:6px 10px;background:#fffbeb;border:1px solid #fde68a;font-size:11px">
  <strong>Class Teacher's Remarks:</strong> {{ $teacherRemarks }}
</div>
@endif
@if($principalRemarks ?? null)
<div style="margin:8px 0;padding:6px 10px;background:#f0f9ff;border:1px solid #bae6fd;font-size:11px">
  <strong>Principal's Remarks:</strong> {{ $principalRemarks }}
</div>
@endif

@if(isset($subjectChart) && $subjectChart->isNotEmpty())
@php
  $chartCount = $subjectChart->count();
  $barW = 14;
  $gap  = 4;
  $svgW = $chartCount * ($barW + $gap) + 20;
  $svgH = 90;
  $maxH = 60;
@endphp
<div class="section-title">Subject-wise Performance Chart</div>
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $svgW }}" height="{{ $svgH }}" style="display:block;margin:4px 0 8px;">
  {{-- Baseline --}}
  <line x1="10" y1="{{ $maxH + 5 }}" x2="{{ $svgW - 5 }}" y2="{{ $maxH + 5 }}" stroke="#cbd5e1" stroke-width="1"/>
  {{-- Pass threshold line at 35% --}}
  @php $threshY = $maxH + 5 - round(35 * $maxH / 100); @endphp
  <line x1="10" y1="{{ $threshY }}" x2="{{ $svgW - 5 }}" y2="{{ $threshY }}" stroke="#fca5a5" stroke-width="0.5" stroke-dasharray="2,2"/>
  @foreach($subjectChart as $i => $s)
    @php
      $x    = 10 + $i * ($barW + $gap);
      $bh   = $s['absent'] ? 4 : max(2, round($s['pct'] * $maxH / 100));
      $by   = $maxH + 5 - $bh;
      $fill = $s['absent'] ? '#94a3b8' : ($s['pass'] ? '#10b981' : '#ef4444');
    @endphp
    <rect x="{{ $x }}" y="{{ $by }}" width="{{ $barW }}" height="{{ $bh }}" fill="{{ $fill }}" rx="2"/>
    @if(!$s['absent'])
    <text x="{{ $x + $barW/2 }}" y="{{ $by - 2 }}" text-anchor="middle" font-size="7" fill="#475569">{{ $s['pct'] }}%</text>
    @endif
    {{-- Truncated subject label rotated --}}
    <text x="{{ $x + $barW/2 }}" y="{{ $maxH + 14 }}" text-anchor="middle" font-size="6.5" fill="#64748b"
          transform="rotate(-40, {{ $x + $barW/2 }}, {{ $maxH + 14 }})">{{ $s['name'] }}</text>
  @endforeach
  <text x="6" y="{{ $threshY - 1 }}" font-size="6" fill="#ef4444">35%</text>
</svg>
@endif

<div class="footer">
  <div class="sign-box">
    @if(isset($signatureBase64) && $signatureBase64)
      <img src="{{ $signatureBase64 }}" style="height:36px;margin-bottom:2px;">
    @endif
    <div class="sign-line">Class Teacher</div>
  </div>
  <div class="sign-box">
    <div class="sign-line" style="margin-top:38px;">Parent/Guardian</div>
  </div>
  <div class="sign-box">
    @if(isset($stampBase64) && $stampBase64)
      <img src="{{ $stampBase64 }}" style="height:36px;margin-bottom:2px;opacity:0.85;">
    @endif
    <div class="sign-line">Principal</div>
  </div>
</div>
</body>
</html>
