<?php $__env->startSection('title', 'Roles & Permissions'); ?>
<?php $__env->startSection('content'); ?>
<?php
$moduleGroups = [
  'Main'         => ['view dashboard'],
  'Admissions'   => ['view admissions','create admissions','edit admissions','delete admissions','export admissions'],
  'Students'     => ['view students','create students','edit students','delete students','export students'],
  'Academics'    => ['view academics','create academics','edit academics','delete academics'],
  'Attendance'   => ['view attendance','mark attendance','edit attendance','export attendance'],
  'Examinations' => ['view examinations','create examinations','edit examinations','delete examinations','enter marks','publish results','export results'],
  'Fees'         => ['view fees','collect fees','edit fees','delete fees','export fees','manage fee structure','approve fees'],
  'HR & Payroll' => ['view employees','create employees','edit employees','delete employees','process payroll','view payroll','export payroll'],
  'Library'      => ['view library','manage library','issue books','return books'],
  'Transport'    => ['view transport','manage transport'],
  'Hostel'       => ['view hostel','manage hostel'],
  'Events'       => ['view events','create events','edit events','delete events'],
  'Gate'         => ['view gate','manage gate'],
  'Communication'=> ['send sms','send whatsapp','send email','manage circulars'],
  'LMS'          => ['view lms','create lms','edit lms','delete lms'],
  'Inventory'    => ['view inventory','manage inventory'],
  'Alumni'       => ['view alumni','manage alumni'],
  'Reports'      => ['view reports','export reports'],
  'System Admin' => ['manage users','manage roles','manage settings','view audit logs'],
  'Portal'       => ['access parent portal','access student portal'],
];
?>

<div class="space-y-6" x-data="{ tab: 'matrix', matrixRole: '<?php echo e($roles->first()?->id); ?>' }">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Roles & Permissions</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('system.audit-log')); ?>" class="btn-sm btn-secondary">Audit Log</a>
      <a href="<?php echo e(route('system.security')); ?>" class="btn-sm btn-secondary">Security</a>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  
  <div class="flex gap-1 border-b border-slate-200">
    <button @click="tab='matrix'"  :class="tab==='matrix'  ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">Permission Matrix</button>
    <button @click="tab='roles'"   :class="tab==='roles'   ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">Manage Roles</button>
    <button @click="tab='users'"   :class="tab==='users'   ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">User Assignments</button>
  </div>

  
  <div x-show="tab==='matrix'">
    <div class="card">
      <div class="flex items-center gap-4 mb-4 flex-wrap">
        <label class="label mb-0">Select Role:</label>
        <select x-model="matrixRole" class="select w-56">
          <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($role->id); ?>"><?php echo e(ucwords(str_replace('_',' ',$role->name))); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="text-xs text-slate-400">Click a checkbox to toggle. Click <strong>Save</strong> to apply.</p>
      </div>

      <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div x-show="matrixRole==='<?php echo e($role->id); ?>'">
        <form method="POST" action="<?php echo e(route('system.roles.update', $role->id)); ?>">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

          <div class="space-y-3">
            <?php $__currentLoopData = $moduleGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleName => $modulePerms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $availPerms = $permissions->whereIn('name', $modulePerms)->values();
            ?>
            <?php if($availPerms->count() > 0): ?>
            <div class="border border-slate-100 rounded-xl overflow-hidden">
              <div class="bg-slate-50 px-4 py-2 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide"><?php echo e($moduleName); ?></span>
                <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                  <input type="checkbox" class="rounded"
                         onchange="toggleGroup(this, '<?php echo e($role->id); ?>_<?php echo e(Str::slug($moduleName)); ?>')"
                         <?php if($availPerms->every(fn($p) => $role->hasPermissionTo($p->name))): ?> checked <?php endif; ?>>
                  all
                </label>
              </div>
              <div class="px-4 py-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-y-2 gap-x-4">
                <?php $__currentLoopData = $availPerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 text-xs cursor-pointer group">
                  <input type="checkbox" name="permissions[]" value="<?php echo e($perm->name); ?>"
                         class="perm-cb-<?php echo e($role->id); ?>_<?php echo e(Str::slug($moduleName)); ?> rounded"
                         <?php if($role->hasPermissionTo($perm->name)): echo 'checked'; endif; ?>>
                  <span class="text-slate-600 group-hover:text-slate-800"><?php echo e($perm->name); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="btn btn-primary">Save Permissions for <?php echo e(ucwords(str_replace('_',' ',$role->name))); ?></button>
            <span class="text-xs text-slate-400"><?php echo e($role->permissions_count); ?> permissions currently assigned</span>
          </div>
        </form>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  
  <div x-show="tab==='roles'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Create New Role</h3>
      <form method="POST" action="<?php echo e(route('system.roles.store')); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Role Name (slug) <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required placeholder="e.g. lab_assistant">
        </div>
        <div>
          <label class="label">Copy permissions from</label>
          <select name="copy_from" class="select">
            <option value="">— Start blank —</option>
            <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($r->name); ?>"><?php echo e(ucwords(str_replace('_',' ',$r->name))); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <button type="submit" class="btn-primary w-full">Create Role</button>
      </form>

      <div class="mt-6 space-y-2">
        <h4 class="text-sm font-semibold text-slate-600 mb-2">All Roles (<?php echo e($roles->count()); ?>)</h4>
        <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-0 text-sm">
          <span class="text-slate-700 capitalize"><?php echo e(str_replace('_', ' ', $role->name)); ?></span>
          <span class="text-xs text-slate-400"><?php echo e($role->users_count); ?> users · <?php echo e($role->permissions_count); ?> perms</span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    
    <div class="lg:col-span-2 space-y-3">
      <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="card py-3 px-4 flex items-center justify-between" x-data="{ open: false }">
        <div>
          <span class="font-medium text-slate-700 capitalize"><?php echo e(str_replace('_', ' ', $role->name)); ?></span>
          <span class="ml-2 text-xs text-slate-400"><?php echo e($role->users_count); ?> users · <?php echo e($role->permissions_count); ?> permissions</span>
        </div>
        <button @click="open=!open; tab='matrix'; matrixRole='<?php echo e($role->id); ?>'" class="btn-xs btn-secondary">
          Edit in Matrix
        </button>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  
  <div x-show="tab==='users'" class="space-y-4">
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Assign Role to User</h3>
      <form method="POST" action="<?php echo e(route('system.roles.assign')); ?>" class="flex flex-wrap gap-3 items-end">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">User</label>
          <select name="user_id" class="select" required>
            <option value="">Select user</option>
            <?php $__currentLoopData = $users->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div>
          <label class="label">Role</label>
          <select name="role" class="select" required>
            <option value="">Select role</option>
            <?php $__currentLoopData = $roles->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($r->name); ?>"><?php echo e(ucwords(str_replace('_',' ',$r->name))); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <button type="submit" class="btn-primary btn-sm">Assign</button>
      </form>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Current User Roles</h3>
      <div class="table-wrap">
        <table class="w-full text-sm">
          <thead><tr>
            <th class="th">User</th>
            <th class="th">Email</th>
            <th class="th">Roles</th>
            <th class="th">Actions</th>
          </tr></thead>
          <tbody>
            <?php $__currentLoopData = $users->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr">
              <td class="td font-medium text-slate-800"><?php echo e($u->name); ?></td>
              <td class="td text-slate-500 text-xs"><?php echo e($u->email); ?></td>
              <td class="td">
                <div class="flex flex-wrap gap-1">
                  <?php $__currentLoopData = $u->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <span class="badge-blue text-xs capitalize"><?php echo e(str_replace('_', ' ', $r->name)); ?></span>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php if($u->roles->isEmpty()): ?> <span class="text-slate-400 text-xs">No role</span> <?php endif; ?>
                </div>
              </td>
              <td class="td">
                <?php $__currentLoopData = $u->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <form method="POST" action="<?php echo e(route('system.roles.remove')); ?>" class="inline">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="user_id" value="<?php echo e($u->id); ?>">
                  <input type="hidden" name="role" value="<?php echo e($r->name); ?>">
                  <button type="submit" class="btn-xs text-rose-600 hover:bg-rose-50 mr-1"
                          onclick="return confirm('Remove <?php echo e($r->name); ?> from <?php echo e($u->name); ?>?')">
                    ✕ <?php echo e(str_replace('_', ' ', $r->name)); ?>

                  </button>
                </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleGroup(masterCb, groupClass) {
  document.querySelectorAll('.perm-cb-' + groupClass).forEach(cb => {
    cb.checked = masterCb.checked;
  });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\system\roles.blade.php ENDPATH**/ ?>