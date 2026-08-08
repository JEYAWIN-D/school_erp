<?php $__env->startSection('title','Admission Pipeline'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Admission Pipeline</h1>
  </div>
  <?php $stages = ['enquiry'=>'Enquiry','application'=>'Application','entrance_test'=>'Entrance Test','interview'=>'Interview','document_verification'=>'Docs Verified','confirmed'=>'Confirmed','enrolled'=>'Enrolled']; ?>
  <div class="flex gap-2 overflow-x-auto pb-2">
    <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $count = $pipeline[$key] ?? 0; ?>
    <a href="?stage=<?php echo e($key); ?>" class="flex-shrink-0 card text-center py-4 px-6 min-w-[130px] <?php echo e(request('stage')===$key ? 'ring-2 ring-indigo-500' : ''); ?> hover:ring-1 hover:ring-indigo-300">
      <p class="text-2xl font-bold text-slate-800"><?php echo e($count); ?></p>
      <p class="text-xs text-slate-500 mt-1"><?php echo e($label); ?></p>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700"><?php echo e($stages[request('stage','enquiry')] ?? 'All Enquiries'); ?></h3>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <?php $__currentLoopData = ['Enquiry ID','Student','Class','Source','Status','Date','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase tracking-wide font-medium"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-mono text-xs text-slate-600"><?php echo e($e->enquiry_number); ?></td>
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($e->student_name); ?></td>
          <td class="px-4 py-3 text-slate-500"><?php echo e($e->class?->name); ?></td>
          <td class="px-4 py-3 text-slate-500 capitalize"><?php echo e($e->source); ?></td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($e->status_color); ?>"><?php echo e($e->status_label); ?></span></td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($e->created_at->format('d M Y')); ?></td>
          <td class="px-4 py-3">
            <a href="<?php echo e(route('admissions.show',$e->id)); ?>" class="text-indigo-600 hover:underline text-xs">View</a>
            <form method="POST" action="<?php echo e(route('admissions.advance-stage',$e->id)); ?>" class="inline ml-2">
              <?php echo csrf_field(); ?> <button type="submit" class="text-green-600 hover:underline text-xs">Advance →</button>
            </form>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No records in this stage.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($enquiries->hasPages()): ?><div class="px-4 pb-3"><?php echo e($enquiries->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\pipeline.blade.php ENDPATH**/ ?>