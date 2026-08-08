<?php $__env->startSection('title', 'Document Expiry Tracking'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Document Expiry Tracking</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Alert window (days ahead)</label>
        <input type="number" name="days" value="<?php echo e($daysAhead); ?>" min="1" max="365" class="input w-28">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Refresh</button>
    </div>
  </form>

  
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
      Expiring Within <?php echo e($daysAhead); ?> Days
      <span class="badge-amber"><?php echo e($expiring->count()); ?></span>
    </h2>
    <?php if($expiring->count()): ?>
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Document Type</th>
          <th class="th">File</th>
          <th class="th">Expiry Date</th>
          <th class="th">Days Remaining</th>
        </tr></thead>
        <tbody>
          <?php $__currentLoopData = $expiring; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false); ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($doc->student?->full_name); ?>

              <div class="text-xs text-slate-400"><?php echo e($doc->student?->admission_number); ?></div>
            </td>
            <td class="td text-slate-600 capitalize"><?php echo e(str_replace('_',' ',$doc->document_type)); ?></td>
            <td class="td text-xs">
              <a href="<?php echo e(Storage::url($doc->file_path)); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo e($doc->original_name); ?></a>
            </td>
            <td class="td"><?php echo e($doc->expiry_date?->format('d M Y')); ?></td>
            <td class="td">
              <span class="<?php echo e($daysLeft <= 7 ? 'badge-red' : 'badge-amber'); ?>"><?php echo e($daysLeft); ?> days</span>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <p class="text-slate-400 text-sm">No documents expiring in the next <?php echo e($daysAhead); ?> days.</p>
    <?php endif; ?>
  </div>

  
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
      Already Expired
      <span class="badge-red"><?php echo e($expired->count()); ?></span>
    </h2>
    <?php if($expired->count()): ?>
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Document Type</th>
          <th class="th">Expiry Date</th>
          <th class="th">Expired</th>
        </tr></thead>
        <tbody>
          <?php $__currentLoopData = $expired; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $daysAgo = abs(now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false)); ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($doc->student?->full_name); ?>

              <div class="text-xs text-slate-400"><?php echo e($doc->student?->admission_number); ?></div>
            </td>
            <td class="td text-slate-600 capitalize"><?php echo e(str_replace('_',' ',$doc->document_type)); ?></td>
            <td class="td text-red-500"><?php echo e($doc->expiry_date?->format('d M Y')); ?></td>
            <td class="td"><span class="badge-red text-xs"><?php echo e($daysAgo); ?> days ago</span></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <p class="text-slate-400 text-sm">No expired documents on record.</p>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\document-expiry.blade.php ENDPATH**/ ?>