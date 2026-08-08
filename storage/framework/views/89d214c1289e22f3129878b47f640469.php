<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>DASA EduERP — Complete School Management Platform</title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="description" content="The all-in-one school management platform trusted by leading institutions. Automate admissions, attendance, fees, exams, communication and more."/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;font-size:16px}
body{font-family:'Inter',system-ui,sans-serif;background:#fff;color:#0f172a;line-height:1.6;overflow-x:hidden}
a{text-decoration:none;color:inherit}
img{max-width:100%;display:block}
button{cursor:pointer;border:none;outline:none;font-family:inherit}

/* ─── TOKENS ─────────────────────────────────────── */
:root{
  --blue:#2563eb;
  --blue-l:#3b82f6;
  --blue-xl:#60a5fa;
  --indigo:#4f46e5;
  --navy:#0f172a;
  --slate:#1e293b;
  --muted:#64748b;
  --soft:#94a3b8;
  --border:#e2e8f0;
  --bg:#f8fafc;
  --bg2:#f1f5f9;
  --white:#ffffff;
  --radius:12px;
  --radius-lg:20px;
  --shadow-sm:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
  --shadow:0 4px 16px rgba(0,0,0,.08);
  --shadow-lg:0 16px 48px rgba(0,0,0,.12);
}

/* ─── TYPOGRAPHY ─────────────────────────────────── */
.h1{font-size:clamp(2.5rem,5vw,4rem);font-weight:800;line-height:1.1;letter-spacing:-.03em;color:var(--navy)}
.h2{font-size:clamp(1.75rem,3vw,2.5rem);font-weight:800;line-height:1.15;letter-spacing:-.025em;color:var(--navy)}
.h3{font-size:1.25rem;font-weight:700;color:var(--slate);line-height:1.3}
.lead{font-size:1.125rem;color:var(--muted);line-height:1.7;max-width:44ch}
.label{font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--blue)}

/* ─── LAYOUT ─────────────────────────────────────── */
.container{max-width:1160px;margin:0 auto;padding:0 1.5rem}
section{padding:5rem 0}

/* ─── BUTTONS ─────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.625rem;border-radius:100px;font-size:.9375rem;font-weight:600;transition:all .18s;white-space:nowrap}
.btn-primary{background:var(--blue);color:#fff;box-shadow:0 4px 14px rgba(37,99,235,.3)}
.btn-primary:hover{background:#1d4ed8;box-shadow:0 6px 20px rgba(37,99,235,.4);transform:translateY(-1px)}
.btn-ghost{background:transparent;color:var(--slate);border:1.5px solid var(--border)}
.btn-ghost:hover{border-color:#94a3b8;background:var(--bg)}
.btn-white{background:#fff;color:var(--blue);box-shadow:var(--shadow)}
.btn-white:hover{box-shadow:var(--shadow-lg);transform:translateY(-1px)}
.btn-lg{padding:.9375rem 2rem;font-size:1rem}
.btn svg{flex-shrink:0}

/* ─── NAV ─────────────────────────────────────────── */
#nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(16px);border-bottom:1px solid rgba(226,232,240,.8);transition:box-shadow .2s}
#nav.scrolled{box-shadow:0 2px 12px rgba(0,0,0,.07)}
.nav-inner{display:flex;align-items:center;height:4rem;gap:2rem}
.nav-logo{display:flex;align-items:center;gap:.625rem;font-weight:800;font-size:1.0625rem;color:var(--navy);flex-shrink:0}
.nav-logo-icon{width:2rem;height:2rem;border-radius:.5rem;background:linear-gradient(135deg,var(--blue),var(--indigo));display:flex;align-items:center;justify-content:center;flex-shrink:0}
.nav-links{display:flex;align-items:center;gap:.25rem;margin-left:1.5rem}
.nav-link{padding:.4rem .875rem;border-radius:.5rem;font-size:.875rem;font-weight:500;color:var(--muted);transition:all .12s}
.nav-link:hover{color:var(--navy);background:var(--bg)}
.nav-cta{margin-left:auto;display:flex;align-items:center;gap:.75rem}
.nav-login{font-size:.875rem;font-weight:600;color:var(--slate)}
.nav-login:hover{color:var(--blue)}
.hamburger{display:none;padding:.5rem;background:none;border:none;cursor:pointer}
.mobile-menu{display:none;flex-direction:column;gap:.25rem;padding:.75rem 1.5rem 1rem;border-top:1px solid var(--border);background:#fff}
.mobile-menu.open{display:flex}
.mobile-menu a{padding:.625rem .75rem;border-radius:.5rem;font-size:.9375rem;font-weight:500;color:var(--slate)}
.mobile-menu a:hover{background:var(--bg)}
@media(max-width:768px){
  .nav-links,.nav-cta .btn-primary{display:none}
  .hamburger{display:block}
  .nav-cta{gap:.5rem}
}

/* ─── HERO ─────────────────────────────────────────── */
.hero{background:linear-gradient(135deg,#fff 0%,#f0f7ff 55%,#e8efff 100%);padding:5rem 0 4rem;overflow:hidden}
.hero-split{display:grid;grid-template-columns:1fr 1fr;gap:3.5rem;align-items:center;min-height:480px}
.hero-left{}
.hero-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem 1rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:100px;font-size:.8125rem;font-weight:600;color:var(--blue);margin-bottom:1.75rem}
.hero-badge span{width:.5rem;height:.5rem;border-radius:50%;background:var(--blue-l);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.grad{background:linear-gradient(135deg,var(--blue),var(--indigo));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-cta{display:flex;flex-wrap:wrap;gap:.875rem;margin-top:2.25rem}
.hero-note{display:flex;align-items:center;gap:.5rem;margin-top:1.125rem;font-size:.8125rem;color:var(--soft)}
.hero-note svg{flex-shrink:0}
/* right visual column */
.hero-right{position:relative;padding:1.5rem 0 1.5rem 1rem}
.hero-deco{position:absolute;border-radius:16px;pointer-events:none}
.hero-deco-1{width:140px;height:140px;background:#fef9c3;top:-18px;left:-18px;z-index:0;opacity:.85}
.hero-deco-2{width:70px;height:70px;background:#d1fae5;bottom:10px;right:-12px;z-index:0;opacity:.9}
.hero-browser{position:relative;z-index:1;background:#fff;border-radius:1rem;box-shadow:0 24px 64px rgba(37,99,235,.18),0 4px 16px rgba(0,0,0,.07);overflow:hidden;border:1px solid var(--border)}
.browser-bar{display:flex;align-items:center;gap:.5rem;padding:.55rem .875rem;background:#f8fafc;border-bottom:1px solid var(--border)}
.browser-dot{width:.55rem;height:.55rem;border-radius:50%}
.browser-url{flex:1;background:#fff;border:1px solid var(--border);border-radius:.375rem;padding:.2rem .625rem;font-size:.7rem;color:var(--soft);margin:0 .375rem}
.browser-body{padding:.875rem;background:var(--bg)}
.db-grid{display:grid;grid-template-columns:130px 1fr;gap:.75rem;min-height:220px}
.db-sidebar{background:#fff;border-radius:.5rem;border:1px solid var(--border);padding:.5rem;display:flex;flex-direction:column;gap:.2rem}
.db-slink{display:flex;align-items:center;gap:.4rem;padding:.3rem .5rem;border-radius:.3rem;font-size:.68rem;font-weight:500;color:var(--muted)}
.db-slink.active{background:#eff6ff;color:var(--blue)}
.db-slink svg{width:.75rem;height:.75rem;flex-shrink:0}
.db-main{display:flex;flex-direction:column;gap:.625rem}
.db-stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem}
.db-stat{background:#fff;border-radius:.5rem;border:1px solid var(--border);padding:.55rem .625rem}
.db-stat-val{font-size:1rem;font-weight:800;color:var(--navy)}
.db-stat-label{font-size:.58rem;color:var(--soft);font-weight:500;margin-top:.1rem}
.db-stat-badge{display:inline-flex;align-items:center;gap:.2rem;font-size:.54rem;font-weight:700;padding:.1rem .35rem;border-radius:100px;margin-top:.2rem}
.db-green{background:#dcfce7;color:#16a34a}
.db-chart{background:#fff;border-radius:.5rem;border:1px solid var(--border);padding:.55rem .625rem;flex:1}
.db-chart-title{font-size:.62rem;font-weight:600;color:var(--slate);margin-bottom:.45rem}
.chart-bars{display:flex;align-items:flex-end;gap:.3rem;height:54px}
.bar{flex:1;border-radius:.2rem .2rem 0 0;opacity:.85}
.db-table-wrap{background:#fff;border-radius:.5rem;border:1px solid var(--border);overflow:hidden}
.db-table{width:100%;border-collapse:collapse;font-size:.6rem}
.db-table th{background:#f8fafc;padding:.35rem .625rem;text-align:left;font-weight:600;color:var(--soft);border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.04em;font-size:.55rem}
.db-table td{padding:.35rem .625rem;color:var(--slate);border-bottom:1px solid var(--border)}
.db-table tr:last-child td{border:none}
.db-pill{display:inline-block;padding:.1rem .4rem;border-radius:100px;font-size:.55rem;font-weight:600}
.pill-green{background:#dcfce7;color:#15803d}
.pill-amber{background:#fef3c7;color:#b45309}
.pill-blue{background:#dbeafe;color:#1d4ed8}

/* ─── TRUST BAR ─────────────────────────────────────── */
.trust-bar{padding:2.5rem 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:#fff}
.trust-inner{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:2.5rem}
.trust-stat{text-align:center}
.trust-num{font-size:2rem;font-weight:800;color:var(--navy);line-height:1}
.trust-label{font-size:.8125rem;color:var(--soft);margin-top:.25rem;font-weight:500}

/* ─── FEATURES ───────────────────────────────────────── */
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3rem}
.feat-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;transition:all .2s}
.feat-card:hover{box-shadow:var(--shadow-lg);border-color:transparent;transform:translateY(-3px)}
.feat-icon{width:3rem;height:3rem;border-radius:.875rem;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;flex-shrink:0}
.feat-card h3{font-size:1.0625rem;font-weight:700;color:var(--navy);margin-bottom:.625rem}
.feat-card p{font-size:.9375rem;color:var(--muted);line-height:1.6}

/* ─── MODULES ────────────────────────────────────────── */
.modules-section{background:var(--bg)}
.modules-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1rem;margin-top:3rem}
.mod-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem 1.375rem;display:flex;align-items:flex-start;gap:.875rem;transition:all .18s}
.mod-card:hover{border-color:var(--blue-xl);box-shadow:0 4px 16px rgba(37,99,235,.1);transform:translateY(-2px)}
.mod-dot{width:2.5rem;height:2.5rem;border-radius:.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mod-card h4{font-size:.9375rem;font-weight:600;color:var(--navy);margin-bottom:.2rem}
.mod-card p{font-size:.8rem;color:var(--muted);line-height:1.5}

/* ─── HOW IT WORKS ───────────────────────────────────── */
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:3.5rem;position:relative}
.steps::before{content:'';position:absolute;top:1.875rem;left:calc(16.66% + 1.875rem);right:calc(16.66% + 1.875rem);height:2px;background:linear-gradient(90deg,var(--blue),var(--indigo));border-radius:1px}
.step{text-align:center;padding:0 .5rem}
.step-num{width:3.75rem;height:3.75rem;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--indigo));color:#fff;font-size:1.25rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;box-shadow:0 4px 14px rgba(37,99,235,.35)}
.step h3{margin-bottom:.5rem}
.step p{font-size:.9375rem;color:var(--muted);line-height:1.6}

/* ─── TESTIMONIALS ───────────────────────────────────── */
.testimonials-section{background:var(--bg)}
.testimonials-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3rem}
.testi-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.75rem}
.testi-stars{display:flex;gap:.25rem;margin-bottom:1rem}
.testi-star{color:#f59e0b;font-size:1rem}
.testi-quote{font-size:.9375rem;color:var(--slate);line-height:1.65;margin-bottom:1.25rem;font-style:italic}
.testi-author{display:flex;align-items:center;gap:.75rem}
.testi-avatar{width:2.5rem;height:2.5rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.875rem;font-weight:700;color:#fff;flex-shrink:0}
.testi-name{font-size:.875rem;font-weight:600;color:var(--navy)}
.testi-role{font-size:.75rem;color:var(--soft)}

/* ─── FAQ ────────────────────────────────────────────── */
.faq-list{max-width:720px;margin:3rem auto 0;display:flex;flex-direction:column;gap:.75rem}
.faq-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.faq-q{width:100%;display:flex;align-items:center;justify-content:space-between;padding:1.125rem 1.375rem;font-size:.9375rem;font-weight:600;color:var(--navy);background:none;text-align:left;gap:1rem;transition:background .12s}
.faq-q:hover{background:var(--bg)}
.faq-q svg{flex-shrink:0;transition:transform .2s;color:var(--muted)}
.faq-item.open .faq-q svg{transform:rotate(45deg)}
.faq-a{display:none;padding:0 1.375rem 1.125rem;font-size:.9375rem;color:var(--muted);line-height:1.7}
.faq-item.open .faq-a{display:block}

/* ─── CTA SECTION ────────────────────────────────────── */
.cta-section{background:linear-gradient(135deg,var(--navy),#1e3a8a);padding:5rem 0;text-align:center;overflow:hidden;position:relative}
.cta-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(99,102,241,.3) 0%,transparent 70%);pointer-events:none}
.cta-section h2{color:#fff;margin-bottom:1rem}
.cta-section p{color:rgba(255,255,255,.65);font-size:1.125rem;max-width:40ch;margin:0 auto 2.25rem}
.cta-btns{display:flex;flex-wrap:wrap;justify-content:center;gap:1rem}

/* ─── FOOTER ─────────────────────────────────────────── */
footer{background:var(--navy);color:rgba(255,255,255,.6);padding:4rem 0 2rem}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:3rem;padding-bottom:3rem;border-bottom:1px solid rgba(255,255,255,.1)}
.footer-brand p{font-size:.9375rem;line-height:1.7;margin:.875rem 0 1.25rem;max-width:28ch}
.footer-col h5{font-size:.8125rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);margin-bottom:.875rem}
.footer-col a{display:block;font-size:.9rem;color:rgba(255,255,255,.55);margin-bottom:.5rem;transition:color .12s}
.footer-col a:hover{color:#fff}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:2rem;font-size:.8125rem;flex-wrap:wrap;gap:.75rem}
.footer-logo-wrap{display:flex;align-items:center;gap:.5rem;font-weight:800;font-size:1rem;color:#fff;margin-bottom:.5rem}

/* ─── SECTION HEADER ─────────────────────────────────── */
.sec-header{text-align:center;max-width:640px;margin:0 auto}
.sec-header .label{margin-bottom:.75rem}
.sec-header .h2{margin-bottom:1rem}
.sec-header .lead{margin:0 auto}

/* ─── UTILS ──────────────────────────────────────────── */
.text-center{text-align:center}
.green-check{color:#16a34a}

/* ─── RESPONSIVE ─────────────────────────────────────── */
@media(max-width:900px){
  .features-grid,.steps,.testimonials-grid{grid-template-columns:repeat(2,1fr)}
  .steps::before{display:none}
  .footer-top{grid-template-columns:1fr 1fr}
  .db-grid{grid-template-columns:1fr}
  .db-sidebar{display:none}
  .db-stat-row{grid-template-columns:repeat(2,1fr)}
  .hero-split{grid-template-columns:1fr;gap:2.5rem}
  .hero-left{text-align:center}
  .hero-cta{justify-content:center}
  .hero-note{justify-content:center;flex-wrap:wrap}
  .hero-right{max-width:560px;margin:0 auto;padding:1.5rem 0}
  .hero-deco-1{left:-8px}
  .hero-deco-2{right:-4px}
}
@media(max-width:600px){
  section{padding:3.5rem 0}
  .features-grid,.testimonials-grid,.steps{grid-template-columns:1fr}
  .footer-top{grid-template-columns:1fr}
  .trust-inner{gap:1.5rem}
  .db-stat-row{grid-template-columns:repeat(2,1fr)}
  .hero-deco-1{width:90px;height:90px}
  .hero-deco-2{width:48px;height:48px}
}
</style>
</head>
<body>

<!-- ═══ NAV ════════════════════════════════════════════ -->
<nav id="nav">
  <div class="container">
    <div class="nav-inner">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">
          <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        DASA EduERP
      </a>
      <div class="nav-links">
        <a href="#features" class="nav-link">Features</a>
        <a href="#modules" class="nav-link">Modules</a>
        <a href="#how-it-works" class="nav-link">How It Works</a>
        <a href="#faq" class="nav-link">FAQ</a>
      </div>
      <div class="nav-cta">
        <a href="<?php echo e(route('login')); ?>" class="nav-login">Sign In</a>
        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary" style="padding:.55rem 1.25rem;font-size:.875rem;">Get Started</a>
        <button class="hamburger" onclick="document.getElementById('mobile-menu').classList.toggle('open')" aria-label="Menu">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
      </div>
    </div>
  </div>
  <div id="mobile-menu" class="mobile-menu">
    <a href="#features">Features</a>
    <a href="#modules">Modules</a>
    <a href="#how-it-works">How It Works</a>
    <a href="#faq">FAQ</a>
    <a href="<?php echo e(route('login')); ?>" style="background:var(--blue);color:#fff;text-align:center;border-radius:.5rem;margin-top:.5rem;font-weight:600;">Get Started Free</a>
  </div>
</nav>

<!-- ═══ HERO ════════════════════════════════════════════ -->
<section class="hero">
  <div class="container">
    <div class="hero-split">

      <!-- LEFT: Text content -->
      <div class="hero-left">
        <div class="hero-badge">
          <span></span>
          Complete School ERP Platform
        </div>
        <h1 class="h1">Run Your School<br>with <span class="grad">Effortless Control</span></h1>
        <p class="lead" style="margin-top:1.375rem;">One unified platform for admissions, attendance, fees, academics, communication and more — built for modern schools that demand results.</p>
        <div class="hero-cta">
          <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-lg">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Start Free Demo
          </a>
          <a href="#features" class="btn btn-ghost btn-lg">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            See Features
          </a>
        </div>
        <div class="hero-note">
          <svg width="14" height="14" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          No credit card required &nbsp;·&nbsp;
          <svg width="14" height="14" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          &nbsp;Setup in minutes &nbsp;·&nbsp;
          <svg width="14" height="14" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          &nbsp;Full support included
        </div>
      </div>

      <!-- RIGHT: Dashboard visual + decorative accent squares -->
      <div class="hero-right">
        <div class="hero-deco hero-deco-1"></div>
        <div class="hero-deco hero-deco-2"></div>
        <div class="hero-browser">
          <div class="browser-bar">
            <div class="browser-dot" style="background:#ff5f57"></div>
            <div class="browser-dot" style="background:#febc2e"></div>
            <div class="browser-dot" style="background:#28c840"></div>
            <div class="browser-url">dasaeduerp.com/dashboard</div>
            <svg width="12" height="12" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          </div>
          <div class="browser-body">
            <div class="db-grid">
              <!-- Sidebar -->
              <div class="db-sidebar">
                <div style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--soft);padding:.2rem .5rem;margin-bottom:.2rem;">Menu</div>
                <div class="db-slink active">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                  Dashboard
                </div>
                <div class="db-slink">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                  Students
                </div>
                <div class="db-slink">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                  Attendance
                </div>
                <div class="db-slink">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  Fees
                </div>
                <div class="db-slink">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                  Exams
                </div>
                <div class="db-slink">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  Messages
                </div>
              </div>
              <!-- Main content -->
              <div class="db-main">
                <div class="db-stat-row">
                  <div class="db-stat">
                    <div class="db-stat-val">1,248</div>
                    <div class="db-stat-label">Students</div>
                    <div class="db-stat-badge db-green">↑ 12%</div>
                  </div>
                  <div class="db-stat">
                    <div class="db-stat-val">94.2%</div>
                    <div class="db-stat-label">Attendance</div>
                    <div class="db-stat-badge db-green">↑ 2.1%</div>
                  </div>
                  <div class="db-stat">
                    <div class="db-stat-val">₹8.4L</div>
                    <div class="db-stat-label">Fees</div>
                    <div class="db-stat-badge db-green">↑ 18%</div>
                  </div>
                  <div class="db-stat">
                    <div class="db-stat-val">87</div>
                    <div class="db-stat-label">Staff</div>
                    <div class="db-stat-badge db-green">All in</div>
                  </div>
                </div>
                <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:.5rem;">
                  <div class="db-chart">
                    <div class="db-chart-title">Monthly Attendance</div>
                    <div class="chart-bars">
                      <div class="bar" style="height:65%;background:#3b82f6"></div>
                      <div class="bar" style="height:80%;background:#3b82f6"></div>
                      <div class="bar" style="height:72%;background:#3b82f6"></div>
                      <div class="bar" style="height:90%;background:#3b82f6"></div>
                      <div class="bar" style="height:85%;background:#3b82f6"></div>
                      <div class="bar" style="height:94%;background:#2563eb"></div>
                      <div class="bar" style="height:88%;background:#c7d2fe"></div>
                    </div>
                  </div>
                  <div class="db-chart">
                    <div class="db-chart-title">Fee Collection</div>
                    <div class="chart-bars">
                      <div class="bar" style="height:55%;background:#10b981"></div>
                      <div class="bar" style="height:70%;background:#10b981"></div>
                      <div class="bar" style="height:60%;background:#10b981"></div>
                      <div class="bar" style="height:85%;background:#10b981"></div>
                      <div class="bar" style="height:92%;background:#059669"></div>
                      <div class="bar" style="height:78%;background:#a7f3d0"></div>
                    </div>
                  </div>
                </div>
                <div class="db-table-wrap">
                  <table class="db-table">
                    <thead><tr><th>Student</th><th>Class</th><th>Attend.</th><th>Fees</th><th>Status</th></tr></thead>
                    <tbody>
                      <tr><td>Aarav Sharma</td><td>X-A</td><td>98%</td><td>Paid</td><td><span class="db-pill pill-green">Active</span></td></tr>
                      <tr><td>Priya Patel</td><td>IX-B</td><td>91%</td><td>Partial</td><td><span class="db-pill pill-amber">Pending</span></td></tr>
                      <tr><td>Rohit Mehta</td><td>XI-C</td><td>95%</td><td>Paid</td><td><span class="db-pill pill-green">Active</span></td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══ TRUST BAR ════════════════════════════════════════ -->
<div class="trust-bar">
  <div class="container">
    <div class="trust-inner">
      <div class="trust-stat"><div class="trust-num">20+</div><div class="trust-label">Modules Built-In</div></div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="trust-stat"><div class="trust-num">500+</div><div class="trust-label">Schools Ready</div></div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="trust-stat"><div class="trust-num">50K+</div><div class="trust-label">Students Managed</div></div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="trust-stat"><div class="trust-num">99.9%</div><div class="trust-label">Uptime SLA</div></div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="trust-stat"><div class="trust-num">4.9★</div><div class="trust-label">Average Rating</div></div>
    </div>
  </div>
</div>

<!-- ═══ FEATURES ═════════════════════════════════════════ -->
<section id="features">
  <div class="container">
    <div class="sec-header">
      <p class="label">Why DASA EduERP</p>
      <h2 class="h2">Everything your school needs,<br>in one place</h2>
      <p class="lead">Stop juggling spreadsheets, paper registers, and disconnected apps. DASA EduERP brings it all together — seamlessly.</p>
    </div>
    <div class="features-grid">
      <div class="feat-card">
        <div class="feat-icon" style="background:#eff6ff;">
          <svg width="22" height="22" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <h3>Instant Setup</h3>
        <p>Go live in hours, not months. Import your existing student data, configure your school profile, and you're running.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:#f0fdf4;">
          <svg width="22" height="22" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h3>Bank-Level Security</h3>
        <p>Role-based access control, encrypted data at rest, audit logs, and IP whitelisting keep your school data safe.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:#fdf4ff;">
          <svg width="22" height="22" fill="none" stroke="#9333ea" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <h3>Parent &amp; Student Portal</h3>
        <p>Parents see attendance, fees, results, and notices in real-time. Students access homework and course materials — anywhere.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:#fff7ed;">
          <svg width="22" height="22" fill="none" stroke="#ea580c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h3>Powerful Analytics</h3>
        <p>Executive dashboards, fee collection trends, attendance heatmaps, and custom report builder — all in real time.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:#eff6ff;">
          <svg width="22" height="22" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h3>Smart Communication</h3>
        <p>Bulk email notices to parents and staff. Track read receipts, send targeted announcements, and maintain a full log.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:#fef9c3;">
          <svg width="22" height="22" fill="none" stroke="#ca8a04" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h3>Integrated LMS</h3>
        <p>Publish courses, video lessons, quizzes, and assignments. Track student progress with detailed completion reports.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ MODULES ══════════════════════════════════════════ -->
<section id="modules" class="modules-section">
  <div class="container">
    <div class="sec-header">
      <p class="label">20 Integrated Modules</p>
      <h2 class="h2">One platform, every school function</h2>
      <p class="lead">No add-ons. No extra licenses. Everything is included and works together out of the box.</p>
    </div>
    <div class="modules-grid">
      <?php
        $modules = [
          ['Admissions', 'Online applications, document verification & enrolment', '#eff6ff', '#2563eb'],
          ['Students', 'Profiles, ID cards, promotions & records', '#f0fdf4', '#16a34a'],
          ['Academics', 'Classes, subjects, timetables & curriculum', '#fdf4ff', '#9333ea'],
          ['Attendance', 'Daily marking, reports & parent alerts', '#fff7ed', '#ea580c'],
          ['Exams & Results', 'Schedules, mark entry, grades & report cards', '#eff6ff', '#0284c7'],
          ['Fee Management', 'Invoicing, payments, receipts & dues tracking', '#fef9c3', '#ca8a04'],
          ['HR & Payroll', 'Staff profiles, leaves, salary & payslips', '#f0fdf4', '#059669'],
          ['Library', 'Catalogue, issues, returns & fine management', '#fdf4ff', '#7c3aed'],
          ['Transport', 'Routes, vehicles, stops & student bus passes', '#fff1f2', '#e11d48'],
          ['Hostel', 'Room allocation, warden records & visitor log', '#f0f9ff', '#0369a1'],
          ['Parent Portal', 'Real-time visibility for guardians', '#fdf4ff', '#9333ea'],
          ['Communication', 'Bulk email, notices & read receipts', '#eff6ff', '#2563eb'],
          ['Reports', 'Analytics, dashboards & custom report builder', '#fff7ed', '#c2410c'],
          ['Inventory', 'Stock, purchase orders & GRN workflow', '#f0fdf4', '#15803d'],
          ['Events', 'Calendar, RSVP & photo gallery', '#fef9c3', '#b45309'],
          ['Gate & Visitors', 'Visitor passes, QR check-in & blacklist', '#fff1f2', '#be123c'],
          ['LMS', 'Courses, quizzes, assignments & progress', '#eff6ff', '#1d4ed8'],
          ['Alumni', 'Directory, achievements & newsletter', '#fdf4ff', '#6d28d9'],
          ['System Admin', 'RBAC, audit log, backups & school settings', '#f1f5f9', '#475569'],
        ];
        $icons = [
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87V15.13a1 1 0 01-1.447.899L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ];
      ?>
      <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="mod-card">
        <div class="mod-dot" style="background:<?php echo e($mod[2]); ?>">
          <svg width="18" height="18" fill="none" stroke="<?php echo e($mod[3]); ?>" viewBox="0 0 24 24"><?php echo $icons[$i]; ?></svg>
        </div>
        <div>
          <h4><?php echo e($mod[0]); ?></h4>
          <p><?php echo e($mod[1]); ?></p>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ═════════════════════════════════════ -->
<section id="how-it-works">
  <div class="container">
    <div class="sec-header">
      <p class="label">Get Started in 3 Steps</p>
      <h2 class="h2">Up and running in hours</h2>
      <p class="lead">No IT team needed. Our onboarding wizard guides you from setup to live school operations.</p>
    </div>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <h3 class="h3">Configure Your School</h3>
        <p>Set up classes, sections, fee heads, and academic year. Import existing student data via Excel — done in minutes.</p>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <h3 class="h3">Invite Your Team</h3>
        <p>Add admins, teachers, and office staff with tailored roles and permissions. No one sees more than they should.</p>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <h3 class="h3">Go Live</h3>
        <p>Share portal links with parents and students. Start marking attendance, collecting fees, and sending notices — today.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ══════════════════════════════════════ -->
<section class="testimonials-section">
  <div class="container">
    <div class="sec-header">
      <p class="label">What Schools Say</p>
      <h2 class="h2">Loved by school leaders</h2>
    </div>
    <div class="testimonials-grid">
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-quote">"We replaced four different software systems with DASA EduERP. Attendance, fees, and communication now happen in one place. Our admin work dropped by 60%."</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#3b82f6,#6366f1)">RS</div>
          <div>
            <div class="testi-name">Rajesh Sharma</div>
            <div class="testi-role">Principal, Sunrise Academy, Jaipur</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-quote">"Parents love the portal. They can see attendance and fees without calling the office. Our phone inquiries dropped by 80% in the first month."</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#10b981,#059669)">PM</div>
          <div>
            <div class="testi-name">Priya Mehta</div>
            <div class="testi-role">Administrator, Greenwood School, Pune</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-quote">"The fee management module alone saved us weeks of manual reconciliation. Collections are up 25% since we started sending automated reminders."</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706)">AK</div>
          <div>
            <div class="testi-name">Amit Kumar</div>
            <div class="testi-role">Director, The Future School, Delhi</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FAQ ══════════════════════════════════════════════ -->
<section id="faq">
  <div class="container">
    <div class="sec-header">
      <p class="label">FAQ</p>
      <h2 class="h2">Common questions</h2>
    </div>
    <div class="faq-list">
      <?php
        $faqs = [
          ['How long does it take to set up?', 'Most schools are fully operational within 1–2 days. Our onboarding wizard walks you through school configuration, data import, and user creation step by step.'],
          ['Can I import existing student data?', 'Yes. You can bulk import students, staff, and historical records using our Excel templates. We support migration from most legacy school software formats.'],
          ['Is there a mobile app for parents?', 'The parent and student portal is fully mobile-responsive and works on any browser — no app download needed. Parents can check attendance, fees, and notices from their phone instantly.'],
          ['How is data kept secure?', 'All data is encrypted at rest and in transit (TLS 1.3). We provide role-based access control, IP whitelisting, complete audit logs, and daily automated backups.'],
          ['Can multiple schools use one installation?', 'Yes, the system supports multiple branches/schools from a single admin panel. Each branch has its own data isolation with a shared management view at the top.'],
          ['What kind of support is provided?', 'Every plan includes email and chat support. We also provide onboarding assistance, video tutorials, and a full documentation library to get your team productive fast.'],
        ];
      ?>
      <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$q, $a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="faq-item" id="faq-<?php echo e($i); ?>">
        <button class="faq-q" onclick="toggleFaq('faq-<?php echo e($i); ?>')">
          <span><?php echo e($q); ?></span>
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        </button>
        <div class="faq-a"><?php echo e($a); ?></div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>

<!-- ═══ CTA ═══════════════════════════════════════════════ -->
<section class="cta-section">
  <div class="container" style="position:relative;z-index:1;">
    <p class="label" style="color:rgba(255,255,255,.5);margin-bottom:.875rem;">Get Started Today</p>
    <h2 class="h2" style="color:#fff;margin-bottom:1.125rem;">Ready to modernize<br>your school?</h2>
    <p style="color:rgba(255,255,255,.6);font-size:1.0625rem;max-width:42ch;margin:0 auto 2.25rem;line-height:1.65;">Join schools that trust DASA EduERP to run their daily operations. Free demo — no commitment required.</p>
    <div class="cta-btns">
      <a href="<?php echo e(route('login')); ?>" class="btn btn-white btn-lg">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Start Free Demo
      </a>
      <a href="mailto:dcinnovisions@gmail.com" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.2);backdrop-filter:blur(8px);">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Contact Sales
      </a>
    </div>
    <p style="color:rgba(255,255,255,.35);font-size:.8125rem;margin-top:1.5rem;">No credit card required &nbsp;·&nbsp; Full data export any time &nbsp;·&nbsp; Cancel anytime</p>
  </div>
</section>

<!-- ═══ FOOTER ════════════════════════════════════════════ -->
<footer>
  <div class="container">
    <div class="footer-top">
      <div class="footer-brand">
        <div class="footer-logo-wrap">
          <div class="nav-logo-icon">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
          </div>
          DASA EduERP
        </div>
        <p>The complete school management platform built for modern educational institutions. 20 modules, one login.</p>
        <p style="font-size:.8125rem;">
          <a href="mailto:dcinnovisions@gmail.com" style="color:rgba(255,255,255,.5);transition:color .1s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">dcinnovisions@gmail.com</a>
        </p>
      </div>
      <div class="footer-col">
        <h5>Product</h5>
        <a href="#features">Features</a>
        <a href="#modules">Modules</a>
        <a href="#how-it-works">How It Works</a>
        <a href="<?php echo e(route('login')); ?>">Login</a>
      </div>
      <div class="footer-col">
        <h5>Modules</h5>
        <a href="#">Admissions</a>
        <a href="#">Attendance</a>
        <a href="#">Fee Management</a>
        <a href="#">Parent Portal</a>
        <a href="#">LMS</a>
      </div>
      <div class="footer-col">
        <h5>Company</h5>
        <a href="#">About DASA EduERP</a>
        <a href="mailto:dcinnovisions@gmail.com">Contact</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo e(date('Y')); ?> DASA EduERP. All rights reserved.</span>
      <span>Built with ❤️ for Indian schools</span>
    </div>
  </div>
</footer>

<script>
// Sticky nav shadow
window.addEventListener('scroll', () => {
  document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 10);
});

// FAQ accordion
function toggleFaq(id) {
  const item = document.getElementById(id);
  const isOpen = item.classList.contains('open');
  document.querySelectorAll('.faq-item').forEach(el => el.classList.remove('open'));
  if (!isOpen) item.classList.add('open');
}

// Smooth scroll for nav links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href').slice(1);
    const el = document.getElementById(id);
    if (el) { e.preventDefault(); el.scrollIntoView({ behavior:'smooth', block:'start' }); document.getElementById('mobile-menu').classList.remove('open'); }
  });
});
</script>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\landing.blade.php ENDPATH**/ ?>