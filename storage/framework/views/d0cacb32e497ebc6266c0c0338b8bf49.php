<?php $__env->startSection('title', 'Gate & Visitor Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Gate &amp; Visitor Management</h1>
      <p class="page-subtitle"><?php echo e($todayTotal); ?> visitor(s) today &bull; <?php echo e($insideCount); ?> currently inside</p>
    </div>
    <a href="<?php echo e(route('gate.create')); ?>" class="btn btn-primary self-start sm:self-auto">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Log Visitor
    </a>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
  <?php if($errors->any()): ?> <div class="alert-danger"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div> <?php endif; ?>

  
  <?php if($pendingOutpasses > 0): ?>
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-semibold text-amber-800">
      <?php echo e($pendingOutpasses); ?> student outpass(es) overdue — students have not returned.
      <a href="<?php echo e(route('gate.outpass')); ?>" class="underline ml-1">View →</a>
    </p>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="<?php echo e(route('gate.index', ['inside_only'=>1])); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-indigo-500 to-blue-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </div>
      <p class="stat-number text-indigo-600"><?php echo e($insideCount); ?></p>
      <p class="text-sm text-slate-500">Currently Inside</p>
    </a>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($todayTotal); ?></p>
      <p class="text-sm text-slate-500">Today's Visitors</p>
    </div>
    <a href="<?php echo e(route('gate.outpass')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p class="stat-number <?php echo e($pendingOutpasses > 0 ? 'text-amber-600' : ''); ?>"><?php echo e($pendingOutpasses); ?></p>
      <p class="text-sm text-slate-500">Overdue Outpasses</p>
    </a>
    <a href="<?php echo e(route('gate.blacklist')); ?>" class="card hover:shadow-card-md transition">
      <div class="stat-icon bg-gradient-to-br from-red-500 to-rose-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
      </div>
      <p class="stat-number"><?php echo e($blacklistCount); ?></p>
      <p class="text-sm text-slate-500">Blacklisted</p>
    </a>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Log Visitor',  'gate.create',    'from-green-500 to-emerald-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'],
      ['Outpasses',    'gate.outpass',   'from-amber-500 to-orange-500',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['Blacklist',    'gate.blacklist', 'from-red-500 to-rose-600',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
      ['Gate Report',  'gate.report',    'from-slate-500 to-gray-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-3.5 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <h2 class="font-semibold text-slate-700">Visitor Log</h2>

  
  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Date</label>
      <input type="date" name="date" value="<?php echo e(request('date', today()->toDateString())); ?>" class="input">
    </div>
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input" placeholder="Name or phone…">
    </div>
    <label class="flex items-center gap-2 text-sm self-end pb-2">
      <input type="checkbox" name="inside_only" value="1" <?php if(request('inside_only')): echo 'checked'; endif; ?> class="rounded"> Currently inside only
    </label>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Visitor</th>
          <th class="th">Purpose</th>
          <th class="th">Whom to Meet</th>
          <th class="th">Vehicle</th>
          <th class="th">In Time</th>
          <th class="th">Out Time</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $visitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td">
            <div class="flex items-center gap-2">
              <?php if($v->visitor_photo): ?>
              <img src="<?php echo e(Storage::url($v->visitor_photo)); ?>" class="w-8 h-8 rounded-full object-cover">
              <?php else: ?>
              <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500"><?php echo e(strtoupper(substr($v->visitor_name,0,1))); ?></div>
              <?php endif; ?>
              <div>
                <p class="font-medium"><?php echo e($v->visitor_name); ?></p>
                <p class="text-xs text-slate-400"><?php echo e($v->visitor_phone); ?></p>
              </div>
            </div>
          </td>
          <td class="td text-xs"><?php echo e($v->purpose); ?></td>
          <td class="td text-xs"><?php echo e($v->whom_to_meet ?? '—'); ?><br><span class="text-slate-400"><?php echo e($v->department ?? ''); ?></span></td>
          <td class="td text-xs"><?php echo e($v->vehicle_number ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($v->in_time->format('h:i A')); ?></td>
          <td class="td text-xs"><?php echo e($v->out_time ? $v->out_time->format('h:i A') : '—'); ?></td>
          <td class="td">
            <?php if($v->isInside()): ?> <span class="badge-green">Inside</span>
            <?php else: ?> <span class="badge-slate">Out</span> <?php endif; ?>
          </td>
          <td class="td flex gap-1">
            <a href="<?php echo e(route('gate.pass', $v->id)); ?>" class="btn-xs btn-secondary">Pass</a>
            <?php if($v->isInside()): ?>
            <form method="POST" action="<?php echo e(route('gate.checkout', $v->id)); ?>"
                  onsubmit="return confirm('Checkout <?php echo e(addslashes($v->visitor_name)); ?>?')">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <button type="submit" class="btn-xs btn-secondary text-emerald-600">Checkout</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-slate-400 text-center" colspan="8">No visitors today.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($visitors->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\index.blade.php ENDPATH**/ ?>