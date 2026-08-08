<?php $__env->startSection('title', 'Fuel Expense Summary'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Fuel Expense Summary</h1>
      <p class="page-subtitle">Monthly fuel consumption by vehicle</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('transport.fuel-summary.pdf', ['month'=>$month])); ?>" target="_blank" class="btn btn-secondary btn-sm">Export PDF</a>
      <a href="<?php echo e(route('transport.fuel-summary.excel', ['month'=>$month])); ?>" class="btn btn-secondary btn-sm">Export Excel</a>
      <a href="<?php echo e(route('transport.fuel')); ?>" class="btn btn-secondary btn-sm">← Fuel Log</a>
    </div>
  </div>

  <div class="card-flat py-4">
    <form method="GET" class="flex gap-3 items-end">
      <div>
        <label class="label">Month</label>
        <input type="month" name="month" value="<?php echo e($month); ?>" class="input" onchange="this.form.submit()">
      </div>
    </form>
  </div>

  <?php [$yr,$mo] = explode('-',$month); $monthLabel = \Carbon\Carbon::createFromDate($yr,$mo,1)->format('F Y'); ?>

  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-slate-800"><?php echo e(number_format($grandTotals['litres'],1)); ?> L</p>
      <p class="text-sm text-slate-500 mt-1">Total Fuel — <?php echo e($monthLabel); ?></p>
    </div>
    <div class="card text-center py-5">
      <p class="text-2xl font-bold text-slate-800">₹<?php echo e(number_format($grandTotals['amount'],0)); ?></p>
      <p class="text-sm text-slate-500 mt-1">Total Cost — <?php echo e($monthLabel); ?></p>
    </div>
  </div>

  <?php if($fuelByVehicle->isEmpty()): ?>
    <div class="card text-center py-12 text-slate-400">No fuel logs for <?php echo e($monthLabel); ?>.</div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Vehicle</th>
        <th class="th text-center">Fill Ups</th>
        <th class="th text-center">Litres</th>
        <th class="th text-center">Amount (₹)</th>
        <th class="th text-center">KM Covered</th>
        <th class="th text-center">Efficiency (km/L)</th>
        <th class="th text-center">Cost/KM (₹)</th>
      </tr></thead>
      <tbody>
        <?php $__currentLoopData = $fuelByVehicle->sortByDesc('total_amount'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr">
          <td class="td font-medium">
            <?php echo e($row->vehicle?->vehicle_number ?? ('Vehicle #'.$row->vehicle_id)); ?>

            <?php if($row->vehicle?->vehicle_type): ?>
              <span class="badge-slate ml-1 text-xs capitalize"><?php echo e($row->vehicle->vehicle_type); ?></span>
            <?php endif; ?>
          </td>
          <td class="td text-center"><?php echo e($row->fill_count); ?></td>
          <td class="td text-center font-medium"><?php echo e(number_format($row->total_litres, 1)); ?></td>
          <td class="td text-center font-semibold text-slate-800"><?php echo e(number_format($row->total_amount, 0)); ?></td>
          <td class="td text-center"><?php echo e($row->km_covered ? number_format($row->km_covered,0) : '—'); ?></td>
          <td class="td text-center">
            <?php if($row->total_litres > 0 && $row->km_covered): ?>
              <?php $efficiency = round($row->km_covered/$row->total_litres,2); ?>
              <span class="<?php echo e($efficiency >= 10 ? 'badge-green' : ($efficiency >= 7 ? 'badge-yellow' : 'badge-red')); ?>"><?php echo e($efficiency); ?> km/L</span>
            <?php else: ?>
              <span class="text-slate-400">—</span>
            <?php endif; ?>
          </td>
          <td class="td text-center">
            <?php if($row->km_covered > 0 && $row->total_amount > 0): ?>
              <?php $costPerKm = round($row->total_amount / $row->km_covered, 2); ?>
              <span class="font-medium text-slate-700">₹<?php echo e(number_format($costPerKm, 2)); ?></span>
            <?php else: ?>
              <span class="text-slate-400">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr class="tr bg-slate-50 font-semibold">
          <td class="td">Total</td>
          <td class="td text-center"><?php echo e($fuelByVehicle->sum('fill_count')); ?></td>
          <td class="td text-center"><?php echo e(number_format($grandTotals['litres'],1)); ?></td>
          <td class="td text-center"><?php echo e(number_format($grandTotals['amount'],0)); ?></td>
          <td class="td text-center">—</td>
          <td class="td text-center">—</td>
          <td class="td text-center">—</td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\fuel-summary.blade.php ENDPATH**/ ?>