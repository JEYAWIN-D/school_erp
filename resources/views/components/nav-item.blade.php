@props(['route', 'icon', 'label', 'active' => false, 'open' => true])

@php
  try { $url = route($route); } catch (\Exception $e) { $url = '#'; }
@endphp

<a href="{{ $url }}"
   class="nav-item {{ $active ? 'active' : '' }}"
   title="{{ $label }}">

  {{-- Icon --}}
  @include('components.icons.' . $icon, ['class' => 'w-5 h-5 flex-shrink-0'])

  {{-- Label --}}
  <span class="truncate transition-all duration-200" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 overflow-hidden'">
    {{ $label }}
  </span>
</a>
