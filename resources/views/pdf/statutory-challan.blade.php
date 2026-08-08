<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
.header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
.school-name { font-size: 14px; font-weight: bold; color: #1e3a5f; }
.challan-title { font-size: 12px; margin-top: 4px; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
th { background: #1e3a5f; color: white; padding: 4px 6px; text-align: left; }
td { padding: 3px 6px; border: 1px solid #d1d5db; }
tr:nth-child(even) td { background: #f8fafc; }
.total-row td { background: #e2e8f0; font-weight: bold; }
.summary { display: flex; gap: 20px; margin: 10px 0; }
.summary-box { border: 1px solid #d1d5db; padding: 6px 10px; flex: 1; text-align: center; }
.summary-label { font-size: 9px; color: #6b7280; }
.summary-value { font-size: 14px; font-weight: bold; color: #1e3a5f; }
.footer-note { margin-top: 15px; font-size: 9px; color: #6b7280; border-top: 1px solid #e2e8f0; padding-top: 5px; }
.sign-section { display: flex; justify-content: space-between; margin-top: 30px; }
.sign-box { text-align: center; }
.sign-line { border-top: 1px solid #333; margin-top: 25px; padding-top: 3px; font-size: 9px; width: 120px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'DASA EduERP' }}</div>
  <div style="font-size:10px;color:#666">{{ $school?->address ?? '' }}</div>
  @if($school?->pf_establishment_code ?? null)<div style="font-size:10px">PF Code: {{ $school->pf_establishment_code }}</div>@endif
  <div class="challan-title">
    @if($type === 'pf') EMPLOYEE PROVIDENT FUND (EPF) CHALLAN
    @elseif($type === 'esi') EMPLOYEES STATE INSURANCE (ESI) CHALLAN
    @else PROFESSIONAL TAX (PT) CHALLAN
    @endif
    — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
  </div>
</div>

<div class="summary">
  @if($type === 'pf')
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['pf_employee'], 2) }}</div>
    <div class="summary-label">Employee PF (12%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['pf_employer'], 2) }}</div>
    <div class="summary-label">Employer PF (12%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['pf_total'], 2) }}</div>
    <div class="summary-label">Total PF Payable</div>
  </div>
  @elseif($type === 'esi')
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['esi_employee'], 2) }}</div>
    <div class="summary-label">Employee ESI (0.75%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['esi_employer'], 2) }}</div>
    <div class="summary-label">Employer ESI (3.25%)</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['esi_total'], 2) }}</div>
    <div class="summary-label">Total ESI Payable</div>
  </div>
  @else
  <div class="summary-box">
    <div class="summary-value">₹{{ number_format($totals['pt_total'], 2) }}</div>
    <div class="summary-label">Total PT Payable</div>
  </div>
  <div class="summary-box">
    <div class="summary-value">{{ $challanData->count() }}</div>
    <div class="summary-label">Employees</div>
  </div>
  @endif
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Employee Name</th>
      <th>Emp ID</th>
      @if($type === 'pf')
      <th>UAN</th>
      <th>Basic (₹)</th>
      <th>Emp PF (₹)</th>
      <th>Emp'r PF (₹)</th>
      <th>Total (₹)</th>
      @elseif($type === 'esi')
      <th>ESI No.</th>
      <th>Gross (₹)</th>
      <th>Emp ESI (₹)</th>
      <th>Emp'r ESI (₹)</th>
      <th>Total (₹)</th>
      @else
      <th>Designation</th>
      <th>Gross (₹)</th>
      <th>PT (₹)</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @foreach($challanData as $i => $row)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row['employee']?->full_name }}</td>
      <td>{{ $row['employee']?->employee_number }}</td>
      @if($type === 'pf')
      <td>{{ $row['employee']?->uan_number ?? '—' }}</td>
      <td>{{ number_format($row['record']->basic_salary ?? 0, 2) }}</td>
      <td>{{ number_format($row['pf_employee'], 2) }}</td>
      <td>{{ number_format($row['pf_employer'], 2) }}</td>
      <td><strong>{{ number_format($row['pf_total'], 2) }}</strong></td>
      @elseif($type === 'esi')
      <td>{{ $row['employee']?->esi_number ?? '—' }}</td>
      <td>{{ number_format($row['record']->gross_salary ?? 0, 2) }}</td>
      <td>{{ number_format($row['esi_employee'], 2) }}</td>
      <td>{{ number_format($row['esi_employer'], 2) }}</td>
      <td><strong>{{ number_format($row['esi_total'], 2) }}</strong></td>
      @else
      <td>{{ $row['employee']?->designation }}</td>
      <td>{{ number_format($row['record']->gross_salary ?? 0, 2) }}</td>
      <td><strong>{{ number_format($row['pt'], 2) }}</strong></td>
      @endif
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="{{ $type === 'pf' ? 5 : ($type === 'esi' ? 4 : 3) }}" style="text-align:right">TOTAL</td>
      @if($type === 'pf')
      <td>{{ number_format($totals['pf_employee'], 2) }}</td>
      <td>{{ number_format($totals['pf_employer'], 2) }}</td>
      <td>{{ number_format($totals['pf_total'], 2) }}</td>
      @elseif($type === 'esi')
      <td>{{ number_format($totals['esi_employee'], 2) }}</td>
      <td>{{ number_format($totals['esi_employer'], 2) }}</td>
      <td>{{ number_format($totals['esi_total'], 2) }}</td>
      @else
      <td>{{ number_format($totals['pt_total'], 2) }}</td>
      @endif
    </tr>
  </tbody>
</table>

<div class="footer-note">
  Generated on: {{ now()->format('d M Y H:i') }} |
  Month: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }} |
  This is a computer-generated statement.
</div>

<div class="sign-section">
  <div class="sign-box"><div class="sign-line">Accounts Officer</div></div>
  <div class="sign-box"><div class="sign-line">HR Manager</div></div>
  <div class="sign-box"><div class="sign-line">Principal / Authorised Signatory</div></div>
</div>
</body>
</html>
