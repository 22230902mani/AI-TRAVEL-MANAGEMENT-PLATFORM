<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TravelMate') — AI Travel Ecosystem</title>
    <meta name="description" content="@yield('meta_description', 'TravelMate – AI-powered travel planning, smart itineraries, and seamless bookings.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            /* ── NEW PREMIUM PALETTE ── */
            /* Blues */
            --blue-deep:   #1e3a8a;
            --blue-vivid:  #2563eb;
            --blue-glow:   #3b82f6;

            /* Purples */
            --purple-deep: #4c1d95;
            --purple-vivid:#7c3aed;
            --purple-glow: #a855f7;

            /* Magentas / Pinks */
            --pink-deep:   #831843;
            --pink-vivid:  #db2777;
            --pink-glow:   #ec4899;

            /* Reds */
            --red-deep:    #7f1d1d;
            --red-vivid:   #dc2626;

            /* Emerald accent */
            --emerald-500: #00c853;
            --emerald-400: #34D399;
            --emerald-glow:rgba(0,200,83,0.3);

            /* Neutrals */
            --slate-100:   #f1f5f9;
            --slate-300:   #cbd5e1;
            --slate-400:   #94a3b8;
            --slate-700:   #1e2540;
            --slate-900:   #0a0d1a;

            /* ── MAIN ASSIGNMENTS ── */
            --bg:          #080b14;
            --surface:     #111827;
            --surface2:    #1a2035;
            --border:      rgba(255,255,255,0.08);
            --text:        #f1f5f9;
            --muted:       #94a3b8;

            --nav-bg:      #06080f;
            --nav-text:    #cbd5e1;

            /* Primary = Violet-Blue gradient */
            --primary:     #7c3aed;
            --primary-dark:#4c1d95;
            --primary-grad:linear-gradient(135deg,#7c3aed,#2563eb);

            /* Secondary = Emerald */
            --secondary:   #00c853;

            /* Accent = Magenta-Pink */
            --accent:      #db2777;
            --accent-grad: linear-gradient(135deg,#db2777,#7c3aed);

            /* Gold stays */
            --gold:        #fbbf24;

            --success:     #00c853;
            --success-bg:  rgba(0,200,83,0.12);
            --success-border:rgba(0,200,83,0.3);

            --footer-bg:   #06080f;
            --footer-text: #94a3b8;

            --card-bg:     #111827;
            --card-shadow: 0 20px 40px rgba(0,0,0,0.5);
            --radius:      18px;
            --shadow:      0 8px 24px rgba(0,0,0,0.35);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family:'Inter',sans-serif;
            background:var(--bg);
            color:var(--text);
            line-height:1.6;
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(124,58,237,0.07) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(37,99,235,0.07) 0%, transparent 50%);
            background-attachment: fixed;
        }
        a { color:inherit; text-decoration:none; }

        /* NAVBAR */
        .navbar {
            position:fixed; top:0; width:100%; z-index:1000; padding:0 2rem;
            background:rgba(6,8,15,0.85); backdrop-filter:blur(24px) saturate(180%);
            -webkit-backdrop-filter:blur(24px) saturate(180%);
            border-bottom:1px solid rgba(124,58,237,0.2); transition:all .3s;
            box-shadow:0 4px 30px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
        }
        .nav-inner { max-width:1400px; margin:0 auto; display:flex; align-items:center;
            justify-content:space-between; height:72px; }
        .nav-logo {
            font-family:'Playfair Display',serif; font-size:1.65rem; font-weight:900;
            background:linear-gradient(135deg,#a855f7 0%,#3b82f6 50%,#00c853 100%);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            letter-spacing:-.02em;
        }
        .nav-links { display:flex; align-items:center; gap:2rem; list-style:none; }
        .nav-links a {
            font-size:.88rem; font-weight:500; color:rgba(255,255,255,0.65);
            transition:.25s; padding:.4rem 0; border-bottom:2px solid transparent;
            letter-spacing:.02em;
        }
        .nav-links a:hover { color:#fff; }
        .nav-links a.active { color:#fff; border-bottom-color:#7c3aed; }
        .nav-actions { display:flex; align-items:center; gap:1rem; }
        .btn {
            display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.5rem;
            border-radius:50px; font-weight:600; font-size:.85rem; cursor:pointer;
            transition:all .25s; border:none; letter-spacing:.03em;
        }
        .btn-primary {
            background:linear-gradient(135deg,#7c3aed,#2563eb);
            color:#fff; box-shadow:0 4px 20px rgba(124,58,237,0.45);
        }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(124,58,237,0.65); filter:brightness(1.1); }
        .btn-outline { background:transparent; border:1px solid rgba(255,255,255,0.18); color:rgba(255,255,255,0.85); }
        .btn-outline:hover { border-color:#7c3aed; color:#a855f7; background:rgba(124,58,237,0.1); }
        .btn-sm { padding:.38rem 1rem; font-size:.8rem; }
        .btn-danger { background:linear-gradient(135deg,#dc2626,#7f1d1d); color:#fff; box-shadow:0 4px 16px rgba(220,38,38,0.4); }

        /* DROPDOWN */
        .nav-dropdown { position:relative; }
        .dropdown-menu { position:absolute; top:calc(100% + 10px); right:0; background:var(--surface);
            border:1px solid var(--border); border-radius:var(--radius); min-width:200px; padding:.5rem 0;
            opacity:0; visibility:hidden; transform:translateY(-8px); transition:.2s; box-shadow:var(--shadow); }
        .dropdown-menu.show { opacity:1; visibility:visible; transform:translateY(0); }
        .dropdown-menu a { display:flex; align-items:center; gap:.75rem; padding:.7rem 1.2rem;
            font-size:.88rem; color:var(--muted); transition:.15s; }
        .dropdown-menu a:hover { color:var(--text); background:var(--surface2); }
        .dropdown-menu hr { border:none; border-top:1px solid var(--border); margin:.4rem 0; }
        .avatar-sm { width:36px; height:36px; border-radius:50%; object-fit:cover;
            border:2px solid var(--primary); cursor:pointer; }

        /* NOTIFICATION BADGE */
        .notif-badge { position:relative; }
        .notif-badge .badge { position:absolute; top:-4px; right:-4px; background:var(--accent);
            color:#fff; font-size:.65rem; width:18px; height:18px; border-radius:50%;
            display:flex; align-items:center; justify-content:center; font-weight:700; }

        /* MAIN */
        main { padding-top:70px; min-height:100vh; }

        /* FOOTER */
        footer { background:var(--footer-bg); border-top:1px solid rgba(255,255,255,0.08); padding:4rem 2rem 2rem; }
        .footer-inner { max-width:1400px; margin:0 auto; }
        .footer-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:3rem; margin-bottom:3rem; }
        .footer-brand p { color:var(--footer-text); font-size:.9rem; margin:.75rem 0 1.5rem; max-width:280px; }
        .footer-social { display:flex; gap:.75rem; }
        .social-btn { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.07);
            border:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center;
            color:var(--footer-text); transition:.2s; font-size:.9rem; }
        .social-btn:hover { background:linear-gradient(135deg,#7c3aed,#2563eb); color:#fff; border-color:transparent; }
        .footer-col h4 { font-size:.95rem; font-weight:700; margin-bottom:1rem; color:#fff; letter-spacing:.05em; }
        .footer-col ul { list-style:none; display:flex; flex-direction:column; gap:.6rem; }
        .footer-col ul a { color:var(--footer-text); font-size:.88rem; transition:.2s; }
        .footer-col ul a:hover { color:#a855f7; padding-left:.25rem; }
        .footer-bottom { border-top:1px solid rgba(255,255,255,0.08); padding-top:1.5rem;
            display:flex; align-items:center; justify-content:space-between;
            color:var(--footer-text); font-size:.85rem; }

        /* CARDS - Premium glassmorphism */
        .card {
            background:linear-gradient(145deg, rgba(30,32,60,0.9) 0%, rgba(17,24,39,0.95) 100%);
            border:1px solid rgba(255,255,255,0.08);
            border-radius:var(--radius);
            overflow:hidden;
            transition:all .4s cubic-bezier(0.175,0.885,0.32,1.275);
            box-shadow:var(--shadow);
            position:relative;
        }
        .card::before {
            content:'';
            position:absolute; top:0; left:0; right:0; height:1px;
            background:linear-gradient(90deg,transparent,rgba(124,58,237,0.4),transparent);
            pointer-events:none;
        }
        .card:hover {
            transform:translateY(-6px);
            box-shadow:0 24px 48px rgba(0,0,0,0.5), 0 0 0 1px rgba(124,58,237,0.25);
            z-index:10;
        }

        /* SECTION */
        .section { padding:5rem 2rem; }
        .section-inner { max-width:1400px; margin:0 auto; }
        .section-header { text-align:center; margin-bottom:3rem; }
        .section-tag {
            display:inline-block;
            background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(37,99,235,0.15));
            color:#a855f7;
            padding:.35rem 1.1rem; border-radius:50px; font-size:.78rem; font-weight:700;
            letter-spacing:.1em; text-transform:uppercase; margin-bottom:.75rem;
            border:1px solid rgba(124,58,237,0.3);
            box-shadow:0 0 12px rgba(124,58,237,0.15);
        }
        .section-title { font-family:'Playfair Display',serif; font-size:2.5rem; font-weight:900; margin-bottom:.75rem; }
        .section-sub { color:var(--muted); font-size:1.05rem; max-width:600px; margin:0 auto; }

        /* GRID */
        .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }
        .grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; }
        .grid-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:1.5rem; }

        /* ALERTS */
        .alert { padding:1rem 1.25rem; border-radius:12px; margin-bottom:1rem; font-size:.9rem; }
        .alert-success { background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.4); color:#6ee7b7; font-weight:500; }
        .alert-error   { background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.4); color:#fca5a5; }
        .alert-info    { background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.4); color:#93c5fd; }

        /* FORM */
        .form-group { margin-bottom:1.25rem; }
        .form-label { display:block; font-size:.87rem; font-weight:600; color:rgba(255,255,255,0.7); margin-bottom:.5rem; letter-spacing:.03em; }
        .form-control {
            width:100%; padding:.75rem 1rem;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.15);
            border-radius:10px; color:#fff; font-size:.92rem;
            transition:.2s; font-family:inherit;
            backdrop-filter:blur(6px);
        }
        .form-control:focus { outline:none; border-color:#ff6f00; box-shadow:0 0 0 3px rgba(255,111,0,.2); background:rgba(255,255,255,0.1); }
        .form-control::placeholder { color:rgba(255,255,255,0.35); }
        select.form-control option { background:#1E293B; color:#fff; }

        /* BADGE */
        .badge-pill { padding:.28rem .85rem; border-radius:50px; font-size:.73rem; font-weight:700; letter-spacing:.04em; }
        .badge-primary { background:rgba(124,58,237,.2); color:#c4b5fd; border:1px solid rgba(124,58,237,.3); }
        .badge-success { background:rgba(0,200,83,.15); color:#6ee7b7; border:1px solid rgba(0,200,83,.3); }
        .badge-warning { background:rgba(251,191,36,.15); color:#fde68a; border:1px solid rgba(251,191,36,.3); }
        .badge-danger  { background:rgba(220,38,38,.15); color:#fca5a5; border:1px solid rgba(220,38,38,.3); }

        /* STAR RATING */
        .stars { color:#ffd700; font-size:.9rem; }

        /* CHATBOT WIDGET */
        #chatbot-widget { position:fixed; bottom:2rem; right:2rem; z-index:9999; font-family:'Inter',sans-serif; }
        #chatbot-toggle {
            width:64px; height:64px; border-radius:18px;
            background:rgba(255,255,255,0.02); backdrop-filter:blur(10px);
            color:#fff; font-size:1.8rem;
            cursor:pointer; display:flex; align-items:center; justify-content:center;
            position:relative; z-index:100; border:none;
            transition:all .3s;
            box-shadow: 0 10px 25px rgba(236,72,153,0.3);
        }
        #chatbot-toggle::before {
            content:''; position:absolute; inset:0;
            border-radius:18px; padding:2px;
            background:linear-gradient(135deg, #3b82f6, #ec4899);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events:none;
            transition:all .3s;
        }
        #chatbot-toggle:hover {
            transform:translateY(-5px) scale(1.05);
            background:rgba(255,255,255,0.05);
            box-shadow: 0 15px 35px rgba(236,72,153,0.5);
        }
        #chatbot-toggle:hover::before {
            background:linear-gradient(135deg, #ec4899, #3b82f6);
        }
        #chatbot-toggle i { filter:drop-shadow(0 0 5px rgba(255,255,255,0.3)); transition:all .3s; }
        #chatbot-box { position:absolute; bottom:75px; right:0; width:380px; height:520px;
            background:rgba(10,13,26,0.95); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.1); border-radius:16px;
            box-shadow:0 20px 50px rgba(0,0,0,0.6); display:none; flex-direction:column; overflow:hidden; }
        #chatbot-box.open { display:flex; }
        .chatbot-header { padding:1.2rem 1.25rem; background:linear-gradient(135deg, rgba(30,58,138,0.5), rgba(157,23,77,0.4)); border-bottom:1px solid rgba(255,255,255,0.08); position:relative; }
        .cb-header-inner { display:flex; justify-content:space-between; align-items:flex-start; }
        .cb-title-wrap { display:flex; gap:.75rem; align-items:center; }
        .cb-icon-box { width:40px; height:40px; border-radius:10px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; color:#3b82f6; position:relative; }
        .cb-icon-box::after { content:''; position:absolute; top:-3px; right:-3px; width:8px; height:8px; border-radius:50%; background:#10b981; box-shadow:0 0 8px #10b981; }
        .cb-title { font-family:'Orbitron',monospace; font-weight:900; font-style:italic; font-size:1.1rem; color:#fff; letter-spacing:.1em; margin-bottom:.15rem; text-transform:uppercase; }
        .cb-status { display:flex; align-items:center; gap:.4rem; font-size:.65rem; color:rgba(255,255,255,0.6); font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
        .cb-status-dot { width:6px; height:6px; border-radius:50%; background:#10b981; }
        .cb-actions { display:flex; gap:.4rem; }
        .cb-action-btn { width:28px; height:28px; border-radius:6px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.6); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.8rem; transition:.2s; }
        .cb-action-btn:hover { background:rgba(255,255,255,0.1); color:#fff; }

        .chatbot-body { flex:1; overflow-y:auto; padding:1.25rem; display:flex; flex-direction:column; gap:1.25rem; }
        .chat-block { display:flex; flex-direction:column; gap:.4rem; }
        .chat-sender-lbl { display:flex; align-items:center; gap:.4rem; font-size:.65rem; color:rgba(255,255,255,0.5); font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
        .chat-msg { max-width:85%; padding:.85rem 1rem; border-radius:12px; font-size:.88rem; line-height:1.5; }
        .chat-msg.user { background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; margin-left:auto; border-bottom-right-radius:4px; box-shadow:0 4px 15px rgba(37,99,235,0.3); }
        .chat-msg.bot  { background:rgba(255,255,255,0.03); color:rgba(255,255,255,0.9); border:1px solid rgba(255,255,255,0.08); border-top-left-radius:4px; }
        
        .chat-suggestions { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.25rem; margin-left:auto; justify-content:flex-end; }
        .chat-suggestion { padding:.4rem .8rem; background:rgba(59,130,246,0.1); color:#93c5fd; border-radius:50px; font-size:.75rem; cursor:pointer; border:1px solid rgba(59,130,246,0.2); transition:.2s; }
        .chat-suggestion:hover { background:rgba(59,130,246,0.2); color:#fff; }

        .chatbot-input { padding:1rem; border-top:1px solid rgba(255,255,255,0.05); display:flex; gap:.75rem; background:rgba(0,0,0,0.2); }
        .chatbot-input input { flex:1; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:.75rem 1rem; color:#fff; font-size:.88rem; transition:.3s; }
        .chatbot-input input:focus { outline:none; border-color:rgba(59,130,246,0.5); background:rgba(59,130,246,0.05); }
        .chatbot-input button { width:42px; height:42px; border-radius:12px; background:linear-gradient(135deg,#3b82f6,#2563eb); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(37,99,235,0.4); transition:.3s; }
        .chatbot-input button:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(37,99,235,0.5); }

        /* UTILS */
        .text-primary { color:#ff6f00; }
        .text-secondary { color:#0288d1; }
        .text-muted { color:var(--muted); }
        .text-accent { color:var(--accent); }
        .text-gold { color:var(--gold); }
        /* Sky Blue icon utility */
        .icon-sky, .fa, .fas, .far, .fab { color:inherit; }
        .icon-sky { color:#0288d1 !important; }
        .mt-1 { margin-top:.5rem; } .mt-2 { margin-top:1rem; } .mt-3 { margin-top:1.5rem; } .mt-4 { margin-top:2rem; }
        .mb-1 { margin-bottom:.5rem; } .mb-2 { margin-bottom:1rem; } .mb-3 { margin-bottom:1.5rem; }
        .flex { display:flex; } .items-center { align-items:center; } .justify-between { justify-content:space-between; }
        .gap-1 { gap:.5rem; } .gap-2 { gap:1rem; } .gap-3 { gap:1.5rem; }
        .font-bold { font-weight:700; } .font-semibold { font-weight:600; }
        .text-sm { font-size:.85rem; } .text-xs { font-size:.75rem; }
        .rounded-full { border-radius:50px; }

        @media(max-width:768px) {
            .nav-links { display:none; }
            .grid-3,.grid-4 { grid-template-columns:1fr; }
            .grid-2 { grid-template-columns:1fr; }
            .footer-grid { grid-template-columns:1fr 1fr; }
            .section-title { font-size:1.8rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="nav-inner">
        <div style="display:flex; align-items:center; gap: 1.5rem;">
            <a href="{{ route('home') }}" class="nav-logo">✈️ TravelMate</a>
            <!-- Global Live Clock -->
            <div style="display:flex; align-items:center; gap: 0.5rem; background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.2); padding: 0.35rem 1rem; border-radius: 50px; box-shadow: 0 0 10px rgba(0, 240, 255, 0.1);">
                <i class="far fa-clock" style="color: #00f0ff; font-size: 0.95rem;"></i>
                <div id="global-live-clock" style="font-family: 'Space Grotesk', monospace; font-size: 0.85rem; font-weight: 700; color: #fff; letter-spacing: 1px;">--:--:--</div>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('destinations.index') }}" class="{{ request()->routeIs('destinations*') ? 'active' : '' }}">Destinations</a></li>
            <li><a href="{{ route('packages.index') }}" class="{{ request()->routeIs('packages*') ? 'active' : '' }}">Packages</a></li>
            @auth
            <li><a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions*') ? 'active' : '' }}">Transactions</a></li>
            @endauth
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact*') ? 'active' : '' }}">Contact Us</a></li>
        </ul>
        <div class="nav-actions">
            @auth
                <a href="{{ route('chatbot.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-robot"></i> AI Chat</a>
                <div class="nav-dropdown notif-badge">
                    <a href="javascript:void(0);" onclick="toggleNavDropdown('notif-menu', event)" style="cursor:pointer;"><i class="fas fa-bell" style="font-size:1.1rem;color:var(--nav-text)"></i>
                    @php 
                        $unreadCount = \App\Models\TravelNotification::where('user_id',auth()->id())->where('is_read',false)->count(); 
                        $recentNotifs = \App\Models\TravelNotification::where('user_id',auth()->id())->latest()->take(5)->get();
                    @endphp
                    @if($unreadCount)<span class="badge">{{ $unreadCount }}</span>@endif</a>
                    
                    <div id="notif-menu" class="dropdown-menu" style="width: 320px; right: -50px; background: rgba(13, 43, 107, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); padding: 0;">
                        <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="color: #fff; font-size: .95rem; font-weight: 700; margin: 0;">Notifications</h4>
                            @if($unreadCount) <span style="background: var(--primary); color: #fff; font-size: .7rem; padding: 2px 8px; border-radius: 50px;">{{ $unreadCount }} New</span> @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse($recentNotifs as $n)
                                <a href="#" style="padding: .8rem 1rem; display: flex; gap: .75rem; color: #cde3ff; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.2s;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $n->is_read ? 'transparent' : 'var(--primary)' }}; margin-top: 6px;"></div>
                                    <div>
                                        <p style="font-size: .85rem; margin-bottom: .25rem; font-weight: {{ $n->is_read ? '400' : '600' }}; color: #fff;">{{ $n->title }}</p>
                                        <p style="font-size: .75rem; color: rgba(255,255,255,0.6); margin: 0; line-height: 1.3;">{{ Str::limit($n->message, 50) }}</p>
                                        <span style="font-size: .65rem; color: rgba(255,255,255,0.4); margin-top: .25rem; display: block;">{{ $n->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 1.5rem 1rem; text-align: center; color: rgba(255,255,255,0.5); font-size: .85rem;">
                                    <i class="fas fa-bell-slash" style="font-size: 1.5rem; margin-bottom: .5rem; opacity: 0.5;"></i><br>
                                    No new notifications
                                </div>
                            @endforelse
                        </div>
                        <div style="padding: .75rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.1);">
                            <a href="{{ route('notifications') }}" style="color: var(--primary); font-size: .8rem; font-weight: 600; display: inline-block;">View All Notifications &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="nav-dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="avatar-sm" onclick="toggleNavDropdown('profile-menu', event)">
                    <div id="profile-menu" class="dropdown-menu">
                        @if(auth()->user()->isGuide())
                        <a href="{{ route('guide.dashboard') }}"><i class="fas fa-gauge-high"></i> Guide Dashboard</a>
                        <a href="{{ route('guide.assigned-bookings') }}"><i class="fas fa-briefcase"></i> Assigned Bookings</a>
                        @else
                        <a href="{{ route('dashboard') }}"><i class="fas fa-gauge-high"></i> Dashboard</a>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" style="color: #ff9800; font-weight: 700;"><i class="fas fa-shield-halved"></i> Admin Panel</a>
                        @endif
                        
                        <a href="{{ route('profile') }}"><i class="fas fa-user"></i> My Profile</a>
                        <a href="{{ route('bookings.index') }}"><i class="fas fa-ticket"></i> My Bookings</a>
                        <a href="{{ route('wishlist') }}"><i class="fas fa-heart"></i> Wishlist</a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" style="width:100%;background:none;border:none;cursor:pointer;padding:.7rem 1.2rem;text-align:left;color:#ff6b6b;font-size:.88rem;display:flex;align-items:center;gap:.75rem;"><i class="fas fa-right-from-bracket"></i> Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
            @endauth
        </div>
    </div>
</nav>

<main>
    @if(session('success'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:9000;max-width:380px">
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:9000;max-width:380px">
            <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> {{ session('error') }}</div>
        </div>
    @endif
    @yield('content')
</main>

{{-- FOOTER --}}
<footer>
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="nav-logo">✈️ TravelMate</div>
                <p>AI-powered travel ecosystem that delivers hyper-personalized itineraries, smart bookings, and real-time travel intelligence.</p>
                <div class="footer-social">
                    <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col"><h4>Explore</h4><ul>
                <li><a href="{{ route('destinations.index') }}">Destinations</a></li>
                <li><a href="{{ route('packages.index') }}">Travel Packages</a></li>
                <li><a href="{{ route('itineraries.create') }}">Plan Itinerary</a></li>
                <li><a href="{{ route('chatbot.index') }}">AI Chatbot</a></li>
            </ul></div>
            <div class="footer-col"><h4>Account</h4><ul>
                @auth
                    @if(auth()->user()->isGuide())
                        <li><a href="{{ route('guide.dashboard') }}">Guide Dashboard</a></li>
                        <li><a href="{{ route('guide.assigned-bookings') }}">Assigned Bookings</a></li>
                        <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                        <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                    @else
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                        <li><a href="{{ route('expenses.dashboard') }}">Expense Tracker</a></li>
                        <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                    @endif
                @else
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                    <li><a href="{{ route('expenses.dashboard') }}">Expense Tracker</a></li>
                    <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                @endauth
            </ul></div>
            <div class="footer-col"><h4>Support</h4><ul>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                <li><a href="{{ route('about') }}">About TravelMate</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul></div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} TravelMate. All rights reserved.</span>
            <span>Built with ❤️ for Final Year Project • Laravel MVC</span>
        </div>
    </div>
</footer>

{{-- AI CHATBOT WIDGET (only for auth users) --}}
@auth
<div id="chatbot-widget">
    <button id="chatbot-toggle" onclick="toggleChatbot()" title="AI Travel Assistant">
        <i class="fas fa-robot"></i>
    </button>
    <div id="chatbot-box">
        <div class="chatbot-header">
            <div class="cb-header-inner">
                <div class="cb-title-wrap">
                    <div class="cb-icon-box"><i class="fas fa-microchip"></i></div>
                    <div>
                        <div class="cb-title">COREAI PROTOCOL</div>
                        <div class="cb-status"><div class="cb-status-dot"></div> NEURAL LINK ESTABLISHED</div>
                    </div>
                </div>
                <div class="cb-actions">
                    <button class="cb-action-btn" onclick="toggleChatbot()"><i class="fas fa-minus"></i></button>
                    <button class="cb-action-btn" onclick="toggleChatbot()"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
        <div class="chatbot-body" id="chatbot-messages">
            <div class="chat-block">
                <div class="chat-sender-lbl"><i class="fas fa-robot"></i> NEURAL ENTITY</div>
                <div class="chat-msg bot">Neural Link established. I am CoreAI v4.0. How can I assist with your logistics manifest?</div>
            </div>
            <div class="chat-suggestions">
                <span class="chat-suggestion" onclick="sendSuggestion(this)">Generate Itinerary</span>
                <span class="chat-suggestion" onclick="sendSuggestion(this)">Analyze Budget</span>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-input" placeholder="Transmit command..." onkeydown="if(event.key==='Enter')sendChat()">
            <button onclick="sendChat()"><i class="far fa-paper-plane"></i></button>
        </div>
    </div>
</div>
@endauth

<script>
let chatSession = null;
function toggleChatbot() {
    const box = document.getElementById('chatbot-box');
    box.classList.toggle('open');
}
function sendSuggestion(el) { document.getElementById('chatbot-input').value = el.textContent.trim(); sendChat(); }
async function sendChat() {
    const input = document.getElementById('chatbot-input');
    const msg = input.value.trim(); if (!msg) return;
    addChatMsg(msg, 'user'); input.value = '';
    const typing = addChatMsg('...', 'bot');
    try {
        const res = await fetch('{{ route("chatbot.send") }}', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ message: msg, session_id: chatSession })
        });
        const data = await res.json();
        chatSession = data.session_id;
        typing.remove();
        const botEl = addChatMsg(data.response.text, 'bot', true);
        if (data.response.suggestions?.length) {
            const sug = document.createElement('div');
            sug.className = 'chat-suggestions';
            data.response.suggestions.forEach(s => {
                const span = document.createElement('span');
                span.className = 'chat-suggestion'; span.textContent = s;
                span.onclick = () => sendSuggestion(span);
                sug.appendChild(span);
            });
            botEl.after(sug);
        }
    } catch(e) { typing.textContent = 'Connection error. Please try again.'; }
}
function addChatMsg(text, role, markdown=false) {
    const body = document.getElementById('chatbot-messages');
    
    if (role === 'user') {
        const div = document.createElement('div');
        div.className = 'chat-msg user';
        div.textContent = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return div;
    } else {
        const block = document.createElement('div');
        block.className = 'chat-block';
        
        const lbl = document.createElement('div');
        lbl.className = 'chat-sender-lbl';
        lbl.innerHTML = '<i class="fas fa-robot"></i> NEURAL ENTITY';
        block.appendChild(lbl);
        
        const msg = document.createElement('div');
        msg.className = 'chat-msg bot';
        msg.innerHTML = markdown ? text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>') : text;
        block.appendChild(msg);
        
        body.appendChild(block);
        body.scrollTop = body.scrollHeight;
        return msg;
    }
}
// Auto-dismiss alerts
setTimeout(() => { document.querySelectorAll('.alert').forEach(a => a.style.opacity = '0'); }, 4000);

// Global Live Clock
setInterval(() => {
    const el = document.getElementById('global-live-clock');
    if(el) {
        el.innerText = new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}, 1000);

// Dropdown click handlers
function toggleNavDropdown(id, event) {
    event.preventDefault();
    event.stopPropagation();
    const menu = document.getElementById(id);
    const wasOpen = menu.classList.contains('show');
    
    // Close all open dropdowns
    document.querySelectorAll('.dropdown-menu.show').forEach(el => {
        el.classList.remove('show');
    });
    
    // Toggle the clicked one
    if (!wasOpen) {
        menu.classList.add('show');
    }
}

// Close dropdowns when clicking outside
window.addEventListener('click', function(e) {
    if (!e.target.closest('.nav-dropdown')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(el => {
            el.classList.remove('show');
        });
    }
});
</script>
@stack('scripts')
</body>
</html>
