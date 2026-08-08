<?php $__env->startSection('title', 'Event Calendar'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Event Calendar</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('events.index')); ?>" class="btn-sm btn-secondary">List View</a>
      <a href="<?php echo e(route('events.create')); ?>" class="btn-primary btn-sm">+ New Event</a>
    </div>
  </div>

  
  <?php
    $prevMonth = \Carbon\Carbon::createFromDate($year,$month,1)->subMonth();
    $nextMonth = \Carbon\Carbon::createFromDate($year,$month,1)->addMonth();
    $monthName = \Carbon\Carbon::createFromDate($year,$month,1)->format('F Y');
    $typeColors = [
      'academic'=>'bg-indigo-100 text-indigo-800','cultural'=>'bg-pink-100 text-pink-800',
      'sports'=>'bg-emerald-100 text-emerald-800','holiday'=>'bg-amber-100 text-amber-800',
      'meeting'=>'bg-blue-100 text-blue-800','other'=>'bg-slate-100 text-slate-600',
    ];
  ?>

  <div class="card">
    <div class="flex items-center justify-between mb-6">
      <a href="<?php echo e(route('events.calendar', ['month'=>$prevMonth->month,'year'=>$prevMonth->year])); ?>" class="btn-sm btn-secondary">← <?php echo e($prevMonth->format('M')); ?></a>
      <div class="flex items-center gap-3">
        <h2 class="text-xl font-bold text-slate-800"><?php echo e($monthName); ?></h2>
        <?php if($month != now()->month || $year != now()->year): ?>
        <a href="<?php echo e(route('events.calendar', ['month'=>now()->month,'year'=>now()->year])); ?>" class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full font-semibold hover:bg-indigo-100 transition">Today</a>
        <?php endif; ?>
      </div>
      <a href="<?php echo e(route('events.calendar', ['month'=>$nextMonth->month,'year'=>$nextMonth->year])); ?>" class="btn-sm btn-secondary"><?php echo e($nextMonth->format('M')); ?> →</a>
    </div>

    
    <div class="grid grid-cols-7 mb-2">
      <?php $__currentLoopData = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="text-center text-xs font-semibold text-slate-500 py-2"><?php echo e($d); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-7 gap-1">
      
      <?php for($i = 0; $i < $startDow; $i++): ?>
      <div class="min-h-24 rounded-lg bg-slate-50 p-1"></div>
      <?php endfor; ?>

      
      <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $isToday = (now()->day === $day && now()->month === $month && now()->year === $year);
        $dayEvents = $events->get($day, collect());
      ?>
      <div class="min-h-24 rounded-lg border border-slate-100 p-1.5 <?php echo e($isToday ? 'bg-indigo-50 border-indigo-300' : 'bg-white hover:bg-slate-50'); ?>">
        <div class="text-xs font-semibold mb-1 <?php echo e($isToday ? 'text-indigo-600' : 'text-slate-400'); ?>"><?php echo e($day); ?></div>
        <?php $__currentLoopData = $dayEvents->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('events.show', $ev->id)); ?>"
           class="block text-xs px-1 py-0.5 rounded mb-0.5 truncate <?php echo e($typeColors[$ev->event_type] ?? 'bg-slate-100 text-slate-600'); ?>">
          <?php echo e($ev->name); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($dayEvents->count() > 3): ?>
        <span class="text-xs text-slate-400">+<?php echo e($dayEvents->count() - 3); ?> more</span>
        <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100">
      <?php $__currentLoopData = ['academic','cultural','sports','holiday','meeting','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <span class="flex items-center gap-1 text-xs">
        <span class="w-3 h-3 rounded-sm <?php echo e($typeColors[$t]); ?>"></span>
        <?php echo e(ucfirst($t)); ?>

      </span>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\events\calendar.blade.php ENDPATH**/ ?>