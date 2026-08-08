<?php $__env->startSection('title', 'Bulk Book Import'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Bulk Book Import</h1>
    <a href="<?php echo e(route('library.books')); ?>" class="btn btn-secondary btn-sm">Back to Books</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Upload Excel / CSV File</h2>
      <form method="POST" action="<?php echo e(route('library.books.bulk-import.process')); ?>" enctype="multipart/form-data" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Select File <span class="text-red-500">*</span></label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                 class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
          <p class="text-xs text-slate-400 mt-1">Accepted: .xlsx, .xls, .csv — max 5 MB</p>
        </div>
        <button type="submit" class="btn btn-primary w-full">Import Books</button>
      </form>
    </div>

    
    <div class="card space-y-3">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Required Column Format</h2>
      <p class="text-sm text-slate-600">First row must be the header row. Supported columns:</p>
      <div class="overflow-x-auto">
        <table class="table-wrap w-full text-xs">
          <thead>
            <tr>
              <th class="th">Column Name</th>
              <th class="th">Required?</th>
              <th class="th">Example</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = [
              ['title', 'Yes', 'Wings of Fire'],
              ['author', 'No', 'A.P.J. Abdul Kalam'],
              ['publisher', 'No', 'Universities Press'],
              ['isbn', 'No', '9788173711466'],
              ['edition', 'No', '2nd'],
              ['language', 'No', 'English'],
              ['total_copies', 'No (default 1)', '3'],
              ['purchase_price', 'No', '250.00'],
              ['purchase_date', 'No', '2024-01-15'],
              ['location', 'No', 'Shelf A-3'],
              ['accession_number', 'No (auto-gen)', 'ACC-000042'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="tr">
              <td class="td font-mono text-indigo-700"><?php echo e($col[0]); ?></td>
              <td class="td"><?php echo e($col[1]); ?></td>
              <td class="td text-slate-500"><?php echo e($col[2]); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <p class="text-xs font-semibold text-slate-600 mb-1">Sample CSV:</p>
        <pre class="bg-slate-800 text-green-300 text-xs p-3 rounded-lg overflow-x-auto">title,author,publisher,isbn,total_copies,purchase_price,location
Wings of Fire,A.P.J. Abdul Kalam,Universities Press,9788173711466,2,250.00,Shelf A-1
Discovery of India,Jawaharlal Nehru,Penguin Books,,1,350.00,Shelf B-2</pre>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\books-bulk-import.blade.php ENDPATH**/ ?>