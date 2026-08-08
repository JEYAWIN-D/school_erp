<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; }
  .header { text-align: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; }
  .school-name { font-size: 12px; font-weight: bold; color: #1e40af; }
  .subtitle { font-size: 9px; color: #64748b; }
  .labels-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
  .label { border: 1px solid #cbd5e1; border-radius: 3px; display: flex; align-items: center; gap: 5px; padding: 4px; page-break-inside: avoid; }
  .qr svg { width: 70px; height: 70px; }
  .info { flex: 1; min-width: 0; }
  .accession { font-size: 10px; font-weight: bold; color: #1e293b; }
  .title { font-size: 7px; color: #475569; line-height: 1.3; margin-top: 1px; }
  .isbn { font-size: 6px; color: #94a3b8; margin-top: 1px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'Library'); ?> — Book QR Labels</div>
  <div class="subtitle">Total: <?php echo e($booksWithQr->count()); ?> labels | Generated: <?php echo e(now()->format('d M Y')); ?></div>
</div>

<div class="labels-grid">
  <?php $__currentLoopData = $booksWithQr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="label">
    <div class="qr"><?php echo $item['qr']; ?></div>
    <div class="info">
      <div class="accession"><?php echo e($item['qrData']); ?></div>
      <div class="title"><?php echo e(\Illuminate\Support\Str::limit($item['book']->title, 35)); ?></div>
      <?php if($item['book']->author): ?>
        <div class="isbn"><?php echo e(\Illuminate\Support\Str::limit($item['book']->author, 20)); ?></div>
      <?php endif; ?>
      <?php if($item['book']->isbn): ?>
        <div class="isbn">ISBN: <?php echo e($item['book']->isbn); ?></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\book-qr-labels-bulk.blade.php ENDPATH**/ ?>