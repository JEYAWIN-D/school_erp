{{-- ── High-Speed Mobile Navigation & Instant Loader Engine ──────────────── --}}
<style>
  /* Top Glow Progress Bar */
  #dasa-nav-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #38bdf8 0%, #6366f1 50%, #a855f7 100%);
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.8), 0 0 5px rgba(56, 189, 248, 0.9);
    z-index: 9999999;
    pointer-events: none;
    opacity: 0;
    border-radius: 0 2px 2px 0;
    transition: width 0.25s cubic-bezier(0.1, 0.9, 0.2, 1), opacity 0.2s ease;
  }
  #dasa-nav-loader.active {
    opacity: 1;
  }
  /* Snappy mobile touch feedback */
  @media (max-width: 1023px) {
    .nav-item:active,
    .ps-nav-item:active,
    a.btn:active,
    button.btn:active {
      transform: scale(0.97) !important;
      opacity: 0.85;
      transition: transform 0.08s ease, opacity 0.08s ease;
    }
  }
</style>

<div id="dasa-nav-loader" aria-hidden="true"></div>

{{-- Modern Speculation Rules for Chrome/Android instant preloading --}}
<script type="speculationrules">
{
  "prefetch": [
    {
      "where": {
        "and": [
          { "href_matches": "/*" },
          { "not": { "href_matches": "*/logout" } },
          { "not": { "href_matches": "*export*" } },
          { "not": { "href_matches": "*download*" } },
          { "not": { "href_matches": "*delete*" } },
          { "not": { "href_matches": "*/destroy" } }
        ]
      },
      "eagerness": "moderate"
    }
  ]
}
</script>

<script>
(function() {
  var loader = document.getElementById('dasa-nav-loader');
  var progressTimer = null;
  var currentWidth = 0;
  var prefetchedUrls = new Set();

  function setProgress(pct, duration) {
    if (!loader) return;
    currentWidth = pct;
    loader.style.transition = duration ? ('width ' + duration + 's ease-out') : 'width 0.2s ease-out';
    loader.style.width = pct + '%';
  }

  function startNavLoader() {
    if (!loader) return;
    clearInterval(progressTimer);
    loader.classList.add('active');
    setProgress(35, 0.15);

    // Smoothly increment while waiting for response
    progressTimer = setInterval(function() {
      if (currentWidth < 80) {
        currentWidth += Math.random() * 8;
        setProgress(Math.min(currentWidth, 80), 0.4);
      }
    }, 400);
  }

  function completeNavLoader() {
    clearInterval(progressTimer);
    if (!loader) return;
    setProgress(100, 0.1);
    setTimeout(function() {
      loader.classList.remove('active');
      setTimeout(function() {
        if (!loader.classList.contains('active')) {
          loader.style.width = '0%';
        }
      }, 250);
    }, 150);
  }

  function isEligibleLink(a) {
    if (!a || !a.href) return false;
    if (a.target === '_blank') return false;
    if (a.hasAttribute('download')) return false;
    if (a.getAttribute('data-no-instant') !== null) return false;
    
    var href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return false;
    
    // Same origin check
    try {
      var url = new URL(a.href, window.location.href);
      if (url.origin !== window.location.origin) return false;
      // Skip actions and heavy downloads
      var path = url.pathname.toLowerCase();
      if (path.includes('/logout') || path.includes('/export') || path.includes('/download') || path.endsWith('.pdf') || path.endsWith('.xlsx')) {
        return false;
      }
      return url.href;
    } catch(e) {
      return false;
    }
  }

  // Instant prefetch using <link rel="prefetch">
  function prefetchUrl(url) {
    if (!url || prefetchedUrls.has(url) || url === window.location.href) return;
    prefetchedUrls.add(url);

    var link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    link.as = 'document';
    document.head.appendChild(link);
  }

  // 1. Listen for link touch/hover to PREFETCH before user even releases finger
  document.addEventListener('touchstart', function(e) {
    var a = e.target.closest('a');
    var validUrl = isEligibleLink(a);
    if (validUrl) prefetchUrl(validUrl);
  }, { passive: true });

  document.addEventListener('mouseover', function(e) {
    var a = e.target.closest('a');
    var validUrl = isEligibleLink(a);
    if (validUrl) prefetchUrl(validUrl);
  }, { passive: true });

  // 2. Listen for click to start instant loading bar
  document.addEventListener('click', function(e) {
    var a = e.target.closest('a');
    var validUrl = isEligibleLink(a);
    if (validUrl && validUrl !== window.location.href) {
      startNavLoader();
    }
  });

  // 3. Complete on beforeunload
  window.addEventListener('beforeunload', function() {
    startNavLoader();
    setProgress(95, 0.1);
  });

  // 4. Reset on bfcache restore
  window.addEventListener('pageshow', function(e) {
    completeNavLoader();
  });

  // 5. Idle prefetch of top primary modules
  function idlePrefetchModules() {
    var primaryRoutes = [
      @can('view admissions') "{{ route('admissions.index') }}", "{{ route('admissions.approvals') }}", @endcan
      @can('view students') "{{ route('students.index') }}", @endcan
      @can('view attendance') "{{ route('attendance.index') }}", @endcan
      @can('view examinations') "{{ route('examinations.index') }}", @endcan
      @can('view fees') "{{ route('fees.index') }}", @endcan
      @can('view expenses') "{{ route('expenses.index') }}", @endcan
      @can('view employees') "{{ route('hr.index') }}", "{{ route('hr.employees') }}", "{{ route('hr.payroll') }}", @endcan
      "{{ route('dashboard') }}"
    ];

    primaryRoutes.forEach(function(url) {
      if (url && url !== window.location.href) {
        prefetchUrl(url);
      }
    });
  }

  if ('requestIdleCallback' in window) {
    requestIdleCallback(function() {
      setTimeout(idlePrefetchModules, 1000);
    });
  } else {
    setTimeout(idlePrefetchModules, 2000);
  }
})();
</script>
