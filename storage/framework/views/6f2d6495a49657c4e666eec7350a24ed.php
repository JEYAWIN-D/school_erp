<?php $__env->startSection('title', 'LMS — Online Learning'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Learning Management System</h1>
      <p class="page-subtitle"><?php echo e($publishedCount); ?> published courses &bull; <?php echo e($totalLessons); ?> lessons</p>
    </div>
    <a href="<?php echo e(route('lms.courses.create')); ?>" class="btn btn-primary self-start sm:self-auto">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      New Course
    </a>
  </div>

  
  <?php if($pendingSubmissions > 0): ?>
  <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
    <p class="text-sm font-semibold text-amber-800"><?php echo e($pendingSubmissions); ?> assignment submission(s) awaiting grading.</p>
  </div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-blue-500 to-indigo-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalCourses); ?></p>
      <p class="text-xs text-slate-500">Courses</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-number text-green-600"><?php echo e($publishedCount); ?></p>
      <p class="text-xs text-slate-500">Published</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-violet-500 to-purple-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8.118v7.764A2 2 0 005 18h8a2 2 0 002-2V8.118a2 2 0 00-1.105-1.79L9 4.382a2 2 0 00-1.789 0L3.105 6.328A2 2 0 003 8.118z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalLessons); ?></p>
      <p class="text-xs text-slate-500">Lessons</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalQuizzes); ?></p>
      <p class="text-xs text-slate-500">Quizzes</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-teal-500 to-cyan-600 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalAssignments); ?></p>
      <p class="text-xs text-slate-500">Assignments</p>
    </div>
    <div class="card">
      <div class="stat-icon bg-gradient-to-br from-pink-500 to-rose-500 mb-3">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p class="stat-number"><?php echo e($totalEnrollments); ?></p>
      <p class="text-xs text-slate-500">Enrolled</p>
    </div>
  </div>

  
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card hover:shadow-md transition group">
      <?php if($course->thumbnail): ?>
        <img src="<?php echo e(Storage::url($course->thumbnail)); ?>" class="w-full h-32 object-cover rounded-xl mb-3 -mt-1 -mx-1">
      <?php else: ?>
        <div class="w-full h-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl mb-3 -mt-1 flex items-center justify-center">
          <svg class="w-8 h-8 text-white opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
      <?php endif; ?>
      <div class="flex items-start justify-between gap-2">
        <h3 class="font-semibold text-slate-800 text-sm leading-tight"><?php echo e($course->title); ?></h3>
        <span class="badge-<?php echo e($course->status === 'published' ? 'green' : ($course->status === 'archived' ? 'red' : 'blue')); ?> flex-shrink-0 text-xs">
          <?php echo e(ucfirst($course->status)); ?>

        </span>
      </div>
      <p class="text-xs text-slate-400 mt-1"><?php echo e($course->units_count); ?> units &bull; <?php echo e($course->lessons_count); ?> lessons</p>
      <p class="text-xs text-slate-400">By <?php echo e($course->creator?->name ?? '—'); ?></p>
      <div class="flex gap-2 mt-3">
        <a href="<?php echo e(route('lms.courses.show', $course->id)); ?>" class="btn-xs btn-primary flex-1 text-center">Manage</a>
        <a href="<?php echo e(route('lms.courses.progress', $course->id)); ?>" class="btn-xs btn-secondary flex-1 text-center">Progress</a>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-full card text-center py-12">
      <p class="text-slate-400 mb-3">No courses created yet</p>
      <a href="<?php echo e(route('lms.courses.create')); ?>" class="btn-primary btn-sm">Create First Course</a>
    </div>
    <?php endif; ?>
  </div>

  <div><?php echo e($courses->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\index.blade.php ENDPATH**/ ?>