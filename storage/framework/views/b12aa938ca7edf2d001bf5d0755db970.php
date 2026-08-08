
<?php $__env->startSection('title', 'Student Directory'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ view: '<?php echo e($view); ?>' }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Student Directory</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?> &mdash; <?php echo e($students->total()); ?> active students</p>
    </div>
    
    <div class="flex items-center gap-2">
      <div class="flex items-center bg-slate-100 rounded-lg p-1">
        <button @click="view='grid'; $nextTick(() => updateUrl('grid'))"
          :class="view==='grid' ? 'bg-white shadow text-slate-800' : 'text-slate-400 hover:text-slate-600'"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          Grid
        </button>
        <button @click="view='list'; $nextTick(() => updateUrl('list'))"
          :class="view==='list' ? 'bg-white shadow text-slate-800' : 'text-slate-400 hover:text-slate-600'"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          List
        </button>
      </div>
    </div>
  </div>

  
  <form method="GET" class="card-flat py-4" id="filterForm">
    <input type="hidden" name="view" :value="view">
    <div class="flex flex-wrap gap-3 items-end">
      <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Name or admission#…" class="input w-56">
      <select name="class_id" class="select w-40">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if(request()->hasAny(['search','class_id'])): ?>
        <a href="<?php echo e(route('students.directory')); ?>" class="btn btn-ghost btn-sm">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  
  <div x-show="view==='grid'" x-transition>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
      <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('students.show', $student->id)); ?>" class="card hover:shadow-md transition group text-center py-5 px-3">
          <div class="w-16 h-16 rounded-full mx-auto mb-3 bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
            <?php if($student->photo): ?>
              <img src="<?php echo e(Storage::url($student->photo)); ?>" class="w-16 h-16 rounded-full object-cover" alt="<?php echo e($student->full_name); ?>">
            <?php else: ?>
              <span class="text-blue-700 text-xl font-bold"><?php echo e(strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1))); ?></span>
            <?php endif; ?>
          </div>
          <p class="font-semibold text-slate-800 text-sm leading-tight group-hover:text-blue-600 transition"><?php echo e($student->full_name); ?></p>
          <p class="text-xs text-slate-400 mt-0.5"><?php echo e($student->currentEnrollment?->class?->name); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($student->currentEnrollment?->section?->name); ?></p>
          <?php if($student->currentEnrollment?->roll_number): ?>
            <p class="text-xs text-indigo-400 mt-1 font-mono">Roll: <?php echo e($student->currentEnrollment->roll_number); ?></p>
          <?php endif; ?>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-6 text-center py-12 text-slate-400">No students found.</div>
      <?php endif; ?>
    </div>
  </div>

  
  <div x-show="view==='list'" x-transition>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Adm. #</th>
            <th class="th">Class</th>
            <th class="th">Roll</th>
            <th class="th">Father</th>
            <th class="th">Mobile</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="tr cursor-pointer" onclick="window.location='<?php echo e(route('students.show', $student->id)); ?>'">
              <td class="td">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-700 text-xs font-bold"><?php echo e(strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1))); ?></span>
                  </div>
                  <div>
                    <p class="font-medium text-slate-800"><?php echo e($student->full_name); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e($student->dob?->format('d M Y')); ?></p>
                  </div>
                </div>
              </td>
              <td class="td font-mono text-xs text-blue-600"><?php echo e($student->admission_number); ?></td>
              <td class="td"><?php echo e($student->currentEnrollment?->class?->name); ?> <?php echo e($student->currentEnrollment?->section?->name); ?></td>
              <td class="td font-mono text-sm"><?php echo e($student->currentEnrollment?->roll_number ?? '—'); ?></td>
              <td class="td text-sm"><?php echo e($student->father_name); ?></td>
              <td class="td font-mono text-sm"><?php echo e($student->father_mobile ?? $student->mobile ?? '—'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="td text-center py-12 text-slate-400">No students found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if($students->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($students->firstItem()); ?>–<?php echo e($students->lastItem()); ?> of <?php echo e($students->total()); ?></span>
      <?php echo e($students->links()); ?>

    </div>
  <?php endif; ?>

</div>
<?php $__env->startPush('scripts'); ?>
<script>
function updateUrl(view) {
  const url = new URL(window.location.href);
  url.searchParams.set('view', view);
  window.history.replaceState({}, '', url);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\directory.blade.php ENDPATH**/ ?>