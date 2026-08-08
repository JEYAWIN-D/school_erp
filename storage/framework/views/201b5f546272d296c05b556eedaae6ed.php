<?php $__env->startSection('title', $student->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-[1400px] mx-auto space-y-6 pb-16">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="<?php echo e(route('students.index')); ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-xs hover:bg-slate-50 flex items-center justify-center text-slate-600 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Student Profile</h2>
        <h1 class="text-xl font-bold text-slate-900"><?php echo e($student->full_name); ?></h1>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <?php if($student->sibling_group_id): ?>
        <a href="<?php echo e(route('students.siblings', $student->id)); ?>" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-xs">
          <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          Siblings
        </a>
      <?php endif; ?>
      <a href="<?php echo e(route('students.id-cards', ['student_id' => $student->id])); ?>" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
        ID Card
      </a>
      <a href="<?php echo e(route('students.tc.form', $student->id)); ?>" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        TC
      </a>
      <?php if($student->status === 'active'): ?>
        <button x-data @click="$dispatch('open-modal','mark-left-<?php echo e($student->id); ?>')" class="btn btn-secondary btn-sm text-amber-600 hover:text-amber-700 shadow-xs">
          Mark as Left
        </button>
      <?php elseif($student->status === 'left'): ?>
        <span class="text-xs text-slate-500 font-medium px-3 py-1 bg-slate-100 rounded-lg">Left: <?php echo e($student->leaving_date?->format('d M Y')); ?></span>
      <?php endif; ?>
      <a href="<?php echo e(route('students.edit', $student->id)); ?>" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Profile
      </a>
    </div>
  </div>

  
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
      
      <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 overflow-hidden shadow-md ring-4 ring-slate-100 relative">
        <?php if($student->photo): ?>
          <img src="<?php echo e(asset('storage/'.$student->photo)); ?>" class="w-full h-full object-cover" alt="<?php echo e($student->full_name); ?>" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
          <span class="hidden text-white text-3xl font-extrabold tracking-wider"><?php echo e(strtoupper(substr($student->first_name,0,1) . substr($student->last_name,0,1))); ?></span>
        <?php else: ?>
          <span class="text-white text-3xl font-extrabold tracking-wider"><?php echo e(strtoupper(substr($student->first_name,0,1) . substr($student->last_name,0,1))); ?></span>
        <?php endif; ?>
      </div>

      
      <div class="flex-1 text-center md:text-left space-y-2.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center justify-center md:justify-start gap-3 flex-wrap">
              <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo e($student->full_name); ?></h1>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?php echo e($student->status === 'active' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?php echo e($student->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                <?php echo e(ucfirst($student->status)); ?>

              </span>
            </div>
            <p class="text-sm font-semibold font-mono text-indigo-600 mt-1">Admission No: <span class="bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 text-indigo-800 font-bold"><?php echo e($student->admission_number); ?></span></p>
          </div>
        </div>

        <p class="text-base text-slate-600 font-medium">
          <span class="font-bold text-slate-900"><?php echo e($student->currentEnrollment?->class?->name ?? 'Unassigned'); ?></span>
          <?php if($student->currentEnrollment?->section): ?>
            &bull; <span class="text-slate-700 font-semibold">Section <?php echo e($student->currentEnrollment->section->name); ?></span>
          <?php endif; ?>
          <?php if($student->roll_number ?? $student->currentEnrollment?->roll_number): ?>
            &bull; <span class="font-mono text-slate-900 font-bold">Roll No: <?php echo e($student->roll_number ?? $student->currentEnrollment?->roll_number); ?></span>
          <?php endif; ?>
        </p>

        
        <div class="flex items-center justify-center md:justify-start gap-2.5 flex-wrap pt-1">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <?php echo e(str_replace('_',' ', ucfirst($student->student_type ?? 'Day Scholar'))); ?>

          </span>

          <?php if($student->blood_group): ?>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.428.583L2.428 17.428a2 2 0 000 2.828l1.428 1.428a2 2 0 002.828 0l1.428-1.428a2 2 0 00.583-1.428l-.477-2.387a6 6 0 01.517-3.86l.158-.318a6 6 0 00.517-3.86L9.12 5.6a2 2 0 01.583-1.428l1.428-1.428a2 2 0 012.828 0l1.428 1.428a2 2 0 010 2.828l-1.428 1.428a2 2 0 00-.583 1.428l.477 2.387a6 6 0 00-.517 3.86l-.158.318a6 6 0 01-.517 3.86l.477 2.387a2 2 0 001.428.583l1.428-1.428a2 2 0 000-2.828l-1.428-1.428z"/></svg>
            Blood Group: <?php echo e($student->blood_group); ?>

          </span>
          <?php endif; ?>

          <?php if($student->currentEnrollment?->house): ?>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            House: <?php echo e($student->currentEnrollment->house); ?>

          </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Date of Birth</p>
        <p class="text-base font-extrabold text-slate-900 mt-0.5"><?php echo e($student->dob?->format('d M Y') ?? '—'); ?></p>
      </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
      <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Age</p>
        <p class="text-base font-extrabold text-slate-900 mt-0.5"><?php echo e($student->dob ? $student->dob->age . ' Years Old' : '—'); ?></p>
      </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student Type</p>
        <p class="text-base font-extrabold text-slate-900 mt-0.5"><?php echo e(str_replace('_',' ', ucfirst($student->student_type ?? 'Day Scholar'))); ?></p>
      </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
      <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Academic Year</p>
        <p class="text-base font-extrabold text-slate-900 mt-0.5"><?php echo e($student->currentEnrollment?->academicYear?->name ?? '2025-2026'); ?></p>
      </div>
    </div>
  </div>

  
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    
    <div class="lg:col-span-2 space-y-6">

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900">Personal Information</h3>
            <p class="text-xs text-slate-400">Complete demographic and personal details</p>
          </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Date of Birth</span>
            </div>
            <p class="text-sm font-bold text-slate-900"><?php echo e($student->dob?->format('d M Y') ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Gender</span>
            </div>
            <p class="text-sm font-bold text-slate-900 capitalize"><?php echo e($student->gender ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-rose-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.428.583L2.428 17.428a2 2 0 000 2.828l1.428 1.428a2 2 0 002.828 0l1.428-1.428a2 2 0 00.583-1.428l-.477-2.387a6 6 0 01.517-3.86l.158-.318a6 6 0 00.517-3.86L9.12 5.6a2 2 0 01.583-1.428l1.428-1.428a2 2 0 012.828 0l1.428 1.428a2 2 0 010 2.828l-1.428 1.428a2 2 0 00-.583 1.428l.477 2.387a6 6 0 00-.517 3.86l-.158.318a6 6 0 01-.517 3.86l.477 2.387a2 2 0 001.428.583l1.428-1.428a2 2 0 000-2.828l-1.428-1.428z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Blood Group</span>
            </div>
            <div>
              <?php if($student->blood_group): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200"><?php echo e($student->blood_group); ?></span>
              <?php else: ?>
                <span class="text-slate-400">—</span>
              <?php endif; ?>
            </div>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Category</span>
            </div>
            <p class="text-sm font-bold text-slate-900 uppercase"><?php echo e($student->category ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l-8-18h16l-8 18z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Religion</span>
            </div>
            <p class="text-sm font-bold text-slate-900"><?php echo e($student->religion ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mother Tongue</span>
            </div>
            <p class="text-sm font-bold text-slate-900"><?php echo e($student->mother_tongue ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Aadhaar Number</span>
            </div>
            <p class="font-mono text-sm font-bold text-slate-900"><?php echo e($student->aadhaar_no ?? $student->aadhaar_number ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mobile Number</span>
            </div>
            <p class="font-mono text-sm font-semibold text-slate-900"><?php echo e($student->mobile ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Roll Number</span>
            </div>
            <p class="font-mono text-sm font-extrabold text-slate-900"><?php echo e($student->roll_number ?? $student->currentEnrollment?->roll_number ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">House</span>
            </div>
            <div>
              <?php if($student->currentEnrollment?->house): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200"><?php echo e($student->currentEnrollment->house); ?></span>
              <?php else: ?>
                <span class="text-slate-400">—</span>
              <?php endif; ?>
            </div>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pincode</span>
            </div>
            <p class="font-mono text-sm font-semibold text-slate-900"><?php echo e($student->pincode ?? '—'); ?></p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">PwD Status</span>
            </div>
            <p class="text-sm font-semibold text-slate-900 <?php echo e($student->is_disabled ? 'text-amber-600 font-bold' : ''); ?>">
              <?php echo e($student->is_disabled ? ($student->disability_description ?? 'Yes') : 'No'); ?>

            </p>
          </div>

          
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-1 hover:border-indigo-300 transition-all shadow-2xs sm:col-span-2 md:col-span-3">
            <div class="flex items-center gap-1.5 text-slate-400">
              <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Email Address</span>
            </div>
            <p class="font-mono text-sm font-semibold text-slate-900 break-all"><?php echo e($student->email ?? '—'); ?></p>
          </div>


        </div>
      </div>

      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
          <div class="flex items-center gap-2 text-slate-900 font-bold border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            </div>
            <h3>Residential Address</h3>
          </div>
          <p class="text-sm text-slate-700 leading-relaxed font-medium">
            <?php echo e($student->residential_address ?? $student->address ?? 'No residential address recorded.'); ?>

          </p>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
          <div class="flex items-center gap-2 text-slate-900 font-bold border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h3>Permanent Address</h3>
          </div>
          <p class="text-sm text-slate-700 leading-relaxed font-medium">
            <?php echo e($student->permanent_address ?? $student->residential_address ?? $student->address ?? 'Same as residential address.'); ?>

          </p>
        </div>
      </div>

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900">Parent &amp; Guardian Information</h3>
            <p class="text-xs text-slate-400">Parental contacts and family background</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
          
          <div class="bg-slate-50/90 p-5 rounded-2xl border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-200 pb-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                👨
              </div>
              <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Father Details</span>
            </div>
            <div class="space-y-2.5 text-xs">
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Father Name:</span>
                <span class="text-sm font-bold text-slate-900 block"><?php echo e($student->father_name ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Occupation:</span>
                <span class="font-semibold text-slate-800 block"><?php echo e($student->father_occupation ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Mobile Number:</span>
                <span class="font-mono text-sm font-bold text-slate-900 block"><?php echo e($student->father_mobile ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Email Address:</span>
                <span class="font-mono text-slate-900 font-medium break-all block"><?php echo e($student->father_email ?? '—'); ?></span>
              </div>
            </div>
          </div>

          
          <div class="bg-slate-50/90 p-5 rounded-2xl border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-200 pb-3">
              <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold shrink-0">
                👩
              </div>
              <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Mother Details</span>
            </div>
            <div class="space-y-2.5 text-xs">
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Mother Name:</span>
                <span class="text-sm font-bold text-slate-900 block"><?php echo e($student->mother_name ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Occupation:</span>
                <span class="font-semibold text-slate-800 block"><?php echo e($student->mother_occupation ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Mobile Number:</span>
                <span class="font-mono text-sm font-bold text-slate-900 block"><?php echo e($student->mother_mobile ?? '—'); ?></span>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                <span class="text-slate-400 font-medium block">Email Address:</span>
                <span class="font-mono text-slate-900 font-medium break-all block"><?php echo e($student->mother_email ?? '—'); ?></span>
              </div>
            </div>
          </div>

          
          <div class="bg-slate-50/90 p-5 rounded-2xl border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-200 pb-3">
              <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold shrink-0">
                👤
              </div>
              <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Guardian Details</span>
            </div>
            <?php if($student->guardian_name): ?>
              <div class="space-y-2.5 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                  <span class="text-slate-400 font-medium block">Guardian Name:</span>
                  <span class="text-sm font-bold text-slate-900 block"><?php echo e($student->guardian_name); ?></span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                  <span class="text-slate-400 font-medium block">Relation:</span>
                  <span class="font-semibold text-slate-800 block"><?php echo e($student->guardian_relation ?? '—'); ?></span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs space-y-0.5">
                  <span class="text-slate-400 font-medium block">Guardian Mobile:</span>
                  <span class="font-mono text-sm font-bold text-slate-900 block"><?php echo e($student->guardian_mobile ?? '—'); ?></span>
                </div>
              </div>
            <?php else: ?>
              <div class="py-8 text-center text-slate-400 space-y-1 bg-white rounded-xl border border-slate-200/70 p-4">
                <p class="text-sm font-semibold text-slate-500">No Guardian Assigned</p>
                <p class="text-xs">Primary contact is managed by parents.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      <?php if($student->previous_school_name || $student->previous_school_board || $student->tc_number || $student->migration_certificate_number || $student->previous_percentage): ?>
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold shrink-0 text-lg">
            🏫
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Previous Academic History & Transfer Credentials</h3>
            <p class="text-xs text-slate-500 font-medium">Information from prior educational institution</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
          <?php if($student->previous_school_name): ?>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Previous School</span>
            <span class="text-sm font-bold text-slate-900 block"><?php echo e($student->previous_school_name); ?></span>
          </div>
          <?php endif; ?>

          <?php if($student->previous_school_board): ?>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Board / Council</span>
            <span class="text-sm font-semibold text-slate-800 block"><?php echo e($student->previous_school_board); ?></span>
          </div>
          <?php endif; ?>

          <?php if($student->previous_percentage): ?>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Previous Marks %</span>
            <span class="text-sm font-extrabold text-indigo-600 font-mono block"><?php echo e($student->previous_percentage); ?>%</span>
          </div>
          <?php endif; ?>

          <?php if($student->tc_number): ?>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">TC Number & Date</span>
            <span class="text-sm font-mono font-bold text-slate-900 block"><?php echo e($student->tc_number); ?> <?php echo e($student->tc_date ? '(' . $student->tc_date->format('d M Y') . ')' : ''); ?></span>
          </div>
          <?php endif; ?>

          <?php if($student->migration_certificate_number): ?>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Migration Cert. No & Date</span>
            <span class="text-sm font-mono font-bold text-slate-900 block"><?php echo e($student->migration_certificate_number); ?> <?php echo e($student->migration_certificate_date ? '(' . $student->migration_certificate_date->format('d M Y') . ')' : ''); ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      
      <?php if($student->allergies || $student->medical_conditions): ?>
      <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-3 border-b border-rose-100 pb-4">
          <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center font-bold shrink-0 text-lg">
            🩺
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Medical Notes & Health Special Conditions</h3>
            <p class="text-xs text-rose-600 font-medium">Important health considerations for school staff</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <?php if($student->allergies): ?>
          <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-200/60 space-y-1">
            <span class="text-rose-500 font-bold uppercase tracking-wider text-[11px] block">Known Allergies</span>
            <span class="text-sm font-semibold text-slate-900 block"><?php echo e($student->allergies); ?></span>
          </div>
          <?php endif; ?>

          <?php if($student->medical_conditions): ?>
          <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 space-y-1">
            <span class="text-amber-600 font-bold uppercase tracking-wider text-[11px] block">Medical Conditions</span>
            <span class="text-sm font-semibold text-slate-900 block"><?php echo e($student->medical_conditions); ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      
      <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl text-white p-6 sm:p-8 shadow-md flex items-center justify-between gap-6">
        <div class="space-y-1">
          <div class="flex items-center gap-2 text-emerald-100">
            <span class="text-2xl">💰</span>
            <span class="text-xs font-bold uppercase tracking-wider">Annual Family Income</span>
          </div>
          <p class="text-3xl sm:text-4xl font-black tracking-tight mt-1">
            <?php echo e($student->annual_family_income ? '₹' . number_format((float)$student->annual_family_income) : 'Not Disclosed'); ?>

          </p>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>

    </div>

    
    <div class="space-y-6">

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="font-bold text-slate-900 text-sm">Attendance (This Year)</h3>
          </div>
          <a href="<?php echo e(route('attendance.report', ['student_id' => $student->id])); ?>" class="text-xs font-semibold text-indigo-600 hover:underline">View Details →</a>
        </div>

        <?php if($attTotal > 0): ?>
          <div class="text-center py-2">
            <p class="text-4xl font-extrabold tracking-tight <?php echo e(($attPct ?? 0) >= 75 ? 'text-emerald-600' : 'text-rose-600'); ?>">
              <?php echo e($attPct); ?>%
            </p>
            <p class="text-xs font-medium text-slate-500 mt-1"><?php echo e($attPresent); ?> / <?php echo e($attTotal); ?> days present</p>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 <?php echo e(($attPct ?? 0) >= 75 ? 'bg-emerald-500' : 'bg-rose-500'); ?>"
                 style="width: <?php echo e(min(100, $attPct ?? 0)); ?>%"></div>
          </div>
          <?php if(($attPct ?? 0) < 75): ?>
          <p class="text-xs text-rose-600 font-medium flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Attendance below 75% threshold
          </p>
          <?php endif; ?>
        <?php else: ?>
          <div class="py-6 text-center text-slate-400">
            <p class="text-sm font-semibold">No attendance records for this year.</p>
          </div>
        <?php endif; ?>
      </div>

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <h3 class="font-bold text-slate-900 text-sm">Fee Status</h3>
          </div>
          <a href="<?php echo e(route('fees.collect', ['student_id' => $student->id])); ?>" class="text-xs font-semibold text-indigo-600 hover:underline">Fee Desk →</a>
        </div>

        <div class="space-y-2 text-xs">
          <div class="flex justify-between items-center py-1">
            <span class="text-slate-500 font-medium">Total Billed:</span>
            <span class="font-mono font-bold text-slate-900">₹<?php echo e(number_format($feeCharged)); ?></span>
          </div>
          <div class="flex justify-between items-center py-1">
            <span class="text-slate-500 font-medium">Total Paid:</span>
            <span class="font-mono font-bold text-emerald-600">₹<?php echo e(number_format($feePaid)); ?></span>
          </div>
          <div class="border-t border-slate-100 pt-2 flex justify-between items-center">
            <span class="font-bold <?php echo e($feeBalance > 0 ? 'text-rose-600' : 'text-emerald-600'); ?>">
              <?php echo e($feeBalance > 0 ? 'Balance Due:' : 'Status:'); ?>

            </span>
            <span class="font-mono text-base font-extrabold <?php echo e($feeBalance > 0 ? 'text-rose-600' : 'text-emerald-600'); ?>">
              <?php echo e($feeBalance > 0 ? '₹' . number_format($feeBalance) : 'Fully Paid'); ?>

            </span>
          </div>
        </div>

        <?php if($recentPayments->count()): ?>
        <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
          <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Recent Payments</p>
          <?php $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex justify-between items-center text-xs py-1">
            <span class="text-slate-500"><?php echo e(\Carbon\Carbon::parse($pay->payment_date)->format('d M Y')); ?></span>
            <span class="font-mono font-semibold text-emerald-600">₹<?php echo e(number_format($pay->total_paid ?? 0)); ?></span>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <a href="<?php echo e(route('fees.collect', ['student_id' => $student->id])); ?>" class="mt-2 btn btn-primary btn-sm w-full text-center flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
          Collect Fee
        </a>
      </div>

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 p-6 space-y-3">
        <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <h3 class="font-bold text-slate-900 text-sm">Enrollment History</h3>
        </div>

        <div class="space-y-2">
          <?php $__empty_1 = true; $__currentLoopData = $student->enrollments->sortByDesc('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $en): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
              <div>
                <p class="font-bold text-slate-900"><?php echo e($en->class?->name); ?> <?php if($en->section): ?> &bull; Section <?php echo e($en->section->name); ?> <?php endif; ?></p>
                <p class="text-[11px] text-slate-500 font-medium"><?php echo e($en->academicYear?->name); ?></p>
              </div>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php echo e($en->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'); ?>">
                <?php echo e(ucfirst($en->status)); ?>

              </span>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-xs text-slate-400 text-center py-3">No enrollment history.</p>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 p-6 space-y-3">
        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">Quick Navigation</h3>
        <div class="space-y-1 text-xs font-medium">
          <a href="<?php echo e(route('students.medical', $student->id)); ?>" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 transition">
            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            Medical Records
          </a>
          <a href="<?php echo e(route('students.disciplinary', $student->id)); ?>" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 transition">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Disciplinary Records
          </a>
          <a href="<?php echo e(route('students.id-cards', ['student_id' => $student->id])); ?>" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 transition">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
            Print ID Card
          </a>
          <a href="<?php echo e(route('students.tc.form', $student->id)); ?>" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 transition">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Transfer Certificate
          </a>
        </div>
      </div>

    </div>
  </div>
</div>


<div x-data="{open:false}" @open-modal.window="if($event.detail==='mark-left-<?php echo e($student->id); ?>')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display:none">
  <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border border-slate-100">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="font-bold text-slate-900 text-base">Mark Student as Left</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
    </div>

    <form method="POST" action="<?php echo e(route('students.mark-left', $student->id)); ?>" class="space-y-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="label mb-1">Date of Leaving <span class="text-red-500">*</span></label>
        <input type="date" name="leaving_date" value="<?php echo e(date('Y-m-d')); ?>" class="input w-full" required>
      </div>
      <div>
        <label class="label mb-1">Reason for Leaving</label>
        <textarea name="leaving_reason" rows="3" class="input w-full" placeholder="e.g. TC issued, relocated..."></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm bg-amber-600 hover:bg-amber-700 border-amber-600">Mark as Left</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\students\show.blade.php ENDPATH**/ ?>