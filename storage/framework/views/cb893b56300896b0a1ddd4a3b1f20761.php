<?php $__env->startSection('title', 'Exam Results'); ?>
<?php $__env->startSection('content'); ?>

<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem;">
  <h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin: 0;">Exams</h2>
  <div style="display: flex; gap: .5rem; flex-wrap: wrap;">
    <a href="<?php echo e(route('portal.student.exams.hall-ticket')); ?>" target="_blank"
       style="display: inline-flex; align-items: center; gap: .375rem; padding: .5rem 1rem; background: #d97706; color: #fff; text-decoration: none; border-radius: .5rem; font-size: .8125rem; font-weight: 600;"
       onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
      <svg style="width:.875rem;height:.875rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
      Hall Ticket
    </a>
    <a href="<?php echo e(route('portal.student.report.pdf')); ?>" target="_blank"
       style="display: inline-flex; align-items: center; gap: .375rem; padding: .5rem 1rem; background: #6366f1; color: #fff; text-decoration: none; border-radius: .5rem; font-size: .8125rem; font-weight: 600;"
       onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
      <svg style="width:.875rem;height:.875rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Report Card
    </a>
  </div>
</div>


<?php if($upcoming->count()): ?>
<div class="portal-card" style="margin-bottom: 1.25rem;">
  <div class="section-title" style="margin-bottom: .875rem;">
    <svg style="width:1rem;height:1rem;color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    Upcoming Exams
  </div>
  <div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: .875rem;">
      <thead>
        <tr style="border-bottom: 2px solid #f1f5f9;">
          <th style="text-align: left; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Exam</th>
          <th style="text-align: left; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Subject</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Date</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Time</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Max Marks</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $up): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $daysLeft = \Carbon\Carbon::parse($up->exam_date)->diffInDays(today(), false); ?>
        <tr class="divider-row">
          <td style="padding: .5rem .625rem; font-weight: 500; color: #1e293b;"><?php echo e($up->exam_name); ?></td>
          <td style="padding: .5rem .625rem; color: #475569;"><?php echo e($up->subject_name); ?></td>
          <td style="padding: .5rem .625rem; text-align: center;">
            <span style="font-weight: 600; color: <?php echo e(abs($daysLeft) <= 3 ? '#dc2626' : '#1e293b'); ?>;"><?php echo e(\Carbon\Carbon::parse($up->exam_date)->format('d M Y')); ?></span>
            <?php if(abs($daysLeft) == 0): ?> <span class="badge-red" style="margin-left: .25rem;">Today</span>
            <?php elseif(abs($daysLeft) == 1): ?> <span class="badge-slate" style="margin-left: .25rem;">Tomorrow</span>
            <?php elseif(abs($daysLeft) <= 3): ?> <span class="badge-red" style="margin-left: .25rem;"><?php echo e(abs($daysLeft)); ?>d</span>
            <?php else: ?> <span style="font-size: .72rem; color: #94a3b8; margin-left: .25rem;"><?php echo e(abs($daysLeft)); ?>d left</span>
            <?php endif; ?>
          </td>
          <td style="padding: .5rem .625rem; text-align: center; color: #64748b;">
            <?php if($up->start_time): ?> <?php echo e(\Carbon\Carbon::parse($up->start_time)->format('g:i A')); ?> <?php else: ?> — <?php endif; ?>
          </td>
          <td style="padding: .5rem .625rem; text-align: center; font-weight: 600; color: #1e293b;"><?php echo e($up->total_marks ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Past Results</h3>

<?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examName => $marks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="portal-card" style="margin-bottom: 1rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: .875rem; flex-wrap: wrap; gap: .5rem;">
    <h3 style="font-size: .9375rem; font-weight: 600; color: #1e293b;"><?php echo e($examName); ?></h3>
    <?php if($marks->first()->exam_date ?? null): ?>
      <span style="font-size: .75rem; color: #94a3b8;"><?php echo e(\Carbon\Carbon::parse($marks->first()->exam_date)->format('d M Y')); ?></span>
    <?php endif; ?>
  </div>
  <div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: .875rem;">
      <thead>
        <tr style="border-bottom: 2px solid #f1f5f9;">
          <th style="text-align: left; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Subject</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Marks</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Max</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">%</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Grade</th>
          <th style="text-align: center; padding: .5rem .625rem; font-size: .72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em;">Result</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $marks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $mpct = $m->max_marks > 0 ? round($m->marks_obtained/$m->max_marks*100, 1) : 0; ?>
        <tr class="divider-row">
          <td style="padding: .625rem .625rem; color: #1e293b; font-weight: 500;"><?php echo e($m->subject_name); ?></td>
          <td style="padding: .625rem .625rem; text-align: center; font-weight: 700; color: #1e293b;"><?php echo e($m->marks_obtained); ?></td>
          <td style="padding: .625rem .625rem; text-align: center; color: #94a3b8;"><?php echo e($m->max_marks); ?></td>
          <td style="padding: .625rem .625rem; text-align: center; font-weight: 600; color: <?php echo e($mpct >= 75 ? '#16a34a' : ($mpct >= 50 ? '#d97706' : '#dc2626')); ?>;"><?php echo e($mpct); ?>%</td>
          <td style="padding: .625rem .625rem; text-align: center;">
            <?php if($m->grade ?? null): ?>
              <span class="badge-blue"><?php echo e($m->grade); ?></span>
            <?php else: ?>
              <span style="color: #94a3b8;">—</span>
            <?php endif; ?>
          </td>
          <td style="padding: .625rem .625rem; text-align: center;">
            <?php if(isset($m->pass_status)): ?>
              <span class="<?php echo e(strtolower($m->pass_status) === 'pass' ? 'badge-green' : ($m->pass_status === 'Absent' ? 'badge-slate' : 'badge-red')); ?>"><?php echo e($m->pass_status); ?></span>
            <?php else: ?>
              <span style="color: #94a3b8;">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr style="background: #f8fafc; border-top: 2px solid #f1f5f9;">
          <td style="padding: .625rem .625rem; font-size: .75rem; font-weight: 700; color: #475569;">Total</td>
          <td style="padding: .625rem .625rem; text-align: center; font-weight: 700; color: #1e293b;"><?php echo e($marks->sum('marks_obtained')); ?></td>
          <td style="padding: .625rem .625rem; text-align: center; color: #94a3b8;"><?php echo e($marks->sum('max_marks')); ?></td>
          <td style="padding: .625rem .625rem; text-align: center; font-weight: 700; color: #1e293b;">
            <?php $totalPct = $marks->sum('max_marks') > 0 ? round($marks->sum('marks_obtained')/$marks->sum('max_marks')*100, 1) : 0; ?>
            <span style="color: <?php echo e($totalPct >= 75 ? '#16a34a' : ($totalPct >= 50 ? '#d97706' : '#dc2626')); ?>"><?php echo e($totalPct); ?>%</span>
          </td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="portal-card" style="text-align: center; padding: 3rem 1rem;">
    <svg style="width: 3rem; height: 3rem; margin: 0 auto .875rem; color: #e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    <p style="color: #94a3b8; font-size: .875rem;">No exam results available yet</p>
  </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\exams.blade.php ENDPATH**/ ?>