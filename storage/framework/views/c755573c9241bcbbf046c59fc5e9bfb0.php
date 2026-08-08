<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: 'Times New Roman', serif; font-size: 12px; margin: 0; padding: 25px; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
.school-name { font-size: 18px; font-weight: bold; }
.school-info { font-size: 10px; color: #555; margin-top: 3px; }
.app-title { font-size: 14px; font-weight: bold; margin: 12px 0 4px; text-decoration: underline; }
.app-number { font-size: 11px; font-weight: bold; color: #333; text-align: right; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
td { padding: 5px 8px; vertical-align: top; border-bottom: 1px dotted #ccc; }
td:first-child { width: 40%; font-weight: bold; color: #444; }
.section-title { font-size: 11px; font-weight: bold; background: #f5f5f5; padding: 5px 8px; margin-top: 12px; border-left: 3px solid #333; }
.status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; }
.sign-line { border-top: 1px solid #000; width: 150px; text-align: center; padding-top: 4px; font-size: 10px; margin-top: 40px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <?php if($school?->address): ?><div class="school-info"><?php echo e($school->address); ?></div><?php endif; ?>
  <?php if($school?->phone): ?><div class="school-info">Ph: <?php echo e($school->phone); ?><?php if($school?->email): ?> | <?php echo e($school->email); ?><?php endif; ?></div><?php endif; ?>
  <?php if($school?->affiliation_number): ?><div class="school-info">Affiliation No: <?php echo e($school->affiliation_number); ?></div><?php endif; ?>
</div>

<div class="app-number">Application No: <?php echo e($app->application_number); ?></div>
<div class="app-title">ADMISSION APPLICATION FORM</div>
<div style="font-size:10px;color:#666;margin-bottom:12px;">
  Form: <?php echo e($app->formConfig?->title); ?>

  <?php if($app->formConfig?->class): ?> | Class: <?php echo e($app->formConfig->class->name); ?><?php endif; ?>
  | Submitted: <?php echo e($app->created_at->format('d M Y')); ?>

  | Status: <strong><?php echo e(ucwords(str_replace('_',' ',$app->status))); ?></strong>
</div>

<div class="section-title">STUDENT DETAILS</div>
<table>
  <?php $__currentLoopData = $app->form_data ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if(!in_array($key, ['parent_name','parent_mobile','parent_email','address'])): ?>
  <tr>
    <td><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></td>
    <td><?php echo e($value); ?></td>
  </tr>
  <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<div class="section-title">PARENT / GUARDIAN DETAILS</div>
<table>
  <tr><td>Parent/Guardian Name</td><td><?php echo e($app->parent_name ?? ($app->form_data['parent_name'] ?? '—')); ?></td></tr>
  <tr><td>Mobile</td><td><?php echo e($app->parent_mobile); ?></td></tr>
  <tr><td>Email</td><td><?php echo e($app->parent_email ?? ($app->form_data['parent_email'] ?? '—')); ?></td></tr>
  <?php if(!empty($app->form_data['address'])): ?>
  <tr><td>Address</td><td><?php echo e($app->form_data['address']); ?></td></tr>
  <?php endif; ?>
</table>

<div class="section-title">DOCUMENTS SUBMITTED</div>
<table>
  <?php $__empty_1 = true; $__currentLoopData = $app->documents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <tr>
    <td><?php echo e(ucwords(str_replace('_', ' ', $name))); ?></td>
    <td>Uploaded</td>
  </tr>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <tr><td colspan="2">No documents uploaded</td></tr>
  <?php endif; ?>
</table>

<?php if($app->admin_notes): ?>
<div class="section-title">ADMIN NOTES</div>
<p style="padding:5px 8px;font-style:italic;color:#555;"><?php echo e($app->admin_notes); ?></p>
<?php endif; ?>

<div class="footer">
  <div class="sign-line">Applicant / Parent Signature</div>
  <div class="sign-line">Admin / Admissions Office</div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\application-form.blade.php ENDPATH**/ ?>