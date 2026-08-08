<?php $__env->startSection('title','Fee Defaulters'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fee Defaulters</h1>
      <p class="page-subtitle">Students with no fee payment in <?php echo e($currentYear?->name); ?></p>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if(session('error')): ?><div class="alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select text-sm w-32">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Portal Status</label>
        <select name="blocked" class="select text-sm w-36">
          <option value="">All</option>
          <option value="1" <?php if(request('blocked') === '1'): echo 'selected'; endif; ?>>Blocked</option>
          <option value="0" <?php if(request('blocked') === '0'): echo 'selected'; endif; ?>>Not Blocked</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?php echo e(route('fees.defaulters')); ?>" class="btn btn-secondary btn-sm text-slate-500">Reset</a>
    </div>
  </form>

  
  <?php
    $total   = $defaulters instanceof \Illuminate\Pagination\LengthAwarePaginator ? $defaulters->total() : $defaulters->count();
    $blocked = $defaulters->where('portal_blocked', true)->count();
  ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-red-600"><?php echo e($total); ?></p>
      <p class="text-xs text-slate-500 mt-0.5">Total Defaulters</p>
    </div>
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-orange-600"><?php echo e($blocked); ?></p>
      <p class="text-xs text-slate-500 mt-0.5">Portal Blocked</p>
    </div>
    <div class="card-flat text-center py-3">
      <p class="text-2xl font-bold text-slate-600"><?php echo e($total - $blocked); ?></p>
      <p class="text-xs text-slate-500 mt-0.5">Portal Active</p>
    </div>
  </div>

  <div class="card overflow-x-auto" x-data="{ blockId: null, blockName: '' }">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Student</th>
          <th class="th">Adm #</th>
          <th class="th">Class</th>
          <th class="th">Parent Mobile</th>
          <th class="th text-center">Portal</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $defaulters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr <?php echo e($s->portal_blocked ? 'bg-red-50/30' : ''); ?>">
          <td class="td font-medium text-slate-800">
            <?php echo e($s->full_name); ?>

            <?php if($s->portal_blocked): ?>
              <p class="text-xs text-red-500 mt-0.5"><?php echo e($s->portal_block_reason); ?></p>
            <?php endif; ?>
          </td>
          <td class="td font-mono text-xs"><?php echo e($s->admission_number); ?></td>
          <td class="td"><?php echo e($s->currentEnrollment?->class?->name ?? '—'); ?></td>
          <td class="td font-mono text-sm"><?php echo e($s->father_mobile ?? $s->mobile ?? '—'); ?></td>
          <td class="td text-center">
            <?php if($s->portal_blocked): ?>
              <span class="badge-red">Blocked</span>
              <?php if($s->portal_blocked_at): ?>
                <p class="text-xs text-slate-400 mt-0.5"><?php echo e($s->portal_blocked_at->format('d M Y')); ?></p>
              <?php endif; ?>
            <?php else: ?>
              <span class="badge-green">Active</span>
            <?php endif; ?>
          </td>
          <td class="td">
            <?php if($s->portal_blocked): ?>
              <form method="POST" action="<?php echo e(route('fees.defaulters.unblock', $s->id)); ?>"
                    onsubmit="return confirm('Restore portal access for <?php echo e($s->full_name); ?>?')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-xs bg-green-50 text-green-700 hover:bg-green-100 rounded px-2 py-0.5 text-xs">
                  Unblock
                </button>
              </form>
            <?php else: ?>
              <button type="button"
                      @click="blockId = <?php echo e($s->id); ?>; blockName = '<?php echo e(addslashes($s->full_name)); ?>'"
                      class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 rounded px-2 py-0.5 text-xs">
                Block Portal
              </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr class="tr">
          <td colspan="6" class="td text-center py-10 text-slate-400">No defaulters found.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <?php if($defaulters instanceof \Illuminate\Pagination\LengthAwarePaginator && $defaulters->hasPages()): ?>
      <div class="p-4 text-sm text-slate-500"><?php echo e($defaulters->links()); ?></div>
    <?php endif; ?>

    
    <div x-show="blockId !== null" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         style="display:none">
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm" @click.away="blockId = null">
        <h3 class="font-semibold text-slate-700 mb-1">Block Portal Access</h3>
        <p class="text-sm text-slate-500 mb-3">Block student portal for <span class="font-medium text-slate-800" x-text="blockName"></span>?</p>
        <form method="POST" :action="'/fees/defaulters/' + blockId + '/block-portal'" class="space-y-3">
          <?php echo csrf_field(); ?>
          <div>
            <label class="label text-xs">Reason (shown to student)</label>
            <input type="text" name="reason" class="input text-sm"
                   placeholder="e.g. Fee dues pending since Term 1"
                   value="Long-term fee defaulter – portal access restricted">
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white btn-sm flex-1">Block Access</button>
            <button type="button" @click="blockId = null" class="btn btn-secondary btn-sm">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <div class="card-flat p-4 text-xs text-slate-500">
    <strong class="text-slate-700">How portal blocking works:</strong>
    Blocked students cannot log in to the student/parent portal until access is restored.
    Check the portal login middleware to enforce <code>portal_blocked</code> flag.
    You can also automate blocking by running a scheduled command that queries students with overdue fees beyond a threshold.
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\defaulters.blade.php ENDPATH**/ ?>