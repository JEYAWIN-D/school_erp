<?php $__env->startSection('title', 'Room-wise Student List'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Room-wise Student List</h1>
      <p class="page-subtitle">View students allocated to each room</p>
    </div>
    <a href="<?php echo e(route('hostel.occupancy')); ?>" class="btn btn-secondary">Occupancy Report</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">All Hostels</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($hostel->id); ?>" <?php echo e(request('hostel_id') == $hostel->id ? 'selected' : ''); ?>><?php echo e($hostel->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Status</label>
        <select name="status" class="select">
          <option value="">All</option>
          <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>>Available</option>
          <option value="full" <?php echo e(request('status') == 'full' ? 'selected' : ''); ?>>Full</option>
          <option value="maintenance" <?php echo e(request('status') == 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="card">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
      <div>
        <h3 class="font-semibold text-slate-800">Room <?php echo e($room->room_number); ?></h3>
        <p class="text-xs text-slate-500"><?php echo e($room->hostel?->name); ?> &bull; Floor: <?php echo e($room->floor ?? 'G'); ?> &bull; Type: <?php echo e(ucfirst($room->room_type)); ?></p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-slate-600"><?php echo e($room->occupied); ?>/<?php echo e($room->capacity); ?> occupied</span>
        <span class="badge-<?php echo e($room->status === 'full' ? 'red' : ($room->status === 'maintenance' ? 'amber' : 'green')); ?>"><?php echo e(ucfirst($room->status)); ?></span>
      </div>
    </div>
    <?php $activeAllotments = $room->allotments->where('status', 'active'); ?>
    <?php if($activeAllotments->isNotEmpty()): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
      <?php $__currentLoopData = $activeAllotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0">
          <span class="text-white text-xs font-bold"><?php echo e(strtoupper(substr($allotment->student?->first_name ?? 'S', 0, 1))); ?></span>
        </div>
        <div class="min-w-0 flex-1">
          <a href="<?php echo e(route('students.show', $allotment->student_id)); ?>" class="text-sm font-medium text-slate-800 hover:text-indigo-600 truncate block">
            <?php echo e($allotment->student?->first_name); ?> <?php echo e($allotment->student?->last_name); ?>

          </a>
          <p class="text-xs text-slate-400">Since <?php echo e(\Carbon\Carbon::parse($allotment->allotment_date)->format('d M Y')); ?></p>
        </div>
        <a href="<?php echo e(route('hostel.id-card', $allotment->id)); ?>" target="_blank" title="Print Hostel ID Card"
           class="flex-shrink-0 text-slate-400 hover:text-indigo-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <p class="text-sm text-slate-400 italic">No students currently in this room.</p>
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="card text-center py-12 text-slate-400">No rooms found for the selected filters.</div>
  <?php endif; ?>

  <?php if($rooms->hasPages()): ?><div><?php echo e($rooms->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\room-students.blade.php ENDPATH**/ ?>