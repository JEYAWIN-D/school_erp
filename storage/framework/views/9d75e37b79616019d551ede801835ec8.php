<?php $__env->startSection('title', 'Most Borrowed Books'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Most Borrowed Books</h1>
      <p class="page-subtitle">Top books by borrow frequency</p>
    </div>
    <a href="<?php echo e(route('library.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Period</label>
        <select name="period" class="select">
          <?php $__currentLoopData = [7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 3 months', 365 => 'Last 1 year']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $days => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($days); ?>" <?php echo e($period == $days ? 'selected' : ''); ?>><?php echo e($label); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Book Title</th>
            <th class="th">Author</th>
            <th class="th">Category</th>
            <th class="th text-center">Times Borrowed</th>
            <th class="th text-center">Available</th>
            <th class="th text-center">Total Copies</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php $maxBorrow = $books->first()->borrow_count ?: 1; ?>
          <tr class="tr">
            <td class="td text-slate-400">
              <?php if($loop->index < 3): ?>
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold <?php echo e(['bg-amber-500','bg-slate-400','bg-orange-600'][$loop->index]); ?>"><?php echo e($loop->iteration); ?></span>
              <?php else: ?>
                <?php echo e($loop->iteration); ?>

              <?php endif; ?>
            </td>
            <td class="td">
              <div class="font-medium text-slate-800"><?php echo e($book->title); ?></div>
              <div class="text-xs text-slate-400 font-mono"><?php echo e($book->accession_number); ?></div>
            </td>
            <td class="td text-slate-500"><?php echo e($book->author ?? '—'); ?></td>
            <td class="td text-slate-500"><?php echo e($book->category ?? '—'); ?></td>
            <td class="td text-center">
              <div class="flex items-center justify-center gap-2">
                <div class="w-16 bg-slate-200 rounded-full h-2">
                  <div class="h-2 rounded-full bg-indigo-500" style="width:<?php echo e($maxBorrow > 0 ? round($book->borrow_count/$maxBorrow*100) : 0); ?>%"></div>
                </div>
                <span class="font-semibold text-indigo-600"><?php echo e($book->borrow_count); ?></span>
              </div>
            </td>
            <td class="td text-center <?php echo e($book->available_copies == 0 ? 'text-red-500 font-semibold' : 'text-green-600'); ?>"><?php echo e($book->available_copies); ?></td>
            <td class="td text-center text-slate-500"><?php echo e($book->total_copies); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No borrowing data for this period.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($books->hasPages()): ?><div class="mt-4"><?php echo e($books->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\most-borrowed.blade.php ENDPATH**/ ?>