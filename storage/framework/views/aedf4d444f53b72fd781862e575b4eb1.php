<?php $__env->startSection('title', 'Academic Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Academic Management</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name ?? 'Current Year'); ?></p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <a href="<?php echo e(route('academics.sections.manage')); ?>" class="btn btn-secondary btn-sm">Manage Sections</a>
      <a href="<?php echo e(route('academics.terms')); ?>" class="btn btn-secondary btn-sm">Terms</a>
      <a href="<?php echo e(route('academics.working-days')); ?>" class="btn btn-secondary btn-sm">Working Days</a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($classes->count()); ?></p>
      <p class="text-sm text-slate-500 mt-1">Classes</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($classes->sum('sections_count')); ?></p>
      <p class="text-sm text-slate-500 mt-1">Sections</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($subjects); ?></p>
      <p class="text-sm text-slate-500 mt-1">Subjects</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($teachers); ?></p>
      <p class="text-sm text-slate-500 mt-1">Staff</p>
    </div>
  </div>

  
  <?php if($pendingHomework > 0 || $activeNotices > 0 || $pendingSubstitutions > 0): ?>
  <div class="flex flex-wrap gap-3">
    <?php if($activeNotices > 0): ?>
    <a href="<?php echo e(route('academics.notices')); ?>" class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-xl text-sm font-medium text-blue-700 hover:bg-blue-100 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      <?php echo e($activeNotices); ?> Active Notice<?php echo e($activeNotices > 1 ? 's' : ''); ?>

    </a>
    <?php endif; ?>
    <?php if($pendingHomework > 0): ?>
    <a href="<?php echo e(route('academics.homework')); ?>" class="flex items-center gap-2 px-4 py-2 bg-purple-50 border border-purple-200 rounded-xl text-sm font-medium text-purple-700 hover:bg-purple-100 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      <?php echo e($pendingHomework); ?> Pending Assignment<?php echo e($pendingHomework > 1 ? 's' : ''); ?>

    </a>
    <?php endif; ?>
    <?php if($pendingSubstitutions > 0): ?>
    <a href="<?php echo e(route('academics.substitutions')); ?>" class="flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-sm font-medium text-amber-700 hover:bg-amber-100 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      <?php echo e($pendingSubstitutions); ?> Substitution<?php echo e($pendingSubstitutions > 1 ? 's' : ''); ?> Today
    </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  
  <div>
    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Core Academics</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
      <?php $__currentLoopData = [
        ['Timetable',    'academics.timetable',    'from-blue-500 to-indigo-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        ['Subjects',     'academics.subjects',     'from-green-500 to-emerald-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
        ['Syllabus',     'academics.syllabus',     'from-amber-500 to-orange-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
        ['Homework',     'academics.homework',     'from-purple-500 to-pink-500',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
        ['Sections',     'academics.sections.manage','from-teal-500 to-cyan-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route($route)); ?>" class="card-flat flex flex-col items-center py-6 gap-2 hover:shadow-card-md transition hover:-translate-y-0.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
        </div>
        <span class="text-sm font-semibold text-slate-600"><?php echo e($label); ?></span>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  
  <div>
    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Planning & Communication</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
      <?php $__currentLoopData = [
        ['Lesson Plans',    'academics.lesson-plans',   'from-rose-500 to-pink-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
        ['Notices',         'academics.notices',        'from-blue-500 to-sky-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>'],
        ['Substitutions',   'academics.substitutions',  'from-amber-500 to-yellow-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>'],
        ['Terms / Semesters','academics.terms',         'from-violet-500 to-purple-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        ['Working Days',    'academics.working-days',   'from-slate-500 to-gray-600',   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route($route)); ?>" class="card-flat flex flex-col items-center py-6 gap-2 hover:shadow-card-md transition hover:-translate-y-0.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
        </div>
        <span class="text-sm font-semibold text-slate-600"><?php echo e($label); ?></span>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  
  <?php if($classes->count()): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Class Structure — <?php echo e($currentYear?->name); ?></h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th text-left">Class</th>
            <th class="th text-center">Sections</th>
            <th class="th text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium text-slate-800"><?php echo e($cls->name); ?></td>
            <td class="td text-center">
              <span class="badge-blue"><?php echo e($cls->sections_count); ?> section<?php echo e($cls->sections_count != 1 ? 's' : ''); ?></span>
            </td>
            <td class="td text-right">
              <div class="flex justify-end gap-1">
                <a href="<?php echo e(route('academics.timetable', ['class_id' => $cls->id])); ?>" class="btn-icon" title="View Timetable">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </a>
                <a href="<?php echo e(route('academics.syllabus', ['class_id' => $cls->id])); ?>" class="btn-icon" title="View Syllabus">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\index.blade.php ENDPATH**/ ?>