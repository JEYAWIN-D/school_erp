<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; }
body { font-family: 'Times New Roman', serif; font-size: 12px; color: #111; margin: 0; padding: 30px 40px; background: #fff; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #991b1b; padding-bottom: 16px; }
.school-name { font-size: 22px; font-weight: bold; color: #1e3a8a; }
.school-info  { font-size: 11px; color: #555; margin-top: 3px; }
.doc-title    { font-size: 17px; font-weight: bold; text-align: center; margin: 18px 0 6px; letter-spacing: 1px; text-decoration: underline; color: #991b1b; }
.ref-row      { display: flex; justify-content: space-between; font-size: 11px; color: #555; margin-bottom: 14px; }
.salutation   { margin-bottom: 12px; font-size: 13px; }
.body-text    { line-height: 1.8; font-size: 12.5px; text-align: justify; }
.highlight    { font-weight: bold; }
table.details { width: 100%; border-collapse: collapse; margin: 16px 0; background: #fff5f5; border: 1px solid #fca5a5; }
table.details td { padding: 7px 12px; font-size: 12px; border-bottom: 1px solid #fecaca; }
table.details td:first-child { font-weight: bold; width: 35%; color: #7f1d1d; }
.warning-box  { background: #fef2f2; border: 2px solid #ef4444; border-radius: 4px; padding: 12px 16px; margin: 16px 0; font-size: 12px; }
.warning-box strong { color: #dc2626; }
.footer       { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
.sign-block   { text-align: center; width: 38%; }
.sign-line    { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; font-size: 11px; }
.ack          { margin-top: 30px; border-top: 1px dashed #aaa; padding-top: 16px; font-size: 11px; color: #555; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
  <div class="school-info"><?php echo e($school->address ?? ''); ?></div>
  <div class="school-info">Ph: <?php echo e($school->phone ?? ''); ?> | Email: <?php echo e($school->email ?? ''); ?></div>
</div>

<div class="doc-title">WARNING LETTER</div>

<div class="ref-row">
  <span>Ref: WRN/<?php echo e($student->admission_number); ?>/<?php echo e(now()->format('Y')); ?></span>
  <span>Date: <?php echo e(now()->format('d F Y')); ?></span>
</div>

<div class="salutation">
  To,<br>
  <strong>The Parent/Guardian of <?php echo e($student->full_name); ?></strong><br>
  <?php if($student->residential_address): ?><?php echo e($student->residential_address); ?><br><?php endif; ?>
</div>

<div class="body-text">
  <p>Dear Parent/Guardian,</p>

  <p>We regret to inform you that your ward <span class="highlight"><?php echo e($student->full_name); ?></span> (Admission No: <strong><?php echo e($student->admission_number); ?></strong>), studying in <span class="highlight"><?php echo e($student->currentEnrollment?->class?->name); ?> <?php echo e($student->currentEnrollment?->section?->name); ?></span>, has been involved in a disciplinary incident as detailed below:</p>
</div>

<table class="details">
  <tr><td>Student Name</td><td><?php echo e($student->full_name); ?></td></tr>
  <tr><td>Admission Number</td><td><?php echo e($student->admission_number); ?></td></tr>
  <tr><td>Class</td><td><?php echo e($student->currentEnrollment?->class?->name); ?> <?php echo e($student->currentEnrollment?->section?->name); ?></td></tr>
  <tr><td>Incident Date</td><td><?php echo e($incident->incident_date?->format('d F Y')); ?></td></tr>
  <tr><td>Incident Type</td><td><?php echo e(ucfirst(str_replace('_', ' ', $incident->incident_type))); ?></td></tr>
  <tr><td>Description</td><td><?php echo e($incident->description); ?></td></tr>
  <?php if($incident->action_taken): ?>
  <tr><td>Action Taken</td><td><?php echo e($incident->action_taken); ?></td></tr>
  <?php endif; ?>
</table>

<div class="warning-box">
  <strong>WARNING:</strong> This is a formal warning letter. Repetition of such behaviour may lead to serious disciplinary action including suspension or expulsion from the school. We expect your full cooperation in counselling your ward to maintain the decorum and discipline of the institution.
</div>

<div class="body-text">
  <p>We request you to kindly meet the Class Teacher / Principal at the earliest to discuss this matter and ensure that such behaviour is not repeated in the future.</p>
  <p>Your cooperation in this regard will be deeply appreciated.</p>
</div>

<div class="footer">
  <div class="sign-block">
    <div class="sign-line">Class Teacher</div>
  </div>
  <div class="sign-block">
    <div class="sign-line">Principal's Signature</div>
  </div>
  <div class="sign-block">
    <div class="sign-line">School Stamp</div>
  </div>
</div>

<div class="ack">
  <p><strong>Acknowledgement:</strong> I, the parent/guardian of <?php echo e($student->full_name); ?>, have received this warning letter and have noted its contents.</p>
  <br>
  <table width="100%"><tr>
    <td width="40%">Signature of Parent/Guardian: ___________________</td>
    <td width="30%">Date: ___________________</td>
    <td width="30%">Mobile: ___________________</td>
  </tr></table>
</div>

</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\warning-letter.blade.php ENDPATH**/ ?>