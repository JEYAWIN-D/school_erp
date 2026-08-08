<?php $__env->startSection('title', 'Reports & Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">


<div class="flex items-center justify-between flex-wrap gap-3">
  <div>
    <h1 class="text-2xl font-bold text-slate-800">Reports &amp; Analytics</h1>
    <p class="text-sm text-slate-500 mt-0.5"><?php echo e($year?->name ?? 'All Years'); ?> — Last updated <?php echo e(now()->format('d M Y, h:i A')); ?></p>
  </div>
  <div class="flex gap-2 flex-wrap">
    <a href="<?php echo e(route('reports.fee')); ?>" class="btn btn-outline">Fee Report</a>
    <a href="<?php echo e(route('reports.attendance')); ?>" class="btn btn-outline">Attendance Report</a>
    <a href="<?php echo e(route('reports.fee.excel')); ?>" class="btn btn-secondary btn-sm">Fee Excel</a>
    <a href="<?php echo e(route('reports.attendance.excel')); ?>" class="btn btn-secondary btn-sm">Attendance Excel</a>
  </div>
</div>


<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
  <div class="card text-center">
    <div class="text-3xl font-bold text-indigo-600"><?php echo e(number_format($totalStudents)); ?></div>
    <div class="text-sm text-slate-500 mt-1">Active Students</div>
  </div>
  <div class="card text-center">
    <div class="text-3xl font-bold <?php echo e($totalStaff > 0 ? 'text-emerald-600' : 'text-slate-300'); ?>"><?php echo e($totalStaff > 0 ? number_format($totalStaff) : '—'); ?></div>
    <div class="text-sm text-slate-500 mt-1">Active Staff</div>
    <?php if($totalStaff === 0): ?><div class="text-xs text-slate-400">Add staff in HR</div><?php endif; ?>
  </div>
  <div class="card text-center">
    <?php if($todayTotal > 0): ?>
      <div class="text-3xl font-bold <?php echo e($todayPct >= 75 ? 'text-emerald-600' : 'text-rose-600'); ?>"><?php echo e($todayPct); ?>%</div>
      <div class="text-sm text-slate-500 mt-1">Today's Attendance</div>
      <div class="text-xs text-slate-400"><?php echo e($todayPresent); ?>/<?php echo e($todayTotal); ?></div>
    <?php else: ?>
      <div class="text-3xl font-bold text-slate-300">—</div>
      <div class="text-sm text-slate-500 mt-1">Today's Attendance</div>
      <div class="text-xs text-slate-400">Not taken yet</div>
    <?php endif; ?>
  </div>
  <div class="card text-center">
    <div class="text-3xl font-bold text-amber-600"><?php echo e($booksOverdue); ?></div>
    <div class="text-sm text-slate-500 mt-1">Overdue Books</div>
    <div class="text-xs text-slate-400"><?php echo e($booksIssued); ?> total issued</div>
  </div>
</div>


<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Fee Collection — <?php echo e($year?->name ?? 'All Time'); ?></h2>
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div class="bg-emerald-50 rounded-xl p-4 text-center">
        <div class="text-xl font-bold text-emerald-700">₹<?php echo e(number_format($totalCollected, 2)); ?></div>
        <div class="text-xs text-emerald-600 mt-1">Collected</div>
      </div>
      <div class="bg-amber-50 rounded-xl p-4 text-center">
        <div class="text-xl font-bold text-amber-700">₹<?php echo e(number_format(max(0, $totalDemand - $totalCollected), 2)); ?></div>
        <div class="text-xs text-amber-600 mt-1">Pending</div>
      </div>
    </div>
    <?php if($monthlyCollection->count()): ?>
    <canvas id="feeChart" height="120"></canvas>
    <div class="overflow-x-auto mt-4">
      <table class="table text-sm">
        <thead><tr><th>Month</th><th class="text-right">Collected (₹)</th></tr></thead>
        <tbody>
          <?php $__currentLoopData = $monthlyCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y')); ?></td>
            <td class="text-right font-medium"><?php echo e(number_format($m->total, 2)); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p class="text-slate-400 text-sm text-center py-4">No fee data yet.</p>
    <?php endif; ?>
  </div>

  
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4">Enrollment by Class</h2>
    <?php if($classSummary->count()): ?>
    <div class="space-y-2">
      <?php $maxStudents = $classSummary->max('total') ?: 1; ?>
      <?php $__currentLoopData = $classSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="text-slate-700 font-medium"><?php echo e($row->class_name); ?></span>
          <span class="text-slate-500"><?php echo e($row->total); ?></span>
        </div>
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-2 bg-indigo-500 rounded-full" style="width: <?php echo e(round($row->total / $maxStudents * 100)); ?>%"></div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
      <p class="text-slate-400 text-sm text-center py-4">No enrollment data yet.</p>
    <?php endif; ?>
  </div>
</div>


<div class="card">
  <h2 class="font-semibold text-slate-700 mb-4">Monthly Attendance Trend</h2>
  <?php if($attMonthly->count()): ?>
  <div class="overflow-x-auto">
    <table class="table text-sm">
      <thead>
        <tr><th>Month</th><th class="text-right">Present</th><th class="text-right">Absent</th><th class="text-right">Attendance %</th><th>Bar</th></tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $attMonthly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $row['month'])->format('M Y')); ?></td>
          <td class="text-right text-emerald-700 font-medium"><?php echo e($row['present']); ?></td>
          <td class="text-right text-rose-600"><?php echo e($row['absent']); ?></td>
          <td class="text-right font-semibold <?php echo e($row['pct'] >= 75 ? 'text-emerald-700' : 'text-rose-600'); ?>"><?php echo e($row['pct']); ?>%</td>
          <td class="w-32">
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-2 rounded-full <?php echo e($row['pct'] >= 75 ? 'bg-emerald-500' : 'bg-rose-400'); ?>" style="width: <?php echo e($row['pct']); ?>%"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-slate-400 text-sm text-center py-4">No attendance records yet.</p>
  <?php endif; ?>
</div>


<?php if($latestExam): ?>
<div class="card">
  <h2 class="font-semibold text-slate-700 mb-1">Exam Results — <?php echo e($latestExam->name); ?></h2>
  <p class="text-xs text-slate-400 mb-4"><?php echo e($latestExam->start_date?->format('d M Y')); ?> – <?php echo e($latestExam->end_date?->format('d M Y')); ?></p>
  <?php if($examResults->count()): ?>
  <div class="overflow-x-auto">
    <table class="table text-sm">
      <thead><tr><th>Class</th><th class="text-right">Appeared</th><th class="text-right">Avg Marks</th></tr></thead>
      <tbody>
        <?php $__currentLoopData = $examResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="font-medium"><?php echo e($r->class_name); ?></td>
          <td class="text-right"><?php echo e($r->appeared); ?></td>
          <td class="text-right font-semibold"><?php echo e(number_format($r->avg_marks, 1)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-slate-400 text-sm text-center py-4">No marks entered yet for this exam.</p>
  <?php endif; ?>
</div>
<?php endif; ?>

</div>
<?php if($monthlyCollection->count()): ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const labels = <?php echo json_encode($monthlyCollection->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y')), 512) ?>;
  const data   = <?php echo json_encode($monthlyCollection->pluck('total'), 15, 512) ?>;
  new Chart(document.getElementById('feeChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Fee Collected (₹)',
        data,
        backgroundColor: 'rgba(99,102,241,0.7)',
        borderColor: '#6366f1',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString('en-IN') } } }
    }
  });
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\index.blade.php ENDPATH**/ ?>