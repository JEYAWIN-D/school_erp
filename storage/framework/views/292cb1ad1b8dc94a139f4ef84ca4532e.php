<?php $__env->startSection('title', 'My Profile'); ?>
<?php $__env->startSection('content'); ?>

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">My Profile</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1rem;">

  
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      Contact Details
    </div>
    <form method="POST" action="<?php echo e(route('portal.parent.profile.update')); ?>">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div style="display: flex; flex-direction: column; gap: .75rem;">
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Full Name</label>
          <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
          <?php $__errorArgs = ['name'];
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
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Email</label>
          <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
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
        <button type="submit"
                style="width: 100%; padding: .625rem; background: #2563eb; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
          Save Changes
        </button>
      </div>
    </form>
  </div>

  
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      Change Password
    </div>
    <form method="POST" action="<?php echo e(route('portal.change-password')); ?>">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div style="display: flex; flex-direction: column; gap: .75rem;">
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
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Confirm New Password</label>
          <input type="password" name="password_confirmation"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <button type="submit"
                style="width: 100%; padding: .625rem; background: #475569; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
                onmouseover="this.style.background='#334155'" onmouseout="this.style.background='#475569'">
          Change Password
        </button>
      </div>
    </form>
  </div>

</div>


<div class="portal-card">
  <div class="section-title">
    <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    Linked Children
  </div>
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem;">
    <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="display: flex; align-items: center; gap: .75rem; padding: .75rem; background: #f8fafc; border-radius: .75rem; border: 1px solid #f1f5f9;">
      <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: .875rem; font-weight: 700; color: white; flex-shrink: 0;">
        <?php echo e(strtoupper(substr($child->first_name, 0, 1))); ?>

      </div>
      <div>
        <p style="font-size: .875rem; font-weight: 600; color: #1e293b;"><?php echo e($child->first_name); ?> <?php echo e($child->last_name); ?></p>
        <p style="font-size: .72rem; color: #94a3b8;">Adm# <?php echo e($child->admission_no); ?></p>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\parent\profile.blade.php ENDPATH**/ ?>