<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; width: 226px; height: 340px; background: #fff; }
  .card { width: 226px; height: 340px; border: 2px solid #1e40af; border-radius: 8px; overflow: hidden; }
  .header { background: #1e40af; color: #fff; padding: 10px 8px 8px; text-align: center; }
  .school-name { font-size: 10px; font-weight: bold; letter-spacing: 0.3px; line-height: 1.3; }
  .card-title { font-size: 8px; background: #fbbf24; color: #1e3a8a; padding: 2px 8px; text-align: center; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }
  .body { padding: 10px 12px; }
  .photo-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px; }
  .photo { width: 60px; height: 72px; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; object-fit: cover; }
  .photo-placeholder { width: 60px; height: 72px; border: 1px solid #cbd5e1; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
  .photo-placeholder span { font-size: 22px; color: #94a3b8; font-weight: bold; }
  .info { flex: 1; }
  .name { font-size: 11px; font-weight: bold; color: #1e293b; line-height: 1.2; margin-bottom: 4px; }
  .field { font-size: 8.5px; color: #475569; line-height: 1.6; }
  .field strong { color: #1e293b; }
  .divider { border-top: 1px solid #e2e8f0; margin: 6px 0; }
  .qr-row { display: flex; align-items: center; justify-content: space-between; margin-top: 6px; }
  .qr-box svg { width: 60px; height: 60px; }
  .room-badge { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 6px 10px; text-align: center; }
  .room-no { font-size: 18px; font-weight: bold; color: #1d4ed8; line-height: 1; }
  .room-label { font-size: 7px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
  .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 5px 12px; text-align: center; }
  .footer-text { font-size: 7.5px; color: #64748b; }
  .hostel-badge { background: #1e40af; color: #fff; font-size: 8px; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <div class="school-name"><?php echo e($school['school_name'] ?? 'School Name'); ?></div>
    <div style="font-size:7.5px; color:#93c5fd; margin-top:2px;"><?php echo e($school['address'] ?? ''); ?></div>
  </div>
  <div class="card-title">Hostel Identity Card</div>

  <div class="body">
    <div class="photo-row">
      <?php if($allotment->student->photo ?? null): ?>
        <img src="<?php echo e(storage_path('app/public/' . $allotment->student->photo)); ?>" class="photo" alt="">
      <?php else: ?>
        <div class="photo-placeholder">
          <span><?php echo e(strtoupper(substr($allotment->student->first_name ?? 'S', 0, 1))); ?></span>
        </div>
      <?php endif; ?>
      <div class="info">
        <div class="name"><?php echo e($allotment->student->full_name); ?></div>
        <div class="field"><strong>Adm No:</strong> <?php echo e($allotment->student->admission_number); ?></div>
        <div class="field"><strong>Class:</strong> <?php echo e($allotment->student->currentClass?->name ?? '—'); ?></div>
        <div class="field"><strong>Blood:</strong> <?php echo e($allotment->student->blood_group ?? '—'); ?></div>
        <div class="field"><strong>Mobile:</strong> <?php echo e($allotment->student->phone ?? $allotment->student->guardian_mobile ?? '—'); ?></div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="field" style="margin-bottom:4px;">
      <strong>Hostel:</strong> <span class="hostel-badge"><?php echo e($allotment->hostel?->name ?? '—'); ?></span>
    </div>
    <div class="field"><strong>Allotted From:</strong> <?php echo e($allotment->allotment_date?->format('d M Y') ?? '—'); ?></div>
    <?php if($allotment->to_date): ?>
    <div class="field"><strong>Valid Until:</strong> <?php echo e($allotment->to_date->format('d M Y')); ?></div>
    <?php endif; ?>

    <div class="qr-row">
      <div class="qr-box"><?php echo $qr; ?></div>
      <div class="room-badge">
        <div class="room-no"><?php echo e($allotment->room?->room_number ?? '—'); ?></div>
        <div class="room-label">Room No.</div>
        <?php if($allotment->bed_number ?? null): ?>
        <div class="room-label" style="margin-top:3px; font-size:8px; color:#1e40af; font-weight:bold;">Bed: <?php echo e($allotment->bed_number); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="footer">
    <div class="footer-text">
      <?php echo e($school['school_name'] ?? ''); ?> &bull; <?php echo e($school['phone'] ?? ''); ?><br>
      If found, please contact the Warden at the address above.
    </div>
  </div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\hostel-id-card.blade.php ENDPATH**/ ?>