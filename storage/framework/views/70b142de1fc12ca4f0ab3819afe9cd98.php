<?php $__env->startSection('title', 'Communication'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Communication Hub</h1>
      <p class="page-subtitle">Emails, notices &amp; portal accounts</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('communication.staff-notices')); ?>" class="btn btn-secondary btn-sm">Staff Notices</a>
      <a href="<?php echo e(route('communication.portal-accounts')); ?>" class="btn btn-secondary btn-sm">Portal Accounts</a>
      <a href="<?php echo e(route('communication.bulk-email')); ?>" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Bulk Email
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="<?php echo e(route('communication.logs')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <p class="stat-number"><?php echo e(number_format($totalEmailSent)); ?></p>
      <p class="text-sm text-slate-500">Emails Sent</p>
    </a>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
      </div>
      <p class="stat-number"><?php echo e(number_format($totalSmsSent)); ?></p>
      <p class="text-sm text-slate-500">SMS Sent</p>
    </div>
    <a href="<?php echo e(route('communication.staff-notices')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalStaffNotices); ?></p>
      <p class="text-sm text-slate-500">
        Staff Notices
        <?php if($unreadCount > 0): ?>
          <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700"><?php echo e($unreadCount); ?> unread</span>
        <?php endif; ?>
      </p>
    </a>
    <a href="<?php echo e(route('communication.portal-accounts')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number"><?php echo e(number_format($portalAccounts)); ?></p>
      <p class="text-sm text-slate-500">Portal Accounts</p>
    </a>
  </div>

  
  <?php if($todaySent > 0): ?>
  <div class="rounded-xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-semibold text-green-800"><?php echo e($todaySent); ?> message(s) sent today.</p>
    <a href="<?php echo e(route('communication.logs')); ?>" class="text-sm text-green-700 underline ml-auto">View logs →</a>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Bulk Email',       'communication.bulk-email',     'from-blue-500 to-indigo-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
      ['Staff Notices',    'communication.staff-notices',  'from-amber-500 to-orange-500',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>'],
      ['Portal Accounts',  'communication.portal-accounts','from-violet-500 to-purple-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'],
      ['Communication Logs','communication.logs',          'from-slate-500 to-gray-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-3.5 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-700">Recent Email Logs</h3>
        <a href="<?php echo e(route('communication.logs')); ?>" class="text-xs text-blue-600 hover:underline">View all →</a>
      </div>
      <?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="py-2.5 border-b border-slate-100 last:border-0">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-700 truncate"><?php echo e($log->subject); ?></p>
            <p class="text-xs text-slate-400 mt-0.5"><?php echo e($log->sent_count); ?> sent &bull; <?php echo e(\Carbon\Carbon::parse($log->sent_at ?? $log->created_at)->diffForHumans()); ?></p>
          </div>
          <span class="badge-<?php echo e($log->status === 'sent' ? 'green' : ($log->status === 'failed' ? 'red' : 'blue')); ?> flex-shrink-0 text-xs">
            <?php echo e(ucfirst($log->status)); ?>

          </span>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="text-center py-8">
        <svg class="w-8 h-8 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <p class="text-sm text-slate-400">No emails sent yet</p>
        <a href="<?php echo e(route('communication.bulk-email')); ?>" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Send your first bulk email →</a>
      </div>
      <?php endif; ?>
    </div>

    
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-700">
          Staff Notices
          <?php if($unreadCount > 0): ?>
            <span class="ml-1.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700"><?php echo e($unreadCount); ?></span>
          <?php endif; ?>
        </h3>
        <a href="<?php echo e(route('communication.staff-notices')); ?>" class="text-xs text-blue-600 hover:underline">View all →</a>
      </div>
      <?php $__empty_1 = true; $__currentLoopData = $staffNotices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="py-2.5 border-b border-slate-100 last:border-0">
        <div class="flex items-start gap-2">
          <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 <?php echo e($n->priority === 'urgent' ? 'bg-red-500' : 'bg-blue-400'); ?>"></span>
          <div class="flex-1 min-w-0">
            <?php if($n->priority === 'urgent'): ?>
              <span class="badge-red text-xs">Urgent</span>
            <?php endif; ?>
            <p class="text-sm font-medium text-slate-700 truncate mt-0.5"><?php echo e($n->title); ?></p>
            <p class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($n->created_at)->diffForHumans()); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="text-center py-8">
        <svg class="w-8 h-8 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        <p class="text-sm text-slate-400">No staff notices</p>
      </div>
      <?php endif; ?>
    </div>

  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\index.blade.php ENDPATH**/ ?>