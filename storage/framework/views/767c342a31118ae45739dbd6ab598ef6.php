<?php $__env->startSection('title','Stock Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Stock Register</h1>
    <a href="<?php echo e(route('library.stock.export')); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Accession No','Title','Author','Publisher','Category','Total','Available','Issued','Lost','Price','Purchase Date']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($b->accession_number); ?></td>
          <td class="px-4 py-3 font-medium text-slate-800 text-sm max-w-xs"><?php echo e($b->title); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($b->author ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($b->publisher ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($b->category ?? '—'); ?></td>
          <td class="px-4 py-3 text-center font-semibold"><?php echo e($b->total_copies); ?></td>
          <td class="px-4 py-3 text-center text-green-700 font-semibold"><?php echo e($b->available_copies); ?></td>
          <td class="px-4 py-3 text-center text-amber-600 font-semibold"><?php echo e($b->total_copies - $b->available_copies); ?></td>
          <td class="px-4 py-3 text-center text-red-500 text-xs"><?php echo e($b->lost_copies ?? 0); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($b->purchase_price ? '₹'.number_format($b->purchase_price,2) : '—'); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($b->purchase_date ? \Carbon\Carbon::parse($b->purchase_date)->format('d M Y') : '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="11" class="px-4 py-8 text-center text-slate-400">No books in catalogue.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($books->hasPages()): ?><div class="px-4 pb-3"><?php echo e($books->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\stock.blade.php ENDPATH**/ ?>