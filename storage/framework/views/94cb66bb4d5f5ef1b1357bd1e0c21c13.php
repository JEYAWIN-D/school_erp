<?php $__env->startSection('title', 'Homework Completion Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Homework Completion Report</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Class *</label>
        <select name="class_id" required class="select w-36">
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Subject</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  <?php if(isset($homeworks)): ?>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Title / Description','Subject','Due Date','Assigned By','Submitted','Not Submitted','Evaluated','% Completion','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $homeworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $sub       = $hw->submissions;
          $submitted = $sub->whereIn('status', ['submitted','late','evaluated'])->count();
          $evaluated = $sub->where('status', 'evaluated')->count();
          $notSub    = $totalStudents - $submitted;
          $pct       = $totalStudents > 0 ? round($submitted / $totalStudents * 100) : 0;
        ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">
            <?php echo e($hw->title ?: Str::limit($hw->description, 50)); ?>

          </td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($hw->subject?->name); ?></td>
          <td class="px-4 py-3 text-xs <?php echo e($hw->due_date?->isPast() ? 'text-red-500' : 'text-slate-500'); ?>"><?php echo e($hw->due_date?->format('d M Y')); ?></td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($hw->teacher?->full_name ?? '—'); ?></td>
          <td class="px-4 py-3 text-center">
            <span class="badge-green text-xs"><?php echo e($submitted); ?></span>
          </td>
          <td class="px-4 py-3 text-center">
            <span class="badge-red text-xs"><?php echo e(max(0,$notSub)); ?></span>
          </td>
          <td class="px-4 py-3 text-center">
            <span class="badge-blue text-xs"><?php echo e($evaluated); ?></span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <div class="flex-1 bg-slate-100 rounded-full h-2 min-w-16">
                <div class="h-2 rounded-full bg-green-500" style="width:<?php echo e($pct); ?>%"></div>
              </div>
              <span class="text-xs text-slate-500 w-8"><?php echo e($pct); ?>%</span>
            </div>
          </td>
          <td class="px-4 py-3">
            <a href="<?php echo e(route('academics.homework.submissions', $hw->id)); ?>" class="btn btn-secondary btn-xs">Manage</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No homework found for this class.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if(isset($homeworks) && method_exists($homeworks, 'hasPages') && $homeworks->hasPages()): ?>
    <div class="px-4 pb-3 text-sm"><?php echo e($homeworks->links()); ?></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\homework-completion-report.blade.php ENDPATH**/ ?>