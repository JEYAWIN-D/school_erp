<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($config->title); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen py-10 px-4">
  <div class="max-w-2xl mx-auto">
    
    <div class="text-center mb-8">
      <h1 class="text-2xl font-bold text-slate-800"><?php echo e($config->title); ?></h1>
      <?php if($config->description): ?><p class="text-slate-600 mt-2"><?php echo e($config->description); ?></p><?php endif; ?>
      <?php if($config->class): ?><p class="text-sm text-indigo-600 mt-1">Class: <?php echo e($config->class->name); ?></p><?php endif; ?>
      <?php if($config->academicYear): ?><p class="text-sm text-slate-400">Academic Year: <?php echo e($config->academicYear->name); ?></p><?php endif; ?>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-6">
      <ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('apply.submit', $config->link_token)); ?>" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-5">
      <?php echo csrf_field(); ?>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Parent/Guardian Mobile <span class="text-red-500">*</span></label>
        <input type="tel" name="parent_mobile" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400" required value="<?php echo e(old('parent_mobile')); ?>">
      </div>

      <?php $__currentLoopData = $config->fields ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          <?php echo e($field['label']); ?>

          <?php if($field['required'] ?? false): ?><span class="text-red-500">*</span><?php endif; ?>
        </label>
        <?php if($field['type'] === 'date'): ?>
          <input type="date" name="fields[<?php echo e($field['name']); ?>]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"
                 <?php echo e(($field['required'] ?? false) ? 'required' : ''); ?> value="<?php echo e(old('fields.' . $field['name'])); ?>">
        <?php elseif($field['type'] === 'select' && $field['name'] === 'gender'): ?>
          <select name="fields[<?php echo e($field['name']); ?>]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm" <?php echo e(($field['required'] ?? false) ? 'required' : ''); ?>>
            <option value="">Select...</option>
            <option value="male" <?php if(old('fields.'.$field['name'])==='male'): echo 'selected'; endif; ?>>Male</option>
            <option value="female" <?php if(old('fields.'.$field['name'])==='female'): echo 'selected'; endif; ?>>Female</option>
            <option value="other" <?php if(old('fields.'.$field['name'])==='other'): echo 'selected'; endif; ?>>Other</option>
          </select>
        <?php elseif($field['type'] === 'select' && $field['name'] === 'category'): ?>
          <select name="fields[<?php echo e($field['name']); ?>]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
            <option value="">Select...</option>
            <?php $__currentLoopData = ['General','SC','ST','OBC','EWS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat); ?>" <?php if(old('fields.'.$field['name'])===$cat): echo 'selected'; endif; ?>><?php echo e($cat); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        <?php else: ?>
          <input type="text" name="fields[<?php echo e($field['name']); ?>]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"
                 <?php echo e(($field['required'] ?? false) ? 'required' : ''); ?> value="<?php echo e(old('fields.' . $field['name'])); ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <?php if(!empty($config->document_fields)): ?>
      <div class="border-t border-slate-100 pt-5">
        <h3 class="text-sm font-semibold text-slate-600 mb-4">Document Uploads</h3>
        <?php $__currentLoopData = $config->document_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mb-4">
          <label class="block text-sm font-medium text-slate-700 mb-1">
            <?php echo e($doc['label']); ?>

            <?php if($doc['required'] ?? false): ?><span class="text-red-500">*</span><?php endif; ?>
          </label>
          <input type="file" name="docs[<?php echo e($doc['name']); ?>]"
                 accept=".pdf,.jpg,.jpeg,.png"
                 <?php echo e(($doc['required'] ?? false) ? 'required' : ''); ?>

                 class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700">
          <p class="text-xs text-slate-400 mt-1">PDF, JPG or PNG — max <?php echo e($doc['max_mb'] ?? 5); ?>MB</p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>

      <div class="pt-3">
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 text-sm transition">
          Submit Application
        </button>
      </div>
    </form>

    <p class="text-center text-xs text-slate-400 mt-6">Your application will be reviewed by the school admissions team.</p>
  </div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\admissions\public-application.blade.php ENDPATH**/ ?>