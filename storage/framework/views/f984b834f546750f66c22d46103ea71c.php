<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Experience Certificate — <?php echo e($employee->full_name); ?></title>
<style>
  @page { margin: 25mm 20mm; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.8; background: #fff; }
  .letterhead { text-align: center; border-bottom: 3px double #1e3a8a; padding-bottom: 12px; margin-bottom: 25px; }
  .school-title { font-size: 22px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 1px; }
  .school-sub { font-size: 11px; color: #475569; margin-top: 3px; font-weight: 600; }
  .meta-bar { display: table; width: 100%; margin-bottom: 30px; font-size: 11px; color: #475569; }
  .meta-left { display: table-cell; text-align: left; }
  .meta-right { display: table-cell; text-align: right; }
  .to-whom { text-align: center; font-size: 12px; font-weight: 700; color: #475569; letter-spacing: 2px; margin-bottom: 10px; }
  .doc-title { font-size: 18px; font-weight: 800; color: #1e3a8a; text-align: center; text-decoration: underline; letter-spacing: 1.5px; margin-bottom: 30px; text-transform: uppercase; }
  .cert-body { font-size: 13px; line-height: 2.2; text-align: justify; margin-bottom: 35px; }
  .cert-body strong { color: #0f172a; font-weight: 700; }
  .signature-section { margin-top: 60px; text-align: right; }
  .sig-line { display: inline-block; border-top: 1.5px solid #334155; width: 220px; padding-top: 5px; font-size: 11px; font-weight: 700; color: #0f172a; text-align: center; }
</style>
</head>
<body>

  
  <div class="letterhead">
    <div class="school-title"><?php echo e($school->school_name ?? config('app.name', 'DASA EduERP')); ?></div>
    <div class="school-sub"><?php echo e($school->address ?? 'Main Campus, Educational Zone'); ?> &bull; Ph: <?php echo e($school->phone ?? '+91 9876543210'); ?></div>
  </div>

  
  <div class="meta-bar">
    <div class="meta-left"><strong>Ref No:</strong> EXP-<?php echo e(date('Y')); ?>-<?php echo e(str_pad($employee->id, 4, '0', STR_PAD_LEFT)); ?></div>
    <div class="meta-right"><strong>Date:</strong> <?php echo e(now()->format('F d, Y')); ?></div>
  </div>

  <div class="to-whom">TO WHOM IT MAY CONCERN</div>
  <div class="doc-title">EXPERIENCE CERTIFICATE</div>

  <div class="cert-body">
    This is to certify that <strong><?php echo e($employee->full_name); ?></strong> (Emp Code: <strong><?php echo e($employee->employee_code); ?></strong>) has served as <strong><?php echo e(is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member')); ?></strong> in the <strong><?php echo e(is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General')); ?></strong> Department at <strong><?php echo e($school->school_name ?? 'DASA EduERP'); ?></strong>.
  </div>

  <div class="cert-body">
    <strong>Period of Service:</strong> <?php echo e($employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('F d, Y') : '01 January 2024')); ?> to <?php echo e($employee->date_of_leaving ? \Carbon\Carbon::parse($employee->date_of_leaving)->format('F d, Y') : 'Present'); ?>.
  </div>

  <div class="cert-body">
    During <?php echo e($employee->gender === 'female' ? 'her' : 'his'); ?> tenure with us, <?php echo e($employee->gender === 'female' ? 'she' : 'he'); ?> has demonstrated commendable professional commitment, diligence, and integrity. <?php echo e(ucfirst($employee->gender === 'female' ? 'her' : 'his')); ?> character and conduct have been exemplary.
  </div>

  <div class="cert-body">
    We wish <?php echo e($employee->gender === 'female' ? 'her' : 'him'); ?> continued success in all <?php echo e($employee->gender === 'female' ? 'her' : 'his'); ?> future endeavours.
  </div>

  
  <div class="signature-section">
    <div class="sig-line">
      Principal / Director<br>
      <span style="font-size: 10px; font-weight: normal; color: #475569;"><?php echo e($school->school_name ?? 'DASA EduERP'); ?></span>
    </div>
  </div>

</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\experience-certificate.blade.php ENDPATH**/ ?>