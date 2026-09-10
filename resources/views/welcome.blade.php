<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmartMeet | Online Meetings & Video Conferencing</title>

    <meta name="description"
          content="SmartMeet is an online meeting platform for video and audio calls, meeting scheduling, participant invitations, real-time chat and live transcription.">

    <meta name="robots" content="index, follow">

    <link rel="canonical" href="https://smartmeet.live/">

    <!-- Open Graph / Social Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="SmartMeet | Online Meetings & Video Conferencing">
    <meta property="og:description"
          content="SmartMeet is an online meeting platform for video and audio calls, meeting scheduling, participant invitations, real-time chat and live transcription.">
    <meta property="og:url" content="https://smartmeet.live/">
    <meta property="og:site_name" content="SmartMeet">
    <meta property="og:image" content="{{ asset('images/s-logo.png') }}">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SmartMeet | Online Meetings & Video Conferencing">
    <meta name="twitter:description"
          content="SmartMeet is an online meeting platform for video and audio calls, meeting scheduling, participant invitations, real-time chat and live transcription.">
    <meta name="twitter:image" content="{{ asset('images/s-logo.png') }}">

    <link rel="icon" href="{{ asset('images/s-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue: #2563eb;
            --blue-bright: #3b82f6;
            --blue-dark: #1d4ed8;
            --blue-deep: #1746b5;
            --blue-soft: #eff6ff;
            --navy: #0b1220;
            --text: #1f2937;
            --muted: #667085;
            --line: #e8edf5;
            --bg: #f8fbff;
            --white: #ffffff;
            --success: #12b76a;
            --danger: #ef4444;
            --shadow: 0 24px 70px rgba(15, 23, 42, .12);
            --shadow-soft: 0 12px 35px rgba(15, 23, 42, .07);
            --ease: cubic-bezier(.22, 1, .36, 1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "DM Sans", sans-serif;
            color: var(--text);
            background: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        button {
            font: inherit;
        }

        .container {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
        }

        .section {
            padding: 110px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--blue);
            margin-bottom: 14px;
        }

        .eyebrow::before {
            content: "";
            width: 22px;
            height: 2px;
            background: currentColor;
            border-radius: 99px;
        }

        .section-title {
            font-family: "Outfit", sans-serif;
            font-size: clamp(36px, 5vw, 56px);
            line-height: 1.05;
            letter-spacing: -.045em;
            color: var(--navy);
            margin: 0;
        }

        .section-copy {
            color: var(--muted);
            font-size: 17px;
            line-height: 1.75;
            max-width: 680px;
            margin: 18px 0 0;
        }

        /* NAVBAR */
        .navbar {
            animation: navEnter .75s .03s var(--ease) both;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: .35s var(--ease);
        }

        @keyframes navEnter {
            from { opacity: 0; transform: translateY(-14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .navbar.scrolled {
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(232, 237, 245, .9);
            box-shadow: 0 8px 28px rgba(15,23,42,.05);
        }

        .nav-inner {
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            font-family: "Outfit", sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -.045em;
            transition: transform .3s var(--ease);
        }

        .brand:hover {
            transform: translateY(-1px);
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            position: relative;
            background: transparent;
            border: 0;
            box-shadow: none;
            overflow: visible;
            transition: transform .35s var(--ease);
        }

        .brand:hover .brand-mark {
            transform: rotate(-3deg) scale(1.05);
            box-shadow: none;
        }


        .brand-mark img {
            width: 34px;
            height: 34px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 6px 12px rgba(37,99,235,.12));
            transition: transform .35s var(--ease), filter .35s ease;
        }

        .brand:hover .brand-mark img {
            transform: scale(1.05);
            filter: drop-shadow(0 9px 18px rgba(37,99,235,.18));
        }

        .brand-name {
            display: inline-flex;
            align-items: baseline;
        }

        .brand-name .smart {
            color: var(--navy);
        }

        .brand-name .meet {
            color: var(--blue);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
            font-size: 14px;
            font-weight: 600;
            color: #475467;
        }

        .nav-links a {
            transition: .25s ease;
        }

        .nav-links a:hover {
            color: var(--blue);
        }

        .nav-links a {
            position: relative;
            padding: 9px 0;
        }

        .nav-links a::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 1px;
            width: 0;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, #60a5fa, #2563eb);
            transform: translateX(-50%);
            transition: width .3s var(--ease);
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--blue);
        }

        .nav-links a.active::after {
            width: 24px;
            height: 3px;
            bottom: -1px;
            background: linear-gradient(90deg, #60a5fa, #2563eb);
            box-shadow: 0 4px 10px rgba(37,99,235,.20);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            min-height: 50px;
            padding: 0 21px;
            border-radius: 14px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            font-size: 14px;
            font-weight: 750;
            letter-spacing: -.01em;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            transition:
                transform .3s var(--ease),
                box-shadow .3s var(--ease),
                border-color .3s ease,
                background .3s ease;
            cursor: pointer;
        }

        .btn::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .btn::after {
            content: "";
            position: absolute;
            top: -60%;
            left: -45%;
            width: 35%;
            height: 220%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.38), transparent);
            transition: left .65s var(--ease);
            pointer-events: none;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        .btn:hover::after {
            left: 120%;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--blue-bright) 0%, var(--blue) 48%, var(--blue-deep) 100%);
            border-color: rgba(37,99,235,.75);
            box-shadow:
                0 10px 24px rgba(37, 99, 235, .22),
                inset 0 1px 0 rgba(255,255,255,.22);
        }

        .btn-primary::before {
            background: linear-gradient(135deg, #4b91ff, #1d4ed8);
        }

        .btn-primary:hover {
            box-shadow:
                0 18px 34px rgba(37, 99, 235, .30),
                inset 0 1px 0 rgba(255,255,255,.28);
        }

        .btn-primary:hover::before {
            opacity: 1;
        }

        .btn-secondary {
            background: rgba(255,255,255,.92);
            color: var(--navy);
            border-color: #dfe7f2;
            box-shadow: 0 5px 16px rgba(15,23,42,.04);
        }

        .btn-secondary::before {
            background: linear-gradient(180deg, #fff, #f5f9ff);
        }

        .btn-secondary:hover {
            color: var(--navy);
            background: #ffffff;
            border-color: #bfd2ef;
            box-shadow: 0 14px 30px rgba(15,23,42,.09);
        }

        .btn-secondary:hover::before {
            opacity: 1;
        }

        .btn-arrow {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            display: inline-grid;
            place-items: center;
            background: rgba(255,255,255,.16);
            transition: transform .3s var(--ease), background .3s ease;
        }

        .btn:hover .btn-arrow {
            transform: translateX(3px);
            background: rgba(255,255,255,.24);
        }

        .menu-btn {
            display: none;
            width: 44px;
            height: 44px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            position: relative;
            cursor: pointer;
        }

        .menu-btn span,
        .menu-btn::before,
        .menu-btn::after {
            content: "";
            position: absolute;
            left: 12px;
            width: 18px;
            height: 2px;
            background: var(--navy);
            border-radius: 99px;
            transition: .25s ease;
        }

        .menu-btn span {
            top: 21px;
        }

        .menu-btn::before {
            top: 15px;
        }

        .menu-btn::after {
            top: 27px;
        }

        .menu-btn.open span {
            opacity: 0;
        }

        .menu-btn.open::before {
            top: 21px;
            transform: rotate(45deg);
        }

        .menu-btn.open::after {
            top: 21px;
            transform: rotate(-45deg);
        }

        .mobile-menu {
            display: none;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(16px);
            border-top: 1px solid var(--line);
            padding: 14px 20px 18px;
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-menu a {
            display: block;
            padding: 11px 0;
            color: #475467;
            font-weight: 650;
        }

        /* HERO */
        .hero {
            position: relative;
            overflow: hidden;
            padding: 160px 0 100px;
            background:
                radial-gradient(circle at 10% 10%, rgba(37,99,235,.12), transparent 28%),
                radial-gradient(circle at 90% 18%, rgba(59,130,246,.12), transparent 28%),
                linear-gradient(180deg, #fbfdff 0%, #f7faff 72%, #ffffff 100%);
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 460px;
            height: 460px;
            right: -240px;
            top: 140px;
            border-radius: 50%;
            border: 1px solid rgba(37, 99, 235, .1);
            box-shadow:
                0 0 0 80px rgba(37,99,235,.025),
                0 0 0 160px rgba(37,99,235,.015);
        }

        .hero-grid {
            display: grid;            grid-template-columns: .88fr 1.12fr;
            gap: 64px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #dbe8ff;
            background: rgba(255,255,255,.88);
            border-radius: 999px;
            color: #2559b6;
            font-size: 13px;
            font-weight: 750;
            box-shadow: 0 8px 28px rgba(37,99,235,.06);
        }

        .hero-badge i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--blue);
            box-shadow: 0 0 0 5px rgba(37,99,235,.1);
            animation: badgePulse 2.4s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 5px rgba(37,99,235,.10); }
            50% { box-shadow: 0 0 0 9px rgba(37,99,235,.025); }
        }

        .hero h1 {
            font-family: "Outfit", sans-serif;
            font-size: clamp(42px, 4.35vw, 60px);
            line-height: 1.01;
            letter-spacing: -.052em;
            color: var(--navy);
            margin: 22px 0;
        }

        .hero h1 span {
            color: transparent;
            background: linear-gradient(100deg, #2563eb 8%, #4f8cff 52%, #1d4ed8 92%);
            -webkit-background-clip: text;
            background-clip: text;
            position: relative;
        }

        .hero h1 span::after {
            content: "";
            position: absolute;
            left: 2px;
            right: 8%;
            bottom: -8px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(37,99,235,.15), rgba(59,130,246,.55), rgba(37,99,235,.08));
            transform: scaleX(.15);
            transform-origin: left;
            animation: underlineGrow .9s .85s var(--ease) forwards;
        }

        @keyframes underlineGrow {
            to { transform: scaleX(1); }
        }

        .hero p {
            margin: 0;
            max-width: 600px;
            font-size: 18px;
            line-height: 1.8;
            color: var(--muted);
        }

        .hero-actions {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-benefits {
            margin-top: 28px;
            display: flex;
            gap: 14px 22px;
            flex-wrap: wrap;
            color: #475467;
            font-size: 13px;
            font-weight: 650;
        }

        .hero-benefits span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .hero-benefits span::before {
            content: "✓";
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: #ecfdf3;
            color: #079455;
            display: grid;
            place-items: center;
            font-size: 10px;
            font-weight: 900;
        }

        /* MEETING MOCKUP */
        .mockup-wrap {
            position: relative;
            width: 86%;
            max-width: 560px;
            margin-left: auto;
            perspective: 1200px;
        }

        .mockup-orbit {
            position: absolute;
            inset: -34px -24px -30px;
            border: 1px solid rgba(37,99,235,.09);
            border-radius: 34px;
            pointer-events: none;
        }

        .mockup-orbit::before,
        .mockup-orbit::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #dbe7f8;
            box-shadow: 0 10px 28px rgba(37,99,235,.10);
        }

        .mockup-orbit::before {
            width: 14px;
            height: 14px;
            top: 17%;
            left: -8px;
            animation: orbitDotOne 5s ease-in-out infinite;
        }

        .mockup-orbit::after {
            width: 10px;
            height: 10px;
            right: 7%;
            bottom: -6px;
            animation: orbitDotTwo 4.6s ease-in-out infinite;
        }

        @keyframes orbitDotOne {
            0%,100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(12px) scale(1.08); }
        }

        @keyframes orbitDotTwo {
            0%,100% { transform: translateX(0) scale(1); }
            50% { transform: translateX(-14px) scale(1.08); }
        }

        .floating-chip {
            position: absolute;
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 13px;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(14px);
            border: 1px solid #e3eaf5;
            box-shadow: 0 16px 38px rgba(15,23,42,.10);
            color: #344054;
            font-size: 11px;
            font-weight: 750;
            pointer-events: none;
        }

        .floating-chip .chip-icon {
            width: 28px;
            height: 28px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            background: #eff6ff;
            color: var(--blue);
        }

        .floating-chip.transcript-chip {
            left: -34px;
            bottom: 65px;
            animation: chipFloatOne 4.8s ease-in-out infinite;
        }

        .floating-chip.people-chip {
            right: -32px;
            top: 86px;
            animation: chipFloatTwo 5.2s ease-in-out infinite;
        }

        @keyframes chipFloatOne {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-7px); }
        }

        @keyframes chipFloatTwo {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(7px); }
        }

        .meeting-top-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meeting-secure {
            color: #667085;
            background: #f8fafc;
            border: 1px solid #edf1f6;
            padding: 6px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
        }

        .video-tile::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(180deg, transparent 50%, rgba(2,6,23,.18));
            pointer-events: none;
        }

        .avatar.avatar-purple {
            background: linear-gradient(145deg, #f2edff, #d8c8ff);
            color: #6941c6;
        }

        .avatar.avatar-green {
            background: linear-gradient(145deg, #ebfff5, #bdf0d6);
            color: #087a4a;
        }

        .avatar.avatar-amber {
            background: linear-gradient(145deg, #fff7e8, #f7db9d);
            color: #a15c06;
        }

        .speaker-wave {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            align-items: end;
            gap: 2px;
            height: 15px;
            padding: 4px 6px;
            border-radius: 999px;
            background: rgba(5,11,22,.62);
            backdrop-filter: blur(8px);
            z-index: 2;
        }

        .speaker-wave span {
            width: 2px;
            border-radius: 99px;
            background: #7fb1ff;
            animation: wave 1.1s ease-in-out infinite;
        }

        .speaker-wave span:nth-child(1) { height: 5px; animation-delay: 0s; }
        .speaker-wave span:nth-child(2) { height: 9px; animation-delay: .15s; }
        .speaker-wave span:nth-child(3) { height: 6px; animation-delay: .3s; }
        .speaker-wave span:nth-child(4) { height: 11px; animation-delay: .45s; }

        @keyframes wave {
            0%,100% { transform: scaleY(.6); opacity: .65; }
            50% { transform: scaleY(1.1); opacity: 1; }
        }

        .meeting-controls .control {
            transition: transform .25s var(--ease), background .25s ease;
        }

        .meeting-controls .control:hover {
            transform: translateY(-2px);
            background: #263652;
        }

        .meeting-controls .control.leave:hover {
            background: #cf3d42;
        }

        .mockup-glow {
            position: absolute;
            inset: 10% 0 -5%;
            background: radial-gradient(circle, rgba(37,99,235,.20), transparent 65%);
            filter: blur(32px);
            z-index: -1;
        }

        .meeting-card {
            border-radius: 25px;
            border: 1px solid #d9e3f1;
            overflow: hidden;
            background: #fff;
            box-shadow:
                0 34px 90px rgba(15,23,42,.15),
                0 10px 28px rgba(37,99,235,.06);
            animation: float 5.8s ease-in-out infinite;
            transition: transform .5s var(--ease), box-shadow .5s var(--ease);
        }

        .mockup-wrap:hover .meeting-card {
            box-shadow:
                0 42px 110px rgba(15,23,42,.18),
                0 14px 34px rgba(37,99,235,.10);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .meeting-top {
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid #edf1f6;
        }

        .meeting-title {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 12px;
            font-weight: 750;
            color: #344054;
        }

        .meeting-title img {
            width: 26px;
            height: 26px;
        }

        .live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #d92d20;
            background: #fff1f2;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
        }

        .live::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--danger);
            box-shadow: 0 0 0 4px rgba(239,68,68,.1);
            animation: livePulse 1.8s ease-in-out infinite;
        }

        @keyframes livePulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(239,68,68,.10); opacity: 1; }
            50% { box-shadow: 0 0 0 8px rgba(239,68,68,.025); opacity: .78; }
        }

        .meeting-body {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 390px;
        }

        .video-grid {
            background: #0d1525;
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .video-tile {
            min-height: 168px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.08);
            background:
                radial-gradient(circle at 25% 20%, rgba(77,130,255,.22), transparent 35%),
                linear-gradient(145deg, #1d2a43, #121b2d);
            display: grid;
            place-items: center;
            position: relative;
        }

        .video-tile.active {
            border: 2px solid #4f8cff;
            box-shadow:
                inset 0 0 0 2px rgba(79,140,255,.14),
                0 0 0 0 rgba(79,140,255,.20);
            animation: speakerPulse 2.8s ease-in-out infinite;
        }

        @keyframes speakerPulse {
            0%, 100% { box-shadow: inset 0 0 0 2px rgba(79,140,255,.14), 0 0 0 0 rgba(79,140,255,.16); }
            50% { box-shadow: inset 0 0 0 2px rgba(79,140,255,.18), 0 0 0 5px rgba(79,140,255,.03); }
        }

        .avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(145deg,#eef4ff,#bed4ff);
            color: #1d4ed8;
            font-family: "Outfit", sans-serif;
            font-size: 18px;
            font-weight: 800;
            display: grid;
            place-items: center;
            border: 3px solid rgba(255,255,255,.75);
        }

        .person {
            position: absolute;
            left: 9px;
            bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(5,11,22,.72);
            color: #fff;
            padding: 5px 7px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 650;
        }

        .sidebar {
            background: #fff;
            border-left: 1px solid #edf1f6;
        }

        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #edf1f6;
        }

        .tab {
            text-align: center;
            padding: 12px 4px;
            font-size: 10px;
            font-weight: 750;
            color: #98a2b3;
        }

        .tab.active {
            color: var(--blue);
            border-bottom: 2px solid var(--blue);
        }

        .chat {
            padding: 12px 10px;
        }

        .chat-item {
            margin-bottom: 13px;
        }

        .chat-item strong {
            display: block;
            font-size: 9px;
            color: #344054;
            margin-bottom: 4px;
        }

        .bubble {
            background: #f7f9fc;
            color: #667085;
            padding: 7px 8px;
            border-radius: 8px;
            font-size: 9px;
            line-height: 1.5;
        }

        .transcript {
            border-top: 1px solid #eef2f6;
            margin-top: 10px;
            padding-top: 10px;
        }

        .transcript strong {
            display: block;
            color: #344054;
            font-size: 9px;
            margin-bottom: 5px;
        }

        .transcript p {
            margin: 0;
            color: #667085;
            font-size: 9px;
            line-height: 1.5;
        }

        .meeting-controls {
            height: 68px;
            background: #0d1525;
            border-top: 1px solid rgba(255,255,255,.06);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .control {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #1d2a43;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 13px;
        }

        .control.leave {
            width: auto;
            padding: 0 13px;
            background: #e5484d;
            font-size: 10px;
            font-weight: 750;
        }


        .hero {
            isolation: isolate;
        }

        .hero-ambient {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .hero-ambient.one {
            width: 240px;
            height: 240px;
            left: 3%;
            top: 36%;
            background: radial-gradient(circle, rgba(96,165,250,.16), transparent 68%);
            animation: ambientOne 8s ease-in-out infinite;
        }

        .hero-ambient.two {
            width: 320px;
            height: 320px;
            right: 2%;
            bottom: -4%;
            background: radial-gradient(circle, rgba(37,99,235,.11), transparent 70%);
            animation: ambientTwo 10s ease-in-out infinite;
        }

        @keyframes ambientOne {
            0%,100% { transform: translate3d(0,0,0) scale(1); }
            50% { transform: translate3d(18px,-14px,0) scale(1.07); }
        }

        @keyframes ambientTwo {
            0%,100% { transform: translate3d(0,0,0) scale(1); }
            50% { transform: translate3d(-22px,16px,0) scale(1.05); }
        }

        .mockup-shell {
            position: relative;
            padding: 16px;
            border-radius: 32px;
            background: linear-gradient(145deg, rgba(255,255,255,.95), rgba(239,246,255,.72));
            border: 1px solid rgba(191,211,238,.82);
            box-shadow:
                0 32px 90px rgba(15,23,42,.11),
                inset 0 1px 0 rgba(255,255,255,.95);
            transition: transform .55s var(--ease), box-shadow .55s var(--ease);
        }

        .mockup-wrap:hover .mockup-shell {
            transform: translateY(-5px) scale(1.008);
            box-shadow:
                0 44px 112px rgba(15,23,42,.15),
                0 16px 38px rgba(37,99,235,.09);
        }

        .mockup-shine {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            overflow: hidden;
            pointer-events: none;
            z-index: 5;
        }

        .mockup-shine::after {
            content: "";
            position: absolute;
            top: -35%;
            left: -60%;
            width: 34%;
            height: 180%;
            transform: rotate(16deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.42), transparent);
            animation: mockupShine 6.8s ease-in-out infinite;
        }

        @keyframes mockupShine {
            0%,70% { left: -60%; opacity: 0; }
            77% { opacity: 1; }
            100% { left: 135%; opacity: 0; }
        }

        .mini-status-card {
            position: absolute;
            z-index: 8;
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 155px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(14px);
            border: 1px solid #e1e9f5;
            box-shadow: 0 16px 40px rgba(15,23,42,.10);
            color: #344054;
            font-size: 11px;
            font-weight: 700;
        }

        .mini-status-card.top-left {
            left: -32px;
            top: 20%;
            animation: miniFloatA 5.4s ease-in-out infinite;
        }

        .mini-status-card.bottom-right {
            right: -28px;
            bottom: 14%;
            animation: miniFloatB 5.8s ease-in-out infinite;
        }

        .mini-status-icon {
            width: 29px;
            height: 29px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            background: #eff6ff;
            color: var(--blue);
            border: 1px solid #deebff;
        }

        .mini-status-copy small {
            display: block;
            margin-top: 2px;
            font-size: 8px;
            color: #98a2b3;
            font-weight: 700;
        }

        @keyframes miniFloatA {
            0%,100% { transform: translateY(0) rotate(-1deg); }
            50% { transform: translateY(-8px) rotate(1deg); }
        }

        @keyframes miniFloatB {
            0%,100% { transform: translateY(0) rotate(1deg); }
            50% { transform: translateY(8px) rotate(-1deg); }
        }

        .meeting-card {
            transform-origin: center;
        }

        .video-tile {
            transition: transform .35s var(--ease), border-color .35s ease, box-shadow .35s ease;
        }

        .video-tile:hover {
            transform: translateY(-3px);
            border-color: rgba(96,165,250,.42);
            box-shadow: 0 14px 32px rgba(2,6,23,.18);
        }

        .avatar {
            transition: transform .35s var(--ease), box-shadow .35s var(--ease);
        }

        .video-tile:hover .avatar {
            transform: scale(1.06);
            box-shadow: 0 12px 30px rgba(37,99,235,.16);
        }

        .mockup-wrap.reveal-ready {
            opacity: 0;
            transform: translateY(22px) scale(.98);
            filter: blur(5px);
        }

        .mockup-wrap.reveal-ready.mockup-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
            transition:
                opacity .95s var(--ease),
                transform .95s var(--ease),
                filter .95s var(--ease);
        }


        .hero-badge {
            transition: transform .3s var(--ease), box-shadow .3s ease, border-color .3s ease;
        }

        .hero-badge:hover {
            transform: translateY(-2px);
            border-color: #c8dbfb;
            box-shadow: 0 12px 34px rgba(37,99,235,.10);
        }

        .hero-copy {
            max-width: 680px;
        }

        .hero-copy .hero-actions {
            margin-top: 26px;
        }

        .hero-copy .hero-benefits {
            margin-top: 24px;
        }

        .meeting-title img {
            background: transparent !important;
            border-radius: 0 !important;
        }

        .mockup-wrap {
            perspective: 1200px;
        }

        .mockup-wrap:hover .meeting-card {
            transform: rotateX(1deg) rotateY(-1deg);
        }

        .meeting-card {
            transition: transform .45s var(--ease), box-shadow .45s var(--ease);
        }


        /* SIMPLER PROFESSIONAL HERO PREVIEW */
        .meeting-body {
            grid-template-columns: 1fr !important;
        }

        .sidebar,
        .side-panel {
            display: none !important;
        }

        .video-grid,
        .video-area {
            grid-template-columns: 1fr 1fr;
            min-height: 350px;
            padding: 12px;
            gap: 10px;
        }

        .video-tile {
            min-height: 160px;
        }

        .meeting-card {
            border-radius: 22px;
        }

        .meeting-controls {
            height: 62px;
        }

        .mockup-orbit,
        .floating-chip,
        .mini-status-card {
            display: none !important;
        }

        .mockup-shell {
            padding: 12px;
            border-radius: 26px;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(206,219,237,.9);
            box-shadow: 0 26px 72px rgba(15,23,42,.10);
        }

        .mockup-wrap:hover .mockup-shell {
            transform: translateY(-3px);
        }

        .mockup-wrap:hover .meeting-card {
            transform: none;
        }

        @media (max-width: 1040px) {
            .mockup-wrap {
                width: 89%;
                max-width: 560px;
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 620px) {
            .mockup-wrap {
                width: 94%;
                max-width: 520px;
            }

            .video-grid,
            .video-area {
                min-height: 310px;
                gap: 7px;
                padding: 8px;
            }

            .video-tile {
                min-height: 140px;
            }
        }


        /* HERO IMAGE VISUAL */
        .hero-visual {
            position: relative;
            width: 100%;
            max-width: 760px;
            min-height: 550px;
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-visual-glow {
            position: absolute;
            width: 88%;
            height: 78%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.18), rgba(37,99,235,.06) 48%, transparent 72%);
            filter: blur(30px);
            animation: heroGlow 6s ease-in-out infinite;
        }

        @keyframes heroGlow {
            0%,100% { transform: scale(1); opacity: .78; }
            50% { transform: scale(1.05); opacity: 1; }
        }

        .hero-image-card {
            position: relative;
            z-index: 2;
            width: 91%;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(255,255,255,.88);
            border: 1px solid rgba(203,216,234,.85);
            box-shadow:
                0 34px 90px rgba(15,23,42,.13),
                0 12px 30px rgba(37,99,235,.07);
            transition: transform .55s var(--ease), box-shadow .55s var(--ease);
        }

        .hero-image-card::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }

        .hero-visual:hover .hero-image-card {
            transform: translateY(-6px) scale(1.01);
            box-shadow:
                0 42px 110px rgba(15,23,42,.16),
                0 16px 38px rgba(37,99,235,.10);
        }

        .hero-meeting-image {
            display: block;
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: transform .7s var(--ease);
        }

        .hero-visual:hover .hero-meeting-image {
            transform: scale(1.02);
        }

        .hero-feature-card {
            position: absolute;
            z-index: 4;
            min-width: 150px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 13px;
            border-radius: 15px;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(14px);
            border: 1px solid #e1e9f5;
            box-shadow: 0 14px 34px rgba(15,23,42,.09);
            color: #344054;
            transition: transform .35s var(--ease), box-shadow .35s var(--ease);
        }

        .hero-feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 42px rgba(15,23,42,.12);
        }

        .hero-feature-card strong {
            display: block;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.2;
        }

        .hero-feature-card small {
            display: block;
            margin-top: 2px;
            font-size: 8.5px;
            color: #98a2b3;
            line-height: 1.25;
        }

        .feature-mini-icon {
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: linear-gradient(145deg,#eff6ff,#dbeafe);
            color: var(--blue);
            border: 1px solid #d8e7fb;
        }

        .feature-video {
            left: 2%;
            top: 14%;
            animation: floatA 5.6s ease-in-out infinite;
        }

        .feature-schedule {
            left: -1%;
            bottom: 15%;
            animation: floatB 6s ease-in-out infinite;
        }

        .feature-collab {
            right: -1%;
            top: 20%;
            animation: floatB 5.8s ease-in-out infinite;
        }

        .feature-transcript {
            right: 1%;
            bottom: 18%;
            animation: floatA 6.2s ease-in-out infinite;
        }

        @keyframes floatA {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-7px); }
        }

        @keyframes floatB {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(7px); }
        }

        @media (max-width: 1100px) {
            .hero-visual {
                max-width: 620px;
                min-height: 500px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-image-card {
                width: 82%;
            }
        }

        @media (max-width: 760px) {
            .hero-visual {
                min-height: auto;
                padding: 34px 0 18px;
            }

            .hero-image-card {
                width: 92%;
            }

            .hero-feature-card {
                display: none;
            }
        }

        @media (max-width: 520px) {
            .hero-visual {
                padding-top: 24px;
            }

            .hero-image-card {
                width: 96%;
                border-radius: 20px;
            }
        }

        /* BENEFITS STRIP */
        .benefit-strip {
            position: relative;
            margin-top: -34px;
            z-index: 5;
        }

        .benefit-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow-soft);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            padding: 14px 18px;
        }

        .benefit {
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-right: 1px solid var(--line);
            color: #344054;
            font-size: 13px;
            font-weight: 700;
        }

        .benefit:last-child {
            border-right: 0;
        }

        .benefit-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--blue-soft);
            color: var(--blue);
            display: grid;
            place-items: center;
        }

        /* FEATURES */
        .features-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 50px;
            margin-bottom: 44px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .feature-card {
            min-height: 220px;
            padding: 24px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #fff, #fbfdff);
            transition: .35s var(--ease);
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: #ccdaf0;
            box-shadow: var(--shadow-soft);
        }

        .feature-card:nth-child(1),
        .feature-card:nth-child(8) {
            grid-column: span 2;
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: linear-gradient(145deg, #f4f8ff, #e8f1ff);
            border: 1px solid #dce9fb;
            color: var(--blue);
            display: grid;
            place-items: center;
            margin-bottom: 26px;
            font-size: 20px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
            transition: transform .35s var(--ease), box-shadow .35s var(--ease);
        }

        .feature-card:hover .feature-icon {
            transform: translateY(-3px) rotate(-3deg) scale(1.05);
            box-shadow: 0 10px 24px rgba(37,99,235,.12);
        }

        .feature-card h3 {
            margin: 0 0 9px;
            font-family: "Outfit", sans-serif;
            color: var(--navy);
            font-size: 18px;
        }

        .feature-card p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        /* SHOWCASE */
        .showcase {
            background: var(--bg);
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: .82fr 1.18fr;
            gap: 66px;
            align-items: center;
        }

        .check-list {
            display: grid;
            gap: 12px;
            margin-top: 26px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #475467;
            font-size: 14px;
            font-weight: 650;
        }

        .check b {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: #ecfdf3;
            color: #079455;
            display: grid;
            place-items: center;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 160px 1fr;
            min-height: 435px;
            overflow: hidden;
            border-radius: 22px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .dash-side {
            background: #0e1728;
            color: #fff;
            padding: 18px 14px;
        }

        .dash-brand {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 25px;
        }

        .dash-brand img {
            width: 24px;
            height: 24px;
        }

        .dash-link {
            padding: 9px 10px;
            border-radius: 8px;
            color: #aeb9ca;
            font-size: 10px;
            font-weight: 650;
            margin-bottom: 6px;
        }

        .dash-link.active {
            background: #1c2a42;
            color: #fff;
        }

        .dash-main {
            background: #fbfcfe;
            padding: 20px;
        }

        .dash-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .dash-top h4 {
            margin: 0;
            color: var(--navy);
        }

        .dash-top span {
            font-size: 9px;
            color: #98a2b3;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
            margin-bottom: 14px;
        }

        .stat {
            background: #fff;
            border: 1px solid #ebeff5;
            border-radius: 10px;
            padding: 12px;
        }

        .stat small {
            display: block;
            font-size: 8px;
            color: #98a2b3;
            margin-bottom: 6px;
        }

        .stat strong {
            font-size: 18px;
            color: var(--navy);
        }

        .meeting-table {
            border: 1px solid #ebeff5;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-head,
        .table-row {
            display: grid;
            grid-template-columns: 1.5fr .8fr .8fr;
            gap: 8px;
            align-items: center;
            padding: 10px 12px;
        }

        .table-head {
            font-size: 8px;
            color: #98a2b3;
            font-weight: 750;
            border-bottom: 1px solid #edf1f6;
        }

        .table-row {
            font-size: 9px;
            color: #475467;
            border-bottom: 1px solid #f0f2f5;
        }

        .table-row:last-child {
            border-bottom: 0;
        }

        .table-row strong {
            color: #344054;
        }

        .status {
            justify-self: start;
            font-size: 7px;
            font-weight: 800;
            padding: 4px 7px;
            border-radius: 999px;
        }

        .status.active {
            background: #ecfdf3;
            color: #067647;
        }

        .status.upcoming {
            background: #eff8ff;
            color: #175cd3;
        }

        .status.completed {
            background: #f2f4f7;
            color: #475467;
        }

        /* HOW IT WORKS */
        .how-head {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 52px;
        }

        .how-head .section-copy {
            margin-left: auto;
            margin-right: auto;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            position: relative;
        }

        .steps::before {
            content: "";
            position: absolute;
            top: 42px;
            left: 16%;
            right: 16%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #cfe0fb 15%, #cfe0fb 85%, transparent);
        }

        .step {
            text-align: center;
            padding: 0 16px;
            position: relative;
        }

        .step-num {
            width: 84px;
            height: 84px;
            border-radius: 24px;
            margin: 0 auto 22px;
            background: #fff;
            border: 1px solid #dbe6f5;
            box-shadow: var(--shadow-soft);
            display: grid;
            place-items: center;
            color: var(--blue);
            font-family: "Outfit", sans-serif;
            font-weight: 800;
            font-size: 20px;
            position: relative;
            z-index: 1;
        }

        .step h3 {
            margin: 0 0 8px;
            font-family: "Outfit", sans-serif;
            font-size: 20px;
            color: var(--navy);
        }

        .step p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        /* EXPERIENCE */
        .experience {
            background: var(--navy);
            color: #fff;
        }

        .experience .section-title {
            color: #fff;
        }

        .experience .section-copy {
            color: #a9b7cb;
        }

        .experience .eyebrow {
            color: #78a8ff;
        }

        .experience-grid {
            margin-top: 44px;
            display: grid;
            grid-template-columns: 1.05fr .95fr .95fr;
            gap: 16px;
        }

        .exp-card {
            background: #101b2e;
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 20px;
            padding: 22px;
            min-height: 290px;
        }

        .exp-card h3 {
            margin: 0 0 18px;
            font-family: "Outfit", sans-serif;
            font-size: 17px;
        }

        .transcript-line {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .transcript-line time {
            color: #61728e;
            font-size: 9px;
        }

        .transcript-line p {
            margin: 0;
            color: #bbc6d7;
            font-size: 11px;
            line-height: 1.55;
        }

        .chat-bubble {
            width: 85%;
            background: #16253d;
            color: #c5d0df;
            padding: 10px 11px;
            border-radius: 11px;
            margin-bottom: 10px;
            font-size: 10px;
            line-height: 1.5;
        }

        .chat-bubble.me {
            margin-left: auto;
            background: #1e4f9f;
            color: #eaf2ff;
        }

        .participant-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .participant-user {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #c7d2e2;
            font-size: 11px;
        }

        .participant-user span {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #1b3152;
            color: #9fc0ff;
            display: grid;
            place-items: center;
            font-size: 10px;
            font-weight: 800;
        }

        .online {
            width: 7px;
            height: 7px;
            background: var(--success);
            border-radius: 50%;
        }

        /* CTA */
        .cta-wrap {
            padding: 92px 0;
        }

        .cta {
            border-radius: 30px;
            overflow: hidden;
            text-align: center;
            padding: 66px 40px;
            color: #fff;
            background:
                radial-gradient(circle at 12% 0%, rgba(96,165,250,.24), transparent 30%),
                radial-gradient(circle at 88% 100%, rgba(37,99,235,.22), transparent 30%),
                var(--navy);
        }

        .cta h2 {
            margin: 0;
            font-family: "Outfit", sans-serif;
            font-size: clamp(34px, 4.4vw, 52px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .cta p {
            max-width: 620px;
            margin: 18px auto 28px;
            color: #aab7ca;
            font-size: 16px;
            line-height: 1.7;
        }

        .cta .btn-secondary {
            background: #ffffff;
            color: var(--navy);
            border-color: rgba(255,255,255,.72);
        }

        .cta .btn-secondary:hover {
            background: #f7faff;
            color: var(--navy);
            border-color: #ffffff;
            box-shadow: 0 16px 34px rgba(0,0,0,.18);
        }

        .cta .btn-secondary::after {
            background: linear-gradient(90deg, transparent, rgba(37,99,235,.10), transparent);
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--line);
            padding: 52px 0 28px;
            background: #fff;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.3fr .7fr .7fr;
            gap: 40px;
        }

        .footer-copy {
            max-width: 380px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
            margin-top: 14px;
        }

        .footer-col h4 {
            margin: 0 0 13px;
            color: #98a2b3;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 12px;
        }

        .footer-col a {
            display: block;
            margin: 10px 0;
            color: #475467;
            font-size: 13px;
            font-weight: 600;
        }

        .footer-bottom {
            margin-top: 34px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
            color: #98a2b3;
            font-size: 12px;
        }


        .features {
            position: relative;
            overflow: hidden;
        }

        .features::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            left: -220px;
            top: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.07), transparent 68%);
            pointer-events: none;
        }

        .feature-card {
            position: relative;
            overflow: hidden;
        }

        .feature-card::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            right: -45px;
            top: -45px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.08), transparent 70%);
            transition: transform .4s var(--ease);
        }

        .feature-card:hover::after {
            transform: scale(1.4);
        }

        .showcase {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(180deg, #f8fbff 0%, #f5f9ff 100%);
        }

        .showcase::before {
            content: "";
            position: absolute;
            width: 520px;
            height: 520px;
            right: -250px;
            top: -120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.09), transparent 70%);
        }

        .showcase::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(37,99,235,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37,99,235,.025) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: linear-gradient(to bottom, transparent, #000 18%, #000 78%, transparent);
            pointer-events: none;
        }

        .showcase .container {
            position: relative;
            z-index: 1;
        }

        #how-it-works {
            position: relative;
            overflow: hidden;
        }

        #how-it-works::before {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 45%;
            background: linear-gradient(180deg, transparent, rgba(239,246,255,.55));
            pointer-events: none;
        }

        #how-it-works .container {
            position: relative;
            z-index: 1;
        }

        .step {
            border-radius: 18px;
            padding: 26px 18px 22px;
            transition: .35s var(--ease);
        }

        .step:hover {
            background: #ffffff;
            box-shadow: 0 18px 46px rgba(15,23,42,.07);
            transform: translateY(-5px);
        }

        .experience {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 12% 10%, rgba(59,130,246,.13), transparent 26%),
                radial-gradient(circle at 88% 90%, rgba(37,99,235,.12), transparent 25%),
                var(--navy);
        }

        .experience::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }

        .experience .container {
            position: relative;
            z-index: 1;
        }

        .exp-card {
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(0,0,0,.16);
            transition: transform .35s var(--ease), border-color .35s ease, background .35s ease;
        }

        .exp-card:hover {
            transform: translateY(-5px);
            border-color: rgba(122,168,255,.22);
            background: #12203a;
        }

        .cta {
            box-shadow: 0 28px 80px rgba(15,23,42,.16);
            border: 1px solid rgba(255,255,255,.06);
        }


        .features {
            position: relative;
            overflow: hidden;
        }

        .features::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            left: -220px;
            top: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.075), transparent 68%);
            pointer-events: none;
        }

        .feature-card {
            position: relative;
            overflow: hidden;
        }

        .feature-card::after {
            content: "";
            position: absolute;
            width: 110px;
            height: 110px;
            right: -52px;
            top: -52px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.09), transparent 70%);
            transition: transform .4s var(--ease);
        }

        .feature-card:hover::after {
            transform: scale(1.5);
        }

        .showcase {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fbff, #f4f8ff);
        }

        .showcase::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(37,99,235,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37,99,235,.025) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: linear-gradient(to bottom, transparent, #000 18%, #000 80%, transparent);
            pointer-events: none;
        }

        .showcase .container {
            position: relative;
            z-index: 1;
        }

        .dashboard {
            transition: transform .45s var(--ease), box-shadow .45s var(--ease);
        }

        .dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 36px 96px rgba(15,23,42,.14);
        }

        .step {
            border-radius: 18px;
            padding: 26px 18px 22px;
            transition: transform .35s var(--ease), background .35s ease, box-shadow .35s var(--ease);
        }

        .step:hover {
            background: #fff;
            box-shadow: 0 18px 46px rgba(15,23,42,.07);
            transform: translateY(-5px);
        }


        /* FREE SERVICES SECTION */
        .free-section {
            padding-top: 82px;
            padding-bottom: 18px;
        }

        .free-card {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 44px;
            align-items: center;
            padding: 42px;
            border-radius: 26px;
            background:
                radial-gradient(circle at 88% 15%, rgba(59,130,246,.10), transparent 28%),
                linear-gradient(145deg, #ffffff, #f7fbff);
            border: 1px solid #e1e9f4;
            box-shadow: 0 22px 60px rgba(15,23,42,.07);
        }

        .free-points {
            display: grid;
            gap: 12px;
        }

        .free-point {
            padding: 14px 16px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e6edf6;
            color: #344054;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 8px 22px rgba(15,23,42,.04);
        }

        @media (max-width: 820px) {
            .free-card {
                grid-template-columns: 1fr;
                padding: 28px;
                gap: 26px;
            }
        }

        @media (max-width: 520px) {
            .free-card {
                padding: 22px;
                border-radius: 20px;
            }
        }

        /* REVEALS */
        .reveal,
        .reveal-up,
        .reveal-left,
        .reveal-right {
            opacity: 0;
            transition:
                opacity .85s var(--ease),
                transform .85s var(--ease),
                filter .85s var(--ease);
        }

        .reveal,
        .reveal-up {
            transform: translateY(34px);
            filter: blur(4px);
        }

        .reveal-left {
            transform: translateX(-38px);
            filter: blur(4px);
        }

        .reveal-right {
            transform: translateX(38px);
            filter: blur(4px);
        }

        .visible {
            opacity: 1;
            transform: translate(0,0);
            filter: blur(0);
        }

        .hero-animate {
            opacity: 0;
            transform: translateY(20px);
            animation: heroIn .8s var(--ease) forwards;
        }

        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .18s; }
        .delay-3 { animation-delay: .30s; }
        .delay-4 { animation-delay: .42s; }
        .delay-5 { animation-delay: .54s; }

        @keyframes heroIn {
            to {
                opacity: 1;
                transform: none;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 1040px) {
            .hero-grid,
            .showcase-grid {
                grid-template-columns: 1fr;
            }

            .mockup-wrap {
                max-width: 760px;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .feature-card:nth-child(1),
            .feature-card:nth-child(8) {
                grid-column: span 1;
            }

            .experience-grid {
                grid-template-columns: 1fr 1fr;
            }

            .experience-grid .exp-card:first-child {
                grid-column: span 2;
            }
        }

        @media (max-width: 820px) {
            .nav-links,
            .nav-actions {
                display: none;
            }

            .menu-btn {
                display: block;
            }

            .benefit-box {
                grid-template-columns: 1fr 1fr;
            }

            .benefit:nth-child(2) {
                border-right: 0;
            }

            .benefit:nth-child(-n+2) {
                border-bottom: 1px solid var(--line);
            }

            .features-head {
                display: block;
            }

            .steps {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .steps::before {
                display: none;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-grid > div:first-child {
                grid-column: span 2;
            }
        }

        @media (max-width: 620px) {
            .container {
                width: min(100% - 28px, 1180px);
            }

            .section {
                padding: 78px 0;
            }

            .hero {
                padding: 126px 0 72px;
            }

            .hero-grid {
                gap: 42px;
            }

            .hero h1 {
                font-size: clamp(48px, 14vw, 66px);
            }

            .hero p {
                font-size: 16px;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .meeting-body {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .video-grid {
                min-height: 330px;
            }

            .video-tile {
                min-height: 145px;
            }

            .benefit-strip {
                margin-top: -18px;
            }

            .benefit-box {
                grid-template-columns: 1fr;
                padding: 8px 14px;
            }

            .benefit {
                justify-content: flex-start;
                border-right: 0 !important;
                border-bottom: 1px solid var(--line);
            }

            .benefit:last-child {
                border-bottom: 0;
            }

            .feature-grid,
            .experience-grid {
                grid-template-columns: 1fr;
            }

            .experience-grid .exp-card:first-child {
                grid-column: auto;
            }

            .dashboard {
                grid-template-columns: 1fr;
            }

            .dash-side {
                display: none;
            }

            .cta {
                padding: 52px 20px;
                border-radius: 22px;
            }

            .cta .btn {
                width: 100%;
                margin: 5px 0;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid > div:first-child {
                grid-column: auto;
            }
        }


        /* EXTRA RESPONSIVE POLISH */
        @media (max-width: 1180px) {
            .container {
                width: min(100% - 36px, 1120px);
            }

            .hero-grid {
                gap: 44px;
            }

            .hero h1 {
                font-size: clamp(46px, 5.8vw, 64px);
            }
        }

        @media (max-width: 900px) {
            .navbar.scrolled {
                background: rgba(255,255,255,.94);
            }

            .nav-inner {
                height: 72px;
            }

            .hero {
                padding-top: 128px;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            .hero > .container {
                max-width: 820px;
            }

            .hero-copy,
            .hero-grid > div:first-child {
                text-align: center;
            }

            .hero-badge {
                margin-inline: auto;
            }

            .hero p {
                margin-inline: auto;
            }

            .hero-actions,
            .hero-benefits {
                justify-content: center;
            }

            .mockup-wrap {
                margin-inline: auto;
                width: 100%;
            }

            .features-head,
            .showcase-grid {
                gap: 34px;
            }
        }

        @media (max-width: 700px) {
            .brand {
                font-size: 18px;
            }

            .brand-mark {
                width: 36px;
                height: 36px;
                border-radius: 11px;
            }

            .brand-mark img {
                width: 27px;
                height: 27px;
            }

            .hero {
                padding-top: 118px;
            }

            .hero h1 {
                font-size: clamp(42px, 11.5vw, 56px);
                line-height: 1.02;
            }

            .hero h1 span::after {
                bottom: -5px;
                height: 5px;
            }

            .hero p {
                font-size: 15.5px;
                line-height: 1.72;
            }

            .hero-benefits {
                display: grid;
                grid-template-columns: 1fr;
                justify-items: center;
            }

            .meeting-card {
                border-radius: 20px;
            }

            .meeting-top {
                height: 52px;
                padding: 0 12px;
            }

            .meeting-title {
                font-size: 10px;
            }

            .meeting-title img {
                width: 22px;
                height: 22px;
            }

            .video-grid {
                gap: 6px;
                padding: 8px;
            }

            .video-tile {
                border-radius: 11px;
            }

            .avatar {
                width: 48px;
                height: 48px;
                font-size: 15px;
            }

            .person {
                left: 7px;
                bottom: 7px;
                font-size: 9px;
                padding: 4px 6px;
            }

            .meeting-controls {
                height: 60px;
            }

            .control {
                width: 34px;
                height: 34px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .feature-card {
                min-height: auto;
            }

            .showcase-grid {
                grid-template-columns: 1fr;
            }

            .dashboard {
                min-height: 370px;
            }

            .dash-main {
                padding: 14px;
            }

            .stats {
                gap: 7px;
            }

            .stat {
                padding: 10px;
            }

            .table-head,
            .table-row {
                grid-template-columns: 1.4fr .8fr .8fr;
                padding: 9px;
            }

            .cta {
                padding: 44px 18px;
            }

            .cta p {
                font-size: 14px;
            }
        }

        @media (max-width: 430px) {
            .container {
                width: min(100% - 22px, 1180px);
            }

            .nav-inner {
                height: 68px;
            }

            .hero {
                padding: 108px 0 62px;
            }

            .hero-badge {
                font-size: 11px;
                padding: 7px 10px;
            }

            .hero h1 {
                font-size: clamp(40px, 11.8vw, 50px);
                margin-top: 18px;
            }

            .hero-actions {
                gap: 9px;
            }

            .btn {
                min-height: 48px;
                border-radius: 13px;
            }

            .section {
                padding: 68px 0;
            }

            .section-title {
                font-size: clamp(32px, 10vw, 42px);
            }

            .section-copy {
                font-size: 15px;
            }

            .video-grid {
                min-height: 292px;
            }

            .video-tile {
                min-height: 132px;
            }

            .benefit-box {
                border-radius: 16px;
            }

            .dashboard {
                border-radius: 18px;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .stat:last-child {
                grid-column: span 2;
            }

            .table-head {
                display: none;
            }

            .table-row {
                grid-template-columns: 1fr auto;
                gap: 6px 10px;
                padding: 12px;
            }

            .table-row > span:nth-child(2) {
                grid-column: 1;
                font-size: 8px;
                color: #98a2b3;
            }

            .table-row .status {
                grid-column: 2;
                grid-row: 1 / span 2;
                align-self: center;
            }

            .steps {
                gap: 28px;
            }

            .step-num {
                width: 72px;
                height: 72px;
                border-radius: 20px;
            }

            .exp-card {
                min-height: auto;
            }

            .footer-grid {
                gap: 28px;
            }

            .footer-bottom {
                text-align: center;
            }
        }


        @media (max-width: 1180px) {
            .floating-chip.transcript-chip {
                left: -12px;
            }

            .floating-chip.people-chip {
                right: -12px;
            }
        }

        @media (max-width: 900px) {
            .floating-chip.transcript-chip {
                left: 12px;
                bottom: 72px;
            }

            .floating-chip.people-chip {
                right: 12px;
                top: 74px;
            }
        }

        @media (max-width: 620px) {
            .floating-chip {
                display: none;
            }

            .mockup-orbit {
                inset: -16px -8px -18px;
                border-radius: 24px;
            }

            .meeting-secure {
                display: none;
            }
        }


        /* HERO PRODUCT PREVIEW SIZE */
        @media (max-width: 1040px) {
            .mockup-wrap {
                width: 88%;
                max-width: 650px;
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 620px) {
            .mockup-wrap {
                width: 96%;
                max-width: 560px;
                margin-left: auto;
                margin-right: auto;
            }
        }


        @media (max-width: 1100px) {
            .mini-status-card.top-left {
                left: 8px;
                top: 17%;
            }

            .mini-status-card.bottom-right {
                right: 8px;
            }
        }

        @media (max-width: 700px) {
            .mockup-shell {
                padding: 10px;
                border-radius: 24px;
            }

            .mini-status-card {
                display: none;
            }
        }


        @media (max-width: 900px) {
            .hero h1 {
                font-size: clamp(40px, 8vw, 54px);
            }
        }

        @media (max-width: 620px) {
            .brand-mark {
                width: 34px;
                height: 34px;
            }

            .brand-mark img {
                width: 31px;
                height: 31px;
            }

            .hero h1 {
                font-size: clamp(38px, 11vw, 48px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }

            .reveal,
            .reveal-up,
            .reveal-left,
            .reveal-right,
            .hero-animate {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>

<body>

<header class="navbar" id="navbar">
    <div class="container nav-inner">

        <a href="{{ url('/') }}" class="brand">
            <span class="brand-mark">
                <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
            </span>
            <span class="brand-name"><span class="smart">Smart</span><span class="meet">Meet</span></span>
        </a>

        <nav class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#experience">Experience</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn btn-secondary">
                Log In
            </a>

            <a href="{{ route('register') }}" class="btn btn-primary">
                Get Started <span class="btn-arrow">→</span>
            </a>
        </div>

        <button class="menu-btn" id="menuBtn" aria-label="Open menu">
            <span></span>
        </button>

    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="#features">Features</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#experience">Experience</a>
        <a href="{{ route('login') }}">Log In</a>
        <a href="{{ route('register') }}">Sign Up</a>
    </div>
</header>

<main>

    <section class="hero">
        <div class="hero-ambient one"></div>
        <div class="hero-ambient two"></div>

        <div class="container hero-grid">

            <div class="hero-copy">
                <div class="hero-badge hero-animate delay-1">
                    <i></i>
                    Free online meetings & collaboration
                </div>

                <h1 class="hero-animate delay-2">
                    Meet.<br>
                    Connect.<br>
                    <span>Collaborate.</span>
                </h1>

                <p class="hero-animate delay-3">
                    Bring video meetings, scheduling, real-time collaboration and live transcription together in one focused workspace — free to use.
                </p>

                <div class="hero-actions hero-animate delay-4">
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Get Started Free <span class="btn-arrow">→</span>
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-secondary">
                        Log In
                    </a>
                </div>

                <div class="hero-benefits hero-animate delay-5">
                    <span>Free to Use</span>
                    <span>Browser Based</span>
                    <span>Simple Setup</span>
                    <span>Real-Time Collaboration</span>
                </div>
            </div>


            <div class="hero-visual hero-animate delay-4">
                <div class="hero-visual-glow"></div>

                <div class="hero-image-card">
                    <img
                        src="{{ asset('images/smartmeet-hero-meeting.png') }}"
                        alt="SmartMeet online meeting experience"
                        class="hero-meeting-image"
                    >
                </div>

                <div class="hero-feature-card feature-video">
                    <span class="feature-mini-icon">◉</span>
                    <div>
                        <strong>HD Video</strong>
                        <small>Clear online meetings</small>
                    </div>
                </div>

                <div class="hero-feature-card feature-schedule">
                    <span class="feature-mini-icon">⌚</span>
                    <div>
                        <strong>Schedule Meetings</strong>
                        <small>Plan in seconds</small>
                    </div>
                </div>

                <div class="hero-feature-card feature-collab">
                    <span class="feature-mini-icon">👥</span>
                    <div>
                        <strong>Collaborate Together</strong>
                        <small>Stay connected</small>
                    </div>
                </div>

                <div class="hero-feature-card feature-transcript">
                    <span class="feature-mini-icon">✦</span>
                    <div>
                        <strong>Live Transcription</strong>
                        <small>Follow every word</small>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <div class="benefit-strip">

        <div class="container">

            <div class="benefit-box reveal-up">

                <div class="benefit">
                    <div class="benefit-icon">◉</div>
                    Video & Audio
                </div>

                <div class="benefit">
                    <div class="benefit-icon">✦</div>
                    Live Transcription
                </div>

                <div class="benefit">
                    <div class="benefit-icon">💬</div>
                    Real-Time Chat
                </div>

                <div class="benefit">
                    <div class="benefit-icon">⌚</div>
                    Smart Scheduling
                </div>

            </div>

        </div>

    </div>

    <section class="section features" id="features">

        <div class="container">

            <div class="features-head">

                <div class="reveal-left">
                    <div class="eyebrow">Everything you need</div>

                    <h2 class="section-title">
                        One workspace for<br>
                        better meetings.
                    </h2>
                </div>

                <p class="section-copy reveal-right">
                    Keep meetings, conversations and collaboration organized without unnecessary complexity.
                </p>

            </div>

            <div class="feature-grid">

                <article class="feature-card reveal-up">
                    <div class="feature-icon">◉</div>
                    <h3>Video Meetings</h3>
                    <p>Run focused browser-based video meetings with a clean meeting experience.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">🎙</div>
                    <h3>Audio Meetings</h3>
                    <p>Collaborate with audio whenever video is not needed.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">✦</div>
                    <h3>Live Transcription</h3>
                    <p>Follow conversations with live meeting transcription.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">💬</div>
                    <h3>Real-Time Chat</h3>
                    <p>Share quick messages and useful context during meetings.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">⌚</div>
                    <h3>Smart Scheduling</h3>
                    <p>Plan meetings and keep upcoming sessions organized.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">↗</div>
                    <h3>Easy Invitations</h3>
                    <p>Invite participants using simple shareable meeting links.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">👥</div>
                    <h3>Participant Management</h3>
                    <p>Keep track of everyone joining and participating in your meetings.</p>
                </article>

                <article class="feature-card reveal-up">
                    <div class="feature-icon">▦</div>
                    <h3>Meeting Dashboard</h3>
                    <p>Organize upcoming, active and completed meetings from one place.</p>
                </article>

            </div>

        </div>

    </section>

    <section class="section showcase">

        <div class="container showcase-grid">

            <div class="reveal-left">

                <div class="eyebrow">Stay organized</div>

                <h2 class="section-title">
                    Your meetings.<br>
                    One clear dashboard.
                </h2>

                <p class="section-copy">
                    See what’s live, what’s next and what’s already completed without jumping between different tools.
                </p>

                <div class="check-list">
                    <div class="check"><b>✓</b> Upcoming meetings</div>
                    <div class="check"><b>✓</b> Live meeting status</div>
                    <div class="check"><b>✓</b> Participants and schedules</div>
                    <div class="check"><b>✓</b> Organized meeting history</div>
                </div>

            </div>

            <div class="dashboard reveal-right">

                <div class="dash-side">

                    <div class="dash-brand">
                        <img src="{{ asset('images/s-logo.png') }}" alt="">
                        SmartMeet
                    </div>

                    <div class="dash-link active">Dashboard</div>
                    <div class="dash-link">My Meetings</div>
                    <div class="dash-link">Schedule</div>
                    <div class="dash-link">Notifications</div>

                </div>

                <div class="dash-main">

                    <div class="dash-top">
                        <h4>Welcome back 👋</h4>
                        <span>SmartMeet</span>
                    </div>

                    <div class="stats">

                        <div class="stat">
                            <small>Upcoming</small>
                            <strong>6</strong>
                        </div>

                        <div class="stat">
                            <small>Live</small>
                            <strong>1</strong>
                        </div>

                        <div class="stat">
                            <small>Completed</small>
                            <strong>18</strong>
                        </div>

                    </div>

                    <div class="meeting-table">

                        <div class="table-head">
                            <span>Meeting</span>
                            <span>Time</span>
                            <span>Status</span>
                        </div>

                        <div class="table-row">
                            <strong>Project Review</strong>
                            <span>10:30 AM</span>
                            <span class="status active">Active</span>
                        </div>

                        <div class="table-row">
                            <strong>Team Sync</strong>
                            <span>12:00 PM</span>
                            <span class="status upcoming">Upcoming</span>
                        </div>

                        <div class="table-row">
                            <strong>Weekly Planning</strong>
                            <span>03:30 PM</span>
                            <span class="status upcoming">Upcoming</span>
                        </div>

                        <div class="table-row">
                            <strong>Client Discussion</strong>
                            <span>Yesterday</span>
                            <span class="status completed">Completed</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="section" id="how-it-works">

        <div class="container">

            <div class="how-head reveal-up">

                <div class="eyebrow">Simple from the start</div>

                <h2 class="section-title">
                    From schedule to conversation<br>
                    in three steps.
                </h2>

                <p class="section-copy">
                    SmartMeet keeps the process simple so you can focus on the conversation.
                </p>

            </div>

            <div class="steps">

                <article class="step reveal-up">
                    <div class="step-num">01</div>
                    <h3>Schedule</h3>
                    <p>Choose the meeting date and time.</p>
                </article>

                <article class="step reveal-up">
                    <div class="step-num">02</div>
                    <h3>Invite</h3>
                    <p>Share the meeting link with participants.</p>
                </article>

                <article class="step reveal-up">
                    <div class="step-num">03</div>
                    <h3>Meet</h3>
                    <p>Start your meeting and collaborate in real time.</p>
                </article>

            </div>

        </div>

    </section>

    <section class="section experience" id="experience">

        <div class="container">

            <div class="reveal-up">

                <div class="eyebrow">Meeting experience</div>

                <h2 class="section-title">
                    Built around the conversation.
                </h2>

                <p class="section-copy">
                    Chat, participant activity and live transcription stay close to the meeting itself.
                </p>

            </div>

            <div class="experience-grid">

                <article class="exp-card reveal-up">

                    <h3>Live Transcript</h3>

                    <div class="transcript-line">
                        <time>10:31</time>
                        <p><strong>Alex:</strong> Let's review the priorities for this week.</p>
                    </div>

                    <div class="transcript-line">
                        <time>10:32</time>
                        <p><strong>Sarah:</strong> The design updates are ready from my side.</p>
                    </div>

                    <div class="transcript-line">
                        <time>10:33</time>
                        <p><strong>David:</strong> Perfect. I’ll update the schedule.</p>
                    </div>

                </article>

                <article class="exp-card reveal-up">

                    <h3>Meeting Chat</h3>

                    <div class="chat-bubble">
                        Can you share the latest timeline?
                    </div>

                    <div class="chat-bubble me">
                        Yes — posting it here now.
                    </div>

                    <div class="chat-bubble">
                        Got it. Thanks!
                    </div>

                </article>

                <article class="exp-card reveal-up">

                    <h3>Participants</h3>

                    <div class="participant-row">
                        <div class="participant-user"><span>AL</span> Alex Morgan</div>
                        <i class="online"></i>
                    </div>

                    <div class="participant-row">
                        <div class="participant-user"><span>SR</span> Sarah Reed</div>
                        <i class="online"></i>
                    </div>

                    <div class="participant-row">
                        <div class="participant-user"><span>DV</span> David Chen</div>
                        <i class="online"></i>
                    </div>

                    <div class="participant-row">
                        <div class="participant-user"><span>EM</span> Emma Lee</div>
                        <i class="online"></i>
                    </div>

                </article>

            </div>

        </div>

    </section>


    <section class="section free-section" id="free">
        <div class="container">
            <div class="free-card reveal-up">
                <div>
                    <div class="eyebrow">No pricing plans</div>
                    <h2 class="section-title">SmartMeet is free to use.</h2>
                    <p class="section-copy">
                        Use SmartMeet's core meeting features without a subscription plan — schedule meetings,
                        invite participants, collaborate in real time and use live transcription in one workspace.
                    </p>
                </div>

                <div class="free-points">
                    <div class="free-point">✓ Free access</div>
                    <div class="free-point">✓ No paid plan required</div>
                    <div class="free-point">✓ Core collaboration features included</div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-wrap">

        <div class="container">

            <div class="cta reveal-up">

                <h2>
                    Ready to make meetings simpler?
                </h2>

                <p>
                    Start using SmartMeet for free and keep meetings, scheduling and collaboration in one place.
                </p>

                <a href="{{ route('register') }}" class="btn btn-primary">
                    Get Started Free <span class="btn-arrow">→</span>
                </a>

                <a href="{{ route('login') }}" class="btn btn-secondary">
                    Log In
                </a>

            </div>

        </div>

    </section>

</main>

<footer>

    <div class="container">

        <div class="footer-grid">

            <div>

                <a href="{{ url('/') }}" class="brand">
                    <span class="brand-mark">
                        <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
                    </span>
                    <span class="brand-name"><span class="smart">Smart</span><span class="meet">Meet</span></span>
                </a>

                <p class="footer-copy">
                    Simple online meetings and real-time collaboration, designed to keep communication focused and organized.
                </p>

            </div>

            <div class="footer-col">
                <h4>Product</h4>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#experience">Experience</a>
            </div>

            <div class="footer-col">
                <h4>Legal</h4>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('data-deletion') }}">Data Deletion</a>
            </div>

        </div>

        <div class="footer-bottom">
            © {{ date('Y') }} SmartMeet. All rights reserved.
        </div>

    </div>

</footer>

<script>
    (() => {

        const navbar = document.getElementById('navbar');
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        function handleNavbar() {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }

        handleNavbar();

        window.addEventListener('scroll', handleNavbar, {
            passive: true
        });

        if (menuBtn && mobileMenu) {

            menuBtn.addEventListener('click', () => {

                menuBtn.classList.toggle('open');
                mobileMenu.classList.toggle('open');

            });

            mobileMenu.querySelectorAll('a').forEach(link => {

                link.addEventListener('click', () => {

                    menuBtn.classList.remove('open');
                    mobileMenu.classList.remove('open');

                });

            });

        }

        const revealElements = document.querySelectorAll(
            '.reveal, .reveal-up, .reveal-left, .reveal-right'
        );

        const observer = new IntersectionObserver((entries, observerInstance) => {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    entry.target.classList.add('visible');
                    observerInstance.unobserve(entry.target);

                }

            });

        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(element => {
            observer.observe(element);
        });

        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.transitionDelay = `${Math.min(index * 55, 260)}ms`;
        });

        document.querySelectorAll('.step').forEach((step, index) => {
            step.style.transitionDelay = `${index * 120}ms`;
        });

        const sectionLinks = [...document.querySelectorAll('.nav-links a[href^="#"]')];
        const observedSections = sectionLinks
            .map(link => document.querySelector(link.getAttribute('href')))
            .filter(Boolean);

        const activeNavObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    sectionLinks.forEach(link => {
                        link.classList.toggle(
                            'active',
                            link.getAttribute('href') === `#${entry.target.id}`
                        );
                    });
                }
            });
        }, {
            rootMargin: '-35% 0px -55% 0px',
            threshold: 0
        });

        observedSections.forEach(section => activeNavObserver.observe(section));

        const mockup = document.querySelector('.mockup-wrap.reveal-ready');
        if (mockup) {
            const mockupObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('mockup-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.18 });

            mockupObserver.observe(mockup);
        }


        document.querySelectorAll('.exp-card').forEach((card, index) => {
            card.style.transitionDelay = `${index * 110}ms`;
        });

    })();
</script>

</body>
</html>
