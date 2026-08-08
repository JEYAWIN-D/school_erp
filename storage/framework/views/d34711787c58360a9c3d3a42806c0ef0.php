<?php $__env->startSection('title', 'Enquiry — ' . $enquiry->enquiry_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6" x-data="{ statusModal: false }">

  
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('admissions.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div class="flex-1">
      <div class="flex items-center gap-3">
        <h1 class="page-title"><?php echo e($enquiry->student_name); ?></h1>
        <span class="<?php echo e($enquiry->status_color); ?>"><?php echo e($enquiry->status_label); ?></span>
      </div>
      <p class="page-subtitle font-mono"><?php echo e($enquiry->enquiry_number); ?> &bull; <?php echo e($enquiry->created_at->format('d M Y')); ?></p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <button @click="statusModal = true" class="btn btn-secondary btn-sm">Update Status</button>
      <a href="<?php echo e(route('admissions.edit', $enquiry->id)); ?>" class="btn btn-secondary btn-sm">Edit</a>
      <?php if(in_array($enquiry->status, ['converted', 'confirmed'])): ?>
        <a href="<?php echo e(route('students.create', ['enquiry_id' => $enquiry->id])); ?>"
           class="btn btn-sm bg-green-600 hover:bg-green-700 text-white flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Admit as Student
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-2 space-y-6">

      
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Name</dt><dd class="font-medium text-slate-800 mt-0.5"><?php echo e($enquiry->student_name); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Gender</dt><dd class="mt-0.5 capitalize"><?php echo e($enquiry->gender ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Date of Birth</dt><dd class="mt-0.5"><?php echo e($enquiry->dob?->format('d M Y') ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Class Applied</dt><dd class="mt-0.5 font-medium"><?php echo e($enquiry->class?->name ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Source</dt><dd class="mt-0.5 capitalize"><?php echo e(str_replace('-', ' ', $enquiry->source ?? '—')); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Follow-up Date</dt><dd class="mt-0.5"><?php echo e($enquiry->follow_up_date?->format('d M Y') ?? '—'); ?></dd></div>
        </dl>
      </div>

      
      <?php if($enquiry->documents && count($enquiry->documents)): ?>
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Uploaded Documents</h3>
        <ul class="space-y-2">
          <?php $__currentLoopData = $enquiry->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <a href="<?php echo e(asset('storage/' . $doc)); ?>" target="_blank" class="text-indigo-600 hover:underline truncate">
              <?php echo e(basename($doc)); ?>

            </a>
          </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
      <?php endif; ?>

      
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Name</dt><dd class="font-medium text-slate-800 mt-0.5"><?php echo e($enquiry->parent_name); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Mobile</dt><dd class="mt-0.5"><a href="tel:<?php echo e($enquiry->parent_mobile); ?>" class="text-blue-600 font-mono"><?php echo e($enquiry->parent_mobile); ?></a></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Email</dt><dd class="mt-0.5"><?php echo e($enquiry->parent_email ?? '—'); ?></dd></div>
          <div class="col-span-2"><dt class="text-slate-400 text-xs uppercase tracking-wide">Address</dt><dd class="mt-0.5"><?php echo e($enquiry->address ?? '—'); ?></dd></div>
        </dl>
      </div>

      <?php if($enquiry->previous_school): ?>
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School</h3>
        <dl class="grid grid-cols-3 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">School</dt><dd class="font-medium mt-0.5"><?php echo e($enquiry->previous_school); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Class</dt><dd class="mt-0.5"><?php echo e($enquiry->previous_class ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">%</dt><dd class="mt-0.5"><?php echo e($enquiry->previous_percentage ?? '—'); ?></dd></div>
        </dl>
      </div>
      <?php endif; ?>

      <?php if($enquiry->notes): ?>
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-2">Notes</h3>
        <p class="text-sm text-slate-600 whitespace-pre-line"><?php echo e($enquiry->notes); ?></p>
      </div>
      <?php endif; ?>

    </div>

    
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4">Follow-up History</h3>
        <?php $__empty_1 = true; $__currentLoopData = $enquiry->followUps->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="relative pl-6 pb-4 last:pb-0 border-l-2 border-slate-100">
            <div class="absolute -left-[9px] top-0.5 w-4 h-4 rounded-full bg-white border-2 border-blue-400"></div>
            <div class="bg-slate-50 rounded-xl p-3">
              <div class="flex items-center justify-between mb-1">
                <span class="<?php echo e(match($fu->status) { 'new' => 'badge-blue', 'follow_up' => 'badge-amber', 'converted' => 'badge-green', 'lost' => 'badge-red', default => 'badge-slate' }); ?> text-xs"><?php echo e(ucfirst(str_replace('_', ' ', $fu->status))); ?></span>
                <span class="text-xs text-slate-400"><?php echo e($fu->created_at->diffForHumans()); ?></span>
              </div>
              <p class="text-sm text-slate-700"><?php echo e($fu->notes); ?></p>
              <?php if($fu->next_follow_up_date): ?>
                <p class="text-xs text-slate-400 mt-1">Next: <?php echo e($fu->next_follow_up_date->format('d M Y')); ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p class="text-sm text-slate-400 text-center py-4">No follow-ups yet.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>

  
  <div x-show="statusModal" x-cloak class="modal-overlay" @click.self="statusModal = false">
    <div class="modal-box" @click.stop>
      <div class="modal-header">
        <h3 class="font-semibold text-slate-800">Update Enquiry Status</h3>
        <button @click="statusModal = false" class="btn-icon">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" action="<?php echo e(route('admissions.status', $enquiry->id)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
        <div class="modal-body space-y-4">
          <div>
            <label class="label">New Status</label>
            <select name="status" class="select" required>
              <option value="new"        <?php if($enquiry->status === 'new'): echo 'selected'; endif; ?>>New</option>
              <option value="follow_up"  <?php if($enquiry->status === 'follow_up'): echo 'selected'; endif; ?>>Follow Up</option>
              <option value="converted"  <?php if($enquiry->status === 'converted'): echo 'selected'; endif; ?>>Converted</option>
              <option value="lost"       <?php if($enquiry->status === 'lost'): echo 'selected'; endif; ?>>Lost</option>
            </select>
          </div>
          <div>
            <label class="label">Follow-up Date</label>
            <input type="date" name="follow_up_date" class="input" min="<?php echo e(now()->toDateString()); ?>">
          </div>
          <div>
            <label class="label">Notes</label>
            <textarea name="notes" rows="3" class="input resize-none" placeholder="Add follow-up notes…"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" @click="statusModal = false" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </div>
      </form>
    </div>
  </div>

  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Entrance Test</h3>
      <?php if($enquiry->entrance_test_date): ?>
        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Date</dt><dd><?php echo e($enquiry->entrance_test_date->format('d M Y')); ?><?php echo e($enquiry->entrance_test_time ? ' at '.$enquiry->entrance_test_time : ''); ?></dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Venue</dt><dd><?php echo e($enquiry->entrance_test_venue ?? '—'); ?></dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Invigilator</dt><dd><?php echo e($enquiry->entrance_test_invigilator ?? '—'); ?></dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Marks</dt><dd><?php echo e($enquiry->entrance_test_marks ?? 'Not entered'); ?></dd></div>
        </dl>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('admissions.entrance-test.save', $enquiry->id)); ?>" class="space-y-2">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Date</label>
            <input type="date" name="entrance_test_date" class="input text-sm" value="<?php echo e($enquiry->entrance_test_date?->toDateString()); ?>">
          </div>
          <div>
            <label class="label text-xs">Time</label>
            <input type="time" name="entrance_test_time" class="input text-sm" value="<?php echo e($enquiry->entrance_test_time); ?>">
          </div>
        </div>
        <div>
          <label class="label text-xs">Venue / Room</label>
          <input type="text" name="entrance_test_venue" class="input text-sm" value="<?php echo e($enquiry->entrance_test_venue); ?>" placeholder="Hall / Room No.">
        </div>
        <div>
          <label class="label text-xs">Invigilator Name</label>
          <input type="text" name="entrance_test_invigilator" class="input text-sm" value="<?php echo e($enquiry->entrance_test_invigilator); ?>" placeholder="Teacher / Staff name">
        </div>
        <div>
          <label class="label text-xs">Marks Obtained</label>
          <input type="number" name="entrance_test_marks" step="0.01" min="0" class="input text-sm" value="<?php echo e($enquiry->entrance_test_marks); ?>" placeholder="After test is conducted">
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Save Entrance Test</button>
      </form>
      <?php if($enquiry->entrance_test_date): ?>
        <a href="<?php echo e(route('admissions.entrance-test.hallticket', $enquiry->id)); ?>" target="_blank"
           class="btn btn-sm btn-primary w-full text-center block mt-1">
          Print Admit Card (PDF)
        </a>
      <?php endif; ?>
    </div>

    
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Interview</h3>
      <?php if($enquiry->interview_date): ?>
        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Date</dt><dd><?php echo e($enquiry->interview_date->format('d M Y')); ?><?php echo e($enquiry->interview_time ? ' at '.$enquiry->interview_time : ''); ?></dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Interviewer</dt><dd><?php echo e($enquiry->interview_interviewer ?? '—'); ?></dd></div>
          <div class="flex justify-between items-start"><dt class="text-slate-400">Feedback</dt><dd class="text-right max-w-[200px]"><?php echo e($enquiry->interview_feedback ?? '—'); ?></dd></div>
        </dl>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('admissions.interview.save', $enquiry->id)); ?>" class="space-y-2">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Interview Date</label>
            <input type="date" name="interview_date" class="input text-sm" value="<?php echo e($enquiry->interview_date?->toDateString()); ?>">
          </div>
          <div>
            <label class="label text-xs">Time</label>
            <input type="time" name="interview_time" class="input text-sm" value="<?php echo e($enquiry->interview_time); ?>">
          </div>
        </div>
        <div>
          <label class="label text-xs">Interviewer Name</label>
          <input type="text" name="interview_interviewer" class="input text-sm" value="<?php echo e($enquiry->interview_interviewer); ?>" placeholder="Staff / Teacher name">
        </div>
        <div>
          <label class="label text-xs">Interviewer Feedback</label>
          <textarea name="interview_feedback" rows="2" class="input text-sm"><?php echo e($enquiry->interview_feedback); ?></textarea>
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Save Interview</button>
      </form>
    </div>

    
    <div class="card space-y-3" x-data="{ saved: false }">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Document Verification Checklist</h3>
      <?php
        $docItems = [
          'birth_certificate'    => 'Birth Certificate',
          'aadhaar'              => 'Aadhaar Card',
          'transfer_certificate' => 'Transfer Certificate (TC)',
          'photo'                => 'Passport-size Photograph',
          'caste_certificate'    => 'Caste Certificate',
          'address_proof'        => 'Address Proof',
          'marksheet'            => 'Previous Class Marksheet',
          'migration_certificate'=> 'Migration Certificate',
          'medical_fitness'      => 'Medical Fitness Certificate',
        ];
        $checked = $enquiry->doc_checklist ?? [];
        $totalDocs = count($docItems);
        $verifiedCount = count(array_intersect(array_keys($docItems), $checked));
      ?>
      <div class="flex items-center gap-3 mb-2">
        <div class="flex-1 bg-slate-100 rounded-full h-2">
          <div class="bg-indigo-500 h-2 rounded-full transition-all" style="width: <?php echo e($totalDocs > 0 ? round($verifiedCount / $totalDocs * 100) : 0); ?>%"></div>
        </div>
        <span class="text-xs font-medium text-slate-600 whitespace-nowrap"><?php echo e($verifiedCount); ?>/<?php echo e($totalDocs); ?> verified</span>
      </div>
      <form method="POST" action="<?php echo e(route('admissions.doc-checklist.save', $enquiry->id)); ?>" class="space-y-2">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 gap-2">
          <?php $__currentLoopData = $docItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
            <input type="checkbox" name="docs[]" value="<?php echo e($key); ?>"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                   <?php echo e(in_array($key, $checked) ? 'checked' : ''); ?>>
            <span class="text-sm <?php echo e(in_array($key, $checked) ? 'text-slate-800 font-medium' : 'text-slate-600'); ?>">
              <?php echo e($label); ?>

            </span>
            <?php if(in_array($key, $checked)): ?>
            <span class="ml-auto">
              <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </span>
            <?php endif; ?>
          </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="submit" class="btn btn-sm btn-primary w-full mt-1">Update Checklist</button>
      </form>
      <?php if($verifiedCount === $totalDocs): ?>
        <p class="text-xs text-green-600 font-medium text-center">All documents verified</p>
      <?php elseif($verifiedCount === 0): ?>
        <p class="text-xs text-amber-600 text-center">No documents verified yet</p>
      <?php else: ?>
        <p class="text-xs text-amber-600 text-center"><?php echo e($totalDocs - $verifiedCount); ?> document(s) still pending</p>
      <?php endif; ?>
    </div>

    
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Flag Missing Documents</h3>
      <?php if(!empty($enquiry->missing_docs)): ?>
        <div class="alert-error text-sm">
          <strong>Missing documents flagged:</strong>
          <?php echo e(collect($enquiry->missing_docs)->map(fn($k) => $docItems[$k] ?? $k)->implode(', ')); ?>

          <?php if($enquiry->docs_flag_note): ?>
            <br>Note: <?php echo e($enquiry->docs_flag_note); ?>

          <?php endif; ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('admissions.flag-missing-docs', $enquiry->id)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 gap-2">
          <?php $__currentLoopData = $docItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-red-50 cursor-pointer">
              <input type="checkbox" name="missing_docs[]" value="<?php echo e($key); ?>"
                     class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500"
                     <?php echo e(in_array($key, $enquiry->missing_docs ?? []) ? 'checked' : ''); ?>>
              <span class="text-sm text-slate-700"><?php echo e($label); ?></span>
              <?php if(in_array($key, $enquiry->doc_checklist ?? [])): ?>
                <span class="ml-auto text-xs text-green-600">Received</span>
              <?php endif; ?>
            </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div>
          <label class="label text-xs">Note to parent (optional)</label>
          <input type="text" name="docs_flag_note" class="input text-sm" placeholder="e.g. Please bring originals for verification"
                 value="<?php echo e($enquiry->docs_flag_note); ?>">
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Update Missing Document Flags</button>
      </form>
    </div>

    
    <?php if(!in_array($enquiry->status, ['rejected', 'enrolled'])): ?>
    <div class="card space-y-3 border-red-100">
      <h3 class="font-semibold text-red-700 pb-2 border-b border-red-100">Reject Application</h3>
      <?php if($enquiry->rejection_reason): ?>
        <p class="text-sm text-slate-500">Reason on record: <em><?php echo e($enquiry->rejection_reason); ?></em></p>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('admissions.reject', $enquiry->id)); ?>" class="space-y-2"
            onsubmit="return confirm('Reject this application?')">
        <?php echo csrf_field(); ?>
        <div>
          <label class="label text-xs">Rejection Reason <span class="text-red-500">*</span></label>
          <textarea name="rejection_reason" rows="2" class="input text-sm" required placeholder="Reason for rejection…"></textarea>
        </div>
        <button type="submit" class="btn btn-sm bg-red-600 text-white hover:bg-red-700 w-full">Reject Application</button>
      </form>
    </div>
    <?php elseif($enquiry->status === 'rejected'): ?>
    <div class="card border-red-100">
      <h3 class="font-semibold text-red-700 pb-2 border-b border-red-100">Rejected</h3>
      <p class="text-sm text-slate-600 mt-2"><?php echo e($enquiry->rejection_reason ?? 'No reason recorded.'); ?></p>
    </div>
    <?php endif; ?>

    
    <?php if(!in_array($enquiry->status, ['enrolled', 'rejected', 'waitlisted'])): ?>
    <div class="card space-y-3 border-amber-100">
      <h3 class="font-semibold text-amber-700 pb-2 border-b border-amber-100">Add to Waitlist</h3>
      <p class="text-xs text-slate-500">If no seat is available, place this applicant on the waitlist with a position number.</p>
      <form method="POST" action="<?php echo e(route('admissions.waitlist.add', $enquiry->id)); ?>"
            onsubmit="return confirm('Add to waitlist?')">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm bg-amber-500 text-white hover:bg-amber-600 w-full">Add to Waitlist</button>
      </form>
    </div>
    <?php elseif($enquiry->status === 'waitlisted'): ?>
    <div class="card border-amber-100">
      <h3 class="font-semibold text-amber-700 pb-2 border-b border-amber-100">On Waitlist</h3>
      <p class="text-sm text-slate-600 mt-2">Position: <strong class="text-amber-700">#<?php echo e($enquiry->waitlist_position); ?></strong></p>
      <form method="POST" action="<?php echo e(route('admissions.waitlist.promote', $enquiry->id)); ?>" class="mt-3"
            onsubmit="return confirm('Promote to Confirmed?')">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm bg-green-600 text-white hover:bg-green-700 w-full">Promote to Confirmed</button>
      </form>
    </div>
    <?php endif; ?>

  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\admissions\show.blade.php ENDPATH**/ ?>