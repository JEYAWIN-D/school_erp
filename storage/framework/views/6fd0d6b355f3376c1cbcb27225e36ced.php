<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }
  .page { padding: 24px; max-width: 500px; margin: 0 auto; }

  .school-header { text-align: center; border-bottom: 2px solid #4338ca; padding-bottom: 12px; margin-bottom: 16px; }
  .school-logo { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; margin-bottom: 6px; }
  .school-name { font-size: 16px; font-weight: bold; color: #1e1b4b; letter-spacing: 0.5px; }
  .school-address { font-size: 10px; color: #64748b; margin-top: 2px; }

  .ticket-title { text-align: center; background: #4338ca; color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 14px; font-weight: bold; letter-spacing: 1px; margin-bottom: 16px; text-transform: uppercase; }

  .info-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  .info-row { display: table-row; }
  .info-label { display: table-cell; width: 38%; font-weight: bold; color: #475569; padding: 4px 6px 4px 0; font-size: 11px; vertical-align: top; }
  .info-value { display: table-cell; color: #0f172a; padding: 4px 0; font-size: 12px; }

  .section-title { font-size: 11px; font-weight: bold; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; margin-top: 14px; }

  .test-box { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 12px; margin-bottom: 14px; }
  .test-box .test-date { font-size: 15px; font-weight: bold; color: #312e81; }
  .test-box .test-time { font-size: 13px; color: #4338ca; margin-top: 2px; }
  .test-box .test-venue { font-size: 12px; color: #475569; margin-top: 4px; }

  .instructions { font-size: 10px; color: #475569; line-height: 1.6; }
  .instructions li { margin-bottom: 3px; padding-left: 2px; }

  .footer-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 14px; }
  .signature-block { font-size: 10px; color: #64748b; text-align: center; }
  .signature-line { width: 120px; border-bottom: 1px solid #94a3b8; margin-bottom: 4px; height: 24px; }
  .qr-block { text-align: center; }
  .qr-block img { width: 80px; height: 80px; }
  .qr-caption { font-size: 9px; color: #94a3b8; margin-top: 2px; }

  .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
  .badge-blue { background: #dbeafe; color: #1d4ed8; }

  .watermark { position: fixed; bottom: 20px; right: 20px; font-size: 9px; color: #cbd5e1; }
</style>
</head>
<body>
<div class="page">

  
  <div class="school-header">
    <?php if($school && $school->logo): ?>
      <div><img src="<?php echo e(public_path('storage/' . $school->logo)); ?>" class="school-logo" alt="Logo"></div>
    <?php endif; ?>
    <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
    <div class="school-address"><?php echo e($school->address ?? ''); ?><?php echo e(($school->city ?? '') ? ', ' . $school->city : ''); ?></div>
  </div>

  
  <div class="ticket-title">Entrance Test Admit Card</div>

  
  <div class="section-title">Candidate Details</div>
  <div class="info-grid">
    <div class="info-row">
      <div class="info-label">Enquiry No.</div>
      <div class="info-value"><strong><?php echo e($enquiry->enquiry_number); ?></strong></div>
    </div>
    <div class="info-row">
      <div class="info-label">Candidate Name</div>
      <div class="info-value"><strong><?php echo e(strtoupper($enquiry->student_name)); ?></strong></div>
    </div>
    <div class="info-row">
      <div class="info-label">Date of Birth</div>
      <div class="info-value"><?php echo e($enquiry->dob ? \Carbon\Carbon::parse($enquiry->dob)->format('d M Y') : '—'); ?></div>
    </div>
    <div class="info-row">
      <div class="info-label">Class Applied For</div>
      <div class="info-value"><?php echo e($enquiry->class?->name ?? '—'); ?></div>
    </div>
    <div class="info-row">
      <div class="info-label">Parent / Guardian</div>
      <div class="info-value"><?php echo e($enquiry->parent_name); ?></div>
    </div>
    <div class="info-row">
      <div class="info-label">Contact</div>
      <div class="info-value"><?php echo e($enquiry->parent_mobile); ?></div>
    </div>
    <div class="info-row">
      <div class="info-label">Academic Year</div>
      <div class="info-value"><?php echo e($enquiry->academicYear?->name ?? '—'); ?></div>
    </div>
  </div>

  
  <div class="section-title">Test Schedule</div>
  <div class="test-box">
    <div class="test-date"><?php echo e($enquiry->entrance_test_date->format('l, d F Y')); ?></div>
    <?php if($enquiry->entrance_test_time): ?>
      <div class="test-time">Time: <?php echo e(\Carbon\Carbon::createFromFormat('H:i', $enquiry->entrance_test_time)->format('h:i A')); ?></div>
    <?php endif; ?>
    <?php if($enquiry->entrance_test_venue): ?>
      <div class="test-venue">Venue: <?php echo e($enquiry->entrance_test_venue); ?></div>
    <?php endif; ?>
    <?php if($enquiry->entrance_test_invigilator): ?>
      <div class="test-venue" style="margin-top:4px;">Invigilator: <?php echo e($enquiry->entrance_test_invigilator); ?></div>
    <?php endif; ?>
  </div>

  
  <div class="section-title">Instructions</div>
  <ol class="instructions">
    <li>Bring this admit card to the examination hall. Entry will not be allowed without it.</li>
    <li>Report at the venue at least 15 minutes before the scheduled time.</li>
    <li>Carry a valid photo identity proof (Aadhaar / Birth Certificate).</li>
    <li>Mobile phones and electronic devices are not permitted inside the hall.</li>
    <li>Bring required stationery (pen, pencil, eraser). Stationery will not be provided.</li>
    <li>Candidates found using unfair means will be disqualified.</li>
  </ol>

  
  <div class="footer-row">
    <div class="signature-block">
      <div class="signature-line"></div>
      <div>Parent / Guardian Signature</div>
    </div>
    <?php if($qrCode): ?>
    <div class="qr-block">
      <img src="data:image/png;base64,<?php echo e($qrCode); ?>" alt="QR">
      <div class="qr-caption">Scan to verify</div>
    </div>
    <?php endif; ?>
    <div class="signature-block">
      <div class="signature-line"></div>
      <div>Principal / Authorised Signatory</div>
    </div>
  </div>

</div>
<div class="watermark">Generated: <?php echo e(now()->format('d M Y, h:i A')); ?></div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\entrance-hall-ticket.blade.php ENDPATH**/ ?>