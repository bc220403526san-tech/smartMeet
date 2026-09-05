<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1220">

    <title>SmartMeet — Simple Online Meetings & Collaboration</title>
    <meta name="description" content="SmartMeet makes online meetings simple with video, audio, scheduling, real-time chat and live transcription.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --bg:#f7f9fc;
            --surface:#ffffff;
            --surface-2:#eef4ff;
            --surface-3:#f1f5f9;
            --text:#0f172a;
            --muted:#64748b;
            --line:#dbe4f0;
            --primary:#2563eb;
            --primary-2:#1d4ed8;
            --primary-soft:#dbeafe;
            --navy:#0b1220;
            --success:#16a34a;
            --danger:#ef4444;
            --shadow-sm:0 10px 30px rgba(15,23,42,.06);
            --shadow:0 24px 70px rgba(15,23,42,.12);
            --radius:18px;
            --radius-lg:28px;
            --container:1180px;
        }

        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            margin:0;
            font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            color:var(--text);
            background:var(--bg);
            overflow-x:hidden;
        }

        a{color:inherit;text-decoration:none}
        button{font:inherit}
        img{max-width:100%;display:block}
        .container{width:min(calc(100% - 40px),var(--container));margin-inline:auto}
        .section{padding:100px 0}
        .section-label{
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:var(--primary);
            font-size:.78rem;
            font-weight:800;
            letter-spacing:.14em;
            text-transform:uppercase;
            margin-bottom:14px;
        }
        .section-label::before{
            content:"";
            width:26px;
            height:2px;
            border-radius:999px;
            background:var(--primary);
        }

        h1,h2,h3{font-family:Manrope,Inter,sans-serif;margin:0;color:var(--text)}
        h1{font-size:clamp(3rem,6vw,5.9rem);line-height:.98;letter-spacing:-.06em}
        h2{font-size:clamp(2.2rem,4vw,3.75rem);line-height:1.05;letter-spacing:-.045em}
        h3{font-size:1.08rem;line-height:1.3}
        p{margin:0;color:var(--muted);line-height:1.75}
        .section-copy{max-width:650px;font-size:1.05rem;margin-top:18px}
        .center{text-align:center}
        .center .section-copy{margin-inline:auto}

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            min-height:50px;
            padding:0 22px;
            border-radius:13px;
            border:1px solid transparent;
            font-weight:700;
            transition:.22s ease;
            cursor:pointer;
        }
        .btn svg{width:18px;height:18px}
        .btn-primary{
            color:#fff;
            background:var(--primary);
            box-shadow:0 10px 24px rgba(37,99,235,.24);
        }
        .btn-primary:hover{background:var(--primary-2);transform:translateY(-2px)}
        .btn-secondary{
            background:#fff;
            border-color:var(--line);
            color:var(--text);
        }
        .btn-secondary:hover{border-color:#b7c7dc;transform:translateY(-2px);box-shadow:var(--shadow-sm)}

        /* Navbar */
        .navbar{
            position:fixed;
            inset:0 0 auto;
            z-index:50;
            padding:17px 0;
            transition:.25s ease;
        }
        .navbar.scrolled{
            background:rgba(247,249,252,.9);
            backdrop-filter:blur(18px);
            border-bottom:1px solid rgba(219,228,240,.9);
            padding:11px 0;
        }
        .nav-inner{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:24px;
        }
        .brand{display:flex;align-items:center;gap:11px;font-family:Manrope,sans-serif;font-weight:800;font-size:1.12rem}
        .brand-mark{
            width:38px;height:38px;border-radius:12px;
            display:grid;place-items:center;
            background:linear-gradient(145deg,#2563eb,#0f3e9f);
            color:#fff;
            box-shadow:0 8px 24px rgba(37,99,235,.26);
        }
        .brand-mark svg{width:22px;height:22px}
        .nav-links{display:flex;align-items:center;gap:8px}
        .nav-links a{
            padding:10px 13px;
            border-radius:10px;
            color:#475569;
            font-size:.92rem;
            font-weight:600;
            transition:.2s;
        }
        .nav-links a:hover{color:var(--text);background:#fff}
        .nav-actions{display:flex;align-items:center;gap:10px}
        .nav-login{font-size:.92rem;font-weight:700;color:#334155;padding:10px 12px}
        .menu-btn{
            width:44px;height:44px;border-radius:11px;border:1px solid var(--line);
            background:#fff;display:none;place-items:center;cursor:pointer;
        }
        .menu-btn svg{width:21px}
        .mobile-menu{
            display:none;
            position:absolute;
            top:74px;left:20px;right:20px;
            background:#fff;border:1px solid var(--line);border-radius:18px;
            padding:10px;box-shadow:var(--shadow);
        }
        .mobile-menu.open{display:block}
        .mobile-menu a{display:block;padding:13px 14px;border-radius:10px;font-weight:650;color:#475569}
        .mobile-menu a:hover{background:var(--surface-3);color:var(--text)}
        .mobile-menu .mobile-cta{margin-top:8px;background:var(--primary);color:#fff;text-align:center}

        /* Hero */
        .hero{
            min-height:100vh;
            display:flex;
            align-items:center;
            position:relative;
            padding:138px 0 90px;
            background:
                radial-gradient(circle at 84% 18%,rgba(37,99,235,.12),transparent 25%),
                radial-gradient(circle at 3% 42%,rgba(14,165,233,.08),transparent 21%),
                linear-gradient(180deg,#fbfdff 0%,#f7f9fc 100%);
        }
        .hero::before{
            content:"";
            position:absolute;inset:0;
            background-image:
                linear-gradient(rgba(148,163,184,.08) 1px,transparent 1px),
                linear-gradient(90deg,rgba(148,163,184,.08) 1px,transparent 1px);
            background-size:42px 42px;
            mask-image:linear-gradient(to bottom,rgba(0,0,0,.55),transparent 77%);
            pointer-events:none;
        }
        .hero-grid{
            position:relative;
            display:grid;
            grid-template-columns:1.02fr .98fr;
            align-items:center;
            gap:66px;
        }
        .hero-badge{
            display:inline-flex;align-items:center;gap:9px;
            padding:8px 12px;border:1px solid #bfdbfe;border-radius:999px;
            background:rgba(239,246,255,.86);color:#1d4ed8;
            font-size:.82rem;font-weight:750;margin-bottom:24px;
        }
        .hero-badge .dot{
            width:8px;height:8px;border-radius:50%;background:#22c55e;
            box-shadow:0 0 0 5px rgba(34,197,94,.12)
        }
        .hero-title .accent{color:var(--primary)}
        .hero-copy{max-width:600px;font-size:1.11rem;margin-top:24px}
        .hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:32px}
        .hero-points{
            display:flex;align-items:center;flex-wrap:wrap;gap:12px;
            margin-top:22px;color:#64748b;font-size:.82rem;font-weight:600;
        }
        .hero-points span{display:flex;align-items:center;gap:7px}
        .hero-points svg{width:15px;height:15px;color:#16a34a}

        /* Product mockup */
        .hero-visual{position:relative}
        .mock-glow{
            position:absolute;width:74%;height:74%;border-radius:50%;
            background:rgba(37,99,235,.16);filter:blur(70px);
            top:16%;left:12%;z-index:0
        }
        .meeting-window{
            position:relative;z-index:2;
            border:1px solid rgba(203,213,225,.9);
            background:#0c1323;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 34px 90px rgba(15,23,42,.25);
            transform:perspective(1500px) rotateY(-5deg) rotateX(2deg);
        }
        .window-top{
            height:50px;display:flex;align-items:center;justify-content:space-between;
            padding:0 16px;background:#111a2c;border-bottom:1px solid rgba(255,255,255,.06);
        }
        .window-dots{display:flex;gap:6px}.window-dots i{width:9px;height:9px;border-radius:50%;background:#475569}
        .window-title{font-size:.78rem;color:#cbd5e1;font-weight:650}
        .live-pill{
            display:flex;align-items:center;gap:6px;color:#dcfce7;background:rgba(22,163,74,.15);
            border:1px solid rgba(74,222,128,.18);padding:5px 8px;border-radius:999px;font-size:.68rem;font-weight:700
        }
        .meeting-body{display:grid;grid-template-columns:1fr 190px;min-height:360px}
        .video-area{padding:13px;display:grid;grid-template-columns:1fr 1fr;gap:10px}
        .video-tile{
            position:relative;min-height:150px;border-radius:15px;overflow:hidden;
            background:linear-gradient(145deg,#1e293b,#0f172a);
            border:1px solid rgba(255,255,255,.06);
        }
        .avatar-bg-1{background:linear-gradient(145deg,#334155,#172033)}
        .avatar-bg-2{background:linear-gradient(145deg,#233151,#111827)}
        .avatar-bg-3{background:linear-gradient(145deg,#27364a,#101827)}
        .avatar-bg-4{background:linear-gradient(145deg,#20344f,#111827)}
        .avatar{
            width:70px;height:70px;border-radius:50%;display:grid;place-items:center;
            color:#e2e8f0;font-size:1.1rem;font-weight:800;letter-spacing:.02em;
            background:linear-gradient(145deg,#475569,#1e293b);
            position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
            border:3px solid rgba(255,255,255,.08)
        }
        .tile-foot{
            position:absolute;left:9px;right:9px;bottom:9px;
            display:flex;align-items:center;justify-content:space-between;
        }
        .tile-name{font-size:.68rem;color:#f8fafc;font-weight:650;background:rgba(15,23,42,.65);padding:5px 7px;border-radius:7px}
        .mic-mini{
            width:25px;height:25px;border-radius:8px;display:grid;place-items:center;
            background:rgba(15,23,42,.66);color:#e2e8f0
        }.mic-mini svg{width:12px;height:12px}
        .side-panel{background:#f8fafc;border-left:1px solid #dbe4f0;padding:14px 12px}
        .side-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
        .side-head strong{font-size:.76rem;color:#0f172a}.side-head span{font-size:.62rem;color:#64748b}
        .transcript-line{display:grid;grid-template-columns:26px 1fr;gap:7px;margin-bottom:12px}
        .mini-avatar{
            width:26px;height:26px;border-radius:8px;display:grid;place-items:center;
            background:#dbeafe;color:#1d4ed8;font-size:.55rem;font-weight:800
        }
        .transcript-line b{display:block;font-size:.59rem;color:#334155;margin-bottom:3px}
        .transcript-line p{font-size:.57rem;line-height:1.45;color:#64748b}
        .meeting-controls{
            display:flex;justify-content:center;align-items:center;gap:8px;
            padding:12px;background:#111a2c;border-top:1px solid rgba(255,255,255,.05)
        }
        .control{
            width:36px;height:36px;border-radius:10px;border:1px solid rgba(255,255,255,.08);
            display:grid;place-items:center;color:#cbd5e1;background:#1b263b
        }.control svg{width:15px;height:15px}
        .control.leave{width:60px;background:#dc2626;color:#fff;border-color:transparent}
        .float-card{
            position:absolute;z-index:4;background:rgba(255,255,255,.96);border:1px solid var(--line);
            border-radius:14px;box-shadow:var(--shadow-sm);padding:11px 13px;
            display:flex;align-items:center;gap:10px;font-size:.75rem;font-weight:700;color:#334155
        }
        .float-card svg{width:17px;height:17px;color:var(--primary)}
        .float-one{top:9%;left:-8%;animation:float 5s ease-in-out infinite}
        .float-two{right:-7%;bottom:16%;animation:float 5s ease-in-out 1.4s infinite}
        @keyframes float{50%{transform:translateY(-8px)}}

        /* Logos / trust */
        .trustbar{padding:28px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff}
        .trust-inner{display:flex;align-items:center;justify-content:center;gap:38px;flex-wrap:wrap;color:#64748b;font-size:.84rem;font-weight:700}
        .trust-item{display:flex;align-items:center;gap:9px}.trust-item svg{width:18px;color:#2563eb}

        /* Features */
        .features-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:46px}
        .feature-card{
            background:#fff;border:1px solid var(--line);border-radius:18px;padding:24px;
            min-height:194px;transition:.22s ease;position:relative;overflow:hidden
        }
        .feature-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-sm);border-color:#c8d5e6}
        .feature-card::after{
            content:"";position:absolute;right:-36px;bottom:-36px;width:100px;height:100px;border-radius:50%;
            background:#eff6ff;transition:.22s
        }
        .feature-card:hover::after{transform:scale(1.15)}
        .icon-box{
            width:43px;height:43px;border-radius:12px;background:#eff6ff;color:#2563eb;
            display:grid;place-items:center;margin-bottom:20px;position:relative;z-index:2
        }.icon-box svg{width:20px;height:20px}
        .feature-card h3,.feature-card p{position:relative;z-index:2}
        .feature-card p{font-size:.88rem;margin-top:9px;line-height:1.6}

        /* Product section */
        .showcase{background:#0b1220;color:#fff;position:relative;overflow:hidden}
        .showcase::before{
            content:"";position:absolute;width:520px;height:520px;border-radius:50%;
            background:rgba(37,99,235,.18);filter:blur(100px);right:-180px;top:-150px
        }
        .showcase h2{color:#fff}.showcase p{color:#94a3b8}
        .dashboard-shell{
            margin-top:52px;background:#eaf0f8;border:1px solid rgba(255,255,255,.08);
            border-radius:22px;overflow:hidden;box-shadow:0 35px 90px rgba(0,0,0,.3)
        }
        .browserbar{height:46px;background:#dce5f0;display:flex;align-items:center;gap:6px;padding:0 15px}
        .browserbar i{width:9px;height:9px;border-radius:50%;background:#94a3b8}
        .browser-url{
            margin-left:12px;width:240px;height:26px;border-radius:8px;background:#f8fafc;
            display:flex;align-items:center;padding:0 10px;color:#94a3b8;font-size:.62rem
        }
        .dashboard{display:grid;grid-template-columns:205px 1fr;min-height:470px;background:#f8fafc}
        .sidebar{background:#0f172a;color:#fff;padding:18px 14px}
        .side-brand{display:flex;align-items:center;gap:8px;font-weight:800;font-size:.82rem;padding:4px 7px 18px}
        .side-brand span{width:28px;height:28px;border-radius:8px;background:#2563eb;display:grid;place-items:center}
        .side-brand svg{width:15px}
        .side-nav{display:grid;gap:5px}.side-nav div{
                                           padding:10px;border-radius:9px;color:#94a3b8;font-size:.69rem;font-weight:600;display:flex;align-items:center;gap:8px
                                       }.side-nav svg{width:14px}.side-nav .active{background:#1e293b;color:#fff}
        .dash-main{padding:24px}
        .dash-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
        .dash-top h3{font-size:1rem}.dash-top p{font-size:.65rem;margin-top:4px}
        .user-chip{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);padding:6px 9px;border-radius:10px;font-size:.66rem;color:#475569}
        .user-circle{width:26px;height:26px;border-radius:8px;background:#dbeafe;color:#1d4ed8;display:grid;place-items:center;font-weight:800}
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .stat-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:13px}
        .stat-card span{font-size:.57rem;color:#64748b}.stat-card strong{display:block;font-size:1.1rem;color:#0f172a;margin-top:7px}
        .meeting-list{margin-top:16px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:15px}
        .meeting-list-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:11px}
        .meeting-list-head strong{font-size:.73rem}.meeting-list-head span{font-size:.58rem;color:#2563eb}
        .meeting-row{
            display:grid;grid-template-columns:1.2fr .7fr .7fr auto;align-items:center;gap:10px;
            padding:10px 0;border-top:1px solid #eef2f7;font-size:.62rem;color:#64748b
        }
        .meeting-row:first-of-type{border-top:0}.meeting-row b{color:#1e293b}
        .status{padding:5px 7px;border-radius:999px;font-size:.52rem;font-weight:800;width:max-content}
        .status.live{background:#dcfce7;color:#15803d}.status.upcoming{background:#dbeafe;color:#1d4ed8}
        .join-btn{background:#2563eb;color:#fff;padding:6px 9px;border-radius:7px;font-weight:700}

        /* How it works */
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;margin-top:50px;position:relative}
        .steps::before{content:"";position:absolute;top:31px;left:16%;right:16%;height:1px;background:#dbe4f0}
        .step{position:relative;text-align:center}
        .step-num{
            width:64px;height:64px;border-radius:50%;margin:0 auto 22px;background:#fff;border:1px solid var(--line);
            display:grid;place-items:center;color:#2563eb;font-family:Manrope,sans-serif;font-weight:800;box-shadow:0 8px 22px rgba(15,23,42,.06)
        }
        .step h3{font-size:1.18rem}.step p{max-width:275px;margin:9px auto 0;font-size:.9rem}

        /* Why */
        .why-wrap{
            display:grid;grid-template-columns:.88fr 1.12fr;gap:58px;align-items:center;
            padding:48px;border-radius:30px;background:#fff;border:1px solid var(--line)
        }
        .benefits{display:grid;gap:13px;margin-top:26px}
        .benefit{display:flex;gap:12px;align-items:flex-start}
        .check{
            width:28px;height:28px;flex:0 0 28px;border-radius:9px;background:#dcfce7;color:#16a34a;display:grid;place-items:center
        }.check svg{width:15px}
        .benefit h3{font-size:.93rem}.benefit p{font-size:.82rem;margin-top:4px}
        .mini-product{
            background:#0f172a;border-radius:22px;padding:18px;box-shadow:var(--shadow);position:relative;overflow:hidden
        }
        .mini-product::before{content:"";position:absolute;width:180px;height:180px;background:#2563eb33;border-radius:50%;filter:blur(50px);right:-30px;top:-40px}
        .mini-head{position:relative;display:flex;justify-content:space-between;align-items:center;color:#fff;font-size:.72rem;margin-bottom:15px}
        .mini-grid{position:relative;display:grid;grid-template-columns:repeat(2,1fr);gap:9px}
        .mini-tile{height:105px;border-radius:12px;background:linear-gradient(145deg,#26354d,#172033);display:grid;place-items:center;color:#dbeafe;font-weight:800}
        .mini-toolbar{position:relative;display:flex;justify-content:center;gap:7px;margin-top:12px}
        .mini-control{width:31px;height:31px;border-radius:9px;background:#1e293b;color:#cbd5e1;display:grid;place-items:center}.mini-control svg{width:13px}
        .mini-control.red{background:#dc2626;color:#fff}

        /* Experience */
        .experience-grid{display:grid;grid-template-columns:1.12fr .88fr;gap:64px;align-items:center}
        .experience-card{background:#111827;border-radius:24px;padding:16px;box-shadow:var(--shadow)}
        .exp-top{display:flex;justify-content:space-between;color:#cbd5e1;font-size:.72rem;margin-bottom:13px}
        .exp-layout{display:grid;grid-template-columns:1fr 160px;gap:10px}
        .exp-videos{display:grid;grid-template-columns:repeat(2,1fr);gap:9px}
        .exp-video{height:125px;border-radius:13px;background:linear-gradient(145deg,#273449,#151e2c);position:relative;overflow:hidden}
        .exp-video .avatar{width:50px;height:50px;font-size:.78rem}
        .exp-chat{background:#f8fafc;border-radius:13px;padding:11px}
        .exp-chat strong{font-size:.65rem}.msg{background:#fff;border:1px solid #e2e8f0;border-radius:9px;padding:8px;margin-top:8px;font-size:.54rem;color:#64748b;line-height:1.4}
        .exp-controls{display:flex;justify-content:center;gap:7px;margin-top:11px}
        .exp-ctrl{width:32px;height:32px;border-radius:9px;background:#1f2937;color:#cbd5e1;display:grid;place-items:center}.exp-ctrl svg{width:13px}
        .exp-ctrl.red{background:#dc2626;color:#fff}
        .experience-list{display:grid;gap:12px;margin-top:26px}
        .experience-item{display:flex;align-items:center;gap:11px;color:#334155;font-weight:650;font-size:.9rem}
        .experience-item span{width:34px;height:34px;border-radius:10px;background:#eff6ff;color:#2563eb;display:grid;place-items:center}.experience-item svg{width:16px}

        /* CTA */
        .cta{padding:90px 0}
        .cta-box{
            position:relative;overflow:hidden;text-align:center;
            background:linear-gradient(135deg,#0b1220,#172554 70%,#1d4ed8);
            border-radius:30px;padding:70px 30px;color:#fff
        }
        .cta-box::before,.cta-box::after{
            content:"";position:absolute;border-radius:50%;background:rgba(255,255,255,.08);filter:blur(1px)
        }
        .cta-box::before{width:260px;height:260px;left:-100px;top:-120px}.cta-box::after{width:180px;height:180px;right:-50px;bottom:-80px}
        .cta-box h2{color:#fff;position:relative}.cta-box p{color:#cbd5e1;max-width:590px;margin:18px auto 0;position:relative}
        .cta-actions{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:28px;position:relative}
        .cta .btn-secondary{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.18)}
        .cta .btn-secondary:hover{background:rgba(255,255,255,.13)}

        /* Footer */
        footer{background:#fff;border-top:1px solid var(--line);padding:52px 0 24px}
        .footer-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:40px;align-items:start}
        .footer-about p{max-width:390px;font-size:.86rem;margin-top:14px}
        .footer-links{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap}
        .footer-links a{padding:8px 10px;color:#64748b;font-size:.83rem;font-weight:650}
        .footer-links a:hover{color:#2563eb}
        .footer-bottom{margin-top:34px;padding-top:20px;border-top:1px solid var(--line);display:flex;justify-content:space-between;gap:20px;color:#94a3b8;font-size:.76rem}

        /* Reveal */
        .reveal{opacity:0;transform:translateY(18px);transition:opacity .65s ease,transform .65s ease}
        .reveal.visible{opacity:1;transform:none}

        @media (prefers-reduced-motion:reduce){
            html{scroll-behavior:auto}
            *,*::before,*::after{animation:none!important;transition:none!important}
            .reveal{opacity:1;transform:none}
        }

        @media(max-width:1024px){
            .nav-links{display:none}.menu-btn{display:grid}
            .nav-actions .nav-login,.nav-actions .btn{display:none}
            .hero-grid{grid-template-columns:1fr;gap:54px}
            .hero-copy{max-width:690px}
            .hero-visual{max-width:820px}
            .meeting-window{transform:none}
            .features-grid{grid-template-columns:repeat(2,1fr)}
            .why-wrap,.experience-grid{grid-template-columns:1fr}
            .dashboard{grid-template-columns:170px 1fr}
            .stat-grid{grid-template-columns:repeat(2,1fr)}
        }

        @media(max-width:768px){
            .container{width:min(calc(100% - 28px),var(--container))}
            .section{padding:76px 0}
            h1{font-size:clamp(2.75rem,13vw,4.25rem)}
            h2{font-size:clamp(2rem,8vw,3rem)}
            .hero{padding-top:120px;min-height:auto}
            .hero-actions .btn{flex:1 1 180px}
            .float-card{display:none}
            .meeting-body{grid-template-columns:1fr}
            .side-panel{display:none}
            .features-grid{grid-template-columns:1fr}
            .feature-card{min-height:0}
            .dashboard{grid-template-columns:1fr}
            .sidebar{display:none}
            .dash-main{padding:15px}
            .meeting-row{grid-template-columns:1fr auto}
            .meeting-row > :nth-child(2),.meeting-row > :nth-child(3){display:none}
            .steps{grid-template-columns:1fr;gap:34px}
            .steps::before{display:none}
            .why-wrap{padding:28px 20px}
            .exp-layout{grid-template-columns:1fr}
            .exp-chat{display:none}
            .footer-grid{grid-template-columns:1fr}
            .footer-links{justify-content:flex-start}
            .footer-bottom{flex-direction:column}
        }

        @media(max-width:480px){
            .navbar{padding:12px 0}
            .brand-mark{width:34px;height:34px}.brand{font-size:1rem}
            .hero{padding-top:105px}
            .hero-badge{font-size:.72rem}
            .hero-copy{font-size:1rem}
            .hero-points{gap:9px;font-size:.75rem}
            .video-area{gap:7px;padding:9px}
            .video-tile{min-height:116px}
            .avatar{width:54px;height:54px;font-size:.85rem}
            .meeting-controls{gap:6px}
            .control{width:32px;height:32px}
            .stat-grid{grid-template-columns:1fr 1fr}
            .cta-box{padding:54px 18px}
        }
    </style>
</head>
<body>
@php
    $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login');
    $registerUrl = \Illuminate\Support\Facades\Route::has('register') ? route('register') : url('/register');
    $dashboardUrl = \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : url('/dashboard');
@endphp

<nav class="navbar" id="navbar">
    <div class="container nav-inner">
        <a href="#home" class="brand" aria-label="SmartMeet home">
            <span class="brand-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/>
                    <rect x="3" y="6" width="12" height="12" rx="3"/>
                </svg>
            </span>
            SmartMeet
        </a>

        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#why-smartmeet">Why SmartMeet</a>
        </div>

        <div class="nav-actions">
            @auth
                <a class="nav-login" href="{{ $dashboardUrl }}">Dashboard</a>
                <a class="btn btn-primary" href="{{ $dashboardUrl }}">Open SmartMeet</a>
            @else
                <a class="nav-login" href="{{ $loginUrl }}">Login</a>
                <a class="btn btn-primary" href="{{ $registerUrl }}">Get Started</a>
            @endauth

            <button class="menu-btn" id="menuBtn" aria-label="Open navigation menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#why-smartmeet">Why SmartMeet</a>
            @auth
                <a class="mobile-cta" href="{{ $dashboardUrl }}">Open Dashboard</a>
            @else
                <a href="{{ $loginUrl }}">Login</a>
                <a class="mobile-cta" href="{{ $registerUrl }}">Get Started</a>
            @endauth
        </div>
    </div>
</nav>

<main>
    <section class="hero" id="home">
        <div class="container hero-grid">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Smarter online meetings
                </div>

                <h1 class="hero-title">
                    Meet. Connect.<br>
                    <span class="accent">Collaborate.</span>
                </h1>

                <p class="hero-copy">
                    SmartMeet brings video meetings, real-time chat, scheduling and live transcription together in one simple workspace.
                </p>

                <div class="hero-actions">
                    @auth
                        <a class="btn btn-primary" href="{{ $dashboardUrl }}">
                            Open Dashboard
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </a>
                    @else
                        <a class="btn btn-primary" href="{{ $registerUrl }}">
                            Get Started
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </a>
                    @endauth

                    <a class="btn btn-secondary" href="#features">
                        Explore Features
                    </a>
                </div>

                <div class="hero-points">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                        Browser Based
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                        Simple Scheduling
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                        Real-Time Collaboration
                    </span>
                </div>
            </div>

            <div class="hero-visual">
                <div class="mock-glow"></div>

                <div class="float-card float-one">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v12H7l-3 3V4Z"/>
                        <path d="M8 8h8M8 11h5"/>
                    </svg>
                    Live Transcript
                </div>

                <div class="float-card float-two">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    8 Participants
                </div>

                <div class="meeting-window">
                    <div class="window-top">
                        <div class="window-dots"><i></i><i></i><i></i></div>
                        <div class="window-title">Weekly Product Sync</div>
                        <div class="live-pill"><span>●</span> LIVE</div>
                    </div>

                    <div class="meeting-body">
                        <div class="video-area">
                            <div class="video-tile avatar-bg-1">
                                <div class="avatar">AM</div>
                                <div class="tile-foot">
                                    <span class="tile-name">Areeb Malik</span>
                                    <span class="mic-mini">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg>
                                    </span>
                                </div>
                            </div>

                            <div class="video-tile avatar-bg-2">
                                <div class="avatar">SK</div>
                                <div class="tile-foot">
                                    <span class="tile-name">Sara Khan</span>
                                    <span class="mic-mini">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg>
                                    </span>
                                </div>
                            </div>

                            <div class="video-tile avatar-bg-3">
                                <div class="avatar">HA</div>
                                <div class="tile-foot">
                                    <span class="tile-name">Hassan Ali</span>
                                    <span class="mic-mini">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 2 20 20"/><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 11.9 5"/></svg>
                                    </span>
                                </div>
                            </div>

                            <div class="video-tile avatar-bg-4">
                                <div class="avatar">ZA</div>
                                <div class="tile-foot">
                                    <span class="tile-name">Zoya Ahmed</span>
                                    <span class="mic-mini">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <aside class="side-panel">
                            <div class="side-head">
                                <strong>Live Transcript</strong>
                                <span>English</span>
                            </div>

                            <div class="transcript-line">
                                <div class="mini-avatar">AM</div>
                                <div>
                                    <b>Areeb Malik</b>
                                    <p>Let's review the next milestone and make sure everyone is aligned.</p>
                                </div>
                            </div>
                            <div class="transcript-line">
                                <div class="mini-avatar">SK</div>
                                <div>
                                    <b>Sara Khan</b>
                                    <p>The design handoff is ready and we can begin implementation today.</p>
                                </div>
                            </div>
                            <div class="transcript-line">
                                <div class="mini-avatar">HA</div>
                                <div>
                                    <b>Hassan Ali</b>
                                    <p>Perfect. I'll share the updated schedule after this meeting.</p>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <div class="meeting-controls">
                        <span class="control">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg>
                        </span>
                        <span class="control">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg>
                        </span>
                        <span class="control">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg>
                        </span>
                        <span class="control">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 8v6M18 11h6"/></svg>
                        </span>
                        <span class="control leave">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="trustbar">
        <div class="container trust-inner">
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                Smart Scheduling
            </div>
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg>
                Video & Audio Meetings
            </div>
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg>
                Real-Time Chat
            </div>
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16M4 10h16M4 15h10M4 20h7"/></svg>
                Live Transcription
            </div>
        </div>
    </div>

    <section class="section" id="features">
        <div class="container">
            <div class="center reveal">
                <div class="section-label">SmartMeet Features</div>
                <h2>Everything you need for better meetings</h2>
                <p class="section-copy">A focused workspace that keeps online meetings simple, organized and easy to manage from start to finish.</p>
            </div>

            <div class="features-grid">
                @php
                    $features = [
                        ['Video Meetings','Connect face-to-face directly from your browser.','video'],
                        ['Audio Meetings','Join conversations even when you do not need your camera.','mic'],
                        ['Live Transcription','Follow conversations with real-time meeting transcription.','text'],
                        ['Real-Time Chat','Keep the conversation moving alongside your meeting.','chat'],
                        ['Smart Scheduling','Plan and organize upcoming meetings with ease.','calendar'],
                        ['Easy Invitations','Invite participants through simple meeting links.','link'],
                        ['Participant Management','See and manage people joining your meeting.','users'],
                        ['Meeting Dashboard','Keep upcoming, active and completed meetings organized.','grid'],
                    ];
                @endphp

                @foreach($features as $feature)
                    <article class="feature-card reveal">
                        <div class="icon-box">
                            @switch($feature[2])
                                @case('video')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg>
                                    @break
                                @case('mic')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg>
                                    @break
                                @case('text')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16M4 10h16M4 15h10M4 20h7"/></svg>
                                    @break
                                @case('chat')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg>
                                    @break
                                @case('calendar')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="3"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                                    @break
                                @case('link')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    @break
                                @case('users')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            @endswitch
                        </div>
                        <h3>{{ $feature[0] }}</h3>
                        <p>{{ $feature[1] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section showcase">
        <div class="container">
            <div class="center reveal">
                <div class="section-label" style="color:#60a5fa">Product Experience</div>
                <h2>One place for all your meetings</h2>
                <p class="section-copy">Stay organized, see what is happening now and keep your next meeting only a click away.</p>
            </div>

            <div class="dashboard-shell reveal">
                <div class="browserbar">
                    <i></i><i></i><i></i>
                    <div class="browser-url">smartmeet.app/dashboard</div>
                </div>

                <div class="dashboard">
                    <aside class="sidebar">
                        <div class="side-brand">
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg>
                            </span>
                            SmartMeet
                        </div>

                        <div class="side-nav">
                            <div class="active">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                Dashboard
                            </div>
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="3"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                                My Meetings
                            </div>
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                Today
                            </div>
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                Schedule Meeting
                            </div>
                        </div>
                    </aside>

                    <div class="dash-main">
                        <div class="dash-top">
                            <div>
                                <h3>Good morning, Areeb 👋</h3>
                                <p>Here is what is happening with your meetings today.</p>
                            </div>
                            <div class="user-chip">
                                <span class="user-circle">AM</span>
                                Areeb Malik
                            </div>
                        </div>

                        <div class="stat-grid">
                            <div class="stat-card"><span>Total Meetings</span><strong>24</strong></div>
                            <div class="stat-card"><span>Today</span><strong>4</strong></div>
                            <div class="stat-card"><span>Live</span><strong style="color:#16a34a">1</strong></div>
                            <div class="stat-card"><span>Upcoming</span><strong>7</strong></div>
                        </div>

                        <div class="meeting-list">
                            <div class="meeting-list-head">
                                <strong>Upcoming Meetings</strong>
                                <span>View all</span>
                            </div>
                            <div class="meeting-row">
                                <b>Weekly Product Sync</b>
                                <span>Today · 3:00 PM</span>
                                <span class="status live">LIVE NOW</span>
                                <span class="join-btn">Join</span>
                            </div>
                            <div class="meeting-row">
                                <b>Client Project Review</b>
                                <span>Today · 5:30 PM</span>
                                <span class="status upcoming">UPCOMING</span>
                                <span class="join-btn">Details</span>
                            </div>
                            <div class="meeting-row">
                                <b>Design Planning</b>
                                <span>Tomorrow · 11:00 AM</span>
                                <span class="status upcoming">UPCOMING</span>
                                <span class="join-btn">Details</span>
                            </div>
                            <div class="meeting-row">
                                <b>Development Standup</b>
                                <span>Tomorrow · 2:00 PM</span>
                                <span class="status upcoming">UPCOMING</span>
                                <span class="join-btn">Details</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="how-it-works">
        <div class="container">
            <div class="center reveal">
                <div class="section-label">How It Works</div>
                <h2>Start meeting in three simple steps</h2>
                <p class="section-copy">No complicated setup. Create your meeting, share the invitation and start collaborating.</p>
            </div>

            <div class="steps">
                <div class="step reveal">
                    <div class="step-num">01</div>
                    <h3>Schedule</h3>
                    <p>Create your meeting and choose the date and time.</p>
                </div>
                <div class="step reveal">
                    <div class="step-num">02</div>
                    <h3>Invite</h3>
                    <p>Share the meeting invitation with your participants.</p>
                </div>
                <div class="step reveal">
                    <div class="step-num">03</div>
                    <h3>Meet</h3>
                    <p>Join from your browser and start collaborating.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="why-smartmeet">
        <div class="container">
            <div class="why-wrap reveal">
                <div>
                    <div class="section-label">Why SmartMeet</div>
                    <h2>Meetings without the complexity</h2>
                    <p class="section-copy">SmartMeet keeps the essentials together so you can focus on the conversation instead of the setup.</p>

                    <div class="benefits">
                        <div class="benefit">
                            <span class="check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div><h3>Browser Based</h3><p>Join meetings directly through the web.</p></div>
                        </div>
                        <div class="benefit">
                            <span class="check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div><h3>Simple Experience</h3><p>A clean interface designed to make meetings easy.</p></div>
                        </div>
                        <div class="benefit">
                            <span class="check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div><h3>Real-Time Collaboration</h3><p>Video, audio, chat and transcription work together.</p></div>
                        </div>
                        <div class="benefit">
                            <span class="check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div><h3>Organized Meetings</h3><p>Keep scheduled, live and completed meetings in one place.</p></div>
                        </div>
                    </div>
                </div>

                <div class="mini-product">
                    <div class="mini-head">
                        <span>Team Collaboration</span>
                        <span style="color:#86efac">● Meeting active</span>
                    </div>
                    <div class="mini-grid">
                        <div class="mini-tile">AM</div>
                        <div class="mini-tile">SK</div>
                        <div class="mini-tile">HA</div>
                        <div class="mini-tile">ZA</div>
                    </div>
                    <div class="mini-toolbar">
                        <span class="mini-control"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg></span>
                        <span class="mini-control"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg></span>
                        <span class="mini-control"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg></span>
                        <span class="mini-control red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3"/></svg></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container experience-grid">
            <div class="experience-card reveal">
                <div class="exp-top">
                    <span>Project Review Meeting</span>
                    <span style="color:#86efac">● Live</span>
                </div>
                <div class="exp-layout">
                    <div class="exp-videos">
                        <div class="exp-video"><div class="avatar">AM</div></div>
                        <div class="exp-video"><div class="avatar">SK</div></div>
                        <div class="exp-video"><div class="avatar">HA</div></div>
                        <div class="exp-video"><div class="avatar">ZA</div></div>
                    </div>
                    <div class="exp-chat">
                        <strong>Meeting Chat</strong>
                        <div class="msg"><b>Sara:</b><br>I've shared the latest update.</div>
                        <div class="msg"><b>Areeb:</b><br>Perfect, let's review it now.</div>
                        <div class="msg"><b>Hassan:</b><br>Looks good from my side.</div>
                    </div>
                </div>
                <div class="exp-controls">
                    <span class="exp-ctrl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg></span>
                    <span class="exp-ctrl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/><rect x="3" y="6" width="12" height="12" rx="3"/></svg></span>
                    <span class="exp-ctrl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg></span>
                    <span class="exp-ctrl red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3"/></svg></span>
                </div>
            </div>

            <div class="reveal">
                <div class="section-label">Meeting Experience</div>
                <h2>Focus on the conversation</h2>
                <p class="section-copy">The controls you need stay close at hand while the interface keeps the meeting itself front and center.</p>

                <div class="experience-list">
                    <div class="experience-item">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></svg></span>
                        Simple audio & video controls
                    </div>
                    <div class="experience-item">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
                        Live participant visibility
                    </div>
                    <div class="experience-item">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg></span>
                        Built-in meeting chat
                    </div>
                    <div class="experience-item">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16M4 10h16M4 15h10M4 20h7"/></svg></span>
                        Real-time transcription
                    </div>
                    <div class="experience-item">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg></span>
                        Easy meeting management
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="cta-box reveal">
                <h2>Ready to make meetings simpler?</h2>
                <p>Bring your conversations, participants and collaboration together with SmartMeet.</p>
                <div class="cta-actions">
                    @auth
                        <a href="{{ $dashboardUrl }}" class="btn btn-primary">Open SmartMeet</a>
                    @else
                        <a href="{{ $registerUrl }}" class="btn btn-primary">Get Started</a>
                        <a href="{{ $loginUrl }}" class="btn btn-secondary">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="#home" class="brand">
                    <span class="brand-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14"/>
                            <rect x="3" y="6" width="12" height="12" rx="3"/>
                        </svg>
                    </span>
                    SmartMeet
                </a>
                <p>Simple online meetings for modern collaboration.</p>
            </div>

            <div class="footer-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="{{ $loginUrl }}">Login</a>
                <a href="{{ $registerUrl }}">Register</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} SmartMeet. All rights reserved.</span>
            <span>Meet. Connect. Collaborate.</span>
        </div>
    </div>
</footer>

<script>
    (() => {
        const navbar = document.getElementById('navbar');
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        const handleScroll = () => {
            navbar.classList.toggle('scrolled', window.scrollY > 16);
        };

        handleScroll();
        window.addEventListener('scroll', handleScroll, { passive: true });

        menuBtn?.addEventListener('click', () => {
            const open = mobileMenu.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', String(open));
        });

        mobileMenu?.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                menuBtn?.setAttribute('aria-expanded', 'false');
            });
        });

        const revealItems = document.querySelectorAll('.reveal');

        if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            revealItems.forEach(item => observer.observe(item));
        } else {
            revealItems.forEach(item => item.classList.add('visible'));
        }
    })();
</script>
</body>
</html>
