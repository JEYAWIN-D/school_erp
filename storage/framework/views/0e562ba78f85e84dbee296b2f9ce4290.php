<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

  .page { padding: 10mm; }
  .cards-grid { display: flex; flex-wrap: wrap; gap: 8px; }

  .id-card {
    width: 85mm;
    height: 54mm;
    border: 1.5px solid #4338ca;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    display: inline-block;
    vertical-align: top;
    page-break-inside: avoid;
  }

  .card-header {
    background: linear-gradient(135deg, #4338ca 0%, #7c3aed 100%);
    color: #fff;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .card-logo { width: 20px; height: 20px; border-radius: 50%; object-fit: cover; background: #fff; }
  .school-name { font-size: 8px; font-weight: bold; letter-spacing: 0.3px; }
  .school-sub { font-size: 7px; opacity: 0.8; }
  .card-type { font-size: 7px; font-weight: bold; background: rgba(255,255,255,0.2); padding: 1px 5px; border-radius: 8px; margin-left: auto; }

  .card-body { display: flex; padding: 6px 8px; gap: 7px; }
  .photo-wrap { flex-shrink: 0; }
  .photo-wrap img { width: 28mm; height: 28mm; object-fit: cover; border-radius: 3px; border: 1px solid #e2e8f0; }
  .no-photo { width: 28mm; height: 28mm; background: #f1f5f9; border-radius: 3px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #94a3b8; text-align: center; }

  .info { flex: 1; }
  .emp-name { font-size: 11px; font-weight: bold; color: #1e1b4b; margin-bottom: 3px; }
  .emp-designation { font-size: 9px; color: #4338ca; font-weight: 600; margin-bottom: 5px; }
  .info-row { font-size: 8px; color: #475569; margin-bottom: 2px; }
  .info-row strong { color: #1e293b; }

  .card-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 4px 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .blood-group { background: #fee2e2; color: #dc2626; font-size: 8px; font-weight: bold; padding: 1px 6px; border-radius: 8px; }
  .validity { font-size: 7px; color: #64748b; }
  .qr-img { width: 16mm; height: 16mm; }
  .emp-id { font-size: 8px; font-weight: bold; color: #1e1b4b; font-family: monospace; }
</style>
</head>
<body>
<div class="page">
  <div class="cards-grid">
    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="id-card">
      <div class="card-header">
        <?php if($school && $school->logo): ?>
          <img src="<?php echo e(public_path('storage/' . $school->logo)); ?>" class="card-logo" alt="">
        <?php endif; ?>
        <div>
          <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
          <div class="school-sub"><?php echo e($school->city ?? ''); ?></div>
        </div>
        <div class="card-type">STAFF ID</div>
      </div>

      <div class="card-body">
        <div class="photo-wrap">
          <?php if($emp->photo): ?>
            <img src="<?php echo e(public_path('storage/' . $emp->photo)); ?>" alt="Photo">
          <?php else: ?>
            <div class="no-photo">No Photo</div>
          <?php endif; ?>
        </div>
        <div class="info">
          <div class="emp-name"><?php echo e(strtoupper($emp->name)); ?></div>
          <div class="emp-designation"><?php echo e($emp->designation ?? '—'); ?></div>
          <div class="info-row">Dept: <strong><?php echo e($emp->department?->name ?? '—'); ?></strong></div>
          <div class="info-row">Emp ID: <strong><?php echo e($emp->employee_id ?? 'EMP-' . str_pad($emp->id, 4, '0', STR_PAD_LEFT)); ?></strong></div>
          <div class="info-row">Mobile: <strong><?php echo e($emp->mobile ?? '—'); ?></strong></div>
        </div>
        <?php if($emp->_qrCode): ?>
        <div>
          <img src="data:image/png;base64,<?php echo e($emp->_qrCode); ?>" class="qr-img" alt="QR">
        </div>
        <?php endif; ?>
      </div>

      <div class="card-footer">
        <?php if($emp->blood_group): ?>
          <span class="blood-group"><?php echo e($emp->blood_group); ?></span>
        <?php else: ?>
          <span></span>
        <?php endif; ?>
        <span class="validity">Valid until: <?php echo e($validUntil); ?></span>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\staff-id-cards.blade.php ENDPATH**/ ?>