<?php $__env->startSection('title','Self Appraisal'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <h1 class="page-title">Employee Self-Appraisal</h1>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Employee</label>
        <select name="employee_id" class="select w-52">
          <option value="">Select Employee</option>
          <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($e->id); ?>" <?php if(request('employee_id') == $e->id): echo 'selected'; endif; ?>>
            <?php echo e($e->full_name); ?> (<?php echo e($e->employee_number); ?>)
          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label text-xs">Appraisal Year</label>
        <select name="year" class="select w-28">
          <?php for($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
          <option value="<?php echo e($y); ?>" <?php if(request('year', date('Y')) == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Load</button>
    </form>
  </div>

  <?php if(request('employee_id')): ?>
  <?php $selectedEmp = $employees->firstWhere('id', request('employee_id')); ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-1">
      Self-Appraisal: <?php echo e($selectedEmp?->full_name); ?>

      <span class="text-slate-400 font-normal text-sm">— <?php echo e(request('year')); ?></span>
    </h3>
    <?php if($appraisal?->self_submitted_at): ?>
    <p class="text-xs text-green-600 mb-4">
      ✓ Submitted on <?php echo e($appraisal->self_submitted_at->format('d M Y H:i')); ?>

      · Self-score: <strong><?php echo e($appraisal->self_score); ?>/5</strong>
    </p>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('hr.self-appraisal.save')); ?>" class="space-y-5">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="employee_id" value="<?php echo e(request('employee_id')); ?>">
      <input type="hidden" name="appraisal_year" value="<?php echo e(request('year')); ?>">

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200">
              <th class="text-left py-2 text-slate-500 font-medium text-xs uppercase tracking-wide w-1/2">KPI / Competency</th>
              <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <th class="text-center py-2 text-slate-500 font-medium text-xs w-12"><?php echo e($star); ?></th>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <th class="text-center py-2 text-slate-500 font-medium text-xs">Score</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" x-data>
            <?php $__currentLoopData = $kpiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $key = Str::slug($kpi); $prev = $appraisal?->self_ratings[$kpi] ?? null; ?>
            <tr x-data="{ val: <?php echo e($prev ?? 0); ?> }">
              <td class="py-3 font-medium text-slate-700"><?php echo e($kpi); ?></td>
              <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <td class="py-3 text-center">
                <input type="radio" name="self_ratings[<?php echo e($kpi); ?>]" value="<?php echo e($star); ?>"
                  <?php if($prev == $star): ?> checked <?php endif; ?>
                  x-model="val" class="accent-amber-400 w-4 h-4 cursor-pointer">
              </td>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <td class="py-3 text-center">
                <span x-text="val > 0 ? val + '/5' : '—'" class="text-xs font-semibold text-slate-600"></span>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <p class="text-xs text-slate-400">Rating: 1 = Needs Improvement · 2 = Below Average · 3 = Meets Expectations · 4 = Exceeds Expectations · 5 = Outstanding</p>

      <div>
        <label class="label">Self-Remarks / Achievements</label>
        <textarea name="self_remarks" rows="4" class="input" placeholder="Describe your key achievements, challenges, and development goals for this year..."><?php echo e($appraisal?->self_remarks); ?></textarea>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">Submit Self-Appraisal</button>
      </div>
    </form>
  </div>

  <?php if($appraisal && $appraisal->overall_score): ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3">HOD / Management Appraisal</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center mb-4">
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-2xl font-bold text-indigo-600"><?php echo e($appraisal->self_score ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-1">Self Score</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-2xl font-bold text-blue-600"><?php echo e($appraisal->overall_score); ?></p>
        <p class="text-xs text-slate-400 mt-1">HOD Score</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <p class="text-lg font-bold text-slate-800"><?php echo e($appraisal->rating_label); ?></p>
        <p class="text-xs text-slate-400 mt-1">Rating</p>
      </div>
      <div class="bg-slate-50 rounded-lg p-3">
        <?php if($appraisal->increment_amount): ?>
        <p class="text-xl font-bold text-green-600">₹<?php echo e(number_format($appraisal->increment_amount, 0)); ?></p>
        <p class="text-xs text-slate-400 mt-1">Increment (<?php echo e($appraisal->increment_percent); ?>%)</p>
        <?php else: ?>
        <p class="text-slate-400 text-sm">No increment</p>
        <p class="text-xs text-slate-300">processed yet</p>
        <?php endif; ?>
      </div>
    </div>

    <?php if(!$appraisal->increment_amount): ?>
    <?php $currentGross = $appraisal->employee?->gross_salary ?? 0; ?>
    <form method="POST" action="<?php echo e(route('hr.appraisal.increment', $appraisal->id)); ?>"
      class="border-t border-slate-100 pt-4 space-y-3"
      x-data="{ type: 'percent', value: '', currentGross: <?php echo e($currentGross); ?>,
        get revisedCTC() {
          const v = parseFloat(this.value) || 0;
          return this.type === 'percent'
            ? this.currentGross + (this.currentGross * v / 100)
            : this.currentGross + v;
        }
      }">
      <?php echo csrf_field(); ?>
      <h4 class="font-medium text-slate-700 text-sm">Process Increment</h4>
      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="label text-xs">Increment Type</label>
          <select name="increment_type" class="select" x-model="type">
            <option value="percent">Percentage (%)</option>
            <option value="amount">Fixed Amount (₹)</option>
          </select>
        </div>
        <div>
          <label class="label text-xs" x-text="type === 'percent' ? 'Increment %' : 'Amount (₹)'"></label>
          <input type="number" name="increment_value" class="input" min="0" step="0.01" required
            x-model="value" :placeholder="type === 'percent' ? 'e.g. 10' : 'e.g. 5000'">
        </div>
        <div>
          <label class="label text-xs">Effective From</label>
          <input type="date" name="increment_effective_date" class="input" required value="<?php echo e(date('Y-m-01')); ?>">
        </div>
      </div>
      
      <div class="bg-slate-50 rounded-lg px-4 py-3 flex items-center gap-6 text-sm">
        <div>
          <span class="text-xs text-slate-500 block">Current Gross (CTC)</span>
          <span class="font-semibold text-slate-700">₹<?php echo e(number_format($currentGross, 2)); ?></span>
        </div>
        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        <div>
          <span class="text-xs text-slate-500 block">Revised CTC</span>
          <span class="font-bold text-green-600" x-text="'₹' + revisedCTC.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})">
            ₹<?php echo e(number_format($currentGross, 2)); ?>

          </span>
        </div>
        <div class="text-xs text-slate-400 ml-auto">
          Rating: <strong class="text-slate-600"><?php echo e($appraisal->rating_label); ?></strong>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-sm"
        onclick="return confirm('Apply increment and update employee salary?')">
        Approve & Apply Increment
      </button>
    </form>
    <?php else: ?>
    <?php
      $revisedCTC = ($appraisal->employee?->gross_salary ?? 0);
      $prevCTC    = $revisedCTC - ($appraisal->increment_amount ?? 0);
    ?>
    <div class="border-t border-slate-100 pt-3 space-y-2">
      <p class="text-xs text-green-600 font-medium">
        ✓ Increment of ₹<?php echo e(number_format($appraisal->increment_amount, 0)); ?>

        (<?php echo e(number_format($appraisal->increment_percent, 1)); ?>%) approved
        on <?php echo e($appraisal->increment_approved_at?->format('d M Y')); ?>,
        effective <?php echo e($appraisal->increment_effective_date?->format('d M Y')); ?>.
      </p>
      <div class="bg-green-50 rounded-lg px-4 py-2 flex items-center gap-6 text-sm">
        <div>
          <span class="text-xs text-slate-500 block">Previous Gross (CTC)</span>
          <span class="font-semibold text-slate-600">₹<?php echo e(number_format($prevCTC, 2)); ?></span>
        </div>
        <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        <div>
          <span class="text-xs text-slate-500 block">Revised CTC</span>
          <span class="font-bold text-green-700 text-base">₹<?php echo e(number_format($revisedCTC, 2)); ?></span>
        </div>
        <div class="text-xs text-green-600 font-medium ml-auto">
          +₹<?php echo e(number_format($appraisal->increment_amount, 0)); ?> / month
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hr\self-appraisal.blade.php ENDPATH**/ ?>