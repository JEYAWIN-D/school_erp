<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Card</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; padding: 24px; }
  .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 14px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-start; }
  .school-name { font-size: 20px; font-weight: 700; color: #1d4ed8; }
  .school-sub { font-size: 11px; color: #64748b; margin-top: 3px; }
  .doc-title { font-size: 16px; font-weight: 700; color: #1e293b; text-align: right; }
  .doc-date { font-size: 10px; color: #94a3b8; text-align: right; margin-top: 3px; }
  .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-bottom: 18px; }
  .info-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; }
  .info-row { display: flex; gap: 24px; font-size: 11px; }
  .info-item { display: flex; flex-direction: column; }
  .info-key { color: #94a3b8; font-size: 9px; }
  .info-val { font-weight: 600; color: #1e293b; }
  .exam-section { margin-bottom: 22px; }
  .exam-title { font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 7px 10px; background: #eff6ff; border-left: 4px solid #1d4ed8; border-radius: 0 4px 4px 0; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
  .exam-date { font-size: 10px; color: #64748b; font-weight: 400; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background: #f8fafc; padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; border-bottom: 2px solid #e2e8f0; }
  td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; }
  tfoot td { background: #f8fafc; font-weight: 700; }
  .pass { background: #dcfce7; color: #16a34a; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .fail { background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .absent { background: #f1f5f9; color: #64748b; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .grade-badge { background: #dbeafe; color: #1d4ed8; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 700; }
  .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; display: flex; justify-content: space-between; }
  .no-results { text-align: center; padding: 40px 0; color: #94a3b8; font-size: 13px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="school-name"><?php echo e(config('app.name')); ?></div>
    <div class="school-sub">Student Report Card — Academic Year <?php echo e(now()->year); ?>-<?php echo e(now()->addYear()->year); ?></div>
  </div>
  <div>
    <div class="doc-title">Report Card</div>
    <div class="doc-date">Generated: <?php echo e(now()->format('d M Y, g:i A')); ?></div>
  </div>
</div>

<div class="info-box">
  <div class="info-label">Student Information</div>
  <div class="info-row">
    <div class="info-item"><span class="info-key">Name</span><span class="info-val"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></span></div>
    <div class="info-item"><span class="info-key">Admission No.</span><span class="info-val"><?php echo e($student->admission_no); ?></span></div>
    <?php if($enrollment): ?>
    <div class="info-item"><span class="info-key">Class</span><span class="info-val"><?php echo e($enrollment->class_name); ?></span></div>
    <div class="info-item"><span class="info-key">Section</span><span class="info-val"><?php echo e($enrollment->section_name); ?></span></div>
    <?php endif; ?>
    <?php if($student->dob ?? null): ?>
    <div class="info-item"><span class="info-key">Date of Birth</span><span class="info-val"><?php echo e(\Carbon\Carbon::parse($student->dob)->format('d M Y')); ?></span></div>
    <?php endif; ?>
  </div>
</div>

<?php if($results->isEmpty()): ?>
  <div class="no-results">No exam results available yet.</div>
<?php else: ?>
  <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examName => $marks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php
    $totalObtained = $marks->sum('marks_obtained');
    $totalMax = $marks->sum('max_marks');
    $totalPct = $totalMax > 0 ? round($totalObtained / $totalMax * 100, 1) : 0;
  ?>
  <div class="exam-section">
    <div class="exam-title">
      <span><?php echo e($examName); ?></span>
      <?php if($marks->first()->exam_date ?? null): ?>
        <span class="exam-date"><?php echo e(\Carbon\Carbon::parse($marks->first()->exam_date)->format('d M Y')); ?></span>
      <?php endif; ?>
    </div>
    <table>
      <thead>
        <tr>
          <th>Subject</th>
          <th style="text-align:center">Marks Obtained</th>
          <th style="text-align:center">Max Marks</th>
          <th style="text-align:center">Percentage</th>
          <th style="text-align:center">Grade</th>
          <th style="text-align:center">Result</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $marks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $pct = $m->max_marks > 0 ? round($m->marks_obtained / $m->max_marks * 100, 1) : 0; ?>
        <tr>
          <td><?php echo e($m->subject_name); ?></td>
          <td style="text-align:center;font-weight:700"><?php echo e($m->marks_obtained); ?></td>
          <td style="text-align:center;color:#94a3b8"><?php echo e($m->max_marks); ?></td>
          <td style="text-align:center;font-weight:700;color:<?php echo e($pct >= 75 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#dc2626')); ?>"><?php echo e($pct); ?>%</td>
          <td style="text-align:center"><?php if($m->grade ?? null): ?><span class="grade-badge"><?php echo e($m->grade); ?></span><?php else: ?> —<?php endif; ?></td>
          <td style="text-align:center">
            <?php $ps = strtolower($m->pass_status ?? ''); ?>
            <?php if($ps === 'pass'): ?><span class="pass">Pass</span>
            <?php elseif($ps === 'absent'): ?><span class="absent">Absent</span>
            <?php else: ?><span class="fail">Fail</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Total</td>
          <td style="text-align:center"><?php echo e($totalObtained); ?></td>
          <td style="text-align:center;color:#94a3b8"><?php echo e($totalMax); ?></td>
          <td style="text-align:center;color:<?php echo e($totalPct >= 75 ? '#16a34a' : ($totalPct >= 50 ? '#d97706' : '#dc2626')); ?>"><?php echo e($totalPct); ?>%</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<div class="footer">
  <span><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?> — Adm# <?php echo e($student->admission_no); ?></span>
  <span>This is a computer-generated report card.</span>
</div>

</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\portal\pdf\report-card.blade.php ENDPATH**/ ?>