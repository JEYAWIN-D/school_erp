<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #111; margin: 0; padding: 20px 30px; background: #fff; }
.header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #1e3a8a; padding-bottom: 12px; }
.school-name { font-size: 18px; font-weight: bold; color: #1e3a8a; }
.doc-title   { font-size: 13px; font-weight: bold; margin-top: 4px; }
table { width: 100%; border-collapse: collapse; }
th { background: #1e3a8a; color: white; padding: 6px 8px; text-align: center; font-size: 9px; letter-spacing: 0.5px; }
td { padding: 5px 6px; border: 1px solid #e2e8f0; font-size: 9px; vertical-align: top; }
tr:nth-child(even) td { background: #f8fafc; }
.period-cell { background: #f0f4ff; font-weight: bold; text-align: center; color: #1e3a8a; }
.subject { font-weight: bold; color: #1e40af; }
.teacher  { font-size: 8px; color: #64748b; }
.footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name">{{ $school->school_name ?? config('app.name') }}</div>
  <div class="doc-title">CLASS TIMETABLE — {{ $class->name }}</div>
</div>

@php
$days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
$maxPeriods = 0;
foreach($timetable as $entries) { $maxPeriods = max($maxPeriods, $entries->count()); }
@endphp

<table>
  <thead>
    <tr>
      <th style="width:80px;">Day</th>
      @for($p = 1; $p <= max($maxPeriods, 8); $p++)
        <th>P{{ $p }}</th>
      @endfor
    </tr>
  </thead>
  <tbody>
    @foreach($days as $dayNum => $dayName)
      @php $dayEntries = $timetable[$dayNum] ?? collect(); @endphp
      <tr>
        <td class="period-cell">{{ $dayName }}</td>
        @for($p = 1; $p <= max($maxPeriods, 8); $p++)
          @php $entry = $dayEntries->firstWhere('period_number', $p); @endphp
          <td>
            @if($entry)
              <div class="subject">{{ $entry->subject?->name ?? '—' }}</div>
              <div class="teacher">{{ $entry->teacher?->first_name ? ($entry->teacher->first_name . ' ' . substr($entry->teacher->last_name,0,1) . '.') : '' }}</div>
              @if($entry->start_time) <div style="font-size:7px;color:#94a3b8;">{{ $entry->start_time }}–{{ $entry->end_time }}</div> @endif
            @endif
          </td>
        @endfor
      </tr>
    @endforeach
  </tbody>
</table>

<div class="footer">Generated: {{ now()->format('d M Y h:i A') }}</div>

</body>
</html>
