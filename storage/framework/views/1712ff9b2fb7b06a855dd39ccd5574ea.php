<?php $__env->startSection('title', 'Fee Reminder Schedule'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Fee Reminder Schedule</h1>
    <a href="<?php echo e(route('fees.index')); ?>" class="btn-secondary btn-sm">← Back to Fees</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 card space-y-6">
      <h2 class="text-base font-semibold text-slate-700">Reminder Schedule Configuration</h2>
      <form method="POST" action="<?php echo e(route('fees.reminder-config.save')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Schedule Name</label>
          <input type="text" name="name" class="input" value="<?php echo e(old('name', $config->name)); ?>" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="label">Days Before Due Date <span class="text-slate-400 text-xs">(comma-separated)</span></label>
            <input type="text" name="before_due_days" class="input" placeholder="e.g. 7,3,1"
              value="<?php echo e(old('before_due_days', implode(',', $config->before_due_days ?? [3,1]))); ?>">
            <p class="text-xs text-slate-500 mt-1">Reminder sent N days before due date.</p>
          </div>
          <div>
            <label class="label">Days After Due Date <span class="text-slate-400 text-xs">(comma-separated)</span></label>
            <input type="text" name="after_due_days" class="input" placeholder="e.g. 7,15,30"
              value="<?php echo e(old('after_due_days', implode(',', $config->after_due_days ?? [7,15,30]))); ?>">
            <p class="text-xs text-slate-500 mt-1">Reminder sent N days after due date.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <input type="checkbox" name="on_due_date" id="on_due_date" class="w-4 h-4"
            <?php echo e(old('on_due_date', $config->on_due_date) ? 'checked' : ''); ?>>
          <label for="on_due_date" class="label mb-0">Send reminder on due date</label>
        </div>

        <div>
          <label class="label">Channel</label>
          <select name="channel" class="select">
            <?php $__currentLoopData = ['email' => 'Email only', 'email_sms' => 'Email + SMS (SMS requires gateway)', 'sms' => 'SMS only (requires gateway)', 'whatsapp' => 'WhatsApp (requires gateway)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($val); ?>" <?php echo e(old('channel', $config->channel) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <hr class="border-slate-200">
        <h3 class="text-sm font-semibold text-slate-600">Email Template</h3>
        <p class="text-xs text-slate-500">Available placeholders: <code><?php echo e('{{'); ?>parent_name<?php echo e('); ?>'}}</code>, <code><?php echo e('{{'); ?>student_name<?php echo e('); ?>'}}</code>, <code><?php echo e('{{'); ?>class<?php echo e('); ?>'}}</code>, <code><?php echo e('{{'); ?>balance<?php echo e('); ?>'}}</code>, <code><?php echo e('{{'); ?>due_date<?php echo e('); ?>'}}</code>, <code><?php echo e('{{'); ?>school_name<?php echo e('); ?>'}}</code></p>

        <div>
          <label class="label">Email Subject</label>
          <input type="text" name="email_subject" class="input"
            value="<?php echo e(old('email_subject', $config->email_subject)); ?>"
            placeholder="Fee Payment Reminder – <?php echo e(config('app.name')); ?>">
        </div>
        <div>
          <label class="label">Email Body</label>
          <textarea name="email_body" rows="8" class="input"><?php echo e(old('email_body', $config->email_body ?? $config->defaultBody)); ?></textarea>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="btn-primary">Save Schedule</button>
        </div>
      </form>
    </div>

    
    <div class="space-y-4">
      <div class="card">
        <h2 class="text-base font-semibold text-slate-700 mb-3">Send Reminders Now</h2>
        <p class="text-sm text-slate-600 mb-4">Manually trigger fee reminder emails to all students with outstanding balances.</p>
        <form method="POST" action="<?php echo e(route('fees.reminders.send')); ?>" class="space-y-4">
          <?php echo csrf_field(); ?>
          <div>
            <label class="label">Filter by Class <span class="text-slate-400 text-xs">(optional)</span></label>
            <select name="class_id" class="select">
              <option value="">All Classes</option>
              <?php $__currentLoopData = \App\Models\Classes::active()->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <button type="submit" class="btn-primary w-full"
            onclick="return confirm('Send fee reminder emails to all students with outstanding fees?')">
            Send Reminders
          </button>
        </form>
      </div>

      <div class="card-flat bg-amber-50 border border-amber-200 rounded-xl p-4">
        <p class="text-xs font-semibold text-amber-800 mb-2">Schedule Information</p>
        <ul class="text-xs text-amber-700 space-y-1 list-disc list-inside">
          <li>Before due: <?php echo e(implode(', ', $config->before_due_days ?? [3,1])); ?> days</li>
          <li>On due date: <?php echo e(($config->on_due_date ?? true) ? 'Yes' : 'No'); ?></li>
          <li>After due: <?php echo e(implode(', ', $config->after_due_days ?? [7,15,30])); ?> days</li>
          <li>Channel: <?php echo e(ucwords(str_replace('_', ' + ', $config->channel ?? 'email'))); ?></li>
        </ul>
        <p class="text-xs text-amber-600 mt-3">Automated scheduling requires a Laravel cron job / scheduled command to be configured on the server.</p>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\fees\reminder-config.blade.php ENDPATH**/ ?>