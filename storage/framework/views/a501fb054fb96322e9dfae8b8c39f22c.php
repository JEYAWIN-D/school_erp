<?php $__env->startSection('title', 'Mess Feedback'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ addOpen: false }">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">Mess Feedback</h1>
      <p class="page-subtitle">Student ratings and comments per meal</p>
    </div>
    <button @click="addOpen = !addOpen" class="btn btn-primary btn-sm">+ Record Feedback</button>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <?php $__currentLoopData = ['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'snacks' => 'Snacks', 'dinner' => 'Dinner']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php $avg = $avgRatings[$key] ?? null; ?>
      <div class="card-flat py-4 text-center">
        <p class="text-xs text-slate-400 mb-1"><?php echo e($label); ?> (This Week)</p>
        <?php if($avg): ?>
          <div class="flex items-center justify-center gap-1">
            <?php for($i = 1; $i <= 5; $i++): ?>
              <svg class="w-4 h-4 <?php echo e($i <= round($avg->avg_rating) ? 'text-amber-400' : 'text-slate-200'); ?>" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <?php endfor; ?>
          </div>
          <p class="text-lg font-bold text-slate-700 mt-1"><?php echo e(number_format($avg->avg_rating, 1)); ?></p>
          <p class="text-xs text-slate-400"><?php echo e($avg->total); ?> reviews</p>
        <?php else: ?>
          <p class="text-slate-300 text-2xl font-bold">—</p>
          <p class="text-xs text-slate-400">No feedback yet</p>
        <?php endif; ?>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <div x-show="addOpen" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Record Feedback</h3>
    <form method="POST" action="<?php echo e(route('hostel.mess-feedback.store')); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select Student</option>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($s->id); ?>"><?php echo e($s->full_name); ?> (<?php echo e($s->admission_number); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">Select Hostel</option>
          <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="label">Date <span class="text-red-500">*</span></label>
        <input type="date" name="feedback_date" class="input" value="<?php echo e(today()->toDateString()); ?>" max="<?php echo e(today()->toDateString()); ?>" required>
      </div>
      <div>
        <label class="label">Meal <span class="text-red-500">*</span></label>
        <select name="meal_type" class="select" required>
          <option value="breakfast">Breakfast</option>
          <option value="lunch">Lunch</option>
          <option value="snacks">Snacks</option>
          <option value="dinner">Dinner</option>
        </select>
      </div>
      <div>
        <label class="label">Rating (1–5) <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-2 mt-1" x-data="{ rating: 0 }">
          <?php for($i = 1; $i <= 5; $i++): ?>
            <button type="button" @click="rating = <?php echo e($i); ?>"
                    :class="rating >= <?php echo e($i); ?> ? 'text-amber-400' : 'text-slate-200'"
                    class="text-3xl leading-none focus:outline-none transition-colors">★</button>
          <?php endfor; ?>
          <input type="hidden" name="rating" :value="rating">
          <span class="text-sm text-slate-500 ml-2" x-text="rating > 0 ? rating + '/5' : 'Select'"></span>
        </div>
      </div>
      <div>
        <label class="label">Anonymous?</label>
        <div class="flex items-center gap-2 mt-2">
          <input type="checkbox" name="is_anonymous" value="1" id="anon" class="rounded">
          <label for="anon" class="text-sm text-slate-600">Submit anonymously</label>
        </div>
      </div>
      <div class="sm:col-span-3">
        <label class="label">Comment</label>
        <textarea name="comment" class="input" rows="2" placeholder="Optional feedback comment…"></textarea>
      </div>
      <div class="sm:col-span-3 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Feedback</button>
        <button type="button" @click="addOpen = false" class="btn btn-ghost btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label">Hostel</label>
      <select name="hostel_id" class="select w-40">
        <option value="">All Hostels</option>
        <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($h->id); ?>" <?php if(request('hostel_id') == $h->id): echo 'selected'; endif; ?>><?php echo e($h->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">Meal</label>
      <select name="meal_type" class="select w-32">
        <option value="">All Meals</option>
        <?php $__currentLoopData = ['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'snacks' => 'Snacks', 'dinner' => 'Dinner']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($k); ?>" <?php if(request('meal_type') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="label">From</label>
      <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="input w-36">
    </div>
    <div>
      <label class="label">To</label>
      <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="input w-36">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <?php if(request()->hasAny(['hostel_id', 'meal_type', 'date_from', 'date_to'])): ?>
      <a href="<?php echo e(route('hostel.mess-feedback')); ?>" class="btn btn-ghost btn-sm">Clear</a>
    <?php endif; ?>
  </form>

  
  <div class="table-wrap">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Date</th>
          <th class="th">Meal</th>
          <th class="th">Student</th>
          <th class="th">Rating</th>
          <th class="th">Comment</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $feedback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="tr">
            <td class="td text-xs text-slate-500"><?php echo e($fb->feedback_date->format('d M Y')); ?></td>
            <td class="td capitalize">
              <span class="badge-slate"><?php echo e($fb->meal_type); ?></span>
            </td>
            <td class="td">
              <?php if($fb->is_anonymous): ?>
                <span class="text-slate-400 italic text-sm">Anonymous</span>
              <?php else: ?>
                <span class="text-slate-700 text-sm"><?php echo e($fb->student?->full_name); ?></span>
              <?php endif; ?>
            </td>
            <td class="td">
              <div class="flex items-center gap-0.5">
                <?php for($i = 1; $i <= 5; $i++): ?>
                  <svg class="w-4 h-4 <?php echo e($i <= $fb->rating ? 'text-amber-400' : 'text-slate-200'); ?>" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <?php endfor; ?>
                <span class="text-xs text-slate-500 ml-1"><?php echo e($fb->rating); ?>/5</span>
              </div>
            </td>
            <td class="td text-sm text-slate-600"><?php echo e($fb->comment ?? '—'); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="td text-center py-10 text-slate-400">No feedback records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($feedback->hasPages()): ?>
    <div class="flex justify-between items-center text-sm text-slate-500">
      <span>Showing <?php echo e($feedback->firstItem()); ?>–<?php echo e($feedback->lastItem()); ?> of <?php echo e($feedback->total()); ?></span>
      <?php echo e($feedback->links()); ?>

    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\hostel\mess-feedback.blade.php ENDPATH**/ ?>