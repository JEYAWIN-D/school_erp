<?php $__env->startSection('title', ($module ?? 'Module') . ' — Coming Soon'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
  <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-blue-glow mb-6">
    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
    </svg>
  </div>
  <h1 class="text-3xl font-extrabold text-slate-800 mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">
    <?php echo e($module ?? 'Module'); ?>

  </h1>
  <p class="text-slate-500 text-lg mb-2">Under Development</p>
  <p class="text-slate-400 text-sm max-w-md">This module is coming soon. We're working hard to bring you a great experience. Check back soon!</p>
  <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary mt-8">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Back to Dashboard
  </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\coming-soon.blade.php ENDPATH**/ ?>