<?php $__env->startSection('title', 'TC Request Workflow'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Transfer Certificate Requests</h1>
    <button x-data @click="$dispatch('open-modal','new-tc-request')" class="btn btn-primary btn-sm">+ New TC Request</button>
  </div>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3">
      <select name="status" class="select w-40">
        <option value="">All Status</option>
        <?php $__currentLoopData = ['pending'=>'Pending','hod_approved'=>'HOD Approved','principal_approved'=>'Principal Approved','issued'=>'Issued','rejected'=>'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($v); ?>" <?php if(request('status')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Student','Class','Requested By','Reason','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $statusColors = [
            'pending'             => 'badge-amber',
            'hod_approved'        => 'badge-blue',
            'principal_approved'  => 'badge-purple',
            'issued'              => 'badge-green',
            'rejected'            => 'badge-red',
          ];
          $statusLabels = [
            'pending'             => 'Pending',
            'hod_approved'        => 'HOD Approved',
            'principal_approved'  => 'Principal Approved',
            'issued'              => 'Issued',
            'rejected'            => 'Rejected',
          ];
        ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">
            <?php echo e($r->student?->full_name); ?>

            <div class="text-xs text-slate-400"><?php echo e($r->student?->admission_number); ?></div>
          </td>
          <td class="px-4 py-3 text-xs text-slate-500">
            <?php echo e($r->student?->currentEnrollment?->class?->name ?? '—'); ?>

          </td>
          <td class="px-4 py-3 text-xs text-slate-500">
            <?php echo e(ucfirst($r->requested_by_type)); ?>

            <?php if($r->requestedByUser): ?> — <?php echo e($r->requestedByUser?->name); ?><?php endif; ?>
            <div class="text-slate-400"><?php echo e($r->created_at?->format('d M Y')); ?></div>
          </td>
          <td class="px-4 py-3 text-slate-600 text-sm max-w-xs"><?php echo e($r->reason ?? '—'); ?></td>
          <td class="px-4 py-3">
            <span class="<?php echo e($statusColors[$r->status] ?? 'badge-slate'); ?>"><?php echo e($statusLabels[$r->status] ?? $r->status); ?></span>
          </td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              <?php if($r->status === 'pending'): ?>
                <form method="POST" action="<?php echo e(route('students.tc-requests.approve', $r->id)); ?>" class="inline">
                  <?php echo csrf_field(); ?> <input type="hidden" name="stage" value="hod">
                  <button class="btn btn-primary btn-xs">HOD Approve</button>
                </form>
              <?php elseif($r->status === 'hod_approved'): ?>
                <form method="POST" action="<?php echo e(route('students.tc-requests.approve', $r->id)); ?>" class="inline">
                  <?php echo csrf_field(); ?> <input type="hidden" name="stage" value="principal">
                  <button class="btn btn-primary btn-xs">Principal Approve</button>
                </form>
              <?php elseif($r->status === 'principal_approved'): ?>
                <?php
                  $dupIssued = \App\Models\TcRequest::where('student_id', $r->student_id)
                    ->where('status', 'issued')->where('id', '!=', $r->id)->exists();
                ?>
                <form method="POST" action="<?php echo e(route('students.tc-requests.approve', $r->id)); ?>" class="inline"
                  x-data="{dup:<?php echo e($dupIssued ? 'true' : 'false'); ?>}">
                  <?php echo csrf_field(); ?> <input type="hidden" name="stage" value="issue">
                  <div x-show="dup" class="text-xs text-red-500 mb-1">⚠ TC already issued. Tick to override:</div>
                  <label x-show="dup" class="flex items-center gap-1 text-xs mb-1">
                    <input type="checkbox" name="force_issue" value="1"> Override — issue duplicate TC
                  </label>
                  <button class="btn btn-green btn-xs">Issue TC</button>
                </form>
              <?php endif; ?>
              <?php if(in_array($r->status, ['pending','hod_approved'])): ?>
              <form method="POST" action="<?php echo e(route('students.tc-requests.reject', $r->id)); ?>" class="inline"
                x-data="{r:''}" @submit.prevent="if(r.trim()){$el.querySelector('[name=reason]').value=r;$el.submit()}else{alert('Enter rejection reason')}">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="reason">
                <input type="text" x-model="r" placeholder="Reason..." class="input text-xs py-1 w-32">
                <button type="submit" class="btn btn-red btn-xs ml-1">Reject</button>
              </form>
              <?php endif; ?>
              <?php if($r->status === 'issued'): ?>
              <a href="<?php echo e(route('students.tc', $r->student_id)); ?>" class="btn btn-secondary btn-xs">View TC</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No TC requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($requests->hasPages()): ?>
    <div class="px-4 pb-3 text-sm"><?php echo e($requests->links()); ?></div>
    <?php endif; ?>
  </div>
</div>

<?php $__env->startPush('modals'); ?>
<div x-data="{show:false}" @open-modal.window="if($event.detail==='new-tc-request') show=true"
  x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="show=false">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">New TC Request</h2>
      <button @click="show=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="<?php echo e(route('students.tc-requests.store')); ?>" class="px-6 py-5 space-y-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Student Admission No / Name</label>
        <input type="text" name="student_search" class="input w-full" placeholder="Search student..."
          x-data="{}" @input.debounce="/* live search hook */">
        <input type="hidden" name="student_id" id="tc_student_id">
        <p class="text-xs text-slate-400 mt-1">Enter admission number directly in the field below if search is unavailable.</p>
      </div>
      <div>
        <label class="label">Student ID (direct)</label>
        <input type="number" name="student_id" class="input w-full" placeholder="Student ID">
      </div>
      <div>
        <label class="label">Reason for TC</label>
        <textarea name="reason" rows="3" class="input w-full" placeholder="Reason for leaving..."></textarea>
      </div>
      <div class="flex gap-3 justify-end">
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\tc-requests.blade.php ENDPATH**/ ?>