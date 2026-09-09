<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — {{ config('app.name') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body { font-family: 'Inter', sans-serif; background: #f8fafc; display: flex; min-height: 100vh; }

    /* ── Left brand panel ── */
    .brand-panel {
      display: none; width: 44%; flex-shrink: 0;
      background: linear-gradient(160deg, #1e3a8a 0%, #1d4ed8 45%, #4f46e5 100%);
      padding: 3rem 3.5rem; flex-direction: column; justify-content: space-between;
      position: sticky; top: 0; height: 100vh;
    }
    @media (min-width: 900px) { .brand-panel { display: flex; } }
    .brand-logo { width: 3rem; height: 3rem; background: rgba(255,255,255,.15); border-radius: .875rem; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,.25); }
    .brand-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.875rem; font-weight: 800; color: #fff; margin-top: 2.5rem; line-height: 1.2; }
    .brand-sub { color: rgba(147,197,253,.85); font-size: .9375rem; margin-top: .625rem; line-height: 1.6; }
    .feature-list { display: flex; flex-direction: column; gap: .875rem; margin-top: 2.5rem; }
    .feature-item { display: flex; align-items: flex-start; gap: .875rem; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: .875rem; padding: .875rem 1rem; }
    .feature-icon { width: 2.25rem; height: 2.25rem; border-radius: .625rem; background: rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feature-item-title { font-size: .875rem; font-weight: 600; color: #fff; }
    .feature-item-desc  { font-size: .75rem; color: rgba(147,197,253,.75); margin-top: .125rem; }
    .brand-footer { font-size: .75rem; color: rgba(147,197,253,.5); }
    .stats-row { display: flex; gap: 2rem; margin-top: 2rem; }
    .stat-num { font-size: 1.375rem; font-weight: 700; color: #fff; }
    .stat-lbl { font-size: .72rem; color: rgba(147,197,253,.6); margin-top: .125rem; }

    /* ── Right form panel ── */
    .form-panel { flex: 1; display: flex; align-items: flex-start; justify-content: center; padding: 2rem 1rem; overflow-y: auto; min-height: 100vh; }
    @media (min-width: 640px) { .form-panel { padding: 2.5rem 1.5rem; } }
    .form-box { width: 100%; max-width: 420px; padding-top: 1rem; padding-bottom: 2rem; }

    .mobile-header { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem; }
    @media (min-width: 900px) { .mobile-header { display: none; } }
    .mobile-logo { width: 2.5rem; height: 2.5rem; border-radius: .75rem; background: linear-gradient(135deg, #1d4ed8, #4f46e5); display: flex; align-items: center; justify-content: center; }
    .mobile-appname { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.125rem; font-weight: 800; color: #1e293b; }

    .form-heading { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.5rem; font-weight: 800; color: #0f172a; }
    @media (min-width: 640px) { .form-heading { font-size: 1.625rem; } }
    .form-sub { font-size: .875rem; color: #64748b; margin-top: .375rem; margin-bottom: 1.5rem; }

    /* Quick-fill top 3 */
    .qf-label { font-size: .6875rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #94a3b8; text-align: center; margin-bottom: .75rem; }
    .qf-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .375rem; margin-bottom: .875rem; }
    @media (min-width: 400px) { .qf-grid { gap: .5rem; } }
    .qf-btn { display: flex; flex-direction: column; align-items: center; gap: .375rem; padding: .625rem .375rem; border-radius: .75rem; border: 1.5px solid #e2e8f0; background: #fff; cursor: pointer; transition: border-color .15s, box-shadow .15s, background .15s; text-align: center; }
    .qf-btn:hover { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.08); background: #eff6ff; }
    .qf-icon { width: 1.875rem; height: 1.875rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    @media (min-width: 400px) { .qf-icon { width: 2rem; height: 2rem; border-radius: .625rem; } }
    .qf-btn-label { font-size: .72rem; font-weight: 600; color: #1e293b; }
    .qf-btn-hint  { font-size: .62rem; color: #94a3b8; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }

    /* View all button */
    .view-all-btn {
      width: 100%; padding: .625rem .5rem; border-radius: .75rem;
      border: 1.5px dashed #cbd5e1; background: #f8fafc;
      font-size: .8125rem; font-weight: 600; color: #64748b;
      cursor: pointer; transition: all .15s; margin-bottom: 1.25rem;
      display: flex; align-items: center; justify-content: center; gap: .375rem;
      touch-action: manipulation;
    }
    .view-all-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
    .view-all-btn svg { transition: transform .25s; }
    .view-all-btn.open svg { transform: rotate(180deg); }

    /* Demo accounts modal overlay */
    .demo-overlay {
      display: none; position: fixed; inset: 0; z-index: 50;
      background: rgba(15,23,42,.6); backdrop-filter: blur(4px);
      align-items: center; justify-content: center; padding: .75rem;
      overflow-y: auto;
    }
    .demo-overlay.show { display: flex; }

    .demo-modal {
      background: #fff; border-radius: 1.25rem; width: 100%; max-width: 640px;
      max-height: calc(100vh - 2rem); display: flex; flex-direction: column;
      box-shadow: 0 25px 60px rgba(0,0,0,.25);
      animation: slideUp .25s ease;
      margin: auto; overflow: hidden;
    }
    @keyframes slideUp { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }

    .demo-modal-head {
      padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9;
      display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    }
    .demo-modal-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .9375rem; font-weight: 800; color: #0f172a; }
    .demo-modal-sub { font-size: .72rem; color: #94a3b8; margin-top: .125rem; }
    .demo-close { width: 2rem; height: 2rem; border-radius: .5rem; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; transition: background .15s; flex-shrink: 0; }
    .demo-close:hover { background: #e2e8f0; color: #1e293b; }

    .demo-modal-body { overflow-y: auto; padding: 1rem 1.25rem; flex: 1; -webkit-overflow-scrolling: touch; }

    /* password badge */
    .pw-badge { display: inline-flex; align-items: center; gap: .375rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: .5rem; padding: .25rem .625rem; font-size: .75rem; font-weight: 600; color: #16a34a; margin-bottom: 1rem; }

    /* role group */
    .role-group { margin-bottom: 1.25rem; }
    .role-group-title { font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: #94a3b8; margin-bottom: .5rem; padding-left: .125rem; }
    .role-grid { display: grid; grid-template-columns: 1fr; gap: .375rem; }
    @media (min-width: 440px) { .role-grid { grid-template-columns: 1fr 1fr; } }
    @media (min-width: 600px) { .role-grid { grid-template-columns: 1fr 1fr 1fr; } }

    .role-card {
      display: flex; align-items: center; gap: .625rem;
      padding: .5rem .625rem; border-radius: .625rem;
      border: 1.5px solid #f1f5f9; background: #fafafa;
      cursor: pointer; transition: all .15s; text-align: left;
      touch-action: manipulation;
    }
    .role-card:hover { border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 3px rgba(37,99,235,.07); }
    .role-card:hover .role-card-name { color: #1d4ed8; }
    .role-dot { width: .5rem; height: .5rem; border-radius: 50%; flex-shrink: 0; }
    .role-card-inner { min-width: 0; flex: 1; }
    .role-card-name { font-size: .75rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .role-card-email { font-size: .65rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .demo-modal-foot { padding: .75rem 1.25rem; border-top: 1px solid #f1f5f9; flex-shrink: 0; text-align: center; }
    .demo-modal-foot-text { font-size: .72rem; color: #94a3b8; }

    .divider { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.25rem; }
    .divider-line { flex: 1; height: 1px; background: #e2e8f0; }
    .divider-text { font-size: .75rem; color: #94a3b8; white-space: nowrap; }

    .field-label { display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem; }
    .field-wrap { margin-bottom: 1rem; }
    .field-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: .625rem; padding: .65rem .875rem; font-size: 16px; color: #0f172a; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; font-family: 'Inter', sans-serif; }
    .field-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
    .field-input::placeholder { color: #cbd5e1; }
    .field-error { font-size: .75rem; color: #dc2626; margin-top: .3rem; }

    .pass-wrap { position: relative; }
    .pass-toggle { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: .25rem; display: flex; align-items: center; }
    .pass-toggle:hover { color: #475569; }

    .row-between { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; gap: .5rem; flex-wrap: wrap; }
    .remember-label { display: flex; align-items: center; gap: .5rem; font-size: .8125rem; color: #475569; cursor: pointer; }
    .remember-cb { width: 1rem; height: 1rem; accent-color: #2563eb; }
    .forgot-link { font-size: .8125rem; color: #2563eb; text-decoration: none; font-weight: 500; }
    .forgot-link:hover { color: #1d4ed8; }

    .submit-btn { width: 100%; padding: .8125rem; background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); color: #fff; border: none; border-radius: .75rem; font-size: .9375rem; font-weight: 700; cursor: pointer; transition: opacity .15s, box-shadow .15s; font-family: 'Inter', sans-serif; box-shadow: 0 4px 14px rgba(37,99,235,.35); display: flex; align-items: center; justify-content: center; gap: .5rem; min-height: 44px; touch-action: manipulation; }
    .submit-btn:hover:not(:disabled) { opacity: .92; box-shadow: 0 6px 20px rgba(37,99,235,.45); }
    .submit-btn:disabled { opacity: .65; cursor: not-allowed; }

    .error-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: .75rem; padding: .75rem 1rem; margin-bottom: 1.25rem; color: #dc2626; font-size: .875rem; display: flex; align-items: flex-start; gap: .5rem; }
    .footer-text { font-size: .75rem; color: #94a3b8; text-align: center; margin-top: 1.75rem; }

    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { animation: spin .7s linear infinite; }
  </style>
</head>
<body>
@include('partials.mobile-nav-speed')

  {{-- ── Brand panel (desktop) ── --}}
  <div class="brand-panel">
    <div>
      <div class="brand-logo">
        <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
      </div>
      <h1 class="brand-title">DASA EduERP</h1>
      <p class="brand-sub">Complete school management platform by DASA EduERP</p>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon"><svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <div><div class="feature-item-title">Student &amp; Parent Portal</div><div class="feature-item-desc">Attendance, exams, fees &amp; notices — all in one place</div></div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
          <div><div class="feature-item-title">20 Integrated Modules</div><div class="feature-item-desc">Admissions, HR, LMS, Library, Transport &amp; more</div></div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
          <div><div class="feature-item-title">Reports &amp; Analytics</div><div class="feature-item-desc">Executive dashboard with real-time insights</div></div>
        </div>
      </div>
      <div class="stats-row" style="margin-top:2.5rem">
        <div><div class="stat-num">20+</div><div class="stat-lbl">Modules</div></div>
        <div><div class="stat-num">23</div><div class="stat-lbl">Role Types</div></div>
        <div><div class="stat-num">100%</div><div class="stat-lbl">Cloud Ready</div></div>
      </div>
    </div>
    <div class="brand-footer">&copy; {{ date('Y') }} DASA EduERP</div>
  </div>

  {{-- ── Form panel ── --}}
  <div class="form-panel">
    <div class="form-box">

      <div class="mobile-header">
        <div class="mobile-logo">
          <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
        </div>
        <span class="mobile-appname">DASA EduERP</span>
      </div>

      <h2 class="form-heading">Welcome back</h2>
      <p class="form-sub">Sign in to your account to continue</p>

      {{-- Quick-fill: top 3 --}}
      <p class="qf-label">Quick Login — Demo Accounts</p>
      <div class="qf-grid">
        <button type="button" class="qf-btn" onclick="fillLogin('admin@schoolerp.in','Admin@1234')">
          <div class="qf-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
            <svg width="14" height="14" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
          <div class="qf-btn-label">Admin</div>
          <div class="qf-btn-hint">admin@schoolerp.in</div>
        </button>
        <button type="button" class="qf-btn" onclick="fillLogin('student@schoolerp.in','Demo@2026')">
          <div class="qf-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
            <svg width="14" height="14" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <div class="qf-btn-label">Student</div>
          <div class="qf-btn-hint">student@schoolerp.in</div>
        </button>
        <button type="button" class="qf-btn" onclick="fillLogin('parent@schoolerp.in','Demo@2026')">
          <div class="qf-icon" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe)">
            <svg width="14" height="14" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div class="qf-btn-label">Parent</div>
          <div class="qf-btn-hint">parent@schoolerp.in</div>
        </button>
      </div>

      {{-- View all roles button --}}
      <button type="button" class="view-all-btn" id="view-all-btn" onclick="openDemoModal()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        View all 23 role accounts
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>

      <div class="divider">
        <div class="divider-line"></div>
        <span class="divider-text">or enter credentials manually</span>
        <div class="divider-line"></div>
      </div>

      @if($errors->any())
        <div class="error-box">
          <svg style="width:1rem;height:1rem;flex-shrink:0;margin-top:.0625rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <div class="field-wrap">
          <label class="field-label">Email or Mobile</label>
          <input id="email-field" type="text" name="email" value="{{ old('email') }}" required autofocus class="field-input" placeholder="admin@schoolerp.in">
          @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <div class="field-wrap" x-data="{ show: false }">
          <label class="field-label">Password</label>
          <div class="pass-wrap">
            <input id="pass-field" :type="show ? 'text' : 'password'" name="password" required class="field-input" style="padding-right:2.75rem" placeholder="••••••••">
            <button type="button" class="pass-toggle" @click="show = !show">
              <svg x-show="!show" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg x-show="show"  style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
          </div>
        </div>
        <div class="row-between">
          <label class="remember-label"><input type="checkbox" name="remember" class="remember-cb"> Remember me</label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>
        <button type="submit" class="submit-btn" :disabled="loading">
          <span x-show="!loading">Sign In &rarr;</span>
          <span x-show="loading" style="display:flex;align-items:center;gap:.5rem">
            <svg class="spinner" style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            Signing in…
          </span>
        </button>
      </form>

      <p class="footer-text">&copy; {{ date('Y') }} DASA EduERP. All rights reserved.</p>
    </div>
  </div>

  {{-- ── All Roles Modal ── --}}
  <div class="demo-overlay" id="demo-overlay" onclick="closeDemoModal(event)">
    <div class="demo-modal" onclick="event.stopPropagation()">

      <div class="demo-modal-head">
        <div>
          <div class="demo-modal-title">All Demo Accounts</div>
          <div class="demo-modal-sub">Click any role to instantly fill the login form</div>
        </div>
        <button class="demo-close" onclick="closeDemoModal()">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="demo-modal-body">

        <div class="pw-badge">
          <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          All passwords: <strong>Demo@2026</strong> &nbsp;|&nbsp; Admin password: <strong>Admin@1234</strong>
        </div>

        {{-- Management --}}
        <div class="role-group">
          <div class="role-group-title">Management</div>
          <div class="role-grid">
            <button class="role-card" onclick="fillAndClose('admin@schoolerp.in','Admin@1234')">
              <div class="role-dot" style="background:#f59e0b"></div>
              <div class="role-card-inner"><div class="role-card-name">Super Admin</div><div class="role-card-email">admin@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('owner@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#dc2626"></div>
              <div class="role-card-inner"><div class="role-card-name">Owner</div><div class="role-card-email">owner@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('schooladmin@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#ea580c"></div>
              <div class="role-card-inner"><div class="role-card-name">School Admin</div><div class="role-card-email">schooladmin@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('principal@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#2563eb"></div>
              <div class="role-card-inner"><div class="role-card-name">Principal</div><div class="role-card-email">principal@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('viceprincipal@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#4f46e5"></div>
              <div class="role-card-inner"><div class="role-card-name">Vice Principal</div><div class="role-card-email">viceprincipal@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('hod@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#7c3aed"></div>
              <div class="role-card-inner"><div class="role-card-name">HOD</div><div class="role-card-email">hod@schoolerp.in</div></div>
            </button>
          </div>
        </div>

        {{-- Teaching --}}
        <div class="role-group">
          <div class="role-group-title">Teaching Staff</div>
          <div class="role-grid">
            <button class="role-card" onclick="fillAndClose('classteacher@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0891b2"></div>
              <div class="role-card-inner"><div class="role-card-name">Class Teacher</div><div class="role-card-email">classteacher@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('teacher@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0284c7"></div>
              <div class="role-card-inner"><div class="role-card-name">Teacher</div><div class="role-card-email">teacher@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('subjectteacher@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0369a1"></div>
              <div class="role-card-inner"><div class="role-card-name">Subject Teacher</div><div class="role-card-email">subjectteacher@schoolerp.in</div></div>
            </button>
          </div>
        </div>

        {{-- Operations & Admin --}}
        <div class="role-group">
          <div class="role-group-title">Administration &amp; Operations</div>
          <div class="role-grid">
            <button class="role-card" onclick="fillAndClose('accountant@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#16a34a"></div>
              <div class="role-card-inner"><div class="role-card-name">Accountant</div><div class="role-card-email">accountant@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('hr@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#059669"></div>
              <div class="role-card-inner"><div class="role-card-name">HR Manager</div><div class="role-card-email">hr@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('admissions@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0d9488"></div>
              <div class="role-card-inner"><div class="role-card-name">Admissions</div><div class="role-card-email">admissions@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('reception@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0891b2"></div>
              <div class="role-card-inner"><div class="role-card-name">Receptionist</div><div class="role-card-email">reception@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('itadmin@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#475569"></div>
              <div class="role-card-inner"><div class="role-card-name">IT Admin</div><div class="role-card-email">itadmin@schoolerp.in</div></div>
            </button>
          </div>
        </div>

        {{-- Facility & Campus --}}
        <div class="role-group">
          <div class="role-group-title">Campus &amp; Facilities</div>
          <div class="role-grid">
            <button class="role-card" onclick="fillAndClose('librarian@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#d97706"></div>
              <div class="role-card-inner"><div class="role-card-name">Librarian</div><div class="role-card-email">librarian@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('transport@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#b45309"></div>
              <div class="role-card-inner"><div class="role-card-name">Transport Mgr</div><div class="role-card-email">transport@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('hostelwarden@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#9333ea"></div>
              <div class="role-card-inner"><div class="role-card-name">Hostel Warden</div><div class="role-card-email">hostelwarden@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('warden@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#a855f7"></div>
              <div class="role-card-inner"><div class="role-card-name">Warden</div><div class="role-card-email">warden@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('inventory@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#0284c7"></div>
              <div class="role-card-inner"><div class="role-card-name">Inventory Mgr</div><div class="role-card-email">inventory@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('events@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#db2777"></div>
              <div class="role-card-inner"><div class="role-card-name">Event Coord</div><div class="role-card-email">events@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('alumni@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#4338ca"></div>
              <div class="role-card-inner"><div class="role-card-name">Alumni Coord</div><div class="role-card-email">alumni@schoolerp.in</div></div>
            </button>
          </div>
        </div>

        {{-- Portals --}}
        <div class="role-group">
          <div class="role-group-title">Portal Users</div>
          <div class="role-grid">
            <button class="role-card" onclick="fillAndClose('student@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#059669"></div>
              <div class="role-card-inner"><div class="role-card-name">Student Portal</div><div class="role-card-email">student@schoolerp.in</div></div>
            </button>
            <button class="role-card" onclick="fillAndClose('parent@schoolerp.in','Demo@2026')">
              <div class="role-dot" style="background:#7c3aed"></div>
              <div class="role-card-inner"><div class="role-card-name">Parent Portal</div><div class="role-card-email">parent@schoolerp.in</div></div>
            </button>
          </div>
        </div>

      </div>{{-- /body --}}

      <div class="demo-modal-foot">
        <span class="demo-modal-foot-text">Clicking any account fills the form and closes this dialog</span>
      </div>

    </div>
  </div>

<script>
function fillLogin(email, password) {
  const e = document.getElementById('email-field');
  const p = document.getElementById('pass-field');
  if (!e || !p) return;
  e.value = email; p.value = password;
  [e, p].forEach(el => {
    el.style.transition = 'border-color .2s, box-shadow .2s';
    el.style.borderColor = '#2563eb';
    el.style.boxShadow = '0 0 0 3px rgba(37,99,235,.15)';
    setTimeout(() => { el.style.borderColor = ''; el.style.boxShadow = ''; }, 800);
  });
  e.focus();
}
function openDemoModal() {
  const el = document.getElementById('demo-overlay');
  if (el) {
    el.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}
function closeDemoModal(e) {
  const el = document.getElementById('demo-overlay');
  if (el && (!e || e.target === el)) {
    el.classList.remove('show');
    document.body.style.overflow = '';
  }
}
function fillAndClose(email, password) {
  fillLogin(email, password);
  closeDemoModal();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDemoModal(); });
</script>
</body>
</html>
