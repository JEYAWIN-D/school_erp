<?php $__env->startSection('title','Bulk Admission Import'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Bulk Admission Import</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Upload Student Excel</h3>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
        <p class="font-medium mb-2">Instructions:</p>
        <ol class="list-decimal ml-4 space-y-1">
          <li>Download the Excel template below</li>
          <li>Fill in all required fields (marked with *)</li>
          <li>Upload the completed file</li>
          <li>Review preview before importing</li>
        </ol>
      </div>
      <a href="<?php echo e(route('admissions.import-template')); ?>" class="btn btn-secondary btn-sm">⬇ Download Template</a>
      <form method="POST" action="<?php echo e(route('admissions.import')); ?>" enctype="multipart/form-data" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div><label class="label">Excel File (.xlsx, .csv) <span class="text-red-500">*</span></label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input" required>
        </div>
        <div><label class="label">Target Class</label>
          <select name="class_id" class="select">
            <option value="">All (from file)</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Upload & Preview</button>
      </form>
    </div>
    <?php if(isset($preview) && count($preview)): ?>
    <?php
      $errorCount = collect($preview)->filter(fn($r) => !empty($r['errors']))->count();
      $okCount    = count($preview) - $errorCount;
    ?>
    <div class="card">
      <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Preview (<?php echo e(count($preview)); ?> records)</h3>
        <div class="flex gap-2">
          <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold"><?php echo e($okCount); ?> OK</span>
          <?php if($errorCount > 0): ?>
            <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold"><?php echo e($errorCount); ?> errors</span>
          <?php endif; ?>
        </div>
      </div>
      <?php if($errorCount > 0): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3 text-xs text-amber-800">
          <?php echo e($errorCount); ?> row(s) have errors and will be skipped. Fix the source file and re-upload, or proceed to import only the valid <?php echo e($okCount); ?> row(s).
        </div>
      <?php endif; ?>
      <div class="overflow-x-auto max-h-80 overflow-y-auto">
        <table class="min-w-full text-xs">
          <thead class="bg-slate-50 sticky top-0"><tr>
            <?php $__currentLoopData = ['Name','Class','Father','Mobile','Status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="text-left px-3 py-2 text-slate-500 font-medium"><?php echo e($h); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php $__currentLoopData = $preview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="<?php echo e($row['errors'] ? 'bg-red-50' : 'hover:bg-slate-50'); ?>">
              <td class="px-3 py-1.5"><?php echo e($row['name']); ?></td>
              <td class="px-3 py-1.5"><?php echo e($row['class']); ?></td>
              <td class="px-3 py-1.5"><?php echo e($row['father']); ?></td>
              <td class="px-3 py-1.5"><?php echo e($row['mobile']); ?></td>
              <td class="px-3 py-1.5">
                <?php if($row['errors']): ?><span class="text-red-600"><?php echo e(implode(', ',$row['errors'])); ?></span>
                <?php else: ?><span class="text-green-600">✓ OK</span><?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <form method="POST" action="<?php echo e(route('admissions.import-confirm')); ?>" class="mt-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="import_key" value="<?php echo e($importKey ?? ''); ?>">
        <button type="submit" class="btn btn-primary">Confirm Import</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
  <?php if(session('import_result')): ?>
  <div class="card bg-green-50 border border-green-200">
    <p class="text-green-700 font-medium">Import completed: <?php echo e(session('import_result.imported')); ?> imported, <?php echo e(session('import_result.failed')); ?> failed.</p>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\bulk-import.blade.php ENDPATH**/ ?>