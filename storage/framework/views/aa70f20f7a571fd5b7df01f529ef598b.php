<?php $__env->startSection('title','Bank Transfer Export'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Bank Transfer / NEFT Export</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Generate Transfer File</h3>
      <form method="GET" action="<?php echo e(route('hr.payroll.bank-transfer')); ?>" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">Month <span class="text-red-500">*</span></label>
            <select name="month" class="select" required>
              <?php for($m=1;$m<=12;$m++): ?>
              <option value="<?php echo e($m); ?>" <?php if(($payrollMonth??now()->month)==$m): echo 'selected'; endif; ?>><?php echo e(\Carbon\Carbon::create(null,$m)->format('F')); ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div><label class="label">Year <span class="text-red-500">*</span></label>
            <select name="year" class="select" required>
              <?php for($y=now()->year-1;$y<=now()->year+1;$y++): ?>
              <option value="<?php echo e($y); ?>" <?php if(($payrollYear??now()->year)==$y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div><label class="label">Bank</label>
          <select name="bank" class="select">
            <option value="">All Banks</option>
            <?php $__currentLoopData = $banks??[]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b); ?>"><?php echo e($b); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Format</label>
          <select name="format" class="select">
            <option value="neft">NEFT/Generic CSV</option>
            <option value="sbi">SBI (Pipe-delimited)</option>
            <option value="hdfc">HDFC NetBanking</option>
            <option value="icici">ICICI Corporate</option>
            <option value="axis">Axis Bank</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Download Transfer File</button>
      </form>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Summary Preview</h3>
      <?php if(isset($payrollSummary)): ?>
      <div class="space-y-2">
        <div class="flex justify-between text-sm"><span class="text-slate-500">Total Employees</span><span class="font-semibold"><?php echo e($payrollSummary->count()); ?></span></div>
        <div class="flex justify-between text-sm"><span class="text-slate-500">Total Net Pay</span><span class="font-bold text-slate-800">₹<?php echo e(number_format($payrollSummary->sum('net_salary'),2)); ?></span></div>
        <div class="border-t border-slate-100 pt-2 space-y-1">
          <?php $__currentLoopData = $payrollSummary->groupBy(fn($p)=>$p->employee?->bank_name); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank=>$rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex justify-between text-xs text-slate-500">
            <span><?php echo e($bank ?: 'Unknown Bank'); ?></span>
            <span><?php echo e($rows->count()); ?> records | ₹<?php echo e(number_format($rows->sum('net_salary'),2)); ?></span>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php else: ?>
      <p class="text-slate-400 text-sm text-center py-8">Select month and year to preview.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\bank-transfer.blade.php ENDPATH**/ ?>