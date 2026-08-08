<?php $__env->startSection('title', 'My Timetable'); ?>
<?php $__env->startSection('content'); ?>

<div style="margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
  <h2 style="font-size:1.0625rem;font-weight:700;color:#1e293b;margin:0;">Weekly Timetable</h2>
  <?php if($enrollment): ?>
    <span style="font-size:.75rem;font-weight:600;color:#6366f1;background:#eef2ff;padding:.25rem .75rem;border-radius:9999px;">
      <?php echo e($enrollment->class_name); ?><?php echo e($enrollment->section_name ? ' · '.$enrollment->section_name : ''); ?>

    </span>
  <?php endif; ?>
</div>

<?php if(!$enrollment): ?>
  <div class="portal-card" style="text-align:center;padding:3rem 1rem;color:#94a3b8;">
    <svg style="width:3rem;height:3rem;margin:0 auto 1rem;opacity:.35;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <p style="font-size:.9375rem;font-weight:500;">No enrollment found for current academic year.</p>
  </div>
<?php else: ?>
  <?php
    $allEmpty = true;
    foreach($timetable as $day) {
      if($day['periods']->isNotEmpty()) { $allEmpty = false; break; }
    }
    $dayColors = [
      1 => ['#eff6ff','#2563eb','#dbeafe'],
      2 => ['#f5f3ff','#7c3aed','#ede9fe'],
      3 => ['#f0fdf4','#16a34a','#dcfce7'],
      4 => ['#fffbeb','#d97706','#fef3c7'],
      5 => ['#fef2f2','#dc2626','#fee2e2'],
      6 => ['#f0f9ff','#0891b2','#e0f2fe'],
    ];
    $typeIcons = [
      'break' => '☕',
      'lunch' => '🍽',
      'free'  => '📖',
      'class' => '',
    ];
  ?>

  <?php if($allEmpty): ?>
    <div class="portal-card" style="text-align:center;padding:3rem 1rem;color:#94a3b8;">
      <svg style="width:3rem;height:3rem;margin:0 auto 1rem;opacity:.35;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/></svg>
      <p style="font-size:.9375rem;font-weight:500;">Timetable not published yet.</p>
      <p style="font-size:.8125rem;margin-top:.375rem;">Please check back later or contact your class teacher.</p>
    </div>
  <?php else: ?>
    
    <style>
      .tt-tabs{display:flex;gap:.5rem;overflow-x:auto;margin-bottom:1rem;padding-bottom:.25rem;-webkit-overflow-scrolling:touch;}
      .tt-tabs::-webkit-scrollbar{height:3px;}
      .tt-tabs::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:9999px;}
      .tt-tab{flex-shrink:0;padding:.4rem .875rem;border-radius:9999px;font-size:.8rem;font-weight:600;cursor:pointer;border:2px solid transparent;transition:all .15s;}
      .tt-tab.active{border-color:currentColor;}
      .tt-day{display:none;}
      .tt-day.active{display:block;}
      .tt-period{display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 0;border-bottom:1px solid #f1f5f9;}
      .tt-period:last-child{border-bottom:none;}
      .tt-time{flex-shrink:0;width:3.25rem;text-align:right;}
      .tt-dot{width:2px;min-height:2.25rem;align-self:stretch;border-radius:9999px;flex-shrink:0;}
      .tt-now-badge{font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:9999px;margin-left:.375rem;}
    </style>

    <?php $nowTime = now()->format('H:i:s'); ?>

    <div class="tt-tabs" id="tt-tabs">
      <?php $__currentLoopData = $timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dow => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php [$bg,$fg,$light] = $dayColors[$dow] ?? ['#f8fafc','#475569','#e2e8f0']; ?>
        <?php if($day['periods']->isNotEmpty()): ?>
          <button class="tt-tab <?php echo e($dow === $todayDow ? 'active' : ''); ?>"
            data-day="<?php echo e($dow); ?>"
            style="background:<?php echo e($dow === $todayDow ? $light : '#f8fafc'); ?>;color:<?php echo e($fg); ?>;border-color:<?php echo e($dow === $todayDow ? $fg : 'transparent'); ?>;"
            onclick="switchDay(<?php echo e($dow); ?>)">
            <?php echo e($day['label']); ?>

            <?php if($dow === $todayDow): ?><span style="margin-left:.25rem;font-size:.65rem;opacity:.7;">(Today)</span><?php endif; ?>
          </button>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php $__currentLoopData = $timetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dow => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if($day['periods']->isNotEmpty()): ?>
        <?php [$bg,$fg,$light] = $dayColors[$dow] ?? ['#f8fafc','#475569','#e2e8f0']; ?>
        <div class="portal-card tt-day <?php echo e($dow === $todayDow ? 'active' : ''); ?>" id="tt-day-<?php echo e($dow); ?>">
          <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.875rem;">
            <div style="width:.375rem;height:1.25rem;border-radius:9999px;background:<?php echo e($fg); ?>;flex-shrink:0;"></div>
            <p style="font-size:.9375rem;font-weight:700;color:#1e293b;"><?php echo e($day['label']); ?></p>
            <span style="font-size:.7rem;font-weight:600;color:<?php echo e($fg); ?>;background:<?php echo e($light); ?>;padding:.15rem .5rem;border-radius:9999px;"><?php echo e($day['periods']->count()); ?> period<?php echo e($day['periods']->count() !== 1 ? 's' : ''); ?></span>
          </div>

          <?php $__currentLoopData = $day['periods']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $st = $period->start_time ? substr($period->start_time,0,8) : null;
              $et = $period->end_time   ? substr($period->end_time,0,8)   : null;
              $isCurrent = $dow === $todayDow && $st && $et && $nowTime >= $st && $nowTime <= $et;
              $isBreak   = in_array($period->period_type ?? 'class', ['break','lunch','free']);
              $icon      = $typeIcons[$period->period_type ?? 'class'] ?? '';
            ?>
            <div class="tt-period" style="<?php echo e($isCurrent ? 'background:'.$light.';border-radius:.75rem;padding:.875rem .75rem;margin:0 -.75rem;' : ''); ?>">
              <div class="tt-time">
                <div style="font-size:.75rem;font-weight:700;color:<?php echo e($isCurrent ? $fg : '#475569'); ?>;">
                  <?php echo e($st ? \Carbon\Carbon::parse($st)->format('H:i') : '—'); ?>

                </div>
                <div style="font-size:.65rem;color:#94a3b8;">
                  <?php echo e($et ? \Carbon\Carbon::parse($et)->format('H:i') : ''); ?>

                </div>
              </div>
              <div class="tt-dot" style="background:<?php echo e($isCurrent ? $fg : '#e2e8f0'); ?>;margin-top:.25rem;"></div>
              <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:.375rem;flex-wrap:wrap;">
                  <?php if($icon): ?><span style="font-size:.875rem;"><?php echo e($icon); ?></span><?php endif; ?>
                  <p style="font-size:.875rem;font-weight:<?php echo e($isBreak ? '500' : '600'); ?>;color:<?php echo e($isBreak ? '#94a3b8' : '#1e293b'); ?>;">
                    <?php echo e($period->subject); ?>

                  </p>
                  <?php if($isCurrent): ?>
                    <span class="tt-now-badge" style="background:<?php echo e($fg); ?>;color:#fff;">Now</span>
                  <?php endif; ?>
                </div>
                <?php if(($period->teacher_first ?? null) || ($period->teacher_last ?? null)): ?>
                  <p style="font-size:.75rem;color:#64748b;margin-top:.15rem;">
                    <?php echo e(trim(($period->teacher_first ?? '').' '.($period->teacher_last ?? ''))); ?>

                  </p>
                <?php endif; ?>
                <?php if($period->room ?? null): ?>
                  <p style="font-size:.7rem;color:#94a3b8;margin-top:.1rem;">
                    <svg style="width:.75rem;height:.75rem;display:inline;vertical-align:middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <?php echo e($period->room); ?>

                  </p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <script>
    function switchDay(dow) {
      document.querySelectorAll('.tt-day').forEach(function(el){ el.classList.remove('active'); });
      var active = document.getElementById('tt-day-' + dow);
      if (active) active.classList.add('active');

      document.querySelectorAll('.tt-tab').forEach(function(btn){
        var d = parseInt(btn.dataset.day);
        var colors = {
          1:['#eff6ff','#2563eb','#dbeafe'],
          2:['#f5f3ff','#7c3aed','#ede9fe'],
          3:['#f0fdf4','#16a34a','#dcfce7'],
          4:['#fffbeb','#d97706','#fef3c7'],
          5:['#fef2f2','#dc2626','#fee2e2'],
          6:['#f0f9ff','#0891b2','#e0f2fe']
        };
        var c = colors[d] || ['#f8fafc','#475569','#e2e8f0'];
        btn.classList.remove('active');
        btn.style.background = '#f8fafc';
        btn.style.borderColor = 'transparent';
        if (d === dow) {
          btn.classList.add('active');
          btn.style.background = c[2];
          btn.style.borderColor = c[1];
        }
      });
    }
    </script>
  <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\student\timetable.blade.php ENDPATH**/ ?>