<?php $__env->startSection('title','Subject Allocation History'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Subject Allocation History</h1>
      <p class="page-subtitle">View teacher-subject assignments across all academic years</p>
    </div>
    <a href="<?php echo e(route('academics.allocation')); ?>" class="btn btn-secondary btn-sm">← Current Allocations</a>
  </div>

  
  <form method="GET" class="card space-y-0 py-3 px-4">
    <div class="flex gap-3 flex-wrap items-end">
      <div>
        <label class="label text-xs">Academic Year</label>
        <select name="academic_year_id" class="select w-40">
          <option value="">All Years</option>
          <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y->id); ?>" <?php if(request('academic_year_id')==$y->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Teacher</label>
        <select name="teacher_id" class="select w-48">
          <option value="">All Teachers</option>
          <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($t->id); ?>" <?php if(request('teacher_id')==$t->id): echo 'selected'; endif; ?>><?php echo e($t->full_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Subject</label>
        <select name="subject_id" class="select w-40">
          <option value="">All Subjects</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if(request()->hasAny(['academic_year_id','teacher_id','subject_id','class_id'])): ?>
        <a href="<?php echo e(route('academics.allocation.history')); ?>" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Academic Year','Teacher','Subject','Class','Section','Assigned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <span class="badge-<?php echo e($a->academicYear?->is_current ? 'green' : 'slate'); ?> text-xs">
              <?php echo e($a->academicYear?->name ?? 'Unspecified'); ?>

            </span>
          </td>
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($a->employee?->full_name ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-700"><?php echo e($a->subject?->name ?? '—'); ?>

            <?php if($a->subject?->type): ?><span class="text-xs text-slate-400 ml-1">(<?php echo e($a->subject->type); ?>)</span><?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-600"><?php echo e($a->class?->name ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-500"><?php echo e($a->section?->name ?? <span class="text-slate-300">All</span>); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($a->created_at?->format('d M Y')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No allocation records found for the selected filters.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($history->hasPages()): ?><div class="px-4 pb-3"><?php echo e($history->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\allocation-history.blade.php ENDPATH**/ ?>