<?php $__env->startSection('title', $exam->title); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6"
     x-data="examRunner(<?php echo e($remaining); ?>, <?php echo e($attempt->id); ?>, '<?php echo e(csrf_token()); ?>')"
     x-init="startTimer()">

  
  <div class="card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title"><?php echo e($exam->title); ?></h1>
      <p class="page-subtitle"><?php echo e($exam->class?->name); ?> <?php if($exam->subject): ?> | <?php echo e($exam->subject->name); ?> <?php endif; ?></p>
    </div>
    <div class="text-center">
      <p class="text-xs text-slate-400 mb-1">Time Remaining</p>
      <p class="text-2xl font-bold font-mono" :class="timeLeft <= 60 ? 'text-red-600' : 'text-slate-800'" x-text="formatTime()"></p>
    </div>
  </div>

  <?php if($exam->instructions): ?>
    <div class="card-flat py-3 text-sm text-slate-600 bg-blue-50 border-blue-200">
      <strong>Instructions:</strong> <?php echo e($exam->instructions); ?>

    </div>
  <?php endif; ?>

  
  <form id="exam-form">
    <div class="space-y-4">
      <?php $__currentLoopData = $orderedQs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card" id="q-<?php echo e($q->id); ?>">
          <p class="font-semibold text-slate-800 mb-3"><?php echo e($idx + 1); ?>. <?php echo e($q->question); ?>

            <span class="text-xs text-slate-400 font-normal ml-2">(<?php echo e($q->marks); ?> mark<?php echo e($q->marks != 1 ? 's' : ''); ?>)</span>
          </p>

          <?php if($q->question_type === 'mcq'): ?>
            <?php $__currentLoopData = ['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($text): ?>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition mb-2">
                  <input type="radio" name="q_<?php echo e($q->id); ?>" value="<?php echo e($opt); ?>"
                         <?php if(isset($responses[$q->id]) && $responses[$q->id] === $opt): ?> checked <?php endif; ?>
                         @change="saveAnswer(<?php echo e($q->id); ?>, $el.value)" class="text-blue-600">
                  <span class="text-sm text-slate-700"><strong><?php echo e(strtoupper($opt)); ?>.</strong> <?php echo e($text); ?></span>
                </label>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          <?php elseif($q->question_type === 'true_false'): ?>
            <?php $__currentLoopData = ['true' => 'True', 'false' => 'False']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition mb-2">
                <input type="radio" name="q_<?php echo e($q->id); ?>" value="<?php echo e($val); ?>"
                       <?php if(isset($responses[$q->id]) && $responses[$q->id] === $val): ?> checked <?php endif; ?>
                       @change="saveAnswer(<?php echo e($q->id); ?>, $el.value)" class="text-blue-600">
                <span class="text-sm text-slate-700"><?php echo e($label); ?></span>
              </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          <?php elseif($q->question_type === 'fill_blank'): ?>
            <input type="text" name="q_<?php echo e($q->id); ?>" class="input"
                   value="<?php echo e($responses[$q->id] ?? ''); ?>"
                   placeholder="Type your answer…"
                   @change="saveAnswer(<?php echo e($q->id); ?>, $el.value)"
                   @keyup.debounce.500ms="saveAnswer(<?php echo e($q->id); ?>, $el.value)">

          <?php elseif($q->question_type === 'short_answer'): ?>
            <textarea name="q_<?php echo e($q->id); ?>" class="input" rows="3"
                      placeholder="Write your answer…"
                      @keyup.debounce.800ms="saveAnswer(<?php echo e($q->id); ?>, $el.value)"><?php echo e($responses[$q->id] ?? ''); ?></textarea>
          <?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </form>

  
  <div class="flex justify-end gap-3">
    <button type="button" @click="confirmSubmit(false)"
            class="btn btn-primary"
            :disabled="submitting">
      <span x-show="!submitting">Submit Exam</span>
      <span x-show="submitting">Submitting…</span>
    </button>
  </div>

</div>


<form id="submit-form" method="POST" action="<?php echo e(route('online-exams.submit', $attempt->id)); ?>" style="display:none">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="auto_submit" id="auto-submit-flag" value="0">
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function examRunner(remainingSeconds, attemptId, csrfToken) {
  return {
    timeLeft: remainingSeconds,
    submitting: false,
    timer: null,

    startTimer() {
      this.timer = setInterval(() => {
        this.timeLeft--;
        if (this.timeLeft <= 0) {
          clearInterval(this.timer);
          this.autoSubmit();
        }
      }, 1000);
    },

    formatTime() {
      const h = Math.floor(this.timeLeft / 3600);
      const m = Math.floor((this.timeLeft % 3600) / 60);
      const s = this.timeLeft % 60;
      return (h > 0 ? h + ':' : '') +
             String(m).padStart(2, '0') + ':' +
             String(s).padStart(2, '0');
    },

    async saveAnswer(qId, answer) {
      try {
        await fetch('/online-exams/attempt/' + attemptId + '/answer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ question_id: qId, answer: answer }),
        });
      } catch (e) {
        console.warn('Save failed:', e);
      }
    },

    confirmSubmit(isAuto) {
      if (!isAuto && !confirm('Are you sure you want to submit the exam? You cannot change answers after submission.')) return;
      this.submitting = true;
      clearInterval(this.timer);
      document.getElementById('auto-submit-flag').value = isAuto ? '1' : '0';
      document.getElementById('submit-form').submit();
    },

    autoSubmit() {
      this.confirmSubmit(true);
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\online-exams\take.blade.php ENDPATH**/ ?>