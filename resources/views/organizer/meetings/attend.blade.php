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
        /* ================================================================
           SMARTMEET ROOM — SINGLE SOURCE OF TRUTH THEME
           ================================================================ */
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

        /* ---------- HEADER ---------- */
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
        }
        .btn-cancel:hover{background:rgba(239,68,68,.2)}

        /* ---------- MAIN LAYOUT ---------- */
        .main{flex:1 1 auto; min-height:0; display:flex; gap:12px; padding:12px}
        .video-area{
            flex:1; min-width:0; position:relative; border-radius:var(--radius-lg); overflow:hidden;
            border:1px solid var(--line); background:linear-gradient(155deg,rgba(10,18,36,.7),rgba(4,9,20,.85));
            box-shadow:inset 0 1px 0 rgba(255,255,255,.03), var(--shadow-lg);
        }
        .video-grid{
            height:100%; width:100%; display:grid; overflow-y:auto;
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

        /* ---------- SIDE PANEL ---------- */
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
        .panel-body{flex:1; overflow:hidden; display:flex; flex-direction:column}

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

        .chat-message-row{display:flex; width:100%; gap:0}
        .chat-message-row.is-me{justify-content:flex-end}
        .chat-message-row.is-other{justify-content:flex-start}
        .chat-message-content{max-width:82%; min-width:80px}
        .chat-message-row.is-me .chat-message-content{text-align:right}
        .chat-message-meta{display:flex; gap:7px; align-items:center; margin:0 4px 4px; font-size:9px; color:var(--muted-2)}
        .chat-message-row.is-me .chat-message-meta{justify-content:flex-end}
        .chat-message-meta strong{color:#e2e8f0; font-size:10px; font-weight:700}
        .chat-message-bubble{padding:9px 12px; border-radius:14px 14px 4px 14px; background:rgba(30,41,59,.85); border:1px solid var(--line); font-size:12px; line-height:1.5; word-break:break-word; display:inline-block; text-align:left}
        .chat-message-row.is-me .chat-message-bubble{border-radius:14px 14px 14px 4px; background:linear-gradient(135deg,#2563eb,#0891b2); border-color:rgba(125,211,252,.3)}
        .chat-input-area{display:flex; align-items:center; gap:8px; padding:12px; border-top:1px solid var(--line); background:rgba(2,6,16,.4)}
        .chat-input{flex:1; min-height:40px; padding:8px 12px; border-radius:12px; background:rgba(255,255,255,.04); border:1px solid var(--line); color:var(--text); font-size:12.5px; outline:none}
        .chat-input:focus{border-color:rgba(56,189,248,.55); box-shadow:0 0 0 3px rgba(56,189,248,.08)}
        .chat-voice-btn,.btn-send{width:40px; height:40px; flex-shrink:0; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.04); color:#e2e8f0; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px}
        .btn-send{background:linear-gradient(135deg,#2563eb,#0891b2); border:none; color:#fff}
        .chat-voice-btn.listening{color:#ef4444; border-color:rgba(239,68,68,.5); background:rgba(239,68,68,.14)}

        .people-body{flex:1; overflow-y:auto; padding:12px; display:flex; flex-direction:column; gap:8px}
        .person-row{display:flex; align-items:center; gap:10px; padding:10px; border-radius:13px; border:1px solid var(--line); background:rgba(255,255,255,.02); transition:opacity .2s, filter .2s, background .2s, border-color .2s}
        .person-row.joined{opacity:1; filter:none; background:rgba(34,197,94,.07); border-color:rgba(34,197,94,.22)}
        .person-row.pending{opacity:.5; filter:grayscale(.5) saturate(.4)}
        .person-avatar{width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; flex-shrink:0}
        .person-info{flex:1; min-width:0}
        .person-name{font-size:12.5px; font-weight:700; display:flex; align-items:center; gap:5px}
        .person-status{font-size:10px; color:var(--muted)}
        .person-status.on{color:var(--green)}
        .person-dot{width:8px; height:8px; border-radius:50%; background:var(--muted-2); flex-shrink:0}
        .person-dot.on{background:var(--green)}
        .person-action{border:1px solid var(--line); background:rgba(255,255,255,.04); color:var(--muted); font-size:10px; padding:5px 9px; border-radius:8px; cursor:pointer; flex-shrink:0}
        .person-action:hover{background:rgba(239,68,68,.16); color:#fecaca; border-color:rgba(239,68,68,.3)}

        /* ---------- CONTROLS ---------- */
        .controls{
            flex-shrink:0; display:flex; align-items:center; justify-content:center; gap:8px;
            margin:0 12px 12px; padding:9px 14px; border-radius:18px; border:1px solid var(--line);
            background:rgba(6,11,22,.92); box-shadow:0 -6px 26px rgba(0,0,0,.2), var(--shadow-lg);
            backdrop-filter:blur(18px); overflow-x:auto; scrollbar-width:none;
        }
        .controls::-webkit-scrollbar{display:none}
        .ctrl-btn{display:flex; flex-direction:column; align-items:center; gap:4px; min-width:52px; padding:4px 6px; border-radius:12px; cursor:pointer; user-select:none}
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

        /* ---------- TOASTS ---------- */
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

        /* ---------- RESPONSIVE / NON-OVERLAPPING ROOM LAYOUT ---------- */
        /*
         * Layout-only fixes:
         * - header/timer stay on the same top row
         * - tiles always reserve their own physical height
         * - rows keep a visible gap and never overlap
         * - video area gets its own vertical scrollbar as soon as another row is needed
         * - mobile side panel remains full-width
         */
        html,body{
            width:100%;
            height:100%;
            overflow:hidden;
        }
        body{
            height:100dvh;
            min-height:100dvh;
            overflow:hidden;
        }
        .header{
            flex:0 0 auto;
        }
        .main{
            flex:1 1 0;
            min-height:0;
            height:auto !important;
            overflow:hidden;
        }
        .video-area{
            min-height:0;
            padding:0 !important;
            overflow:hidden !important;
        }
        .video-grid{
            width:100%;
            height:100%;
            min-height:0;
            overflow-y:auto !important;
            overflow-x:hidden !important;
            scrollbar-gutter:stable;
            overscroll-behavior:contain;
            display:grid;
            grid-auto-flow:row;
            grid-auto-rows:max-content !important;
            align-content:start;
            align-items:start;
            justify-items:stretch;
            row-gap:16px;
            column-gap:16px;
            padding:16px;
        }
        .video-tile{
            position:relative;
            width:100%;
            max-width:none;
            margin:0;
            min-width:0;
            min-height:220px !important;
            height:clamp(220px,18vw,300px) !important;
            aspect-ratio:auto !important;
            align-self:start;
        }
        #maximized-overlay .video-tile{
            width:100%;
            height:100% !important;
            min-height:0 !important;
            max-width:none;
            aspect-ratio:auto !important;
        }

        /* Desktop */
        @media(min-width:1201px){
            .video-grid{
                grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
                gap:18px;
                padding:18px;
            }
            #side-panel{
                height:100%;
                min-height:0;
                align-self:stretch;
            }
        }

        /* Laptop / iPad landscape */
        @media(min-width:901px) and (max-width:1200px){
            .main{padding:10px; gap:10px}
            .video-grid{
                grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
                gap:16px;
                padding:16px;
            }
            .video-tile{
                min-height:200px !important;
                height:clamp(200px,22vw,270px) !important;
            }
            #side-panel{
                width:clamp(280px,31vw,340px);
                min-width:280px;
                max-width:360px;
                height:100%;
                min-height:0;
                align-self:stretch;
            }
        }

        /* Tablet portrait and smaller */
        @media(max-width:900px){
            .header{
                flex-wrap:nowrap !important;
                row-gap:0;
                gap:8px;
                min-height:58px;
                padding:8px 10px;
            }
            .header-left{
                flex:1 1 auto;
                min-width:0;
                gap:8px;
                overflow:hidden;
            }
            .header-center{
                order:0 !important;
                width:auto !important;
                min-width:max-content;
                flex:0 0 auto;
                justify-content:center;
                padding:6px 10px;
                margin:0;
            }
            .header-right{
                flex:0 0 auto;
                gap:7px;
            }
            .meeting-title{
                max-width:24vw;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
            }

            .main{
                flex-direction:column;
                padding:8px;
                gap:10px;
                min-height:0;
                overflow:hidden;
            }
            .video-area{
                flex:1 1 0;
                min-height:0;
                width:100%;
            }
            .video-grid{
                grid-template-columns:repeat(2,minmax(0,1fr));
                grid-auto-rows:max-content !important;
                gap:16px;
                padding:14px;
                overflow-y:auto !important;
            }
            .video-tile{
                min-height:190px !important;
                height:clamp(190px,30vw,270px) !important;
            }

            #side-panel{
                position:fixed;
                left:0;
                right:0;
                bottom:76px;
                transform:none;
                width:100% !important;
                min-width:0 !important;
                max-width:none !important;
                height:min(58dvh,540px);
                min-height:220px;
                max-height:calc(100dvh - 128px);
                z-index:55;
                border-radius:18px 18px 0 0;
            }
            .panel-drag-handle{display:flex}
        }

        @media(min-width:901px){
            .panel-drag-handle{display:none}
        }

        /* Phones: exactly one tile per row with a real gap between rows */
        @media(max-width:640px){
            .header{
                padding:7px 8px;
                gap:6px;
                min-height:56px;
            }
            .header-brand-text,.participants-count,.meeting-meta{display:none}
            .header-brand{
                padding-right:6px;
                gap:4px;
            }
            .header-brand img{
                width:28px;
                height:28px;
            }
            .live-badge{
                padding:4px 7px;
                font-size:9px;
                gap:4px;
            }
            .meeting-title{
                max-width:22vw;
                font-size:12px;
            }
            .header-center{
                padding:5px 8px;
                font-size:11px;
                gap:5px;
            }
            .header-right{gap:6px}
            .btn-leave span,.btn-cancel span{display:none}
            .btn-leave,.btn-cancel{
                padding:0;
                width:36px;
                height:36px;
                border-radius:50%;
                justify-content:center;
            }

            .main{
                padding:6px;
                gap:10px;
            }
            .video-grid{
                grid-template-columns:minmax(0,1fr) !important;
                grid-auto-rows:max-content !important;
                row-gap:16px !important;
                column-gap:0 !important;
                padding:10px;
                overflow-y:auto !important;
            }
            .video-grid:has(>.video-tile:only-child){
                grid-template-columns:minmax(0,1fr) !important;
            }
            .video-tile{
                width:100%;
                max-width:none;
                min-height:220px !important;
                height:clamp(220px,58vw,330px) !important;
                aspect-ratio:auto !important;
                border-radius:14px;
                margin:0 !important;
            }
            .tile-info{
                left:0;
                right:0;
                bottom:0;
                padding:9px 11px;
            }

            .controls{
                gap:2px;
                padding:7px 8px;
                margin:0 6px 6px;
            }
            .ctrl-btn{min-width:44px}

            #side-panel{
                bottom:70px;
                width:100% !important;
                min-width:0 !important;
                max-width:none !important;
                border-left:none;
                border-right:none;
                border-bottom:none;
            }
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
            .video-tile{
                min-height:210px !important;
                height:clamp(210px,62vw,300px) !important;
            }
            .avatar-circle{width:58px; height:58px; font-size:20px}
            .tile-name{font-size:11px}
            .role-badge{font-size:7px}
            .controls{padding:6px 4px; gap:0}
            .ctrl-btn{min-width:42px; padding:3px 4px}
            .ctrl-icon{width:36px; height:36px}
            .ctrl-label{font-size:8px}
            #side-panel{bottom:66px}
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
            $startForCalc = $meeting->actual_start
                ? \Carbon\Carbon::parse($meeting->actual_start)
                : \Carbon\Carbon::parse($meeting->date . ' ' . $meeting->time, $tz);
            $meetingEnd = $startForCalc->copy()->addMinutes((int) $durationMinutes)->utc()->toIso8601String();
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
        <div class="participants-count"><i class="fa fa-circle" style="color:var(--green);font-size:8px;"></i><span data-online-count>1</span> online</div>
        <button class="btn-cancel" onclick="cancelMeeting()"><i class="fa fa-ban"></i><span>Cancel</span></button>
        <button class="btn-leave" onclick="leaveMeeting()"><i class="fa fa-phone-slash"></i><span>Leave</span></button>
    </div>
</div>
<form id="cancel-form" action="{{ route('organizer.meetings.cancel', $meeting) }}" method="POST" style="display:none;">
    @csrf
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
            <div id="tab-chat" style="display:none; flex-direction:column; flex:1; overflow:hidden;">
                <div class="chat-body" id="chat-body">
                    <div class="empty-note" data-empty>No messages yet — say hello 👋</div>
                </div>
                <div class="chat-input-area">
                    <input class="chat-input" id="chat-input" placeholder="Type a message…" onkeydown="if(event.key==='Enter') sendChat()">
                    <button class="btn-send" onclick="sendChat()"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>
            <div id="tab-people" style="display:none; flex:1; overflow:hidden;">
                <div class="people-body" id="people-body"></div>
            </div>
        </div>
    </div>
</div>

<div class="controls">
    <div class="ctrl-btn" onclick="toggleMic()"><div class="ctrl-icon off" id="ctrl-mic"><i class="fa fa-microphone-slash"></i></div><span class="ctrl-label">Mic</span></div>
    <div class="ctrl-btn" onclick="toggleCamera()"><div class="ctrl-icon off" id="ctrl-camera"><i class="fa fa-video-slash"></i></div><span class="ctrl-label">Camera</span></div>
    <div class="ctrl-divider"></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('transcript')"><div class="ctrl-icon" id="ctrl-transcript"><i class="fa fa-closed-captioning"></i></div><span class="ctrl-label">Captions</span></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('chat')"><div class="ctrl-icon" id="ctrl-chat"><i class="fa fa-comment"></i><span id="chat-badge">0</span></div><span class="ctrl-label">Chat</span></div>
    <div class="ctrl-btn" onclick="toggleSidePanel('people')"><div class="ctrl-icon" id="ctrl-people"><i class="fa fa-users"></i></div><span class="ctrl-label">People</span></div>
    <div class="ctrl-divider"></div>
    <div class="ctrl-btn"><button class="btn-end" style="background:linear-gradient(135deg,#7f1d1d,#450a0a);" onclick="cancelMeeting()" title="Cancel meeting for everyone"><i class="fa fa-ban"></i></button><span class="ctrl-label" style="color:var(--red);">Cancel</span></div>
    <div class="ctrl-btn"><button class="btn-end" onclick="leaveMeeting()"><i class="fa fa-phone-slash"></i></button><span class="ctrl-label" style="color:var(--red);">Leave</span></div>
</div>

<div id="toast-stack"></div>

<script>
    /* ============================================================
       SMARTMEET — ORGANIZER ROOM (clean single implementation)
       ============================================================ */
    const IS_ORGANIZER   = true;
    const MEETING_ID      = "{{ $meeting->id }}";
    const MY_USER_ID      = "{{ auth()->id() }}";
    const ORGANIZER_ID    = String(MY_USER_ID);
    const MY_NAME         = @json(auth()->user()->name);
    const MY_INITIALS     = @json($userInitials);
    const SIGNAL_URL      = @json(route('organizer.meetings.signal', $meeting));
    const TRANSCRIPT_URL  = @json(route('organizer.meetings.transcript', $meeting));
    const MARK_LEFT_URL   = @json(route('organizer.meetings.markLeft', $meeting));
    const LEAVE_URL       = @json(route('organizer.meetings.index'));
    const CANCEL_URL      = @json(route('organizer.meetings.cancel', $meeting));
    const CSRF            = @json(csrf_token());
    const ALL_PARTICIPANTS = @json($allParticipants);
    const MEETING_END_TIME   = @json($meetingEnd);
    const ACTUAL_START = @json($meeting->actual_start ? \Carbon\Carbon::parse($meeting->actual_start)->utc()->toIso8601String() : now()->utc()->toIso8601String());
    const COLORS = ['#3b82f6,#06b6d4','#8b5cf6,#ec4899','#22c55e,#06b6d4','#f59e0b,#ef4444','#64748b,#334155','#ec4899,#f59e0b'];
    const IS_MOBILE_BROWSER = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

    /* ---------- Known participants (id -> {name, initials, isOrganizer, hasJoined}) ---------- */
    const knownParticipants = {};
    ALL_PARTICIPANTS.forEach(p => { knownParticipants[String(p.userId)] = { name: p.name, initials: p.initials, isOrganizer: false, hasJoined: Boolean(p.hasJoined) }; });

    /* ---------- Runtime state ---------- */
    const onlineUsers   = new Set([String(MY_USER_ID)]);
    const leftUsers     = new Set();
    const peers         = {};
    const remoteStreams = {};
    const negotiationTimers = {};
    const makingOffer   = {};
    const ignoreOffer   = {};
    const pendingCandidates = {};
    const micStatus     = {};
    const camStatus     = {};
    const receivedSignalIds = new Set();
    let localStream = null, isMicOn = false, isCameraOn = false;
    let maximizedUserId = null, maximizedPlaceholder = null;
    let activeTab = null, panelOpen = false, unreadChat = 0;
    let leftNotified = false, autoEndTimer = null, autoEndTriggered = false;

    function colorFor(uid, isOrganizer){ if(isOrganizer) return COLORS[0]; let h=0; for(const c of String(uid)) h=(h*31+c.charCodeAt(0))>>>0; return COLORS[1+(h%(COLORS.length-1))]; }
    function escapeHtml(t){ const d=document.createElement('div'); d.textContent=String(t??''); return d.innerHTML; }
    function initialsOf(name){ const parts=String(name||'').trim().split(/\s+/); if(!parts.length) return '?'; return (parts[0][0]+(parts.length>1?parts[parts.length-1][0]:'')).toUpperCase(); }

    /* ---------- Toast ---------- */
    const recentToasts = new Set();
    function showToast(msg){
        if(recentToasts.has(msg)) return;
        recentToasts.add(msg); setTimeout(()=>recentToasts.delete(msg),4000);
        const stack=document.getElementById('toast-stack'); if(!stack) return;
        const el=document.createElement('div'); el.className='toast'; el.textContent=msg;
        stack.appendChild(el);
        requestAnimationFrame(()=>el.classList.add('show'));
        setTimeout(()=>{ el.classList.remove('show'); el.classList.add('leaving'); setTimeout(()=>el.remove(),260); },3400);
    }
    function showModerationNotice(msg){
        const old=document.getElementById('mod-notice'); if(old) old.remove();
        const el=document.createElement('div'); el.id='mod-notice'; el.className='moderation-notice'; el.textContent=msg;
        document.body.appendChild(el);
        requestAnimationFrame(()=>el.classList.add('show'));
        setTimeout(()=>el.classList.remove('show'),3200);
        setTimeout(()=>el.remove(),3600);
    }

    /* ---------- Timer ---------- */
    let seconds = Math.max(0, Math.floor((Date.now()-new Date(ACTUAL_START).getTime())/1000));
    setInterval(()=>{
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
        if(autoEndTriggered) return; autoEndTriggered=true;
        showToast('⏰ Meeting time has ended.');
        setTimeout(()=>{ cleanup(); window.location.href=LEAVE_URL; },1800);
    }

    /* ---------- Online count / people list ---------- */
    let largeMeetingWarned=false;
    function updateOnlineCount(){
        document.querySelectorAll('[data-online-count]').forEach(el=>el.textContent=onlineUsers.size);
        // This is a full mesh (every browser connects directly to every other
        // browser) — each extra person means every device sends/decodes one more
        // audio+video stream. Phones on mobile data typically struggle past ~6-7
        // people at once, showing up as choppy audio or stalled video. This is a
        // one-time heads-up, not a hard limit — for bigger calls, an SFU/MCU
        // media-server backend (e.g. mediasoup, LiveKit, Janus) is the real fix.
        if(!largeMeetingWarned && onlineUsers.size>=7){
            largeMeetingWarned=true;
            showToast('👥 Large call: ask people who aren\'t speaking to turn off their camera for clearer audio.');
        }
    }
    function markOnline(uid){ uid=String(uid); onlineUsers.add(uid); if(knownParticipants[uid]) knownParticipants[uid].hasJoined=true; updateOnlineCount(); renderPersonRow(uid); }
    function markOffline(uid){ uid=String(uid); onlineUsers.delete(uid); if(knownParticipants[uid]) knownParticipants[uid].hasJoined=false; updateOnlineCount(); renderPersonRow(uid); }

    function renderPeopleList(){
        const body=document.getElementById('people-body'); if(!body) return;
        body.innerHTML='';
        const ids=[String(MY_USER_ID), ...ALL_PARTICIPANTS.map(p=>String(p.userId))];
        ids.forEach(uid=>renderPersonRow(uid));
    }
    function renderPersonRow(uid){
        uid=String(uid);
        const body=document.getElementById('people-body'); if(!body) return;
        const isMe = uid===String(MY_USER_ID);
        const info = isMe ? { name: MY_NAME, initials: MY_INITIALS, isOrganizer: true } : knownParticipants[uid];
        if(!info) return;
        const isOnline = isMe || onlineUsers.has(uid);
        let row=document.getElementById('person-row-'+uid);
        if(!row){ row=document.createElement('div'); row.id='person-row-'+uid; body.appendChild(row); }
        row.className='person-row '+(isOnline?'joined':'pending');
        const color=colorFor(uid, info.isOrganizer);
        const canMute = IS_ORGANIZER && !isMe && isOnline;
        row.innerHTML = `
        <div class="person-avatar" style="background:linear-gradient(135deg,${color})">${escapeHtml(info.initials||initialsOf(info.name))}</div>
        <div class="person-info">
            <div class="person-name">${escapeHtml(info.name)}${isMe?' <span style="color:var(--blue);font-weight:600;">(You)</span>':''}${info.isOrganizer?'<i class="fa fa-crown" style="color:#fbbf24;font-size:10px;"></i>':''}</div>
            <div class="person-status ${isOnline?'on':''}">${info.isOrganizer?'Organizer':'Participant'} • ${isOnline?'Joined':'Not joined yet'}</div>
        </div>
        ${canMute ? `<button class="person-action" onclick="muteParticipant('${uid}')" title="Mute this participant"><i class="fa fa-microphone-slash"></i></button>` : ''}
        <span class="person-dot ${isOnline?'on':''}"></span>`;
    }
    function muteParticipant(uid){
        uid=String(uid);
        const info=knownParticipants[uid];
        sendSignal(uid, 'mute', {});
        showToast(`🎙️ ${escapeHtml(info?info.name:'Participant')}'s microphone has been muted.`);
    }

    function renderMyOwnTile(){
        const grid=document.getElementById('video-grid');
        const tile=document.createElement('div');
        tile.className='video-tile'; tile.id='tile-'+MY_USER_ID;
        tile.innerHTML = `
        <div class="video-placeholder">
            <video id="localVideo" autoplay muted playsinline class="mirrored" style="display:none;"></video>
            <div class="avatar-circle" id="avatar-${MY_USER_ID}" style="background:linear-gradient(135deg,${COLORS[1]})">${escapeHtml(MY_INITIALS)}</div>
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

    function refreshEmptyStage(){ /* empty-stage overlay removed by request */ }

    /* ---------- Tiles ---------- */
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
            <div class="avatar-circle" id="avatar-${uid}" style="background:linear-gradient(135deg,${color});display:${cameraOn?'none':'flex'};">${escapeHtml(initials)}</div>
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
        refreshEmptyStage();
    }
    function removeParticipantTile(uid, announce){
        uid=String(uid);
        if(uid===String(maximizedUserId)){
            document.getElementById('maximized-overlay')?.classList.remove('active');
            maximizedPlaceholder?.remove(); maximizedPlaceholder=null; maximizedUserId=null;
        }
        document.getElementById('tile-'+uid)?.remove();
        markOffline(uid);
        refreshEmptyStage();
        if(announce){
            const info=knownParticipants[uid];
            showToast(`👋 ${escapeHtml(info?info.name:'A participant')} has left the meeting.`);
        }
    }

    /* ---------- Maximize ---------- */
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

    /* ---------- Side panel drag-resize (mobile only) ---------- */
    function setupPanelResize(){
        const panel=document.getElementById('side-panel');
        const handle=document.getElementById('panel-drag-handle');
        if(!panel || !handle || handle.dataset.bound) return;
        handle.dataset.bound='1';

        let dragging=false, startY=0, startHeight=0;
        const isMobile=()=>window.innerWidth<=900;

        const resetForViewport=()=>{
            if(!isMobile()){
                /* Remove mobile drag height so desktop/right sidebar stretches full height again. */
                panel.style.removeProperty('height');
            }
        };

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

        window.addEventListener('resize', resetForViewport, {passive:true});
        window.addEventListener('orientationchange', resetForViewport, {passive:true});
        resetForViewport();
    }

    /* ---------- Side panel tabs ---------- */
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
        if(tab==='chat'){ unreadChat=0; updateChatBadge(); }
        if(tab==='people') renderPeopleList();
    }
    function updateChatBadge(){
        const badge=document.getElementById('chat-badge'); if(!badge) return;
        if(unreadChat>0){ badge.textContent = unreadChat>99?'99+':String(unreadChat); badge.style.display='flex'; }
        else badge.style.display='none';
    }

    /* ============================================================
       WEBRTC — perfect negotiation, single implementation
       ============================================================ */
    const TURN_HOST = @json(config('services.turn.host'));
    const TURN_USERNAME = @json(config('services.turn.username'));
    const TURN_CREDENTIAL = @json(config('services.turn.credential'));
    const iceServers = [{ urls: ['stun:stun.l.google.com:19302','stun:stun1.l.google.com:19302'] }];
    if(TURN_HOST && TURN_USERNAME && TURN_CREDENTIAL){
        // 3478 udp/tcp for normal networks, PLUS TURN-over-TLS on 5349 so mobile
        // carrier networks / offices that block raw UDP or port 3478 can still
        // relay media. Two participants who are both on mobile data behind
        // symmetric/carrier-grade NAT can only ever reach each other through a
        // relay like this — this is the #1 reason "organizer hears everyone but
        // participants can't hear each other" on phones.
        iceServers.push({
            urls:[
                `turn:${TURN_HOST}:3478?transport=udp`,
                `turn:${TURN_HOST}:3478?transport=tcp`,
                `turns:${TURN_HOST}:5349?transport=tcp`
            ],
            username:TURN_USERNAME, credential:TURN_CREDENTIAL
        });
    } else {
        // No custom TURN configured (services.turn.host/username/credential in .env).
        // Without ANY relay, only STUN-reachable pairs connect directly; two mobile
        // participants behind symmetric NAT simply cannot reach each other. Add a
        // temporary public relay as a safety net so calls still work, but this is
        // rate-limited/shared — configure a dedicated TURN server (coturn, Twilio,
        // Cloudflare Calls, etc.) in .env as soon as possible for reliable calls.
        console.warn('[SmartMeet] Custom TURN is not configured — using a public fallback relay. Configure services.turn.host/username/credential in .env for reliable participant-to-participant audio/video.');
        iceServers.push(
            { urls:'turn:openrelay.metered.ca:80', username:'openrelayproject', credential:'openrelayproject' },
            { urls:'turn:openrelay.metered.ca:443', username:'openrelayproject', credential:'openrelayproject' },
            { urls:'turn:openrelay.metered.ca:443?transport=tcp', username:'openrelayproject', credential:'openrelayproject' }
        );
    }
    // relayOnly=true forces every candidate through TURN — used as an escalation
    // when normal (host/srflx) ICE candidates keep failing between two peers.
    function buildIceConfig(relayOnly=false){
        return { iceServers, iceCandidatePoolSize:10, bundlePolicy:'max-bundle', rtcpMuxPolicy:'require', iceTransportPolicy: relayOnly?'relay':'all' };
    }
    const iceConfig = buildIceConfig(false);
    console.log('[SmartMeet] ICE servers configured:', iceServers.map(s=>s.urls));

    function isPolite(otherUserId){
        const a=Number(MY_USER_ID), b=Number(otherUserId);
        if(!Number.isNaN(a) && !Number.isNaN(b)) return a>b;
        return String(MY_USER_ID) > String(otherUserId);
    }

    function getOrCreateRemoteStream(uid){ uid=String(uid); if(!remoteStreams[uid]) remoteStreams[uid]=new MediaStream(); return remoteStreams[uid]; }

    function shouldInitiate(uid){
        const a=Number(MY_USER_ID), b=Number(uid);
        if(!Number.isNaN(a) && !Number.isNaN(b)) return a<b;
        return String(MY_USER_ID)<String(uid);
    }

    async function negotiatePeer(uid, iceRestart=false){
        uid=String(uid);
        const pc=peers[uid];
        if(!pc || pc.signalingState==='closed' || leftUsers.has(uid)) return false;
        if(!iceRestart && (pc.connectionState==='connected' || pc.iceConnectionState==='connected' || pc.iceConnectionState==='completed')) return false;
        if(makingOffer[uid] || pc.signalingState!=='stable') return false;
        // onnegotiationneeded + presence repair can fire almost together.
        // Do not create a second normal offer while the first negotiation is settling.
        if(!iceRestart && pc.__lastOfferAt && (Date.now()-pc.__lastOfferAt)<4500) return false;
        try{
            makingOffer[uid]=true;
            ensureOfferTransceivers(pc);
            await syncLocalTracksToPeer(uid);
            const offer=await pc.createOffer(iceRestart ? {iceRestart:true} : undefined);
            if(pc.signalingState!=='stable') return false;
            await pc.setLocalDescription(offer);
            pc.__lastOfferAt=Date.now();
            console.log('[SmartMeet] sending offer ->', uid, iceRestart?'(ICE restart)':'');
            return await sendSignal(uid,'offer',{
                type:pc.localDescription.type,
                sdp:btoa(unescape(encodeURIComponent(pc.localDescription.sdp))),
                iceRestart:Boolean(iceRestart)
            });
        }catch(err){
            console.warn('[SmartMeet] negotiate failed', uid, err);
            return false;
        }finally{
            makingOffer[uid]=false;
        }
    }

    function armPeerWatchdog(uid, pc){
        if(!pc || pc.signalingState==='closed') return;
        clearTimeout(pc.__connectWatchdog);
        pc.__connectWatchdog=setTimeout(()=>{
            if(!peers[uid] || peers[uid]!==pc || pc.signalingState==='closed') return;
            const stuckIce=['checking','new'].includes(pc.iceConnectionState);
            const stuckConn=['connecting','new'].includes(pc.connectionState);
            if(stuckIce || stuckConn){
                console.warn('[SmartMeet] peer stuck while connecting', uid, {
                    ice:pc.iceConnectionState,
                    connection:pc.connectionState
                });
                if(shouldInitiate(uid)) restartPeer(uid);
                else requestPresence(true);
            }
        },10000);
    }

    function bindPeerTransceivers(pc){
        if(!pc) return;
        const txs=pc.getTransceivers();
        pc.__audioTx=txs.find(tx=>tx.receiver?.track?.kind==='audio' || tx.sender?.track?.kind==='audio') || pc.__audioTx || null;
        pc.__videoTx=txs.find(tx=>tx.receiver?.track?.kind==='video' || tx.sender?.track?.kind==='video') || pc.__videoTx || null;
    }

    function ensureOfferTransceivers(pc){
        if(!pc) return;
        bindPeerTransceivers(pc);
        if(!pc.__audioTx) pc.__audioTx=pc.addTransceiver('audio',{direction:'sendrecv'});
        if(!pc.__videoTx) pc.__videoTx=pc.addTransceiver('video',{direction:'sendrecv'});
    }

    function createPeerConnection(uid, forceRelay=false){
        uid=String(uid);
        if(uid===String(MY_USER_ID) || leftUsers.has(uid)) return null;
        let pc=peers[uid];
        if(pc && pc.signalingState!=='closed' && pc.connectionState!=='closed' && (!forceRelay || pc.__forcedRelay)) return pc;
        if(pc){ try{ pc.close(); }catch(e){} }

        pc=new RTCPeerConnection(forceRelay ? buildIceConfig(true) : iceConfig);
        pc.__forcedRelay=Boolean(forceRelay);
        peers[uid]=pc;
        pc.__createdAt=Date.now();
        pc.__lastOfferAt=0;
        pc.__connectedOnce=false;
        pc.__audioTx=null;
        pc.__videoTx=null;
        if(shouldInitiate(uid)) ensureOfferTransceivers(pc);
        syncLocalTracksToPeer(uid);

        // Only one side proactively initiates. The other side answers.
        // This avoids offer glare on mobile while still keeping perfect-negotiation handling.
        pc.onnegotiationneeded = async () => {
            if(!shouldInitiate(uid)) return;
            if(pc.connectionState==='connected' || pc.iceConnectionState==='connected' || pc.iceConnectionState==='completed') return;
            await negotiatePeer(uid,false);
        };

        pc.onicecandidate = (e)=>{
            if(e.candidate) sendSignal(uid,'ice-candidate',{ candidate:e.candidate.toJSON() });
        };
        pc.onicecandidateerror = (e)=>{
            console.warn('[SmartMeet] ICE candidate error', uid, e?.errorCode||'', e?.errorText||'');
        };

        pc.ontrack = (event)=>{
            if(leftUsers.has(uid)) return;
            const info=knownParticipants[uid];
            if(info){ info.hasJoined=true; addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer)); markOnline(uid); }

            const stream=getOrCreateRemoteStream(uid);
            const track=event.track;
            if(track && !stream.getTracks().some(t=>t.id===track.id)) stream.addTrack(track);

            attachRemoteStream(uid);

            event.track.onunmute = ()=>{
                attachRemoteStream(uid);
                setTimeout(()=>attachRemoteStream(uid),120);
                if(event.track.kind==='audio') unlockRemoteAudio();
            };
            event.track.onmute = ()=>{
                setTimeout(()=>attachRemoteStream(uid),250);
            };
            event.track.onended = ()=>{
                const s=remoteStreams[uid];
                const existing=s?.getTracks().find(t=>t.id===event.track.id);
                if(existing) s.removeTrack(existing);
                setTimeout(()=>attachRemoteStream(uid),80);
            };
        };

        pc.oniceconnectionstatechange = ()=>{
            const state=pc.iceConnectionState;
            console.log('[SmartMeet] ICE state', uid, '->', state);
            if(state==='checking' || state==='new'){
                armPeerWatchdog(uid,pc);
            }else if(state==='connected' || state==='completed'){
                clearTimeout(pc.__connectWatchdog);
                pc.__connectedOnce=true;
                ensureTileVisible(uid);
                attachRemoteStream(uid);
                unlockRemoteAudio();
            }else if(state==='failed'){
                clearTimeout(pc.__connectWatchdog);
                console.warn('[SmartMeet] ICE FAILED for', uid, '- attempting recovery');
                if(shouldInitiate(uid)) restartPeer(uid);
                else requestPresence(true);
            }else if(state==='closed'){
                clearTimeout(pc.__connectWatchdog);
            }
        };
        pc.onconnectionstatechange = ()=>{
            console.log('[SmartMeet] connection state', uid, '->', pc.connectionState);
            if(pc.connectionState==='connecting' || pc.connectionState==='new'){
                armPeerWatchdog(uid,pc);
            }else if(pc.connectionState==='connected'){
                clearTimeout(pc.__connectWatchdog);
                pc.__connectedOnce=true;
                clearTimeout(pc.__disconnectTimer);
                ensureTileVisible(uid);
                attachRemoteStream(uid);
                unlockRemoteAudio();
                pc.__restartAttempts=0;
            }else if(pc.connectionState==='disconnected'){
                clearTimeout(pc.__connectWatchdog);
                clearTimeout(pc.__disconnectTimer);
                pc.__disconnectTimer=setTimeout(()=>{
                    if(pc.connectionState==='disconnected'){
                        if(shouldInitiate(uid)) restartPeer(uid);
                        else requestPresence(true);
                    }
                },8000);
            }else if(pc.connectionState==='failed'){
                clearTimeout(pc.__connectWatchdog);
                if(shouldInitiate(uid)) restartPeer(uid);
                else requestPresence(true);
            }else if(pc.connectionState==='closed'){
                clearTimeout(pc.__connectWatchdog);
            }
        };
        pc.onicegatheringstatechange = ()=>{ console.log('[SmartMeet] ICE gathering', uid, '->', pc.iceGatheringState); };

        return pc;
    }

    function ensureTileVisible(uid){
        uid=String(uid); if(leftUsers.has(uid)) return;
        const info=knownParticipants[uid];
        if(info){ info.hasJoined=true; addParticipantTile(uid, info.name, info.initials, Boolean(info.isOrganizer)); markOnline(uid); }
    }

    async function restartPeer(uid){
        uid=String(uid);
        if(leftUsers.has(uid) || uid===String(MY_USER_ID)) return;
        const pc=peers[uid];
        if(!pc || pc.signalingState==='closed') return;

        // Backoff + cap: without this, a persistently broken relay path (e.g. bad
        // TURN credentials) causes an endless offer/answer loop that hammers the
        // signaling server and battery without ever actually recovering the call.
        pc.__restartAttempts = pc.__restartAttempts || 0;
        const backoff = Math.min(20000, 1500 * Math.pow(1.8, pc.__restartAttempts));
        const sinceLast = Date.now() - (pc.__lastRestartAt || 0);
        if(pc.__lastRestartAt && sinceLast < backoff) return;

        clearTimeout(pc.__connectWatchdog);
        clearTimeout(pc.__restartTimer);
        pc.__restartTimer=setTimeout(async ()=>{
            if(!peers[uid] || peers[uid]!==pc || pc.signalingState==='closed' || leftUsers.has(uid)) return;
            if(pc.iceConnectionState==='connected' || pc.iceConnectionState==='completed'){ pc.__restartAttempts=0; return; }
            pc.__lastRestartAt=Date.now();
            pc.__restartAttempts++;

            // Plain ICE restarts keep retrying the same direct (host/srflx) candidate
            // paths. If both sides sit behind symmetric/carrier-grade NAT (the classic
            // mobile participant-to-participant case) those paths were never going to
            // work, so repeating them just loops forever. After a few failed attempts,
            // stop hoping and force the connection through the TURN relay instead.
            if(pc.__restartAttempts>=3 && !pc.__forcedRelay){
                console.warn('[SmartMeet] forcing TURN relay for', uid, 'after repeated ICE failures');
                const attemptsSoFar=pc.__restartAttempts;
                createPeerConnection(uid, true);
                const fresh=peers[uid];
                if(fresh){
                    fresh.__restartAttempts=attemptsSoFar;
                    fresh.__lastRestartAt=Date.now();
                    if(shouldInitiate(uid)) await negotiatePeer(uid,false);
                }
                return;
            }

            try{
                // createOffer({iceRestart:true}) already generates fresh ICE credentials —
                // calling pc.restartIce() as well fired a SECOND, overlapping negotiation
                // and was the cause of the continuous offer/answer loop. Either side may
                // now initiate a restart; the polite/impolite handling in handleOffer()
                // safely resolves any collision if both sides try at once.
                await negotiatePeer(uid,true);
            }catch(e){ console.warn('[SmartMeet] ICE restart failed', uid, e); }
            if(pc.__restartAttempts===5){
                showToast('⚠️ Connection to a participant is unstable — this usually means the TURN relay server needs checking.');
            }
        },250);
    }

    async function syncLocalTracksToPeer(uid){
        const pc=peers[uid];
        if(!pc || pc.signalingState==='closed') return;
        removeDeadLocalTracks();
        bindPeerTransceivers(pc);

        const audioTrack=liveLocalTrack('audio');
        const videoTrack=liveLocalTrack('video');

        try{
            if(pc.__audioTx?.sender){
                if(pc.__audioTx.sender.track!==audioTrack) await pc.__audioTx.sender.replaceTrack(audioTrack);
                if(audioTrack) audioTrack.enabled=Boolean(isMicOn);
            }
        }catch(e){ console.warn('[SmartMeet] audio sender sync failed',uid,e); }

        try{
            if(pc.__videoTx?.sender){
                if(pc.__videoTx.sender.track!==videoTrack) await pc.__videoTx.sender.replaceTrack(videoTrack);
                if(videoTrack) videoTrack.enabled=Boolean(isCameraOn);
            }
        }catch(e){ console.warn('[SmartMeet] video sender sync failed',uid,e); }
    }

    async function syncTracksToEveryPeer(){ await Promise.allSettled(Object.keys(peers).map(uid=>syncLocalTracksToPeer(uid))); }

    function sameTrackSet(stream, tracks){
        const a=(stream?.getTracks?.()||[]).map(t=>t.id).sort().join('|');
        const b=(tracks||[]).map(t=>t.id).sort().join('|');
        return a===b;
    }

    function attachRemoteStream(uid){
        uid=String(uid);
        const localIds=new Set((localStream?.getTracks?.()||[]).map(t=>t.id));
        const pc=peers[uid];

        // After ICE recovery / renegotiation Chrome may temporarily expose
        // duplicate receivers (e.g. 2 audio + 2 video). If all receiver tracks
        // are bound to one media element, the browser can render the first stale
        // muted video track and keep the tile blank even though another receiver
        // is already live. Pick exactly one best track per media kind.
        const receiverTracks=(pc?.getReceivers?.()||[])
            .map(r=>r.track)
            .filter(t=>t && t.readyState!=='ended' && !localIds.has(t.id));

        const pickBest=(kind)=>{
            const tracks=receiverTracks.filter(t=>t.kind===kind);
            return tracks.find(t=>t.readyState==='live' && !t.muted)
                || tracks.find(t=>t.readyState==='live')
                || null;
        };

        const bestAudio=pickBest('audio');
        const bestVideo=pickBest('video');

        // Keep cached remote stream canonical: at most one audio and one video.
        const source=getOrCreateRemoteStream(uid);
        const keepIds=new Set([bestAudio?.id,bestVideo?.id].filter(Boolean));
        source.getTracks().forEach(t=>{
            if(!keepIds.has(t.id)){
                try{ source.removeTrack(t); }catch(e){}
            }
        });
        [bestAudio,bestVideo].filter(Boolean).forEach(t=>{
            if(!source.getTracks().some(x=>x.id===t.id)){
                try{ source.addTrack(t); }catch(e){}
            }
        });

        // -------- Remote audio --------
        let audio=document.getElementById('audio-'+uid);
        if(!audio){
            audio=document.createElement('audio');
            audio.id='audio-'+uid;
            audio.autoplay=true;
            audio.playsInline=true;
            audio.setAttribute('playsinline','');
            audio.style.display='none';
            document.body.appendChild(audio);
        }

        const wantedAudio=bestAudio?[bestAudio]:[];
        if(!sameTrackSet(audio.srcObject,wantedAudio)){
            audio.srcObject=new MediaStream(wantedAudio);
        }
        audio.muted=false;
        audio.defaultMuted=false;
        audio.volume=1;
        audio.preload='auto';

        if(!audio.__smartMeetUnlockBound){
            audio.__smartMeetUnlockBound=true;
            audio.addEventListener('canplay',()=>audio.play().catch(()=>armAudioUnlock()));
            audio.addEventListener('loadedmetadata',()=>audio.play().catch(()=>armAudioUnlock()));
        }
        if(bestAudio){
            const tryPlay=()=>audio.play().catch(()=>armAudioUnlock());
            tryPlay();
            if(!audio.__smartMeetResumeBound){
                audio.__smartMeetResumeBound=true;
                audio.addEventListener('pause',()=>{
                    const live=(audio.srcObject?.getAudioTracks?.()||[]).some(t=>t.readyState==='live');
                    if(live && document.visibilityState==='visible') setTimeout(tryPlay,120);
                });
            }
        }

        // -------- Remote video --------
        const video=document.getElementById('rvideo-'+uid);
        const avatar=document.getElementById('avatar-'+uid);
        if(video){
            const wantedVideo=bestVideo?[bestVideo]:[];
            if(!sameTrackSet(video.srcObject,wantedVideo)){
                video.srcObject=new MediaStream(wantedVideo);
            }

            video.muted=true;
            video.autoplay=true;
            video.playsInline=true;
            video.setAttribute('playsinline','');

            // Actual received frames are the source of truth.
            // A stale camera-status signal must not hide a real unmuted track.
            const show=Boolean(bestVideo && bestVideo.readyState==='live');
            video.style.display=show?'block':'none';
            if(avatar) avatar.style.display=show?'none':'flex';

            if(show){
                video.play().catch(()=>{});
                if(!video.__smartMeetPlayBound){
                    video.__smartMeetPlayBound=true;
                    video.addEventListener('loadedmetadata',()=>video.play().catch(()=>{}));
                    video.addEventListener('canplay',()=>video.play().catch(()=>{}));
                }
            }
        }

        if(bestVideo && bestVideo.readyState==='live') camStatus[uid]=true;
    }

    let audioUnlockArmed=false;
    async function unlockRemoteAudio(){
        const audios=[...document.querySelectorAll('audio[id^="audio-"]')];
        await Promise.allSettled(audios.map(a=>{
            a.muted=false;
            a.defaultMuted=false;
            a.volume=1;
            return a.play();
        }));
    }

    function armAudioUnlock(){
        if(audioUnlockArmed) return;
        audioUnlockArmed=true;
        showToast('🔊 Tap once to enable meeting audio.');
        const unlock=()=>{
            unlockRemoteAudio().finally(()=>{ audioUnlockArmed=false; });
            document.removeEventListener('pointerdown',unlock);
            document.removeEventListener('touchstart',unlock);
            document.removeEventListener('click',unlock);
        };
        document.addEventListener('pointerdown',unlock,{once:true,passive:true});
        document.addEventListener('touchstart',unlock,{once:true,passive:true});
        document.addEventListener('click',unlock,{once:true,passive:true});
    }

    function decodeSdp(sdp){ if(!sdp) return ''; try{ return decodeURIComponent(escape(atob(sdp))); }catch(e){ return sdp; } }

    async function handleOffer(from, data){
        console.log('[SmartMeet] received offer <-', from);
        const pc=createPeerConnection(from); if(!pc) return;
        const polite=isPolite(from);
        const offerCollision = makingOffer[from] || pc.signalingState!=='stable';
        ignoreOffer[from] = !polite && offerCollision;
        if(ignoreOffer[from]){ console.log('[SmartMeet] ignoring colliding offer from', from); return; }
        try{
            await pc.setRemoteDescription({ type:data.type||'offer', sdp:decodeSdp(data.sdp) });
            bindPeerTransceivers(pc);
            await syncLocalTracksToPeer(from);
            if(pendingCandidates[from]?.length){ for(const c of pendingCandidates[from]) await pc.addIceCandidate(c).catch(()=>{}); delete pendingCandidates[from]; }
            const answer=await pc.createAnswer();
            await pc.setLocalDescription(answer);
            console.log('[SmartMeet] sending answer ->', from);
            sendSignal(from,'answer',{ type:pc.localDescription.type, sdp:btoa(unescape(encodeURIComponent(pc.localDescription.sdp))) });
        }catch(err){ console.warn('[SmartMeet] offer handling failed', from, err); }
    }
    async function handleAnswer(from, data){
        console.log('[SmartMeet] received answer <-', from);
        const pc=peers[from]; if(!pc) return;
        // An answer is valid only while our local offer is outstanding.
        // Reverb/repair retries can deliver an old or duplicate answer after the
        // connection has already returned to stable; ignore it safely.
        if(pc.signalingState!=='have-local-offer'){
            console.log('[SmartMeet] ignoring stale/duplicate answer <-', from, 'state:', pc.signalingState);
            return;
        }
        try{
            await pc.setRemoteDescription({ type:data.type||'answer', sdp:decodeSdp(data.sdp) });
            bindPeerTransceivers(pc);
            await syncLocalTracksToPeer(from);
            if(pendingCandidates[from]?.length){
                for(const c of pendingCandidates[from]) await pc.addIceCandidate(c).catch(()=>{});
                delete pendingCandidates[from];
            }
            attachRemoteStream(from);
        }catch(err){ console.warn('[SmartMeet] answer handling failed', from, err); }
    }
    async function handleIceCandidate(from, data){
        const candidate=data.candidate; if(!candidate) return;
        const pc=peers[from];
        if(!pc || !pc.remoteDescription){ (pendingCandidates[from]=pendingCandidates[from]||[]).push(candidate); return; }
        try{ await pc.addIceCandidate(candidate); }catch(err){ if(!ignoreOffer[from]) console.warn('[SmartMeet] ICE candidate error', from, err); }
    }

    /* ---------- Signal transport (idempotent + retried) ---------- */
    function makeSignalId(type){ return `${MY_USER_ID}:${type}:${Date.now()}:${Math.random().toString(36).slice(2,8)}`; }
    async function postSignal(toUserId, type, payload, attempts=3){
        for(let i=0;i<attempts;i++){
            const ctrl=new AbortController(); const timeout=setTimeout(()=>ctrl.abort(),6500);
            try{
                const res=await fetch(SIGNAL_URL,{ method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF }, body:JSON.stringify({ to_user_id:toUserId, type, data:payload }), signal:ctrl.signal });
                if(res.ok) return true;
            }catch(e){}
            finally{ clearTimeout(timeout); }
            if(i<attempts-1) await new Promise(r=>setTimeout(r,400*(i+1)));
        }
        return false;
    }
    async function sendSignal(toUserId, type, data){
        const payload={ ...(data||{}), _signalId: data?._signalId || makeSignalId(type) };
        return postSignal(toUserId, type, payload, 3);
    }

    /* ---------- Realtime channel ---------- */
    function listenForSignals(){
        return new Promise(resolve=>{
            if(typeof window.Echo==='undefined'){ console.error('Echo not initialized'); resolve(false); return; }
            const channel=window.Echo.channel('meeting.'+MEETING_ID);
            let done=false; const finish=v=>{ if(!done){ done=true; resolve(v); } };
            channel.listen('.signal', handleSignal);
            channel.listen('.transcript', handleRemoteTranscript);
            if(typeof channel.subscribed==='function') channel.subscribed(()=>{
                console.log('[SmartMeet] Reverb channel subscribed');
                finish(true);
            });
            if(typeof channel.error==='function') channel.error(err=>{
                console.error('[SmartMeet] Reverb channel error', err);
                finish(false);
            });
            setTimeout(()=>finish(true), 1200);
        });
    }

    async function handleSignal(data){
        const from=String(data.fromUserId);
        const isSelf = from===String(MY_USER_ID);
        const sigId=data.data?._signalId;
        if(sigId){ if(receivedSignalIds.has(sigId)) return; receivedSignalIds.add(sigId); if(receivedSignalIds.size>1000){ receivedSignalIds.clear(); receivedSignalIds.add(sigId); } }
        if(isSelf && !['meeting-cancelled','meeting-ended'].includes(data.type)) return;

        if(data.type==='meeting-cancelled'){
            showToast('🚫 The organizer cancelled this meeting.');
            setTimeout(()=>{ cleanup(); window.location.href=LEAVE_URL; },2200);
            return;
        }
        if(data.type==='meeting-ended'){
            showToast(data.data?.auto ? '⏰ Meeting time has ended.' : '📞 The meeting has ended.');
            setTimeout(()=>{ cleanup(); window.location.href=LEAVE_URL; },2200);
            return;
        }
        if(data.type==='presence-request'){
            const requester=String(data.data?.userId || from);
            if(requester!==String(MY_USER_ID)) sendPresence(requester);
            return;
        }
        if(data.type==='presence-response'){
            const uid=String(data.data?.userId || from);
            if(uid===String(MY_USER_ID)) return;
            registerJoinedUser(uid, data.data?.name, data.data?.initials, Boolean(data.data?.isOrganizer));

            // Update remote state directly. The previous helper calls were undefined
            // and could abort this handler before WebRTC negotiation was scheduled.
            camStatus[uid]=Boolean(data.data?.cameraOn);
            micStatus[uid]=!Boolean(data.data?.micOn);
            const micEl=document.getElementById('micoff-'+uid);
            if(micEl) micEl.style.display=micStatus[uid] ? 'flex' : 'none';
            attachRemoteStream(uid);

            const pc=peers[uid];
            const connected=pc && (
                pc.connectionState==='connected' ||
                pc.iceConnectionState==='connected' ||
                pc.iceConnectionState==='completed'
            );
            if(shouldInitiate(uid) && pc && !connected && pc.signalingState==='stable'){
                setTimeout(()=>negotiatePeer(uid,false),80);
            }
            return;
        }
        if(data.type==='user-joined'){
            const uid=String(data.data.userId); if(uid===String(MY_USER_ID)) return;
            const wasOnline=onlineUsers.has(uid);
            leftUsers.delete(uid);
            registerJoinedUser(uid, data.data.name, data.data.initials, Boolean(data.data?.isOrganizer || uid===String(ORGANIZER_ID)));
            const pc=peers[uid];
            if(shouldInitiate(uid) && pc && pc.signalingState==='stable' &&
                !['connected'].includes(pc.connectionState) &&
                !['connected','completed'].includes(pc.iceConnectionState)){
                setTimeout(()=>negotiatePeer(uid,false),60);
            }
            if(!wasOnline) showToast(`✅ ${escapeHtml(data.data.name)} has joined the meeting.`);
            sendSignal(uid,'mic-status',{ userId:MY_USER_ID, muted:!isMicOn });
            sendSignal(uid,'camera-status',{ userId:MY_USER_ID, cameraOn:isCameraOn });
            return;
        }

        if(data.type==='user-left'){
            if(isSelf) return;
            leftUsers.add(from);
            removeParticipantTile(from, true);
            if(peers[from]){ peers[from].close(); delete peers[from]; }
            delete pendingCandidates[from];
            return;
        }
        if(data.type==='chat'){
            if(isSelf) return;
            const text=data.data?.text||''; if(!text) return;
            addChatBubble(data.data?.name||'User', text, false);
            if(activeTab!=='chat'){ unreadChat++; updateChatBadge(); }
            return;
        }
        if(data.type==='mic-status'){
            const uid=String(data.data.userId||from); if(uid===String(MY_USER_ID)) return;
            micStatus[uid]=data.data.muted;
            const el=document.getElementById('micoff-'+uid); if(el) el.style.display=data.data.muted?'flex':'none';
            return;
        }
        if(data.type==='camera-status'){
            const uid=String(data.data.userId||from); if(uid===String(MY_USER_ID)) return;
            camStatus[uid]=Boolean(data.data.cameraOn);
            attachRemoteStream(uid);
            return;
        }
        if(String(data.toUserId)!==String(MY_USER_ID)) return;
        if(!data.data) return;
        if(leftUsers.has(from) && ['offer','ice-candidate'].includes(data.type)) return;

        if(data.type==='offer') return handleOffer(from, data.data);
        if(data.type==='answer') return handleAnswer(from, data.data);
        if(data.type==='ice-candidate') return handleIceCandidate(from, data.data);
        if(data.type==='mute'){
            isMicOn=false;
            if(localStream) localStream.getAudioTracks().forEach(t=>t.enabled=false);
            setMicButton(false);
            stopRecognition();
            showModerationNotice('🎙️ Your microphone was muted by the organizer.');
            if(localStream) broadcastMyMicStatus();
            return;
        }
        if(data.type==='unmute'){ showModerationNotice('🎙️ The organizer allowed your microphone. Tap Mic to speak.'); return; }
    }

    /* ---------- Media ---------- */
    const audioConstraints = {
        echoCancellation:true,
        noiseSuppression:true,
        autoGainControl:true,
        channelCount:1
    };

    function liveLocalTrack(kind){
        if(!localStream) return null;
        const list = kind==='audio' ? localStream.getAudioTracks() : localStream.getVideoTracks();
        return list.find(t=>t.readyState==='live') || null;
    }

    function removeDeadLocalTracks(kind=null){
        if(!localStream) return;
        localStream.getTracks().forEach(t=>{
            if((!kind || t.kind===kind) && t.readyState==='ended'){
                try{ localStream.removeTrack(t); }catch(e){}
            }
        });
    }

    async function ensureAudioTrack(enableNow=false){
        if(!window.isSecureContext || !navigator.mediaDevices?.getUserMedia){
            showToast('⚠️ Mic/Camera needs HTTPS and browser permission.');
            return null;
        }

        removeDeadLocalTracks('audio');
        let track=liveLocalTrack('audio');

        if(!track){
            try{
                const s=await navigator.mediaDevices.getUserMedia({audio:audioConstraints,video:false});
                track=s.getAudioTracks().find(t=>t.readyState==='live') || null;
                if(!track) throw new Error('No live microphone track returned');
                if(!localStream) localStream=new MediaStream();
                // Keep one microphone track only.
                localStream.getAudioTracks().forEach(old=>{
                    if(old!==track){
                        try{ localStream.removeTrack(old); }catch(e){}
                        try{ old.stop(); }catch(e){}
                    }
                });
                localStream.addTrack(track);

                track.onended=async ()=>{
                    try{ localStream?.removeTrack(track); }catch(e){}
                    const shouldRecover=isMicOn;
                    if(!shouldRecover) return;
                    isMicOn=false;
                    setMicButton(false);
                    const replacement=await ensureAudioTrack(true);
                    if(replacement){
                        isMicOn=true;
                        setMicButton(true);
                        await syncTracksToEveryPeer();
                        broadcastMyMicStatus();
                        startTranscript();
                        startRecognition();
                    }
                };
            }catch(err){
                console.error('[SmartMeet] microphone error',err);
                if(err?.name==='NotAllowedError') showToast('🎙️ Allow microphone permission in browser settings.');
                else if(err?.name==='NotFoundError') showToast('🎙️ No microphone found.');
                else showToast('🎙️ Could not start microphone.');
                return null;
            }
        }

        track.enabled=Boolean(enableNow);
        return track;
    }

    async function startAudio(){
        const track=await ensureAudioTrack(false);
        if(!track) return;
        isMicOn=false;
        setMicButton(false);
        await syncTracksToEveryPeer();
        broadcastMyMicStatus();

        const localVideo=document.getElementById('localVideo');
        if(localVideo && localStream){
            localVideo.srcObject=localStream;
            localVideo.playsInline=true;
        }

        // Prepare SpeechRecognition on every supported browser, including mobile.
        startTranscript();
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

    async function toggleMic(){
        const targetOn=!isMicOn;
        const track=await ensureAudioTrack(targetOn);
        if(!track) return;

        // Set state explicitly instead of toggling after an async permission request.
        isMicOn=targetOn;
        track.enabled=targetOn;
        setMicButton(targetOn);

        await syncTracksToEveryPeer();
        // A second replaceTrack after the sender has settled fixes slower Android browsers.
        setTimeout(()=>syncTracksToEveryPeer(),180);
        setTimeout(()=>syncTracksToEveryPeer(),700);

        broadcastMyMicStatus();

        if(targetOn){
            unlockRemoteMedia();
            startTranscript();
            startRecognition();
            if(IS_MOBILE_BROWSER){
                setTimeout(()=>recoverMobileLocalMedia(),350);
                setTimeout(()=>recoverMobileLocalMedia(),1200);
            }
        }else{
            stopRecognition();
            const sp=document.getElementById('speaking-'+MY_USER_ID);
            if(sp) sp.style.display='none';
        }
    }

    async function ensureVideoTrack(enableNow=false){
        if(!window.isSecureContext || !navigator.mediaDevices?.getUserMedia){
            showToast('⚠️ Mic/Camera needs HTTPS and browser permission.');
            return null;
        }

        removeDeadLocalTracks('video');
        let track=liveLocalTrack('video');
        if(!track){
            try{
                const s=await navigator.mediaDevices.getUserMedia({
                    audio:false,
                    video: IS_MOBILE_BROWSER ? {
                        width:{ideal:640,max:1280},
                        height:{ideal:480,max:720},
                        frameRate:{ideal:20,max:24},
                        facingMode:'user'
                    } : {
                        width:{ideal:1280},
                        height:{ideal:720},
                        frameRate:{ideal:24,max:30},
                        facingMode:'user'
                    }
                });
                track=s.getVideoTracks().find(t=>t.readyState==='live') || null;
                if(!track) throw new Error('No live camera track returned');

                if(!localStream) localStream=new MediaStream();
                localStream.getVideoTracks().forEach(old=>{
                    if(old!==track){
                        try{ localStream.removeTrack(old); }catch(e){}
                        try{ old.stop(); }catch(e){}
                    }
                });
                localStream.addTrack(track);

                track.onended=async ()=>{
                    try{ localStream?.removeTrack(track); }catch(e){}
                    if(!isCameraOn) return;
                    isCameraOn=false;
                    setCameraButton(false);
                    await syncTracksToEveryPeer();
                    broadcastMyCameraStatus();
                };
            }catch(err){
                console.error('[SmartMeet] camera error',err);
                if(err?.name==='NotAllowedError') showToast('📷 Allow camera permission in browser settings.');
                else if(err?.name==='NotFoundError') showToast('📷 No camera found.');
                else showToast('📷 Camera could not start.');
                return null;
            }
        }
        track.enabled=Boolean(enableNow);
        return track;
    }

    function setCameraButton(on){
        const btn=document.getElementById('ctrl-camera');
        const localVideo=document.getElementById('localVideo');
        const avatar=document.getElementById('avatar-'+MY_USER_ID);
        if(btn){
            btn.innerHTML=on?'<i class="fa fa-video"></i>':'<i class="fa fa-video-slash"></i>';
            btn.classList.toggle('off',!on);
            btn.classList.toggle('active',on);
        }
        if(localVideo) localVideo.style.display=on?'block':'none';
        if(avatar) avatar.style.display=on?'none':'flex';
    }

    async function toggleCamera(){
        const targetOn=!isCameraOn;
        const track=await ensureVideoTrack(targetOn);
        if(!track) return;

        isCameraOn=targetOn;
        track.enabled=targetOn;
        setCameraButton(targetOn);

        const localVideo=document.getElementById('localVideo');
        if(localVideo && localStream){
            localVideo.srcObject=localStream;
            localVideo.muted=true;
            localVideo.autoplay=true;
            localVideo.playsInline=true;
            localVideo.setAttribute('playsinline','');
            if(targetOn) localVideo.play().catch(()=>{});
        }

        await syncTracksToEveryPeer();
        setTimeout(()=>syncTracksToEveryPeer(),180);
        setTimeout(()=>syncTracksToEveryPeer(),700);
        setTimeout(()=>syncTracksToEveryPeer(),1600);
        broadcastMyCameraStatus();
        unlockRemoteMedia();
        if(IS_MOBILE_BROWSER){
            setTimeout(()=>recoverMobileLocalMedia(),350);
            setTimeout(()=>recoverMobileLocalMedia(),1200);
        }
    }

    function broadcastMyMicStatus(){
        sendSignal('all','mic-status',{userId:MY_USER_ID,muted:!isMicOn});
    }
    function broadcastMyCameraStatus(){
        sendSignal('all','camera-status',{userId:MY_USER_ID,cameraOn:isCameraOn});
    }

    /* ---------- Mobile media recovery ---------- */
    let mobileMicMutedSince=0;
    let mobileVideoMutedSince=0;
    let mobileRecoveryBusy=false;

    async function recoverMobileLocalMedia(){
        if(!IS_MOBILE_BROWSER || mobileRecoveryBusy || document.visibilityState!=='visible') return;
        mobileRecoveryBusy=true;
        try{
            if(isMicOn){
                let a=liveLocalTrack('audio');
                if(!a || a.readyState!=='live'){
                    a=await ensureAudioTrack(true);
                    if(a){
                        a.enabled=true;
                        await syncTracksToEveryPeer();
                        broadcastMyMicStatus();
                    }
                }else if(a.muted){
                    if(!mobileMicMutedSince) mobileMicMutedSince=Date.now();
                    if(Date.now()-mobileMicMutedSince>2500){
                        try{ localStream?.removeTrack(a); }catch(e){}
                        try{ a.stop(); }catch(e){}
                        const replacement=await ensureAudioTrack(true);
                        if(replacement){
                            replacement.enabled=true;
                            await syncTracksToEveryPeer();
                            broadcastMyMicStatus();
                        }
                        mobileMicMutedSince=0;
                    }
                }else{
                    mobileMicMutedSince=0;
                    a.enabled=true;
                }
            }else{
                mobileMicMutedSince=0;
            }

            if(isCameraOn){
                let v=liveLocalTrack('video');
                if(!v || v.readyState!=='live'){
                    v=await ensureVideoTrack(true);
                    if(v){
                        v.enabled=true;
                        const lv=document.getElementById('localVideo');
                        if(lv && localStream){
                            lv.srcObject=localStream;
                            lv.muted=true;
                            lv.autoplay=true;
                            lv.playsInline=true;
                            lv.setAttribute('playsinline','');
                            lv.play().catch(()=>{});
                        }
                        await syncTracksToEveryPeer();
                        broadcastMyCameraStatus();
                    }
                }else if(v.muted){
                    if(!mobileVideoMutedSince) mobileVideoMutedSince=Date.now();
                    if(Date.now()-mobileVideoMutedSince>3000){
                        try{ localStream?.removeTrack(v); }catch(e){}
                        try{ v.stop(); }catch(e){}
                        const replacement=await ensureVideoTrack(true);
                        if(replacement){
                            replacement.enabled=true;
                            const lv=document.getElementById('localVideo');
                            if(lv && localStream){
                                lv.srcObject=localStream;
                                lv.muted=true;
                                lv.autoplay=true;
                                lv.playsInline=true;
                                lv.setAttribute('playsinline','');
                                lv.play().catch(()=>{});
                            }
                            await syncTracksToEveryPeer();
                            broadcastMyCameraStatus();
                        }
                        mobileVideoMutedSince=0;
                    }
                }else{
                    mobileVideoMutedSince=0;
                    v.enabled=true;
                }
            }else{
                mobileVideoMutedSince=0;
            }
        }finally{
            mobileRecoveryBusy=false;
        }
    }

    function unlockRemoteMedia(){
        unlockRemoteAudio();
        document.querySelectorAll('video[id^="rvideo-"]').forEach(v=>{
            if(v.srcObject && v.style.display!=='none'){
                v.muted=true;
                v.autoplay=true;
                v.playsInline=true;
                v.setAttribute('playsinline','');
                v.play().catch(()=>{});
            }
        });
    }

    /* ---------- Transcript (Web Speech API) ---------- */
    let recognition=null, recognitionRunning=false, recognitionStopping=false, recognitionRestartTimer=null;
    function startTranscript(){
        // CONFIRMED REGRESSION (2026-08-31 field test): enabling this on mobile
        // Chrome causes Chrome to fight WebRTC for exclusive mic access ("Speech
        // Recognition and Synthesis from Google cannot record now as Chrome is
        // recording audio"), which destabilizes the whole peer connection — not
        // just outgoing captions, but incoming audio/video from other people
        // stopped arriving on the phone too. Live call quality matters far more
        // than local captions, so mobile stays off. Mobile still receives other
        // people's captions via the signaling channel (handleRemoteTranscript).
        if(IS_MOBILE_BROWSER) return;
        const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
        if(!SR){ showToast('⚠️ Live captions require Chrome or Edge.'); return; }
        if(recognition) return;
        recognition=new SR();
        recognition.continuous=true; recognition.interimResults=true; recognition.maxAlternatives=1; recognition.lang='en-US';
        recognition.onstart=()=>{ recognitionRunning=true; const ind=document.getElementById('listening-indicator'); if(ind) ind.style.display='flex'; };
        recognition.onresult=(e)=>{
            if(!isMicOn){ stopRecognition(); return; }
            let interim='';
            for(let i=e.resultIndex;i<e.results.length;i++){
                const r=e.results[i]; const text=r[0].transcript.trim(); if(!text) continue;
                if(r.isFinal){
                    const sp=document.getElementById('speaking-'+MY_USER_ID); if(sp) sp.style.display='none';
                    showLocalTranscript(text,false); saveTranscript(text);
                } else interim += (interim?' ':'')+text;
            }
            if(interim){ const sp=document.getElementById('speaking-'+MY_USER_ID); if(sp) sp.style.display='flex'; showLocalTranscript(interim,true); }
        };
        recognition.onerror=(e)=>{
            recognitionRunning=false;
            if(e.error==='not-allowed'||e.error==='service-not-allowed'){
                showToast('Microphone/caption permission is required.');
                return;
            }
            if(e.error==='audio-capture'){
                console.warn('[SmartMeet] SpeechRecognition audio capture unavailable; WebRTC audio stays active.');
                scheduleRecognitionRestart(IS_MOBILE_BROWSER?2500:900);
                return;
            }
            scheduleRecognitionRestart(e.error==='network'?1200:500);
        };
        recognition.onend=()=>{ recognitionRunning=false; const ind=document.getElementById('listening-indicator'); if(ind) ind.style.display='none'; scheduleRecognitionRestart(400); };
    }
    function scheduleRecognitionRestart(delay=400){
        if(!recognition || recognitionStopping || !isMicOn || document.visibilityState!=='visible') return;
        if(recognitionRestartTimer) clearTimeout(recognitionRestartTimer);
        recognitionRestartTimer=setTimeout(()=>{ recognitionRestartTimer=null; startRecognition(); }, delay);
    }
    function startRecognition(){
        if(IS_MOBILE_BROWSER) return;
        if(!recognition || recognitionRunning || recognitionStopping || !isMicOn || document.visibilityState!=='visible') return;
        const mic=liveLocalTrack('audio');
        if(!mic || !mic.enabled || mic.readyState!=='live') return;
        try{ recognition.start(); recognitionRunning=true; }
        catch(e){
            recognitionRunning=false;
            scheduleRecognitionRestart(500);
        }
    }

    function stopRecognition(){
        if(!recognition) return;
        if(recognitionRestartTimer){ clearTimeout(recognitionRestartTimer); recognitionRestartTimer=null; }
        recognitionStopping=true;
        try{ if(recognitionRunning) recognition.abort(); }catch(e){}
        recognitionRunning=false;
        setTimeout(()=>{ recognitionStopping=false; },250);
    }
    function toggleTranscriptLanguage(){
        const btn=document.getElementById('lang-toggle-btn');
        const langs=[['en-US','🌐 English'],['ur-PK','🌐 Urdu']];
        const current = recognition?.lang || 'en-US';
        const next = current==='en-US' ? langs[1] : langs[0];
        stopRecognition(); recognition=null; startTranscript();
        if(recognition) recognition.lang=next[0];
        if(btn) btn.textContent=next[1];
        showToast('Captions language: '+(next[0]==='en-US'?'English':'Urdu'));
        if(isMicOn) scheduleRecognitionRestart(300);
    }
    function showLocalTranscript(text, isInterim){
        const body=document.getElementById('transcript-body'); if(!body) return;
        body.querySelector('[data-empty]')?.remove();
        let live=document.getElementById('live-entry-'+MY_USER_ID);
        if(isInterim){
            if(!live){
                live=document.createElement('div'); live.className='transcript-entry'; live.id='live-entry-'+MY_USER_ID;
                live.innerHTML=`<div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4)">${escapeHtml(MY_INITIALS)}</div>
            <div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span><span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span></div>
            <div class="transcript-text" style="opacity:.6;font-style:italic;"></div></div>`;
                body.appendChild(live);
            }
            live.querySelector('.transcript-text').textContent=text;
        } else {
            if(live){ const t=live.querySelector('.transcript-text'); t.style.opacity='1'; t.style.fontStyle='normal'; t.textContent=text; live.removeAttribute('id'); }
            else {
                const div=document.createElement('div'); div.className='transcript-entry';
                div.innerHTML=`<div class="transcript-avatar" style="background:linear-gradient(135deg,#3b82f6,#06b6d4)">${escapeHtml(MY_INITIALS)}</div>
            <div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(MY_NAME)} (You)</span><span class="transcript-time">${new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</span></div>
            <div class="transcript-text">${escapeHtml(text)}</div></div>`;
                body.appendChild(div);
            }
        }
        body.scrollTop=body.scrollHeight;
    }
    function handleRemoteTranscript(data){
        if(String(data.userId)===String(MY_USER_ID)) return;
        const body=document.getElementById('transcript-body'); if(!body) return;
        body.querySelector('[data-empty]')?.remove();
        const div=document.createElement('div'); div.className='transcript-entry';
        div.innerHTML = `<div class="transcript-avatar" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)">${escapeHtml(data.userInitials||'?')}</div>
    <div class="transcript-content"><div class="transcript-meta"><span class="transcript-name">${escapeHtml(data.userName||'User')}</span><span class="transcript-time">${data.spokenAt||''}</span></div>
    <div class="transcript-text">${escapeHtml(data.text||'')}</div></div>`;
        body.appendChild(div); body.scrollTop=body.scrollHeight;
    }
    async function saveTranscript(text){
        const clean=String(text||'').trim();
        if(!clean) return false;
        for(let attempt=0; attempt<2; attempt++){
            try{
                const ctrl=new AbortController();
                const timer=setTimeout(()=>ctrl.abort(),6500);
                const res=await fetch(TRANSCRIPT_URL,{
                    method:'POST',
                    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
                    body:JSON.stringify({text:clean}),
                    signal:ctrl.signal
                });
                clearTimeout(timer);
                if(res.ok) return true;
                console.error('transcript save failed',res.status);
            }catch(e){
                if(e?.name!=='AbortError') console.error('transcript save error',e);
            }
            await new Promise(r=>setTimeout(r,350));
        }
        return false;
    }

    /* ---------- Chat ---------- */
    function addChatBubble(name, text, isMe){
        const body=document.getElementById('chat-body'); if(!body) return;
        body.querySelector('[data-empty]')?.remove();
        const safeName=String(name||(isMe?MY_NAME:'User')).trim()||'User';
        const time=new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
        const row=document.createElement('div'); row.className='chat-message-row '+(isMe?'is-me':'is-other');
        row.innerHTML = `<div class="chat-message-content"><div class="chat-message-meta"><strong>${escapeHtml(isMe?MY_NAME+' (You)':safeName)}</strong><span>${time}</span></div><div class="chat-message-bubble">${escapeHtml(text)}</div></div>`;
        body.appendChild(row); body.scrollTop=body.scrollHeight;
    }
    let chatSending=false;
    async function sendChat(){
        const input=document.getElementById('chat-input'); if(!input || chatSending) return;
        const text=input.value.trim(); if(!text) return;
        chatSending=true;
        const ok=await sendSignal('all','chat',{text,name:MY_NAME});
        chatSending=false;
        if(ok){
            addChatBubble(MY_NAME,text,true);
            input.value='';
        }else{
            showToast('💬 Message could not be sent. Check your connection.');
            input.focus();
        }
    }
    function setupChatVoiceInput(){
        const btn=document.getElementById('chat-voice-btn'); const input=document.getElementById('chat-input');
        if(!btn || !input) return;
        const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
        if(!SR){ btn.disabled=true; btn.title='Voice input not supported in this browser'; return; }
        const rec=new SR(); rec.lang='en-US'; rec.continuous=false; rec.interimResults=true; rec.maxAlternatives=1;
        let baseText='';
        btn.addEventListener('click', ()=>{ try{ baseText=input.value.trim(); btn.classList.add('listening'); rec.start(); }catch(e){} });
        rec.onresult=(e)=>{ let spoken=''; for(let i=e.resultIndex;i<e.results.length;i++) spoken+=e.results[i][0].transcript; input.value=[baseText,spoken.trim()].filter(Boolean).join(' '); };
        rec.onend=()=>btn.classList.remove('listening');
        rec.onerror=()=>btn.classList.remove('listening');
    }

    /* ---------- Cancel meeting (organizer only) ---------- */
    let cancelling=false;
    async function cancelMeeting(){
        if(cancelling) return;
        if(!window.confirm('Cancel this meeting for everyone? This cannot be undone.')) return;
        cancelling=true; leftNotified=true;
        if(autoEndTimer) clearTimeout(autoEndTimer);
        try{
            await sendSignal('all','meeting-cancelled',{ message:'Meeting has been cancelled by the organizer.' });
        }catch(e){}
        showToast('🚫 Meeting cancelled for everyone.');
        cleanup();
        setTimeout(()=>{ document.getElementById('cancel-form')?.submit(); }, 250);
    }

    /* ---------- Leave / cleanup ---------- */
    async function leaveMeeting(){
        if(leftNotified) return; leftNotified=true;
        if(autoEndTimer) clearTimeout(autoEndTimer);
        showToast('👋 You have left the meeting.');
        await sendSignal('all','user-left',{ userId:MY_USER_ID, name:MY_NAME });
        try{ await fetch(MARK_LEFT_URL,{ method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF }, body:JSON.stringify({}), keepalive:true }); }catch(e){}
        cleanup();
        setTimeout(()=>{ window.location.href=LEAVE_URL; },350);
    }
    function notifyDisconnectBeacon(){
        if(leftNotified) return; leftNotified=true;
        const payload=JSON.stringify({});
        try{ fetch(MARK_LEFT_URL,{ method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF }, body:payload, keepalive:true }); }catch(e){}
        try{ navigator.sendBeacon(MARK_LEFT_URL, new Blob([payload],{ type:'application/json' })); }catch(e){}
    }
    window.addEventListener('pagehide', ()=>{ notifyDisconnectBeacon(); cleanup(); });
    window.addEventListener('beforeunload', notifyDisconnectBeacon);
    function cleanup(){
        if(autoEndTimer){ clearTimeout(autoEndTimer); autoEndTimer=null; }
        Object.values(peers).forEach(pc=>{ try{ pc.close(); }catch(e){} });
        localStream?.getTracks().forEach(t=>t.stop());
        stopRecognition();
    }

    /* ---------- Presence / reconnection ---------- */
    function registerJoinedUser(uid, name, initials, isOrganizer=false){
        uid=String(uid);
        if(uid===String(MY_USER_ID)) return;
        leftUsers.delete(uid);
        const old=knownParticipants[uid] || {};
        knownParticipants[uid]={
            ...old,
            name:name || old.name || ('User '+uid),
            initials:initials || old.initials || ((name||old.name||'U').trim().charAt(0).toUpperCase()),
            isOrganizer:Boolean(isOrganizer || old.isOrganizer || uid===String(ORGANIZER_ID)),
            hasJoined:true
        };
        addParticipantTile(uid, knownParticipants[uid].name, knownParticipants[uid].initials, knownParticipants[uid].isOrganizer);
        markOnline(uid);
        renderPeopleList();
        createPeerConnection(uid);
    }
    function sendPresence(to='all'){
        return sendSignal(to,'presence-response',{
            userId:MY_USER_ID, name:MY_NAME, initials:MY_INITIALS,
            isOrganizer:String(MY_USER_ID)===String(ORGANIZER_ID),
            micOn:Boolean(isMicOn), cameraOn:Boolean(isCameraOn)
        });
    }
    function connectToAll(){
        Object.keys(knownParticipants).forEach(uid=>{
            uid=String(uid);
            if(uid===String(MY_USER_ID) || leftUsers.has(uid)) return;
            if(!(knownParticipants[uid]?.hasJoined || onlineUsers.has(uid))) return;

            const pc=createPeerConnection(uid);
            if(!pc) return;
            syncLocalTracksToPeer(uid);

            // If signaling was missed while a phone/laptop slept or changed network,
            // deterministic initiator re-sends a fresh offer.
            const age=Date.now()-(pc.__lastOfferAt||pc.__createdAt||0);
            const alreadyConnected=(
                pc.connectionState==='connected' ||
                pc.iceConnectionState==='connected' ||
                pc.iceConnectionState==='completed'
            );
            if(!alreadyConnected &&
                shouldInitiate(uid) &&
                ['new','connecting','disconnected'].includes(pc.connectionState) &&
                pc.signalingState==='stable' &&
                age>5000){
                negotiatePeer(uid,pc.connectionState==='disconnected');
            }
        });
    }
    let joinAnnounced=false;
    let lastPresenceRequestAt=0;
    function requestPresence(force=false){
        const now=Date.now();
        if(!force && now-lastPresenceRequestAt<5000) return;
        lastPresenceRequestAt=now;
        sendSignal('all','presence-request',{ userId:MY_USER_ID });
    }
    function announceJoin(force=false){
        if(!force && joinAnnounced) return;
        joinAnnounced=true;
        sendSignal('all','user-joined',{ userId:MY_USER_ID, name:MY_NAME, initials:MY_INITIALS, isOrganizer:String(MY_USER_ID)===String(ORGANIZER_ID) });
        requestPresence(true);
    }

    function repairMeetingMedia(forcePresence=false){
        if(document.visibilityState!=='visible') return;
        connectToAll();
        syncTracksToEveryPeer();
        broadcastMyMicStatus();
        broadcastMyCameraStatus();

        Object.keys(peers).forEach(uid=>{
            const pc=peers[uid];
            if(!pc || pc.connectionState==='closed') return;
            if(pc.connectionState==='failed'){
                if(shouldInitiate(uid)) restartPeer(uid);
                else requestPresence(true);
            }else if(pc.connectionState==='connected' || pc.iceConnectionState==='connected' || pc.iceConnectionState==='completed'){
                attachRemoteStream(uid);
            }else if(pc.connectionState==='connecting' || pc.iceConnectionState==='checking'){
                armPeerWatchdog(uid,pc);
            }
        });

        // Always request the current roster. requestPresence() is throttled,
        // so late joiners are discovered without refresh and without flooding.
        requestPresence(forcePresence);
        unlockRemoteAudio();
    }

    window.addEventListener('online', ()=>{
        setTimeout(()=>repairMeetingMedia(true),150);
        setTimeout(()=>recoverMobileLocalMedia(),300);
        setTimeout(()=>unlockRemoteMedia(),450);
    });
    window.addEventListener('pageshow', ()=>{
        setTimeout(()=>repairMeetingMedia(true),150);
        setTimeout(()=>recoverMobileLocalMedia(),300);
        setTimeout(()=>unlockRemoteMedia(),450);
    });
    document.addEventListener('visibilitychange', ()=>{
        if(document.visibilityState==='visible'){
            setTimeout(()=>repairMeetingMedia(true),120);
            setTimeout(()=>recoverMobileLocalMedia(),220);
            if(isMicOn){ startTranscript(); startRecognition(); }
            unlockRemoteMedia();
        }else{
            stopRecognition();
        }
    });
    document.addEventListener('pointerdown', unlockRemoteMedia, { passive:true });
    document.addEventListener('touchstart', unlockRemoteMedia, { passive:true });
    document.addEventListener('click', unlockRemoteMedia, { passive:true });
    document.addEventListener('keydown', unlockRemoteMedia, { passive:true });

    /* ---------- Boot ---------- */
    window.addEventListener('load', async () => {
        renderMyOwnTile();
        setupPanelResize();
        renderPeopleList();

        ALL_PARTICIPANTS.forEach(p=>{ if(p.hasJoined){ addParticipantTile(p.userId, p.name, p.initials, false); markOnline(p.userId); } });
        renderPeopleList();
        refreshEmptyStage();

        await startAudio();
        await listenForSignals();
        announceJoin(true);
        scheduleAutoEnd();

        [150,700,1800].forEach(delay=>setTimeout(()=>repairMeetingMedia(false),delay));
        setInterval(()=>repairMeetingMedia(false),10000);

        setInterval(()=>{
            if(document.visibilityState!=='visible') return;
            Object.keys(peers).forEach(uid=>{
                const pc=peers[uid];
                if(!pc || pc.connectionState==='closed' || leftUsers.has(String(uid))) return;
                syncLocalTracksToPeer(uid);
                attachRemoteStream(uid);
            });
            unlockRemoteAudio();
        },2500);

        // Stalled-media watchdog: ICE can report "connected" while the actual
        // media path is dead (e.g. a flaky TURN relay drops packets after the
        // handshake). That shows up as a frozen/black remote video or silent
        // remote audio that never recovers on its own. Detect it by checking
        // whether the received video frame / audio timestamp is actually
        // advancing, and force an ICE restart if it's been stuck too long.
        const __stallTracker={};
        setInterval(()=>{
            if(document.visibilityState!=='visible') return;
            Object.keys(peers).forEach(uid=>{
                const pc=peers[uid];
                if(!pc || pc.connectionState!=='connected' || leftUsers.has(String(uid))) return;
                const video=document.getElementById('rvideo-'+uid);
                const audio=document.getElementById('audio-'+uid);
                const vTime=video && video.style.display!=='none' ? video.currentTime : null;
                const aTime=audio ? audio.currentTime : null;
                const prev=__stallTracker[uid] || {};
                const now=Date.now();
                const vStuck = vTime!=null && prev.vTime===vTime;
                const aStuck = aTime!=null && prev.aTime===aTime;
                const firstSeenStuckAt = (vStuck || aStuck) ? (prev.stuckSince || now) : null;
                __stallTracker[uid]={ vTime, aTime, stuckSince: firstSeenStuckAt };
                if(firstSeenStuckAt && (now-firstSeenStuckAt)>7000){
                    console.warn('[SmartMeet] media appears stalled for', uid, '- forcing reconnect');
                    __stallTracker[uid]={ vTime:null, aTime:null, stuckSince:null };
                    if(shouldInitiate(uid)) restartPeer(uid); else requestPresence(true);
                }
            });
        },3000);

        if(IS_MOBILE_BROWSER){
            setInterval(()=>recoverMobileLocalMedia(),3500);
            setInterval(()=>unlockRemoteMedia(),1800);
        }

        setInterval(()=>{
            Object.keys(peers).forEach(uid=>{
                const pc=peers[uid];
                if(!pc || pc.connectionState!=='connected') return;
                const aSend=pc.__audioTx?.sender?.track;
                const vSend=pc.__videoTx?.sender?.track;
                const recv=pc.getReceivers().map(r=>({
                    kind:r.track?.kind,
                    state:r.track?.readyState,
                    muted:r.track?.muted
                }));
                console.log('[SmartMeet] media health', uid, {
                    audioSend:Boolean(aSend && aSend.readyState==='live'),
                    audioEnabled:Boolean(aSend?.enabled),
                    videoSend:Boolean(vSend && vSend.readyState==='live'),
                    videoEnabled:Boolean(vSend?.enabled),
                    receivers:recv
                });
            });
        },30000);
    });
</script>
</body>
</html>
