<?php $__env->startSection('title','Examinations'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Examinations</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name); ?></p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="<?php echo e(route('examinations.marks-progress')); ?>" class="btn btn-secondary btn-sm">Marks Progress</a>
      <a href="<?php echo e(route('examinations.tabulation')); ?>" class="btn btn-secondary btn-sm">Tabulation Sheet</a>
      <a href="<?php echo e(route('examinations.class-result')); ?>" class="btn btn-secondary btn-sm">Result Summary</a>
      <a href="<?php echo e(route('examinations.marks-import-template')); ?>" class="btn btn-secondary btn-sm">Import Template</a>
      <a href="<?php echo e(route('examinations.create')); ?>" class="btn btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Exam
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['total']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Total Exams</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-blue-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['upcoming']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Upcoming</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-amber-500" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['ongoing']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Ongoing</p>
    </div>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold text-green-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($stats['results']); ?></p>
      <p class="text-sm text-slate-500 mt-1">Results Published</p>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <?php $__currentLoopData = [
      ['Result Summary',      'examinations.class-result',          'from-blue-500 to-indigo-600',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
      ['Failed Students',     'examinations.failed',                'from-red-500 to-rose-600',     '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
      ['Subject Performance', 'examinations.subject-performance',   'from-purple-500 to-indigo-600','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
      ['Student History',     'examinations.student-result-history','from-teal-500 to-cyan-600',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
      ['Marks Progress',      'examinations.marks-progress',        'from-green-500 to-emerald-600', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$route,$color,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($route)); ?>" class="card-flat flex items-center gap-3 py-4 px-4 hover:shadow-card-md transition">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($color); ?> flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $icon; ?></svg>
      </div>
      <span class="font-semibold text-sm text-slate-700"><?php echo e($label); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <?php if($upcomingSchedules->count()): ?>
  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700">Upcoming Exams — Next 7 Days</h3>
      <span class="badge-blue"><?php echo e($upcomingSchedules->count()); ?> schedule<?php echo e($upcomingSchedules->count() > 1 ? 's' : ''); ?></span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Date</th>
            <th class="th">Exam</th>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Time</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $upcomingSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td">
              <span class="font-semibold text-slate-800"><?php echo e(\Carbon\Carbon::parse($s->date)->format('d M')); ?></span>
              <span class="text-xs text-slate-400 ml-1"><?php echo e(\Carbon\Carbon::parse($s->date)->format('D')); ?></span>
            </td>
            <td class="td text-slate-600"><?php echo e($s->exam_name); ?></td>
            <td class="td"><span class="badge-slate"><?php echo e($s->class); ?></span></td>
            <td class="td font-medium text-slate-800"><?php echo e($s->subject); ?></td>
            <td class="td text-xs text-slate-500"><?php echo e($s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '—'); ?> <?php if($s->end_time): ?> – <?php echo e(\Carbon\Carbon::parse($s->end_time)->format('h:i A')); ?> <?php endif; ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">All Exams — <?php echo e($currentYear?->name); ?></h3>
    <?php if($exams->isEmpty()): ?>
      <div class="text-center py-12 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        No exams created yet. <a href="<?php echo e(route('examinations.create')); ?>" class="text-blue-600 hover:underline">Create the first exam</a>.
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Exam Name</th>
            <th class="th">Type</th>
            <th class="th">Period</th>
            <th class="th">Pass %</th>
            <th class="th">Status</th>
            <th class="th text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $started = \Carbon\Carbon::parse($exam->start_date)->isPast();
            $ended   = \Carbon\Carbon::parse($exam->end_date)->isPast();
            $state   = $exam->result_published ? 'results' : ($ended ? 'ended' : ($started ? 'ongoing' : 'upcoming'));
          ?>
          <tr class="tr">
            <td class="td">
              <p class="font-semibold text-slate-800"><?php echo e($exam->name); ?></p>
              <?php if($exam->term_label): ?>
              <p class="text-xs text-slate-400"><?php echo e($exam->term_label); ?></p>
              <?php endif; ?>
            </td>
            <td class="td">
              <span class="badge-slate capitalize"><?php echo e(str_replace('_',' ',$exam->type)); ?></span>
              <?php if($exam->is_external): ?>
              <span class="badge-amber text-xs ml-1">External</span>
              <?php endif; ?>
            </td>
            <td class="td text-sm">
              <p><?php echo e(\Carbon\Carbon::parse($exam->start_date)->format('d M Y')); ?></p>
              <p class="text-xs text-slate-400">to <?php echo e(\Carbon\Carbon::parse($exam->end_date)->format('d M Y')); ?></p>
            </td>
            <td class="td text-sm font-mono"><?php echo e($exam->passing_percentage ? $exam->passing_percentage.'%' : '—'); ?></td>
            <td class="td">
              <?php if($state === 'results'): ?>
                <span class="badge-green">Results Out</span>
              <?php elseif($state === 'ongoing'): ?>
                <span class="badge-amber">Ongoing</span>
              <?php elseif($state === 'ended'): ?>
                <span class="badge-slate">Ended</span>
              <?php elseif($state === 'upcoming'): ?>
                <span class="badge-blue">Upcoming</span>
              <?php elseif($exam->is_published): ?>
                <span class="badge-slate">Published</span>
              <?php else: ?>
                <span class="badge-amber text-xs font-semibold">Draft</span>
              <?php endif; ?>
            </td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-1">
                <a href="<?php echo e(route('examinations.marks', $exam->id)); ?>" class="btn-icon" title="Enter Marks">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <a href="<?php echo e(route('examinations.results', $exam->id)); ?>" class="btn-icon" title="Results">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <a href="<?php echo e(route('examinations.hall-tickets', $exam->id)); ?>" class="btn-icon text-amber-500 hover:text-amber-700 hover:bg-amber-50" title="Hall Tickets">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </a>
                <a href="<?php echo e(route('examinations.edit', $exam->id)); ?>" class="btn-icon" title="Edit Exam">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form method="POST" action="<?php echo e(route('examinations.destroy', $exam->id)); ?>" onsubmit="return confirm('Delete exam <?php echo e(addslashes($exam->name)); ?>?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn-icon text-red-400 hover:text-red-600 hover:bg-red-50" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\index.blade.php ENDPATH**/ ?>