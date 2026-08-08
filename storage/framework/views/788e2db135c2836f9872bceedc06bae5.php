<?php $__env->startSection('title', 'Discussion Forum'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Forum — <?php echo e($course->title); ?></h1>
    <a href="<?php echo e(route('lms.courses.show', $course->id)); ?>" class="btn-secondary btn-sm">← Course</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Post a Question</h3>
      <form method="POST" action="<?php echo e(route('lms.forum.thread', $course->id)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label">Title</label>
          <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="What's your question?" class="input w-full" required>
        </div>
        <div>
          <label class="label">Details</label>
          <textarea name="body" rows="4" class="input w-full" placeholder="Describe your question in detail..." required><?php echo e(old('body')); ?></textarea>
        </div>
        <button type="submit" class="btn-primary w-full">Post Question</button>
      </form>
    </div>

    
    <div class="lg:col-span-2 space-y-4">
      <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="card" x-data="{ replyOpen: false }">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <?php if($thread->is_answered): ?>
                <span class="badge-green text-xs">Answered</span>
              <?php endif; ?>
              <h3 class="font-semibold text-slate-800"><?php echo e($thread->title); ?></h3>
            </div>
            <p class="text-sm text-slate-600 mt-1"><?php echo e($thread->body); ?></p>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
              <span><?php echo e($thread->author?->name ?? 'User'); ?></span>
              <span>&bull;</span>
              <span><?php echo e($thread->created_at->diffForHumans()); ?></span>
              <span>&bull;</span>
              <span><?php echo e($thread->replies_count); ?> replies</span>
            </div>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <button @click="replyOpen = !replyOpen" class="btn-xs btn-secondary">Reply</button>
            <?php if(auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD'])): ?>
            <form method="POST" action="<?php echo e(route('lms.forum.hide', $thread->id)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
              <button type="submit" onclick="return confirm('Hide this thread?')" class="btn-xs text-red-500 border border-red-200 rounded px-1.5 py-0.5 hover:bg-red-50">Hide</button>
            </form>
            <?php endif; ?>
          </div>
        </div>

        
        <?php if($thread->replies->count()): ?>
        <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
          <?php $__currentLoopData = $thread->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-start gap-2 <?php echo e($reply->is_answer ? 'bg-green-50 rounded-xl p-2 -mx-1' : ''); ?>">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5">
              <?php echo e(strtoupper(substr($reply->author?->name ?? 'U', 0, 1))); ?>

            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-slate-600"><?php echo e($reply->author?->name ?? 'User'); ?></span>
                <?php if($reply->is_answer): ?> <span class="badge-green text-xs">✓ Accepted Answer</span> <?php endif; ?>
                <span class="text-xs text-slate-400"><?php echo e($reply->created_at->diffForHumans()); ?></span>
              </div>
              <p class="text-sm text-slate-700 mt-0.5"><?php echo e($reply->body); ?></p>
              <?php if(!$reply->is_answer && auth()->user()->hasRole(['Super Admin', 'School Admin', 'Teacher', 'HOD'])): ?>
              <form method="POST" action="<?php echo e(route('lms.forum.answer', $reply->id)); ?>" class="mt-1">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <button type="submit" class="text-xs text-green-600 hover:underline">Mark as Answer</button>
              </form>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        
        <div x-show="replyOpen" x-transition class="mt-3 pt-3 border-t border-slate-100">
          <form method="POST" action="<?php echo e(route('lms.forum.reply', $thread->id)); ?>" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <textarea name="body" rows="2" class="input flex-1 text-sm" placeholder="Write your reply..." required></textarea>
            <button type="submit" class="btn-sm btn-primary self-end">Post</button>
          </form>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="card text-center py-10">
        <p class="text-slate-400">No discussions yet. Be the first to ask a question!</p>
      </div>
      <?php endif; ?>

      <div><?php echo e($threads->links()); ?></div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\lms\discussion\forum.blade.php ENDPATH**/ ?>