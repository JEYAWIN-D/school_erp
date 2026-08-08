<?php $__env->startSection('title', 'Visitor Pass'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Visitor Pass</h1>
    <div class="flex gap-2">
      <button onclick="window.print()" class="btn-sm btn-secondary">Print Pass</button>
      <a href="<?php echo e(route('hostel.visitors')); ?>" class="btn-sm btn-secondary">← Back</a>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <div id="pass-card" class="card border-2 border-indigo-300 print:border-black">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
      <div>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Hostel Visitor Pass</p>
        <h2 class="text-lg font-bold text-slate-800"><?php echo e($school?->school_name ?? config('app.name')); ?></h2>
      </div>
      <?php if($visitor->isPassValid()): ?>
        <span class="badge-green text-sm px-3 py-1">VALID</span>
      <?php else: ?>
        <span class="badge-red text-sm px-3 py-1">EXPIRED</span>
      <?php endif; ?>
    </div>

    <div class="grid grid-cols-3 gap-4">
      <div class="col-span-2 space-y-2 text-sm">
        <div class="grid grid-cols-2 gap-x-4 gap-y-1">
          <div><span class="text-slate-500 text-xs">Visitor Name</span><p class="font-semibold"><?php echo e($visitor->visitor_name); ?></p></div>
          <div><span class="text-slate-500 text-xs">Relation</span><p class="font-semibold"><?php echo e($visitor->relation ?? '—'); ?></p></div>
          <div><span class="text-slate-500 text-xs">Phone</span><p class="font-semibold"><?php echo e($visitor->visitor_phone ?? $visitor->visitor_mobile ?? '—'); ?></p></div>
          <div><span class="text-slate-500 text-xs">ID / <?php echo e($visitor->id_type ?? 'Document'); ?></span><p class="font-semibold"><?php echo e($visitor->id_number ?? '—'); ?></p></div>
          <div><span class="text-slate-500 text-xs">Visiting</span><p class="font-semibold"><?php echo e($visitor->student?->full_name); ?></p></div>
          <div><span class="text-slate-500 text-xs">Hostel</span><p class="font-semibold"><?php echo e($visitor->hostel?->name ?? '—'); ?></p></div>
          <div><span class="text-slate-500 text-xs">Entry Time</span><p class="font-semibold"><?php echo e($visitor->entry_time?->format('d M Y, h:i A')); ?></p></div>
          <div><span class="text-slate-500 text-xs">Valid Until</span>
            <p class="font-semibold <?php echo e($visitor->isPassValid() ? 'text-green-700' : 'text-red-600'); ?>">
              <?php echo e($visitor->pass_valid_until?->format('h:i A')); ?>

            </p>
          </div>
          <?php if($visitor->purpose): ?>
          <div class="col-span-2"><span class="text-slate-500 text-xs">Purpose</span><p class="font-semibold"><?php echo e($visitor->purpose); ?></p></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex flex-col items-center gap-2">
        <?php if($visitor->visitor_photo): ?>
          <img src="<?php echo e(Storage::url($visitor->visitor_photo)); ?>" alt="Visitor Photo"
            class="w-20 h-24 object-cover rounded border border-slate-200">
        <?php endif; ?>
        
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?php echo e(urlencode('HOSTEL-PASS:'.$visitor->pass_token)); ?>"
          alt="QR Pass" class="w-20 h-20">
        <p class="text-xs text-slate-400 text-center font-mono break-all"><?php echo e(substr($visitor->pass_token, 0, 12)); ?>…</p>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-400">
      <span>Pass #<?php echo e($visitor->id); ?></span>
      <span><?php echo e($visitor->pass_valid_until?->format('d M Y')); ?></span>
      <span>Generated <?php echo e(now()->format('h:i A')); ?></span>
    </div>
  </div>

  
  <?php if(!$visitor->exit_time): ?>
  <form method="POST" action="<?php echo e(route('hostel.visitors.checkout', $visitor->id)); ?>">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn-primary w-full" onclick="return confirm('Mark visitor as checked out?')">Check Out Visitor</button>
  </form>
  <?php else: ?>
  <div class="alert-success">Visitor checked out at <?php echo e($visitor->exit_time?->format('h:i A')); ?></div>
  <?php endif; ?>
</div>

<style>
@media print {
  nav, .btn-sm, form, .alert-success { display: none !important; }
  #pass-card { border: 2px solid #000 !important; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\visitor-pass.blade.php ENDPATH**/ ?>