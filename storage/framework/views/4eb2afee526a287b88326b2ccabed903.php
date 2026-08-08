<!DOCTYPE html>
<html lang="en" x-data="appShell()" x-init="init()">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — <?php echo e(config('app.name')); ?></title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" defer></script>
  <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-slate-50 font-sans">


<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80" x-data="toasts()">
  <template x-for="toast in list" :key="toast.id">
    <div class="bg-white rounded-xl shadow-lg border-l-4 px-4 py-3 flex items-start gap-3 animate-slide-right"
         :class="{
           'border-green-500': toast.type==='success',
           'border-red-500':   toast.type==='error',
           'border-amber-500': toast.type==='warning',
           'border-blue-500':  toast.type==='info'
         }">
      <div class="flex-1">
        <p class="text-sm font-semibold text-slate-800" x-text="toast.title"></p>
        <p class="text-xs text-slate-500 mt-0.5" x-text="toast.message" x-show="toast.message"></p>
      </div>
      <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </template>
</div>

<div class="flex h-screen overflow-hidden">

  
  <div x-show="isMobile && sidebarOpen"
       @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/50 z-40 lg:hidden"
       x-transition:enter="transition-opacity duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-300"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       style="display:none">
  </div>

  
  <aside id="sidebar"
         class="fixed lg:relative inset-y-0 left-0 flex-shrink-0 flex flex-col h-full overflow-hidden transition-all duration-300 ease-in-out z-50 lg:z-30"
         :class="{
           'w-72 translate-x-0':      isMobile &&  sidebarOpen,
           'w-72 -translate-x-full':  isMobile && !sidebarOpen,
           'w-64':                   !isMobile &&  sidebarOpen,
           'w-16':                   !isMobile && !sidebarOpen
         }"
         style="background: var(--gradient-sidebar)">

    
    <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 flex-shrink-0">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0 shadow-blue-glow">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
      </div>
      <div class="flex-1 overflow-hidden" x-show="sidebarOpen || isMobile"
           x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <p class="text-white font-bold text-sm leading-tight" style="font-family:'Plus Jakarta Sans',sans-serif;"><?php echo e(config('app.name')); ?></p>
        <p class="text-slate-400 text-xs">2025-2026 Academic Year</p>
      </div>
      
      <button x-show="isMobile" @click="sidebarOpen = false"
              class="lg:hidden flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 space-y-0.5 min-h-0" style="-ms-overflow-style:none;scrollbar-width:none">
    <style>#sidebar nav::-webkit-scrollbar{display:none}</style>

      <?php $currentRoute = request()->route()?->getName() ?? ''; ?>

      
      <p class="nav-group-label" x-show="sidebarOpen">Main</p>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'dashboard','icon' => 'home','label' => 'Dashboard','active' => str_starts_with($currentRoute, 'dashboard'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'dashboard','icon' => 'home','label' => 'Dashboard','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'dashboard')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>

      
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view admissions','view students','view academics','view attendance','view examinations'])): ?>
      <p class="nav-group-label" x-show="sidebarOpen">Academics</p>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view admissions')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'admissions.index','icon' => 'clipboard-document-list','label' => 'Admissions','active' => str_starts_with($currentRoute, 'admissions'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'admissions.index','icon' => 'clipboard-document-list','label' => 'Admissions','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'admissions')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view students')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'students.index','icon' => 'users','label' => 'Students','active' => str_starts_with($currentRoute, 'students'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'students.index','icon' => 'users','label' => 'Students','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'students')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view academics')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'academics.index','icon' => 'academic-cap','label' => 'Academics','active' => str_starts_with($currentRoute, 'academics'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'academics.index','icon' => 'academic-cap','label' => 'Academics','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'academics')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view attendance')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'attendance.index','icon' => 'calendar-days','label' => 'Attendance','active' => str_starts_with($currentRoute, 'attendance'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'attendance.index','icon' => 'calendar-days','label' => 'Attendance','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'attendance')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view examinations')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'examinations.index','icon' => 'document-text','label' => 'Examinations','active' => str_starts_with($currentRoute, 'examinations'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'examinations.index','icon' => 'document-text','label' => 'Examinations','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'examinations')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php endif; ?>

      
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view fees','view employees'])): ?>
      <p class="nav-group-label" x-show="sidebarOpen">Finance</p>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view fees')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'fees.index','icon' => 'banknotes','label' => 'Fee Management','active' => str_starts_with($currentRoute, 'fees'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'fees.index','icon' => 'banknotes','label' => 'Fee Management','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'fees')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view employees')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'hr.index','icon' => 'identification','label' => 'HR & Payroll','active' => str_starts_with($currentRoute, 'hr'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'hr.index','icon' => 'identification','label' => 'HR & Payroll','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'hr')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php endif; ?>

      
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view library','view transport','view hostel','view inventory'])): ?>
      <p class="nav-group-label" x-show="sidebarOpen">Administration</p>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view library')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'library.index','icon' => 'book-open','label' => 'Library','active' => str_starts_with($currentRoute, 'library'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'library.index','icon' => 'book-open','label' => 'Library','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'library')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view transport')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'transport.index','icon' => 'truck','label' => 'Transport','active' => str_starts_with($currentRoute, 'transport'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'transport.index','icon' => 'truck','label' => 'Transport','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'transport')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view hostel')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'hostel.index','icon' => 'building-office-2','label' => 'Hostel','active' => str_starts_with($currentRoute, 'hostel'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'hostel.index','icon' => 'building-office-2','label' => 'Hostel','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'hostel')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view inventory')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'inventory.index','icon' => 'archive-box','label' => 'Inventory','active' => str_starts_with($currentRoute, 'inventory'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'inventory.index','icon' => 'archive-box','label' => 'Inventory','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'inventory')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php endif; ?>

      
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['send email','view lms','view events','view gate','view alumni'])): ?>
      <p class="nav-group-label" x-show="sidebarOpen">Engagement</p>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send email')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'communication.index','icon' => 'chat-bubble-left-right','label' => 'Communication','active' => str_starts_with($currentRoute, 'communication'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'communication.index','icon' => 'chat-bubble-left-right','label' => 'Communication','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'communication')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view lms')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'lms.index','icon' => 'play-circle','label' => 'LMS','active' => str_starts_with($currentRoute, 'lms'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'lms.index','icon' => 'play-circle','label' => 'LMS','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'lms')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view events')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'events.index','icon' => 'calendar','label' => 'Events','active' => str_starts_with($currentRoute, 'events'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'events.index','icon' => 'calendar','label' => 'Events','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'events')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view gate')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'gate.index','icon' => 'user-plus','label' => 'Gate/Visitors','active' => str_starts_with($currentRoute, 'gate'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'gate.index','icon' => 'user-plus','label' => 'Gate/Visitors','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'gate')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view alumni')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'alumni.index','icon' => 'star','label' => 'Alumni','active' => str_starts_with($currentRoute, 'alumni'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'alumni.index','icon' => 'star','label' => 'Alumni','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'alumni')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php endif; ?>

      
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view reports','view audit logs','manage settings'])): ?>
      <p class="nav-group-label" x-show="sidebarOpen">System</p>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view reports')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'reports.index','icon' => 'chart-bar','label' => 'Reports','active' => str_starts_with($currentRoute, 'reports'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'reports.index','icon' => 'chart-bar','label' => 'Reports','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'reports')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view audit logs','manage settings'])): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'system.audit-log','icon' => 'shield-check','label' => 'Audit/Security','active' => str_starts_with($currentRoute, 'system'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'system.audit-log','icon' => 'shield-check','label' => 'Audit/Security','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'system')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
      <?php if (isset($component)) { $__componentOriginal6cced52613a484e7295a90162a92d81b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6cced52613a484e7295a90162a92d81b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-item','data' => ['route' => 'settings.index','icon' => 'cog-6-tooth','label' => 'Settings','active' => str_starts_with($currentRoute, 'settings'),'open' => $sidebarOpen ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => 'settings.index','icon' => 'cog-6-tooth','label' => 'Settings','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with($currentRoute, 'settings')),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarOpen ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $attributes = $__attributesOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__attributesOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6cced52613a484e7295a90162a92d81b)): ?>
<?php $component = $__componentOriginal6cced52613a484e7295a90162a92d81b; ?>
<?php unset($__componentOriginal6cced52613a484e7295a90162a92d81b); ?>
<?php endif; ?>
      <?php endif; ?>
      <?php endif; ?>

    </nav>

    
    <div class="flex-shrink-0 px-3 pb-2"
         x-show="sidebarOpen"
         x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none">
      <div class="px-3 py-2.5 rounded-xl" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">
        <a href="mailto:support@dcinnovision.com"
           class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-xs font-medium mb-1.5">
          <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Help & Support
        </a>
        <p class="text-slate-600 text-[10px]">DASA EduERP &bull; v1.0</p>
      </div>
    </div>

    
    <div class="border-t border-white/10 p-3 flex-shrink-0"
         x-data="{
           open: false,
           dropBottom: 0,
           dropLeft: 0,
           toggle() {
             const rect = this.$el.getBoundingClientRect();
             this.dropBottom = window.innerHeight - rect.top + 6;
             this.dropLeft   = rect.left + 6;
             this.open = !this.open;
           }
         }"
         @click.outside="open = false">

      
      <div @click="toggle()"
           class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-white/10 transition cursor-pointer select-none"
           :class="open ? 'bg-white/10' : ''">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0 ring-2 ring-white/20">
          <span class="text-white text-xs font-bold"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span>
        </div>
        <div x-show="sidebarOpen" class="flex-1 min-w-0"
             x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
          <p class="text-white text-xs font-semibold truncate"><?php echo e(auth()->user()->name); ?></p>
          <p class="text-slate-400 text-xs truncate capitalize"><?php echo e(str_replace('_',' ', auth()->user()->getRoleNames()->first() ?? 'User')); ?></p>
        </div>
        <div x-show="sidebarOpen" class="text-slate-400 flex-shrink-0"
             x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
          <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
          </svg>
        </div>
      </div>

      
      <div x-show="open"
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 scale-95"
           x-transition:enter-end="opacity-100 scale-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 scale-100"
           x-transition:leave-end="opacity-0 scale-95"
           class="fixed z-[9999] bg-white rounded-xl shadow-2xl border border-slate-100 overflow-hidden"
           :style="'bottom:' + dropBottom + 'px; left:' + dropLeft + 'px; min-width:240px'"
           style="display:none">

        
        <div class="px-3 py-2.5 bg-slate-50 border-b border-slate-100">
          <p class="text-xs font-semibold text-slate-800 truncate"><?php echo e(auth()->user()->name); ?></p>
          <p class="text-xs text-slate-400 truncate"><?php echo e(auth()->user()->email); ?></p>
          <span class="inline-block mt-1 px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs font-medium capitalize">
            <?php echo e(str_replace('_',' ', auth()->user()->getRoleNames()->first() ?? 'User')); ?>

          </span>
        </div>

        
        <div class="py-1">
          <a href="<?php echo e(route('settings.index')); ?>"
             class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            My Profile
          </a>
          <a href="<?php echo e(route('settings.index')); ?>"
             class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
          </a>
        </div>

        <div class="border-t border-slate-100 py-1">
          <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Sign Out / Log Out
            </button>
          </form>
        </div>
      </div>

    </div>

    
    <button x-show="!isMobile" @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white border border-slate-200 rounded-full items-center justify-center z-40 group transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50"
            style="box-shadow:0 2px 8px rgba(0,0,0,0.15),0 0 0 1px rgba(255,255,255,0.8)"
            :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'">
      <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 transition-all duration-300"
           :class="sidebarOpen ? '' : 'rotate-180'"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>

  </aside>

  
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    
    <header class="h-14 bg-white border-b border-slate-200 flex items-center px-3 md:px-6 gap-3 flex-shrink-0 z-20">

      
      <button @click="sidebarOpen = true" class="lg:hidden btn-icon flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      
      <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex btn-icon flex-shrink-0" :title="sidebarOpen ? 'Collapse' : 'Expand'">
        <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M21 19l-7-7 7-7"/>
        </svg>
      </button>

      
      <div class="flex-1 flex items-center gap-1.5 text-xs text-slate-400">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-slate-600 transition">Home</a>
        <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
          <span>/</span>
          <?php echo $__env->yieldContent('breadcrumb'); ?>
        <?php endif; ?>
      </div>

      
      <div class="flex items-center gap-2" x-data="{ notifOpen: false, topUserOpen: false }">

        
        <span class="badge-blue text-xs cursor-default" title="Academic Year">2025-2026</span>

        
        <?php $roleName = auth()->user()->getRoleNames()->first(); ?>
        <?php if($roleName): ?>
          <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 capitalize">
            <?php echo e(str_replace('_', ' ', $roleName)); ?>

          </span>
        <?php endif; ?>

        
        <div class="relative" @click.outside="notifOpen = false">
          <button @click="notifOpen = !notifOpen" class="btn-icon relative" title="Notifications">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
          </button>

          
          <div x-show="notifOpen"
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               class="absolute right-0 top-11 z-50 w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
               style="display:none">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Notifications</span>
              <span class="badge-blue text-[10px]">3 New</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
              <a href="<?php echo e(route('communication.index')); ?>" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">Fee Collection Summary</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Today's collection report is ready.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">5 minutes ago</span>
              </a>
              <a href="<?php echo e(route('attendance.index')); ?>" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">Attendance Alert</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Class 10-A attendance marked successfully.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">1 hour ago</span>
              </a>
              <a href="<?php echo e(route('admissions.index')); ?>" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">New Admission Enquiry</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Parent submitted a new enquiry for Class 1.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">2 hours ago</span>
              </a>
            </div>
            <div class="p-2 bg-slate-50 border-t border-slate-100 text-center">
              <a href="<?php echo e(route('communication.index')); ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All Communications &rarr;</a>
            </div>
          </div>
        </div>

        
        <div class="relative" @click.outside="topUserOpen = false">
          <div @click="topUserOpen = !topUserOpen"
               class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center cursor-pointer ring-2 ring-indigo-100 hover:ring-indigo-300 transition-all select-none"
               title="<?php echo e(auth()->user()->name); ?> — Click for menu">
            <span class="text-white text-xs font-bold"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span>
          </div>

          
          <div x-show="topUserOpen"
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               class="absolute right-0 top-11 z-50 w-60 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
               style="display:none">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
              <p class="text-xs font-bold text-slate-900 truncate"><?php echo e(auth()->user()->name); ?></p>
              <p class="text-xs text-slate-500 truncate mt-0.5"><?php echo e(auth()->user()->email); ?></p>
              <span class="inline-block mt-1.5 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold capitalize">
                <?php echo e(str_replace('_', ' ', $roleName ?? 'User')); ?>

              </span>
            </div>

            <div class="py-1">
              <a href="<?php echo e(route('settings.index')); ?>"
                 class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
              </a>
              <a href="<?php echo e(route('settings.index')); ?>"
                 class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
              </a>
            </div>

            
            <div class="border-t border-slate-100 py-1">
              <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition cursor-pointer">
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Sign Out / Log Out
                </button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </header>

    
    <main class="flex-1 overflow-y-auto p-3 md:p-6 page-enter">

      
      <?php if(session('success')): ?>
        <div class="alert-success mb-5">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span><?php echo e(session('success')); ?></span>
        </div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div class="alert-error mb-5">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          <span><?php echo e(session('error')); ?></span>
        </div>
      <?php endif; ?>

      <?php echo $__env->yieldContent('content'); ?>
    </main>

  </div>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>
<script>
function appShell() {
  return {
    sidebarOpen: Alpine.$persist(true).as('sidebar_open'),
    isMobile: window.innerWidth < 1024,
    init() {
      // On mobile, always start with sidebar closed
      if (this.isMobile) this.sidebarOpen = false;

      const onResize = () => {
        const mobile = window.innerWidth < 1024;
        if (mobile !== this.isMobile) {
          this.isMobile = mobile;
          this.sidebarOpen = !mobile; // open on desktop, closed on mobile
        }
      };
      window.addEventListener('resize', onResize);

      // Lock body scroll when mobile drawer is open
      this.$watch('sidebarOpen', open => {
        if (this.isMobile) {
          document.body.style.overflow = open ? 'hidden' : '';
        }
      });
    }
  }
}
function toasts() {
  return {
    list: [],
    add(type, title, message = '') {
      const id = Date.now()
      this.list.push({ id, type, title, message })
      setTimeout(() => this.remove(id), 4000)
    },
    remove(id) { this.list = this.list.filter(t => t.id !== id) }
  }
}
</script>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views/layouts/app.blade.php ENDPATH**/ ?>