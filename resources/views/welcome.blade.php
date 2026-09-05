<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmartMeet — Simple Online Meetings & Collaboration</title>

    <meta name="description"
          content="SmartMeet makes online meetings simple with video, audio, scheduling, real-time chat and live transcription.">

    <link rel="icon" href="{{ asset('images/s-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
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
            font-family: "Inter", sans-serif;
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
            font-family: "Manrope", sans-serif;
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
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: .35s var(--ease);
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
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: "Manrope", sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -.03em;
        }

        .brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
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

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            min-height: 48px;
            padding: 0 20px;
            border-radius: 13px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            transition: .28s var(--ease);
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            box-shadow: 0 16px 30px rgba(37, 99, 235, .28);
        }

        .btn-secondary {
            background: #fff;
            color: var(--navy);
            border-color: var(--line);
        }

        .btn-secondary:hover {
            box-shadow: var(--shadow-soft);
            border-color: #cfd8e7;
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
            display: grid;
            grid-template-columns: .92fr 1.08fr;
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
        }

        .hero h1 {
            font-family: "Manrope", sans-serif;
            font-size: clamp(54px, 6vw, 80px);
            line-height: .97;
            letter-spacing: -.06em;
            color: var(--navy);
            margin: 22px 0;
        }

        .hero h1 span {
            color: var(--blue);
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
        }

        .mockup-glow {
            position: absolute;
            inset: 10% 0 -5%;
            background: radial-gradient(circle, rgba(37,99,235,.20), transparent 65%);
            filter: blur(32px);
            z-index: -1;
        }

        .meeting-card {
            border-radius: 24px;
            border: 1px solid #dfe7f2;
            overflow: hidden;
            background: #fff;
            box-shadow: var(--shadow);
            animation: float 5.5s ease-in-out infinite;
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
        }

        .meeting-body {
            display: grid;
            grid-template-columns: 1fr 180px;
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
            box-shadow: inset 0 0 0 2px rgba(79,140,255,.14);
        }

        .avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(145deg,#eef4ff,#bed4ff);
            color: #1d4ed8;
            font-family: "Manrope", sans-serif;
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
            background: var(--blue-soft);
            color: var(--blue);
            display: grid;
            place-items: center;
            margin-bottom: 26px;
            font-size: 20px;
        }

        .feature-card h3 {
            margin: 0 0 9px;
            font-family: "Manrope", sans-serif;
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
            font-family: "Manrope", sans-serif;
            font-weight: 800;
            font-size: 20px;
            position: relative;
            z-index: 1;
        }

        .step h3 {
            margin: 0 0 8px;
            font-family: "Manrope", sans-serif;
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
            font-family: "Manrope", sans-serif;
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
            font-family: "Manrope", sans-serif;
            font-size: clamp(38px, 5vw, 58px);
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
            background: rgba(255,255,255,.08);
            color: #fff;
            border-color: rgba(255,255,255,.16);
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

        /* REVEALS */
        .reveal,
        .reveal-up,
        .reveal-left,
        .reveal-right {
            opacity: 0;
            transition:
                opacity .75s var(--ease),
                transform .75s var(--ease);
        }

        .reveal,
        .reveal-up {
            transform: translateY(30px);
        }

        .reveal-left {
            transform: translateX(-34px);
        }

        .reveal-right {
            transform: translateX(34px);
        }

        .visible {
            opacity: 1;
            transform: translate(0,0);
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
            <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
            SmartMeet
        </a>

        <nav class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#experience">Experience</a>
        </nav>

        <div class="nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-secondary">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            @else
                @php
                    $dashboardUrl = \Illuminate\Support\Facades\Route::has('organizer.dashboard')
                        ? route('organizer.dashboard')
                        : (\Illuminate\Support\Facades\Route::has('participant.dashboard')
                            ? route('participant.dashboard')
                            : url('/'));
                @endphp

                <a href="{{ $dashboardUrl }}" class="btn btn-primary">Dashboard</a>
            @endguest
        </div>

        <button class="menu-btn" id="menuBtn" aria-label="Open menu">
            <span></span>
        </button>

    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="#features">Features</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#experience">Experience</a>

        @guest
            <a href="{{ route('login') }}">Log In</a>
            <a href="{{ route('register') }}">Get Started</a>
        @endguest
    </div>
</header>

<main>

    <section class="hero">

        <div class="container hero-grid">

            <div>
                <div class="hero-badge hero-animate delay-1">
                    <i></i>
                    Smarter online collaboration
                </div>

                <h1 class="hero-animate delay-2">
                    Meet.<br>
                    Connect.<br>
                    <span>Collaborate.</span>
                </h1>

                <p class="hero-animate delay-3">
                    SmartMeet brings video meetings, real-time chat, scheduling and live transcription together in one simple workspace.
                </p>

                <div class="hero-actions hero-animate delay-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            Get Started Free →
                        </a>
                    @endguest

                    <a href="#features" class="btn btn-secondary">
                        Explore Features
                    </a>
                </div>

                <div class="hero-benefits hero-animate delay-5">
                    <span>Browser Based</span>
                    <span>Simple Setup</span>
                    <span>Real-Time Collaboration</span>
                </div>
            </div>

            <div class="mockup-wrap hero-animate delay-4">

                <div class="mockup-glow"></div>

                <div class="meeting-card">

                    <div class="meeting-top">

                        <div class="meeting-title">
                            <img src="{{ asset('images/s-logo.png') }}" alt="">
                            Weekly Product Sync
                        </div>

                        <div class="live">
                            LIVE
                        </div>

                    </div>

                    <div class="meeting-body">

                        <div class="video-grid">

                            <div class="video-tile active">
                                <div class="avatar">AL</div>
                                <div class="person">Alex · 🎙</div>
                            </div>

                            <div class="video-tile">
                                <div class="avatar">SR</div>
                                <div class="person">Sarah · 🎙</div>
                            </div>

                            <div class="video-tile">
                                <div class="avatar">DV</div>
                                <div class="person">David · 🎙</div>
                            </div>

                            <div class="video-tile">
                                <div class="avatar">EM</div>
                                <div class="person">Emma · 🎙</div>
                            </div>

                        </div>

                        <aside class="sidebar">

                            <div class="tabs">
                                <div class="tab active">Chat</div>
                                <div class="tab">Transcript</div>
                            </div>

                            <div class="chat">

                                <div class="chat-item">
                                    <strong>Sarah</strong>
                                    <div class="bubble">
                                        The new milestone looks good from my side.
                                    </div>
                                </div>

                                <div class="chat-item">
                                    <strong>David</strong>
                                    <div class="bubble">
                                        Great — I’ll update the schedule today.
                                    </div>
                                </div>

                                <div class="transcript">
                                    <strong>Live transcript</strong>
                                    <p>
                                        Let's review the project milestones for this week...
                                    </p>
                                </div>

                            </div>

                        </aside>

                    </div>

                    <div class="meeting-controls">
                        <div class="control">🎙</div>
                        <div class="control">◉</div>
                        <div class="control">👥</div>
                        <div class="control">💬</div>
                        <div class="control leave">Leave</div>
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

    <section class="section" id="features">

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

    <section class="cta-wrap">

        <div class="container">

            <div class="cta reveal-up">

                <h2>
                    Ready to make meetings simpler?
                </h2>

                <p>
                    Bring scheduling, conversations and collaboration together with SmartMeet.
                </p>

                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Get Started Free
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-secondary">
                        Log In
                    </a>
                @else
                    <a href="#features" class="btn btn-primary">
                        Explore SmartMeet
                    </a>
                @endguest

            </div>

        </div>

    </section>

</main>

<footer>

    <div class="container">

        <div class="footer-grid">

            <div>

                <a href="{{ url('/') }}" class="brand">
                    <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
                    SmartMeet
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

    })();
</script>

</body>
</html>
