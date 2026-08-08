<?php
  $hc       = $template?->id_card_header_color ?? '#1e3a5f';
  $tc       = $template?->id_card_text_color ?? '#1e3a5f';
  $bg       = $template?->id_card_bg_color ?? '#ffffff';
  $hdrText  = $template?->id_card_header_text ?: ($school?->school_name ?? 'DASA EduERP');
  $ftrText  = $template?->id_card_footer_text ?: (($school?->school_name ?? '') . ($school?->phone ? ' | ' . $school->phone : ''));
  $showPhoto = $template?->id_card_show_photo ?? true;
  $showBlood = $template?->id_card_show_blood_group ?? true;
  $showDob   = $template?->id_card_show_dob ?? true;
  $showQr    = $template?->id_card_show_qr ?? true;
  $showMob   = $template?->id_card_show_mobile ?? true;
  $showAddr  = $template?->id_card_show_address ?? false;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #fff; }
.id-card { width: 85mm; border: 2px solid <?php echo e($hc); ?>; border-radius: 8px; overflow: hidden; page-break-inside: avoid; margin: 6px; display: inline-block; vertical-align: top; background: <?php echo e($bg); ?>; }
.card-header { background: <?php echo e($hc); ?>; color: white; text-align: center; padding: 6px 5px; }
.school-name { font-size: 9px; font-weight: bold; letter-spacing: 0.3px; }
.card-type { font-size: 7px; margin-top: 1px; letter-spacing: 1px; opacity: 0.9; }
.card-body { padding: 7px 8px; background: <?php echo e($bg); ?>; }
.photo-row { display: flex; gap: 7px; align-items: flex-start; }
.photo-box { width: 28mm; height: 33mm; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f5f5f5; flex-shrink: 0; font-size: 7px; color: #999; text-align: center; overflow: hidden; }
.photo-box img { width: 100%; height: 100%; object-fit: cover; }
.info-area { flex: 1; min-width: 0; }
.student-name { font-size: 11px; font-weight: bold; color: <?php echo e($tc); ?>; word-break: break-word; }
.info-row { font-size: 8px; margin-top: 3px; color: #444; }
.lbl { color: #888; }
.qr-row { display: flex; justify-content: flex-end; margin-top: 5px; }
.qr-row img { width: 28px; height: 28px; }
.card-footer { background: <?php echo e($hc); ?>; color: white; text-align: center; padding: 4px 5px; font-size: 7px; }
.validity { font-size: 6px; margin-top: 1px; opacity: 0.8; }
</style>
</head>
<body>
<?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $enrollment = $student->currentEnrollment; ?>
<div class="id-card">
  <div class="card-header">
    <div class="school-name"><?php echo e($hdrText); ?></div>
    <div class="card-type">STUDENT IDENTITY CARD</div>
  </div>
  <div class="card-body">
    <div class="photo-row">
      <?php if($showPhoto): ?>
      <div class="photo-box">
        <?php if($student->photo): ?>
          <img src="<?php echo e(public_path('storage/' . $student->photo)); ?>" alt="Photo">
        <?php else: ?>
          Photo
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="info-area">
        <div class="student-name"><?php echo e($student->full_name); ?></div>
        <div class="info-row"><span class="lbl">Adm No: </span><?php echo e($student->admission_number); ?></div>
        <div class="info-row"><span class="lbl">Class: </span><?php echo e($enrollment?->class?->name); ?> <?php echo e($enrollment?->section?->name); ?></div>
        <?php if($showDob): ?>
        <div class="info-row"><span class="lbl">DOB: </span><?php echo e($student->dob?->format('d/m/Y')); ?></div>
        <?php endif; ?>
        <?php if($showBlood): ?>
        <div class="info-row"><span class="lbl">Blood: </span><?php echo e($student->blood_group ?? '—'); ?></div>
        <?php endif; ?>
        <?php if($showMob): ?>
        <div class="info-row"><span class="lbl">Mobile: </span><?php echo e($student->father_mobile ?? $student->mobile ?? '—'); ?></div>
        <?php endif; ?>
        <?php if($showAddr && $student->residential_address): ?>
        <div class="info-row"><span class="lbl">Addr: </span><?php echo e(\Illuminate\Support\Str::limit($student->residential_address, 40)); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <?php if($showQr && !empty($qrCodes[$student->id])): ?>
    <div class="qr-row">
      <img src="data:image/png;base64,<?php echo e($qrCodes[$student->id]); ?>" alt="QR">
    </div>
    <?php endif; ?>
  </div>
  <div class="card-footer">
    <div><?php echo e($ftrText); ?></div>
    <div class="validity">Valid: <?php echo e($currentYear?->name ?? ''); ?></div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\student-id-card.blade.php ENDPATH**/ ?>