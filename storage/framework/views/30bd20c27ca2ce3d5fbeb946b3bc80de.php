<?php $__env->startSection('title', 'TC Issue Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">TC Issue Register</h1>
      <p class="page-subtitle">Log of all Transfer Certificates issued</p>
    </div>
    <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Students
    </a>
  </div>

  <form method="GET" class="card-flat py-4">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Search</label>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Name, TC#, Adm#…" class="input w-56">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if(request('search')): ?>
        <a href="<?php echo e(route('students.tc-register')); ?>" class="btn btn-ghost btn-sm">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Student</th>
          <th class="th">Adm. #</th>
          <th class="th">Class</th>
          <th class="th">TC Number</th>
          <th class="th">TC Date</th>
          <th class="th">Previous School</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td text-slate-400 text-xs"><?php echo e($records->firstItem() + $i); ?></td>
            <td class="td">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-700 text-xs font-bold"><?php echo e(strtoupper(substr($s->first_name,0,1).substr($s->last_name,0,1))); ?></span>
                </div>
                <div>
                  <p class="font-medium text-slate-800 text-sm"><?php echo e($s->full_name); ?></p>
                  <p class="text-xs text-slate-400"><?php echo e($s->gender ? ucfirst($s->gender) : ''); ?></p>
                </div>
              </div>
            </td>
            <td class="td font-mono text-xs text-blue-600"><?php echo e($s->admission_number); ?></td>
            <td class="td"><?php echo e($s->currentEnrollment?->class?->name ?? '—'); ?></td>
            <td class="td font-mono text-sm font-semibold text-slate-700"><?php echo e($s->tc_number); ?></td>
            <td class="td text-sm"><?php echo e($s->tc_date?->format('d M Y') ?? '—'); ?></td>
            <td class="td text-sm text-slate-500"><?php echo e($s->previous_school_name ?? '—'); ?></td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-1">
                <a href="<?php echo e(route('students.show', $s->id)); ?>" class="btn-icon" title="View Profile">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="<?php echo e(route('students.tc.form', $s->id)); ?>" target="_blank" class="btn-icon text-green-500 hover:text-green-700 hover:bg-green-50" title="Download TC">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="8" class="td text-center py-12 text-slate-400">
              <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              No TC records found.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($records->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($records->firstItem()); ?>–<?php echo e($records->lastItem()); ?> of <?php echo e($records->total()); ?> TC records</span>
      <?php echo e($records->links()); ?>

    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\tc-register.blade.php ENDPATH**/ ?>