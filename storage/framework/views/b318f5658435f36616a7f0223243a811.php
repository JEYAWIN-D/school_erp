<?php $__env->startSection('title', 'Online Exams'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Online Exams</h1>
      <p class="page-subtitle">Schedule, manage, and evaluate online examinations</p>
    </div>
    <a href="<?php echo e(route('online-exams.create')); ?>" class="btn btn-primary btn-sm">+ Create Exam</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert-error"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select w-32">
        <option value="">All</option>
        <?php $__currentLoopData = ['draft' => 'Draft', 'published' => 'Published', 'ongoing' => 'Ongoing', 'completed' => 'Completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Exam Title</th>
          <th class="th">Class</th>
          <th class="th">Subject</th>
          <th class="th">Scheduled</th>
          <th class="th">Duration</th>
          <th class="th">Marks</th>
          <th class="th">Status</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800"><?php echo e($exam->title); ?></td>
            <td class="td"><?php echo e($exam->class?->name); ?></td>
            <td class="td text-sm text-slate-500"><?php echo e($exam->subject?->name ?? '—'); ?></td>
            <td class="td text-xs text-slate-500">
              <?php echo e($exam->start_time->format('d M Y, g:i A')); ?><br>
              <span class="text-slate-400">to <?php echo e($exam->end_time->format('g:i A')); ?></span>
            </td>
            <td class="td text-sm"><?php echo e($exam->duration_minutes); ?> min</td>
            <td class="td text-sm"><?php echo e($exam->total_marks); ?><br><span class="text-xs text-slate-400">Pass: <?php echo e($exam->pass_marks); ?></span></td>
            <td class="td">
              <?php $sc = ['draft'=>'badge-slate','published'=>'badge-blue','ongoing'=>'badge-green','completed'=>'badge-indigo'] ?>
              <span class="<?php echo e($sc[$exam->status] ?? 'badge-slate'); ?> capitalize"><?php echo e($exam->status); ?></span>
            </td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-1">
                <a href="<?php echo e(route('online-exams.questions', $exam->id)); ?>" class="btn btn-secondary btn-xs">Questions</a>
                <a href="<?php echo e(route('online-exams.attempts', $exam->id)); ?>" class="btn btn-secondary btn-xs">Attempts</a>
                <?php if($exam->status === 'draft'): ?>
                  <form method="POST" action="<?php echo e(route('online-exams.publish', $exam->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary btn-xs">Publish</button>
                  </form>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('online-exams.destroy', $exam->id)); ?>"
                      onsubmit="return confirm('Delete this exam and all attempt data?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="td text-center py-10 text-slate-400">No online exams yet. <a href="<?php echo e(route('online-exams.create')); ?>" class="text-blue-600 hover:underline">Create the first one</a>.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($exams->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($exams->firstItem()); ?>–<?php echo e($exams->lastItem()); ?> of <?php echo e($exams->total()); ?></span>
      <?php echo e($exams->links()); ?>

    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\index.blade.php ENDPATH**/ ?>