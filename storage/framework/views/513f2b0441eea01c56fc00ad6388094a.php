<?php $__env->startSection('title', 'Submitted Applications'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Submitted Applications</h1>
    <a href="<?php echo e(route('admissions.form-builder')); ?>" class="btn btn-secondary btn-sm">← Form Builder</a>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label text-xs">Form</label>
      <select name="form_config_id" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Forms</option>
        <?php $__currentLoopData = $configs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('form_config_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label text-xs">Status</label>
      <select name="status" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="submitted"     <?php if(request('status')=='submitted'): echo 'selected'; endif; ?>>Submitted</option>
        <option value="under_review"  <?php if(request('status')=='under_review'): echo 'selected'; endif; ?>>Under Review</option>
        <option value="shortlisted"   <?php if(request('status')=='shortlisted'): echo 'selected'; endif; ?>>Shortlisted</option>
        <option value="rejected"      <?php if(request('status')=='rejected'): echo 'selected'; endif; ?>>Rejected</option>
        <option value="admitted"      <?php if(request('status')=='admitted'): echo 'selected'; endif; ?>>Admitted</option>
      </select>
    </div>
    <div class="flex-1">
      <label class="label text-xs">Search</label>
      <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input text-sm py-1.5" placeholder="Name, application no, mobile...">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">App. No.</th>
        <th class="th">Student</th>
        <th class="th">Parent</th>
        <th class="th">Form / Class</th>
        <th class="th">Status</th>
        <th class="th">Documents</th>
        <th class="th">Submitted</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $apps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td font-mono text-sm font-semibold"><?php echo e($app->application_number); ?></td>
          <td class="td">
            <p class="font-medium text-slate-800 text-sm"><?php echo e($app->student_name ?? ($app->form_data['student_name'] ?? '—')); ?></p>
          </td>
          <td class="td text-sm">
            <p><?php echo e($app->parent_name ?? ($app->form_data['parent_name'] ?? '—')); ?></p>
            <p class="text-xs text-slate-400"><?php echo e($app->parent_mobile); ?></p>
          </td>
          <td class="td text-sm text-slate-500"><?php echo e($app->formConfig?->title); ?></td>
          <td class="td">
            <?php $sc = ['submitted'=>'indigo','under_review'=>'amber','shortlisted'=>'green','rejected'=>'red','admitted'=>'teal']; ?>
            <span class="badge-<?php echo e($sc[$app->status] ?? 'slate'); ?> text-xs capitalize"><?php echo e(str_replace('_',' ',$app->status)); ?></span>
          </td>
          <td class="td text-xs text-slate-500"><?php echo e(count($app->documents ?? [])); ?> file(s)</td>
          <td class="td text-xs text-slate-400"><?php echo e($app->created_at->format('d M Y')); ?></td>
          <td class="td">
            <div x-data="{ open: false }">
              <button @click="open=!open" class="btn btn-xs btn-secondary">Review</button>
              <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center" @click.self="open=false">
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
                  <h3 class="font-semibold text-slate-700 mb-4">Application: <?php echo e($app->application_number); ?></h3>
                  <dl class="space-y-2 text-sm mb-4">
                    <?php $__currentLoopData = $app->form_data ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex gap-2">
                      <dt class="text-slate-400 w-40 shrink-0"><?php echo e(ucwords(str_replace('_',' ',$k))); ?></dt>
                      <dd class="font-medium text-slate-700"><?php echo e($v); ?></dd>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </dl>
                  <?php if(!empty($app->documents)): ?>
                  <div class="border-t border-slate-100 pt-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Documents</p>
                    <?php $__currentLoopData = $app->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(Storage::url($path)); ?>" target="_blank" class="text-indigo-600 text-xs hover:underline block"><?php echo e(ucwords(str_replace('_',' ',$name))); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                  <?php endif; ?>
                  <form method="POST" action="<?php echo e(route('admissions.applications.status', $app->id)); ?>" class="space-y-3 border-t border-slate-100 pt-3">
                    <?php echo csrf_field(); ?>
                    <select name="status" class="select text-sm">
                      <?php $__currentLoopData = ['submitted'=>'Submitted','under_review'=>'Under Review','shortlisted'=>'Shortlisted','rejected'=>'Rejected','admitted'=>'Admitted']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($v); ?>" <?php if($app->status===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <textarea name="admin_notes" class="input text-sm" rows="2" placeholder="Admin notes..."><?php echo e($app->admin_notes); ?></textarea>
                    <div class="flex gap-2">
                      <button type="submit" class="btn btn-primary btn-sm">Save</button>
                      <a href="<?php echo e(route('admissions.applications.pdf', $app->id)); ?>" class="btn btn-secondary btn-sm">PDF</a>
                      <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="td text-center py-8 text-slate-400">No applications submitted yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($apps->hasPages()): ?><div class="mt-4"><?php echo e($apps->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\applications.blade.php ENDPATH**/ ?>