<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
  .page { padding: 40px; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 14px; margin-bottom: 20px; }
  .school-name { font-size: 20px; font-weight: bold; color: #1e3a8a; }
  .doc-title { font-size: 14px; font-weight: bold; color: #1e40af; margin-top: 4px; letter-spacing: 2px; }
  .receipt-no { font-size: 13px; font-weight: bold; color: #16a34a; border: 2px solid #16a34a; display: inline-block; padding: 3px 14px; border-radius: 4px; margin-top: 8px; }
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .info-table td { padding: 6px 10px; }
  .info-table .label { color: #64748b; font-size: 11px; width: 40%; }
  .info-table .value { font-weight: bold; font-size: 12px; }
  .detail-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-bottom: 20px; background: #f8fafc; }
  .detail-row { display: table; width: 100%; margin-bottom: 8px; }
  .detail-left { display: table-cell; width: 60%; color: #475569; }
  .detail-right { display: table-cell; text-align: right; font-weight: bold; font-size: 13px; }
  .total-box { background: #1e3a8a; color: white; padding: 12px 16px; border-radius: 6px; display: table; width: 100%; margin-top: 16px; }
  .total-left { display: table-cell; font-size: 14px; }
  .total-right { display: table-cell; text-align: right; font-size: 18px; font-weight: bold; }
  .mode-badge { display: inline-block; background: #dbeafe; color: #1e40af; padding: 3px 10px; border-radius: 10px; font-size: 11px; }
  .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
  .stamp { text-align: right; margin-top: 30px; font-size: 10px; color: #64748b; }
</style>
</head>
<body>
<div class="page">

  <div class="header">
    <div class="school-name">{{ $school?->name ?? 'School Name' }}</div>
    @if($school?->address)<div style="font-size:10px;color:#64748b;margin-top:2px;">{{ $school->address }}</div>@endif
    <div class="doc-title">FEE PAYMENT RECEIPT</div>
    <div class="receipt-no">Receipt # {{ $payment->receipt_number }}</div>
  </div>

  <table class="info-table">
    <tr>
      <td class="label">Student Name</td>
      <td class="value">{{ $student->full_name }}</td>
      <td class="label">Admission No.</td>
      <td class="value">{{ $student->admission_no }}</td>
    </tr>
    <tr>
      <td class="label">Class</td>
      <td class="value">{{ $enrollment?->class_name ?? '—' }}</td>
      <td class="label">Payment Date</td>
      <td class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
    </tr>
  </table>

  <div class="detail-box">
    <div class="detail-row">
      <div class="detail-left">Fee Head</div>
      <div class="detail-right">{{ $payment->fee_head_name ?? 'General Fee' }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-left">Fee Amount</div>
      <div class="detail-right">₹{{ number_format($payment->amount, 2) }}</div>
    </div>
    @if($payment->discount > 0)
    <div class="detail-row">
      <div class="detail-left" style="color:#16a34a;">Discount</div>
      <div class="detail-right" style="color:#16a34a;">- ₹{{ number_format($payment->discount, 2) }}</div>
    </div>
    @endif
    @if($payment->late_fee > 0)
    <div class="detail-row">
      <div class="detail-left" style="color:#dc2626;">Late Fee</div>
      <div class="detail-right" style="color:#dc2626;">+ ₹{{ number_format($payment->late_fee, 2) }}</div>
    </div>
    @endif

    <div class="total-box">
      <div class="total-left">Amount Paid</div>
      <div class="total-right">₹{{ number_format($payment->total_paid, 2) }}</div>
    </div>
  </div>

  <div style="margin-bottom:16px;">
    <span style="color:#64748b;font-size:11px;">Payment Mode: </span>
    <span class="mode-badge">{{ strtoupper(str_replace('_',' ',$payment->payment_mode ?? 'cash')) }}</span>
    @if($payment->transaction_id)
    <span style="color:#64748b;font-size:11px;margin-left:12px;">Transaction ID: </span>
    <span style="font-weight:bold;font-size:11px;">{{ $payment->transaction_id }}</span>
    @endif
  </div>

  @if($payment->remarks)
  <div style="font-size:10px;color:#64748b;">Remarks: {{ $payment->remarks }}</div>
  @endif

  <div class="stamp">
    <div style="margin-bottom:40px;"></div>
    <div style="border-top:1px solid #94a3b8;width:160px;display:inline-block;padding-top:5px;">Authorised Signature</div>
  </div>

  <div class="footer">
    This is a computer generated receipt — no signature required. | Generated on {{ now()->format('d M Y h:i A') }}
  </div>
</div>
</body>
</html>
