<?php $__env->startSection('title', 'My Profile'); ?>
<?php $__env->startSection('content'); ?>

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">My Profile</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">

  
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Student Information
    </div>
    <dl style="display: flex; flex-direction: column; gap: .5rem;">
      <?php $__currentLoopData = [
        ['Name', $student->first_name.' '.($student->middle_name ? $student->middle_name.' ' : '').$student->last_name],
        ['Admission No.', $student->admission_no],
        ['Date of Birth', \Carbon\Carbon::parse($student->dob)->format('d M Y')],
        ['Gender', ucfirst($student->gender)],
        ['Blood Group', $student->blood_group ?? '—'],
        ['Category', strtoupper($student->category ?? '—')],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="divider-row" style="display: flex; justify-content: space-between; padding: .375rem 0; font-size: .875rem; align-items: center;">
        <dt style="color: #94a3b8; font-weight: 500;"><?php echo e($label); ?></dt>
        <dd style="color: #1e293b; font-weight: 600; text-align: right;"><?php echo e($value); ?></dd>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </dl>
  </div>

  
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      Contact Details
    </div>
    <form method="POST" action="<?php echo e(route('portal.student.profile.update')); ?>">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div style="display: flex; flex-direction: column; gap: .75rem;">
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Email</label>
          <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color .1s;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color: #dc2626; font-size: .75rem; margin-top: .25rem;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Mobile</label>
          <input type="text" name="mobile" value="<?php echo e(old('mobile', $user->mobile ?? '')); ?>"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color .1s;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <button type="submit"
                style="width: 100%; padding: .625rem; background: #2563eb; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
          Update Contact
        </button>
      </div>
    </form>
  </div>

</div>


<div class="portal-card" style="margin-top: 1rem;">
  <div class="section-title">
    <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    Change Password
  </div>
  <form method="POST" action="<?php echo e(route('portal.change-password')); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: .75rem;">
      <div>
        <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Current Password</label>
        <input type="password" name="current_password"
               style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color: #dc2626; font-size: .75rem; margin-top: .25rem;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div>
        <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">New Password</label>
        <input type="password" name="password"
               style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
      </div>
      <div>
        <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Confirm Password</label>
        <input type="password" name="password_confirmation"
               style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
               onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
      </div>
    </div>
    <div style="margin-top: .875rem;">
      <button type="submit"
              style="padding: .5rem 1.5rem; background: #475569; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
              onmouseover="this.style.background='#334155'" onmouseout="this.style.background='#475569'">
        Change Password
      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\profile.blade.php ENDPATH**/ ?>