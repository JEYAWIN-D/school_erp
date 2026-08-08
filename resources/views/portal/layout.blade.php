<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Portal') — {{ config('app.name') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#f1f5f9;height:100vh;overflow:hidden;}

    /* ── Shell ── */
    .ps-shell{display:flex;height:100vh;overflow:hidden;}

    /* ── Sidebar ── */
    .ps-sidebar{
      width:240px;flex-shrink:0;height:100vh;overflow-y:auto;overflow-x:hidden;
      background:linear-gradient(180deg,#0f172a 0%,#1e293b 60%,#1e3a5f 100%);
      display:flex;flex-direction:column;transition:width .25s ease;
      scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent;
      position:relative;z-index:30;
    }
    .ps-sidebar.ps-collapsed{width:64px;}
    .ps-sidebar::-webkit-scrollbar{width:4px;}
    .ps-sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:2px;}

    /* Logo */
    .ps-logo{display:flex;align-items:center;gap:.75rem;padding:1rem 1rem .875rem;border-bottom:1px solid rgba(255,255,255,.08);flex-shrink:0;min-height:3.5rem;}
    .ps-logo-icon{width:2.25rem;height:2.25rem;border-radius:.625rem;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(99,102,241,.4);}
    .ps-logo-text{overflow:hidden;white-space:nowrap;opacity:1;transition:opacity .2s;}
    .ps-collapsed .ps-logo-text{opacity:0;width:0;}

    /* Nav groups */
    .ps-group-label{font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(148,163,184,.5);padding:.875rem 1rem .3rem;white-space:nowrap;overflow:hidden;opacity:1;transition:opacity .15s;}
    .ps-collapsed .ps-group-label{opacity:0;}

    /* Nav item */
    .ps-nav-item{display:flex;align-items:center;gap:.75rem;padding:.55rem .875rem;margin:.06rem .5rem;border-radius:.625rem;text-decoration:none;cursor:pointer;transition:background .15s,color .15s;overflow:hidden;white-space:nowrap;min-height:2.5rem;}
    .ps-nav-item:hover{background:rgba(255,255,255,.07);color:#e2e8f0;}
    .ps-nav-item .ps-nav-icon{flex-shrink:0;width:1.125rem;height:1.125rem;color:rgba(148,163,184,.7);transition:color .15s;}
    .ps-nav-item .ps-nav-label{font-size:.8125rem;font-weight:500;color:rgba(203,213,225,.85);white-space:nowrap;overflow:hidden;opacity:1;transition:opacity .2s;}
    .ps-nav-item.active{background:rgba(59,130,246,.18);box-shadow:inset 0 0 0 1px rgba(59,130,246,.25);}
    .ps-nav-item.active .ps-nav-icon{color:#60a5fa;}
    .ps-nav-item.active .ps-nav-label{color:#93c5fd;font-weight:600;}
    .ps-collapsed .ps-nav-label{opacity:0;width:0;}
    .ps-collapsed .ps-nav-item{justify-content:center;padding:.55rem;margin:.06rem auto;width:2.75rem;}
    .ps-collapsed .ps-nav-item .ps-nav-icon{color:rgba(148,163,184,.8);}
    .ps-collapsed .ps-nav-item.active .ps-nav-icon{color:#60a5fa;}

    /* Sidebar footer */
    .ps-sidebar-footer{border-top:1px solid rgba(255,255,255,.08);padding:.75rem;flex-shrink:0;}
    .ps-user-row{display:flex;align-items:center;gap:.75rem;padding:.5rem .5rem;border-radius:.625rem;overflow:hidden;}
    .ps-user-avatar{width:2rem;height:2rem;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .ps-user-info{flex:1;min-width:0;overflow:hidden;opacity:1;transition:opacity .2s;}
    .ps-collapsed .ps-user-info{opacity:0;width:0;}
    .ps-logout-btn{background:transparent;border:none;cursor:pointer;padding:.25rem;border-radius:.375rem;display:flex;align-items:center;color:rgba(148,163,184,.7);transition:color .15s,background .15s;flex-shrink:0;}
    .ps-logout-btn:hover{color:#f87171;background:rgba(248,113,113,.1);}

    /* ── Main area ── */
    .ps-main{flex:1;display:flex;flex-direction:column;min-width:0;overflow:hidden;}

    /* Topbar */
    .ps-topbar{height:3.5rem;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;padding:0 1.25rem;gap:.875rem;flex-shrink:0;z-index:20;}
    .ps-topbar-title{flex:1;font-size:.9375rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ps-icon-btn{width:2.25rem;height:2.25rem;border-radius:.5rem;border:none;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;transition:background .15s,color .15s;flex-shrink:0;position:relative;}
    .ps-icon-btn:hover{background:#f1f5f9;color:#1e293b;}

    /* Notification dropdown */
    .ps-notif-dropdown{position:absolute;right:0;top:calc(100% + .375rem);width:20rem;background:#fff;border-radius:.875rem;box-shadow:0 12px 32px rgba(0,0,0,.13);border:1px solid #e2e8f0;z-index:100;overflow:hidden;display:none;}
    .ps-notif-dropdown.open{display:block;}
    .ps-notif-header{display:flex;align-items:center;justify-content:space-between;padding:.875rem 1rem .625rem;border-bottom:1px solid #f1f5f9;}
    .ps-notif-item{display:flex;align-items:flex-start;gap:.75rem;padding:.75rem 1rem;border-bottom:1px solid #f8fafc;transition:background .12s;}
    .ps-notif-item:hover{background:#f8fafc;}
    .ps-notif-item:last-child{border-bottom:none;}
    .ps-unread-dot{width:.4375rem;height:.4375rem;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:.3rem;}

    /* Content */
    .ps-content{flex:1;overflow-y:auto;padding:1.25rem;}

    /* Mobile overlay */
    .ps-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:25;backdrop-filter:blur(2px);}
    .ps-overlay.show{display:block;}

    /* Portal cards (used by all pages) */
    .portal-card{background:#fff;border-radius:.875rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);border:1px solid #e2e8f0;padding:1.25rem;}
    .badge-green{display:inline-flex;align-items:center;padding:.15rem .625rem;border-radius:9999px;font-size:.72rem;font-weight:600;background:#dcfce7;color:#16a34a;}
    .badge-red{display:inline-flex;align-items:center;padding:.15rem .625rem;border-radius:9999px;font-size:.72rem;font-weight:600;background:#fee2e2;color:#dc2626;}
    .badge-blue{display:inline-flex;align-items:center;padding:.15rem .625rem;border-radius:9999px;font-size:.72rem;font-weight:600;background:#dbeafe;color:#2563eb;}
    .badge-amber{display:inline-flex;align-items:center;padding:.15rem .625rem;border-radius:9999px;font-size:.72rem;font-weight:600;background:#fef3c7;color:#d97706;}
    .badge-slate{display:inline-flex;align-items:center;padding:.15rem .625rem;border-radius:9999px;font-size:.72rem;font-weight:600;background:#f1f5f9;color:#475569;}
    .section-title{font-size:.9375rem;font-weight:600;color:#1e293b;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;}
    .link-sm{font-size:.75rem;color:#3b82f6;text-decoration:none;font-weight:500;}
    .link-sm:hover{text-decoration:underline;}
    .divider-row{border-bottom:1px solid #f1f5f9;}
    .divider-row:last-child{border-bottom:none;}
    .att-present{background:#dcfce7;color:#16a34a;}
    .att-absent{background:#fee2e2;color:#dc2626;}
    .att-late{background:#fef3c7;color:#d97706;}
    .att-leave{background:#dbeafe;color:#2563eb;}
    .att-holiday{background:#f1f5f9;color:#94a3b8;}
    .progress-bar{height:.375rem;border-radius:9999px;background:#e2e8f0;overflow:hidden;}
    .progress-fill{height:100%;border-radius:9999px;}

    /* Mobile responsive */
    @media (max-width:767px){
      .ps-sidebar{position:fixed;left:0;top:0;height:100vh;transform:translateX(-100%);transition:transform .25s ease,width .25s ease;width:240px !important;}
      .ps-sidebar.ps-mobile-open{transform:translateX(0);}
      .ps-hamburger{display:flex !important;}
      .ps-collapse-btn{display:none !important;}
    }
    @media (min-width:768px){
      .ps-hamburger{display:none !important;}
    }
  </style>
</head>
<body>

<div class="ps-shell">

  {{-- ── Sidebar ────────────────────────────────────── --}}
  <aside class="ps-sidebar" id="ps-sidebar">

    {{-- Logo --}}
    <div class="ps-logo">
      <div class="ps-logo-icon">
        <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
      </div>
      <div class="ps-logo-text">
        <p style="color:#fff;font-weight:700;font-size:.875rem;line-height:1.25;white-space:nowrap;">{{ config('app.name') }}</p>
        <p style="color:rgba(148,163,184,.6);font-size:.7rem;white-space:nowrap;">Student Portal</p>
      </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1;padding:.5rem 0;">

      @php $route = request()->route()?->getName() ?? ''; @endphp

      <p class="ps-group-label">Menu</p>

      @if(auth()->user()->hasRole('parent'))

        <a href="{{ route('portal.parent.dashboard') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.dashboard') ? 'active' : '' }}" title="Dashboard">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span class="ps-nav-label">Dashboard</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">My Child</p>

        <a href="{{ route('portal.parent.attendance') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.attendance') ? 'active' : '' }}" title="Attendance">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span class="ps-nav-label">Attendance</span>
        </a>

        <a href="{{ route('portal.parent.exams') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.exams') ? 'active' : '' }}" title="Exam Results">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span class="ps-nav-label">Exam Results</span>
        </a>

        <a href="{{ route('portal.parent.fees') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.fees') ? 'active' : '' }}" title="Fees">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span class="ps-nav-label">Fees</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">School</p>

        <a href="{{ route('portal.parent.notices') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.notices') ? 'active' : '' }}" title="Notices" id="ps-notices-link">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
          <span class="ps-nav-label">Notices</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">Account</p>

        <a href="{{ route('portal.parent.profile') }}" class="ps-nav-item {{ str_starts_with($route,'portal.parent.profile') ? 'active' : '' }}" title="Profile">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span class="ps-nav-label">My Profile</span>
        </a>

      @elseif(auth()->user()->hasRole('student'))

        <a href="{{ route('portal.student.dashboard') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.dashboard') ? 'active' : '' }}" title="Dashboard">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span class="ps-nav-label">Dashboard</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">Academics</p>

        <a href="{{ route('portal.student.attendance') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.attendance') ? 'active' : '' }}" title="Attendance">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span class="ps-nav-label">Attendance</span>
        </a>

        <a href="{{ route('portal.student.timetable') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.timetable') ? 'active' : '' }}" title="Timetable">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span class="ps-nav-label">Timetable</span>
        </a>

        <a href="{{ route('portal.student.academics') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.academics') ? 'active' : '' }}" title="Academics / LMS">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <span class="ps-nav-label">Academics</span>
        </a>

        <a href="{{ route('portal.student.exams') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.exams') ? 'active' : '' }}" title="Exam Results">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span class="ps-nav-label">Exam Results</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">Finance</p>

        <a href="{{ route('portal.student.fees') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.fees') ? 'active' : '' }}" title="Fees">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span class="ps-nav-label">Fees</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">School</p>

        <a href="{{ route('portal.student.notices') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.notices') ? 'active' : '' }}" title="Notices" id="ps-notices-link">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
          <span class="ps-nav-label">Notices</span>
        </a>

        <p class="ps-group-label" style="margin-top:.25rem;">Account</p>

        <a href="{{ route('portal.student.profile') }}" class="ps-nav-item {{ str_starts_with($route,'portal.student.profile') ? 'active' : '' }}" title="Profile">
          <svg class="ps-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span class="ps-nav-label">My Profile</span>
        </a>

      @endif

    </nav>

    {{-- User footer --}}
    <div class="ps-sidebar-footer">
      <div class="ps-user-row">
        <div class="ps-user-avatar">
          <span style="color:#fff;font-size:.7rem;font-weight:700;">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
        </div>
        <div class="ps-user-info">
          <p style="color:#f1f5f9;font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;">{{ auth()->user()->name }}</p>
          <p style="color:rgba(148,163,184,.6);font-size:.7rem;white-space:nowrap;">{{ auth()->user()->hasRole('parent') ? 'Parent' : 'Student' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
          @csrf
          <button type="submit" class="ps-logout-btn" title="Sign Out">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          </button>
        </form>
      </div>
    </div>

  </aside>

  {{-- Mobile overlay --}}
  <div class="ps-overlay" id="ps-overlay" onclick="closeSidebar()"></div>

  {{-- ── Main ─────────────────────────────────────────── --}}
  <div class="ps-main">

    {{-- Topbar --}}
    <header class="ps-topbar">

      {{-- Hamburger (mobile) --}}
      <button class="ps-icon-btn ps-hamburger" style="display:none;" onclick="openSidebar()" title="Menu">
        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>

      {{-- Collapse toggle (desktop) --}}
      <button class="ps-icon-btn ps-collapse-btn" onclick="toggleSidebar()" title="Toggle sidebar">
        <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>

      {{-- Page title --}}
      <span class="ps-topbar-title">@yield('title', 'Portal')</span>

      {{-- Notifications bell --}}
      <div style="position:relative;" id="ps-notif-wrapper">
        <button class="ps-icon-btn" id="ps-bell-btn" onclick="toggleNotif()" title="Notifications">
          <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span id="ps-notif-badge" style="position:absolute;top:.3rem;right:.3rem;width:.5rem;height:.5rem;border-radius:50%;background:#ef4444;display:none;border:1.5px solid #fff;"></span>
        </button>

        <div class="ps-notif-dropdown" id="ps-notif-dropdown">
          <div class="ps-notif-header">
            <span style="font-size:.875rem;font-weight:700;color:#1e293b;">Notifications</span>
            <button onclick="markAllRead()" style="font-size:.75rem;color:#3b82f6;background:transparent;border:none;cursor:pointer;font-weight:500;">Mark all read</button>
          </div>
          <div id="ps-notif-list" style="max-height:22rem;overflow-y:auto;">
            <div style="padding:2.5rem 1rem;text-align:center;">
              <svg style="width:2.5rem;height:2.5rem;color:#e2e8f0;margin:0 auto .5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/></svg>
              <p style="color:#94a3b8;font-size:.8125rem;">No new notifications</p>
            </div>
          </div>
        </div>
      </div>

      {{-- User avatar --}}
      <div style="width:2rem;height:2rem;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;flex-shrink:0;" title="{{ auth()->user()->name }}">
        <span style="color:#fff;font-size:.7rem;font-weight:700;">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
      </div>

    </header>

    {{-- Page content --}}
    <main class="ps-content">

      @if(session('success'))
        <div style="margin-bottom:1rem;padding:.75rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:.75rem;font-size:.875rem;display:flex;align-items:center;gap:.5rem;">
          <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="margin-bottom:1rem;padding:.75rem 1rem;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:.75rem;font-size:.875rem;display:flex;align-items:center;gap:.5rem;">
          <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </main>

  </div>
</div>

@stack('scripts')
<script>
// ── Sidebar toggle ──────────────────────────────────
var sidebar  = document.getElementById('ps-sidebar');
var overlay  = document.getElementById('ps-overlay');
var collapsed = localStorage.getItem('ps_collapsed') === '1';
if (collapsed && window.innerWidth >= 768) sidebar.classList.add('ps-collapsed');

function toggleSidebar() {
  if (window.innerWidth < 768) return;
  collapsed = !collapsed;
  sidebar.classList.toggle('ps-collapsed', collapsed);
  localStorage.setItem('ps_collapsed', collapsed ? '1' : '0');
}
function openSidebar() {
  sidebar.classList.add('ps-mobile-open');
  overlay.classList.add('show');
}
function closeSidebar() {
  sidebar.classList.remove('ps-mobile-open');
  overlay.classList.remove('show');
}

// ── Notification dropdown ───────────────────────────
var notifOpen = false;
function toggleNotif() {
  notifOpen = !notifOpen;
  document.getElementById('ps-notif-dropdown').classList.toggle('open', notifOpen);
  if (notifOpen) loadNotifications();
}
document.addEventListener('click', function(e) {
  var wrapper = document.getElementById('ps-notif-wrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    notifOpen = false;
    document.getElementById('ps-notif-dropdown').classList.remove('open');
  }
});

function loadNotifications() {
  var csrf = document.querySelector('meta[name="csrf-token"]').content;
  fetch('/portal/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
    .then(function(r){ return r.ok ? r.json() : null; })
    .then(function(data) {
      if (!data) return;
      var badge = document.getElementById('ps-notif-badge');
      var list  = document.getElementById('ps-notif-list');
      if (data.unread_count > 0) {
        badge.style.display = 'block';
      } else {
        badge.style.display = 'none';
      }
      if (data.items && data.items.length > 0) {
        list.innerHTML = data.items.map(function(n) {
          return '<div class="ps-notif-item">' +
            (n.unread ? '<span class="ps-unread-dot"></span>' : '<span style="width:.4375rem;flex-shrink:0;"></span>') +
            '<div style="flex:1;min-width:0;">' +
              '<p style="font-size:.8125rem;font-weight:' + (n.unread ? '600' : '500') + ';color:#1e293b;margin-bottom:.125rem;">' + n.title + '</p>' +
              '<p style="font-size:.72rem;color:#94a3b8;">' + n.date + '</p>' +
            '</div>' +
          '</div>';
        }).join('');
      }
    }).catch(function(){});
}

// Load unread count on page load
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]').content;
  fetch('/portal/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
    .then(function(r){ return r.ok ? r.json() : null; })
    .then(function(data) {
      if (data && data.unread_count > 0) {
        document.getElementById('ps-notif-badge').style.display = 'block';
      }
    }).catch(function(){});
})();

function markAllRead() {
  document.getElementById('ps-notif-badge').style.display = 'none';
  document.querySelectorAll('.ps-unread-dot').forEach(function(d){ d.remove(); });
}
</script>
</body>
</html>
