<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; }
.page { padding: 20px; }
.header { text-align: center; border: 2px solid #333; padding: 10px; margin-bottom: 10px; }
.header h1 { font-size: 14px; margin: 0 0 4px; }
.header h2 { font-size: 12px; margin: 0; color: #555; }
.form-title { background: #1e3a5f; color: white; text-align: center; padding: 6px; font-size: 13px; font-weight: bold; margin: 10px 0; }
.section-title { background: #e8f0fe; padding: 4px 8px; font-weight: bold; font-size: 11px; margin: 8px 0 4px; border-left: 3px solid #1e3a5f; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
td, th { border: 1px solid #aaa; padding: 4px 6px; font-size: 10px; }
th { background: #f0f0f0; font-weight: bold; }
.label-col { width: 60%; background: #fafafa; }
.amount-col { text-align: right; width: 40%; }
.total-row td { background: #e8f0fe; font-weight: bold; }
.sign-row { margin-top: 30px; display: flex; justify-content: space-between; }
.sign-box { text-align: center; width: 30%; }
.sign-line { border-top: 1px solid #333; padding-top: 4px; font-size: 9px; }
.highlight { background: #fffbeb; }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <h1>{{ $school?->school_name ?? 'School Name' }}</h1>
    <div style="font-size:10px">{{ $school?->address ?? '' }}</div>
    <div style="font-size:10px">PAN of Deductor: {{ $school?->pan_number ?? 'XXXXXXXXXX' }} | TAN: {{ $school?->tan_number ?? 'XXXXXXXXXX' }}</div>
  </div>

  <div class="form-title">FORM 16 — CERTIFICATE OF TAX DEDUCTED AT SOURCE</div>
  <div style="font-size:10px;text-align:center;color:#555;margin-bottom:8px">
    Financial Year: {{ $financialYear }} | Issued under Section 203 of the Income Tax Act, 1961
  </div>

  <div class="section-title">Part A — Deductee Details</div>
  <table>
    <tr>
      <td class="label-col">Name of Employee</td>
      <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
    </tr>
    <tr>
      <td class="label-col">PAN of Employee</td>
      <td>{{ $employee->pan_no ?? 'Not Available' }}</td>
    </tr>
    <tr>
      <td class="label-col">Employee Code</td>
      <td>{{ $employee->employee_code }}</td>
    </tr>
    <tr>
      <td class="label-col">Period of Employment</td>
      <td>01 April {{ substr($financialYear, 0, 4) }} to 31 March {{ substr($financialYear, -4) }}</td>
    </tr>
    <tr>
      <td class="label-col">Tax Regime</td>
      <td>{{ strtoupper($employee->tax_regime ?? 'new') }} REGIME</td>
    </tr>
  </table>

  <div class="section-title">Part B — Computation of Income under Salary</div>
  <table>
    <tr>
      <td class="label-col">1. Gross Salary (Sum of all months)</td>
      <td class="amount-col">₹ {{ number_format($annualGross, 2) }}</td>
    </tr>
    <tr>
      <td class="label-col">2. Standard Deduction u/s 16(ia)</td>
      <td class="amount-col">₹ {{ number_format($tds['std_deduction'], 2) }}</td>
    </tr>
    @if($employee->tax_regime === 'old')
    <tr>
      <td class="label-col">3. HRA Exemption u/s 10(13A)</td>
      <td class="amount-col">₹ {{ number_format($tds['hra_exemption'], 2) }}</td>
    </tr>
    <tr>
      <td class="label-col">4. Deduction u/s 80C (LIC/PF/ELSS etc.)</td>
      <td class="amount-col">₹ {{ number_format($tds['invest_80c'], 2) }}</td>
    </tr>
    <tr>
      <td class="label-col">5. Deduction u/s 80D (Medical Insurance)</td>
      <td class="amount-col">₹ {{ number_format($tds['invest_80d'], 2) }}</td>
    </tr>
    @endif
    <tr class="total-row">
      <td>Net Taxable Income</td>
      <td class="amount-col">₹ {{ number_format($tds['taxable_income'], 2) }}</td>
    </tr>
  </table>

  <div class="section-title">Tax Computation</div>
  <table>
    <tr>
      <td class="label-col">Income Tax on above</td>
      <td class="amount-col">₹ {{ number_format($tds['base_tax'], 2) }}</td>
    </tr>
    <tr>
      <td class="label-col">Health & Education Cess @ 4%</td>
      <td class="amount-col">₹ {{ number_format($tds['cess'], 2) }}</td>
    </tr>
    <tr class="total-row">
      <td>Total Annual Tax Liability</td>
      <td class="amount-col">₹ {{ number_format($tds['annual_tax'], 2) }}</td>
    </tr>
  </table>

  <div class="section-title">Part C — Monthly TDS Details</div>
  <table>
    <thead>
      <tr>
        <th>Month</th>
        <th>Gross Salary</th>
        <th>Deductions</th>
        <th>Net Salary</th>
        <th>TDS Deducted</th>
      </tr>
    </thead>
    <tbody>
      @foreach($payrolls as $p)
      <tr>
        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->month)->format('M Y') }}</td>
        <td style="text-align:right">₹ {{ number_format($p->gross_salary, 2) }}</td>
        <td style="text-align:right">₹ {{ number_format($p->deductions, 2) }}</td>
        <td style="text-align:right">₹ {{ number_format($p->net_salary, 2) }}</td>
        <td style="text-align:right">₹ {{ number_format($p->tds_amount, 2) }}</td>
      </tr>
      @endforeach
      <tr class="total-row">
        <td><strong>Total</strong></td>
        <td style="text-align:right"><strong>₹ {{ number_format($annualGross, 2) }}</strong></td>
        <td style="text-align:right"><strong>₹ {{ number_format($totalDeductions, 2) }}</strong></td>
        <td style="text-align:right"><strong>₹ {{ number_format($netPaid, 2) }}</strong></td>
        <td style="text-align:right"><strong>₹ {{ number_format($totalTds, 2) }}</strong></td>
      </tr>
    </tbody>
  </table>

  <div style="background:#fffbeb;border:1px solid #f59e0b;padding:8px;font-size:10px;margin:8px 0;">
    <strong>Verification:</strong> I, the undersigned, certify that the information given above is true, correct and complete to the best of my knowledge and belief.
  </div>

  <div class="sign-row">
    <div class="sign-box">
      <div class="sign-line">Date: {{ now()->format('d M Y') }}</div>
    </div>
    <div class="sign-box">
      <div class="sign-line">Signature of DDO / Principal</div>
    </div>
    <div class="sign-box">
      <div class="sign-line">Stamp / Seal</div>
    </div>
  </div>
</div>
</body>
</html>
