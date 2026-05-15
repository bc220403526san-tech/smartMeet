<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/meeting-room.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">

    <div class="header-left">
        <div class="live-badge">
            <div class="live-dot"></div>
            LIVE
        </div>
        <div>
            <div class="meeting-title">Q4 Product Review</div>
            <div class="meeting-meta">
                <span><i class="fa fa-users"></i> 5 Participants</span>
                <span>·</span>
                <span>Asia/Karachi</span>
            </div>
        </div>
    </div>

    <div class="header-center">
        <i class="fa fa-clock timer-icon"></i>
        <span id="timer">00:24:35</span>
    </div>

    <div class="header-right">
        <div class="participants-count">
            <i class="fa fa-circle" style="color:var(--green);font-size:8px;"></i>
            5 online
        </div>
        <button class="btn-leave">
            <i class="fa fa-phone-slash"></i>
            Leave
        </button>
    </div>

</div>

<!-- MAIN -->
<div class="main">

    <!-- VIDEO AREA -->
    <div class="video-area">

        <div class="video-grid">

            <!-- Active Speaker — Ali (Organizer) -->
            <div class="video-tile active-speaker speaking">
                <div class="video-placeholder">
                    <div class="avatar-circle lg" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">AK</div>
                </div>
                <div class="tile-info">
                    <div class="tile-name">
                        <i class="fa fa-crown crown-icon"></i>
                        Ali Khan
                    </div>
                    <div class="tile-icons">
                        <div class="speaking-indicator">
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                            <div class="speaking-bar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sara -->
            <div class="video-tile">
                <div class="video-placeholder">
                    <div class="avatar-circle" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);">SA</div>
                </div>
                <div class="tile-info">
                    <div class="tile-name">Sara Ahmed</div>
                    <div class="tile-icons">
                        <div class="mic-off"><i class="fa fa-microphone-slash"></i></div>
                    </div>
                </div>
            </div>

            <!-- Ahmed -->
            <div class="video-tile">
                <div class="video-placeholder">
                    <div class="avatar-circle" style="background:linear-gradient(135deg,#22c55e,#06b6d4);">AR</div>
                </div>
                <div class="tile-info">
                    <div class="tile-name">Ahmed Raza</div>
                    <div class="tile-icons"></div>
                </div>
            </div>

            <!-- Fatima -->
            <div class="video-tile">
                <div class="video-placeholder">
                    <div class="avatar-circle" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">FM</div>
                </div>
                <div class="tile-info">
                    <div class="tile-name">Fatima Malik</div>
                    <div class="tile-icons">
                        <div class="mic-off"><i class="fa fa-microphone-slash"></i></div>
                    </div>
                </div>
            </div>

            <!-- You -->
            <div class="video-tile">
                <div class="video-placeholder">
                    <div class="avatar-circle" style="background:linear-gradient(135deg,#64748b,#334155);">ME</div>
                </div>
                <div class="you-badge">You</div>
                <div class="tile-info">
                    <div class="tile-name">You</div>
                    <div class="tile-icons"></div>
                </div>
            </div>

        </div>

    </div>

    <!-- SIDE PANEL -->
    <div class="transcript-panel">

        <!-- Tabs -->
        <div class="panel-tabs">
            <div class="tab active" onclick="switchTab('transcript', this)">
                <i class="fa fa-closed-captioning" style="margin-right:4px;"></i>
                Transcript
            </div>
            <div class="tab" onclick="switchTab('chat', this)">
                <i class="fa fa-comment" style="margin-right:4px;"></i>
                Chat
            </div>
            <div class="tab" onclick="switchTab('participants', this)">
                <i class="fa fa-users" style="margin-right:4px;"></i>
                People
            </div>
        </div>

        <!-- TRANSCRIPT TAB -->
        <div id="tab-transcript" style="display:flex;flex-direction:column;flex:1;overflow:hidden;">

            <div class="transcript-body">

                <div class="transcript-entry">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">AK</div>
                    <div class="transcript-content">
                        <div class="transcript-meta">
                            <span class="transcript-name">Ali Khan</span>
                            <span class="transcript-time">10:02</span>
                        </div>
                        <div class="transcript-text">Good morning everyone, let's start with the Q4 agenda review.</div>
                    </div>
                </div>

                <div class="transcript-entry">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);">SA</div>
                    <div class="transcript-content">
                        <div class="transcript-meta">
                            <span class="transcript-name">Sara Ahmed</span>
                            <span class="transcript-time">10:03</span>
                        </div>
                        <div class="transcript-text">Sure, I have the design reports ready to present.</div>
                    </div>
                </div>

                <div class="transcript-entry">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#22c55e,#06b6d4);">AR</div>
                    <div class="transcript-content">
                        <div class="transcript-meta">
                            <span class="transcript-name">Ahmed Raza</span>
                            <span class="transcript-time">10:05</span>
                        </div>
                        <div class="transcript-text">Backend integration is complete. We can demo the API today.</div>
                    </div>
                </div>

                <div class="transcript-entry">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">AK</div>
                    <div class="transcript-content">
                        <div class="transcript-meta">
                            <span class="transcript-name">Ali Khan</span>
                            <span class="transcript-time">10:06</span>
                        </div>
                        <div class="transcript-text">Great, let's also discuss the budget allocation for next quarter...</div>
                    </div>
                </div>

                <!-- Live/interim -->
                <div class="transcript-entry">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4);">AK</div>
                    <div class="transcript-content">
                        <div class="transcript-meta">
                            <span class="transcript-name">Ali Khan</span>
                            <span class="transcript-time" style="color:var(--blue);">live</span>
                        </div>
                        <div class="transcript-text interim">I think we should focus on the mobile experience first...</div>
                    </div>
                </div>

            </div>

            <div class="listening-indicator">
                <div class="listening-dot"></div>
                Listening — Ali Khan is speaking
            </div>

        </div>

        <!-- CHAT TAB -->
        <div id="tab-chat" class="panel-hidden" style="display:flex;flex-direction:column;flex:1;overflow:hidden;">

            <div class="chat-body">

                <div class="chat-msg">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);width:28px;height:28px;font-size:10px;font-weight:700;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;">SA</div>
                    <div>
                        <div style="font-size:10px;color:var(--muted);margin-bottom:3px;">Sara  10:02</div>
                        <div class="chat-bubble">Has everyone reviewed the agenda?</div>
                    </div>
                </div>

                <div class="chat-msg mine">
                    <div>
                        <div style="font-size:10px;color:var(--muted);margin-bottom:3px;text-align:right;">You  10:03</div>
                        <div class="chat-bubble">Yes, ready to go! 👍</div>
                    </div>
                </div>

                <div class="chat-msg">
                    <div class="transcript-avatar" style="background:linear-gradient(135deg,#22c55e,#06b6d4);width:28px;height:28px;font-size:10px;font-weight:700;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;">AR</div>
                    <div>
                        <div style="font-size:10px;color:var(--muted);margin-bottom:3px;">Ahmed  10:05</div>
                        <div class="chat-bubble">Sharing my screen shortly</div>
                    </div>
                </div>

            </div>

            <div class="chat-input-area">
                <input class="chat-input" placeholder="Type a message..." />
                <button class="btn-send"><i class="fa fa-paper-plane"></i></button>
            </div>

        </div>

        <!-- PARTICIPANTS TAB -->
        <div id="tab-participants" class="panel-hidden" style="flex:1;overflow-y:auto;padding:12px;">

            <div style="display:flex;flex-direction:column;gap:8px;">

                <!-- Organizer -->
                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">AK</div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;">
                            Ali Khan
                            <i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>
                        </div>
                        <div style="font-size:10px;color:var(--blue);">Organizer</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:5px;">
                        <span style="width:8px;height:8px;background:var(--green);border-radius:50%;"></span>
                    </div>
                </div>

                <!-- Participants -->
                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">SA</div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;">Sara Ahmed</div>
                        <div style="font-size:10px;color:var(--muted);">Participant</div>
                    </div>
                    <i class="fa fa-microphone-slash" style="color:var(--red);font-size:12px;"></i>
                </div>

                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">AR</div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;">Ahmed Raza</div>
                        <div style="font-size:10px;color:var(--muted);">Participant</div>
                    </div>
                    <i class="fa fa-microphone" style="color:var(--green);font-size:12px;"></i>
                </div>

                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">FM</div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;">Fatima Malik</div>
                        <div style="font-size:10px;color:var(--muted);">Participant</div>
                    </div>
                    <i class="fa fa-microphone-slash" style="color:var(--red);font-size:12px;"></i>
                </div>

                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:rgba(59,130,246,0.08);border-radius:12px;border:1px solid rgba(59,130,246,0.2);">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#64748b,#334155);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;">ME</div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;">You</div>
                        <div style="font-size:10px;color:var(--blue);">Participant (You)</div>
                    </div>
                    <i class="fa fa-microphone" style="color:var(--green);font-size:12px;"></i>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- CONTROLS -->
<div class="controls">

    <div class="ctrl-btn" onclick="toggleCtrl(this, 'mic')">
        <div class="ctrl-icon" id="ctrl-mic">
            <i class="fa fa-microphone"></i>
        </div>
        <span class="ctrl-label">Mic</span>
    </div>

    <div class="ctrl-btn" onclick="toggleCtrl(this, 'cam')">
        <div class="ctrl-icon active" id="ctrl-cam">
            <i class="fa fa-video"></i>
        </div>
        <span class="ctrl-label">Camera</span>
    </div>

    <div class="ctrl-btn" onclick="toggleCtrl(this, 'screen')">
        <div class="ctrl-icon" id="ctrl-screen">
            <i class="fa fa-desktop"></i>
        </div>
        <span class="ctrl-label">Share</span>
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

    <div class="ctrl-btn">
        <button class="btn-end" title="Leave Meeting">
            <i class="fa fa-phone-slash"></i>
        </button>
        <span class="ctrl-label" style="color:var(--red);">Leave</span>
    </div>

</div>

<script>
    // Timer
    let seconds = 24 * 60 + 35;
    setInterval(() => {
        seconds++;
        const h = Math.floor(seconds / 3600).toString().padStart(2,'0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2,'0');
        const s = (seconds % 60).toString().padStart(2,'0');
        document.getElementById('timer').textContent = `${h}:${m}:${s}`;
    }, 1000);

    // Tab switching
    function switchTab(tab, tabEl) {
        ['transcript','chat','participants'].forEach(t => {
            document.getElementById('tab-' + t).style.display = 'none';
            document.getElementById('tab-' + t).classList.add('panel-hidden');
        });
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

        const el = document.getElementById('tab-' + tab);
        el.style.display = 'flex';
        el.classList.remove('panel-hidden');
        if(tab === 'participants') { el.style.display = 'block'; }

        if(tabEl) tabEl.classList.add('active');
    }

    // Control toggle
    function toggleCtrl(btn, type) {
        const icon = btn.querySelector('.ctrl-icon');
        if(icon.classList.contains('active')) {
            icon.classList.remove('active');
            icon.classList.add('off');
        } else if(icon.classList.contains('off')) {
            icon.classList.remove('off');
        } else {
            icon.classList.add('active');
        }
    }
</script>

</body>
</html>
