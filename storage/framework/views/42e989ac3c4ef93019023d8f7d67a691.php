<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a; }
  .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; }
  .school-name { font-size: 15px; font-weight: bold; color: #1e3a5f; }
  .report-title { font-size: 12px; font-weight: bold; margin-top: 4px; }
  .sub { font-size: 9px; color: #555; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e3a5f; color: white; padding: 5px 6px; font-size: 8px; text-align: center; border: 1px solid #15305a; }
  td { padding: 4px 6px; border: 1px solid #d1dce8; font-size: 9px; text-align: center; }
  .name-col { text-align: left; }
  tr:nth-child(even) td { background: #f7f9fc; }
  .fail { color: #dc2626; font-weight: bold; }
  .top3 { background: #fef9c3 !important; }
  .footer { margin-top: 14px; font-size: 8px; color: #888; display: flex; justify-content: space-between; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'SCHOOL NAME'); ?></div>
  <div class="report-title">Tabulation Sheet &mdash; <?php echo e($exam->name); ?></div>
  <div class="sub">Class: <?php echo e($class->name); ?> &nbsp;|&nbsp; Generated: <?php echo e(now()->format('d M Y H:i')); ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Adm No</th>
      <th class="name-col">Student Name</th>
      <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th><?php echo e(Str::limit($subject->name, 10)); ?><br>(<?php echo e($subject->pivot->max_marks ?? 100); ?>)</th>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <th>Total</th>
      <th>%</th>
      <th>Grade</th>
      <th>Rank</th>
      <th>Result</th>
    </tr>
  </thead>
  <tbody>
    <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $rowMarks   = $marksMap[$enrollment->student_id] ?? [];
      $total      = $totals[$enrollment->id] ?? 0;
      $maxTotal   = $subjects->sum(fn($s) => $s->pivot->max_marks ?? 100);
      $percentage = $maxTotal > 0 ? round($total / $maxTotal * 100, 1) : 0;
      $hasFail    = false;
      foreach ($rowMarks as $m) {
          if (!$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)) { $hasFail = true; break; }
      }
      $grade = '—';
      if ($gradingScheme) {
          foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
              if ($percentage >= $range->min_percentage) { $grade = $range->grade; break; }
          }
      }
      $rank = $ranks[$enrollment->id] ?? '—';
    ?>
    <tr class="<?php echo e($rank <= 3 ? 'top3' : ''); ?>">
      <td><?php echo e($loop->iteration); ?></td>
      <td><?php echo e($enrollment->student->admission_no ?? $enrollment->student->admission_number ?? '—'); ?></td>
      <td class="name-col"><?php echo e($enrollment->student->first_name); ?> <?php echo e($enrollment->student->last_name); ?></td>
      <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $m = $rowMarks[$subject->id] ?? null; ?>
        <td class="<?php echo e($m && !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35) ? 'fail' : ''); ?>">
          <?php echo e($m ? ($m->is_absent ? 'AB' : $m->marks_obtained) : '—'); ?>

        </td>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <td><strong><?php echo e($total); ?></strong></td>
      <td><?php echo e($percentage); ?>%</td>
      <td><?php echo e($grade); ?></td>
      <td><?php echo e($rank); ?></td>
      <td class="<?php echo e($hasFail ? 'fail' : ''); ?>"><?php echo e($hasFail ? 'F' : 'P'); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>

<div class="footer">
  <span>Total Students: <?php echo e($enrollments->count()); ?></span>
  <span>Passed: <?php echo e(collect($enrollments)->filter(fn($e) => !collect($marksMap[$e->student_id] ?? [])->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)))->count()); ?></span>
  <span>Failed: <?php echo e(collect($enrollments)->filter(fn($e) => collect($marksMap[$e->student_id] ?? [])->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)))->count()); ?></span>
  <span>Class Average: <?php echo e($enrollments->count() > 0 ? round(collect($totals)->avg(), 1) : 0); ?></span>
  <span>Examiner/Principal: ____________________</span>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\tabulation.blade.php ENDPATH**/ ?>