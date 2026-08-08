<?php $__env->startSection('title', 'Attendance — ' . $child->first_name); ?>
<?php $__env->startSection('content'); ?>


<?php if($children->count() > 1): ?>
<div style="margin-bottom: 1rem; display: flex; flex-wrap: wrap; gap: .5rem;">
  <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('portal.parent.attendance', ['student_id' => $c->id, 'month' => $month, 'year' => $year])); ?>"
       style="display: inline-flex; align-items: center; padding: .35rem 1rem; border-radius: 9999px; font-size: .8125rem; font-weight: 500; text-decoration: none; border: 1.5px solid <?php echo e($c->id == $child->id ? '#2563eb' : '#e2e8f0'); ?>; background: <?php echo e($c->id == $child->id ? '#2563eb' : '#fff'); ?>; color: <?php echo e($c->id == $child->id ? '#fff' : '#475569'); ?>;">
      <?php echo e($c->first_name); ?>

    </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php if(isset($ytd) && $ytd['total'] > 0): ?>
<div class="portal-card" style="margin-bottom: 1rem; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #fff;">
  <div style="font-size: .8rem; font-weight: 600; opacity: .75; margin-bottom: .75rem; letter-spacing: .05em; text-transform: uppercase;"><?php echo e($child->first_name); ?>'s Year-to-Date Attendance</div>
  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem;">
    <div style="text-align: center;"><div style="font-size: 1.5rem; font-weight: 700;"><?php echo e($ytd['present']); ?></div><div style="font-size: .7rem; opacity: .75; margin-top: .2rem;">Present</div></div>
    <div style="text-align: center;"><div style="font-size: 1.5rem; font-weight: 700; color: #fca5a5;"><?php echo e($ytd['absent']); ?></div><div style="font-size: .7rem; opacity: .75; margin-top: .2rem;">Absent</div></div>
    <div style="text-align: center;"><div style="font-size: 1.5rem; font-weight: 700; color: #fde68a;"><?php echo e($ytd['leave']); ?></div><div style="font-size: .7rem; opacity: .75; margin-top: .2rem;">Leave</div></div>
    <div style="text-align: center;"><div style="font-size: 1.5rem; font-weight: 700; color: <?php echo e(($ytd['pct'] ?? 0) >= 75 ? '#86efac' : '#fca5a5'); ?>;"><?php echo e($ytd['pct'] ?? '—'); ?>%</div><div style="font-size: .7rem; opacity: .75; margin-top: .2rem;">Overall</div></div>
  </div>
  <?php if(($ytd['pct'] ?? 0) < 75): ?>
  <div style="margin-top: .75rem; background: rgba(239,68,68,.2); border-radius: .5rem; padding: .5rem .75rem; font-size: .75rem;">
    ⚠️ <?php echo e($child->first_name); ?>'s attendance is below 75%. Please ensure regular attendance.
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="portal-card">
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: .5rem;">
    <h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b;"><?php echo e($from->format('F Y')); ?> — <?php echo e($child->first_name); ?></h2>
    <div style="display: flex; gap: .5rem;">
      <a href="<?php echo e(route('portal.parent.attendance', ['student_id' => $child->id, 'month' => $from->copy()->subMonth()->month, 'year' => $from->copy()->subMonth()->year])); ?>"
         style="padding: .35rem .75rem; font-size: .8125rem; border: 1px solid #e2e8f0; border-radius: .5rem; text-decoration: none; color: #475569; background: #fff;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">&#8249; Prev</a>
      <?php if($from->lt(now()->startOfMonth())): ?>
      <a href="<?php echo e(route('portal.parent.attendance', ['student_id' => $child->id, 'month' => $from->copy()->addMonth()->month, 'year' => $from->copy()->addMonth()->year])); ?>"
         style="padding: .35rem .75rem; font-size: .8125rem; border: 1px solid #e2e8f0; border-radius: .5rem; text-decoration: none; color: #475569; background: #fff;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Next &#8250;</a>
      <?php endif; ?>
    </div>
  </div>

  
  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-bottom: 1.25rem;">
    <div style="text-align: center; padding: .75rem .5rem; background: #f0fdf4; border-radius: .75rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: #16a34a;"><?php echo e($attData['present']); ?></div>
      <div style="font-size: .72rem; color: #16a34a; font-weight: 500;">Present</div>
    </div>
    <div style="text-align: center; padding: .75rem .5rem; background: #fef2f2; border-radius: .75rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: #dc2626;"><?php echo e($attData['absent']); ?></div>
      <div style="font-size: .72rem; color: #dc2626; font-weight: 500;">Absent</div>
    </div>
    <div style="text-align: center; padding: .75rem .5rem; background: #eff6ff; border-radius: .75rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: #2563eb;"><?php echo e($attData['leave']); ?></div>
      <div style="font-size: .72rem; color: #2563eb; font-weight: 500;">Leave</div>
    </div>
    <div style="text-align: center; padding: .75rem .5rem; background: <?php echo e(($percentage ?? 0) >= 75 ? '#f0fdf4' : '#fef2f2'); ?>; border-radius: .75rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: <?php echo e(($percentage ?? 0) >= 75 ? '#16a34a' : '#dc2626'); ?>;"><?php echo e($percentage ?? '—'); ?>%</div>
      <div style="font-size: .72rem; color: #94a3b8; font-weight: 500;">Attendance</div>
    </div>
  </div>

  
  <?php
    $weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    $startDay = $from->dayOfWeek;
    $daysInMonth = $from->daysInMonth;
  ?>
  <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: .25rem; text-align: center;">
    <?php $__currentLoopData = $weekdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div style="font-size: .72rem; font-weight: 600; color: #94a3b8; padding: .4rem 0;"><?php echo e($wd); ?></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php for($i = 0; $i < $startDay; $i++): ?><div></div><?php endfor; ?>
    <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
      <?php
        $dateStr = $from->copy()->day($day)->toDateString();
        $rec = $attData['records'][$dateStr] ?? null;
        $status = $rec?->status ?? null;
        $bg = match($status) {
          'present'  => '#dcfce7', 'late' => '#fef3c7',
          'absent'   => '#fee2e2', 'on_leave' => '#dbeafe',
          'holiday'  => '#f1f5f9', default => '#f8fafc',
        };
        $color = match($status) {
          'present'  => '#16a34a', 'late' => '#d97706',
          'absent'   => '#dc2626', 'on_leave' => '#2563eb',
          'holiday'  => '#94a3b8', default => '#cbd5e1',
        };
        $label = match($status) {
          'present' => 'P', 'late' => 'L', 'absent' => 'A', 'on_leave' => 'OL', 'holiday' => 'H', default => $day,
        };
      ?>
      <div style="aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-radius: .5rem; font-size: .72rem; font-weight: 600; background: <?php echo e($bg); ?>; color: <?php echo e($color); ?>; min-height: 2rem;"><?php echo e($label); ?></div>
    <?php endfor; ?>
  </div>

  
  <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: .75rem; font-size: .75rem; color: #64748b;">
    <span style="display: flex; align-items: center; gap: .375rem;"><span style="width: .875rem; height: .875rem; border-radius: .25rem; background: #dcfce7; display: inline-block;"></span> P = Present</span>
    <span style="display: flex; align-items: center; gap: .375rem;"><span style="width: .875rem; height: .875rem; border-radius: .25rem; background: #fee2e2; display: inline-block;"></span> A = Absent</span>
    <span style="display: flex; align-items: center; gap: .375rem;"><span style="width: .875rem; height: .875rem; border-radius: .25rem; background: #fef3c7; display: inline-block;"></span> L = Late</span>
    <span style="display: flex; align-items: center; gap: .375rem;"><span style="width: .875rem; height: .875rem; border-radius: .25rem; background: #dbeafe; display: inline-block;"></span> OL = On Leave</span>
  </div>
</div>


<div class="portal-card" style="margin-top: 1rem;">
  <div class="section-title" style="margin-bottom: 1rem;">
    <svg style="width:1rem;height:1rem;color:#2563eb" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Apply for Leave — <?php echo e($child->first_name); ?>

  </div>

  <?php if(session('success')): ?>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.625rem;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1rem;">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('portal.leave.submit')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="student_id" value="<?php echo e($child->id); ?>">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:.875rem;">
      <div>
        <label style="display:block;font-size:.75rem;font-weight:600;color:#475569;margin-bottom:.375rem;">Leave Type</label>
        <select name="leave_type" required
                style="width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;color:#1e293b;outline:none;background:#fff;"
                onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
          <option value="">Select type…</option>
          <option value="Sick Leave">Sick Leave</option>
          <option value="Personal Leave">Personal Leave</option>
          <option value="Function Leave">Function Leave</option>
          <option value="Medical Leave">Medical Leave</option>
        </select>
      </div>
      <div></div>
      <div>
        <label style="display:block;font-size:.75rem;font-weight:600;color:#475569;margin-bottom:.375rem;">From Date</label>
        <input type="date" name="from_date" required min="<?php echo e(today()->toDateString()); ?>"
               style="width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;color:#1e293b;outline:none;box-sizing:border-box;"
               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
      </div>
      <div>
        <label style="display:block;font-size:.75rem;font-weight:600;color:#475569;margin-bottom:.375rem;">To Date</label>
        <input type="date" name="to_date" required min="<?php echo e(today()->toDateString()); ?>"
               style="width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;color:#1e293b;outline:none;box-sizing:border-box;"
               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
      </div>
    </div>
    <div style="margin-bottom:.875rem;">
      <label style="display:block;font-size:.75rem;font-weight:600;color:#475569;margin-bottom:.375rem;">Reason</label>
      <textarea name="reason" required rows="3"
                style="width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;color:#1e293b;outline:none;resize:vertical;box-sizing:border-box;"
                onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'"
                placeholder="Brief reason for leave…"></textarea>
    </div>
    <button type="submit"
            style="padding:.625rem 1.5rem;background:#2563eb;color:#fff;border:none;border-radius:.625rem;font-size:.875rem;font-weight:600;cursor:pointer;"
            onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
      Submit Leave Request
    </button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\portal\parent\attendance.blade.php ENDPATH**/ ?>