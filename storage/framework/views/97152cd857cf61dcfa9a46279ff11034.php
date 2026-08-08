<?php $__env->startSection('title','Library Members'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Library Members</h1>
      <p class="page-subtitle">Manage student and staff library memberships</p>
    </div>
    <a href="<?php echo e(route('library.settings')); ?>" class="btn btn-secondary btn-sm">Borrowing Limits</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <div class="flex gap-1 border-b border-slate-200">
    <a href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'students'])); ?>"
       class="px-4 py-2 text-sm font-medium border-b-2 <?php echo e($tab==='students' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
      Students
    </a>
    <a href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'staff'])); ?>"
       class="px-4 py-2 text-sm font-medium border-b-2 <?php echo e($tab==='staff' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700'); ?>">
      Staff (HR Sync)
    </a>
  </div>

  <form method="GET" class="card-flat py-3">
    <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
    <div class="flex gap-3 flex-wrap">
      <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input flex-1"
             placeholder="<?php echo e($tab==='staff' ? 'Search by name or employee ID...' : 'Search by name or admission no...'); ?>">
      <?php if($tab === 'students'): ?>
      <select name="status" class="select w-36">
        <option value="">All Members</option>
        <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option>
        <option value="suspended" <?php if(request('status')==='suspended'): echo 'selected'; endif; ?>>Suspended</option>
      </select>
      <?php endif; ?>
      <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    </div>
  </form>

  <?php if($tab === 'students' && $members): ?>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Admission No','Class','Status','Books Issued','Overdue','Fine Due','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $activeIssues = $s->bookIssues; // eager-loaded (status='issued')
          $issued  = $activeIssues->count();
          $overdue = $activeIssues->filter(fn($i) => $i->due_date < today())->count();
          $fineDue = $activeIssues->filter(fn($i) => $i->due_date < today())->sum('fine_amount');
        ?>
        <tr class="hover:bg-slate-50 <?php echo e($s->library_suspended ? 'opacity-70 bg-red-50/30' : ''); ?>" x-data="{ suspendOpen: false }">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($s->full_name); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($s->admission_number); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($s->currentEnrollment?->class?->name ?? '—'); ?></td>
          <td class="px-4 py-3">
            <?php if($s->library_suspended): ?>
              <span class="badge-red text-xs">Suspended</span>
              <?php if($s->library_suspension_reason): ?>
                <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate" title="<?php echo e($s->library_suspension_reason); ?>"><?php echo e($s->library_suspension_reason); ?></p>
              <?php endif; ?>
            <?php else: ?>
              <span class="badge-green text-xs">Active</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700"><?php echo e($issued ?: '—'); ?></td>
          <td class="px-4 py-3 text-center <?php echo e($overdue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($overdue ?: '—'); ?></td>
          <td class="px-4 py-3 text-center <?php echo e($fineDue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($fineDue > 0 ? '₹'.number_format($fineDue,0) : '—'); ?></td>
          <td class="px-4 py-3 space-y-1">
            <a href="<?php echo e(route('library.members.card', $s->id)); ?>" target="_blank" class="text-indigo-600 hover:underline text-xs block">Print Card</a>
            <a href="<?php echo e(route('library.members.history', $s->id)); ?>" class="text-slate-500 hover:underline text-xs block">History</a>
            <?php if($s->library_suspended): ?>
              <form method="POST" action="<?php echo e(route('library.members.unsuspend', $s->id)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-green-600 hover:underline text-xs">Restore Access</button>
              </form>
            <?php else: ?>
              <button @click="suspendOpen=!suspendOpen" class="text-amber-600 hover:underline text-xs">Suspend</button>
              <div x-show="suspendOpen" x-transition class="mt-1" style="display:none">
                <form method="POST" action="<?php echo e(route('library.members.suspend', $s->id)); ?>" class="flex gap-1">
                  <?php echo csrf_field(); ?>
                  <input type="text" name="reason" class="input text-xs py-0.5 px-2 flex-1" placeholder="Reason (optional)">
                  <button type="submit" class="btn btn-xs bg-amber-100 text-amber-700 border border-amber-300">OK</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No members found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($members->hasPages()): ?><div class="px-4 pb-3"><?php echo e($members->links()); ?></div><?php endif; ?>
  </div>

  <?php elseif($tab === 'staff' && $staff): ?>
  <div class="card overflow-hidden">
    <div class="px-4 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-700">
      All active employees from HR are automatically available as library members. No manual registration needed.
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Employee','ID','Department','Designation','Books Issued','Overdue','Fine Due']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $issued  = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->count();
          $overdue = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->where('due_date','<',today())->count();
          $fineDue = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->where('due_date','<',today())->sum('fine_amount');
        ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($emp->employee_number); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($emp->department ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($emp->designation ?? '—'); ?></td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700"><?php echo e($issued ?: '—'); ?></td>
          <td class="px-4 py-3 text-center <?php echo e($overdue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($overdue ?: '—'); ?></td>
          <td class="px-4 py-3 text-center <?php echo e($fineDue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400'); ?>"><?php echo e($fineDue > 0 ? '₹'.number_format($fineDue,0) : '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No active staff found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($staff->hasPages()): ?><div class="px-4 pb-3"><?php echo e($staff->links()); ?></div><?php endif; ?>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\members.blade.php ENDPATH**/ ?>