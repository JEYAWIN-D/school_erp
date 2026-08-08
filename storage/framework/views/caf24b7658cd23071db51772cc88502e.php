<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
  .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 8px; margin-bottom: 12px; }
  .school-name { font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
  .exam-title { font-size: 12px; font-weight: bold; margin-top: 4px; text-transform: uppercase; }
  .meta-row { display: flex; justify-content: space-between; font-size: 10px; margin: 6px 0; }
  .instructions { border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px; background: #f8fafc; margin-bottom: 12px; font-size: 9.5px; }
  .instructions strong { display: block; margin-bottom: 2px; font-size: 10px; }
  .section-header { background: #1e293b; color: white; padding: 3px 8px; font-weight: bold; font-size: 10px; margin: 10px 0 6px; border-radius: 2px; }
  .question { margin-bottom: 10px; }
  .q-header { display: flex; justify-content: space-between; margin-bottom: 3px; }
  .q-num { font-weight: bold; font-size: 11px; }
  .q-marks { color: #64748b; font-size: 10px; }
  .q-text { font-size: 11px; }
  .q-type { display: inline-block; border: 1px solid #cbd5e1; border-radius: 3px; padding: 0px 5px; font-size: 9px; color: #64748b; margin-right: 4px; }
  .options { margin-top: 4px; padding-left: 12px; }
  .option { font-size: 10px; color: #475569; margin: 1px 0; }
  .answer-space { border-bottom: 1px dashed #cbd5e1; height: 16px; margin: 2px 0; }
  .footer { border-top: 1px solid #cbd5e1; margin-top: 16px; padding-top: 6px; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
  .page-break { page-break-before: always; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name"><?php echo e($school?->school_name ?? 'School'); ?></div>
  <?php if($school?->address): ?><div style="font-size:9px;color:#64748b;margin-top:2px;"><?php echo e($school->address); ?></div><?php endif; ?>
  <div class="exam-title"><?php echo e($request->paper_title); ?></div>
  <?php if($exam): ?><div style="font-size:10px;margin-top:2px;"><?php echo e($exam->name); ?></div><?php endif; ?>
</div>

<div class="meta-row">
  <span>Total Marks: <strong><?php echo e($totalMarks); ?></strong></span>
  <span>Duration: <strong><?php echo e($duration); ?> Minutes</strong></span>
  <span>Date: _________________</span>
</div>
<div class="meta-row">
  <span>Name: _______________________________________________</span>
  <span>Class: ________________</span>
  <span>Roll No.: ________</span>
</div>

<?php if($request->instructions): ?>
<div class="instructions">
  <strong>Instructions:</strong>
  <?php echo e($request->instructions); ?>

</div>
<?php endif; ?>

<?php
  $grouped = $questions->groupBy('question_type');
  $sectionLetters = ['A','B','C','D','E','F'];
  $typeLabels = ['mcq'=>'Multiple Choice Questions','true_false'=>'True or False','short'=>'Short Answer Questions','long'=>'Long Answer Questions','descriptive'=>'Descriptive / Essay Questions'];
  $sectionIdx = 0;
  $qNum = 1;
?>

<?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $typeQuestions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="section-header">
  SECTION <?php echo e($sectionLetters[$sectionIdx++]); ?>: <?php echo e(strtoupper($typeLabels[$type] ?? str_replace('_',' ',$type))); ?>

  (<?php echo e($typeQuestions->count()); ?> Questions &times; marks as specified)
</div>

<?php $__currentLoopData = $typeQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="question">
  <div class="q-header">
    <div>
      <span class="q-num">Q<?php echo e($qNum++); ?>.</span>
      <?php if($q->chapter): ?> <span class="q-type"><?php echo e($q->chapter); ?></span><?php endif; ?>
    </div>
    <div class="q-marks">[<?php echo e($q->marks); ?> Mark<?php echo e($q->marks != 1 ? 's' : ''); ?>]</div>
  </div>
  <div class="q-text"><?php echo e($q->question_text); ?></div>
  <?php if($type === 'mcq' && $q->options): ?>
    <?php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); ?>
    <?php if(is_array($opts)): ?>
    <div class="options">
      <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="option"><?php echo e(chr(65+$i)); ?>) <?php echo e($opt['text'] ?? ''); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
  <?php elseif($type === 'true_false'): ?>
    <div class="options"><span class="option">( ) True &nbsp;&nbsp;&nbsp;&nbsp; ( ) False</span></div>
  <?php elseif($type === 'short'): ?>
    <div class="answer-space"></div><div class="answer-space"></div>
  <?php elseif(in_array($type, ['long','descriptive'])): ?>
    <?php for($i=0;$i<5;$i++): ?><div class="answer-space"></div><?php endfor; ?>
  <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div class="footer">
  <span><?php echo e($school?->school_name ?? ''); ?></span>
  <span><?php echo e($request->paper_title); ?> — Total: <?php echo e($totalMarks); ?> Marks</span>
  <span>Generated: <?php echo e(now()->format('d M Y')); ?></span>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\pdf\assembled-question-paper.blade.php ENDPATH**/ ?>