<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 2px 4px; text-align: center; }
    th { background: #f1f5f9; font-weight: bold; font-size: 8px; text-transform: uppercase; }
    .name-col { text-align: left; white-space: nowrap; min-width: 120px; }
    .present { color: #16a34a; font-weight: bold; }
    .absent { color: #dc2626; font-weight: bold; }
    .header { margin-bottom: 8px; }
    h2 { font-size: 14px; margin: 0 0 4px 0; }
    p { font-size: 9px; margin: 0; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Attendance Register</h2>
    <p>{{ request('month') }}</p>
  </div>
  <table>
    <thead>
      <tr>
        <th class="name-col">Student</th>
        @foreach($dates as $d)
        <th>{{ $d->format('d') }}</th>
        @endforeach
        <th>P</th><th>A</th><th>%</th>
      </tr>
    </thead>
    <tbody>
      @foreach($students as $s)
      @php $rec = $attendanceMap[$s->student_id] ?? []; @endphp
      <tr>
        <td class="name-col">{{ $s->student?->full_name }}</td>
        @foreach($dates as $d)
        @php $status = $rec[$d->toDateString()] ?? null; @endphp
        <td>
          @if($status==='present')<span class="present">P</span>
          @elseif($status==='absent')<span class="absent">A</span>
          @elseif($status==='late')<span style="color:#d97706">L</span>
          @else<span style="color:#e2e8f0">-</span>@endif
        </td>
        @endforeach
        @php
          $p = collect($rec)->filter(fn($v)=>in_array($v,['present','late']))->count();
          $a = collect($rec)->filter(fn($v)=>$v==='absent')->count();
          $t = $p+$a;
          $pct = $t>0 ? round($p/$t*100) : 0;
        @endphp
        <td class="present">{{ $p }}</td>
        <td class="absent">{{ $a }}</td>
        <td style="{{ $pct<75 ? 'color:#dc2626;font-weight:bold' : '' }}">{{ $pct }}%</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
