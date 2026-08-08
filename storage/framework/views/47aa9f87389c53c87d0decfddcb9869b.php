<?php $__env->startSection('title', 'Emergency Contacts'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Emergency Contacts per Route</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('transport.index')); ?>" class="btn btn-secondary">← Transport</a>
      <?php if($routeId): ?><button @click="showAdd=!showAdd" class="btn btn-primary">+ Add Contact</button><?php endif; ?>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <form method="GET" class="card py-4">
    <div class="flex gap-3 items-end">
      <div class="flex-1">
        <label class="label">Select Route</label>
        <select name="route_id" class="select" onchange="this.form.submit()">
          <option value="">Choose a route...</option>
          <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($r->id); ?>" <?php if($routeId == $r->id): echo 'selected'; endif; ?>><?php echo e($r->route_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
  </form>

  <?php if($routeId): ?>
  
  <div x-show="showAdd" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Add Emergency Contact</h3>
    <form method="POST" action="<?php echo e(route('transport.emergency-contacts.store')); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="route_id" value="<?php echo e($routeId); ?>">
      <div>
        <label class="label">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="Contact full name">
      </div>
      <div>
        <label class="label">Phone <span class="text-red-500">*</span></label>
        <input type="text" name="phone" class="input" required placeholder="Mobile/landline">
      </div>
      <div>
        <label class="label">Relationship / Role</label>
        <select name="relationship" class="select">
          <option value="">Select...</option>
          <option>Police Station</option>
          <option>Hospital</option>
          <option>Admin/Principal</option>
          <option>Parent Representative</option>
          <option>Transport In-charge</option>
          <option>Other</option>
        </select>
      </div>
      <div>
        <label class="label">Designation / Details</label>
        <input type="text" name="designation" class="input" placeholder="e.g. Incharge, SHO, Doctor">
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_primary" value="1" id="is_primary" class="w-4 h-4">
        <label for="is_primary" class="text-sm text-slate-600">Mark as Primary Contact</label>
      </div>
      <div>
        <label class="label">Notes</label>
        <input type="text" name="notes" class="input" placeholder="Available hours, etc.">
      </div>
      <div class="md:col-span-2 flex gap-3">
        <button type="submit" class="btn btn-primary">Save Contact</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  
  <div class="card">
    <h3 class="font-semibold text-slate-700 mb-4">
      Route Emergency Contacts
      <span class="text-xs font-normal text-slate-400 ml-2">(<?php echo e(count($contacts)); ?> contacts)</span>
    </h3>
    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="flex items-start justify-between py-3 border-b border-slate-100 last:border-0">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg <?php echo e($c->is_primary ? 'bg-red-100' : 'bg-slate-100'); ?> flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 <?php echo e($c->is_primary ? 'text-red-600' : 'text-slate-500'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <p class="font-medium text-slate-800 text-sm"><?php echo e($c->name); ?></p>
            <?php if($c->is_primary): ?><span class="badge-red text-xs">Primary</span><?php endif; ?>
          </div>
          <p class="text-xs text-slate-500"><?php echo e($c->relationship); ?><?php if($c->designation): ?> — <?php echo e($c->designation); ?><?php endif; ?></p>
          <p class="text-sm font-mono text-slate-700 mt-0.5"><?php echo e($c->phone); ?></p>
          <?php if($c->notes): ?><p class="text-xs text-slate-400 mt-0.5"><?php echo e($c->notes); ?></p><?php endif; ?>
        </div>
      </div>
      <form method="POST" action="<?php echo e(route('transport.emergency-contacts.delete', $c->id)); ?>" onsubmit="return confirm('Remove this contact?')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-xs text-red-400 hover:text-red-600">Remove</button>
      </form>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="text-slate-400 text-sm text-center py-8">No emergency contacts added for this route yet.</p>
    <?php endif; ?>
  </div>

  <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-800">
    <strong>Tip:</strong> Add police station, nearest hospital, school admin, and a parent representative as emergency contacts for each route. Mark the most important one as Primary.
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\emergency-contacts.blade.php ENDPATH**/ ?>