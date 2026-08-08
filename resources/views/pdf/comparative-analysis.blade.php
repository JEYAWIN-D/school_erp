<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 16px; }
  .school-name { font-size: 15px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 12px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 9px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #1e40af; color: #fff; padding: 5px 6px; text-align: center; font-size: 9px; }
  th.left { text-align: left; }
  td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; text-align: center; }
  td.left { text-align: left; }
  tr:nth-child(even) td { background: #f8fafc; }
  .above { color: #16a34a; font-weight: bold; }
  .below { color: #dc2626; font-weight: bold; }
  .school-row td { background: #dbeafe; font-weight: bold; color: #1e40af; }
  .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="report-title">Comparative Analysis — {{ $exam->name }}</div>
  <div class="meta">School Overall Average: {{ $schoolAvg ?? '—' }}% &nbsp;|&nbsp; Generated: {{ now()->format('d M Y H:i') }}</div>
</div>

<table>
  <thead>
    <tr>
      <th class="left">Class</th>
      <th>Overall Avg</th>
      <th>vs School Avg</th>
      @foreach(array_keys($subjectSchoolAvg) as $subj)
        <th>{{ $subj }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($classData as $row)
    @php $diff = round($row['overall_avg'] - ($schoolAvg ?? 0), 1); @endphp
    <tr>
      <td class="left"><strong>{{ $row['class']->name }}</strong></td>
      <td><strong>{{ $row['overall_avg'] }}%</strong></td>
      <td class="{{ $diff >= 0 ? 'above' : 'below' }}">{{ $diff >= 0 ? '+' : '' }}{{ $diff }}%</td>
      @foreach(array_keys($subjectSchoolAvg) as $subj)
      @php
        $cAvg = $row['subject_avgs'][$subj] ?? null;
        $sAvg = $subjectSchoolAvg[$subj];
      @endphp
      <td class="{{ $cAvg !== null ? ($cAvg >= $sAvg ? 'above' : 'below') : '' }}">
        {{ $cAvg ?? '—' }}
      </td>
      @endforeach
    </tr>
    @endforeach
    @if(!empty($subjectSchoolAvg))
    <tr class="school-row">
      <td class="left">School Average</td>
      <td>{{ $schoolAvg ?? '—' }}%</td>
      <td>—</td>
      @foreach($subjectSchoolAvg as $avg)
        <td>{{ $avg }}</td>
      @endforeach
    </tr>
    @endif
  </tbody>
</table>

<div class="footer">
  Green = above school average | Red = below school average &nbsp;|&nbsp;
  {{ $school?->school_name ?? '' }} | Printed: {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
