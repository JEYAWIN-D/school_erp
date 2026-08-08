<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 40px; }
  .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 12px; margin-bottom: 20px; }
  .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
  .school-sub { font-size: 10px; color: #555; margin-top: 3px; }
  .receipt-title { text-align: center; font-size: 13px; font-weight: bold; letter-spacing: 2px; margin: 10px 0; text-transform: uppercase; }
  .duplicate-badge { text-align: center; color: #dc2626; font-weight: bold; font-size: 10px; letter-spacing: 2px; margin-bottom: 12px; }
  .meta-row { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 10px; }
  table { width: 100%; border-collapse: collapse; }
  tr { border-bottom: 1px solid #e8eef4; }
  td { padding: 7px 6px; font-size: 11px; }
  td:first-child { color: #666; width: 40%; }
  td:last-child { font-weight: 600; }
  .total-row td { background: #eff6ff; font-weight: bold; font-size: 13px; border-top: 2px solid #1e3a5f; }
  .footer { margin-top: 24px; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 8px; }
  .sig { margin-top: 32px; text-align: right; }
  .sig-line { display: inline-block; border-top: 1px solid #333; width: 160px; padding-top: 4px; font-size: 9px; color: #555; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'SCHOOL NAME'); ?></div>
  <div class="school-sub"><?php echo e($school?->address ?? ''); ?><?php echo e($school?->phone ? ' | ' . $school?->phone : ''); ?></div>
  <?php if($school?->gstin): ?>
  <div class="school-sub">GSTIN: <?php echo e($school->gstin); ?></div>
  <?php endif; ?>
</div>

<div class="receipt-title">Fee Receipt / Tax Invoice</div>
<?php if(isset($isDuplicate) && $isDuplicate): ?>
<div class="duplicate-badge">*** DUPLICATE COPY ***</div>
<?php endif; ?>

<div class="meta-row">
  <span><strong>Receipt No:</strong> <?php echo e($payment->receipt_number); ?></span>
  <span><strong>Date:</strong> <?php echo e(\Carbon\Carbon::parse($payment->payment_date)->format('d M Y')); ?></span>
</div>

<table>
  <tr><td>Student Name</td><td><?php echo e($payment->student?->first_name); ?> <?php echo e($payment->student?->last_name); ?></td></tr>
  <tr><td>Admission No</td><td><?php echo e($payment->student?->admission_no ?? $payment->student?->admission_number ?? '—'); ?></td></tr>
  <tr><td>Class</td><td><?php echo e($payment->student?->currentEnrollment?->class?->name ?? '—'); ?></td></tr>
  <tr><td>Fee Head</td><td><?php echo e($payment->feeHead?->name ?? '—'); ?></td></tr>
  <?php if($payment->feeHead?->hsn_code): ?>
  <tr><td>SAC/HSN Code</td><td><?php echo e($payment->feeHead->hsn_code); ?></td></tr>
  <?php endif; ?>
  <tr><td>Payment Mode</td><td><?php echo e(ucfirst($payment->payment_mode)); ?></td></tr>
  <?php if($payment->transaction_id): ?>
  <tr><td>Transaction ID</td><td><?php echo e($payment->transaction_id); ?></td></tr>
  <?php endif; ?>
  <?php
    $feeHead = $payment->feeHead;
    $baseAmount = $payment->amount ?? 0;
    $gstAmt = 0;
    if ($feeHead?->gst_applicable && $feeHead->gst_percent > 0) {
      if ($feeHead->gst_type === 'inclusive') {
        $gstAmt = round($baseAmount - ($baseAmount * 100 / (100 + $feeHead->gst_percent)), 2);
        $baseAmount = $baseAmount - $gstAmt;
      } else {
        $gstAmt = round($baseAmount * $feeHead->gst_percent / 100, 2);
      }
    }
  ?>
  <?php if($feeHead?->gst_applicable && $feeHead->gst_percent > 0): ?>
  <tr><td>Taxable Amount</td><td>₹<?php echo e(number_format($baseAmount, 2)); ?></td></tr>
  <tr><td>GST (<?php echo e($feeHead->gst_percent); ?>% — <?php echo e(strtoupper($feeHead->gst_type ?? 'exclusive')); ?>)</td><td>₹<?php echo e(number_format($gstAmt, 2)); ?></td></tr>
  <?php else: ?>
  <tr><td>Amount</td><td>₹<?php echo e(number_format($payment->amount, 2)); ?></td></tr>
  <?php endif; ?>
  <?php if($payment->late_fee > 0): ?>
  <tr><td>Late Fee</td><td>₹<?php echo e(number_format($payment->late_fee, 2)); ?></td></tr>
  <?php endif; ?>
  <?php if($payment->discount > 0): ?>
  <tr><td>Discount</td><td>- ₹<?php echo e(number_format($payment->discount, 2)); ?></td></tr>
  <?php endif; ?>
  <tr class="total-row"><td>Total Paid</td><td>₹<?php echo e(number_format($payment->total_paid, 2)); ?></td></tr>
</table>

<div class="sig">
  <div class="sig-line">Authorized Signatory</div>
</div>

<div class="footer">This is a computer generated receipt. No signature required. | Collected by: <?php echo e($payment->collectedBy?->name ?? '—'); ?></div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\receipt.blade.php ENDPATH**/ ?>