<?php $__env->startSection('title','Subject Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{editId:null,editOpen:false}">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Subject Management</h1>
    <button x-data @click="$dispatch('open-modal','add-subject')" class="btn btn-primary btn-sm">+ Add Subject</button>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Subject','Code','Type','Classes','Status','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50" x-data="{}">
          <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($s->name); ?></td>
          <td class="px-4 py-3 font-mono text-xs text-slate-500"><?php echo e($s->code ?? '—'); ?></td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs"><?php echo e($s->type ?? 'theory'); ?></span></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($s->allocations_count ?? 0); ?> classes</td>
          <td class="px-4 py-3"><span class="<?php echo e($s->is_active ? 'badge-green' : 'badge-slate'); ?>"><?php echo e($s->is_active ? 'Active' : 'Off'); ?></span></td>
          <td class="px-4 py-3 flex gap-2">
            <button @click="$dispatch('open-modal','edit-subject-<?php echo e($s->id); ?>')" class="text-indigo-600 hover:underline text-xs">Edit</button>
            <form method="POST" action="<?php echo e(route('academics.subjects.toggle',$s->id)); ?>" class="inline"><?php echo csrf_field(); ?>
              <button type="submit" class="text-amber-600 hover:underline text-xs"><?php echo e($s->is_active ? 'Deactivate' : 'Activate'); ?></button>
            </form>
          </td>
        </tr>
        
        <div x-data x-show="false" x-on:open-modal.window="$el.style.display = ($event.detail === 'edit-subject-<?php echo e($s->id); ?>') ? 'block' : $el.style.display"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md" @click.away="$el.style.display='none'">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Subject</h3>
            <form method="POST" action="<?php echo e(route('academics.subjects.update',$s->id)); ?>" class="space-y-3">
              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <div><label class="label">Name</label><input type="text" name="name" class="input" value="<?php echo e($s->name); ?>" required></div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Code</label><input type="text" name="code" class="input" value="<?php echo e($s->code); ?>"></div>
                <div><label class="label">Type</label>
                  <select name="type" class="select">
                    <?php $__currentLoopData = ['theory'=>'Theory','practical'=>'Practical','activity'=>'Activity','language'=>'Language']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if($s->type===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>
              <div class="flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="$el.closest('.fixed').style.display='none'" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No subjects found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($subjects->hasPages()): ?><div class="px-4 pb-3"><?php echo e($subjects->links()); ?></div><?php endif; ?>
  </div>
</div>


<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-subject')" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden" style="display:none" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Add New Subject</h3>
    <form method="POST" action="<?php echo e(route('academics.subjects.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Code</label><input type="text" name="code" class="input"></div>
        <div><label class="label">Type</label>
          <select name="type" class="select">
            <?php $__currentLoopData = ['theory'=>'Theory','practical'=>'Practical','activity'=>'Activity','language'=>'Language']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div><label class="label">Medium of Instruction</label>
        <select name="medium" class="select">
          <option value="english">English</option>
          <option value="tamil">Tamil</option>
          <option value="hindi">Hindi</option>
        </select>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Subject</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\academics\subjects-manage.blade.php ENDPATH**/ ?>