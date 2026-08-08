<?php $__env->startSection('title','Student Promotion'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Student Promotion & Rollover</h1>
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center py-5"><p class="text-3xl font-bold text-green-600"><?php echo e($stats['promoted'] ?? 0); ?></p><p class="text-sm text-slate-500 mt-1">Promoted</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-amber-600"><?php echo e($stats['detained'] ?? 0); ?></p><p class="text-sm text-slate-500 mt-1">Detained</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-slate-600"><?php echo e($stats['pending'] ?? 0); ?></p><p class="text-sm text-slate-500 mt-1">Pending</p></div>
  </div>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Bulk Promotion</h3>
    <form method="POST" action="<?php echo e(route('students.promotions.bulk')); ?>" class="space-y-4" x-data="{confirm:false}">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="label">From Class <span class="text-red-500">*</span></label>
          <select name="from_class_id" class="select" required>
            <option value="">Select</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">To Class (next year)</label>
          <select name="to_class_id" class="select">
            <option value="">Select (leave blank to retain)</option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Target Academic Year</label>
          <select name="to_academic_year_id" class="select">
            <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($y->id); ?>"><?php echo e($y->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700">
        ⚠️ This will promote ALL active students of the selected class. Individual overrides can be done from each student profile.
      </div>
      <button type="submit" class="btn btn-primary" @click.prevent="confirm=true" x-show="!confirm">Promote All Students</button>
      <div x-show="confirm" class="flex items-center gap-3">
        <span class="text-sm text-slate-600">Are you sure? This cannot be undone.</span>
        <button type="submit" class="btn btn-primary">Yes, Promote</button>
        <button type="button" @click="confirm=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
  
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Selective Promotion</h3>
    <?php if($preselectIds): ?>
        <div class="alert-info mb-4 text-sm">
            <?php echo e(count($preselectIds)); ?> student(s) pre-selected from exam eligibility check.
        </div>
    <?php endif; ?>
    <form method="GET" class="flex flex-wrap gap-4 items-end mb-5">
        <div>
            <label class="label">From Class</label>
            <select name="class_id" class="select">
                <option value="">Select to load students</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php if(request('class_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php if(request('preselect_ids')): ?>
            <input type="hidden" name="preselect_ids" value="<?php echo e(request('preselect_ids')); ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-secondary">Load Students</button>
        <a href="<?php echo e(route('examinations.promotion-eligibility')); ?>" class="btn btn-secondary text-sm">Check Eligibility by Exam</a>
    </form>

    <?php if($fromClassStudents->isNotEmpty()): ?>
    <form method="POST" action="<?php echo e(route('students.promotions.process')); ?>" x-data="{confirm:false}">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="from_class_id" value="<?php echo e(request('class_id')); ?>">
        <div class="mb-4">
            <label class="label">To Class</label>
            <select name="to_class_id" class="select" required>
                <option value="">Select target class</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="mb-4 flex gap-3">
            <button type="button" onclick="document.querySelectorAll('.promo-cb').forEach(c=>c.checked=true)" class="btn btn-secondary text-xs">Select All</button>
            <button type="button" onclick="document.querySelectorAll('.promo-cb').forEach(c=>c.checked=false)" class="btn btn-secondary text-xs">Deselect All</button>
        </div>
        <div class="table-wrap mb-4">
            <table class="w-full">
                <thead><tr>
                    <th class="th w-10"><input type="checkbox" onclick="document.querySelectorAll('.promo-cb').forEach(c=>c.checked=this.checked)"></th>
                    <th class="th">Student</th>
                    <th class="th">Admission No.</th>
                </tr></thead>
                <tbody>
                    <?php $__currentLoopData = $fromClassStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="tr">
                        <td class="td"><input type="checkbox" name="student_ids[]" value="<?php echo e($enrollment->student_id); ?>" class="promo-cb"
                            <?php echo e(in_array($enrollment->student_id, $preselectIds) ? 'checked' : ''); ?>></td>
                        <td class="td font-medium"><?php echo e($enrollment->student?->first_name); ?> <?php echo e($enrollment->student?->last_name); ?></td>
                        <td class="td text-slate-500"><?php echo e($enrollment->student?->admission_number); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div x-show="!confirm">
            <button type="button" @click="confirm=true" class="btn btn-primary">Promote Selected</button>
        </div>
        <div x-show="confirm" class="flex items-center gap-3">
            <span class="text-sm text-slate-600">Promote selected students?</span>
            <button type="submit" class="btn btn-primary">Confirm</button>
            <button type="button" @click="confirm=false" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
    <?php elseif(request('class_id')): ?>
        <p class="text-slate-400 text-sm">No active enrollments found for this class.</p>
    <?php endif; ?>
  </div>

  <div class="card overflow-hidden">
    <h3 class="font-semibold text-slate-700 px-4 pt-4 pb-2">Promotion History</h3>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','From Class','To Class','From Year','To Year','Status','Date']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-2 font-medium text-slate-800"><?php echo e($p->student?->full_name); ?></td>
          <td class="px-4 py-2 text-slate-500"><?php echo e($p->fromClass?->name); ?></td>
          <td class="px-4 py-2 text-slate-500"><?php echo e($p->toClass?->name ?? '—'); ?></td>
          <td class="px-4 py-2 text-slate-400 text-xs"><?php echo e($p->fromAcademicYear?->name ?? '—'); ?></td>
          <td class="px-4 py-2 text-slate-400 text-xs"><?php echo e($p->toAcademicYear?->name ?? '—'); ?></td>
          <td class="px-4 py-2"><span class="badge-<?php echo e($p->status === 'promoted' ? 'green' : ($p->status === 'detained' ? 'amber' : 'slate')); ?> capitalize"><?php echo e($p->status); ?></span></td>
          <td class="px-4 py-2 text-slate-400 text-xs"><?php echo e($p->promoted_at?->format('d M Y')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No promotion records.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($promotions->hasPages()): ?><div class="px-4 pb-3"><?php echo e($promotions->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\promotions.blade.php ENDPATH**/ ?>