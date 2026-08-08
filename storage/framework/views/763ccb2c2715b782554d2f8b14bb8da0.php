<?php $__env->startSection('title', 'Submissions'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title"><?php echo e($assignment->title); ?> — Submissions</h1>
      <p class="text-sm text-slate-400"><?php echo e($submissions->total()); ?> submission(s) &bull; Max: <?php echo e($assignment->max_marks); ?> marks</p>
    </div>
    <a href="<?php echo e(route('lms.assignments.index', $assignment->course_id)); ?>" class="btn-secondary btn-sm">← Assignments</a>
  </div>

  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Submitted</th>
          <th class="th">Status</th>
          <th class="th text-center">Score</th>
          <th class="th">Feedback</th>
          <th class="th text-center">Actions</th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr" x-data="{ open: false }">
            <td class="td">
              <div class="font-medium text-slate-700"><?php echo e($sub->student_name); ?></div>
              <div class="text-xs text-slate-400 font-mono"><?php echo e($sub->admission_no); ?></div>
            </td>
            <td class="td text-xs text-slate-500">
              <?php echo e(\Carbon\Carbon::parse($sub->created_at)->format('d M Y H:i')); ?>

              <?php if($sub->is_late): ?> <span class="badge-red text-xs ml-1">Late</span> <?php endif; ?>
            </td>
            <td class="td">
              <?php if($sub->evaluated_at): ?>
                <span class="badge-green text-xs">Evaluated</span>
              <?php else: ?>
                <span class="badge-amber text-xs">Pending</span>
              <?php endif; ?>
            </td>
            <td class="td text-center">
              <?php if($sub->evaluated_at): ?>
                <span class="font-semibold <?php echo e($sub->score >= ($assignment->max_marks * 0.5) ? 'text-green-600' : 'text-red-600'); ?>">
                  <?php echo e($sub->score); ?>/<?php echo e($assignment->max_marks); ?>

                </span>
              <?php else: ?>
                <span class="text-slate-400">—</span>
              <?php endif; ?>
            </td>
            <td class="td text-xs text-slate-500 max-w-xs truncate">
              <?php echo e($sub->feedback ?? '—'); ?>

            </td>
            <td class="td text-center">
              <div class="flex items-center justify-center gap-2">
                <a href="<?php echo e(Storage::url($sub->file_path)); ?>" target="_blank" class="btn-xs btn-secondary">Download</a>
                <button @click="open = !open" class="btn-xs btn-primary">Evaluate</button>
              </div>
            </td>
          </tr>
          
          <tr x-show="open" x-transition class="bg-blue-50">
            <td colspan="6" class="px-4 py-3">
              <form method="POST" action="<?php echo e(route('lms.assignments.evaluate', $sub->id)); ?>" class="flex flex-wrap items-end gap-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div>
                  <label class="label text-xs">Score (max <?php echo e($assignment->max_marks); ?>)</label>
                  <input type="number" name="score" value="<?php echo e($sub->score); ?>" min="0" max="<?php echo e($assignment->max_marks); ?>" step="0.5" class="input text-sm w-24" required>
                </div>
                <div class="flex-1 min-w-48">
                  <label class="label text-xs">Feedback</label>
                  <input type="text" name="feedback" value="<?php echo e($sub->feedback); ?>" placeholder="Optional remarks..." class="input text-sm w-full">
                </div>
                <button type="submit" class="btn-sm btn-primary mb-0.5">Save Evaluation</button>
                <button type="button" @click="open = false" class="btn-sm btn-secondary mb-0.5">Cancel</button>
              </form>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="6" class="td text-center text-slate-400 py-8">No submissions yet</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4"><?php echo e($submissions->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\submission-list.blade.php ENDPATH**/ ?>