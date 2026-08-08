<?php $__env->startSection('title', 'Vendors'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showForm: <?php echo e(old('name') ? 'true' : 'false'); ?>, editId: null, editData: {} }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Vendors</h1>
    <div class="flex gap-2">
      <a href="<?php echo e(route('inventory.index')); ?>" class="btn-sm btn-secondary">← Inventory</a>
      <button @click="showForm=!showForm" class="btn-primary btn-sm">+ Add Vendor</button>
    </div>
  </div>

  <?php if(session('success')): ?> <div class="alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

  
  <div x-show="showForm" x-collapse class="card">
    <h3 class="font-semibold text-slate-700 mb-4">Add Vendor</h3>
    <form method="POST" action="<?php echo e(route('inventory.vendors.store')); ?>" class="grid grid-cols-2 gap-4">
      <?php echo csrf_field(); ?>
      <div><label class="label">Vendor Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
      <div><label class="label">Code</label><input type="text" name="code" class="input"></div>
      <div><label class="label">Contact Person</label><input type="text" name="contact_person" class="input"></div>
      <div><label class="label">Phone</label><input type="tel" name="phone" class="input"></div>
      <div><label class="label">Email</label><input type="email" name="email" class="input"></div>
      <div><label class="label">GSTIN</label><input type="text" name="gstin" class="input"></div>
      <div><label class="label">PAN</label><input type="text" name="pan" class="input"></div>
      <div class="col-span-2"><label class="label">Address</label><textarea name="address" rows="2" class="input"></textarea></div>
      <div class="col-span-2 flex gap-2">
        <button type="submit" class="btn-primary">Add Vendor</button>
        <button type="button" @click="showForm=false" class="btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="flex gap-3">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input max-w-xs" placeholder="Search name or phone…">
    <button type="submit" class="btn-primary btn-sm">Search</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Vendor</th>
          <th class="th">Contact</th>
          <th class="th">Phone</th>
          <th class="th">Email</th>
          <th class="th">GSTIN</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="tr">
          <td class="td"><p class="font-medium"><?php echo e($v->name); ?></p><p class="text-xs text-slate-400"><?php echo e($v->code); ?></p></td>
          <td class="td"><?php echo e($v->contact_person ?? '—'); ?></td>
          <td class="td"><?php echo e($v->phone ?? '—'); ?></td>
          <td class="td text-xs"><?php echo e($v->email ?? '—'); ?></td>
          <td class="td font-mono text-xs"><?php echo e($v->gstin ?? '—'); ?></td>
          <td class="td"><?php if($v->is_active): ?> <span class="badge-green">Active</span> <?php else: ?> <span class="badge-slate">Inactive</span> <?php endif; ?></td>
          <td class="td">
            <button @click="editId=<?php echo e($v->id); ?>;editData=<?php echo e(json_encode($v)); ?>;showForm=false" class="btn-xs btn-secondary">Edit</button>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td class="td text-center text-slate-400" colspan="7">No vendors found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div><?php echo e($vendors->withQueryString()->links()); ?></div>

  
  <div x-show="editId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg space-y-4 max-h-screen overflow-y-auto" @click.stop>
      <h3 class="font-semibold text-slate-700">Edit Vendor</h3>
      <form method="POST" :action="'/inventory/vendors/' + editId" class="grid grid-cols-2 gap-3">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div><label class="label">Name</label><input type="text" name="name" :value="editData.name" class="input" required></div>
        <div><label class="label">Code</label><input type="text" name="code" :value="editData.code" class="input"></div>
        <div><label class="label">Contact Person</label><input type="text" name="contact_person" :value="editData.contact_person" class="input"></div>
        <div><label class="label">Phone</label><input type="tel" name="phone" :value="editData.phone" class="input"></div>
        <div><label class="label">Email</label><input type="email" name="email" :value="editData.email" class="input"></div>
        <div><label class="label">GSTIN</label><input type="text" name="gstin" :value="editData.gstin" class="input"></div>
        <div><label class="label">PAN</label><input type="text" name="pan" :value="editData.pan" class="input"></div>
        <div class="col-span-2"><label class="label">Address</label><textarea name="address" rows="2" class="input" x-text="editData.address"></textarea></div>
        <label class="flex items-center gap-2 text-sm col-span-2">
          <input type="checkbox" name="is_active" value="1" :checked="editData.is_active" class="rounded"> Active
        </label>
        <div class="col-span-2 flex gap-2">
          <button type="submit" class="btn-primary flex-1">Update</button>
          <button type="button" @click="editId=null" class="btn-secondary flex-1">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\inventory\vendors.blade.php ENDPATH**/ ?>