<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 12px; }
  .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
  .report-title { font-size: 11px; font-weight: bold; margin-top: 4px; color: #334155; }
  .meta { font-size: 9px; color: #64748b; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: #fff; padding: 4px 5px; text-align: center; font-size: 8px; }
  th.left { text-align: left; }
  td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; font-size: 8px; text-align: right; }
  td.left { text-align: left; }
  tr:nth-child(even) td { background: #f8fafc; }
  .footer { margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; text-align: right; }
  .total-row td { font-weight: bold; background: #dbeafe; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="report-title">Monthly Salary Register — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
  <div class="meta">Generated: {{ now()->format('d M Y H:i') }}</div>
</div>

<table>
  <thead>
    <tr>
      <th class="left">#</th>
      <th class="left">Employee</th>
      <th class="left">Dept</th>
      <th>Gross (₹)</th>
      <th>Deductions (₹)</th>
      <th>Net Pay (₹)</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($records as $rec)
    <tr>
      <td class="left">{{ $loop->iteration }}</td>
      <td class="left"><strong>{{ $rec->employee?->name }}</strong><br><span style="color:#64748b;font-size:7px;">{{ $rec->employee?->designation }}</span></td>
      <td class="left">{{ $rec->employee?->department?->name ?? '—' }}</td>
      <td>{{ number_format($rec->gross_salary, 2) }}</td>
      <td>{{ number_format($rec->deductions, 2) }}</td>
      <td><strong>{{ number_format($rec->net_salary, 2) }}</strong></td>
      <td style="text-align:center; color:{{ $rec->status === 'paid' ? '#16a34a' : '#d97706' }};">{{ ucfirst($rec->status) }}</td>
    </tr>
    @endforeach
    @if($records->isEmpty())
    <tr><td colspan="7" style="text-align:center; padding:14px; color:#94a3b8;">No payroll records found.</td></tr>
    @endif
  </tbody>
  @if($records->isNotEmpty())
  <tfoot>
    <tr class="total-row">
      <td class="left" colspan="3">TOTAL</td>
      <td>{{ number_format($records->sum('gross_salary'), 2) }}</td>
      <td>{{ number_format($records->sum('deductions'), 2) }}</td>
      <td>{{ number_format($records->sum('net_salary'), 2) }}</td>
      <td></td>
    </tr>
  </tfoot>
  @endif
</table>

<div class="footer">
  {{ $school?->school_name ?? '' }} | Confidential — Not for distribution | Printed: {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
