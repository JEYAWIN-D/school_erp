<?php $__env->startSection('title', 'Portal Account Management'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Portal Account Management</h1>
    <a href="<?php echo e(route('communication.index')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex gap-3 mb-5">
      <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search student..." class="input flex-1">
      <button type="submit" class="btn-primary btn-sm">Search</button>
    </form>

    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Adm No.</th>
          <th class="th">Student Login</th>
          <th class="th">Parent Login</th>
          <th class="th">Actions</th>
        </tr></thead>
        <tbody>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $hasStudentLogin = in_array($s->id, $linkedStudentUserIds);
            $parentCount     = $parentLinks[$s->id]?->count() ?? 0;
            $userId          = $studentUsers[$s->id] ?? null;
          ?>
          <tr class="tr">
            <td class="td font-medium text-slate-700"><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?></td>
            <td class="td font-mono text-xs"><?php echo e($s->admission_no); ?></td>
            <td class="td">
              <?php if($hasStudentLogin): ?>
                <span class="badge-green">Active</span>
              <?php else: ?>
                <span class="badge-red">None</span>
              <?php endif; ?>
            </td>
            <td class="td">
              <?php if($parentCount > 0): ?>
                <span class="badge-green"><?php echo e($parentCount); ?> account(s)</span>
              <?php else: ?>
                <span class="badge-red">None</span>
              <?php endif; ?>
            </td>
            <td class="td flex gap-1 flex-wrap">
              <button onclick="openModal(<?php echo e($s->id); ?>, '<?php echo e(addslashes($s->first_name . ' ' . $s->last_name)); ?>', '<?php echo e(addslashes($s->father_name ?? '')); ?>', '<?php echo e(addslashes($s->father_mobile ?? '')); ?>')"
                      class="btn-xs btn-primary">
                + Create
              </button>
              <?php if($userId): ?>
              <button onclick="openResetModal(<?php echo e($userId); ?>, '<?php echo e(addslashes($s->first_name . ' ' . $s->last_name)); ?>')"
                      class="btn-xs btn-secondary">
                Reset Pwd
              </button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4"><?php echo e($students->links()); ?></div>
  </div>
</div>


<div id="accountModal" class="fixed inset-0 z-50 hidden" x-data="{ show: false }" :class="show ? 'flex' : 'hidden'" style="display:none">
  <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>
  <div class="relative z-10 m-auto w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
    <h3 class="font-bold text-slate-800 mb-4">Create Portal Account</h3>
    <form method="POST" action="<?php echo e(route('communication.portal-accounts.create')); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="student_id" id="modal_student_id">
      <div class="space-y-3">
        <div>
          <label class="label">Account Type</label>
          <select name="account_type" id="modal_type" class="select w-full" onchange="toggleParentFields()">
            <option value="student">Student Portal</option>
            <option value="parent">Parent Portal</option>
          </select>
        </div>
        <div id="parent_name_field" style="display:none">
          <label class="label">Parent Name</label>
          <input type="text" name="parent_name" id="modal_parent_name" class="input w-full">
        </div>
        <div id="parent_mobile_field" style="display:none">
          <label class="label">Parent Mobile</label>
          <input type="text" name="mobile" id="modal_mobile" class="input w-full">
        </div>
        <div>
          <label class="label">Login Email</label>
          <input type="email" name="email" id="modal_email" class="input w-full" required>
        </div>
        <div>
          <label class="label">Initial Password</label>
          <input type="password" name="password" class="input w-full" required minlength="8">
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary flex-1">Create Account</button>
          <button type="button" onclick="closeModal()" class="btn-secondary flex-1">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div id="resetModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div class="relative z-10 m-auto w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6">
    <h3 class="font-bold text-slate-800 mb-1">Reset Password</h3>
    <p class="text-sm text-slate-500 mb-4" id="reset_name"></p>
    <form id="resetForm" method="POST" class="space-y-3">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">New Password</label>
        <input type="password" name="password" class="input w-full" required minlength="8">
      </div>
      <div>
        <label class="label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="input w-full" required minlength="8">
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-primary flex-1">Reset</button>
        <button type="button" onclick="closeResetModal()" class="btn-secondary flex-1">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openModal(studentId, studentName, parentName, parentMobile) {
  document.getElementById('modal_student_id').value = studentId;
  document.getElementById('modal_parent_name').value = parentName;
  document.getElementById('modal_mobile').value = parentMobile;
  document.getElementById('accountModal').style.display = 'flex';
}
function closeModal() {
  document.getElementById('accountModal').style.display = 'none';
}
function toggleParentFields() {
  const t = document.getElementById('modal_type').value;
  document.getElementById('parent_name_field').style.display = t === 'parent' ? 'block' : 'none';
  document.getElementById('parent_mobile_field').style.display = t === 'parent' ? 'block' : 'none';
}
function openResetModal(userId, name) {
  document.getElementById('reset_name').textContent = name;
  document.getElementById('resetForm').action = '/communication/portal-accounts/' + userId + '/reset-password';
  document.getElementById('resetModal').style.display = 'flex';
}
function closeResetModal() {
  document.getElementById('resetModal').style.display = 'none';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\portal-accounts.blade.php ENDPATH**/ ?>