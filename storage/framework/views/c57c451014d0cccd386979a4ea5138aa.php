<?php $__env->startSection('title','Room Complaint History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Complaint History — Room <?php echo e($room->room_number); ?></h1>
      <p class="page-subtitle"><?php echo e($room->hostel?->name); ?> &mdash; <?php echo e(ucfirst($room->room_type ?? 'standard')); ?> room</p>
    </div>
    <a href="<?php echo e(route('hostel.complaints')); ?>" class="btn btn-secondary btn-sm">← All Complaints</a>
  </div>

  <div class="grid grid-cols-3 gap-4">
    <?php
      $total    = $complaints->total();
      $open     = $complaints->getCollection()->where('status','open')->count();
      $resolved = $complaints->getCollection()->where('status','resolved')->count();
    ?>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-slate-700"><?php echo e($total); ?></p><p class="text-xs text-slate-400 mt-1">Total Complaints</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-red-600"><?php echo e($open); ?></p><p class="text-xs text-slate-400 mt-1">Open (this page)</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-green-600"><?php echo e($resolved); ?></p><p class="text-xs text-slate-400 mt-1">Resolved (this page)</p></div>
  </div>

  <div class="card overflow-hidden">
    <?php if($complaints->isEmpty()): ?>
      <p class="text-center py-10 text-slate-400">No complaints recorded for this room.</p>
    <?php else: ?>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Student</th>
          <th class="th">Category</th>
          <th class="th">Description</th>
          <th class="th">Priority</th>
          <th class="th">Status</th>
          <th class="th">Assigned To</th>
          <th class="th">Resolved On</th>
        </tr></thead>
        <tbody>
          <?php $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td text-xs"><?php echo e($c->created_at->format('d M Y')); ?></td>
            <td class="td font-medium"><?php echo e($c->student?->full_name ?? '—'); ?></td>
            <td class="td"><span class="badge-slate capitalize text-xs"><?php echo e(str_replace('_',' ',$c->category ?? $c->complaint_type)); ?></span></td>
            <td class="td text-xs text-slate-500 max-w-xs">
              <p class="truncate"><?php echo e($c->description); ?></p>
              <?php if($c->resolution_notes): ?><p class="text-green-600 italic truncate mt-0.5"><?php echo e($c->resolution_notes); ?></p><?php endif; ?>
            </td>
            <td class="td text-xs font-semibold <?php echo e(($c->priority==='high')?'text-red-600':(($c->priority==='medium')?'text-amber-600':'text-slate-400')); ?> capitalize">
              <?php echo e($c->priority ?? 'medium'); ?>

            </td>
            <td class="td"><span class="badge-<?php echo e($c->status==='resolved'?'green':($c->status==='in_progress'?'amber':'red')); ?> capitalize text-xs"><?php echo e(str_replace('_',' ',$c->status)); ?></span></td>
            <td class="td text-xs">
              <?php if($c->assignedTo): ?>
                <?php echo e($c->assignedTo->first_name); ?> <?php echo e($c->assignedTo->last_name); ?>

              <?php elseif($c->vendor_name): ?>
                <span class="text-amber-600"><?php echo e($c->vendor_name); ?></span>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td class="td text-xs text-slate-400"><?php echo e($c->resolved_at?->format('d M Y') ?? '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php if($complaints->hasPages()): ?><div class="p-4"><?php echo e($complaints->links()); ?></div><?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\complaints-room.blade.php ENDPATH**/ ?>