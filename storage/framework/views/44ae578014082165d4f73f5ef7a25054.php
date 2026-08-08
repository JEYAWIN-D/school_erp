<?php $__env->startSection('title', 'Deaccession Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Deaccession Register</h1>
    <a href="<?php echo e(route('library.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">From</label>
        <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">To</label>
        <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="input text-sm">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  <?php if($books->isEmpty()): ?>
    <div class="card text-center py-12 text-slate-400">
      No books deaccessioned in this period.
    </div>
  <?php else: ?>
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Publisher</th>
          <th class="th">Total Copies</th>
          <th class="th">Write-off Date</th>
          <th class="th">Reason</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td font-mono text-xs"><?php echo e($book->accession_number); ?></td>
          <td class="td font-medium"><?php echo e($book->title); ?></td>
          <td class="td text-slate-500"><?php echo e($book->author ?? '—'); ?></td>
          <td class="td text-slate-500"><?php echo e($book->publisher ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($book->total_copies); ?></td>
          <td class="td"><?php echo e($book->deaccession_date?->format('d M Y') ?? '—'); ?></td>
          <td class="td"><?php echo e($book->deaccession_reason ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="5" class="td text-right">Total Books Written Off:</td>
          <td class="td text-center"><?php echo e($books->sum('total_copies')); ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php endif; ?>

  <p class="text-xs text-slate-400 text-right">Period: <?php echo e($from->format('d M Y')); ?> – <?php echo e($to->format('d M Y')); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\deaccession-register.blade.php ENDPATH**/ ?>