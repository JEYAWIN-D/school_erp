<?php $__env->startSection('title','Fee Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="page-title">Fee Management</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?></p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('fees.structure')); ?>" class="btn btn-secondary btn-sm">Fee Structure</a>
      <a href="<?php echo e(route('fees.defaulters')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Defaulters
      </a>
      <a href="<?php echo e(route('fees.collect')); ?>" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Collect Fee
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-emerald-50">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="badge-green">Today</span>
      </div>
      <div class="mt-3">
        <p class="stat-number text-emerald-700">₹<?php echo e(number_format($todayCollection)); ?></p>
        <p class="text-xs text-slate-500 mt-1 font-medium">Today's Collection</p>
      </div>
    </div>

    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-blue-50">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <span class="badge-blue">Month</span>
      </div>
      <div class="mt-3">
        <p class="stat-number">₹<?php echo e(number_format($monthCollection)); ?></p>
        <p class="text-xs text-slate-500 mt-1 font-medium">This Month</p>
      </div>
    </div>

    <div class="card">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-violet-50">
          <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <span class="badge-indigo">Year</span>
      </div>
      <div class="mt-3">
        <p class="stat-number">₹<?php echo e(number_format($yearCollection)); ?></p>
        <p class="text-xs text-slate-500 mt-1 font-medium">Year Collected</p>
      </div>
    </div>

    <a href="<?php echo e(route('fees.defaulters')); ?>" class="card hover:shadow-md transition-shadow group">
      <div class="flex items-start justify-between">
        <div class="stat-icon bg-red-50 group-hover:bg-red-100 transition-colors">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="badge-rose">View →</span>
      </div>
      <div class="mt-3">
        <p class="stat-number text-red-700"><?php echo e(number_format($defaultersCount)); ?></p>
        <p class="text-xs text-slate-500 mt-1 font-medium">Defaulters</p>
      </div>
    </a>

  </div>

  
  <?php if($yearDemand > 0): ?>
  <div class="card">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
      <div>
        <h3 class="section-title mb-0">Year Collection Progress</h3>
        <p class="text-xs text-slate-400 mt-0.5">Total billed vs collected &mdash; <?php echo e($currentYear?->name); ?></p>
      </div>
      <div class="text-right">
        <p class="text-lg font-bold text-slate-900" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
          <?php echo e($yearDemand > 0 ? round($yearCollection / $yearDemand * 100, 1) : 0); ?>%
        </p>
        <p class="text-xs text-slate-400 mt-0.5">₹<?php echo e(number_format($yearCollection)); ?> of ₹<?php echo e(number_format($yearDemand)); ?></p>
      </div>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
      <div class="h-2.5 rounded-full bg-emerald-500 transition-all duration-500"
           style="width: <?php echo e(min(100, $yearDemand > 0 ? round($yearCollection / $yearDemand * 100, 1) : 0)); ?>%"></div>
    </div>
    <div class="flex justify-between mt-2.5 text-xs font-semibold">
      <span class="text-emerald-600">Collected: ₹<?php echo e(number_format($yearCollection)); ?></span>
      <?php if($outstanding > 0): ?>
        <span class="text-red-600">Outstanding: ₹<?php echo e(number_format($outstanding)); ?></span>
      <?php else: ?>
        <span class="text-emerald-600">Fully collected ✓</span>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Collect Fee',        'fees.collect',             'bg-emerald-50 text-emerald-700 border-emerald-200',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
      ['Fee Structure',      'fees.structure',           'bg-blue-50 text-blue-700 border-blue-200',           '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>'],
      ['Defaulters',         'fees.defaulters',          'bg-red-50 text-red-700 border-red-200',              '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
      ['Monthly Collection', 'fees.collection.monthly',  'bg-amber-50 text-amber-700 border-amber-200',        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
      ['Class-wise Report',  'fees.collection.class-wise','bg-teal-50 text-teal-700 border-teal-200',          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
      ['Fee Head Report',    'fees.collection.fee-head', 'bg-violet-50 text-violet-700 border-violet-200',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'],
      ['Concessions',        'fees.concessions',         'bg-pink-50 text-pink-700 border-pink-200',           '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'],
      ['Cheque Pending',     'fees.cheque-pending',      'bg-slate-100 text-slate-700 border-slate-200',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>"
       class="card-flat flex items-center gap-3 py-3 px-3.5 border hover:shadow-sm transition-all hover:border-slate-300 group <?php echo e($color); ?>">
      <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white/60 group-hover:bg-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm leading-tight"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="section-title mb-0">Recent Payments</h3>
        <p class="text-xs text-slate-400 mt-0.5">Latest fee receipts collected</p>
      </div>
      <a href="<?php echo e(route('fees.collect')); ?>" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Collect Fee
      </a>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Receipt #</th>
            <th class="th">Student</th>
            <th class="th">Fee Head</th>
            <th class="th">Amount</th>
            <th class="th">Mode</th>
            <th class="th">Date</th>
            <th class="th text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-mono text-xs font-semibold text-indigo-600"><?php echo e($p->receipt_number); ?></td>
            <td class="td font-semibold text-slate-800"><?php echo e($p->student?->full_name); ?></td>
            <td class="td text-slate-500"><?php echo e($p->feeHead?->name ?? '—'); ?></td>
            <td class="td font-bold text-emerald-700">₹<?php echo e(number_format($p->total_paid, 2)); ?></td>
            <td class="td">
              <span class="badge-slate capitalize"><?php echo e($p->payment_mode ?? 'cash'); ?></span>
            </td>
            <td class="td text-slate-400 text-xs"><?php echo e(\Carbon\Carbon::parse($p->payment_date)->format('d M Y')); ?></td>
            <td class="td text-right">
              <a href="<?php echo e(route('fees.receipt', $p->id)); ?>" target="_blank" class="btn-icon text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50" title="Print receipt">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              </a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="7" class="td">
              <div class="py-12 text-center">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-2.5">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 mb-1">No payments yet</p>
                <p class="text-xs text-slate-400">Collected fees will appear here.</p>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\index.blade.php ENDPATH**/ ?>