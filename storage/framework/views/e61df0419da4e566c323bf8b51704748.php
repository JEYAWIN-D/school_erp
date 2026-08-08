<?php $__env->startSection('title', 'Acquisition Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Acquisition Report</h1>
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
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </form>
  </div>

  
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700"><?php echo e($books->count()); ?></p>
      <p class="text-xs text-slate-500 mt-1">Titles Acquired</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800"><?php echo e($totalCopies); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Copies</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹<?php echo e(number_format($totalValue, 2)); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Value</p>
    </div>
  </div>

  
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Publisher</th>
          <th class="th">ISBN</th>
          <th class="th">Copies</th>
          <th class="th">Price (₹)</th>
          <th class="th">Total (₹)</th>
          <th class="th">Purchase Date</th>
          <th class="th">Location</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($loop->iteration); ?></td>
          <td class="td font-mono text-xs"><?php echo e($book->accession_number); ?></td>
          <td class="td font-medium"><?php echo e($book->title); ?></td>
          <td class="td text-slate-500"><?php echo e($book->author ?? '—'); ?></td>
          <td class="td text-slate-500"><?php echo e($book->publisher ?? '—'); ?></td>
          <td class="td font-mono text-xs"><?php echo e($book->isbn ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($book->total_copies); ?></td>
          <td class="td text-right"><?php echo e($book->purchase_price ? number_format($book->purchase_price, 2) : '—'); ?></td>
          <td class="td text-right font-semibold"><?php echo e($book->purchase_price ? number_format($book->purchase_price * $book->total_copies, 2) : '—'); ?></td>
          <td class="td"><?php echo e($book->purchase_date?->format('d M Y') ?? '—'); ?></td>
          <td class="td text-slate-500"><?php echo e($book->location ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="11" class="td text-center text-slate-400 py-8">No acquisitions in this period.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if($books->count()): ?>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="6" class="td text-right">Total:</td>
          <td class="td text-center"><?php echo e($totalCopies); ?></td>
          <td class="td"></td>
          <td class="td text-right">₹<?php echo e(number_format($totalValue, 2)); ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>

  <p class="text-xs text-slate-400 text-right"><?php echo e($from->format('d M Y')); ?> – <?php echo e($to->format('d M Y')); ?> | Generated: <?php echo e(now()->format('d M Y, h:i A')); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\acquisition-report.blade.php ENDPATH**/ ?>