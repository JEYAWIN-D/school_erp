<?php $__env->startSection('title', 'Books'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Book Catalogue</h1>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('library.books.bulk-import')); ?>" class="btn btn-secondary btn-sm">Bulk Import</a>
      <a href="<?php echo e(route('library.books.qr-bulk')); ?>" target="_blank" class="btn btn-secondary btn-sm">Print QR Labels</a>
      <a href="<?php echo e(route('library.acquisition-report')); ?>" class="btn btn-secondary btn-sm">Acquisition Report</a>
      <a href="<?php echo e(route('library.deaccession-register')); ?>" class="btn btn-secondary btn-sm">Deaccession Register</a>
      <a href="<?php echo e(route('library.books.create')); ?>" class="btn btn-primary btn-sm">Add Book</a>
    </div>
  </div>
  <form method="GET" class="card-flat py-4"><div class="flex gap-3">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Title, author, accession#..." class="input flex-1">
    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="available" value="1" <?php if(request('available')): echo 'checked'; endif; ?> class="w-4 h-4"> Available only</label>
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
  </div></form>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Acc. #</th><th class="th">Cover</th><th class="th">Title</th><th class="th">Author</th><th class="th">Publisher</th><th class="th">Copies</th><th class="th">Available</th><th class="th">Actions</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr <?php echo e($book->is_deaccessioned ? 'opacity-50' : ''); ?>">
            <td class="td font-mono text-xs text-blue-600"><?php echo e($book->accession_number); ?></td>
            <td class="td">
              <?php if($book->cover_image): ?>
                <img src="<?php echo e(asset('storage/' . $book->cover_image)); ?>" alt="Cover" class="w-8 h-10 object-cover rounded shadow-sm">
              <?php else: ?>
                <div class="w-8 h-10 bg-slate-100 rounded flex items-center justify-center text-slate-300 text-xs">—</div>
              <?php endif; ?>
            </td>
            <td class="td font-medium text-slate-800">
              <?php echo e($book->title); ?>

              <?php if($book->is_deaccessioned): ?>
                <span class="badge-red text-xs ml-1">Written Off</span>
              <?php endif; ?>
            </td>
            <td class="td"><?php echo e($book->author ?? '—'); ?></td>
            <td class="td"><?php echo e($book->publisher ?? '—'); ?></td>
            <td class="td"><?php echo e($book->total_copies); ?></td>
            <td class="td">
              <span class="<?php echo e($book->available_copies > 0 ? 'badge-green' : 'badge-red'); ?>"><?php echo e($book->available_copies); ?></span>
            </td>
            <td class="td">
              <div class="flex gap-1">
                <a href="<?php echo e(route('library.books.edit', $book->id)); ?>" class="btn-xs btn-secondary">Edit</a>
                <a href="<?php echo e(route('library.books.qr-label', $book->id)); ?>" target="_blank" title="Print QR Label"
                   class="btn-xs bg-slate-100 text-slate-600 hover:bg-slate-200">QR</a>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="td text-center py-10 text-slate-400">No books found. <a href="<?php echo e(route('library.books.create')); ?>" class="text-blue-600">Add one</a>.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($books->hasPages()): ?><div class="text-sm mt-3"><?php echo e($books->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\books.blade.php ENDPATH**/ ?>