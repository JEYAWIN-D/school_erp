<?php $__env->startSection('title','Lesson Plans'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Lesson Plans</h1>
    <button x-data @click="$dispatch('open-modal','add-lesson-plan')" class="btn btn-primary btn-sm">+ New Plan</button>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject</label>
        <select name="subject_id" class="select w-36">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Status</label>
        <select name="status" class="select w-32">
          <option value="">All</option>
          <?php $__currentLoopData = ['draft'=>'Draft','submitted'=>'Submitted','approved'=>'Approved','rejected'=>'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
        <?php $__currentLoopData = ['Topic','Class/Subject','Date','Teacher','Status','HOD Remarks','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($plan->topic); ?></p>
            <?php if($plan->learning_objectives): ?>
            <p class="text-xs text-slate-400 truncate max-w-xs"><?php echo e(Str::limit($plan->learning_objectives, 60)); ?></p>
            <?php endif; ?>
            <?php if($plan->is_completed): ?><span class="badge-green text-xs">Completed</span><?php endif; ?>
          </td>
          <td class="px-4 py-3 text-xs text-slate-600">
            <?php echo e($plan->class?->name); ?><br><?php echo e($plan->subject?->name); ?>

          </td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($plan->plan_date?->format('d M Y')); ?></td>
          <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($plan->createdBy?->name); ?></td>
          <td class="px-4 py-3">
            <?php $statusColors = ['draft'=>'badge-slate','submitted'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red']; ?>
            <span class="<?php echo e($statusColors[$plan->status] ?? 'badge-slate'); ?> text-xs capitalize"><?php echo e($plan->status); ?></span>
          </td>
          <td class="px-4 py-3 text-xs text-slate-500 max-w-xs"><?php echo e($plan->hod_remarks ?? '—'); ?></td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1" x-data="{review:false}">
              <?php if($plan->status === 'submitted'): ?>
              <button @click="review=!review" class="btn btn-secondary btn-xs">Review</button>
              <div x-show="review" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="review=false">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
                  <h3 class="font-semibold">HOD Review — <?php echo e($plan->topic); ?></h3>
                  <form method="POST" action="<?php echo e(route('academics.lesson-plans.review', $plan->id)); ?>" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                      <label class="label">Decision</label>
                      <select name="decision" class="select" required>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                      </select>
                    </div>
                    <div>
                      <label class="label">Remarks</label>
                      <textarea name="hod_remarks" rows="3" class="input" placeholder="Feedback for teacher..."></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                      <button type="button" @click="review=false" class="btn btn-secondary btn-sm">Cancel</button>
                      <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                    </div>
                  </form>
                </div>
              </div>
              <?php endif; ?>
              <?php if($plan->status === 'approved' && !$plan->is_completed): ?>
              <form method="POST" action="<?php echo e(route('academics.lesson-plans.complete', $plan->id)); ?>" class="inline">
                <?php echo csrf_field(); ?> <button class="btn btn-ghost btn-xs text-green-600">✓ Done</button>
              </form>
              <?php endif; ?>
              <a href="<?php echo e(route('academics.lesson-plans.pdf', $plan->id)); ?>" target="_blank" class="btn btn-ghost btn-xs" title="Print PDF">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No lesson plans found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($plans->hasPages()): ?>
    <div class="px-4 pb-3"><?php echo e($plans->links()); ?></div>
    <?php endif; ?>
  </div>
</div>


<div x-data="{open:false}" @open-modal.window="if($event.detail==='add-lesson-plan')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">New Lesson Plan</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="<?php echo e(route('academics.lesson-plans.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label text-xs">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select Class</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label text-xs">Subject <span class="text-red-500">*</span></label>
          <select name="subject_id" class="select" required>
            <option value="">Select Subject</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <?php if($syllabusTopics->count()): ?>
        <div class="col-span-2">
          <label class="label text-xs">Link to Syllabus Topic</label>
          <select name="syllabus_id" class="select">
            <option value="">— None —</option>
            <?php $__currentLoopData = $syllabusTopics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($st->id); ?>"><?php echo e($st->topic); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <?php endif; ?>
        <div class="col-span-2">
          <label class="label text-xs">Topic <span class="text-red-500">*</span></label>
          <input type="text" name="topic" class="input" required placeholder="Lesson topic">
        </div>
        <div>
          <label class="label text-xs">Plan Date <span class="text-red-500">*</span></label>
          <input type="date" name="plan_date" class="input" value="<?php echo e(today()->toDateString()); ?>" required>
        </div>
        <div>
          <label class="label text-xs">Duration (mins)</label>
          <input type="number" name="duration_minutes" class="input" value="45" min="1">
        </div>
        <div>
          <label class="label text-xs">Period No.</label>
          <input type="text" name="period_number" class="input" placeholder="e.g. 3">
        </div>
        <div>
          <label class="label text-xs">Teaching Method</label>
          <input type="text" name="teaching_method" class="input" placeholder="e.g. Discussion, Demo">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Learning Objectives</label>
          <textarea name="learning_objectives" rows="2" class="input"></textarea>
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Resources Required</label>
          <input type="text" name="resources_required" class="input" placeholder="Textbook, chart, lab equipment...">
        </div>
        <div class="col-span-2">
          <label class="label text-xs">Teacher Notes</label>
          <textarea name="teacher_notes" rows="2" class="input" placeholder="Internal notes (not shown in review)"></textarea>
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit for Review</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\lesson-plans.blade.php ENDPATH**/ ?>