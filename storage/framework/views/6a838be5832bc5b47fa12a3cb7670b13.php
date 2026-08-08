<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #000; padding: 30px; }
.header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 20px; }
.school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
.letter-title { font-size: 14px; text-decoration: underline; font-weight: bold; margin: 15px 0; text-align: center; }
p { line-height: 1.8; margin: 8px 0; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; }
td { padding: 6px 10px; border: 1px solid #ddd; }
td:first-child { font-weight: bold; background: #f5f5f5; width: 40%; }
.warning-box { background: #fff3cd; border: 1px solid #ffc107; padding: 12px; margin: 15px 0; border-radius: 4px; }
.footer { margin-top: 40px; display: flex; justify-content: space-between; }
.sign-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; font-size: 11px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? 'DASA EduERP'); ?></div>
  <div><?php echo e($school->address ?? ''); ?></div>
</div>
<p>Date: <?php echo e(now()->format('d/m/Y')); ?></p>
<p>To,<br><?php echo e($student->father_name ?? $student->guardian_name ?? 'Parent/Guardian'); ?><br>Parent/Guardian of <?php echo e($student->full_name); ?></p>
<div class="letter-title">ATTENDANCE SHORTAGE NOTICE</div>
<p>Dear Parent/Guardian,</p>
<p>This is to inform you that your ward <strong><?php echo e($student->full_name); ?></strong> (Admission No: <strong><?php echo e($student->admission_number); ?></strong>) has shortage of attendance as detailed below:</p>
<table>
  <tr><td>Student Name</td><td><?php echo e($student->full_name); ?></td></tr>
  <tr><td>Class & Section</td><td><?php echo e($enrollment?->class?->name); ?> <?php echo e($enrollment?->section?->name); ?></td></tr>
  <tr><td>Total Working Days</td><td><?php echo e($stats['total_days']); ?></td></tr>
  <tr><td>Days Present</td><td><?php echo e($stats['present']); ?></td></tr>
  <tr><td>Days Absent</td><td><?php echo e($stats['absent']); ?></td></tr>
  <tr><td>Attendance Percentage</td><td><strong><?php echo e($stats['percentage']); ?>%</strong></td></tr>
  <tr><td>Required Minimum</td><td>75%</td></tr>
</table>
<div class="warning-box">
  ⚠️ <strong>Notice:</strong> As per school rules, a minimum of 75% attendance is required for eligibility to appear in examinations. Your ward is currently below the required threshold.
</div>
<p>You are requested to meet the class teacher/principal at the earliest and regularise your ward's attendance. Please note that condonation may be granted by the Principal on genuine medical grounds with supporting documents.</p>
<p>We look forward to your cooperation.</p>
<p>Yours sincerely,</p>
<div class="footer">
  <div><div class="sign-line">Class Teacher</div></div>
  <div><div class="sign-line">Principal</div></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\shortage-letter.blade.php ENDPATH**/ ?>