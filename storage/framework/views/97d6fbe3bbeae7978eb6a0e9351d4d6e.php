<?php $__env->startSection('title', 'Employee Directory'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ viewMode: 'grid' }">

  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-indigo-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-800">Employee Directory</h1>
        <p class="page-subtitle text-xs text-slate-500 mt-0.5">Manage <?php echo e($categoryCounts['total']); ?> total employees categorized by role & department</p>
      </div>
    </div>

    
    <div class="flex items-center gap-2">
      <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5">
        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-md text-xs font-medium transition flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          Cards Grid
        </button>
        <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-md text-xs font-medium transition flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          Table
        </button>
      </div>

      <a href="<?php echo e(route('hr.employees.create')); ?>" class="btn btn-primary shadow-lg shadow-indigo-100 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Add Employee
      </a>
    </div>
  </div>

  
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">

    
    <?php $isTeaching = request('type') === 'teaching'; ?>
    <a href="<?php echo e(route('hr.employees', array_merge(request()->except('page'), ['type' => 'teaching']))); ?>"
       class="group relative overflow-hidden bg-white p-4 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 <?php echo e($isTeaching ? 'border-indigo-500 ring-2 ring-indigo-200 shadow-md' : 'border-slate-200 hover:border-indigo-300 hover:shadow-md'); ?>">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <span class="text-2xl font-black text-slate-800 tracking-tight"><?php echo e($categoryCounts['teaching']); ?></span>
      </div>
      <div class="mt-3">
        <p class="text-xs font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">Teaching Staff</p>
        <p class="text-[11px] text-slate-400">Teachers & Lecturers</p>
      </div>
      <?php if($isTeaching): ?>
        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-indigo-500 rounded-bl"></div>
      <?php endif; ?>
    </a>

    
    <?php $isNonTeaching = request('type') === 'non_teaching'; ?>
    <a href="<?php echo e(route('hr.employees', array_merge(request()->except('page'), ['type' => 'non_teaching']))); ?>"
       class="group relative overflow-hidden bg-white p-4 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 <?php echo e($isNonTeaching ? 'border-purple-500 ring-2 ring-purple-200 shadow-md' : 'border-slate-200 hover:border-purple-300 hover:shadow-md'); ?>">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/>
          </svg>
        </div>
        <span class="text-2xl font-black text-slate-800 tracking-tight"><?php echo e($categoryCounts['non_teaching']); ?></span>
      </div>
      <div class="mt-3">
        <p class="text-xs font-bold text-slate-700 group-hover:text-purple-600 transition-colors">Non-Teaching Staff</p>
        <p class="text-[11px] text-slate-400">Admin, Accounts & IT</p>
      </div>
      <?php if($isNonTeaching): ?>
        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-purple-500 rounded-bl"></div>
      <?php endif; ?>
    </a>

    
    <?php $isDriver = request('type') === 'driver'; ?>
    <a href="<?php echo e(route('hr.employees', array_merge(request()->except('page'), ['type' => 'driver']))); ?>"
       class="group relative overflow-hidden bg-white p-4 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 <?php echo e($isDriver ? 'border-amber-500 ring-2 ring-amber-200 shadow-md' : 'border-slate-200 hover:border-amber-300 hover:shadow-md'); ?>">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4 4h4m1 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <span class="text-2xl font-black text-slate-800 tracking-tight"><?php echo e($categoryCounts['driver']); ?></span>
      </div>
      <div class="mt-3">
        <p class="text-xs font-bold text-slate-700 group-hover:text-amber-600 transition-colors">Drivers</p>
        <p class="text-[11px] text-slate-400">Bus & Van Drivers</p>
      </div>
      <?php if($isDriver): ?>
        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-amber-500 rounded-bl"></div>
      <?php endif; ?>
    </a>

    
    <?php $isCleaner = request('type') === 'cleaner'; ?>
    <a href="<?php echo e(route('hr.employees', array_merge(request()->except('page'), ['type' => 'cleaner']))); ?>"
       class="group relative overflow-hidden bg-white p-4 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 <?php echo e($isCleaner ? 'border-teal-500 ring-2 ring-teal-200 shadow-md' : 'border-slate-200 hover:border-teal-300 hover:shadow-md'); ?>">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>
        </div>
        <span class="text-2xl font-black text-slate-800 tracking-tight"><?php echo e($categoryCounts['cleaner']); ?></span>
      </div>
      <div class="mt-3">
        <p class="text-xs font-bold text-slate-700 group-hover:text-teal-600 transition-colors">Cleaners</p>
        <p class="text-[11px] text-slate-400">Housekeeping & Hygiene</p>
      </div>
      <?php if($isCleaner): ?>
        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-teal-500 rounded-bl"></div>
      <?php endif; ?>
    </a>

    
    <?php $isNanny = in_array(request('type'), ['nanny', 'naani']); ?>
    <a href="<?php echo e(route('hr.employees', array_merge(request()->except('page'), ['type' => 'nanny']))); ?>"
       class="group relative overflow-hidden bg-white p-4 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 <?php echo e($isNanny ? 'border-rose-500 ring-2 ring-rose-200 shadow-md' : 'border-slate-200 hover:border-rose-300 hover:shadow-md'); ?>">
      <div class="flex items-center justify-between">
        <div class="w-10 h-10 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
        </div>
        <span class="text-2xl font-black text-slate-800 tracking-tight"><?php echo e($categoryCounts['nanny']); ?></span>
      </div>
      <div class="mt-3">
        <p class="text-xs font-bold text-slate-700 group-hover:text-rose-600 transition-colors">Nannies (Naanis)</p>
        <p class="text-[11px] text-slate-400">Pre-Primary Caretakers</p>
      </div>
      <?php if($isNanny): ?>
        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-rose-500 rounded-bl"></div>
      <?php endif; ?>
    </a>

  </div>

  
  <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-3 shadow-sm">

    <form method="GET" action="<?php echo e(route('hr.employees')); ?>" class="flex-1 w-full flex flex-wrap items-center gap-2.5">
      <?php if(request('type')): ?>
        <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
      <?php endif; ?>

      
      <div class="relative flex-1 min-w-[220px]">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search employee name, code, mobile, designation..." class="input pl-9 text-xs w-full">
      </div>

      
      <select name="department" class="select text-xs w-44">
        <option value="">All Departments</option>
        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($d); ?>" <?php if(request('department') === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>

      <button type="submit" class="btn btn-secondary btn-sm text-xs">Filter</button>

      <?php if(request('search') || request('department') || request('type')): ?>
        <a href="<?php echo e(route('hr.employees')); ?>" class="text-xs text-slate-500 hover:text-red-600 transition flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          Clear Filters
        </a>
      <?php endif; ?>
    </form>

    
    <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
      <a href="<?php echo e(route('hr.employees')); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(!request('type') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        All (<?php echo e($categoryCounts['total']); ?>)
      </a>
      <a href="<?php echo e(route('hr.employees', ['type' => 'teaching'])); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(request('type') === 'teaching' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        Teaching (<?php echo e($categoryCounts['teaching']); ?>)
      </a>
      <a href="<?php echo e(route('hr.employees', ['type' => 'non_teaching'])); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(request('type') === 'non_teaching' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        Non-Teaching (<?php echo e($categoryCounts['non_teaching']); ?>)
      </a>
      <a href="<?php echo e(route('hr.employees', ['type' => 'driver'])); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(request('type') === 'driver' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        Drivers (<?php echo e($categoryCounts['driver']); ?>)
      </a>
      <a href="<?php echo e(route('hr.employees', ['type' => 'cleaner'])); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(request('type') === 'cleaner' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        Cleaners (<?php echo e($categoryCounts['cleaner']); ?>)
      </a>
      <a href="<?php echo e(route('hr.employees', ['type' => 'nanny'])); ?>" class="px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition <?php echo e(in_array(request('type'), ['nanny', 'naani']) ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
        Nannies (<?php echo e($categoryCounts['nanny']); ?>)
      </a>
    </div>

  </div>

  
  <div x-show="viewMode === 'grid'">
    <?php if($employees->count() > 0): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="group bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200 flex flex-col justify-between relative overflow-hidden">

            
            <div class="absolute top-0 left-0 right-0 h-1.5 
                 <?php echo e(strtolower($emp->employee_type) === 'teaching' ? 'bg-indigo-500' : ''); ?>

                 <?php echo e(strtolower($emp->employee_type) === 'non_teaching' ? 'bg-purple-500' : ''); ?>

                 <?php echo e(strtolower($emp->employee_type) === 'driver' ? 'bg-amber-500' : ''); ?>

                 <?php echo e(strtolower($emp->employee_type) === 'cleaner' ? 'bg-teal-500' : ''); ?>

                 <?php echo e(in_array(strtolower($emp->employee_type), ['nanny', 'naani']) ? 'bg-rose-500' : ''); ?>

            "></div>

            <div>
              
              <div class="flex items-start justify-between gap-3 pt-1">
                <div class="flex items-center gap-3">
                  <?php if($emp->photo): ?>
                    <img src="<?php echo e(asset('storage/'.$emp->photo)); ?>" alt="<?php echo e($emp->full_name); ?>" class="w-12 h-12 rounded-xl object-cover ring-2 ring-slate-100 shadow-sm flex-shrink-0">
                  <?php else: ?>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br 
                         <?php echo e(strtolower($emp->employee_type) === 'teaching' ? 'from-indigo-500 to-blue-600' : ''); ?>

                         <?php echo e(strtolower($emp->employee_type) === 'non_teaching' ? 'from-purple-500 to-indigo-600' : ''); ?>

                         <?php echo e(strtolower($emp->employee_type) === 'driver' ? 'from-amber-500 to-orange-600' : ''); ?>

                         <?php echo e(strtolower($emp->employee_type) === 'cleaner' ? 'from-teal-500 to-emerald-600' : ''); ?>

                         <?php echo e(in_array(strtolower($emp->employee_type), ['nanny', 'naani']) ? 'from-rose-500 to-pink-600' : ''); ?>

                         flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                      <?php echo e(strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1))); ?>

                    </div>
                  <?php endif; ?>

                  <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition-colors"><?php echo e($emp->full_name); ?></h3>
                    <p class="text-xs text-slate-500 truncate"><?php echo e($emp->designation); ?></p>
                    <span class="inline-block font-mono text-[10px] font-semibold text-slate-400 mt-0.5"><?php echo e($emp->employee_code); ?></span>
                  </div>
                </div>

                
                <span class="<?php echo e($emp->category_badge_class); ?> text-[10px] px-2 py-0.5 font-bold uppercase tracking-wider rounded-md flex-shrink-0">
                  <?php echo e($emp->category_label); ?>

                </span>
              </div>

              
              <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs text-slate-600">

                <?php if(strtolower($emp->employee_type) === 'driver'): ?>
                  
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">License No:</span>
                    <span class="font-mono font-bold text-slate-800"><?php echo e($emp->license_number ?? 'DL-Available'); ?></span>
                  </div>
                  <?php if($emp->assigned_vehicle): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Assigned Vehicle:</span>
                      <span class="font-semibold text-amber-700 truncate max-w-[170px]"><?php echo e($emp->assigned_vehicle); ?></span>
                    </div>
                  <?php endif; ?>
                  <?php if($emp->license_expiry): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">License Expiry:</span>
                      <span class="text-slate-700"><?php echo e(\Carbon\Carbon::parse($emp->license_expiry)->format('d M Y')); ?></span>
                    </div>
                  <?php endif; ?>

                <?php elseif(in_array(strtolower($emp->employee_type), ['nanny', 'naani'])): ?>
                  
                  <?php if($emp->assigned_block): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Assigned Section:</span>
                      <span class="font-semibold text-rose-700 truncate max-w-[170px]"><?php echo e($emp->assigned_block); ?></span>
                    </div>
                  <?php endif; ?>
                  <?php if($emp->shift_timing): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Shift Timings:</span>
                      <span class="text-slate-700 truncate max-w-[170px]"><?php echo e($emp->shift_timing); ?></span>
                    </div>
                  <?php endif; ?>
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Role:</span>
                    <span class="text-slate-700 font-medium"><?php echo e($emp->department ?? 'Pre-Primary Caretaker'); ?></span>
                  </div>

                <?php elseif(strtolower($emp->employee_type) === 'cleaner'): ?>
                  
                  <?php if($emp->assigned_block): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Assigned Zone:</span>
                      <span class="font-semibold text-teal-700 truncate max-w-[170px]"><?php echo e($emp->assigned_block); ?></span>
                    </div>
                  <?php endif; ?>
                  <?php if($emp->shift_timing): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Work Shift:</span>
                      <span class="text-slate-700 truncate max-w-[170px]"><?php echo e($emp->shift_timing); ?></span>
                    </div>
                  <?php endif; ?>
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Department:</span>
                    <span class="text-slate-700 font-medium"><?php echo e($emp->department ?? 'Housekeeping'); ?></span>
                  </div>

                <?php else: ?>
                  
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Department:</span>
                    <span class="font-semibold text-slate-800 truncate max-w-[170px]"><?php echo e($emp->department ?? 'Academics'); ?></span>
                  </div>
                  <?php if($emp->qualification): ?>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-slate-400 font-medium">Qualification:</span>
                      <span class="text-slate-700 font-medium truncate max-w-[170px]"><?php echo e($emp->qualification); ?></span>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>

                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/60">
                  <span class="text-slate-400">Mobile:</span>
                  <a href="tel:<?php echo e($emp->mobile); ?>" class="font-mono text-indigo-600 font-semibold hover:underline"><?php echo e($emp->mobile); ?></a>
                </div>
              </div>
            </div>

            
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
              <span class="inline-flex items-center gap-1 text-[11px] font-medium <?php echo e($emp->is_active ? 'text-emerald-600' : 'text-slate-400'); ?>">
                <span class="w-2 h-2 rounded-full <?php echo e($emp->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'); ?>"></span>
                <?php echo e($emp->is_active ? 'Active' : 'Inactive'); ?>

              </span>

              <div class="flex items-center gap-1.5">
                <a href="tel:<?php echo e($emp->mobile); ?>" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 flex items-center justify-center transition" title="Call Employee">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </a>
                <a href="<?php echo e(route('hr.employees.show', $emp->id)); ?>" class="btn btn-secondary btn-xs text-xs font-semibold px-3 py-1 flex items-center gap-1">
                  View Card &rarr;
                </a>
              </div>
            </div>

          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">No Employees Found</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">No employees match your selected category or filter criteria. Add a new employee to get started.</p>
        <a href="<?php echo e(route('hr.employees.create')); ?>" class="btn btn-primary btn-sm mt-4 inline-flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Employee
        </a>
      </div>
    <?php endif; ?>
  </div>

  
  <div x-show="viewMode === 'table'" style="display:none">
    <div class="table-wrap bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider font-bold border-b border-slate-200">
            <th class="py-3.5 px-4">Emp #</th>
            <th class="py-3.5 px-4">Employee Name</th>
            <th class="py-3.5 px-4">Category</th>
            <th class="py-3.5 px-4">Designation</th>
            <th class="py-3.5 px-4">Department / Zone</th>
            <th class="py-3.5 px-4">Mobile</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
          <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3 px-4 font-mono font-bold text-indigo-600"><?php echo e($emp->employee_code); ?></td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-700 text-xs">
                    <?php echo e(strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1))); ?>

                  </div>
                  <div>
                    <p class="font-bold text-slate-800"><?php echo e($emp->full_name); ?></p>
                    <p class="text-[10px] text-slate-400"><?php echo e($emp->official_email ?? $emp->email); ?></p>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4">
                <span class="<?php echo e($emp->category_badge_class); ?> text-[10px] px-2 py-0.5 font-bold uppercase rounded">
                  <?php echo e($emp->category_label); ?>

                </span>
              </td>
              <td class="py-3 px-4 text-slate-700 font-medium"><?php echo e($emp->designation); ?></td>
              <td class="py-3 px-4 text-slate-600"><?php echo e($emp->department ?? $emp->assigned_block ?? '—'); ?></td>
              <td class="py-3 px-4 font-mono text-slate-700"><?php echo e($emp->mobile); ?></td>
              <td class="py-3 px-4">
                <span class="<?php echo e($emp->is_active ? 'badge-green' : 'badge-red'); ?>">
                  <?php echo e($emp->is_active ? 'Active' : 'Inactive'); ?>

                </span>
              </td>
              <td class="py-3 px-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <a href="<?php echo e(route('hr.employees.show', $emp->id)); ?>" class="btn btn-secondary btn-xs" title="View Card">View</a>
                  <a href="<?php echo e(route('hr.employees.edit', $emp->id)); ?>" class="btn-icon" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="8" class="text-center py-8 text-slate-400">No employees found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <?php if($employees->hasPages()): ?>
    <div class="pt-2">
      <?php echo e($employees->links()); ?>

    </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views/hr/employees.blade.php ENDPATH**/ ?>