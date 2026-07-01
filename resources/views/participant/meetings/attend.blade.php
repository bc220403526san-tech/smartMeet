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
@endphp

<body>

{{-- ── HEADER ── --}}
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

{{-- ── MAIN ── --}}
<div class="main">

    {{-- VIDEO AREA --}}
    <div class="video-area">
        <div class="video-grid" id="video-grid">

            {{-- Apni (participant ki) tile — hamesha static, "Participant" label ke saath --}}
            <div class="video-tile" id="tile-{{ auth()->id() }}">
                <div class="video-placeholder">
                    <div class="avatar-circle" style="background:linear-gradient(135deg,{{ $colors[1] }});">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
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

            {{-- Organizer + baqi participants ke liye tiles purely JS/WebRTC presence se control hoti hain --}}

        </div>
    </div>

    {{-- SIDE PANEL — sirf tab button click pe khulta hai, default hidden --}}
    <div class="transcript-panel" id="side-panel" style="display:none;">

        {{-- TRANSCRIPT TAB --}}
        <div id="tab-transcript" style="display:flex;flex-direction:column;flex:1;overflow:hidden;">
            <div class="transcript-body" id="transcript-body">
                <div data-empty style="text-align:center;color:#64748b;font-size:12px;padding:20px;">
                    Transcript will appear here...
                </div>
            </div>
            <div class="listening-indicator" id="listening-indicator" style="display:none;">
                <div class="listening-dot"></div>
                <span id="listening-text">Listening...</span>
            </div>
        </div>

        {{-- CHAT TAB --}}
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

        {{-- PARTICIPANTS TAB — SAB log (organizer + joined + not joined) hamesha dikhte hain --}}
        <div id="tab-participants" class="panel-hidden" style="display:none;flex:1;overflow-y:auto;padding:12px;">
            <div style="display:flex;flex-direction:column;gap:8px;">

                {{-- Apni row --}}
                <div style="display:flex;align-items:center;gap:10px;padding:10px;
                    background:rgba(59,130,246,0.08);border-radius:12px;
                    border:1px solid rgba(59,130,246,0.2);">
                    <div style="width:36px;height:36px;border-radius:50%;
                        background:linear-gradient(135deg,{{ $colors[1] }});
                        display:flex;align-items:center;justify-content:center;
                        font-size:12px;font-weight:700;color:white;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;">
                            {{ auth()->user()->name }}
                            <span style="font-size:10px;color:#3b82f6;">(You)</span>
                        </div>
                        <div style="font-size:10px;color:var(--green);">Participant • Joined</div>
                    </div>
                    <span id="people-online-{{ auth()->id() }}"
                          style="width:8px;height:8px;background:var(--green);border-radius:50%;"></span>
                </div>

                {{-- Organizer row (server-rendered, hamesha dikhti hai) --}}
                <div id="panel-row-{{ $organizer->id }}" style="display:flex;align-items:center;gap:10px;padding:10px;
                    background:var(--surface2);border-radius:12px;border:1px solid var(--border);opacity:0.5;">
                    <div style="width:36px;height:36px;border-radius:50%;
                        background:linear-gradient(135deg,#3b82f6,#06b6d4);
                        display:flex;align-items:center;justify-content:center;
                        font-size:12px;font-weight:700;color:white;">
                        {{ $orgInitials }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                            {{ $organizer->name }}
                            <i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>
                        </div>
                        <div class="join-status" style="font-size:10px;color:var(--muted);">Organizer • Not joined yet</div>
                    </div>
                    <span class="online-dot" style="width:8px;height:8px;background:var(--surface2);border-radius:50%;border:1px solid var(--border);"></span>
                </div>

                {{-- SAB baqi participants — joined aur not-joined, dono hamesha yahan dikhenge --}}
                <div id="other-participants-panel">
                    @foreach($meeting->participants as $participant)
                        @continue($participant->user_id === auth()->id())
                        @php
                            $p         = $participant->user;
                            $pInitials = strtoupper(substr($p->name, 0, 1) . substr(strrchr($p->name, ' ') ?: ' ', 1, 1));
                            $hasJoined = !is_null($participant->joined_at);
                        @endphp
                        <div id="panel-row-{{ $p->id }}" style="display:flex;align-items:center;gap:10px;padding:10px;margin-top:8px;
                            background:{{ $hasJoined ? 'rgba(34,197,94,0.08)' : 'var(--surface2)' }};
                            border-radius:12px;
                            border:1px solid {{ $hasJoined ? 'rgba(34,197,94,0.2)' : 'var(--border)' }};
                            opacity:{{ $hasJoined ? '1' : '0.5' }};">
                            <div style="width:36px;height:36px;border-radius:50%;
                                background:linear-gradient(135deg,#22c55e,#06b6d4);
                                display:flex;align-items:center;justify-content:center;
                                font-size:12px;font-weight:700;color:white;">
                                {{ $pInitials }}
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;">{{ $p->name }}</div>
                                <div class="join-status" style="font-size:10px;color:{{ $hasJoined ? 'var(--green)' : 'var(--muted)' }};">
                                    Participant • {{ $hasJoined ? 'Joined' : 'Not joined yet' }}
                                </div>
                            </div>
                            <span class="online-dot" style="width:8px;height:8px;
                                background:{{ $hasJoined ? 'var(--green)' : 'var(--surface2)' }};
                                border-radius:50%;
                                border:{{ $hasJoined ? 'none' : '1px solid var(--border)' }};"></span>
                        </div>
                    @endforeach
                </div>

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

    {{-- Participant sirf leave kar sakta hai — cancel nahi --}}
    <div class="ctrl-btn">
        <button class="btn-end" onclick="leaveMeeting()">
            <i class="fa fa-phone-slash"></i>
        </button>
        <span class="ctrl-label" style="color:var(--red);">Leave</span>
    </div>

</div>

<script>

    // ── CONFIG ──
    const MEETING_ID     = "{{ $meeting->id }}";
    const MY_USER_ID     = "{{ auth()->id() }}";
    const MY_NAME        = "{{ auth()->user()->name }}";
    const MY_INITIALS    = "{{ strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}";
    const SIGNAL_URL     = "{{ route('participant.meetings.signal', $meeting) }}";
    const TRANSCRIPT_URL = "{{ route('participant.meetings.transcript', $meeting) }}";
    const LEAVE_URL      = "{{ route('participant.meetings.index') }}";
    const MARK_LEFT_URL  = "{{ route('participant.meetings.markLeft', $meeting) }}";
    const CSRF           = "{{ csrf_token() }}";

    const ALL_USER_IDS   = @json($allUserIds);
    const ALREADY_JOINED = @json($alreadyJoined);
    const ORGANIZER_ID   = "{{ $organizer->id }}";

    // ── KNOWN PARTICIPANTS (organizer + sab participants, joined ya nahi) ──
    const knownParticipants = {};
    knownParticipants[ORGANIZER_ID] = { name: "{{ addslashes($organizer->name) }}", initials: "{{ $orgInitials }}" };
    @foreach($meeting->participants as $participant)
        @if($participant->user_id !== auth()->id())
        knownParticipants["{{ $participant->user->id }}"] = {
        name: "{{ addslashes($participant->user->name) }}",
        initials: "{{ strtoupper(substr($participant->user->name, 0, 1) . substr(strrchr($participant->user->name, ' ') ?: ' ', 1, 1)) }}"
    };
    @endif
    @endforeach

    // ── ONLINE ──
    const onlineUsers = new Set([String(MY_USER_ID)]);
    const departedAnnounced = new Set();

    function markOnline(userId) {
        onlineUsers.add(String(userId));
        departedAnnounced.delete(String(userId));
        updateOnlineCount();
        const dot = document.getElementById('people-online-' + userId);
        if (dot) { dot.style.background = 'var(--green)'; dot.style.border = 'none'; }
    }

    function markOffline(userId) {
        onlineUsers.delete(String(userId));
        updateOnlineCount();
        const dot = document.getElementById('people-online-' + userId);
        if (dot) { dot.style.background = 'var(--surface2)'; dot.style.border = '1px solid var(--border)'; }
    }

    function updateOnlineCount() {
        const c = onlineUsers.size;
        document.querySelectorAll('[data-online-count]').forEach(el => el.textContent = c);
        document.querySelectorAll('[data-total-count]').forEach(el => el.textContent = c);
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
    let activeTab  = null;
    let panelOpen  = false;

    function updateChatBadge() {
        const badge = document.getElementById('chat-badge');
        if (!badge) return;
        if (unreadChat > 0) {
            badge.textContent = unreadChat > 99 ? '99+' : String(unreadChat);
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function switchTab(tab, tabEl) {
        ['transcript','chat','participants'].forEach(t => {
            const el = document.getElementById('tab-' + t);
            if (el) { el.style.display = 'none'; el.classList.add('panel-hidden'); }
        });
        document.querySelectorAll('.ctrl-icon').forEach(t => t.classList.remove('active'));
        const active = document.getElementById('tab-' + tab);
        if (active) {
            active.style.display = tab === 'participants' ? 'block' : 'flex';
            active.classList.remove('panel-hidden');
        }
        activeTab = tab;
        const icon = document.getElementById('ctrl-' + tab);
        if (icon) icon.classList.add('active');
        if (tab === 'chat') { unreadChat = 0; updateChatBadge(); }
    }

    function toggleSidePanel(tab, tabEl) {
        const panel = document.getElementById('side-panel');
        if (!panel) return;

        if (panelOpen && activeTab === tab) {
            panel.style.display = 'none';
            panelOpen = false;
            activeTab = null;
            document.querySelectorAll('.ctrl-icon').forEach(t => t.classList.remove('active'));
            return;
        }

        panel.style.removeProperty('display');
        panelOpen = true;
        switchTab(tab, tabEl);
    }

    // ── WEBRTC VARS ──
    let localStream        = null;
    let peers               = {};
    let pendingCandidates   = {};
    let makingOffer         = {};
    let isMicOn             = false;
    let recognition         = null;
    let recognitionRunning  = false;
    const offlineTimers     = {};

    const iceConfig = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
        ]
    };

    function isPolite(otherUserId) {
        return String(MY_USER_ID) < String(otherUserId);
    }

    // ── START ──
    window.addEventListener('load', async () => {
        listenForSignals();
        await startAudio();
    });

    // ── MIC ACCESS ──
    async function startAudio() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: false,
                    noiseSuppression: false,
                    autoGainControl: false,
                    googEchoCancellation: false,
                    googNoiseSuppression: false,
                    googAutoGainControl: false
                },
                video: false
            });
            localStream.getAudioTracks().forEach(t => t.enabled = false);
            isMicOn = false;
            connectToAll();
            startTranscript();
        } catch (err) {
            console.error('Mic error:', err);
            if (err.name === 'NotFoundError')       alert('Microphone not found.');
            else if (err.name === 'NotAllowedError') alert('Microphone permission denied.');
            else alert('Microphone error: ' + err.message);
        }
    }

    function listenForSignals() {
        if (typeof window.Echo === 'undefined') { console.error('Echo not initialized'); return; }
        window.Echo.channel('meeting.' + MEETING_ID).listen('.signal', handleSignal);
    }

    function connectToAll() {
        for (const userId of ALL_USER_IDS) {
            if (String(userId) !== String(MY_USER_ID)) createPeerConnection(userId);
        }
    }

    function removeParticipantTileSilently(userId, announce) {
        const tile = document.getElementById('tile-' + userId);
        if (tile) tile.remove();
        markOffline(userId);
        updatePanelRowOffline(userId);
        if (announce && !departedAnnounced.has(String(userId))) {
            departedAnnounced.add(String(userId));
            const info = knownParticipants[String(userId)];
            showToast(`⚠️ ${escapeHtml(info ? info.name : 'A participant')} has disconnected.`);
        }
    }

    function ensureParticipantTileVisible(userId) {
        const info = knownParticipants[String(userId)];
        if (info) {
            addParticipantTile(userId, info.name, info.initials, String(userId) === ORGANIZER_ID);
            updatePanelRowOnline(userId, info.name, info.initials);
        }
        markOnline(userId);
    }

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
                sendSignal(userId, 'offer', {
                    type: pc.localDescription.type,
                    sdp:  btoa(unescape(encodeURIComponent(pc.localDescription.sdp)))
                });
            } catch (err) {
                console.error('negotiationneeded error:', err);
            } finally {
                makingOffer[userId] = false;
            }
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
            audio.play().catch(() => {
                const unlock = () => { audio.play(); document.removeEventListener('click', unlock); };
                document.addEventListener('click', unlock);
            });
        };

        pc.onicecandidate = (event) => {
            if (event.candidate) sendSignal(userId, 'ice-candidate', { candidate: event.candidate.toJSON() });
        };

        pc.oniceconnectionstatechange = () => {
            const state = pc.iceConnectionState;

            if (state === 'failed') {
                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }
                removeParticipantTileSilently(userId, true);
                try { pc.restartIce(); } catch (e) {}

            } else if (state === 'disconnected') {
                if (offlineTimers[userId]) clearTimeout(offlineTimers[userId]);
                offlineTimers[userId] = setTimeout(() => {
                    const cur = peers[userId];
                    if (!cur || ['disconnected', 'failed', 'closed'].includes(cur.iceConnectionState)) {
                        removeParticipantTileSilently(userId, true);
                    }
                    delete offlineTimers[userId];
                }, 1500);

            } else if (state === 'connected' || state === 'completed') {
                if (offlineTimers[userId]) { clearTimeout(offlineTimers[userId]); delete offlineTimers[userId]; }
                ensureParticipantTileVisible(userId);

            } else if (state === 'checking' || state === 'new') {
                if (offlineTimers[userId]) clearTimeout(offlineTimers[userId]);
                offlineTimers[userId] = setTimeout(() => {
                    const cur = peers[userId];
                    if (cur && ['checking', 'new', 'disconnected'].includes(cur.iceConnectionState)) {
                        try { cur.restartIce(); } catch (e) {}
                    }
                    delete offlineTimers[userId];
                }, 6000);
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

    function decodeSdp(sdp) {
        if (!sdp) return '';
        try { return decodeURIComponent(escape(atob(sdp))); }
        catch(e) { return sdp; }
    }

    // ── HANDLE SIGNAL ──
    async function handleSignal(data) {
        const from = String(data.fromUserId);

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
            if (String(data.data.userId) === String(MY_USER_ID)) return;
            knownParticipants[String(data.data.userId)] = { name: data.data.name, initials: data.data.initials };
            updatePanelRowOnline(data.data.userId, data.data.name, data.data.initials);
            markOnline(data.data.userId);
            createPeerConnection(data.data.userId);
            showToast(`✅ ${escapeHtml(data.data.name)} has joined the meeting.`);
            return;
        }

        if (data.type === 'user-left') {
            if (offlineTimers[from]) { clearTimeout(offlineTimers[from]); delete offlineTimers[from]; }
            removeParticipantTileSilently(from, false);
            if (peers[from]) { peers[from].close(); delete peers[from]; }

            if (!departedAnnounced.has(from)) {
                departedAnnounced.add(from);
                const name = data.data?.name || (knownParticipants[from] && knownParticipants[from].name) || 'A participant';
                if (data.data?.temporary) {
                    showToast(`⚠️ ${escapeHtml(name)} has disconnected.`);
                } else {
                    showToast(`👋 ${escapeHtml(name)} has left the meeting.`);
                }
            }

            updatePanelRowOffline(from);
            return;
        }

        if (data.type === 'chat') {
            if (String(data.fromUserId) === String(MY_USER_ID)) return;
            const name = data.data?.name || 'User';
            const text = data.data?.text || '';
            if (!text) return;
            addChatBubble(name, text, false);
            if (activeTab !== 'chat') { unreadChat++; updateChatBadge(); }
            return;
        }

        if (data.type === 'transcript') {
            if (String(data.fromUserId) === String(MY_USER_ID)) return;
            const body = document.getElementById('transcript-body');
            if (!body) return;
            body.querySelector('[data-empty]')?.remove();
            const div = document.createElement('div');
            div.className = 'transcript-entry';
            div.innerHTML = `
                <div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);">
                    ${escapeHtml(data.data?.userInitials || '?')}
                </div>
                <div class="transcript-content">
                    <div class="transcript-meta">
                        <span class="transcript-name">${escapeHtml(data.data?.userName || 'User')}</span>
                        <span class="transcript-time">${data.data?.spokenAt || ''}</span>
                    </div>
                    <div class="transcript-text">${escapeHtml(data.data?.text || '')}</div>
                </div>`;
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
            const listenText = document.getElementById('listening-text');
            if (listenText) listenText.textContent = `${escapeHtml(data.data?.userName || '')} is speaking`;
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

                if (offerCollision && polite) {
                    await pc.setLocalDescription({ type: 'rollback' });
                }

                await pc.setRemoteDescription(new RTCSessionDescription({ type: data.data.type || 'offer', sdp }));
                if (pendingCandidates[from]?.length) {
                    for (const c of pendingCandidates[from]) await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
                    delete pendingCandidates[from];
                }
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                sendSignal(from, 'answer', {
                    type: pc.localDescription.type,
                    sdp:  btoa(unescape(encodeURIComponent(pc.localDescription.sdp)))
                });

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
                const btn    = document.getElementById('ctrl-mic');
                const micOff = document.getElementById('micoff-' + MY_USER_ID);
                if (btn)    { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }
                if (micOff) micOff.style.display = 'flex';
                stopRecognition();
                showToast('You have been muted by the organizer');

            } else if (data.type === 'unmute') {
                showToast('The organizer has unmuted you');

            } else if (data.type === 'mic-status') {
                const micOff = document.getElementById('micoff-' + from);
                if (micOff) micOff.style.display = data.data.muted ? 'flex' : 'none';
            }

        } catch (err) {
            console.error('Signal handle error:', err);
        }
    }

    async function sendSignal(toUserId, type, data) {
        try {
            const res = await fetch(SIGNAL_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ to_user_id: toUserId, type, data })
            });
            if (!res.ok) console.error('sendSignal failed:', await res.text());
        } catch (err) {
            console.error('sendSignal error:', err);
        }
    }

    function toggleMic() {
        if (!localStream) return;
        isMicOn = !isMicOn;
        localStream.getAudioTracks().forEach(t => t.enabled = isMicOn);
        const btn      = document.getElementById('ctrl-mic');
        const micOff   = document.getElementById('micoff-' + MY_USER_ID);
        const speaking = document.getElementById('speaking-' + MY_USER_ID);
        if (isMicOn) {
            if (btn)    { btn.innerHTML = '<i class="fa fa-microphone"></i>'; btn.classList.remove('off'); }
            if (micOff) micOff.style.display = 'none';
            startRecognition();
        } else {
            if (btn)      { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }
            if (micOff)   micOff.style.display = 'flex';
            if (speaking) speaking.style.display = 'none';
            stopRecognition();
        }
        for (const userId of ALL_USER_IDS) {
            if (String(userId) !== String(MY_USER_ID)) sendSignal(userId, 'mic-status', { muted: !isMicOn });
        }
    }

    // ── ADD PARTICIPANT TILE — "Organizer" ya "Participant" label naam ke saath ──
    function addParticipantTile(userId, name, initials, isOrganizer) {
        knownParticipants[String(userId)] = { name, initials };
        if (document.getElementById('tile-' + userId)) return;
        const colorList = ['#3b82f6,#06b6d4','#8b5cf6,#ec4899','#22c55e,#06b6d4','#f59e0b,#ef4444','#64748b,#334155','#ec4899,#f59e0b'];
        const color = isOrganizer ? colorList[0] : colorList[Math.floor(Math.random() * colorList.length)];
        const grid = document.getElementById('video-grid');
        const tile = document.createElement('div');
        tile.className = 'video-tile';
        tile.id = 'tile-' + userId;
        tile.innerHTML = `
            <div class="video-placeholder">
                <div class="avatar-circle" style="background:linear-gradient(135deg,${color});">
                    ${escapeHtml(initials)}
                </div>
            </div>
            <div class="tile-info">
                <div class="tile-name">
                    ${isOrganizer ? '<i class="fa fa-crown crown-icon"></i> ' : ''}${escapeHtml(name)}<span class="role-badge ${isOrganizer ? 'organizer' : 'participant'}">${isOrganizer ? 'Organizer' : 'Participant'}</span>
                </div>
                <div class="tile-icons">
                    <div class="speaking-indicator" id="speaking-${userId}" style="display:none;">
                        <div class="speaking-bar"></div>
                        <div class="speaking-bar"></div>
                        <div class="speaking-bar"></div>
                    </div>
                    <div class="mic-off" id="micoff-${userId}" style="display:none;">
                        <i class="fa fa-microphone-slash"></i>
                    </div>
                </div>
            </div>`;
        if (isOrganizer) grid.prepend(tile); else grid.appendChild(tile);
        markOnline(userId);
    }

    // ── PEOPLE TAB rows — "Organizer • Joined/Not joined" / "Participant • Joined/Not joined" ──
    function panelRowHtml(userId, name, initials, online, isOrganizer) {
        const color = isOrganizer ? '#3b82f6,#06b6d4' : '#22c55e,#06b6d4';
        const roleLabel = isOrganizer ? 'Organizer' : 'Participant';
        return `
            <div id="panel-row-${userId}" style="display:flex;align-items:center;gap:10px;padding:10px;margin-top:8px;
                background:${online ? 'rgba(34,197,94,0.08)' : 'var(--surface2)'};
                border-radius:12px;
                border:1px solid ${online ? 'rgba(34,197,94,0.2)' : 'var(--border)'};
                opacity:${online ? '1' : '0.5'};">
                <div style="width:36px;height:36px;border-radius:50%;
                    background:linear-gradient(135deg,${color});
                    display:flex;align-items:center;justify-content:center;
                    font-size:12px;font-weight:700;color:white;">
                    ${escapeHtml(initials)}
                </div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                        ${escapeHtml(name)}
                        ${isOrganizer ? '<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>' : ''}
                    </div>
                    <div class="join-status" style="font-size:10px;color:${online ? 'var(--green)' : 'var(--muted)'};">
                        ${roleLabel} • ${online ? 'Joined' : 'Not joined yet'}
                    </div>
                </div>
                <span class="online-dot" style="width:8px;height:8px;
                    background:${online ? 'var(--green)' : 'var(--surface2)'};
                    border-radius:50%;
                    border:${online ? 'none' : '1px solid var(--border)'};"></span>
            </div>`;
    }

    function updatePanelRowOnline(userId, name, initials) {
        const isOrganizer = String(userId) === ORGANIZER_ID;
        const existing = document.getElementById('panel-row-' + userId);
        const html = panelRowHtml(userId, name, initials, true, isOrganizer);
        if (existing) {
            existing.outerHTML = html;
        } else {
            const container = document.getElementById('other-participants-panel');
            if (container) container.insertAdjacentHTML('beforeend', html);
        }
    }

    function updatePanelRowOffline(userId) {
        const existing = document.getElementById('panel-row-' + userId);
        const info = knownParticipants[String(userId)];
        const isOrganizer = String(userId) === ORGANIZER_ID;
        if (existing && info) existing.outerHTML = panelRowHtml(userId, info.name, info.initials, false, isOrganizer);
    }

    function startTranscript() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) return;
        recognition = new SR();
        recognition.continuous     = true;
        recognition.interimResults = true;
        recognition.lang           = 'en-US';
        const indicator  = document.getElementById('listening-indicator');
        const listenText = document.getElementById('listening-text');
        recognition.onstart = () => {
            recognitionRunning = true;
            if (indicator)  indicator.style.display = 'flex';
            if (listenText) listenText.textContent   = 'Listening...';
        };
        recognition.onresult = (e) => {
            if (!isMicOn) { stopRecognition(); return; }
            const result = e.results[e.results.length - 1];
            const text   = result[0].transcript.trim();
            if (!text) return;
            const speaking = document.getElementById('speaking-' + MY_USER_ID);
            if (speaking) speaking.style.display = 'flex';
            if (result.isFinal) {
                if (speaking) speaking.style.display = 'none';
                showLocalTranscript(text);
                saveTranscript(text);
            }
        };
        recognition.onerror = (e) => {
            recognitionRunning = false;
            if (['aborted','no-speech'].includes(e.error)) return;
            if (isMicOn) setTimeout(() => { if (isMicOn && !recognitionRunning) startRecognition(); }, 1500);
        };
        recognition.onend = () => {
            recognitionRunning = false;
            if (indicator) indicator.style.display = 'none';
            if (isMicOn) setTimeout(() => { if (isMicOn && !recognitionRunning) startRecognition(); }, 400);
        };
    }

    function startRecognition() {
        if (!recognition || recognitionRunning) return;
        try { recognition.start(); } catch(e) { console.warn('Recognition start:', e.message); }
    }

    function stopRecognition() {
        if (!recognition) return;
        recognitionRunning = false;
        try { recognition.abort(); } catch(e) {}
    }

    function showLocalTranscript(text) {
        const body = document.getElementById('transcript-body');
        if (!body) return;
        body.querySelector('[data-empty]')?.remove();
        const div = document.createElement('div');
        div.className = 'transcript-entry';
        div.innerHTML = `
            <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">
                ${escapeHtml(MY_INITIALS)}
            </div>
            <div class="transcript-content">
                <div class="transcript-meta">
                    <span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span>
                    <span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span>
                </div>
                <div class="transcript-text">${escapeHtml(text)}</div>
            </div>`;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    async function saveTranscript(text) {
        try {
            await fetch(TRANSCRIPT_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ text })
            });
        } catch (err) { console.error('Transcript save error:', err); }
    }

    function sendChat() {
        const input = document.getElementById('chat-input');
        const text  = input.value.trim();
        if (!text) return;
        addChatBubble(MY_NAME, text, true);
        input.value = '';
        fetch(SIGNAL_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ to_user_id: 'all', type: 'chat', data: { text, name: MY_NAME, initials: MY_INITIALS } })
        }).catch(err => console.error('Chat error:', err));
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

    // ── EXPLICIT LEAVE (button click) — sirf yahi DB mein "left" mark karta hai ──
    let leftNotified = false;
    async function leaveMeeting() {
        if (!confirm('Are you sure you want to leave?')) return;
        leftNotified = true;
        try {
            await fetch(MARK_LEFT_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({})
            });
        } catch (e) { console.error('markLeft error:', e); }
        cleanup();
        window.location.href = LEAVE_URL;
    }

    function notifyDisconnectBeacon() {
        if (leftNotified) return;
        leftNotified = true;

        const payloadObj = { to_user_id: 'all', type: 'user-left', data: { name: MY_NAME, temporary: true }, _token: CSRF };
        const payload     = JSON.stringify(payloadObj);
        const url          = SIGNAL_URL + '?_token=' + encodeURIComponent(CSRF);

        try {
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon(url, blob);
        } catch (e) {}

        try {
            fetch(url, {
                method: 'POST', keepalive: true,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: payload
            }).catch(() => {});
        } catch (e) {}
    }

    window.addEventListener('pagehide', () => {
        notifyDisconnectBeacon();
        cleanup();
    });
    window.addEventListener('beforeunload', () => {
        notifyDisconnectBeacon();
    });

    function cleanup() {
        Object.values(offlineTimers).forEach(t => clearTimeout(t));
        Object.values(peers).forEach(pc => pc.close());
        localStream?.getTracks().forEach(t => t.stop());
        stopRecognition();
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = String(text ?? '');
        return d.innerHTML;
    }

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
