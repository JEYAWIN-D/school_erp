<?php $__env->startSection('title','Hostel Complaints'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Maintenance Complaints</h1>
      <p class="page-subtitle">Track, assign, and resolve hostel maintenance issues</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('hostel.overdue-outpasses')); ?>" class="btn btn-secondary btn-sm">Late Returns</a>
      <a href="<?php echo e(route('hostel.complaints-report')); ?>" class="btn btn-secondary btn-sm">Report</a>
      <button x-data @click="$dispatch('open-modal','add-complaint')" class="btn btn-primary btn-sm">+ Raise Complaint</button>
    </div>
  </div>

  
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-end">
    <select name="status" class="select w-36">
      <option value="">All Status</option>
      <option value="open" <?php if(request('status')==='open'): echo 'selected'; endif; ?>>Open</option>
      <option value="in_progress" <?php if(request('status')==='in_progress'): echo 'selected'; endif; ?>>In Progress</option>
      <option value="resolved" <?php if(request('status')==='resolved'): echo 'selected'; endif; ?>>Resolved</option>
    </select>
    <select name="priority" class="select w-32">
      <option value="">All Priority</option>
      <option value="high" <?php if(request('priority')==='high'): echo 'selected'; endif; ?>>High</option>
      <option value="medium" <?php if(request('priority')==='medium'): echo 'selected'; endif; ?>>Medium</option>
      <option value="low" <?php if(request('priority')==='low'): echo 'selected'; endif; ?>>Low</option>
    </select>
    <select name="category" class="select w-40">
      <option value="">All Categories</option>
      <?php $__currentLoopData = ['electrical','plumbing','furniture','cleanliness','pest_control','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($cat); ?>" <?php if(request('category')===$cat): echo 'selected'; endif; ?>><?php echo e(ucfirst(str_replace('_',' ',$cat))); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <?php if(request()->hasAny(['status','priority','category'])): ?>
      <a href="<?php echo e(route('hostel.complaints')); ?>" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
    <?php endif; ?>
  </div></form>

  <div class="grid grid-cols-3 gap-4 mb-2">
    <?php $open=$complaints->where('status','open')->count(); $inProgress=$complaints->where('status','in_progress')->count(); $resolved=$complaints->where('status','resolved')->count(); ?>
    <div class="card text-center py-3"><p class="text-xs text-slate-400">Open</p><p class="text-2xl font-bold text-red-600"><?php echo e($open); ?></p></div>
    <div class="card text-center py-3"><p class="text-xs text-slate-400">In Progress</p><p class="text-2xl font-bold text-amber-600"><?php echo e($inProgress); ?></p></div>
    <div class="card text-center py-3"><p class="text-xs text-slate-400">Resolved</p><p class="text-2xl font-bold text-green-600"><?php echo e($resolved); ?></p></div>
  </div>

  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Student','Room','Category','Description','Priority','Status','Assigned To / Vendor','Raised On','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr x-data="{open:false}" class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800 text-sm"><?php echo e($c->student?->full_name); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs">
            <?php echo e($c->room?->room_number ?? '—'); ?>

            <?php if($c->room_id): ?>
              <a href="<?php echo e(route('hostel.complaints.by-room', $c->room_id)); ?>" class="text-indigo-500 block text-xs hover:underline">History</a>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs"><?php echo e(str_replace('_',' ',$c->category ?? $c->complaint_type)); ?></span></td>
          <td class="px-4 py-3 text-slate-500 text-xs max-w-xs">
            <p class="truncate"><?php echo e($c->description); ?></p>
            <?php if($c->resolution_notes): ?><p class="text-green-600 text-xs mt-1 italic truncate">✓ <?php echo e($c->resolution_notes); ?></p><?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <span class="text-xs font-semibold <?php echo e(($c->priority==='high')?'text-red-600':(($c->priority==='medium')?'text-amber-600':'text-slate-400')); ?> capitalize"><?php echo e($c->priority ?? 'medium'); ?></span>
          </td>
          <td class="px-4 py-3"><span class="badge-<?php echo e($c->status==='resolved'?'green':($c->status==='in_progress'?'amber':'red')); ?> capitalize text-xs"><?php echo e(str_replace('_',' ',$c->status)); ?></span>
            <?php if($c->resolved_at): ?><p class="text-xs text-slate-400"><?php echo e($c->resolved_at->format('d M')); ?></p><?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs">
            <?php if($c->assignedTo): ?>
              <span class="text-indigo-600"><?php echo e($c->assignedTo->first_name); ?> <?php echo e($c->assignedTo->last_name); ?></span>
            <?php elseif($c->vendor_name): ?>
              <span class="text-amber-600"><?php echo e($c->vendor_name); ?></span>
            <?php else: ?>
              <span class="text-slate-300">—</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-400 text-xs"><?php echo e($c->created_at->format('d M')); ?></td>
          <td class="px-4 py-3">
            <button @click="open=!open" class="text-xs text-indigo-500 hover:underline"><?php echo e($c->status==='resolved'?'View':'Edit'); ?></button>
          </td>
        </tr>
        <tr x-show="open" style="display:none" class="bg-indigo-50/40">
          <td colspan="9" class="px-4 pb-4 pt-2">
            <form method="POST" action="<?php echo e(route('hostel.complaints.update', $c->id)); ?>" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <div>
                <label class="label">Status</label>
                <select name="status" class="select text-sm">
                  <option value="open" <?php if($c->status==='open'): echo 'selected'; endif; ?>>Open</option>
                  <option value="in_progress" <?php if($c->status==='in_progress'): echo 'selected'; endif; ?>>In Progress</option>
                  <option value="resolved" <?php if($c->status==='resolved'): echo 'selected'; endif; ?>>Resolved</option>
                </select>
              </div>
              <div>
                <label class="label">Assign to Staff</label>
                <select name="assigned_to" class="select text-sm">
                  <option value="">None</option>
                  <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($emp->id); ?>" <?php if($c->assigned_to==$emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?><?php if($emp->designation): ?> (<?php echo e($emp->designation); ?>)<?php endif; ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div>
                <label class="label">Assign to Vendor</label>
                <input type="text" name="vendor_name" value="<?php echo e($c->vendor_name); ?>" class="input text-sm" placeholder="Vendor / contractor name">
              </div>
              <div>
                <label class="label">Resolution Notes</label>
                <input type="text" name="resolution_notes" value="<?php echo e($c->resolution_notes); ?>" class="input text-sm" placeholder="What was done...">
              </div>
              <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No complaints.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($complaints->hasPages()): ?><div class="px-4 pb-3"><?php echo e($complaints->links()); ?></div><?php endif; ?>
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-complaint')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Raise Complaint</h3>
    <form method="POST" action="<?php echo e(route('hostel.complaints.store')); ?>" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->full_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Category</label>
          <select name="category" class="select">
            <?php $__currentLoopData = ['electrical'=>'Electrical','plumbing'=>'Plumbing','furniture'=>'Furniture','cleanliness'=>'Cleanliness','pest_control'=>'Pest Control','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div><label class="label">Priority</label>
          <select name="priority" class="select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
          </select>
        </div>
      </div>
      <div><label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input h-20" required></textarea>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\complaints.blade.php ENDPATH**/ ?>