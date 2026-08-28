<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">

    <title>{{ env('APP_NAME') }} — {{ $meeting->title }}</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    >

    @vite([
        'resources/css/meeting-room.css',
        'resources/js/app.js'
    ])

    <style>
        .main {
            display: flex;
            min-height: 0;
        }

        .video-area {
            flex: 1;
            min-width: 0;
            position: relative;
        }

        #side-panel {
            flex-shrink: 0;
        }

        .role-badge {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: .3px;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 99px;
            margin-left: 6px;
            vertical-align: middle;
        }

        .role-badge.organizer {
            background: rgba(251,191,36,.18);
            color: #fbbf24;
        }

        .role-badge.participant {
            background: rgba(59,130,246,.18);
            color: #60a5fa;
        }

        .participant-online {
            background: rgba(34,197,94,.08);
            border: 1px solid rgba(34,197,94,.2);
            opacity: 1;
        }

        .participant-offline {
            background: var(--surface2);
            border: 1px solid var(--border);
            opacity: .5;
        }

        .video-placeholder {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 50% 35%,
                    rgba(124,58,237,.14),
                    transparent 45%
                );
        }

        .video-placeholder video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
            background: #0f172a;
        }

        .video-placeholder video.mirrored {
            transform: scaleX(-1);
        }

        .ctrl-icon.off {
            opacity: .85;
        }

        .video-tile {
            position: relative;
        }

        .tile-expand-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 25px;
            height: 25px;
            border-radius: 9px;
            background: rgba(15,23,42,.65);
            border: 1px solid rgba(255,255,255,.15);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
            z-index: 6;
            opacity: 0;
            transition:
                opacity .2s,
                background .2s;
        }

        .video-tile:hover .tile-expand-btn,
        .video-tile.maximized .tile-expand-btn {
            opacity: 1;
        }

        .tile-expand-btn:hover {
            background: rgba(59,130,246,.85);
        }

        @media (hover:none) {
            .tile-expand-btn {
                opacity: 1;
            }
        }

        #maximized-overlay {
            position: absolute;
            inset: 0;
            z-index: 30;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            display: none;
        }

        #maximized-overlay.active {
            display: block;
        }

        #maximized-overlay .video-tile {
            width: 100%;
            height: 100%;
        }

        .maximize-close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(15,23,42,.75);
            border: 1px solid rgba(255,255,255,.15);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
        }

        .maximize-close-btn:hover {
            background: rgba(239,68,68,.85);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
            height: 100%;
        }

        :root {
            --room-bg: #050816;
            --room-panel: rgba(15,23,42,.82);
            --room-card: rgba(15,23,42,.68);
            --room-card-border: rgba(148,163,184,.16);
            --room-accent: #7c3aed;
            --room-accent-2: #06b6d4;
            --room-line: rgba(148,163,184,.14);
            --room-text: #f8fafc;
            --room-muted: #94a3b8;
            --room-primary: #3b82f6;
            --room-danger: #ef4444;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100dvh;
            color: var(--room-text);

            background:
                radial-gradient(
                    circle at 12% 8%,
                    rgba(124,58,237,.18),
                    transparent 31%
                ),
                radial-gradient(
                    circle at 88% 18%,
                    rgba(6,182,212,.13),
                    transparent 27%
                ),
                linear-gradient(
                    145deg,
                    #040712 0%,
                    #07111f 48%,
                    #050816 100%
                ) !important;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: -1;

            background-image:
                linear-gradient(
                    rgba(255,255,255,.018) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255,255,255,.018) 1px,
                    transparent 1px
                );

            background-size: 36px 36px;
        }

        .header {
            min-height: 54px !important;
            height: auto !important;
            padding: 7px 14px !important;
            gap: 10px !important;
            flex-wrap: wrap;
            row-gap: 6px;

            background: rgba(5,8,22,.82) !important;
            border-bottom: 1px solid rgba(148,163,184,.12) !important;

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);

            box-shadow: 0 10px 35px rgba(0,0,0,.16);
            z-index: 60;
        }

        .header-left {
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
            gap: 9px !important;
        }

        .header-right,
        .header-center,
        .header-brand {
            flex-shrink: 0;
        }

        .header-brand {
            gap: 8px !important;
            padding-right: 12px !important;
        }

        .header-brand img {
            width: 27px !important;
            height: 27px !important;
        }

        .header-brand-text > div:first-child {
            font-size: 12px !important;
        }

        .header-brand-text > div:last-child {
            font-size: 8px !important;
        }

        .header-meeting-info {
            min-width: 0;
            overflow: hidden;
        }

        .live-badge {
            font-size: 9px !important;
            padding: 3px 8px !important;
            border-radius: 999px !important;
            letter-spacing: .55px;
        }

        .meeting-title {
            max-width: 40vw;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 13px !important;
            font-weight: 700 !important;
        }

        .meeting-meta {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 9px !important;
        }

        .header-center {
            min-width: 108px;
            padding: 5px 10px !important;
            border: 1px solid var(--room-line);
            border-radius: 10px !important;
            background: rgba(255,255,255,.04);
            font-size: 12px !important;
            gap: 6px !important;
        }

        .timer-icon {
            font-size: 10px !important;
        }

        .participants-count {
            padding: 5px 9px !important;
            border-radius: 9px !important;
            font-size: 10px !important;
            background: rgba(15,23,42,.58) !important;
            border: 1px solid var(--room-line);
        }

        .btn-leave {
            min-height: 31px !important;
            padding: 6px 11px !important;
            border-radius: 10px !important;
            font-size: 10px !important;
            gap: 5px !important;
            box-shadow: none !important;
        }

        .main {
            flex: 1 1 auto;
            min-height: 0;
            height: calc(100dvh - 118px) !important;
            padding: 10px !important;
            gap: 10px !important;
            overflow: hidden;
        }

        .video-area {
            border: 1px solid rgba(125,211,252,.15) !important;
            border-radius: 22px;
            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(8,15,31,.88),
                    rgba(9,20,38,.72)
                ) !important;

            box-shadow:
                0 24px 70px rgba(0,0,0,.32),
                inset 0 1px 0 rgba(255,255,255,.04) !important;
        }

        .video-grid {
            height: 100%;
            display: grid;
            padding: 16px !important;
            gap: 16px !important;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(min(250px,100%),1fr)
                ) !important;

            grid-auto-rows:
                minmax(180px,1fr) !important;

            align-content: start !important;
        }

        .video-tile {
            min-height: 180px;

            border-radius: 20px !important;
            overflow: hidden;

            border:
                1px solid
                rgba(148,163,184,.18) !important;

            background:
                linear-gradient(
                    155deg,
                    rgba(24,38,64,.95),
                    rgba(7,15,30,.98)
                ) !important;

            box-shadow:
                0 18px 45px rgba(0,0,0,.3),
                inset 0 1px 0 rgba(255,255,255,.04) !important;

            transition:
                transform .22s ease,
                border-color .22s ease,
                box-shadow .22s ease;
        }

        .video-tile:hover {
            transform: translateY(-2px);

            border-color:
                rgba(129,140,248,.42) !important;

            box-shadow:
                0 21px 52px rgba(0,0,0,.32),
                0 0 0 1px rgba(124,58,237,.08);
        }

        .video-tile::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: inherit;

            background:
                linear-gradient(
                    135deg,
                    rgba(56,189,248,.07),
                    transparent 38%,
                    rgba(139,92,246,.06)
                );
        }

        .avatar-circle,
        .avatar-circle.lg {
            box-shadow:
                0 14px 38px rgba(0,0,0,.28),
                0 0 0 6px rgba(255,255,255,.045);
        }

        .avatar-circle.lg {
            width: 78px !important;
            height: 78px !important;
            font-size: 25px !important;
        }

        .avatar-circle:not(.lg) {
            width: 62px !important;
            height: 62px !important;
            font-size: 20px !important;
        }

        .tile-info {
            min-height: 42px !important;
            padding: 8px 10px !important;

            background:
                linear-gradient(
                    to top,
                    rgba(2,6,23,.94),
                    rgba(2,6,23,.70)
                ) !important;

            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .tile-name {
            font-size: 11px !important;
            gap: 3px !important;
        }

        .role-badge {
            font-size: 7px !important;
            padding: 2px 5px !important;
            margin-left: 3px !important;
        }

        .you-badge {
            top: 7px !important;
            left: 7px !important;
            font-size: 8px !important;
            padding: 2px 6px !important;
        }

        .mic-off {
            width: 24px !important;
            height: 24px !important;
            font-size: 10px !important;
        }

        #side-panel {
            width: min(330px,33vw) !important;
            min-width: 280px;
            height: 100%;

            border:
                1px solid
                rgba(125,211,252,.16) !important;

            border-radius: 20px !important;

            background:
                linear-gradient(
                    180deg,
                    rgba(13,24,44,.96),
                    rgba(7,14,29,.96)
                ) !important;

            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);

            box-shadow:
                0 20px 60px rgba(0,0,0,.35);

            overflow: hidden;
        }

        .transcript-body,
        .chat-body {
            padding: 10px !important;
        }

        .transcript-entry {
            border-radius: 13px;
            padding: 9px !important;
            margin-bottom: 7px;
            background: rgba(255,255,255,.025);
        }

        .transcript-avatar {
            width: 29px !important;
            height: 29px !important;
            font-size: 9px !important;
        }

        .transcript-name {
            font-size: 10px !important;
        }

        .transcript-time {
            font-size: 8px !important;
        }

        .transcript-text {
            font-size: 11px !important;
            line-height: 1.45 !important;
        }

        .chat-body {
            display: flex !important;
            flex-direction: column;
            gap: 12px;
            padding: 14px !important;

            background:
                radial-gradient(
                    circle at 90% 0,
                    rgba(59,130,246,.07),
                    transparent 35%
                );
        }

        .chat-message-row {
            display: flex;
            align-items: flex-end;
            gap: 9px;
            width: 100%;
        }

        .chat-message-row.is-me {
            flex-direction: row-reverse;
        }

        .chat-message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 11px;
            flex: 0 0 32px;

            display: grid;
            place-items: center;

            color: #fff;
            font-size: 10px;
            font-weight: 800;

            background:
                linear-gradient(
                    135deg,
                    #8b5cf6,
                    #ec4899
                );

            box-shadow:
                0 7px 18px rgba(0,0,0,.24);
        }

        .chat-message-row.is-me .chat-message-avatar {
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #06b6d4
                );
        }

        .chat-message-content {
            max-width: min(82%,290px);
            min-width: 0;
        }

        .chat-message-meta {
            display: flex;
            gap: 8px;
            align-items: center;
            margin: 0 5px 5px;

            color: #94a3b8;
            font-size: 9px;
        }

        .chat-message-meta strong {
            color: #e2e8f0;
            font-size: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-message-meta span {
            margin-left: auto;
            flex-shrink: 0;
        }

        .chat-message-row.is-me .chat-message-meta {
            flex-direction: row-reverse;
        }

        .chat-message-row.is-me .chat-message-meta span {
            margin-left: 0;
            margin-right: auto;
        }

        .chat-message-bubble {
            padding: 10px 12px;

            border-radius:
                5px 16px 16px 16px;

            background:
                rgba(30,41,59,.88);

            border:
                1px solid
                rgba(148,163,184,.14);

            color: #f8fafc;

            font-size: 12px;
            line-height: 1.5;

            word-break: break-word;
        }

        .chat-message-row.is-me .chat-message-bubble {
            border-radius:
                16px 5px 16px 16px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #0891b2
                );

            border-color:
                rgba(125,211,252,.28);
        }

        .chat-input-area {
            padding: 12px !important;
            gap: 7px !important;

            background:
                rgba(2,6,23,.56);

            border-top:
                1px solid
                rgba(148,163,184,.12);
        }

        .chat-input {
            min-height: 40px !important;

            padding:
                7px 10px !important;

            border-radius:
                13px !important;

            font-size:
                11px !important;

            background:
                rgba(15,23,42,.9) !important;

            border:
                1px solid
                rgba(148,163,184,.18) !important;
        }

        .chat-input:focus {
            border-color:
                rgba(56,189,248,.65) !important;

            box-shadow:
                0 0 0 3px
                rgba(56,189,248,.09);
        }

        .btn-send {
            min-width: 40px !important;
            width: 40px !important;
            height: 40px !important;

            border-radius:
                13px !important;

            font-size:
                11px !important;
        }

        #participants-list > div,
        #other-participants-panel > div {
            transition:
                background .18s ease,
                opacity .18s ease,
                border-color .18s ease;
        }

        .controls {
            flex: 0 0 auto;

            min-height: 64px !important;
            height: 64px !important;

            padding: 6px 10px !important;

            gap: 5px !important;

            margin:
                0 12px 12px !important;

            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;

            border:
                1px solid
                rgba(148,163,184,.14) !important;

            border-radius:
                18px !important;

            background:
                rgba(5,12,26,.88) !important;

            backdrop-filter:
                blur(18px);

            -webkit-backdrop-filter:
                blur(18px);

            justify-content: center;
            z-index: 70;
        }

        .controls::-webkit-scrollbar {
            display: none;
        }

        .ctrl-btn {
            min-width: 48px !important;
            gap: 3px !important;
            padding: 2px 4px !important;
            border-radius: 10px;
        }

        .ctrl-btn:hover {
            background:
                rgba(148,163,184,.07);
        }

        .ctrl-icon,
        .btn-end {
            width: 34px !important;
            height: 34px !important;

            min-width: 34px !important;
            min-height: 34px !important;

            border-radius:
                12px !important;

            font-size:
                12px !important;

            box-shadow:
                inset 0 1px 0
                rgba(255,255,255,.05);

            transition:
                transform .16s ease,
                background .16s ease,
                border-color .16s ease !important;
        }

        .ctrl-icon {
            background:
                rgba(30,41,59,.72) !important;

            border:
                1px solid
                rgba(148,163,184,.13) !important;
        }

        .ctrl-icon.active {
            background:
                rgba(59,130,246,.22) !important;

            border-color:
                rgba(96,165,250,.45) !important;
        }

        .ctrl-icon.off {
            background:
                rgba(51,65,85,.72) !important;
        }

        .ctrl-btn:hover .ctrl-icon,
        .btn-end:hover {
            transform:
                translateY(-2px);
        }

        .ctrl-label {
            font-size: 8px !important;
            line-height: 1 !important;
        }

        .ctrl-divider {
            height: 28px !important;
            margin: 0 3px !important;
            opacity: .45;
        }

        #listening-indicator {
            font-size: 10px !important;
            padding: 6px 10px !important;
        }

        #lang-toggle-btn {
            min-height: 29px;
        }

        #toast-stack {
            position: fixed;
            bottom: 78px !important;
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

            background:
                rgba(15,23,42,.92);

            backdrop-filter:
                blur(12px);

            -webkit-backdrop-filter:
                blur(12px);

            border:
                1px solid
                rgba(255,255,255,.10);

            color: #fff;

            padding:
                9px 13px !important;

            border-radius:
                11px !important;

            font-size:
                11px !important;

            font-weight: 500;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.35);

            opacity: 0;

            transform:
                translateY(16px)
                scale(.98);

            transition:
                opacity .25s ease,
                transform .25s ease;

            max-width:
                min(90vw,420px);
        }

        .toast.show {
            opacity: 1;

            transform:
                translateY(0)
                scale(1);
        }

        .toast.leaving {
            opacity: 0;

            transform:
                translateY(-6px)
                scale(.98);
        }

        @media (max-width:900px) {
            .header {
                gap: 8px;
                padding: 8px 12px;
            }

            .header-center {
                order: initial !important;
                width: auto !important;
            }

            .main {
                flex-direction: column;

                height:
                    calc(100dvh - 112px) !important;

                padding:
                    10px !important;

                gap:
                    10px !important;
            }

            #side-panel {
                position: fixed;

                left: 0;
                right: 0;

                top: auto;

                bottom:
                    64px !important;

                width:
                    100% !important;

                min-width: 0;

                height:
                    min(64dvh,580px) !important;

                border-radius:
                    20px 20px 0 0 !important;

                z-index: 50;

                box-shadow:
                    0 -4px 24px
                    rgba(0,0,0,.4);
            }

            .video-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0,1fr)
                    ) !important;

                grid-auto-rows:
                    minmax(145px,1fr) !important;

                padding:
                    12px !important;

                gap:
                    12px !important;
            }

            .video-tile {
                min-height:
                    145px;
            }

            .controls {
                min-height:
                    59px !important;

                padding:
                    6px 8px !important;

                margin:
                    0 8px 8px !important;

                justify-content:
                    flex-start;
            }

            .ctrl-icon,
            .btn-end {
                width:
                    32px !important;

                height:
                    32px !important;

                min-width:
                    32px !important;

                min-height:
                    32px !important;
            }

            .ctrl-btn {
                min-width:
                    43px !important;
            }
        }

        @media (max-width:640px) {
            .header {
                min-height:
                    49px !important;

                padding:
                    6px 8px !important;
            }

            .header-brand,
            .participants-count {
                display:
                    none !important;
            }

            .meeting-meta {
                display:
                    none !important;
            }

            .meeting-title {
                max-width:
                    42vw !important;

                font-size:
                    11px !important;
            }

            .header-center {
                min-width:
                    88px;

                font-size:
                    10px !important;

                padding:
                    4px 7px !important;
            }

            .main {
                height:
                    calc(100dvh - 107px) !important;

                padding:
                    8px !important;
            }

            .video-area {
                border-radius:
                    16px !important;
            }

            .video-grid {
                display:
                    grid !important;

                grid-template-columns:
                    1fr !important;

                grid-auto-rows:
                    minmax(210px,auto) !important;

                padding:
                    12px !important;

                gap:
                    14px !important;

                overflow-y:
                    auto !important;

                align-content:
                    start !important;
            }

            .video-tile {
                width:
                    100% !important;

                min-height:
                    210px !important;

                margin:
                    0 !important;

                border-radius:
                    17px !important;
            }

            .avatar-circle.lg,
            .avatar-circle {
                width:
                    54px !important;

                height:
                    54px !important;

                font-size:
                    18px !important;
            }

            .tile-info {
                min-height:
                    35px !important;

                padding:
                    6px 7px !important;
            }

            .tile-name {
                max-width:
                    calc(100% - 30px);

                overflow:
                    hidden;

                white-space:
                    nowrap;

                text-overflow:
                    ellipsis;

                font-size:
                    9px !important;
            }

            .role-badge {
                display:
                    none;
            }

            #side-panel {
                left:
                    8px !important;

                right:
                    8px !important;

                width:
                    auto !important;

                bottom:
                    72px !important;
            }

            .controls {
                height:
                    58px !important;

                min-height:
                    58px !important;

                margin:
                    0 6px 6px !important;

                padding:
                    7px 6px !important;

                border-radius:
                    16px !important;

                justify-content:
                    flex-start !important;

                gap:
                    2px !important;
            }

            .ctrl-btn {
                min-width:
                    43px !important;
            }

            .ctrl-icon,
            .btn-end {
                width:
                    31px !important;

                height:
                    31px !important;

                min-width:
                    31px !important;

                min-height:
                    31px !important;

                font-size:
                    11px !important;
            }

            .ctrl-label {
                font-size:
                    7px !important;
            }

            .chat-message-content {
                max-width:
                    78%;
            }
        }

        @media (max-width:390px) {
            .video-grid {
                padding:
                    10px !important;

                gap:
                    12px !important;

                grid-auto-rows:
                    minmax(190px,auto) !important;
            }

            .video-tile {
                min-height:
                    190px !important;
            }

            .ctrl-divider {
                display:
                    none;
            }

            .ctrl-btn {
                min-width:
                    42px !important;
            }

            .ctrl-icon,
            .btn-end {
                width:
                    32px !important;

                height:
                    32px !important;

                min-width:
                    32px !important;

                min-height:
                    32px !important;
            }
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


        /* SMARTMEET SAFE MAXIMIZED VIDEO FIX
           Keep the selected tile filling the complete meeting video area.
           Does not touch WebRTC/media tracks. */
        #maximized-overlay.active {
            display: flex !important;
            align-items: stretch !important;
            justify-content: stretch !important;
        }

        #maximized-overlay.active > .video-tile {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            min-width: 0 !important;
            min-height: 0 !important;
            max-width: none !important;
            max-height: none !important;
            aspect-ratio: auto !important;
            margin: 0 !important;
            grid-column: auto !important;
            grid-row: auto !important;
            transform: none !important;
            border-radius: 12px !important;
        }

        #maximized-overlay.active > .video-tile > .video-placeholder {
            position: absolute !important;
            inset: 0 0 42px 0 !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
        }

        #maximized-overlay.active > .video-tile > .video-placeholder video {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: cover !important;
            object-position: center center !important;
        }

        #maximized-overlay.active > .video-tile > .tile-info {
            position: absolute !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            height: 42px !important;
            min-height: 42px !important;
            z-index: 8 !important;
        }

        #maximized-overlay.active .tile-expand-btn {
            opacity: 1 !important;
            z-index: 12 !important;
        }

        #maximized-overlay.active .maximize-close-btn {
            z-index: 20 !important;
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

</head>

@php
    $organizer = $meeting->organizer;

    $orgInitials = strtoupper(
        substr($organizer->name, 0, 1)
        .
        substr(
            strrchr($organizer->name, ' ') ?: ' ',
            1,
            1
        )
    );

    $colors = [
        '#3b82f6,#06b6d4',
        '#8b5cf6,#ec4899',
        '#22c55e,#06b6d4',
        '#f59e0b,#ef4444',
        '#64748b,#334155',
        '#ec4899,#f59e0b'
    ];

    $tz = $meeting->timezone ?? 'Asia/Karachi';

    $meetingEnd = null;

    if (!empty($meeting->end_time)) {
        $meetingEnd = \Carbon\Carbon::parse(
            $meeting->end_time,
            $tz
        )
            ->utc()
            ->toIso8601String();
    } else {
        $durationMinutes =
            $meeting->duration_minutes
            ??
            $meeting->duration
            ??
            null;

        if ($durationMinutes) {
            $startForCalc =
                $meeting->actual_start
                    ? \Carbon\Carbon::parse(
                        $meeting->actual_start
                    )
                    : \Carbon\Carbon::parse(
                        $meeting->date
                        .
                        ' '
                        .
                        $meeting->time,
                        $tz
                    );

            $meetingEnd =
                $startForCalc
                    ->copy()
                    ->addMinutes(
                        (int) $durationMinutes
                    )
                    ->utc()
                    ->toIso8601String();
        }
    }
@endphp

<body>

{{-- HEADER --}}
<div class="header">

    <div class="header-left">

        <div
            class="header-brand"
            style="
                display:flex;
                align-items:center;
                gap:10px;
                padding-right:16px;
                border-right:1px solid rgba(255,255,255,.08);
            "
        >
            <img
                src="{{ asset('images/s-logo.png') }}"
                style="
                    width:32px;
                    height:32px;
                    object-fit:contain;
                "
                alt="SmartMeet"
            >

            <div class="header-brand-text">
                <div
                    style="
                        font-weight:700;
                        font-size:14px;
                        color:white;
                    "
                >
                    SmartMeet
                </div>

                <div
                    style="
                        font-size:10px;
                        color:#64748b;
                    "
                >
                    Meeting Suite
                </div>
            </div>
        </div>

        <div class="live-badge">
            <div class="live-dot"></div>
            LIVE
        </div>

        <div class="header-meeting-info">

            <div class="meeting-title">
                {{ $meeting->title }}
            </div>

            <div class="meeting-meta">
                <span>
                    <i class="fa fa-users"></i>

                    <span data-total-count>
                        {{ $meeting->participants->count() + 1 }}
                    </span>

                    Participants
                </span>

                <span>·</span>

                <span>
                    {{ $meeting->timezone ?? 'Asia/Karachi' }}
                </span>
            </div>

        </div>

    </div>

    <div class="header-center">
        <i class="fa fa-clock timer-icon"></i>
        <span id="timer">00:00:00</span>
    </div>

    <div class="header-right">

        <div class="participants-count">

            <i
                class="fa fa-circle"
                style="
                    color:var(--green);
                    font-size:8px;
                "
            ></i>

            <span data-online-count>1</span>
            online
        </div>

        <button
            class="btn-leave"
            onclick="leaveMeeting()"
        >
            <i class="fa fa-phone-slash"></i>

            <span>
                Leave
            </span>
        </button>

    </div>

</div>

{{-- MAIN --}}
<div class="main">

    <div class="video-area">

        <div
            class="video-grid"
            id="video-grid"
        >

            {{-- Organizer Tile --}}
            <div
                class="video-tile"
                id="tile-{{ $organizer->id }}"
            >

                <div class="video-placeholder">

                    <video
                        id="localVideo"
                        autoplay
                        muted
                        playsinline
                        class="mirrored"
                        style="display:none;"
                    ></video>

                    <div
                        class="avatar-circle lg"
                        id="avatar-{{ $organizer->id }}"
                        style="
                            background:
                            linear-gradient(
                                135deg,
                                {{ $colors[0] }}
                            );
                        "
                    >
                        {{ $orgInitials }}
                    </div>

                    <button
                        class="tile-expand-btn"
                        onclick="toggleMaximize('{{ $organizer->id }}')"
                        title="Maximize / Minimize"
                    >
                        <i
                            class="fa fa-expand"
                            id="expand-icon-{{ $organizer->id }}"
                        ></i>
                    </button>

                </div>

                <div class="tile-info">

                    <div class="tile-name">

                        <i
                            class="fa fa-crown crown-icon"
                        ></i>

                        {{ $organizer->name }}

                        <span
                            class="role-badge organizer"
                        >
                            Organizer
                        </span>

                        <span
                            style="
                                font-size:10px;
                                background:rgba(59,130,246,.3);
                                padding:2px 6px;
                                border-radius:99px;
                                margin-left:4px;
                            "
                        >
                            You
                        </span>

                    </div>

                    <div class="tile-icons">

                        <div
                            class="speaking-indicator"
                            id="speaking-{{ $organizer->id }}"
                            style="display:none;"
                        >
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                        </div>

                        <div
                            class="mic-off"
                            id="micoff-{{ $organizer->id }}"
                            style="display:flex;"
                        >
                            <i
                                class="fa fa-microphone-slash"
                            ></i>
                        </div>

                    </div>

                </div>

                <div class="you-badge">
                    You
                </div>

            </div>

        </div>

        {{-- MAXIMIZED TILE OVERLAY --}}
        <div id="maximized-overlay">

            <button
                class="maximize-close-btn"
                onclick="restoreMaximized()"
                title="Exit fullscreen"
            >
                <i class="fa fa-compress"></i>
            </button>

        </div>

    </div>

    {{-- SIDE PANEL --}}
    <div
        class="transcript-panel"
        id="side-panel"
        style="display:none;"
    >

        {{-- TRANSCRIPT --}}
        <div
            id="tab-transcript"
            style="
                display:flex;
                flex-direction:column;
                flex:1;
                overflow:hidden;
            "
        >

            <div
                class="transcript-body"
                id="transcript-body"
            >
                <div
                    data-empty
                    style="
                        text-align:center;
                        color:#64748b;
                        font-size:12px;
                        padding:20px;
                    "
                >
                    Transcript will appear here...
                </div>
            </div>

            <div
                style="
                    display:flex;
                    justify-content:flex-end;
                    padding:8px 12px;
                    border-bottom:
                    1px solid var(--border);
                "
            >
                <button
                    onclick="toggleTranscriptLanguage()"
                    id="lang-toggle-btn"
                    style="
                        background:var(--surface2);
                        border:1px solid var(--border);
                        color:var(--muted);
                        font-size:11px;
                        padding:4px 10px;
                        border-radius:99px;
                        cursor:pointer;
                    "
                >
                    🌐 English only
                </button>
            </div>

            <div
                class="listening-indicator"
                id="listening-indicator"
                style="display:none;"
            >
                <div class="listening-dot"></div>

                <span id="listening-text">
                    Listening...
                </span>
            </div>

        </div>

        {{-- CHAT --}}
        <div
            id="tab-chat"
            class="panel-hidden"
            style="
                display:none;
                flex-direction:column;
                flex:1;
                overflow:hidden;
            "
        >

            <div
                class="chat-body"
                id="chat-body"
            >

                <div
                    data-empty
                    style="
                        text-align:center;
                        color:#64748b;
                        font-size:12px;
                        padding:20px;
                    "
                >
                    No messages yet...
                </div>

            </div>

            <div class="chat-input-area">

                <input
                    class="chat-input"
                    id="chat-input"
                    placeholder="Type a message..."
                    onkeydown="
                        if(event.key==='Enter'){
                            sendChat();
                        }
                    "
                >

                <button
                    class="btn-send"
                    onclick="sendChat()"
                >
                    <i class="fa fa-paper-plane"></i>
                </button>

            </div>

        </div>

        {{-- PARTICIPANTS --}}
        <div
            id="tab-participants"
            class="panel-hidden"
            style="
                display:none;
                flex:1;
                overflow-y:auto;
                padding:12px;
            "
        >

            <div
                id="participants-list"
                style="
                    display:flex;
                    flex-direction:column;
                    gap:8px;
                "
            >

                {{-- Organizer row --}}
                <div
                    id="panel-row-{{ $organizer->id }}"
                    class="participant-online"
                    style="
                        display:flex;
                        align-items:center;
                        gap:10px;
                        padding:10px;
                        border-radius:12px;
                    "
                >

                    <div
                        style="
                            width:36px;
                            height:36px;
                            border-radius:50%;
                            background:
                                linear-gradient(
                                    135deg,
                                    #3b82f6,
                                    #06b6d4
                                );
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:12px;
                            font-weight:700;
                            color:white;
                        "
                    >
                        {{ $orgInitials }}
                    </div>

                    <div style="flex:1;">

                        <div
                            style="
                                font-size:13px;
                                font-weight:600;
                                display:flex;
                                align-items:center;
                                gap:5px;
                            "
                        >
                            {{ $organizer->name }}

                            <i
                                class="fa fa-crown"
                                style="
                                    color:#fbbf24;
                                    font-size:10px;
                                "
                            ></i>

                            <span
                                style="
                                    font-size:10px;
                                    color:#3b82f6;
                                "
                            >
                                (You)
                            </span>
                        </div>

                        <div
                            class="join-status"
                            data-role="Organizer"
                            style="
                                font-size:10px;
                                color:var(--green);
                            "
                        >
                            Organizer • Joined
                        </div>

                    </div>

                    <span
                        class="online-dot"
                        style="
                            width:8px;
                            height:8px;
                            background:var(--green);
                            border-radius:50%;
                        "
                    ></span>

                </div>

                <div
                    id="other-participants-panel"
                ></div>

            </div>

        </div>

    </div>

</div>

{{-- CONTROLS --}}
<div class="controls">

    <div
        class="ctrl-btn"
        onclick="toggleMic()"
    >
        <div
            class="ctrl-icon"
            id="ctrl-mic"
        >
            <i class="fa fa-microphone"></i>
        </div>

        <span class="ctrl-label">
            Mic
        </span>
    </div>

    <div
        class="ctrl-btn"
        onclick="toggleCamera()"
    >
        <div
            class="ctrl-icon off"
            id="ctrl-camera"
        >
            <i class="fa fa-video-slash"></i>
        </div>

        <span class="ctrl-label">
            Camera
        </span>
    </div>

    <div class="ctrl-divider"></div>

    <div
        class="ctrl-btn"
        onclick="toggleSidePanel('transcript',this)"
    >
        <div
            class="ctrl-icon"
            id="ctrl-transcript"
        >
            <i
                class="fa fa-closed-captioning"
            ></i>
        </div>

        <span class="ctrl-label">
            Transcript
        </span>
    </div>

    <div
        class="ctrl-btn"
        onclick="toggleSidePanel('chat',this)"
        style="position:relative;"
    >
        <div
            class="ctrl-icon"
            id="ctrl-chat"
            style="position:relative;"
        >
            <i class="fa fa-comment"></i>

            <span
                id="chat-badge"
                style="
                    display:none;
                    position:absolute;
                    top:-6px;
                    right:-6px;
                    background:var(--red,#ef4444);
                    color:#fff;
                    font-size:10px;
                    font-weight:700;
                    line-height:1;
                    min-width:16px;
                    height:16px;
                    border-radius:99px;
                    align-items:center;
                    justify-content:center;
                    padding:0 4px;
                "
            >
                0
            </span>
        </div>

        <span class="ctrl-label">
            Chat
        </span>
    </div>

    <div
        class="ctrl-btn"
        onclick="toggleSidePanel('participants',this)"
    >
        <div
            class="ctrl-icon"
            id="ctrl-people"
        >
            <i class="fa fa-users"></i>
        </div>

        <span class="ctrl-label">
            People
        </span>
    </div>

    <div class="ctrl-divider"></div>

    <div class="ctrl-btn">
        <button
            class="btn-end"
            style="
                background:var(--red);
                opacity:.85;
            "
            onclick="cancelMeeting()"
        >
            <i class="fa fa-ban"></i>
        </button>

        <span
            class="ctrl-label"
            style="color:var(--red);"
        >
            Cancel
        </span>
    </div>

    <div class="ctrl-btn">
        <button
            class="btn-end"
            onclick="leaveMeeting()"
        >
            <i class="fa fa-phone-slash"></i>
        </button>

        <span
            class="ctrl-label"
            style="color:var(--red);"
        >
            Leave
        </span>
    </div>

</div>

<div id="toast-stack"></div>

<form
    id="cancel-form"
    action="{{ route('organizer.meetings.cancel', $meeting) }}"
    method="POST"
    style="display:none;"
>
    @csrf
    @method('PATCH')
</form>

<script>
    /* ============================================================
   SMARTMEET ORGANIZER ATTEND
   Existing meeting logic + immediate camera delivery fix
============================================================ */

    const MEETING_ID = "{{ $meeting->id }}";

    const MY_USER_ID = "{{ auth()->id() }}";

    const MY_NAME =
        @json(auth()->user()->name);

    const MY_INITIALS =
        @json($orgInitials);

    const SIGNAL_URL =
        @json(
        route(
            'organizer.meetings.signal',
            $meeting
        )
    );

    const TRANSCRIPT_URL =
        @json(
        route(
            'organizer.meetings.transcript',
            $meeting
        )
    );

    const MARK_LEFT_URL =
        @json(
        route(
            'organizer.meetings.markLeft',
            $meeting
        )
    );

    const LEAVE_URL =
        @json(
        route(
            'organizer.meetings.index'
        )
    );

    const CSRF =
        @json(csrf_token());

    const ALL_USER_IDS =
        @json($allUserIds);

    const ALREADY_JOINED =
        @json($alreadyJoined);

    const ALL_PARTICIPANTS =
        @json($allParticipants);

    const ORGANIZER_ID =
        "{{ $organizer->id }}";

    const MEETING_END_TIME =
        @json($meetingEnd);

    /* ============================================================
   KNOWN PARTICIPANTS
============================================================ */

    const knownParticipants = {};

    knownParticipants[ORGANIZER_ID] = {
        name:
        @json($organizer->name),

        initials:
        @json($orgInitials),

        isOrganizer:
            true,

        hasJoined:
            true
    };

    ALL_PARTICIPANTS.forEach(
        participant => {

            const uid =
                String(
                    participant.userId
                );

            knownParticipants[uid] = {
                name:
                participant.name,

                initials:
                participant.initials,

                isOrganizer:
                    false,

                hasJoined:
                    Boolean(
                        participant.hasJoined
                    )
            };

        }
    );

    ALREADY_JOINED.forEach(
        participant => {

            const uid =
                String(
                    participant.userId
                );

            knownParticipants[uid] = {
                ...(knownParticipants[uid] || {}),

                name:
                participant.name,

                initials:
                participant.initials,

                isOrganizer:
                    false,

                hasJoined:
                    true
            };

        }
    );

    /* ============================================================
   ONLINE STATE
============================================================ */

    const onlineUsers =
        new Set([
            String(MY_USER_ID)
        ]);

    const departedAnnounced =
        new Set();

    const leftUsers =
        new Set();

    function markOnline(userId) {

        const uid =
            String(userId);

        onlineUsers.add(uid);

        departedAnnounced.delete(uid);

        leftUsers.delete(uid);

        if (knownParticipants[uid]) {
            knownParticipants[
                uid
                ].hasJoined = true;
        }

        updateOnlineCount();

        updateParticipantRow(
            uid,
            true
        );
    }

    function markOffline(userId) {

        const uid =
            String(userId);

        onlineUsers.delete(uid);

        updateOnlineCount();

        updateParticipantRow(
            uid,
            false
        );
    }

    function updateOnlineCount() {

        const count =
            onlineUsers.size;

        document
            .querySelectorAll(
                '[data-online-count]'
            )
            .forEach(
                element => {

                    element.textContent =
                        count;

                }
            );
    }

    function updateParticipantRow(
        userId,
        isOnline
    ) {

        const uid =
            String(userId);

        const row =
            document.getElementById(
                'panel-row-' + uid
            );

        if (!row) {
            return;
        }

        const status =
            row.querySelector(
                '.join-status'
            );

        const role =
            status?.dataset?.role
            ||
            'Participant';

        const dot =
            row.querySelector(
                '.online-dot'
            );

        if (isOnline) {

            row.className =
                'participant-online';

            row.style.cssText =
                `
                display:flex;
                align-items:center;
                gap:10px;
                padding:10px;
                margin-top:8px;
                border-radius:12px;
                background:rgba(34,197,94,.08);
                border:1px solid rgba(34,197,94,.2);
                opacity:1;
            `;

            if (status) {
                status.textContent =
                    `${role} • Joined`;

                status.style.color =
                    'var(--green)';
            }

            if (dot) {
                dot.style.background =
                    'var(--green)';

                dot.style.border =
                    'none';
            }

            return;
        }

        row.className =
            'participant-offline';

        row.style.cssText =
            `
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px;
            margin-top:8px;
            border-radius:12px;
            background:var(--surface2);
            border:1px solid var(--border);
            opacity:.5;
        `;

        if (status) {

            const label =
                leftUsers.has(uid)
                    ? 'Left'
                    : 'Not joined yet';

            status.textContent =
                `${role} • ${label}`;

            status.style.color =
                'var(--muted)';
        }

        if (dot) {
            dot.style.background =
                'var(--surface2)';

            dot.style.border =
                '1px solid var(--border)';
        }
    }

    markOnline(MY_USER_ID);

    /* ============================================================
   TIMER
============================================================ */

    const ACTUAL_START =
        @json(
        $meeting->actual_start
            ? \Carbon\Carbon::parse(
                $meeting->actual_start
            )
                ->utc()
                ->toIso8601String()
            : now()
                ->utc()
                ->toIso8601String()
    );

    let seconds =
        Math.floor(
            (
                Date.now()
                -
                new Date(
                    ACTUAL_START
                ).getTime()
            )
            /
            1000
        );

    if (seconds < 0) {
        seconds = 0;
    }

    setInterval(
        () => {

            seconds++;

            const hours =
                String(
                    Math.floor(
                        seconds / 3600
                    )
                )
                    .padStart(
                        2,
                        '0'
                    );

            const minutes =
                String(
                    Math.floor(
                        (
                            seconds % 3600
                        )
                        /
                        60
                    )
                )
                    .padStart(
                        2,
                        '0'
                    );

            const secs =
                String(
                    seconds % 60
                )
                    .padStart(
                        2,
                        '0'
                    );

            const timer =
                document.getElementById(
                    'timer'
                );

            if (timer) {
                timer.textContent =
                    `${hours}:${minutes}:${secs}`;
            }

        },
        1000
    );

    /* ============================================================
   AUTO END
============================================================ */

    let autoEndTimer =
        null;

    let autoEndTriggered =
        false;

    function scheduleAutoEnd() {

        if (!MEETING_END_TIME) {
            return;
        }

        const msLeft =
            new Date(
                MEETING_END_TIME
            ).getTime()
            -
            Date.now();

        if (msLeft <= 0) {
            triggerAutoEnd();
            return;
        }

        autoEndTimer =
            setTimeout(
                triggerAutoEnd,
                msLeft
            );
    }

    async function triggerAutoEnd() {

        if (autoEndTriggered) {
            return;
        }

        autoEndTriggered =
            true;

        showToast(
            '⏰ Meeting time has ended.'
        );

        try {
            await sendSignal(
                'all',
                'meeting-ended',
                {
                    message:
                        'Meeting time has ended.',

                    auto:
                        true
                }
            );
        } catch (error) {}

        setTimeout(
            () => {

                cleanup();

                window.location.href =
                    LEAVE_URL;

            },
            1800
        );
    }

    /* ============================================================
   CHAT PANEL
============================================================ */

    let unreadChat =
        0;

    let activeTab =
        null;

    let panelOpen =
        false;

    function updateChatBadge() {

        const badge =
            document.getElementById(
                'chat-badge'
            );

        if (!badge) {
            return;
        }

        if (unreadChat > 0) {

            badge.textContent =
                unreadChat > 99
                    ? '99+'
                    : String(
                        unreadChat
                    );

            badge.style.display =
                'flex';

        } else {

            badge.style.display =
                'none';

        }
    }

    function switchTab(tab) {

        [
            'transcript',
            'chat',
            'participants'
        ].forEach(
            currentTab => {

                const element =
                    document.getElementById(
                        'tab-' + currentTab
                    );

                if (element) {
                    element.style.display =
                        'none';

                    element.classList.add(
                        'panel-hidden'
                    );
                }

            }
        );

        document
            .querySelectorAll(
                '.ctrl-icon'
            )
            .forEach(
                icon => {
                    icon.classList.remove(
                        'active'
                    );
                }
            );

        const active =
            document.getElementById(
                'tab-' + tab
            );

        if (active) {

            active.style.display =
                tab === 'participants'
                    ? 'block'
                    : 'flex';

            active.classList.remove(
                'panel-hidden'
            );
        }

        activeTab =
            tab;

        const icon =
            document.getElementById(
                tab === 'participants'
                    ? 'ctrl-people'
                    : 'ctrl-' + tab
            );

        if (icon) {
            icon.classList.add(
                'active'
            );
        }

        if (tab === 'chat') {

            unreadChat = 0;

            updateChatBadge();
        }
    }

    function toggleSidePanel(tab) {

        const panel =
            document.getElementById(
                'side-panel'
            );

        if (!panel) {
            return;
        }

        if (
            panelOpen
            &&
            activeTab === tab
        ) {

            panel.style.display =
                'none';

            panelOpen =
                false;

            activeTab =
                null;

            document
                .querySelectorAll(
                    '.ctrl-icon'
                )
                .forEach(
                    icon => {

                        icon.classList.remove(
                            'active'
                        );

                    }
                );

            return;
        }

        panel.style.removeProperty(
            'display'
        );

        panelOpen =
            true;

        switchTab(tab);
    }

    /* ============================================================
   WEBRTC STATE
============================================================ */

    let localStream =
        null;

    let peers =
        {};

    let pendingCandidates =
        {};

    let makingOffer =
        {};

    let ignoreOffer =
        {};

    let isMicOn =
        false;

    let isCameraOn =
        false;

    let recognition =
        null;

    let currentLang =
        'auto';

    let recognitionRunning =
        false;

    const participantMicStatus =
        {};

    const participantCameraStatus =
        {};

    const offlineTimers =
        {};

    const remoteStreams =
        {};

    const negotiationTimers =
        {};

    const negotiatingPeers =
        {};

    const audioRecoveryTimers =
        {};

    let mediaStartPromise =
        null;

    let lastAudioUnlockNotice =
        0;

    /* ============================================================
   MAXIMIZE
============================================================ */

    let maximizedUserId =
        null;

    let maximizedPlaceholder =
        null;

    function toggleMaximize(
        userId
    ) {

        const uid =
            String(userId);

        const overlay =
            document.getElementById(
                'maximized-overlay'
            );

        const grid =
            document.getElementById(
                'video-grid'
            );

        if (
            !overlay
            ||
            !grid
        ) {
            return;
        }

        if (
            maximizedUserId === uid
        ) {
            restoreMaximized();
            return;
        }

        if (maximizedUserId) {
            restoreMaximized();
        }

        const tile =
            document.getElementById(
                'tile-' + uid
            );

        if (!tile) {
            return;
        }

        maximizedPlaceholder =
            document.createComment(
                'tile-placeholder-' + uid
            );

        tile.parentNode.insertBefore(
            maximizedPlaceholder,
            tile
        );

        overlay.appendChild(tile);

        overlay.classList.add(
            'active'
        );

        tile.classList.add(
            'maximized'
        );

        maximizedUserId =
            uid;

        updateExpandIcons();
    }

    function restoreMaximized() {

        if (!maximizedUserId) {
            return;
        }

        const tile =
            document.getElementById(
                'tile-' + maximizedUserId
            );

        const overlay =
            document.getElementById(
                'maximized-overlay'
            );

        const grid =
            document.getElementById(
                'video-grid'
            );

        if (tile) {

            if (
                maximizedPlaceholder
                &&
                maximizedPlaceholder.parentNode
            ) {

                maximizedPlaceholder
                    .parentNode
                    .insertBefore(
                        tile,
                        maximizedPlaceholder
                    );

                maximizedPlaceholder.remove();

            } else if (grid) {

                grid.appendChild(
                    tile
                );
            }

            tile.classList.remove(
                'maximized'
            );
        }

        if (overlay) {
            overlay.classList.remove(
                'active'
            );
        }

        maximizedPlaceholder =
            null;

        maximizedUserId =
            null;

        updateExpandIcons();
    }

    function updateExpandIcons() {

        document
            .querySelectorAll(
                '.tile-expand-btn i[id^="expand-icon-"]'
            )
            .forEach(
                icon => {

                    const uid =
                        icon.id.replace(
                            'expand-icon-',
                            ''
                        );

                    icon.className =
                        maximizedUserId === uid
                            ? 'fa fa-compress'
                            : 'fa fa-expand';

                }
            );
    }

    /* ============================================================
   ICE
============================================================ */
    /* ============================================================
       ICE / STUN / TURN — production configuration
       Add TURN_HOST, TURN_USERNAME and TURN_CREDENTIAL in .env and
       config/services.php as shown in the deployment guide.
    ============================================================ */
    const TURN_HOST = @json(config('services.turn.host', 'smartmeet.live'));
    const TURN_USERNAME = @json(config('services.turn.username', 'smartmeet'));
    const TURN_CREDENTIAL = @json(config('services.turn.credential', ''));

    const iceServers = [
        { urls: ['stun:stun.l.google.com:19302', 'stun:stun1.l.google.com:19302'] }
    ];

    if (TURN_HOST && TURN_USERNAME && TURN_CREDENTIAL) {
        iceServers.push({
            urls: [
                `turn:${TURN_HOST}:3478?transport=udp`,
                `turn:${TURN_HOST}:3478?transport=tcp`
            ],
            username: TURN_USERNAME,
            credential: TURN_CREDENTIAL
        });
    } else {
        console.warn('TURN is not configured. Cross-network WebRTC may be unreliable.');
    }

    const iceConfig = {
        iceServers,
        iceCandidatePoolSize: 10,
        iceTransportPolicy: 'all',
        bundlePolicy: 'max-bundle',
        rtcpMuxPolicy: 'require'
    };

    function isPolite(
        otherUserId
    ) {

        const a =
            Number(MY_USER_ID);

        const b =
            Number(otherUserId);

        if (
            !Number.isNaN(a)
            &&
            !Number.isNaN(b)
        ) {
            return a < b;
        }

        return (
            String(MY_USER_ID)
            <
            String(otherUserId)
        );
    }

    function shouldInitiatePeer(
        otherUserId
    ) {

        const mine =
            String(MY_USER_ID);

        const other =
            String(otherUserId);

        if (mine === other) {
            return false;
        }

        const a =
            Number(mine);

        const b =
            Number(other);

        if (
            !Number.isNaN(a)
            &&
            !Number.isNaN(b)
        ) {
            return a < b;
        }

        return (
            mine.localeCompare(
                other
            )
            <
            0
        );
    }

    /* ============================================================
   STATUS BROADCAST
============================================================ */

    function broadcastMyMicStatus() {

        sendSignal(
            'all',
            'mic-status',
            {
                userId:
                MY_USER_ID,

                muted:
                    !isMicOn
            }
        );
    }

    function broadcastMyCameraStatus() {

        sendSignal(
            'all',
            'camera-status',
            {
                userId:
                MY_USER_ID,

                cameraOn:
                isCameraOn
            }
        );
    }

    /* ============================================================
   AUDIO
============================================================ */

    const preferredAudioConstraints = {
        echoCancellation:
            true,

        noiseSuppression:
            true,

        autoGainControl:
            true,

        channelCount:
            1,

        sampleRate:
            48000,

        sampleSize:
            16,

        latency:
            0
    };

    async function applyBestAudioConstraints(
        stream
    ) {

        const track =
            stream
                ?.getAudioTracks
                ?.()[0];

        if (
            !track
            ||
            !track.applyConstraints
        ) {
            return;
        }

        try {
            await track.applyConstraints(
                preferredAudioConstraints
            );
        } catch (error) {
            console.warn(
                'Advanced audio constraints unavailable:',
                error
            );
        }

        try {
            track.contentHint =
                'speech';
        } catch (error) {}
    }

    async function safelyPlayRemoteAudio(
        audio
    ) {

        if (!audio) {
            return;
        }

        audio.autoplay =
            true;

        audio.playsInline =
            true;

        audio.muted =
            false;

        audio.defaultMuted =
            false;

        audio.volume =
            1;

        try {
            if (audio.setSinkId) {
                await audio.setSinkId(
                    'default'
                );
            }
        } catch (error) {}

        try {
            await audio.play();
        } catch (error) {

            const now =
                Date.now();

            if (
                now
                -
                lastAudioUnlockNotice
                >
                5000
            ) {

                lastAudioUnlockNotice =
                    now;

                showToast(
                    '🔊 Tap anywhere once to enable meeting audio.'
                );
            }

            const unlock =
                async () => {

                    try {
                        await audio.play();
                    } catch (error) {}

                    document.removeEventListener(
                        'pointerdown',
                        unlock
                    );

                    document.removeEventListener(
                        'keydown',
                        unlock
                    );
                };

            document.addEventListener(
                'pointerdown',
                unlock,
                {
                    once:
                        true
                }
            );

            document.addEventListener(
                'keydown',
                unlock,
                {
                    once:
                        true
                }
            );
        }
    }

    /* ============================================================
   REMOTE STREAM
============================================================ */

    function getOrCreateRemoteStream(
        userId
    ) {

        const uid =
            String(userId);

        if (!remoteStreams[uid]) {
            remoteStreams[uid] =
                new MediaStream();
        }

        return remoteStreams[uid];
    }

    function attachRemoteStream(
        userId
    ) {

        const uid =
            String(userId);

        /*
     * Never play our own outgoing media.
     */
        if (
            uid ===
            String(MY_USER_ID)
        ) {

            const ownAudio =
                document.getElementById(
                    'audio-' + uid
                );

            if (ownAudio) {

                try {
                    ownAudio.pause();
                } catch (error) {}

                ownAudio.srcObject =
                    null;

                ownAudio.remove();
            }

            document
                .querySelectorAll(
                    `audio[data-peer-id="${uid}"]`
                )
                .forEach(
                    element => {

                        try {
                            element.pause();
                        } catch (error) {}

                        element.srcObject =
                            null;

                        element.remove();

                    }
                );

            return;
        }

        const sourceStream =
            getOrCreateRemoteStream(
                uid
            );

        const localIds =
            new Set(
                (
                    localStream
                        ?.getTracks
                        ?.()
                    ||
                    []
                )
                    .map(
                        track =>
                            track.id
                    )
            );

        /* ---------- AUDIO ---------- */

        const audioTracks =
            sourceStream
                .getAudioTracks()
                .filter(
                    track =>
                        track.readyState
                        !==
                        'ended'
                        &&
                        !localIds.has(
                            track.id
                        )
                );

        document
            .querySelectorAll(
                `audio[data-peer-id="${uid}"]`
            )
            .forEach(
                (
                    element,
                    index
                ) => {

                    if (index > 0) {

                        try {
                            element.pause();
                        } catch (error) {}

                        element.srcObject =
                            null;

                        element.remove();
                    }

                }
            );

        let audio =
            document.getElementById(
                'audio-' + uid
            );

        if (!audio) {

            audio =
                document.createElement(
                    'audio'
                );

            audio.id =
                'audio-' + uid;

            audio.autoplay =
                true;

            audio.playsInline =
                true;

            audio.preload =
                'auto';

            audio.muted =
                false;

            audio.defaultMuted =
                false;

            audio.volume =
                1;

            audio.dataset.peerId =
                uid;

            audio.style.display =
                'none';

            document.body.appendChild(
                audio
            );
        }

        if (audioTracks.length) {

            const currentIds =
                audio.srcObject
                instanceof
                MediaStream
                    ? audio
                        .srcObject
                        .getAudioTracks()
                        .map(
                            track =>
                                track.id
                        )
                        .join(',')
                    : '';

            const nextIds =
                audioTracks
                    .map(
                        track =>
                            track.id
                    )
                    .join(',');

            if (
                currentIds
                !==
                nextIds
            ) {
                audio.srcObject =
                    new MediaStream(
                        audioTracks
                    );
            }

            audio.muted =
                false;

            audio.defaultMuted =
                false;

            audio.volume =
                1;

            safelyPlayRemoteAudio(
                audio
            );
        }

        /* ---------- VIDEO ---------- */

        const videoTracks =
            sourceStream
                .getVideoTracks()
                .filter(
                    track =>
                        track.readyState
                        !==
                        'ended'
                        &&
                        !localIds.has(
                            track.id
                        )
                );

        const video =
            document.getElementById(
                'rvideo-' + uid
            );

        const avatar =
            document.getElementById(
                'avatar-' + uid
            );

        if (!video) {
            return;
        }

        video.autoplay =
            true;

        video.playsInline =
            true;

        /*
     * Remote audio is already playing through
     * separate audio element.
     */
        video.muted =
            true;

        if (videoTracks.length) {

            const currentIds =
                video.srcObject
                instanceof
                MediaStream
                    ? video
                        .srcObject
                        .getVideoTracks()
                        .map(
                            track =>
                                track.id
                        )
                        .join(',')
                    : '';

            const nextIds =
                videoTracks
                    .map(
                        track =>
                            track.id
                    )
                    .join(',');

            if (
                currentIds
                !==
                nextIds
            ) {
                video.srcObject =
                    new MediaStream(
                        videoTracks
                    );
            }

            /*
         * Critical camera fix:
         * camera status and WebRTC ontrack can arrive
         * in different order.
         */
            if (
                participantCameraStatus[
                    uid
                    ]
                ===
                true
            ) {

                video.style.display =
                    'block';

                if (avatar) {
                    avatar.style.display =
                        'none';
                }

                const playVideo =
                    () => {

                        video
                            .play()
                            .catch(
                                () => {}
                            );

                    };

                playVideo();

                setTimeout(
                    playVideo,
                    80
                );

                setTimeout(
                    playVideo,
                    250
                );
            }

        } else {

            video.style.display =
                'none';

            if (avatar) {
                avatar.style.display =
                    'flex';
            }
        }
    }

    /* ============================================================
   TRACK SYNC
============================================================ */

    async function syncLocalTracksToPeer(
        userId
    ) {

        const uid =
            String(userId);

        const pc =
            peers[uid];

        if (
            !pc
            ||
            pc.signalingState
            ===
            'closed'
            ||
            !localStream
        ) {
            return false;
        }

        let changed =
            false;

        const localTracks =
            localStream.getTracks();

        for (
            const kind
            of
            [
                'audio',
                'video'
            ]
            ) {

            const track =
                localTracks.find(
                    item =>
                        item.kind === kind
                        &&
                        item.readyState
                        !==
                        'ended'
                )
                ||
                null;

            const sender =
                pc
                    .getSenders()
                    .find(
                        item =>
                            item.track?.kind
                            ===
                            kind
                    );

            if (sender) {

                if (
                    sender.track
                    !==
                    track
                ) {

                    try {
                        await sender.replaceTrack(
                            track
                        );

                        changed =
                            true;
                    } catch (error) {

                        console.warn(
                            'replaceTrack failed:',
                            uid,
                            kind,
                            error
                        );
                    }
                }

            } else if (track) {

                try {
                    pc.addTrack(
                        track,
                        localStream
                    );

                    changed =
                        true;
                } catch (error) {

                    console.warn(
                        'addTrack failed:',
                        uid,
                        kind,
                        error
                    );
                }
            }
        }

        return changed;
    }

    /* ============================================================
   NEGOTIATION
============================================================ */

    async function negotiatePeer(
        userId,
        options = {}
    ) {

        const uid =
            String(userId);

        const pc =
            peers[uid];

        if (
            !pc
            ||
            pc.signalingState
            ===
            'closed'
            ||
            leftUsers.has(uid)
        ) {
            return;
        }

        if (
            !shouldInitiatePeer(uid)
            &&
            !options.force
        ) {
            return;
        }

        if (
            negotiatingPeers[uid]
            ||
            makingOffer[uid]
        ) {
            return;
        }

        try {

            negotiatingPeers[uid] =
                true;

            makingOffer[uid] =
                true;

            await syncLocalTracksToPeer(
                uid
            );

            if (
                pc.signalingState
                !==
                'stable'
            ) {
                return;
            }

            const offer =
                await pc.createOffer({
                    iceRestart:
                        Boolean(
                            options.iceRestart
                        )
                });

            if (
                pc.signalingState
                !==
                'stable'
            ) {
                return;
            }

            await pc.setLocalDescription(
                offer
            );

            await sendSignal(
                uid,
                'offer',
                {
                    type:
                    pc.localDescription.type,

                    sdp:
                        btoa(
                            unescape(
                                encodeURIComponent(
                                    pc.localDescription.sdp
                                )
                            )
                        ),

                    iceRestart:
                        Boolean(
                            options.iceRestart
                        ),

                    reason:
                        options.reason
                        ||
                        'track-sync'
                }
            );

        } catch (error) {

            console.warn(
                'Peer negotiation failed:',
                uid,
                error
            );

        } finally {

            makingOffer[uid] =
                false;

            negotiatingPeers[uid] =
                false;
        }
    }

    function queuePeerNegotiation(
        userId,
        options = {}
    ) {

        const uid =
            String(userId);

        if (
            negotiationTimers[uid]
        ) {
            clearTimeout(
                negotiationTimers[uid]
            );
        }

        negotiationTimers[uid] =
            setTimeout(
                () => {

                    delete negotiationTimers[
                        uid
                        ];

                    negotiatePeer(
                        uid,
                        options
                    );

                },
                options.delay
                ??
                120
            );
    }

    async function syncTracksToEveryPeer(
        forceNegotiation = false
    ) {

        const tasks =
            Object
                .keys(peers)
                .map(
                    async uid => {

                        const changed =
                            await syncLocalTracksToPeer(
                                uid
                            );

                        if (
                            changed
                            ||
                            forceNegotiation
                        ) {

                            queuePeerNegotiation(
                                uid,
                                {
                                    reason:
                                        'local-track-change',

                                    force:
                                    forceNegotiation,

                                    delay:
                                        80
                                }
                            );
                        }

                    }
                );

        await Promise.allSettled(
            tasks
        );
    }

    /* ============================================================
   CAMERA DELIVERY FIX
============================================================ */

    async function syncCameraToAllPeers(
        videoTrack
    ) {

        if (!videoTrack) {
            return;
        }

        const joinedPeerIds =
            Object
                .keys(
                    knownParticipants
                )
                .filter(
                    uid => {

                        uid =
                            String(uid);

                        if (
                            uid
                            ===
                            String(MY_USER_ID)
                        ) {
                            return false;
                        }

                        if (
                            leftUsers.has(
                                uid
                            )
                        ) {
                            return false;
                        }

                        return Boolean(
                            knownParticipants[
                                uid
                                ]?.hasJoined
                            ||
                            onlineUsers.has(
                                uid
                            )
                        );

                    }
                );

        for (
            const uid
            of
            joinedPeerIds
            ) {

            let pc =
                peers[uid];

            if (
                !pc
                ||
                pc.signalingState
                ===
                'closed'
                ||
                [
                    'closed',
                    'failed'
                ].includes(
                    pc.connectionState
                )
            ) {

                closePeerCompletely(
                    uid
                );

                pc =
                    createPeerConnection(
                        uid
                    );
            }

            if (!pc) {
                continue;
            }

            let sender =
                pc
                    .getSenders()
                    .find(
                        item =>
                            item.track?.kind
                            ===
                            'video'
                    );

            if (sender) {

                if (
                    sender.track
                    !==
                    videoTrack
                ) {

                    try {
                        await sender.replaceTrack(
                            videoTrack
                        );
                    } catch (error) {

                        console.warn(
                            'replaceTrack(video) failed:',
                            uid,
                            error
                        );
                    }
                }

            } else {

                const transceiver =
                    pc
                        .getTransceivers()
                        .find(
                            item =>
                                item.receiver
                                    ?.track
                                    ?.kind
                                ===
                                'video'
                        );

                if (
                    transceiver
                        ?.sender
                ) {

                    try {

                        await transceiver
                            .sender
                            .replaceTrack(
                                videoTrack
                            );

                        sender =
                            transceiver.sender;

                    } catch (error) {

                        console.warn(
                            'video transceiver replaceTrack failed:',
                            uid,
                            error
                        );
                    }
                }

                if (!sender) {

                    try {
                        pc.addTrack(
                            videoTrack,
                            localStream
                        );
                    } catch (error) {

                        console.warn(
                            'addTrack(video) failed:',
                            uid,
                            error
                        );
                    }

                    /*
                 * This peer did not previously have a
                 * video sender, so renegotiation is needed.
                 */
                    queuePeerNegotiation(
                        uid,
                        {
                            reason:
                                'camera-added',

                            force:
                                true,

                            delay:
                                10
                        }
                    );
                }
            }
        }
    }

    /* ============================================================
   PEER RECOVERY
============================================================ */

    function closePeerCompletely(
        userId
    ) {

        const uid =
            String(userId);

        if (
            audioRecoveryTimers[uid]
        ) {

            clearTimeout(
                audioRecoveryTimers[
                    uid
                    ]
            );

            delete audioRecoveryTimers[
                uid
                ];
        }

        const pc =
            peers[uid];

        if (pc) {

            try {

                pc.ontrack =
                    null;

                pc.onicecandidate =
                    null;

                pc.onnegotiationneeded =
                    null;

                pc.close();

            } catch (error) {}

            if (
                peers[uid]
                ===
                pc
            ) {
                delete peers[uid];
            }
        }

        delete pendingCandidates[
            uid
            ];

        delete makingOffer[
            uid
            ];

        delete ignoreOffer[
            uid
            ];
    }

    async function restartPeerConnection(
        userId,
        reason = 'recovery'
    ) {

        const uid =
            String(userId);

        if (
            uid ===
            String(MY_USER_ID)
            ||
            leftUsers.has(uid)
            ||
            !knownParticipants[uid]
        ) {
            return;
        }

        closePeerCompletely(
            uid
        );

        createPeerConnection(
            uid
        );

        await syncLocalTracksToPeer(
            uid
        );

        if (
            isCameraOn
        ) {

            const videoTrack =
                localStream
                    ?.getVideoTracks()
                    .find(
                        track =>
                            track.readyState
                            !==
                            'ended'
                    );

            if (videoTrack) {
                await syncCameraToAllPeers(
                    videoTrack
                );
            }
        }

        if (
            shouldInitiatePeer(
                uid
            )
        ) {

            queuePeerNegotiation(
                uid,
                {
                    reason,
                    iceRestart:
                        true,
                    force:
                        true,
                    delay:
                        10
                }
            );

        } else {

            sendSignal(
                uid,
                'user-joined',
                {
                    userId:
                    MY_USER_ID,

                    name:
                    MY_NAME,

                    initials:
                    MY_INITIALS,

                    recovery:
                        true,

                    reason
                }
            );
        }
    }

    function schedulePeerRecovery(
        userId,
        reason,
        delay = 1400
    ) {

        const uid =
            String(userId);

        if (
            audioRecoveryTimers[
                uid
                ]
        ) {
            clearTimeout(
                audioRecoveryTimers[
                    uid
                    ]
            );
        }

        audioRecoveryTimers[uid] =
            setTimeout(
                () => {

                    delete audioRecoveryTimers[
                        uid
                        ];

                    restartPeerConnection(
                        uid,
                        reason
                    );

                },
                delay
            );
    }

    function recoverAllJoinedPeers(
        reason = 'resume'
    ) {

        Object
            .keys(
                knownParticipants
            )
            .forEach(
                uid => {

                    if (
                        uid
                        ===
                        String(MY_USER_ID)
                    ) {
                        return;
                    }

                    if (
                        !(
                            knownParticipants[
                                uid
                                ]?.hasJoined
                            ||
                            onlineUsers.has(
                                String(uid)
                            )
                        )
                    ) {
                        return;
                    }

                    const pc =
                        peers[uid];

                    const hasInboundAudio =
                        Boolean(
                            pc
                                ?.getReceivers
                                ?.()
                                .some(
                                    receiver =>
                                        receiver.track?.kind
                                        ===
                                        'audio'
                                        &&
                                        receiver.track.readyState
                                        !==
                                        'ended'
                                )
                        );

                    if (
                        !pc
                        ||
                        [
                            'failed',
                            'closed',
                            'disconnected'
                        ].includes(
                            pc.connectionState
                        )
                        ||
                        [
                            'failed',
                            'closed',
                            'disconnected'
                        ].includes(
                            pc.iceConnectionState
                        )
                        ||
                        !hasInboundAudio
                    ) {

                        schedulePeerRecovery(
                            uid,
                            reason,
                            250
                        );

                    } else {

                        attachRemoteStream(
                            uid
                        );

                        safelyPlayRemoteAudio(
                            document.getElementById(
                                'audio-' + uid
                            )
                        );
                    }

                }
            );
    }

    /* ============================================================
   START AUDIO
============================================================ */

    async function startAudio() {

        if (mediaStartPromise) {
            return mediaStartPromise;
        }

        mediaStartPromise =
            (async () => {

                try {

                    const audioStream =
                        await navigator
                            .mediaDevices
                            .getUserMedia({
                                audio:
                                preferredAudioConstraints,

                                video:
                                    false
                            });

                    await applyBestAudioConstraints(
                        audioStream
                    );

                    localStream =
                        new MediaStream();

                    audioStream
                        .getAudioTracks()
                        .forEach(
                            track => {

                                /*
                             * Mic is OFF by default.
                             */
                                track.enabled =
                                    false;

                                localStream.addTrack(
                                    track
                                );

                            }
                        );

                    isMicOn =
                        false;

                    /*
                 * Request a video track once during startup,
                 * but keep it DISABLED.
                 *
                 * This means the video sender/transceiver
                 * normally already exists when Camera is
                 * later switched ON, making camera delivery
                 * much faster.
                 */
                    try {

                        const cameraStream =
                            await navigator
                                .mediaDevices
                                .getUserMedia({
                                    audio:
                                        false,

                                    video: {
                                        width: {
                                            ideal:
                                                960
                                        },

                                        height: {
                                            ideal:
                                                540
                                        },

                                        facingMode:
                                            'user'
                                    }
                                });

                        cameraStream
                            .getVideoTracks()
                            .forEach(
                                track => {

                                    track.enabled =
                                        false;

                                    localStream.addTrack(
                                        track
                                    );

                                }
                            );

                    } catch (cameraError) {

                        /*
                     * Important:
                     * Never hide the Camera button because
                     * of an initial temporary failure.
                     * toggleCamera() will request it again.
                     */
                        console.warn(
                            'Camera unavailable during initial setup; it will be requested again when Camera is clicked:',
                            cameraError
                        );
                    }

                    isCameraOn =
                        false;

                    const localVideo =
                        document.getElementById(
                            'localVideo'
                        );

                    if (localVideo) {

                        localVideo.srcObject =
                            localStream;

                        localVideo.muted =
                            true;

                        localVideo.playsInline =
                            true;

                        localVideo
                            .play()
                            .catch(
                                () => {}
                            );
                    }

                    const micBtn =
                        document.getElementById(
                            'ctrl-mic'
                        );

                    const micOff =
                        document.getElementById(
                            'micoff-'
                            +
                            MY_USER_ID
                        );

                    if (micBtn) {

                        micBtn.innerHTML =
                            '<i class="fa fa-microphone-slash"></i>';

                        micBtn.classList.add(
                            'off'
                        );
                    }

                    if (micOff) {
                        micOff.style.display =
                            'flex';
                    }

                    await syncTracksToEveryPeer(
                        true
                    );

                    connectToAll();

                    startTranscript();

                    stopRecognition();

                    broadcastMyMicStatus();

                } catch (error) {

                    console.error(
                        'Microphone access failed:',
                        error
                    );

                    isMicOn =
                        false;

                    const micBtn =
                        document.getElementById(
                            'ctrl-mic'
                        );

                    if (micBtn) {

                        micBtn.innerHTML =
                            '<i class="fa fa-microphone-slash"></i>';

                        micBtn.classList.add(
                            'off'
                        );
                    }

                    if (
                        error.name
                        ===
                        'NotAllowedError'
                    ) {

                        showToast(
                            '🎙️ Microphone is blocked. Allow it in browser Site settings, then reload.'
                        );

                    } else if (
                        error.name
                        ===
                        'NotFoundError'
                    ) {

                        showToast(
                            '🎙️ No microphone was found on this device.'
                        );

                    } else {

                        showToast(
                            '🎙️ Meeting audio could not start. Please check your microphone.'
                        );
                    }

                } finally {

                    mediaStartPromise =
                        null;
                }

            })();

        return mediaStartPromise;
    }

    /* ============================================================
   REVERB / ECHO
============================================================ */

    function listenForSignals() {

        return new Promise(
            resolve => {

                if (
                    typeof window.Echo
                    ===
                    'undefined'
                ) {

                    console.error(
                        'Echo not initialized'
                    );

                    resolve(false);

                    return;
                }

                const channel =
                    window.Echo.channel(
                        'meeting.'
                        +
                        MEETING_ID
                    );

                let done =
                    false;

                const finish =
                    value => {

                        if (!done) {
                            done =
                                true;

                            resolve(
                                value
                            );
                        }
                    };

                channel.listen(
                    '.signal',
                    handleSignal
                );

                channel.listen(
                    '.transcript',
                    handleTranscript
                );

                if (
                    typeof channel.subscribed
                    ===
                    'function'
                ) {

                    channel.subscribed(
                        () => {
                            finish(true);
                        }
                    );
                }

                if (
                    typeof channel.error
                    ===
                    'function'
                ) {

                    channel.error(
                        error => {

                            console.error(
                                'Meeting channel subscription failed:',
                                error
                            );

                            finish(false);
                        }
                    );
                }

                setTimeout(
                    () => {
                        finish(true);
                    },
                    1200
                );
            }
        );
    }

    /* ============================================================
   PEER CONNECTION
============================================================ */

    function createPeerConnection(
        userId
    ) {

        const uid =
            String(userId);

        let pc =
            peers[uid];

        if (
            pc
            &&
            ![
                'closed',
                'failed'
            ].includes(
                pc.connectionState
            )
        ) {
            return pc;
        }

        if (pc) {
            try {
                pc.close();
            } catch (error) {}
        }

        pc =
            new RTCPeerConnection(
                iceConfig
            );

        peers[uid] =
            pc;

        if (localStream) {

            localStream
                .getTracks()
                .forEach(
                    track => {

                        try {
                            pc.addTrack(
                                track,
                                localStream
                            );
                        } catch (error) {}
                    }
                );
        }

        setTimeout(
            () => {

                syncLocalTracksToPeer(
                    uid
                )
                    .finally(
                        () => {

                            if (
                                shouldInitiatePeer(
                                    uid
                                )
                            ) {

                                queuePeerNegotiation(
                                    uid,
                                    {
                                        reason:
                                            'peer-created',

                                        delay:
                                            20
                                    }
                                );
                            }

                        }
                    );

            },
            0
        );

        pc.onnegotiationneeded =
            () => {

                if (
                    !shouldInitiatePeer(
                        uid
                    )
                ) {
                    return;
                }

                queuePeerNegotiation(
                    uid,
                    {
                        reason:
                            'negotiation-needed',

                        delay:
                            20
                    }
                );
            };

        pc.ontrack =
            event => {

                if (
                    uid
                    ===
                    String(MY_USER_ID)
                    ||
                    leftUsers.has(uid)
                ) {
                    return;
                }

                const localIds =
                    new Set(
                        (
                            localStream
                                ?.getTracks
                                ?.()
                            ||
                            []
                        )
                            .map(
                                track =>
                                    track.id
                            )
                    );

                /*
             * Prevent local track loopback.
             */
                if (
                    localIds.has(
                        event.track.id
                    )
                ) {

                    console.warn(
                        'Blocked looped-back local track:',
                        uid,
                        event.track.kind
                    );

                    return;
                }

                try {

                    if (
                        'playoutDelayHint'
                        in
                        event.receiver
                    ) {
                        event.receiver.playoutDelayHint =
                            0;
                    }

                    if (
                        'jitterBufferTarget'
                        in
                        event.receiver
                    ) {
                        event.receiver.jitterBufferTarget =
                            0;
                    }

                } catch (error) {}

                ensureParticipantTileVisible(
                    uid
                );

                const remoteStream =
                    getOrCreateRemoteStream(
                        uid
                    );

                if (
                    !remoteStream
                        .getTracks()
                        .some(
                            track =>
                                track.id
                                ===
                                event.track.id
                        )
                ) {

                    remoteStream.addTrack(
                        event.track
                    );
                }

                /*
             * CRITICAL CAMERA FIX:
             *
             * As soon as remote video track becomes
             * live/unmuted, reveal it immediately.
             */
                event.track.onunmute =
                    () => {

                        if (
                            event.track.kind
                            ===
                            'video'
                        ) {

                            participantCameraStatus[
                                uid
                                ] = true;

                            const video =
                                document.getElementById(
                                    'rvideo-' + uid
                                );

                            const avatar =
                                document.getElementById(
                                    'avatar-' + uid
                                );

                            if (video) {

                                video.autoplay =
                                    true;

                                video.playsInline =
                                    true;

                                video.muted =
                                    true;

                                video.style.display =
                                    'block';

                                video
                                    .play()
                                    .catch(
                                        () => {}
                                    );
                            }

                            if (avatar) {
                                avatar.style.display =
                                    'none';
                            }
                        }

                        attachRemoteStream(
                            uid
                        );
                    };

                event.track.onended =
                    () => {

                        const stream =
                            remoteStreams[
                                uid
                                ];

                        const existing =
                            stream
                                ?.getTracks()
                                .find(
                                    track =>
                                        track.id
                                        ===
                                        event.track.id
                                );

                        if (existing) {

                            stream.removeTrack(
                                existing
                            );
                        }

                        if (
                            event.track.kind
                            ===
                            'video'
                        ) {

                            participantCameraStatus[
                                uid
                                ] = false;

                            const video =
                                document.getElementById(
                                    'rvideo-' + uid
                                );

                            const avatar =
                                document.getElementById(
                                    'avatar-' + uid
                                );

                            if (video) {

                                video.style.display =
                                    'none';

                                video.srcObject =
                                    null;
                            }

                            if (avatar) {

                                avatar.style.display =
                                    'flex';
                            }
                        }

                        attachRemoteStream(
                            uid
                        );
                    };

                attachRemoteStream(
                    uid
                );

                /*
             * camera-status may already have arrived
             * before this WebRTC track.
             */
                if (
                    event.track.kind
                    ===
                    'video'
                    &&
                    participantCameraStatus[
                        uid
                        ]
                    ===
                    true
                ) {

                    const video =
                        document.getElementById(
                            'rvideo-' + uid
                        );

                    const avatar =
                        document.getElementById(
                            'avatar-' + uid
                        );

                    if (video) {

                        video.autoplay =
                            true;

                        video.playsInline =
                            true;

                        video.muted =
                            true;

                        video.style.display =
                            'block';

                        video
                            .play()
                            .catch(
                                () => {}
                            );
                    }

                    if (avatar) {

                        avatar.style.display =
                            'none';
                    }
                }

                if (
                    event.track.kind
                    ===
                    'video'
                ) {

                    [
                        80,
                        250,
                        600
                    ].forEach(
                        delay => {

                            setTimeout(
                                () => {

                                    attachRemoteStream(
                                        uid
                                    );

                                },
                                delay
                            );

                        }
                    );
                }
            };

        pc.onicecandidate =
            event => {

                if (
                    event.candidate
                ) {

                    sendSignal(
                        uid,
                        'ice-candidate',
                        {
                            candidate:
                                event
                                    .candidate
                                    .toJSON()
                        }
                    );
                }
            };

        pc.oniceconnectionstatechange =
            () => {

                const state =
                    pc.iceConnectionState;

                if (
                    state ===
                    'failed'
                ) {

                    if (
                        offlineTimers[
                            uid
                            ]
                    ) {

                        clearTimeout(
                            offlineTimers[
                                uid
                                ]
                        );

                        delete offlineTimers[
                            uid
                            ];
                    }

                    removeParticipantTileSilently(
                        uid,
                        true
                    );

                    try {
                        pc.close();
                    } catch (error) {}

                    if (
                        peers[uid]
                        ===
                        pc
                    ) {
                        delete peers[
                            uid
                            ];
                    }

                    schedulePeerRecovery(
                        uid,
                        'ice-failed',
                        450
                    );

                } else if (
                    state ===
                    'disconnected'
                ) {

                    if (
                        offlineTimers[
                            uid
                            ]
                    ) {
                        clearTimeout(
                            offlineTimers[
                                uid
                                ]
                        );
                    }

                    offlineTimers[uid] =
                        setTimeout(
                            () => {

                                const current =
                                    peers[uid];

                                if (
                                    !current
                                    ||
                                    [
                                        'disconnected',
                                        'failed',
                                        'closed'
                                    ].includes(
                                        current
                                            .iceConnectionState
                                    )
                                ) {

                                    removeParticipantTileSilently(
                                        uid,
                                        true
                                    );

                                    if (current) {

                                        try {
                                            current.close();
                                        } catch (error) {}

                                        if (
                                            peers[uid]
                                            ===
                                            current
                                        ) {
                                            delete peers[
                                                uid
                                                ];
                                        }
                                    }
                                }

                                delete offlineTimers[
                                    uid
                                    ];

                                schedulePeerRecovery(
                                    uid,
                                    'ice-disconnected',
                                    900
                                );

                            },
                            3500
                        );

                } else if (
                    state ===
                    'connected'
                    ||
                    state ===
                    'completed'
                ) {

                    if (
                        offlineTimers[
                            uid
                            ]
                    ) {

                        clearTimeout(
                            offlineTimers[
                                uid
                                ]
                        );

                        delete offlineTimers[
                            uid
                            ];
                    }

                    ensureParticipantTileVisible(
                        uid
                    );

                    attachRemoteStream(
                        uid
                    );

                    syncLocalTracksToPeer(
                        uid
                    );

                    /*
                 * If camera is currently ON,
                 * push it again to a peer that
                 * has just connected/recovered.
                 */
                    if (isCameraOn) {

                        const cameraTrack =
                            localStream
                                ?.getVideoTracks()
                                .find(
                                    track =>
                                        track.readyState
                                        !==
                                        'ended'
                                );

                        if (cameraTrack) {

                            syncCameraToAllPeers(
                                cameraTrack
                            );
                        }
                    }

                    broadcastMyMicStatus();

                    broadcastMyCameraStatus();
                }
            };

        pc.onconnectionstatechange =
            () => {

                if (
                    pc.connectionState
                    ===
                    'closed'
                ) {

                    if (
                        peers[uid]
                        ===
                        pc
                    ) {

                        delete peers[
                            uid
                            ];
                    }

                    removeParticipantTileSilently(
                        uid,
                        false
                    );
                }
            };

        return pc;
    }

    /* ============================================================
   SDP
============================================================ */

    function decodeSdp(sdp) {

        if (!sdp) {
            return '';
        }

        try {

            return decodeURIComponent(
                escape(
                    atob(
                        sdp
                    )
                )
            );

        } catch (error) {

            return sdp;
        }
    }

    /* ============================================================
   DISCONNECT TILE
============================================================ */

    function removeParticipantTileSilently(
        userId,
        announce
    ) {

        const uid =
            String(userId);

        if (
            uid
            ===
            String(maximizedUserId)
        ) {

            const overlay =
                document.getElementById(
                    'maximized-overlay'
                );

            if (overlay) {
                overlay.classList.remove(
                    'active'
                );
            }

            if (
                maximizedPlaceholder
                &&
                maximizedPlaceholder.parentNode
            ) {

                maximizedPlaceholder.remove();
            }

            maximizedPlaceholder =
                null;

            maximizedUserId =
                null;
        }

        const tile =
            document.getElementById(
                'tile-' + uid
            );

        if (tile) {
            tile.remove();
        }

        markOffline(
            uid
        );

        if (
            knownParticipants[
                uid
                ]
        ) {
            knownParticipants[
                uid
                ].hasJoined =
                false;
        }

        if (
            announce
            &&
            !departedAnnounced.has(
                uid
            )
        ) {

            departedAnnounced.add(
                uid
            );

            const info =
                knownParticipants[
                    uid
                    ];

            showToast(
                `⚠️ ${
                    info
                        ? info.name
                        : 'A participant'
                } has disconnected.`
            );
        }
    }

    function handleUserLeft(
        userId
    ) {

        const uid =
            String(userId);

        leftUsers.add(
            uid
        );

        if (
            offlineTimers[
                uid
                ]
        ) {

            clearTimeout(
                offlineTimers[
                    uid
                    ]
            );

            delete offlineTimers[
                uid
                ];
        }

        if (
            peers[uid]
        ) {

            try {
                peers[
                    uid
                    ].close();
            } catch (error) {}

            delete peers[
                uid
                ];
        }

        delete pendingCandidates[
            uid
            ];

        if (
            uid
            ===
            String(
                maximizedUserId
            )
        ) {

            restoreMaximized();
        }

        const tile =
            document.getElementById(
                'tile-' + uid
            );

        if (tile) {
            tile.remove();
        }

        const audio =
            document.getElementById(
                'audio-' + uid
            );

        if (audio) {

            audio.pause();

            audio.srcObject =
                null;

            audio.remove();
        }

        if (
            remoteStreams[
                uid
                ]
        ) {

            remoteStreams[
                uid
                ]
                .getTracks()
                .forEach(
                    track => {

                        try {

                            remoteStreams[
                                uid
                                ].removeTrack(
                                track
                            );

                        } catch (error) {}
                    }
                );

            delete remoteStreams[
                uid
                ];
        }

        delete participantMicStatus[
            uid
            ];

        delete participantCameraStatus[
            uid
            ];

        if (
            knownParticipants[
                uid
                ]
        ) {

            knownParticipants[
                uid
                ].hasJoined =
                false;
        }

        markOffline(
            uid
        );
    }

    function ensureParticipantTileVisible(
        userId
    ) {

        const uid =
            String(userId);

        if (
            leftUsers.has(
                uid
            )
        ) {
            return;
        }

        const info =
            knownParticipants[
                uid
                ];

        if (info) {

            info.hasJoined =
                true;

            addParticipantTile(
                uid,
                info.name,
                info.initials,
                info.isOrganizer
                ||
                false
            );

            markOnline(
                uid
            );
        }
    }

    /* ============================================================
   HANDLE SIGNAL
============================================================ */

    async function handleSignal(
        data
    ) {

        const from =
            String(
                data.fromUserId
            );

        const isSelf =
            from
            ===
            String(MY_USER_ID);

        if (
            isSelf
            &&
            ![
                'meeting-cancelled',
                'meeting-ended'
            ].includes(
                data.type
            )
        ) {
            return;
        }

        /* ---------- CANCEL ---------- */

        if (
            data.type
            ===
            'meeting-cancelled'
        ) {

            showToast(
                '🚫 The organizer cancelled the meeting for everyone.'
            );

            cleanup();

            document
                .querySelectorAll(
                    '.video-tile'
                )
                .forEach(
                    tile => {

                        if (
                            !tile.id.endsWith(
                                String(
                                    MY_USER_ID
                                )
                            )
                        ) {
                            tile.remove();
                        }

                    }
                );

            setTimeout(
                () => {

                    window.location.href =
                        LEAVE_URL;

                },
                1800
            );

            return;
        }

        /* ---------- ENDED ---------- */

        if (
            data.type
            ===
            'meeting-ended'
        ) {

            const message =
                data.data?.auto
                    ? '⏰ Meeting time has ended.'
                    : '📞 Meeting has ended.';

            showToast(
                message
            );

            setTimeout(
                () => {

                    cleanup();

                    window.location.href =
                        LEAVE_URL;

                },
                2500
            );

            return;
        }

        /* ---------- USER JOINED ---------- */

        if (
            data.type
            ===
            'user-joined'
        ) {

            const joinedId =
                String(
                    data.data.userId
                );

            if (
                joinedId
                ===
                String(
                    MY_USER_ID
                )
            ) {
                return;
            }

            const wasAlreadyOnline =
                onlineUsers.has(
                    joinedId
                );

            leftUsers.delete(
                joinedId
            );

            if (
                !knownParticipants[
                    joinedId
                    ]
            ) {

                knownParticipants[
                    joinedId
                    ] = {
                    name:
                    data.data.name,

                    initials:
                    data.data.initials,

                    isOrganizer:
                        false,

                    hasJoined:
                        true
                };

            } else {

                knownParticipants[
                    joinedId
                    ].hasJoined =
                    true;
            }

            if (
                !ALL_USER_IDS
                    .map(String)
                    .includes(
                        joinedId
                    )
            ) {

                ALL_USER_IDS.push(
                    joinedId
                );
            }

            ensurePanelRow(
                joinedId,
                data.data.name,
                data.data.initials,
                false
            );

            addParticipantTile(
                joinedId,
                data.data.name,
                data.data.initials,
                false
            );

            markOnline(
                joinedId
            );

            createPeerConnection(
                joinedId
            );

            setTimeout(
                async () => {

                    await syncLocalTracksToPeer(
                        joinedId
                    );

                    /*
                 * If our camera is already ON when
                 * this new user joins, immediately
                 * synchronize the video track.
                 */
                    if (
                        isCameraOn
                    ) {

                        const cameraTrack =
                            localStream
                                ?.getVideoTracks()
                                .find(
                                    track =>
                                        track.readyState
                                        !==
                                        'ended'
                                );

                        if (
                            cameraTrack
                        ) {

                            await syncCameraToAllPeers(
                                cameraTrack
                            );
                        }
                    }

                    if (
                        shouldInitiatePeer(
                            joinedId
                        )
                    ) {

                        queuePeerNegotiation(
                            joinedId,
                            {
                                reason:
                                    'user-joined',

                                delay:
                                    20
                            }
                        );
                    }

                },
                20
            );

            if (
                !wasAlreadyOnline
            ) {

                showToast(
                    `✅ ${data.data.name} has joined the meeting.`
                );
            }

            sendSignal(
                joinedId,
                'mic-status',
                {
                    userId:
                    MY_USER_ID,

                    muted:
                        !isMicOn
                }
            );

            sendSignal(
                joinedId,
                'camera-status',
                {
                    userId:
                    MY_USER_ID,

                    cameraOn:
                    isCameraOn
                }
            );

            return;
        }

        /* ---------- USER LEFT ---------- */

        if (
            data.type
            ===
            'user-left'
        ) {

            if (isSelf) {
                return;
            }

            handleUserLeft(
                from
            );

            if (
                !departedAnnounced.has(
                    from
                )
            ) {

                departedAnnounced.add(
                    from
                );

                const name =
                    data.data?.name
                    ||
                    knownParticipants[
                        from
                        ]?.name
                    ||
                    'A participant';

                showToast(
                    `👋 ${name} has left the meeting.`
                );
            }

            return;
        }

        /* ---------- CHAT ---------- */

        if (
            data.type
            ===
            'chat'
        ) {

            if (isSelf) {
                return;
            }

            const name =
                data.data?.name
                ||
                'User';

            const text =
                data.data?.text
                ||
                '';

            if (!text) {
                return;
            }

            addChatBubble(
                name,
                text,
                false
            );

            if (
                activeTab
                !==
                'chat'
            ) {

                unreadChat++;

                updateChatBadge();
            }

            return;
        }

        /* ---------- MIC STATUS ---------- */

        if (
            data.type
            ===
            'mic-status'
        ) {

            const uid =
                String(
                    data.data.userId
                    ||
                    data.fromUserId
                );

            if (
                uid ===
                String(MY_USER_ID)
            ) {
                return;
            }

            participantMicStatus[
                uid
                ] =
                data.data.muted;

            const micOff =
                document.getElementById(
                    'micoff-' + uid
                );

            if (micOff) {

                micOff.style.display =
                    data.data.muted
                        ? 'flex'
                        : 'none';
            }

            const icon =
                document.getElementById(
                    'participant-mic-icon-'
                    +
                    uid
                );

            if (icon) {

                icon.className =
                    data.data.muted
                        ? 'fa fa-microphone-slash'
                        : 'fa fa-microphone';

                icon.style.color =
                    data.data.muted
                        ? 'var(--red)'
                        : 'var(--green)';
            }

            return;
        }

        /* ---------- CAMERA STATUS ---------- */

        if (
            data.type
            ===
            'camera-status'
        ) {

            const uid =
                String(
                    data.data.userId
                    ||
                    data.fromUserId
                );

            if (
                uid ===
                String(MY_USER_ID)
            ) {
                return;
            }

            const cameraOn =
                Boolean(
                    data.data.cameraOn
                );

            participantCameraStatus[
                uid
                ] =
                cameraOn;

            const info =
                knownParticipants[
                    uid
                    ];

            if (
                info
                &&
                !leftUsers.has(
                    uid
                )
            ) {

                addParticipantTile(
                    uid,
                    info.name,
                    info.initials,
                    info.isOrganizer
                    ||
                    false
                );
            }

            const video =
                document.getElementById(
                    'rvideo-' + uid
                );

            const avatar =
                document.getElementById(
                    'avatar-' + uid
                );

            if (!cameraOn) {

                if (video) {
                    video.style.display =
                        'none';
                }

                if (avatar) {
                    avatar.style.display =
                        'flex';
                }

                return;
            }

            /*
         * FIX:
         * camera-status may arrive before
         * actual WebRTC video track.
         */
            const revealRemoteCamera =
                () => {

                    attachRemoteStream(
                        uid
                    );

                    const stream =
                        remoteStreams[
                            uid
                            ];

                    const hasVideo =
                        Boolean(
                            stream
                                ?.getVideoTracks()
                                .some(
                                    track =>
                                        track.readyState
                                        !==
                                        'ended'
                                )
                        );

                    const currentVideo =
                        document.getElementById(
                            'rvideo-' + uid
                        );

                    const currentAvatar =
                        document.getElementById(
                            'avatar-' + uid
                        );

                    if (
                        hasVideo
                        &&
                        currentVideo
                    ) {

                        currentVideo.autoplay =
                            true;

                        currentVideo.playsInline =
                            true;

                        currentVideo.muted =
                            true;

                        currentVideo.style.display =
                            'block';

                        currentVideo
                            .play()
                            .catch(
                                () => {}
                            );

                        if (
                            currentAvatar
                        ) {

                            currentAvatar.style.display =
                                'none';
                        }
                    }
                };

            revealRemoteCamera();

            [
                100,
                300,
                700,
                1500
            ].forEach(
                delay => {

                    setTimeout(
                        revealRemoteCamera,
                        delay
                    );

                }
            );

            return;
        }

        /* ---------- DIRECT SIGNALS ---------- */

        if (
            String(
                data.toUserId
            )
            !==
            String(
                MY_USER_ID
            )
        ) {
            return;
        }

        if (!data.data) {
            return;
        }

        if (
            leftUsers.has(
                from
            )
            &&
            [
                'offer',
                'ice-candidate'
            ].includes(
                data.type
            )
        ) {
            return;
        }

        try {

            /* ---------- OFFER ---------- */

            if (
                data.type
                ===
                'offer'
            ) {

                const pc =
                    createPeerConnection(
                        from
                    );

                const polite =
                    isPolite(
                        from
                    );

                const collision =
                    makingOffer[
                        from
                        ]
                    ||
                    pc.signalingState
                    !==
                    'stable';

                ignoreOffer[
                    from
                    ] =
                    !polite
                    &&
                    collision;

                if (
                    ignoreOffer[
                        from
                        ]
                ) {
                    return;
                }

                const sdp =
                    decodeSdp(
                        data.data.sdp
                    );

                await pc.setRemoteDescription(
                    new RTCSessionDescription({
                        type:
                            data.data.type
                            ||
                            'offer',

                        sdp
                    })
                );

                await syncLocalTracksToPeer(
                    from
                );

                if (
                    pendingCandidates[
                        from
                        ]?.length
                ) {

                    for (
                        const candidate
                        of
                        pendingCandidates[
                            from
                            ]
                        ) {

                        await pc
                            .addIceCandidate(
                                new RTCIceCandidate(
                                    candidate
                                )
                            )
                            .catch(
                                () => {}
                            );
                    }

                    delete pendingCandidates[
                        from
                        ];
                }

                await pc.setLocalDescription();

                sendSignal(
                    from,
                    'answer',
                    {
                        type:
                        pc.localDescription.type,

                        sdp:
                            btoa(
                                unescape(
                                    encodeURIComponent(
                                        pc.localDescription.sdp
                                    )
                                )
                            )
                    }
                );

                /* ---------- ANSWER ---------- */

            } else if (
                data.type
                ===
                'answer'
            ) {

                const pc =
                    peers[from];

                if (!pc) {
                    return;
                }

                const sdp =
                    decodeSdp(
                        data.data.sdp
                    );

                if (
                    pc.signalingState
                    ===
                    'have-local-offer'
                ) {

                    await pc.setRemoteDescription(
                        new RTCSessionDescription({
                            type:
                                data.data.type
                                ||
                                'answer',

                            sdp
                        })
                    );

                    if (
                        pendingCandidates[
                            from
                            ]?.length
                    ) {

                        for (
                            const candidate
                            of
                            pendingCandidates[
                                from
                                ]
                            ) {

                            await pc
                                .addIceCandidate(
                                    new RTCIceCandidate(
                                        candidate
                                    )
                                )
                                .catch(
                                    () => {}
                                );
                        }

                        delete pendingCandidates[
                            from
                            ];
                    }
                }

                /* ---------- ICE ---------- */

            } else if (
                data.type
                ===
                'ice-candidate'
            ) {

                const candidate =
                    data.data.candidate;

                if (!candidate) {
                    return;
                }

                const pc =
                    peers[from];

                if (
                    !pc
                    ||
                    !pc.remoteDescription
                ) {

                    if (
                        !pendingCandidates[
                            from
                            ]
                    ) {
                        pendingCandidates[
                            from
                            ] = [];
                    }

                    pendingCandidates[
                        from
                        ].push(
                        candidate
                    );

                    return;
                }

                try {

                    await pc.addIceCandidate(
                        new RTCIceCandidate(
                            candidate
                        )
                    );

                } catch (error) {

                    if (
                        !ignoreOffer[
                            from
                            ]
                    ) {

                        console.error(
                            'ICE candidate error:',
                            error
                        );
                    }
                }

                /* ---------- MUTE ---------- */

            } else if (
                data.type
                ===
                'mute'
            ) {

                if (!localStream) {
                    return;
                }

                await applyBestAudioConstraints(
                    localStream
                );

                localStream
                    .getAudioTracks()
                    .forEach(
                        track => {
                            track.enabled =
                                false;
                        }
                    );

                isMicOn =
                    false;

                const button =
                    document.getElementById(
                        'ctrl-mic'
                    );

                const micOff =
                    document.getElementById(
                        'micoff-'
                        +
                        MY_USER_ID
                    );

                if (button) {

                    button.innerHTML =
                        '<i class="fa fa-microphone-slash"></i>';

                    button.classList.add(
                        'off'
                    );
                }

                if (micOff) {

                    micOff.style.display =
                        'flex';
                }

                stopRecognition();

                showToast(
                    'You have been muted'
                );

                broadcastMyMicStatus();

            } else if (
                data.type
                ===
                'unmute'
            ) {

                showToast(
                    'You have been unmuted'
                );
            }

        } catch (error) {

            console.error(
                'Signal handle error:',
                error
            );
        }
    }

    /* ============================================================
   SIGNAL REQUEST
============================================================ */

    async function sendSignal(
        toUserId,
        type,
        data
    ) {

        try {

            const response =
                await fetch(
                    SIGNAL_URL,
                    {
                        method:
                            'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                            CSRF
                        },

                        body:
                            JSON.stringify({
                                to_user_id:
                                toUserId,

                                type,

                                data
                            })
                    }
                );

            if (!response.ok) {

                console.error(
                    'sendSignal failed:',
                    await response.text()
                );
            }

        } catch (error) {

            console.error(
                'sendSignal error:',
                error
            );
        }
    }

    /* ============================================================
   MIC
============================================================ */

    async function toggleMic() {

        if (
            !localStream
            ||
            !localStream
                .getAudioTracks()
                .length
        ) {

            await startAudio();

            if (
                !localStream
                ||
                !localStream
                    .getAudioTracks()
                    .length
            ) {
                return;
            }
        }

        isMicOn =
            !isMicOn;

        localStream
            .getAudioTracks()
            .forEach(
                track => {

                    track.enabled =
                        isMicOn;

                }
            );

        const button =
            document.getElementById(
                'ctrl-mic'
            );

        const micOff =
            document.getElementById(
                'micoff-'
                +
                MY_USER_ID
            );

        const speaking =
            document.getElementById(
                'speaking-'
                +
                MY_USER_ID
            );

        if (isMicOn) {

            if (button) {

                button.innerHTML =
                    '<i class="fa fa-microphone"></i>';

                button.classList.remove(
                    'off'
                );
            }

            if (micOff) {

                micOff.style.display =
                    'none';
            }

            if (!recognition) {
                startTranscript();
            }
            setTimeout(startRecognition, 50);
            connectToAll();
            await syncTracksToEveryPeer(true);
            [120, 450].forEach(delay => setTimeout(() => {
                if (isMicOn) {
                    connectToAll();
                    syncTracksToEveryPeer(true);
                }
            }, delay));

        } else {

            if (button) {

                button.innerHTML =
                    '<i class="fa fa-microphone-slash"></i>';

                button.classList.add(
                    'off'
                );
            }

            if (micOff) {

                micOff.style.display =
                    'flex';
            }

            if (speaking) {

                speaking.style.display =
                    'none';
            }

            stopRecognition();
        }

        await syncTracksToEveryPeer(
            false
        );

        if (isMicOn) {

            Object
                .keys(peers)
                .forEach(
                    uid => {

                        if (
                            shouldInitiatePeer(
                                uid
                            )
                        ) {

                            queuePeerNegotiation(
                                uid,
                                {
                                    reason:
                                        'microphone-enabled',

                                    delay:
                                        10
                                }
                            );
                        }

                    }
                );
        }

        broadcastMyMicStatus();
    }

    /* ============================================================
   CAMERA - FIXED
============================================================ */

    async function toggleCamera() {

        if (!localStream) {

            await startAudio();

            if (!localStream) {
                return;
            }
        }

        let videoTrack =
            localStream
                .getVideoTracks()
                .find(
                    track =>
                        track.readyState
                        ===
                        'live'
                )
            ||
            null;

        /* ---------- CAMERA OFF -> ON ---------- */

        if (!isCameraOn) {

            /*
         * If initial startup camera request
         * failed, request it now.
         */
            if (!videoTrack) {

                try {

                    const cameraStream =
                        await navigator
                            .mediaDevices
                            .getUserMedia({
                                audio:
                                    false,

                                video: {
                                    width: {
                                        ideal:
                                            1280
                                    },

                                    height: {
                                        ideal:
                                            720
                                    },

                                    frameRate: {
                                        ideal:
                                            24,

                                        max:
                                            30
                                    },

                                    facingMode:
                                        'user'
                                }
                            });

                    videoTrack =
                        cameraStream
                            .getVideoTracks()[0];

                    if (!videoTrack) {

                        throw new Error(
                            'Camera did not return a video track.'
                        );
                    }

                    /*
                 * Remove stale video tracks.
                 */
                    localStream
                        .getVideoTracks()
                        .forEach(
                            oldTrack => {

                                if (
                                    oldTrack
                                    !==
                                    videoTrack
                                ) {

                                    localStream.removeTrack(
                                        oldTrack
                                    );

                                    try {
                                        oldTrack.stop();
                                    } catch (error) {}
                                }

                            }
                        );

                    videoTrack.enabled =
                        false;

                    localStream.addTrack(
                        videoTrack
                    );

                    try {

                        videoTrack.contentHint =
                            'motion';

                    } catch (error) {}

                } catch (error) {

                    console.error(
                        'Camera access failed:',
                        error
                    );

                    showToast(
                        '📷 Camera could not start. Allow camera access in browser settings.'
                    );

                    return;
                }
            }

            /*
         * Enable BEFORE telling other users.
         */
            videoTrack.enabled =
                true;

            isCameraOn =
                true;

            const button =
                document.getElementById(
                    'ctrl-camera'
                );

            const localVideo =
                document.getElementById(
                    'localVideo'
                );

            const avatar =
                document.getElementById(
                    'avatar-'
                    +
                    MY_USER_ID
                );

            if (button) {

                button.innerHTML =
                    '<i class="fa fa-video"></i>';

                button.classList.remove(
                    'off'
                );
            }

            if (localVideo) {

                localVideo.srcObject =
                    localStream;

                localVideo.autoplay =
                    true;

                localVideo.muted =
                    true;

                localVideo.playsInline =
                    true;

                localVideo.style.display =
                    'block';

                localVideo
                    .play()
                    .catch(
                        () => {}
                    );
            }

            if (avatar) {

                avatar.style.display =
                    'none';
            }

            /*
         * MAIN FIX:
         * Push camera track to EVERY joined peer
         * before camera-status is broadcast.
         */
            await syncCameraToAllPeers(
                videoTrack
            );

            await syncTracksToEveryPeer(
                false
            );

            broadcastMyCameraStatus();

            /*
         * Tiny retries cover browsers where
         * sender/transceiver update is delayed.
         */
            [
                350,
                900
            ].forEach(
                delay => {

                    setTimeout(
                        async () => {

                            if (!isCameraOn) {
                                return;
                            }

                            const currentTrack =
                                localStream
                                    ?.getVideoTracks()
                                    .find(
                                        track =>
                                            track.readyState
                                            ===
                                            'live'
                                    );

                            if (!currentTrack) {
                                return;
                            }

                            await syncCameraToAllPeers(
                                currentTrack
                            );

                            broadcastMyCameraStatus();

                        },
                        delay
                    );

                }
            );

            return;
        }

        /* ---------- CAMERA ON -> OFF ---------- */

        isCameraOn =
            false;

        /*
     * Do not stop/remove video track.
     * Only disable it. This keeps existing
     * video sender alive and makes next ON instant.
     */
        if (videoTrack) {

            videoTrack.enabled =
                false;
        }

        const button =
            document.getElementById(
                'ctrl-camera'
            );

        const localVideo =
            document.getElementById(
                'localVideo'
            );

        const avatar =
            document.getElementById(
                'avatar-'
                +
                MY_USER_ID
            );

        if (button) {

            button.innerHTML =
                '<i class="fa fa-video-slash"></i>';

            button.classList.add(
                'off'
            );
        }

        if (localVideo) {

            localVideo.style.display =
                'none';
        }

        if (avatar) {

            avatar.style.display =
                'flex';
        }

        broadcastMyCameraStatus();
    }

    /* ============================================================
   ORGANIZER MUTE PARTICIPANT
============================================================ */

    function toggleParticipantMic(
        userId
    ) {

        const uid =
            String(userId);

        const isMuted =
            participantMicStatus[
                uid
                ]
            ||
            false;

        const willBeMuted =
            !isMuted;

        participantMicStatus[
            uid
            ] =
            willBeMuted;

        sendSignal(
            uid,
            willBeMuted
                ? 'mute'
                : 'unmute',
            {
                by:
                MY_USER_ID
            }
        );

        const icon =
            document.getElementById(
                'participant-mic-icon-'
                +
                uid
            );

        if (icon) {

            icon.className =
                willBeMuted
                    ? 'fa fa-microphone-slash'
                    : 'fa fa-microphone';

            icon.style.color =
                willBeMuted
                    ? 'var(--red)'
                    : 'var(--green)';
        }

        const micOff =
            document.getElementById(
                'micoff-' + uid
            );

        if (micOff) {

            micOff.style.display =
                willBeMuted
                    ? 'flex'
                    : 'none';
        }
    }

    /* ============================================================
   PEOPLE ROW
============================================================ */

    function ensurePanelRow(
        userId,
        name,
        initials,
        isOrganizer
    ) {

        if (
            document.getElementById(
                'panel-row-' + userId
            )
        ) {
            return;
        }

        addParticipantPanelRow(
            userId,
            name,
            initials,
            isOrganizer
        );
    }

    function addParticipantPanelRow(
        userId,
        name,
        initials,
        isOrganizer
    ) {

        const uid =
            String(userId);

        const container =
            document.getElementById(
                'other-participants-panel'
            );

        if (!container) {
            return;
        }

        if (
            document.getElementById(
                'panel-row-' + uid
            )
        ) {
            return;
        }

        const color =
            isOrganizer
                ? '#3b82f6,#06b6d4'
                : '#22c55e,#06b6d4';

        const roleLabel =
            isOrganizer
                ? 'Organizer'
                : 'Participant';

        const isOnline =
            onlineUsers.has(
                uid
            );

        const hasLeft =
            leftUsers.has(
                uid
            );

        const statusLabel =
            isOnline
                ? 'Joined'
                : (
                    hasLeft
                        ? 'Left'
                        : 'Not joined yet'
                );

        const row =
            document.createElement(
                'div'
            );

        row.id =
            'panel-row-' + uid;

        row.className =
            isOnline
                ? 'participant-online'
                : 'participant-offline';

        row.style.cssText =
            `
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px;
            margin-top:8px;
            border-radius:12px;
            background:${
                isOnline
                    ? 'rgba(34,197,94,.08)'
                    : 'var(--surface2)'
            };
            border:1px solid ${
                isOnline
                    ? 'rgba(34,197,94,.2)'
                    : 'var(--border)'
            };
            opacity:${
                isOnline
                    ? '1'
                    : '.5'
            };
        `;

        row.innerHTML =
            `
            <div
                style="
                    width:36px;
                    height:36px;
                    border-radius:50%;
                    background:
                        linear-gradient(
                            135deg,
                            ${color}
                        );
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:12px;
                    font-weight:700;
                    color:white;
                "
            >
                ${escapeHtml(initials)}
            </div>

            <div style="flex:1;">

                <div
                    style="
                        font-size:13px;
                        font-weight:600;
                        display:flex;
                        align-items:center;
                        gap:5px;
                    "
                >
                    ${escapeHtml(name)}

                    ${
                isOrganizer
                    ?
                    '<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>'
                    :
                    ''
            }
                </div>

                <div
                    class="join-status"
                    data-role="${roleLabel}"
                    style="
                        font-size:10px;
                        color:${
                isOnline
                    ? 'var(--green)'
                    : 'var(--muted)'
            };
                    "
                >
                    ${roleLabel} • ${statusLabel}
                </div>

            </div>

            ${
                !isOrganizer
                    ?
                    `
                    <button
                        onclick="toggleParticipantMic('${uid}')"
                        id="participant-mic-btn-${uid}"
                        title="Mute/Unmute"
                        style="
                            background:none;
                            border:none;
                            cursor:pointer;
                            padding:4px;
                        "
                    >
                        <i
                            class="fa fa-microphone"
                            id="participant-mic-icon-${uid}"
                            style="
                                font-size:13px;
                                color:var(--green);
                            "
                        ></i>
                    </button>
                    `
                    :
                    ''
            }

            <span
                class="online-dot"
                style="
                    width:8px;
                    height:8px;
                    background:${
                isOnline
                    ? 'var(--green)'
                    : 'var(--surface2)'
            };
                    border-radius:50%;
                    border:${
                isOnline
                    ? 'none'
                    : '1px solid var(--border)'
            };
                "
            ></span>
        `;

        container.appendChild(
            row
        );
    }

    /* ============================================================
   VIDEO TILE
============================================================ */

    function addParticipantTile(
        userId,
        name,
        initials,
        isOrganizer
    ) {

        const uid =
            String(userId);

        if (
            document.getElementById(
                'tile-' + uid
            )
        ) {
            return;
        }

        if (
            leftUsers.has(
                uid
            )
        ) {
            return;
        }

        const colors = [
            '#3b82f6,#06b6d4',
            '#8b5cf6,#ec4899',
            '#22c55e,#06b6d4',
            '#f59e0b,#ef4444',
            '#64748b,#334155',
            '#ec4899,#f59e0b'
        ];

        const color =
            isOrganizer
                ? colors[0]
                : colors[
                    Math.floor(
                        Math.random()
                        *
                        colors.length
                    )
                    ];

        const grid =
            document.getElementById(
                'video-grid'
            );

        if (!grid) {
            return;
        }

        const tile =
            document.createElement(
                'div'
            );

        tile.className =
            'video-tile';

        tile.id =
            'tile-' + uid;

        const startsMuted =
            participantMicStatus[
                uid
                ]
            !==
            false;

        const cameraOn =
            participantCameraStatus[
                uid
                ]
            ===
            true;

        tile.innerHTML =
            `
            <div class="video-placeholder">

                <video
                    id="rvideo-${uid}"
                    autoplay
                    muted
                    playsinline
                    style="
                        display:${
                cameraOn
                    ? 'block'
                    : 'none'
            };
                    "
                ></video>

                <div
                    class="avatar-circle"
                    id="avatar-${uid}"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                ${color}
                            );
                        display:${
                cameraOn
                    ? 'none'
                    : 'flex'
            };
                    "
                >
                    ${escapeHtml(initials)}
                </div>

                <button
                    class="tile-expand-btn"
                    onclick="toggleMaximize('${uid}')"
                    title="Maximize / Minimize"
                >
                    <i
                        class="fa fa-expand"
                        id="expand-icon-${uid}"
                    ></i>
                </button>

            </div>

            <div class="tile-info">

                <div class="tile-name">

                    ${
                isOrganizer
                    ?
                    '<i class="fa fa-crown crown-icon"></i> '
                    :
                    ''
            }

                    ${escapeHtml(name)}

                    <span
                        class="role-badge ${
                isOrganizer
                    ? 'organizer'
                    : 'participant'
            }"
                    >
                        ${
                isOrganizer
                    ? 'Organizer'
                    : 'Participant'
            }
                    </span>

                </div>

                <div class="tile-icons">

                    <div
                        class="speaking-indicator"
                        id="speaking-${uid}"
                        style="display:none;"
                    >
                        <div class="speaking-bar"></div>
                        <div class="speaking-bar"></div>
                        <div class="speaking-bar"></div>
                    </div>

                    <div
                        class="mic-off"
                        id="micoff-${uid}"
                        style="
                            display:${
                startsMuted
                    ? 'flex'
                    : 'none'
            };
                        "
                    >
                        <i
                            class="fa fa-microphone-slash"
                        ></i>
                    </div>

                </div>

            </div>
        `;

        if (isOrganizer) {
            grid.prepend(
                tile
            );
        } else {
            grid.appendChild(
                tile
            );
        }

        ensurePanelRow(
            uid,
            name,
            initials,
            isOrganizer
        );

        updateParticipantRow(
            uid,
            onlineUsers.has(
                uid
            )
        );

        /*
     * If WebRTC track already exists, attach it
     * immediately to newly created tile.
     */
        attachRemoteStream(
            uid
        );
    }

    /* ============================================================
   TRANSCRIPTION
============================================================ */

    function startTranscript() {

        const SpeechRecognition =
            window.SpeechRecognition
            ||
            window.webkitSpeechRecognition;

        if (!SpeechRecognition) {

            showToast(
                '⚠️ Live transcription requires Chrome or Edge.'
            );

            return;
        }

        recognition =
            new SpeechRecognition();

        recognition.continuous =
            true;

        recognition.interimResults =
            true;

        recognition.maxAlternatives =
            1;

        recognition.lang =
            'en-US';

        const indicator =
            document.getElementById(
                'listening-indicator'
            );

        const listenText =
            document.getElementById(
                'listening-text'
            );

        recognition.onstart =
            () => {

                recognitionRunning =
                    true;

                if (indicator) {

                    indicator.style.display =
                        'flex';
                }

                if (listenText) {

                    listenText.textContent =
                        'Listening in English…';
                }
            };

        recognition.onresult =
            event => {

                if (!isMicOn) {

                    stopRecognition();

                    return;
                }

                let interimText =
                    '';

                for (
                    let index =
                        event.resultIndex;

                    index
                    <
                    event.results.length;

                    index++
                ) {

                    const result =
                        event.results[
                            index
                            ];

                    const text =
                        result[0]
                            .transcript
                            .trim();

                    if (!text) {
                        continue;
                    }

                    if (
                        result.isFinal
                    ) {

                        const speaking =
                            document.getElementById(
                                'speaking-'
                                +
                                MY_USER_ID
                            );

                        if (speaking) {

                            speaking.style.display =
                                'none';
                        }

                        showLocalTranscript(
                            text,
                            false
                        );

                        saveTranscript(
                            text
                        );

                    } else {

                        interimText +=
                            (
                                interimText
                                    ? ' '
                                    : ''
                            )
                            +
                            text;
                    }
                }

                if (interimText) {

                    const speaking =
                        document.getElementById(
                            'speaking-'
                            +
                            MY_USER_ID
                        );

                    if (speaking) {

                        speaking.style.display =
                            'flex';
                    }

                    showLocalTranscript(
                        interimText,
                        true
                    );
                }
            };

        recognition.onerror =
            event => {

                recognitionRunning =
                    false;

                if (
                    event.error
                    ===
                    'not-allowed'
                    ||
                    event.error
                    ===
                    'service-not-allowed'
                ) {

                    showToast(
                        'Microphone/speech recognition permission is required.'
                    );

                    return;
                }

                if (
                    event.error
                    !==
                    'aborted'
                    &&
                    event.error
                    !==
                    'no-speech'
                ) {

                    console.warn(
                        'Speech recognition:',
                        event.error
                    );
                }

                scheduleRecognitionRestart(
                    300
                );
            };

        recognition.onend =
            () => {

                recognitionRunning =
                    false;

                if (indicator) {

                    indicator.style.display =
                        'none';
                }

                scheduleRecognitionRestart(
                    300
                );
            };
    }

    let recognitionRestartTimer =
        null;

    let recognitionStopping =
        false;

    function scheduleRecognitionRestart(
        delay = 300
    ) {

        if (
            !recognition
            ||
            recognitionStopping
            ||
            !isMicOn
            ||
            document.visibilityState
            !==
            'visible'
        ) {
            return;
        }

        if (recognitionRestartTimer) {

            clearTimeout(
                recognitionRestartTimer
            );
        }

        recognitionRestartTimer =
            setTimeout(
                () => {

                    recognitionRestartTimer =
                        null;

                    startRecognition();
                },
                delay
            );
    }

    function startRecognition() {

        if (
            !recognition
            ||
            recognitionRunning
            ||
            recognitionStopping
            ||
            !isMicOn
            ||
            document.visibilityState
            !==
            'visible'
        ) {
            return;
        }

        try {

            recognition.start();

            recognitionRunning =
                true;

        } catch (error) {

            recognitionRunning =
                false;
        }
    }

    function toggleTranscriptLanguage() {

        currentLang =
            'en-US';

        const button =
            document.getElementById(
                'lang-toggle-btn'
            );

        if (button) {

            button.textContent =
                '🌐 English only';
        }

        showToast(
            'Transcript language: English'
        );

        if (recognition) {

            stopRecognition();

            recognition =
                null;
        }

        startTranscript();

        if (isMicOn) {

            scheduleRecognitionRestart(
                300
            );
        }
    }

    function stopRecognition() {

        if (!recognition) {
            return;
        }

        if (recognitionRestartTimer) {

            clearTimeout(
                recognitionRestartTimer
            );

            recognitionRestartTimer =
                null;
        }

        recognitionStopping =
            true;

        try {

            if (recognitionRunning) {
                recognition.abort();
            }

        } catch (error) {}

        recognitionRunning =
            false;

        setTimeout(
            () => {

                recognitionStopping =
                    false;
            },
            250
        );
    }

    function showLocalTranscript(
        text,
        isInterim
    ) {

        const body =
            document.getElementById(
                'transcript-body'
            );

        if (!body) {
            return;
        }

        body
            .querySelector(
                '[data-empty]'
            )
            ?.remove();

        let liveEntry =
            document.getElementById(
                'live-entry-'
                +
                MY_USER_ID
            );

        if (isInterim) {

            if (!liveEntry) {

                liveEntry =
                    document.createElement(
                        'div'
                    );

                liveEntry.className =
                    'transcript-entry';

                liveEntry.id =
                    'live-entry-'
                    +
                    MY_USER_ID;

                liveEntry.innerHTML =
                    `
                    <div
                        class="transcript-avatar"
                        style="
                            background:
                                linear-gradient(
                                    135deg,
                                    #3b82f6,
                                    #06b6d4
                                );
                        "
                    >
                        ${escapeHtml(MY_INITIALS)}
                    </div>

                    <div class="transcript-content">

                        <div class="transcript-meta">

                            <span class="transcript-name">
                                ${escapeHtml(MY_NAME)} (You)
                            </span>

                            <span class="transcript-time">
                                ${
                        new Date()
                            .toLocaleTimeString(
                                'en-US',
                                {
                                    hour:
                                        '2-digit',

                                    minute:
                                        '2-digit'
                                }
                            )
                    }
                            </span>

                        </div>

                        <div
                            class="transcript-text"
                            style="
                                opacity:.6;
                                font-style:italic;
                            "
                        ></div>

                    </div>
                `;

                body.appendChild(
                    liveEntry
                );
            }

            liveEntry
                .querySelector(
                    '.transcript-text'
                )
                .textContent =
                text;

            body.scrollTop =
                body.scrollHeight;

            return;
        }

        if (liveEntry) {

            const textElement =
                liveEntry.querySelector(
                    '.transcript-text'
                );

            textElement.style.opacity =
                '1';

            textElement.style.fontStyle =
                'normal';

            textElement.textContent =
                text;

            liveEntry.removeAttribute(
                'id'
            );

        } else {

            const element =
                document.createElement(
                    'div'
                );

            element.className =
                'transcript-entry';

            element.innerHTML =
                `
                <div
                    class="transcript-avatar"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #3b82f6,
                                #06b6d4
                            );
                    "
                >
                    ${escapeHtml(MY_INITIALS)}
                </div>

                <div class="transcript-content">

                    <div class="transcript-meta">

                        <span class="transcript-name">
                            ${escapeHtml(MY_NAME)} (You)
                        </span>

                        <span class="transcript-time">
                            ${
                    new Date()
                        .toLocaleTimeString(
                            'en-US',
                            {
                                hour:
                                    '2-digit',

                                minute:
                                    '2-digit'
                            }
                        )
                }
                        </span>

                    </div>

                    <div class="transcript-text">
                        ${escapeHtml(text)}
                    </div>

                </div>
            `;

            body.appendChild(
                element
            );
        }

        body.scrollTop =
            body.scrollHeight;
    }

    function handleTranscript(
        data
    ) {

        if (
            String(
                data.userId
            )
            ===
            String(
                MY_USER_ID
            )
        ) {
            return;
        }

        const body =
            document.getElementById(
                'transcript-body'
            );

        if (!body) {
            return;
        }

        body
            .querySelector(
                '[data-empty]'
            )
            ?.remove();

        const element =
            document.createElement(
                'div'
            );

        element.className =
            'transcript-entry';

        element.innerHTML =
            `
            <div
                class="transcript-avatar"
                style="
                    background:
                        linear-gradient(
                            135deg,
                            #8b5cf6,
                            #ec4899
                        );
                "
            >
                ${escapeHtml(
                data.userInitials
                ||
                '?'
            )}
            </div>

            <div class="transcript-content">

                <div class="transcript-meta">

                    <span class="transcript-name">
                        ${escapeHtml(
                data.userName
                ||
                'User'
            )}
                    </span>

                    <span class="transcript-time">
                        ${escapeHtml(
                data.spokenAt
                ||
                ''
            )}
                    </span>

                </div>

                <div class="transcript-text">
                    ${escapeHtml(
                data.text
                ||
                ''
            )}
                </div>

            </div>
        `;

        body.appendChild(
            element
        );

        body.scrollTop =
            body.scrollHeight;
    }

    async function saveTranscript(
        text
    ) {

        try {

            await fetch(
                TRANSCRIPT_URL,
                {
                    method:
                        'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                        CSRF
                    },

                    body:
                        JSON.stringify({
                            text
                        })
                }
            );

        } catch (error) {

            console.error(
                'Transcript save error:',
                error
            );
        }
    }

    /* ============================================================
   CHAT
============================================================ */

    function sendChat() {

        const input =
            document.getElementById(
                'chat-input'
            );

        if (!input) {
            return;
        }

        const text =
            input.value.trim();

        if (!text) {
            return;
        }

        addChatBubble(
            MY_NAME,
            text,
            true
        );

        input.value =
            '';

        sendSignal(
            'all',
            'chat',
            {
                text,

                name:
                MY_NAME,

                initials:
                MY_INITIALS
            }
        );
    }

    function addChatBubble(
        name,
        text,
        isMe
    ) {

        const body =
            document.getElementById(
                'chat-body'
            );

        if (!body) {
            return;
        }

        body
            .querySelector(
                '[data-empty]'
            )
            ?.remove();

        const safeName =
            String(
                name
                ||
                (
                    isMe
                        ? MY_NAME
                        : 'User'
                )
            )
                .trim()
            ||
            'User';

        const initials =
            safeName
                .split(/\s+/)
                .slice(
                    0,
                    2
                )
                .map(
                    part =>
                        part.charAt(0)
                )
                .join('')
                .toUpperCase()
            ||
            '?';

        const time =
            new Date()
                .toLocaleTimeString(
                    'en-US',
                    {
                        hour:
                            '2-digit',

                        minute:
                            '2-digit'
                    }
                );

        const row =
            document.createElement(
                'div'
            );

        row.className =
            'chat-message-row '
            +
            (
                isMe
                    ? 'is-me'
                    : 'is-other'
            );

        row.innerHTML =
            `
            <div class="chat-message-avatar">
                ${
                escapeHtml(
                    isMe
                        ? MY_INITIALS
                        : initials
                )
            }
            </div>

            <div class="chat-message-content">

                <div class="chat-message-meta">

                    <strong>
                        ${
                escapeHtml(
                    isMe
                        ? MY_NAME + ' (You)'
                        : safeName
                )
            }
                    </strong>

                    <span>
                        ${time}
                    </span>

                </div>

                <div class="chat-message-bubble">
                    ${escapeHtml(text)}
                </div>

            </div>
        `;

        body.appendChild(
            row
        );

        body.scrollTop =
            body.scrollHeight;
    }

    /* ============================================================
   TOAST
============================================================ */

    const recentToastMessages =
        new Set();

    function showToast(
        message
    ) {

        if (
            recentToastMessages.has(
                message
            )
        ) {
            return;
        }

        recentToastMessages.add(
            message
        );

        setTimeout(
            () => {

                recentToastMessages.delete(
                    message
                );

            },
            4000
        );

        const stack =
            document.getElementById(
                'toast-stack'
            );

        if (!stack) {
            return;
        }

        const element =
            document.createElement(
                'div'
            );

        element.className =
            'toast';

        element.textContent =
            message;

        stack.appendChild(
            element
        );

        requestAnimationFrame(
            () => {

                element.classList.add(
                    'show'
                );

            }
        );

        setTimeout(
            () => {

                element.classList.remove(
                    'show'
                );

                element.classList.add(
                    'leaving'
                );

                setTimeout(
                    () => {

                        element.remove();

                    },
                    260
                );

            },
            3200
        );
    }

    /* ============================================================
   CONNECT
============================================================ */

    function connectToAll() {

        Object
            .keys(
                knownParticipants
            )
            .forEach(
                userId => {

                    if (
                        String(userId)
                        !==
                        String(MY_USER_ID)
                        &&
                        knownParticipants[
                            userId
                            ].hasJoined
                    ) {

                        createPeerConnection(
                            userId
                        );
                    }

                }
            );
    }

    function announceJoin() {

        sendSignal(
            'all',
            'user-joined',
            {
                userId:
                MY_USER_ID,

                name:
                MY_NAME,

                initials:
                MY_INITIALS
            }
        );
    }

    /* ============================================================
   CANCEL
============================================================ */

    async function cancelMeeting() {

        await sendSignal(
            'all',
            'meeting-cancelled',
            {
                message:
                    'Meeting has been cancelled by the organizer.'
            }
        );

        showToast(
            '🚫 Meeting cancelled.'
        );

        if (autoEndTimer) {

            clearTimeout(
                autoEndTimer
            );
        }

        await new Promise(
            resolve => {

                setTimeout(
                    resolve,
                    900
                );

            }
        );

        cleanup();

        document
            .getElementById(
                'cancel-form'
            )
            ?.submit();
    }

    /* ============================================================
   LEAVE
============================================================ */

    let disconnectNotified =
        false;

    async function leaveMeeting() {

        if (autoEndTimer) {

            clearTimeout(
                autoEndTimer
            );
        }

        if (
            disconnectNotified
        ) {
            return;
        }

        disconnectNotified =
            true;

        showToast(
            '📞 You left the meeting.'
        );

        try {

            await fetch(
                MARK_LEFT_URL,
                {
                    method:
                        'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                        CSRF
                    },

                    body:
                        JSON.stringify({})
                }
            );

        } catch (error) {

            console.error(
                'markLeft error:',
                error
            );
        }

        cleanup();

        setTimeout(
            () => {

                window.location.href =
                    LEAVE_URL;

            },
            600
        );
    }

    function notifyDisconnectBeacon() {

        if (
            disconnectNotified
        ) {
            return;
        }

        disconnectNotified =
            true;

        const payload =
            JSON.stringify({
                _token:
                CSRF
            });

        try {

            fetch(
                MARK_LEFT_URL,
                {
                    method:
                        'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                        CSRF
                    },

                    body:
                    payload,

                    keepalive:
                        true
                }
            )
                .catch(
                    () => {}
                );

        } catch (error) {}

        try {

            navigator.sendBeacon(
                MARK_LEFT_URL,
                new Blob(
                    [
                        payload
                    ],
                    {
                        type:
                            'application/json'
                    }
                )
            );

        } catch (error) {}
    }

    /* ============================================================
   CLEANUP
============================================================ */

    function cleanup() {

        if (autoEndTimer) {

            clearTimeout(
                autoEndTimer
            );

            autoEndTimer =
                null;
        }

        Object
            .values(
                offlineTimers
            )
            .forEach(
                timer => {

                    clearTimeout(
                        timer
                    );

                }
            );

        Object
            .values(
                audioRecoveryTimers
            )
            .forEach(
                timer => {

                    clearTimeout(
                        timer
                    );

                }
            );

        Object
            .values(peers)
            .forEach(
                pc => {

                    try {
                        pc.close();
                    } catch (error) {}

                }
            );

        localStream
            ?.getTracks()
            .forEach(
                track => {

                    try {
                        track.stop();
                    } catch (error) {}

                }
            );

        stopRecognition();
    }

    /* ============================================================
   HELPERS
============================================================ */

    function escapeHtml(
        text
    ) {

        const element =
            document.createElement(
                'div'
            );

        element.textContent =
            String(
                text
                ??
                ''
            );

        return element.innerHTML;
    }

    /* ============================================================
   RECOVERY EVENTS
============================================================ */

    window.addEventListener(
        'online',
        () => {

            recoverAllJoinedPeers(
                'network-online'
            );

        }
    );

    window.addEventListener(
        'pageshow',
        () => {

            recoverAllJoinedPeers(
                'page-show'
            );

        }
    );

    document.addEventListener(
        'visibilitychange',
        () => {

            if (
                document.visibilityState
                ===
                'visible'
            ) {

                setTimeout(
                    () => {

                        recoverAllJoinedPeers(
                            'tab-visible'
                        );

                    },
                    300
                );

                if (isMicOn) {

                    startRecognition();
                }

                /*
             * Camera is ON and tab/device resumed:
             * push track again without page reload.
             */
                if (isCameraOn) {

                    const videoTrack =
                        localStream
                            ?.getVideoTracks()
                            .find(
                                track =>
                                    track.readyState
                                    ===
                                    'live'
                            );

                    if (videoTrack) {

                        setTimeout(
                            () => {

                                syncCameraToAllPeers(
                                    videoTrack
                                );

                            },
                            300
                        );
                    }
                }
            }
        }
    );

    setInterval(
        () => {

            recoverAllJoinedPeers(
                'periodic-health-check'
            );

        },
        3000
    );

    window.addEventListener(
        'pagehide',
        () => {

            notifyDisconnectBeacon();

            cleanup();

        }
    );

    window.addEventListener(
        'beforeunload',
        () => {

            notifyDisconnectBeacon();

        }
    );

    /* ============================================================
   INITIAL LOAD
============================================================ */

    window.addEventListener(
        'load',
        async () => {

            await Promise.all([
                listenForSignals(),
                startAudio()
            ]);

            scheduleAutoEnd();

            /*
         * Render all invited participants in
         * People tab. Joined users also get
         * a video/audio peer tile.
         */
            Object
                .entries(
                    knownParticipants
                )
                .forEach(
                    (
                        [
                            userId,
                            participant
                        ]
                    ) => {

                        if (
                            String(userId)
                            ===
                            String(MY_USER_ID)
                            ||
                            participant.isOrganizer
                        ) {
                            return;
                        }

                        ensurePanelRow(
                            userId,
                            participant.name,
                            participant.initials,
                            false
                        );

                        if (
                            participant.hasJoined
                        ) {

                            addParticipantTile(
                                userId,
                                participant.name,
                                participant.initials,
                                false
                            );

                            markOnline(
                                userId
                            );

                            createPeerConnection(
                                userId
                            );
                        }

                    }
                );

            /*
         * Startup/reconnect handshake.
         */
            [
                0,
                500,
                1500,
                3500
            ].forEach(
                delay => {

                    setTimeout(
                        async () => {

                            announceJoin();

                            connectToAll();

                            await syncTracksToEveryPeer(
                                false
                            );

                            /*
                         * Camera may have been switched
                         * on while connections were
                         * being established.
                         */
                            if (isCameraOn) {

                                const videoTrack =
                                    localStream
                                        ?.getVideoTracks()
                                        .find(
                                            track =>
                                                track.readyState
                                                ===
                                                'live'
                                        );

                                if (videoTrack) {

                                    await syncCameraToAllPeers(
                                        videoTrack
                                    );
                                }
                            }

                        },
                        delay
                    );

                }
            );
        }
    );



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

    // V5 network heartbeat disabled.
    // Original handlers + V6 already synchronize state; duplicate POSTs caused /signal request storms.
    if (!window.__smartMeetV5StateHeartbeat) {
        window.__smartMeetV5StateHeartbeat = true;
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


    // Organizer cancellation: notify everyone before the HTTP form redirects.
    async function cancelMeeting() {
        if (window.__smV5Cancelling) return;
        window.__smV5Cancelling = true;

        try {
            await sendSignal('all', 'meeting-cancelled', {
                message: 'Meeting has been cancelled by the organizer.'
            });
        } catch (e) {}

        showToast('🚫 Meeting cancelled for everyone.');

        await new Promise(resolve => setTimeout(resolve, 220));
        cleanup();
        document.getElementById('cancel-form')?.submit();
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

    /* Initial join repair — one controlled pass only.
       Avoid repeated announce/status POST bursts on page load. */
    window.addEventListener('load', () => {
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
            smV6UnlockRemoteAudio();
        }, 350);
    });

    /* Repair only genuinely unhealthy peers.
       Healthy calls generate ZERO periodic /signal POST traffic. */
    if (!window.__smV6RepairTimer) {
        window.__smV6RepairTimer = setInterval(() => {
            if (document.visibilityState !== 'visible') return;

            Object.keys(knownParticipants || {}).forEach(uid => {
                uid = String(uid);
                if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;
                if (!(knownParticipants[uid]?.hasJoined || onlineUsers.has(uid))) return;

                const pc = peers[uid];
                if (!pc || pc.signalingState === 'closed') return;

                const unhealthy =
                    ['new','connecting','disconnected','failed'].includes(pc.connectionState) ||
                    ['new','checking','disconnected','failed'].includes(pc.iceConnectionState);

                if (unhealthy) {
                    smV6SyncPeerMedia(uid);
                    smV6ForceHandshake(uid, 'repair');
                } else {
                    attachRemoteStream(uid);
                }
            });

            // Audio playback refresh is local-only and creates no network request.
            smV6UnlockRemoteAudio();
        }, 8000);
    }

    /* One controlled post-toggle repair.
       Existing toggle handlers already change the real media track. */
    window.addEventListener('load', () => {
        document.getElementById('ctrl-mic')?.addEventListener('click', () => {
            setTimeout(() => {
                Object.keys(peers).forEach(uid => {
                    smV6SyncPeerMedia(uid);
                    smV6ForceHandshake(uid, 'mic-toggle');
                });
                broadcastMyMicStatus();
                smV6UnlockRemoteAudio();
            }, 220);
        });

        document.getElementById('ctrl-camera')?.addEventListener('click', () => {
            setTimeout(() => {
                Object.keys(peers).forEach(uid => {
                    smV6SyncPeerMedia(uid);
                    smV6ForceHandshake(uid, 'camera-toggle');
                });
                broadcastMyCameraStatus();
            }, 280);
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

</body>
</html>

