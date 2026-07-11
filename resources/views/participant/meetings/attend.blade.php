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
        .video-area { flex: 1; min-width: 0; }
        #side-panel { flex-shrink: 0; }
        .role-badge {
            font-size: 9px; font-weight: 600; letter-spacing: .3px; text-transform: uppercase;
            padding: 1px 6px; border-radius: 99px; margin-left: 6px; vertical-align: middle;
        }
        .role-badge.organizer { background: rgba(251,191,36,0.18); color: #fbbf24; }
        .role-badge.participant { background: rgba(59,130,246,0.18); color: #60a5fa; }
        .participant-online { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); opacity: 1; }
        .participant-offline { background: var(--surface2); border: 1px solid var(--border); opacity: 0.5; }
        @media (max-width: 900px) {
            .header { flex-wrap: wrap; gap: 8px; padding: 8px 12px; }
            .header-center { order: 3; width: 100%; justify-content: center; }
            .main { flex-direction: column; }
            #side-panel {
                position: fixed; inset: 0; top: auto; bottom: 86px; height: 60vh; width: 100%;
                z-index: 50; border-radius: 16px 16px 0 0; box-shadow: 0 -4px 24px rgba(0,0,0,0.4);
            }
            .video-grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)) !important; }
            .controls { flex-wrap: wrap; justify-content: center; gap: 10px; padding: 10px; }
        }
        @media (max-width: 480px) {
            .meeting-title { font-size: 14px; }
            .video-grid { grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)) !important; }
        }
    </style>
</head>
@php
    $organizer   = $meeting->organizer;
    $orgInitials = strtoupper(substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1));
    $colors      = ['#3b82f6,#06b6d4', '#8b5cf6,#ec4899', '#22c55e,#06b6d4', '#f59e0b,#ef4444', '#64748b,#334155', '#ec4899,#f59e0b'];
    $userInitials = strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1));
@endphp
<body>
{{-- HEADER --}}
<div class="header">
    <div class="header-left">
        <div style="display:flex;align-items:center;gap:10px;padding-right:16px;border-right:1px solid rgba(255,255,255,0.08);">
            <img src="{{ asset('images/s-logo.png') }}" style="width:32px;height:32px;object-fit:contain;">
            <div>
                <div style="font-weight:700;font-size:14px;color:white;">SmartMeet</div>
                <div style="font-size:10px;color:#64748b;">Meeting Suite</div>
            </div>
        </div>
        <div class="live-badge">
            <div class="live-dot"></div>
            LIVE
        </div>
        <div>
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
            <i class="fa fa-phone-slash"></i> Leave
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
                    <div class="avatar-circle" style="background:linear-gradient(135deg,{{ $colors[1] }});">
                        {{ $userInitials }}
                    </div>
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
                    🌐 English
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
        <div class="ctrl-icon off" id="ctrl-mic">
            <i class="fa fa-microphone-slash"></i>
        </div>
        <span class="ctrl-label">Mic</span>
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
<script>
    // ═══════════════════════════════════════════════════════════
    // PARTICIPANT — FULLY FIXED VERSION (v2)
    // Fix 1: handleSignal skips events that originated from
    //      yourself for chat / mic-status / user-joined.
    // Fix 2 (NEW): broadcastMyMicStatus() is called the moment a
    //      peer connection actually becomes connected, so every
    //      newly-visible tile (organizer or other participants)
    //      gets your REAL mic state immediately instead of
    //      defaulting to "muted" until a mic-status signal happens
    //      to arrive later. This fixes the bug where other users
    //      always saw your tile (and you always saw the organizer's
    //      tile) as muted regardless of actual state.
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
    const ORGANIZER_JOINED = @json($organizerJoined);
    // ── KNOWN PARTICIPANTS ──
    const knownParticipants = {};
    knownParticipants[ORGANIZER_ID] = { name: ORGANIZER_NAME, initials: ORGANIZER_INITIALS, isOrganizer: true, hasJoined: ORGANIZER_JOINED };
    ALL_PARTICIPANTS.forEach(p => {
        knownParticipants[p.userId] = { name: p.name, initials: p.initials, isOrganizer: false, hasJoined: p.hasJoined };
    });
    // ── ONLINE USERS ──
    const onlineUsers = new Set([String(MY_USER_ID)]);
    const departedAnnounced = new Set();
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
    let isMicOn = false;
    let recognition = null;
    let currentLang = 'en-US';
    let recognitionRunning = false;
    const participantMicStatus = {};
    const offlineTimers = {};
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
    function isPolite(otherUserId) { return String(MY_USER_ID) < String(otherUserId); }
    // ✅ NEW: broadcast our real current mic state to everyone.
    function broadcastMyMicStatus() {
        sendSignal('all', 'mic-status', { userId: MY_USER_ID, muted: !isMicOn });
    }
    // ── START ──
    window.addEventListener('load', async () => {
        listenForSignals();
        await startAudio();
        announceJoin();
        renderAllParticipants();
    });
    function renderAllParticipants() {
        ensurePanelRow(ORGANIZER_ID, ORGANIZER_NAME, ORGANIZER_INITIALS, true);
        if (ORGANIZER_JOINED) {
            addParticipantTile(ORGANIZER_ID, ORGANIZER_NAME, ORGANIZER_INITIALS, true);
            markOnline(ORGANIZER_ID);
            createPeerConnection(ORGANIZER_ID);
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
    // ── MIC ACCESS ──
    async function startAudio() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                video: false
            });
            localStream.getAudioTracks().forEach(t => t.enabled = false);
            isMicOn = false;
            startTranscript();
        } catch (err) {
            console.error('Mic error:', err);
            if (err.name === 'NotFoundError') alert('Microphone not found.');
            else if (err.name === 'NotAllowedError') alert('Microphone permission denied.');
            else alert('Microphone error: ' + err.message);
        }
    }
    // ── LISTEN FOR SIGNALS ──
    function listenForSignals() {
        if (typeof window.Echo === 'undefined') { console.error('Echo not initialized'); return; }
        const channel = window.Echo.channel('meeting.' + MEETING_ID);
        channel.listen('.signal', handleSignal);
        channel.listen('.transcript', handleTranscript);
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
        if (localStream) localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
        pc.onnegotiationneeded = async () => {
            try {
                makingOffer[userId] = true;
                const offer = await pc.createOffer();
                if (pc.signalingState !== 'stable') return;
                await pc.setLocalDescription(offer);
                sendSignal(userId, 'offer', { type: pc.localDescription.type, sdp: btoa(unescape(encodeURIComponent(pc.localDescription.sdp))) });
            } catch (err) { console.error('negotiationneeded error:', err); }
            finally { makingOffer[userId] = false; }
        };
        pc.ontrack = (event) => {
            ensureParticipantTileVisible(userId);
            let audio = document.getElementById('audio-' + userId);
            if (!audio) {
                audio = document.createElement('audio');
                audio.id = 'audio-' + userId;
                audio.autoplay = true;
                audio.style.display = 'none';
                document.body.appendChild(audio);
            }
            audio.srcObject = event.streams[0];
            audio.play().catch(() => {});
        };
        pc.onicecandidate = (event) => { if (event.candidate) sendSignal(userId, 'ice-candidate', { candidate: event.candidate.toJSON() }); };
        pc.oniceconnectionstatechange = () => {
            const state = pc.iceConnectionState;
            if (state === 'failed') {
                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }
                removeParticipantTileSilently(userId, true);
            } else if (state === 'disconnected') {
                if (offlineTimers[userId]) clearTimeout(offlineTimers[userId]);
                offlineTimers[userId] = setTimeout(() => {
                    const cur = peers[userId];
                    if (!cur || ['disconnected', 'failed', 'closed'].includes(cur.iceConnectionState)) removeParticipantTileSilently(userId, true);
                    delete offlineTimers[userId];
                }, 2000);
            } else if (state === 'connected' || state === 'completed') {
                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }
                ensureParticipantTileVisible(userId);
                broadcastMyMicStatus(); // ✅ FIX: send our real mic state the moment we connect
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
        const info = knownParticipants[String(userId)];
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
        if (data.type === 'meeting-cancelled') {
            showToast('⚠️ Meeting has been cancelled by the organizer.');
            setTimeout(() => { cleanup(); window.location.href = LEAVE_URL; }, 2500);
            return;
        }
        if (data.type === 'meeting-ended') {
            showToast('📞 Meeting has ended.');
            setTimeout(() => { cleanup(); window.location.href = LEAVE_URL; }, 2500);
            return;
        }
        if (data.type === 'user-joined') {
            const joinedId = String(data.data.userId);
            if (joinedId === String(MY_USER_ID)) return;
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
            showToast(`✅ ${escapeHtml(data.data.name)} has joined the meeting.`);
            sendSignal(joinedId, 'mic-status', { userId: MY_USER_ID, muted: !isMicOn }); // ✅ direct hello to the new joiner
            return;
        }
        if (data.type === 'user-left') {
            if (offlineTimers[from]) { clearTimeout(offlineTimers[from]); delete offlineTimers[from]; }
            removeParticipantTileSilently(from, false);
            if (peers[from]) { peers[from].close(); delete peers[from]; }
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
        if (String(data.toUserId) !== String(MY_USER_ID)) return;
        if (!data.data) return;
        try {
            if (data.type === 'offer') {
                const pc = createPeerConnection(from);
                const polite = isPolite(from);
                const offerCollision = (makingOffer[from]) || (pc.signalingState !== 'stable');
                if (offerCollision && !polite) return;
                const sdp = decodeSdp(data.data.sdp);
                if (offerCollision && polite) await pc.setLocalDescription({ type: 'rollback' });
                await pc.setRemoteDescription(new RTCSessionDescription({ type: data.data.type || 'offer', sdp }));
                if (pendingCandidates[from]?.length) {
                    for (const c of pendingCandidates[from]) await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
                    delete pendingCandidates[from];
                }
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
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
                await pc.addIceCandidate(new RTCIceCandidate(candidate)).catch(() => {});
            } else if (data.type === 'mute') {
                if (!localStream) return;
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
    function toggleMic() {
        if (!localStream) return;
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
        broadcastMyMicStatus();
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
        const colorList = ['#3b82f6,#06b6d4','#8b5cf6,#ec4899','#22c55e,#06b6d4','#f59e0b,#ef4444','#64748b,#334155','#ec4899,#f59e0b'];
        const color = isOrganizer ? colorList[0] : colorList[Math.floor(Math.random() * colorList.length)];
        const grid = document.getElementById('video-grid');
        const tile = document.createElement('div');
        tile.className = 'video-tile';
        tile.id = 'tile-' + userId;
        // Default to "muted" visually only until the real mic-status arrives
        // (broadcastMyMicStatus() on connect now makes that arrive almost instantly).
        const startsMuted = participantMicStatus[userId] !== false;
        tile.innerHTML = `
        <div class="video-placeholder">
            <div class="avatar-circle" style="background:linear-gradient(135deg,${color});">${escapeHtml(initials)}</div>
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
        if (!SR) { showToast('⚠️ Transcript not supported in this browser. Please use Chrome or Edge.'); return; }
        recognition = new SR();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = currentLang;
        const indicator = document.getElementById('listening-indicator');
        const listenText = document.getElementById('listening-text');
        recognition.onstart = () => { recognitionRunning = true; if (indicator) indicator.style.display = 'flex'; if (listenText) listenText.textContent = 'Listening...'; };
        recognition.onresult = (e) => {
            if (!isMicOn) { stopRecognition(); return; }
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const result = e.results[i];
                const text = result[0].transcript.trim();
                if (!text) continue;
                const speaking = document.getElementById('speaking-' + MY_USER_ID);
                if (speaking) speaking.style.display = 'flex';
                if (result.isFinal) {
                    if (speaking) speaking.style.display = 'none';
                    showLocalTranscript(text, false);
                    saveTranscript(text);
                } else {
                    showLocalTranscript(text, true);
                }
            }
        };
        recognition.onerror = (e) => { recognitionRunning = false; if (isMicOn) startRecognition(); };
        recognition.onend = () => { recognitionRunning = false; if (indicator) indicator.style.display = 'none'; if (isMicOn) startRecognition(); };
    }
    function startRecognition() { if (!recognition || recognitionRunning) return; try { recognition.start(); } catch (e) {} }
    function toggleTranscriptLanguage() {
        currentLang = (currentLang === 'en-US') ? 'ur-PK' : 'en-US';
        const btn = document.getElementById('lang-toggle-btn');
        if (btn) btn.textContent = (currentLang === 'ur-PK') ? '🌐 اردو' : '🌐 English';
        showToast(currentLang === 'ur-PK' ? 'Transcript language: Urdu' : 'Transcript language: English');
        if (recognition) { stopRecognition(); recognition = null; }
        if (isMicOn) { startTranscript(); setTimeout(() => startRecognition(), 300); }
    }
    function stopRecognition() { if (!recognition) return; recognitionRunning = false; try { recognition.abort(); } catch(e) {} }
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
        const div = document.createElement('div');
        div.style.cssText = `display:flex;align-items:flex-end;gap:8px;margin-bottom:12px;${isMe ? 'flex-direction:row-reverse;' : 'flex-direction:row;'}`;
        div.innerHTML = isMe
            ? `<div style="max-width:75%;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-align:right;padding-right:4px;">You</div>
            <div style="background:linear-gradient(135deg,#3b82f6,#06b6d4);color:white;padding:10px 14px;border-radius:18px 18px 4px 18px;font-size:13px;line-height:1.5;word-break:break-word;box-shadow:0 2px 8px rgba(59,130,246,0.3);">${escapeHtml(text)}</div>
            <div style="font-size:10px;color:var(--muted);margin-top:3px;text-align:right;padding-right:4px;">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</div>
           </div>
           <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:white;flex-shrink:0;margin-bottom:18px;">${escapeHtml(MY_INITIALS)}</div>`
            : `<div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:white;flex-shrink:0;margin-bottom:18px;">${escapeHtml(name.charAt(0).toUpperCase())}</div>
           <div style="max-width:75%;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:4px;padding-left:4px;">${escapeHtml(name)}</div>
            <div style="background:var(--surface2);color:white;padding:10px 14px;border-radius:18px 18px 18px 4px;font-size:13px;line-height:1.5;word-break:break-word;border:1px solid var(--border);">${escapeHtml(text)}</div>
            <div style="font-size:10px;color:var(--muted);margin-top:3px;padding-left:4px;">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</div>
           </div>`;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }
    // ── LEAVE ──
    let leftNotified = false;
    async function leaveMeeting() {
        if (!confirm('Are you sure you want to leave?')) return;
        leftNotified = true;
        try { await fetch(MARK_LEFT_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({}) }); }
        catch (e) { console.error('markLeft error:', e); }
        cleanup();
        window.location.href = LEAVE_URL;
    }
    function notifyDisconnectBeacon() {
        if (leftNotified) return;
        leftNotified = true;
        const payload = JSON.stringify({ to_user_id: 'all', type: 'user-left', data: { name: MY_NAME, temporary: true }, _token: CSRF });
        const url = SIGNAL_URL + '?_token=' + encodeURIComponent(CSRF);
        try { navigator.sendBeacon(url, new Blob([payload], { type: 'application/json' })); } catch(e) {}
        try { fetch(url, { method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: payload }).catch(() => {}); } catch(e) {}
    }
    window.addEventListener('pagehide', () => { notifyDisconnectBeacon(); cleanup(); });
    window.addEventListener('beforeunload', () => { notifyDisconnectBeacon(); });
    function cleanup() {
        Object.values(offlineTimers).forEach(t => clearTimeout(t));
        Object.values(peers).forEach(pc => pc.close());
        localStream?.getTracks().forEach(t => t.stop());
        stopRecognition();
    }
    function escapeHtml(text) { const d = document.createElement('div'); d.textContent = String(text ?? ''); return d.innerHTML; }
    function showToast(message) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            Object.assign(container.style, { position:'fixed', bottom:'90px', left:'50%', transform:'translateX(-50%)', zIndex:'9999', display:'flex', flexDirection:'column', gap:'8px', pointerEvents:'none' });
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        Object.assign(toast.style, { background:'#1e293b', color:'white', padding:'10px 20px', borderRadius:'8px', fontSize:'14px', fontWeight:'500', minWidth:'200px', textAlign:'center', boxShadow:'0 4px 12px rgba(0,0,0,.3)', borderLeft:'3px solid #f59e0b', opacity:'1', transition:'opacity .3s' });
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
</script>
</body>
</html>
