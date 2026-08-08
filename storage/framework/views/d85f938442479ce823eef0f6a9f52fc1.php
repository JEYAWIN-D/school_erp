<?php $__env->startSection('title', 'Overtime Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Overtime Entry</h1>
    <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Overtime Entry</h2>
      <form method="POST" action="<?php echo e(route('hr.overtime.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label text-xs">Month</label>
          <input type="month" name="month_filter" value="<?php echo e($month); ?>" class="input text-sm"
                 onchange="this.form.action='<?php echo e(route('hr.overtime')); ?>?month='+this.value; this.form.submit();">
        </div>
        <div>
          <label class="label text-xs">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select text-sm" required>
            <option value="">Select Employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?> — <?php echo e($emp->department?->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label text-xs">Date <span class="text-red-500">*</span></label>
          <input type="date" name="entry_date" class="input text-sm" required value="<?php echo e(old('entry_date')); ?>">
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Hours <span class="text-red-500">*</span></label>
            <input type="number" name="hours" step="0.5" min="0.5" max="24" class="input text-sm" required value="<?php echo e(old('hours')); ?>">
          </div>
          <div>
            <label class="label text-xs">Rate/Hour (₹) <span class="text-red-500">*</span></label>
            <input type="number" name="rate_per_hour" step="0.01" min="0" class="input text-sm" required value="<?php echo e(old('rate_per_hour')); ?>">
          </div>
        </div>
        <div>
          <label class="label text-xs">Remarks</label>
          <input type="text" name="remarks" class="input text-sm" value="<?php echo e(old('remarks')); ?>" placeholder="Optional note">
        </div>
        <input type="hidden" name="redirect_month" value="<?php echo e($month); ?>">
        <button type="submit" class="btn btn-primary w-full">Save Entry</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 card overflow-x-auto">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-700">Entries for <?php echo e(\Carbon\Carbon::parse($month.'-01')->format('F Y')); ?></h2>
        <form method="GET" class="flex gap-2">
          <input type="month" name="month" value="<?php echo e($month); ?>" class="input text-sm">
          <button type="submit" class="btn btn-secondary btn-sm">Go</button>
        </form>
      </div>
      <?php if($entries->isEmpty()): ?>
        <p class="text-slate-400 text-sm text-center py-8">No overtime entries for this month.</p>
      <?php else: ?>
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Date</th>
            <th class="th">Hours</th>
            <th class="th">Rate</th>
            <th class="th">Amount</th>
            <th class="th">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td">
              <?php echo e($entry->employee->name); ?><br>
              <span class="text-xs text-slate-400"><?php echo e($entry->employee->department?->name); ?></span>
            </td>
            <td class="td"><?php echo e($entry->entry_date->format('d M Y')); ?></td>
            <td class="td text-center"><?php echo e($entry->hours); ?></td>
            <td class="td text-right">₹<?php echo e(number_format($entry->rate_per_hour, 2)); ?></td>
            <td class="td text-right font-semibold text-indigo-700">₹<?php echo e(number_format($entry->amount, 2)); ?></td>
            <td class="td text-slate-500 text-xs"><?php echo e($entry->remarks ?? '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
          <tr class="bg-slate-50 font-semibold">
            <td colspan="4" class="td text-right">Total:</td>
            <td class="td text-right text-indigo-700">₹<?php echo e(number_format($entries->sum('amount'), 2)); ?></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\overtime.blade.php ENDPATH**/ ?>