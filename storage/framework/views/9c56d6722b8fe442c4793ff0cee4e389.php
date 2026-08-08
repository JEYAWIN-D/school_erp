<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 50px; line-height: 1.7; }
  .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 12px; margin-bottom: 24px; }
  .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
  .school-sub { font-size: 10px; color: #555; margin-top: 3px; }
  .title { text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-decoration: underline; margin-bottom: 24px; text-transform: uppercase; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10px; }
  p { margin-bottom: 14px; }
  .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
  .info-table td { padding: 6px 10px; border: 1px solid #d1dce8; }
  .info-table td:first-child { background: #f0f4f8; font-weight: 600; width: 40%; }
  .highlight { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 4px; padding: 10px 14px; margin: 14px 0; font-weight: bold; font-size: 13px; }
  .sig { margin-top: 50px; display: flex; justify-content: space-between; }
  .sig-box { text-align: center; border-top: 1px solid #333; width: 180px; padding-top: 4px; font-size: 9px; color: #555; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? $school->name ?? 'SCHOOL NAME'); ?></div>
  <div class="school-sub"><?php echo e($school->address ?? ''); ?></div>
</div>

<div class="title">Increment Letter</div>
<div class="meta">
  <span><strong>Ref:</strong> <?php echo e($refNumber); ?></span>
  <span><strong>Date:</strong> <?php echo e(now()->format('d F Y')); ?></span>
</div>

<p>To,</p>
<p>
  <strong><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></strong><br>
  <?php echo e($employee->designation ?? ''); ?>, <?php echo e($employee->department ?? ''); ?>

</p>

<p>Dear <?php echo e($employee->first_name); ?>,</p>

<p>
  We are pleased to inform you that upon review of your performance and contributions to
  <strong><?php echo e($school->school_name ?? $school->name ?? 'our institution'); ?></strong>,
  the management has decided to revise your salary as follows:
</p>

<table class="info-table">
  <tr><td>Employee Name</td><td><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></td></tr>
  <tr><td>Employee Number</td><td><?php echo e($employee->employee_number); ?></td></tr>
  <tr><td>Designation</td><td><?php echo e($employee->designation ?? '—'); ?></td></tr>
  <tr><td>Department</td><td><?php echo e($employee->department ?? '—'); ?></td></tr>
  <tr><td>Date of Joining</td><td><?php echo e(\Carbon\Carbon::parse($employee->joining_date)->format('d M Y')); ?></td></tr>
  <tr><td>Current Basic Salary</td><td>₹<?php echo e(number_format($employee->basic_salary ?? 0, 2)); ?> per month</td></tr>
  <tr><td>Revised Basic Salary</td><td>₹<?php echo e(number_format($newSalary, 2)); ?> per month</td></tr>
  <tr><td>Effective From</td><td><?php echo e(now()->addMonth()->startOfMonth()->format('d M Y')); ?></td></tr>
</table>

<div class="highlight">
  Net Salary Increment: ₹<?php echo e(number_format($newSalary - ($employee->basic_salary ?? 0), 2)); ?> per month
</div>

<p>
  This increment is in recognition of your dedication, performance, and valuable contributions.
  Please continue to maintain the same level of commitment.
</p>
<p>
  Please sign and return the duplicate copy of this letter as an acknowledgment.
</p>

<div class="sig">
  <div class="sig-box">Employee Signature &amp; Date</div>
  <div class="sig-box">Principal / Director<br><?php echo e($school->school_name ?? 'School Name'); ?></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\increment-letter.blade.php ENDPATH**/ ?>