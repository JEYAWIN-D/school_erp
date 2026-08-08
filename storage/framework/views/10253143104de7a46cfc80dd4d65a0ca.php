<?php $__env->startSection('title', 'Bulk Email'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="bulkEmail()">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Compose Bulk Email</h1>
    <a href="<?php echo e(route('communication.index')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('communication.bulk-email.send')); ?>" x-ref="form">
    <?php echo csrf_field(); ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

      
      <div class="lg:col-span-2 space-y-4">
        <div class="card">
          <h3 class="font-semibold text-slate-700 mb-4">Email Content</h3>
          <div class="space-y-3">
            <div>
              <label class="label">Subject</label>
              <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" placeholder="Email subject..."
                     class="input w-full" required>
              <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
              <label class="label">Message Body</label>
              <div class="flex gap-1 mb-1 flex-wrap">
                <button type="button" onclick="emailFmt('bold')" class="btn-xs btn-secondary" title="Bold"><strong>B</strong></button>
                <button type="button" onclick="emailFmt('italic')" class="btn-xs btn-secondary" title="Italic"><em>I</em></button>
                <button type="button" onclick="emailFmt('underline')" class="btn-xs btn-secondary" title="Underline"><u>U</u></button>
                <button type="button" onclick="emailFmt('insertUnorderedList')" class="btn-xs btn-secondary">• List</button>
                <button type="button" onclick="emailFmt('insertOrderedList')" class="btn-xs btn-secondary">1. List</button>
                <button type="button" onclick="emailFmt('justifyLeft')" class="btn-xs btn-secondary">≡</button>
                <button type="button" onclick="emailFmt('justifyCenter')" class="btn-xs btn-secondary">≡ center</button>
              </div>
              <div id="email-body-editor" contenteditable="true"
                   class="input min-h-[200px] text-sm leading-relaxed"
                   style="white-space:pre-wrap;"><?php echo old('body'); ?></div>
              <textarea name="body" id="email-body-hidden" class="hidden"><?php echo e(old('body')); ?></textarea>
              <script>
                function emailFmt(cmd) { document.execCommand(cmd, false, null); document.getElementById('email-body-editor').focus(); }
                document.querySelector('form').addEventListener('submit', function() {
                  document.getElementById('email-body-hidden').value = document.getElementById('email-body-editor').innerHTML;
                }, true);
              </script>
              <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>
        </div>
      </div>

      
      <div class="space-y-4">
        <div class="card">
          <h3 class="font-semibold text-slate-700 mb-4">Target Audience</h3>
          <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition"
                   :class="audience === 'all_parents' ? 'border-blue-400 bg-blue-50' : ''">
              <input type="radio" name="audience_type" value="all_parents" x-model="audience" class="text-blue-600">
              <div class="flex-1">
                <p class="text-sm font-medium text-slate-700">All Parents</p>
                <p class="text-xs text-slate-400">Emails all parent contacts</p>
              </div>
              <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">~<?php echo e($parentCount); ?></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition"
                   :class="audience === 'all_staff' ? 'border-blue-400 bg-blue-50' : ''">
              <input type="radio" name="audience_type" value="all_staff" x-model="audience" class="text-blue-600">
              <div class="flex-1">
                <p class="text-sm font-medium text-slate-700">All Staff</p>
                <p class="text-xs text-slate-400">Emails all active staff</p>
              </div>
              <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">~<?php echo e($staffCount); ?></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition"
                   :class="audience === 'specific_class' ? 'border-blue-400 bg-blue-50' : ''">
              <input type="radio" name="audience_type" value="specific_class" x-model="audience" class="text-blue-600">
              <div class="flex-1">
                <p class="text-sm font-medium text-slate-700">Specific Class</p>
                <p class="text-xs text-slate-400">Parents of a specific class</p>
              </div>
              <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full" x-text="classCount > 0 ? '~' + classCount : '—'"></span>
            </label>
          </div>

          
          <div x-show="audience === 'specific_class'" x-transition class="mt-3">
            <label class="label">Select Class</label>
            <select name="class_id" class="select w-full" x-model="classId" @change="updateClassCount">
              <option value="">— Select class —</option>
              <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->name); ?><?php if($classStudentCounts->has($cls->id)): ?> (<?php echo e($classStudentCounts[$cls->id]); ?> students)<?php endif; ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>

        
        <div class="card bg-amber-50 border border-amber-200">
          <p class="text-xs text-amber-700 font-medium mb-1">Before sending:</p>
          <ul class="text-xs text-amber-600 space-y-1 list-disc list-inside">
            <li>Emails send via SMTP configured in Settings</li>
            <li>All emails go immediately (no scheduling)</li>
            <li>Delivery failures are logged</li>
          </ul>
        </div>

        <button type="submit" class="btn-primary w-full"
                onclick="return confirm('Send email to selected audience now?')">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          Send Bulk Email
        </button>
      </div>

    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const classCounts = <?php echo json_encode($classStudentCounts, 15, 512) ?>;
function bulkEmail() {
  return {
    audience: '<?php echo e(old('audience_type', 'all_parents')); ?>',
    classId: '<?php echo e(old('class_id', '')); ?>',
    classCount: 0,
    updateClassCount() {
      this.classCount = this.classId ? (classCounts[this.classId] ?? 0) : 0;
    },
  }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\bulk-email.blade.php ENDPATH**/ ?>