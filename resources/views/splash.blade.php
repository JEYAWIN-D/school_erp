<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erode Public School &mdash; Welcome</title>
    <meta name="description" content="Erode Public School Senior Secondary &mdash; School Management Portal">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Cinzel:wght@600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { width: 100%; height: 100%; overflow: hidden; background: #0a0205; }

    /* Starfield canvas */
    #bg-canvas { position: fixed; inset: 0; width: 100%; height: 100%; z-index: 0; }

    /* Deep ambient crimson glow */
    .ambient {
        position: fixed; inset: 0; z-index: 1; pointer-events: none;
        background:
            radial-gradient(ellipse 90% 70% at 50% 0%,  rgba(139,17,24,0.4)  0%, transparent 60%),
            radial-gradient(ellipse 70% 50% at 85% 90%, rgba(180,30,30,0.22) 0%, transparent 60%),
            radial-gradient(ellipse 60% 40% at 10% 70%, rgba(100,0,10,0.28)  0%, transparent 60%),
            radial-gradient(ellipse 50% 50% at 50% 50%, rgba(60,0,5,0.45)    0%, transparent 70%);
    }

    /* Floating geometry */
    .geo { position: fixed; z-index: 1; pointer-events: none; border: 1px solid rgba(180,30,30,0.15); border-radius: 4px; animation: geoFloat linear infinite; }
    .g1 { width:160px; height:160px; top:8%;  left:6%;  animation-duration:18s; }
    .g2 { width:90px;  height:90px;  top:70%; left:3%;  animation-duration:23s; }
    .g3 { width:120px; height:120px; top:15%; right:5%; animation-duration:20s; }
    .g4 { width:60px;  height:60px;  top:75%; right:7%; animation-duration:15s; }
    .g5 { width:200px; height:200px; top:42%; left:1%;  animation-duration:28s; border-radius:50%; }
    @keyframes geoFloat { 0%{transform:translateY(0) rotate(0deg);opacity:.25;} 50%{opacity:.5;} 100%{transform:translateY(-30px) rotate(360deg);opacity:.25;} }

    /* Scene */
    .scene { position: fixed; inset: 0; z-index: 10; display: flex; align-items: center; justify-content: center; perspective: 1400px; }

    /* Main glassmorphic card */
    .card {
        position: relative; width: min(640px, 92vw); padding: 50px 50px 44px; border-radius: 20px;
        background: linear-gradient(145deg, rgba(139,17,24,0.2) 0%, rgba(18,2,4,0.88) 45%, rgba(8,1,3,0.93) 100%);
        backdrop-filter: blur(36px) saturate(220%); -webkit-backdrop-filter: blur(36px) saturate(220%);
        border: 1px solid rgba(180,30,30,0.3);
        box-shadow:
            0 0 0 1px rgba(180,30,30,0.12),
            0 60px 120px rgba(0,0,0,0.85),
            0 0 120px rgba(139,17,24,0.22),
            inset 0 1px 0 rgba(255,180,180,0.07),
            inset 0 -1px 0 rgba(0,0,0,0.5);
        text-align: center; transform-style: preserve-3d;
        transform: perspective(1400px) rotateX(30deg) rotateY(-12deg) scale(0.45) translateZ(-300px);
        opacity: 0;
        animation: cardIn 1.0s cubic-bezier(0.16,1,0.3,1) 0.05s forwards;
    }

    @keyframes cardIn {
        0%  { transform:perspective(1400px) rotateX(30deg)  rotateY(-12deg) scale(0.45) translateZ(-300px); opacity:0; }
        55% { transform:perspective(1400px) rotateX(-4deg)  rotateY(3deg)   scale(1.04) translateZ(15px);   opacity:1; }
        78% { transform:perspective(1400px) rotateX(1.5deg) rotateY(-1deg)  scale(0.99) translateZ(-8px);   opacity:1; }
        100%{ transform:perspective(1400px) rotateX(0deg)   rotateY(0deg)   scale(1)    translateZ(0px);    opacity:1; }
    }

    .card.exit { animation: cardOut 0.65s cubic-bezier(0.55,0,1,0.45) forwards !important; }
    @keyframes cardOut {
        0%  { transform:perspective(1400px) rotateX(0deg)  rotateY(0deg)   scale(1)    translateZ(0px);    opacity:1; }
        25% { transform:perspective(1400px) rotateX(-6deg) rotateY(5deg)   scale(1.05) translateZ(40px);   opacity:1; }
        100%{ transform:perspective(1400px) rotateX(40deg) rotateY(-15deg) scale(0.35) translateZ(-500px); opacity:0; }
    }

    /* Spinning shimmer border */
    .card::before {
        content:''; position:absolute; inset:-1px; border-radius:21px;
        background: conic-gradient(from 180deg at 50% 50%, rgba(180,30,30,0.9), rgba(220,60,60,0.55), rgba(255,200,200,0.35), rgba(139,17,24,0.8), rgba(180,30,30,0.9));
        z-index:-1; opacity:0; animation: bIn 0.4s ease 0.8s forwards;
    }
    /* Glow bloom */
    .card::after {
        content:''; position:absolute; inset:-2px; border-radius:22px;
        background: conic-gradient(from 0deg at 50% 50%, rgba(180,30,30,0.75), rgba(220,60,60,0.35), rgba(139,17,24,0.65), rgba(180,30,30,0.75));
        z-index:-2; filter:blur(28px); opacity:0; animation: gIn 0.6s ease 0.7s forwards;
    }
    @keyframes bIn { to{opacity:.65;} }
    @keyframes gIn { to{opacity:.32;} }

    /* Shield crest */
    .crest-wrap { display:flex; align-items:center; justify-content:center; margin-bottom:18px; opacity:0; animation: crestIn 0.7s cubic-bezier(0.34,1.56,0.64,1) 0.65s forwards; }
    @keyframes crestIn { 0%{opacity:0;transform:translateY(-40px) scale(0.6);} 60%{opacity:1;transform:translateY(4px) scale(1.08);} 100%{opacity:1;transform:translateY(0) scale(1);} }
    .shield-img { width:140px; height:140px; object-fit:contain; filter: drop-shadow(0 8px 24px rgba(0,0,0,0.75)) drop-shadow(0 0 24px rgba(220,60,60,0.4)); border-radius:50%; }

    /* Ornamental divider */
    .orn { display:flex; align-items:center; gap:12px; margin-bottom:14px; opacity:0; animation:up 0.45s ease 0.9s forwards; }
    .orn-l { flex:1; height:1px; background:linear-gradient(to right,transparent,rgba(180,30,30,0.65),transparent); }
    .orn-d { width:6px; height:6px; background:rgba(200,60,60,0.85); transform:rotate(45deg); box-shadow:0 0 10px rgba(220,60,60,0.9); }

    /* Typography */
    .sn  { font-family:'Cinzel',serif; font-size:clamp(22px,4.5vw,32px); font-weight:700; color:#fff; letter-spacing:.04em; line-height:1.15; text-shadow:0 0 32px rgba(220,60,60,0.65),0 2px 4px rgba(0,0,0,0.5); margin-bottom:6px; opacity:0; animation:up 0.45s ease 1.0s forwards; }
    .ss  { font-family:'Outfit',sans-serif; font-size:13px; font-weight:400; letter-spacing:.18em; text-transform:uppercase; color:rgba(220,140,140,0.8); margin-bottom:4px; opacity:0; animation:up 0.45s ease 1.05s forwards; }
    .sa  { font-family:'Inter',sans-serif; font-size:11px; font-weight:300; color:rgba(255,255,255,0.35); letter-spacing:.06em; margin-bottom:26px; opacity:0; animation:up 0.45s ease 1.1s forwards; }

    /* Stats strip */
    .stats { display:flex; background:rgba(139,17,24,0.15); border:1px solid rgba(180,30,30,0.2); border-radius:12px; margin-bottom:26px; overflow:hidden; opacity:0; animation:up 0.45s ease 1.15s forwards; }
    .sv { flex:1; padding:14px 10px; text-align:center; position:relative; }
    .sv:not(:last-child)::after { content:''; position:absolute; right:0; top:20%; bottom:20%; width:1px; background:rgba(180,30,30,0.25); }
    .sv-n { font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#fff; text-shadow:0 0 20px rgba(220,60,60,0.5); line-height:1; margin-bottom:3px; }
    .sv-l { font-size:9.5px; font-weight:400; color:rgba(255,180,180,0.55); letter-spacing:.12em; text-transform:uppercase; }

    /* Divider */
    .div { width:100%; height:1px; background:linear-gradient(to right,transparent,rgba(180,30,30,0.4),transparent); margin-bottom:22px; opacity:0; animation:up 0.45s ease 1.2s forwards; }

    /* Progress */
    .prog { opacity:0; animation:up 0.45s ease 1.25s forwards; }
    .prog-l { font-size:10px; font-weight:400; font-family:'Inter',sans-serif; color:rgba(255,255,255,0.3); letter-spacing:.15em; text-transform:uppercase; margin-bottom:10px; }
    .prog-t { width:100%; height:3px; background:rgba(255,255,255,0.07); border-radius:100px; overflow:hidden; }
    .prog-f { height:100%; width:0%; border-radius:100px; background:linear-gradient(90deg,#8b1118,#c0392b,#e74c3c,#ff6b6b,#c0392b,#8b1118); background-size:300% 100%; animation:fG 1.5s cubic-bezier(0.25,1,0.5,1) 1.1s forwards, fS 2s linear 1.1s infinite; }
    @keyframes fG { to{width:100%;} }
    @keyframes fS { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }

    @keyframes up { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }

    /* Fade overlay */
    .fo { position:fixed; inset:0; z-index:100; background:#0a0205; opacity:0; pointer-events:none; transition:opacity .55s ease; }
    .fo.on { opacity:1; pointer-events:all; }

    /* Corner accents */
    .co { position:absolute; width:22px; height:22px; opacity:0; animation:cA .3s ease 1.2s forwards; }
    .co.tl { top:12px; left:12px;  border-top:2px solid rgba(200,60,60,0.6); border-left:2px solid rgba(200,60,60,0.6); border-radius:4px 0 0 0; }
    .co.tr { top:12px; right:12px; border-top:2px solid rgba(200,60,60,0.6); border-right:2px solid rgba(200,60,60,0.6); border-radius:0 4px 0 0; }
    .co.bl { bottom:12px; left:12px;  border-bottom:2px solid rgba(200,60,60,0.6); border-left:2px solid rgba(200,60,60,0.6); border-radius:0 0 0 4px; }
    .co.br { bottom:12px; right:12px; border-bottom:2px solid rgba(200,60,60,0.6); border-right:2px solid rgba(200,60,60,0.6); border-radius:0 0 4px 0; }
    @keyframes cA { to{opacity:1;} }
    </style>
</head>
<body>
<canvas id="bg-canvas"></canvas>
<div class="ambient"></div>
<div class="geo g1"></div><div class="geo g2"></div><div class="geo g3"></div><div class="geo g4"></div><div class="geo g5"></div>

<div class="scene">
  <div class="card" id="mc">
    <div class="co tl"></div><div class="co tr"></div><div class="co bl"></div><div class="co br"></div>

    <!-- Official School Crest Logo -->
    <div class="crest-wrap">
      <img src="{{ asset('images/school-seal-badge.png') }}" alt="Erode Public School Crest" class="shield-img">
    </div>

    <!-- Ornament -->
    <div class="orn"><div class="orn-l"></div><div class="orn-d"></div><div class="orn-l"></div></div>

    <!-- School identity -->
    <h1 class="sn">Erode Public School</h1>
    <div class="ss">Senior Secondary</div>
    <div class="sa">Affiliated to CBSE, New Delhi &nbsp;&bull;&nbsp; Aff No. 1931585</div>

    <!-- Stats -->
    <div class="stats">
      <div class="sv"><div class="sv-n">20+</div><div class="sv-l">Modules</div></div>
      <div class="sv"><div class="sv-n">Smart</div><div class="sv-l">ERP</div></div>
      <div class="sv"><div class="sv-n">Since 1985</div><div class="sv-l">Excellence</div></div>
    </div>

    <div class="div"></div>

    <!-- Progress -->
    <div class="prog">
      <div class="prog-l">Launching your portal&hellip;</div>
      <div class="prog-t"><div class="prog-f"></div></div>
    </div>
  </div>
</div>

<div class="fo" id="fo"></div>

<script>
/* Starfield + ember particles */
(function(){
  var c=document.getElementById('bg-canvas'),x=c.getContext('2d'),W,H;
  var st=[],em=[];
  for(var i=0;i<120;i++) st.push({x:0,y:0,r:Math.random()*1.2+0.2,a:Math.random()*0.6+0.1,t:Math.random()*6.28});
  for(var j=0;j<22;j++) em.push({x:0,y:0,r:Math.random()*2.5+1,vx:(Math.random()-.5)*.3,vy:-(Math.random()*.4+.1),a:Math.random()*.4+.1});
  function ri(){W=c.width=innerWidth;H=c.height=innerHeight;st.forEach(function(s){s.x=Math.random()*W;s.y=Math.random()*H;});em.forEach(function(e){e.x=Math.random()*W;e.y=Math.random()*H;});}
  ri(); window.addEventListener('resize',ri);
  function d(){
    x.clearRect(0,0,W,H);
    st.forEach(function(s){s.t+=.025;var a=s.a*(.6+.4*Math.sin(s.t));x.beginPath();x.arc(s.x,s.y,s.r,0,6.28);x.fillStyle='rgba(255,200,200,'+a+')';x.fill();});
    em.forEach(function(e){e.x+=e.vx;e.y+=e.vy;if(e.y<-5){e.y=H+5;e.x=Math.random()*W;}if(e.x<-5)e.x=W+5;if(e.x>W+5)e.x=-5;x.beginPath();x.arc(e.x,e.y,e.r,0,6.28);x.fillStyle='rgba(220,60,60,'+e.a+')';x.fill();});
    requestAnimationFrame(d);
  }
  d();
})();
/* Auto-redirect to login after 2 seconds */
(function(){
  var card = document.getElementById('mc'), ov = document.getElementById('fo');
  var url = '{{ route("login") }}';
  var hasRedirected = false;

  function proceedToLogin(immediate) {
    if (hasRedirected) return;
    hasRedirected = true;
    card.classList.add('exit');
    setTimeout(function(){ ov.classList.add('on'); }, immediate ? 80 : 250);
    setTimeout(function(){ window.location.href = url; }, immediate ? 280 : 800);
  }

  // Auto proceed after 1.8 seconds of entry animation
  setTimeout(function(){
    proceedToLogin(false);
  }, 1800);

  // Click or keypress anywhere to skip directly
  document.addEventListener('click', function(){ proceedToLogin(true); });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape') {
      proceedToLogin(true);
    }
  });
})();
</script>
</body>
</html>