<?php $__env->startSection('title', 'Competency Master — NEP 2020'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ addOpen: false }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Competency Master</h1>
      <p class="page-subtitle">NEP 2020 — Subject-wise competencies per class</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('examinations.competency-assessment')); ?>" class="btn btn-secondary btn-sm">Assess Students</a>
      <a href="<?php echo e(route('examinations.competency-report')); ?>" class="btn btn-secondary btn-sm">Progress Report</a>
      <a href="<?php echo e(route('examinations.coscholastic-nep')); ?>" class="btn btn-secondary btn-sm">Co-Scholastic</a>
      <a href="<?php echo e(route('examinations.activity-records')); ?>" class="btn btn-secondary btn-sm">Activity Records</a>
      <button @click="addOpen = !addOpen" class="btn btn-primary btn-sm">+ Add Competency</button>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <div x-show="addOpen" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Add Competency</h3>
    <form method="POST" action="<?php echo e(route('examinations.competencies.store')); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Class <span class="text-red-500">*</span></label>
        <select name="class_id" class="select" required>
          <option value="">Select Class</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <option value="">Select Subject</option>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Competency Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" placeholder="e.g. Understands place value" required>
      </div>
      <div>
        <label class="label">Code</label>
        <input type="text" name="code" class="input" placeholder="e.g. MATH-G3-C1">
      </div>
      <div>
        <label class="label">Domain <span class="text-red-500">*</span></label>
        <select name="domain" class="select" required>
          <option value="cognitive">Cognitive</option>
          <option value="affective">Affective</option>
          <option value="psychomotor">Psychomotor</option>
          <option value="co_scholastic">Co-Scholastic</option>
        </select>
      </div>
      <div>
        <label class="label">Sort Order</label>
        <input type="number" name="sort_order" class="input" value="0" min="0">
      </div>
      <div class="sm:col-span-3">
        <label class="label">Description</label>
        <textarea name="description" class="input" rows="2" placeholder="Optional description…"></textarea>
      </div>
      <div class="sm:col-span-3 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Competency</button>
        <button type="button" @click="addOpen = false" class="btn btn-ghost btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Subject</label>
      <select name="subject_id" class="select w-40">
        <option value="">All Subjects</option>
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($sub->id); ?>" <?php if(request('subject_id') == $sub->id): echo 'selected'; endif; ?>><?php echo e($sub->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <?php if(request()->hasAny(['class_id','subject_id'])): ?>
      <a href="<?php echo e(route('examinations.competencies')); ?>" class="btn btn-ghost btn-sm">Clear</a>
    <?php endif; ?>
  </form>

  
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Code</th>
          <th class="th">Competency</th>
          <th class="th">Class</th>
          <th class="th">Subject</th>
          <th class="th">Domain</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td font-mono text-xs text-blue-600"><?php echo e($comp->code ?? '—'); ?></td>
            <td class="td font-medium text-slate-800">
              <?php echo e($comp->name); ?>

              <?php if($comp->description): ?>
                <p class="text-xs text-slate-400 mt-0.5"><?php echo e(Str::limit($comp->description, 80)); ?></p>
              <?php endif; ?>
            </td>
            <td class="td"><?php echo e($comp->class?->name); ?></td>
            <td class="td"><?php echo e($comp->subject?->name); ?></td>
            <td class="td">
              <?php $dc = ['cognitive'=>'badge-blue','affective'=>'badge-purple','psychomotor'=>'badge-green','co_scholastic'=>'badge-amber'] ?>
              <span class="<?php echo e($dc[$comp->domain] ?? 'badge-slate'); ?> capitalize"><?php echo e(str_replace('_', ' ', $comp->domain)); ?></span>
            </td>
            <td class="td text-right">
              <form method="POST" action="<?php echo e(route('examinations.competencies.delete', $comp->id)); ?>" onsubmit="return confirm('Delete this competency?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-icon text-red-400 hover:text-red-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No competencies defined yet. Add your first competency above.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($competencies->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($competencies->firstItem()); ?>–<?php echo e($competencies->lastItem()); ?> of <?php echo e($competencies->total()); ?></span>
      <?php echo e($competencies->links()); ?>

    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\competencies.blade.php ENDPATH**/ ?>