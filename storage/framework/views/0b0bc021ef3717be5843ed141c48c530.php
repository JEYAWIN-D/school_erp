<?php $__env->startSection('title','Book Reservations'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Book Reservation Queue</h1>
    <button x-data @click="$dispatch('open-modal','add-reservation')" class="btn btn-primary btn-sm">+ Reserve</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3">
    <select name="status" class="select w-36">
      <option value="">All</option>
      <option value="pending" <?php if(request('status')==='pending'): echo 'selected'; endif; ?>>Pending</option>
      <option value="ready" <?php if(request('status')==='ready'): echo 'selected'; endif; ?>>Ready</option>
      <option value="cancelled" <?php if(request('status')==='cancelled'): echo 'selected'; endif; ?>>Cancelled</option>
    </select>
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input flex-1" placeholder="Search book or student...">
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['#','Book','Member','Reserved On','Expiry','Status','Position','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($reservations->firstItem()+$i); ?></td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800 text-sm"><?php echo e($r->book?->title); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($r->book?->isbn); ?></p>
          </td>
          <td class="px-4 py-3 text-slate-700 text-sm"><?php echo e($r->member?->name ?? ($r->student?->full_name ?? '—')); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($r->reserved_at?->format('d M Y')); ?></td>
          <td class="px-4 py-3 text-xs <?php echo e($r->expiry_date?->isPast() ? 'text-red-500' : 'text-slate-400'); ?>"><?php echo e($r->expiry_date?->format('d M Y')); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($r->status==='ready'?'green':($r->status==='cancelled'?'red':'amber')); ?> capitalize text-xs"><?php echo e($r->status); ?></span></td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700"><?php echo e($r->queue_position ?? '—'); ?></td>
          <td class="px-4 py-3 flex gap-2">
            <?php if($r->status === 'pending'): ?>
            <form method="POST" action="<?php echo e(route('library.reservations.ready',$r->id)); ?>" class="inline"><?php echo csrf_field(); ?>
              <button type="submit" class="text-green-600 hover:underline text-xs">Mark Ready</button>
            </form>
            <form method="POST" action="<?php echo e(route('library.reservations.cancel',$r->id)); ?>" class="inline"><?php echo csrf_field(); ?>
              <button type="submit" class="text-red-400 hover:underline text-xs">Cancel</button>
            </form>
            <?php endif; ?>
            <?php if($r->status === 'ready'): ?>
            <form method="POST" action="<?php echo e(route('library.issue.store')); ?>" class="inline"><?php echo csrf_field(); ?>
              <input type="hidden" name="reservation_id" value="<?php echo e($r->id); ?>">
              <input type="hidden" name="book_id" value="<?php echo e($r->book_id); ?>">
              <input type="hidden" name="student_id" value="<?php echo e($r->student_id); ?>">
              <input type="hidden" name="member_type" value="student">
              <input type="hidden" name="due_date" value="<?php echo e(now()->addDays(14)->toDateString()); ?>">
              <button type="submit" class="text-indigo-600 hover:underline text-xs">Issue Now</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No reservations.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($reservations->hasPages()): ?><div class="px-4 pb-3"><?php echo e($reservations->links()); ?></div><?php endif; ?>
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-reservation')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Reserve a Book</h3>
    <form method="POST" action="<?php echo e(route('library.reservations.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Book <span class="text-red-500">*</span></label>
        <select name="book_id" class="select" required>
          <option value="">Search & select book</option>
          <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b->id); ?>"><?php echo e($b->title); ?> (<?php echo e($b->available_copies); ?> available)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Member / Student <span class="text-red-500">*</span></label>
        <select name="member_id" class="select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Expiry Date</label>
        <input type="date" name="expiry_date" class="input" value="<?php echo e(now()->addDays(7)->toDateString()); ?>">
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Reserve</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\reservations.blade.php ENDPATH**/ ?>