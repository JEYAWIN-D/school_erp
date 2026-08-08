<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Submitted</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
  <div class="max-w-md w-full text-center bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <h1 class="text-xl font-bold text-slate-800 mb-2">Application Submitted!</h1>
    <p class="text-slate-500 text-sm mb-4">Your application has been received successfully.</p>
    <div class="bg-indigo-50 rounded-xl px-4 py-3 mb-6">
      <p class="text-xs text-indigo-500 mb-1">Application Number</p>
      <p class="text-2xl font-bold text-indigo-700 font-mono"><?php echo e($app->application_number); ?></p>
    </div>
    <p class="text-xs text-slate-400">Please save this number for future reference. The school will contact you on <strong><?php echo e($app->parent_mobile); ?></strong> regarding the next steps.</p>
  </div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\admissions\application-success.blade.php ENDPATH**/ ?>