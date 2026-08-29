<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">

    <title>{{ env('APP_NAME') }} — {{ $meeting->title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    @vite(['resources/css/meeting-room.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-0: #0a0e17;
            --surface: rgba(255, 255, 255, .045);
            --surface-2: rgba(255, 255, 255, .08);
            --surface-hover: rgba(255, 255, 255, .12);
            --border: rgba(255, 255, 255, .10);
            --border-soft: rgba(255, 255, 255, .06);
            --text: #eef1f8;
            --text-dim: #98a2b8;
            --text-faint: #626c82;
            --accent: #7c6cff;
            --accent-2: #22d3ee;
            --accent-grad: linear-gradient(135deg, #7c6cff, #22d3ee);
            --organizer-clr: #fbbf24;
            --participant-clr: #22d3ee;
            --danger: #f4415f;
            --danger-grad: linear-gradient(135deg, #f4415f, #fb7185);
            --success: #2dd4a7;
            --warn: #fbbf24;
            --radius: 16px;
            --radius-sm: 10px;
            --shadow-lg: 0 20px 50px -12px rgba(0, 0, 0, .55);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            background:
                radial-gradient(1200px 600px at 15% -10%, #1c2440 0%, transparent 60%),
                radial-gradient(1000px 700px at 110% 10%, #1a1030 0%, transparent 55%),
                var(--bg-0);
            color: var(--text);
            overflow: hidden;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-2); border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--surface-hover); }

        .app-shell { display: flex; flex-direction: column; height: 100vh; }

        /* ---------- HEADER ---------- */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border-soft);
            background: rgba(15, 19, 32, .55);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            z-index: 20;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }

        .meeting-badge {
            width: 38px; height: 38px; border-radius: 12px;
            background: var(--accent-grad);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #0a0e17; flex-shrink: 0;
            box-shadow: 0 6px 18px -6px rgba(124, 108, 255, .6);
        }

        .meeting-info { min-width: 0; }
        .meeting-title { font-weight: 700; font-size: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 46vw; }
        .meeting-sub { font-size: 12px; color: var(--text-dim); display: flex; align-items: center; gap: 8px; margin-top: 1px; }

        .role-badge {
            font-size: 9.5px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase;
            padding: 2px 8px; border-radius: 99px;
        }
        .role-badge.organizer { background: rgba(251, 191, 36, .16); color: var(--organizer-clr); }
        .role-badge.participant { background: rgba(34, 211, 238, .16); color: var(--participant-clr); }

        .live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 3px rgba(45, 212, 167, .18); animation: pulse-dot 2s infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.45} }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .pill-count {
            display: flex; align-items: center; gap: 6px;
            background: var(--surface); border: 1px solid var(--border-soft);
            padding: 6px 12px; border-radius: 99px; font-size: 12.5px; color: var(--text-dim); font-weight: 600;
        }

        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            border: 1px solid var(--border); background: var(--surface);
            color: var(--text); font-weight: 600; font-size: 13px;
            padding: 8px 14px; border-radius: var(--radius-sm);
            cursor: pointer; transition: all .15s ease; white-space: nowrap;
        }
        .btn:hover { background: var(--surface-hover); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn.danger { background: var(--danger-grad); border-color: transparent; color: #fff; }
        .btn.danger:hover { filter: brightness(1.08); box-shadow: 0 8px 24px -8px rgba(244, 65, 95, .55); }

        /* ---------- MAIN LAYOUT ---------- */
        .main { display: flex; flex: 1; min-height: 0; }
        .video-area { flex: 1; min-width: 0; position: relative; display: flex; flex-direction: column; padding: 18px; gap: 14px; }

        .video-grid {
            flex: 1; min-height: 0;
            display: grid;
            gap: 14px;
            place-content: center;
            place-items: stretch;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            align-content: center;
        }
        .video-grid[data-count="1"] { grid-template-columns: minmax(320px, 780px); justify-content: center; }
        .video-grid[data-count="2"] { grid-template-columns: repeat(2, minmax(300px, 560px)); justify-content: center; }
        .video-grid[data-count="3"],
        .video-grid[data-count="4"] { grid-template-columns: repeat(2, minmax(280px, 520px)); justify-content: center; }

        .video-tile {
            position: relative; border-radius: var(--radius);
            overflow: hidden; background: var(--surface);
            border: 1px solid var(--border-soft);
            aspect-ratio: 16/10;
            box-shadow: var(--shadow-lg);
            transition: border-color .2s ease;
        }
        .video-tile.speaking { border-color: var(--accent-2); box-shadow: 0 0 0 2px rgba(34, 211, 238, .35), var(--shadow-lg); }
        .video-tile.reconnecting::after {
            content: 'Reconnecting…'; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            background: rgba(10, 14, 23, .55); font-size: 12px; color: var(--warn); font-weight: 600; z-index: 4;
        }

        .video-tile video {
            width: 100%; height: 100%; object-fit: cover; background: #0d1220; display: block;
        }
        .video-tile video.mirror { transform: scaleX(-1); }
        .video-tile video.hidden-video { display: none; }

        .avatar-placeholder {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px;
            background:
                radial-gradient(circle at 50% 35%, rgba(124, 108, 255, .28), transparent 60%),
                linear-gradient(160deg, #1a2036, #0e1220);
        }
        .avatar-placeholder::before {
            content: ''; position: absolute; inset: 0;
            backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
            background: rgba(10, 13, 22, .25);
        }
        .avatar-circle {
            position: relative; z-index: 1;
            width: 74px; height: 74px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 800; color: #fff;
            background: var(--accent-grad);
            box-shadow: 0 10px 26px -8px rgba(124, 108, 255, .55);
        }
        .avatar-name { position: relative; z-index: 1; font-size: 12.5px; color: var(--text-dim); font-weight: 600; }

        .tile-info {
            position: absolute; left: 10px; bottom: 10px; z-index: 3;
            display: flex; align-items: center; gap: 6px;
            background: rgba(8, 10, 18, .55); backdrop-filter: blur(8px);
            padding: 5px 10px; border-radius: 99px; font-size: 12px; font-weight: 600;
        }
        .tile-info .mic-off-icon { color: var(--danger); font-size: 11px; }
        .tile-info .tag-organizer { color: var(--organizer-clr); font-size: 10px; }

        .waiting-dots span { animation: bounce-dot 1.4s infinite ease-in-out; display: inline-block; }
        .waiting-dots span:nth-child(2) { animation-delay: .2s; }
        .waiting-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes bounce-dot { 0%,80%,100%{opacity:.25} 40%{opacity:1} }

        /* ---------- CONTROL BAR ---------- */
        .control-bar {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 10px 14px; border-radius: 99px;
            background: rgba(15, 19, 32, .65); border: 1px solid var(--border-soft);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            align-self: center; box-shadow: var(--shadow-lg);
        }
        .ctrl-btn {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: var(--surface-2); border: 1px solid var(--border-soft); color: var(--text);
            cursor: pointer; transition: all .15s ease; font-size: 16px;
        }
        .ctrl-btn:hover { background: var(--surface-hover); }
        .ctrl-btn.off { background: var(--danger-grad); color: #fff; border-color: transparent; }
        .ctrl-btn.leave { background: var(--danger-grad); color: #fff; border-color: transparent; width: auto; padding: 0 22px; border-radius: 24px; gap: 8px; font-weight: 700; font-size: 13px; }
        .ctrl-btn.active-panel { background: var(--accent); color: #fff; }
        .ctrl-divider { width: 1px; height: 26px; background: var(--border); margin: 0 2px; }
        .ctrl-badge {
            position: absolute; top: -3px; right: -3px; background: var(--accent); color: #fff;
            font-size: 9.5px; font-weight: 800; min-width: 16px; height: 16px; border-radius: 99px;
            display: flex; align-items: center; justify-content: center; padding: 0 3px;
        }
        .ctrl-wrap { position: relative; }

        /* ---------- SIDE PANEL ---------- */
        #side-panel {
            width: 340px; flex-shrink: 0; border-left: 1px solid var(--border-soft);
            background: rgba(13, 17, 28, .6); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
            display: flex; flex-direction: column; transition: margin-right .25s ease; min-height: 0;
        }
        #side-panel.collapsed { margin-right: -340px; }

        .panel-tabs { display: flex; border-bottom: 1px solid var(--border-soft); padding: 10px 12px 0; gap: 4px; }
        .panel-tab {
            flex: 1; text-align: center; padding: 10px 6px; font-size: 12.5px; font-weight: 700;
            color: var(--text-faint); cursor: pointer; border-bottom: 2px solid transparent; border-radius: 8px 8px 0 0;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .panel-tab.active { color: var(--text); border-color: var(--accent); background: var(--surface); }
        .panel-tab .count { background: var(--surface-2); border-radius: 99px; padding: 0 6px; font-size: 10.5px; }

        .panel-body { flex: 1; min-height: 0; display: flex; flex-direction: column; }
        .panel-view { flex: 1; min-height: 0; display: none; flex-direction: column; }
        .panel-view.active { display: flex; }

        /* Chat */
        .chat-list { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 10px; }
        .chat-msg { max-width: 84%; }
        .chat-msg .meta { font-size: 11px; color: var(--text-faint); margin-bottom: 3px; display: flex; gap: 6px; align-items: center; }
        .chat-msg .bubble { padding: 9px 13px; border-radius: 14px; font-size: 13.5px; line-height: 1.4; background: var(--surface-2); border: 1px solid var(--border-soft); word-wrap: break-word; }
        .chat-msg.own { align-self: flex-end; }
        .chat-msg.own .bubble { background: var(--accent-grad); color: #fff; border: none; }
        .chat-msg.own .meta { justify-content: flex-end; }
        .chat-empty, .people-empty, .transcript-empty { margin: auto; text-align: center; color: var(--text-faint); font-size: 13px; padding: 24px; }
        .chat-empty i, .transcript-empty i { font-size: 22px; opacity: .5; display: block; margin-bottom: 8px; }

        .chat-input-row { display: flex; gap: 8px; padding: 12px; border-top: 1px solid var(--border-soft); }
        .chat-input-row input {
            flex: 1; background: var(--surface); border: 1px solid var(--border); color: var(--text);
            padding: 10px 14px; border-radius: 99px; font-size: 13px; outline: none;
        }
        .chat-input-row input:focus { border-color: var(--accent); }
        .chat-send-btn {
            width: 40px; height: 40px; border-radius: 50%; background: var(--accent-grad); border: none; color: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .chat-send-btn:disabled { opacity: .4; cursor: not-allowed; }

        /* People */
        .people-list { flex: 1; overflow-y: auto; padding: 10px; display: flex; flex-direction: column; gap: 6px; }
        .person-row {
            display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 12px;
            border: 1px solid var(--border-soft); background: var(--surface); transition: opacity .2s ease;
        }
        .person-row.offline { opacity: .48; }
        .person-avatar {
            position: relative; width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff;
            background: var(--accent-grad); overflow: hidden;
        }
        .person-avatar::after { content: ''; position: absolute; inset: 0; backdrop-filter: blur(6px); }
        .status-dot { position: absolute; bottom: -1px; right: -1px; width: 10px; height: 10px; border-radius: 50%; border: 2px solid #0d1220; background: var(--text-faint); z-index: 2; }
        .status-dot.online { background: var(--success); }
        .person-name { font-size: 13px; font-weight: 600; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .person-status { font-size: 10.5px; color: var(--text-faint); font-weight: 600; }
        .person-status.online { color: var(--success); }

        /* Transcript */
        .transcript-list { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 12px; }
        .transcript-item .meta { font-size: 11px; color: var(--text-faint); margin-bottom: 3px; }
        .transcript-item .meta b { color: var(--accent-2); }
        .transcript-item .txt { font-size: 13.5px; line-height: 1.5; color: var(--text); }
        .transcript-status { padding: 8px 14px; font-size: 11px; color: var(--text-faint); border-top: 1px solid var(--border-soft); display: flex; align-items: center; gap: 6px; }
        .rec-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--danger); animation: pulse-dot 1.4s infinite; }

        /* ---------- MODAL ---------- */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(6, 8, 14, .72); backdrop-filter: blur(6px);
            display: none; align-items: center; justify-content: center; z-index: 100;
        }
        .modal-overlay.active { display: flex; }
        .modal-card {
            background: linear-gradient(160deg, #171d30, #0e1220);
            border: 1px solid var(--border); border-radius: var(--radius); padding: 28px;
            width: 340px; text-align: center; box-shadow: var(--shadow-lg);
        }
        .modal-card i { font-size: 32px; color: var(--danger); margin-bottom: 12px; }
        .modal-card h3 { margin: 0 0 6px; font-size: 16px; }
        .modal-card p { margin: 0 0 18px; font-size: 13px; color: var(--text-dim); }

        @media (max-width: 900px) {
            #side-panel { position: fixed; right: 0; top: 0; bottom: 0; z-index: 40; width: 88vw; max-width: 360px; }
            #side-panel.collapsed { margin-right: -88vw; }
            .meeting-title { max-width: 42vw; }
        }
    </style>
</head>

@php
    $organizer = $meeting->organizer;

    $organizerInitials = strtoupper(
        substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1)
    );
@endphp

<body>
<div class="app-shell">

    <header class="topbar">
        <div class="topbar-left">
            <div class="meeting-badge"><i class="fa-solid fa-video"></i></div>
            <div class="meeting-info">
                <div class="meeting-title" title="{{ $meeting->title }}">{{ $meeting->title }}</div>
                <div class="meeting-sub">
                    <span class="live-dot"></span> Live
                    <span class="role-badge participant">Participant</span>
                </div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="pill-count"><i class="fa-solid fa-users"></i> <span id="online-count">1</span> online</div>
            <button class="btn danger" id="leave-meeting-top-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Leave
            </button>
        </div>
    </header>

    <div class="main">
        <div class="video-area">
            <div class="video-grid" id="video-grid" data-count="1"></div>

            <div class="control-bar">
                <div class="ctrl-wrap">
                    <button class="ctrl-btn" id="mic-btn" title="Toggle microphone"><i class="fa-solid fa-microphone"></i></button>
                </div>
                <div class="ctrl-wrap">
                    <button class="ctrl-btn" id="camera-btn" title="Toggle camera"><i class="fa-solid fa-video"></i></button>
                </div>
                <div class="ctrl-divider"></div>
                <div class="ctrl-wrap">
                    <button class="ctrl-btn" id="chat-toggle-btn" title="Chat"><i class="fa-solid fa-comment"></i>
                        <span class="ctrl-badge" id="chat-badge" style="display:none;">0</span>
                    </button>
                </div>
                <div class="ctrl-wrap">
                    <button class="ctrl-btn" id="people-toggle-btn" title="People"><i class="fa-solid fa-users"></i></button>
                </div>
                <div class="ctrl-wrap">
                    <button class="ctrl-btn" id="transcript-toggle-btn" title="Transcript"><i class="fa-solid fa-closed-captioning"></i></button>
                </div>
                <div class="ctrl-divider"></div>
                <button class="ctrl-btn leave" id="leave-btn"><i class="fa-solid fa-phone-slash"></i> Leave</button>
            </div>
        </div>

        <aside id="side-panel">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="people">People <span class="count" id="people-count-badge">1</span></div>
                <div class="panel-tab" data-tab="chat">Chat <span class="count" id="chat-count-badge">0</span></div>
                <div class="panel-tab" data-tab="transcript">Captions</div>
            </div>
            <div class="panel-body">
                <div class="panel-view" data-view="people">
                    <div class="people-list" id="people-list"></div>
                </div>

                <div class="panel-view" data-view="chat">
                    <div class="chat-list" id="chat-list">
                        <div class="chat-empty"><i class="fa-regular fa-comments"></i>No messages yet. Say hello 👋</div>
                    </div>
                    <div class="chat-input-row">
                        <input type="text" id="chat-input" placeholder="Type a message…" maxlength="500" autocomplete="off">
                        <button class="chat-send-btn" id="chat-send-btn"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </div>

                <div class="panel-view" data-view="transcript">
                    <div class="transcript-list" id="transcript-list">
                        <div class="transcript-empty"><i class="fa-solid fa-closed-captioning"></i>Live captions will appear here as people speak.</div>
                    </div>
                    <div class="transcript-status">
                        <span class="rec-dot" id="rec-dot" style="display:none;"></span>
                        <span id="rec-status-text">Captions off</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<div class="modal-overlay" id="cancelled-modal">
    <div class="modal-card">
        <i class="fa-solid fa-circle-exclamation"></i>
        <h3 id="cancelled-title">Meeting ended</h3>
        <p id="cancelled-text">The organizer has ended this meeting. Redirecting…</p>
    </div>
</div>

<script>
    /* ============================================================
       SMARTMEET — PARTICIPANT ROOM
       Mirrors the organizer room's WebRTC mesh + Reverb signaling.
    ============================================================ */
    (function () {
        'use strict';

        // ---------- Server-injected constants ----------
        const MEETING_ID = "{{ $meeting->id }}";
        const MY_USER_ID = "{{ auth()->id() }}";
        const MY_NAME = @json(auth()->user()->name);
        const MY_INITIALS = @json((function () {
            $parts = preg_split('/\s+/', trim(auth()->user()->name)) ?: [];
            $f = $parts[0][0] ?? '';
            $l = count($parts) > 1 ? $parts[count($parts) - 1][0] : '';
            return strtoupper($f . $l) ?: '?';
        })());
        const IS_ORGANIZER = false;
        const ORGANIZER_ID = "{{ $organizer->id }}";
        const ORGANIZER_NAME = @json($organizer->name);
        const ORGANIZER_INITIALS = @json($organizerInitials);
        const ORGANIZER_JOINED = @json($organizerJoined ?? false);

        const SIGNAL_URL = @json(route('participant.meetings.signal', $meeting));
        const TRANSCRIPT_URL = @json(route('participant.meetings.transcript', $meeting));
        const MARK_LEFT_URL = @json(route('participant.meetings.markLeft', $meeting));
        const LEAVE_URL = @json(route('participant.meetings.index'));
        const CSRF = @json(csrf_token());

        const ALL_PARTICIPANTS = @json($allParticipants);
        const ALREADY_JOINED = @json($alreadyJoined);

        const ICE_SERVERS = [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ];

        // ---------- State ----------
        const people = {};
        const peers = {};
        const pendingCandidates = {};
        let localStream = null;
        let isMicOn = true;
        let isCameraOn = true;
        let chatUnread = 0;
        let recognizing = false;
        let recognitionRestartTimer = null;
        let meetingEnded = false;

        people[MY_USER_ID] = { name: MY_NAME, initials: MY_INITIALS, isOrganizer: false, joined: true };
        people[ORGANIZER_ID] = { name: ORGANIZER_NAME, initials: ORGANIZER_INITIALS, isOrganizer: true, joined: !!ORGANIZER_JOINED };
        ALL_PARTICIPANTS.forEach(p => {
            people[String(p.userId)] = { name: p.name, initials: p.initials, isOrganizer: false, joined: !!p.hasJoined };
        });

        // ---------- DOM refs ----------
        const $ = (sel, root = document) => root.querySelector(sel);
        const grid = $('#video-grid');
        const peopleList = $('#people-list');
        const chatList = $('#chat-list');
        const transcriptList = $('#transcript-list');
        const onlineCountEl = $('#online-count');
        const peopleCountBadge = $('#people-count-badge');
        const chatCountBadge = $('#chat-count-badge');
        const chatBadge = $('#chat-badge');

        function initials(name) {
            const parts = (name || '').trim().split(/\s+/);
            const f = parts[0]?.[0] || '';
            const l = parts.length > 1 ? parts[parts.length - 1][0] : '';
            return (f + l).toUpperCase() || '?';
        }

        // ============================================================
        // MEDIA
        // ============================================================
        async function initMedia() {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
                });
            } catch (e) {
                try {
                    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    isCameraOn = false;
                } catch (e2) {
                    localStream = new MediaStream();
                    isMicOn = false;
                    isCameraOn = false;
                }
            }
            renderLocalTile();
            updateControlIcons();
        }

        // ============================================================
        // VIDEO TILES — only rendered for users who have actually joined
        // ============================================================
        function ensureTile(userId) {
            let tile = document.getElementById('tile-' + userId);
            if (tile) return tile;

            const p = people[userId] || { name: 'User', initials: '?', isOrganizer: false };
            tile = document.createElement('div');
            tile.className = 'video-tile';
            tile.id = 'tile-' + userId;
            tile.innerHTML = `
                <video id="video-${userId}" autoplay playsinline ${userId === MY_USER_ID ? 'muted' : ''}></video>
                <div class="avatar-placeholder" id="avatar-${userId}">
                    <div class="avatar-circle">${p.initials}</div>
                    <div class="avatar-name">${p.name}</div>
                </div>
                <div class="tile-info">
                    <span>${userId === MY_USER_ID ? 'You' : p.name}</span>
                    ${p.isOrganizer ? '<span class="tag-organizer">★</span>' : ''}
                    <i class="fa-solid fa-microphone-slash mic-off-icon" id="micoff-${userId}" style="display:none;"></i>
                </div>
            `;
            grid.appendChild(tile);
            updateGridCount();
            return tile;
        }

        function removeTile(userId) {
            const tile = document.getElementById('tile-' + userId);
            if (tile) tile.remove();
            updateGridCount();
        }

        function updateGridCount() {
            grid.dataset.count = String(grid.children.length);
        }

        function setTileStream(userId, stream, mirrored = false) {
            ensureTile(userId);
            const video = document.getElementById('video-' + userId);
            const avatar = document.getElementById('avatar-' + userId);
            if (video) {
                video.srcObject = stream;
                video.classList.toggle('mirror', mirrored);
            }
            const hasVideoTrack = stream && stream.getVideoTracks().some(t => t.enabled);
            if (video) video.classList.toggle('hidden-video', !hasVideoTrack);
            if (avatar) avatar.style.display = hasVideoTrack ? 'none' : 'flex';
        }

        function renderLocalTile() {
            ensureTile(MY_USER_ID);
            setTileStream(MY_USER_ID, localStream, true);
        }

        function setMicIndicator(userId, muted) {
            const el = document.getElementById('micoff-' + userId);
            if (el) el.style.display = muted ? 'inline-block' : 'none';
        }

        // ============================================================
        // PEOPLE PANEL
        // ============================================================
        function renderPeopleList() {
            peopleList.innerHTML = '';
            const ids = Object.keys(people).sort((a, b) => {
                if (a === MY_USER_ID) return -1;
                if (b === MY_USER_ID) return 1;
                if (people[a].isOrganizer) return -1;
                if (people[b].isOrganizer) return 1;
                return people[b].joined - people[a].joined;
            });

            let onlineCount = 0;
            ids.forEach(id => {
                const p = people[id];
                if (p.joined) onlineCount++;
                const row = document.createElement('div');
                row.className = 'person-row ' + (p.joined ? '' : 'offline');
                row.id = 'person-row-' + id;
                row.innerHTML = `
                    <div class="person-avatar">${p.initials}<span class="status-dot ${p.joined ? 'online' : ''}"></span></div>
                    <div class="person-name">${id === MY_USER_ID ? p.name + ' (You)' : p.name}</div>
                    <div>
                        ${p.isOrganizer ? '<span class="role-badge organizer">Organizer</span>' : `<span class="person-status ${p.joined ? 'online' : ''}">${p.joined ? 'In meeting' : 'Not joined'}</span>`}
                    </div>
                `;
                peopleList.appendChild(row);
            });

            onlineCountEl.textContent = onlineCount;
            peopleCountBadge.textContent = onlineCount;
        }

        function setPersonJoined(userId, joined, meta = null) {
            if (!people[userId]) people[userId] = meta || { name: 'User', initials: '?', isOrganizer: false, joined: false };
            people[userId].joined = joined;
            if (meta) Object.assign(people[userId], meta);
            renderPeopleList();
        }

        // ============================================================
        // WEBRTC — deterministic offerer to avoid glare: the smaller userId always offers
        // ============================================================
        function amOfferer(peerId) {
            return String(MY_USER_ID) < String(peerId);
        }

        function getPeer(peerId) {
            if (peers[peerId]) return peers[peerId];

            const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });
            peers[peerId] = pc;
            pendingCandidates[peerId] = [];

            if (localStream) {
                localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
            }

            pc.ontrack = (event) => {
                setTileStream(peerId, event.streams[0], false);
            };

            pc.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal(peerId, 'ice-candidate', { candidate: event.candidate.toJSON() });
                }
            };

            pc.onnegotiationneeded = async () => {
                if (!amOfferer(peerId)) return;
                try {
                    const offer = await pc.createOffer();
                    await pc.setLocalDescription(offer);
                    sendSignal(peerId, 'offer', { sdp: pc.localDescription.sdp, sdpType: pc.localDescription.type });
                } catch (e) { /* ignore, will retry on next negotiation */ }
            };

            pc.onconnectionstatechange = () => {
                const tile = document.getElementById('tile-' + peerId);
                if (!tile) return;
                if (pc.connectionState === 'failed' || pc.connectionState === 'disconnected') {
                    tile.classList.add('reconnecting');
                    if (pc.connectionState === 'failed' && amOfferer(peerId)) {
                        restartIce(peerId);
                    }
                } else if (pc.connectionState === 'connected') {
                    tile.classList.remove('reconnecting');
                }
            };

            return pc;
        }

        async function restartIce(peerId) {
            const pc = peers[peerId];
            if (!pc) return;
            try {
                const offer = await pc.createOffer({ iceRestart: true });
                await pc.setLocalDescription(offer);
                sendSignal(peerId, 'offer', { sdp: pc.localDescription.sdp, sdpType: pc.localDescription.type });
            } catch (e) { /* noop */ }
        }

        function connectTo(peerId) {
            if (peerId === MY_USER_ID) return;
            ensureTile(peerId);
            getPeer(peerId);
        }

        function closePeer(peerId) {
            const pc = peers[peerId];
            if (pc) {
                pc.ontrack = null;
                pc.onicecandidate = null;
                pc.onnegotiationneeded = null;
                pc.close();
            }
            delete peers[peerId];
            delete pendingCandidates[peerId];
            removeTile(peerId);
        }

        // ============================================================
        // SIGNALING (Laravel Echo / Reverb)
        // ============================================================
        function sendSignal(toUserId, type, data) {
            return fetch(SIGNAL_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ to_user_id: toUserId === 'all' ? null : toUserId, type, data })
            }).catch(() => {});
        }

        async function handleSignal(payload) {
            const { type, data, fromUserId, toUserId } = payload;
            if (!type || meetingEnded) return;
            if (type !== 'meeting-cancelled' && (!fromUserId || String(fromUserId) === String(MY_USER_ID))) return;
            // Public channel delivers every signal to every client — direct
            // signals (offer/answer/ice-candidate) carry a specific toUserId
            // and must be ignored by anyone else, or peers get cross-wired.
            if (toUserId && toUserId !== 'all' && String(toUserId) !== String(MY_USER_ID)) return;

            switch (type) {
                case 'user-joined': {
                    const uid = String(data.userId);
                    setPersonJoined(uid, true, { name: data.name, initials: data.initials || initials(data.name), isOrganizer: !!data.isOrganizer || uid === String(ORGANIZER_ID) });
                    connectTo(uid);
                    sendSignal(uid, 'mic-status', { userId: MY_USER_ID, muted: !isMicOn });
                    sendSignal(uid, 'camera-status', { userId: MY_USER_ID, cameraOn: isCameraOn });
                    break;
                }
                case 'user-left': {
                    const uid = String(data.userId);
                    setPersonJoined(uid, false);
                    closePeer(uid);
                    break;
                }
                case 'offer': {
                    const uid = String(fromUserId);
                    const pc = getPeer(uid);
                    await pc.setRemoteDescription({ type: data.sdpType || 'offer', sdp: data.sdp });
                    await flushCandidates(uid);
                    const answer = await pc.createAnswer();
                    await pc.setLocalDescription(answer);
                    sendSignal(uid, 'answer', { sdp: pc.localDescription.sdp, sdpType: pc.localDescription.type });
                    break;
                }
                case 'answer': {
                    const uid = String(fromUserId);
                    const pc = peers[uid];
                    if (pc && pc.signalingState !== 'stable') {
                        await pc.setRemoteDescription({ type: data.sdpType || 'answer', sdp: data.sdp });
                        await flushCandidates(uid);
                    }
                    break;
                }
                case 'ice-candidate': {
                    const uid = String(fromUserId);
                    const pc = peers[uid];
                    if (pc && pc.remoteDescription && pc.remoteDescription.type) {
                        try { await pc.addIceCandidate(data.candidate); } catch (e) {}
                    } else {
                        pendingCandidates[uid] = pendingCandidates[uid] || [];
                        pendingCandidates[uid].push(data.candidate);
                    }
                    break;
                }
                case 'chat':
                    appendChatMessage({ userId: fromUserId, name: data.name, text: data.text }, false);
                    break;
                case 'mic-status':
                    setMicIndicator(String(data.userId), !!data.muted);
                    break;
                case 'camera-status':
                    break;
                case 'meeting-cancelled':
                    endMeetingLocally();
                    break;
            }
        }

        function endMeetingLocally() {
            if (meetingEnded) return;
            meetingEnded = true;
            $('#cancelled-modal').classList.add('active');
            teardown();
            setTimeout(() => { window.location.href = LEAVE_URL; }, 1800);
        }

        async function flushCandidates(uid) {
            const queued = pendingCandidates[uid] || [];
            pendingCandidates[uid] = [];
            const pc = peers[uid];
            if (!pc) return;
            for (const c of queued) {
                try { await pc.addIceCandidate(c); } catch (e) {}
            }
        }

        // ============================================================
        // CHAT
        // ============================================================
        function appendChatMessage({ userId, name, text }, own) {
            const empty = chatList.querySelector('.chat-empty');
            if (empty) empty.remove();

            const wrap = document.createElement('div');
            wrap.className = 'chat-msg' + (own ? ' own' : '');
            wrap.innerHTML = `
                <div class="meta">${own ? 'You' : escapeHtml(name)}</div>
                <div class="bubble">${escapeHtml(text)}</div>
            `;
            chatList.appendChild(wrap);
            chatList.scrollTop = chatList.scrollHeight;

            if (!own && !isPanelOpen('chat')) {
                chatUnread++;
                chatBadge.style.display = 'flex';
                chatBadge.textContent = chatUnread;
                chatCountBadge.textContent = chatUnread;
            }
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        function sendChat() {
            const input = $('#chat-input');
            const text = input.value.trim();
            if (!text) return;
            appendChatMessage({ userId: MY_USER_ID, name: MY_NAME, text }, true);
            sendSignal('all', 'chat', { text, name: MY_NAME, initials: MY_INITIALS });
            input.value = '';
        }

        $('#chat-send-btn').addEventListener('click', sendChat);
        $('#chat-input').addEventListener('keydown', (e) => { if (e.key === 'Enter') sendChat(); });

        // ============================================================
        // TRANSCRIPT / CAPTIONS (Web Speech API)
        // ============================================================
        function appendTranscript({ userName, text, spokenAt }) {
            const empty = transcriptList.querySelector('.transcript-empty');
            if (empty) empty.remove();
            const item = document.createElement('div');
            item.className = 'transcript-item';
            item.innerHTML = `<div class="meta"><b>${escapeHtml(userName)}</b> · ${escapeHtml(spokenAt || '')}</div><div class="txt">${escapeHtml(text)}</div>`;
            transcriptList.appendChild(item);
            transcriptList.scrollTop = transcriptList.scrollHeight;
        }

        async function saveTranscript(text) {
            try {
                const res = await fetch(TRANSCRIPT_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ text })
                });
                if (res.ok) {
                    appendTranscript({ userName: 'You', text, spokenAt: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
                }
            } catch (e) {}
        }

        function startTranscription() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const statusText = $('#rec-status-text');
            const recDot = $('#rec-dot');

            if (!SpeechRecognition) {
                statusText.textContent = 'Captions not supported in this browser';
                return;
            }
            if (!isMicOn) {
                statusText.textContent = 'Captions off (mic muted)';
                recDot.style.display = 'none';
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            recognition.onresult = (event) => {
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    if (event.results[i].isFinal) {
                        const text = event.results[i][0].transcript.trim();
                        if (text) saveTranscript(text);
                    }
                }
            };

            recognition.onerror = (e) => {
                if (e.error === 'not-allowed' || e.error === 'service-not-allowed') {
                    statusText.textContent = 'Microphone permission needed for captions';
                    recDot.style.display = 'none';
                    recognizing = false;
                }
            };

            recognition.onend = () => {
                recognizing = false;
                if (isMicOn && !meetingEnded) {
                    clearTimeout(recognitionRestartTimer);
                    recognitionRestartTimer = setTimeout(() => startTranscription(), 400);
                } else {
                    statusText.textContent = 'Captions off (mic muted)';
                    recDot.style.display = 'none';
                }
            };

            try {
                recognition.start();
                recognizing = true;
                statusText.textContent = 'Live captions on';
                recDot.style.display = 'inline-block';
                window.__smartmeetRecognition = recognition;
            } catch (e) { /* already started */ }
        }

        function stopTranscription() {
            if (window.__smartmeetRecognition) {
                try { window.__smartmeetRecognition.stop(); } catch (e) {}
            }
            clearTimeout(recognitionRestartTimer);
            $('#rec-status-text').textContent = 'Captions off (mic muted)';
            $('#rec-dot').style.display = 'none';
        }

        window.Echo
            .channel('meeting.' + MEETING_ID)
            .listen('.transcript', (data) => {
                if (!data || String(data.userId) === String(MY_USER_ID)) return;
                appendTranscript({ userName: data.userName || 'User', text: data.text || '', spokenAt: data.spokenAt || '' });
            })
            .listen('.signal', handleSignal);

        // ============================================================
        // CONTROLS
        // ============================================================
        function updateControlIcons() {
            const micBtn = $('#mic-btn');
            const camBtn = $('#camera-btn');
            micBtn.classList.toggle('off', !isMicOn);
            micBtn.innerHTML = `<i class="fa-solid ${isMicOn ? 'fa-microphone' : 'fa-microphone-slash'}"></i>`;
            camBtn.classList.toggle('off', !isCameraOn);
            camBtn.innerHTML = `<i class="fa-solid ${isCameraOn ? 'fa-video' : 'fa-video-slash'}"></i>`;
        }

        $('#mic-btn').addEventListener('click', () => {
            isMicOn = !isMicOn;
            localStream.getAudioTracks().forEach(t => t.enabled = isMicOn);
            updateControlIcons();
            setMicIndicator(MY_USER_ID, !isMicOn);
            sendSignal('all', 'mic-status', { userId: MY_USER_ID, muted: !isMicOn });
            isMicOn ? startTranscription() : stopTranscription();
        });

        $('#camera-btn').addEventListener('click', () => {
            isCameraOn = !isCameraOn;
            localStream.getVideoTracks().forEach(t => t.enabled = isCameraOn);
            updateControlIcons();
            setTileStream(MY_USER_ID, localStream, true);
            sendSignal('all', 'camera-status', { userId: MY_USER_ID, cameraOn: isCameraOn });
        });

        function isPanelOpen(tab) {
            return !$('#side-panel').classList.contains('collapsed') && $('.panel-tab.active')?.dataset.tab === tab;
        }

        function openPanelTab(tab) {
            const panel = $('#side-panel');
            const alreadyOnTab = !panel.classList.contains('collapsed') && $('.panel-tab.active')?.dataset.tab === tab;
            if (alreadyOnTab) {
                panel.classList.add('collapsed');
                return;
            }
            panel.classList.remove('collapsed');
            document.querySelectorAll('.panel-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
            document.querySelectorAll('.panel-view').forEach(v => v.classList.toggle('active', v.dataset.view === tab));
            if (tab === 'chat') {
                chatUnread = 0;
                chatBadge.style.display = 'none';
                chatCountBadge.textContent = '0';
            }
        }

        document.querySelectorAll('.panel-tab').forEach(tab => {
            tab.addEventListener('click', () => openPanelTab(tab.dataset.tab));
        });
        $('#chat-toggle-btn').addEventListener('click', () => openPanelTab('chat'));
        $('#people-toggle-btn').addEventListener('click', () => openPanelTab('people'));
        $('#transcript-toggle-btn').addEventListener('click', () => openPanelTab('transcript'));

        // ============================================================
        // LEAVE
        // ============================================================
        function teardown() {
            Object.keys(peers).forEach(closePeer);
            if (localStream) localStream.getTracks().forEach(t => t.stop());
            stopTranscription();
        }

        async function leaveMeeting() {
            if (meetingEnded) return;
            await sendSignal('all', 'user-left', { userId: MY_USER_ID, name: MY_NAME });
            await fetch(MARK_LEFT_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }).catch(() => {});
            teardown();
            window.location.href = LEAVE_URL;
        }

        $('#leave-btn').addEventListener('click', leaveMeeting);
        $('#leave-meeting-top-btn').addEventListener('click', leaveMeeting);

        window.addEventListener('beforeunload', () => {
            navigator.sendBeacon?.(MARK_LEFT_URL, new Blob([JSON.stringify({})], { type: 'application/json' }));
            teardown();
        });

        // ============================================================
        // INIT
        // ============================================================
        (async function init() {
            await initMedia();
            renderPeopleList();
            if (ORGANIZER_JOINED) connectTo(String(ORGANIZER_ID));
            ALREADY_JOINED.forEach(p => connectTo(String(p.userId)));
            updateGridCount();
            startTranscription();
        })();
    })();
</script>
</body>
</html>
