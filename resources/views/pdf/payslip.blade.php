<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
.header { text-align: center; padding: 10px; background: #1e3a5f; color: white; margin-bottom: 12px; }
.school-name { font-size: 16px; font-weight: bold; }
.payslip-for { font-size: 12px; margin-top: 4px; }
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.info-table td { padding: 4px 8px; border: 1px solid #ddd; }
.info-table td:nth-child(odd) { font-weight: bold; background: #f0f4ff; width: 20%; }
.earnings-table { width: 48%; border-collapse: collapse; }
.deductions-table { width: 48%; border-collapse: collapse; float: right; margin-top: -130px; }
.earnings-table th, .deductions-table th { background: #2c5282; color: white; padding: 5px 8px; text-align: left; }
.earnings-table td, .deductions-table td { padding: 4px 8px; border-bottom: 1px solid #eee; }
.total-row td { font-weight: bold; border-top: 2px solid #333; padding-top: 6px; }
.net-pay-box { background: #e8f4fd; border: 2px solid #2c5282; padding: 10px; text-align: center; margin-top: 12px; clear:both; }
.net-amount { font-size: 22px; font-weight: bold; color: #1e3a5f; }
.clearfix::after { content: ""; display: table; clear: both; }
.footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school->school_name ?? 'DASA EduERP' }}</div>
  <div class="payslip-for">SALARY SLIP — {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</div>
</div>
<table class="info-table">
  <tr>
    <td>Employee Name</td><td>{{ $employee->full_name }}</td>
    <td>Employee ID</td><td>{{ $employee->employee_number }}</td>
  </tr>
  <tr>
    <td>Designation</td><td>{{ $employee->designation?->name }}</td>
    <td>Department</td><td>{{ $employee->department?->name }}</td>
  </tr>
  <tr>
    <td>Date of Joining</td><td>{{ $employee->date_of_joining?->format('d/m/Y') }}</td>
    <td>Bank Account</td><td>{{ $employee->bank_account_number ?? '—' }}</td>
  </tr>
  <tr>
    <td>PAN Number</td><td>{{ $employee->pan_number ?? '—' }}</td>
    <td>PF Number</td><td>{{ $employee->pf_account_number ?? '—' }}</td>
  </tr>
</table>
<div class="clearfix">
<table class="earnings-table">
  <thead><tr><th colspan="2">Earnings</th></tr></thead>
  <tbody>
    <tr><td>Basic Salary</td><td style="text-align:right">₹{{ number_format($payroll->basic_salary,2) }}</td></tr>
    <tr><td>HRA</td><td style="text-align:right">₹{{ number_format($payroll->hra ?? 0,2) }}</td></tr>
    <tr><td>Transport Allowance</td><td style="text-align:right">₹{{ number_format($payroll->transport_allowance ?? 0,2) }}</td></tr>
    <tr><td>Medical Allowance</td><td style="text-align:right">₹{{ number_format($payroll->medical_allowance ?? 0,2) }}</td></tr>
    <tr><td>Other Allowances</td><td style="text-align:right">₹{{ number_format($payroll->other_allowances ?? 0,2) }}</td></tr>
    @if($payroll->bonus ?? 0)<tr><td>Bonus</td><td style="text-align:right">₹{{ number_format($payroll->bonus,2) }}</td></tr>@endif
    <tr class="total-row"><td>Gross Salary</td><td style="text-align:right">₹{{ number_format($payroll->gross_salary,2) }}</td></tr>
  </tbody>
</table>
<table class="deductions-table">
  <thead><tr><th colspan="2">Deductions</th></tr></thead>
  <tbody>
    <tr><td>PF (Employee)</td><td style="text-align:right">₹{{ number_format($payroll->pf_employee ?? 0,2) }}</td></tr>
    <tr><td>Professional Tax</td><td style="text-align:right">₹{{ number_format($payroll->professional_tax ?? 0,2) }}</td></tr>
    <tr><td>TDS</td><td style="text-align:right">₹{{ number_format($payroll->tds ?? 0,2) }}</td></tr>
    @if($payroll->loan_deduction ?? 0)<tr><td>Loan EMI</td><td style="text-align:right">₹{{ number_format($payroll->loan_deduction,2) }}</td></tr>@endif
    @if($payroll->advance_deduction ?? 0)<tr><td>Advance Recovery</td><td style="text-align:right">₹{{ number_format($payroll->advance_deduction,2) }}</td></tr>@endif
    <tr class="total-row"><td>Total Deductions</td><td style="text-align:right">₹{{ number_format($payroll->total_deductions ?? 0,2) }}</td></tr>
  </tbody>
</table>
</div>
<div class="net-pay-box">
  <div>Net Salary Payable</div>
  <div class="net-amount">₹{{ number_format($payroll->net_salary,2) }}</div>
  <div style="font-size:10px;color:#666;margin-top:4px">{{ $payroll->payment_status === 'paid' ? 'PAID' : 'PENDING' }}</div>
</div>
<div class="footer">This is a computer-generated payslip. No signature required.</div>
</body>
</html>
