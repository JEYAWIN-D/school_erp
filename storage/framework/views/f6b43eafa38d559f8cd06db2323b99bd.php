<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route', 'icon', 'label', 'active' => false, 'open' => true]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['route', 'icon', 'label', 'active' => false, 'open' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
  try { $url = route($route); } catch (\Exception $e) { $url = '#'; }
?>

<a href="<?php echo e($url); ?>"
   class="nav-item <?php echo e($active ? 'active' : ''); ?>"
   title="<?php echo e($label); ?>">

  
  <?php echo $__env->make('components.icons.' . $icon, ['class' => 'w-5 h-5 flex-shrink-0'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  
  <span class="truncate transition-all duration-200" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 overflow-hidden'">
    <?php echo e($label); ?>

  </span>
</a>
<?php /**PATH E:\school_erp - Copy\resources\views/components/nav-item.blade.php ENDPATH**/ ?>