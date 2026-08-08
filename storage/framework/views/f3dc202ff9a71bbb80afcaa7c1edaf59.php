<?php $__env->startSection('title', 'General Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">General Register</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('dashboard')); ?>" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  
  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id')==$cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Gender</label>
      <select name="gender" class="select">
        <option value="">All</option>
        <option value="male" <?php if(request('gender')=='male'): echo 'selected'; endif; ?>>Male</option>
        <option value="female" <?php if(request('gender')=='female'): echo 'selected'; endif; ?>>Female</option>
        <option value="other" <?php if(request('gender')=='other'): echo 'selected'; endif; ?>>Other</option>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="<?php echo e(route('reports.general-register')); ?>" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">Total: <strong><?php echo e($students->total()); ?></strong> students</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Adm. No.</th>
          <th class="th">Name</th>
          <th class="th">Class</th>
          <th class="th">DOB</th>
          <th class="th">Gender</th>
          <th class="th">Blood</th>
          <th class="th">Father</th>
          <th class="th">Phone</th>
          <th class="th">Religion</th>
          <th class="th">Admission Date</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td text-slate-400"><?php echo e($students->firstItem() + $i); ?></td>
          <td class="td font-mono text-xs"><?php echo e($s->admission_number); ?></td>
          <td class="td font-medium"><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?></td>
          <td class="td"><?php echo e($s->class_name ?? '—'); ?></td>
          <td class="td"><?php echo e($s->dob ? \Carbon\Carbon::parse($s->dob)->format('d/m/Y') : '—'); ?></td>
          <td class="td capitalize"><?php echo e($s->gender); ?></td>
          <td class="td"><?php echo e($s->blood_group ?? '—'); ?></td>
          <td class="td"><?php echo e($s->father_name ?? '—'); ?></td>
          <td class="td"><?php echo e($s->phone ?? '—'); ?></td>
          <td class="td"><?php echo e($s->religion ?? '—'); ?></td>
          <td class="td"><?php echo e($s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->format('d/m/Y') : '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-slate-400 text-center" colspan="11">No students found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4"><?php echo e($students->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\reports\general-register.blade.php ENDPATH**/ ?>