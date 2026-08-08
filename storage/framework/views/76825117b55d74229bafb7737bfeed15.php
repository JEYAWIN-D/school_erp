
<?php $__env->startSection('title','Admission Analytics'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="page-title">Admission Analytics</h1>
      <p class="page-subtitle"><?php echo e($currentYear?->name ?? 'Current Year'); ?> — Enquiry & conversion insights</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('admissions.analytics.pdf')); ?>" target="_blank" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Export PDF
      </a>
      <a href="<?php echo e(route('admissions.analytics.excel')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
      </a>
      <a href="<?php echo e(route('admissions.index')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Enquiries
      </a>
    </div>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <?php $__currentLoopData = [
      ['label' => 'Total Enquiries', 'value' => $byStatus->sum(),           'color' => 'text-indigo-600'],
      ['label' => 'Converted',       'value' => $byStatus['converted'] ?? 0, 'color' => 'text-green-600'],
      ['label' => 'Follow Up',       'value' => $byStatus['follow_up'] ?? 0, 'color' => 'text-amber-600'],
      ['label' => 'Lost',            'value' => $byStatus['lost'] ?? 0,      'color' => 'text-red-600'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card text-center py-5">
      <p class="text-3xl font-bold <?php echo e($s['color']); ?>" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($s['value']); ?></p>
      <p class="text-sm text-slate-500 mt-1"><?php echo e($s['label']); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <?php if($yoyData): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Year-over-Year Comparison</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <?php $__currentLoopData = [$yoyData['current'], $yoyData['prev']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
        <p class="text-sm font-semibold text-slate-500 mb-3"><?php echo e($yr['year']); ?></p>
        <div class="flex gap-6">
          <div>
            <p class="text-2xl font-bold text-indigo-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($yr['enquiries']); ?></p>
            <p class="text-xs text-slate-400">Enquiries</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-green-600" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e($yr['converted']); ?></p>
            <p class="text-xs text-slate-400">Converted</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-slate-600" style="font-family:'Plus Jakarta Sans',sans-serif;">
              <?php echo e($yr['enquiries'] > 0 ? round(($yr['converted'] / $yr['enquiries']) * 100) : 0); ?>%
            </p>
            <p class="text-xs text-slate-400">Conversion Rate</p>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Source-wise Breakdown</h3>
      <canvas id="sourceChart" height="220"></canvas>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Monthly Enquiry Trend</h3>
      <canvas id="trendChart" height="220"></canvas>
    </div>
  </div>

  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Status Breakdown</h3>
    <div class="space-y-3">
      <?php
        $total = $byStatus->sum() ?: 1;
        $statusColors = ['new'=>'bg-blue-500','follow_up'=>'bg-amber-500','converted'=>'bg-green-500','lost'=>'bg-red-500','application'=>'bg-purple-500','confirmed'=>'bg-cyan-500','enrolled'=>'bg-teal-500'];
        $statusLabels = ['new'=>'New','follow_up'=>'Follow Up','converted'=>'Converted','lost'=>'Lost','application'=>'Application','confirmed'=>'Confirmed','enrolled'=>'Enrolled'];
      ?>
      <?php $__currentLoopData = $byStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="flex items-center gap-3">
        <span class="w-28 text-xs text-slate-600 text-right"><?php echo e($statusLabels[$status] ?? ucfirst($status)); ?></span>
        <div class="flex-1 bg-slate-100 rounded-full h-2">
          <div class="<?php echo e($statusColors[$status] ?? 'bg-slate-400'); ?> h-2 rounded-full" style="width: <?php echo e(round(($count / $total) * 100)); ?>%"></div>
        </div>
        <span class="w-10 text-xs font-semibold text-slate-700 text-right"><?php echo e($count); ?></span>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card overflow-hidden">
      <h3 class="font-semibold text-slate-700 px-4 pt-4 pb-3">Class-wise Enquiry Count</h3>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="th">Class</th>
            <th class="th text-right">Enquiries</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php $__currentLoopData = $byClass->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td"><?php echo e($row->class?->name ?? 'Unknown'); ?></td>
            <td class="td text-right font-semibold text-indigo-600"><?php echo e($row->total); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>

    
    <?php if($byCounsellor->isNotEmpty()): ?>
    <div class="card overflow-hidden">
      <h3 class="font-semibold text-slate-700 px-4 pt-4 pb-3">Counsellor-wise Conversion</h3>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="th">Counsellor</th>
            <th class="th text-right">Enquiries</th>
            <th class="th text-right">Converted</th>
            <th class="th text-right">Rate</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php $__currentLoopData = $byCounsellor->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $rate = $row->total > 0 ? round(($row->converted / $row->total) * 100) : 0; ?>
          <tr class="tr">
            <td class="td"><?php echo e($row->assignedTo?->name ?? 'Unassigned'); ?></td>
            <td class="td text-right"><?php echo e($row->total); ?></td>
            <td class="td text-right text-green-600 font-semibold"><?php echo e($row->converted); ?></td>
            <td class="td text-right">
              <span class="px-2 py-0.5 rounded text-xs <?php echo e($rate >= 50 ? 'bg-green-100 text-green-700' : ($rate >= 25 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')); ?>">
                <?php echo e($rate); ?>%
              </span>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>
<?php $__env->startPush('scripts'); ?>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const sourceRaw = <?php echo json_encode($bySource, 15, 512) ?>;
const trendRaw  = <?php echo json_encode($monthlyTrend, 15, 512) ?>;

const sourceLabels = Object.keys(sourceRaw).map(k => k ? k.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Unknown');
new Chart(document.getElementById('sourceChart'), {
  type: 'doughnut',
  data: { labels: sourceLabels, datasets: [{ data: Object.values(sourceRaw), backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#06b6d4'] }] },
  options: { plugins: { legend: { position: 'bottom' } }, cutout: '60%' }
});

const trendValues = Array.from({length: 12}, (_, i) => trendRaw[i + 1] ?? 0);
new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: { labels: months, datasets: [{ label: 'Enquiries', data: trendValues, backgroundColor: '#6366f1', borderRadius: 4 }] },
  options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\analytics.blade.php ENDPATH**/ ?>