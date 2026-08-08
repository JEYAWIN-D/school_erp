<?php $__env->startSection('title', 'Transport Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Transport Management</h1>
      <p class="page-subtitle">Fleet status &amp; route operations</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('transport.allotment')); ?>"   class="btn btn-secondary btn-sm">Student Allotment</a>
      <a href="<?php echo e(route('transport.bus-attendance')); ?>" class="btn btn-secondary btn-sm">Bus Attendance</a>
      <a href="<?php echo e(route('transport.vehicles.create')); ?>" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Vehicle
      </a>
    </div>
  </div>

  
  <?php if($docAlerts->count() > 0): ?>
  <div class="rounded-xl border border-red-200 bg-red-50 p-4">
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div class="flex-1">
        <p class="text-sm font-semibold text-red-800">Document Expiry Warning — <?php echo e($docAlerts->count()); ?> vehicle(s) need attention</p>
        <div class="flex flex-wrap gap-2 mt-1.5">
          <?php $__currentLoopData = $docAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">
              <?php echo e($v->vehicle_number); ?>

              <?php if($v->insurance_expiry && \Carbon\Carbon::parse($v->insurance_expiry)->lte(today()->addDays(30))): ?> · Ins <?php echo e(\Carbon\Carbon::parse($v->insurance_expiry)->format('d M')); ?> <?php endif; ?>
              <?php if($v->fitness_expiry && \Carbon\Carbon::parse($v->fitness_expiry)->lte(today()->addDays(30))): ?> · Fit <?php echo e(\Carbon\Carbon::parse($v->fitness_expiry)->format('d M')); ?> <?php endif; ?>
            </span>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('transport.documents')); ?>" class="text-xs text-red-700 underline font-semibold">View All →</a>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if($maintenanceAlerts->count() > 0): ?>
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <div class="flex-1">
        <p class="text-sm font-semibold text-amber-800">Maintenance Due — <?php echo e($maintenanceAlerts->count()); ?> vehicle(s) in next 7 days</p>
        <a href="<?php echo e(route('transport.maintenance')); ?>" class="text-xs text-amber-700 underline font-semibold mt-1 inline-block">View Schedule →</a>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <a href="<?php echo e(route('transport.routes')); ?>" class="card hover:shadow-card-md transition hover:-translate-y-0.5">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['routes']); ?></p>
      <p class="text-sm text-slate-500">Active Routes</p>
    </a>
    <a href="<?php echo e(route('transport.vehicles')); ?>" class="card hover:shadow-card-md transition hover:-translate-y-0.5">
      <div class="stat-icon bg-gradient-to-br from-slate-600 to-slate-800 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['vehicles']); ?></p>
      <p class="text-sm text-slate-500">Vehicles</p>
    </a>
    <a href="<?php echo e(route('transport.allotment')); ?>" class="card hover:shadow-card-md transition hover:-translate-y-0.5">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['students']); ?></p>
      <p class="text-sm text-slate-500">Students Allotted</p>
    </a>
    <a href="<?php echo e(route('transport.drivers')); ?>" class="card hover:shadow-card-md transition hover:-translate-y-0.5">
      <div class="stat-icon bg-gradient-to-br from-teal-500 to-cyan-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['drivers']); ?></p>
      <p class="text-sm text-slate-500">Drivers</p>
    </a>
    <a href="<?php echo e(route('transport.bus-attendance')); ?>" class="card hover:shadow-card-md transition hover:-translate-y-0.5">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      </div>
      <p class="stat-number"><?php echo e($stats['bus_present']); ?></p>
      <p class="text-sm text-slate-500">On Bus Today</p>
    </a>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Routes',           'transport.routes',           'from-blue-500 to-indigo-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>'],
      ['Vehicles',         'transport.vehicles',         'from-slate-600 to-slate-800',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>'],
      ['Drivers',          'transport.drivers',          'from-teal-500 to-cyan-600',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
      ['Bus Attendance',   'transport.bus-attendance',   'from-violet-500 to-purple-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
      ['Stops',            'transport.stops',            'from-amber-500 to-orange-500',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>'],
      ['Student Allotment','transport.allotment',        'from-green-500 to-emerald-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
      ['Maintenance',      'transport.maintenance',      'from-rose-500 to-red-600',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
      ['Fuel Log',         'transport.fuel',             'from-yellow-500 to-amber-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>'],
      ['Documents',        'transport.documents',        'from-pink-500 to-rose-500',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ['Route Roster',     'transport.roster',           'from-cyan-500 to-sky-600',       '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>'],
      ['Tracking',         'transport.tracking',         'from-lime-500 to-green-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
      ['Incidents',        'transport.incidents',        'from-orange-500 to-amber-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-3.5 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\index.blade.php ENDPATH**/ ?>