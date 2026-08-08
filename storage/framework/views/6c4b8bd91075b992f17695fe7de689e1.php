<?php $__env->startSection('title','Edit Question'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-2xl">
  <h1 class="page-title">Edit Question</h1>
  <form method="POST" action="<?php echo e(route('examinations.qbank.update',$question->id)); ?>" class="card space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="grid grid-cols-2 gap-3">
      <div><label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if($question->subject_id==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Class</label>
        <select name="class_id" class="select">
          <option value="">All</option>
          <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if($question->class_id==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <div><label class="label">Question Text <span class="text-red-500">*</span></label>
      <textarea name="question_text" class="input h-24" required><?php echo e(old('question_text',$question->question_text)); ?></textarea>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div><label class="label">Type</label>
        <select name="question_type" class="select">
          <?php $__currentLoopData = ['mcq'=>'MCQ','short_answer'=>'Short Answer','long_answer'=>'Long Answer','true_false'=>'True/False','fill_blank'=>'Fill Blank']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>" <?php if($question->question_type===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Difficulty</label>
        <select name="difficulty" class="select">
          <?php $__currentLoopData = ['easy'=>'Easy','medium'=>'Medium','hard'=>'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>" <?php if($question->difficulty===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div><label class="label">Marks</label>
        <input type="number" name="marks" class="input" value="<?php echo e(old('marks',$question->marks)); ?>" min="0.5" step="0.5">
      </div>
    </div>
    <div><label class="label">Answer / Correct Option</label>
      <input type="text" name="answer" class="input" value="<?php echo e(old('answer',$question->answer)); ?>">
    </div>
    <div><label class="label">Chapter / Unit</label>
      <input type="text" name="chapter" class="input" value="<?php echo e(old('chapter',$question->chapter)); ?>">
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="<?php echo e(route('examinations.qbank')); ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\examinations\question-edit.blade.php ENDPATH**/ ?>