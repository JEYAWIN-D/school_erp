<?php $__env->startSection('title', 'Monthly Collection Report'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Monthly Collection Report</h1>
      <p class="page-subtitle">Month-wise fee collection for <?php echo e($year); ?></p>
    </div>
    <a href="<?php echo e(route('fees.index')); ?>" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Year</label>
        <select name="year" class="select">
          <?php for($y = now()->year; $y >= now()->year - 4; $y--): ?>
          <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Load</button>
    </form>
  </div>

  <?php
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $maxVal = max($data) ?: 1;
  ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">Monthly Breakdown</h2>
      <div class="table-wrap">
        <table class="w-full">
          <thead>
            <tr>
              <th class="th">Month</th>
              <th class="th text-right">Collection</th>
              <th class="th">Bar</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $val = $data[$i+1] ?? 0; ?>
            <tr class="tr">
              <td class="td font-medium"><?php echo e($month); ?> <?php echo e($year); ?></td>
              <td class="td text-right font-semibold">₹<?php echo e(number_format($val, 2)); ?></td>
              <td class="td w-40">
                <div class="bg-slate-200 rounded-full h-2">
                  <div class="h-2 rounded-full bg-indigo-500" style="width:<?php echo e($maxVal > 0 ? round($val/$maxVal*100) : 0); ?>%"></div>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr font-bold">
              <td class="td">Total</td>
              <td class="td text-right text-indigo-700">₹<?php echo e(number_format($total, 2)); ?></td>
              <td class="td"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">Collection Chart</h2>
      <canvas id="monthlyChart" height="300"></canvas>
    </div>
  </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($months, 15, 512) ?>,
    datasets: [{
      label: 'Collection (₹)',
      data: <?php echo json_encode(array_values($data), 15, 512) ?>,
      backgroundColor: 'rgba(99,102,241,0.7)',
      borderRadius: 4,
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\monthly-collection.blade.php ENDPATH**/ ?>