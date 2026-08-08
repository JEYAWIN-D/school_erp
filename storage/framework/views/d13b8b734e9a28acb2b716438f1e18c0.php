<?php $__env->startSection('title', $employee->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto space-y-6">
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <?php if($employee->photo): ?>
          <img src="<?php echo e(asset('storage/'.$employee->photo)); ?>" alt="<?php echo e($employee->full_name); ?>" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-slate-100 shadow-md">
        <?php else: ?>
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 via-blue-600 to-indigo-700 flex items-center justify-center flex-shrink-0 shadow-md text-white font-black text-xl">
            <?php echo e(strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1))); ?>

          </div>
        <?php endif; ?>

        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h1 class="page-title text-xl font-bold text-slate-900"><?php echo e($employee->full_name); ?></h1>
            <span class="<?php echo e($employee->category_badge_class); ?> text-xs px-2.5 py-0.5 font-bold uppercase tracking-wider rounded-md">
              <?php echo e($employee->category_label); ?>

            </span>
            <span class="<?php echo e($employee->is_active ? 'badge-green' : 'badge-red'); ?>">
              <?php echo e($employee->is_active ? 'Active' : 'Inactive'); ?>

            </span>
          </div>
          <p class="text-xs text-slate-500 font-mono mt-1">
            Emp Code: <span class="font-bold text-indigo-600"><?php echo e($employee->employee_code); ?></span> &bull; <?php echo e($employee->designation); ?> &bull; <?php echo e($employee->department ?? 'General'); ?>

          </p>
        </div>
      </div>

      
      <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <a href="tel:<?php echo e($employee->mobile); ?>" class="btn bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 btn-sm font-bold flex items-center gap-1.5" title="Call Mobile">
          📞 <?php echo e($employee->mobile); ?>

        </a>
        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $employee->mobile)); ?>" target="_blank" class="btn bg-emerald-600 hover:bg-emerald-700 text-white btn-sm font-bold flex items-center gap-1.5" title="Send WhatsApp Message">
          💬 WhatsApp
        </a>
        <a href="<?php echo e(route('hr.employees.id-card', $employee->id)); ?>" class="btn bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 btn-sm font-bold flex items-center gap-1.5" title="Generate & Download ID Card">
          📇 Staff ID Card
        </a>
        <a href="<?php echo e(route('hr.employees.edit', $employee->id)); ?>" class="btn btn-primary btn-sm">
          ✏️ Edit Profile
        </a>

        <div x-data="{ open: false }" class="relative inline-block text-left">
          <button type="button" @click="open = !open" class="btn btn-secondary btn-sm flex items-center gap-1.5 font-semibold">
            <span>Service Docs</span>
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" class="absolute right-0 mt-1.5 bg-white shadow-xl rounded-xl border border-slate-200 z-50 min-w-[220px] py-1 text-left">
            <a href="<?php echo e(route('hr.employees.id-card', $employee->id)); ?>" class="block px-4 py-2.5 text-xs font-bold text-indigo-600 hover:bg-indigo-50 flex items-center gap-2">
              <span class="text-sm">📇</span> <span>Staff ID Card (Front & Back)</span>
            </a>
            <a href="<?php echo e(route('hr.employees.appointment-letter', $employee->id)); ?>" target="_blank" class="block px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2">
              <span class="text-sm">📄</span> <span>Appointment Letter (PDF)</span>
            </a>
            <a href="<?php echo e(route('hr.employees.experience-certificate', $employee->id)); ?>" target="_blank" class="block px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2">
              <span class="text-sm">🏆</span> <span>Experience Certificate (PDF)</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <?php if(strtolower($employee->employee_type) === 'driver'): ?>
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 p-5 rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-amber-900 text-sm flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm font-black">🚍</span>
          Driver Card Details & Vehicle Assignment
        </h3>
        <span class="badge-amber text-xs font-bold">Active Driver Profile</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-white p-3 rounded-xl border border-amber-100">
          <p class="text-slate-400 font-medium">Driving License No.</p>
          <p class="font-mono font-black text-amber-900 text-sm mt-0.5"><?php echo e($employee->license_number ?? 'DL-042019881234'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-amber-100">
          <p class="text-slate-400 font-medium">License Expiry Date</p>
          <p class="font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->license_expiry ? \Carbon\Carbon::parse($employee->license_expiry)->format('d M Y') : 'Valid till Nov 2028'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-amber-100">
          <p class="text-slate-400 font-medium">Assigned Vehicle / Route</p>
          <p class="font-bold text-amber-800 text-sm mt-0.5 truncate"><?php echo e($employee->assigned_vehicle ?? 'Bus No. MH-12-AB-1001 (Route 1)'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-amber-100">
          <p class="text-slate-400 font-medium">Emergency Mobile</p>
          <p class="font-mono font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->emergency_contact_mobile ?? $employee->mobile); ?></p>
        </div>
      </div>
    </div>

  <?php elseif(in_array(strtolower($employee->employee_type), ['nanny', 'naani'])): ?>
    <div class="bg-gradient-to-r from-rose-50 to-pink-50 border-2 border-rose-200 p-5 rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-rose-900 text-sm flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center text-sm font-black">👶</span>
          Nanny (Naani) Caretaker Profile Card
        </h3>
        <span class="badge-rose text-xs font-bold">Pre-Primary Staff</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-white p-3 rounded-xl border border-rose-100">
          <p class="text-slate-400 font-medium">Assigned Classroom Section</p>
          <p class="font-bold text-rose-900 text-sm mt-0.5"><?php echo e($employee->assigned_block ?? 'Nursery & LKG Section'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-rose-100">
          <p class="text-slate-400 font-medium">Shift Timings</p>
          <p class="font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->shift_timing ?? 'School Hours (8:00 AM - 2:30 PM)'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-rose-100">
          <p class="text-slate-400 font-medium">Child Safety Clearance</p>
          <p class="font-bold text-emerald-600 text-sm mt-0.5">✓ Verified & Background Checked</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-rose-100">
          <p class="text-slate-400 font-medium">Emergency Mobile</p>
          <p class="font-mono font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->emergency_contact_mobile ?? $employee->mobile); ?></p>
        </div>
      </div>
    </div>

  <?php elseif(strtolower($employee->employee_type) === 'cleaner'): ?>
    <div class="bg-gradient-to-r from-teal-50 to-emerald-50 border-2 border-teal-200 p-5 rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-teal-900 text-sm flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-teal-500 text-white flex items-center justify-center text-sm font-black">🧹</span>
          Cleaner & Housekeeping Staff Profile Card
        </h3>
        <span class="badge-teal text-xs font-bold">Hygiene Team</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-white p-3 rounded-xl border border-teal-100">
          <p class="text-slate-400 font-medium">Assigned Zone / Building</p>
          <p class="font-bold text-teal-900 text-sm mt-0.5"><?php echo e($employee->assigned_block ?? 'Main Administrative Building'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-teal-100">
          <p class="text-slate-400 font-medium">Work Shift</p>
          <p class="font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->shift_timing ?? 'Morning Shift (7:00 AM - 3:30 PM)'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-teal-100">
          <p class="text-slate-400 font-medium">Department</p>
          <p class="font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->department ?? 'Sanitation & Hygiene'); ?></p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-teal-100">
          <p class="text-slate-400 font-medium">Mobile</p>
          <p class="font-mono font-bold text-slate-800 text-sm mt-0.5"><?php echo e($employee->mobile); ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Employee Profile Information</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Category</dt><dd class="mt-0.5 font-bold text-slate-800"><?php echo e($employee->category_label); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Designation</dt><dd class="mt-0.5 font-medium"><?php echo e($employee->designation); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Department</dt><dd class="mt-0.5 font-medium"><?php echo e($employee->department ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Mobile Number</dt><dd class="mt-0.5 font-mono text-indigo-600 font-bold"><?php echo e($employee->mobile); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Email</dt><dd class="mt-0.5 text-slate-700"><?php echo e($employee->official_email ?? $employee->email ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Joining Date</dt><dd class="mt-0.5 font-medium"><?php echo e(\Carbon\Carbon::parse($employee->joining_date)->format('d M Y')); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Qualification</dt><dd class="mt-0.5 text-slate-700"><?php echo e($employee->qualification ?? '—'); ?></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Basic Monthly Salary</dt><dd class="mt-0.5 font-mono font-bold text-emerald-600">₹<?php echo e(number_format($employee->basic_salary ?? 0, 2)); ?></dd></div>
          <div class="col-span-2"><dt class="text-slate-400 text-xs uppercase tracking-wide">Residential Address</dt><dd class="mt-0.5 text-slate-700"><?php echo e($employee->address ?? $employee->residential_address ?? '—'); ?></dd></div>
        </dl>
      </div>

      
      <div class="card mt-6" x-data="{addQual:false}">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-700">Educational Qualifications</h3>
          <button @click="addQual=!addQual" class="btn btn-secondary btn-xs">+ Add</button>
        </div>
        <div x-show="addQual" x-transition class="mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
          <form method="POST" action="<?php echo e(route('hr.employees.qualifications.store', $employee->id)); ?>" class="grid grid-cols-2 gap-3">
            <?php echo csrf_field(); ?>
            <div><label class="label text-xs">Degree / Certificate <span class="text-red-500">*</span></label>
              <input type="text" name="degree" class="input" required placeholder="B.Ed, M.Sc, B.E etc."></div>
            <div><label class="label text-xs">Subject / Specialisation</label>
              <input type="text" name="subject" class="input" placeholder="Mathematics, Physics..."></div>
            <div><label class="label text-xs">Institution <span class="text-red-500">*</span></label>
              <input type="text" name="institution" class="input" required></div>
            <div><label class="label text-xs">University / Board</label>
              <input type="text" name="university" class="input"></div>
            <div><label class="label text-xs">Year of Passing</label>
              <input type="number" name="year_of_passing" class="input" min="1970" max="<?php echo e(date('Y')); ?>"></div>
            <div><label class="label text-xs">Grade / Percentage</label>
              <input type="text" name="grade_or_percentage" class="input" placeholder="85% / First Class / A"></div>
            <div class="col-span-2"><label class="label text-xs">Level <span class="text-red-500">*</span></label>
              <select name="education_level" class="select" required>
                <?php $__currentLoopData = ['secondary'=>'Secondary (10th)','higher_secondary'=>'Higher Secondary (12th)','diploma'=>'Diploma',
                  'graduate'=>'Graduate (UG)','post_graduate'=>'Post Graduate (PG)','doctorate'=>'Doctorate (Ph.D)','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($v); ?>"><?php echo e($l); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select></div>
            <div class="col-span-2 flex justify-end gap-2">
              <button type="button" @click="addQual=false" class="btn btn-secondary btn-sm">Cancel</button>
              <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
          </form>
        </div>
        <?php if($qualifications->count()): ?>
        <div class="space-y-2">
          <?php $__currentLoopData = $qualifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
            <div>
              <p class="font-medium text-slate-800 text-sm"><?php echo e($q->degree); ?> <?php if($q->subject): ?>— <span class="text-indigo-500"><?php echo e($q->subject); ?></span><?php endif; ?></p>
              <p class="text-xs text-slate-400"><?php echo e($q->institution); ?><?php echo e($q->university ? ', ' . $q->university : ''); ?><?php echo e($q->year_of_passing ? ' (' . $q->year_of_passing . ')' : ''); ?>

                <?php echo e($q->grade_or_percentage ? ' · ' . $q->grade_or_percentage : ''); ?></p>
            </div>
            <form method="POST" action="<?php echo e(route('hr.employees.qualifications.delete', [$employee->id, $q->id])); ?>" class="inline"
              onsubmit="return confirm('Remove this qualification?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
            </form>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-sm text-slate-400">No qualifications added yet.</p>
        <?php endif; ?>
      </div>

      
      <div class="card mt-6" x-data="{addExp:false}">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-700">Previous Experience</h3>
          <button @click="addExp=!addExp" class="btn btn-secondary btn-xs">+ Add</button>
        </div>
        <div x-show="addExp" x-transition class="mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
          <form method="POST" action="<?php echo e(route('hr.employees.experiences.store', $employee->id)); ?>"
            class="grid grid-cols-2 gap-3" x-data="{current:false}">
            <?php echo csrf_field(); ?>
            <div><label class="label text-xs">Organisation <span class="text-red-500">*</span></label>
              <input type="text" name="organisation" class="input" required></div>
            <div><label class="label text-xs">Role / Designation <span class="text-red-500">*</span></label>
              <input type="text" name="role" class="input" required></div>
            <div><label class="label text-xs">From Date <span class="text-red-500">*</span></label>
              <input type="date" name="from_date" class="input" required></div>
            <div><label class="label text-xs">To Date</label>
              <input type="date" name="to_date" class="input" :disabled="current" :class="current ? 'opacity-50' : ''">
              <label class="flex items-center gap-1 mt-1 text-xs text-slate-500">
                <input type="checkbox" name="is_current" value="1" x-model="current"> Currently working here
              </label></div>
            <div><label class="label text-xs">Reason for Leaving</label>
              <input type="text" name="reason_for_leaving" class="input" placeholder="Relocation, Better opportunity..."></div>
            <div><label class="label text-xs">Reference Contact</label>
              <input type="text" name="reference_contact" class="input" placeholder="Name / Mobile"></div>
            <div class="col-span-2"><label class="label text-xs">Key Responsibilities</label>
              <textarea name="responsibilities" rows="2" class="input" placeholder="Brief description..."></textarea></div>
            <div class="col-span-2 flex justify-end gap-2">
              <button type="button" @click="addExp=false" class="btn btn-secondary btn-sm">Cancel</button>
              <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
          </form>
        </div>
        <?php if($experiences->count()): ?>
        <div class="space-y-3">
          <?php $__currentLoopData = $experiences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="border border-slate-100 rounded-lg p-3">
            <div class="flex items-start justify-between">
              <div>
                <p class="font-medium text-slate-800 text-sm"><?php echo e($exp->role); ?> <span class="text-slate-400">@</span> <?php echo e($exp->organisation); ?></p>
                <p class="text-xs text-slate-400 mt-0.5">
                  <?php echo e($exp->from_date?->format('M Y')); ?> — <?php echo e($exp->is_current ? 'Present' : $exp->to_date?->format('M Y')); ?>

                  <span class="ml-2 font-medium text-slate-500"><?php echo e($exp->duration); ?></span>
                  <?php if($exp->reason_for_leaving): ?> · <?php echo e($exp->reason_for_leaving); ?><?php endif; ?>
                </p>
                <?php if($exp->responsibilities): ?><p class="text-xs text-slate-500 mt-1"><?php echo e($exp->responsibilities); ?></p><?php endif; ?>
              </div>
              <form method="POST" action="<?php echo e(route('hr.employees.experiences.delete', [$employee->id, $exp->id])); ?>" class="inline"
                onsubmit="return confirm('Remove this experience?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="text-xs text-red-400 hover:text-red-600 ml-2">Remove</button>
              </form>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-sm text-slate-400">No previous experience added yet.</p>
        <?php endif; ?>
      </div>
    </div>
      
      <div class="card mt-6" x-data="{addCert:false}">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-700">Certifications & Training</h3>
          <button @click="addCert=!addCert" class="btn btn-secondary btn-xs">+ Add</button>
        </div>
        <div x-show="addCert" x-transition class="mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
          <form method="POST" action="<?php echo e(route('hr.employees.certifications.store', $employee->id)); ?>" enctype="multipart/form-data" class="grid grid-cols-2 gap-3">
            <?php echo csrf_field(); ?>
            <div class="col-span-2"><label class="label text-xs">Certification / Training Name <span class="text-red-500">*</span></label>
              <input type="text" name="certification_name" class="input" required placeholder="e.g. TET, CTET, First Aid, Fire Safety..."></div>
            <div><label class="label text-xs">Issuing Authority</label>
              <input type="text" name="issuing_authority" class="input" placeholder="CBSE, Government, etc."></div>
            <div><label class="label text-xs">Certificate Number</label>
              <input type="text" name="certificate_number" class="input" placeholder="e.g. CERT-98765"></div>
            <div class="col-span-2"><label class="label text-xs">Issue Date</label>
              <input type="date" name="issue_date" class="input"></div>
            <div class="col-span-2"><label class="label text-xs">Upload Certificate File (PDF/image, optional)</label>
              <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="input"></div>
            <div class="col-span-2 flex justify-end gap-2">
              <button type="button" @click="addCert=false" class="btn btn-secondary btn-sm">Cancel</button>
              <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
          </form>
        </div>
        <?php if($certifications->count()): ?>
        <div class="space-y-3">
          <?php $__currentLoopData = $certifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-slate-50/70 rounded-xl border border-slate-100 gap-3">
            <div>
              <p class="font-bold text-slate-800 text-sm"><?php echo e($cert->certification_name); ?></p>
              <p class="text-xs text-slate-500 mt-0.5">
                <?php echo e($cert->issuing_authority ?? 'Authority N/A'); ?>

                <?php if($cert->issue_date): ?> · Issued: <?php echo e($cert->issue_date->format('d M Y')); ?><?php endif; ?>
                <?php if($cert->certificate_number): ?> · No: <?php echo e($cert->certificate_number); ?><?php endif; ?>
              </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
              <?php if($cert->file_path): ?>
                <a href="<?php echo e(route('hr.employees.certifications.preview', [$employee->id, $cert->id])); ?>" target="_blank" class="btn-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold flex items-center gap-1">
                  👁️ Preview
                </a>
                <a href="<?php echo e(route('hr.employees.certifications.download', [$employee->id, $cert->id])); ?>" class="btn-xs bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold flex items-center gap-1">
                  📥 Download
                </a>
              <?php endif; ?>
              <form method="POST" action="<?php echo e(route('hr.employees.certifications.delete', [$employee->id, $cert->id])); ?>" class="inline"
                onsubmit="return confirm('Delete this certification?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 font-semibold">Delete</button>
              </form>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-sm text-slate-400">No certifications added yet.</p>
        <?php endif; ?>
      </div>

      
      <div class="card mt-6" x-data="{addDoc:false}">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-700">Employee Documents</h3>
          <button @click="addDoc=!addDoc" class="btn btn-secondary btn-xs">+ Upload</button>
        </div>
        <div x-show="addDoc" x-transition class="mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
          <form method="POST" action="<?php echo e(route('hr.employees.documents.upload', $employee->id)); ?>" enctype="multipart/form-data" class="grid grid-cols-2 gap-3">
            <?php echo csrf_field(); ?>
            <div><label class="label text-xs">Document Type <span class="text-red-500">*</span></label>
              <select name="document_type" class="select" required>
                <?php $__currentLoopData = ['aadhaar'=>'Aadhaar Card','pan'=>'PAN Card','degree'=>'Degree Certificate',
                  'experience_letter'=>'Experience Letter','police_verification'=>'Police Verification',
                  'photo'=>'Passport Photo','marksheet'=>'Marksheet','other'=>'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($v); ?>"><?php echo e($l); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select></div>
            <div><label class="label text-xs">Document Name / Description <span class="text-red-500">*</span></label>
              <input type="text" name="document_name" class="input" required placeholder="e.g. Aadhaar – XXXX 4567"></div>
            <div class="col-span-2"><label class="label text-xs">File <span class="text-red-500">*</span></label>
              <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="input" required></div>
            <div class="col-span-2"><label class="label text-xs">Notes (optional)</label>
              <input type="text" name="notes" class="input" placeholder="Any remarks..."></div>
            <div class="col-span-2 flex justify-end gap-2">
              <button type="button" @click="addDoc=false" class="btn btn-secondary btn-sm">Cancel</button>
              <button type="submit" class="btn btn-primary btn-sm">Upload</button>
            </div>
          </form>
        </div>
        <?php if($empDocuments->count()): ?>
        <div class="overflow-x-auto border border-slate-200 rounded-xl mt-2">
          <table class="w-full text-xs text-left min-w-[550px]">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
              <tr>
                <th class="px-3 py-2.5">Type</th>
                <th class="px-3 py-2.5">Document</th>
                <th class="px-3 py-2.5">Status</th>
                <th class="px-3 py-2.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php $__currentLoopData = $empDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="hover:bg-slate-50/50">
                <td class="px-3 py-2.5 whitespace-nowrap">
                  <span class="badge-slate text-[10px] font-semibold uppercase"><?php echo e(str_replace('_',' ',$doc->document_type)); ?></span>
                </td>
                <td class="px-3 py-2.5">
                  <p class="font-bold text-slate-800"><?php echo e($doc->document_name); ?></p>
                  <?php if($doc->file_original_name): ?><p class="text-[10px] text-slate-400 font-mono"><?php echo e($doc->file_original_name); ?></p><?php endif; ?>
                </td>
                <td class="px-3 py-2.5 whitespace-nowrap">
                  <form method="POST" action="<?php echo e(route('hr.employees.documents.verify', [$employee->id, $doc->id])); ?>" class="inline-flex items-center gap-1">
                    <?php echo csrf_field(); ?>
                    <select name="status" onchange="this.form.submit()" class="text-[10px] font-bold border border-slate-200 rounded px-1.5 py-0.5 bg-white">
                      <option value="pending"  <?php if($doc->verification_status==='pending'): echo 'selected'; endif; ?>>🟡 Pending</option>
                      <option value="verified" <?php if($doc->verification_status==='verified'): echo 'selected'; endif; ?>>🟢 Verified</option>
                      <option value="rejected" <?php if($doc->verification_status==='rejected'): echo 'selected'; endif; ?>>🔴 Rejected</option>
                    </select>
                  </form>
                </td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo e(route('hr.employees.documents.preview', [$employee->id, $doc->id])); ?>" target="_blank" class="btn-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold flex items-center gap-1" title="Preview Document">
                      👁️ Preview
                    </a>
                    <a href="<?php echo e(route('hr.employees.documents.download', [$employee->id, $doc->id])); ?>" class="btn-xs bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold flex items-center gap-1" title="Download Document">
                      📥 Download
                    </a>
                    <form method="POST" action="<?php echo e(route('hr.employees.documents.delete', [$employee->id, $doc->id])); ?>" class="inline"
                      onsubmit="return confirm('Delete this document?')">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 font-semibold" title="Delete Document">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <p class="text-sm text-slate-400">No documents uploaded yet.</p>
        <?php endif; ?>
      </div>
    </div>
    <div>
      
      <div class="card mb-6" x-data="{ createLogin: false, changeRole: false }">
        <h3 class="font-semibold text-slate-700 mb-3">Portal Login & Role</h3>
        <?php if(isset($linkedUser) && $linkedUser): ?>
          <div class="flex items-center gap-2 mb-3">
            <span class="badge-green text-xs">Account Active</span>
            <span class="text-sm text-slate-500"><?php echo e($linkedUser->email); ?></span>
          </div>
          <div class="mb-3">
            <span class="text-xs text-slate-400 uppercase tracking-wide">Current Role</span>
            <div class="mt-1">
              <span class="badge-blue text-xs capitalize"><?php echo e($linkedUser->getRoleNames()->first() ?? 'No role assigned'); ?></span>
            </div>
          </div>
          <button @click="changeRole = !changeRole" class="btn btn-secondary btn-xs w-full">Change Role</button>
          <div x-show="changeRole" x-transition class="mt-3">
            <form method="POST" action="<?php echo e(route('hr.employees.assign-role', $employee->id)); ?>">
              <?php echo csrf_field(); ?>
              <select name="role" class="select text-sm mb-2">
                <?php $__currentLoopData = $staffRoles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($r); ?>" <?php if($linkedUser->hasRole($r)): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $r))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <button type="submit" class="btn btn-primary btn-xs w-full">Save Role</button>
            </form>
          </div>
        <?php else: ?>
          <p class="text-sm text-slate-400 mb-3">No portal login yet.</p>
          <button @click="createLogin = !createLogin" class="btn btn-primary btn-xs w-full">+ Create Login</button>
          <div x-show="createLogin" x-transition class="mt-3">
            <form method="POST" action="<?php echo e(route('hr.employees.portal-login', $employee->id)); ?>" class="space-y-2">
              <?php echo csrf_field(); ?>
              <input type="email" name="email" class="input text-sm" placeholder="Email address"
                     value="<?php echo e($employee->email); ?>" required>
              <input type="password" name="password" class="input text-sm" placeholder="Password (min 8 chars)" required minlength="8">
              <select name="role" class="select text-sm">
                <?php $__currentLoopData = $staffRoles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($r); ?>"><?php echo e(ucwords(str_replace('_', ' ', $r))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <button type="submit" class="btn btn-primary btn-xs w-full">Create Account</button>
            </form>
          </div>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3">Recent Payroll</h3>
        <?php $__empty_1 = true; $__currentLoopData = $payroll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="flex justify-between items-center text-sm py-2 border-b border-slate-100 last:border-0">
            <span class="text-slate-600"><?php echo e($p->month); ?></span>
            <span class="font-semibold text-slate-800">₹<?php echo e(number_format($p->net_salary, 2)); ?></span>
            <span class="<?php echo e($p->status === 'paid' ? 'badge-green' : 'badge-slate'); ?> text-xs"><?php echo e(ucfirst($p->status)); ?></span>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p class="text-sm text-slate-400">No payroll records.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views/hr/employees-show.blade.php ENDPATH**/ ?>