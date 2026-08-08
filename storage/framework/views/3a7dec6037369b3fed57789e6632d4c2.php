<?php $__env->startSection('title', 'ID Card Template Designer'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{
  headerColor: '<?php echo e($school?->id_card_header_color ?? '#1e3a5f'); ?>',
  textColor: '<?php echo e($school?->id_card_text_color ?? '#1e3a5f'); ?>',
  bgColor: '<?php echo e($school?->id_card_bg_color ?? '#ffffff'); ?>',
  headerText: '<?php echo e(addslashes($school?->id_card_header_text ?? '')); ?>',
  footerText: '<?php echo e(addslashes($school?->id_card_footer_text ?? '')); ?>',
  showBlood: <?php echo e($school?->id_card_show_blood_group ? 'true' : 'true'); ?>,
  showDob: <?php echo e($school?->id_card_show_dob ? 'true' : 'true'); ?>,
  showQr: <?php echo e($school?->id_card_show_qr ? 'true' : 'true'); ?>,
  showMobile: <?php echo e($school?->id_card_show_mobile ? 'true' : 'true'); ?>,
  showAddress: <?php echo e($school?->id_card_show_address ? 'true' : 'false'); ?>,
  showPhoto: <?php echo e($school?->id_card_show_photo !== false ? 'true' : 'false'); ?>,
}">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">ID Card Template Designer</h1>
      <p class="page-subtitle">Configure logo, colours, and visible fields for student ID cards</p>
    </div>
    <div class="flex gap-2">
      <a href="<?php echo e(route('students.id-cards')); ?>" class="btn btn-secondary btn-sm">← ID Cards</a>
    </div>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    
    <form method="POST" action="<?php echo e(route('students.id-card-template.save')); ?>" class="space-y-5">
      <?php echo csrf_field(); ?>

      <div class="card space-y-4">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Colours & Style</h2>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="label text-xs">Header Background</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_header_color" x-model="headerColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="headerColor"></span>
            </div>
          </div>
          <div>
            <label class="label text-xs">Name / Label Color</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_text_color" x-model="textColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="textColor"></span>
            </div>
          </div>
          <div>
            <label class="label text-xs">Card Background</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_bg_color" x-model="bgColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="bgColor"></span>
            </div>
          </div>
        </div>
        <div>
          <label class="label text-xs">Custom Header Text <span class="text-slate-400">(leave blank to use school name)</span></label>
          <input type="text" name="id_card_header_text" x-model="headerText" class="input text-sm"
                 placeholder="<?php echo e($school?->school_name ?? 'School Name'); ?>">
        </div>
        <div>
          <label class="label text-xs">Footer Text</label>
          <input type="text" name="id_card_footer_text" x-model="footerText" class="input text-sm"
                 placeholder="Address or emergency contact">
        </div>
      </div>

      <div class="card space-y-3">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Visible Fields</h2>
        <p class="text-xs text-slate-500">Choose which information to display on the ID card.</p>
        <div class="grid grid-cols-2 gap-3">
          <?php
            $toggles = [
              ['id_card_show_photo',       'showPhoto',   'Student Photo'],
              ['id_card_show_blood_group', 'showBlood',   'Blood Group'],
              ['id_card_show_dob',         'showDob',     'Date of Birth'],
              ['id_card_show_mobile',      'showMobile',  'Parent Mobile'],
              ['id_card_show_qr',          'showQr',      'QR Code'],
              ['id_card_show_address',     'showAddress', 'Residential Address'],
            ];
          ?>
          <?php $__currentLoopData = $toggles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $model, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
            <input type="hidden" name="<?php echo e($name); ?>" value="0">
            <input type="checkbox" name="<?php echo e($name); ?>" value="1" x-model="<?php echo e($model); ?>"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700"><?php echo e($label); ?></span>
          </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full">Save Template</button>
    </form>

    
    <div>
      <h2 class="font-semibold text-slate-700 mb-3">Live Preview</h2>
      <p class="text-xs text-slate-400 mb-4">Approximate preview — actual PDF rendering may vary slightly.</p>

      
      <div class="inline-block rounded-xl overflow-hidden border-2 shadow-lg"
           :style="`border-color: ${headerColor}; background: ${bgColor}; width: 260px;`">
        
        <div class="text-center py-2 px-3" :style="`background: ${headerColor};`">
          <p class="text-white font-bold text-xs tracking-wide" x-text="headerText || '<?php echo e($school?->school_name ?? 'School Name'); ?>'"></p>
          <p class="text-white text-xs opacity-80 mt-0.5">STUDENT IDENTITY CARD</p>
        </div>
        
        <div class="p-3" :style="`background: ${bgColor};`">
          <div class="flex gap-3">
            
            <div x-show="showPhoto" class="w-16 h-20 rounded border border-slate-200 bg-slate-100 flex items-center justify-center text-slate-400 text-xs shrink-0">Photo</div>
            
            <div class="flex-1 min-w-0">
              <p class="font-bold text-xs truncate" :style="`color: ${textColor};`">Student Full Name</p>
              <p class="text-xs text-slate-500 mt-1">Adm: SC-2025-001</p>
              <p class="text-xs text-slate-500">Class: X — A</p>
              <p x-show="showDob" class="text-xs text-slate-500">DOB: 12/06/2010</p>
              <p x-show="showBlood" class="text-xs text-slate-500">Blood: B+</p>
              <p x-show="showMobile" class="text-xs text-slate-500">Ph: 9876543210</p>
              <p x-show="showAddress" class="text-xs text-slate-500 truncate">123, Main Street</p>
            </div>
            
            <div x-show="showQr" class="w-8 h-8 border border-slate-200 bg-slate-100 flex items-center justify-center shrink-0 mt-auto text-slate-300 text-xs">QR</div>
          </div>
        </div>
        
        <div class="py-1 px-3 text-center" :style="`background: ${headerColor};`">
          <p class="text-white text-xs opacity-90" x-text="footerText || '<?php echo e($school?->phone ?? 'School Phone'); ?>'"></p>
          <p class="text-white text-xs opacity-60">Valid: <?php echo e(\App\Models\AcademicYear::current()?->name ?? '2025-2026'); ?></p>
        </div>
      </div>

      <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
        <strong>Note:</strong> Colors and layout are applied to all generated ID cards.
        Go to <a href="<?php echo e(route('students.id-cards')); ?>" class="underline">ID Cards</a> to download with the new template.
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\id-card-template.blade.php ENDPATH**/ ?>