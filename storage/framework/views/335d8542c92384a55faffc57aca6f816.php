<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #000; padding: 30px; line-height: 1.8; }
.header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 20px; }
.school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
.letter-title { font-size: 14px; text-decoration: underline; font-weight: bold; margin: 15px 0; text-align: center; }
table { width: 100%; border-collapse: collapse; margin: 12px 0; }
td { padding: 6px 10px; border: 1px solid #ddd; }
td:first-child { font-weight: bold; background: #f5f5f5; width: 40%; }
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
<p>To,<br><?php echo e($student->father_name ?? 'Parent/Guardian'); ?><br>Parent/Guardian of <?php echo e($student->full_name); ?></p>
<div class="letter-title">HOSTEL ROOM ALLOTMENT LETTER</div>
<p>Dear Parent/Guardian,</p>
<p>This is to inform you that the following room has been allotted to your ward in the hostel of <?php echo e($school->school_name ?? 'our school'); ?>:</p>
<table>
  <tr><td>Student Name</td><td><?php echo e($student->full_name); ?></td></tr>
  <tr><td>Admission Number</td><td><?php echo e($student->admission_number); ?></td></tr>
  <tr><td>Class & Section</td><td><?php echo e($enrollment?->class?->name); ?> <?php echo e($enrollment?->section?->name); ?></td></tr>
  <tr><td>Hostel Name</td><td><?php echo e($allotment->room?->hostel?->name); ?></td></tr>
  <tr><td>Room Number</td><td><?php echo e($allotment->room?->room_number); ?></td></tr>
  <tr><td>Allotment Date</td><td><?php echo e($allotment->allotment_date?->format('d/m/Y')); ?></td></tr>
  <tr><td>Monthly Hostel Fee</td><td>₹<?php echo e(number_format($allotment->monthly_fee, 2)); ?></td></tr>
</table>
<p>Please report to the Warden, <?php echo e($allotment->room?->hostel?->name); ?>, on the allotment date with original admission documents.</p>
<p>For further enquiries, contact the Hostel Warden: <?php echo e($allotment->room?->hostel?->warden_mobile ?? '—'); ?></p>
<div class="footer">
  <div><div class="sign-line">Warden</div></div>
  <div><div class="sign-line">Principal</div></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\room-allotment-letter.blade.php ENDPATH**/ ?>