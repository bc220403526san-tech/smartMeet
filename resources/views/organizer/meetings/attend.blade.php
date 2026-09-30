<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }} — {{ $meeting->title }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite(['resources/css/meeting-room.css', 'resources/js/app.js'])
    <style>
        :root{
            --bg-1:#050a16; --bg-2:#0a1226; --bg-3:#0d1830;
            --panel:rgba(13,22,42,.92); --panel-soft:rgba(15,25,46,.7);
            --line:rgba(148,163,184,.14); --line-strong:rgba(148,163,184,.24);
            --text:#f1f5f9; --muted:#8b98ad; --muted-2:#64748b;
            --blue:#3b82f6; --blue-soft:rgba(59,130,246,.18);
            --violet:#8b5cf6; --cyan:#22d3ee;
            --green:#22c55e; --amber:#f59e0b; --red:#ef4444;
            --radius-lg:20px; --radius-md:14px; --radius-sm:10px;
            --shadow-lg:0 20px 55px rgba(0,0,0,.35);
        }
        *,*::before,*::after{box-sizing:border-box}
        html,body{height:100%;max-width:100%;overflow-x:hidden}
        body{
            margin:0; min-height:100dvh; display:flex; flex-direction:column;
            color:var(--text); font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;
            background:
                radial-gradient(circle at 10% 6%, rgba(59,130,246,.16), transparent 32%),
                radial-gradient(circle at 92% 88%, rgba(139,92,246,.14), transparent 32%),
                linear-gradient(160deg,var(--bg-1),var(--bg-2) 55%,var(--bg-3));
        }

        .header{
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            flex-wrap:wrap; row-gap:8px; min-height:60px;
            padding:10px 18px; background:rgba(6,11,22,.86); backdrop-filter:blur(18px);
            border-bottom:1px solid var(--line); box-shadow:0 10px 30px rgba(0,0,0,.2);
            position:relative; z-index:60;
        }
        .header-left{display:flex; align-items:center; gap:12px; min-width:0; flex:1 1 auto; overflow:hidden}
        .header-brand{display:flex; align-items:center; gap:9px; padding-right:14px; border-right:1px solid var(--line); flex-shrink:0}
        .header-brand img{width:30px; height:30px; object-fit:contain}
        .header-brand-text .name{font-weight:700; font-size:14px}
        .header-brand-text .tag{font-size:10px; color:var(--muted-2)}
        .live-badge{
            display:flex; align-items:center; gap:6px; font-size:10px; font-weight:700; letter-spacing:.5px;
            text-transform:uppercase; color:#fecaca; background:rgba(239,68,68,.14);
            border:1px solid rgba(239,68,68,.3); padding:4px 10px; border-radius:999px; flex-shrink:0;
        }
        .live-dot{width:7px; height:7px; border-radius:50%; background:var(--red); box-shadow:0 0 0 0 rgba(239,68,68,.5); animation:pulse-dot 1.6s infinite}
        @keyframes pulse-dot{0%{box-shadow:0 0 0 0 rgba(239,68,68,.55)}70%{box-shadow:0 0 0 8px rgba(239,68,68,0)}100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}}
        .header-meeting-info{min-width:0; overflow:hidden}
        .meeting-title{font-size:14px; font-weight:700; max-width:38vw; overflow:hidden; text-overflow:ellipsis; white-space:nowrap}
        .meeting-meta{font-size:10.5px; color:var(--muted); display:flex; gap:6px; align-items:center; overflow:hidden; white-space:nowrap; text-overflow:ellipsis}
        .header-center{
            display:flex; align-items:center; gap:7px; padding:6px 12px; border-radius:11px;
            border:1px solid var(--line); background:rgba(255,255,255,.03); font-size:12.5px; font-weight:600; flex-shrink:0;
        }
        .header-right{display:flex; align-items:center; gap:10px; flex-shrink:0}
        .participants-count{display:flex; align-items:center; gap:6px; font-size:11px; color:var(--muted); padding:6px 10px; border-radius:9px; background:rgba(255,255,255,.03); border:1px solid var(--line)}
        .btn-leave{
            display:flex; align-items:center; gap:7px; border:none; cursor:pointer; color:#fff; font-weight:700; font-size:12px;
            padding:9px 16px; border-radius:11px; background:linear-gradient(135deg,#ef4444,#b91c1c);
            box-shadow:0 10px 24px rgba(239,68,68,.28); transition:transform .15s ease, box-shadow .15s ease;
        }
        .btn-leave:hover{transform:translateY(-1px); box-shadow:0 14px 30px rgba(239,68,68,.36)}
        .btn-cancel{
            display:flex; align-items:center; gap:7px; border:1px solid rgba(239,68,68,.4); cursor:pointer;
            color:#fecaca; font-weight:700; font-size:11.5px; padding:8px 14px; border-radius:11px;
            background:rgba(239,68,68,.1); transition:background .15s ease;
            white-space:nowrap; flex-shrink:0;
        }
        .btn-cancel:hover{background:rgba(239,68,68,.2)}

        .main{flex:1 1 auto; min-height:0; display:flex; gap:12px; padding:12px}
        .video-area{
            flex:1; min-width:0; position:relative; border-radius:var(--radius-lg); overflow:hidden;
            border:1px solid var(--line); background:linear-gradient(155deg,rgba(10,18,36,.7),rgba(4,9,20,.85));
            box-shadow:inset 0 1px 0 rgba(255,255,255,.03), var(--shadow-lg);
        }
        .video-grid{
            height:100%; width:100%; display:grid; overflow-y:auto; overscroll-behavior:contain; scrollbar-gutter:stable;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            grid-auto-rows:minmax(150px,220px); align-content:start; justify-content:center;
            gap:16px; padding:16px; background-color:#02060f;
        }
        .video-grid:has(> .video-tile:only-child){grid-template-columns:minmax(240px,min(620px,92%)); align-content:center}
        .video-tile{max-width:420px; margin:0 auto; width:100%}
        .video-grid:has(> .video-tile:only-child) .video-tile{max-width:620px}

        .video-tile{
            position:relative; border-radius:var(--radius-md); overflow:hidden; aspect-ratio:16/10;
            background:linear-gradient(155deg,rgba(28,42,68,.9),rgba(8,13,26,.96));
            border:2px solid rgba(148,163,184,.32);
            box-shadow:0 14px 34px rgba(0,0,0,.4), 0 0 0 1px rgba(0,0,0,.5);
            transition:transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }
        .video-tile:hover{transform:translateY(-2px); border-color:rgba(96,165,250,.4); box-shadow:0 18px 40px rgba(0,0,0,.34)}
        .video-placeholder{position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:radial-gradient(circle at 50% 35%,rgba(59,130,246,.1),transparent 55%)}
        .video-placeholder video{position:absolute; inset:0; width:100%; height:100%; object-fit:cover; background:#050a16}
        .video-placeholder video.mirrored{transform:scaleX(-1)}
        .avatar-circle{
            width:74px; height:74px; border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:24px; font-weight:800; color:#fff; box-shadow:0 12px 30px rgba(0,0,0,.3), 0 0 0 6px rgba(255,255,255,.04);
        }
        .tile-info{
            position:absolute; left:0; right:0; bottom:0; z-index:5; padding:9px 12px;
            background:linear-gradient(to top,rgba(2,6,16,.94),rgba(2,6,16,.5),transparent);
            display:flex; align-items:center; justify-content:space-between; gap:8px;
        }
        .tile-name{font-size:12px; font-weight:650; display:flex; align-items:center; gap:6px; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap}
        .role-badge{font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:2px 6px; border-radius:99px; flex-shrink:0}
        .role-badge.organizer{background:rgba(251,191,36,.18); color:#fbbf24}
        .role-badge.participant{background:rgba(59,130,246,.18); color:#60a5fa}
        .tile-icons{display:flex; align-items:center; gap:6px; flex-shrink:0}
        .mic-off{width:24px; height:24px; border-radius:8px; background:rgba(15,23,42,.92); border:1px solid rgba(148,163,184,.28); display:flex; align-items:center; justify-content:center; font-size:10px; color:#cbd5e1; box-shadow:0 4px 12px rgba(0,0,0,.3)}
        .speaking-indicator{display:flex; align-items:flex-end; gap:2px; height:14px}
        .speaking-bar{width:2.5px; background:var(--green); border-radius:2px; animation:speak 0.9s infinite ease-in-out}
        .speaking-bar:nth-child(2){animation-delay:.15s} .speaking-bar:nth-child(3){animation-delay:.3s}
        @keyframes speak{0%,100%{height:4px}50%{height:13px}}
        .you-badge{position:absolute; top:8px; left:8px; z-index:5; font-size:9px; font-weight:700; padding:3px 8px; border-radius:99px; background:rgba(59,130,246,.35); border:1px solid rgba(96,165,250,.4)}
        .raised-hand-badge{
            position:absolute; top:9px; left:50%; transform:translateX(-50%);
            z-index:8; display:inline-flex; align-items:center; gap:5px;
            padding:5px 9px; border-radius:999px;
            background:rgba(245,158,11,.94); color:#fff;
            border:1px solid rgba(253,230,138,.75);
            box-shadow:0 8px 20px rgba(245,158,11,.20);
            font-size:10px; font-weight:800; pointer-events:none;
        }
        .raised-hand-badge i{font-size:11px}
        .people-hand{color:#fbbf24;font-size:13px;flex-shrink:0}
        .tile-expand-btn{
            position:absolute; top:8px; right:8px; z-index:6; width:28px; height:28px; border-radius:8px;
            background:rgba(8,13,26,.65); border:1px solid rgba(255,255,255,.14); color:#fff; display:flex;
            align-items:center; justify-content:center; font-size:12px; cursor:pointer; opacity:0; transition:opacity .2s, background .2s;
        }
        .video-tile:hover .tile-expand-btn, .video-tile.maximized .tile-expand-btn{opacity:1}
        .tile-expand-btn:hover{background:rgba(59,130,246,.85)}
        @media(hover:none){.tile-expand-btn{opacity:1}}

        #maximized-overlay{position:absolute; inset:0; z-index:30; background:#000; display:none}
        #maximized-overlay.active{display:block}
        #maximized-overlay .video-tile{width:100%; height:100%; border-radius:0; aspect-ratio:auto}
        .maximize-close-btn{
            position:absolute; top:14px; right:14px; z-index:40; width:38px; height:38px; border-radius:50%;
            background:rgba(8,13,26,.75); border:1px solid rgba(255,255,255,.16); color:#fff; display:flex;
            align-items:center; justify-content:center; font-size:15px; cursor:pointer;
        }
        .maximize-close-btn:hover{background:rgba(239,68,68,.85)}

        #side-panel{
            width:min(340px,32vw); min-width:300px; flex-shrink:0; display:none; flex-direction:column;
            border-radius:var(--radius-lg); border:1px solid var(--line); background:var(--panel);
            box-shadow:var(--shadow-lg); overflow:hidden; backdrop-filter:blur(20px); position:relative;
        }
        .panel-drag-handle{
            display:none; align-items:center; justify-content:center; height:16px; flex-shrink:0;
            cursor:ns-resize; touch-action:none; user-select:none;
        }
        .panel-drag-handle::before{content:""; width:36px; height:4px; border-radius:99px; background:rgba(203,213,225,.4)}
        .panel-drag-handle:hover::before{background:rgba(96,165,250,.7)}
        .panel-tabbar{display:flex; border-bottom:1px solid var(--line)}
        .panel-tabbtn{
            flex:1; text-align:center; padding:10px 6px; font-size:11.5px; font-weight:700; color:var(--muted);
            cursor:pointer; border-bottom:2px solid transparent; transition:color .15s, border-color .15s; background:none; border-top:none; border-left:none; border-right:none;
        }
        .panel-tabbtn.active{color:var(--text); border-bottom-color:var(--blue)}
        .panel-body{flex:1; min-height:0; overflow:hidden; display:flex; flex-direction:column}

        .transcript-body,.chat-body{flex:1; overflow-y:auto; padding:14px; display:flex; flex-direction:column; gap:10px}
        .empty-note{text-align:center; color:var(--muted-2); font-size:12px; padding:26px 10px}
        .transcript-entry{display:flex; gap:9px; padding:9px; border-radius:12px; background:rgba(255,255,255,.025)}
        .transcript-avatar{width:30px; height:30px; border-radius:9px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:#fff}
        .transcript-content{min-width:0; flex:1}
        .transcript-meta{display:flex; align-items:center; gap:8px; margin-bottom:3px}
        .transcript-name{font-size:11px; font-weight:700}
        .transcript-time{font-size:9px; color:var(--muted-2); margin-left:auto}
        .transcript-text{font-size:12px; line-height:1.5; color:#e2e8f0; word-break:break-word}
        .lang-row{display:flex; justify-content:space-between; align-items:center; padding:8px 12px; border-top:1px solid var(--line)}
        #lang-toggle-btn{background:rgba(255,255,255,.04); border:1px solid var(--line); color:var(--muted); font-size:10.5px; padding:5px 11px; border-radius:99px; cursor:pointer}
        .listening-indicator{display:none; align-items:center; gap:8px; margin:0 12px 10px; padding:7px 11px; border-radius:11px; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.22); font-size:11px; color:#86efac}
        .listening-dot{width:7px; height:7px; border-radius:50%; background:var(--green); animation:pulse-dot 1.4s infinite}

        .chat-body{min-height:0;overflow-y:auto !important;overflow-x:hidden !important;scrollbar-width:thin;scrollbar-color:rgba(148,163,184,.42) transparent;overscroll-behavior:contain}
        .chat-body::-webkit-scrollbar{width:7px}
        .chat-body::-webkit-scrollbar-track{background:transparent}
        .chat-body::-webkit-scrollbar-thumb{background:rgba(148,163,184,.36);border-radius:999px}
        .chat-body::-webkit-scrollbar-thumb:hover{background:rgba(148,163,184,.58)}
        .chat-message-row{display:flex;width:100%;gap:8px;align-items:flex-end;margin-bottom:4px}
        .chat-message-row.is-me{justify-content:flex-start}
        .chat-message-row.is-other{justify-content:flex-end;animation:chatMessageArrive .22s ease-out}
        .chat-message-avatar{width:30px;height:30px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex:0 0 30px;color:#eaf2ff;font-size:9px;font-weight:800;border:1px solid rgba(148,163,184,.16);box-shadow:0 4px 12px rgba(0,0,0,.16);overflow:hidden;user-select:none}
        .chat-message-content{max-width:78%;min-width:90px}
        .chat-message-row.is-me .chat-message-content{text-align:left}
        .chat-message-row.is-other .chat-message-content{text-align:right}
        .chat-message-meta{display:flex;gap:7px;align-items:center;margin:0 5px 4px;font-size:9px;color:#718096}
        .chat-message-row.is-me .chat-message-meta{justify-content:flex-start}
        .chat-message-row.is-other .chat-message-meta{justify-content:flex-end}
        .chat-message-meta strong{font-size:10.5px;font-weight:750;color:#dbe7f7}
        .chat-message-row.is-me .chat-message-meta strong{color:#bfdbfe}
        .chat-message-bubble{padding:9px 12px;border-radius:14px 14px 14px 5px;border:1px solid rgba(148,163,184,.14);font-size:12px;line-height:1.5;word-break:break-word;display:inline-block;text-align:left;color:#e8eef8;background:rgba(30,41,59,.72);box-shadow:0 4px 12px rgba(0,0,0,.12)}
        .chat-message-row.is-me .chat-message-bubble{border-radius:14px 14px 14px 5px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-color:rgba(96,165,250,.26);color:#fff;box-shadow:0 6px 16px rgba(37,99,235,.16)}
        .chat-message-row.is-other .chat-message-bubble{border-radius:14px 14px 5px 14px;background:rgba(30,41,59,.72)}
        .chat-message-receipt{min-height:14px;margin:4px 5px 0;font-size:8.5px;line-height:1.35;color:#64748b}
        .chat-message-row.is-me .chat-message-receipt{text-align:left}
        .chat-message-row.chat-send-failed .chat-message-bubble{opacity:.68}
        .chat-message-row.chat-send-failed .chat-message-receipt{color:#fca5a5}
        .chat-message-row.is-other .chat-message-receipt{text-align:right}
        @keyframes chatMessageArrive{from{opacity:.55;transform:translateY(3px)}to{opacity:1;transform:translateY(0)}}
        .chat-typing-indicator{display:none;align-items:center;gap:7px;min-height:28px;padding:5px 14px 7px;color:var(--muted);font-size:10.5px;border-top:1px solid rgba(148,163,184,.07);background:rgba(2,6,16,.2)}
        .chat-typing-indicator.show{display:flex}
        .chat-typing-dots{display:inline-flex;align-items:center;gap:3px}
        .chat-typing-dots span{width:4px;height:4px;border-radius:50%;background:#94a3b8;animation:chatTypingDot 1s infinite ease-in-out}
        .chat-typing-dots span:nth-child(2){animation-delay:.15s}
        .chat-typing-dots span:nth-child(3){animation-delay:.3s}
        @keyframes chatTypingDot{0%,60%,100%{transform:translateY(0);opacity:.45}30%{transform:translateY(-3px);opacity:1}}
        .chat-input-area{display:flex; align-items:center; gap:8px; padding:12px; border-top:1px solid var(--line); background:rgba(2,6,16,.4)}
        .chat-input{flex:1; min-height:40px; padding:8px 12px; border-radius:12px; background:rgba(255,255,255,.04); border:1px solid var(--line); color:var(--text); font-size:12.5px; outline:none}
        .chat-input:focus{border-color:rgba(56,189,248,.55); box-shadow:0 0 0 3px rgba(56,189,248,.08)}
        .chat-voice-btn,.btn-send{width:40px; height:40px; flex-shrink:0; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.04); color:#e2e8f0; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px}
        .btn-send{background:linear-gradient(135deg,#2563eb,#0891b2); border:none; color:#fff}
        .chat-voice-btn.listening{color:#ef4444; border-color:rgba(239,68,68,.5); background:rgba(239,68,68,.14)}

        #tab-people{
            min-height:0;
            overflow-y:auto !important;
            overflow-x:hidden !important;
            overscroll-behavior:contain;
            scrollbar-gutter:stable;
            scrollbar-width:thin;
            scrollbar-color:rgba(148,163,184,.65) transparent;
            -webkit-overflow-scrolling:touch;
        }
        #tab-people::-webkit-scrollbar{width:7px}
        #tab-people::-webkit-scrollbar-track{background:transparent}
        #tab-people::-webkit-scrollbar-thumb{background:rgba(148,163,184,.65);border-radius:999px}
        #tab-people::-webkit-scrollbar-thumb:hover{background:rgba(100,116,139,.85)}

        .people-scroll-shell{position:relative; flex:1; min-height:0; overflow:hidden}
        .people-body{height:100%; min-height:0; overflow-y:auto !important; overflow-x:hidden !important; overscroll-behavior:contain; padding:12px 20px 12px 12px; display:flex; flex-direction:column; gap:8px; scrollbar-width:thin; scrollbar-color:rgba(148,163,184,.42) transparent; -webkit-overflow-scrolling:touch}
        .people-body::-webkit-scrollbar{width:7px}
        .people-body::-webkit-scrollbar-track{background:transparent}
        .people-body::-webkit-scrollbar-thumb{background:rgba(148,163,184,.36);border-radius:999px}
        .people-body::-webkit-scrollbar-thumb:hover{background:rgba(148,163,184,.58)}
        .people-grab-track{display:none !important}
        .people-grab-thumb{position:absolute; top:0; left:1px; width:8px; min-height:44px; border-radius:999px; background:#e5e7eb; cursor:grab; touch-action:none; user-select:none; box-shadow:0 0 0 1px rgba(15,23,42,.65)}
        .person-row{display:flex; align-items:center; gap:10px; padding:10px; border-radius:13px; border:1px solid var(--line); background:rgba(255,255,255,.02); transition:opacity .2s, filter .2s, background .2s, border-color .2s}
        .person-row.joined{opacity:1; filter:none; background:rgba(34,197,94,.07); border-color:rgba(34,197,94,.22)}
        .person-row.pending{opacity:.5; filter:grayscale(.5) saturate(.4)}
        .person-avatar{width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; flex-shrink:0; overflow:hidden}
        .avatar-circle{overflow:hidden}
        .avatar-circle img,.person-avatar img{width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block}
        .person-info{flex:1; min-width:0}
        .person-name{font-size:12.5px; font-weight:700; display:flex; align-items:center; gap:5px}
        .person-status{font-size:10px; color:var(--muted)}
        .person-status.on{color:var(--green)}
        .person-dot{width:8px; height:8px; border-radius:50%; background:var(--muted-2); flex-shrink:0}
        .person-dot.on{background:var(--green)}
        .person-action{border:1px solid var(--line); background:rgba(255,255,255,.04); color:var(--muted); font-size:10px; padding:6px 8px; border-radius:8px; cursor:pointer; flex-shrink:0}
        .person-actions{display:flex;align-items:center;justify-content:flex-end;gap:4px;flex-wrap:wrap;flex-shrink:0;max-width:100px}
        .person-action.remove{color:#fda4af;border-color:rgba(244,63,94,.28)}
        .person-action.remove:hover{background:rgba(244,63,94,.18);color:#fff;border-color:rgba(244,63,94,.42)}
        .person-action.is-on{color:#86efac;border-color:rgba(34,197,94,.30);background:rgba(34,197,94,.08)}
        .person-action.is-off{color:#fca5a5;border-color:rgba(239,68,68,.30);background:rgba(239,68,68,.08)}

        .room-invite-card{
            flex-shrink:0; margin:12px 12px 4px; padding:11px; border-radius:13px;
            border:1px solid rgba(59,130,246,.24); background:rgba(59,130,246,.07);
        }
        .room-invite-title{display:flex;align-items:center;gap:7px;font-size:11.5px;font-weight:800;color:#dbeafe}
        .room-invite-note{margin-top:4px;font-size:9.5px;line-height:1.45;color:var(--muted)}
        .room-invite-actions{display:flex;gap:7px;flex-wrap:wrap;margin-top:9px}
        .room-invite-btn{
            min-width:0; flex:1 1 112px; display:flex;align-items:center;justify-content:center;gap:6px;
            border-radius:9px;padding:7px 9px;border:1px solid var(--line);cursor:pointer;
            background:rgba(255,255,255,.045);color:#e2e8f0;font-size:10px;font-weight:700;
        }
        .room-invite-btn:hover{background:rgba(59,130,246,.16);border-color:rgba(96,165,250,.35)}
        .room-invite-btn.primary{background:linear-gradient(135deg,#2563eb,#0891b2);border-color:transparent;color:#fff}
        .room-invite-link{
            margin-top:8px; padding:7px 8px; border-radius:8px; background:rgba(2,6,23,.42);
            border:1px solid var(--line); color:var(--muted); font-size:9px; white-space:nowrap;
            overflow:hidden; text-overflow:ellipsis; user-select:all;
        }

        .room-email-overlay{
            position:fixed; inset:0; z-index:10000; display:none;
            align-items:center; justify-content:center; padding:16px;
            background:rgba(0,0,0,.62); backdrop-filter:blur(5px);
        }
        .room-email-overlay.open{display:flex}
        .room-email-dialog{
            width:min(440px,100%); max-height:min(86dvh,680px); overflow-y:auto;
            border-radius:18px; border:1px solid var(--line-strong);
            background:linear-gradient(160deg,rgba(13,22,42,.99),rgba(7,13,27,.99));
            color:var(--text); box-shadow:0 24px 70px rgba(0,0,0,.55);
            padding:18px;
        }
        .room-email-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px}
        .room-email-title{font-size:15px;font-weight:800}
        .room-email-close{
            width:34px;height:34px;display:flex;align-items:center;justify-content:center;
            border-radius:10px;border:1px solid var(--line);background:rgba(255,255,255,.04);
            color:#cbd5e1;cursor:pointer;flex-shrink:0;
        }
        .room-email-close:hover{background:rgba(239,68,68,.14);color:#fecaca}
        .room-email-help{font-size:10.5px;line-height:1.5;color:var(--muted);margin:0 0 14px}
        .room-email-field{margin-bottom:12px}
        .room-email-label{display:block;font-size:10.5px;font-weight:700;color:#cbd5e1;margin-bottom:5px}
        .room-email-input,.room-email-textarea{
            width:100%;border-radius:10px;border:1px solid var(--line);
            background:rgba(255,255,255,.045);color:var(--text);
            font:inherit;font-size:12px;outline:none;padding:9px 10px;
        }
        .room-email-textarea{resize:vertical;min-height:76px}
        .room-email-input:focus,.room-email-textarea:focus{
            border-color:rgba(96,165,250,.55);box-shadow:0 0 0 3px rgba(59,130,246,.09)
        }
        .room-email-hint{font-size:9.5px;color:var(--muted-2);margin-top:5px}
        .room-email-msg{display:none;font-size:10.5px;line-height:1.45;margin:4px 0 10px}
        .room-email-msg.show{display:block}
        .room-email-msg.error{color:#fca5a5}
        .room-email-msg.success{color:#86efac}
        .room-email-actions{display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap}
        .room-email-action{
            border-radius:10px;padding:8px 13px;border:1px solid var(--line);
            font-size:11px;font-weight:700;cursor:pointer;color:#e2e8f0;
            background:rgba(255,255,255,.04);
        }
        .room-email-action.primary{border:none;color:#fff;background:linear-gradient(135deg,#2563eb,#0891b2)}
        .room-email-action:disabled{opacity:.55;cursor:not-allowed}

        .controls{
            flex-shrink:0; display:flex; align-items:center; justify-content:center; gap:8px;
            margin:0 12px 12px; padding:9px 14px; border-radius:18px; border:1px solid var(--line);
            background:rgba(6,11,22,.92); box-shadow:0 -6px 26px rgba(0,0,0,.2), var(--shadow-lg);
            backdrop-filter:blur(18px); overflow:hidden; scrollbar-width:none; min-width:0;
        }
        .controls::-webkit-scrollbar{display:none}
        .ctrl-btn{display:flex; flex-direction:column; align-items:center; gap:4px; min-width:0; width:52px; flex:0 1 52px; padding:4px 4px; border-radius:12px; cursor:pointer; user-select:none}
        .ctrl-btn:hover{background:rgba(255,255,255,.05)}
        .ctrl-icon{
            width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.045); border:1px solid var(--line); font-size:13px; position:relative;
            transition:transform .15s ease, background .15s ease, border-color .15s ease;
        }
        .ctrl-btn:hover .ctrl-icon{transform:translateY(-2px)}
        .ctrl-icon.active{background:linear-gradient(135deg,rgba(37,99,235,.4),rgba(8,145,178,.32)); border-color:rgba(96,165,250,.45)}
        .ctrl-icon.off{background:rgba(51,65,85,.7)}
        .ctrl-label{font-size:8.5px; color:var(--muted); font-weight:600}
        .ctrl-divider{width:1px; height:30px; background:var(--line); margin:0 2px; flex-shrink:0}
        .btn-end{width:38px; height:38px; border-radius:12px; border:none; background:linear-gradient(135deg,#ef4444,#b91c1c); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; cursor:pointer}
        .btn-end:hover{transform:translateY(-2px)}
        #chat-badge{position:absolute; top:-6px; right:-6px; background:var(--red); color:#fff; font-size:9px; font-weight:800; min-width:16px; height:16px; border-radius:99px; display:none; align-items:center; justify-content:center; padding:0 4px}

        #toast-stack{position:fixed; bottom:96px; left:50%; transform:translateX(-50%); z-index:999; display:flex; flex-direction:column; align-items:center; gap:8px; pointer-events:none}
        .toast{
            pointer-events:auto; display:flex; align-items:center; gap:10px; background:rgba(13,22,42,.94); backdrop-filter:blur(14px);
            border:1px solid var(--line-strong); color:#fff; padding:11px 18px; border-radius:14px; font-size:13px; font-weight:600;
            line-height:1.4; box-shadow:0 10px 30px rgba(0,0,0,.4); opacity:0; transform:translateY(16px) scale(.98);
            transition:opacity .25s ease, transform .25s ease; max-width:min(90vw,420px);
        }
        .toast.show{opacity:1; transform:translateY(0) scale(1)}
        .toast.leaving{opacity:0; transform:translateY(-6px) scale(.98)}
        .moderation-notice{
            position:fixed; top:80px; left:50%; transform:translateX(-50%); z-index:9999; background:#0f172a; color:#fff;
            padding:12px 20px; border-radius:14px; box-shadow:0 14px 40px rgba(0,0,0,.4); font-weight:700; font-size:13px;
            max-width:min(92vw,460px); text-align:center; opacity:0; transition:opacity .25s ease;
        }
        .moderation-notice.show{opacity:1}

        html,body{width:100%;height:100%;overflow:hidden}
        body{height:100dvh;min-height:100dvh;overflow:hidden}
        .header{flex:0 0 auto}
        .main{flex:1 1 0;min-height:0;height:auto !important;overflow:hidden}
        .video-area{min-height:0;padding:0 !important;overflow:hidden !important}
        .video-grid{
            width:100%;height:100%;min-height:0;overflow-y:auto !important;overflow-x:hidden !important;
            scrollbar-gutter:stable;overscroll-behavior:contain;display:grid;grid-auto-flow:row;
            grid-auto-rows:max-content !important;align-content:start;align-items:start;justify-items:stretch;
            row-gap:16px;column-gap:16px;padding:16px;
        }
        .video-tile{
            position:relative;width:100%;max-width:none;margin:0;min-width:0;
            min-height:220px !important;height:clamp(220px,18vw,300px) !important;
            aspect-ratio:auto !important;align-self:start;
        }
        #maximized-overlay .video-tile{width:100%;height:100% !important;min-height:0 !important;max-width:none;aspect-ratio:auto !important}

        @media(min-width:1201px){
            .video-grid{grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;padding:18px}
            #side-panel{height:100%;min-height:0;align-self:stretch}
        }
        @media(min-width:901px) and (max-width:1200px){
            .main{padding:10px; gap:10px}
            .video-grid{grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px;padding:16px}
            .video-tile{min-height:200px !important;height:clamp(200px,22vw,270px) !important}
            #side-panel{width:clamp(280px,31vw,340px);min-width:280px;max-width:360px;height:100%;min-height:0;align-self:stretch}
        }
        @media(max-width:900px){
            .header{flex-wrap:nowrap !important;row-gap:0;gap:8px;min-height:58px;padding:8px 10px}
            .header-left{flex:1 1 auto;min-width:0;gap:8px;overflow:hidden}
            .header-center{order:0 !important;width:auto !important;min-width:max-content;flex:0 0 auto;justify-content:center;padding:6px 10px;margin:0}
            .header-right{flex:0 0 auto;gap:7px}
            .meeting-title{max-width:24vw;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
            .main{flex-direction:column;padding:8px;gap:10px;min-height:0;overflow:hidden}
            .video-area{flex:1 1 0;min-height:0;width:100%}
            .video-grid{grid-template-columns:repeat(2,minmax(0,1fr));grid-auto-rows:max-content !important;gap:16px;padding:14px;overflow-y:auto !important}
            .video-tile{min-height:190px !important;height:clamp(190px,30vw,270px) !important}
            #side-panel{
                position:fixed;left:0;right:0;bottom:76px;transform:none;width:100% !important;min-width:0 !important;
                max-width:none !important;height:min(58dvh,540px);min-height:220px;max-height:calc(100dvh - 128px);
                z-index:55;border-radius:18px 18px 0 0;
            }
            .panel-drag-handle{display:flex}
        }
        @media(min-width:901px){.panel-drag-handle{display:none}}
        @media(max-width:640px){
            .header{padding:7px 8px;gap:6px;min-height:56px}
            .header-brand-text,.participants-count,.meeting-meta{display:none}
            .header-brand{padding-right:6px;gap:4px}
            .header-brand img{width:28px;height:28px}
            .live-badge{padding:4px 7px;font-size:9px;gap:4px}
            .meeting-title{max-width:22vw;font-size:12px}
            .header-center{padding:5px 8px;font-size:11px;gap:5px}
            .header-right{gap:6px}
            .btn-leave span,.btn-cancel span{display:none}
            .btn-leave,.btn-cancel{padding:0;width:36px;height:36px;border-radius:50%;justify-content:center}
            .main{padding:6px;gap:10px}
            .video-grid{grid-template-columns:minmax(0,1fr) !important;grid-auto-rows:max-content !important;row-gap:16px !important;column-gap:0 !important;padding:10px;overflow-y:auto !important}
            .video-grid:has(>.video-tile:only-child){grid-template-columns:minmax(0,1fr) !important}
            .video-tile{width:100%;max-width:none;min-height:220px !important;height:clamp(220px,58vw,330px) !important;aspect-ratio:auto !important;border-radius:14px;margin:0 !important}
            .tile-info{left:0;right:0;bottom:0;padding:9px 11px}
            .controls{gap:2px;padding:7px 8px;margin:0 6px 6px}
            .ctrl-btn{min-width:0;width:42px;flex-basis:42px;padding:3px 2px}
            #side-panel{bottom:70px;width:100% !important;min-width:0 !important;max-width:none !important;border-left:none;border-right:none;border-bottom:none}
        }
        @media(max-width:420px){
            .header{gap:4px; padding:6px}
            .header-left{gap:5px}
            .header-brand{padding-right:3px}
            .live-badge{padding:4px 6px}
            .meeting-title{max-width:18vw; font-size:11.5px}
            .header-center{padding:5px 6px; font-size:10.5px}
            .btn-leave,.btn-cancel{width:34px; height:34px}
            .main{padding:5px}
            .video-grid{padding:8px; row-gap:14px !important}
            .video-tile{min-height:210px !important;height:clamp(210px,62vw,300px) !important}
            .avatar-circle{width:58px; height:58px; font-size:20px}
            .tile-name{font-size:11px}
            .role-badge{font-size:7px}
            .controls{padding:6px 3px; gap:0;margin-left:4px;margin-right:4px}
            .ctrl-btn{min-width:0;width:38px;flex-basis:38px;padding:2px 1px;gap:3px}
            .ctrl-icon,.btn-end{width:32px;height:32px;border-radius:10px;font-size:12px}
            .ctrl-label{font-size:7.5px;white-space:nowrap}
            #side-panel{bottom:66px}
        }
        @media(max-width:900px){
            .controls{gap:2px;padding:7px 5px}
            .ctrl-btn{width:44px;flex-basis:44px;padding-left:2px;padding-right:2px}
            .ctrl-icon,.btn-end{width:34px;height:34px}
            .ctrl-label{font-size:8px}
            .ctrl-divider{margin:0 1px}
        }
        @media(max-width:520px){
            .controls{gap:0;padding:5px 2px}
            .ctrl-btn{width:36px;flex-basis:36px;padding:2px 0}
            .ctrl-icon,.btn-end{width:30px;height:30px;border-radius:9px}
            .ctrl-label{font-size:7px}
            .ctrl-divider{height:25px;margin:0}
        }
        @media(max-width:640px){
            #transcript-btn,#transcriptBtn,[data-panel="transcript"],[data-tab="transcript"],
            button[aria-label*="transcript" i],button[title*="transcript" i],
            .ctrl-btn[onclick*="transcript"],.panel-tabbtn[data-tab="transcript"],#tab-transcript{
                display:none !important;
            }
        }
    </style>
</head>
@php
    $organizer   = $meeting->organizer;
    $orgInitials = strtoupper(substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1));
    $palette     = ['#3b82f6,#06b6d4', '#8b5cf6,#ec4899', '#22c55e,#06b6d4', '#f59e0b,#ef4444', '#64748b,#334155', '#ec4899,#f59e0b'];
    $userInitials = strtoupper(substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' ') ?: ' ', 1, 1));
    $tz = $meeting->timezone ?? 'Asia/Karachi';
    $meetingEnd = null;
    if (!empty($meeting->end_time)) {
        $meetingEnd = \Carbon\Carbon::parse($meeting->end_time, $tz)->utc()->toIso8601String();
    } else {
        $durationMinutes = $meeting->duration_minutes ?? $meeting->duration ?? null;
        if ($durationMinutes) {
            $startForCalc = \Carbon\Carbon::parse(
                $meeting->date . ' ' . $meeting->time,
                $tz
            );
            $meetingEnd = $startForCalc
                ->copy()
                ->addMinutes((int) $durationMinutes)
                ->utc()
                ->toIso8601String();
        }
    }
@endphp
<body>

<div class="header">
    <div class="header-left">
        <div class="header-brand">
            <img src="{{ asset('images/s-logo.png') }}" alt="logo">
            <div class="header-brand-text">
                <div class="name">SmartMeet</div>
                <div class="tag">Meeting Suite</div>
            </div>
        </div>
        <div class="live-badge"><div class="live-dot"></div>LIVE</div>
        <div class="header-meeting-info">
            <div class="meeting-title">{{ $meeting->title }}</div>
            <div class="meeting-meta">
                <span><i class="fa fa-users"></i> <span data-total-count>{{ $meeting->participants->count() + 1 }}</span> Participants</span>
                <span>·</span>
                <span>{{ $tz }}</span>
            </div>
        </div>
    </div>
    <div class="header-center"><i class="fa fa-clock"></i><span id="timer">00:00:00</span></div>
    <div class="header-right">
        <button class="btn-cancel" onclick="endMeeting()"><i class="fa-solid fa-circle-stop"></i><span>End Meeting</span></button>
        <button class="btn-leave" onclick="safeLeaveMeeting()"><i class="fa fa-phone-slash"></i><span>Leave</span></button>
    </div>
</div>
<form id="cancel-form" action="{{ route('organizer.meetings.cancel', $meeting) }}" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<div class="main">
    <div class="video-area">
        <div class="video-grid" id="video-grid"></div>
        <div id="maximized-overlay">
            <button class="maximize-close-btn" onclick="restoreMaximized()"><i class="fa fa-compress"></i></button>
        </div>
    </div>

    <div id="side-panel">
        <div class="panel-drag-handle" id="panel-drag-handle" title="Drag to resize"></div>
        <div class="panel-tabbar">
            <button class="panel-tabbtn" data-tab="transcript" onclick="toggleSidePanel('transcript')"><i class="fa fa-closed-captioning"></i> Transcript</button>
            <button class="panel-tabbtn" data-tab="chat" onclick="toggleSidePanel('chat')">Chat</button>
            <button class="panel-tabbtn" data-tab="people" onclick="toggleSidePanel('people')">People</button>
        </div>
        <div class="panel-body">
            <div id="tab-transcript" style="display:none; flex-direction:column; flex:1; overflow:hidden;">
                <div class="transcript-body" id="transcript-body">
                    <div class="empty-note" data-empty>Live captions will appear here as people speak.</div>
                </div>
                <div class="lang-row">
                    <span style="font-size:10px;color:var(--muted-2)">Live captions</span>
                    <button id="lang-toggle-btn" onclick="toggleTranscriptLanguage()">🌐 English</button>
                </div>
                <div class="listening-indicator" id="listening-indicator"><div class="listening-dot"></div><span id="listening-text">Listening…</span></div>
            </div>
            <div id="tab-chat" style="display:none; flex-direction:column; flex:1; min-height:0; overflow:hidden;">
                <div class="chat-body" id="chat-body">
                    <div class="empty-note" data-empty>No messages yet — say hello 👋</div>
                </div>
                <div class="chat-typing-indicator" id="chat-typing-indicator" aria-live="polite">
                    <span class="chat-typing-dots"><span></span><span></span><span></span></span>
                    <span id="chat-typing-text"></span>
                </div>
                <div class="chat-input-area">
                    <input class="chat-input" id="chat-input" placeholder="Type a message…" onkeydown="if(event.key==='Enter') sendChat()">
                    <button class="btn-send" onclick="sendChat()"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>
            <div id="tab-people" style="display:none; flex-direction:column; flex:1; min-height:0; overflow:hidden;">
                <div class="room-invite-card">
                    <div class="room-invite-title"><i class="fa-solid fa-user-plus"></i> Invite people</div>
                    <div class="room-invite-note">Invite someone without leaving the live meeting. Copy the link for WhatsApp/SMS, or send it by email.</div>
                    <div class="room-invite-actions">
                        <button type="button" class="room-invite-btn primary" onclick="copyMeetingInviteLink()">
                            <i class="fa-solid fa-link"></i> Copy link
                        </button>
                        <button type="button" class="room-invite-btn" onclick="openRoomEmailInvite()">
                            <i class="fa-regular fa-envelope"></i> Email invite
                        </button>
                    </div>
                    <div class="room-invite-link" id="room-invite-link-preview" title="Meeting invite link"></div>
                </div>
                <div class="people-scroll-shell" id="people-scroll-shell">
                    <div class="people-body" id="people-body"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="controls">
    <div class="ctrl-btn" onclick="safeToggleMic()"><div class="ctrl-icon off" id="ctrl-mic"><i class="fa fa-microphone-slash"></i></div><span class="ctrl-label">Mic</span></div>
    <div class="ctrl-btn" onclick="safeToggleCamera()"><div class="ctrl-icon off" id="ctrl-camera"><i class="fa fa-video-slash"></i></div><span class="ctrl-label">Camera</span></div>
    <div class="ctrl-btn" onclick="toggleScreenShare()"><div class="ctrl-icon" id="ctrl-screen"><i class="fa-solid fa-display"></i></div><span class="ctrl-label" id="ctrl-screen-label">Share</span></div>
    <div class="ctrl-divider"></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('transcript')"><div class="ctrl-icon" id="ctrl-transcript"><i class="fa fa-closed-captioning"></i></div><span class="ctrl-label">Captions</span></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('chat')"><div class="ctrl-icon" id="ctrl-chat"><i class="fa fa-comment"></i><span id="chat-badge">0</span></div><span class="ctrl-label">Chat</span></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('people')"><div class="ctrl-icon" id="ctrl-people"><i class="fa fa-users"></i></div><span class="ctrl-label">People</span></div>
    <div class="ctrl-btn" onclick="muteAllParticipants()"><div class="ctrl-icon" id="ctrl-mute-all"><i class="fa-solid fa-volume-xmark"></i></div><span class="ctrl-label">Mute all</span></div>
    <div class="ctrl-divider"></div>
    <div class="ctrl-btn"><button class="btn-end" style="background:linear-gradient(135deg,#7f1d1d,#450a0a);" onclick="endMeeting()" title="End meeting for everyone"><i class="fa-solid fa-circle-stop"></i></button><span class="ctrl-label" style="color:var(--red);white-space:nowrap;">End</span></div>
    <div class="ctrl-btn"><button class="btn-end" onclick="safeLeaveMeeting()"><i class="fa fa-phone-slash"></i></button><span class="ctrl-label" style="color:var(--red);">Leave</span></div>
</div>

<div id="room-email-overlay" class="room-email-overlay" aria-hidden="true">
    <div class="room-email-dialog" role="dialog" aria-modal="true" aria-labelledby="room-email-title">
        <div class="room-email-head">
            <div class="room-email-title" id="room-email-title">Send email invitation</div>
            <button type="button" class="room-email-close" onclick="closeRoomEmailInvite()" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p class="room-email-help">
            Send an invitation without leaving the live meeting.
            Existing users can join this meeting; new users can register first.
        </p>
        <form id="room-email-form" onsubmit="sendRoomEmailInvite(event)">
            <div class="room-email-field">
                <label class="room-email-label" for="room-email-emails">Emails</label>
                <textarea id="room-email-emails" class="room-email-textarea" rows="2" placeholder="email1@example.com, email2@example.com"></textarea>
                <div class="room-email-hint">Separate multiple email addresses with commas.</div>
            </div>
            <div class="room-email-field">
                <label class="room-email-label" for="room-email-subject">Subject</label>
                <input id="room-email-subject" class="room-email-input" type="text">
            </div>
            <div class="room-email-field">
                <label class="room-email-label" for="room-email-message">Message (optional)</label>
                <textarea id="room-email-message" class="room-email-textarea" rows="3" placeholder="Hello, please join our meeting..."></textarea>
            </div>
            <div id="room-email-msg" class="room-email-msg"></div>
            <div class="room-email-actions">
                <button type="button" class="room-email-action" onclick="closeRoomEmailInvite()">Cancel</button>
                <button type="submit" class="room-email-action primary" id="room-email-send-btn">
                    <i class="fa-regular fa-envelope"></i> Send
                </button>
            </div>
        </form>
    </div>
</div>

<div id="toast-stack"></div>

<script>
    const IS_ORGANIZER   = true;
    const MEETING_ID      = "{{ $meeting->id }}";
    const MEETING_TITLE   = @json($meeting->title);
    const INVITE_LINK     = @json(route('meetings.join.link', $meeting->unique_code));
    const MY_USER_ID      = "{{ auth()->id() }}";
    const ORGANIZER_ID    = String(MY_USER_ID);
    const MY_NAME         = @json(auth()->user()->name);
    const MY_INITIALS     = @json($userInitials);
    const MY_AVATAR_URL   = @json($myAvatarUrl ?? null);
    const LIVEKIT_TOKEN_URL = @json(route('organizer.meetings.livekit-token', $meeting));
    const SIGNAL_URL      = @json(route('organizer.meetings.signal', $meeting));
    const TRANSCRIPT_URL  = @json(route('organizer.meetings.transcript', $meeting));
    const MARK_LEFT_URL   = @json(route('organizer.meetings.markLeft', $meeting));
    const COMPLETE_BY_TIME_URL = @json(route('organizer.meetings.completeByTime', $meeting));
    const LEAVE_URL       = @json(route('organizer.meetings.index'));
    const CANCEL_URL      = @json(route('organizer.meetings.cancel', $meeting));
    const END_MEETING_URL = @json(route('organizer.meetings.end', $meeting));
    const MODERATION_URL  = @json(route('organizer.meetings.moderate', $meeting));
    const CANCELLED_PAGE_URL = @json(route('meetings.cancelled', $meeting));
    const ENDED_PAGE_URL     = @json(route('meetings.ended', $meeting));
    const CSRF            = @json(csrf_token());
    const ALL_PARTICIPANTS = @json($allParticipants);
    const MEETING_END_TIME   = @json($meetingEnd);
    const ACTUAL_START = @json($meeting->actual_start ? \Carbon\Carbon::parse($meeting->actual_start)->utc()->toIso8601String() : now()->utc()->toIso8601String());
    const COLORS = ['#3b82f6,#06b6d4','#8b5cf6,#ec4899','#22c55e,#06b6d4','#f59e0b,#ef4444','#64748b,#334155','#ec4899,#f59e0b'];

    const knownParticipants = {};
    const raisedHands = new Set();
    ALL_PARTICIPANTS.forEach(p => { knownParticipants[String(p.userId)] = { name: p.name, initials: p.initials, avatarUrl: p.avatarUrl || null, isOrganizer: false, hasJoined: Boolean(p.hasJoined) }; });

    const onlineUsers   = new Set([String(MY_USER_ID)]);
    const leftUsers     = new Set();
    const micStatus     = {};
    const camStatus     = {};
    const receivedSignalIds = new Set();
    let isMicOn = false, isCameraOn = false;

    let isScreenSharing = false, screenShareBusy = false;
    let maximizedUserId = null, maximizedPlaceholder = null;
    let activeTab = null, panelOpen = false, unreadChat = 0;
    let leftNotified = false, autoEndTimer = null, autoEndTriggered = false;

    function colorFor(uid, isOrganizer){ if(isOrganizer) return COLORS[0]; let h=0; for(const c of String(uid)) h=(h*31+c.charCodeAt(0))>>>0; return COLORS[1+(h%(COLORS.length-1))]; }
    function escapeHtml(t){ const d=document.createElement('div'); d.textContent=String(t??''); return d.innerHTML; }
    function initialsOf(name){ const parts=String(name||'').trim().split(/\s+/); if(!parts.length) return '?'; return (parts[0][0]+(parts.length>1?parts[parts.length-1][0]:'')).toUpperCase(); }

    function avatarContent(avatarUrl, initials){
        const safeInitials=escapeHtml(initials||'?');
        if(!avatarUrl) return safeInitials;
        const safeUrl=escapeHtml(String(avatarUrl));
        return `<img src="${safeUrl}" alt="" loading="eager" onerror="const p=this.parentElement; this.remove(); if(p && !p.textContent.trim()) p.textContent='${safeInitials}'">`;
    }

    async function copyMeetingInviteLink(){
        const link=String(INVITE_LINK||'').trim();
        if(!link){ showToast('Invite link is unavailable.'); return; }
        try{
            if(navigator.clipboard?.writeText){
                await navigator.clipboard.writeText(link);
            }else{
                const input=document.createElement('textarea');
                input.value=link;
                input.setAttribute('readonly','');
                input.style.position='fixed';
                input.style.opacity='0';
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                input.remove();
            }
            showToast('🔗 Invite link copied.');
        }catch(e){
            showToast('Could not copy automatically. Select the link from People panel.');
        }
    }

    function initRoomInviteUI(){
        const preview=document.getElementById('room-invite-link-preview');
        if(preview) preview.textContent=INVITE_LINK;
    }

    function setRoomEmailMessage(message='', type=''){
        const box=document.getElementById('room-email-msg');
        if(!box) return;
        box.textContent=message;
        box.className='room-email-msg';
        if(message){
            box.classList.add('show');
            if(type) box.classList.add(type);
        }
    }

    function openRoomEmailInvite(){
        const overlay=document.getElementById('room-email-overlay');
        const subject=document.getElementById('room-email-subject');
        const message=document.getElementById('room-email-message');
        const emails=document.getElementById('room-email-emails');
        if(!overlay) return;
        if(subject) subject.value=`You're invited: ${MEETING_TITLE}`;
        if(message) message.value='';
        if(emails) emails.value='';
        setRoomEmailMessage('');
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden','false');
        setTimeout(()=>emails?.focus(),30);
    }

    function closeRoomEmailInvite(){
        const overlay=document.getElementById('room-email-overlay');
        const form=document.getElementById('room-email-form');
        overlay?.classList.remove('open');
        overlay?.setAttribute('aria-hidden','true');
        form?.reset();
        setRoomEmailMessage('');
    }

    let roomEmailInviteSending=false;
    async function sendRoomEmailInvite(event){
        event.preventDefault();
        if(roomEmailInviteSending) return;
        const emails=document.getElementById('room-email-emails')?.value.trim() || '';
        const subject=document.getElementById('room-email-subject')?.value.trim() || '';
        const message=document.getElementById('room-email-message')?.value.trim() || '';
        const sendBtn=document.getElementById('room-email-send-btn');
        if(!emails){
            setRoomEmailMessage('At least one email is required.','error');
            return;
        }
        roomEmailInviteSending=true;
        if(sendBtn){
            sendBtn.disabled=true;
            sendBtn.dataset.originalHtml=sendBtn.innerHTML;
            sendBtn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
        }
        setRoomEmailMessage('Sending invitation…','success');
        try{
            const response=await fetch(`/organizer/meetings/${encodeURIComponent(MEETING_ID)}/send-invite`,{
                method:'POST',
                credentials:'same-origin',
                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-Requested-With':'XMLHttpRequest',
                    'X-CSRF-TOKEN':CSRF
                },
                body:JSON.stringify({emails,subject,message})
            });
            let data={};
            try{ data=await response.json(); }catch(e){}
            if(!response.ok) throw new Error(data.message || `Email invite failed (HTTP ${response.status})`);
            setRoomEmailMessage(data.message || 'Invitation sent successfully.','success');
            showToast('✉️ Invitation sent.');
            setTimeout(closeRoomEmailInvite,1200);
        }catch(error){
            setRoomEmailMessage(error?.message || 'Failed to send invitation. Please try again.','error');
        }finally{
            roomEmailInviteSending=false;
            if(sendBtn){
                sendBtn.disabled=false;
                sendBtn.innerHTML=sendBtn.dataset.originalHtml || '<i class="fa-regular fa-envelope"></i> Send';
            }
        }
    }

    document.addEventListener('click',(event)=>{
        const overlay=document.getElementById('room-email-overlay');
        if(overlay && event.target===overlay) closeRoomEmailInvite();
    });

    document.addEventListener('keydown',(event)=>{
        if(event.key==='Escape'){
            const overlay=document.getElementById('room-email-overlay');
            if(overlay?.classList.contains('open')) closeRoomEmailInvite();
        }
    });

    const recentToasts = new Set();
    function showToast(msg){
        if(recentToasts.has(msg)) return;
        recentToasts.add(msg); setTimeout(()=>recentToasts.delete(msg),5200);
        const stack=document.getElementById('toast-stack'); if(!stack) return;
        const el=document.createElement('div'); el.className='toast'; el.textContent=msg;
        stack.appendChild(el);
        requestAnimationFrame(()=>el.classList.add('show'));
        setTimeout(()=>{ el.classList.remove('show'); el.classList.add('leaving'); setTimeout(()=>el.remove(),260); },4500);
    }

    function showModerationNotice(msg){
        const old=document.getElementById('mod-notice'); if(old) old.remove();
        const el=document.createElement('div'); el.id='mod-notice'; el.className='moderation-notice'; el.textContent=msg;
        document.body.appendChild(el);
        requestAnimationFrame(()=>el.classList.add('show'));
        setTimeout(()=>el.classList.remove('show'),3200);
        setTimeout(()=>el.remove(),3600);
    }

    let seconds = Math.max(0, Math.floor((Date.now()-new Date(ACTUAL_START).getTime())/1000));
    const meetingClockInterval=setInterval(()=>{
        seconds++;
        const h=String(Math.floor(seconds/3600)).padStart(2,'0');
        const m=String(Math.floor((seconds%3600)/60)).padStart(2,'0');
        const s=String(seconds%60).padStart(2,'0');
        const el=document.getElementById('timer'); if(el) el.textContent=`${h}:${m}:${s}`;
    },1000);

    function scheduleAutoEnd(){
        if(!MEETING_END_TIME) return;
        const msLeft = new Date(MEETING_END_TIME).getTime()-Date.now();
        if(msLeft<=0){ triggerAutoEnd(); return; }
        autoEndTimer = setTimeout(triggerAutoEnd, msLeft);
    }

    async function triggerAutoEnd(){
        if(autoEndTriggered || endingMeeting) return;
        autoEndTriggered=true;
        let finalStatus='';
        try{
            const response=await fetch(COMPLETE_BY_TIME_URL,{
                method:'POST',
                credentials:'same-origin',
                headers:{
                    'Accept':'application/json',
                    'X-Requested-With':'XMLHttpRequest',
                    'X-CSRF-TOKEN':CSRF
                },
                keepalive:true
            });
            const data=await response.json().catch(()=>({}));
            finalStatus=String(data.status||'');
        }catch(e){}

        if(finalStatus==='ended'){ window.location.href=ENDED_PAGE_URL; return; }
        if(finalStatus==='cancelled'){ window.location.href=CANCELLED_PAGE_URL; return; }
        showToast('⏰ Meeting time has ended.');
        setTimeout(()=>{ cleanup(); window.location.href=LEAVE_URL; },1800);
    }

    function updateOnlineCount(){ document.querySelectorAll('[data-online-count]').forEach(el=>el.textContent=onlineUsers.size); }
    function markOnline(uid){
        uid=String(uid);
        onlineUsers.add(uid);
        if(knownParticipants[uid]) knownParticipants[uid].hasJoined=true;
        updateOnlineCount();
        renderPersonRow(uid);
    }

    function markOffline(uid){
        uid=String(uid);
        onlineUsers.delete(uid);
        if(knownParticipants[uid]) knownParticipants[uid].hasJoined=false;
        updateOnlineCount();
        renderPersonRow(uid);
    }

    function markUserLeft(uid){
        uid=String(uid);
        leftUsers.add(uid);
        onlineUsers.delete(uid);
        if(knownParticipants[uid]) knownParticipants[uid].hasJoined=false;
        updateOnlineCount();
        renderPersonRow(uid);
    }

    function renderPeopleList(){
        const body=document.getElementById('people-body'); if(!body) return;
        body.innerHTML='';
        const ids=Object.keys(knownParticipants);
        if(!ids.includes(String(MY_USER_ID))) ids.unshift(String(MY_USER_ID));
        ids.sort((a,b)=>{
            if(a===String(MY_USER_ID)) return -1;
            if(b===String(MY_USER_ID)) return 1;
            const ao=onlineUsers.has(String(a)) ? 0 : 1;
            const bo=onlineUsers.has(String(b)) ? 0 : 1;
            if(ao!==bo) return ao-bo;
            return String(knownParticipants[a]?.name||'').localeCompare(String(knownParticipants[b]?.name||''));
        });
        ids.forEach(uid=>renderPersonRow(String(uid)));
    }

    function flashParticipantMediaState(uid, type){
        uid=String(uid);
        const row=document.getElementById('person-row-'+uid);
        if(!row) return;
        const selector=type==='camera' ? '[data-media-action="camera"]' : '[data-media-action="mic"]';
        const button=row.querySelector(selector);
        if(!button) return;
        button.classList.remove('media-state-pulse');
        void button.offsetWidth;
        button.classList.add('media-state-pulse');
        setTimeout(()=>button.classList.remove('media-state-pulse'),480);
    }

    function renderPersonRow(uid){
        uid=String(uid);
        const body=document.getElementById('people-body'); if(!body) return;
        const isMe = uid===String(MY_USER_ID);
        const info = isMe ? { name: MY_NAME, initials: MY_INITIALS, avatarUrl: MY_AVATAR_URL, isOrganizer: true } : knownParticipants[uid];
        if(!info) return;

        const isLeft = !isMe && leftUsers.has(uid);
        const isOnline = !isLeft && (isMe || onlineUsers.has(uid));
        const muted = micStatus[uid] !== false;
        const cameraOn = camStatus[uid] === true;

        let row=document.getElementById('person-row-'+uid);
        if(!row){
            row=document.createElement('div');
            row.id='person-row-'+uid;
            body.appendChild(row);
        }

        row.className='person-row '+(isOnline?'joined':'pending');
        const color=colorFor(uid, info.isOrganizer);
        const canModerate = IS_ORGANIZER && !isMe && isOnline;
        const presenceLabel = isLeft ? 'Left' : (isOnline ? 'Joined' : 'Not joined yet');

        row.innerHTML = `
        <div class="person-avatar" style="background:linear-gradient(135deg,${color})">${avatarContent(info.avatarUrl,info.initials||initialsOf(info.name))}</div>
        <div class="person-info">
            <div class="person-name">${escapeHtml(info.name)}${isMe?' <span style="color:var(--blue);font-weight:600;">(You)</span>':''}${info.isOrganizer?'<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>':''}</div>
            <div class="person-status ${isOnline?'on':''}" ${isLeft?'style="color:#fca5a5"':''}>${info.isOrganizer?'Organizer':'Participant'} • ${presenceLabel}</div>
        </div>
        ${canModerate ? `<div class="person-actions">
            <button class="person-action ${muted?'is-off':'is-on'}" data-media-action="mic" onclick="toggleParticipantMic('${uid}')" title="${muted?'Unmute participant':'Mute participant'}">
                <i class="fa-solid ${muted?'fa-microphone-slash':'fa-microphone'}"></i>
            </button>
            ${cameraOn ? `
            <button class="person-action is-on" data-media-action="camera" onclick="toggleParticipantCamera('${uid}')" title="Turn camera off">
                <i class="fa-solid fa-video"></i>
            </button>` : ''}
            <button class="person-action remove" onclick="restrictParticipant('${uid}')" title="Restrict participant">
                <i class="fa-solid fa-user-xmark"></i>
            </button>
        </div>` : ''}
        ${raisedHands.has(uid) ? '<i class="fa-solid fa-hand people-hand" title="Hand raised"></i>' : ''}
        <span class="person-dot ${isOnline?'on':''}" ${isLeft?'style="background:#ef4444"':''}></span>`;
    }

    async function toggleParticipantMic(uid){
        uid=String(uid);
        if(!onlineUsers.has(uid)) return;
        const info=knownParticipants[uid];
        const name=info?.name || 'Participant';

        if(micStatus[uid] !== false){
            showToast(`🔇 ${name} is already muted.`);
            return;
        }

        const delivered=await sendSignal(uid,'chat',{
            smartmeetControl:'force-mute',
            userId:uid,
            organizerId:MY_USER_ID,
            text:''
        });

        if(!delivered){
            showToast(`Could not mute ${name}. Please try again.`);
            return;
        }

        micStatus[uid]=true;
        const micOffEl=document.getElementById('micoff-'+uid);
        if(micOffEl) micOffEl.style.display='flex';
        renderPersonRow(uid);
        flashParticipantMediaState(uid,'mic');

        await sendSignal('all','chat',{
            smartmeetControl:'moderation-notice',
            action:'mute',
            userId:uid,
            name,
            organizerId:MY_USER_ID,
            text:''
        });

        showToast(`🔇 Muted ${name}.`);
    }

    async function muteAllParticipants(){
        const activeIds=[...onlineUsers].map(String).filter(uid=>uid!==String(MY_USER_ID));
        if(!activeIds.length){
            showToast('There are no active participants to mute.');
            return;
        }

        const unmutedIds=activeIds.filter(uid=>micStatus[uid]===false);
        if(!unmutedIds.length){
            showToast('🔇 All active participants are already muted.');
            return;
        }

        await Promise.allSettled(
            unmutedIds.map(uid=>sendSignal(uid,'chat',{
                smartmeetControl:'force-mute',
                userId:uid,
                organizerId:MY_USER_ID,
                text:''
            }))
        );

        unmutedIds.forEach(uid=>{
            micStatus[uid]=true;
            const micOffEl=document.getElementById('micoff-'+uid);
            if(micOffEl) micOffEl.style.display='flex';
            renderPersonRow(uid);
            flashParticipantMediaState(uid,'mic');
        });

        showToast(`🔇 Muted ${unmutedIds.length} participant(s).`);
    }

    async function moderateParticipant(uid, action){
        uid=String(uid);
        try{
            const res=await fetch(MODERATION_URL,{
                method:'POST',
                headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
                body:JSON.stringify({user_id:uid,action})
            });
            const data=await res.json().catch(()=>({}));
            if(!res.ok) throw new Error(data.message||`HTTP ${res.status}`);
            return true;
        }catch(e){
            showToast('Moderation action failed. Please try again.');
            return false;
        }
    }

    async function toggleParticipantCamera(uid){
        uid=String(uid);
        if(!onlineUsers.has(uid)) return;
        const info=knownParticipants[uid];
        const name=info?.name || 'Participant';

        const ok=await moderateParticipant(uid,'camera-off');
        if(!ok) return;

        camStatus[uid]=false;
        const remoteVideo=document.getElementById('rvideo-'+uid);
        const remoteAvatar=document.getElementById('avatar-'+uid);
        if(remoteVideo) remoteVideo.style.display='none';
        if(remoteAvatar) remoteAvatar.style.display='flex';
        renderPersonRow(uid);
        flashParticipantMediaState(uid,'camera');

        await sendSignal('all','chat',{
            smartmeetControl:'moderation-notice',
            action:'camera-off',
            userId:uid,
            name,
            organizerId:MY_USER_ID,
            text:''
        });

        showToast(`📷 Turned off ${name}'s camera.`);
    }

    async function restrictParticipant(uid){
        uid=String(uid);
        const info=knownParticipants[uid];
        const name=info?.name || 'this participant';
        if(!window.confirm(`Restrict ${name} from this meeting?`)) return;

        await sendSignal(uid,'chat',{
            smartmeetControl:'participant-removed',
            userId:uid,
            organizerId:MY_USER_ID,
            text:''
        });

        const ok=await moderateParticipant(uid,'remove');
        if(!ok) return;

        raisedHands.delete(uid);
        micStatus[uid]=true;
        camStatus[uid]=false;
        markUserLeft(uid);
        removeParticipantTile(uid,false);
        document.getElementById('person-row-'+uid)?.remove();
        delete knownParticipants[uid];
        renderPeopleList();
        showToast(`🚫 ${escapeHtml(name)} has been restricted.`);
    }

    function renderMyOwnTile(){
        const grid=document.getElementById('video-grid');
        const tile=document.createElement('div');
        tile.className='video-tile'; tile.id='tile-'+MY_USER_ID;
        tile.innerHTML = `
        <div class="video-placeholder">
            <video id="localVideo" autoplay muted playsinline class="mirrored" style="display:none;"></video>
            <div class="avatar-circle" id="avatar-${MY_USER_ID}" style="background:linear-gradient(135deg,${COLORS[1]})">${avatarContent(MY_AVATAR_URL,MY_INITIALS)}</div>
            <button class="tile-expand-btn" onclick="toggleMaximize('${MY_USER_ID}')"><i class="fa fa-expand" id="expand-icon-${MY_USER_ID}"></i></button>
        </div>
        <div class="tile-info">
            <div class="tile-name"><i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i> ${escapeHtml(MY_NAME)}<span class="role-badge organizer">You</span></div>
            <div class="tile-icons">
                <div class="speaking-indicator" id="speaking-${MY_USER_ID}" style="display:none;"><div class="speaking-bar"></div><div class="speaking-bar"></div><div class="speaking-bar"></div></div>
                <div class="mic-off" id="micoff-${MY_USER_ID}" style="display:flex;"><i class="fa fa-microphone-slash"></i></div>
            </div>
        </div>`;
        grid.appendChild(tile);
    }

    function syncRaisedHandBadge(uid){
        uid=String(uid);
        const tile=document.getElementById('tile-'+uid);
        if(!tile) return;
        let badge=tile.querySelector('.raised-hand-badge');
        if(raisedHands.has(uid)){
            if(!badge){
                badge=document.createElement('div');
                badge.className='raised-hand-badge';
                badge.innerHTML='<i class="fa-solid fa-hand"></i><span>Hand raised</span>';
                tile.appendChild(badge);
            }
        }else{
            badge?.remove();
        }
    }

    function addParticipantTile(uid, name, initials, isOrganizer){
        uid=String(uid);
        if(uid===String(MY_USER_ID) || leftUsers.has(uid)) return;
        if(document.getElementById('tile-'+uid)) return;
        const color=colorFor(uid, isOrganizer);
        const grid=document.getElementById('video-grid');
        const startsMuted = micStatus[uid] !== false;
        const cameraOn = camStatus[uid] === true;
        const tile=document.createElement('div');
        tile.className='video-tile'; tile.id='tile-'+uid;
        tile.innerHTML = `
        <div class="video-placeholder">
            <video id="rvideo-${uid}" autoplay playsinline style="display:${cameraOn?'block':'none'};"></video>
            <div class="avatar-circle" id="avatar-${uid}" style="background:linear-gradient(135deg,${color});display:${cameraOn?'none':'flex'};">${avatarContent(knownParticipants[uid]?.avatarUrl,initials)}</div>
            <button class="tile-expand-btn" onclick="toggleMaximize('${uid}')"><i class="fa fa-expand" id="expand-icon-${uid}"></i></button>
        </div>
        <div class="tile-info">
            <div class="tile-name">${isOrganizer?'<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i> ':''}${escapeHtml(name)}<span class="role-badge ${isOrganizer?'organizer':'participant'}">${isOrganizer?'Organizer':'Participant'}</span></div>
            <div class="tile-icons">
                <div class="speaking-indicator" id="speaking-${uid}" style="display:none;"><div class="speaking-bar"></div><div class="speaking-bar"></div><div class="speaking-bar"></div></div>
                <div class="mic-off" id="micoff-${uid}" style="display:${startsMuted?'flex':'none'};"><i class="fa fa-microphone-slash"></i></div>
            </div>
        </div>`;
        if(isOrganizer) grid.prepend(tile); else grid.appendChild(tile);
        syncRaisedHandBadge(uid);
    }

    function removeParticipantTile(uid, announce){
        uid=String(uid);
        if(uid===String(maximizedUserId)){
            document.getElementById('maximized-overlay')?.classList.remove('active');
            maximizedPlaceholder?.remove(); maximizedPlaceholder=null; maximizedUserId=null;
        }
        document.getElementById('tile-'+uid)?.remove();
        markOffline(uid);
        if(announce){
            const info=knownParticipants[uid];
            showToast(`👋 ${escapeHtml(info?info.name:'A participant')} has left the meeting.`);
        }
    }

    function toggleMaximize(uid){
        uid=String(uid);
        const overlay=document.getElementById('maximized-overlay'); if(!overlay) return;
        if(maximizedUserId===uid){ restoreMaximized(); return; }
        if(maximizedUserId) restoreMaximized();
        const tile=document.getElementById('tile-'+uid); if(!tile) return;
        maximizedPlaceholder=document.createComment('ph-'+uid);
        tile.parentNode.insertBefore(maximizedPlaceholder, tile);
        overlay.appendChild(tile); overlay.classList.add('active'); tile.classList.add('maximized');
        maximizedUserId=uid; updateExpandIcons();
    }

    function restoreMaximized(){
        if(!maximizedUserId) return;
        const tile=document.getElementById('tile-'+maximizedUserId);
        const overlay=document.getElementById('maximized-overlay');
        const grid=document.getElementById('video-grid');
        if(tile){
            if(maximizedPlaceholder?.parentNode){ maximizedPlaceholder.parentNode.insertBefore(tile, maximizedPlaceholder); maximizedPlaceholder.remove(); }
            else grid?.appendChild(tile);
            tile.classList.remove('maximized');
        }
        overlay?.classList.remove('active');
        maximizedPlaceholder=null; maximizedUserId=null; updateExpandIcons();
    }

    function updateExpandIcons(){
        document.querySelectorAll('[id^="expand-icon-"]').forEach(icon=>{
            const id=icon.id.replace('expand-icon-','');
            icon.className = maximizedUserId===id ? 'fa fa-compress' : 'fa fa-expand';
        });
    }

    function setupPanelResize(){
        const panel=document.getElementById('side-panel');
        const handle=document.getElementById('panel-drag-handle');
        if(!panel || !handle || handle.dataset.bound) return;
        handle.dataset.bound='1';

        let dragging=false, startY=0, startHeight=0;
        const isMobile=()=>window.innerWidth<=900;

        const begin=(y)=>{
            if(!isMobile()) return;
            dragging=true;
            startY=y;
            startHeight=panel.getBoundingClientRect().height;
            document.body.style.userSelect='none';
        };

        const move=(y)=>{
            if(!dragging || !isMobile()) return;
            const delta=startY-y;
            const controls=document.querySelector('.controls');
            const controlsH=controls ? controls.getBoundingClientRect().height : 70;
            const maxH=Math.max(220, window.innerHeight-controlsH-58);
            const nextH=Math.max(220, Math.min(maxH, startHeight+delta));
            panel.style.setProperty('height', nextH+'px', 'important');
        };

        const end=()=>{
            dragging=false;
            document.body.style.userSelect='';
        };

        handle.addEventListener('pointerdown', e=>{
            try{ handle.setPointerCapture(e.pointerId); }catch(err){}
            begin(e.clientY);
        });
        handle.addEventListener('pointermove', e=>move(e.clientY));
        handle.addEventListener('pointerup', end);
        handle.addEventListener('pointercancel', end);

        window.addEventListener('resize', ()=>{
            if(!isMobile()) panel.style.removeProperty('height');
        }, {passive:true});
    }

    function toggleSidePanel(tab){
        const panel=document.getElementById('side-panel'); if(!panel) return;
        if(panelOpen && activeTab===tab){ panel.style.display='none'; panelOpen=false; activeTab=null; document.querySelectorAll('.ctrl-icon').forEach(i=>i.classList.remove('active')); document.querySelectorAll('.panel-tabbtn').forEach(b=>b.classList.remove('active')); return; }
        panel.style.display='flex'; panelOpen=true; switchTab(tab);
    }

    function switchTab(tab){
        ['transcript','chat','people'].forEach(t=>{ const el=document.getElementById('tab-'+t); if(el) el.style.display='none'; });
        document.querySelectorAll('.ctrl-icon').forEach(i=>i.classList.remove('active'));
        document.querySelectorAll('.panel-tabbtn').forEach(b=>b.classList.toggle('active', b.dataset.tab===tab));
        const active=document.getElementById('tab-'+tab);
        if(active) active.style.display = tab==='people' ? 'block' : 'flex';
        activeTab=tab;
        document.getElementById('ctrl-'+tab)?.classList.add('active');
        if(tab==='chat'){ unreadChat=0; updateChatBadge(); setTimeout(markPendingChatSeen,0); }
        if(tab==='people') renderPeopleList();
    }

    function updateChatBadge(){
        const badge=document.getElementById('chat-badge'); if(!badge) return;
        if(unreadChat>0){ badge.textContent = unreadChat>99?'99+':String(unreadChat); badge.style.display='flex'; }
        else badge.style.display='none';
    }

    function setMicButton(on){
        const btn=document.getElementById('ctrl-mic');
        const off=document.getElementById('micoff-'+MY_USER_ID);
        if(btn){
            btn.innerHTML=on?'<i class="fa fa-microphone"></i>':'<i class="fa fa-microphone-slash"></i>';
            btn.classList.toggle('off',!on);
            btn.classList.toggle('active',on);
        }
        if(off) off.style.display=on?'none':'flex';
    }

    function setCameraButton(on){
        on=Boolean(on);
        const btn=document.getElementById('ctrl-camera');
        const video=document.getElementById('localVideo');
        const avatar=document.getElementById('avatar-'+MY_USER_ID);
        const showVideo=Boolean(on || isScreenSharing);

        if(btn){
            btn.innerHTML=on ? '<i class="fa fa-video"></i>' : '<i class="fa fa-video-slash"></i>';
            btn.classList.toggle('off',!on);
            btn.classList.toggle('active',on);
        }
        if(video) video.style.display=showVideo?'block':'none';
        if(avatar) avatar.style.display=showVideo?'none':'flex';
    }

    function setScreenShareButton(on){
        const btn=document.getElementById('ctrl-screen');
        const label=document.getElementById('ctrl-screen-label');
        if(btn){
            btn.classList.toggle('active',Boolean(on));
            btn.classList.toggle('off',false);
            btn.innerHTML=on ? '<i class="fa-solid fa-stop"></i>' : '<i class="fa-solid fa-display"></i>';
        }
        if(label) label.textContent=on?'Stop share':'Share';
    }

    async function toggleMic(){
        if(toggleMic.busy) return;
        toggleMic.busy=true;

        try{
            window.SmartMeetLiveKit?.room?.startAudio?.().catch(()=>{});
        }catch(e){}

        try{
            const targetOn=!isMicOn;
            const liveKit=window.SmartMeetLiveKit;

            if(!liveKit?.connected || !liveKit?.room){
                showToast('🎙️ Media connection is still starting. Please try again.');
                return;
            }

            await liveKit.setMicrophoneEnabled(targetOn);

            isMicOn=targetOn;
            setMicButton(targetOn);

            if(!targetOn){
                stopRecognition(true);
                const sp=document.getElementById('speaking-'+MY_USER_ID);
                if(sp) sp.style.display='none';
            }else{
                unlockRemoteAudio();
                startTranscript();
                startRecognition();
            }

            broadcastMyMicStatus();
        }catch(error){
            isMicOn=false;
            setMicButton(false);
            stopRecognition(true);
            showToast('🎙️ Could not access microphone. Please check permissions.');
        }finally{
            toggleMic.busy=false;
        }
    }

    async function toggleCamera(){
        if(toggleCamera.busy) return;
        toggleCamera.busy=true;

        try{
            const targetOn=!isCameraOn;
            const liveKit=window.SmartMeetLiveKit;

            if(!liveKit?.connected || !liveKit?.room){
                showToast('📷 Media connection is still starting. Please try again.');
                return;
            }

            await liveKit.setCameraEnabled(targetOn);

            isCameraOn=targetOn;
            setCameraButton(targetOn);

            const publication=liveKit.room.localParticipant.getTrackPublication?.('camera');
            const mediaTrack=publication?.track?.mediaStreamTrack;
            const localVideo=document.getElementById('localVideo');

            if(targetOn && localVideo && mediaTrack){
                localVideo.srcObject=new MediaStream([mediaTrack]);
                localVideo.muted=true;
                localVideo.autoplay=true;
                localVideo.playsInline=true;
                localVideo.setAttribute('playsinline','');
                localVideo.style.display='block';
                localVideo.play().catch(()=>{});
            }

            if(!targetOn && localVideo){
                localVideo.srcObject=null;
                localVideo.style.display=isScreenSharing?'block':'none';
            }

            broadcastMyCameraStatus();
        }finally{
            toggleCamera.busy=false;
        }
    }

    async function toggleScreenShare(){
        if(screenShareBusy) return;
        if(isScreenSharing){
            await stopScreenShare();
            return;
        }

        const liveKit=window.SmartMeetLiveKit;
        if(!liveKit?.connected || !liveKit?.room){
            showToast('🖥️ Media connection is still starting. Please try again.');
            return;
        }

        screenShareBusy=true;
        try{
            await liveKit.setScreenShareEnabled(true);
            const publication=liveKit.room.localParticipant.getTrackPublication?.('screen_share');
            const mediaTrack=publication?.track?.mediaStreamTrack || null;

            isScreenSharing=true;
            if(mediaTrack){
                mediaTrack.addEventListener('ended',()=>{
                    if(isScreenSharing) void stopScreenShare(true);
                },{once:true});

                const localVideo=document.getElementById('localVideo');
                const avatar=document.getElementById('avatar-'+MY_USER_ID);
                if(localVideo){
                    localVideo.srcObject=new MediaStream([mediaTrack]);
                    localVideo.muted=true;
                    localVideo.autoplay=true;
                    localVideo.playsInline=true;
                    localVideo.setAttribute('playsinline','');
                    localVideo.classList.remove('mirrored');
                    localVideo.style.display='block';
                    localVideo.play().catch(()=>{});
                }
                if(avatar) avatar.style.display='none';
            }

            setScreenShareButton(true);
            broadcastMyCameraStatus();
            showToast('🖥️ Screen sharing started.');
        }catch(err){
            if(err?.name!=='NotAllowedError' && err?.name!=='AbortError'){
                showToast('🖥️ Could not start screen sharing.');
            }
        }finally{
            screenShareBusy=false;
        }
    }

    async function stopScreenShare(fromBrowser=false){
        if(screenShareBusy && !fromBrowser) return;
        screenShareBusy=true;
        try{
            const liveKit=window.SmartMeetLiveKit;
            isScreenSharing=false;

            if(liveKit?.connected && liveKit?.room){
                try{ await liveKit.setScreenShareEnabled(false); }catch(err){}
            }

            const localVideo=document.getElementById('localVideo');
            const avatar=document.getElementById('avatar-'+MY_USER_ID);
            const cameraPublication=liveKit?.room?.localParticipant?.getTrackPublication?.('camera');
            const cameraTrack=cameraPublication?.track?.mediaStreamTrack || null;

            if(localVideo){
                localVideo.classList.add('mirrored');
                if(isCameraOn && cameraTrack){
                    localVideo.srcObject=new MediaStream([cameraTrack]);
                    localVideo.style.display='block';
                    localVideo.play().catch(()=>{});
                }else{
                    localVideo.srcObject=null;
                    localVideo.style.display='none';
                }
            }

            if(avatar) avatar.style.display=isCameraOn?'none':'flex';
            setScreenShareButton(false);
            broadcastMyCameraStatus();
        }finally{
            screenShareBusy=false;
        }
    }

    function broadcastMyMicStatus(){
        void sendSignal('all','mic-status',{userId:MY_USER_ID,muted:!isMicOn}).catch(()=>{});
    }
    function broadcastMyCameraStatus(){
        void sendSignal('all','camera-status',{userId:MY_USER_ID,cameraOn:Boolean(isCameraOn || isScreenSharing)}).catch(()=>{});
    }

    function attachLiveKitRemoteAudio(uid, liveKitTrack){
        uid=String(uid);
        if(!uid || !liveKitTrack || liveKitTrack.kind!=='audio') return;

        let audio=document.getElementById('audio-'+uid);
        if(!audio){
            audio=document.createElement('audio');
            audio.id='audio-'+uid;
            audio.autoplay=true;
            audio.playsInline=true;
            audio.setAttribute('playsinline','');
            audio.setAttribute('aria-hidden','true');
            audio.style.position='absolute';
            audio.style.width='1px';
            audio.style.height='1px';
            audio.style.opacity='0.01';
            audio.style.pointerEvents='none';
            audio.style.zIndex='-1';
            document.body.appendChild(audio);
        }

        audio.muted=false;
        audio.defaultMuted=false;
        audio.volume=1.0;

        try{
            liveKitTrack.attach(audio);
            audio.__smartMeetLiveKitTrackId=liveKitTrack.sid || liveKitTrack.mediaStreamTrack?.id;
        }catch(e){
            console.warn('[LiveKit] Track attach error:', e);
        }

        const playAudio=()=>{
            if(audio && audio.paused){
                audio.play().catch(()=>{
                    armAudioUnlock();
                });
            }
        };

        playAudio();

        if(!audio.__smartMeetAudioBound){
            audio.__smartMeetAudioBound=true;
            audio.addEventListener('canplay',playAudio);
            audio.addEventListener('loadedmetadata',playAudio);
        }
    }

    function attachLiveKitRemoteTrack(track, participant){
        const uid=liveKitMediaUserId(participant);
        if(!uid || uid===String(MY_USER_ID) || !track) return;

        registerLiveKitParticipant(uid, participant);

        if(track.kind==='audio'){
            micStatus[uid]=Boolean(track.isMuted);
            const micEl=document.getElementById('micoff-'+uid);
            if(micEl) micEl.style.display=micStatus[uid]?'flex':'none';
            renderPersonRow(uid);

            track.on('muted',()=>{
                micStatus[uid]=true;
                const el=document.getElementById('micoff-'+uid);
                if(el) el.style.display='flex';
                renderPersonRow(uid);
            });
            track.on('unmuted',()=>{
                micStatus[uid]=false;
                const el=document.getElementById('micoff-'+uid);
                if(el) el.style.display='none';
                attachLiveKitRemoteAudio(uid, track);
                renderPersonRow(uid);
            });

            attachLiveKitRemoteAudio(uid, track);
            return;
        }

        const video=document.getElementById('rvideo-'+uid);
        const avatar=document.getElementById('avatar-'+uid);
        if(video){
            track.attach(video);
            camStatus[uid]=!track.isMuted;
            video.style.display=track.isMuted?'none':'block';
            if(avatar) avatar.style.display=track.isMuted?'flex':'none';

            track.on('muted',()=>{
                camStatus[uid]=false;
                video.style.display='none';
                if(avatar) avatar.style.display='flex';
                renderPersonRow(uid);
            });
            track.on('unmuted',()=>{
                camStatus[uid]=true;
                video.style.display='block';
                if(avatar) avatar.style.display='none';
                renderPersonRow(uid);
            });
        }
    }

    function detachLiveKitRemoteTrack(track, participant){
        const uid=liveKitMediaUserId(participant);
        if(!uid || !track) return;

        if(track.kind==='audio'){
            const audio=document.getElementById('audio-'+uid);
            if(audio){
                try{ track.detach(audio); }catch(e){}
                audio.remove();
            }
            micStatus[uid]=true;
            const micEl=document.getElementById('micoff-'+uid);
            if(micEl) micEl.style.display='flex';
            renderPersonRow(uid);
            return;
        }

        const video=document.getElementById('rvideo-'+uid);
        const avatar=document.getElementById('avatar-'+uid);
        if(video){
            try{ track.detach(video); }catch(e){}
            video.style.display='none';
        }
        if(avatar) avatar.style.display='flex';
        camStatus[uid]=false;
        renderPersonRow(uid);
    }

    let audioUnlockArmed=false;
    function armAudioUnlock(){
        if(audioUnlockArmed) return;
        audioUnlockArmed=true;
        showToast('🔊 Tap anywhere to enable room audio.');
    }

    async function unlockRemoteAudio(){
        try{
            const room=window.SmartMeetLiveKit?.room;
            if(room && typeof room.startAudio==='function'){
                await room.startAudio();
            }
        }catch(e){}

        const audios=document.querySelectorAll('audio[id^="audio-"]');
        audios.forEach(a=>{
            a.muted=false;
            a.defaultMuted=false;
            a.volume=1.0;
            a.play().catch(()=>{});
        });
        audioUnlockArmed=false;
    }

    function installAudioPlaybackUnlock(){
        const unlock=()=>{
            unlockRemoteAudio().catch(()=>{});
        };
        document.addEventListener('pointerdown',unlock,{passive:true});
        document.addEventListener('keydown',unlock,{passive:true});
    }

    installAudioPlaybackUnlock();

    function makeSignalId(type){ return `${MY_USER_ID}:${type}:${Date.now()}:${Math.random().toString(36).slice(2,8)}`; }

    async function postSignal(toUserId, type, payload){
        try{
            const res=await fetch(SIGNAL_URL,{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':CSRF
                },
                credentials:'same-origin',
                cache:'no-store',
                body:JSON.stringify({ to_user_id:toUserId, type, data:payload })
            });
            return res.ok;
        }catch(e){
            return false;
        }
    }

    function sendSignal(toUserId, type, data){
        const payload={ ...(data||{}), _signalId: data?._signalId || makeSignalId(type) };
        return postSignal(toUserId, type, payload);
    }

    function listenForSignals(){
        return new Promise(resolve=>{
            if(typeof window.Echo==='undefined'){ resolve(false); return; }
            const channel=window.Echo.channel('meeting.'+MEETING_ID);
            channel.listen('.signal', handleSignal);
            channel.listen('.transcript', handleRemoteTranscript);
            setTimeout(()=>resolve(true), 1500);
        });
    }

    async function handleSignal(data){
        const from=String(data.fromUserId);
        const isSelf = from===String(MY_USER_ID);
        const sigId=data.data?._signalId;
        if(sigId){
            if(receivedSignalIds.has(sigId)) return;
            receivedSignalIds.add(sigId);
        }
        if(isSelf && !['meeting-cancelled','meeting-ended'].includes(data.type)) return;

        if(data.type==='meeting-cancelled'){
            showToast('🚫 The meeting was cancelled.');
            setTimeout(()=>{ cleanup(); window.location.href=CANCELLED_PAGE_URL; },2000);
            return;
        }
        if(data.type==='meeting-ended'){
            showToast('📞 Meeting has ended.');
            setTimeout(()=>{ cleanup(); window.location.href=ENDED_PAGE_URL; },1200);
            return;
        }
        if(data.type==='presence-request'){
            sendPresence(from);
            return;
        }
        if(data.type==='presence-response'){
            const uid=String(data.data?.userId || from);
            if(uid===String(MY_USER_ID)) return;
            registerJoinedUser(uid, data.data?.name, data.data?.initials, Boolean(data.data?.isOrganizer), data.data?.avatarUrl || null);
            micStatus[uid]=!Boolean(data.data?.micOn);
            camStatus[uid]=Boolean(data.data?.cameraOn);
            if(Boolean(data.data?.handRaised)) raisedHands.add(uid); else raisedHands.delete(uid);
            renderPersonRow(uid);
            syncRaisedHandBadge(uid);
            return;
        }
        if(data.type==='user-joined'){
            const uid=String(data.data.userId);
            if(uid===String(MY_USER_ID)) return;
            registerJoinedUser(uid, data.data.name, data.data.initials, Boolean(data.data?.isOrganizer), data.data?.avatarUrl || null);
            showToast(`✅ ${escapeHtml(data.data.name)} has joined.`);
            sendSignal(uid,'mic-status',{ userId:MY_USER_ID, muted:!isMicOn });
            sendSignal(uid,'camera-status',{ userId:MY_USER_ID, cameraOn:isCameraOn });
            return;
        }
        if(data.type==='user-left'){
            if(isSelf) return;
            markUserLeft(from);
            removeParticipantTile(from, true);
            return;
        }
        if(data.type==='chat'){
            if(isSelf) return;
            const control=String(data.data?.smartmeetControl || '');
            if(control==='transcript-line'){
                handleRemoteTranscript(data.data || {});
                return;
            }
            const controlUser=String(data.data?.userId || from);
            if(control==='raise-hand'){
                raisedHands.add(controlUser);
                renderPersonRow(controlUser);
                syncRaisedHandBadge(controlUser);
                return;
            }
            if(control==='lower-hand'){
                raisedHands.delete(controlUser);
                renderPersonRow(controlUser);
                syncRaisedHandBadge(controlUser);
                return;
            }
            const text=data.data?.text||''; if(!text) return;
            const messageId=String(data.data?.messageId||'').trim();
            const senderName=chatDisplayName(from,data.data?.name||'User');
            addChatBubble(senderName,text,false,from,messageId||null);
            if(activeTab!=='chat'){ unreadChat++; updateChatBadge(); }
            return;
        }
        if(data.type==='mic-status'){
            const uid=String(data.data?.userId||from);
            if(uid===String(MY_USER_ID)) return;
            micStatus[uid]=Boolean(data.data?.muted);
            const el=document.getElementById('micoff-'+uid);
            if(el) el.style.display=micStatus[uid]?'flex':'none';
            renderPersonRow(uid);
            return;
        }
        if(data.type==='camera-status'){
            const uid=String(data.data?.userId||from);
            if(uid===String(MY_USER_ID)) return;
            camStatus[uid]=Boolean(data.data?.cameraOn);
            renderPersonRow(uid);
            return;
        }
    }

    let recognition=null, recognitionRunning=false, recognitionStarting=false, recognitionStopping=false, recognitionRestartTimer=null;
    let transcriptLanguage='en-US';
    let transcriptLastFinal='';
    let transcriptLastFinalAt=0;

    function shouldRecognitionRun(){
        return !!isMicOn && document.visibilityState==='visible';
    }

    function startTranscript(){
        const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
        if(!SR) return;
        if(recognition) return;

        const instance=new SR();
        recognition=instance;
        instance.continuous=true;
        instance.interimResults=true;
        instance.lang=transcriptLanguage;

        instance.onresult=(e)=>{
            if(recognition!==instance || !isMicOn) return;
            let interim='';
            const finals=[];
            for(let i=e.resultIndex;i<e.results.length;i++){
                const result=e.results[i];
                const text=String(result?.[0]?.transcript||'').trim();
                if(!text) continue;
                if(result.isFinal) finals.push(text);
                else interim+=(interim?' ':'')+text;
            }
            if(interim) showLocalTranscript(interim,true);
            for(const normalized of finals){
                const now=Date.now();
                if(normalized.toLowerCase()===transcriptLastFinal.toLowerCase() && now-transcriptLastFinalAt<3000) continue;
                transcriptLastFinal=normalized;
                transcriptLastFinalAt=now;
                showLocalTranscript(normalized,false);
                saveTranscript(normalized);
            }
        };

        instance.onend=()=>{
            if(recognition!==instance) return;
            recognitionRunning=false;
            if(!recognitionStopping && shouldRecognitionRun()){
                scheduleRecognitionRestart(300);
            }
        };
    }

    function scheduleRecognitionRestart(delay=300){
        if(recognitionRestartTimer) clearTimeout(recognitionRestartTimer);
        recognitionRestartTimer=setTimeout(()=>{
            if(shouldRecognitionRun()) startRecognition();
        },delay);
    }

    function startRecognition(){
        if(!shouldRecognitionRun()) return;
        if(!recognition) startTranscript();
        if(!recognition || recognitionRunning) return;
        try{
            recognition.lang=transcriptLanguage;
            recognition.start();
            recognitionRunning=true;
        }catch(e){}
    }

    function stopRecognition(intentional=false){
        if(recognitionRestartTimer){ clearTimeout(recognitionRestartTimer); recognitionRestartTimer=null; }
        recognitionStopping=true;
        const instance=recognition;
        recognition=null;
        recognitionRunning=false;
        if(instance){
            try{ instance.abort(); }catch(e){}
        }
        setTimeout(()=>{ recognitionStopping=false; }, 200);
    }

    function toggleTranscriptLanguage(){
        transcriptLanguage=transcriptLanguage==='en-US'?'ur-PK':'en-US';
        const btn=document.getElementById('lang-toggle-btn');
        if(btn) btn.textContent=transcriptLanguage==='en-US'?'🌐 English':'🌐 Urdu';
        stopRecognition();
        if(isMicOn) startRecognition();
    }

    function showLocalTranscript(text,isInterim){
        const body=document.getElementById('transcript-body'); if(!body) return;
        body.querySelector('[data-empty]')?.remove();
        let live=document.getElementById('live-entry-'+MY_USER_ID);
        const color=colorFor(MY_USER_ID, true);

        if(isInterim){
            if(!live){
                live=document.createElement('div');
                live.className='transcript-entry is-interim';
                live.id='live-entry-'+MY_USER_ID;
                live.innerHTML=`<div class="transcript-avatar" style="background:linear-gradient(135deg,${color})">${escapeHtml(MY_INITIALS)}</div><div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span><span class="transcript-time">Now</span></div><div class="transcript-text"></div></div>`;
                body.appendChild(live);
            }
            const t=live.querySelector('.transcript-text');
            if(t) t.textContent=text;
        }else{
            if(live){ live.remove(); live=null; }
            const div=document.createElement('div');
            div.className='transcript-entry';
            div.innerHTML=`<div class="transcript-avatar" style="background:linear-gradient(135deg,${color})">${escapeHtml(MY_INITIALS)}</div><div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span><span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span></div><div class="transcript-text">${escapeHtml(text)}</div></div>`;
            body.appendChild(div);
        }
        body.scrollTop=body.scrollHeight;
    }

    function handleRemoteTranscript(data){
        if(!data || String(data.userId)===String(MY_USER_ID)) return;
        const text=String(data.text||'').trim();
        if(!text) return;

        const body=document.getElementById('transcript-body'); if(!body) return;
        body.querySelector('[data-empty]')?.remove();
        const name=String(data.userName||knownParticipants?.[String(data.userId)]?.name||'User');
        const color=colorFor(data.userId, false);
        const div=document.createElement('div');
        div.className='transcript-entry';
        div.innerHTML=`<div class="transcript-avatar" style="background:linear-gradient(135deg,${color})">${escapeHtml(data.userInitials||initialsOf(name))}</div><div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(name)}</span><span class="transcript-time">${escapeHtml(data.spokenAt||'')}</span></div><div class="transcript-text">${escapeHtml(text)}</div></div>`;
        body.appendChild(div);
        body.scrollTop=body.scrollHeight;
    }

    async function saveTranscript(text){
        const clean=String(text||'').trim();
        if(!clean) return;
        try{
            await fetch(TRANSCRIPT_URL,{
                method:'POST',
                headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
                body:JSON.stringify({text:clean})
            });
            await sendSignal('all','chat',{
                smartmeetControl:'transcript-line',
                userId:MY_USER_ID,
                userName:MY_NAME,
                userInitials:MY_INITIALS,
                text:clean,
                spokenAt:new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})
            });
        }catch(e){}
    }

    function chatDisplayName(userId,fallback='User'){
        const uid=String(userId||'');
        if(uid===String(MY_USER_ID)) return MY_NAME;
        return String(knownParticipants[uid]?.name||fallback||'User').trim()||'User';
    }

    function addChatBubble(name,text,isMe,userId=null,messageId=null){
        const body=document.getElementById('chat-body'); if(!body) return null;
        body.querySelector('[data-empty]')?.remove();
        const safeName=String(name||(isMe?MY_NAME:'User')).trim()||'User';
        const senderId=String(userId||(isMe?MY_USER_ID:'')||safeName);
        const time=new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
        const row=document.createElement('div');
        row.className='chat-message-row '+(isMe?'is-me':'is-other');

        const avatar=`<div class="chat-message-avatar" style="background:linear-gradient(135deg,${colorFor(senderId,isMe)})">${escapeHtml(initialsOf(safeName))}</div>`;
        const content=`<div class="chat-message-content"><div class="chat-message-meta"><strong>${escapeHtml(isMe?'You':safeName)}</strong><span>${time}</span></div><div class="chat-message-bubble">${escapeHtml(text)}</div></div>`;
        row.innerHTML=isMe?(avatar+content):(content+avatar);
        body.appendChild(row);
        body.scrollTop=body.scrollHeight;
        return row;
    }

    let chatSending=false;
    async function sendChat(){
        const input=document.getElementById('chat-input'); if(!input||chatSending) return;
        const text=input.value.trim(); if(!text) return;
        chatSending=true;
        addChatBubble(MY_NAME,text,true,String(MY_USER_ID));
        input.value='';
        input.focus();
        try{
            await sendSignal('all','chat',{text});
        }catch(e){
            showToast('💬 Message failed to send.');
        }finally{
            chatSending=false;
        }
    }

    async function safeToggleMic(){ await toggleMic(); }
    async function safeToggleCamera(){ await toggleCamera(); }
    async function safeLeaveMeeting(){ await leaveMeeting(); }

    let endingMeeting=false;
    async function endMeeting(){
        if(endingMeeting) return;
        if(!window.confirm('End this meeting for everyone?')) return;
        endingMeeting=true;
        try{
            const res=await fetch(END_MEETING_URL,{
                method:'POST',
                credentials:'same-origin',
                headers:{
                    'Accept':'application/json',
                    'Content-Type':'application/json',
                    'X-Requested-With':'XMLHttpRequest',
                    'X-CSRF-TOKEN':CSRF
                }
            });
            await sendSignal('all','meeting-ended',{});
            cleanup();
            window.location.href=ENDED_PAGE_URL;
        }catch(e){
            endingMeeting=false;
            showToast('Meeting could not be ended.');
        }
    }

    async function leaveMeeting(){
        if(leftNotified) return;
        leftNotified=true;
        try{
            await sendSignal('all','user-left',{ userId:MY_USER_ID, name:MY_NAME });
            await fetch(MARK_LEFT_URL,{
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                keepalive:true
            });
        }catch(e){}
        cleanup();
        window.location.href=LEAVE_URL;
    }

    function cleanup(){
        if(autoEndTimer) clearInterval(autoEndTimer);
        if(meetingClockInterval) clearInterval(meetingClockInterval);
        document.querySelectorAll('audio[id^="audio-"]').forEach(a=>a.remove());
        try{ window.SmartMeetLiveKit?.disconnect?.(); }catch(e){}
        stopRecognition();
    }

    function liveKitMediaUserId(participant){
        const identity=String(participant?.identity || '');
        if(!identity.startsWith('user-')) return null;
        return identity.slice(5) || null;
    }

    function registerLiveKitParticipant(uid, participant=null){
        uid=String(uid);
        if(uid===String(MY_USER_ID)) return;
        const name=participant?.name || ('User ' + uid);
        knownParticipants[uid]={
            userId:uid,
            name,
            initials:initialsOf(name),
            isOrganizer:false,
            hasJoined:true
        };
        addParticipantTile(uid, name, initialsOf(name), false);
        markOnline(uid);
        renderPeopleList();
    }

    function unregisterLiveKitParticipant(uid){
        uid=String(uid);
        if(uid===String(MY_USER_ID)) return;
        removeParticipantTile(uid, false);
        markOffline(uid);
        renderPeopleList();
    }

    function registerJoinedUser(uid, name, initials, isOrganizer=false, avatarUrl=null){
        uid=String(uid);
        if(uid===String(MY_USER_ID)) return;
        knownParticipants[uid]={
            name:name || ('User '+uid),
            initials:initials || initialsOf(name),
            avatarUrl,
            isOrganizer,
            hasJoined:true
        };
        addParticipantTile(uid, knownParticipants[uid].name, knownParticipants[uid].initials, isOrganizer);
        markOnline(uid);
        renderPeopleList();
    }

    function sendPresence(to='all'){
        return sendSignal(to,'presence-response',{
            userId:MY_USER_ID, name:MY_NAME, initials:MY_INITIALS, avatarUrl:MY_AVATAR_URL,
            isOrganizer:true, micOn:Boolean(isMicOn), cameraOn:Boolean(isCameraOn)
        });
    }

    function bindLiveKitMedia(){
        window.addEventListener('smartmeet:livekit-track-subscribed', event=>{
            attachLiveKitRemoteTrack(event.detail?.track, event.detail?.participant);
        });
        window.addEventListener('smartmeet:livekit-track-unsubscribed', event=>{
            detachLiveKitRemoteTrack(event.detail?.track, event.detail?.participant);
        });
        window.addEventListener('smartmeet:livekit-track-stream-state-changed', event=>{
            const track=event.detail?.publication?.track;
            const participant=event.detail?.participant;
            if(track?.kind==='audio' && event.detail?.streamState==='active'){
                attachLiveKitRemoteAudio(liveKitMediaUserId(participant), track);
            }
        });
        window.addEventListener('smartmeet:livekit-audio-playback-changed', event=>{
            if(event.detail?.canPlaybackAudio===false){
                armAudioUnlock();
            }else{
                unlockRemoteAudio();
            }
        });
    }

    function bindLiveKitPresence(){
        window.addEventListener('smartmeet:livekit-participant-connected', event=>{
            const uid=liveKitMediaUserId(event.detail?.participant);
            if(uid) registerLiveKitParticipant(uid, event.detail?.participant);
        });
        window.addEventListener('smartmeet:livekit-participant-disconnected', event=>{
            const uid=liveKitMediaUserId(event.detail?.participant);
            if(uid) unregisterLiveKitParticipant(uid);
        });
    }

    async function initLiveKit(){
        try{
            bindLiveKitPresence();
            bindLiveKitMedia();
            await window.SmartMeetLiveKit.connect({
                tokenUrl: LIVEKIT_TOKEN_URL,
                csrfToken: CSRF,
            });
            window.SmartMeetLiveKit.room.remoteParticipants.forEach(p=>{
                p.trackPublications.forEach(pub=>{
                    if(pub.track) attachLiveKitRemoteTrack(pub.track, p);
                });
            });
        }catch(e){
            console.error('[LiveKit] Connection failed:', e);
        }
    }

    window.addEventListener('load', async () => {
        renderMyOwnTile();
        setupPanelResize();
        initRoomInviteUI();
        renderPeopleList();
        await initLiveKit();
        await listenForSignals();
        sendSignal('all','user-joined',{ userId:MY_USER_ID, name:MY_NAME, initials:MY_INITIALS, isOrganizer:true });
        sendSignal('all','presence-request',{ userId:MY_USER_ID });
        scheduleAutoEnd();
    });
</script>
</body>
</html>
