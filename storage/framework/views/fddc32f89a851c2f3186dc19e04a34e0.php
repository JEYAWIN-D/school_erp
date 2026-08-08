<?php $__env->startSection('title','Mess Expense Tracking'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Mess Expense Tracking</h1>
      <p class="page-subtitle">Record and monitor mess operational expenses</p>
    </div>
    <button @click="showAdd=!showAdd" class="btn btn-primary btn-sm">+ Add Expense</button>
  </div>

  <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

  
  <div x-show="showAdd" x-transition class="card space-y-4" style="display:none">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Record Mess Expense</h3>
    <form method="POST" action="<?php echo e(route('hostel.mess-expenses.store')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">All / General</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Expense Date <span class="text-red-500">*</span></label>
        <input type="date" name="expense_date" class="input" value="<?php echo e(date('Y-m-d')); ?>" required>
      </div>
      <div>
        <label class="label">Category <span class="text-red-500">*</span></label>
        <select name="category" class="select" required>
          <option value="ingredients">Ingredients / Raw Materials</option>
          <option value="vendor">Vendor / Caterer</option>
          <option value="utilities">Utilities (Gas, Electricity)</option>
          <option value="staff">Staff Wages</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="label">Description <span class="text-red-500">*</span></label>
        <input type="text" name="description" class="input" required placeholder="e.g. Vegetables purchase, LPG refill...">
      </div>
      <div>
        <label class="label">Amount (₹) <span class="text-red-500">*</span></label>
        <input type="number" name="amount" class="input" step="0.01" min="0" required>
      </div>
      <div>
        <label class="label">Vendor / Supplier</label>
        <input type="text" name="vendor" class="input" placeholder="Vendor name (optional)">
      </div>
      <div>
        <label class="label">Invoice Number</label>
        <input type="text" name="invoice_number" class="input" placeholder="Bill/Invoice #">
      </div>
      <div>
        <label class="label">Meal Type</label>
        <select name="meal_type" class="select">
          <option value="all">All Meals</option>
          <option value="breakfast">Breakfast</option>
          <option value="lunch">Lunch</option>
          <option value="snacks">Snacks</option>
          <option value="dinner">Dinner</option>
        </select>
      </div>
      <div class="md:col-span-3">
        <label class="label">Notes</label>
        <input type="text" name="notes" class="input" placeholder="Additional notes...">
      </div>
      <div class="md:col-span-3 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Expense</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap items-end">
      <select name="hostel_id" class="select w-40">
        <option value="">All Hostels</option>
        <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id')==$h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <select name="category" class="select w-40">
        <option value="">All Categories</option>
        <?php $__currentLoopData = ['ingredients'=>'Ingredients','vendor'=>'Vendor','utilities'=>'Utilities','staff'=>'Staff','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(request('category')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <input type="month" name="month" value="<?php echo e(request('month', date('Y-m'))); ?>" class="input w-40">
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  
  <div class="card-flat py-4 px-5 flex items-center justify-between">
    <div>
      <p class="text-xs text-slate-400">Total for filtered period</p>
      <p class="text-2xl font-bold text-slate-800">₹<?php echo e(number_format($monthTotal, 2)); ?></p>
    </div>
    <div class="text-xs text-slate-400"><?php echo e($expenses->total()); ?> records</div>
  </div>

  
  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        <?php $__currentLoopData = ['Date','Hostel','Category','Description','Vendor','Invoice','Amount','Meal','']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide"><?php echo e($h); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-600"><?php echo e(\Carbon\Carbon::parse($e->expense_date)->format('d M Y')); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($e->hostel_name ?? '—'); ?></td>
          <td class="px-4 py-3">
            <?php $catColors = ['ingredients'=>'green','vendor'=>'blue','utilities'=>'amber','staff'=>'indigo','other'=>'slate']; ?>
            <span class="badge-<?php echo e($catColors[$e->category] ?? 'slate'); ?> capitalize text-xs"><?php echo e(str_replace('_',' ',$e->category)); ?></span>
          </td>
          <td class="px-4 py-3 text-slate-800 font-medium max-w-xs truncate"><?php echo e($e->description); ?></td>
          <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($e->vendor ?? '—'); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs font-mono"><?php echo e($e->invoice_number ?? '—'); ?></td>
          <td class="px-4 py-3 font-semibold text-slate-800">₹<?php echo e(number_format($e->amount, 2)); ?></td>
          <td class="px-4 py-3 text-slate-400 text-xs capitalize"><?php echo e($e->meal_type ?? '—'); ?></td>
          <td class="px-4 py-3">
            <form method="POST" action="<?php echo e(route('hostel.mess-expenses.delete', $e->id)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Delete this expense?')">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No expense records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if($expenses->hasPages()): ?><div class="px-4 pb-3"><?php echo e($expenses->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\mess-expenses.blade.php ENDPATH**/ ?>