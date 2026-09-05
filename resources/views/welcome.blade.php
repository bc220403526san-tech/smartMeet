<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>SmartMeet — Simple Online Meetings &amp; Collaboration</title>
    <meta name="description" content="SmartMeet makes online meetings simple with video, audio, scheduling, real-time chat and live transcription.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           1. VARIABLES & RESET
           ============================================================ */
        :root{
            --navy:#0b1424;
            --navy-2:#0f1b31;
            --text:#0f1b2e;
            --muted:#5c6b82;
            --muted-soft:#8391a6;
            --blue:#2563eb;
            --blue-dark:#1846b3;
            --blue-soft:#eaf1ff;
            --bg:#ffffff;
            --bg-soft:#f5f9ff;
            --border:#e6edf7;
            --border-soft:#eef3fa;
            --radius-lg:22px;
            --radius-md:16px;
            --radius-sm:10px;
            --shadow-sm:0 2px 10px rgba(15,27,46,.05);
            --shadow-md:0 12px 32px rgba(15,27,46,.08);
            --shadow-lg:0 24px 60px rgba(15,27,46,.12);
            --ease:cubic-bezier(0.22,1,0.36,1);
            --dur:750ms;
        }
        *,*::before,*::after{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            margin:0;
            color:var(--text);
            background:var(--bg);
            font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
            line-height:1.6;
            -webkit-font-smoothing:antialiased;
            overflow-x:hidden;
        }
        h1,h2,h3,h4{
            font-family:'Manrope',sans-serif;
            color:var(--navy);
            margin:0;
            letter-spacing:-0.02em;
        }
        p{margin:0}
        a{text-decoration:none;color:inherit}
        img{max-width:100%;display:block}
        ul{list-style:none;margin:0;padding:0}
        button{font-family:inherit;cursor:pointer}
        .container{
            width:100%;
            max-width:1240px;
            margin:0 auto;
            padding:0 24px;
        }
        section{scroll-margin-top:90px}
        .eyebrow{
            display:inline-flex;align-items:center;gap:8px;
            font-size:12.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
            color:var(--blue);background:var(--blue-soft);
            padding:7px 14px;border-radius:99px;border:1px solid #d7e6ff;
        }
        .section-head{max-width:640px;margin-bottom:52px}
        .section-head h2{font-size:clamp(28px,3.4vw,42px);line-height:1.15;margin:14px 0 14px}
        .section-head p{color:var(--muted);font-size:16px;max-width:560px}
        .section-head.centered{margin-left:auto;margin-right:auto;text-align:center}

        /* Buttons */
        .btn{
            display:inline-flex;align-items:center;gap:9px;
            padding:14px 26px;border-radius:12px;font-weight:600;font-size:15px;
            border:1px solid transparent;transition:transform .25s var(--ease),box-shadow .25s var(--ease),background .25s var(--ease),color .25s var(--ease);
            white-space:nowrap;
        }
        .btn svg{transition:transform .25s var(--ease)}
        .btn:hover svg{transform:translateX(3px)}
        .btn-primary{background:var(--blue);color:#fff;box-shadow:0 10px 24px rgba(37,99,235,.28)}
        .btn-primary:hover{background:var(--blue-dark);transform:translateY(-2px);box-shadow:0 16px 34px rgba(37,99,235,.34)}
        .btn-secondary{background:#fff;color:var(--navy);border-color:var(--border)}
        .btn-secondary:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);border-color:#d7e2f3}
        .btn-ghost{background:transparent;color:var(--navy)}
        .btn-ghost:hover{color:var(--blue)}
        .btn-block{width:100%;justify-content:center}

        /* Reveal animations */
        .reveal,.reveal-up,.reveal-left,.reveal-right{opacity:0;transition:opacity var(--dur) var(--ease),transform var(--dur) var(--ease)}
        .reveal-up{transform:translateY(30px)}
        .reveal-left{transform:translateX(-30px)}
        .reveal-right{transform:translateX(30px)}
        .reveal{transform:translateY(16px)}
        .reveal.in-view,.reveal-up.in-view,.reveal-left.in-view,.reveal-right.in-view{opacity:1;transform:translate(0,0)}
        @media (prefers-reduced-motion: reduce){
            html{scroll-behavior:auto}
            .reveal,.reveal-up,.reveal-left,.reveal-right{opacity:1!important;transform:none!important;transition:none!important}
            .float,.hero-badge,.hero-title span,.hero-text,.hero-cta,.hero-mock{animation:none!important}
        }

        /* ============================================================
           2. NAVBAR
           ============================================================ */
        .navbar{
            position:fixed;top:0;left:0;right:0;z-index:100;
            padding:20px 0;transition:background .3s var(--ease),box-shadow .3s var(--ease),border-color .3s var(--ease),padding .3s var(--ease);
            border-bottom:1px solid transparent;
            animation:navIn .7s var(--ease) both;
        }
        @keyframes navIn{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}
        .navbar.scrolled{
            background:rgba(255,255,255,.86);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
            border-bottom-color:var(--border);box-shadow:0 6px 24px rgba(15,27,46,.05);padding:14px 0;
        }
        .nav-inner{display:flex;align-items:center;justify-content:space-between;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-family:'Manrope',sans-serif;font-weight:800;font-size:19px;color:var(--navy)}
        .brand img{width:32px;height:32px;object-fit:contain}
        .nav-links{display:flex;align-items:center;gap:36px}
        .nav-links a{font-size:14.5px;font-weight:600;color:var(--muted);transition:color .2s}
        .nav-links a:hover{color:var(--navy)}
        .nav-actions{display:flex;align-items:center;gap:10px}
        .hamburger{display:none;flex-direction:column;gap:5px;background:none;border:none;padding:6px}
        .hamburger span{width:22px;height:2px;background:var(--navy);border-radius:2px;transition:transform .3s var(--ease),opacity .3s var(--ease)}
        .hamburger.active span:nth-child(1){transform:translateY(7px) rotate(45deg)}
        .hamburger.active span:nth-child(2){opacity:0}
        .hamburger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
        .mobile-menu{
            display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:99;
            background:#fff;padding:96px 24px 40px;
            transform:translateY(-100%);transition:transform .35s var(--ease);
            overflow-y:auto;
        }
        .mobile-menu.open{transform:translateY(0)}
        .mobile-menu a{display:block;padding:16px 4px;font-size:17px;font-weight:600;color:var(--navy);border-bottom:1px solid var(--border-soft)}
        .mobile-menu .nav-actions{flex-direction:column;margin-top:24px;gap:12px}
        .mobile-menu .btn{width:100%;justify-content:center}

        /* ============================================================
           3. HERO
           ============================================================ */
        .hero{
            position:relative;padding:168px 0 100px;
            background:
                radial-gradient(560px 320px at 88% -6%, #eaf1ff 0%, transparent 60%),
                radial-gradient(500px 320px at 6% 10%, #f3f7ff 0%, transparent 55%),
                var(--bg);
            overflow:hidden;
        }
        .hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center}
        .hero-badge{animation:fadeUp .7s var(--ease) both;animation-delay:.05s}
        .hero-title{font-size:clamp(40px,5.6vw,64px);line-height:1.06;margin:22px 0 20px}
        .hero-title span{display:block;animation:fadeUp .7s var(--ease) both}
        .hero-title span:nth-child(1){animation-delay:.15s}
        .hero-title span:nth-child(2){animation-delay:.27s}
        .hero-title span:nth-child(3){animation-delay:.39s;color:var(--blue)}
        .hero-text{font-size:17.5px;color:var(--muted);max-width:480px;animation:fadeUp .7s var(--ease) both;animation-delay:.5s}
        .hero-cta{display:flex;flex-wrap:wrap;gap:14px;margin-top:30px;animation:fadeUp .7s var(--ease) both;animation-delay:.62s}
        .hero-mini{display:flex;flex-wrap:wrap;gap:22px;margin-top:30px;animation:fadeUp .7s var(--ease) both;animation-delay:.74s}
        .hero-mini div{display:flex;align-items:center;gap:8px;font-size:13.5px;font-weight:600;color:var(--muted)}
        .hero-mini svg{color:var(--blue);flex-shrink:0}
        @keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}

        /* Meeting mockup */
        .hero-mock-wrap{position:relative;animation:fadeUp .8s var(--ease) both;animation-delay:.4s}
        .hero-blob{position:absolute;border-radius:50%;filter:blur(60px);z-index:0;opacity:.55}
        .hero-blob.b1{width:280px;height:280px;background:#bcd6ff;top:-40px;right:-40px}
        .hero-blob.b2{width:220px;height:220px;background:#dcecff;bottom:-30px;left:-30px}
        .meeting-mock{
            position:relative;z-index:1;background:#fff;border-radius:var(--radius-lg);
            border:1px solid var(--border);box-shadow:var(--shadow-lg);overflow:hidden;
            animation:floatY 6s ease-in-out infinite;
        }
        @keyframes floatY{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .mock-top{
            display:flex;align-items:center;justify-content:space-between;padding:14px 18px;
            border-bottom:1px solid var(--border-soft);background:#fbfcff;
        }
        .mock-top-left{display:flex;align-items:center;gap:10px;font-weight:700;font-size:13px;color:var(--navy)}
        .mock-dot{width:8px;height:8px;border-radius:50%;background:#22c55e}
        .mock-live{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:#dc2626;background:#fee2e2;padding:4px 10px;border-radius:99px}
        .mock-body{display:grid;grid-template-columns:1fr 168px}
        .mock-tiles{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:14px}
        .mock-tile{
            position:relative;aspect-ratio:4/3;border-radius:12px;background:linear-gradient(150deg,#eef3fc,#e3ecfb);
            border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;
        }
        .mock-tile.active{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.14)}
        .mock-avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff}
        .mock-tile-name{position:absolute;left:8px;bottom:8px;font-size:10.5px;font-weight:700;color:#fff;background:rgba(15,27,46,.55);padding:3px 8px;border-radius:99px;display:flex;align-items:center;gap:5px}
        .mock-side{border-left:1px solid var(--border-soft);display:flex;flex-direction:column}
        .mock-tabs{display:flex;border-bottom:1px solid var(--border-soft)}
        .mock-tab{flex:1;text-align:center;padding:10px 4px;font-size:11px;font-weight:700;color:var(--muted-soft);border-bottom:2px solid transparent;transition:.2s}
        .mock-tab.active{color:var(--blue);border-color:var(--blue)}
        .mock-panel{padding:10px;font-size:11px;flex:1}
        .mock-chat-msg{margin-bottom:9px}
        .mock-chat-msg b{display:block;font-size:10px;color:var(--navy);margin-bottom:2px}
        .mock-chat-msg span{color:var(--muted);display:block;background:var(--bg-soft);padding:6px 8px;border-radius:8px 8px 8px 3px}
        .mock-transcript p{color:var(--muted);font-size:10.5px;line-height:1.5;margin-bottom:8px;padding-left:8px;border-left:2px solid var(--border)}
        .mock-controls{display:flex;align-items:center;justify-content:center;gap:10px;padding:12px;border-top:1px solid var(--border-soft);background:#fbfcff}
        .mock-ctrl{width:34px;height:34px;border-radius:10px;background:#eef2f8;display:flex;align-items:center;justify-content:center;color:var(--navy)}
        .mock-ctrl.leave{background:#fee2e2;color:#dc2626}

        /* ============================================================
           4. BENEFIT STRIP
           ============================================================ */
        .strip{border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--bg-soft)}
        .strip-inner{display:flex;flex-wrap:wrap;justify-content:space-between;gap:20px;padding:26px 0}
        .strip-item{display:flex;align-items:center;gap:10px;font-weight:700;font-size:14px;color:var(--navy)}
        .strip-item svg{color:var(--blue);flex-shrink:0}

        /* ============================================================
           5. FEATURES (bento)
           ============================================================ */
        .features{padding:120px 0}
        .bento{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
        .feature-card{
            grid-column:span 1;padding:28px;border-radius:var(--radius-md);border:1px solid var(--border);
            background:#fff;transition:transform .3s var(--ease),box-shadow .3s var(--ease),border-color .3s var(--ease);
        }
        .feature-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);border-color:#d7e2f3}
        .feature-card.wide{grid-column:span 2}
        .feature-icon{
            width:46px;height:46px;border-radius:12px;background:var(--blue-soft);color:var(--blue);
            display:flex;align-items:center;justify-content:center;margin-bottom:18px;
        }
        .feature-card h3{font-size:17px;margin-bottom:8px}
        .feature-card p{color:var(--muted);font-size:14px}

        /* ============================================================
           6. PRODUCT SHOWCASE
           ============================================================ */
        .showcase{padding:0 0 120px}
        .showcase-grid{display:grid;grid-template-columns:.85fr 1.15fr;gap:60px;align-items:center}
        .showcase-text p{color:var(--muted);font-size:16px;margin:16px 0 26px}
        .showcase-list{display:flex;flex-direction:column;gap:14px}
        .showcase-list li{display:flex;align-items:center;gap:12px;font-size:14.5px;font-weight:600;color:var(--navy)}
        .showcase-list svg{color:var(--blue);flex-shrink:0}

        .dash-mock{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);overflow:hidden;display:grid;grid-template-columns:180px 1fr}
        .dash-side{background:var(--navy);color:#fff;padding:22px 16px}
        .dash-brand{display:flex;align-items:center;gap:8px;font-weight:800;font-size:14px;margin-bottom:26px}
        .dash-brand img{width:22px;height:22px}
        .dash-nav a{display:flex;align-items:center;gap:10px;padding:10px 10px;border-radius:9px;font-size:12.5px;font-weight:600;color:#9fb0cc;margin-bottom:4px}
        .dash-nav a.active{background:rgba(255,255,255,.08);color:#fff}
        .dash-main{padding:24px}
        .dash-main h4{font-size:12px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px}
        .dash-main h3{font-size:19px;margin-bottom:18px}
        .dash-row{display:flex;align-items:center;justify-content:space-between;padding:13px 14px;border:1px solid var(--border-soft);border-radius:12px;margin-bottom:10px}
        .dash-row-left{display:flex;align-items:center;gap:12px}
        .dash-avatar{width:32px;height:32px;border-radius:9px;background:var(--blue-soft);color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px}
        .dash-row-title{font-size:13.5px;font-weight:700;color:var(--navy)}
        .dash-row-sub{font-size:11.5px;color:var(--muted-soft)}
        .pill{font-size:10.5px;font-weight:700;padding:4px 10px;border-radius:99px}
        .pill.upcoming{background:#eaf1ff;color:#2563eb}
        .pill.active{background:#fff4e5;color:#d97706}
        .pill.completed{background:#e9f9ef;color:#16a34a}

        /* ============================================================
           7. HOW IT WORKS
           ============================================================ */
        .how{padding:100px 0;background:var(--bg-soft);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
        .steps{position:relative;display:grid;grid-template-columns:repeat(3,1fr);gap:30px}
        .steps::before{content:"";position:absolute;top:34px;left:12%;right:12%;height:1px;background:linear-gradient(90deg,transparent,var(--border) 15%,var(--border) 85%,transparent)}
        .step{position:relative;z-index:1}
        .step-num{
            width:68px;height:68px;border-radius:18px;background:#fff;border:1px solid var(--border);
            display:flex;align-items:center;justify-content:center;font-family:'Manrope',sans-serif;font-weight:800;
            font-size:22px;color:var(--blue);margin-bottom:20px;box-shadow:var(--shadow-sm);
        }
        .step h3{font-size:18px;margin-bottom:8px}
        .step p{color:var(--muted);font-size:14.5px}

        /* ============================================================
           8. WHY SMARTMEET
           ============================================================ */
        .why{padding:120px 0}
        .why-grid{display:grid;grid-template-columns:.9fr 1.1fr;gap:60px;align-items:start}
        .why-grid h2{font-size:clamp(28px,3.4vw,40px);line-height:1.18;margin-bottom:16px}
        .why-grid > div:first-child p{color:var(--muted);font-size:16px;max-width:420px}
        .why-list{display:flex;flex-direction:column;gap:16px}
        .why-row{display:flex;gap:16px;padding:22px;border:1px solid var(--border);border-radius:var(--radius-md);transition:box-shadow .3s var(--ease),transform .3s var(--ease),border-color .3s}
        .why-row:hover{box-shadow:var(--shadow-md);transform:translateY(-4px);border-color:#d7e2f3}
        .why-row .feature-icon{margin-bottom:0;flex-shrink:0}
        .why-row h4{font-size:15.5px;margin-bottom:4px}
        .why-row p{font-size:13.5px;color:var(--muted)}

        /* ============================================================
           9. EXPERIENCE
           ============================================================ */
        .experience{padding:120px 0;background:linear-gradient(170deg,var(--navy),var(--navy-2));color:#fff}
        .experience .section-head h2{color:#fff}
        .experience .section-head p{color:#93a3bf}
        .experience .eyebrow{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.16);color:#bcd0ff}
        .exp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
        .exp-panel{background:rgba(255,255,255,.045);border:1px solid rgba(255,255,255,.09);border-radius:var(--radius-md);padding:20px;backdrop-filter:blur(6px)}
        .exp-panel h4{color:#fff;font-size:13.5px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px}
        .exp-panel h4 svg{color:#7ba7ff}
        .exp-line{display:flex;gap:10px;margin-bottom:11px;font-size:12px}
        .exp-line time{color:#7486a3;flex-shrink:0;font-variant-numeric:tabular-nums}
        .exp-line span{color:#cbd7ec}
        .exp-bubble{background:rgba(255,255,255,.07);padding:8px 11px;border-radius:9px 9px 9px 3px;font-size:12px;color:#dbe5f5;margin-bottom:9px;max-width:88%}
        .exp-bubble b{display:block;color:#8fb2ff;font-size:10.5px;margin-bottom:2px}
        .exp-person{display:flex;align-items:center;gap:10px;margin-bottom:13px}
        .exp-person .mock-avatar{width:32px;height:32px;font-size:11px}
        .exp-person div span{display:block;font-size:12.5px;font-weight:600;color:#fff}
        .exp-person div small{color:#7486a3;font-size:10.5px}
        .exp-status{width:7px;height:7px;border-radius:50%;background:#22c55e;margin-left:auto}

        /* ============================================================
           10. FINAL CTA
           ============================================================ */
        .final-cta{position:relative;padding:110px 0;text-align:center;overflow:hidden}
        .final-cta::before{content:"";position:absolute;inset:0;background:radial-gradient(600px 300px at 50% 0%,#eaf1ff,transparent 70%);z-index:0}
        .final-cta-inner{position:relative;z-index:1;max-width:620px;margin:0 auto}
        .final-cta h2{font-size:clamp(30px,4vw,44px);margin-bottom:14px}
        .final-cta p{color:var(--muted);font-size:16.5px;margin-bottom:34px}
        .final-cta .hero-cta{justify-content:center}

        /* ============================================================
           11. FOOTER
           ============================================================ */
        footer{border-top:1px solid var(--border);padding:56px 0 28px}
        .footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:40px;padding-bottom:36px}
        .footer-brand .brand{margin-bottom:10px}
        .footer-brand p{color:var(--muted);font-size:14px;max-width:280px}
        .footer-col h5{font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--navy);margin-bottom:14px}
        .footer-col a{display:block;color:var(--muted);font-size:14px;margin-bottom:10px;transition:color .2s}
        .footer-col a:hover{color:var(--blue)}
        .footer-bottom{border-top:1px solid var(--border);padding-top:22px;font-size:13px;color:var(--muted-soft);text-align:center}

        /* ============================================================
           12. RESPONSIVE
           ============================================================ */
        @media (max-width:1024px){
            .hero-grid{grid-template-columns:1fr;gap:48px}
            .hero-mock-wrap{max-width:520px;margin:0 auto}
            .bento{grid-template-columns:repeat(2,1fr)}
            .feature-card.wide{grid-column:span 2}
            .showcase-grid{grid-template-columns:1fr;gap:40px}
            .dash-mock{max-width:560px;margin:0 auto}
            .why-grid{grid-template-columns:1fr;gap:36px}
            .exp-grid{grid-template-columns:1fr;gap:16px}
            .footer-grid{grid-template-columns:1fr 1fr}
            .footer-brand{grid-column:span 2}
        }
        @media (max-width:900px){
            .nav-links{display:none}
            .nav-actions{display:none}
            .hamburger{display:flex}
            .mobile-menu{display:block}
        }
        @media (max-width:768px){
            .hero{padding:130px 0 70px}
            .features,.showcase,.why,.experience{padding:76px 0}
            .how{padding:70px 0}
            .final-cta{padding:80px 0}
            .steps{grid-template-columns:1fr;gap:34px}
            .steps::before{display:none}
            .bento{grid-template-columns:1fr}
            .feature-card.wide{grid-column:span 1}
            .strip-inner{justify-content:flex-start;gap:22px 34px}
        }
        @media (max-width:430px){
            .container{padding:0 18px}
            .hero-cta{flex-direction:column;align-items:stretch}
            .hero-cta .btn{width:100%;justify-content:center}
            .mock-body{grid-template-columns:1fr}
            .mock-side{display:none}
            .mock-tiles{grid-template-columns:1fr 1fr}
            .dash-mock{grid-template-columns:1fr}
            .dash-side{display:none}
            .footer-grid{grid-template-columns:1fr}
            .footer-brand{grid-column:span 1}
            .section-head{margin-bottom:36px}
        }
        @media (max-width:360px){
            .hero-title{font-size:34px}
            .btn{padding:13px 20px;font-size:14px}
            .mock-tiles{padding:10px;gap:8px}
        }
    </style>
</head>
<body>

{{-- ================= NAVBAR ================= --}}
<header class="navbar" id="navbar">
    <div class="container nav-inner">
        <a href="/" class="brand">
            <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet">
            SmartMeet
        </a>

        <nav class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#experience">Experience</a>
        </nav>

        <div class="nav-actions">
            @auth
                @php
                    $role = auth()->user()->role;
                    $dashboardUrl = match ($role) {
                        'organizer' => '/organizer/dashboard',
                        'participant' => '/participant/dashboard',
                        'admin' => '/admin/dashboard',
                        default => '/',
                    };
                @endphp
                <a href="{{ $dashboardUrl }}" class="btn btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            @endauth
        </div>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <a href="#features">Features</a>
    <a href="#how-it-works">How It Works</a>
    <a href="#experience">Experience</a>
    <div class="nav-actions">
        @auth
            <a href="{{ $dashboardUrl }}" class="btn btn-primary btn-block">Go to Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-secondary btn-block">Log In</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-block">Get Started</a>
        @endauth
    </div>
</div>

{{-- ================= HERO ================= --}}
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow hero-badge">Smarter online collaboration</span>
            <h1 class="hero-title">
                <span>Meet.</span>
                <span>Connect.</span>
                <span>Collaborate.</span>
            </h1>
            <p class="hero-text">SmartMeet brings video meetings, real-time chat, scheduling and live transcription together in one simple workspace.</p>

            <div class="hero-cta">
                @auth
                    <a href="{{ $dashboardUrl }}" class="btn btn-primary">Go to Dashboard
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary">Get Started Free
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endauth
                <a href="#features" class="btn btn-secondary">Explore Features</a>
            </div>

            <div class="hero-mini">
                <div><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Browser Based</div>
                <div><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Simple Setup</div>
                <div><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Real-Time Collaboration</div>
            </div>
        </div>

        <div class="hero-mock-wrap">
            <div class="hero-blob b1"></div>
            <div class="hero-blob b2"></div>

            <div class="meeting-mock">
                <div class="mock-top">
                    <div class="mock-top-left"><span class="mock-dot"></span> SmartMeet — Weekly Sync</div>
                    <span class="mock-live">● LIVE</span>
                </div>
                <div class="mock-body">
                    <div class="mock-tiles">
                        <div class="mock-tile active">
                            <div class="mock-avatar" style="background:linear-gradient(135deg,#2563eb,#38bdf8)">A</div>
                            <span class="mock-tile-name"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 14a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v5a3 3 0 0 0 3 3Z"/></svg> Alex</span>
                        </div>
                        <div class="mock-tile">
                            <div class="mock-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)">S</div>
                            <span class="mock-tile-name">Sarah</span>
                        </div>
                        <div class="mock-tile">
                            <div class="mock-avatar" style="background:linear-gradient(135deg,#22c55e,#06b6d4)">D</div>
                            <span class="mock-tile-name">David</span>
                        </div>
                        <div class="mock-tile">
                            <div class="mock-avatar" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">E</div>
                            <span class="mock-tile-name">Emma</span>
                        </div>
                    </div>
                    <div class="mock-side">
                        <div class="mock-tabs">
                            <div class="mock-tab active" data-tab="chat">Chat</div>
                            <div class="mock-tab" data-tab="transcript">Transcript</div>
                        </div>
                        <div class="mock-panel" data-panel="chat">
                            <div class="mock-chat-msg"><b>Sarah</b><span>Sounds good to me!</span></div>
                            <div class="mock-chat-msg"><b>David</b><span>Sharing the doc now.</span></div>
                            <div class="mock-chat-msg"><b>Emma</b><span>Great, thanks 👍</span></div>
                        </div>
                        <div class="mock-panel" data-panel="transcript" style="display:none">
                            <p>"Let's review the project milestones for this week..."</p>
                            <p>"Next, David will walk us through the timeline."</p>
                        </div>
                    </div>
                </div>
                <div class="mock-controls">
                    <div class="mock-ctrl"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 14a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v5a3 3 0 0 0 3 3Zm5-3a5 5 0 0 1-10 0M12 19v3"/></svg></div>
                    <div class="mock-ctrl"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m23 7-7 5 7 5V7Z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                    <div class="mock-ctrl"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    <div class="mock-ctrl"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                    <div class="mock-ctrl leave"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m10.68 13.31 8.32-2.68M3 21l2.5-6.5c.3-.8 1.1-1.3 1.9-1.3h9.2c.8 0 1.6.5 1.9 1.3L21 21l-9-4-9 4Z"/></svg></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= BENEFIT STRIP ================= --}}
<div class="strip">
    <div class="container strip-inner">
        <div class="strip-item"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m23 7-7 5 7 5V7Z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg> Video &amp; Audio</div>
        <div class="strip-item"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H7l-3 3V4Z"/><path d="M8 9h8M8 12h5"/></svg> Live Transcription</div>
        <div class="strip-item"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/></svg> Real-Time Chat</div>
        <div class="strip-item"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Smart Scheduling</div>
    </div>
</div>

{{-- ================= FEATURES ================= --}}
<section id="features" class="features">
    <div class="container">
        <div class="section-head reveal-up">
            <span class="eyebrow">Everything you need</span>
            <h2>One workspace for better meetings.</h2>
            <p>Keep your meetings, conversations and collaboration organized without unnecessary complexity.</p>
        </div>

        <div class="bento">
            <div class="feature-card wide reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m23 7-7 5 7 5V7Z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                <h3>Video Meetings</h3>
                <p>High-quality browser-based video meeting experience with no downloads required.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 14a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v5a3 3 0 0 0 3 3Zm5-3a5 5 0 0 1-10 0M12 19v3"/></svg></div>
                <h3>Audio Meetings</h3>
                <p>Join and collaborate even when video isn't needed.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H7l-3 3V4Z"/><path d="M8 9h8M8 12h5"/></svg></div>
                <h3>Live Transcription</h3>
                <p>Follow the conversation with live meeting transcription.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/></svg></div>
                <h3>Real-Time Chat</h3>
                <p>Share messages with participants during meetings.</p>
            </div>
            <div class="feature-card wide reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                <h3>Smart Scheduling</h3>
                <p>Plan meetings and keep upcoming sessions organized in one calendar-friendly view.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
                <h3>Easy Invitations</h3>
                <p>Invite participants using simple meeting links.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <h3>Participant Management</h3>
                <p>See and manage people participating in your meetings.</p>
            </div>
            <div class="feature-card reveal-up">
                <div class="feature-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg></div>
                <h3>Meeting Dashboard</h3>
                <p>Keep upcoming, active and completed meetings organized.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= PRODUCT SHOWCASE ================= --}}
<section class="showcase">
    <div class="container showcase-grid">
        <div class="showcase-text reveal-left">
            <span class="eyebrow">Stay organized</span>
            <h2 style="font-size:clamp(28px,3.4vw,40px);line-height:1.18;margin:16px 0">Your meetings. One clear dashboard.</h2>
            <p>Quickly see your upcoming meetings, what's live right now, meeting status, participants and your full schedule — all from a single organized view.</p>
            <ul class="showcase-list">
                <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Upcoming meetings at a glance</li>
                <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Live status, updated automatically</li>
                <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Full participant &amp; schedule visibility</li>
            </ul>
        </div>

        <div class="reveal-right">
            <div class="dash-mock">
                <div class="dash-side">
                    <div class="dash-brand"><img src="{{ asset('images/s-logo.png') }}" alt=""> SmartMeet</div>
                    <nav class="dash-nav">
                        <a class="active"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg> Dashboard</a>
                        <a><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m23 7-7 5 7 5V7Z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg> My Meetings</a>
                        <a><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Schedule</a>
                        <a><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg> Notifications</a>
                    </nav>
                </div>
                <div class="dash-main">
                    <h4>Welcome back</h4>
                    <h3>Upcoming Meetings</h3>

                    <div class="dash-row">
                        <div class="dash-row-left">
                            <div class="dash-avatar">PR</div>
                            <div>
                                <div class="dash-row-title">Project Review</div>
                                <div class="dash-row-sub">Today · 3:00 PM</div>
                            </div>
                        </div>
                        <span class="pill upcoming">Upcoming</span>
                    </div>

                    <div class="dash-row">
                        <div class="dash-row-left">
                            <div class="dash-avatar" style="background:#fff4e5;color:#d97706">TS</div>
                            <div>
                                <div class="dash-row-title">Team Sync</div>
                                <div class="dash-row-sub">Live now · 41 participants</div>
                            </div>
                        </div>
                        <span class="pill active">Active</span>
                    </div>

                    <div class="dash-row">
                        <div class="dash-row-left">
                            <div class="dash-avatar" style="background:#e9f9ef;color:#16a34a">WP</div>
                            <div>
                                <div class="dash-row-title">Weekly Planning</div>
                                <div class="dash-row-sub">Yesterday · 10:00 AM</div>
                            </div>
                        </div>
                        <span class="pill completed">Completed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= HOW IT WORKS ================= --}}
<section id="how-it-works" class="how">
    <div class="container">
        <div class="section-head centered reveal-up" style="margin-left:auto;margin-right:auto">
            <span class="eyebrow">Simple from the start</span>
            <h2>From schedule to conversation in three steps.</h2>
        </div>

        <div class="steps">
            <div class="step reveal-up">
                <div class="step-num">01</div>
                <h3>Schedule</h3>
                <p>Choose the meeting date and time that works for everyone.</p>
            </div>
            <div class="step reveal-up" style="transition-delay:.12s">
                <div class="step-num">02</div>
                <h3>Invite</h3>
                <p>Share the meeting link with participants in a click.</p>
            </div>
            <div class="step reveal-up" style="transition-delay:.24s">
                <div class="step-num">03</div>
                <h3>Meet</h3>
                <p>Start your meeting and collaborate in real time.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= WHY SMARTMEET ================= --}}
<section class="why">
    <div class="container why-grid">
        <div class="reveal-left">
            <h2>Meetings shouldn't feel complicated.</h2>
            <p>SmartMeet keeps the essential collaboration tools you actually need in one focused, uncluttered experience — nothing more, nothing less.</p>
        </div>

        <div class="why-list">
            <div class="why-row reveal-up">
                <div class="feature-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
                <div><h4>Browser Based</h4><p>Join directly from a modern browser — no installs.</p></div>
            </div>
            <div class="why-row reveal-up" style="transition-delay:.08s">
                <div class="feature-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 11-14h-7l1-6Z"/></svg></div>
                <div><h4>Simple Experience</h4><p>Clean workflows without unnecessary complexity.</p></div>
            </div>
            <div class="why-row reveal-up" style="transition-delay:.16s">
                <div class="feature-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/></svg></div>
                <div><h4>Real-Time Collaboration</h4><p>Chat and communicate while the meeting is happening.</p></div>
            </div>
            <div class="why-row reveal-up" style="transition-delay:.24s">
                <div class="feature-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg></div>
                <div><h4>Organized Meetings</h4><p>Keep scheduled and previous meetings easier to manage.</p></div>
            </div>
        </div>
    </div>
</section>

{{-- ================= EXPERIENCE ================= --}}
<section id="experience" class="experience">
    <div class="container">
        <div class="section-head centered reveal-up" style="margin-left:auto;margin-right:auto">
            <span class="eyebrow">Up close</span>
            <h2>Built around the conversation.</h2>
            <p>A closer look at the tools that keep every meeting productive.</p>
        </div>

        <div class="exp-grid">
            <div class="exp-panel reveal-up">
                <h4><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H7l-3 3V4Z"/></svg> Live Transcript</h4>
                <div class="exp-line"><time>09:41</time><span>"Let's review the milestones for this week."</span></div>
                <div class="exp-line"><time>09:42</time><span>"David will walk us through the timeline."</span></div>
                <div class="exp-line"><time>09:43</time><span>"Sounds good — sharing my screen now."</span></div>
            </div>

            <div class="exp-panel reveal-up" style="transition-delay:.1s">
                <h4><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/></svg> Meeting Chat</h4>
                <div class="exp-bubble"><b>Sarah</b>Can you share the slides?</div>
                <div class="exp-bubble"><b>Alex</b>Sending them now 👍</div>
                <div class="exp-bubble"><b>Emma</b>Got it, thanks!</div>
            </div>

            <div class="exp-panel reveal-up" style="transition-delay:.2s">
                <h4><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Participants</h4>
                <div class="exp-person"><div class="mock-avatar" style="background:linear-gradient(135deg,#2563eb,#38bdf8)">A</div><div><span>Alex</span><small>Organizer</small></div><span class="exp-status"></span></div>
                <div class="exp-person"><div class="mock-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)">S</div><div><span>Sarah</span><small>Participant</small></div><span class="exp-status"></span></div>
                <div class="exp-person"><div class="mock-avatar" style="background:linear-gradient(135deg,#22c55e,#06b6d4)">D</div><div><span>David</span><small>Participant</small></div><span class="exp-status"></span></div>
            </div>
        </div>
    </div>
</section>

{{-- ================= FINAL CTA ================= --}}
<section class="final-cta">
    <div class="container final-cta-inner reveal-up">
        <h2>Ready to make meetings simpler?</h2>
        <p>Bring scheduling, conversations and collaboration together with SmartMeet.</p>
        <div class="hero-cta">
            @auth
                <a href="{{ $dashboardUrl }}" class="btn btn-primary">Go to Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started Free</a>
                <a href="{{ route('login') }}" class="btn btn-secondary">Log In</a>
            @endauth
        </div>
    </div>
</section>

{{-- ================= FOOTER ================= --}}
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="/" class="brand">
                    <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet">
                    SmartMeet
                </a>
                <p>Simple online meetings and real-time collaboration.</p>
            </div>
            <div class="footer-col">
                <h5>Product</h5>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#experience">Experience</a>
            </div>
            <div class="footer-col">
                <h5>Legal</h5>
                @if(Route::has('privacy-policy'))<a href="{{ route('privacy-policy') }}">Privacy Policy</a>@endif
                @if(Route::has('terms'))<a href="{{ route('terms') }}">Terms</a>@endif
                @if(Route::has('data-deletion'))<a href="{{ route('data-deletion') }}">Data Deletion</a>@endif
            </div>
        </div>
        <div class="footer-bottom">© {{ date('Y') }} SmartMeet. All rights reserved.</div>
    </div>
</footer>

<script>
    (function(){
        // Navbar scroll state
        const navbar = document.getElementById('navbar');
        const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 20);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive:true });

        // Mobile menu
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });
        mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        }));

        // Scroll reveal (fires once)
        const revealEls = document.querySelectorAll('.reveal, .reveal-up, .reveal-left, .reveal-right');
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            revealEls.forEach(el => io.observe(el));
        } else {
            revealEls.forEach(el => el.classList.add('in-view'));
        }

        // Hero mockup chat/transcript tab toggle
        document.querySelectorAll('.mock-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const parent = tab.closest('.mock-side');
                parent.querySelectorAll('.mock-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.tab;
                parent.querySelectorAll('.mock-panel').forEach(p => {
                    p.style.display = (p.dataset.panel === target) ? 'block' : 'none';
                });
            });
        });
    })();
</script>

</body>
</html>
