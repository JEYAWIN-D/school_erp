<?php $__env->startSection('title', 'Homework Calendar'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Homework Calendar — <?php echo e($start->format('F Y')); ?></h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('academics.homework')); ?>" class="btn-sm btn-secondary">List View</a>
    </div>
  </div>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <select name="month" class="select w-36">
          <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($m); ?>" <?php echo e($m == $month ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Year</label>
        <select name="year" class="select w-24">
          <?php $__currentLoopData = range(now()->year-1, now()->year+1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($y); ?>" <?php echo e($y == $year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-40">
          <option value="">All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cls->id); ?>" <?php echo e($classId == $cls->id ? 'selected' : ''); ?>><?php echo e($cls->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn-sm btn-primary">Apply</button>
      
      <?php
        $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
        $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
      ?>
      <a href="<?php echo e(request()->fullUrlWithQuery(['month' => $prevMonth->month, 'year' => $prevMonth->year])); ?>"
        class="btn-sm btn-secondary">← Prev</a>
      <a href="<?php echo e(request()->fullUrlWithQuery(['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>"
        class="btn-sm btn-secondary">Next →</a>
    </div>
  </form>

  
  <div class="card overflow-hidden">
    
    <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
      <?php $__currentLoopData = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-2 py-2 text-center text-xs font-semibold text-slate-500 uppercase"><?php echo e($day); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php
      $firstDayOfWeek = $start->dayOfWeek; // 0=Sun, 6=Sat
      $totalCells = $firstDayOfWeek + count($calendar);
      $rows = ceil($totalCells / 7);
      $calKeys = array_keys($calendar);
      $cellIdx = 0;
    ?>

    <?php for($row = 0; $row < $rows; $row++): ?>
      <div class="grid grid-cols-7 divide-x divide-slate-100 border-b border-slate-100">
        <?php for($col = 0; $col < 7; $col++): ?>
          <?php
            $cellNumber = $row * 7 + $col;
            $dayOffset  = $cellNumber - $firstDayOfWeek;
          ?>
          <?php if($dayOffset < 0 || $dayOffset >= count($calKeys)): ?>
            <div class="bg-slate-50 min-h-[80px] p-2"></div>
          <?php else: ?>
            <?php
              $dateKey = $calKeys[$dayOffset];
              $cell    = $calendar[$dateKey];
              $isToday = $cell['date']->isToday();
              $hw      = $cell['homework'];
            ?>
            <div class="min-h-[80px] p-2 <?php echo e($isToday ? 'bg-blue-50' : ''); ?> hover:bg-slate-50 transition-colors">
              <div class="text-right mb-1">
                <span class="text-xs font-semibold <?php echo e($isToday ? 'bg-blue-600 text-white rounded-full px-1.5 py-0.5' : 'text-slate-400'); ?>">
                  <?php echo e($cell['date']->day); ?>

                </span>
              </div>
              <div class="space-y-1">
                <?php $__currentLoopData = $hw->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-xs rounded px-1.5 py-0.5 truncate
                  <?php if($h->subject?->name): ?>
                    <?php echo e(['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-purple-100 text-purple-800', 'bg-orange-100 text-orange-800', 'bg-pink-100 text-pink-800'][crc32($h->subject->name) % 5]); ?>

                  <?php else: ?>
                    bg-slate-100 text-slate-700
                  <?php endif; ?>
                  " title="<?php echo e($h->title); ?> — <?php echo e($h->class?->name); ?>">
                  <?php echo e($h->subject?->short_name ?? $h->subject?->name ?? '—'); ?>: <?php echo e(\Str::limit($h->title, 20)); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($hw->count() > 3): ?>
                  <div class="text-xs text-slate-400 text-right">+<?php echo e($hw->count() - 3); ?> more</div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endfor; ?>
  </div>

  
  <div class="card">
    <h2 class="text-sm font-semibold text-slate-700 mb-3">All Homework This Month</h2>
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Due Date</th>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Title</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = collect($calendar)->pluck('homework')->flatten()->sortBy('due_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr <?php echo e($h->due_date->isPast() ? 'opacity-60' : ''); ?>">
            <td class="td font-mono text-xs"><?php echo e($h->due_date->format('d M Y')); ?>

              <?php if($h->due_date->isToday()): ?> <span class="badge-blue ml-1">Today</span> <?php endif; ?>
            </td>
            <td class="td"><?php echo e($h->class?->name); ?></td>
            <td class="td"><?php echo e($h->subject?->name); ?></td>
            <td class="td"><?php echo e($h->title); ?></td>
            <td class="td">
              <a href="<?php echo e(route('academics.homework.submissions', $h->id)); ?>" class="btn-xs btn-secondary">Submissions</a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="td text-center text-slate-400">No homework due this month.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\homework-calendar.blade.php ENDPATH**/ ?>