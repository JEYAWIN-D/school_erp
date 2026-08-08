<?php $__env->startSection('title', 'Notices'); ?>
<?php $__env->startSection('content'); ?>

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Notice Board</h2>

<div style="display: flex; flex-direction: column; gap: .75rem;">
  <?php $__empty_1 = true; $__currentLoopData = $notices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <?php $isRead = isset($n->read_at) && $n->read_at; ?>
  <div class="portal-card notice-card" data-notice-id="<?php echo e($n->id); ?>"
       style="cursor: pointer; transition: opacity .2s; <?php echo e($isRead ? 'opacity:.75;' : ''); ?>">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;">
      <div style="flex: 1; min-width: 0;">
        <div style="margin-bottom: .375rem; display: flex; align-items: center; flex-wrap: wrap; gap: .375rem;">
          <?php if(isset($n->priority) && $n->priority === 'urgent'): ?>
            <span class="badge-red">Urgent</span>
          <?php endif; ?>
          <?php if(!$isRead): ?>
            <span style="width:.5rem;height:.5rem;border-radius:50%;background:#3b82f6;display:inline-block;flex-shrink:0;" title="Unread"></span>
          <?php endif; ?>
          <span style="font-size: .9375rem; font-weight: <?php echo e($isRead ? '500' : '600'); ?>; color: #1e293b;"><?php echo e($n->title); ?></span>
        </div>
        <?php if(($n->body ?? null) || ($n->description ?? null)): ?>
          <p style="font-size: .8125rem; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo e(strip_tags($n->body ?? $n->description ?? '')); ?></p>
        <?php endif; ?>
      </div>
      <div style="display: flex; flex-direction: column; align-items: flex-end; gap: .25rem; flex-shrink: 0;">
        <span style="font-size: .75rem; color: #94a3b8; white-space: nowrap;"><?php echo e(\Carbon\Carbon::parse($n->created_at)->format('d M')); ?></span>
        <?php if($isRead): ?>
          <span style="font-size: .7rem; color: #94a3b8;">Read</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="portal-card" style="text-align: center; padding: 3rem 1rem;">
    <svg style="width: 3rem; height: 3rem; margin: 0 auto .875rem; color: #e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    <p style="color: #94a3b8; font-size: .875rem;">No notices at this time</p>
  </div>
  <?php endif; ?>
</div>

<?php if($notices->hasPages()): ?>
<div style="margin-top: 1.25rem;"><?php echo e($notices->links()); ?></div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]').content;
  document.querySelectorAll('.notice-card').forEach(function(card) {
    card.addEventListener('click', function() {
      var id = card.dataset.noticeId;
      var dot = card.querySelector('span[title="Unread"]');
      if (!dot) return; // already read
      fetch('/portal/notice/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' }
      }).then(function() {
        dot.remove();
        card.style.opacity = '.75';
        var title = card.querySelector('span[style*="font-weight: 600"]') || card.querySelector('span[style*="font-weight:600"]');
        if (title) title.style.fontWeight = '500';
      });
    });
  });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\notices.blade.php ENDPATH**/ ?>