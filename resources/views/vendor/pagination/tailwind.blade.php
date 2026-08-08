@if ($paginator->hasPages())
<nav aria-label="Pagination" class="flex items-center gap-1">

  {{-- Previous --}}
  @if ($paginator->onFirstPage())
    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-300 cursor-default">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}"
       class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors"
       rel="prev" aria-label="Previous">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
  @endif

  {{-- Pages --}}
  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400">…</span>
    @endif

    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span aria-current="page"
                class="inline-flex items-center justify-center w-8 h-8 rounded-md text-xs font-bold text-white"
                style="background: var(--color-primary)">
            {{ $page }}
          </span>
        @else
          <a href="{{ $url }}"
             class="inline-flex items-center justify-center w-8 h-8 rounded-md text-xs text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
            {{ $page }}
          </a>
        @endif
      @endforeach
    @endif
  @endforeach

  {{-- Next --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}"
       class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors"
       rel="next" aria-label="Next">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  @else
    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-300 cursor-default">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </span>
  @endif

</nav>
@endif
