<?php $__env->startSection('title', 'Library'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Library Management</h1>
      <p class="page-subtitle"><?php echo e($stats['total_books']); ?> book titles · <?php echo e($stats['total_copies']); ?> total copies</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('library.books')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Book Catalogue
      </a>
      <a href="<?php echo e(route('library.books.create')); ?>" class="btn btn-secondary btn-sm">Add Book</a>
      <a href="<?php echo e(route('library.issue')); ?>" class="btn btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Issue Book
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="<?php echo e(route('library.books')); ?>" class="card text-center py-5 hover:shadow-card-md transition">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['total_copies']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Total Copies</p>
    </a>
    <a href="<?php echo e(route('library.books', ['available' => 1])); ?>" class="card text-center py-5 hover:shadow-card-md transition">
      <p class="text-3xl font-bold text-green-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['available']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Available</p>
    </a>
    <a href="<?php echo e(route('library.currently-issued')); ?>" class="card text-center py-5 hover:shadow-card-md transition">
      <p class="text-3xl font-bold text-blue-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['issued']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Issued</p>
    </a>
    <a href="<?php echo e(route('library.overdue-report')); ?>" class="card text-center py-5 hover:shadow-card-md transition">
      <p class="text-3xl font-bold <?php echo e($stats['overdue'] > 0 ? 'text-red-500' : 'text-slate-800'); ?>" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['overdue']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Overdue</p>
    </a>
  </div>

  
  <?php if($stats['overdue'] > 0): ?>
  <div class="rounded-xl border border-red-200 bg-red-50 p-4">
    <div class="flex items-start gap-3 mb-3">
      <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div class="flex-1">
        <p class="font-semibold text-red-800"><?php echo e($stats['overdue']); ?> overdue book<?php echo e($stats['overdue'] > 1 ? 's' : ''); ?>

          <?php if($totalFinesPending > 0): ?>
          — ₹<?php echo e(number_format($totalFinesPending, 2)); ?> in pending fines
          <?php endif; ?>
        </p>
        <?php if($overdueIssues->count()): ?>
        <div class="mt-2 space-y-1">
          <?php $__currentLoopData = $overdueIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center justify-between text-sm">
            <span class="text-red-700"><?php echo e($oi->book?->title ?? '—'); ?></span>
            <span class="text-red-500 text-xs font-medium"><?php echo e($oi->student?->full_name ?? $oi->borrowerName()); ?> · Due <?php echo e(\Carbon\Carbon::parse($oi->due_date)->format('d M')); ?></span>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if($stats['overdue'] > $overdueIssues->count()): ?>
          <a href="<?php echo e(route('library.overdue-report')); ?>" class="text-xs text-red-600 hover:underline">+<?php echo e($stats['overdue'] - $overdueIssues->count()); ?> more — view full report</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <a href="<?php echo e(route('library.overdue-report')); ?>" class="btn btn-sm bg-red-600 hover:bg-red-700 text-white flex-shrink-0">Full Report</a>
    </div>
  </div>
  <?php endif; ?>

  
  <form method="GET" action="<?php echo e(route('library.books')); ?>" class="card-flat py-4">
    <div class="flex gap-2">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" placeholder="Search books by title, author or accession number…" class="input pl-9 w-full">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      <a href="<?php echo e(route('library.books', ['available' => 1])); ?>" class="btn btn-secondary btn-sm">Available Only</a>
    </div>
  </form>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    <?php $__currentLoopData = [
      ['Issue Book',       'library.issue',          'from-green-500 to-emerald-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
      ['Fine Waiver',      'library.fine.waiver',    'from-blue-500 to-indigo-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>'],
      ['All Issued',       'library.currently-issued','from-indigo-500 to-purple-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['Overdue Report',   'library.overdue-report', 'from-red-500 to-rose-600',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
      ['Most Borrowed',    'library.most-borrowed',  'from-amber-500 to-orange-500',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
      ['Stock Audit',      'library.stock-audit',    'from-slate-500 to-gray-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
      ['Fine Defaulters',  'library.fine-defaulters','from-rose-500 to-red-600',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-4 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">Recent Issues</h3>
      <a href="<?php echo e(route('library.currently-issued')); ?>" class="text-sm text-blue-600 hover:underline">View All →</a>
    </div>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Borrower</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $recentIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="tr">
              <td class="td">
                <p class="font-medium text-slate-800"><?php echo e($issue->book?->title ?? '—'); ?></p>
                <?php if($issue->book?->author): ?>
                <p class="text-xs text-slate-400"><?php echo e($issue->book->author); ?></p>
                <?php endif; ?>
              </td>
              <td class="td"><?php echo e($issue->borrowerName()); ?></td>
              <td class="td text-sm"><?php echo e(\Carbon\Carbon::parse($issue->issue_date)->format('d M Y')); ?></td>
              <td class="td text-sm <?php echo e($issue->status === 'issued' && \Carbon\Carbon::parse($issue->due_date)->isPast() ? 'text-red-600 font-semibold' : 'text-slate-600'); ?>">
                <?php echo e(\Carbon\Carbon::parse($issue->due_date)->format('d M Y')); ?>

              </td>
              <td class="td">
                <?php if($issue->status === 'returned'): ?>
                  <span class="badge-green">Returned</span>
                <?php elseif($issue->status === 'issued' && \Carbon\Carbon::parse($issue->due_date)->isPast()): ?>
                  <span class="badge-red">Overdue</span>
                <?php else: ?>
                  <span class="badge-blue">Issued</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="td text-center py-8 text-slate-400">No recent issues.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\index.blade.php ENDPATH**/ ?>