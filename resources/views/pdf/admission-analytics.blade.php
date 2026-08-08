<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 14px; }
  .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 11px; color: #475569; margin-top: 2px; }
  .meta { font-size: 8px; color: #94a3b8; margin-top: 2px; }
  .section { margin-bottom: 14px; }
  .section-title { font-size: 10px; font-weight: bold; background: #f1f5f9; padding: 4px 6px; border-left: 3px solid #1e40af; margin-bottom: 6px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
  th { background: #1e40af; color: white; font-size: 8px; padding: 4px 6px; text-align: left; }
  td { border-bottom: 1px solid #e2e8f0; padding: 3px 6px; font-size: 9px; }
  tr:nth-child(even) td { background: #f8fafc; }
  .two-col { display: table; width: 100%; }
  .col-half { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
  .col-half:last-child { padding-right: 0; padding-left: 8px; }
  .badge-up { color: #16a34a; font-weight: bold; }
  .badge-down { color: #dc2626; font-weight: bold; }
  .yoy-box { border: 1px solid #e2e8f0; border-radius: 3px; padding: 6px 10px; text-align: center; display: inline-block; width: 45%; margin: 0 2%; }
  .yoy-year { font-size: 9px; color: #64748b; }
  .yoy-val { font-size: 18px; font-weight: bold; color: #1e40af; }
  .yoy-label { font-size: 8px; color: #94a3b8; }
  footer { border-top: 1px solid #e2e8f0; margin-top: 16px; padding-top: 6px; text-align: center; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="report-title">Admission Analytics Report</div>
  <div class="meta">
    Academic Year: {{ $currentYear?->name ?? 'Current' }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
  </div>
</div>

{{-- Year-over-Year --}}
@if($yoyData)
<div class="section">
  <div class="section-title">Year-over-Year Comparison</div>
  <div style="text-align:center; padding:6px 0;">
    <div class="yoy-box">
      <div class="yoy-year">{{ $yoyData['prev']['year'] }}</div>
      <div class="yoy-val">{{ number_format($yoyData['prev']['enquiries']) }}</div>
      <div class="yoy-label">Enquiries</div>
      <div style="margin-top:4px; font-size:10px; font-weight:bold; color:#475569;">{{ number_format($yoyData['prev']['converted']) }} converted</div>
    </div>
    <div class="yoy-box">
      <div class="yoy-year">{{ $yoyData['current']['year'] }}</div>
      <div class="yoy-val">{{ number_format($yoyData['current']['enquiries']) }}</div>
      <div class="yoy-label">Enquiries</div>
      <div style="margin-top:4px; font-size:10px; font-weight:bold; color:#475569;">{{ number_format($yoyData['current']['converted']) }} converted</div>
    </div>
    @php
      $diff = $yoyData['current']['enquiries'] - $yoyData['prev']['enquiries'];
      $pct  = $yoyData['prev']['enquiries'] > 0
        ? round($diff / $yoyData['prev']['enquiries'] * 100, 1) : null;
    @endphp
    @if($pct !== null)
      <div style="display:inline-block; width:8%; text-align:center; vertical-align:top; padding-top:12px;">
        <span class="{{ $diff >= 0 ? 'badge-up' : 'badge-down' }}">
          {{ $diff >= 0 ? '▲' : '▼' }} {{ abs($pct) }}%
        </span>
      </div>
    @endif
  </div>
</div>
@endif

{{-- Two-column: Source + Status --}}
<div class="two-col">
  <div class="col-half">
    <div class="section">
      <div class="section-title">Source-wise Breakdown</div>
      <table>
        <thead><tr><th>Source</th><th>Count</th><th>%</th></tr></thead>
        <tbody>
          @php $sourceTotal = $bySource->sum(); @endphp
          @forelse($bySource as $source => $count)
          <tr>
            <td>{{ ucfirst($source ?: 'Unknown') }}</td>
            <td>{{ $count }}</td>
            <td>{{ $sourceTotal > 0 ? round($count/$sourceTotal*100,1).'%' : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="3" style="text-align:center;color:#94a3b8;">No data</td></tr>
          @endforelse
          @if($bySource->isNotEmpty())
          <tr style="font-weight:bold; background:#f1f5f9;">
            <td>Total</td><td>{{ $sourceTotal }}</td><td>100%</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-half">
    <div class="section">
      <div class="section-title">Status-wise Breakdown</div>
      <table>
        <thead><tr><th>Status</th><th>Count</th><th>%</th></tr></thead>
        <tbody>
          @php $statusTotal = $byStatus->sum(); @endphp
          @forelse($byStatus as $status => $count)
          <tr>
            <td>{{ ucfirst(str_replace('_',' ',$status ?: 'Unknown')) }}</td>
            <td>{{ $count }}</td>
            <td>{{ $statusTotal > 0 ? round($count/$statusTotal*100,1).'%' : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="3" style="text-align:center;color:#94a3b8;">No data</td></tr>
          @endforelse
          @if($byStatus->isNotEmpty())
          <tr style="font-weight:bold; background:#f1f5f9;">
            <td>Total</td><td>{{ $statusTotal }}</td><td>100%</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Class vs Seats --}}
<div class="section">
  <div class="section-title">Class-wise Admission Progress vs Seat Capacity</div>
  <table>
    <thead><tr><th>Class</th><th>Enquiries</th><th>Total Seats</th><th>Fill %</th></tr></thead>
    <tbody>
      @forelse($classSeats as $row)
      <tr>
        <td>{{ $row['class'] }}</td>
        <td>{{ $row['enquiries'] }}</td>
        <td>{{ $row['seats'] ?: '—' }}</td>
        <td>
          @if($row['fill_pct'] !== null)
            <span class="{{ $row['fill_pct'] >= 100 ? 'badge-up' : ($row['fill_pct'] >= 70 ? '' : 'badge-down') }}">
              {{ $row['fill_pct'] }}%
            </span>
          @else
            —
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#94a3b8;">No class data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Monthly Trend --}}
<div class="section">
  <div class="section-title">Monthly Enquiry Trend ({{ now()->year }})</div>
  @php $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
  <table>
    <thead><tr>
      @for($i=1;$i<=12;$i++)<th style="text-align:center;">{{ $months[$i] }}</th>@endfor
    </tr></thead>
    <tbody><tr>
      @for($i=1;$i<=12;$i++)
        <td style="text-align:center;">{{ $monthlyTrend->get($i, 0) }}</td>
      @endfor
    </tr></tbody>
  </table>
</div>

{{-- Counsellor Conversion --}}
@if($byCounsellor->isNotEmpty())
<div class="section">
  <div class="section-title">Counsellor-wise Conversion</div>
  <table>
    <thead><tr><th>Counsellor</th><th>Total Enquiries</th><th>Converted</th><th>Conversion Rate</th></tr></thead>
    <tbody>
      @foreach($byCounsellor->sortByDesc('total') as $row)
      <tr>
        <td>{{ $row->assignedTo?->name ?? ('Staff #'.$row->assigned_to) }}</td>
        <td>{{ $row->total }}</td>
        <td>{{ $row->converted }}</td>
        <td>
          @php $rate = $row->total > 0 ? round($row->converted/$row->total*100,1) : 0; @endphp
          <span class="{{ $rate >= 50 ? 'badge-up' : 'badge-down' }}">{{ $rate }}%</span>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<footer>
  {{ $school?->school_name ?? 'School' }} &mdash; Admission Analytics &mdash; {{ now()->format('d M Y') }}
</footer>
</body>
</html>
