<?php $__env->startSection('title','Marks Recheck Requests'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Marks Recheck Requests</h1>
    <button x-data @click="$dispatch('open-modal','new-recheck')" class="btn btn-primary btn-sm">+ New Request</button>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Exam</label>
        <select name="exam_id" class="select w-48">
          <option value="">All Exams</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>" <?php if(request('exam_id')==$e->id): echo 'selected'; endif; ?>><?php echo e($e->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Status</label>
        <select name="status" class="select w-36">
          <option value="">All</option>
          <?php $__currentLoopData = ['pending'=>'Pending','under_review'=>'Under Review','revised'=>'Revised','rejected'=>'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($v); ?>" <?php if(request('status')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Student','Exam','Subject','Marks Before','Marks After','Reason','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($r->student?->full_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($r->student?->admission_number); ?></p>
          </td>
          <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($r->exam?->name); ?></td>
          <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($r->examSchedule?->subject?->name); ?></td>
          <td class="px-4 py-3 font-mono text-slate-700"><?php echo e($r->marks_before ?? '—'); ?></td>
          <td class="px-4 py-3 font-mono <?php echo e($r->marks_after ? 'text-green-600 font-semibold' : 'text-slate-300'); ?>">
            <?php echo e($r->marks_after ?? '—'); ?>

          </td>
          <td class="px-4 py-3 text-xs text-slate-500 max-w-xs"><?php echo e(Str::limit($r->reason, 50) ?? '—'); ?></td>
          <td class="px-4 py-3">
            <?php $statusColors = ['pending'=>'badge-amber','under_review'=>'badge-blue','revised'=>'badge-green','rejected'=>'badge-red']; ?>
            <span class="<?php echo e($statusColors[$r->status] ?? 'badge-slate'); ?> text-xs"><?php echo e(str_replace('_',' ',ucfirst($r->status))); ?></span>
          </td>
          <td class="px-4 py-3" x-data="{process:false}">
            <?php if(in_array($r->status, ['pending','under_review'])): ?>
            <button @click="process=!process" class="btn btn-secondary btn-xs">Process</button>
            <div x-show="process" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="process=false">
              <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
                <h3 class="font-semibold">Process Recheck — <?php echo e($r->student?->first_name); ?></h3>
                <p class="text-sm text-slate-500"><?php echo e($r->examSchedule?->subject?->name); ?> | Original: <?php echo e($r->marks_before); ?></p>
                <form method="POST" action="<?php echo e(route('examinations.recheck-requests.process', $r->id)); ?>" class="space-y-3">
                  <?php echo csrf_field(); ?>
                  <div>
                    <label class="label text-xs">Action <span class="text-red-500">*</span></label>
                    <select name="action" class="select" required>
                      <option value="under_review">Mark as Under Review</option>
                      <option value="revised">Revised (update marks)</option>
                      <option value="rejected">Reject Request</option>
                    </select>
                  </div>
                  <div>
                    <label class="label text-xs">Revised Marks (if revising)</label>
                    <input type="number" name="marks_after" class="input" min="0" max="<?php echo e($r->examSchedule?->max_marks); ?>" step="0.5" placeholder="New marks">
                  </div>
                  <div>
                    <label class="label text-xs">Admin Remarks</label>
                    <textarea name="admin_remarks" rows="2" class="input" placeholder="Remarks for student/parent"></textarea>
                  </div>
                  <div class="flex gap-3 justify-end">
                    <button type="button" @click="process=false" class="btn btn-secondary btn-sm">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                  </div>
                </form>
              </div>
            </div>
            <?php elseif($r->admin_remarks): ?>
            <span class="text-xs text-slate-400"><?php echo e(Str::limit($r->admin_remarks, 40)); ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No recheck requests.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($requests->hasPages()): ?>
    <div class="px-4 pb-3"><?php echo e($requests->links()); ?></div>
    <?php endif; ?>
  </div>
</div>


<div x-data="{open:false}" @open-modal.window="if($event.detail==='new-recheck')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6 z-10 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">New Recheck Request</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="<?php echo e(route('examinations.recheck-requests.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label text-xs">Exam <span class="text-red-500">*</span></label>
        <select name="exam_id" class="select" required>
          <option value="">Select Exam</option>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>"><?php echo e($e->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Student ID <span class="text-red-500">*</span></label>
        <input type="number" name="student_id" class="input" required placeholder="Student database ID">
      </div>
      <div>
        <label class="label text-xs">Exam Schedule ID <span class="text-red-500">*</span></label>
        <input type="number" name="exam_schedule_id" class="input" required placeholder="Exam Schedule ID (from schedules)">
      </div>
      <div>
        <label class="label text-xs">Reason</label>
        <textarea name="reason" rows="3" class="input" placeholder="Reason for recheck request..."></textarea>
      </div>
      <div class="flex gap-3 justify-end pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit Request</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\recheck-requests.blade.php ENDPATH**/ ?>