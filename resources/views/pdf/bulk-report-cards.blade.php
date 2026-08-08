<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
  .card { page-break-after: always; padding: 24px 32px; }
  .card:last-child { page-break-after: auto; }
  .school-header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 16px; }
  .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
  .school-sub { font-size: 10px; color: #555; margin-top: 2px; }
  .report-title { text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 1px; margin: 10px 0; text-transform: uppercase; color: #1e3a5f; }
  .info-row { display: flex; gap: 12px; margin-bottom: 14px; }
  .info-box { flex: 1; background: #f0f4f8; border: 1px solid #d1dce8; border-radius: 4px; padding: 8px 12px; }
  .info-label { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
  .info-value { font-size: 12px; font-weight: bold; color: #1a1a1a; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  th { background: #1e3a5f; color: white; padding: 7px 10px; font-size: 10px; text-align: left; }
  td { padding: 6px 10px; border-bottom: 1px solid #e8eef4; font-size: 11px; }
  tr:nth-child(even) td { background: #f7f9fc; }
  .fail { color: #dc2626; font-weight: bold; }
  .summary-box { background: #1e3a5f; color: white; border-radius: 4px; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; }
  .summary-item { text-align: center; }
  .summary-number { font-size: 22px; font-weight: bold; }
  .summary-label { font-size: 9px; opacity: 0.8; margin-top: 2px; }
  .grade-pill { display: inline-block; padding: 2px 10px; border-radius: 10px; font-weight: bold; font-size: 12px; }
  .footer-note { font-size: 9px; color: #888; margin-top: 14px; border-top: 1px solid #ddd; padding-top: 8px; }
  .sig-row { display: flex; justify-content: space-between; margin-top: 28px; padding-top: 10px; }
  .sig-box { text-align: center; width: 140px; border-top: 1px solid #333; padding-top: 4px; font-size: 9px; color: #555; }
</style>
</head>
<body>
@foreach($reportData as $row)
<div class="card">
  <div class="school-header">
    <div class="school-name">{{ $school?->school_name ?? 'SCHOOL NAME' }}</div>
    <div class="school-sub">{{ $school?->address ?? '' }}{{ $school?->phone ? ' | ' . $school?->phone : '' }}</div>
  </div>
  <div class="report-title">Progress Report Card &mdash; {{ $exam->name }}</div>

  <div class="info-row">
    <div class="info-box">
      <div class="info-label">Student Name</div>
      <div class="info-value">{{ $row['enrollment']->student->full_name ?? ($row['enrollment']->student->first_name . ' ' . $row['enrollment']->student->last_name) }}</div>
    </div>
    <div class="info-box">
      <div class="info-label">Admission No</div>
      <div class="info-value">{{ $row['enrollment']->student->admission_no ?? $row['enrollment']->student->admission_number ?? '—' }}</div>
    </div>
    <div class="info-box">
      <div class="info-label">Class / Roll</div>
      <div class="info-value">{{ $row['enrollment']->class->name ?? '' }} &mdash; {{ $row['enrollment']->roll_number }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Subject</th>
        <th>Max Marks</th>
        <th>Marks Obtained</th>
        <th>Pass Marks</th>
        <th>Result</th>
        <th>Grade</th>
      </tr>
    </thead>
    <tbody>
      @foreach($row['marks'] as $mark)
      @php
        $passMarks  = $mark->examSchedule?->pass_marks ?? 35;
        $maxMarks   = $mark->examSchedule?->max_marks ?? 100;
        $isPassed   = $mark->is_absent ? false : $mark->marks_obtained >= $passMarks;
        $pct        = $maxMarks > 0 ? ($mark->marks_obtained / $maxMarks * 100) : 0;
        $grade = '—';
        if ($gradingScheme && !$mark->is_absent) {
            foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
                if ($pct >= $range->min_percentage) { $grade = $range->grade; break; }
            }
        }
      @endphp
      <tr>
        <td>{{ $mark->examSchedule?->subject?->name ?? '—' }}</td>
        <td>{{ $maxMarks }}</td>
        <td class="{{ !$isPassed && !$mark->is_absent ? 'fail' : '' }}">
          {{ $mark->is_absent ? 'Absent' : $mark->marks_obtained }}
        </td>
        <td>{{ $passMarks }}</td>
        <td class="{{ $isPassed ? '' : 'fail' }}">{{ $mark->is_absent ? 'AB' : ($isPassed ? 'Pass' : 'Fail') }}</td>
        <td>{{ $grade }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @php
    $percentage = $row['percentage'];
    $overallGrade = '—';
    if ($gradingScheme) {
        foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
            if ($percentage >= $range->min_percentage) { $overallGrade = $range->grade; break; }
        }
    }
  @endphp

  <div class="summary-box">
    <div class="summary-item">
      <div class="summary-number">{{ $row['obtained'] }}</div>
      <div class="summary-label">Marks Obtained</div>
    </div>
    <div class="summary-item">
      <div class="summary-number">{{ $row['totalMarks'] }}</div>
      <div class="summary-label">Total Marks</div>
    </div>
    <div class="summary-item">
      <div class="summary-number">{{ $percentage }}%</div>
      <div class="summary-label">Percentage</div>
    </div>
    <div class="summary-item">
      <div class="summary-number">{{ $overallGrade }}</div>
      <div class="summary-label">Overall Grade</div>
    </div>
    <div class="summary-item">
      <div class="summary-number">{{ $row['hasFail'] ? 'Fail' : 'Pass' }}</div>
      <div class="summary-label">Result</div>
    </div>
  </div>

  <div class="sig-row">
    <div class="sig-box">Class Teacher</div>
    <div class="sig-box">Principal</div>
    <div class="sig-box">Parent / Guardian</div>
  </div>

  <div class="footer-note">This report card was generated on {{ now()->format('d M Y') }}. Please keep this for your records.</div>
</div>
@endforeach
</body>
</html>
