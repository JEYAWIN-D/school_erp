<?php $__env->startSection('title', 'Staff Notice Board'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data>
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Staff Notice Board</h1>
    <a href="<?php echo e(route('communication.index')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Post New Notice</h3>
      <form method="POST" action="<?php echo e(route('communication.staff-notices.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Title</label>
          <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="input w-full" required>
          <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label class="label">Message</label>
          <textarea name="body" rows="5" class="input w-full" required><?php echo e(old('body')); ?></textarea>
        </div>
        <div>
          <label class="label">Priority</label>
          <select name="priority" class="select w-full">
            <option value="normal">Normal</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div>
          <label class="label">Target Department (optional)</label>
          <input type="text" name="target_department" value="<?php echo e(old('target_department')); ?>"
                 placeholder="Leave blank for all staff" class="input w-full">
        </div>
        <div>
          <label class="label">Expires At (optional)</label>
          <input type="date" name="expires_at" value="<?php echo e(old('expires_at')); ?>" class="input w-full">
        </div>
        <button type="submit" class="btn-primary w-full">Post Notice</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 space-y-3">
      <?php $__empty_1 = true; $__currentLoopData = $notices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card" x-data="{ read: <?php echo e(in_array($n->id, $readIds) ? 'true' : 'false'); ?> }">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <?php if($n->priority === 'urgent'): ?>
                <span class="badge-red text-xs">Urgent</span>
              <?php endif; ?>
              <span class="font-semibold text-slate-800"><?php echo e($n->title); ?></span>
              <span x-show="!read" class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
            </div>
            <p class="text-sm text-slate-600 whitespace-pre-line"><?php echo e($n->body); ?></p>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
              <span><?php echo e($n->author?->name ?? '—'); ?></span>
              <span>&bull;</span>
              <span><?php echo e(\Carbon\Carbon::parse($n->created_at)->format('d M Y H:i')); ?></span>
              <?php if($n->target_department): ?> <span>&bull; Dept: <?php echo e($n->target_department); ?></span> <?php endif; ?>
              <?php if($n->expires_at): ?> <span>&bull; Expires <?php echo e(\Carbon\Carbon::parse($n->expires_at)->format('d M Y')); ?></span> <?php endif; ?>
              <a href="<?php echo e(route('communication.staff-notices.receipts', $n->id)); ?>" class="text-blue-500 hover:underline">Read receipts</a>
            </div>
          </div>
          <div class="flex flex-col gap-2 flex-shrink-0">
            <?php if (! (in_array($n->id, $readIds))): ?>
            <button x-on:click="fetch('<?php echo e(route('communication.staff-notices.read', $n->id)); ?>', {method:'POST', headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Content-Type':'application/json'}}); read = true"
                    class="btn-xs btn-secondary whitespace-nowrap">Mark Read</button>
            <?php endif; ?>
            <?php if(auth()->user()->hasRole(['Super Admin', 'School Admin'])): ?>
            <form method="POST" action="<?php echo e(route('communication.staff-notices.delete', $n->id)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" onclick="return confirm('Delete this notice?')" class="btn-xs text-red-600 hover:bg-red-50 border border-red-200 rounded-lg px-2 py-1 w-full">Delete</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-10">
        <p class="text-slate-400">No staff notices at this time</p>
      </div>
      <?php endif; ?>
      <div><?php echo e($notices->links()); ?></div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\staff-notices.blade.php ENDPATH**/ ?>