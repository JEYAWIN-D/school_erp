<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 10px; }
  .school-name { font-size: 12px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 9px; color: #475569; margin-top: 2px; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .route-block { margin-bottom: 14px; page-break-inside: avoid; }
  .route-header { background: #1e40af; color: white; padding: 4px 8px; font-size: 9px; font-weight: bold; border-radius: 3px 3px 0 0; }
  .route-meta { font-size: 8px; color: #64748b; padding: 2px 8px 4px; background: #f8fafc; border: 1px solid #e2e8f0; border-top: 0; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #e2e8f0; font-size: 8px; padding: 3px 5px; text-align: left; color: #475569; }
  td { border-bottom: 1px solid #f1f5f9; padding: 3px 5px; font-size: 8px; }
  .stop-name { font-weight: bold; color: #334155; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 12px; padding-top: 5px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="report-title">Transport Route-wise Student Roster</div>
  <div class="meta">Generated: {{ now()->format('d M Y') }}</div>
</div>

@foreach($routes as $route)
<div class="route-block">
  <div class="route-header">Route: {{ $route->route_name }} @if($route->route_number)({{ $route->route_number }})@endif</div>
  <div class="route-meta">
    Vehicle: {{ $route->vehicle?->vehicle_number ?? '—' }} &nbsp;|&nbsp;
    Students: {{ $route->allotments->count() }}
  </div>
  <table>
    <thead><tr><th>#</th><th>Stop</th><th>Pickup Time</th><th>Student</th><th>Class</th></tr></thead>
    <tbody>
      @php $sr=1; @endphp
      @foreach($route->stops->sortBy('sequence') as $stop)
        @php $studentsAtStop = $route->allotments->filter(fn($a) => $a->stop_id == $stop->id); @endphp
        @if($studentsAtStop->isEmpty())
          <tr>
            <td>{{ $sr++ }}</td>
            <td class="stop-name">{{ $stop->stop_name }}</td>
            <td>{{ $stop->pickup_time ?? '—' }}</td>
            <td colspan="2" style="color:#94a3b8;">—</td>
          </tr>
        @else
          @foreach($studentsAtStop as $allotment)
          <tr>
            <td>{{ $sr++ }}</td>
            <td class="stop-name">{{ $stop->stop_name }}</td>
            <td>{{ $stop->pickup_time ?? '—' }}</td>
            <td>{{ $allotment->enrollment?->student?->full_name ?? '—' }}</td>
            <td>{{ $allotment->enrollment?->class?->name ?? '—' }}</td>
          </tr>
          @endforeach
        @endif
      @endforeach
    </tbody>
  </table>
</div>
@endforeach

<footer>{{ $school?->school_name ?? '' }} — Transport Roster — {{ now()->format('d M Y') }}</footer>
</body>
</html>
