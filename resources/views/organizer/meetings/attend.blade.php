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




    <style id="sm-final-sidebar-position-fix">
        /* ================================================================
           SMARTMEET — ONE FINAL SIDEBAR SOURCE OF TRUTH
           Desktop: original right sidebar.
           <= 900px: centered near-full-screen overlay with small equal margins.
           No left-shift, no merging with video grid, no arbitrary 430px box.
        ================================================================ */

        /* Desktop / laptop */
        @media (min-width: 901px) {
            .main {
                flex-direction: row !important;
            }

            #side-panel {
                position: relative !important;
                inset: auto !important;
                transform: none !important;

                flex: 0 0 min(330px, 33vw) !important;
                width: min(330px, 33vw) !important;
                min-width: 280px !important;
                max-width: 360px !important;

                height: 100% !important;
                min-height: 0 !important;
                max-height: 100% !important;

                border-radius: 20px !important;
                overflow: hidden !important;
                z-index: 40 !important;
            }
        }

        /* Tablet / narrow desktop / small browser window */
        @media (max-width: 900px) {
            .main {
                position: relative !important;
                flex-direction: column !important;
            }

            .video-area {
                width: 100% !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }

            #side-panel {
                position: fixed !important;

                /* Almost full available screen, visually centered */
                left: 8px !important;
                right: 8px !important;
                top: 62px !important;
                bottom: 72px !important;
                transform: none !important;

                width: auto !important;
                min-width: 0 !important;
                max-width: none !important;

                height: auto !important;
                min-height: 220px !important;
                max-height: none !important;

                margin: 0 !important;
                border-radius: 18px !important;
                overflow: hidden !important;
                z-index: 90 !important;

                box-shadow: 0 24px 70px rgba(0,0,0,.48) !important;
            }
        }

        /* Phones */
        @media (max-width: 520px) {
            #side-panel {
                left: 6px !important;
                right: 6px !important;
                top: 54px !important;
                bottom: 66px !important;
                border-radius: 16px !important;
            }
        }

        /* Landscape phones / short screens */
        @media (max-height: 520px) and (orientation: landscape) {
            #side-panel {
                top: 46px !important;
                bottom: 54px !important;
                left: 6px !important;
                right: 6px !important;
            }
        }

        /* Tiny, non-invasive resize handle at top edge. */
        #side-panel .sm-panel-drag-handle {
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 62px;
            height: 18px;
            z-index: 120;
            cursor: ns-resize;
            touch-action: none;
            user-select: none;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        #side-panel .sm-panel-drag-handle::before {
            content: "";
            width: 40px;
            height: 4px;
            border-radius: 999px;
            background: rgba(203,213,225,.48);
        }

        #side-panel .sm-panel-drag-handle:hover::before {
            background: rgba(96,165,250,.85);
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
       ICE / STUN / TURN — AWS production configuration
       IMPORTANT:
       TURN credential is configured here and must match the
       coturn user configured on the server. Keep this value private.
    ============================================================ */
    const TURN_HOST = 'smartmeet.live';
    const TURN_IP = '13.203.230.232';
    const TURN_USERNAME = 'smartmeet';
    const TURN_CREDENTIAL = 'SAna09007@@';

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

    function attachRemoteStream(userId) {
        const uid = String(userId);

        if (uid === String(MY_USER_ID)) {
            const ownAudio = document.getElementById('audio-' + uid);

            if (ownAudio) {
                try { ownAudio.pause(); } catch (error) {}
                ownAudio.srcObject = null;
                ownAudio.remove();
            }

            document.querySelectorAll(`audio[data-peer-id="${uid}"]`).forEach(element => {
                try { element.pause(); } catch (error) {}
                element.srcObject = null;
                element.remove();
            });

            return;
        }

        const sourceStream = getOrCreateRemoteStream(uid);

        const localIds = new Set(
            (localStream?.getTracks?.() || []).map(track => track.id)
        );

        const audioTracks = sourceStream
            .getAudioTracks()
            .filter(track => track.readyState !== 'ended' && !localIds.has(track.id));

        document.querySelectorAll(`audio[data-peer-id="${uid}"]`).forEach((element, index) => {
            if (index === 0) return;
            try { element.pause(); } catch (error) {}
            element.srcObject = null;
            element.remove();
        });

        let audio = document.getElementById('audio-' + uid);

        if (!audio) {
            audio = document.createElement('audio');
            audio.id = 'audio-' + uid;
            audio.autoplay = true;
            audio.playsInline = true;
            audio.preload = 'auto';
            audio.muted = false;
            audio.defaultMuted = false;
            audio.volume = 1;
            audio.dataset.peerId = uid;
            audio.style.display = 'none';
            document.body.appendChild(audio);
        }

        const currentAudioIds = new Set(
            (audio.srcObject?.getAudioTracks?.() || []).map(track => track.id)
        );
        const nextAudioIds = new Set(audioTracks.map(track => track.id));

        const audioChanged =
            currentAudioIds.size !== nextAudioIds.size ||
            [...nextAudioIds].some(id => !currentAudioIds.has(id));

        if (audioChanged) {
            audio.srcObject = new MediaStream(audioTracks);
        }

        audio.muted = false;
        audio.defaultMuted = false;
        audio.volume = 1;

        if (audioTracks.length) {
            safelyPlayRemoteAudio(audio);
        }

        const videoTracks = sourceStream
            .getVideoTracks()
            .filter(track => track.readyState !== 'ended' && !localIds.has(track.id));

        const video = document.getElementById('rvideo-' + uid);
        const avatar = document.getElementById('avatar-' + uid);

        if (!video) return;

        const liveVideo = videoTracks.find(track =>
            track.readyState === 'live' && track.enabled
        ) || null;

        const currentVideoIds = new Set(
            (video.srcObject?.getVideoTracks?.() || []).map(track => track.id)
        );
        const nextVideoIds = new Set(videoTracks.map(track => track.id));

        const videoChanged =
            currentVideoIds.size !== nextVideoIds.size ||
            [...nextVideoIds].some(id => !currentVideoIds.has(id));

        if (videoChanged) {
            video.srcObject = new MediaStream(videoTracks);
        }

        video.autoplay = true;
        video.playsInline = true;
        video.muted = true;

        /*
         * camera-status tells us the user's intent; a live unmuted track also
         * proves the camera is actually arriving. Either ordering is accepted.
         */
        const shouldShow = Boolean(
            liveVideo &&
            (
                participantCameraStatus[uid] === true ||
                liveVideo.muted === false
            )
        );

        video.style.display = shouldShow ? 'block' : 'none';

        if (avatar) {
            avatar.style.display = shouldShow ? 'none' : 'flex';
        }

        if (shouldShow) {
            video.play().catch(error => {
                if (error?.name !== 'AbortError') {
                    console.warn('Remote video play failed:', uid, error);
                }
            });
        }
    }

    /* ============================================================
   TRACK SYNC
============================================================ */

    function peerSenderForKind(pc, kind) {
        if (!pc) return null;

        if (kind === 'audio' && pc.__smAudioSender) return pc.__smAudioSender;
        if (kind === 'video' && pc.__smVideoSender) return pc.__smVideoSender;

        const tx = (pc.getTransceivers?.() || []).find(item =>
            item.receiver?.track?.kind === kind ||
            item.sender?.track?.kind === kind
        );

        return tx?.sender || null;
    }

    async function syncLocalTracksToPeer(userId) {
        const uid = String(userId);
        const pc = peers[uid];

        if (!pc || pc.signalingState === 'closed') return false;

        const audioTrack =
            localStream?.getAudioTracks?.().find(track => track.readyState === 'live')
            || null;

        const videoTrack =
            localStream?.getVideoTracks?.().find(track => track.readyState === 'live')
            || null;

        const audioSender = peerSenderForKind(pc, 'audio');
        const videoSender = peerSenderForKind(pc, 'video');

        let changed = false;

        try {
            if (audioSender && audioSender.track !== audioTrack) {
                await audioSender.replaceTrack(audioTrack);
                changed = true;
            }
        } catch (error) {
            console.warn('Audio replaceTrack failed:', uid, error);
        }

        try {
            if (videoSender && videoSender.track !== videoTrack) {
                await videoSender.replaceTrack(videoTrack);
                changed = true;
            }
        } catch (error) {
            console.warn('Video replaceTrack failed:', uid, error);
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

    async function syncTracksToEveryPeer(forceNegotiation = false) {
        const tasks = Object.keys(peers).map(async uid => {
            const pc = peers[uid];
            if (!pc || pc.signalingState === 'closed' || leftUsers.has(String(uid))) return;

            await syncLocalTracksToPeer(uid);

            /*
             * With permanent audio/video transceivers, replaceTrack() itself
             * does NOT require a new SDP offer. Negotiate only when this peer
             * has never negotiated, or ICE/connection is genuinely failed.
             */
            const neverNegotiated = !pc.localDescription && !pc.remoteDescription;
            const failed =
                pc.connectionState === 'failed' ||
                pc.iceConnectionState === 'failed';

            if ((neverNegotiated || failed) && shouldInitiatePeer(uid)) {
                queuePeerNegotiation(uid, {
                    reason: neverNegotiated ? 'initial-media' : 'ice-repair',
                    iceRestart: failed,
                    force: failed,
                    delay: 30
                });
            }
        });

        await Promise.allSettled(tasks);
    }

    /* ============================================================
   CAMERA DELIVERY FIX
============================================================ */

    async function syncCameraToAllPeers(videoTrack) {
        if (!videoTrack) return;

        const ids = new Set();

        try {
            onlineUsers.forEach(uid => {
                uid = String(uid);
                if (uid !== String(MY_USER_ID) && !leftUsers.has(uid)) ids.add(uid);
            });
        } catch (error) {}

        try {
            Object.keys(knownParticipants || {}).forEach(uid => {
                uid = String(uid);
                if (
                    uid !== String(MY_USER_ID) &&
                    !leftUsers.has(uid) &&
                    knownParticipants?.[uid]?.hasJoined
                ) {
                    ids.add(uid);
                }
            });
        } catch (error) {}

        for (const uid of ids) {
            let pc = peers[uid];

            if (!pc || pc.signalingState === 'closed' || pc.connectionState === 'closed') {
                pc = createPeerConnection(uid);
            }

            if (!pc) continue;

            await syncLocalTracksToPeer(uid);

            const neverNegotiated = !pc.localDescription && !pc.remoteDescription;
            const failed =
                pc.connectionState === 'failed' ||
                pc.iceConnectionState === 'failed';

            if ((neverNegotiated || failed) && shouldInitiatePeer(uid)) {
                queuePeerNegotiation(uid, {
                    reason: neverNegotiated ? 'camera-initial-peer' : 'camera-ice-repair',
                    iceRestart: failed,
                    force: failed,
                    delay: 30
                });
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

    function createPeerConnection(userId) {
        const uid = String(userId);

        if (!uid || uid === String(MY_USER_ID) || leftUsers.has(uid)) {
            return null;
        }

        let pc = peers[uid];

        if (
            pc &&
            pc.signalingState !== 'closed' &&
            pc.connectionState !== 'closed' &&
            pc.connectionState !== 'failed'
        ) {
            return pc;
        }

        if (pc) {
            try { pc.close(); } catch (error) {}
        }

        pc = new RTCPeerConnection(iceConfig);
        peers[uid] = pc;

        /*
         * SINGLE STABLE WEBRTC MODEL:
         * Always create the same two m-lines in the same order.
         * Camera/mic toggles only replaceTrack(); they never add new m-lines.
         */
        const audioTx = pc.addTransceiver('audio', { direction: 'sendrecv' });
        const videoTx = pc.addTransceiver('video', { direction: 'sendrecv' });

        pc.__smAudioSender = audioTx.sender;
        pc.__smVideoSender = videoTx.sender;

        syncLocalTracksToPeer(uid).catch(error =>
            console.warn('Initial media sync failed:', uid, error)
        );

        pc.onnegotiationneeded = () => {
            if (!shouldInitiatePeer(uid) || pc.signalingState !== 'stable') return;

            const neverNegotiated = !pc.localDescription && !pc.remoteDescription;
            if (!neverNegotiated) return;

            queuePeerNegotiation(uid, {
                reason: 'initial-negotiation',
                delay: 30
            });
        };

        pc.onicecandidate = event => {
            if (!event.candidate) return;
            sendSignal(uid, 'ice-candidate', {
                candidate: event.candidate.toJSON()
            });
        };

        pc.ontrack = event => {
            if (uid === String(MY_USER_ID) || leftUsers.has(uid)) return;

            ensureParticipantTileVisible(uid);

            const remoteStream = getOrCreateRemoteStream(uid);

            if (!remoteStream.getTracks().some(track => track.id === event.track.id)) {
                remoteStream.addTrack(event.track);
            }

            const applyTrack = () => {
                if (event.track.kind === 'video' && event.track.readyState === 'live') {
                    /*
                     * The live incoming video track is authoritative. This avoids
                     * camera-status / ontrack ordering races.
                     */
                    participantCameraStatus[uid] = true;
                }

                attachRemoteStream(uid);
            };

            event.track.onunmute = applyTrack;

            event.track.onended = () => {
                const stream = remoteStreams[uid];
                const current = stream?.getTracks?.().find(track => track.id === event.track.id);

                if (current) {
                    try { stream.removeTrack(current); } catch (error) {}
                }

                if (event.track.kind === 'video') {
                    participantCameraStatus[uid] = false;
                }

                attachRemoteStream(uid);
            };

            applyTrack();
        };

        const recover = reason => {
            if (leftUsers.has(uid)) return;

            const current = peers[uid];
            if (current !== pc) return;

            if (shouldInitiatePeer(uid) && pc.signalingState === 'stable') {
                queuePeerNegotiation(uid, {
                    reason,
                    iceRestart: true,
                    force: true,
                    delay: 120
                });
            } else {
                /*
                 * Wake the deterministic offerer without creating a second
                 * competing local offer.
                 */
                try { announceJoin(); } catch (error) {}
            }
        };

        pc.oniceconnectionstatechange = () => {
            const state = pc.iceConnectionState;

            if (state === 'connected' || state === 'completed') {
                if (offlineTimers[uid]) {
                    clearTimeout(offlineTimers[uid]);
                    delete offlineTimers[uid];
                }

                ensureParticipantTileVisible(uid);
                attachRemoteStream(uid);
                syncLocalTracksToPeer(uid);
                broadcastMyMicStatus();
                broadcastMyCameraStatus();
                return;
            }

            if (state === 'failed') {
                if (offlineTimers[uid]) {
                    clearTimeout(offlineTimers[uid]);
                    delete offlineTimers[uid];
                }

                /* Presence owns the tile; ICE recovery must not remove it. */
                setTimeout(() => recover('ice-failed'), 180);
                return;
            }

            if (state === 'disconnected') {
                if (offlineTimers[uid]) clearTimeout(offlineTimers[uid]);

                offlineTimers[uid] = setTimeout(() => {
                    delete offlineTimers[uid];

                    if (
                        pc.iceConnectionState === 'disconnected' ||
                        pc.iceConnectionState === 'failed'
                    ) {
                        recover('ice-disconnected');
                    }
                }, 1200);
            }
        };

        pc.onconnectionstatechange = () => {
            if (pc.connectionState === 'failed') {
                setTimeout(() => recover('connection-failed'), 180);
            }

            if (pc.connectionState === 'closed' && peers[uid] === pc) {
                delete peers[uid];
            }
        };

        /*
         * Only the deterministic initiator creates the first offer.
         */
        setTimeout(() => {
            if (
                shouldInitiatePeer(uid) &&
                pc.signalingState === 'stable' &&
                !pc.localDescription &&
                !pc.remoteDescription
            ) {
                queuePeerNegotiation(uid, {
                    reason: 'peer-created',
                    delay: 30
                });
            }
        }, 0);

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
                        // Do not create a remote video tile from historical DB state.
                        // A live user-joined / mic-status / camera-status / ontrack event
                        // will create it only when this participant is actually online.


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
</script>



















<script id="sm-final-sidebar-resize">
    (() => {
        const panel = document.getElementById('side-panel');
        if (!panel || panel.dataset.smResizeReady === '1') return;

        panel.dataset.smResizeReady = '1';

        const handle = document.createElement('div');
        handle.className = 'sm-panel-drag-handle';
        handle.title = 'Drag up/down to resize';
        panel.appendChild(handle);

        let dragging = false;
        let startY = 0;
        let startHeight = 0;

        const start = y => {
            if (window.innerWidth > 900) return;
            dragging = true;
            startY = y;
            startHeight = panel.getBoundingClientRect().height;
            document.body.style.userSelect = 'none';
        };

        const move = y => {
            if (!dragging || window.innerWidth > 900) return;

            const delta = startY - y; // pull up = taller, pull down = shorter
            const minHeight = Math.min(260, Math.max(180, window.innerHeight * .34));
            const maxHeight = Math.max(minHeight, window.innerHeight - 125);
            const next = Math.max(minHeight, Math.min(maxHeight, startHeight + delta));

            /* Keep the panel centered horizontally and anchored above controls. */
            panel.style.setProperty('top', 'auto', 'important');
            panel.style.setProperty('height', next + 'px', 'important');
            panel.style.setProperty('bottom', window.innerWidth <= 520 ? '66px' : '72px', 'important');
        };

        const end = () => {
            if (!dragging) return;
            dragging = false;
            document.body.style.userSelect = '';
        };

        handle.addEventListener('mousedown', e => start(e.clientY));
        document.addEventListener('mousemove', e => move(e.clientY));
        document.addEventListener('mouseup', end);

        handle.addEventListener('touchstart', e => {
            const t = e.touches[0];
            if (t) start(t.clientY);
        }, { passive: true });

        document.addEventListener('touchmove', e => {
            if (!dragging) return;
            const t = e.touches[0];
            if (t) move(t.clientY);
        }, { passive: true });

        document.addEventListener('touchend', end);
    })();
</script>

</body>
</html>
