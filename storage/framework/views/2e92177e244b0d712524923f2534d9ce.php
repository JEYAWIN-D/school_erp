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
  .sig { margin-top: 50px; }
  .sig-box { display: inline-block; border-top: 1px solid #333; width: 200px; padding-top: 4px; font-size: 9px; color: #555; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? $school->name ?? 'SCHOOL NAME'); ?></div>
  <div class="school-sub"><?php echo e($school->address ?? ''); ?></div>
</div>

<div class="title">Relieving Letter</div>
<div class="meta">
  <span><strong>Ref:</strong> <?php echo e($refNumber); ?></span>
  <span><strong>Date:</strong> <?php echo e(now()->format('d F Y')); ?></span>
</div>

<p>To Whom It May Concern,</p>

<p>
  This is to certify that <strong><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></strong>,
  bearing Employee Number <strong><?php echo e($employee->employee_number); ?></strong>, was employed with
  <strong><?php echo e($school->school_name ?? $school->name ?? 'our institution'); ?></strong> as
  <strong><?php echo e($employee->designation ?? 'Staff Member'); ?></strong> in the
  <strong><?php echo e($employee->department ?? ''); ?></strong> department from
  <strong><?php echo e(\Carbon\Carbon::parse($employee->joining_date)->format('d F Y')); ?></strong>
  to <strong><?php echo e(now()->format('d F Y')); ?></strong>.
</p>

<p>
  He/She has been relieved from the services of this institution with effect from
  <strong><?php echo e(now()->format('d F Y')); ?></strong>, upon his/her request. All company property
  has been returned and no dues are outstanding.
</p>

<p>
  We wish <?php echo e($employee->first_name); ?> the very best in all future endeavours.
</p>

<p>Issued in good faith on the request of the individual.</p>

<div class="sig">
  <div class="sig-box">Principal / Director<br><?php echo e($school->school_name ?? 'School Name'); ?></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\relieving-letter.blade.php ENDPATH**/ ?>