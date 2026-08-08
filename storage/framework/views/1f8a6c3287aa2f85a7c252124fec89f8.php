<?php $__env->startSection('title', 'Annual Stock Audit'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Annual Stock Audit Report</h1>
    <div class="flex gap-2">
      <form method="GET" class="flex gap-2 items-center">
        <label class="label text-xs">Year</label>
        <select name="year" class="select text-sm" onchange="this.form.submit()">
          <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
          <option value="<?php echo e($y); ?>" <?php if($y == $year): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
          <?php endfor; ?>
        </select>
      </form>
      <a href="<?php echo e(route('library.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  
  <div class="grid grid-cols-4 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700"><?php echo e($totalBooks); ?></p>
      <p class="text-xs text-slate-500 mt-1">Unique Titles</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800"><?php echo e($totalCopies); ?></p>
      <p class="text-xs text-slate-500 mt-1">Total Copies</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600"><?php echo e($totalAvailable); ?></p>
      <p class="text-xs text-slate-500 mt-1">Available</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-500"><?php echo e($totalDeaccessioned); ?></p>
      <p class="text-xs text-slate-500 mt-1">Written Off</p>
    </div>
  </div>

  
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Total</th>
          <th class="th">Available</th>
          <th class="th">Issued</th>
          <th class="th">Lost</th>
          <th class="th">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $book = $row['book']; ?>
        <tr class="tr <?php echo e($row['deaccessioned'] ? 'opacity-50' : ''); ?>">
          <td class="td font-mono text-xs"><?php echo e($book->accession_number); ?></td>
          <td class="td font-medium"><?php echo e($book->title); ?></td>
          <td class="td text-slate-500"><?php echo e($book->author ?? '—'); ?></td>
          <td class="td text-center"><?php echo e($row['total_copies']); ?></td>
          <td class="td text-center text-green-600 font-semibold"><?php echo e($row['available']); ?></td>
          <td class="td text-center text-indigo-600"><?php echo e($row['issued']); ?></td>
          <td class="td text-center <?php echo e($row['lost'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($row['lost'] ?: '—'); ?></td>
          <td class="td">
            <?php if($row['deaccessioned']): ?>
              <span class="badge-red">Written Off</span>
            <?php elseif($row['available'] == 0): ?>
              <span class="badge-amber">All Issued</span>
            <?php else: ?>
              <span class="badge-green">Available</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No books found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <p class="text-xs text-slate-400 text-right">Generated: <?php echo e(now()->format('d M Y, h:i A')); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\stock-audit.blade.php ENDPATH**/ ?>