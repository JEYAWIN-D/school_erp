<?php $__env->startSection('title', 'Accident & Incident Log'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Accident & Incident Log</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('transport.drivers')); ?>" class="btn btn-secondary">← Drivers</a>
      <button @click="showAdd=!showAdd" class="btn btn-primary">Log Incident</button>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <div x-show="showAdd" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Log New Incident</h3>
    <form method="POST" action="<?php echo e(route('transport.incidents.store')); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Date <span class="text-red-500">*</span></label>
        <input type="date" name="incident_date" class="input" value="<?php echo e(date('Y-m-d')); ?>" required>
      </div>
      <div>
        <label class="label">Type <span class="text-red-500">*</span></label>
        <select name="incident_type" class="select" required>
          <option value="">Select type</option>
          <option value="accident">Accident</option>
          <option value="breakdown">Breakdown</option>
          <option value="theft">Theft</option>
          <option value="vandalism">Vandalism</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select">
          <option value="">Select vehicle</option>
          <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>"><?php echo e($v->vehicle_number); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Route</label>
        <select name="route_id" class="select">
          <option value="">Select route</option>
          <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->route_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Location</label>
        <input type="text" name="location" class="input" placeholder="Where it occurred">
      </div>
      <div>
        <label class="label">Severity <span class="text-red-500">*</span></label>
        <select name="severity" class="select" required>
          <option value="minor">Minor</option>
          <option value="moderate">Moderate</option>
          <option value="major">Major</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input" rows="3" required placeholder="What happened?"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="label">Action Taken</label>
        <textarea name="action_taken" class="input" rows="2" placeholder="Immediate response, repairs initiated, etc."></textarea>
      </div>
      <div>
        <label class="label">Reported By</label>
        <input type="text" name="reported_by" class="input" placeholder="Driver / warden / admin">
      </div>
      <div>
        <label class="label">FIR Number</label>
        <input type="text" name="fir_number" class="input" placeholder="If police case filed">
      </div>
      <div>
        <label class="label">Estimated Loss (₹)</label>
        <input type="number" name="estimated_loss" class="input" min="0" step="0.01">
      </div>
      <div class="md:col-span-2 flex gap-3">
        <button type="submit" class="btn btn-primary">Save Incident</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="card flex flex-wrap gap-3 items-end">
    <div>
      <label class="label text-xs">Vehicle</label>
      <select name="vehicle_id" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Vehicles</option>
        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v->id); ?>" <?php if(request('vehicle_id')==$v->id): echo 'selected'; endif; ?>><?php echo e($v->vehicle_number); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label text-xs">Type</label>
      <select name="incident_type" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="accident"   <?php if(request('incident_type')=='accident'): echo 'selected'; endif; ?>>Accident</option>
        <option value="breakdown"  <?php if(request('incident_type')=='breakdown'): echo 'selected'; endif; ?>>Breakdown</option>
        <option value="theft"      <?php if(request('incident_type')=='theft'): echo 'selected'; endif; ?>>Theft</option>
        <option value="vandalism"  <?php if(request('incident_type')=='vandalism'): echo 'selected'; endif; ?>>Vandalism</option>
        <option value="other"      <?php if(request('incident_type')=='other'): echo 'selected'; endif; ?>>Other</option>
      </select>
    </div>
    <div>
      <label class="label text-xs">Status</label>
      <select name="status" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="open"         <?php if(request('status')=='open'): echo 'selected'; endif; ?>>Open</option>
        <option value="under_review" <?php if(request('status')=='under_review'): echo 'selected'; endif; ?>>Under Review</option>
        <option value="resolved"     <?php if(request('status')=='resolved'): echo 'selected'; endif; ?>>Resolved</option>
        <option value="closed"       <?php if(request('status')=='closed'): echo 'selected'; endif; ?>>Closed</option>
      </select>
    </div>
  </form>

  
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Date</th>
        <th class="th">Type</th>
        <th class="th">Vehicle / Route</th>
        <th class="th">Location</th>
        <th class="th">Severity</th>
        <th class="th">Description</th>
        <th class="th">Status</th>
        <th class="th">Action</th>
      </tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($inc->incident_date)->format('d M Y')); ?></td>
          <td class="td">
            <?php $tc = ['accident'=>'red','breakdown'=>'amber','theft'=>'orange','vandalism'=>'purple','other'=>'slate']; ?>
            <span class="badge-<?php echo e($tc[$inc->incident_type] ?? 'slate'); ?> capitalize"><?php echo e($inc->incident_type); ?></span>
          </td>
          <td class="td text-sm">
            <?php echo e($inc->vehicle_number ?? '—'); ?><br>
            <span class="text-xs text-slate-400"><?php echo e($inc->route_name ?? ''); ?></span>
          </td>
          <td class="td text-sm text-slate-600"><?php echo e($inc->location ?? '—'); ?></td>
          <td class="td">
            <?php $sc = ['minor'=>'green','moderate'=>'amber','major'=>'red']; ?>
            <span class="badge-<?php echo e($sc[$inc->severity] ?? 'slate'); ?> capitalize"><?php echo e($inc->severity); ?></span>
          </td>
          <td class="td text-sm max-w-xs">
            <p class="truncate max-w-[200px]" title="<?php echo e($inc->description); ?>"><?php echo e($inc->description); ?></p>
            <?php if($inc->fir_number): ?><p class="text-xs text-slate-400 mt-0.5">FIR: <?php echo e($inc->fir_number); ?></p><?php endif; ?>
            <?php if($inc->estimated_loss): ?><p class="text-xs text-slate-400">Loss: ₹<?php echo e(number_format($inc->estimated_loss, 2)); ?></p><?php endif; ?>
          </td>
          <td class="td">
            <?php $stc = ['open'=>'red','under_review'=>'amber','resolved'=>'green','closed'=>'slate']; ?>
            <span class="badge-<?php echo e($stc[$inc->status] ?? 'slate'); ?> capitalize text-xs"><?php echo e(str_replace('_',' ',$inc->status)); ?></span>
          </td>
          <td class="td">
            <div x-data="{ open: false }">
              <button @click="open=!open" class="btn btn-xs btn-secondary">Update</button>
              <form x-show="open" x-transition method="POST" action="<?php echo e(route('transport.incidents.update-status', $inc->id)); ?>" class="mt-2 space-y-1 min-w-[180px]">
                <?php echo csrf_field(); ?>
                <select name="status" class="select text-xs py-1">
                  <option value="open"         <?php if($inc->status=='open'): echo 'selected'; endif; ?>>Open</option>
                  <option value="under_review" <?php if($inc->status=='under_review'): echo 'selected'; endif; ?>>Under Review</option>
                  <option value="resolved"     <?php if($inc->status=='resolved'): echo 'selected'; endif; ?>>Resolved</option>
                  <option value="closed"       <?php if($inc->status=='closed'): echo 'selected'; endif; ?>>Closed</option>
                </select>
                <textarea name="resolution_notes" class="input text-xs" rows="2" placeholder="Resolution notes"><?php echo e($inc->resolution_notes); ?></textarea>
                <button type="submit" class="btn btn-xs btn-primary">Save</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No incidents logged.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($incidents->hasPages()): ?><div class="mt-4"><?php echo e($incidents->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\transport\incidents.blade.php ENDPATH**/ ?>