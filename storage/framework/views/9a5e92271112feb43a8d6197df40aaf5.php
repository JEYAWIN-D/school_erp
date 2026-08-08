<?php $__env->startSection('title', 'Arrears & Bonus'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Arrears & Bonus Addition</h1>
    <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Entry</h2>
      <form method="POST" action="<?php echo e(route('hr.arrears-bonus.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label text-xs">Pay Month <span class="text-red-500">*</span></label>
          <input type="month" name="month" class="input text-sm" required value="<?php echo e(old('month', $month)); ?>">
        </div>
        <div>
          <label class="label text-xs">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select text-sm" required>
            <option value="">Select Employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->name); ?> — <?php echo e($emp->department?->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label text-xs">Type <span class="text-red-500">*</span></label>
          <select name="type" class="select text-sm" required>
            <option value="arrears" <?php if(old('type') === 'arrears'): echo 'selected'; endif; ?>>Arrears</option>
            <option value="bonus" <?php if(old('type') === 'bonus'): echo 'selected'; endif; ?>>Bonus</option>
            <option value="other_addition" <?php if(old('type') === 'other_addition'): echo 'selected'; endif; ?>>Other Addition</option>
          </select>
        </div>
        <div>
          <label class="label text-xs">Amount (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" step="0.01" min="0.01" class="input text-sm" required value="<?php echo e(old('amount')); ?>">
        </div>
        <div>
          <label class="label text-xs">Description</label>
          <input type="text" name="description" class="input text-sm" value="<?php echo e(old('description')); ?>" placeholder="e.g. Festival bonus, Salary correction">
        </div>
        <button type="submit" class="btn btn-primary w-full">Save</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 card overflow-x-auto">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h2 class="font-semibold text-slate-700">Entries for <?php echo e(\Carbon\Carbon::parse($month.'-01')->format('F Y')); ?></h2>
          <p class="text-xs text-slate-500 mt-0.5">Total: ₹<?php echo e(number_format($totalMonth, 2)); ?></p>
        </div>
        <form method="GET" class="flex gap-2">
          <input type="month" name="month" value="<?php echo e($month); ?>" class="input text-sm">
          <button type="submit" class="btn btn-secondary btn-sm">Go</button>
        </form>
      </div>
      <?php if($entries->isEmpty()): ?>
        <p class="text-slate-400 text-sm text-center py-8">No entries for this month.</p>
      <?php else: ?>
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Type</th>
            <th class="th">Amount</th>
            <th class="th">Description</th>
            <th class="th">Added On</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td">
              <?php echo e($entry->employee->name); ?><br>
              <span class="text-xs text-slate-400"><?php echo e($entry->employee->department?->name); ?></span>
            </td>
            <td class="td">
              <span class="badge-<?php echo e($entry->type === 'arrears' ? 'amber' : ($entry->type === 'bonus' ? 'green' : 'blue')); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $entry->type))); ?>

              </span>
            </td>
            <td class="td font-semibold text-right text-green-700">₹<?php echo e(number_format($entry->amount, 2)); ?></td>
            <td class="td text-slate-500 text-xs"><?php echo e($entry->description ?? '—'); ?></td>
            <td class="td text-xs text-slate-400"><?php echo e($entry->created_at->format('d M Y')); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
          <tr class="bg-slate-50 font-semibold">
            <td colspan="2" class="td text-right">Total:</td>
            <td class="td text-right text-green-700">₹<?php echo e(number_format($totalMonth, 2)); ?></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\arrears-bonus.blade.php ENDPATH**/ ?>