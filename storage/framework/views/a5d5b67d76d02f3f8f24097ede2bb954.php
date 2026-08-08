<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
.header { text-align: center; border-bottom: 2px solid #2c5282; padding-bottom: 10px; margin-bottom: 14px; }
.school-name { font-size: 16px; font-weight: bold; color: #2c5282; }
h2 { font-size: 13px; color: #1e40af; margin: 0 0 4px; }
.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
.meta-table td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 11px; }
.meta-table td:first-child { font-weight: bold; background: #f0f4ff; width: 30%; }
.section { margin-bottom: 14px; }
.section-title { background: #e8f0fe; padding: 5px 10px; font-weight: bold; font-size: 11px;
  border-left: 3px solid #2c5282; margin-bottom: 6px; }
.content-box { border: 1px solid #e2e8f0; padding: 8px 10px; border-radius: 4px; min-height: 30px; font-size: 11px; line-height: 1.5; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
.badge-green { background: #d1fae5; color: #065f46; }
.badge-blue  { background: #dbeafe; color: #1e40af; }
.badge-amber { background: #fef3c7; color: #92400e; }
.badge-red   { background: #fee2e2; color: #991b1b; }
.sign-row { display: flex; justify-content: space-between; margin-top: 30px; }
.sign-box { text-align: center; }
.sign-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 4px; font-size: 10px; width: 150px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'DASA EduERP'); ?></div>
  <div style="color:#666;font-size:11px"><?php echo e($school?->address ?? ''); ?></div>
  <h2 style="margin-top:6px">LESSON PLAN</h2>
</div>

<table class="meta-table">
  <tr><td>Class</td><td><?php echo e($plan->class?->name); ?></td><td>Subject</td><td><?php echo e($plan->subject?->name); ?></td></tr>
  <tr><td>Topic</td><td colspan="3"><?php echo e($plan->topic); ?></td></tr>
  <tr><td>Plan Date</td><td><?php echo e($plan->plan_date?->format('d M Y')); ?></td><td>Period No.</td><td><?php echo e($plan->period_number ?? '—'); ?></td></tr>
  <tr><td>Duration</td><td><?php echo e($plan->duration_minutes ? $plan->duration_minutes . ' mins' : '—'); ?></td>
      <td>Teaching Method</td><td><?php echo e($plan->teaching_method ?? '—'); ?></td></tr>
  <tr><td>Prepared by</td><td><?php echo e($plan->createdBy?->name ?? '—'); ?></td>
      <td>Status</td>
      <td>
        <?php $st = $plan->status ?? 'draft'; ?>
        <span class="badge <?php echo e(match($st){ 'approved'=>'badge-green','rejected'=>'badge-red','submitted'=>'badge-blue',default=>'badge-amber' }); ?>">
          <?php echo e(ucfirst($st)); ?>

        </span>
        <?php if($plan->is_completed): ?> <span class="badge badge-green" style="margin-left:4px">Completed</span> <?php endif; ?>
      </td>
  </tr>
  <?php if($plan->syllabus): ?>
  <tr><td>Syllabus Topic</td><td colspan="3"><?php echo e($plan->syllabus->chapter ?? ''); ?> <?php echo e($plan->syllabus->topic ?? $plan->syllabus->name ?? ''); ?></td></tr>
  <?php endif; ?>
</table>

<?php if($plan->learning_objectives): ?>
<div class="section">
  <div class="section-title">Learning Objectives</div>
  <div class="content-box"><?php echo e($plan->learning_objectives); ?></div>
</div>
<?php endif; ?>

<?php if($plan->resources_required): ?>
<div class="section">
  <div class="section-title">Resources / Materials Required</div>
  <div class="content-box"><?php echo e($plan->resources_required); ?></div>
</div>
<?php endif; ?>

<?php if($plan->teacher_notes): ?>
<div class="section">
  <div class="section-title">Teacher's Notes / Methodology</div>
  <div class="content-box"><?php echo e($plan->teacher_notes); ?></div>
</div>
<?php endif; ?>

<?php if($plan->hod_decision): ?>
<div class="section">
  <div class="section-title">HOD Review</div>
  <table class="meta-table">
    <tr><td>Decision</td><td><span class="badge <?php echo e($plan->hod_decision === 'approved' ? 'badge-green' : 'badge-red'); ?>"><?php echo e(ucfirst($plan->hod_decision)); ?></span></td>
        <td>Reviewed by</td><td><?php echo e($plan->hodReviewedBy?->name ?? '—'); ?></td></tr>
    <?php if($plan->hod_remarks): ?><tr><td>Remarks</td><td colspan="3"><?php echo e($plan->hod_remarks); ?></td></tr><?php endif; ?>
    <tr><td>Reviewed on</td><td colspan="3"><?php echo e($plan->hod_reviewed_at?->format('d M Y h:i A')); ?></td></tr>
  </table>
</div>
<?php endif; ?>

<div class="sign-row">
  <div class="sign-box"><div class="sign-line">Class Teacher</div></div>
  <div class="sign-box"><div class="sign-line">HOD / Subject Head</div></div>
  <div class="sign-box"><div class="sign-line">Principal</div></div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\lesson-plan.blade.php ENDPATH**/ ?>