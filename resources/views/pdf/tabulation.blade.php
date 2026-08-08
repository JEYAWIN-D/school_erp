<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a; }
  .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; }
  .school-name { font-size: 15px; font-weight: bold; color: #1e3a5f; }
  .report-title { font-size: 12px; font-weight: bold; margin-top: 4px; }
  .sub { font-size: 9px; color: #555; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e3a5f; color: white; padding: 5px 6px; font-size: 8px; text-align: center; border: 1px solid #15305a; }
  td { padding: 4px 6px; border: 1px solid #d1dce8; font-size: 9px; text-align: center; }
  .name-col { text-align: left; }
  tr:nth-child(even) td { background: #f7f9fc; }
  .fail { color: #dc2626; font-weight: bold; }
  .top3 { background: #fef9c3 !important; }
  .footer { margin-top: 14px; font-size: 8px; color: #888; display: flex; justify-content: space-between; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'SCHOOL NAME' }}</div>
  <div class="report-title">Tabulation Sheet &mdash; {{ $exam->name }}</div>
  <div class="sub">Class: {{ $class->name }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y H:i') }}</div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Adm No</th>
      <th class="name-col">Student Name</th>
      @foreach($subjects as $subject)
        <th>{{ Str::limit($subject->name, 10) }}<br>({{ $subject->pivot->max_marks ?? 100 }})</th>
      @endforeach
      <th>Total</th>
      <th>%</th>
      <th>Grade</th>
      <th>Rank</th>
      <th>Result</th>
    </tr>
  </thead>
  <tbody>
    @foreach($enrollments as $enrollment)
    @php
      $rowMarks   = $marksMap[$enrollment->student_id] ?? [];
      $total      = $totals[$enrollment->id] ?? 0;
      $maxTotal   = $subjects->sum(fn($s) => $s->pivot->max_marks ?? 100);
      $percentage = $maxTotal > 0 ? round($total / $maxTotal * 100, 1) : 0;
      $hasFail    = false;
      foreach ($rowMarks as $m) {
          if (!$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)) { $hasFail = true; break; }
      }
      $grade = '—';
      if ($gradingScheme) {
          foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
              if ($percentage >= $range->min_percentage) { $grade = $range->grade; break; }
          }
      }
      $rank = $ranks[$enrollment->id] ?? '—';
    @endphp
    <tr class="{{ $rank <= 3 ? 'top3' : '' }}">
      <td>{{ $loop->iteration }}</td>
      <td>{{ $enrollment->student->admission_no ?? $enrollment->student->admission_number ?? '—' }}</td>
      <td class="name-col">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
      @foreach($subjects as $subject)
        @php $m = $rowMarks[$subject->id] ?? null; @endphp
        <td class="{{ $m && !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35) ? 'fail' : '' }}">
          {{ $m ? ($m->is_absent ? 'AB' : $m->marks_obtained) : '—' }}
        </td>
      @endforeach
      <td><strong>{{ $total }}</strong></td>
      <td>{{ $percentage }}%</td>
      <td>{{ $grade }}</td>
      <td>{{ $rank }}</td>
      <td class="{{ $hasFail ? 'fail' : '' }}">{{ $hasFail ? 'F' : 'P' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="footer">
  <span>Total Students: {{ $enrollments->count() }}</span>
  <span>Passed: {{ collect($enrollments)->filter(fn($e) => !collect($marksMap[$e->student_id] ?? [])->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)))->count() }}</span>
  <span>Failed: {{ collect($enrollments)->filter(fn($e) => collect($marksMap[$e->student_id] ?? [])->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)))->count() }}</span>
  <span>Class Average: {{ $enrollments->count() > 0 ? round(collect($totals)->avg(), 1) : 0 }}</span>
  <span>Examiner/Principal: ____________________</span>
</div>
</body>
</html>
