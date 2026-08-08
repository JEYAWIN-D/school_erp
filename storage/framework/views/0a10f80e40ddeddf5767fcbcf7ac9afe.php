<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Library Member Card</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
  <style>
    @media print { body { margin: 0; } .no-print { display: none; } }
    .card-box { width: 86mm; height: 54mm; border: 2px solid #4f46e5; border-radius: 8px; padding: 12px; font-family: sans-serif; display: inline-block; margin: 4px; box-sizing: border-box; }
  </style>
</head>
<body class="bg-white p-4">
  <div class="no-print mb-4 flex gap-2">
    <button onclick="window.print()" class="btn btn-primary btn-sm">Print Card</button>
    <a href="<?php echo e(route('library.members')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>
  <div class="card-box">
    <div class="flex items-center gap-2 mb-2">
      <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg">
        <?php echo e(strtoupper(substr($student->first_name, 0, 1))); ?>

      </div>
      <div>
        <p class="font-bold text-slate-800 text-sm leading-tight"><?php echo e($student->full_name); ?></p>
        <p class="text-xs text-slate-500"><?php echo e($student->currentEnrollment?->class?->name ?? ''); ?></p>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-1 text-xs text-slate-600">
      <div><span class="text-slate-400">Adm No:</span> <?php echo e($student->admission_number); ?></div>
      <div><span class="text-slate-400">Year:</span> <?php echo e($student->currentEnrollment?->academicYear?->name ?? date('Y')); ?></div>
    </div>
    <div class="mt-2 pt-2 border-t border-indigo-100 flex items-center justify-between">
      <p class="text-xs font-semibold text-indigo-700">LIBRARY MEMBER CARD</p>
      <p class="text-xs text-slate-400">Valid: <?php echo e(date('Y')); ?>-<?php echo e(date('y', strtotime('+1 year'))); ?></p>
    </div>
  </div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\library\member-card.blade.php ENDPATH**/ ?>