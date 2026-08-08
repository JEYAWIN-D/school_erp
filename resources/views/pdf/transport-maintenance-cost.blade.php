<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 10px; }
  .school-name { font-size: 12px; font-weight: bold; color: #1e40af; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .summary-row { display: table; width: 100%; margin-bottom: 12px; }
  .sum-cell { display: table-cell; text-align: center; border: 1px solid #e2e8f0; padding: 5px; }
  .sum-val { font-size: 13px; font-weight: bold; color: #1e40af; }
  .sum-lbl { font-size: 7px; color: #64748b; }
  .vehicle-header { background: #f1f5f9; padding: 5px 6px; margin: 8px 0 4px; font-weight: bold; font-size: 9px; border-left: 3px solid #1e40af; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
  th { background: #1e40af; color: white; font-size: 7.5px; padding: 3px 4px; text-align: left; }
  td { border-bottom: 1px solid #f1f5f9; padding: 3px 4px; font-size: 8px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .total-row td { font-weight: bold; background: #eff6ff; }
  .grand-total td { font-weight: bold; background: #1e40af; color: white; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 5px; text-align: center; font-size: 7px; color: #94a3b8; }
  .badge-red { color: #dc2626; font-weight: bold; }
  .badge-green { color: #15803d; font-weight: bold; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="meta">Vehicle Maintenance Cost Report — {{ $year }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y') }}</div>
</div>

<div class="summary-row">
  <div class="sum-cell"><div class="sum-val">₹{{ number_format($grandTotal, 0) }}</div><div class="sum-lbl">Total Maintenance Cost</div></div>
  <div class="sum-cell"><div class="sum-val">{{ $perVehicle->count() }}</div><div class="sum-lbl">Vehicles</div></div>
  <div class="sum-cell"><div class="sum-val">{{ $perVehicle->sum(fn($v) => $v['logs']->count()) }}</div><div class="sum-lbl">Total Jobs</div></div>
  <div class="sum-cell"><div class="sum-val">₹{{ number_format($perVehicle->avg('total'), 0) }}</div><div class="sum-lbl">Avg per Vehicle</div></div>
</div>

@foreach($perVehicle->filter(fn($v) => $v['total'] > 0) as $row)
<div class="vehicle-header">
  {{ $row['vehicle']->vehicle_number }}
  @if($row['vehicle']->make ?? null) — {{ $row['vehicle']->make }}@endif
  &nbsp;&nbsp;|&nbsp;&nbsp;
  Total: ₹{{ number_format($row['total'], 0) }}
  &nbsp;|&nbsp; {{ $row['logs']->count() }} service(s)
  &nbsp;|&nbsp; {{ $row['breakdown_count'] }} breakdown(s)
</div>
<table>
  <thead><tr>
    <th>Date</th><th>Type</th><th>Work Done</th><th>Vendor</th><th>Cost (₹)</th><th>Next Service</th>
  </tr></thead>
  <tbody>
    @foreach($row['logs']->sortByDesc('maintenance_date') as $log)
    <tr>
      <td>{{ \Carbon\Carbon::parse($log->maintenance_date)->format('d M Y') }}</td>
      <td class="{{ $log->maintenance_type==='breakdown'?'badge-red':'badge-green' }}">{{ ucfirst($log->maintenance_type) }}</td>
      <td>{{ \Illuminate\Support\Str::limit($log->work_done, 40) }}</td>
      <td>{{ $log->vendor ?? '—' }}</td>
      <td style="text-align:right;">₹{{ number_format($log->cost, 0) }}</td>
      <td>{{ $log->next_service_date ? \Carbon\Carbon::parse($log->next_service_date)->format('d M Y') : '—' }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="4" style="text-align:right;">Vehicle Total</td>
      <td style="text-align:right;">₹{{ number_format($row['total'], 0) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>
@endforeach

<table>
  <tbody>
    <tr class="grand-total">
      <td colspan="4">GRAND TOTAL — All Vehicles</td>
      <td style="text-align:right;">₹{{ number_format($grandTotal, 0) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>

<footer>{{ $school?->school_name ?? '' }} — Vehicle Maintenance Cost Report — {{ $year }}</footer>
</body>
</html>
