<?php $__env->startSection('title','Archive — ' . $year->name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('academics.year-archive', ['year_id' => $year->id])); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="page-title"><?php echo e($year->name); ?> — Student Archive</h1>
      <p class="text-sm text-slate-400">Read-only historical data</p>
    </div>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label text-xs">Filter by Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Class</th>
            <th class="th">Section</th>
            <th class="th">Roll No</th>
            <th class="th">Status</th>
            <th class="th">Promoted</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td text-slate-400"><?php echo e($enrollments->firstItem() + $i); ?></td>
            <td class="td font-medium"><?php echo e($e->student?->full_name); ?></td>
            <td class="td text-slate-500"><?php echo e($e->student?->admission_number); ?></td>
            <td class="td"><?php echo e($e->class?->name); ?></td>
            <td class="td"><?php echo e($e->section?->name ?? '—'); ?></td>
            <td class="td"><?php echo e($e->roll_number ?? '—'); ?></td>
            <td class="td">
              <span class="badge-<?php echo e(match($e->status ?? 'active'){ 'active'=>'green','transferred'=>'amber','left'=>'red',default=>'slate' }); ?> text-xs capitalize">
                <?php echo e($e->status ?? 'active'); ?>

              </span>
            </td>
            <td class="td text-center">
              <?php if($e->promoted): ?>
                <span class="badge-green text-xs">Yes</span>
              <?php elseif($e->promoted === false): ?>
                <span class="badge-red text-xs">No</span>
              <?php else: ?>
                <span class="text-slate-300">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($enrollments->hasPages()): ?>
    <div class="px-4 pb-3 text-sm"><?php echo e($enrollments->links()); ?></div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\year-archive-students.blade.php ENDPATH**/ ?>