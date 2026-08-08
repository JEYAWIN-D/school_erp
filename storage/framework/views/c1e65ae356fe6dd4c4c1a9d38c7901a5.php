<?php $__env->startSection('title', 'Annual Appraisal'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Annual Staff Appraisal</h1>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-1">
      <form method="POST" action="<?php echo e(route('hr.appraisal.save')); ?>" class="card space-y-4">
        <?php echo csrf_field(); ?>
        <h2 class="font-semibold text-slate-800">New Appraisal Entry</h2>
        <div>
          <label class="label">Employee *</label>
          <select name="employee_id" required class="select w-full">
            <option value="">Select Employee</option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($e->id); ?>"><?php echo e($e->full_name); ?> (<?php echo e($e->employee_code); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Appraisal Year *</label>
          <input type="number" name="appraisal_year" value="<?php echo e(date('Y')); ?>" min="2020" max="2099" required class="input w-full">
        </div>
        <div>
          <label class="label mb-2 block">KPI Ratings (1–5)</label>
          <div class="space-y-2">
            <?php $__currentLoopData = $kpiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between">
              <label class="text-sm text-slate-700 flex-1"><?php echo e($kpi); ?></label>
              <select name="ratings[<?php echo e($kpi); ?>]" class="select w-20 text-sm">
                <?php for($i=1; $i<=5; $i++): ?>
                  <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
        <div>
          <label class="label">HOD Remarks</label>
          <textarea name="hod_remarks" rows="2" class="input w-full" placeholder="Optional..."></textarea>
        </div>
        <div>
          <label class="label">Principal Remarks</label>
          <textarea name="principal_remarks" rows="2" class="input w-full" placeholder="Optional..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full">Save Appraisal</button>
      </form>

      <form method="GET" class="card mt-4">
        <label class="label">View Appraisals for Year</label>
        <div class="flex gap-2">
          <input type="number" name="year" value="<?php echo e(request('year', date('Y'))); ?>" class="input flex-1" placeholder="<?php echo e(date('Y')); ?>">
          <button type="submit" class="btn btn-secondary btn-sm">Load</button>
        </div>
      </form>
    </div>

    
    <div class="lg:col-span-2">
      <?php if($appraisals->count()): ?>
      <div class="card overflow-hidden">
        <h2 class="font-semibold text-slate-800 px-4 pt-4 pb-2">Appraisals — <?php echo e(request('year', date('Y'))); ?></h2>
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100"><tr>
            <th class="th">Employee</th>
            <th class="th text-center">Score</th>
            <th class="th">Rating</th>
            <th class="th">Appraised By</th>
            <th class="th">Date</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php $__currentLoopData = $appraisals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $badgeClass = match($a->rating_label) {
                'Outstanding' => 'badge-green',
                'Very Good'   => 'badge-blue',
                'Good'        => 'badge-purple',
                'Average'     => 'badge-amber',
                default       => 'badge-red',
              };
            ?>
            <tr class="hover:bg-slate-50" x-data="{open:false}">
              <td class="td">
                <div class="font-medium text-slate-800"><?php echo e($a->employee?->full_name); ?></div>
                <div class="text-xs text-slate-400"><?php echo e($a->employee?->employee_code); ?></div>
              </td>
              <td class="td text-center font-bold text-slate-700"><?php echo e($a->overall_score); ?>/5</td>
              <td class="td"><span class="<?php echo e($badgeClass); ?>"><?php echo e($a->rating_label); ?></span></td>
              <td class="td text-xs text-slate-500"><?php echo e($a->appraisedBy?->name); ?></td>
              <td class="td text-xs text-slate-400"><?php echo e($a->appraised_at?->format('d M Y')); ?></td>
            </tr>
            <?php if($a->hod_remarks || $a->principal_remarks): ?>
            <tr class="bg-slate-50 text-xs text-slate-500">
              <td colspan="5" class="px-4 py-2">
                <?php if($a->hod_remarks): ?><span class="font-medium">HOD: </span><?php echo e($a->hod_remarks); ?>  <?php endif; ?>
                <?php if($a->principal_remarks): ?><span class="font-medium ml-4">Principal: </span><?php echo e($a->principal_remarks); ?><?php endif; ?>
              </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="card text-center py-10 text-slate-400">
        <?php if(request('year')): ?>
          No appraisals found for <?php echo e(request('year')); ?>.
        <?php else: ?>
          Select a year to view appraisals.
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\appraisal.blade.php ENDPATH**/ ?>