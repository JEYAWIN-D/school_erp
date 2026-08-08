<?php $__env->startSection('title', 'Communication Logs'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showBody: false, bodyHtml: '', bodySubject: '' }">

  
  <div x-show="showBody" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showBody=false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col" @click.stop>
      <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-semibold text-slate-700 truncate" x-text="bodySubject"></h3>
        <button @click="showBody=false" class="text-slate-400 hover:text-slate-600">&times;</button>
      </div>
      <div class="overflow-y-auto p-4 text-sm text-slate-700 prose max-w-none" x-html="bodyHtml"></div>
    </div>
  </div>
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Communication Logs</h1>
    <a href="<?php echo e(route('communication.index')); ?>" class="btn-secondary btn-sm">← Back</a>
  </div>

  
  <form method="GET" class="card flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Type</label>
      <select name="type" class="select">
        <option value="">All Types</option>
        <option value="email"           <?php echo e(request('type') === 'email' ? 'selected' : ''); ?>>Email</option>
        <option value="internal_notice" <?php echo e(request('type') === 'internal_notice' ? 'selected' : ''); ?>>Internal Notice</option>
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select name="status" class="select">
        <option value="">All Statuses</option>
        <option value="sent"    <?php echo e(request('status') === 'sent' ? 'selected' : ''); ?>>Sent</option>
        <option value="failed"  <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
        <option value="sending" <?php echo e(request('status') === 'sending' ? 'selected' : ''); ?>>Sending</option>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="<?php echo e(route('communication.logs')); ?>" class="btn-secondary btn-sm">Reset</a>
  </form>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full text-sm">
        <thead><tr>
          <th class="th">Date</th>
          <th class="th">Type</th>
          <th class="th">Subject</th>
          <th class="th">Audience</th>
          <th class="th text-center">Sent</th>
          <th class="th text-center">Failed</th>
          <th class="th">Sent By</th>
          <th class="th">Status</th>
          <th class="th"></th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td text-xs text-slate-500"><?php echo e(\Carbon\Carbon::parse($log->sent_at ?? $log->created_at)->format('d M Y H:i')); ?></td>
            <td class="td"><span class="badge-<?php echo e($log->type === 'email' ? 'blue' : 'purple'); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $log->type))); ?></span></td>
            <td class="td font-medium text-slate-700"><?php echo e($log->subject); ?></td>
            <td class="td text-xs text-slate-500">
              <?php echo e(ucfirst(str_replace('_', ' ', $log->audience_meta['type'] ?? '—'))); ?>

              <?php if(isset($log->audience_meta['class_id'])): ?> (Class <?php echo e($log->audience_meta['class_id']); ?>) <?php endif; ?>
            </td>
            <td class="td text-center text-green-600 font-medium"><?php echo e($log->sent_count); ?></td>
            <td class="td text-center <?php echo e($log->failed_count > 0 ? 'text-red-600 font-medium' : 'text-slate-400'); ?>"><?php echo e($log->failed_count); ?></td>
            <td class="td text-xs"><?php echo e($log->sender?->name ?? '—'); ?></td>
            <td class="td">
              <span class="badge-<?php echo e(match($log->status) { 'sent' => 'green', 'failed' => 'red', default => 'blue' }); ?>">
                <?php echo e(ucfirst($log->status)); ?>

              </span>
            </td>
            <td class="td">
              <?php if($log->body): ?>
              <button type="button" class="btn-xs btn-secondary"
                @click="bodyHtml = <?php echo e(Js::from($log->body)); ?>; bodySubject = <?php echo e(Js::from($log->subject)); ?>; showBody = true">
                View
              </button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="9" class="td text-center text-slate-400 py-6">No communication logs found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4"><?php echo e($logs->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\communication\logs.blade.php ENDPATH**/ ?>