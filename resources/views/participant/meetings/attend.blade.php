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

            {{-- Organizer Tile --}}
            <div class="video-tile" id="tile-{{ $organizer->id }}">
                <div class="video-placeholder">
                    <div class="avatar-circle lg" style="background:linear-gradient(135deg,{{ $colors[0] }});">
                        {{ $orgInitials }}
                    </div>
                </div>
                <div class="tile-info">
                    <div class="tile-name">
                        <i class="fa fa-crown crown-icon"></i>
                        {{ $organizer->name }}
                    </div>
                    <div class="tile-icons">
                        <div class="speaking-indicator" id="speaking-{{ $organizer->id }}" style="display:none;">
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                        </div>
                        <div class="mic-off" id="micoff-{{ $organizer->id }}" style="display:none;">
                            <i class="fa fa-microphone-slash"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Participants Tiles --}}
            @foreach($meeting->participants as $index => $participant)
                @php
                    $p        = $participant->user;
                    $initials = strtoupper(substr($p->name, 0, 1) . substr(strrchr($p->name, ' ') ?: ' ', 1, 1));
                    $color    = $colors[($index + 1) % count($colors)];
                    $isMe     = $p->id === auth()->id();
                @endphp
                <div class="video-tile" id="tile-{{ $p->id }}">
                    <div class="video-placeholder">
                        <div class="avatar-circle" style="background:linear-gradient(135deg,{{ $color }});">
                            {{ $initials }}
                        </div>
                    </div>
                    <div class="tile-info">
                        <div class="tile-name">
                            {{ $p->name }}
                            @if($isMe)
                                <span style="font-size:10px;background:rgba(59,130,246,0.3);padding:2px 6px;border-radius:99px;">You</span>
                            @endif
                        </div>
                        <div class="tile-icons">
                            <div class="speaking-indicator" id="speaking-{{ $p->id }}" style="display:none;">
                                <div class="speaking-bar"></div>
                                <div class="speaking-bar"></div>
                                <div class="speaking-bar"></div>
                            </div>
                            <div class="mic-off" id="micoff-{{ $p->id }}" style="display:none;">
                                <i class="fa fa-microphone-slash"></i>
                            </div>
                        </div>
                    </div>
                    @if($isMe)
                        <div class="you-badge">You</div>
                    @endif
                </div>
            @endforeach

        </div>
    </div>

    {{-- SIDE PANEL --}}
    <div class="transcript-panel">

        <div class="panel-tabs">
            <div class="tab active" onclick="switchTab('transcript', this)">
                <i class="fa fa-closed-captioning" style="margin-right:4px;"></i>Transcript
            </div>
            <div class="tab" onclick="switchTab('chat', this)">
                <i class="fa fa-comment" style="margin-right:4px;"></i>Chat
            </div>
            <div class="tab" onclick="switchTab('participants', this)">
                <i class="fa fa-users" style="margin-right:4px;"></i>People
            </div>
        </div>

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

        {{-- PARTICIPANTS TAB --}}
        <div id="tab-participants" class="panel-hidden" style="display:none;flex:1;overflow-y:auto;padding:12px;">
            <div style="display:flex;flex-direction:column;gap:8px;">

                {{-- Organizer --}}
                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">
                        {{ $orgInitials }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                            {{ $organizer->name }}
                            <i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>
                        </div>
                        <div style="font-size:10px;color:var(--blue);">Organizer</div>
                    </div>
                    <span id="people-online-{{ $organizer->id }}"
                          style="width:8px;height:8px;background:var(--surface2);border-radius:50%;border:1px solid var(--border);"></span>
                </div>

                {{-- Participants --}}
                @foreach($meeting->participants as $index => $participant)
                    @php
                        $p         = $participant->user;
                        $pInitials = strtoupper(substr($p->name, 0, 1) . substr(strrchr($p->name, ' ') ?: ' ', 1, 1));
                        $color     = $colors[($index + 1) % count($colors)];
                        $isMe      = $p->id === auth()->id();
                    @endphp
                    <div style="display:flex;align-items:center;gap:10px;padding:10px;
                                background:{{ $isMe ? 'rgba(59,130,246,0.08)' : 'var(--surface2)' }};
                                border-radius:12px;
                                border:1px solid {{ $isMe ? 'rgba(59,130,246,0.2)' : 'var(--border)' }};">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,{{ $color }});display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">
                            {{ $pInitials }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;">
                                {{ $p->name }}
                                @if($isMe)
                                    <span style="font-size:10px;color:#3b82f6;">(You)</span>
                                @endif
                            </div>
                            <div style="font-size:10px;color:var(--muted);">Participant</div>
                        </div>
                        <span id="people-online-{{ $p->id }}"
                              style="width:8px;height:8px;background:var(--surface2);border-radius:50%;border:1px solid var(--border);"></span>
                    </div>
                @endforeach

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

    <div class="ctrl-btn" onclick="switchTab('transcript', null)">
        <div class="ctrl-icon" id="ctrl-transcript">
            <i class="fa fa-closed-captioning"></i>
        </div>
        <span class="ctrl-label">Transcript</span>
    </div>

    <div class="ctrl-btn" onclick="switchTab('chat', null)">
        <div class="ctrl-icon" id="ctrl-chat">
            <i class="fa fa-comment"></i>
        </div>
        <span class="ctrl-label">Chat</span>
    </div>

    <div class="ctrl-btn" onclick="switchTab('participants', null)">
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
    const CSRF           = "{{ csrf_token() }}";

    const ALL_USER_IDS = @json(
        $meeting->participants->pluck('user_id')
            ->push($meeting->organizer_id)
            ->unique()
            ->values()
    );

    // ── ONLINE ──
    const onlineUsers = new Set([String(MY_USER_ID)]);

    function markOnline(userId) {
        onlineUsers.add(String(userId));
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
    let seconds = 0;
    setInterval(() => {
        seconds++;
        const h = String(Math.floor(seconds / 3600)).padStart(2,'0');
        const m = String(Math.floor((seconds % 3600) / 60)).padStart(2,'0');
        const s = String(seconds % 60).padStart(2,'0');
        document.getElementById('timer').textContent = `${h}:${m}:${s}`;
    }, 1000);

    // ── TAB SWITCH ──
    function switchTab(tab, tabEl) {
        ['transcript','chat','participants'].forEach(t => {
            const el = document.getElementById('tab-' + t);
            if (el) { el.style.display = 'none'; el.classList.add('panel-hidden'); }
        });
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        const active = document.getElementById('tab-' + tab);
        if (active) {
            active.style.display = tab === 'participants' ? 'block' : 'flex';
            active.classList.remove('panel-hidden');
        }
        if (tabEl) {
            tabEl.classList.add('active');
        } else {
            document.querySelectorAll('.tab').forEach(t => {
                if (t.getAttribute('onclick')?.includes(`'${tab}'`)) t.classList.add('active');
            });
        }
    }

    // ── WEBRTC VARS ──
    let localStream        = null;
    let peers              = {};
    let pendingCandidates  = {};
    let isMicOn            = false;   // Participant bhi default OFF
    let recognition        = null;
    let recognitionRunning = false;

    const iceConfig = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
        ]
    };

    // ── START ──
    window.addEventListener('load', async () => {
        listenForSignals();
        await startAudio();
    });

    // ── 1. MIC ACCESS ──
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

            // Participant mic bhi default OFF
            localStream.getAudioTracks().forEach(t => t.enabled = false);
            isMicOn = false;

            await sendOfferToAll();
            startTranscript();

        } catch (err) {
            console.error('Mic error:', err);
            if (err.name === 'NotFoundError') {
                alert('Microphone not found. Please connect a microphone.');
            } else if (err.name === 'NotAllowedError') {
                alert('Microphone permission denied. Please allow mic access.');
            } else {
                alert('Microphone error: ' + err.message);
            }
        }
    }

    // ── 2. REVERB SUBSCRIBE ──
    function listenForSignals() {
        if (typeof window.Echo === 'undefined') { console.error('Echo not initialized'); return; }
        window.Echo.channel('meeting.' + MEETING_ID)
            .listen('.signal', handleSignal);
    }

    // ── 3. OFFER TO ALL ──
    async function sendOfferToAll() {
        for (const userId of ALL_USER_IDS) {
            if (String(userId) !== String(MY_USER_ID)) {
                await createOffer(userId);
            }
        }
    }

    // ── 4. PEER CONNECTION ──
    function createPeerConnection(userId) {
        if (peers[userId]) return peers[userId];
        const pc = new RTCPeerConnection(iceConfig);
        peers[userId] = pc;

        if (localStream) {
            localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
        }

        pc.ontrack = (event) => {
            markOnline(userId);
            let audio = document.getElementById('audio-' + userId);
            if (!audio) {
                audio          = document.createElement('audio');
                audio.id       = 'audio-' + userId;
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
            if (event.candidate) {
                sendSignal(userId, 'ice-candidate', { candidate: event.candidate.toJSON() });
            }
        };

        pc.onconnectionstatechange = () => {
            if (['disconnected','failed','closed'].includes(pc.connectionState)) {
                markOffline(userId);
                delete peers[userId];
            } else if (pc.connectionState === 'connected') {
                markOnline(userId);
            }
        };

        pc.onsignalingstatechange = () => {
            if (pc.signalingState === 'stable' && pendingCandidates[userId]?.length) {
                pendingCandidates[userId].forEach(c => {
                    pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
                });
                delete pendingCandidates[userId];
            }
        };

        return pc;
    }

    // ── 5. CREATE OFFER ──
    async function createOffer(toUserId) {
        const pc    = createPeerConnection(toUserId);
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        sendSignal(toUserId, 'offer', {
            type: pc.localDescription.type,
            sdp:  btoa(unescape(encodeURIComponent(pc.localDescription.sdp)))
        });
    }

    // ── SDP DECODE ──
    function decodeSdp(sdp) {
        if (!sdp) return '';
        try { return decodeURIComponent(escape(atob(sdp))); }
        catch(e) { return sdp; }
    }

    // ── 6. HANDLE SIGNAL ──
    async function handleSignal(data) {
        const from = String(data.fromUserId);

        // ── CHAT (toUserId = 'all') ──
        if (data.type === 'chat') {
            if (String(data.fromUserId) === String(MY_USER_ID)) return;
            const name = data.data?.name || 'User';
            const text = data.data?.text || '';
            if (!text) return;
            addChatBubble(name, text, false);
            return;
        }

        // ── TRANSCRIPT (toUserId = 'all') ──
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

        // ── WEBRTC + MUTE — sirf apne liye ──
        if (String(data.toUserId) !== String(MY_USER_ID)) return;
        if (!data.data) return;

        try {
            if (data.type === 'offer') {
                const pc  = createPeerConnection(from);
                const sdp = decodeSdp(data.data.sdp);
                await pc.setRemoteDescription(new RTCSessionDescription({ type: data.data.type || 'offer', sdp }));

                if (pendingCandidates[from]?.length) {
                    for (const c of pendingCandidates[from]) {
                        await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
                    }
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
                        for (const c of pendingCandidates[from]) {
                            await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
                        }
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

    // ── 7. SEND SIGNAL ──
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

        // ✅ WebRTC track enable/disable
        localStream.getAudioTracks().forEach(t => t.enabled = isMicOn);

        const btn      = document.getElementById('ctrl-mic');
        const micOff   = document.getElementById('micoff-' + MY_USER_ID);
        const speaking = document.getElementById('speaking-' + MY_USER_ID);

        if (isMicOn) {
            if (btn)    { btn.innerHTML = '<i class="fa fa-microphone"></i>'; btn.classList.remove('off'); }
            if (micOff) micOff.style.display = 'none';
            // ✅ Mic on hone par recognition start karo
            startRecognition();
        } else {
            if (btn)      { btn.innerHTML = '<i class="fa fa-microphone-slash"></i>'; btn.classList.add('off'); }
            if (micOff)   micOff.style.display = 'flex';
            if (speaking) speaking.style.display = 'none';
            // ✅ Mic off hone par recognition band karo
            stopRecognition();
        }

        for (const userId of ALL_USER_IDS) {
            if (String(userId) !== String(MY_USER_ID)) {
                sendSignal(userId, 'mic-status', { muted: !isMicOn });
            }
        }
    }

    // ── 9. TRANSCRIPT SETUP ──
    function startTranscript() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) return;

        recognition            = new SR();
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
            if (['aborted', 'no-speech'].includes(e.error)) return;
            if (isMicOn) setTimeout(() => {
                if (isMicOn && !recognitionRunning) startRecognition();
            }, 1500);
        };

        recognition.onend = () => {
            recognitionRunning = false;
            if (indicator) indicator.style.display = 'none';
            if (isMicOn) setTimeout(() => {
                if (isMicOn && !recognitionRunning) startRecognition();
            }, 400);
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

    // ── APNA TRANSCRIPT LOCALLY DIKHAO ──
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

    // ── DOOSRON KA TRANSCRIPT ──
    function handleTranscriptUpdate(data) {
        if (String(data.userId) === String(MY_USER_ID)) return;
        const body = document.getElementById('transcript-body');
        if (!body) return;
        body.querySelector('[data-empty]')?.remove();
        const div = document.createElement('div');
        div.className = 'transcript-entry';
        div.innerHTML = `
            <div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);">
                ${escapeHtml(data.userInitials)}
            </div>
            <div class="transcript-content">
                <div class="transcript-meta">
                    <span class="transcript-name">${escapeHtml(data.userName)}</span>
                    <span class="transcript-time">${data.spokenAt}</span>
                </div>
                <div class="transcript-text">${escapeHtml(data.text)}</div>
            </div>`;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;

        const listenText = document.getElementById('listening-text');
        if (listenText) listenText.textContent = `${escapeHtml(data.userName)} is speaking`;
    }

    // ── CHAT ──
    function sendChat() {
        const input = document.getElementById('chat-input');
        const text  = input.value.trim();
        if (!text) return;
        addChatBubble(MY_NAME, text, true);
        input.value = '';
        fetch(SIGNAL_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({
                to_user_id: 'all',
                type:       'chat',
                data:       { text, name: MY_NAME, initials: MY_INITIALS }
            })
        }).catch(err => console.error('Chat error:', err));
    }

    function handleChatMessage(data) {
        if (String(data.fromUserId) === String(MY_USER_ID)) return;
        const name = data.data?.name || 'User';
        const text = data.data?.text || '';
        if (!text) return;
        addChatBubble(name, text, false);
    }

    function addChatBubble(name, text, isMe) {
        const body = document.getElementById('chat-body');
        if (!body) return;
        body.querySelector('[data-empty]')?.remove();

        const div = document.createElement('div');
        div.className = 'chat-msg' + (isMe ? ' mine' : '');
        div.style.cssText = `
        display: flex;
        align-items: flex-end;
        gap: 8px;
        margin-bottom: 12px;
        ${isMe ? 'flex-direction: row-reverse;' : 'flex-direction: row;'}
    `;

        div.innerHTML = isMe
            ? `<div style="max-width:75%;">
               <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-align:right;padding-right:4px;">You</div>
               <div style="
                   background: linear-gradient(135deg,#3b82f6,#06b6d4);
                   color: white;
                   padding: 10px 14px;
                   border-radius: 18px 18px 4px 18px;
                   font-size: 13px;
                   line-height: 1.5;
                   word-break: break-word;
                   box-shadow: 0 2px 8px rgba(59,130,246,0.3);
               ">${escapeHtml(text)}</div>
               <div style="font-size:10px;color:var(--muted);margin-top:3px;text-align:right;padding-right:4px;">
                   ${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}
               </div>
           </div>
           <div style="
               width:30px;height:30px;border-radius:50%;
               background:linear-gradient(135deg,#3b82f6,#06b6d4);
               display:flex;align-items:center;justify-content:center;
               font-size:11px;font-weight:700;color:white;
               flex-shrink:0;margin-bottom:18px;
           ">${escapeHtml(MY_INITIALS)}</div>`

            : `<div style="
               width:30px;height:30px;border-radius:50%;
               background:linear-gradient(135deg,#8b5cf6,#ec4899);
               display:flex;align-items:center;justify-content:center;
               font-size:11px;font-weight:700;color:white;
               flex-shrink:0;margin-bottom:18px;
           ">${escapeHtml(name.charAt(0).toUpperCase())}</div>
           <div style="max-width:75%;">
               <div style="font-size:10px;color:var(--muted);margin-bottom:4px;padding-left:4px;">${escapeHtml(name)}</div>
               <div style="
                   background: var(--surface2);
                   color: white;
                   padding: 10px 14px;
                   border-radius: 18px 18px 18px 4px;
                   font-size: 13px;
                   line-height: 1.5;
                   word-break: break-word;
                   border: 1px solid var(--border);
               ">${escapeHtml(text)}</div>
               <div style="font-size:10px;color:var(--muted);margin-top:3px;padding-left:4px;">
                   ${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}
               </div>
           </div>`;

        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }
    // ── LEAVE ──
    function leaveMeeting() {
        if (!confirm('Are you sure you want to leave?')) return;
        cleanup();
        window.location.href = LEAVE_URL;
    }

    function cleanup() {
        Object.values(peers).forEach(pc => pc.close());
        localStream?.getTracks().forEach(t => t.stop());
        stopRecognition();
    }

    // ── HELPERS ──
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
            Object.assign(container.style, {
                position: 'fixed', bottom: '90px', left: '50%',
                transform: 'translateX(-50%)', zIndex: '9999',
                display: 'flex', flexDirection: 'column', gap: '8px',
                pointerEvents: 'none'
            });
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        Object.assign(toast.style, {
            background: '#1e293b', color: 'white', padding: '10px 20px',
            borderRadius: '8px', fontSize: '14px', fontWeight: '500',
            minWidth: '200px', textAlign: 'center',
            boxShadow: '0 4px 12px rgba(0,0,0,.3)',
            borderLeft: '3px solid #f59e0b',
            opacity: '1', transition: 'opacity .3s'
        });
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

</script>

</body>
</html>
