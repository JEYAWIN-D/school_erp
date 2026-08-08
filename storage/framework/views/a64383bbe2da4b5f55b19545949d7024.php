<?php $__env->startSection('title', 'Advance Payment'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('fees.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="page-title">Advance Payment</h1>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>

  
  <form method="GET" class="card py-4">
    <div class="flex gap-3">
      <input type="text" name="student_id" value="<?php echo e(request('student_id')); ?>" placeholder="Student ID or search..." class="input flex-1">
      <button type="submit" class="btn btn-secondary">Search</button>
    </div>
  </form>

  <?php if($student): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card space-y-4">
      <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
          <span class="text-white text-sm font-bold"><?php echo e(strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1))); ?></span>
        </div>
        <div>
          <p class="font-semibold text-slate-800"><?php echo e($student->full_name); ?></p>
          <p class="text-xs text-slate-500"><?php echo e($student->admission_number); ?> &bull; <?php echo e($student->currentEnrollment?->class?->name); ?></p>
        </div>
      </div>

      <?php if($totalBalance > 0): ?>
      <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
        <p class="text-sm font-medium text-emerald-800">Available Advance Balance</p>
        <p class="text-2xl font-bold text-emerald-700 mt-0.5">₹<?php echo e(number_format($totalBalance, 2)); ?></p>
      </div>
      <?php endif; ?>

      <h3 class="font-semibold text-slate-700 text-sm">Record New Advance Payment</h3>
      <form method="POST" action="<?php echo e(route('fees.advance.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
        <div>
          <label class="label">Amount (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" class="input" min="1" step="0.01" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Payment Date <span class="text-red-500">*</span></label>
            <input type="date" name="payment_date" class="input" value="<?php echo e(today()->toDateString()); ?>" required>
          </div>
          <div>
            <label class="label">Mode <span class="text-red-500">*</span></label>
            <select name="payment_mode" class="select" required>
              <option value="cash">Cash</option>
              <option value="cheque">Cheque</option>
              <option value="online">Online</option>
              <option value="upi">UPI</option>
            </select>
          </div>
        </div>
        <div>
          <label class="label">Transaction ID</label>
          <input type="text" name="transaction_id" class="input" placeholder="UTR / ref no.">
        </div>
        <div>
          <label class="label">Notes</label>
          <input type="text" name="notes" class="input" placeholder="Optional notes">
        </div>
        <button type="submit" class="btn btn-primary w-full">Record Advance Payment</button>
      </form>
    </div>

    
    <?php if($totalBalance > 0): ?>
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Apply Advance to Fee</h3>
      <form method="POST" action="<?php echo e(route('fees.advance.apply')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
        <div>
          <label class="label">Fee Head</label>
          <select name="fee_head_id" class="select" required>
            <option value="">Select fee head</option>
            <?php $__currentLoopData = \App\Models\FeeHead::where('is_active', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($fh->id); ?>"><?php echo e($fh->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Amount to Apply (₹)</label>
          <input type="number" name="amount" class="input" min="1" max="<?php echo e($totalBalance); ?>" step="0.01" required placeholder="Max ₹<?php echo e(number_format($totalBalance, 2)); ?>">
        </div>
        <button type="submit" class="btn btn-primary w-full">Apply from Advance Balance</button>
      </form>

      
      <div class="mt-4">
        <h4 class="text-sm font-medium text-slate-600 mb-2">Advance Records</h4>
        <div class="space-y-2">
          <?php $__currentLoopData = $advances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
            <div class="flex justify-between items-start">
              <div>
                <p class="font-medium text-slate-700">₹<?php echo e(number_format($adv->amount, 2)); ?> <span class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($adv->payment_date)->format('d M Y')); ?></span></p>
                <p class="text-xs text-slate-400 mt-0.5"><?php echo e($adv->receipt_number); ?> &bull; <?php echo e(ucfirst($adv->payment_mode)); ?></p>
              </div>
              <div class="text-right">
                <p class="text-xs text-slate-500">Remaining</p>
                <p class="font-semibold <?php echo e($adv->remaining_amount > 0 ? 'text-emerald-600' : 'text-slate-400'); ?>">₹<?php echo e(number_format($adv->remaining_amount, 2)); ?></p>
              </div>
            </div>
            <?php if($adv->applied_amount > 0): ?>
            <p class="text-xs text-amber-600 mt-1">Applied: ₹<?php echo e(number_format($adv->applied_amount, 2)); ?></p>
            <?php endif; ?>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="card text-center py-10 text-slate-400">
      <p class="text-sm">No advance balance on record for this student.</p>
      <p class="text-xs mt-1">Record a new advance payment to build up a balance.</p>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\advance-payments.blade.php ENDPATH**/ ?>