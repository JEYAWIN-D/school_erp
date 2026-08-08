<?php $__env->startSection('title', 'Hostel Occupancy Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Occupancy Report</h1>
      <p class="page-subtitle">Current occupancy across all hostels</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('hostel.occupancy.pdf')); ?>" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="<?php echo e(route('hostel.occupancy.excel')); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="<?php echo e(route('hostel.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5"><p class="text-3xl font-bold text-slate-700"><?php echo e($totalRooms); ?></p><p class="text-sm text-slate-500 mt-1">Total Rooms</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-indigo-600"><?php echo e($totalCapacity); ?></p><p class="text-sm text-slate-500 mt-1">Total Capacity</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-green-600"><?php echo e($totalOccupied); ?></p><p class="text-sm text-slate-500 mt-1">Occupied</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-blue-600"><?php echo e($availableRooms); ?></p><p class="text-sm text-slate-500 mt-1">Available Rooms</p></div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-semibold text-slate-800"><?php echo e($row['hostel']->name); ?></h3>
          <p class="text-xs text-slate-500 capitalize"><?php echo e($row['hostel']->type); ?> hostel</p>
        </div>
        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($row['pct'] >= 90 ? 'bg-red-100 text-red-700' : ($row['pct'] >= 70 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')); ?>">
          <?php echo e($row['pct']); ?>% Full
        </span>
      </div>
      <div class="grid grid-cols-3 gap-3 text-center text-sm mb-4">
        <div><p class="text-xl font-bold text-slate-700"><?php echo e($row['totalCap']); ?></p><p class="text-slate-400 text-xs">Capacity</p></div>
        <div><p class="text-xl font-bold text-green-600"><?php echo e($row['occupied']); ?></p><p class="text-slate-400 text-xs">Occupied</p></div>
        <div><p class="text-xl font-bold text-blue-600"><?php echo e($row['available']); ?></p><p class="text-slate-400 text-xs">Available</p></div>
      </div>
      <div class="bg-slate-200 rounded-full h-3">
        <div class="h-3 rounded-full transition-all <?php echo e($row['pct'] >= 90 ? 'bg-red-500' : ($row['pct'] >= 70 ? 'bg-amber-500' : 'bg-green-500')); ?>" style="width:<?php echo e($row['pct']); ?>%"></div>
      </div>
      <?php if($row['hostel']->warden_name): ?>
      <p class="text-xs text-slate-500 mt-3">Warden: <span class="font-medium"><?php echo e($row['hostel']->warden_name); ?></span></p>
      <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <div class="flex gap-3 justify-center">
    <a href="<?php echo e(route('hostel.room-students')); ?>" class="btn btn-secondary">Room-wise Student List</a>
    <a href="<?php echo e(route('hostel.fee-outstanding')); ?>" class="btn btn-secondary">Fee Outstanding</a>
    <a href="<?php echo e(route('hostel.wardens')); ?>" class="btn btn-secondary">Manage Wardens</a>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\occupancy.blade.php ENDPATH**/ ?>