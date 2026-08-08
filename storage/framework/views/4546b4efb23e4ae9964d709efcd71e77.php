<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; width: 180px; height: 120px; }
  .label { width: 180px; height: 120px; border: 1px solid #cbd5e1; border-radius: 4px; display: flex; align-items: center; gap: 8px; padding: 6px; }
  .qr { flex-shrink: 0; }
  .qr svg { width: 80px; height: 80px; }
  .info { flex: 1; min-width: 0; }
  .school { font-size: 6px; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px; }
  .accession { font-size: 12px; font-weight: bold; color: #1e293b; margin-top: 2px; }
  .title { font-size: 7px; color: #475569; margin-top: 2px; line-height: 1.3; }
  .isbn { font-size: 6px; color: #94a3b8; margin-top: 2px; }
</style>
</head>
<body>
<div class="label">
  <div class="qr"><?php echo $qr; ?></div>
  <div class="info">
    <div class="school"><?php echo e($school?->school_name ?? 'Library'); ?></div>
    <div class="accession"><?php echo e($book->accession_number ?? ('BOOK-'.$book->id)); ?></div>
    <div class="title"><?php echo e(\Illuminate\Support\Str::limit($book->title, 40)); ?></div>
    <?php if($book->author): ?>
      <div class="isbn">by <?php echo e(\Illuminate\Support\Str::limit($book->author, 25)); ?></div>
    <?php endif; ?>
    <?php if($book->isbn): ?>
      <div class="isbn">ISBN: <?php echo e($book->isbn); ?></div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\book-qr-label.blade.php ENDPATH**/ ?>