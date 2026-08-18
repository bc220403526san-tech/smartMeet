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

    const iceConfig = {

        iceServers: [

            { urls: 'stun:stun.l.google.com:19302' },

            { urls: 'stun:stun1.l.google.com:19302' },

            { urls: 'stun:stun2.l.google.com:19302' },

            { urls: 'stun:stun3.l.google.com:19302' },

            { urls: 'stun:stun4.l.google.com:19302' },

            { urls: 'stun:stun.ekiga.net' },

            { urls: 'stun:stun.ideasip.com' },

            { urls: 'stun:stun.schlund.de' },

        ],

        iceCandidatePoolSize: 10,

        iceTransportPolicy: 'all',

        bundlePolicy: 'max-bundle',

        rtcpMuxPolicy: 'require'

    };

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
                    delay: 80
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

            if (p.hasJoined) {

                addParticipantTile(p.userId, p.name, p.initials, false);

                markOnline(p.userId);

                createPeerConnection(p.userId);

            }

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

                try {
                    const cameraStream = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: { width: { ideal: 960 }, height: { ideal: 540 }, facingMode: 'user' }
                    });
                    cameraStream.getVideoTracks().forEach(track => {
                        track.enabled = false;
                        localStream.addTrack(track);
                    });
                } catch (cameraError) {
                    console.warn('Camera unavailable; continuing with audio:', cameraError);
                    const camBtn = document.getElementById('ctrl-camera');
                    if (camBtn?.parentElement) camBtn.parentElement.style.display = 'none';
                }

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

            if (uid === String(MY_USER_ID)) return;

            participantMicStatus[uid] = data.data.muted;

            const micOff = document.getElementById('micoff-' + uid);

            if (micOff) micOff.style.display = data.data.muted ? 'flex' : 'none';

            return;

        }

        if (data.type === 'camera-status') {

            const uid = String(data.data.userId || data.fromUserId);

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

                showToast('You have been muted by the organizer');

                broadcastMyMicStatus();

            } else if (data.type === 'unmute') {

                showToast('The organizer has unmuted you');

            }

        } catch (err) { console.error('Signal handle error:', err); }

    }

    async function sendSignal(toUserId, type, data) {

        try {

            const res = await fetch(SIGNAL_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({ to_user_id: toUserId, type, data }) });

            if (!res.ok) console.error('sendSignal failed:', await res.text());

        } catch (err) { console.error('sendSignal error:', err); }

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

            startRecognition();

        } else {

            if (btn) { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }

            if (micOff) micOff.style.display = 'flex';

            if (speaking) speaking.style.display = 'none';

            stopRecognition();

        }

        // The audio track remains attached while muted. Enabling it
        // therefore starts audio-only calling immediately on every peer.
        await syncTracksToEveryPeer(false);
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
            if (e.error !== 'aborted' && e.error !== 'no-speech') console.warn('Speech recognition:', e.error);
            if (isMicOn && document.visibilityState === 'visible') setTimeout(startRecognition, 180);
        };

        recognition.onend = () => {
            recognitionRunning = false;
            if (indicator) indicator.style.display = 'none';
            if (isMicOn && document.visibilityState === 'visible') setTimeout(startRecognition, 120);
        };

    }

    function startRecognition() {
        if (!recognition || recognitionRunning || !isMicOn || document.visibilityState !== 'visible') return;
        try { recognition.start(); } catch (e) {}
    }

    function toggleTranscriptLanguage() {
        currentLang = 'en-US';
        const btn = document.getElementById('lang-toggle-btn');
        if (btn) btn.textContent = '🌐 English only';
        showToast('Transcript language: English');
        if (recognition) { stopRecognition(); recognition = null; }
        startTranscript();
        if (isMicOn) setTimeout(startRecognition, 120);
    }

    function stopRecognition() {
        if (!recognition) return;
        recognitionRunning = false;
        try { recognition.abort(); } catch(e) {}
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
            <div class="chat-message-avatar">${escapeHtml(isMe ? MY_INITIALS : initials)}</div>
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

</script>
</body>
</html>
