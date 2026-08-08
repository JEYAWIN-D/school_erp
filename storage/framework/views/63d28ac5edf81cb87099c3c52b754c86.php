<?php $__env->startSection('title', 'Admission Enquiries'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5" x-data="bulkSelect()">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="page-title">Admission Enquiries</h1>
      <p class="page-subtitle">Manage and track all admission enquiries</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <a href="<?php echo e(route('admissions.form-builder')); ?>" class="btn btn-secondary btn-sm">Form Builder</a>
      <a href="<?php echo e(route('admissions.applications')); ?>" class="btn btn-secondary btn-sm">Applications</a>
      <a href="<?php echo e(route('admissions.application-form')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Application Form
      </a>
      <a href="<?php echo e(route('admissions.export', request()->query())); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
      </a>
      <a href="<?php echo e(route('admissions.bulk-import')); ?>" class="btn btn-secondary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
        Import
      </a>
      <a href="<?php echo e(route('admissions.create')); ?>" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Enquiry
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
    <?php $__currentLoopData = [
      ['label' => 'Total',     'value' => $stats['total'],     'badge' => 'badge-slate',  'href' => route('admissions.index')],
      ['label' => 'New',       'value' => $stats['new'],       'badge' => 'badge-blue',   'href' => route('admissions.index', ['status'=>'new'])],
      ['label' => 'Follow Up', 'value' => $stats['follow_up'], 'badge' => 'badge-amber',  'href' => route('admissions.index', ['status'=>'follow_up'])],
      ['label' => 'Converted', 'value' => $stats['converted'], 'badge' => 'badge-green',  'href' => route('admissions.index', ['status'=>'converted'])],
      ['label' => 'Lost',      'value' => $stats['lost'],      'badge' => 'badge-rose',   'href' => route('admissions.index', ['status'=>'lost'])],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e($stat['href']); ?>"
         class="card-sm hover:shadow-sm hover:border-slate-300 transition-all text-center group">
        <p class="text-2xl font-bold text-slate-900 leading-none" style="font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.03em">
          <?php echo e($stat['value']); ?>

        </p>
        <span class="<?php echo e($stat['badge']); ?> mt-2"><?php echo e($stat['label']); ?></span>
      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <form method="GET" class="filter-bar" id="filter-form">
    <div>
      <label class="label">Search</label>
      <input type="text" name="search" value="<?php echo e(request('search')); ?>"
             placeholder="Name, mobile, enquiry no…" class="input w-52">
    </div>
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All Classes</option>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cls->id); ?>" <?php if(request('class_id') == $cls->id): echo 'selected'; endif; ?>><?php echo e($cls->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select w-32">
        <option value="">All Status</option>
        <option value="new"         <?php if(request('status') === 'new'): echo 'selected'; endif; ?>>New</option>
        <option value="follow_up"   <?php if(request('status') === 'follow_up'): echo 'selected'; endif; ?>>Follow Up</option>
        <option value="converted"   <?php if(request('status') === 'converted'): echo 'selected'; endif; ?>>Converted</option>
        <option value="lost"        <?php if(request('status') === 'lost'): echo 'selected'; endif; ?>>Lost</option>
        <option value="application" <?php if(request('status') === 'application'): echo 'selected'; endif; ?>>Application</option>
      </select>
    </div>
    <div>
      <label class="label">From</label>
      <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="input w-36">
    </div>
    <div>
      <label class="label">To</label>
      <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="input w-36">
    </div>
    <div class="flex items-end gap-2 pb-0">
      <button type="submit" class="btn btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
        Filter
      </button>
      <?php if(request()->hasAny(['search', 'class_id', 'status', 'date_from', 'date_to'])): ?>
        <a href="<?php echo e(route('admissions.index')); ?>" class="btn btn-ghost btn-sm text-slate-500">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  
  <div x-show="selected.length > 0"
       x-transition:enter="transition duration-150"
       x-transition:enter-start="opacity-0 -translate-y-1"
       x-transition:enter-end="opacity-100 translate-y-0"
       class="card-flat border-indigo-200 bg-indigo-50 flex flex-wrap items-center gap-3">
    <div class="flex items-center gap-2">
      <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center" x-text="selected.length"></span>
      <span class="text-sm font-semibold text-indigo-800" x-text="selected.length + ' enqu' + (selected.length === 1 ? 'iry' : 'iries') + ' selected'"></span>
    </div>
    <form method="POST" action="<?php echo e(route('admissions.bulk-status')); ?>" x-ref="bulkForm" @submit.prevent="submitBulk($refs.bulkForm)">
      <?php echo csrf_field(); ?>
      <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
      </template>
      <div class="flex items-center gap-2">
        <select name="status" class="select-sm w-36">
          <option value="new">Mark: New</option>
          <option value="follow_up">Mark: Follow Up</option>
          <option value="converted">Mark: Converted</option>
          <option value="lost">Mark: Lost</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
      </div>
    </form>
    <button @click="selected = []" class="btn btn-ghost btn-sm text-indigo-700 ml-auto">Clear Selection</button>
  </div>

  
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th w-10">
            <input type="checkbox" class="rounded border-slate-300 w-3.5 h-3.5"
                   @change="toggleAll($event)"
                   :checked="selected.length === <?php echo e($enquiries->count()); ?> && <?php echo e($enquiries->count()); ?> > 0">
          </th>
          <th class="th">Enquiry #</th>
          <th class="th">Student</th>
          <th class="th">Class</th>
          <th class="th">Parent / Mobile</th>
          <th class="th">Source</th>
          <th class="th">Status</th>
          <th class="th">Follow Up</th>
          <th class="th">Date</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr" :class="selected.includes(<?php echo e($enq->id); ?>) ? 'bg-indigo-50/50' : ''">
            <td class="td">
              <input type="checkbox" class="rounded border-slate-300 w-3.5 h-3.5" :value="<?php echo e($enq->id); ?>" x-model="selected">
            </td>
            <td class="td">
              <span class="font-mono text-xs font-semibold text-indigo-600"><?php echo e($enq->enquiry_number); ?></span>
            </td>
            <td class="td">
              <p class="font-semibold text-slate-800 text-sm leading-tight"><?php echo e($enq->student_name); ?></p>
              <?php if($enq->gender): ?>
                <span class="text-xs text-slate-400"><?php echo e(ucfirst($enq->gender)); ?></span>
              <?php endif; ?>
              <?php if($enq->dob): ?>
                <p class="text-xs text-slate-400">Age <?php echo e($enq->dob->age); ?> yrs</p>
              <?php endif; ?>
            </td>
            <td class="td text-sm text-slate-700"><?php echo e($enq->class?->name ?? '—'); ?></td>
            <td class="td">
              <p class="font-semibold text-slate-800 text-sm leading-tight"><?php echo e($enq->parent_name); ?></p>
              <p class="text-xs text-slate-400 font-mono"><?php echo e($enq->parent_mobile); ?></p>
            </td>
            <td class="td">
              <?php if($enq->source): ?>
                <span class="badge-slate capitalize"><?php echo e(str_replace('_', ' ', $enq->source)); ?></span>
              <?php else: ?>
                <span class="text-slate-300 text-sm">—</span>
              <?php endif; ?>
            </td>
            <td class="td">
              <span class="<?php echo e($enq->status_color); ?>"><?php echo e($enq->status_label); ?></span>
            </td>
            <td class="td text-xs <?php echo e($enq->follow_up_date && $enq->follow_up_date->isPast() ? 'text-red-500 font-semibold' : 'text-slate-500'); ?>">
              <?php echo e($enq->follow_up_date?->format('d M Y') ?? '—'); ?>

            </td>
            <td class="td text-xs text-slate-400"><?php echo e($enq->created_at->format('d M Y')); ?></td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-0.5">
                <a href="<?php echo e(route('admissions.show', $enq->id)); ?>" class="btn-icon" title="View">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="<?php echo e(route('admissions.edit', $enq->id)); ?>" class="btn-icon" title="Edit">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <?php if(in_array($enq->status, ['converted', 'confirmed'])): ?>
                  <a href="<?php echo e(route('admissions.confirmation-letter', $enq->id)); ?>" target="_blank"
                     class="btn-icon text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50" title="Confirmation Letter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  </a>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('admissions.destroy', $enq->id)); ?>"
                      onsubmit="return confirm('Delete this enquiry?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn-icon text-red-400 hover:text-red-600 hover:bg-red-50" title="Delete">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="10" class="td">
              <div class="py-14 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 mb-1">No enquiries found</p>
                <p class="text-xs text-slate-400 mb-4">Try adjusting your filters or create a new enquiry.</p>
                <a href="<?php echo e(route('admissions.create')); ?>" class="btn btn-primary btn-sm">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                  New Enquiry
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <?php if($enquiries->hasPages()): ?>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
      <span>
        Showing <span class="font-semibold text-slate-700"><?php echo e($enquiries->firstItem()); ?></span>–<span class="font-semibold text-slate-700"><?php echo e($enquiries->lastItem()); ?></span>
        of <span class="font-semibold text-slate-700"><?php echo e($enquiries->total()); ?></span> enquiries
      </span>
      <div><?php echo e($enquiries->links()); ?></div>
    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function bulkSelect() {
  return {
    selected: [],
    toggleAll(e) {
      const ids = <?php echo json_encode($enquiries->pluck('id'), 15, 512) ?>;
      this.selected = e.target.checked ? ids : [];
    },
    submitBulk(form) {
      if (!this.selected.length) return;
      form.submit();
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\index.blade.php ENDPATH**/ ?>