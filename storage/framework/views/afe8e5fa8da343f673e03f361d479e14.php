<?php $__env->startSection('title','Hall Tickets'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Hall Tickets — <?php echo e($exam->name); ?></h1>
    <p class="text-sm text-slate-500"><?php echo e($exam->start_date?->format('d M Y')); ?> to <?php echo e($exam->end_date?->format('d M Y')); ?></p>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Section</option>
      <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('section_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Load</button>
  </div></form>

  <?php if($enrollments->count()): ?>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <span class="font-semibold text-slate-700"><?php echo e($enrollments->count()); ?> students</span>
      <a href="<?php echo e(route('examinations.hall-tickets.bulk', $exam->id)); ?>?<?php echo e(http_build_query(request()->only('class_id','section_id'))); ?>"
         class="btn btn-primary btn-sm">
        ↓ Download All (ZIP)
      </a>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['#','Student','Admission No','Class','Section','Hall Ticket']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$enr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($i+1); ?></td>
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($enr->student?->full_name); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700"><?php echo e($enr->student?->admission_number); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($enr->class?->name); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($enr->section?->name ?? '—'); ?></td>
          <td class="px-4 py-3">
            <a href="<?php echo e(route('examinations.hall-ticket.pdf', $enr->id)); ?>?exam_id=<?php echo e($exam->id); ?>" target="_blank" class="btn btn-secondary btn-sm text-xs">Download PDF</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card text-center py-12 text-slate-400">Select class and section to load students.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\hall-tickets.blade.php ENDPATH**/ ?>