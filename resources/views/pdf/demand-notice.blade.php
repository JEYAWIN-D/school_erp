<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 40px; }
  .header { text-align: center; border-bottom: 2px solid #7f1d1d; padding-bottom: 12px; margin-bottom: 20px; }
  .school-name { font-size: 18px; font-weight: bold; color: #7f1d1d; }
  .school-sub { font-size: 10px; color: #555; margin-top: 3px; }
  .notice-title { text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 1px; margin: 16px 0; text-transform: uppercase; text-decoration: underline; }
  .notice-date { text-align: right; margin-bottom: 16px; font-size: 10px; color: #555; }
  .to-block { margin-bottom: 16px; }
  .to-block strong { font-size: 12px; }
  p { margin-bottom: 10px; line-height: 1.6; }
  table { width: 100%; border-collapse: collapse; margin: 16px 0; }
  th { background: #7f1d1d; color: white; padding: 7px 10px; font-size: 10px; text-align: left; border: 1px solid #7f1d1d; }
  td { padding: 6px 10px; border: 1px solid #ddd; font-size: 11px; }
  tr:nth-child(even) td { background: #fef2f2; }
  .total-row td { font-weight: bold; background: #fee2e2 !important; }
  .warning-box { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 4px; padding: 12px 16px; margin: 16px 0; }
  .sig-row { display: flex; justify-content: space-between; margin-top: 40px; }
  .sig-box { text-align: center; width: 160px; border-top: 1px solid #333; padding-top: 4px; font-size: 9px; color: #555; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'SCHOOL NAME' }}</div>
  <div class="school-sub">{{ $school?->address ?? '' }}{{ $school?->phone ? ' | ' . $school?->phone : '' }}</div>
</div>

<div class="notice-title">Fee Demand Notice</div>
<div class="notice-date">Date: {{ now()->format('d F Y') }}</div>

<div class="to-block">
  <p>To,</p>
  <strong>Parent / Guardian of</strong><br>
  <strong>{{ $student->first_name }} {{ $student->last_name }}</strong><br>
  Class: {{ $student->currentEnrollment?->class?->name ?? '—' }}<br>
  Admission No: {{ $student->admission_no ?? $student->admission_number ?? '—' }}
</div>

<p>Dear Parent / Guardian,</p>
<p>
  This is to inform you that the following fee dues are outstanding for the academic year
  <strong>{{ $currentYear?->name ?? now()->year }}</strong>. Kindly clear the outstanding balance at the earliest.
</p>

<table>
  <thead>
    <tr>
      <th>Fee Head</th>
      <th>Total Fee</th>
      <th>Amount Paid</th>
      <th>Balance Due</th>
    </tr>
  </thead>
  <tbody>
    @foreach($dues as $due)
    <tr>
      <td>{{ $due['feeHead']?->name ?? '—' }}</td>
      <td>₹{{ number_format($due['amount'], 2) }}</td>
      <td>₹{{ number_format($due['paid'], 2) }}</td>
      <td><strong>₹{{ number_format($due['balance'], 2) }}</strong></td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td>TOTAL</td>
      <td>₹{{ number_format($dues->sum('amount'), 2) }}</td>
      <td>₹{{ number_format($dues->sum('paid'), 2) }}</td>
      <td>₹{{ number_format($dues->sum('balance'), 2) }}</td>
    </tr>
  </tbody>
</table>

<div class="warning-box">
  <strong>Important:</strong> If the outstanding balance of <strong>₹{{ number_format($dues->sum('balance'), 2) }}</strong>
  is not paid within <strong>7 working days</strong> from the date of this notice, the school reserves the right to
  withhold the student's progress report / hall ticket / transfer certificate until the dues are cleared.
</div>

<p>Please visit the fee counter during school hours (9:00 AM – 2:00 PM) with this notice.</p>
<p>For any queries, contact the accounts office at: {{ $school?->phone ?? '—' }}</p>

<div class="sig-row">
  <div class="sig-box">Accounts Officer</div>
  <div class="sig-box">Principal</div>
</div>
</body>
</html>
