<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Statement</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; padding: 24px; }
  .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 14px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-start; }
  .school-name { font-size: 20px; font-weight: 700; color: #1d4ed8; }
  .school-sub { font-size: 11px; color: #64748b; margin-top: 3px; }
  .doc-title { font-size: 16px; font-weight: 700; color: #1e293b; text-align: right; }
  .doc-date { font-size: 10px; color: #94a3b8; text-align: right; margin-top: 3px; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
  .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
  .info-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; }
  .info-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 11px; }
  .info-key { color: #64748b; }
  .info-val { font-weight: 600; color: #1e293b; }
  .summary { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 18px; }
  .sum-card { border-radius: 6px; padding: 10px 12px; text-align: center; }
  .sum-val { font-size: 18px; font-weight: 700; }
  .sum-lbl { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-top: 2px; }
  .section-title { font-size: 11px; font-weight: 700; color: #1e293b; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #f1f5f9; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 18px; }
  th { background: #f8fafc; padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; border-bottom: 2px solid #e2e8f0; }
  td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; }
  .badge-paid { background: #dcfce7; color: #16a34a; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .badge-unpaid { background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .balance-box { padding: 10px 14px; border-radius: 6px; font-size: 13px; font-weight: 700; }
  .footer { margin-top: 20px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="school-name"><?php echo e(config('app.name')); ?></div>
    <div class="school-sub">Fee Statement — Academic Year <?php echo e(now()->year); ?>-<?php echo e(now()->addYear()->year); ?></div>
  </div>
  <div>
    <div class="doc-title">Fee Statement</div>
    <div class="doc-date">Generated: <?php echo e(now()->format('d M Y, g:i A')); ?></div>
  </div>
</div>

<div class="info-grid">
  <div class="info-box">
    <div class="info-label">Student Details</div>
    <div class="info-row"><span class="info-key">Name</span><span class="info-val"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></span></div>
    <div class="info-row"><span class="info-key">Admission No.</span><span class="info-val"><?php echo e($student->admission_no); ?></span></div>
    <?php if($enrollment): ?>
    <div class="info-row"><span class="info-key">Class</span><span class="info-val"><?php echo e($enrollment->class_name); ?> <?php echo e($enrollment->section_name); ?></span></div>
    <?php endif; ?>
  </div>
  <div class="info-box">
    <div class="info-label">Fee Summary</div>
    <div class="info-row"><span class="info-key">Total Billed</span><span class="info-val">₹<?php echo e(number_format($totalDue)); ?></span></div>
    <div class="info-row"><span class="info-key">Total Paid</span><span class="info-val" style="color:#16a34a">₹<?php echo e(number_format($totalPaid)); ?></span></div>
    <div class="info-row"><span class="info-key">Balance Due</span><span class="info-val" style="color:<?php echo e($balance > 0 ? '#dc2626' : '#16a34a'); ?>">₹<?php echo e(number_format($balance)); ?></span></div>
  </div>
</div>

<?php if($invoices->count()): ?>
<div class="section-title">Fee Invoices</div>
<table>
  <thead>
    <tr>
      <th>Fee Head</th>
      <th style="text-align:right">Amount</th>
      <th>Due Date</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($inv->fee_head_name ?? 'Fee'); ?></td>
      <td style="text-align:right;font-weight:700">₹<?php echo e(number_format($inv->amount)); ?></td>
      <td><?php echo e(\Carbon\Carbon::parse($inv->due_date)->format('d M Y')); ?></td>
      <td><span class="<?php echo e(($inv->status ?? '') === 'paid' ? 'badge-paid' : 'badge-unpaid'); ?>"><?php echo e(($inv->status ?? '') === 'paid' ? 'Paid' : 'Unpaid'); ?></span></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr style="background:#f8fafc;font-weight:700">
      <td>Total</td>
      <td style="text-align:right">₹<?php echo e(number_format($totalDue)); ?></td>
      <td colspan="2"></td>
    </tr>
  </tbody>
</table>
<?php endif; ?>

<?php if($payments->count()): ?>
<div class="section-title">Payment History</div>
<table>
  <thead>
    <tr>
      <th>Receipt No.</th>
      <th>Payment Date</th>
      <th>Mode</th>
      <th style="text-align:right">Amount Paid</th>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td>#<?php echo e($pay->receipt_number ?? $pay->id); ?></td>
      <td><?php echo e(\Carbon\Carbon::parse($pay->payment_date)->format('d M Y')); ?></td>
      <td><?php echo e(ucfirst($pay->payment_mode ?? 'Cash')); ?></td>
      <td style="text-align:right;font-weight:700;color:#16a34a">₹<?php echo e(number_format($pay->total_paid ?? 0)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr style="background:#f8fafc;font-weight:700">
      <td colspan="3">Total Paid</td>
      <td style="text-align:right;color:#16a34a">₹<?php echo e(number_format($totalPaid)); ?></td>
    </tr>
  </tbody>
</table>
<?php endif; ?>

<?php if($balance > 0): ?>
<div class="balance-box" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
  Outstanding Balance: ₹<?php echo e(number_format($balance)); ?> — Please contact the school fee office.
</div>
<?php else: ?>
<div class="balance-box" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
  All fees cleared. No outstanding balance.
</div>
<?php endif; ?>

<div class="footer">
  This is a computer-generated statement and does not require a signature. | <?php echo e(config('app.url')); ?>

</div>

</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\portal\pdf\fee-receipt.blade.php ENDPATH**/ ?>