<?php $__env->startSection('title', 'Homework Submissions'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Homework Submissions</h1>
      <p class="text-slate-500 text-sm mt-0.5">
        <?php echo e($homework->subject?->name); ?> — <?php echo e($homework->class?->name); ?><?php echo e($homework->section ? ' / '.$homework->section?->name : ''); ?>

        <span class="ml-2 text-slate-400">Due: <?php echo e($homework->due_date?->format('d M Y')); ?></span>
      </p>
      <?php if($homework->title): ?><p class="font-medium text-slate-700 text-sm mt-0.5"><?php echo e($homework->title); ?></p><?php endif; ?>
    </div>
    <a href="<?php echo e(route('academics.homework')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <?php
    $total     = $enrollments->count();
    $submitted = collect($submissions)->where('status','submitted')->count() + collect($submissions)->where('status','late')->count() + collect($submissions)->where('status','evaluated')->count();
    $pct       = $total > 0 ? round($submitted/$total*100) : 0;
  ?>
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-slate-700">Submission Rate</span>
      <span class="font-bold <?php echo e($pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-amber-600' : 'text-red-500')); ?>"><?php echo e($submitted); ?>/<?php echo e($total); ?> (<?php echo e($pct); ?>%)</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-2">
      <div class="h-2 rounded-full bg-green-500" style="width:<?php echo e($pct); ?>%"></div>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('academics.homework.submissions.save', $homework->id)); ?>">
    <?php echo csrf_field(); ?>
    <div class="card overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100"><tr>
          <th class="th">Student</th>
          <th class="th">Adm. No.</th>
          <th class="th">Status</th>
          <th class="th">Submitted On</th>
          <th class="th">Score <?php if($homework->max_score): ?>(/ <?php echo e($homework->max_score); ?>)<?php endif; ?></th>
          <th class="th">Feedback</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $s = $submissions[$enrollment->student_id] ?? null; ?>
          <tr class="hover:bg-slate-50">
            <td class="td font-medium"><?php echo e($enrollment->student?->full_name); ?></td>
            <td class="td text-xs font-mono text-slate-500"><?php echo e($enrollment->student?->admission_number); ?></td>
            <td class="td">
              <select name="submissions[<?php echo e($enrollment->student_id); ?>][status]" class="select text-xs py-1 w-32">
                <option value="not_submitted" <?php if(($s?->status ?? 'not_submitted')==='not_submitted'): echo 'selected'; endif; ?>>Not Submitted</option>
                <option value="submitted" <?php if($s?->status==='submitted'): echo 'selected'; endif; ?>>Submitted</option>
                <option value="late" <?php if($s?->status==='late'): echo 'selected'; endif; ?>>Late</option>
                <option value="evaluated" <?php if($s?->status==='evaluated'): echo 'selected'; endif; ?>>Evaluated</option>
              </select>
            </td>
            <td class="td">
              <input type="date" name="submissions[<?php echo e($enrollment->student_id); ?>][submitted_at]"
                value="<?php echo e($s?->submitted_at?->format('Y-m-d')); ?>"
                class="input text-xs py-1 w-36">
            </td>
            <td class="td">
              <input type="number" name="submissions[<?php echo e($enrollment->student_id); ?>][score]"
                value="<?php echo e($s?->score); ?>" step="0.5" min="0"
                <?php if($homework->max_score): ?> max="<?php echo e($homework->max_score); ?>" <?php endif; ?>
                class="input text-xs py-1 w-20">
            </td>
            <td class="td">
              <input type="text" name="submissions[<?php echo e($enrollment->student_id); ?>][teacher_feedback]"
                value="<?php echo e($s?->teacher_feedback); ?>" placeholder="Remarks"
                class="input text-xs py-1 w-full min-w-32">
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <button type="submit" class="btn btn-primary">Save Submission Status</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\homework-submissions.blade.php ENDPATH**/ ?>