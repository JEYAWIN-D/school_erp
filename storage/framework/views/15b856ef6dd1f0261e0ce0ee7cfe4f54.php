<?php $__env->startSection('title','Digital Resources'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Digital Resources</h1>
    <button x-data @click="$dispatch('open-modal','add-resource')" class="btn btn-primary btn-sm">+ Upload Resource</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="type" class="select w-36">
      <option value="">All Types</option>
      <?php $__currentLoopData = ['ebook'=>'E-Book','video'=>'Video','audio'=>'Audio','document'=>'Document','link'=>'Web Link']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($k); ?>" <?php if(request('type')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="subject_id" class="select w-40">
      <option value="">All Subjects</option>
      <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('subject_id')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="class_id" class="select w-40">
      <option value="">All Classes</option>
      <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('class_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="access" class="select w-36">
      <option value="">Any Access</option>
      <option value="all" <?php if(request('access')==='all'): echo 'selected'; endif; ?>>All Classes (Global)</option>
      <option value="restricted" <?php if(request('access')==='restricted'): echo 'selected'; endif; ?>>Class-Restricted</option>
    </select>
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input flex-1" placeholder="Search title...">
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    <?php if(request()->hasAny(['type','subject_id','class_id','access','search'])): ?>
      <a href="<?php echo e(route('library.digital')); ?>" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
    <?php endif; ?>
  </div></form>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card flex flex-col justify-between">
      <div class="space-y-2">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="badge-<?php echo e(match($r->type ?? '') {'ebook'=>'green','video'=>'red','audio'=>'amber',default=>'slate'}); ?> text-xs capitalize"><?php echo e($r->type ?? '—'); ?></span>
          <?php if($r->is_premium ?? false): ?><span class="badge-indigo text-xs">Premium</span><?php endif; ?>
          <?php if($r->class_id): ?>
            <span class="badge-amber text-xs" title="Restricted to this class only">
              <?php echo e($r->class?->name ?? 'Class #'.$r->class_id); ?>

            </span>
          <?php else: ?>
            <span class="badge-green text-xs">All Classes</span>
          <?php endif; ?>
        </div>
        <p class="font-semibold text-slate-800 text-sm"><?php echo e($r->title); ?></p>
        <?php if($r->description): ?><p class="text-xs text-slate-400 line-clamp-2"><?php echo e($r->description); ?></p><?php endif; ?>
        <div class="flex items-center gap-3 text-xs text-slate-400">
          <?php if($r->subject): ?><span><?php echo e($r->subject?->name); ?></span><?php endif; ?>
          <?php if($r->file_size ?? null): ?><span><?php echo e(round($r->file_size/1024/1024,1)); ?>MB</span><?php endif; ?>
          <span><?php echo e($r->access_count ?? $r->download_count ?? 0); ?> views</span>
        </div>
      </div>
      <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
        <a href="<?php echo e(route('library.digital.view',$r->id)); ?>" target="_blank" class="btn btn-secondary btn-sm flex-1 text-center text-xs">View</a>
        <?php if($r->file_path): ?>
        <a href="<?php echo e(route('library.digital.download',$r->id)); ?>" class="btn btn-primary btn-sm text-xs">Download</a>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('library.digital.delete',$r->id)); ?>" class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button type="submit" class="btn btn-secondary btn-sm text-xs text-red-400" onclick="return confirm('Delete?')">Del</button>
        </form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-3 card text-center py-12 text-slate-400">No digital resources.</div>
    <?php endif; ?>
  </div>
  <?php if($resources->hasPages()): ?><div><?php echo e($resources->links()); ?></div><?php endif; ?>
</div>

<div x-data="{show:false,rtype:'ebook'}" x-on:open-modal.window="show=($event.detail==='add-resource')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
    <h3 class="font-semibold text-slate-700 mb-4">Add Digital Resource</h3>
    <form method="POST" action="<?php echo e(route('library.digital.store')); ?>" enctype="multipart/form-data" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Title <span class="text-red-500">*</span></label><input type="text" name="title" class="input" required></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Type</label>
          <select name="type" class="select" x-model="rtype">
            <?php $__currentLoopData = ['ebook'=>'E-Book','video'=>'Video','audio'=>'Audio','document'=>'Document','link'=>'Web Link']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Subject</label>
          <select name="subject_id" class="select">
            <option value="">None</option>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div>
        <label class="label">Access Control</label>
        <select name="class_id" class="select">
          <option value="">Available to All Classes</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?> only</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="text-xs text-slate-400 mt-1">Leave blank to make this resource accessible to all classes.</p>
      </div>
      <div x-show="rtype!=='link'"><label class="label">Upload File</label><input type="file" name="file" class="input"></div>
      <div x-show="rtype==='link'"><label class="label">URL</label><input type="url" name="url" class="input" placeholder="https://..."></div>
      <div><label class="label">Description</label><textarea name="description" class="input h-16"></textarea></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="is_premium" value="1"><label class="text-sm text-slate-600">Premium (restricted access)</label></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\digital-resources.blade.php ENDPATH**/ ?>