<?php $__env->startSection('title','Report Cards — ' . $exam->name); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="<?php echo e(route('examinations.index')); ?>" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <h1 class="page-title"><?php echo e($exam->name); ?> — Report Cards</h1>
    </div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">Select Class</option>
          <?php $__currentLoopData = \App\Models\Classes::active()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php echo e(request('class_id') == $cls->id ? 'selected' : ''); ?>><?php echo e($cls->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
      <?php if(request('class_id')): ?>
      <a href="<?php echo e(route('examinations.report-cards.bulk-pdf', [$exam->id, 'class_id' => request('class_id'), 'section_id' => request('section_id')])); ?>" class="btn btn-secondary">
        Download All PDFs
      </a>
      <?php endif; ?>
    </form>
  </div>

  
  <?php $principalRemarks = \App\Models\SchoolSetting::get('principal_remarks_exam_' . $exam->id, \App\Models\SchoolSetting::get('principal_remarks', '')); ?>
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-3">Principal's Remarks <span class="text-xs font-normal text-slate-400">(printed on every report card for this exam)</span></h3>
    <form method="POST" action="<?php echo e(route('examinations.principal-remarks', $exam->id)); ?>" class="flex gap-3 items-end">
      <?php echo csrf_field(); ?>
      <div class="flex-1">
        <textarea name="principal_remarks" rows="2" class="input w-full" placeholder="e.g. Congratulations to all students for their hard work and dedication this term."><?php echo e($principalRemarks); ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Save Remarks</button>
    </form>
  </div>

  <?php if(isset($enrollments) && $enrollments->count()): ?>
  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Roll</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr class="tr">
            <td class="td font-medium"><?php echo e($enrollment->student->first_name); ?> <?php echo e($enrollment->student->last_name); ?></td>
            <td class="td text-slate-500"><?php echo e($enrollment->student->admission_no ?? $enrollment->student->admission_number ?? '—'); ?></td>
            <td class="td"><?php echo e($enrollment->roll_number); ?></td>
            <td class="td">
              <a href="<?php echo e(route('examinations.report-card.pdf', [$enrollment->id, 'exam_id' => $exam->id])); ?>" class="btn btn-secondary btn-sm" target="_blank">
                PDF
              </a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select a class to view report cards.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\report-cards.blade.php ENDPATH**/ ?>