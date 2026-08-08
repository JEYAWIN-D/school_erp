<?php $__env->startSection('title','Hostel Disciplinary Records'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Disciplinary Records</h1>
      <p class="page-subtitle">Log incidents, issue warnings, and track repeat offences</p>
    </div>
    <button @click="showAdd=!showAdd" class="btn btn-primary btn-sm">+ Log Incident</button>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <div x-show="showAdd" x-transition class="card space-y-4" style="display:none">
    <h3 class="font-semibold text-slate-700 pb-2 border-b">New Disciplinary Record</h3>
    <form method="POST" action="<?php echo e(route('hostel.disciplinary.store')); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select student</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->full_name); ?> (<?php echo e($s->admission_number); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Incident Date <span class="text-red-500">*</span></label>
        <input type="date" name="incident_date" class="input" value="<?php echo e(date('Y-m-d')); ?>" required>
      </div>
      <div class="md:col-span-2">
        <label class="label">Incident Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input h-20" required placeholder="Describe the incident..."></textarea>
      </div>
      <div>
        <label class="label">Action Taken</label>
        <input type="text" name="action_taken" class="input" placeholder="Counselled, suspended from activity, etc.">
      </div>
      <div>
        <label class="label">Severity</label>
        <select name="severity" class="select">
          <option value="minor">Minor</option>
          <option value="moderate">Moderate</option>
          <option value="severe">Severe</option>
        </select>
      </div>
      <div>
        <label class="label">Reported By</label>
        <select name="reported_by" class="select">
          <option value="">Select staff</option>
          <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($e->id); ?>"><?php echo e($e->first_name); ?> <?php echo e($e->last_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="flex items-end gap-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_warning" value="1" class="w-4 h-4 rounded border-slate-300 text-amber-600">
          <span class="text-sm font-medium text-slate-700">Issue Formal Warning</span>
        </label>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Record</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap items-end">
      <select name="student_id" class="select w-48">
        <option value="">All Students</option>
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('student_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->full_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="severity" class="select w-32">
        <option value="">All Severity</option>
        <option value="minor" <?php if(request('severity')==='minor'): echo 'selected'; endif; ?>>Minor</option>
        <option value="moderate" <?php if(request('severity')==='moderate'): echo 'selected'; endif; ?>>Moderate</option>
        <option value="severe" <?php if(request('severity')==='severe'): echo 'selected'; endif; ?>>Severe</option>
      </select>
      <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="warning_only" value="1" <?php if(request('warning_only')): echo 'checked'; endif; ?> class="rounded">
        Warnings only
      </label>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if(request()->hasAny(['student_id','severity','warning_only'])): ?>
        <a href="<?php echo e(route('hostel.disciplinary')); ?>" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  
  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Date','Description','Severity','Action Taken','Warning','Reported By','Delete']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50 <?php echo e($rec->severity==='severe' ? 'bg-red-50/40' : ''); ?>">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800"><?php echo e($rec->student_name); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($rec->admission_number); ?>

              <?php if(($repeatCounts[$rec->student_id] ?? 0) > 1): ?>
                <span class="badge-red ml-1"><?php echo e($repeatCounts[$rec->student_id]); ?> incidents</span>
              <?php endif; ?>
            </p>
          </td>
          <td class="px-4 py-3 text-slate-600"><?php echo e(\Carbon\Carbon::parse($rec->incident_date)->format('d M Y')); ?></td>
          <td class="px-4 py-3 text-slate-500 max-w-xs"><p class="line-clamp-2"><?php echo e($rec->description); ?></p></td>
          <td class="px-4 py-3">
            <span class="badge-<?php echo e($rec->severity==='severe'?'red':($rec->severity==='moderate'?'amber':'slate')); ?> capitalize text-xs">
              <?php echo e($rec->severity ?? 'minor'); ?>

            </span>
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($rec->action_taken ?? '—'); ?></td>
          <td class="px-4 py-3 text-center">
            <?php if($rec->is_warning): ?>
              <span class="badge-amber text-xs">Warning #<?php echo e($rec->warning_number); ?></span>
              <?php if($rec->warning_date): ?><p class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($rec->warning_date)->format('d M Y')); ?></p><?php endif; ?>
            <?php else: ?>
              <span class="text-slate-300 text-xs">—</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($rec->reported_by ? 'Staff #'.$rec->reported_by : '—'); ?></td>
          <td class="px-4 py-3">
            <form method="POST" action="<?php echo e(route('hostel.disciplinary.delete', $rec->id)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Delete this record?')">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No disciplinary records.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($records->hasPages()): ?><div class="px-4 pb-3"><?php echo e($records->links()); ?></div><?php endif; ?>
  </div>

  
  <?php $repeatStudents = $repeatCounts->filter(fn($count) => $count >= 3); ?>
  <?php if($repeatStudents->isNotEmpty()): ?>
  <div class="card border-l-4 border-red-400 bg-red-50/40">
    <h3 class="font-semibold text-red-700 mb-2">Repeat Offenders (3+ incidents)</h3>
    <div class="flex flex-wrap gap-2">
      <?php $__currentLoopData = $repeatStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sid => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $stu = $students->firstWhere('id', $sid); ?>
        <?php if($stu): ?>
          <span class="badge-red text-xs"><?php echo e($stu->full_name); ?> — <?php echo e($count); ?> incidents</span>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\hostel-disciplinary.blade.php ENDPATH**/ ?>