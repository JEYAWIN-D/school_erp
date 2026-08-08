<?php $__env->startSection('title', 'Mark Attendance'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('attendance.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Mark Attendance</h1>
  </div>

  
  <form method="GET" class="card py-4">
    <div class="flex flex-wrap gap-3">
      <select name="class_id" class="select w-36" onchange="this.form.submit()">
        <option value="">Select Class</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if($classId == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php if($sections->count()): ?>
        <select name="section_id" class="select w-32" onchange="this.form.submit()">
          <option value="">All Sections</option>
          <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sec->id); ?>" <?php if($sectionId == $sec->id): echo 'selected'; endif; ?>><?php echo e($sec->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      <?php endif; ?>
      <input type="date" name="date" value="<?php echo e($date); ?>" max="<?php echo e(today()->toDateString()); ?>" class="input w-44" onchange="this.form.submit()">
    </div>
  </form>

  <?php if($students->count()): ?>
    <form method="POST" action="<?php echo e(route('attendance.save')); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="class_id" value="<?php echo e($classId); ?>">
      <input type="hidden" name="section_id" value="<?php echo e($sectionId); ?>">
      <input type="hidden" name="date" value="<?php echo e($date); ?>">

      <?php
        $cutoff   = \App\Models\SchoolSetting::get('attendance_cutoff_time', '12:00');
        $lateTime = \App\Models\SchoolSetting::get('late_arrival_time', '09:30');
        $isPastCutoff = ($date === today()->toDateString()) && (now()->format('H:i') > $cutoff);
      ?>

      
      <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
        <p class="font-semibold text-slate-700 text-sm"><?php echo e($students->count()); ?> Students</p>
        <div class="flex gap-2 text-xs">
          <button type="button" onclick="markAll('present')" class="btn btn-ghost btn-sm text-green-600">✓ All Present</button>
          <button type="button" onclick="markAll('absent')" class="btn btn-ghost btn-sm text-red-600">✗ All Absent</button>
        </div>
      </div>

      
      <div class="space-y-2 md:hidden">
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $current = $existing[$student->id]?->status ?? 'present'; ?>
          <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 space-y-3" id="card-<?php echo e($student->id); ?>">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-slate-800 text-sm"><?php echo e($student->full_name); ?></p>
                <p class="text-xs text-slate-400 font-mono">Roll <?php echo e($student->currentEnrollment?->roll_number ?? '—'); ?></p>
              </div>
              <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-slate-100 text-slate-600" id="badge-<?php echo e($student->id); ?>">
                <?php echo e(ucfirst(str_replace('_',' ',$current))); ?>

              </span>
            </div>
            <div class="grid grid-cols-5 gap-1.5">
              <?php $__currentLoopData = [
                ['present','P','green'],
                ['absent','A','red'],
                ['late','L','amber'],
                ['half_day','H','orange'],
                ['leave','Le','blue'],
              ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$status,$lbl,$clr]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="flex flex-col items-center gap-1 cursor-pointer">
                <input type="radio" name="attendance[<?php echo e($student->id); ?>]" value="<?php echo e($status); ?>"
                  <?php if($current === $status): echo 'checked'; endif; ?>
                  class="sr-only peer"
                  onchange="highlightRow(<?php echo e($student->id); ?>, '<?php echo e($status); ?>'); document.getElementById('badge-<?php echo e($student->id); ?>').textContent='<?php echo e(ucfirst(str_replace('_',' ',$status))); ?>'">
                <span class="w-10 h-10 rounded-xl border-2 border-slate-200 flex items-center justify-center text-sm font-bold text-slate-400 peer-checked:border-<?php echo e($clr); ?>-500 peer-checked:bg-<?php echo e($clr); ?>-50 peer-checked:text-<?php echo e($clr); ?>-600 transition-all"><?php echo e($lbl); ?></span>
                <span class="text-[10px] text-slate-400"><?php echo e(str_replace('_',' ',ucfirst($status))); ?></span>
              </label>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <input type="text" name="remarks[<?php echo e($student->id); ?>]"
              value="<?php echo e($existing[$student->id]?->remark ?? ''); ?>"
              placeholder="Reason (optional)"
              class="input text-xs py-1.5 w-full" id="remark-<?php echo e($student->id); ?>">
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      
      <div class="hidden md:block table-wrap">
        <table class="w-full">
          <thead><tr>
            <th class="th">Roll</th><th class="th">Student</th>
            <th class="th text-center">Present</th><th class="th text-center">Absent</th>
            <th class="th text-center">Late</th><th class="th text-center">Half Day</th><th class="th text-center">Leave</th>
            <th class="th">Arrival Time</th>
            <th class="th">Reason</th>
          </tr></thead>
          <tbody>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $current = $existing[$student->id]?->status ?? 'present'; $existingRemark = $existing[$student->id]?->remark ?? ''; ?>
              <tr class="tr" id="row-<?php echo e($student->id); ?>">
                <td class="td font-mono"><?php echo e($student->currentEnrollment?->roll_number ?? '—'); ?></td>
                <td class="td font-medium text-slate-800"><?php echo e($student->full_name); ?></td>
                <?php $__currentLoopData = ['present','absent','late','half_day','leave']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <td class="td text-center">
                    <input type="radio" name="attendance[<?php echo e($student->id); ?>]" value="<?php echo e($status); ?>"
                      <?php if($current === $status): echo 'checked'; endif; ?>
                      class="w-4 h-4"
                      onchange="highlightRow(<?php echo e($student->id); ?>, '<?php echo e($status); ?>')">
                  </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td class="td">
                    <input type="time" name="arrival_time[<?php echo e($student->id); ?>]"
                        value="<?php echo e($existing[$student->id]?->arrival_time ?? ''); ?>"
                        class="input text-xs py-1 w-28">
                </td>
                <td class="td">
                    <input type="text" name="remarks[<?php echo e($student->id); ?>]"
                        value="<?php echo e($existingRemark); ?>"
                        placeholder="Optional reason"
                        class="input text-xs py-1 w-36 <?php if($current === 'present'): ?> opacity-40 <?php endif; ?>"
                        id="remark-<?php echo e($student->id); ?>">
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>

      <?php if($isPastCutoff): ?>
      <div class="alert-warning mt-4">
          Attendance cutoff time (<?php echo e($cutoff); ?>) has passed.
          <label class="ml-3 flex items-center gap-2 cursor-pointer inline-flex">
              <input type="checkbox" name="override_cutoff" value="1" class="w-4 h-4">
              <span class="text-sm font-medium">Override cutoff (requires justification)</span>
          </label>
      </div>
      <?php endif; ?>

      <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-4">
        <a href="<?php echo e(route('attendance.index')); ?>" class="btn btn-secondary w-full sm:w-auto text-center">Cancel</a>
        <button type="submit" class="btn btn-primary w-full sm:w-auto">Save Attendance</button>
      </div>
    </form>
  <?php elseif($classId): ?>
    <div class="card text-center py-12 text-slate-400">No students found for this class.</div>
  <?php else: ?>
    <div class="card text-center py-12 text-slate-400">Select a class to mark attendance.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function markAll(status) {
  document.querySelectorAll(`input[type=radio][value=${status}]`).forEach(r => {
    r.checked = true;
    highlightRow(r.name.match(/\d+/)[0], status);
  });
}
function highlightRow(id, status) {
  const row = document.getElementById('row-' + id);
  row.className = 'tr ' + (status === 'absent' ? 'bg-red-50' : status === 'late' ? 'bg-amber-50' : status === 'half_day' ? 'bg-orange-50' : status === 'leave' ? 'bg-blue-50' : '');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\attendance\mark.blade.php ENDPATH**/ ?>