<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/s-logo.png') }}">

    <title>{{ env('APP_NAME') }} — {{ $meeting->title }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    @vite(['resources/css/meeting-room.css', 'resources/js/app.js'])

    <style>

        .main { display: flex; min-height: 0; }

        .video-area { flex: 1; min-width: 0; position: relative; }

        #side-panel { flex-shrink: 0; }

        .role-badge {

            font-size: 9px; font-weight: 600; letter-spacing: .3px; text-transform: uppercase;

            padding: 1px 6px; border-radius: 99px; margin-left: 6px; vertical-align: middle;

        }

        .role-badge.organizer { background: rgba(251,191,36,0.18); color: #fbbf24; }

        .role-badge.participant { background: rgba(59,130,246,0.18); color: #60a5fa; }

        .participant-online { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); opacity: 1; }

        .participant-offline { background: var(--surface2); border: 1px solid var(--border); opacity: 0.5; }

        .video-placeholder { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden; }

        .video-placeholder video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: inherit; background: #0f172a; }

        .video-placeholder video.mirrored { transform: scaleX(-1); }

        .ctrl-icon.off { opacity: 0.85; }

        /* ── MAXIMIZE / ENLARGE TILE ── */

        .video-tile { position: relative; }

        .tile-expand-btn {

            position: absolute;

            top: 8px;

            right: 8px;

            width: 28px;

            height: 28px;

            border-radius: 8px;

            background: rgba(15,23,42,0.65);

            border: 1px solid rgba(255,255,255,0.15);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            cursor: pointer;

            z-index: 6;

            opacity: 0;

            transition: opacity .2s, background .2s;

        }

        .video-tile:hover .tile-expand-btn,

        .video-tile.maximized .tile-expand-btn { opacity: 1; }

        .tile-expand-btn:hover { background: rgba(59,130,246,0.85); }

        @media (hover: none) { .tile-expand-btn { opacity: 1; } }

        #maximized-overlay {

            position: absolute;

            inset: 0;

            z-index: 30;

            background: #000;

            border-radius: 12px;

            overflow: hidden;

            display: none;

        }

        #maximized-overlay.active { display: block; }

        #maximized-overlay .video-tile { width: 100%; height: 100%; }

        .maximize-close-btn {

            position: absolute;

            top: 12px;

            right: 12px;

            z-index: 10;

            width: 36px;

            height: 36px;

            border-radius: 50%;

            background: rgba(15,23,42,0.75);

            border: 1px solid rgba(255,255,255,0.15);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;

            cursor: pointer;

        }

        .maximize-close-btn:hover { background: rgba(239,68,68,0.85); }

        /* ── RESPONSIVENESS ── */

        *, *::before, *::after { box-sizing: border-box; }

        html, body { max-width: 100%; overflow-x: hidden; }

        .header {

            flex-wrap: wrap;

            row-gap: 6px;

        }

        .header-left {

            min-width: 0;

            flex: 1 1 auto;

            overflow: hidden;

        }

        .header-right {

            flex-shrink: 0;

        }

        .header-center {

            flex-shrink: 0;

        }

        .header-brand {

            flex-shrink: 0;

        }

        .header-meeting-info {

            min-width: 0;

            overflow: hidden;

        }

        .meeting-title { max-width: 40vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .meeting-meta { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Controls bar can scroll horizontally instead of ever clipping a

           button off-screen — the ultimate safety net on very narrow

           devices, on top of the icon-shrinking rules below. */

        .controls {

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;

            scrollbar-width: none;

        }

        .controls::-webkit-scrollbar { display: none; }

        @media (max-width: 1200px) {

            .video-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important; }

        }

        @media (max-width: 900px) {

            .header { flex-wrap: wrap; gap: 8px; padding: 8px 12px; }

            .header-center { order: 3; width: 100%; justify-content: center; }

            .main { flex-direction: column; }

            #side-panel {

                position: fixed; inset: 0; top: auto; bottom: 86px; height: 60vh; width: 100%;

                z-index: 50; border-radius: 16px 16px 0 0; box-shadow: 0 -4px 24px rgba(0,0,0,0.4);

            }

            .video-grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)) !important; }

            .controls { flex-wrap: nowrap; justify-content: flex-start; gap: 8px; padding: 10px; }

            .tile-expand-btn { width: 24px; height: 24px; font-size: 11px; }

            .maximize-close-btn { width: 32px; height: 32px; top: 8px; right: 8px; }

            .meeting-title { max-width: 45vw; }

        }

        @media (max-width: 768px) {

            .header-brand { gap: 6px !important; padding-right: 8px !important; }

            .header-brand-text { display: none; }

            .header-brand img { width: 26px !important; height: 26px !important; }

            .meeting-meta span:nth-child(3) { display: none; } /* hide timezone text */

            .live-badge { font-size: 10px; padding: 3px 8px; }

        }

        @media (max-width: 640px) {

            .header { padding: 8px 10px; gap: 6px; }

            .participants-count { display: none; }

            .header-right { gap: 6px; }

            .btn-leave { padding: 6px 10px !important; font-size: 12px !important; }

            .meeting-title { max-width: 50vw; font-size: 13px; }

        }

        @media (max-width: 480px) {

            .meeting-title { font-size: 13px; max-width: 42vw; }

            .video-grid { grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)) !important; }

            .header-left { gap: 6px; }

            .header-brand { display: none; } /* free up space, keep LIVE badge + title */

            .participants-count { display: none; }

            .tile-expand-btn { top: 4px; right: 4px; }

            .btn-leave span { display: none; }

            .btn-leave {

                padding: 0 !important;

                width: 34px; height: 34px;

                border-radius: 50% !important;

                display: flex; align-items: center; justify-content: center;

            }

        }

        @media (max-width: 360px) {

            .video-grid { grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)) !important; }

            .ctrl-label { display: none; }

            .controls { gap: 6px; padding: 8px 6px; }

            .header { padding: 6px 8px; }

            .meeting-title { max-width: 34vw; font-size: 12px; }

            .live-badge span, .live-badge { font-size: 9px; padding: 2px 6px; }

        }

        /* Short/landscape screens — icons were getting squeezed vertically */

        @media (max-height: 480px) and (orientation: landscape) {

            .header { padding: 4px 10px; }

            .controls { padding: 6px 10px; }

            #side-panel { height: 80vh; }

            .video-grid { grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important; }

        }

        /* ── MODERN TOAST NOTIFICATIONS (matches organizer view) ── */

        #toast-stack {

            position: fixed;

            bottom: 100px;

            left: 50%;

            transform: translateX(-50%);

            z-index: 999;

            display: flex;

            flex-direction: column;

            align-items: center;

            gap: 8px;

            pointer-events: none;

        }

        .toast {

            pointer-events: auto;

            display: flex;

            align-items: center;

            gap: 10px;

            background: rgba(15, 23, 42, 0.92);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            border: 1px solid rgba(255,255,255,0.10);

            color: #fff;

            padding: 11px 18px;

            border-radius: 14px;

            font-size: 13px;

            font-weight: 500;

            line-height: 1.4;

            box-shadow: 0 10px 30px rgba(0,0,0,0.35);

            opacity: 0;

            transform: translateY(16px) scale(0.98);

            transition: opacity .25s ease, transform .25s ease;

            max-width: min(90vw, 420px);

        }

        .toast.show { opacity: 1; transform: translateY(0) scale(1); }

        .toast.leaving { opacity: 0; transform: translateY(-6px) scale(0.98); }


        /* ═══════════════════════════════════════════════════════════
           COMPACT MODERN MEETING ROOM — SHARED ORGANIZER/PARTICIPANT UI
           ═══════════════════════════════════════════════════════════ */
        :root {
            --room-bg: #07111f;
            --room-surface: rgba(15, 23, 42, .86);
            --room-surface-2: rgba(30, 41, 59, .78);
            --room-line: rgba(148, 163, 184, .14);
            --room-text: #f8fafc;
            --room-muted: #94a3b8;
            --room-primary: #3b82f6;
            --room-danger: #ef4444;
        }

        html, body {
            height: 100%;
            background:
                radial-gradient(circle at 14% 12%, rgba(59,130,246,.15), transparent 25%),
                radial-gradient(circle at 88% 82%, rgba(139,92,246,.12), transparent 28%),
                var(--room-bg);
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100dvh;
            color: var(--room-text);
        }

        .header {
            min-height: 58px !important;
            padding: 7px 14px !important;
            gap: 10px !important;
            background: rgba(7,17,31,.88) !important;
            border-bottom: 1px solid var(--room-line) !important;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            z-index: 60;
        }

        .header-left { gap: 9px !important; }
        .header-brand { gap: 8px !important; padding-right: 12px !important; }
        .header-brand img { width: 27px !important; height: 27px !important; }
        .header-brand-text > div:first-child { font-size: 12px !important; }
        .header-brand-text > div:last-child { font-size: 9px !important; }

        .live-badge {
            padding: 3px 8px !important;
            border-radius: 999px !important;
            font-size: 9px !important;
            letter-spacing: .55px;
            box-shadow: 0 0 0 1px rgba(239,68,68,.12);
        }

        .meeting-title {
            font-size: 13px !important;
            font-weight: 650 !important;
            letter-spacing: -.1px;
        }

        .meeting-meta { font-size: 9px !important; margin-top: 1px !important; }
        .header-center {
            min-width: 108px;
            padding: 5px 10px !important;
            border: 1px solid var(--room-line);
            border-radius: 10px;
            background: rgba(15,23,42,.62);
            font-size: 12px !important;
            gap: 6px !important;
        }

        .timer-icon { font-size: 10px !important; }
        .participants-count {
            padding: 5px 9px !important;
            border-radius: 9px !important;
            font-size: 10px !important;
            background: rgba(15,23,42,.58) !important;
            border: 1px solid var(--room-line);
        }

        .btn-leave {
            min-height: 31px !important;
            padding: 5px 10px !important;
            border-radius: 9px !important;
            font-size: 10px !important;
            gap: 5px !important;
            box-shadow: none !important;
        }

        .main {
            flex: 1 1 auto;
            min-height: 0;
            padding: 10px;
            gap: 10px;
            overflow: hidden;
        }

        .video-area {
            border: 1px solid var(--room-line);
            border-radius: 16px;
            background: rgba(2,6,23,.42);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.025), 0 18px 55px rgba(0,0,0,.18);
            overflow: hidden;
        }

        .video-grid {
            height: 100%;
            padding: 10px !important;
            gap: 10px !important;
            align-content: center;
            grid-auto-rows: minmax(165px, 1fr);
        }

        .video-tile {
            min-height: 165px;
            border-radius: 14px !important;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,.14) !important;
            background: linear-gradient(145deg, rgba(30,41,59,.88), rgba(15,23,42,.94)) !important;
            box-shadow: 0 12px 28px rgba(0,0,0,.22);
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .video-tile:hover {
            transform: translateY(-1px);
            border-color: rgba(96,165,250,.35) !important;
            box-shadow: 0 16px 34px rgba(0,0,0,.28);
        }

        .video-placeholder { background: radial-gradient(circle at center, rgba(59,130,246,.08), transparent 55%); }
        .avatar-circle.lg { width: 78px !important; height: 78px !important; font-size: 25px !important; }
        .avatar-circle:not(.lg) { width: 62px !important; height: 62px !important; font-size: 20px !important; }

        .tile-info {
            min-height: 36px !important;
            padding: 7px 9px !important;
            background: linear-gradient(to top, rgba(2,6,23,.94), rgba(2,6,23,.62)) !important;
        }

        .tile-name { font-size: 11px !important; gap: 3px !important; }
        .role-badge { font-size: 7px !important; padding: 1px 5px !important; margin-left: 3px !important; }
        .you-badge { top: 7px !important; left: 7px !important; font-size: 8px !important; padding: 2px 6px !important; }
        .tile-expand-btn { width: 25px !important; height: 25px !important; border-radius: 7px !important; font-size: 10px !important; }
        .mic-off { width: 24px !important; height: 24px !important; font-size: 10px !important; }

        #side-panel {
            width: min(325px, 30vw) !important;
            min-width: 280px;
            height: 100%;
            border: 1px solid var(--room-line) !important;
            border-radius: 16px !important;
            background: rgba(15,23,42,.88) !important;
            box-shadow: 0 18px 50px rgba(0,0,0,.28);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            overflow: hidden;
        }

        .transcript-body, .chat-body { padding: 10px !important; }
        .transcript-entry { padding: 8px !important; border-radius: 11px !important; gap: 8px !important; }
        .transcript-avatar { width: 29px !important; height: 29px !important; font-size: 9px !important; }
        .transcript-name { font-size: 10px !important; }
        .transcript-time { font-size: 8px !important; }
        .transcript-text { font-size: 11px !important; line-height: 1.45 !important; }
        .chat-input-area { padding: 9px !important; gap: 7px !important; }
        .chat-input { min-height: 35px !important; padding: 7px 10px !important; font-size: 11px !important; border-radius: 10px !important; }
        .btn-send { width: 35px !important; height: 35px !important; border-radius: 10px !important; font-size: 11px !important; }

        #participants-list > div,
        #other-participants-panel > div {
            transition: background .18s ease, opacity .18s ease, border-color .18s ease;
        }

        .controls {
            flex: 0 0 auto;
            min-height: 64px !important;
            padding: 7px 12px !important;
            gap: 7px !important;
            background: rgba(7,17,31,.92) !important;
            border-top: 1px solid var(--room-line) !important;
            box-shadow: 0 -10px 35px rgba(0,0,0,.2);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            justify-content: center;
            z-index: 70;
        }

        .ctrl-btn {
            min-width: 47px !important;
            gap: 3px !important;
            padding: 2px 4px !important;
            border-radius: 10px;
        }

        .ctrl-btn:hover { background: rgba(148,163,184,.07); }

        .ctrl-icon,
        .btn-end {
            width: 34px !important;
            height: 34px !important;
            min-width: 34px !important;
            min-height: 34px !important;
            border-radius: 10px !important;
            font-size: 12px !important;
            box-shadow: none !important;
        }

        .ctrl-icon {
            background: rgba(30,41,59,.72) !important;
            border: 1px solid rgba(148,163,184,.13) !important;
        }

        .ctrl-icon.active { background: rgba(59,130,246,.22) !important; border-color: rgba(96,165,250,.45) !important; }
        .ctrl-icon.off { background: rgba(51,65,85,.72) !important; }
        .ctrl-label { font-size: 8px !important; line-height: 1 !important; }
        .ctrl-divider { height: 28px !important; margin: 0 1px !important; opacity: .45; }

        #toast-stack { bottom: 78px !important; }
        .toast { padding: 9px 13px !important; border-radius: 11px !important; font-size: 11px !important; }

        @media (max-width: 900px) {
            .main { padding: 7px; }
            #side-panel { bottom: 70px !important; height: min(62vh, 520px) !important; width: 100% !important; min-width: 0; border-radius: 16px 16px 0 0 !important; }
            .controls { min-height: 59px !important; padding: 6px 8px !important; justify-content: flex-start; }
            .ctrl-icon, .btn-end { width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; }
            .ctrl-btn { min-width: 43px !important; }
            .video-grid { grid-auto-rows: minmax(145px, 1fr); padding: 7px !important; gap: 7px !important; }
            .video-tile { min-height: 145px; }
        }

        @media (max-width: 640px) {
            .header { min-height: 52px !important; padding: 6px 9px !important; }
            .header-center { min-width: 88px; padding: 4px 7px !important; font-size: 10px !important; }
            .meeting-meta { display: none; }
            .main { padding: 5px; }
            .video-area { border-radius: 12px; }
            .video-grid { grid-auto-rows: minmax(120px, 1fr); grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)) !important; }
            .video-tile { min-height: 120px; border-radius: 11px !important; }
            .avatar-circle.lg { width: 60px !important; height: 60px !important; font-size: 20px !important; }
            .avatar-circle:not(.lg) { width: 52px !important; height: 52px !important; font-size: 17px !important; }
            .tile-info { min-height: 31px !important; padding: 5px 7px !important; }
            .tile-name { font-size: 9px !important; }
            .role-badge { display: none; }
            .controls { min-height: 56px !important; gap: 4px !important; }
            .ctrl-label { font-size: 7px !important; }
        }



        /* ═══════════════════════════════════════════════════════════
           PREMIUM RESPONSIVE ROOM — compact controls + modern tiles
           ═══════════════════════════════════════════════════════════ */
        :root {
            --room-bg: #050816;
            --room-panel: rgba(15, 23, 42, .82);
            --room-card: rgba(15, 23, 42, .68);
            --room-card-border: rgba(148, 163, 184, .16);
            --room-accent: #7c3aed;
            --room-accent-2: #06b6d4;
        }
        html, body { height: 100%; }
        body {
            min-height: 100dvh;
            background:
                radial-gradient(circle at 12% 8%, rgba(124,58,237,.18), transparent 31%),
                radial-gradient(circle at 88% 18%, rgba(6,182,212,.13), transparent 27%),
                linear-gradient(145deg, #040712 0%, #07111f 48%, #050816 100%) !important;
        }
        body::before {
            content: ""; position: fixed; inset: 0; pointer-events: none; z-index: -1;
            background-image: linear-gradient(rgba(255,255,255,.018) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.018) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.55), transparent 80%);
        }
        .header {
            min-height: 54px !important; height: auto !important; padding: 7px 14px !important;
            background: rgba(5,8,22,.82) !important; border-bottom: 1px solid rgba(148,163,184,.12) !important;
            backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
            box-shadow: 0 10px 35px rgba(0,0,0,.16);
        }
        .header-brand img { width: 27px !important; height: 27px !important; }
        .header-brand-text > div:first-child { font-size: 12px !important; }
        .header-brand-text > div:last-child { font-size: 8px !important; }
        .live-badge { font-size: 9px !important; padding: 3px 8px !important; border-radius: 999px !important; }
        .meeting-title { font-size: 13px !important; font-weight: 700 !important; }
        .meeting-meta { font-size: 9px !important; }
        .header-center { font-size: 12px !important; padding: 5px 10px !important; border-radius: 10px !important; background: rgba(255,255,255,.04); }
        .participants-count { font-size: 10px !important; }
        .btn-leave { min-height: 31px !important; padding: 6px 11px !important; font-size: 10px !important; border-radius: 10px !important; }
        .main { height: calc(100dvh - 118px) !important; padding: 10px !important; gap: 10px !important; }
        .video-area {
            border: 1px solid rgba(148,163,184,.10); border-radius: 22px; overflow: hidden;
            background: linear-gradient(145deg, rgba(15,23,42,.46), rgba(2,6,23,.72));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03), 0 18px 50px rgba(0,0,0,.22);
        }
        .video-grid {
            height: 100%; padding: 12px !important; gap: 12px !important;
            grid-template-columns: repeat(auto-fit, minmax(min(250px, 100%), 1fr)) !important;
            grid-auto-rows: minmax(180px, 1fr) !important; align-content: center;
        }
        .video-tile {
            min-height: 180px; border-radius: 20px !important; overflow: hidden;
            background: linear-gradient(145deg, rgba(30,41,59,.74), rgba(10,15,30,.92)) !important;
            border: 1px solid var(--room-card-border) !important;
            box-shadow: 0 16px 45px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.045);
            transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
        }
        .video-tile:hover { transform: translateY(-2px); border-color: rgba(129,140,248,.42) !important; box-shadow: 0 21px 52px rgba(0,0,0,.32), 0 0 0 1px rgba(124,58,237,.08); }
        .video-placeholder { background: radial-gradient(circle at 50% 35%, rgba(124,58,237,.14), transparent 45%); }
        .avatar-circle, .avatar-circle.lg { box-shadow: 0 14px 38px rgba(0,0,0,.28), 0 0 0 6px rgba(255,255,255,.045); }
        .tile-info {
            min-height: 42px !important; padding: 8px 10px !important;
            background: linear-gradient(to top, rgba(2,6,23,.94), rgba(2,6,23,.70)) !important;
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        }
        .tile-name { font-size: 11px !important; }
        .role-badge { font-size: 7px !important; padding: 2px 5px !important; }
        .tile-expand-btn { width: 25px !important; height: 25px !important; border-radius: 9px !important; font-size: 10px !important; }
        #side-panel {
            width: min(330px, 33vw) !important; border-radius: 20px !important; overflow: hidden;
            background: var(--room-panel) !important; border: 1px solid rgba(148,163,184,.14) !important;
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }
        .transcript-entry { border-radius: 13px; padding: 9px !important; margin-bottom: 7px; background: rgba(255,255,255,.025); }
        .chat-input { min-height: 36px !important; border-radius: 11px !important; font-size: 11px !important; }
        .btn-send { width: 34px !important; height: 34px !important; border-radius: 11px !important; }
        .controls {
            min-height: 64px !important; height: 64px !important; padding: 6px 10px !important; gap: 5px !important;
            background: rgba(5,8,22,.88) !important; border-top: 1px solid rgba(148,163,184,.12) !important;
            backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
        }
        .ctrl-btn { min-width: 48px !important; gap: 3px !important; }
        .ctrl-icon, .btn-end {
            width: 34px !important; height: 34px !important; min-width: 34px !important;
            border-radius: 11px !important; font-size: 12px !important;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.05);
        }
        .ctrl-icon:hover, .btn-end:hover { transform: translateY(-1px); }
        .ctrl-label { font-size: 8px !important; line-height: 1 !important; }
        .ctrl-divider { height: 28px !important; margin: 0 3px !important; }
        #listening-indicator { font-size: 10px !important; padding: 6px 10px !important; }
        #lang-toggle-btn { min-height: 29px; }
        @media (max-width: 900px) {
            .main { height: calc(100dvh - 112px) !important; padding: 7px !important; }
            #side-panel { width: 100% !important; max-width: none !important; height: min(64dvh, 580px) !important; bottom: 64px !important; border-radius: 20px 20px 0 0 !important; }
            .video-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; grid-auto-rows: minmax(145px, 1fr) !important; padding: 8px !important; gap: 8px !important; }
            .video-tile { min-height: 145px; border-radius: 16px !important; }
            .header-center { order: initial !important; width: auto !important; }
        }
        @media (max-width: 640px) {
            .header { min-height: 49px !important; padding: 6px 8px !important; }
            .header-brand, .participants-count { display: none !important; }
            .meeting-meta { display: none !important; }
            .meeting-title { max-width: 42vw !important; font-size: 11px !important; }
            .header-center { font-size: 10px !important; padding: 4px 7px !important; }
            .main { height: calc(100dvh - 107px) !important; padding: 5px !important; }
            .video-area { border-radius: 15px; }
            .video-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; grid-auto-rows: minmax(120px, 1fr) !important; padding: 5px !important; gap: 5px !important; }
            .video-tile { min-height: 120px; border-radius: 13px !important; }
            .avatar-circle.lg, .avatar-circle { width: 54px !important; height: 54px !important; font-size: 18px !important; }
            .tile-info { min-height: 35px !important; padding: 6px 7px !important; }
            .tile-name { max-width: calc(100% - 30px); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: 9px !important; }
            .role-badge { display: none; }
            .controls { height: 58px !important; min-height: 58px !important; justify-content: flex-start !important; padding: 5px 6px !important; gap: 2px !important; }
            .ctrl-btn { min-width: 43px !important; }
            .ctrl-icon, .btn-end { width: 31px !important; height: 31px !important; min-width: 31px !important; border-radius: 10px !important; font-size: 11px !important; }
            .ctrl-label { font-size: 7px !important; }
        }
        @media (max-width: 390px) {
            .video-grid { grid-template-columns: 1fr !important; grid-auto-rows: minmax(128px, 1fr) !important; overflow-y: auto; }
            .video-tile { min-height: 128px; max-height: 42dvh; }
            .ctrl-divider { display: none; }
        }
        @media (max-height: 520px) and (orientation: landscape) {
            .header { min-height: 42px !important; padding: 4px 8px !important; }
            .main { height: calc(100dvh - 92px) !important; }
            .controls { height: 50px !important; min-height: 50px !important; }
            .ctrl-label { display: none !important; }
            .video-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important; grid-auto-rows: minmax(100px, 1fr) !important; }
        }



        /* FINAL UI, CHAT, MOBILE SPACING AND ACCESSIBILITY OVERRIDES */
        .video-area {
            background: linear-gradient(145deg, rgba(8,15,31,.88), rgba(9,20,38,.72)) !important;
            border: 1px solid rgba(125,211,252,.15) !important;
            box-shadow: 0 24px 70px rgba(0,0,0,.32), inset 0 1px 0 rgba(255,255,255,.04) !important;
        }
        .video-grid {
            padding: 16px !important;
            gap: 16px !important;
            align-content: start !important;
        }
        .video-tile {
            border-radius: 20px !important;
            border: 1px solid rgba(148,163,184,.18) !important;
            background: linear-gradient(155deg, rgba(24,38,64,.95), rgba(7,15,30,.98)) !important;
            box-shadow: 0 18px 45px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.04) !important;
        }
        .video-tile::after {
            content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: inherit;
            background: linear-gradient(135deg, rgba(56,189,248,.07), transparent 38%, rgba(139,92,246,.06));
        }
        .tile-info { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .controls {
            margin: 0 12px 12px !important;
            border: 1px solid rgba(148,163,184,.14) !important;
            border-radius: 18px !important;
            background: rgba(5,12,26,.88) !important;
        }
        .ctrl-icon, .btn-end {
            border-radius: 12px !important;
            transition: transform .16s ease, background .16s ease, border-color .16s ease !important;
        }
        .ctrl-btn:hover .ctrl-icon, .btn-end:hover { transform: translateY(-2px); }
        #side-panel {
            border-radius: 20px !important;
            border-color: rgba(125,211,252,.16) !important;
            background: linear-gradient(180deg, rgba(13,24,44,.96), rgba(7,14,29,.96)) !important;
        }
        .chat-body {
            display: flex !important;
            flex-direction: column;
            gap: 12px;
            padding: 14px !important;
            background: radial-gradient(circle at 90% 0, rgba(59,130,246,.07), transparent 35%);
        }
        .chat-message-row { display: flex; align-items: flex-end; gap: 9px; width: 100%; }
        .chat-message-row.is-me { flex-direction: row-reverse; }
        .chat-message-avatar {
            width: 32px; height: 32px; border-radius: 11px; flex: 0 0 32px;
            display: grid; place-items: center; color: #fff; font-size: 10px; font-weight: 800;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            box-shadow: 0 7px 18px rgba(0,0,0,.24);
        }
        .chat-message-row.is-me .chat-message-avatar { background: linear-gradient(135deg, #2563eb, #06b6d4); }
        .chat-message-content { max-width: min(82%, 290px); min-width: 0; }
        .chat-message-meta { display: flex; gap: 8px; align-items: center; margin: 0 5px 5px; color: #94a3b8; font-size: 9px; }
        .chat-message-meta strong { color: #e2e8f0; font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-message-meta span { margin-left: auto; flex-shrink: 0; }
        .chat-message-row.is-me .chat-message-meta { flex-direction: row-reverse; }
        .chat-message-row.is-me .chat-message-meta span { margin-left: 0; margin-right: auto; }
        .chat-message-bubble {
            padding: 10px 12px; border-radius: 5px 16px 16px 16px;
            background: rgba(30,41,59,.88); border: 1px solid rgba(148,163,184,.14);
            color: #f8fafc; font-size: 12px; line-height: 1.5; word-break: break-word;
        }
        .chat-message-row.is-me .chat-message-bubble {
            border-radius: 16px 5px 16px 16px;
            background: linear-gradient(135deg, #2563eb, #0891b2); border-color: rgba(125,211,252,.28);
        }
        .chat-input-area {
            padding: 12px !important; background: rgba(2,6,23,.56); border-top: 1px solid rgba(148,163,184,.12);
        }
        .chat-input {
            min-height: 40px !important; border-radius: 13px !important;
            background: rgba(15,23,42,.9) !important; border: 1px solid rgba(148,163,184,.18) !important;
        }
        .chat-input:focus { border-color: rgba(56,189,248,.65) !important; box-shadow: 0 0 0 3px rgba(56,189,248,.09); }
        .btn-send { min-width: 40px !important; width: 40px !important; height: 40px !important; border-radius: 13px !important; }

        @media (max-width: 900px) {
            .main { padding: 10px !important; gap: 10px !important; }
            .video-grid { padding: 12px !important; gap: 12px !important; }
            .controls { margin: 0 8px 8px !important; }
        }
        @media (max-width: 640px) {
            .main { padding: 8px !important; }
            .video-area { border-radius: 16px !important; }
            .video-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                grid-auto-rows: minmax(210px, auto) !important;
                padding: 12px !important;
                gap: 14px !important;
                overflow-y: auto !important;
                align-content: start !important;
            }
            .video-tile {
                width: 100% !important;
                min-height: 210px !important;
                margin: 0 !important;
                border-radius: 17px !important;
            }
            #side-panel { left: 8px !important; right: 8px !important; width: auto !important; bottom: 72px !important; }
            .controls { margin: 0 6px 6px !important; padding: 7px 6px !important; border-radius: 16px !important; }
            .chat-message-content { max-width: 78%; }
        }
        @media (max-width: 390px) {
            .video-grid { padding: 10px !important; gap: 12px !important; grid-auto-rows: minmax(190px, auto) !important; }
            .video-tile { min-height: 190px !important; }
            .ctrl-btn { min-width: 42px !important; }
            .ctrl-icon, .btn-end { width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; }
        }

        /* ============================================================
           SMARTMEET FINAL ROOM LAYOUT OVERRIDE
           Keeps 1–2 users compact and scales cleanly for larger rooms.
        ============================================================ */
        .video-grid {
            align-content: center !important;
            justify-content: center !important;
            grid-auto-rows: minmax(210px, min(42vh, 430px)) !important;
        }
        .video-grid:has(> .video-tile:only-child) {
            grid-template-columns: minmax(280px, min(760px, 82%)) !important;
        }
        .video-grid:has(> .video-tile:first-child:nth-last-child(2)) {
            grid-template-columns: repeat(2, minmax(260px, 520px)) !important;
        }
        .video-tile {
            width: 100%;
            max-height: 460px;
            aspect-ratio: 16 / 9;
            min-height: 0 !important;
        }
        .video-placeholder { min-height: 0; }
        .video-placeholder video { object-fit: cover !important; }
        .participant-online, .participant-offline {
            border-radius: 14px !important;
        }
        @media (max-width: 760px) {
            .video-grid,
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)) {
                grid-template-columns: 1fr !important;
                grid-auto-rows: auto !important;
                align-content: start !important;
            }
            .video-tile {
                aspect-ratio: 16 / 10;
                max-height: none;
            }
        }


        /* ===== SmartMeet final compact professional grid ===== */
        .video-grid{
            display:grid !important;
            grid-template-columns:repeat(auto-fill,minmax(280px,360px)) !important;
            grid-auto-rows:auto !important;
            justify-content:start !important;
            align-content:start !important;
            align-items:start !important;
            gap:14px !important;
            padding:18px !important;
        }
        .video-grid .video-tile{
            width:100% !important;
            max-width:360px !important;
            height:auto !important;
            min-height:0 !important;
            aspect-ratio:16/10 !important;
            border-radius:18px !important;
            overflow:hidden !important;
            box-shadow:0 10px 30px rgba(15,23,42,.12) !important;
        }
        .video-grid .video-placeholder,
        .video-grid video{
            width:100% !important;
            height:100% !important;
        }
        .video-grid video{object-fit:cover !important}
        @media(max-width:700px){
            .video-grid{
                grid-template-columns:minmax(0,1fr) !important;
                padding:12px !important;
            }
            .video-grid .video-tile{
                max-width:100% !important;
                aspect-ratio:16/10 !important;
            }
        }
        /* People: joined users crisp, invited/not-present users intentionally muted */
        .participant-online{opacity:1 !important;filter:none !important}
        .participant-offline{
            opacity:.56 !important;
            filter:saturate(.35) !important;
            background:rgba(148,163,184,.08) !important;
        }



        /* ============================================================
           SMARTMEET FINAL PROFESSIONAL ROOM LAYOUT
           Larger compact tiles, top-left alignment, no full-width stretching.
        ============================================================ */
        .video-area{
            padding:0 !important;
        }
        .video-grid{
            display:grid !important;
            grid-template-columns:repeat(auto-fill,minmax(360px,470px)) !important;
            grid-auto-rows:auto !important;
            justify-content:start !important;
            align-content:start !important;
            align-items:start !important;
            gap:16px !important;
            padding:22px !important;
        }
        .video-grid .video-tile{
            width:100% !important;
            max-width:470px !important;
            min-width:0 !important;
            min-height:0 !important;
            height:auto !important;
            aspect-ratio:16/10 !important;
            border-radius:20px !important;
            overflow:hidden !important;
            border:1px solid rgba(148,163,184,.20) !important;
            background:linear-gradient(180deg,rgba(15,23,42,.98),rgba(2,6,23,.98)) !important;
            box-shadow:0 14px 38px rgba(0,0,0,.22) !important;
            transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease !important;
        }
        .video-grid .video-tile:hover{
            transform:translateY(-2px);
            border-color:rgba(59,130,246,.45) !important;
            box-shadow:0 18px 44px rgba(0,0,0,.30) !important;
        }
        .video-grid .video-placeholder{
            width:100% !important;
            height:calc(100% - 48px) !important;
            min-height:0 !important;
        }
        .video-grid .video-placeholder video{
            width:100% !important;
            height:100% !important;
            object-fit:cover !important;
        }
        .video-grid .tile-info{
            min-height:48px !important;
            padding:10px 13px !important;
            backdrop-filter:blur(10px);
        }
        .video-grid .avatar-circle{
            width:76px !important;
            height:76px !important;
            font-size:24px !important;
            box-shadow:0 10px 30px rgba(0,0,0,.22);
        }
        .participant-online{
            opacity:1 !important;
            filter:none !important;
        }
        .participant-offline{
            opacity:.46 !important;
            filter:saturate(.30) blur(.15px) !important;
        }
        #side-panel{
            box-shadow:0 18px 55px rgba(0,0,0,.30) !important;
        }
        .controls{
            box-shadow:0 -8px 30px rgba(0,0,0,.12) !important;
        }
        .ctrl-icon{
            transition:transform .15s ease,background .15s ease !important;
        }
        .ctrl-icon:hover{
            transform:translateY(-2px) !important;
        }
        @media(max-width:1050px){
            .video-grid{
                grid-template-columns:repeat(auto-fill,minmax(320px,430px)) !important;
            }
            .video-grid .video-tile{
                max-width:430px !important;
            }
        }
        @media(max-width:720px){
            .video-grid{
                grid-template-columns:1fr !important;
                gap:12px !important;
                padding:12px !important;
            }
            .video-grid .video-tile{
                max-width:100% !important;
                aspect-ratio:16/10 !important;
            }
        }


        #listening-indicator{
            border-radius:12px !important;
            background:rgba(34,197,94,.10) !important;
            border:1px solid rgba(34,197,94,.20) !important;
            margin:8px 10px !important;
        }


        /* ============================================================
           SMARTMEET FINAL RELIABLE ROOM UI
           Layout/chat only. WebRTC tracks, SDP, ICE and TURN are untouched.
           ============================================================ */

        .video-grid {
            align-content: start !important;
            grid-auto-rows: auto !important;
        }

        /* 1 visible tile: wide, not excessively tall */
        .video-grid:has(> .video-tile:first-child:last-child) {
            grid-template-columns: minmax(0, min(920px, 92%)) !important;
            justify-content: start !important;
            align-content: start !important;
        }

        .video-grid:has(> .video-tile:first-child:last-child) > .video-tile {
            width: 100% !important;
            max-width: 920px !important;
            aspect-ratio: 16 / 9 !important;
            height: auto !important;
            min-height: 0 !important;
        }

        /* 2 visible tiles: use the width, keep a cinematic ratio */
        .video-grid:has(> .video-tile:nth-child(2):last-child) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px !important;
            align-content: start !important;
        }

        .video-grid:has(> .video-tile:nth-child(2):last-child) > .video-tile {
            width: 100% !important;
            aspect-ratio: 16 / 9 !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: 430px !important;
        }

        /* maximized tile fills stage */
        #maximized-overlay.active {
            display: block !important;
        }

        #maximized-overlay.active > .video-tile {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            min-height: 0 !important;
            aspect-ratio: auto !important;
            margin: 0 !important;
            transform: none !important;
        }

        #maximized-overlay.active > .video-tile .video-placeholder {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }

        #maximized-overlay.active > .video-tile video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
        }

        #maximized-overlay.active > .video-tile .tile-info {
            position: absolute !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 8 !important;
        }

        /* Clean chat: full sender names, no alphabet avatar */
        .chat-message-avatar {
            display: none !important;
        }

        .chat-body {
            gap: 9px !important;
            padding: 12px !important;
        }

        .chat-message-row {
            display: flex !important;
            width: 100% !important;
            gap: 0 !important;
            align-items: flex-start !important;
        }

        .chat-message-row.is-me {
            justify-content: flex-start !important;
            flex-direction: row !important;
        }

        .chat-message-row.is-other {
            justify-content: flex-end !important;
            flex-direction: row !important;
        }

        .chat-message-content {
            min-width: 130px !important;
            max-width: 88% !important;
        }

        .chat-message-meta,
        .chat-message-row.is-me .chat-message-meta {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 0 5px 4px !important;
        }

        .chat-message-meta strong {
            display: block !important;
            max-width: 240px !important;
            color: #f8fafc !important;
            font-size: 10px !important;
            font-weight: 750 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        .chat-message-meta span,
        .chat-message-row.is-me .chat-message-meta span {
            margin-left: auto !important;
            margin-right: 0 !important;
            color: #64748b !important;
            font-size: 8px !important;
        }

        .chat-message-bubble,
        .chat-message-row.is-me .chat-message-bubble {
            padding: 9px 12px !important;
            border-radius: 12px !important;
            font-size: 11px !important;
            line-height: 1.5 !important;
            box-shadow: none !important;
        }

        .chat-message-row.is-me .chat-message-bubble {
            background: rgba(37,99,235,.20) !important;
            border: 1px solid rgba(96,165,250,.28) !important;
        }

        .chat-message-row.is-other .chat-message-bubble {
            background: rgba(30,41,59,.86) !important;
            border: 1px solid rgba(148,163,184,.14) !important;
        }

        @media (max-width: 760px) {
            .video-grid:has(> .video-tile:nth-child(2):last-child) {
                grid-template-columns: 1fr !important;
            }
        }

    </style>


    <style>
        /* SmartMeet V4: larger 1–2 person layout with tighter spacing */
        .video-grid{gap:10px!important;padding:14px!important;}
        .video-grid:has(> .video-tile:only-child){grid-template-columns:minmax(520px,720px)!important;}
        .video-grid:has(> .video-tile:first-child:nth-last-child(2)){grid-template-columns:repeat(2,minmax(420px,570px))!important;}
        .video-grid .video-tile{max-width:570px!important;}
        @media(max-width:980px){
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){grid-template-columns:repeat(auto-fit,minmax(320px,1fr))!important;}
            .video-grid .video-tile{max-width:100%!important;}
        }
        @media(max-width:700px){.video-grid{gap:8px!important;padding:10px!important;}}
    </style>

    <style>
        /* ============================================================
           SMARTMEET V5 FINAL LAYOUT
           - wider 1–2 person tiles (not taller)
           - camera fills the full tile
           - info bar overlays the video instead of consuming tile height
           ============================================================ */
        .video-grid{
            gap:10px !important;
            padding:12px !important;
            grid-auto-rows:auto !important;
            align-content:start !important;
            justify-content:start !important;
        }
        .video-grid:has(> .video-tile:only-child){
            grid-template-columns:minmax(600px,780px) !important;
        }
        .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
            grid-template-columns:repeat(2,minmax(480px,650px)) !important;
        }
        .video-grid .video-tile{
            width:100% !important;
            max-width:650px !important;
            min-height:0 !important;
            height:auto !important;
            aspect-ratio:16/9 !important;
            position:relative !important;
        }
        .video-grid:has(> .video-tile:only-child) .video-tile{
            max-width:780px !important;
        }
        .video-grid .video-placeholder{
            position:absolute !important;
            inset:0 !important;
            width:100% !important;
            height:100% !important;
        }
        .video-grid .video-placeholder video{
            position:absolute !important;
            inset:0 !important;
            width:100% !important;
            height:100% !important;
            object-fit:cover !important;
        }
        .video-grid .tile-info{
            position:absolute !important;
            left:0 !important;
            right:0 !important;
            bottom:0 !important;
            z-index:8 !important;
            min-height:44px !important;
            background:linear-gradient(to top,rgba(2,6,23,.95),rgba(2,6,23,.62),transparent) !important;
        }
        .video-grid .you-badge,
        .video-grid .tile-expand-btn,
        .video-grid .mic-off,
        .video-grid .speaking-indicator{
            z-index:10 !important;
        }
        @media(max-width:1150px){
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
                grid-template-columns:repeat(auto-fit,minmax(360px,1fr)) !important;
            }
            .video-grid .video-tile{
                max-width:100% !important;
            }
        }
        @media(max-width:700px){
            .video-grid{
                grid-template-columns:1fr !important;
                gap:8px !important;
                padding:9px !important;
            }
            .video-grid .video-tile{
                max-width:100% !important;
                aspect-ratio:16/10 !important;
            }
        }
    </style>


    <style>
        /* ============================================================
           SMARTMEET V6 — balanced professional tiles
           Smaller than V5, full 16:9 camera surface, no wasted black area.
           ============================================================ */
        .video-grid{
            display:grid !important;
            grid-template-columns:repeat(auto-fill,minmax(360px,520px)) !important;
            grid-auto-rows:auto !important;
            justify-content:start !important;
            align-content:start !important;
            align-items:start !important;
            gap:10px !important;
            padding:12px !important;
        }
        .video-grid:has(> .video-tile:only-child){
            grid-template-columns:minmax(440px,560px) !important;
        }
        .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
            grid-template-columns:repeat(2,minmax(390px,520px)) !important;
        }
        .video-grid .video-tile{
            width:100% !important;
            max-width:520px !important;
            height:auto !important;
            min-height:0 !important;
            aspect-ratio:16/9 !important;
            position:relative !important;
            overflow:hidden !important;
        }
        .video-grid:has(> .video-tile:only-child) .video-tile{
            max-width:560px !important;
        }
        .video-grid .video-placeholder{
            position:absolute !important;
            inset:0 !important;
            width:100% !important;
            height:100% !important;
            min-height:0 !important;
        }
        .video-grid .video-placeholder video{
            position:absolute !important;
            inset:0 !important;
            display:block;
            width:100% !important;
            height:100% !important;
            object-fit:cover !important;
            background:#050816 !important;
        }
        .video-grid .tile-info{
            position:absolute !important;
            left:0 !important;
            right:0 !important;
            bottom:0 !important;
            z-index:8 !important;
            min-height:42px !important;
            padding:8px 10px !important;
            background:linear-gradient(to top,rgba(2,6,23,.96),rgba(2,6,23,.68),transparent) !important;
        }
        @media(max-width:1000px){
            .video-grid,
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
                grid-template-columns:repeat(auto-fit,minmax(320px,1fr)) !important;
            }
            .video-grid .video-tile{max-width:100% !important;}
        }
        @media(max-width:700px){
            .video-grid,
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
                grid-template-columns:1fr !important;
            }
            .video-grid{gap:8px !important;padding:9px !important;}
            .video-grid .video-tile{max-width:100% !important;aspect-ratio:16/10 !important;}
        }
    </style>


    <style>
        /* ============================================================
           SMARTMEET FINAL UI PATCH
           ============================================================ */

        /* Remove mic status button/icon from every video tile only. */
        .video-tile .mic-off,
        .video-tile [id^="micoff-"]{
            display:none !important;
        }

        /* Balanced compact tiles: full camera coverage without oversized cards. */
        .video-grid{
            grid-template-columns:repeat(auto-fill,minmax(340px,460px)) !important;
            gap:10px !important;
            padding:12px !important;
            justify-content:start !important;
            align-content:start !important;
            grid-auto-rows:auto !important;
        }
        .video-grid:has(> .video-tile:only-child){
            grid-template-columns:minmax(400px,500px) !important;
        }
        .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
            grid-template-columns:repeat(2,minmax(360px,460px)) !important;
        }
        .video-grid .video-tile{
            width:100% !important;
            max-width:460px !important;
            min-height:0 !important;
            height:auto !important;
            aspect-ratio:16/9 !important;
            position:relative !important;
            overflow:hidden !important;
        }
        .video-grid:has(> .video-tile:only-child) .video-tile{
            max-width:500px !important;
        }
        .video-grid .video-placeholder{
            position:absolute !important;
            inset:0 !important;
            width:100% !important;
            height:100% !important;
        }
        .video-grid .video-placeholder video{
            position:absolute !important;
            inset:0 !important;
            width:100% !important;
            height:100% !important;
            object-fit:cover !important;
        }
        .video-grid .tile-info{
            position:absolute !important;
            left:0 !important;
            right:0 !important;
            bottom:0 !important;
            z-index:8 !important;
            min-height:40px !important;
            background:linear-gradient(to top,rgba(2,6,23,.96),rgba(2,6,23,.64),transparent) !important;
        }

        /* Chat: MY messages LEFT, messages received from others RIGHT. */
        .chat-message-row{
            width:100% !important;
            display:flex !important;
            align-items:flex-end !important;
            gap:8px !important;
            margin:7px 0 !important;
        }
        .chat-message-row.is-me{
            flex-direction:row !important;
            justify-content:flex-start !important;
        }
        .chat-message-row.is-other{
            flex-direction:row-reverse !important;
            justify-content:flex-start !important;
        }
        .chat-message-content{
            max-width:min(82%,320px) !important;
        }
        .chat-message-row.is-me .chat-message-content{
            text-align:left !important;
        }
        .chat-message-row.is-other .chat-message-content{
            text-align:right !important;
        }
        .chat-message-row.is-me .chat-message-meta{
            flex-direction:row !important;
            justify-content:flex-start !important;
        }
        .chat-message-row.is-other .chat-message-meta{
            flex-direction:row-reverse !important;
            justify-content:flex-start !important;
        }
        .chat-message-row.is-me .chat-message-bubble{
            border-radius:6px 15px 15px 15px !important;
        }
        .chat-message-row.is-other .chat-message-bubble{
            border-radius:15px 6px 15px 15px !important;
        }
        .chat-voice-btn{
            width:36px;
            height:36px;
            flex:0 0 36px;
            border:1px solid rgba(148,163,184,.18);
            border-radius:10px;
            background:rgba(15,23,42,.88);
            color:#e2e8f0;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
        }
        .chat-voice-btn.listening{
            color:#ef4444;
            border-color:rgba(239,68,68,.55);
            background:rgba(239,68,68,.12);
        }

        @media(max-width:950px){
            .video-grid,
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
                grid-template-columns:repeat(auto-fit,minmax(300px,1fr)) !important;
            }
            .video-grid .video-tile{
                max-width:100% !important;
            }
        }
        @media(max-width:700px){
            .video-grid,
            .video-grid:has(> .video-tile:only-child),
            .video-grid:has(> .video-tile:first-child:nth-last-child(2)){
                grid-template-columns:1fr !important;
            }
        }
    </style>


    <style id="smartmeet-final-ui-polish">
        /* Final visual authority: compact, balanced room without oversized single tiles. */
        :root{--sm-bg:#06111f;--sm-panel:#0b1728;--sm-panel2:#101f34;--sm-line:rgba(148,163,184,.16);--sm-text:#f8fafc;--sm-muted:#94a3b8;--sm-blue:#38bdf8;--sm-violet:#8b5cf6;--sm-green:#22c55e;--sm-red:#ef4444}
        body{background:radial-gradient(circle at 12% 8%,rgba(56,189,248,.12),transparent 28%),radial-gradient(circle at 88% 18%,rgba(139,92,246,.10),transparent 28%),linear-gradient(145deg,#040a14,#071525 55%,#07101d)!important}
        .main{padding:12px!important;gap:12px!important}.video-area{background:linear-gradient(145deg,rgba(8,18,33,.94),rgba(5,13,25,.96))!important;border:1px solid rgba(125,211,252,.14)!important;border-radius:18px!important}
        .video-grid{display:grid!important;grid-template-columns:repeat(auto-fit,minmax(280px,520px))!important;grid-auto-rows:auto!important;align-content:start!important;justify-content:start!important;gap:12px!important;padding:14px!important;overflow:auto!important}
        .video-grid>.video-tile{width:100%!important;max-width:520px!important;min-height:0!important;aspect-ratio:16/9!important;border-radius:16px!important;background:linear-gradient(145deg,#12233a,#081424)!important;border:1px solid rgba(148,163,184,.16)!important;box-shadow:0 16px 42px rgba(0,0,0,.25)!important}
        .video-grid:has(>.video-tile:only-child){grid-template-columns:minmax(320px,500px)!important;justify-content:start!important;align-content:center!important}.video-grid:has(>.video-tile:only-child)>.video-tile{max-width:500px!important}
        .video-placeholder,.video-placeholder video{width:100%!important;height:100%!important}.video-placeholder video{object-fit:cover!important}
        .tile-info{background:linear-gradient(to top,rgba(2,6,23,.95),rgba(2,6,23,.62))!important}.mic-off{display:none!important}
        #side-panel{background:linear-gradient(180deg,rgba(12,26,45,.98),rgba(7,16,30,.98))!important;border:1px solid rgba(125,211,252,.14)!important}
        .participant-offline{filter:saturate(.35);opacity:.38!important}.participant-online{opacity:1!important;filter:none!important;border-color:rgba(34,197,94,.28)!important}
        .chat-body{background:linear-gradient(180deg,rgba(8,18,34,.55),rgba(5,13,25,.75))!important}.chat-message-row.is-me .chat-message-bubble{background:linear-gradient(135deg,#2563eb,#0891b2)!important}.chat-message-row:not(.is-me) .chat-message-bubble{background:linear-gradient(135deg,#172554,#312e81)!important}
        .controls{background:rgba(5,13,25,.94)!important;border-color:rgba(148,163,184,.14)!important}.ctrl-icon.active{background:linear-gradient(135deg,rgba(37,99,235,.45),rgba(8,145,178,.35))!important}
        @media(max-width:900px){.video-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;justify-content:stretch!important}.video-grid>.video-tile{max-width:none!important}.video-grid:has(>.video-tile:only-child){grid-template-columns:minmax(260px,480px)!important}}
        @media(max-width:620px){.video-grid{grid-template-columns:1fr!important;padding:8px!important;gap:8px!important}.video-grid>.video-tile,.video-grid:has(>.video-tile:only-child)>.video-tile{max-width:100%!important}.main{padding:6px!important}}


        /* ================================================================
           SMARTMEET AWS FINAL ROOM POLISH
           - one live tile stays wide/compact
           - only live participants have video tiles
           - People rows distinguish Joined / Left / Not joined yet
           ================================================================ */
        .video-area {
            position: relative !important;
            background:
                radial-gradient(circle at 18% 18%, rgba(14,165,233,.10), transparent 30%),
                radial-gradient(circle at 84% 78%, rgba(139,92,246,.10), transparent 34%),
                linear-gradient(145deg, rgba(4,11,25,.96), rgba(8,20,38,.94)) !important;
        }
        .video-area::before {
            content: "";
            position: absolute;
            inset: 18px;
            pointer-events: none;
            border-radius: 22px;
            border: 1px dashed rgba(148,163,184,.055);
            background:
                linear-gradient(rgba(255,255,255,.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.012) 1px, transparent 1px);
            background-size: 34px 34px;
        }
        .video-grid {
            position: relative;
            z-index: 1;
            align-content: start !important;
            justify-content: start !important;
            grid-auto-rows: auto !important;
        }
        .video-grid:has(> .video-tile:first-child:last-child) {
            grid-template-columns: minmax(0, min(940px, 88%)) !important;
        }
        .video-grid:has(> .video-tile:first-child:last-child) > .video-tile {
            width: 100% !important;
            max-width: 940px !important;
            min-height: 0 !important;
            height: auto !important;
            aspect-ratio: 16 / 9 !important;
            max-height: min(56vh, 520px) !important;
        }
        .video-grid:has(> .video-tile:nth-child(2):last-child) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
        .video-grid:has(> .video-tile:nth-child(2):last-child) > .video-tile {
            min-height: 0 !important;
            height: auto !important;
            aspect-ratio: 16 / 9 !important;
            max-height: 430px !important;
        }
        .video-grid:has(> .video-tile:nth-child(3)) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
        .video-grid:has(> .video-tile:nth-child(3)) > .video-tile {
            min-height: 0 !important;
            aspect-ratio: 16 / 9 !important;
        }
        .participant-left {
            opacity: .42 !important;
            filter: saturate(.45);
            border-color: rgba(248,113,113,.16) !important;
        }
        .participant-left .join-status {
            color: #f87171 !important;
        }
        @media (max-width: 760px) {
            .video-grid:has(> .video-tile:first-child:last-child),
            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)) {
                grid-template-columns: 1fr !important;
            }
            .video-grid:has(> .video-tile:first-child:last-child) {
                width: 100% !important;
            }
        }

    </style>


    <style>

        /* =====================================================================
           SMARTMEET PROFESSIONAL ROOM UI — PRESENTATION ONLY
           Inspired by the supplied reference. Existing JS IDs/classes and
           WebRTC/Reverb/Chat/Transcription/Presence behavior remain untouched.
           ===================================================================== */
        :root{
            --sm-bg:#070b14;
            --sm-surface:#0d1322;
            --sm-surface-2:#111827;
            --sm-panel:#0b1020;
            --sm-line:rgba(148,163,184,.12);
            --sm-text:#f8fafc;
            --sm-muted:#8d98ad;
            --sm-accent:#6d6bff;
            --sm-cyan:#21d4fd;
            --sm-green:#2dd4bf;
            --sm-danger:#ff5d78;
        }

        html,body{
            background:var(--sm-bg)!important;
            color:var(--sm-text)!important;
        }
        .meeting-app{
            background:
                radial-gradient(circle at 12% 0%,rgba(99,102,241,.08),transparent 25%),
                linear-gradient(180deg,#0a0f1d 0%,#070b14 100%)!important;
        }

        /* Top bar: slim and professional */
        .topbar{
            min-height:70px!important;
            height:70px!important;
            padding:0 22px!important;
            background:rgba(13,19,34,.96)!important;
            border-bottom:1px solid var(--sm-line)!important;
            box-shadow:0 8px 28px rgba(0,0,0,.18)!important;
            backdrop-filter:blur(16px);
        }
        .brand-logo{
            border-radius:14px!important;
            background:linear-gradient(135deg,#7c7cff,#27c9f7)!important;
            box-shadow:0 10px 30px rgba(99,102,241,.22)!important;
        }
        .meeting-title{font-weight:800!important;letter-spacing:-.02em!important}
        .meeting-subtitle{color:var(--sm-muted)!important}
        .role-badge{
            border:1px solid rgba(34,211,238,.16)!important;
            background:rgba(34,211,238,.11)!important;
            color:#3dd9f5!important;
        }
        .online-pill{
            background:rgba(255,255,255,.045)!important;
            border:1px solid var(--sm-line)!important;
            border-radius:999px!important;
        }

        /* Main stage */
        .meeting-body{
            background:var(--sm-bg)!important;
        }
        .video-area{
            padding:22px 22px 104px!important;
            background:
                radial-gradient(circle at 50% 45%,rgba(99,102,241,.055),transparent 34%),
                #070b14!important;
        }
        .video-area::before{
            display:none!important;
        }
        .video-grid{
            width:100%!important;
            max-width:1220px!important;
            margin:0 auto!important;
            padding:0!important;
            gap:14px!important;
            align-content:center!important;
            justify-content:center!important;
        }

        /* Only existing live tiles are styled; no placeholder tiles are created. */
        .video-tile{
            overflow:hidden!important;
            border-radius:18px!important;
            border:1px solid rgba(148,163,184,.13)!important;
            background:#101827!important;
            box-shadow:0 16px 45px rgba(0,0,0,.28)!important;
        }
        .video-tile video{
            width:100%!important;
            height:100%!important;
            object-fit:cover!important;
            background:#0b1220!important;
        }
        .video-grid:has(> .video-tile:first-child:last-child){
            grid-template-columns:minmax(320px,min(860px,82vw))!important;
        }
        .video-grid:has(> .video-tile:first-child:last-child)>.video-tile{
            width:100%!important;
            min-height:0!important;
            height:auto!important;
            aspect-ratio:16/9!important;
            max-height:58vh!important;
        }
        .video-grid:has(> .video-tile:nth-child(2):last-child){
            grid-template-columns:repeat(2,minmax(280px,1fr))!important;
            max-width:1180px!important;
        }
        .video-grid:has(> .video-tile:nth-child(2):last-child)>.video-tile{
            aspect-ratio:16/9!important;
            min-height:0!important;
            height:auto!important;
            max-height:54vh!important;
        }
        .video-grid:has(> .video-tile:nth-child(3)){
            grid-template-columns:repeat(2,minmax(260px,1fr))!important;
        }
        .video-grid:has(> .video-tile:nth-child(5)){
            grid-template-columns:repeat(3,minmax(220px,1fr))!important;
        }
        .video-name{
            background:rgba(4,8,18,.68)!important;
            border:1px solid rgba(255,255,255,.08)!important;
            backdrop-filter:blur(10px);
            border-radius:9px!important;
        }

        /* Right sidebar: same toggle behavior, visual redesign only */
        .sidebar{
            background:#0b0f1d!important;
            border-left:1px solid var(--sm-line)!important;
            box-shadow:-16px 0 40px rgba(0,0,0,.15)!important;
        }
        .sidebar-header{
            min-height:52px!important;
            background:#0d1221!important;
            border-bottom:1px solid var(--sm-line)!important;
        }
        .tabs{
            background:#0d1221!important;
            border-bottom:1px solid var(--sm-line)!important;
            gap:3px!important;
            padding:0 10px!important;
        }
        .tab{
            min-height:52px!important;
            color:#778196!important;
            border-radius:10px 10px 0 0!important;
            font-weight:700!important;
        }
        .tab.active{
            color:#f8fafc!important;
            background:rgba(255,255,255,.035)!important;
        }
        .tab.active::after{
            background:linear-gradient(90deg,#706cff,#8b7cff)!important;
            height:2px!important;
        }
        .badge{
            background:rgba(255,255,255,.08)!important;
            color:#cbd5e1!important;
        }
        .sidebar-content{
            background:#0b0f1d!important;
        }

        /* People */
        .participant-row{
            border:1px solid transparent!important;
            border-radius:13px!important;
            background:transparent!important;
            transition:.18s ease!important;
        }
        .participant-row:hover{
            background:rgba(255,255,255,.035)!important;
            border-color:rgba(148,163,184,.08)!important;
        }
        .participant-online{
            opacity:1!important;
            filter:none!important;
        }
        .participant-offline,
        .participant-left{
            opacity:.40!important;
            filter:saturate(.35)!important;
        }
        .avatar{
            background:linear-gradient(135deg,#696cff,#22c9f5)!important;
            box-shadow:0 7px 18px rgba(99,102,241,.18)!important;
        }

        /* Chat */
        .chat-message{
            max-width:86%!important;
        }
        .chat-bubble{
            border-radius:14px!important;
            box-shadow:none!important;
        }
        .chat-message.own .chat-bubble{
            background:linear-gradient(135deg,#6568f4,#7758e8)!important;
            color:#fff!important;
        }
        .chat-message:not(.own) .chat-bubble{
            background:#151d2e!important;
            border:1px solid rgba(148,163,184,.10)!important;
            color:#eef2ff!important;
        }
        .chat-input-wrap{
            background:#0d1322!important;
            border-top:1px solid var(--sm-line)!important;
        }
        .chat-input{
            background:#111827!important;
            border:1px solid rgba(148,163,184,.14)!important;
            color:#f8fafc!important;
            border-radius:13px!important;
        }
        .chat-input:focus{
            border-color:rgba(109,107,255,.7)!important;
            box-shadow:0 0 0 3px rgba(109,107,255,.10)!important;
        }

        /* Captions/transcription */
        .transcript-item{
            background:#111827!important;
            border:1px solid rgba(148,163,184,.10)!important;
            border-radius:13px!important;
        }
        .transcript-name{color:#a9b4ff!important}

        /* Bottom floating controls, reference-style */
        .controls{
            left:50%!important;
            right:auto!important;
            bottom:18px!important;
            transform:translateX(-50%)!important;
            width:auto!important;
            max-width:calc(100vw - 30px)!important;
            padding:10px 12px!important;
            gap:8px!important;
            border:1px solid rgba(148,163,184,.12)!important;
            border-radius:999px!important;
            background:rgba(13,18,33,.95)!important;
            box-shadow:0 18px 50px rgba(0,0,0,.36)!important;
            backdrop-filter:blur(18px)!important;
        }
        .control-btn{
            width:48px!important;
            height:48px!important;
            min-width:48px!important;
            border-radius:50%!important;
            background:#1a2131!important;
            border:1px solid rgba(148,163,184,.12)!important;
            color:#f8fafc!important;
        }
        .control-btn:hover{
            transform:translateY(-2px)!important;
            background:#222b3e!important;
        }
        .control-btn.active{
            background:rgba(109,107,255,.18)!important;
            border-color:rgba(109,107,255,.35)!important;
            color:#b9b8ff!important;
        }
        .leave-btn,.cancel-btn{
            min-height:48px!important;
            border-radius:999px!important;
            padding:0 20px!important;
            font-weight:800!important;
        }
        .leave-btn{
            background:#ff5d78!important;
            border-color:#ff5d78!important;
            color:white!important;
        }
        .cancel-btn{
            background:rgba(255,93,120,.12)!important;
            border-color:rgba(255,93,120,.28)!important;
            color:#ff8297!important;
        }

        /* Toasts/dialogs */
        .toast{
            background:#111827!important;
            border:1px solid rgba(148,163,184,.14)!important;
            color:#f8fafc!important;
            box-shadow:0 16px 50px rgba(0,0,0,.35)!important;
        }

        /* Responsive */
        @media(max-width:1000px){
            .video-area{padding:16px 16px 100px!important}
            .video-grid:has(> .video-tile:nth-child(5)){
                grid-template-columns:repeat(2,minmax(220px,1fr))!important;
            }
        }
        @media(max-width:760px){
            .topbar{
                height:auto!important;
                min-height:62px!important;
                padding:9px 12px!important;
            }
            .meeting-title{font-size:15px!important}
            .meeting-subtitle{font-size:11px!important}
            .video-area{padding:12px 10px 94px!important}
            .video-grid:has(> .video-tile:first-child:last-child),
            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)),
            .video-grid:has(> .video-tile:nth-child(5)){
                grid-template-columns:1fr!important;
            }
            .video-grid:has(> .video-tile:first-child:last-child)>.video-tile{
                max-height:none!important;
            }
            .controls{
                bottom:10px!important;
                padding:7px 8px!important;
                gap:5px!important;
            }
            .control-btn{
                width:43px!important;
                height:43px!important;
                min-width:43px!important;
            }
            .leave-btn,.cancel-btn{
                min-height:43px!important;
                padding:0 14px!important;
            }
        }

    </style>

    <style>

        /* ===============================================================
           SMARTMEET CONTROL BAR POSITION FIX — UI ONLY
           Existing onclick handlers / IDs / JS logic remain unchanged.
           =============================================================== */
        .controls{
            position:fixed !important;
            left:50% !important;
            right:auto !important;
            bottom:18px !important;
            top:auto !important;
            transform:translateX(-50%) !important;

            display:inline-flex !important;
            flex:0 0 auto !important;
            width:max-content !important;
            min-width:0 !important;
            max-width:calc(100vw - 28px) !important;
            height:auto !important;
            min-height:64px !important;

            margin:0 !important;
            padding:8px 11px !important;
            gap:6px !important;

            align-items:center !important;
            justify-content:center !important;
            flex-wrap:nowrap !important;
            overflow-x:auto !important;
            overflow-y:hidden !important;

            border:1px solid rgba(148,163,184,.13) !important;
            border-radius:999px !important;
            background:rgba(13,18,33,.96) !important;
            box-shadow:0 18px 50px rgba(0,0,0,.38) !important;
            backdrop-filter:blur(18px) !important;
            -webkit-backdrop-filter:blur(18px) !important;
            z-index:90 !important;

            scrollbar-width:none;
        }
        .controls::-webkit-scrollbar{display:none!important}

        /* Actual project class is .ctrl-btn, not .control-btn */
        .controls .ctrl-btn{
            flex:0 0 auto !important;
            min-width:50px !important;
            width:auto !important;
            padding:3px 4px !important;
            gap:4px !important;
            display:flex !important;
            flex-direction:column !important;
            align-items:center !important;
            justify-content:center !important;
            border-radius:12px !important;
        }
        .controls .ctrl-icon{
            width:40px !important;
            height:40px !important;
            min-width:40px !important;
            min-height:40px !important;
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
            border-radius:50% !important;
            background:#1a2131 !important;
            border:1px solid rgba(148,163,184,.12) !important;
            color:#f8fafc !important;
            font-size:13px !important;
            transition:.18s ease !important;
        }
        .controls .ctrl-btn:hover .ctrl-icon{
            transform:translateY(-2px) !important;
            background:#222b3e !important;
        }
        .controls .ctrl-icon.active{
            background:rgba(109,107,255,.20) !important;
            border-color:rgba(109,107,255,.38) !important;
            color:#c4c3ff !important;
        }
        .controls .ctrl-icon.off{
            background:#242a38 !important;
            color:#cbd5e1 !important;
        }
        .controls .ctrl-label{
            display:block !important;
            font-size:8px !important;
            line-height:1 !important;
            color:#9aa5b8 !important;
            white-space:nowrap !important;
        }
        .controls .ctrl-divider{
            flex:0 0 1px !important;
            width:1px !important;
            height:30px !important;
            margin:0 2px !important;
            background:rgba(148,163,184,.15) !important;
        }

        /* Existing end/leave/cancel buttons retain their actions, only appearance */
        .controls .btn-end,
        .controls .leave-btn,
        .controls .cancel-btn{
            flex:0 0 auto !important;
        }

        /* Keep stage content clear of floating bar */
        .video-area{
            padding-bottom:100px !important;
        }

        @media(max-width:760px){
            .controls{
                bottom:8px !important;
                max-width:calc(100vw - 14px) !important;
                min-height:56px !important;
                padding:6px 7px !important;
                gap:3px !important;
            }
            .controls .ctrl-btn{
                min-width:44px !important;
                padding:2px !important;
            }
            .controls .ctrl-icon{
                width:36px !important;
                height:36px !important;
                min-width:36px !important;
                min-height:36px !important;
                font-size:12px !important;
            }
            .controls .ctrl-label{
                font-size:7px !important;
            }
            .controls .ctrl-divider{
                height:26px !important;
                margin:0 1px !important;
            }
            .video-area{
                padding-bottom:82px !important;
            }
        }

    </style>

    <style>

        /* ===============================================================
           SMARTMEET TABLET / RESPONSIVE TILE SPACING FIX — UI ONLY
           No WebRTC / Reverb / chat / transcript / presence JS changed.
           =============================================================== */

        /* Keep controls truly centered on desktop/tablet */
        .controls{
            position:fixed !important;
            left:50% !important;
            right:auto !important;
            bottom:16px !important;
            transform:translateX(-50%) !important;
            width:max-content !important;
            max-width:calc(100vw - 24px) !important;
            margin:0 !important;
        }

        /* Give the meeting stage enough room around all tiles */
        .video-area{
            overflow:auto !important;
            padding:18px 18px 104px !important;
        }

        /* Desktop / laptop */
        .video-grid{
            display:grid !important;
            width:100% !important;
            max-width:1220px !important;
            margin:0 auto !important;
            padding:0 !important;
            gap:16px !important;
            row-gap:16px !important;
            column-gap:16px !important;
            align-items:stretch !important;
            justify-items:stretch !important;
            grid-auto-rows:auto !important;
        }

        /* Never let tiles visually collide */
        .video-grid > .video-tile{
            margin:0 !important;
            min-width:0 !important;
            min-height:0 !important;
            width:100% !important;
            box-sizing:border-box !important;
        }

        /* 1 tile */
        .video-grid:has(> .video-tile:first-child:last-child){
            grid-template-columns:minmax(320px,min(860px,82vw)) !important;
            justify-content:center !important;
        }
        .video-grid:has(> .video-tile:first-child:last-child) > .video-tile{
            aspect-ratio:16/9 !important;
            max-height:56vh !important;
        }

        /* 2 tiles */
        .video-grid:has(> .video-tile:nth-child(2):last-child){
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        }
        .video-grid:has(> .video-tile:nth-child(2):last-child) > .video-tile{
            aspect-ratio:16/9 !important;
        }

        /* 3-4 tiles */
        .video-grid:has(> .video-tile:nth-child(3)){
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        }
        .video-grid:has(> .video-tile:nth-child(3)) > .video-tile{
            aspect-ratio:16/9 !important;
        }

        /* Tablet landscape / medium width */
        @media (max-width:1100px){
            .video-area{
                padding:16px 16px 96px !important;
            }

            .video-grid{
                gap:14px !important;
                row-gap:14px !important;
                column-gap:14px !important;
            }

            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)){
                grid-template-columns:repeat(2,minmax(0,1fr)) !important;
            }

            .video-grid > .video-tile{
                aspect-ratio:16/9 !important;
                height:auto !important;
            }
        }

        /* Tablet portrait */
        @media (max-width:820px){
            .video-area{
                padding:14px 12px 90px !important;
            }

            .video-grid{
                grid-template-columns:1fr !important;
                gap:14px !important;
                row-gap:14px !important;
                max-width:720px !important;
            }

            .video-grid:has(> .video-tile:first-child:last-child),
            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)),
            .video-grid:has(> .video-tile:nth-child(5)){
                grid-template-columns:1fr !important;
            }

            .video-grid > .video-tile{
                width:100% !important;
                max-width:100% !important;
                aspect-ratio:16/9 !important;
                height:auto !important;
                max-height:none !important;
                margin-bottom:0 !important;
            }

            /* Sidebar remains toggle panel and should not squeeze/merge tiles */
            #side-panel,
            .sidebar{
                max-width:100% !important;
            }

            .controls{
                bottom:8px !important;
                max-width:calc(100vw - 12px) !important;
            }
        }

        /* Phones */
        @media (max-width:560px){
            .video-area{
                padding:10px 8px 82px !important;
            }

            .video-grid{
                gap:10px !important;
                row-gap:10px !important;
            }

            .video-grid > .video-tile{
                border-radius:14px !important;
            }

            .controls{
                left:50% !important;
                right:auto !important;
                transform:translateX(-50%) !important;
                bottom:6px !important;
                width:max-content !important;
                max-width:calc(100vw - 8px) !important;
                overflow-x:auto !important;
            }
        }

    </style>

    <style>

        /* =====================================================================
           SMARTMEET FULL RESPONSIVE FINAL — UI ONLY
           Existing WebRTC / Laravel Reverb / chat / transcription / presence /
           leave / cancel JavaScript remains unchanged.
           ===================================================================== */

        html, body {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        body {
            min-height: 100dvh !important;
        }

        /* ---------- HEADER ---------- */
        .header {
            width: 100% !important;
            min-width: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            padding: 8px 14px !important;
        }

        .header-left {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .header-right {
            flex: 0 0 auto !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .header-center {
            flex: 0 0 auto !important;
        }

        .header-meeting-info {
            min-width: 0 !important;
        }

        .meeting-title,
        .meeting-meta {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        /* ---------- MAIN ---------- */
        .main {
            width: 100% !important;
            min-width: 0 !important;
            min-height: 0 !important;
            display: flex !important;
            gap: 10px !important;
            overflow: hidden !important;
        }

        .video-area {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            min-height: 0 !important;
            overflow: auto !important;
            padding: 14px 14px 100px !important;
        }

        .video-grid {
            width: 100% !important;
            max-width: 1240px !important;
            margin: 0 auto !important;
            display: grid !important;
            gap: 14px !important;
            row-gap: 14px !important;
            column-gap: 14px !important;
            align-items: stretch !important;
            justify-items: stretch !important;
            grid-auto-rows: auto !important;
        }

        .video-grid > .video-tile {
            width: 100% !important;
            min-width: 0 !important;
            min-height: 0 !important;
            height: auto !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            aspect-ratio: 16 / 9 !important;
        }

        /* 1 tile */
        .video-grid:has(> .video-tile:first-child:last-child) {
            grid-template-columns: minmax(0, min(860px, 92%)) !important;
            justify-content: center !important;
        }

        .video-grid:has(> .video-tile:first-child:last-child) > .video-tile {
            max-width: 860px !important;
            max-height: 58vh !important;
        }

        /* 2 tiles */
        .video-grid:has(> .video-tile:nth-child(2):last-child) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        /* 3-4 tiles */
        .video-grid:has(> .video-tile:nth-child(3)) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        /* 5+ */
        .video-grid:has(> .video-tile:nth-child(5)) {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        /* ---------- SIDE PANEL ---------- */
        #side-panel,
        .sidebar {
            flex: 0 0 min(330px, 32vw) !important;
            width: min(330px, 32vw) !important;
            min-width: 260px !important;
            max-width: 360px !important;
            height: 100% !important;
            overflow: hidden !important;
        }

        /* ---------- CONTROLS ---------- */
        .controls {
            position: fixed !important;
            left: 50% !important;
            right: auto !important;
            bottom: 12px !important;
            top: auto !important;
            transform: translateX(-50%) !important;

            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-wrap: nowrap !important;

            width: max-content !important;
            min-width: 0 !important;
            max-width: calc(100vw - 24px) !important;

            margin: 0 !important;
            padding: 7px 10px !important;
            gap: 5px !important;

            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: none !important;

            z-index: 100 !important;
        }

        .controls::-webkit-scrollbar {
            display: none !important;
        }

        .controls .ctrl-btn {
            flex: 0 0 auto !important;
            min-width: 46px !important;
            width: auto !important;
        }

        .controls .ctrl-icon,
        .controls .btn-end {
            flex: 0 0 auto !important;
        }

        /* ==========================================================
           LARGE LAPTOP / SMALL DESKTOP
           ========================================================== */
        @media (max-width: 1280px) {
            #side-panel,
            .sidebar {
                flex-basis: 300px !important;
                width: 300px !important;
                min-width: 280px !important;
            }

            .video-grid:has(> .video-tile:nth-child(5)) {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        /* ==========================================================
           TABLET LANDSCAPE / DEVTOOLS-SQUEEZED VIEW
           ========================================================== */
        @media (max-width: 1024px) {
            .header {
                padding: 7px 10px !important;
                gap: 7px !important;
            }

            .meeting-title {
                max-width: 32vw !important;
                font-size: 13px !important;
            }

            .meeting-meta {
                font-size: 8px !important;
            }

            .participants-count {
                padding: 4px 7px !important;
                font-size: 9px !important;
            }

            .btn-leave {
                padding: 5px 8px !important;
                font-size: 9px !important;
            }

            .main {
                gap: 8px !important;
            }

            .video-area {
                padding: 12px 12px 92px !important;
            }

            .video-grid {
                gap: 12px !important;
                row-gap: 12px !important;
                column-gap: 12px !important;
            }

            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)),
            .video-grid:has(> .video-tile:nth-child(5)) {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            #side-panel,
            .sidebar {
                flex-basis: 280px !important;
                width: 280px !important;
                min-width: 250px !important;
            }

            .controls {
                bottom: 8px !important;
                max-width: calc(100vw - 16px) !important;
            }
        }

        /* ==========================================================
           TABLET PORTRAIT
           Side panel becomes overlay, so video tiles never get crushed.
           ========================================================== */
        @media (max-width: 820px) {
            .header {
                min-height: 58px !important;
                flex-wrap: nowrap !important;
            }

            .header-brand-text {
                display: none !important;
            }

            .header-brand {
                padding-right: 6px !important;
            }

            .meeting-title {
                max-width: 42vw !important;
                font-size: 12px !important;
            }

            .meeting-meta {
                display: none !important;
            }

            .header-center {
                min-width: auto !important;
                padding: 4px 7px !important;
                font-size: 10px !important;
            }

            .main {
                display: block !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .video-area {
                width: 100% !important;
                height: 100% !important;
                padding: 10px 10px 86px !important;
            }

            .video-grid,
            .video-grid:has(> .video-tile:first-child:last-child),
            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)),
            .video-grid:has(> .video-tile:nth-child(5)) {
                grid-template-columns: 1fr !important;
                max-width: 700px !important;
                gap: 12px !important;
                row-gap: 12px !important;
            }

            .video-grid > .video-tile {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: none !important;
                aspect-ratio: 16 / 9 !important;
            }

            #side-panel,
            .sidebar {
                position: fixed !important;
                left: 8px !important;
                right: 8px !important;
                bottom: 72px !important;
                top: auto !important;

                width: auto !important;
                min-width: 0 !important;
                max-width: none !important;
                height: min(62dvh, 540px) !important;

                border-radius: 18px !important;
                z-index: 95 !important;
            }

            .controls {
                bottom: 6px !important;
                min-height: 54px !important;
                padding: 5px 7px !important;
                gap: 3px !important;
            }

            .controls .ctrl-btn {
                min-width: 42px !important;
            }

            .controls .ctrl-icon,
            .controls .btn-end {
                width: 35px !important;
                height: 35px !important;
                min-width: 35px !important;
                min-height: 35px !important;
                font-size: 11px !important;
            }

            .controls .ctrl-label {
                font-size: 7px !important;
            }
        }

        /* ==========================================================
           MOBILE
           ========================================================== */
        @media (max-width: 600px) {
            .header {
                min-height: 52px !important;
                padding: 6px 8px !important;
                gap: 5px !important;
            }

            .header-brand {
                display: none !important;
            }

            .live-badge {
                font-size: 8px !important;
                padding: 2px 6px !important;
            }

            .meeting-title {
                max-width: 44vw !important;
                font-size: 11px !important;
            }

            .header-center {
                font-size: 9px !important;
                padding: 3px 6px !important;
            }

            .participants-count {
                display: none !important;
            }

            .btn-leave {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 50% !important;
            }

            .btn-leave span {
                display: none !important;
            }

            .video-area {
                padding: 8px 7px 78px !important;
            }

            .video-grid {
                gap: 9px !important;
                row-gap: 9px !important;
            }

            .video-grid > .video-tile {
                border-radius: 14px !important;
            }

            #side-panel,
            .sidebar {
                left: 5px !important;
                right: 5px !important;
                bottom: 65px !important;
                height: min(66dvh, 500px) !important;
                border-radius: 15px !important;
            }

            .controls {
                bottom: 5px !important;
                max-width: calc(100vw - 8px) !important;
                min-height: 50px !important;
                padding: 4px 5px !important;
                gap: 2px !important;
            }

            .controls .ctrl-btn {
                min-width: 39px !important;
            }

            .controls .ctrl-icon,
            .controls .btn-end {
                width: 33px !important;
                height: 33px !important;
                min-width: 33px !important;
                min-height: 33px !important;
                font-size: 10px !important;
            }

            .controls .ctrl-label {
                display: none !important;
            }

            .controls .ctrl-divider {
                height: 24px !important;
                margin: 0 1px !important;
            }
        }

        /* ==========================================================
           VERY SMALL PHONES
           ========================================================== */
        @media (max-width: 390px) {
            .meeting-title {
                max-width: 38vw !important;
                font-size: 10px !important;
            }

            .header-center {
                display: none !important;
            }

            .video-area {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            .controls .ctrl-btn {
                min-width: 36px !important;
            }

            .controls .ctrl-icon,
            .controls .btn-end {
                width: 31px !important;
                height: 31px !important;
                min-width: 31px !important;
                min-height: 31px !important;
            }
        }

        /* ==========================================================
           SHORT / LANDSCAPE DEVICES
           ========================================================== */
        @media (max-height: 520px) and (orientation: landscape) {
            .header {
                min-height: 46px !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important;
            }

            .video-area {
                padding: 7px 8px 64px !important;
            }

            .video-grid {
                gap: 8px !important;
            }

            .video-grid:has(> .video-tile:nth-child(2):last-child),
            .video-grid:has(> .video-tile:nth-child(3)),
            .video-grid:has(> .video-tile:nth-child(5)) {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .controls {
                bottom: 4px !important;
                min-height: 45px !important;
                padding: 3px 5px !important;
            }

            .controls .ctrl-label {
                display: none !important;
            }

            #side-panel,
            .sidebar {
                height: calc(100dvh - 60px) !important;
                bottom: 52px !important;
            }
        }

    </style>

    <style id="sm-side-panel-final-fix">
        /* ================================================================
           SMARTMEET FINAL SIDE PANEL FIX
           - Chat / Transcript / People never pushes the video grid left/right
           - Desktop/tablet: floating panel stays on RIGHT
           - Mobile: bottom sheet
           - Top handle can be dragged vertically to resize panel height
        ================================================================ */
        #side-panel.sm-resizable-panel {
            position: fixed !important;
            right: 12px !important;
            left: auto !important;
            top: 70px !important;
            bottom: 82px !important;
            width: min(340px, calc(100vw - 24px)) !important;
            min-width: 280px !important;
            height: auto !important;
            max-height: calc(100dvh - 152px) !important;
            z-index: 85 !important;
            display: none;
            flex-direction: column !important;
            overflow: hidden !important;
            border-radius: 18px !important;
            resize: none !important;
        }

        #side-panel.sm-resizable-panel.sm-panel-open {
            display: flex !important;
        }

        #side-panel .sm-panel-resize-header {
            flex: 0 0 34px;
            height: 34px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2, 6, 23, .72);
            border-bottom: 1px solid rgba(148, 163, 184, .13);
            cursor: ns-resize;
            user-select: none;
            touch-action: none;
            z-index: 20;
        }

        #side-panel .sm-panel-drag-line {
            width: 46px;
            height: 5px;
            border-radius: 999px;
            background: rgba(203, 213, 225, .45);
            transition: background .15s ease, width .15s ease;
        }

        #side-panel .sm-panel-resize-header:hover .sm-panel-drag-line {
            width: 58px;
            background: rgba(96, 165, 250, .85);
        }

        #side-panel .sm-panel-collapse-btn {
            position: absolute;
            right: 8px;
            top: 5px;
            width: 25px;
            height: 25px;
            border: 1px solid rgba(148, 163, 184, .15);
            border-radius: 8px;
            background: rgba(30, 41, 59, .72);
            color: #cbd5e1;
            display: grid;
            place-items: center;
            cursor: pointer;
            font-size: 10px;
        }

        #side-panel.sm-panel-collapsed {
            bottom: auto !important;
            height: 34px !important;
            min-height: 34px !important;
        }

        #side-panel.sm-panel-collapsed > :not(.sm-panel-resize-header) {
            display: none !important;
        }

        #side-panel.sm-panel-collapsed .sm-panel-collapse-btn i {
            transform: rotate(180deg);
        }

        /* The side panel is now an overlay, so video area always keeps full width. */
        .main {
            position: relative !important;
        }
        .video-area {
            width: 100% !important;
            flex: 1 1 100% !important;
        }

        /* Mobile bottom sheet; still never becomes a left sidebar. */
        @media (max-width: 640px) {
            #side-panel.sm-resizable-panel {
                top: auto !important;
                right: 7px !important;
                left: 7px !important;
                bottom: 70px !important;
                width: auto !important;
                min-width: 0 !important;
                height: min(58dvh, 520px) !important;
                max-height: calc(100dvh - 120px) !important;
                border-radius: 18px 18px 12px 12px !important;
            }

            #side-panel.sm-panel-collapsed {
                top: auto !important;
                bottom: 70px !important;
                height: 34px !important;
            }
        }
    </style>

</head>

@php

    $organizer   = $meeting->organizer;

    $orgInitials = strtoupper(substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1));

    $colors      = ['#3b82f6,#06b6d4', '#8b5cf6,#ec4899', '#22c55e,#06b6d4', '#f59e0b,#ef4444', '#64748b,#334155', '#ec4899,#f59e0b'];

    $userInitials = strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1));

    $tz = $meeting->timezone ?? 'Asia/Karachi';

    $meetingEnd = null;

    if (!empty($meeting->end_time)) {

        $meetingEnd = \Carbon\Carbon::parse($meeting->end_time, $tz)->utc()->toIso8601String();

    } else {

        $durationMinutes = $meeting->duration_minutes ?? $meeting->duration ?? null;

        if ($durationMinutes) {

            $startForCalc = $meeting->actual_start

                ? \Carbon\Carbon::parse($meeting->actual_start)

                : \Carbon\Carbon::parse($meeting->date . ' ' . $meeting->time, $tz);

            $meetingEnd = $startForCalc->copy()->addMinutes((int) $durationMinutes)->utc()->toIso8601String();

        }

    }

@endphp

<body>

{{-- HEADER --}}

<div class="header">

    <div class="header-left">

        <div class="header-brand" style="display:flex;align-items:center;gap:10px;padding-right:16px;border-right:1px solid rgba(255,255,255,0.08);">

            <img src="{{ asset('images/s-logo.png') }}" style="width:32px;height:32px;object-fit:contain;">

            <div class="header-brand-text">

                <div style="font-weight:700;font-size:14px;color:white;">SmartMeet</div>

                <div style="font-size:10px;color:#64748b;">Meeting Suite</div>

            </div>

        </div>

        <div class="live-badge">

            <div class="live-dot"></div>

            LIVE

        </div>

        <div class="header-meeting-info">

            <div class="meeting-title">{{ $meeting->title }}</div>

            <div class="meeting-meta">

                <span><i class="fa fa-users"></i> <span data-total-count>{{ $meeting->participants->count() + 1 }}</span> Participants</span>

                <span>·</span>

                <span>{{ $meeting->timezone ?? 'Asia/Karachi' }}</span>

            </div>

        </div>

    </div>

    <div class="header-center">

        <i class="fa fa-clock timer-icon"></i>

        <span id="timer">00:00:00</span>

    </div>

    <div class="header-right">

        <div class="participants-count">

            <i class="fa fa-circle" style="color:var(--green);font-size:8px;"></i>

            <span data-online-count>1</span> online

        </div>

        <button class="btn-leave" onclick="leaveMeeting()">

            <i class="fa fa-phone-slash"></i> <span>Leave</span>

        </button>

    </div>

</div>

{{-- MAIN --}}

<div class="main">

    <div class="video-area">

        <div class="video-grid" id="video-grid">

            {{-- Participant's own tile --}}

            <div class="video-tile" id="tile-{{ auth()->id() }}">

                <div class="video-placeholder">

                    <video id="localVideo" autoplay muted playsinline class="mirrored" style="display:none;"></video>

                    <div class="avatar-circle" id="avatar-{{ auth()->id() }}" style="background:linear-gradient(135deg,{{ $colors[1] }});">

                        {{ $userInitials }}

                    </div>

                    <button class="tile-expand-btn" onclick="toggleMaximize('{{ auth()->id() }}')" title="Maximize / Minimize">

                        <i class="fa fa-expand" id="expand-icon-{{ auth()->id() }}"></i>

                    </button>

                </div>

                <div class="tile-info">

                    <div class="tile-name">

                        {{ auth()->user()->name }}

                        <span class="role-badge participant">Participant</span>

                        <span style="font-size:10px;background:rgba(59,130,246,0.3);padding:2px 6px;border-radius:99px;margin-left:4px;">You</span>

                    </div>

                    <div class="tile-icons">

                        <div class="speaking-indicator" id="speaking-{{ auth()->id() }}" style="display:none;">

                            <div class="speaking-bar"></div>

                            <div class="speaking-bar"></div>

                            <div class="speaking-bar"></div>

                        </div>

                        <div class="mic-off" id="micoff-{{ auth()->id() }}" style="display:flex;">

                            <i class="fa fa-microphone-slash"></i>

                        </div>

                    </div>

                </div>

                <div class="you-badge">You</div>

            </div>

        </div>

        {{-- MAXIMIZED TILE OVERLAY --}}

        <div id="maximized-overlay">

            <button class="maximize-close-btn" onclick="restoreMaximized()" title="Exit fullscreen">

                <i class="fa fa-compress"></i>

            </button>

        </div>

    </div>

    {{-- SIDE PANEL --}}

    <div class="transcript-panel" id="side-panel" style="display:none;">

        <div id="tab-transcript" style="display:flex;flex-direction:column;flex:1;overflow:hidden;">

            <div class="transcript-body" id="transcript-body">

                <div data-empty style="text-align:center;color:#64748b;font-size:12px;padding:20px;">

                    Transcript will appear here...

                </div>

            </div>

            <div style="display:flex;justify-content:flex-end;padding:8px 12px;border-bottom:1px solid var(--border);">

                <button onclick="toggleTranscriptLanguage()" id="lang-toggle-btn"

                        style="background:var(--surface2);border:1px solid var(--border);color:var(--muted);

                   font-size:11px;padding:4px 10px;border-radius:99px;cursor:pointer;">

                    🌐 English only

                </button>

            </div>

            <div class="listening-indicator" id="listening-indicator" style="display:none;">

                <div class="listening-dot"></div>

                <span id="listening-text">Listening...</span>

            </div>

        </div>

        <div id="tab-chat" class="panel-hidden" style="display:none;flex-direction:column;flex:1;overflow:hidden;">

            <div class="chat-body" id="chat-body">

                <div data-empty style="text-align:center;color:#64748b;font-size:12px;padding:20px;">

                    No messages yet...

                </div>

            </div>

            <div class="chat-input-area">

                <input class="chat-input" id="chat-input" placeholder="Type a message..."

                       onkeydown="if(event.key==='Enter') sendChat()" />

                <button class="btn-send" onclick="sendChat()">

                    <i class="fa fa-paper-plane"></i>

                </button>

            </div>

        </div>

        <div id="tab-participants" class="panel-hidden" style="display:none;flex:1;overflow-y:auto;padding:12px;">

            <div style="display:flex;flex-direction:column;gap:8px;" id="participants-list">

                {{-- Participant's own row --}}

                <div id="panel-row-{{ auth()->id() }}" class="participant-online" style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:12px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);">

                    <div style="width:36px;height:36px;border-radius:50%;

                        background:linear-gradient(135deg,{{ $colors[1] }});

                        display:flex;align-items:center;justify-content:center;

                        font-size:12px;font-weight:700;color:white;">

                        {{ $userInitials }}

                    </div>

                    <div style="flex:1;">

                        <div style="font-size:13px;font-weight:600;">

                            {{ auth()->user()->name }}

                            <span style="font-size:10px;color:#3b82f6;">(You)</span>

                        </div>

                        <div class="join-status" style="font-size:10px;color:var(--green);">Participant • Joined</div>

                    </div>

                    <span class="online-dot" style="width:8px;height:8px;background:var(--green);border-radius:50%;"></span>

                </div>

                {{-- Every other person (organizer + participants) gets a row via JS --}}

                <div id="other-participants-panel"></div>

            </div>

        </div>

    </div>

</div>

{{-- CONTROLS --}}

<div class="controls">

    <div class="ctrl-btn" onclick="toggleMic()">

        <div class="ctrl-icon" id="ctrl-mic">

            <i class="fa fa-microphone"></i>

        </div>

        <span class="ctrl-label">Mic</span>

    </div>

    <div class="ctrl-btn" onclick="toggleCamera()">

        <div class="ctrl-icon off" id="ctrl-camera">

            <i class="fa fa-video-slash"></i>

        </div>

        <span class="ctrl-label">Camera</span>

    </div>

    <div class="ctrl-divider"></div>

    <div class="ctrl-btn" onclick="toggleSidePanel('transcript', this)">

        <div class="ctrl-icon" id="ctrl-transcript">

            <i class="fa fa-closed-captioning"></i>

        </div>

        <span class="ctrl-label">Transcript</span>

    </div>

    <div class="ctrl-btn" onclick="toggleSidePanel('chat', this)" style="position:relative;">

        <div class="ctrl-icon" id="ctrl-chat" style="position:relative;">

            <i class="fa fa-comment"></i>

            <span id="chat-badge" style="display:none;position:absolute;top:-6px;right:-6px;background:var(--red,#ef4444);color:#fff;font-size:10px;font-weight:700;line-height:1;min-width:16px;height:16px;border-radius:99px;align-items:center;justify-content:center;padding:0 4px;">0</span>

        </div>

        <span class="ctrl-label">Chat</span>

    </div>

    <div class="ctrl-btn" onclick="toggleSidePanel('participants', this)">

        <div class="ctrl-icon" id="ctrl-people">

            <i class="fa fa-users"></i>

        </div>

        <span class="ctrl-label">People</span>

    </div>

    <div class="ctrl-divider"></div>

    <div class="ctrl-btn">

        <button class="btn-end" onclick="leaveMeeting()">

            <i class="fa fa-phone-slash"></i>

        </button>

        <span class="ctrl-label" style="color:var(--red);">Leave</span>

    </div>

</div>

{{-- TOAST CONTAINER --}}

<div id="toast-stack"></div>


<script>
    // ═══════════════════════════════════════════════════════════

    // PARTICIPANT — FINAL VERSION (v9 — INSTANT AUDIO HANDSHAKE)

    //

    // v8 FIXES (this pass, on top of v7):

    // Fix E: handleSignal('user-joined') now checks whether the user was

    //        already marked online *before* processing the event. Every

    //        time ANY connected user's tab reloads, their page re-fires

    //        announceJoin() on window 'load' — correct behavior for

    //        actually reconnecting, but it also meant everyone else got a

    //        fresh "✅ X has joined the meeting" toast even though X never

    //        actually left. We still reconcile the tile/peer/panel state

    //        (harmless, useful if anything had drifted), we just no

    //        longer show a toast for someone who was already known to be

    //        online. Genuine joins are unaffected.

    // Fix F: notifyDisconnectBeacon() (fired from pagehide/beforeunload —

    //        i.e. browser back/forward, tab close, or navigating away

    //        without clicking "Leave") now sends fetch(..., {keepalive:

    //        true}) FIRST, with the standard X-CSRF-TOKEN header, and

    //        falls back to sendBeacon(). sendBeacon() alone was not

    //        reliably reaching the server in every browser/navigation

    //        scenario, which is what let a departed user's video tile and

    //        "Joined" status in the People tab keep showing for everyone

    //        else — and even survive a refresh, since markLeft() never

    //        actually ran and joined_at/left_at were never updated in the

    //        DB. Both calls are guarded by the same one-shot

    //        "leftNotified" flag, and markLeft()/the toast de-dup logic

    //        are already idempotent, so it's safe even if both land.

    // ═══════════════════════════════════════════════════════════

    // ── CONFIG ──

    const MEETING_ID     = "{{ $meeting->id }}";

    const MY_USER_ID     = "{{ auth()->id() }}";

    const MY_NAME        = "{{ auth()->user()->name }}";

    const MY_INITIALS    = "{{ $userInitials }}";

    const SIGNAL_URL     = "{{ route('participant.meetings.signal', $meeting) }}";

    const TRANSCRIPT_URL = "{{ route('participant.meetings.transcript', $meeting) }}";

    const LEAVE_URL      = "{{ route('participant.meetings.index') }}";

    const MARK_LEFT_URL  = "{{ route('participant.meetings.markLeft', $meeting) }}";

    const CSRF           = "{{ csrf_token() }}";

    const ALL_USER_IDS   = @json($allUserIds);

    const ALREADY_JOINED = @json($alreadyJoined);

    const ALL_PARTICIPANTS = @json($allParticipants);

    const ORGANIZER_ID   = "{{ $organizer->id }}";

    const ORGANIZER_NAME = "{{ addslashes($organizer->name) }}";

    const ORGANIZER_INITIALS = "{{ $orgInitials }}";

    const ORGANIZER_JOINED = @json($organizerJoined ?? false);

    const MEETING_END_TIME = @json($meetingEnd); // UTC ISO string or null

    // ── KNOWN PARTICIPANTS ──

    const knownParticipants = {};

    knownParticipants[ORGANIZER_ID] = { name: ORGANIZER_NAME, initials: ORGANIZER_INITIALS, isOrganizer: true, hasJoined: ORGANIZER_JOINED };

    ALL_PARTICIPANTS.forEach(p => {

        knownParticipants[p.userId] = { name: p.name, initials: p.initials, isOrganizer: false, hasJoined: p.hasJoined };

    });

    // ── ONLINE USERS ──

    const onlineUsers = new Set([String(MY_USER_ID)]);

    const departedAnnounced = new Set();

    const leftUsers = new Set(); // users who explicitly left — block tile re-creation until they rejoin

    function markOnline(userId) {

        onlineUsers.add(String(userId));

        departedAnnounced.delete(String(userId));

        updateOnlineCount();

        updateParticipantRow(userId, true);

    }

    function markOffline(userId) {

        onlineUsers.delete(String(userId));

        updateOnlineCount();

        updateParticipantRow(userId, false);

    }

    function updateParticipantRow(userId, isOnline) {

        const row = document.getElementById('panel-row-' + userId);

        if (!row) return;

        if (isOnline) {

            row.className = 'participant-online';

            row.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px;margin-top:8px;border-radius:12px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);opacity:1;';

            const status = row.querySelector('.join-status');

            if (status) { status.textContent = status.textContent.replace('Not joined yet', 'Joined'); status.style.color = 'var(--green)'; }

            const dot = row.querySelector('.online-dot');

            if (dot) { dot.style.background = 'var(--green)'; dot.style.border = 'none'; }

        } else {

            row.className = 'participant-offline';

            row.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px;margin-top:8px;border-radius:12px;background:var(--surface2);border:1px solid var(--border);opacity:0.5;';

            const status = row.querySelector('.join-status');

            if (status) { status.textContent = status.textContent.replace('Joined', 'Not joined yet'); status.style.color = 'var(--muted)'; }

            const dot = row.querySelector('.online-dot');

            if (dot) { dot.style.background = 'var(--surface2)'; dot.style.border = '1px solid var(--border)'; }

        }

    }

    function updateOnlineCount() {

        const c = onlineUsers.size;

        document.querySelectorAll('[data-online-count]').forEach(el => el.textContent = c);

    }

    markOnline(MY_USER_ID);

    // ── TIMER ──

    const ACTUAL_START = "{{ $meeting->actual_start ? \Carbon\Carbon::parse($meeting->actual_start)->utc()->toIso8601String() : now()->utc()->toIso8601String() }}";

    let seconds = Math.floor((Date.now() - new Date(ACTUAL_START).getTime()) / 1000);

    if (seconds < 0) seconds = 0;

    setInterval(() => {

        seconds++;

        const h = String(Math.floor(seconds / 3600)).padStart(2,'0');

        const m = String(Math.floor((seconds % 3600) / 60)).padStart(2,'0');

        const s = String(seconds % 60).padStart(2,'0');

        document.getElementById('timer').textContent = `${h}:${m}:${s}`;

    }, 1000);

    // ── AUTO-END WHEN MEETING TIME IS UP ──

    let autoEndTimer = null;

    function scheduleAutoEnd() {

        if (!MEETING_END_TIME) return; // no end-time info available — skip silently

        const msLeft = new Date(MEETING_END_TIME).getTime() - Date.now();

        if (msLeft <= 0) { triggerAutoEnd(); return; }

        autoEndTimer = setTimeout(triggerAutoEnd, msLeft);

    }

    let autoEndTriggered = false;

    async function triggerAutoEnd() {

        if (autoEndTriggered) return;

        autoEndTriggered = true;

        showToast('⏰ Meeting time has ended.');

        setTimeout(() => { cleanup(); window.location.href = LEAVE_URL; }, 1800);

    }

    // ── CHAT UNREAD BADGE ──

    let unreadChat = 0;

    let activeTab = null;

    let panelOpen = false;

    function updateChatBadge() {

        const badge = document.getElementById('chat-badge');

        if (!badge) return;

        if (unreadChat > 0) { badge.textContent = unreadChat > 99 ? '99+' : String(unreadChat); badge.style.display = 'flex'; }

        else { badge.style.display = 'none'; }

    }

    function switchTab(tab) {

        ['transcript','chat','participants'].forEach(t => {

            const el = document.getElementById('tab-' + t);

            if (el) { el.style.display = 'none'; el.classList.add('panel-hidden'); }

        });

        document.querySelectorAll('.ctrl-icon').forEach(t => t.classList.remove('active'));

        const active = document.getElementById('tab-' + tab);

        if (active) { active.style.display = tab === 'participants' ? 'block' : 'flex'; active.classList.remove('panel-hidden'); }

        activeTab = tab;

        const icon = document.getElementById('ctrl-' + tab);

        if (icon) icon.classList.add('active');

        if (tab === 'chat') { unreadChat = 0; updateChatBadge(); }

    }

    function toggleSidePanel(tab) {

        const panel = document.getElementById('side-panel');

        if (!panel) return;

        if (panelOpen && activeTab === tab) {

            panel.style.display = 'none'; panelOpen = false; activeTab = null;

            document.querySelectorAll('.ctrl-icon').forEach(t => t.classList.remove('active'));

            return;

        }

        panel.style.removeProperty('display');

        panelOpen = true;

        switchTab(tab);

    }

    // ── WEBRTC ──

    let localStream = null;

    let peers = {};

    let pendingCandidates = {};

    let makingOffer = {};

    let ignoreOffer = {};   // tracks peers whose incoming offer we intentionally dropped (impolite + collision)

    let isMicOn = false;

    let isCameraOn = false;

    let recognition = null;

    let currentLang = 'auto';

    let recognitionRunning = false;

    const participantMicStatus = {};

    const participantCameraStatus = {};

    const offlineTimers = {};

    // ── MAXIMIZE / ENLARGE TILE (local UI only — never touches WebRTC/signaling) ──

    let maximizedUserId = null;

    let maximizedPlaceholder = null;

    function toggleMaximize(userId) {

        userId = String(userId);

        const overlay = document.getElementById('maximized-overlay');

        const grid = document.getElementById('video-grid');

        if (!overlay || !grid) return;

        if (maximizedUserId === userId) { restoreMaximized(); return; }

        if (maximizedUserId) restoreMaximized();

        const tile = document.getElementById('tile-' + userId);

        if (!tile) return;

        maximizedPlaceholder = document.createComment('tile-placeholder-' + userId);

        tile.parentNode.insertBefore(maximizedPlaceholder, tile);

        overlay.appendChild(tile);

        overlay.classList.add('active');

        tile.classList.add('maximized');

        maximizedUserId = userId;

        updateExpandIcons();

    }

    function restoreMaximized() {

        if (!maximizedUserId) return;

        const tile = document.getElementById('tile-' + maximizedUserId);

        const overlay = document.getElementById('maximized-overlay');

        const grid = document.getElementById('video-grid');

        if (tile) {

            if (maximizedPlaceholder && maximizedPlaceholder.parentNode) {

                maximizedPlaceholder.parentNode.insertBefore(tile, maximizedPlaceholder);

                maximizedPlaceholder.remove();

            } else if (grid) {

                grid.appendChild(tile);

            }

            tile.classList.remove('maximized');

        }

        if (overlay) overlay.classList.remove('active');

        maximizedPlaceholder = null;

        maximizedUserId = null;

        updateExpandIcons();

    }

    function updateExpandIcons() {

        document.querySelectorAll('.tile-expand-btn i[id^="expand-icon-"]').forEach(icon => {

            const id = icon.id.replace('expand-icon-', '');

            icon.className = (maximizedUserId === id) ? 'fa fa-compress' : 'fa fa-expand';

        });

    }
    /* ============================================================
       ICE / STUN / TURN — AWS production configuration
       IMPORTANT:
       Replace ONLY PASTE_YOUR_TURN_PASSWORD_HERE below with the
       same password used in /etc/turnserver.conf:
           user=smartmeet:YOUR_PASSWORD
    ============================================================ */
    const TURN_HOST = 'smartmeet.live';
    const TURN_IP = '13.203.230.232';
    const TURN_USERNAME = 'smartmeet';
    const TURN_CREDENTIAL = 'PASTE_YOUR_TURN_PASSWORD_HERE';

    const iceServers = [
        {
            urls: [
                'stun:stun.l.google.com:19302',
                'stun:stun1.l.google.com:19302'
            ]
        },
        {
            urls: [
                `turn:${TURN_HOST}:3478?transport=udp`,
                `turn:${TURN_HOST}:3478?transport=tcp`,
                `turn:${TURN_IP}:3478?transport=udp`,
                `turn:${TURN_IP}:3478?transport=tcp`
            ],
            username: TURN_USERNAME,
            credential: TURN_CREDENTIAL
        }
    ];

    const iceConfig = {
        iceServers,
        iceCandidatePoolSize: 10,
        iceTransportPolicy: 'all',
        bundlePolicy: 'max-bundle',
        rtcpMuxPolicy: 'require'
    };

    console.info('SmartMeet ICE ready: STUN + TURN configured for cross-network audio/video.');

    function isPolite(otherUserId) {

        const a = Number(MY_USER_ID), b = Number(otherUserId);

        if (!Number.isNaN(a) && !Number.isNaN(b)) return a < b;

        return String(MY_USER_ID) < String(otherUserId);

    }

    function shouldInitiatePeer(otherUserId) {
        const mine = String(MY_USER_ID);
        const other = String(otherUserId);
        if (mine === other) return false;
        const a = Number(mine), b = Number(other);
        if (!Number.isNaN(a) && !Number.isNaN(b)) return a < b;
        return mine.localeCompare(other) < 0;
    }

    function broadcastMyMicStatus() {

        sendSignal('all', 'mic-status', { userId: MY_USER_ID, muted: !isMicOn });

    }

    function broadcastMyCameraStatus() {

        sendSignal('all', 'camera-status', { userId: MY_USER_ID, cameraOn: isCameraOn });

    }



    // ═══════════════════════════════════════════════════════════
    // AUDIO RELIABILITY / ECHO CONTROL / IDLE RECOVERY
    // ═══════════════════════════════════════════════════════════
    const audioRecoveryTimers = {};
    let mediaStartPromise = null;
    let lastAudioUnlockNotice = 0;

    const preferredAudioConstraints = {
        echoCancellation: true,
        noiseSuppression: true,
        autoGainControl: true,
        channelCount: 1,
        sampleRate: 48000,
        sampleSize: 16,
        latency: 0
    };

    async function applyBestAudioConstraints(stream) {
        const track = stream?.getAudioTracks?.()[0];
        if (!track?.applyConstraints) return;
        try { await track.applyConstraints(preferredAudioConstraints); }
        catch (error) { console.warn('Advanced audio constraints unavailable:', error); }
        try { track.contentHint = 'speech'; } catch (e) {}
    }

    async function safelyPlayRemoteAudio(audio) {
        if (!audio) return;
        audio.autoplay = true;
        audio.playsInline = true;
        audio.muted = false;
        audio.defaultMuted = false;
        audio.volume = 1;
        try { audio.setSinkId && audio.setSinkId('default'); } catch (e) {}
        try { await audio.play(); }
        catch (error) {
            const now = Date.now();
            if (now - lastAudioUnlockNotice > 5000) {
                lastAudioUnlockNotice = now;
                showToast('🔊 Tap anywhere once to enable meeting audio.');
            }
            const unlock = async () => {
                try { await audio.play(); } catch (e) {}
                document.removeEventListener('pointerdown', unlock);
                document.removeEventListener('keydown', unlock);
            };
            document.addEventListener('pointerdown', unlock, { once: true });
            document.addEventListener('keydown', unlock, { once: true });
        }
    }

    function closePeerCompletely(userId) {
        const uid = String(userId);
        if (audioRecoveryTimers[uid]) { clearTimeout(audioRecoveryTimers[uid]); delete audioRecoveryTimers[uid]; }
        const pc = peers[uid];
        if (pc) {
            try { pc.ontrack = null; pc.onicecandidate = null; pc.onnegotiationneeded = null; pc.close(); } catch (e) {}
            if (peers[uid] === pc) delete peers[uid];
        }
        delete pendingCandidates[uid];
        delete makingOffer[uid];
        delete ignoreOffer[uid];
    }

    async function restartPeerConnection(userId, reason = 'recovery') {
        const uid = String(userId);
        if (uid === String(MY_USER_ID) || leftUsers.has(uid) || !knownParticipants[uid]) return;
        closePeerCompletely(uid);
        createPeerConnection(uid);
        await syncLocalTracksToPeer(uid);
        if (shouldInitiatePeer(uid)) {
            queuePeerNegotiation(uid, { reason, iceRestart: true, force: true, delay: 10 });
        } else {
            sendSignal(uid, 'user-joined', { userId: MY_USER_ID, name: MY_NAME, initials: MY_INITIALS, recovery: true, reason });
        }
    }

    function schedulePeerRecovery(userId, reason, delay = 1400) {
        const uid = String(userId);
        if (audioRecoveryTimers[uid]) clearTimeout(audioRecoveryTimers[uid]);
        audioRecoveryTimers[uid] = setTimeout(() => {
            delete audioRecoveryTimers[uid];
            restartPeerConnection(uid, reason);
        }, delay);
    }

    function recoverAllJoinedPeers(reason = 'resume') {
        Object.keys(knownParticipants).forEach(uid => {
            if (uid !== String(MY_USER_ID) && (knownParticipants[uid]?.hasJoined || onlineUsers.has(String(uid)))) {
                const pc = peers[uid];
                const hasInboundAudio = Boolean(
                    pc?.getReceivers?.().some(receiver =>
                        receiver.track?.kind === 'audio' &&
                        receiver.track.readyState !== 'ended'
                    )
                );

                if (!pc || ['failed', 'closed', 'disconnected'].includes(pc.connectionState) ||
                    ['failed', 'closed', 'disconnected'].includes(pc.iceConnectionState) ||
                    !hasInboundAudio) {
                    schedulePeerRecovery(uid, reason, 250);
                } else {
                    attachRemoteStream(uid);
                    safelyPlayRemoteAudio(document.getElementById('audio-' + uid));
                }
            }
        });
    }

    window.addEventListener('online', () => recoverAllJoinedPeers('network-online'));
    window.addEventListener('pageshow', () => recoverAllJoinedPeers('page-show'));
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            setTimeout(() => recoverAllJoinedPeers('tab-visible'), 300);
            if (isMicOn) startRecognition();
        }
    });
    setInterval(() => recoverAllJoinedPeers('periodic-health-check'), 3000);



    // ═══════════════════════════════════════════════════════════
    // RELIABLE MULTI-DEVICE WEBRTC TRACK SYNC
    // ═══════════════════════════════════════════════════════════
    const remoteStreams = {};
    const negotiationTimers = {};
    const negotiatingPeers = {};

    function getOrCreateRemoteStream(userId) {
        const uid = String(userId);
        if (!remoteStreams[uid]) remoteStreams[uid] = new MediaStream();
        return remoteStreams[uid];
    }

    function attachRemoteStream(userId) {
        const uid = String(userId);
        if (uid === String(MY_USER_ID)) {
            const ownAudio = document.getElementById('audio-' + uid);
            if (ownAudio) { try { ownAudio.pause(); } catch (e) {} ownAudio.srcObject = null; ownAudio.remove(); }
            document.querySelectorAll(`audio[data-peer-id="${uid}"]`).forEach(el => { try { el.pause(); } catch (e) {} el.srcObject = null; el.remove(); });
            return;
        }
        const sourceStream = getOrCreateRemoteStream(uid);
        const localIds = new Set((localStream?.getTracks?.() || []).map(t => t.id));
        const audioTracks = sourceStream.getAudioTracks().filter(t => t.readyState !== 'ended' && !localIds.has(t.id));
        document.querySelectorAll(`audio[data-peer-id="${uid}"]`).forEach((el,i) => { if (i>0) { try { el.pause(); } catch(e) {} el.srcObject=null; el.remove(); } });
        let audio = document.getElementById('audio-' + uid);
        if (!audio) {
            audio = document.createElement('audio'); audio.id='audio-'+uid; audio.autoplay=true; audio.playsInline=true; audio.preload='auto';
            audio.muted=false; audio.defaultMuted=false; audio.volume=1; audio.dataset.peerId=uid; audio.style.display='none'; document.body.appendChild(audio);
        }
        audio.srcObject = new MediaStream(audioTracks); audio.muted=false; audio.defaultMuted=false; audio.volume=1;
        if (audioTracks.length) safelyPlayRemoteAudio(audio);
        const videoTracks = sourceStream.getVideoTracks().filter(t => t.readyState !== 'ended' && !localIds.has(t.id));
        const video = document.getElementById('rvideo-' + uid);
        if (video) { video.srcObject = new MediaStream(videoTracks); video.muted=true; video.playsInline=true; if (videoTracks.length) video.play().catch(()=>{}); }
    }

    async function syncLocalTracksToPeer(userId) {
        const uid = String(userId);
        const pc = peers[uid];
        if (!pc || pc.signalingState === 'closed' || !localStream) return false;

        let changed = false;
        const localTracks = localStream.getTracks();

        for (const kind of ['audio', 'video']) {
            const track = localTracks.find(item => item.kind === kind) || null;
            const sender = pc.getSenders().find(item => item.track?.kind === kind);

            if (sender) {
                if (sender.track !== track) {
                    await sender.replaceTrack(track).catch(error => {
                        console.warn('replaceTrack failed:', uid, kind, error);
                    });
                    changed = true;
                }
            } else if (track) {
                pc.addTrack(track, localStream);
                changed = true;
            }
        }

        return changed;
    }

    async function negotiatePeer(userId, options = {}) {
        const uid = String(userId);
        const pc = peers[uid];
        if (!pc || pc.signalingState === 'closed' || leftUsers.has(uid)) return;
        if (!shouldInitiatePeer(uid) && !options.force) return;
        if (negotiatingPeers[uid] || makingOffer[uid]) return;

        try {
            negotiatingPeers[uid] = true;
            makingOffer[uid] = true;

            await syncLocalTracksToPeer(uid);

            if (pc.signalingState !== 'stable') return;

            const offer = await pc.createOffer({
                iceRestart: Boolean(options.iceRestart)
            });

            if (pc.signalingState !== 'stable') return;

            await pc.setLocalDescription(offer);

            await sendSignal(uid, 'offer', {
                type: pc.localDescription.type,
                sdp: btoa(unescape(encodeURIComponent(pc.localDescription.sdp))),
                iceRestart: Boolean(options.iceRestart),
                reason: options.reason || 'track-sync'
            });
        } catch (error) {
            console.warn('Peer negotiation failed:', uid, error);
        } finally {
            makingOffer[uid] = false;
            negotiatingPeers[uid] = false;
        }
    }

    function queuePeerNegotiation(userId, options = {}) {
        const uid = String(userId);
        if (negotiationTimers[uid]) clearTimeout(negotiationTimers[uid]);

        negotiationTimers[uid] = setTimeout(() => {
            delete negotiationTimers[uid];
            negotiatePeer(uid, options);
        }, options.delay ?? 120);
    }

    async function syncTracksToEveryPeer(forceNegotiation = false) {
        const tasks = Object.keys(peers).map(async uid => {
            const changed = await syncLocalTracksToPeer(uid);
            if (changed || forceNegotiation) {
                queuePeerNegotiation(uid, {
                    reason: 'local-track-change',
                    force: forceNegotiation,
                    delay: 35
                });
            }
        });

        await Promise.allSettled(tasks);
    }


    // ── START ──

    window.addEventListener('load', async () => {
        await Promise.all([listenForSignals(), startAudio()]);
        scheduleAutoEnd();


        renderAllParticipants();

        [0, 500, 1500, 3500].forEach(delay => setTimeout(() => {
            announceJoin();
            connectToAll();
            syncTracksToEveryPeer(false);
        }, delay));
    });

    function renderAllParticipants() {

        ensurePanelRow(ORGANIZER_ID, ORGANIZER_NAME, ORGANIZER_INITIALS, true);

        // Show the organizer whenever the server says they are currently joined.
        // A later live user-joined event also creates this tile immediately.
        if (Boolean(ORGANIZER_JOINED)) {
            leftUsers.delete(String(ORGANIZER_ID));
            addParticipantTile(
                String(ORGANIZER_ID),
                ORGANIZER_NAME,
                ORGANIZER_INITIALS,
                true
            );
            markOnline(String(ORGANIZER_ID));
            createPeerConnection(String(ORGANIZER_ID));
        }

        ALL_PARTICIPANTS.forEach(p => {

            ensurePanelRow(p.userId, p.name, p.initials, false);
            // Other participants are shown in People immediately, but their
            // video tile is created only after a live realtime signal/ontrack.


        });

    }

    function announceJoin() {

        sendSignal('all', 'user-joined', { userId: MY_USER_ID, name: MY_NAME, initials: MY_INITIALS });

    }

    // ── MIC + CAMERA ACCESS ──

    async function startAudio() {
        if (mediaStartPromise) return mediaStartPromise;

        mediaStartPromise = (async () => {
            try {
                const audioStream = await navigator.mediaDevices.getUserMedia({
                    audio: preferredAudioConstraints,
                    video: false
                });
                await applyBestAudioConstraints(audioStream);

                localStream = new MediaStream();
                audioStream.getAudioTracks().forEach(track => {
                    track.enabled = false;
                    localStream.addTrack(track);
                });
                isMicOn = false;

                // Camera is intentionally NOT requested during room startup.
                // It is requested only after the user clicks Camera, so a
                // temporary permission/device error can never remove the button.

                isCameraOn = false;
                const localVideo = document.getElementById('localVideo');
                if (localVideo) {
                    localVideo.srcObject = localStream;
                    localVideo.muted = true;
                    localVideo.playsInline = true;
                    localVideo.play().catch(() => {});
                }

                const micBtn = document.getElementById('ctrl-mic');
                const micOff = document.getElementById('micoff-' + MY_USER_ID);
                if (micBtn) {
                    micBtn.innerHTML = '<i class="fa fa-microphone-slash"></i>';
                    micBtn.classList.add('off');
                }
                if (micOff) micOff.style.display = 'flex';

                Object.entries(peers).forEach(([uid, pc]) => {
                    if (!pc || pc.signalingState === 'closed') return;
                    const audioTrack = localStream.getAudioTracks()[0];
                    const sender = pc.getSenders().find(item => item.track?.kind === 'audio');
                    if (sender && audioTrack) sender.replaceTrack(audioTrack).catch(console.warn);
                    else if (audioTrack) pc.addTrack(audioTrack, localStream);
                });

                startTranscript();
                stopRecognition();
                broadcastMyMicStatus();
            } catch (error) {
                console.error('Microphone access failed:', error);
                isMicOn = false;
                const micBtn = document.getElementById('ctrl-mic');
                if (micBtn) {
                    micBtn.innerHTML = '<i class="fa fa-microphone-slash"></i>';
                    micBtn.classList.add('off');
                }
                if (error.name === 'NotAllowedError') {
                    showToast('🎙️ Microphone is blocked. Allow it in browser Site settings, then reload.');
                } else if (error.name === 'NotFoundError') {
                    showToast('🎙️ No microphone was found on this device.');
                } else {
                    showToast('🎙️ Meeting audio could not start. Please check your microphone and reload.');
                }
            } finally {
                mediaStartPromise = null;
            }
        })();
        return mediaStartPromise;
    }

    // ── LISTEN FOR SIGNALS ──

    function listenForSignals() {
        return new Promise((resolve) => {
            if (typeof window.Echo === 'undefined') { console.error('Echo not initialized'); resolve(false); return; }
            const channel = window.Echo.channel('meeting.' + MEETING_ID);
            let done = false;
            const finish = value => { if (!done) { done = true; resolve(value); } };
            channel.listen('.signal', handleSignal);
            channel.listen('.transcript', handleTranscript);
            if (typeof channel.subscribed === 'function') channel.subscribed(() => finish(true));
            if (typeof channel.error === 'function') channel.error(error => { console.error('Meeting channel subscription failed:', error); finish(false); });
            setTimeout(() => finish(true), 1200);
        });
    }

    function handleTranscript(data) {

        if (String(data.userId) === String(MY_USER_ID)) return;

        const body = document.getElementById('transcript-body');

        if (!body) return;

        body.querySelector('[data-empty]')?.remove();

        const div = document.createElement('div');

        div.className = 'transcript-entry';

        div.innerHTML = `

        <div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);">${escapeHtml(data.userInitials || '?')}</div>

        <div class="transcript-content">

            <div class="transcript-meta">

                <span class="transcript-name">${escapeHtml(data.userName || 'User')}</span>

                <span class="transcript-time">${data.spokenAt || ''}</span>

            </div>

            <div class="transcript-text">${escapeHtml(data.text || '')}</div>

        </div>`;

        body.appendChild(div);

        body.scrollTop = body.scrollHeight;

    }

    // ── PEER CONNECTION ──

    function createPeerConnection(userId) {

        let pc = peers[userId];

        if (pc && pc.connectionState !== 'closed') return pc;

        if (pc) { try { pc.close(); } catch (e) {} }

        pc = new RTCPeerConnection(iceConfig);

        peers[userId] = pc;

        if (localStream) {
            localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
        }

        setTimeout(() => {
            syncLocalTracksToPeer(userId).finally(() => {
                if (shouldInitiatePeer(userId)) {
                    queuePeerNegotiation(userId, { reason: 'peer-created', delay: 20 });
                }
            });
        }, 0);

        pc.onnegotiationneeded = () => {
            if (!shouldInitiatePeer(userId)) return;
            queuePeerNegotiation(userId, { reason: 'negotiation-needed', delay: 20 });
        };

        pc.ontrack = (event) => {
            const uid = String(userId);
            if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
            const localIds = new Set((localStream?.getTracks?.() || []).map(t => t.id));
            if (localIds.has(event.track.id)) { console.warn('Blocked looped-back local track:', uid, event.track.kind); return; }
            try {
                if ('playoutDelayHint' in event.receiver) event.receiver.playoutDelayHint = 0;
                if ('jitterBufferTarget' in event.receiver) event.receiver.jitterBufferTarget = 0;
            } catch (e) {}

            ensureParticipantTileVisible(uid);

            const remoteStream = getOrCreateRemoteStream(uid);

            if (!remoteStream.getTracks().some(track => track.id === event.track.id)) {
                remoteStream.addTrack(event.track);
            }

            event.track.onunmute = () => {
                attachRemoteStream(uid);
            };

            event.track.onended = () => {
                const stream = remoteStreams[uid];
                const existing = stream?.getTracks().find(track => track.id === event.track.id);
                if (existing) stream.removeTrack(existing);
            };

            attachRemoteStream(uid);

            const video = document.getElementById('rvideo-' + uid);
            const avatar = document.getElementById('avatar-' + uid);

            if (event.track.kind === 'video' && participantCameraStatus[uid]) {
                if (video) video.style.display = 'block';
                if (avatar) avatar.style.display = 'none';
            }
        };

        pc.onicecandidate = (event) => { if (event.candidate) sendSignal(userId, 'ice-candidate', { candidate: event.candidate.toJSON() }); };

        pc.oniceconnectionstatechange = () => {

            const state = pc.iceConnectionState;

            if (state === 'failed') {

                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }

                removeParticipantTileSilently(userId, true);
                try { pc.close(); } catch (e) {}
                if (peers[userId] === pc) delete peers[userId];
                schedulePeerRecovery(userId, 'ice-failed', 450);

            } else if (state === 'disconnected') {

                if (offlineTimers[userId]) clearTimeout(offlineTimers[userId]);

                offlineTimers[userId] = setTimeout(() => {

                    const cur = peers[userId];

                    if (!cur || ['disconnected', 'failed', 'closed'].includes(cur.iceConnectionState)) removeParticipantTileSilently(userId, true);

                    delete offlineTimers[userId];

                    schedulePeerRecovery(userId, 'ice-disconnected', 900);

                }, 3500);

            } else if (state === 'connected' || state === 'completed') {

                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }

                ensureParticipantTileVisible(userId);
                attachRemoteStream(userId);
                syncLocalTracksToPeer(userId);

                broadcastMyMicStatus();

                broadcastMyCameraStatus();

            }

        };

        pc.onconnectionstatechange = () => {

            if (pc.connectionState === 'closed') {

                if (peers[userId] === pc) delete peers[userId];

                removeParticipantTileSilently(userId, false);

            }

        };

        return pc;

    }

    function decodeSdp(sdp) { if (!sdp) return ''; try { return decodeURIComponent(escape(atob(sdp))); } catch(e) { return sdp; } }

    function removeParticipantTileSilently(userId, announce) {

        if (String(userId) === String(maximizedUserId)) {

            const overlay = document.getElementById('maximized-overlay');

            if (overlay) overlay.classList.remove('active');

            if (maximizedPlaceholder && maximizedPlaceholder.parentNode) maximizedPlaceholder.remove();

            maximizedPlaceholder = null;

            maximizedUserId = null;

        }

        const tile = document.getElementById('tile-' + userId);

        if (tile) tile.remove();

        markOffline(userId);

        if (knownParticipants[String(userId)]) knownParticipants[String(userId)].hasJoined = false;

        if (announce && !departedAnnounced.has(String(userId))) {

            departedAnnounced.add(String(userId));

            const info = knownParticipants[String(userId)];

            showToast(`⚠️ ${escapeHtml(info ? info.name : 'A participant')} has disconnected.`);

        }

    }

    function ensureParticipantTileVisible(userId) {

        const uid = String(userId);

        if (leftUsers.has(uid)) return; // they already left — don't resurrect their tile

        const info = knownParticipants[uid];

        if (info) {

            info.hasJoined = true;

            addParticipantTile(userId, info.name, info.initials, info.isOrganizer || false);

            markOnline(userId);

        }

    }

    // ── HANDLE SIGNAL ──

    async function handleSignal(data) {

        const from = String(data.fromUserId);

        const isSelf = from === String(MY_USER_ID);

        const incomingSignalId = data.data?._signalId || '';
        if (incomingSignalId) {
            if (receivedSignalIds.has(incomingSignalId)) {
                return;
            }

            receivedSignalIds.add(incomingSignalId);

            if (receivedSignalIds.size > 1000) {
                receivedSignalIds.clear();
                receivedSignalIds.add(incomingSignalId);
            }
        }

        if (isSelf && !['meeting-cancelled', 'meeting-ended'].includes(data.type)) return;

        if (data.type === 'meeting-cancelled') {

            showToast('⚠️ Meeting has been cancelled by the organizer.');

            setTimeout(() => { cleanup(); window.location.href = LEAVE_URL; }, 2500);

            return;

        }

        if (data.type === 'meeting-ended') {

            const msg = data.data?.auto ? '⏰ Meeting time has ended.' : '📞 Meeting has ended.';

            showToast(msg);

            setTimeout(() => { cleanup(); window.location.href = LEAVE_URL; }, 2500);

            return;

        }

        if (data.type === 'user-joined') {

            const joinedId = String(data.data.userId);

            if (joinedId === String(MY_USER_ID)) return;

            // Fix E: only announce a "has joined" toast the first time we

            // see this user online. A refresh on their end re-fires this

            // exact same event even though they never left, so without

            // this check everyone else got a duplicate "has joined" toast

            // every single time someone reloaded their tab.

            const wasAlreadyOnline = onlineUsers.has(joinedId);

            leftUsers.delete(joinedId); // rejoin clears the "left" flag

            if (!knownParticipants[joinedId]) {

                knownParticipants[joinedId] = { name: data.data.name, initials: data.data.initials, isOrganizer: joinedId === ORGANIZER_ID, hasJoined: true };

            } else {

                knownParticipants[joinedId].hasJoined = true;

            }

            if (!ALL_USER_IDS.map(String).includes(joinedId)) ALL_USER_IDS.push(joinedId);

            ensurePanelRow(joinedId, data.data.name, data.data.initials, joinedId === ORGANIZER_ID);

            addParticipantTile(joinedId, data.data.name, data.data.initials, joinedId === ORGANIZER_ID);

            markOnline(joinedId);

            createPeerConnection(joinedId);

            setTimeout(async () => {
                await syncLocalTracksToPeer(joinedId);
                if (shouldInitiatePeer(joinedId)) {
                    queuePeerNegotiation(joinedId, { reason: 'user-joined', delay: 20 });
                }
            }, 20);

            if (!wasAlreadyOnline) {

                showToast(`✅ ${escapeHtml(data.data.name)} has joined the meeting.`);

            }

            sendSignal(joinedId, 'mic-status', { userId: MY_USER_ID, muted: !isMicOn });

            sendSignal(joinedId, 'camera-status', { userId: MY_USER_ID, cameraOn: isCameraOn });

            return;

        }

        if (data.type === 'user-left') {

            // ── FIX: ignore our own echoed-back "user-left" broadcast.

            // The server broadcasts this type to everyone (no ->toOthers

            // effect over fetch/sendBeacon), so without this guard the

            // person who just left would briefly see their own name in a

            // "has left the meeting" toast — stacked on top of their own

            // local "You have left the meeting" message.

            if (isSelf) return;

            leftUsers.add(from); // mark as left so late ICE/ontrack events can't re-add the tile

            if (offlineTimers[from]) { clearTimeout(offlineTimers[from]); delete offlineTimers[from]; }

            removeParticipantTileSilently(from, false);

            if (peers[from]) { peers[from].close(); delete peers[from]; }

            delete pendingCandidates[from];

            if (!departedAnnounced.has(from)) {

                departedAnnounced.add(from);

                const name = data.data?.name || (knownParticipants[from] && knownParticipants[from].name) || 'A participant';

                showToast(`👋 ${escapeHtml(name)} has left the meeting.`);

            }

            return;

        }

        if (data.type === 'chat') {

            if (isSelf) return;

            const name = data.data?.name || 'User';

            const text = data.data?.text || '';

            if (!text) return;

            addChatBubble(name, text, false);

            if (activeTab !== 'chat') { unreadChat++; updateChatBadge(); }

            return;

        }

        if (data.type === 'mic-status') {

            const uid = String(data.data.userId || data.fromUserId);

            const liveInfo = knownParticipants[uid];
            if (
                liveInfo &&
                uid !== String(MY_USER_ID) &&
                !leftUsers.has(uid)
            ) {
                liveInfo.hasJoined = true;
                addParticipantTile(
                    uid,
                    liveInfo.name,
                    liveInfo.initials,
                    Boolean(liveInfo.isOrganizer)
                );
                markOnline(uid);
            }


            if (uid === String(MY_USER_ID)) return;

            participantMicStatus[uid] = data.data.muted;

            const micOff = document.getElementById('micoff-' + uid);

            if (micOff) micOff.style.display = data.data.muted ? 'flex' : 'none';

            return;

        }

        if (data.type === 'camera-status') {

            const uid = String(data.data.userId || data.fromUserId);

            const liveInfo = knownParticipants[uid];
            if (
                liveInfo &&
                uid !== String(MY_USER_ID) &&
                !leftUsers.has(uid)
            ) {
                liveInfo.hasJoined = true;
                addParticipantTile(
                    uid,
                    liveInfo.name,
                    liveInfo.initials,
                    Boolean(liveInfo.isOrganizer)
                );
                markOnline(uid);
            }


            if (uid === String(MY_USER_ID)) return;

            participantCameraStatus[uid] = data.data.cameraOn;

            const video = document.getElementById('rvideo-' + uid);

            const avatar = document.getElementById('avatar-' + uid);

            if (video) video.style.display = data.data.cameraOn ? 'block' : 'none';

            if (avatar) avatar.style.display = data.data.cameraOn ? 'none' : 'flex';

            return;

        }

        if (String(data.toUserId) !== String(MY_USER_ID)) return;

        if (!data.data) return;

        if (leftUsers.has(from) && ['offer', 'ice-candidate'].includes(data.type)) return; // ignore stale signals from a departed user

        try {

            if (data.type === 'offer') {

                const pc = createPeerConnection(from);

                const polite = isPolite(from);

                const offerCollision = (makingOffer[from]) || (pc.signalingState !== 'stable');

                ignoreOffer[from] = !polite && offerCollision;

                if (ignoreOffer[from]) return;

                const sdp = decodeSdp(data.data.sdp);

                await pc.setRemoteDescription(new RTCSessionDescription({ type: data.data.type || 'offer', sdp }));

                // Guarantee that our audio-only track and any active camera
                // track are attached before generating the answer.
                await syncLocalTracksToPeer(from);

                if (pendingCandidates[from]?.length) {

                    for (const c of pendingCandidates[from]) await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});

                    delete pendingCandidates[from];

                }

                await pc.setLocalDescription();

                sendSignal(from, 'answer', { type: pc.localDescription.type, sdp: btoa(unescape(encodeURIComponent(pc.localDescription.sdp))) });

            } else if (data.type === 'answer') {

                const pc = peers[from];

                if (!pc) return;

                const sdp = decodeSdp(data.data.sdp);

                if (pc.signalingState === 'have-local-offer') {

                    await pc.setRemoteDescription(new RTCSessionDescription({ type: data.data.type || 'answer', sdp }));

                    if (pendingCandidates[from]?.length) {

                        for (const c of pendingCandidates[from]) await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});

                        delete pendingCandidates[from];

                    }

                }

            } else if (data.type === 'ice-candidate') {

                const candidate = data.data.candidate;

                if (!candidate) return;

                const pc = peers[from];

                if (!pc || !pc.remoteDescription) {

                    if (!pendingCandidates[from]) pendingCandidates[from] = [];

                    pendingCandidates[from].push(candidate);

                    return;

                }

                try {

                    await pc.addIceCandidate(new RTCIceCandidate(candidate));

                } catch (err) {

                    if (!ignoreOffer[from]) console.error('ICE candidate error:', err);

                }

            } else if (data.type === 'mute') {

                if (!localStream) return;

                await applyBestAudioConstraints(localStream);

                localStream.getAudioTracks().forEach(t => t.enabled = false);

                isMicOn = false;

                const btn = document.getElementById('ctrl-mic');

                const micOff = document.getElementById('micoff-' + MY_USER_ID);

                if (btn) { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }

                if (micOff) micOff.style.display = 'flex';

                stopRecognition();

                showModerationNotice('🎙️ Your microphone was muted by the organizer.');

                broadcastMyMicStatus();

            } else if (data.type === 'unmute') {

                showModerationNotice('🎙️ The organizer has allowed your microphone. Tap Mic when you are ready to speak.');

            }

        } catch (err) { console.error('Signal handle error:', err); }

    }


    const receivedSignalIds = new Set();

    function makeSignalId(type) {
        return String(MY_USER_ID)
            + ':' + String(type)
            + ':' + Date.now()
            + ':' + Math.random().toString(36).slice(2, 8);
    }

    async function postSignalReliable(toUserId, type, payload, attempts = 3) {
        for (let attempt = 0; attempt < attempts; attempt++) {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 6500);

            try {
                const response = await fetch(SIGNAL_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        to_user_id: toUserId,
                        type,
                        data: payload
                    }),
                    signal: controller.signal
                });

                if (response.ok) {
                    return true;
                }
            } catch (error) {
                // Temporary network failure. Retry the SAME signal id.
            } finally {
                clearTimeout(timeout);
            }

            if (attempt < attempts - 1) {
                await new Promise(resolve =>
                    setTimeout(resolve, 450 * (attempt + 1))
                );
            }
        }

        console.warn('Signal delivery failed after retries:', type);
        return false;
    }

    async function sendSignal(toUserId, type, data) {
        const payload = {
            ...(data || {}),
            _signalId: data?._signalId || makeSignalId(type)
        };

        // Idempotent retries: every retry carries the same _signalId and
        // receivers ignore duplicate copies. This makes temporary /signal
        // timeouts survivable without creating duplicate SDP/chat events.
        return await postSignalReliable(toUserId, type, payload, 3);
    }

    // ── TOGGLE MIC ──

    async function toggleMic() {

        if (!localStream || !localStream.getAudioTracks().length) {
            await startAudio();
            if (!localStream || !localStream.getAudioTracks().length) return;
        }

        isMicOn = !isMicOn;

        localStream.getAudioTracks().forEach(t => t.enabled = isMicOn);

        const btn = document.getElementById('ctrl-mic');

        const micOff = document.getElementById('micoff-' + MY_USER_ID);

        const speaking = document.getElementById('speaking-' + MY_USER_ID);

        if (isMicOn) {

            if (btn) { btn.innerHTML = '<i class="fa fa-microphone"></i>'; btn.classList.remove('off'); }

            if (micOff) micOff.style.display = 'none';

            // Rebuild/restart the transcript engine if the browser stopped it.
            if (!recognition) startTranscript();
            setTimeout(startRecognition, 50);

            // Ensure every currently joined peer has this audio track now.
            connectToAll();
            await syncTracksToEveryPeer(true);
            [120, 450].forEach(delay => setTimeout(() => {
                if (isMicOn) {
                    connectToAll();
                    syncTracksToEveryPeer(true);
                }
            }, delay));

        } else {

            if (btn) { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }

            if (micOff) micOff.style.display = 'flex';

            if (speaking) speaking.style.display = 'none';

            stopRecognition();

        }

        // The audio track remains attached while muted. Enabling it
        // therefore starts audio-only calling immediately on every peer.
        await syncTracksToEveryPeer(isMicOn);
        if (isMicOn) {
            Object.keys(peers).forEach(uid => {
                if (shouldInitiatePeer(uid)) queuePeerNegotiation(uid, { reason: 'microphone-enabled', delay: 10 });
            });
        }
        broadcastMyMicStatus();

    }

    // ── TOGGLE CAMERA ──

    async function toggleCamera() {
        if (!localStream) {
            await startAudio();
            if (!localStream) return;
        }

        let videoTrack = localStream.getVideoTracks()[0] || null;

        // Request the camera only when the user actually turns it on.
        // This also repairs devices where the initial camera request failed.
        if (!videoTrack || videoTrack.readyState === 'ended') {
            try {
                const cameraStream = await navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 24, max: 30 },
                        facingMode: 'user'
                    }
                });

                videoTrack = cameraStream.getVideoTracks()[0];
                videoTrack.enabled = false;
                localStream.addTrack(videoTrack);

                const localVideo = document.getElementById('localVideo');
                if (localVideo) {
                    localVideo.srcObject = localStream;
                    localVideo.muted = true;
                    localVideo.playsInline = true;
                    localVideo.play().catch(() => {});
                }

                await syncTracksToEveryPeer(true);
            } catch (error) {
                console.error('Camera access failed:', error);
                showToast('📷 Camera could not start. Allow camera access in browser settings.');
                return;
            }
        }

        isCameraOn = !isCameraOn;
        videoTrack.enabled = isCameraOn;

        const btn = document.getElementById('ctrl-camera');
        const localVideo = document.getElementById('localVideo');
        const avatar = document.getElementById('avatar-' + MY_USER_ID);

        if (isCameraOn) {
            if (btn) {
                btn.innerHTML = '<i class="fa fa-video"></i>';
                btn.classList.remove('off');
            }
            if (localVideo) localVideo.style.display = 'block';
            if (avatar) avatar.style.display = 'none';
        } else {
            if (btn) {
                btn.innerHTML = '<i class="fa fa-video-slash"></i>';
                btn.classList.add('off');
            }
            if (localVideo) localVideo.style.display = 'none';
            if (avatar) avatar.style.display = 'flex';
        }

        // replaceTrack/addTrack on every peer, then renegotiate so every
        // joined device receives the camera without refreshing.
        await syncTracksToEveryPeer(true);
        broadcastMyCameraStatus();
    }

    // ── PEOPLE TAB ROW (always shown, joined or not) ──

    function ensurePanelRow(userId, name, initials, isOrganizer) {

        if (document.getElementById('panel-row-' + userId)) return;

        addParticipantPanelRow(userId, name, initials, isOrganizer);

    }

    function addParticipantPanelRow(userId, name, initials, isOrganizer) {

        const container = document.getElementById('other-participants-panel');

        if (!container) return;

        if (document.getElementById('panel-row-' + userId)) return;

        const color = isOrganizer ? '#3b82f6,#06b6d4' : '#22c55e,#06b6d4';

        const roleLabel = isOrganizer ? 'Organizer' : 'Participant';

        const isOnline = onlineUsers.has(String(userId));

        const row = document.createElement('div');

        row.id = 'panel-row-' + userId;

        row.className = isOnline ? 'participant-online' : 'participant-offline';

        row.style.cssText = `display:flex;align-items:center;gap:10px;padding:10px;margin-top:8px;border-radius:12px;

            background:${isOnline ? 'rgba(34,197,94,0.08)' : 'var(--surface2)'};

            border:1px solid ${isOnline ? 'rgba(34,197,94,0.2)' : 'var(--border)'};

            opacity:${isOnline ? '1' : '0.5'};`;

        row.innerHTML = `

        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,${color});display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">

            ${escapeHtml(initials)}

        </div>

        <div style="flex:1;">

            <div style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">

                ${escapeHtml(name)}

                ${isOrganizer ? '<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>' : ''}

            </div>

            <div class="join-status" style="font-size:10px;color:${isOnline ? 'var(--green)' : 'var(--muted)'};">

                ${roleLabel} • ${isOnline ? 'Joined' : 'Not joined yet'}

            </div>

        </div>

        <span class="online-dot" style="width:8px;height:8px;background:${isOnline ? 'var(--green)' : 'var(--surface2)'};border-radius:50%;border:${isOnline ? 'none' : '1px solid var(--border)'};"></span>

    `;

        container.appendChild(row);

    }

    // ── VIDEO TILE (only for users who have actually joined) ──

    function addParticipantTile(userId, name, initials, isOrganizer) {

        if (document.getElementById('tile-' + userId)) return;

        if (leftUsers.has(String(userId))) return; // extra guard, belt & suspenders

        const colorList = ['#3b82f6,#06b6d4','#8b5cf6,#ec4899','#22c55e,#06b6d4','#f59e0b,#ef4444','#64748b,#334155','#ec4899,#f59e0b'];

        const color = isOrganizer ? colorList[0] : colorList[Math.floor(Math.random() * colorList.length)];

        const grid = document.getElementById('video-grid');

        const tile = document.createElement('div');

        tile.className = 'video-tile';

        tile.id = 'tile-' + userId;

        const startsMuted = participantMicStatus[userId] !== false;

        const cameraOn = participantCameraStatus[userId] === true;

        tile.innerHTML = `

        <div class="video-placeholder">

            <video id="rvideo-${userId}" autoplay playsinline style="display:${cameraOn ? 'block' : 'none'};"></video>

            <div class="avatar-circle" id="avatar-${userId}" style="background:linear-gradient(135deg,${color});display:${cameraOn ? 'none' : 'flex'};">${escapeHtml(initials)}</div>

            <button class="tile-expand-btn" onclick="toggleMaximize('${userId}')" title="Maximize / Minimize">

                <i class="fa fa-expand" id="expand-icon-${userId}"></i>

            </button>

        </div>

        <div class="tile-info">

            <div class="tile-name">

                ${isOrganizer ? '<i class="fa fa-crown crown-icon"></i> ' : ''}${escapeHtml(name)}

                <span class="role-badge ${isOrganizer ? 'organizer' : 'participant'}">${isOrganizer ? 'Organizer' : 'Participant'}</span>

            </div>

            <div class="tile-icons">

                <div class="speaking-indicator" id="speaking-${userId}" style="display:none;">

                    <div class="speaking-bar"></div>

                    <div class="speaking-bar"></div>

                    <div class="speaking-bar"></div>

                </div>

                <div class="mic-off" id="micoff-${userId}" style="display:${startsMuted ? 'flex' : 'none'};">

                    <i class="fa fa-microphone-slash"></i>

                </div>

            </div>

        </div>`;

        if (isOrganizer) grid.prepend(tile); else grid.appendChild(tile);

        ensurePanelRow(userId, name, initials, isOrganizer);

        updateParticipantRow(userId, onlineUsers.has(String(userId)));

    }

    // ── TRANSCRIPT ──

    function startTranscript() {

        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SR) { showToast('⚠️ Live transcription requires Chrome or Edge.'); return; }

        recognition = new SR();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;
        // ur-PK generally handles Urdu plus common English words better than
        // en-US handles Urdu. Users can still switch to dedicated English.
        recognition.lang = 'en-US';

        const indicator = document.getElementById('listening-indicator');
        const listenText = document.getElementById('listening-text');

        recognition.onstart = () => {
            recognitionRunning = true;
            if (indicator) indicator.style.display = 'flex';
            if (listenText) listenText.textContent = currentLang === 'auto' ? 'Listening in English…' : 'Listening…';
        };

        recognition.onresult = (e) => {
            if (!isMicOn) { stopRecognition(); return; }
            let interimText = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const result = e.results[i];
                const text = result[0].transcript.trim();
                if (!text) continue;
                if (result.isFinal) {
                    const speaking = document.getElementById('speaking-' + MY_USER_ID);
                    if (speaking) speaking.style.display = 'none';
                    showLocalTranscript(text, false);
                    saveTranscript(text);
                } else {
                    interimText += (interimText ? ' ' : '') + text;
                }
            }
            if (interimText) {
                const speaking = document.getElementById('speaking-' + MY_USER_ID);
                if (speaking) speaking.style.display = 'flex';
                // Interim text is painted immediately, without waiting for
                // the recognition engine to mark the sentence final.
                showLocalTranscript(interimText, true);
            }
        };

        recognition.onerror = (e) => {
            recognitionRunning = false;

            if (e.error === 'not-allowed' || e.error === 'service-not-allowed') {
                showToast('Microphone/speech recognition permission is required.');
                return;
            }

            if (e.error !== 'aborted' && e.error !== 'no-speech' && e.error !== 'network') {
                console.warn('Speech recognition:', e.error);
            }

            scheduleRecognitionRestart(300);
        };

        recognition.onend = () => {
            recognitionRunning = false;
            if (indicator) indicator.style.display = 'none';
            scheduleRecognitionRestart(300);
        };

    }

    let recognitionRestartTimer = null;
    let recognitionStopping = false;

    function scheduleRecognitionRestart(delay = 300) {
        if (
            !recognition ||
            recognitionStopping ||
            !isMicOn ||
            document.visibilityState !== 'visible'
        ) {
            return;
        }

        if (recognitionRestartTimer) {
            clearTimeout(recognitionRestartTimer);
        }

        recognitionRestartTimer = setTimeout(() => {
            recognitionRestartTimer = null;
            startRecognition();
        }, delay);
    }

    function startRecognition() {
        if (
            !recognition ||
            recognitionRunning ||
            recognitionStopping ||
            !isMicOn ||
            document.visibilityState !== 'visible'
        ) {
            return;
        }

        try {
            recognition.start();
            recognitionRunning = true;
        } catch (e) {
            recognitionRunning = false;
        }
    }

    function toggleTranscriptLanguage() {
        currentLang = 'en-US';
        const btn = document.getElementById('lang-toggle-btn');
        if (btn) btn.textContent = '🌐 English only';
        showToast('Transcript language: English');

        if (recognition) {
            stopRecognition();
            recognition = null;
        }

        startTranscript();

        if (isMicOn) {
            scheduleRecognitionRestart(300);
        }
    }

    function stopRecognition() {
        if (!recognition) return;

        if (recognitionRestartTimer) {
            clearTimeout(recognitionRestartTimer);
            recognitionRestartTimer = null;
        }

        recognitionStopping = true;

        try {
            if (recognitionRunning) {
                recognition.abort();
            }
        } catch (e) {}

        recognitionRunning = false;

        setTimeout(() => {
            recognitionStopping = false;
        }, 250);
    }


    function showLocalTranscript(text, isInterim) {

        const body = document.getElementById('transcript-body');

        if (!body) return;

        body.querySelector('[data-empty]')?.remove();

        let liveEntry = document.getElementById('live-entry-' + MY_USER_ID);

        if (isInterim) {

            if (!liveEntry) {

                liveEntry = document.createElement('div');

                liveEntry.className = 'transcript-entry';

                liveEntry.id = 'live-entry-' + MY_USER_ID;

                liveEntry.innerHTML = `

                <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">${escapeHtml(MY_INITIALS)}</div>

                <div class="transcript-content">

                    <div class="transcript-meta">

                        <span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span>

                        <span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span>

                    </div>

                    <div class="transcript-text" style="opacity:0.6;font-style:italic;"></div>

                </div>`;

                body.appendChild(liveEntry);

            }

            liveEntry.querySelector('.transcript-text').textContent = text;

            body.scrollTop = body.scrollHeight;

        } else {

            if (liveEntry) {

                const textEl = liveEntry.querySelector('.transcript-text');

                textEl.style.opacity = '1'; textEl.style.fontStyle = 'normal'; textEl.textContent = text;

                liveEntry.removeAttribute('id');

            } else {

                const div = document.createElement('div');

                div.className = 'transcript-entry';

                div.innerHTML = `

                <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">${escapeHtml(MY_INITIALS)}</div>

                <div class="transcript-content">

                    <div class="transcript-meta">

                        <span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span>

                        <span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span>

                    </div>

                    <div class="transcript-text">${escapeHtml(text)}</div>

                </div>`;

                body.appendChild(div);

            }

            body.scrollTop = body.scrollHeight;

        }

    }

    async function saveTranscript(text) {

        try { await fetch(TRANSCRIPT_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({ text }) }); }

        catch (err) { console.error('Transcript save error:', err); }

    }

    // ── CHAT ──

    function sendChat() {

        const input = document.getElementById('chat-input');

        const text = input.value.trim();

        if (!text) return;

        addChatBubble(MY_NAME, text, true);

        input.value = '';

        sendSignal('all', 'chat', { text, name: MY_NAME, initials: MY_INITIALS });

    }

    function addChatBubble(name, text, isMe) {
        const body = document.getElementById('chat-body');
        if (!body) return;
        body.querySelector('[data-empty]')?.remove();

        const safeName = String(name || (isMe ? MY_NAME : 'User')).trim() || 'User';
        const initials = safeName.split(/\s+/).slice(0, 2).map(part => part.charAt(0)).join('').toUpperCase() || '?';
        const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const row = document.createElement('div');
        row.className = 'chat-message-row ' + (isMe ? 'is-me' : 'is-other');
        row.innerHTML = `
            <div class="chat-message-content">
                <div class="chat-message-meta">
                    <strong>${escapeHtml(isMe ? MY_NAME + ' (You)' : safeName)}</strong>
                    <span>${time}</span>
                </div>
                <div class="chat-message-bubble">${escapeHtml(text)}</div>
            </div>`;
        body.appendChild(row);
        body.scrollTop = body.scrollHeight;
    }

    // ── LEAVE ──

    // MARK_LEFT_URL is only ever called once per session, guarded by

    // leftNotified below — either here (explicit click) or by

    // notifyDisconnectBeacon() (tab close / back-forward nav), never both.

    let leftNotified = false;

    async function leaveMeeting() {

        if (leftNotified) return;

        leftNotified = true;

        if (autoEndTimer) clearTimeout(autoEndTimer);

        showToast('👋 You have left the meeting.');

        try { await fetch(MARK_LEFT_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({}) }); }

        catch (e) { console.error('markLeft error:', e); }

        cleanup();

        setTimeout(() => { window.location.href = LEAVE_URL; }, 600);

    }

    // ── UNLOAD / NAVIGATION-AWAY HANDLING ──

    // Fires on tab close, browser back/forward, or any navigation away

    // without clicking the explicit Leave button. This is the single

    // source of truth for an "unannounced" departure.

    //

    // Fix F: previously this relied on sendBeacon() alone, which was not

    // reliably reaching the server on every browser/navigation path — so

    // markLeft() sometimes never ran, joined_at/left_at were never updated

    // in the DB, and a refresh (by this user OR by anyone else) kept

    // resurrecting the tile and the "Joined" status in the People tab.

    // Now we fire fetch(..., {keepalive: true}) FIRST — the modern,

    // reliable way to notify a server on page unload, with the standard

    // X-CSRF-TOKEN header — and keep sendBeacon as a fallback for older

    // browsers. Both are guarded by the same one-shot flag, and

    // markLeft() + the toast de-dup logic are already idempotent, so it's

    // safe even if both happen to land.

    function notifyDisconnectBeacon() {

        if (leftNotified) return;

        leftNotified = true;

        const payload = JSON.stringify({ _token: CSRF });

        try {

            fetch(MARK_LEFT_URL, {

                method: 'POST',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },

                body: payload,

                keepalive: true

            }).catch(() => {});

        } catch (e) {}

        try {

            navigator.sendBeacon(

                MARK_LEFT_URL,

                new Blob([payload], { type: 'application/json' })

            );

        } catch (e) {}

    }

    window.addEventListener('pagehide', () => { notifyDisconnectBeacon(); cleanup(); });

    window.addEventListener('beforeunload', () => { notifyDisconnectBeacon(); });

    function cleanup() {

        if (autoEndTimer) { clearTimeout(autoEndTimer); autoEndTimer = null; }

        Object.values(offlineTimers).forEach(t => clearTimeout(t));

        Object.values(peers).forEach(pc => pc.close());

        localStream?.getTracks().forEach(t => t.stop());

        stopRecognition();

    }

    function escapeHtml(text) { const d = document.createElement('div'); d.textContent = String(text ?? ''); return d.innerHTML; }

    // ── MODERN TOAST (matches organizer view, with de-duplication) ──

    const recentToastMessages = new Set();

    function showToast(msg) {

        if (recentToastMessages.has(msg)) return; // same message already visible/just shown — skip

        recentToastMessages.add(msg);

        setTimeout(() => recentToastMessages.delete(msg), 4000);

        const stack = document.getElementById('toast-stack');

        if (!stack) return;

        const el = document.createElement('div');

        el.className = 'toast';

        el.textContent = msg;

        stack.appendChild(el);

        requestAnimationFrame(() => el.classList.add('show'));

        setTimeout(() => {

            el.classList.remove('show');

            el.classList.add('leaving');

            setTimeout(() => el.remove(), 260);

        }, 3200);

    }



    /* Final browser audio-unlock safety net.
       Remote audio is retried after any real user gesture. */
    function unlockAllRemoteAudio() {
        document.querySelectorAll('audio[data-peer-id]').forEach(audio => {
            audio.muted = false;
            audio.defaultMuted = false;
            audio.volume = 1;
            audio.play().catch(() => {});
        });
    }
    ['pointerdown', 'touchstart', 'keydown'].forEach(eventName => {
        document.addEventListener(eventName, unlockAllRemoteAudio, { passive: true });
    });



    /* ===== SmartMeet moderation notice ===== */
    function showModerationNotice(message) {
        const old = document.getElementById('smartmeet-moderation-notice');
        if (old) old.remove();
        const el = document.createElement('div');
        el.id = 'smartmeet-moderation-notice';
        el.style.cssText = 'position:fixed;top:82px;left:50%;transform:translateX(-50%);z-index:99999;background:#0f172a;color:#fff;padding:12px 18px;border-radius:14px;box-shadow:0 14px 40px rgba(15,23,42,.25);font-weight:700;font-size:14px;max-width:min(92vw,520px);text-align:center';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .25s'; }, 3200);
        setTimeout(() => el.remove(), 3500);
    }


    /* ============================================================
       SMARTMEET RELIABLE WEBRTC MESH V4
       - deterministic single-offer peer negotiation
       - permanent audio/video transceivers
       - camera replaceTrack without page refresh
       - remote audio autoplay recovery
       - keeps joined tiles visible during ICE recovery
    ============================================================ */

    function smV4SenderForKind(pc, kind) {
        if (!pc) return null;
        if (kind === 'audio' && pc.__smAudioSender) return pc.__smAudioSender;
        if (kind === 'video' && pc.__smVideoSender) return pc.__smVideoSender;
        const tx = (pc.getTransceivers?.() || []).find(t => t.receiver?.track?.kind === kind);
        return tx?.sender || pc.getSenders().find(s => s.track?.kind === kind) || null;
    }

    async function syncLocalTracksToPeer(userId) {
        const uid = String(userId);
        const pc = peers[uid];
        if (!pc || pc.signalingState === 'closed') return false;

        const audioTrack = localStream?.getAudioTracks?.().find(t => t.readyState === 'live') || null;
        const videoTrack = localStream?.getVideoTracks?.().find(t => t.readyState === 'live') || null;
        let changed = false;

        const audioSender = smV4SenderForKind(pc, 'audio');
        const videoSender = smV4SenderForKind(pc, 'video');

        if (audioSender && audioSender.track !== audioTrack) {
            try { await audioSender.replaceTrack(audioTrack); changed = true; } catch (e) { console.warn('Audio replaceTrack failed', uid, e); }
        }
        if (videoSender && videoSender.track !== videoTrack) {
            try { await videoSender.replaceTrack(videoTrack); changed = true; } catch (e) { console.warn('Video replaceTrack failed', uid, e); }
        }

        return changed;
    }

    async function syncTracksToEveryPeer(forceNegotiation = false) {
        Object.keys(knownParticipants || {}).forEach(uid => {
            uid = String(uid);
            if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
            if (knownParticipants[uid]?.hasJoined || onlineUsers.has(uid)) createPeerConnection(uid);
        });

        const jobs = Object.keys(peers).map(async uid => {
            const pc = peers[uid];
            if (!pc || pc.signalingState === 'closed') return;
            await syncLocalTracksToPeer(uid);

            // Only ONE deterministic side creates offers. This removes glare/
            // collision loops that previously left media at "2 online" but with
            // no audio/video flowing.
            if (shouldInitiatePeer(uid) && pc.signalingState === 'stable') {
                if (!pc.remoteDescription || forceNegotiation || pc.connectionState === 'failed') {
                    queuePeerNegotiation(uid, {
                        reason: forceNegotiation ? 'media-track-sync' : 'initial-mesh',
                        iceRestart: pc.connectionState === 'failed',
                        force: false,
                        delay: 25
                    });
                }
            }
        });
        await Promise.allSettled(jobs);
    }

    async function syncCameraToAllPeers(videoTrack) {
        if (!videoTrack) return;
        if (localStream && !localStream.getVideoTracks().includes(videoTrack)) localStream.addTrack(videoTrack);
        await syncTracksToEveryPeer(false);
    }

    function createPeerConnection(userId) {
        const uid = String(userId);
        if (!uid || uid === String(MY_USER_ID) || leftUsers.has(uid)) return null;

        let pc = peers[uid];
        if (pc && !['closed', 'failed'].includes(pc.connectionState)) return pc;
        if (pc) { try { pc.close(); } catch (e) {} }

        pc = new RTCPeerConnection(iceConfig);
        peers[uid] = pc;

        // Create BOTH m-lines immediately. Camera can therefore be turned on
        // later with replaceTrack() and becomes visible remotely without reload.
        const audioTx = pc.addTransceiver('audio', { direction: 'sendrecv' });
        const videoTx = pc.addTransceiver('video', { direction: 'sendrecv' });
        pc.__smAudioSender = audioTx.sender;
        pc.__smVideoSender = videoTx.sender;

        syncLocalTracksToPeer(uid).catch(console.warn);

        pc.onnegotiationneeded = () => {
            if (!shouldInitiatePeer(uid) || pc.signalingState !== 'stable') return;
            queuePeerNegotiation(uid, { reason: 'negotiation-needed-v4', delay: 30 });
        };

        pc.onicecandidate = event => {
            if (!event.candidate) return;
            sendSignal(uid, 'ice-candidate', { candidate: event.candidate.toJSON() });
        };

        pc.ontrack = event => {
            if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;

            const info = knownParticipants[uid];
            if (info) {
                info.hasJoined = true;
                addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer));
                markOnline(uid);
            } else {
                ensureParticipantTileVisible(uid);
            }

            const stream = getOrCreateRemoteStream(uid);
            if (!stream.getTracks().some(t => t.id === event.track.id)) stream.addTrack(event.track);

            const applyRemote = () => {
                attachRemoteStream(uid);
                if (event.track.kind === 'audio') {
                    const audio = document.getElementById('audio-' + uid);
                    if (audio) {
                        audio.muted = false;
                        audio.defaultMuted = false;
                        audio.volume = 1;
                        audio.play().catch(() => {});
                    }
                }
                if (event.track.kind === 'video' && event.track.readyState === 'live' && !event.track.muted) {
                    participantCameraStatus[uid] = true;
                    const video = document.getElementById('rvideo-' + uid);
                    const avatar = document.getElementById('avatar-' + uid);
                    if (video) {
                        video.style.display = 'block';
                        video.autoplay = true;
                        video.playsInline = true;
                        video.muted = true;
                        video.play().catch(() => {});
                    }
                    if (avatar) avatar.style.display = 'none';
                }
            };

            event.track.onunmute = applyRemote;
            event.track.onmute = () => {
                if (event.track.kind !== 'video') return;
                const video = document.getElementById('rvideo-' + uid);
                const avatar = document.getElementById('avatar-' + uid);
                if (video) video.style.display = 'none';
                if (avatar) avatar.style.display = 'flex';
            };
            event.track.onended = () => {
                try { stream.removeTrack(event.track); } catch (e) {}
                attachRemoteStream(uid);
            };
            applyRemote();
        };

        const recover = () => {
            if (leftUsers.has(uid)) return;
            const current = peers[uid];
            if (current !== pc) return;
            if (shouldInitiatePeer(uid)) {
                if (pc.signalingState === 'stable') {
                    queuePeerNegotiation(uid, { reason: 'ice-recovery-v4', iceRestart: true, delay: 120 });
                }
            } else {
                // Wake the deterministic offerer. user-joined is intentionally a
                // broadcast event in the controllers, so it works as a safe probe.
                announceJoin();
            }
        };

        pc.oniceconnectionstatechange = () => {
            const state = pc.iceConnectionState;
            if (state === 'connected' || state === 'completed') {
                if (offlineTimers[uid]) { clearTimeout(offlineTimers[uid]); delete offlineTimers[uid]; }
                ensureParticipantTileVisible(uid);
                attachRemoteStream(uid);
                syncLocalTracksToPeer(uid);
                broadcastMyMicStatus();
                broadcastMyCameraStatus();
            } else if (state === 'failed') {
                // Do NOT remove the participant tile. Presence and media state are
                // separate; recover ICE in-place instead of forcing a page refresh.
                setTimeout(recover, 250);
            } else if (state === 'disconnected') {
                if (offlineTimers[uid]) clearTimeout(offlineTimers[uid]);
                offlineTimers[uid] = setTimeout(recover, 1200);
            }
        };

        pc.onconnectionstatechange = () => {
            if (pc.connectionState === 'failed') setTimeout(recover, 200);
        };

        // Only the deterministic initiator sends the initial SDP offer.
        if (shouldInitiatePeer(uid)) {
            setTimeout(() => {
                if (peers[uid] === pc && pc.signalingState === 'stable') {
                    queuePeerNegotiation(uid, { reason: 'peer-created-v4', delay: 10 });
                }
            }, 40);
        }

        return pc;
    }

    function connectToAll() {
        Object.keys(knownParticipants || {}).forEach(uid => {
            uid = String(uid);
            if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
            if (knownParticipants[uid]?.hasJoined || onlineUsers.has(uid)) createPeerConnection(uid);
        });
    }

    // Re-announce presence periodically. This makes a user who opened the room a
    // fraction of a second before another subscription visible without refresh.
    if (!window.__smartMeetPresenceV4) {
        window.__smartMeetPresenceV4 = true;
        window.addEventListener('load', () => {
            [250, 900, 2200, 5000].forEach(delay => setTimeout(() => {
                announceJoin();
                connectToAll();
                syncTracksToEveryPeer(false);
            }, delay));
        });
        setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            announceJoin();
            connectToAll();
            syncTracksToEveryPeer(false);
        }, 8000);
    }



    /* ============================================================
       SMARTMEET V5 REALTIME STATE + AUDIO HARDENING
       This is intentionally an independent status listener so that
       leave/mic/camera/cancel UI updates are not dependent on SDP flow.
       ============================================================ */
    window.__smV5LeftSeen = window.__smV5LeftSeen || new Set();
    window.__smV5CancelSeen = window.__smV5CancelSeen || false;

    function smV5SetRemoteMic(uid, muted) {
        uid = String(uid);
        participantMicStatus[uid] = Boolean(muted);

        const tileMic = document.getElementById('micoff-' + uid);
        if (tileMic) tileMic.style.display = muted ? 'flex' : 'none';

        const peopleIcon = document.getElementById('participant-mic-icon-' + uid);
        if (peopleIcon) {
            peopleIcon.className = muted ? 'fa fa-microphone-slash' : 'fa fa-microphone';
            peopleIcon.style.color = muted ? 'var(--red)' : 'var(--green)';
        }
    }

    function smV5SetRemoteCamera(uid, cameraOn) {
        uid = String(uid);
        cameraOn = Boolean(cameraOn);
        participantCameraStatus[uid] = cameraOn;

        const video = document.getElementById('rvideo-' + uid);
        const avatar = document.getElementById('avatar-' + uid);

        if (!cameraOn) {
            if (video) video.style.display = 'none';
            if (avatar) avatar.style.display = 'flex';
            return;
        }

        attachRemoteStream(uid);
        const stream = remoteStreams[uid];
        const liveVideo = stream?.getVideoTracks?.().find(t => t.readyState === 'live');
        if (liveVideo && video) {
            video.style.display = 'block';
            video.muted = true;
            video.autoplay = true;
            video.playsInline = true;
            video.play().catch(() => {});
            if (avatar) avatar.style.display = 'none';
        }
    }

    function smV5ProcessPresenceSignal(data) {
        if (!data || !data.type) return;

        const uid = String(data.data?.userId || data.fromUserId || '');
        const myId = String(MY_USER_ID);

        if (data.type === 'user-joined' && uid && uid !== myId) {
            leftUsers.delete(uid);
            window.__smV5LeftSeen.delete(uid);

            if (!knownParticipants[uid]) {
                knownParticipants[uid] = {
                    name: data.data?.name || 'Participant',
                    initials: data.data?.initials || '?',
                    isOrganizer: Boolean(data.data?.isOrganizer),
                    hasJoined: true
                };
            } else {
                knownParticipants[uid].hasJoined = true;
                if (data.data?.name) knownParticipants[uid].name = data.data.name;
                if (data.data?.initials) knownParticipants[uid].initials = data.data.initials;
                if (data.data?.isOrganizer !== undefined) knownParticipants[uid].isOrganizer = Boolean(data.data.isOrganizer);
            }

            const info = knownParticipants[uid];
            ensurePanelRow(uid, info.name, info.initials, Boolean(info.isOrganizer));
            addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer));
            markOnline(uid);
            createPeerConnection(uid);

            // Exchange current UI/media state immediately.
            setTimeout(() => {
                broadcastMyMicStatus();
                broadcastMyCameraStatus();
                syncTracksToEveryPeer(true);
                unlockAllRemoteAudioV5();
            }, 80);
            return;
        }

        if (data.type === 'user-left' && uid && uid !== myId) {
            const name = data.data?.name || knownParticipants[uid]?.name || 'A participant';

            // Always remove media/tile and mark the People row offline immediately.
            handleUserLeft(uid);
            if (knownParticipants[uid]) knownParticipants[uid].hasJoined = false;
            markOffline(uid);

            if (!window.__smV5LeftSeen.has(uid)) {
                window.__smV5LeftSeen.add(uid);
                showToast(`👋 ${name} left the meeting.`);
            }
            return;
        }

        if (data.type === 'mic-status' && uid && uid !== myId) {
            smV5SetRemoteMic(uid, Boolean(data.data?.muted));
            return;
        }

        if (data.type === 'camera-status' && uid && uid !== myId) {
            smV5SetRemoteCamera(uid, Boolean(data.data?.cameraOn));
            return;
        }

        if (data.type === 'meeting-cancelled') {
            if (window.__smV5CancelSeen) return;
            window.__smV5CancelSeen = true;
            showToast('🚫 The organizer cancelled the meeting for everyone.');
            cleanup();
            setTimeout(() => { window.location.href = LEAVE_URL; }, 1700);
            return;
        }

        if (data.type === 'meeting-ended') {
            showToast(data.data?.auto ? '⏰ Meeting time has ended.' : '📞 Meeting has ended.');
            cleanup();
            setTimeout(() => { window.location.href = LEAVE_URL; }, 1800);
        }
    }

    function unlockAllRemoteAudioV5() {
        Object.keys(remoteStreams || {}).forEach(uid => {
            if (String(uid) === String(MY_USER_ID) || leftUsers.has(String(uid))) return;
            attachRemoteStream(uid);
        });

        document.querySelectorAll('audio[data-peer-id]').forEach(audio => {
            audio.autoplay = true;
            audio.playsInline = true;
            audio.muted = false;
            audio.defaultMuted = false;
            audio.volume = 1;
            audio.play().catch(() => {});
        });
    }

    // Browser autoplay policies require a real user gesture. Any meeting action
    // now unlocks every existing remote audio element.
    ['pointerdown','touchstart','keydown','click'].forEach(eventName => {
        document.addEventListener(eventName, unlockAllRemoteAudioV5, { passive:true });
    });

    // Add a second lightweight Reverb listener dedicated to realtime UI state.
    // Main handleSignal still handles SDP/ICE/chat/transcription.
    if (!window.__smartMeetV5StatusListener && window.Echo) {
        window.__smartMeetV5StatusListener = true;
        const smV5Channel = window.Echo.channel('meeting.' + MEETING_ID);
        smV5Channel.listen('.signal', smV5ProcessPresenceSignal);
    }

    // When the user toggles mic/camera, re-sync the real MediaStreamTrack and
    // rebroadcast state after the original button handler completes.
    window.addEventListener('load', () => {
        const micBtn = document.getElementById('ctrl-mic');
        const camBtn = document.getElementById('ctrl-camera');

        micBtn?.addEventListener('click', () => {
            setTimeout(async () => {
                const track = localStream?.getAudioTracks?.().find(t => t.readyState === 'live');
                if (track) track.enabled = Boolean(isMicOn);

                Object.keys(peers).forEach(uid => {
                    const pc = peers[uid];
                    if (!pc || pc.signalingState === 'closed') return;
                    const sender = smV4SenderForKind(pc, 'audio');
                    if (sender && track) sender.replaceTrack(track).catch(console.warn);
                });

                await syncTracksToEveryPeer(true);
                broadcastMyMicStatus();
                unlockAllRemoteAudioV5();
            }, 120);

            setTimeout(() => broadcastMyMicStatus(), 500);
        });

        camBtn?.addEventListener('click', () => {
            setTimeout(async () => {
                await syncTracksToEveryPeer(true);
                broadcastMyCameraStatus();
            }, 180);

            setTimeout(() => broadcastMyCameraStatus(), 600);
        });
    });

    // Periodic state refresh repairs a missed status packet without reloading.
    if (!window.__smartMeetV5StateHeartbeat) {
        window.__smartMeetV5StateHeartbeat = true;
        setInterval(() => {
            if (document.visibilityState !== 'visible') return;
            broadcastMyMicStatus();
            broadcastMyCameraStatus();
            unlockAllRemoteAudioV5();
        }, 5000);
    }

    // Backup leave notification for closing/back-navigation.
    // keepalive lets the POST continue while the page is unloading.
    if (!window.__smartMeetV5PageHide) {
        window.__smartMeetV5PageHide = true;
        window.addEventListener('pagehide', () => {
            if (disconnectNotified) return;
            disconnectNotified = true;

            try {
                sendSignal('all', 'user-left', {
                    userId: MY_USER_ID,
                    name: MY_NAME
                });
            } catch (e) {}

            try {
                fetch(MARK_LEFT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({}),
                    keepalive: true
                });
            } catch (e) {}
        });
    }

    // Strong leave path: first tell every connected client immediately, then
    // persist left_at on the backend, then navigate away.
    async function leaveMeeting() {
        if (disconnectNotified) return;
        disconnectNotified = true;

        if (typeof autoEndTimer !== 'undefined' && autoEndTimer) {
            clearTimeout(autoEndTimer);
        }

        showToast('📞 You left the meeting.');

        try {
            await sendSignal('all', 'user-left', {
                userId: MY_USER_ID,
                name: MY_NAME
            });
        } catch (e) {}

        // Give Reverb a short moment to deliver the realtime leave event.
        await new Promise(resolve => setTimeout(resolve, 140));

        try {
            await fetch(MARK_LEFT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({}),
                keepalive: true
            });
        } catch (error) {
            console.error('markLeft error:', error);
        }

        cleanup();
        setTimeout(() => { window.location.href = LEAVE_URL; }, 350);
    }


    /* ============================================================
       SMARTMEET V6 — WEBRTC HANDSHAKE / MEDIA / STATUS REPAIR
       Main purpose:
       1) guarantee an SDP offer is actually created after presence,
       2) repair peers stuck in "new",
       3) derive mic/camera UI from the real MediaStreamTrack,
       4) make incoming audio play after the first normal page gesture.
       ============================================================ */

    window.__smV6 = window.__smV6 || {
        handshakeTimers: {},
        lastOfferAt: {},
        statusChannelBound: false
    };

    function smV6RealMicOn() {
        const track = localStream?.getAudioTracks?.().find(t => t.readyState === 'live');
        return Boolean(track && track.enabled);
    }

    function smV6RealCameraOn() {
        const track = localStream?.getVideoTracks?.().find(t => t.readyState === 'live');
        return Boolean(track && track.enabled);
    }

    /* Override status broadcasters so remote UI always reflects the REAL track,
       not a stale boolean left over from an earlier toggle. */
    function broadcastMyMicStatus() {
        const micOn = smV6RealMicOn();
        isMicOn = micOn;
        sendSignal('all', 'mic-status', {
            userId: MY_USER_ID,
            muted: !micOn
        });
    }

    function broadcastMyCameraStatus() {
        const cameraOn = smV6RealCameraOn();
        isCameraOn = cameraOn;
        sendSignal('all', 'camera-status', {
            userId: MY_USER_ID,
            cameraOn
        });
    }

    async function smV6SyncPeerMedia(uid) {
        uid = String(uid);
        const pc = peers[uid] || createPeerConnection(uid);
        if (!pc || pc.signalingState === 'closed') return;

        const audioTrack = localStream?.getAudioTracks?.().find(t => t.readyState === 'live') || null;
        const videoTrack = localStream?.getVideoTracks?.().find(t => t.readyState === 'live') || null;

        const audioSender = smV4SenderForKind(pc, 'audio');
        const videoSender = smV4SenderForKind(pc, 'video');

        if (audioSender) {
            try { await audioSender.replaceTrack(audioTrack); } catch (e) { console.warn('V6 audio replaceTrack', e); }
        }
        if (videoSender) {
            try { await videoSender.replaceTrack(videoTrack); } catch (e) { console.warn('V6 video replaceTrack', e); }
        }

        if (audioTrack) audioTrack.enabled = Boolean(isMicOn);
        if (videoTrack) videoTrack.enabled = Boolean(isCameraOn);
    }

    /* We intentionally allow either side to force one offer if a connection is
       stuck at "new". Existing perfect-negotiation collision handling decides
       which offer wins, preventing the old deterministic-side deadlock. */
    async function smV6ForceHandshake(userId, reason = 'presence') {
        const uid = String(userId);
        if (!uid || uid === String(MY_USER_ID) || leftUsers.has(uid)) return;

        const info = knownParticipants?.[uid];
        if (info && info.hasJoined === false && !onlineUsers.has(uid)) return;

        const pc = createPeerConnection(uid);
        if (!pc || pc.signalingState === 'closed') return;

        await smV6SyncPeerMedia(uid);

        if (pc.connectionState === 'connected' &&
            (pc.iceConnectionState === 'connected' || pc.iceConnectionState === 'completed')) {
            attachRemoteStream(uid);
            return;
        }

        if (pc.signalingState !== 'stable') return;

        const now = Date.now();
        if ((window.__smV6.lastOfferAt[uid] || 0) + 700 > now) return;
        window.__smV6.lastOfferAt[uid] = now;

        queuePeerNegotiation(uid, {
            reason: 'v6-' + reason,
            force: true,
            iceRestart: ['failed','disconnected'].includes(pc.iceConnectionState) ||
                ['failed','disconnected'].includes(pc.connectionState),
            delay: 10
        });
    }

    function smV6HandshakeBurst(uid, reason = 'join') {
        uid = String(uid);
        [40, 280, 850, 1800, 3500].forEach(delay => {
            setTimeout(() => smV6ForceHandshake(uid, reason), delay);
        });
    }

    function smV6SetRemoteMic(uid, muted) {
        uid = String(uid);
        muted = Boolean(muted);
        participantMicStatus[uid] = muted;

        const tileIcon = document.getElementById('micoff-' + uid);
        if (tileIcon) tileIcon.style.display = muted ? 'flex' : 'none';

        const peopleIcon = document.getElementById('participant-mic-icon-' + uid);
        if (peopleIcon) {
            peopleIcon.className = muted ? 'fa fa-microphone-slash' : 'fa fa-microphone';
            peopleIcon.style.color = muted ? 'var(--red)' : 'var(--green)';
        }
    }

    function smV6SetRemoteCamera(uid, cameraOn) {
        uid = String(uid);
        cameraOn = Boolean(cameraOn);
        participantCameraStatus[uid] = cameraOn;

        attachRemoteStream(uid);

        const stream = remoteStreams?.[uid];
        const videoTrack = stream?.getVideoTracks?.().find(t => t.readyState === 'live');
        const video = document.getElementById('rvideo-' + uid);
        const avatar = document.getElementById('avatar-' + uid);

        const actuallyVisible = cameraOn && Boolean(videoTrack);
        if (video) {
            video.autoplay = true;
            video.playsInline = true;
            video.muted = true;
            video.style.display = actuallyVisible ? 'block' : 'none';
            if (actuallyVisible) video.play().catch(() => {});
        }
        if (avatar) avatar.style.display = actuallyVisible ? 'none' : 'flex';
    }

    function smV6UnlockRemoteAudio() {
        document.querySelectorAll('audio[data-peer-id]').forEach(audio => {
            audio.autoplay = true;
            audio.playsInline = true;
            audio.muted = false;
            audio.defaultMuted = false;
            audio.volume = 1;
            audio.play().catch(() => {});
        });
    }

    /* A normal click on Mic/Camera/Chat/People/room is enough. No special
       "tap to enable audio" action is required from the user. */
    ['pointerdown','touchstart','keydown','click'].forEach(eventName => {
        document.addEventListener(eventName, smV6UnlockRemoteAudio, { passive:true });
    });

    function smV6HandleStatus(data) {
        if (!data?.type) return;
        const uid = String(data.data?.userId || data.fromUserId || '');
        if (!uid || uid === String(MY_USER_ID)) return;

        if (data.type === 'user-joined') {
            leftUsers.delete(uid);

            if (!knownParticipants[uid]) {
                knownParticipants[uid] = {
                    name: data.data?.name || 'Participant',
                    initials: data.data?.initials || '?',
                    isOrganizer: Boolean(data.data?.isOrganizer),
                    hasJoined: true
                };
            } else {
                knownParticipants[uid].hasJoined = true;
                if (data.data?.name) knownParticipants[uid].name = data.data.name;
                if (data.data?.initials) knownParticipants[uid].initials = data.data.initials;
                if (data.data?.isOrganizer !== undefined) {
                    knownParticipants[uid].isOrganizer = Boolean(data.data.isOrganizer);
                }
            }

            const info = knownParticipants[uid];
            ensurePanelRow(uid, info.name, info.initials, Boolean(info.isOrganizer));
            addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer));
            markOnline(uid);

            createPeerConnection(uid);
            smV6HandshakeBurst(uid, 'user-joined');

            setTimeout(() => {
                broadcastMyMicStatus();
                broadcastMyCameraStatus();
            }, 120);
            return;
        }

        if (data.type === 'mic-status') {
            smV6SetRemoteMic(uid, Boolean(data.data?.muted));
            return;
        }

        if (data.type === 'camera-status') {
            smV6SetRemoteCamera(uid, Boolean(data.data?.cameraOn));
            if (data.data?.cameraOn) smV6HandshakeBurst(uid, 'remote-camera-on');
            return;
        }

        if (data.type === 'user-left') {
            const name = data.data?.name || knownParticipants?.[uid]?.name || 'Participant';
            handleUserLeft(uid);
            if (knownParticipants?.[uid]) knownParticipants[uid].hasJoined = false;
            markOffline(uid);
            showToast(`👋 ${name} left the meeting.`);
            return;
        }

        if (data.type === 'meeting-cancelled') {
            showToast('🚫 The organizer cancelled the meeting for everyone.');
            cleanup();
            setTimeout(() => { window.location.href = LEAVE_URL; }, 1600);
            return;
        }

        if (data.type === 'meeting-ended') {
            showToast(data.data?.auto ? '⏰ Meeting time has ended.' : '📞 Meeting has ended.');
            cleanup();
            setTimeout(() => { window.location.href = LEAVE_URL; }, 1700);
        }
    }

    /* Independent status listener. The original handleSignal remains responsible
       for SDP offer/answer/ICE. */
    if (!window.__smV6.statusChannelBound && window.Echo) {
        window.__smV6.statusChannelBound = true;
        window.Echo.channel('meeting.' + MEETING_ID).listen('.signal', smV6HandleStatus);
    }

    /* Initial join repair. */
    window.addEventListener('load', () => {
        [250, 700, 1600, 3200].forEach(delay => {
            setTimeout(() => {
                announceJoin();
                Object.keys(knownParticipants || {}).forEach(uid => {
                    uid = String(uid);
                    if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
                    if (knownParticipants[uid]?.hasJoined || onlineUsers.has(uid)) {
                        createPeerConnection(uid);
                        smV6ForceHandshake(uid, 'page-load');
                    }
                });
                broadcastMyMicStatus();
                broadcastMyCameraStatus();
            }, delay);
        });
    });

    /* Repair peers that remain in "new"/"connecting". This is the exact state
       visible in WebRTC Internals when no successful SDP/ICE path has formed. */
    if (!window.__smV6RepairTimer) {
        window.__smV6RepairTimer = setInterval(() => {
            if (document.visibilityState !== 'visible') return;

            Object.keys(knownParticipants || {}).forEach(uid => {
                uid = String(uid);
                if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
                if (!(knownParticipants[uid]?.hasJoined || onlineUsers.has(uid))) return;

                const pc = peers[uid] || createPeerConnection(uid);
                if (!pc) return;

                smV6SyncPeerMedia(uid);

                if (['new','connecting','disconnected','failed'].includes(pc.connectionState) ||
                    ['new','checking','disconnected','failed'].includes(pc.iceConnectionState)) {
                    smV6ForceHandshake(uid, 'repair');
                } else {
                    attachRemoteStream(uid);
                }
            });

            broadcastMyMicStatus();
            broadcastMyCameraStatus();
            smV6UnlockRemoteAudio();
        }, 3000);
    }

    /* Extra post-toggle repair. Existing handlers still request permissions and
       update the buttons; V6 then guarantees the resulting real track reaches
       every peer and advertises the correct status. */
    window.addEventListener('load', () => {
        document.getElementById('ctrl-mic')?.addEventListener('click', () => {
            [100, 350, 900].forEach(delay => setTimeout(async () => {
                Object.keys(peers).forEach(uid => smV6SyncPeerMedia(uid));
                broadcastMyMicStatus();
                Object.keys(peers).forEach(uid => smV6ForceHandshake(uid, 'mic-toggle'));
            }, delay));
        });

        document.getElementById('ctrl-camera')?.addEventListener('click', () => {
            [150, 450, 1000].forEach(delay => setTimeout(async () => {
                Object.keys(peers).forEach(uid => smV6SyncPeerMedia(uid));
                broadcastMyCameraStatus();
                Object.keys(peers).forEach(uid => smV6ForceHandshake(uid, 'camera-toggle'));
            }, delay));
        });
    });


    /* ============================================================
       SMARTMEET FINAL CHAT + MEDIA STATUS PATCH
       ============================================================ */

    window.__smartMeetChatSeen = window.__smartMeetChatSeen || new Set();

    function smartMeetChatKey(name, text, fromUserId='') {
        return `${String(fromUserId)}|${String(name)}|${String(text)}`;
    }

    /* Render own message on LEFT and remote messages on RIGHT. */
    function addChatBubble(name, text, isMe, fromUserId = '') {
        const body = document.getElementById('chat-body');
        if (!body) return;

        body.querySelector('[data-empty]')?.remove();

        const safeName = String(name || (isMe ? MY_NAME : 'User')).trim() || 'User';
        const key = smartMeetChatKey(safeName, text, isMe ? MY_USER_ID : fromUserId);

        if (window.__smartMeetChatSeen.has(key)) return;
        window.__smartMeetChatSeen.add(key);

        const initials = safeName
            .split(/\s+/)
            .filter(Boolean)
            .slice(0,2)
            .map(part => part.charAt(0))
            .join('')
            .toUpperCase() || '?';

        const time = new Date().toLocaleTimeString('en-US', {
            hour:'2-digit',
            minute:'2-digit'
        });

        const row = document.createElement('div');
        row.className = `chat-message-row ${isMe ? 'is-me' : 'is-other'}`;
        row.innerHTML = `
        <div class="chat-message-avatar">
            ${escapeHtml(isMe ? MY_INITIALS : initials)}
        </div>
        <div class="chat-message-content">
            <div class="chat-message-meta">
                <strong>${escapeHtml(isMe ? `${MY_NAME} (You)` : safeName)}</strong>
                <span>${time}</span>
            </div>
            <div class="chat-message-bubble">${escapeHtml(text)}</div>
        </div>
    `;

        body.appendChild(row);
        body.scrollTop = body.scrollHeight;
    }

    function sendChat() {
        const input = document.getElementById('chat-input');
        if (!input) return;

        const text = input.value.trim();
        if (!text) return;

        addChatBubble(MY_NAME, text, true, MY_USER_ID);
        input.value = '';

        sendSignal('all', 'chat', {
            text,
            name: MY_NAME,
            initials: MY_INITIALS,
            userId: MY_USER_ID
        });
    }

    /* Dedicated realtime chat listener. It is independent from the media
       negotiation code and guarantees every joined browser receives chat. */
    if (!window.__smartMeetFinalChatListener && window.Echo) {
        window.__smartMeetFinalChatListener = true;

        window.Echo
            .channel('meeting.' + MEETING_ID)
            .listen('.signal', payload => {
                if (!payload || payload.type !== 'chat') return;

                const from = String(payload.fromUserId || payload.data?.userId || '');
                if (from === String(MY_USER_ID)) return;

                addChatBubble(
                    payload.data?.name || knownParticipants?.[from]?.name || 'User',
                    payload.data?.text || '',
                    false,
                    from
                );

                if (typeof activeTab !== 'undefined' && activeTab !== 'chat') {
                    if (typeof unreadChat !== 'undefined') unreadChat++;
                    if (typeof updateChatBadge === 'function') updateChatBadge();
                }
            });
    }

    /* Web Speech API for CHAT INPUT. This is separate from meeting transcript. */
    window.addEventListener('load', () => {
        const input = document.getElementById('chat-input');
        if (!input || document.getElementById('chat-voice-btn')) return;

        const sendBtn = input.parentElement?.querySelector('.btn-send');
        if (!sendBtn) return;

        const voiceBtn = document.createElement('button');
        voiceBtn.type = 'button';
        voiceBtn.id = 'chat-voice-btn';
        voiceBtn.className = 'chat-voice-btn';
        voiceBtn.title = 'Voice input';
        voiceBtn.innerHTML = '<i class="fa fa-microphone"></i>';
        sendBtn.parentElement.insertBefore(voiceBtn, sendBtn);

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            voiceBtn.disabled = true;
            voiceBtn.title = 'Voice input is not supported in this browser';
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;

        let baseText = '';

        voiceBtn.addEventListener('click', () => {
            try {
                baseText = input.value.trim();
                voiceBtn.classList.add('listening');
                recognition.start();
            } catch (e) {}
        });

        recognition.onresult = event => {
            let spoken = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                spoken += event.results[i][0].transcript;
            }
            input.value = [baseText, spoken.trim()].filter(Boolean).join(' ');
            input.focus();
        };

        recognition.onend = () => voiceBtn.classList.remove('listening');
        recognition.onerror = () => voiceBtn.classList.remove('listening');
    });

    /* Always derive mic/camera status from the actual local tracks. */
    function smartMeetBroadcastRealMediaStatus() {
        const audioTrack = localStream?.getAudioTracks?.().find(t => t.readyState === 'live');
        const videoTrack = localStream?.getVideoTracks?.().find(t => t.readyState === 'live');

        isMicOn = Boolean(audioTrack && audioTrack.enabled);
        isCameraOn = Boolean(videoTrack && videoTrack.enabled);

        sendSignal('all', 'mic-status', {
            userId: MY_USER_ID,
            muted: !isMicOn
        });

        sendSignal('all', 'camera-status', {
            userId: MY_USER_ID,
            cameraOn: isCameraOn
        });
    }

    window.addEventListener('load', () => {
        document.getElementById('ctrl-mic')?.addEventListener('click', () => {
            setTimeout(smartMeetBroadcastRealMediaStatus, 180);
            setTimeout(smartMeetBroadcastRealMediaStatus, 600);
        });

        document.getElementById('ctrl-camera')?.addEventListener('click', () => {
            setTimeout(smartMeetBroadcastRealMediaStatus, 220);
            setTimeout(smartMeetBroadcastRealMediaStatus, 700);
        });
    });

</script>

<script>
    /* ============================================================
       SMARTMEET V8 FINAL STABILITY PATCH
       - never replaceTrack() on a closed RTCPeerConnection
       - recreate closed peers cleanly
       - realtime transcript listener for every joined user
       - transcript POST errors are visible in console
       ============================================================ */

    function smV8EnsureOpenPeer(uid) {
        uid = String(uid);

        let pc = peers?.[uid] || null;

        if (pc && (pc.signalingState === 'closed' || pc.connectionState === 'closed')) {
            try { pc.ontrack = null; pc.onicecandidate = null; pc.onconnectionstatechange = null; } catch (e) {}
            try { pc.close(); } catch (e) {}
            delete peers[uid];
            pc = null;
        }

        if (!pc) {
            pc = createPeerConnection(uid);
        }

        return pc;
    }

    /* Override the V6 helper with a closed-peer-safe version. */
    async function smV6SyncPeerMedia(uid) {
        uid = String(uid);

        const pc = smV8EnsureOpenPeer(uid);
        if (!pc || pc.signalingState === 'closed' || pc.connectionState === 'closed') return;

        const audioTrack =
            localStream?.getAudioTracks?.().find(t => t.readyState === 'live') || null;

        const videoTrack =
            localStream?.getVideoTracks?.().find(t => t.readyState === 'live') || null;

        const audioSender = smV4SenderForKind(pc, 'audio');
        const videoSender = smV4SenderForKind(pc, 'video');

        if (audioSender && pc.signalingState !== 'closed' && pc.connectionState !== 'closed') {
            try {
                await audioSender.replaceTrack(audioTrack);
            } catch (error) {
                if (error?.name !== 'InvalidStateError') {
                    console.warn('Audio replaceTrack failed:', error);
                }
            }
        }

        if (videoSender && pc.signalingState !== 'closed' && pc.connectionState !== 'closed') {
            try {
                await videoSender.replaceTrack(videoTrack);
            } catch (error) {
                if (error?.name !== 'InvalidStateError') {
                    console.warn('Video replaceTrack failed:', error);
                }
            }
        }

        if (audioTrack) audioTrack.enabled = Boolean(isMicOn);
        if (videoTrack) videoTrack.enabled = Boolean(isCameraOn);
    }

    /* Override force-handshake so it never negotiates a dead peer. */
    async function smV6ForceHandshake(userId, reason = 'presence') {
        const uid = String(userId);

        if (!uid || uid === String(MY_USER_ID) || leftUsers.has(uid)) return;

        const info = knownParticipants?.[uid];
        if (info && info.hasJoined === false && !onlineUsers.has(uid)) return;

        const pc = smV8EnsureOpenPeer(uid);
        if (!pc || pc.signalingState === 'closed' || pc.connectionState === 'closed') return;

        await smV6SyncPeerMedia(uid);

        if (pc.connectionState === 'connected' &&
            ['connected', 'completed'].includes(pc.iceConnectionState)) {
            attachRemoteStream(uid);
            return;
        }

        if (pc.signalingState !== 'stable') return;

        const now = Date.now();
        if ((window.__smV6?.lastOfferAt?.[uid] || 0) + 900 > now) return;

        window.__smV6.lastOfferAt[uid] = now;

        queuePeerNegotiation(uid, {
            reason: 'v8-' + reason,
            force: true,
            iceRestart:
                ['failed', 'disconnected'].includes(pc.iceConnectionState) ||
                ['failed', 'disconnected'].includes(pc.connectionState),
            delay: 20
        });
    }

    /* Realtime transcription for all OTHER users. */
    if (!window.__smartMeetV8TranscriptListener && window.Echo) {
        window.__smartMeetV8TranscriptListener = true;

        window.Echo
            .channel('meeting.' + MEETING_ID)
            .listen('.transcript', data => {
                if (!data) return;

                if (String(data.userId) === String(MY_USER_ID)) return;

                handleTranscript({
                    userId: String(data.userId || ''),
                    userName: data.userName || 'User',
                    userInitials: data.userInitials || '?',
                    text: data.text || '',
                    spokenAt: data.spokenAt || ''
                });
            });
    }

    /* Save transcript and surface backend failures clearly. */
    async function saveTranscript(text) {
        try {
            const response = await fetch(TRANSCRIPT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({ text })
            });

            if (!response.ok) {
                const body = await response.text();
                console.error('Transcript endpoint failed:', response.status, body);
            }
        } catch (error) {
            console.error('Transcript save error:', error);
        }
    }

    /* Clean dead peers instead of repeatedly trying replaceTrack on them. */
    setInterval(() => {
        Object.keys(peers || {}).forEach(uid => {
            const pc = peers[uid];

            if (!pc) return;

            if (pc.signalingState === 'closed' || pc.connectionState === 'closed') {
                try { pc.close(); } catch (e) {}
                delete peers[uid];

                if (!leftUsers.has(String(uid)) &&
                    (knownParticipants?.[uid]?.hasJoined || onlineUsers.has(String(uid)))) {
                    setTimeout(() => smV6ForceHandshake(uid, 'recreate-closed-peer'), 150);
                }
            }
        });
    }, 2500);
</script>



<script>
    /* ================================================================
       SMARTMEET AWS V7 — AUTHORITATIVE PRESENCE / LEAVE / CANCEL LAYER
       Participant room counterpart.
       ================================================================ */
    (() => {
        const MY_ID = String(MY_USER_ID);
        const ROOM_CHANNEL = 'meeting.' + MEETING_ID;

        window.__smAwsV7 = window.__smAwsV7 || {
            left: new Set(),
            leaving: false,
            cancelled: false
        };

        function rowFor(uid) {
            return document.getElementById('participant-row-' + uid)
                || document.querySelector(`[data-user-id="${CSS.escape(String(uid))}"]`);
        }

        function setPeopleState(uid, state) {
            uid = String(uid);
            const row = rowFor(uid);
            if (!row) return;

            row.classList.remove('participant-online', 'participant-offline', 'participant-left');

            const status = row.querySelector('.join-status');
            const info = knownParticipants?.[uid];
            const role = info?.isOrganizer ? 'Organizer' : 'Participant';

            if (state === 'joined') {
                row.classList.add('participant-online');
                row.style.opacity = '1';
                if (status) {
                    status.textContent = role + ' • Joined';
                    status.style.color = 'var(--green)';
                }
                return;
            }

            row.classList.add('participant-offline');

            if (state === 'left') {
                row.classList.add('participant-left');
                if (status) {
                    status.textContent = role + ' • Left';
                    status.style.color = '#f87171';
                }
            } else {
                row.style.opacity = '.46';
                if (status) {
                    status.textContent = role + ' • Not joined yet';
                    status.style.color = 'var(--muted)';
                }
            }
        }

        function removeLiveTile(uid) {
            uid = String(uid);
            leftUsers.add(uid);
            onlineUsers.delete(uid);

            try { handleUserLeft(uid); } catch (e) {}

            const tile = document.getElementById('tile-' + uid);
            if (tile) tile.remove();

            const audio = document.querySelector(`audio[data-peer-id="${CSS.escape(uid)}"]`);
            if (audio) {
                try { audio.pause(); } catch (e) {}
                audio.srcObject = null;
                audio.remove();
            }

            const pc = peers?.[uid];
            if (pc) {
                try {
                    pc.ontrack = null;
                    pc.onicecandidate = null;
                    pc.onconnectionstatechange = null;
                    pc.close();
                } catch (e) {}
                delete peers[uid];
            }

            if (remoteStreams?.[uid]) {
                try { remoteStreams[uid].getTracks().forEach(t => t.stop()); } catch (e) {}
                delete remoteStreams[uid];
            }

            if (knownParticipants?.[uid]) knownParticipants[uid].hasJoined = false;
            setPeopleState(uid, 'left');
        }

        function terminateRoom(message) {
            if (window.__smAwsV7.cancelled) return;
            window.__smAwsV7.cancelled = true;

            try {
                Object.keys(peers || {}).forEach(uid => removeLiveTile(uid));
            } catch (e) {}

            try {
                localStream?.getTracks?.().forEach(track => track.stop());
            } catch (e) {}

            try { cleanup(); } catch (e) {}

            try {
                window.Echo?.leave?.(ROOM_CHANNEL);
            } catch (e) {}

            showToast(message || '🚫 Meeting cancelled.');
        }

        /* Keep negotiation stable: a healthy call is never renegotiated just
           because a presence/status heartbeat was received. */
        window.smV6ForceHandshake = async function(userId, reason = 'presence') {
            const uid = String(userId);
            if (!uid || uid === MY_ID || leftUsers.has(uid) || window.__smAwsV7.left.has(uid)) return;

            if (typeof shouldInitiatePeer === 'function' && !shouldInitiatePeer(uid)) return;

            const pc = peers?.[uid] || createPeerConnection(uid);
            if (!pc || pc.signalingState === 'closed') return;

            if (
                pc.connectionState === 'connected' &&
                ['connected','completed'].includes(pc.iceConnectionState)
            ) {
                attachRemoteStream(uid);
                return;
            }

            if (pc.signalingState !== 'stable') return;

            try { await smV6SyncPeerMedia(uid); } catch (e) {}

            queuePeerNegotiation(uid, {
                reason: 'aws-v7-' + reason,
                force: false,
                iceRestart: ['failed','disconnected'].includes(pc.iceConnectionState)
                    || ['failed','disconnected'].includes(pc.connectionState),
                delay: 30
            });
        };
        try { smV6ForceHandshake = window.smV6ForceHandshake; } catch (e) {}

        async function reliableMarkLeft() {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 5500);

            try {
                await fetch(MARK_LEFT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({}),
                    keepalive: true,
                    signal: controller.signal
                });
            } catch (e) {
                console.warn('markLeft request did not complete before navigation');
            } finally {
                clearTimeout(timer);
            }
        }

        window.leaveMeeting = async function() {
            if (window.__smAwsV7.leaving || window.__smAwsV7.cancelled) return;
            window.__smAwsV7.leaving = true;
            disconnectNotified = true;

            showToast('📞 You left the meeting.');

            /* Persist before navigation, then broadcast the realtime state. */
            await reliableMarkLeft();

            try {
                await sendSignal('all', 'user-left', {
                    userId: MY_ID,
                    name: MY_NAME,
                    isOrganizer: false,
                    leftAt: new Date().toISOString()
                });
            } catch (e) {}

            try {
                localStream?.getTracks?.().forEach(track => track.stop());
            } catch (e) {}

            try { cleanup(); } catch (e) {}

            setTimeout(() => window.location.assign(LEAVE_URL), 260);
        };

        function receiveAuthoritativeState(data) {
            if (!data?.type) return;
            const uid = String(data.data?.userId || data.fromUserId || '');

            if (data.type === 'user-joined' && uid && uid !== MY_ID) {
                window.__smAwsV7.left.delete(uid);
                leftUsers.delete(uid);
                if (knownParticipants?.[uid]) knownParticipants[uid].hasJoined = true;
                setPeopleState(uid, 'joined');
                return;
            }

            if (data.type === 'user-left' && uid && uid !== MY_ID) {
                window.__smAwsV7.left.add(uid);
                removeLiveTile(uid);
                return;
            }

            if (data.type === 'meeting-cancelled') {
                terminateRoom('🚫 The organizer cancelled the meeting for everyone.');
                setTimeout(() => window.location.assign(LEAVE_URL), 850);
            }
        }

        if (!window.__smAwsV7.partBound && window.Echo) {
            window.__smAwsV7.partBound = true;
            window.Echo.channel(ROOM_CHANNEL).listen('.signal', receiveAuthoritativeState);
        }
    })();
</script>



<script>
    /* =====================================================================
       SMARTMEET REALTIME MEDIA FINAL
       Goal:
       - mic ON => current joined peers receive audio without refresh
       - camera ON => current joined peers receive video without refresh
       - new join => peer is connected immediately
       This patch keeps the existing fixed audio/video transceiver architecture.
       It intentionally avoids renegotiating healthy connected peers.
       ===================================================================== */
    (() => {
        const SELF = String(MY_USER_ID);
        const ROOM = 'meeting.' + MEETING_ID;

        window.__smRealtimeMedia = window.__smRealtimeMedia || {
            bound: false,
            syncing: false,
            joinTimers: {}
        };

        function liveAudioTrack() {
            return localStream?.getAudioTracks?.().find(t => t.readyState === 'live') || null;
        }

        function liveVideoTrack() {
            return localStream?.getVideoTracks?.().find(t => t.readyState === 'live') || null;
        }

        function peerSender(pc, kind) {
            if (!pc) return null;

            if (kind === 'audio' && pc.__smAudioSender) return pc.__smAudioSender;
            if (kind === 'video' && pc.__smVideoSender) return pc.__smVideoSender;

            return (pc.getTransceivers?.() || [])
                    .find(tx => tx.receiver?.track?.kind === kind)?.sender
                || pc.getSenders?.().find(sender => sender.track?.kind === kind)
                || null;
        }

        function joinedPeerIds() {
            const ids = new Set();

            try {
                onlineUsers?.forEach(uid => {
                    uid = String(uid);
                    if (uid !== SELF && !leftUsers.has(uid)) ids.add(uid);
                });
            } catch (e) {}

            try {
                Object.keys(knownParticipants || {}).forEach(uid => {
                    uid = String(uid);
                    if (
                        uid !== SELF &&
                        !leftUsers.has(uid) &&
                        knownParticipants?.[uid]?.hasJoined
                    ) {
                        ids.add(uid);
                    }
                });
            } catch (e) {}

            return [...ids];
        }

        async function ensurePeerMedia(uid, { allowInitialOffer = true } = {}) {
            uid = String(uid);

            if (!uid || uid === SELF || leftUsers.has(uid)) return null;

            const info = knownParticipants?.[uid];
            if (info && info.hasJoined === false && !onlineUsers.has(uid)) return null;

            const pc = peers?.[uid] || createPeerConnection(uid);
            if (!pc || pc.signalingState === 'closed') return null;

            const audioTrack = liveAudioTrack();
            const videoTrack = liveVideoTrack();

            const audioSender = peerSender(pc, 'audio');
            const videoSender = peerSender(pc, 'video');

            try {
                if (audioSender && audioSender.track !== audioTrack) {
                    await audioSender.replaceTrack(audioTrack);
                }
            } catch (error) {
                console.warn('Realtime audio replaceTrack failed:', uid, error);
            }

            try {
                if (videoSender && videoSender.track !== videoTrack) {
                    await videoSender.replaceTrack(videoTrack);
                }
            } catch (error) {
                console.warn('Realtime video replaceTrack failed:', uid, error);
            }

            /*
             * Permanent sendrecv transceivers mean mic/camera toggles themselves
             * do NOT need a new SDP offer. Only create an offer if this peer has
             * never negotiated, or ICE is genuinely failed.
             */
            const healthy =
                pc.connectionState === 'connected' &&
                ['connected', 'completed'].includes(pc.iceConnectionState);

            if (healthy) {
                try { attachRemoteStream(uid); } catch (e) {}
                return pc;
            }

            const needsInitialSdp = !pc.localDescription && !pc.remoteDescription;
            const failed =
                ['failed'].includes(pc.connectionState) ||
                ['failed'].includes(pc.iceConnectionState);

            if (
                pc.signalingState === 'stable' &&
                typeof shouldInitiatePeer === 'function' &&
                shouldInitiatePeer(uid) &&
                ((allowInitialOffer && needsInitialSdp) || failed)
            ) {
                queuePeerNegotiation(uid, {
                    reason: needsInitialSdp ? 'realtime-initial-peer' : 'realtime-ice-repair',
                    iceRestart: failed,
                    force: false,
                    delay: 20
                });
            }

            return pc;
        }

        async function syncMediaToJoinedPeers() {
            if (window.__smRealtimeMedia.syncing) return;
            window.__smRealtimeMedia.syncing = true;

            try {
                const ids = joinedPeerIds();
                await Promise.allSettled(ids.map(uid => ensurePeerMedia(uid)));
            } finally {
                window.__smRealtimeMedia.syncing = false;
            }
        }

        function unlockIncomingAudio() {
            document.querySelectorAll('audio[data-peer-id]').forEach(audio => {
                audio.autoplay = true;
                audio.playsInline = true;
                audio.muted = false;
                audio.defaultMuted = false;
                audio.volume = 1;
                audio.play().catch(() => {});
            });
        }

        ['pointerdown', 'click', 'touchstart', 'keydown'].forEach(eventName => {
            document.addEventListener(eventName, unlockIncomingAudio, {
                passive: true
            });
        });

        /* ---------------- MIC: authoritative final override ---------------- */
        const previousToggleMic = window.toggleMic || (typeof toggleMic === 'function' ? toggleMic : null);

        window.toggleMic = async function () {
            if (!localStream || !localStream.getAudioTracks().some(t => t.readyState === 'live')) {
                await startAudio();
            }

            const track = liveAudioTrack();

            if (!track) {
                showToast('🎙️ Microphone could not start. Allow microphone access in browser settings.');
                return;
            }

            isMicOn = !Boolean(isMicOn);
            track.enabled = isMicOn;

            const button = document.getElementById('ctrl-mic');
            const micOff = document.getElementById('micoff-' + SELF);
            const speaking = document.getElementById('speaking-' + SELF);

            if (button) {
                button.innerHTML = isMicOn
                    ? '<i class="fa fa-microphone"></i>'
                    : '<i class="fa fa-microphone-slash"></i>';
                button.classList.toggle('off', !isMicOn);
            }

            if (micOff) micOff.style.display = isMicOn ? 'none' : 'flex';
            if (!isMicOn && speaking) speaking.style.display = 'none';

            if (isMicOn) {
                if (!recognition) {
                    try { startTranscript(); } catch (e) {}
                }
                setTimeout(() => {
                    try { startRecognition(); } catch (e) {}
                }, 40);
            } else {
                try { stopRecognition(); } catch (e) {}
            }

            /*
             * The same audio track remains installed in every sender. Enabling it
             * starts sound immediately; no refresh and no healthy-peer renegotiation.
             */
            await syncMediaToJoinedPeers();

            try { broadcastMyMicStatus(); } catch (e) {
                sendSignal('all', 'mic-status', {
                    userId: MY_USER_ID,
                    muted: !isMicOn
                });
            }

            /* A small second replaceTrack pass helps Safari/Chrome after permission. */
            if (isMicOn) {
                setTimeout(syncMediaToJoinedPeers, 180);
            }
        };

        try { toggleMic = window.toggleMic; } catch (e) {}

        /* ---------------- CAMERA: authoritative final override ------------- */
        window.toggleCamera = async function () {
            if (!localStream) {
                await startAudio();
                if (!localStream) return;
            }

            let track = liveVideoTrack();

            if (!isCameraOn && !track) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: {
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                            frameRate: { ideal: 24, max: 30 },
                            facingMode: 'user'
                        }
                    });

                    track = stream.getVideoTracks()[0] || null;

                    if (!track) throw new Error('No camera video track returned.');

                    /* Remove only dead/stale video tracks. */
                    localStream.getVideoTracks().forEach(oldTrack => {
                        if (oldTrack !== track && oldTrack.readyState !== 'live') {
                            try { localStream.removeTrack(oldTrack); } catch (e) {}
                            try { oldTrack.stop(); } catch (e) {}
                        }
                    });

                    localStream.addTrack(track);

                    try { track.contentHint = 'motion'; } catch (e) {}
                } catch (error) {
                    console.error('Camera access failed:', error);
                    showToast('📷 Camera could not start. Allow camera access in browser settings.');
                    return;
                }
            }

            track = liveVideoTrack();

            if (!track) {
                showToast('📷 Camera track is unavailable.');
                return;
            }

            isCameraOn = !Boolean(isCameraOn);
            track.enabled = isCameraOn;

            const button = document.getElementById('ctrl-camera');
            const localVideo = document.getElementById('localVideo');
            const avatar = document.getElementById('avatar-' + SELF);

            if (button) {
                button.innerHTML = isCameraOn
                    ? '<i class="fa fa-video"></i>'
                    : '<i class="fa fa-video-slash"></i>';
                button.classList.toggle('off', !isCameraOn);
            }

            if (localVideo) {
                localVideo.srcObject = localStream;
                localVideo.autoplay = true;
                localVideo.playsInline = true;
                localVideo.muted = true;
                localVideo.style.display = isCameraOn ? 'block' : 'none';

                if (isCameraOn) {
                    localVideo.play().catch(() => {});
                }
            }

            if (avatar) avatar.style.display = isCameraOn ? 'none' : 'flex';

            /*
             * Replace the sender's video track for every currently joined user.
             * Because the video transceiver was negotiated from peer creation,
             * turning camera on becomes visible remotely without page refresh.
             */
            await syncMediaToJoinedPeers();

            try { broadcastMyCameraStatus(); } catch (e) {
                sendSignal('all', 'camera-status', {
                    userId: MY_USER_ID,
                    cameraOn: isCameraOn
                });
            }

            if (isCameraOn) {
                [160, 520].forEach(delay => {
                    setTimeout(async () => {
                        if (!isCameraOn) return;
                        await syncMediaToJoinedPeers();
                        try { broadcastMyCameraStatus(); } catch (e) {}
                    }, delay);
                });
            }
        };

        try { toggleCamera = window.toggleCamera; } catch (e) {}

        /* ---------------- Realtime join/status bridge ---------------------- */
        function onRealtimeSignal(event) {
            if (!event?.type) return;

            const uid = String(event.data?.userId || event.fromUserId || '');
            if (!uid || uid === SELF) return;

            if (event.type === 'user-joined') {
                leftUsers.delete(uid);

                if (!knownParticipants[uid]) {
                    knownParticipants[uid] = {
                        name: event.data?.name || 'Participant',
                        initials: event.data?.initials || '?',
                        isOrganizer: Boolean(event.data?.isOrganizer),
                        hasJoined: true
                    };
                } else {
                    knownParticipants[uid].hasJoined = true;
                }

                try {
                    const info = knownParticipants[uid];
                    ensurePanelRow(uid, info.name, info.initials, Boolean(info.isOrganizer));
                    addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer));
                    markOnline(uid);
                } catch (e) {}

                /* Create/sync immediately so no page refresh is required. */
                ensurePeerMedia(uid).then(() => {
                    setTimeout(() => ensurePeerMedia(uid), 180);
                });

                setTimeout(() => {
                    try { broadcastMyMicStatus(); } catch (e) {}
                    try { broadcastMyCameraStatus(); } catch (e) {}
                }, 80);

                return;
            }

            if (event.type === 'camera-status' && event.data?.cameraOn) {
                ensurePeerMedia(uid);
                setTimeout(() => {
                    try { attachRemoteStream(uid); } catch (e) {}
                }, 120);
                return;
            }

            if (event.type === 'mic-status' && !event.data?.muted) {
                ensurePeerMedia(uid);
                setTimeout(unlockIncomingAudio, 80);
            }
        }

        if (!window.__smRealtimeMedia.bound && window.Echo) {
            window.__smRealtimeMedia.bound = true;
            window.Echo.channel(ROOM).listen('.signal', onRealtimeSignal);
        }

        /* Initial room repair: current live users connect without refresh. */
        window.addEventListener('load', () => {
            [120, 480, 1200].forEach(delay => {
                setTimeout(async () => {
                    await syncMediaToJoinedPeers();
                    try { announceJoin(); } catch (e) {}
                    try { broadcastMyMicStatus(); } catch (e) {}
                    try { broadcastMyCameraStatus(); } catch (e) {}
                }, delay);
            });
        });
    })();
</script>


<script id="sm-side-panel-video-repair">
    /* ================================================================
       FINAL PANEL RESIZE + REMOTE VIDEO REPAIR
    ================================================================ */
    (() => {
        /* ---------------- Side panel position / resize ---------------- */
        const panel = document.getElementById('side-panel');

        if (panel && !panel.dataset.smResizableReady) {
            panel.dataset.smResizableReady = '1';
            panel.classList.add('sm-resizable-panel');

            const header = document.createElement('div');
            header.className = 'sm-panel-resize-header';
            header.title = 'Drag up/down to resize';

            const dragLine = document.createElement('div');
            dragLine.className = 'sm-panel-drag-line';

            const collapse = document.createElement('button');
            collapse.type = 'button';
            collapse.className = 'sm-panel-collapse-btn';
            collapse.title = 'Minimize / expand panel';
            collapse.innerHTML = '<i class="fa fa-chevron-up"></i>';

            header.appendChild(dragLine);
            header.appendChild(collapse);
            panel.insertBefore(header, panel.firstChild);

            let dragging = false;
            let startY = 0;
            let startHeight = 0;

            const beginResize = (clientY) => {
                if (panel.classList.contains('sm-panel-collapsed')) return;
                dragging = true;
                startY = clientY;
                startHeight = panel.getBoundingClientRect().height;
                document.body.style.userSelect = 'none';
            };

            const moveResize = (clientY) => {
                if (!dragging) return;

                /*
                 * Panel bottom remains stable. Pulling the TOP handle upward makes
                 * the panel taller; pulling it downward makes it shorter.
                 */
                const delta = startY - clientY;
                const maxHeight = Math.max(220, window.innerHeight - 145);
                const nextHeight = Math.max(190, Math.min(maxHeight, startHeight + delta));

                panel.style.setProperty('height', nextHeight + 'px', 'important');
                panel.style.setProperty('top', 'auto', 'important');
                panel.style.setProperty('max-height', maxHeight + 'px', 'important');
            };

            const endResize = () => {
                if (!dragging) return;
                dragging = false;
                document.body.style.userSelect = '';
            };

            header.addEventListener('mousedown', e => {
                if (e.target.closest('.sm-panel-collapse-btn')) return;
                beginResize(e.clientY);
            });

            document.addEventListener('mousemove', e => moveResize(e.clientY));
            document.addEventListener('mouseup', endResize);

            header.addEventListener('touchstart', e => {
                if (e.target.closest('.sm-panel-collapse-btn')) return;
                const t = e.touches[0];
                if (t) beginResize(t.clientY);
            }, { passive: true });

            document.addEventListener('touchmove', e => {
                if (!dragging) return;
                const t = e.touches[0];
                if (t) moveResize(t.clientY);
            }, { passive: true });

            document.addEventListener('touchend', endResize);

            collapse.addEventListener('click', e => {
                e.stopPropagation();
                panel.classList.toggle('sm-panel-collapsed');
            });

            /*
             * Keep compatibility with the existing toggleSidePanel().
             * MutationObserver only mirrors its display state into our final class.
             */
            const syncOpenState = () => {
                const hidden = panel.style.display === 'none';
                panel.classList.toggle('sm-panel-open', !hidden);
            };

            new MutationObserver(syncOpenState).observe(panel, {
                attributes: true,
                attributeFilter: ['style']
            });

            syncOpenState();
        }

        /* ---------------- Remote video visibility repair ----------------
           A received LIVE video track is authoritative. This fixes the case where
           the WebRTC track arrives before/after camera-status and the avatar stays
           visible even though the other user's camera is actually streaming.
        ---------------------------------------------------------------- */
        function repairRemoteVideo(uid) {
            uid = String(uid || '');
            if (!uid || uid === String(MY_USER_ID)) return;

            const stream = remoteStreams?.[uid];
            const video = document.getElementById('rvideo-' + uid);
            const avatar = document.getElementById('avatar-' + uid);

            if (!stream || !video) return;

            const tracks = stream.getVideoTracks().filter(track =>
                track.readyState === 'live'
            );

            if (!tracks.length) return;

            const liveTrack = tracks.find(track => track.enabled && !track.muted) || tracks[0];

            if (!(video.srcObject instanceof MediaStream) ||
                !video.srcObject.getVideoTracks().some(t => t.id === liveTrack.id)) {
                video.srcObject = new MediaStream([liveTrack]);
            }

            video.autoplay = true;
            video.playsInline = true;
            video.muted = true;

            const show = () => {
                if (liveTrack.readyState !== 'live') return;
                participantCameraStatus[uid] = true;
                video.style.display = 'block';
                if (avatar) avatar.style.display = 'none';
                video.play().catch(() => {});
            };

            if (!liveTrack.muted) show();
            liveTrack.addEventListener('unmute', show, { once: true });

            setTimeout(() => {
                if (liveTrack.readyState === 'live' && !liveTrack.muted) show();
            }, 180);
        }

        function repairAllRemoteVideos() {
            try {
                Object.keys(remoteStreams || {}).forEach(repairRemoteVideo);
            } catch (e) {}
        }

        /*
         * Re-sync current local camera/audio tracks to every joined peer.
         * Uses the existing replaceTrack-based implementation; it does NOT add
         * new m-lines or repeatedly renegotiate healthy peer connections.
         */
        async function resyncCurrentMedia() {
            try {
                if (typeof syncMediaToJoinedPeers === 'function') {
                    await syncMediaToJoinedPeers();
                } else if (typeof syncTracksToEveryPeer === 'function') {
                    await syncTracksToEveryPeer(false);
                }
            } catch (e) {
                console.warn('SmartMeet media resync:', e);
            }

            repairAllRemoteVideos();
        }

        /* When a camera-status arrives, immediately repair the receiving tile. */
        if (window.Echo && typeof ROOM !== 'undefined' && !window.__smFinalVideoRepairBound) {
            window.__smFinalVideoRepairBound = true;

            window.Echo.channel(ROOM).listen('.signal', event => {
                const uid = String(event?.data?.userId || event?.fromUserId || '');
                if (!uid || uid === String(MY_USER_ID)) return;

                if (event?.type === 'camera-status' && event?.data?.cameraOn) {
                    setTimeout(() => repairRemoteVideo(uid), 40);
                    setTimeout(() => repairRemoteVideo(uid), 220);
                    setTimeout(resyncCurrentMedia, 320);
                }

                if (event?.type === 'user-joined') {
                    setTimeout(resyncCurrentMedia, 120);
                    setTimeout(resyncCurrentMedia, 650);
                }
            });
        }

        window.addEventListener('load', () => {
            [250, 900, 1800].forEach(ms => setTimeout(resyncCurrentMedia, ms));
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) setTimeout(resyncCurrentMedia, 120);
        });
    })();
</script>

</body>
</html>

