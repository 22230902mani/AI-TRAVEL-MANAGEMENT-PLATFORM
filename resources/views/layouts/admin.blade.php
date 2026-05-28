<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TravelMate Control Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --sidebar-w: 260px;
            --topbar-h: 64px;
            --sidebar-bg: #0d1b2a;
            --sidebar-border: rgba(255,255,255,0.06);
            --sidebar-text: #8da0b5;
            --sidebar-active: #ff6f00;
            --topbar-bg: rgba(5, 5, 8, 0.75);
            --bg: #050508;
            --card-bg: rgba(255, 255, 255, 0.025);
            --text: #f8fafc;
            --muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.08);
            --primary: #ff6f00;
            --blue: #0d2b6b;
            --cyan: #0288d1;
            --green: #2e7d32;
            --red: #c62828;
            --gold: #f9a825;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.06);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
            --radius: 14px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        body::before, body::after {
            content: ''; position: fixed; border-radius: 50%; filter: blur(120px); z-index: 0; pointer-events: none;
        }
        body::before {
            width: 600px; height: 600px; background: rgba(99,102,241,0.1);
            top: -100px; right: -100px;
        }
        body::after {
            width: 500px; height: 500px; background: rgba(6,182,212,0.1);
            bottom: -100px; left: 200px;
        }

        /* ─────── SIDEBAR ─────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            border-right: 1px solid var(--sidebar-border);
        }

        .sidebar-logo {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .sidebar-logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #ff6f00, #ffca28);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .sidebar-logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem; font-weight: 900;
            color: #fff;
            line-height: 1;
        }
        .sidebar-logo-text span { color: #ff6f00; }
        .sidebar-badge {
            font-size: .6rem; font-weight: 700; letter-spacing: .08em;
            background: rgba(255,111,0,.2); color: #ff6f00;
            border: 1px solid rgba(255,111,0,.3);
            border-radius: 4px; padding: 1px 6px;
            margin-top: 2px; display: block;
        }

        .sidebar-section {
            padding: 1.25rem 1rem .5rem;
            font-size: .65rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: rgba(255,255,255,.25);
        }

        .sidebar-nav { flex: 1; overflow-y: auto; padding-bottom: 1rem; }
        .nav-item {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.25rem;
            color: var(--sidebar-text);
            font-size: .875rem; font-weight: 500;
            text-decoration: none;
            border-radius: 0;
            transition: all .2s;
            position: relative;
            margin: 1px 0;
        }
        .nav-item:hover {
            background: rgba(255,255,255,.05);
            color: #fff;
        }
        .nav-item.active {
            background: rgba(255,111,0,.12);
            color: #ff6f00;
            font-weight: 600;
        }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px;
            background: #ff6f00;
            border-radius: 0 2px 2px 0;
        }
        .nav-item .nav-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            background: rgba(255,255,255,.05);
            flex-shrink: 0;
            transition: background .2s;
        }
        .nav-item.active .nav-icon { background: rgba(255,111,0,.2); }
        .nav-item:hover .nav-icon { background: rgba(255,255,255,.1); }
        .nav-badge {
            margin-left: auto;
            background: #ff6f00; color: #fff;
            font-size: .65rem; font-weight: 700;
            border-radius: 50px; padding: 1px 7px;
        }

        .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 1rem 1.25rem;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-user img {
            width: 36px; height: 36px; border-radius: 50%;
            border: 2px solid rgba(255,111,0,.5);
            object-fit: cover;
        }
        .sidebar-user-name { font-size: .82rem; font-weight: 600; color: #fff; }
        .sidebar-user-role { font-size: .7rem; color: var(--sidebar-text); }
        .sidebar-logout {
            margin-left: auto;
            background: rgba(255,255,255,.07);
            border: none; cursor: pointer;
            width: 30px; height: 30px; border-radius: 8px;
            color: var(--sidebar-text);
            display: flex; align-items: center; justify-content: center;
            transition: .2s; font-size: .85rem;
        }
        .sidebar-logout:hover { background: rgba(198,40,40,.2); color: #ef5350; }

        /* ─────── MAIN AREA ─────── */
        .main-area {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─────── TOP BAR ─────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 2rem;
            position: sticky; top: 0; z-index: 50;
            gap: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .topbar-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem; font-weight: 800;
            color: var(--text);
        }
        .topbar-breadcrumb {
            font-size: .78rem; color: var(--muted);
            margin-left: .5rem;
        }
        .topbar-spacer { flex: 1; }
        .topbar-time {
            font-size: .78rem; color: var(--muted);
            background: var(--bg); border-radius: 8px;
            padding: .35rem .85rem;
            border: 1px solid var(--border);
        }
        .topbar-notif {
            position: relative;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); cursor: pointer;
            transition: .2s; text-decoration: none;
        }
        .topbar-notif:hover { background: #fff; border-color: #ff6f00; color: #ff6f00; }
        .topbar-notif .dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: #ff6f00; border-radius: 50%;
            border: 2px solid var(--topbar-bg);
        }
        .topbar-avatar {
            width: 38px; height: 38px; border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(255,111,0,.4);
            cursor: pointer;
        }
        .view-site-btn {
            font-size: .78rem; font-weight: 600;
            background: rgba(255,111,0,.1); color: #ff6f00;
            border: 1px solid rgba(255,111,0,.3);
            border-radius: 8px; padding: .35rem .85rem;
            text-decoration: none; transition: .2s;
        }
        .view-site-btn:hover { background: #ff6f00; color: #fff; }

        /* ─────── PAGE CONTENT ─────── */
        .page-content { padding: 1.75rem 2rem; flex: 1; }

        /* ─────── CARDS ─────── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
        }
        .card::before {
            content: ''; position: absolute; inset: 0; border-radius: var(--radius);
            padding: 1px; background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none;
        }
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: .95rem; font-weight: 700;
            color: var(--text);
            display: flex; align-items: center; gap: .5rem;
        }
        .card-body { padding: 1.5rem; }

        /* ─────── KPI CARDS ─────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .kpi-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }
        .kpi-card::before {
            content: ''; position: absolute; inset: 0; border-radius: var(--radius);
            padding: 1px; background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none;
            z-index: 10;
        }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
        .kpi-card::after {
            content: '';
            position: absolute; top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: .08;
            transform: translate(20px, -20px);
        }
        .kpi-card.orange::after { background: #ff6f00; }
        .kpi-card.blue::after   { background: #0d2b6b; }
        .kpi-card.green::after  { background: #2e7d32; }
        .kpi-card.cyan::after   { background: #0288d1; }

        .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .75rem; }
        .kpi-label { font-size: .78rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
        .kpi-icon {
            width: 42px; height: 42px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .kpi-icon.orange { background: rgba(255,111,0,.12); color: #ff6f00; }
        .kpi-icon.blue   { background: rgba(13,43,107,.12); color: #0d2b6b; }
        .kpi-icon.green  { background: rgba(46,125,50,.12); color: #2e7d32; }
        .kpi-icon.cyan   { background: rgba(2,136,209,.12); color: #0288d1; }

        .kpi-value { font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 900; color: var(--text); line-height: 1; margin-bottom: .4rem; }
        .kpi-sub { font-size: .75rem; color: var(--muted); }
        .kpi-trend {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: .72rem; font-weight: 600;
            padding: 2px 7px; border-radius: 50px;
        }
        .kpi-trend.up   { background: rgba(46,125,50,.12); color: #2e7d32; }
        .kpi-trend.down { background: rgba(198,40,40,.1);  color: #c62828; }

        /* ─────── TABLES ─────── */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            padding: .75rem 1rem; text-align: left;
            font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--muted);
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid var(--border);
        }
        .admin-table td {
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: .875rem; color: var(--text);
            vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: rgba(255,255,255,0.05); }

        /* ─────── BADGES ─────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 50px;
            font-size: .72rem; font-weight: 700;
        }
        .badge.success { background: rgba(46,125,50,.12); color: #2e7d32; }
        .badge.warning { background: rgba(249,168,37,.15); color: #e65100; }
        .badge.danger  { background: rgba(198,40,40,.12);  color: #c62828; }
        .badge.info    { background: rgba(2,136,209,.12);   color: #0288d1; }
        .badge.primary { background: rgba(255,111,0,.12);   color: #ff6f00; }

        /* ─────── BUTTONS ─────── */
        .btn {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .55rem 1.25rem; border-radius: 9px;
            font-size: .82rem; font-weight: 600;
            cursor: pointer; border: none; transition: all .2s;
            text-decoration: none;
        }
        .btn-orange { background: #ff6f00; color: #fff; box-shadow: 0 4px 14px rgba(255,111,0,.3); }
        .btn-orange:hover { background: #e65c00; transform: translateY(-1px); }
        .btn-ghost { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { border-color: #ff6f00; color: #ff6f00; background: rgba(255,111,0,.05); }
        .btn-sm { padding: .35rem .85rem; font-size: .75rem; }
        .btn-icon { width: 32px; height: 32px; border-radius: 8px; padding: 0; justify-content: center; }

        /* ─────── DIVIDER LABEL ─────── */
        .section-label {
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--muted); margin-bottom: 1rem;
            display: flex; align-items: center; gap: .75rem;
        }
        .section-label::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ─────── ALERTS ─────── */
        .alert { padding: .85rem 1.25rem; border-radius: 10px; font-size: .875rem; margin-bottom: 1rem; }
        .alert-success { background: rgba(46,125,50,.1); border: 1px solid rgba(46,125,50,.25); color: #1b5e20; }
        .alert-error   { background: rgba(198,40,40,.08); border: 1px solid rgba(198,40,40,.2); color: #b71c1c; }

        /* ─────── UTILS ─────── */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: .5rem; }
        .gap-2 { gap: 1rem; }
        .gap-3 { gap: 1.5rem; }
        .text-muted { color: var(--muted); }
        .font-bold { font-weight: 700; }
        .mt-1 { margin-top: .5rem; }
        .mt-2 { margin-top: 1rem; }

        /* ─────── FORM STYLES ─────── */
        .form-group {
            margin-bottom: .75rem;
        }
        .form-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: .45rem;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .form-label small {
            text-transform: none;
            font-weight: 400;
            color: rgba(148,163,184,.6);
            letter-spacing: 0;
        }
        .form-control {
            width: 100%;
            padding: .65rem 1rem;
            font-size: .88rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: all .25s ease;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .form-control::placeholder {
            color: rgba(148,163,184,.4);
        }
        .form-control:hover {
            border-color: rgba(255,255,255,.15);
            background: rgba(255,255,255,.06);
        }
        .form-control:focus {
            border-color: rgba(255,111,0,.5);
            background: rgba(255,255,255,.06);
            box-shadow: 0 0 0 3px rgba(255,111,0,.1), 0 4px 20px rgba(0,0,0,.2);
        }
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }
        select.form-control option {
            background: #0d1b2a;
            color: var(--text);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        input[type="file"].form-control {
            padding: .5rem .75rem;
            cursor: pointer;
        }
        input[type="file"].form-control::file-selector-button {
            background: rgba(255,111,0,.15);
            color: #ff6f00;
            border: 1px solid rgba(255,111,0,.3);
            border-radius: 6px;
            padding: .3rem .85rem;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            margin-right: .75rem;
            transition: all .2s;
        }
        input[type="file"].form-control::file-selector-button:hover {
            background: #ff6f00;
            color: #fff;
        }

        /* ─────── FORM SECTION CARDS ─────── */
        .form-section {
            margin-bottom: 2rem;
            padding: 1.75rem;
            background: rgba(255,255,255,.025);
            border-radius: 16px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .form-section::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            padding: 1px;
            background: linear-gradient(180deg, rgba(255,255,255,.06) 0%, rgba(255,255,255,0) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        .form-section-promo {
            background: linear-gradient(135deg, rgba(255,111,0,.04), rgba(255,111,0,.08));
            border: 1px solid rgba(255,111,0,.2);
        }
        .form-section-promo::before {
            background: linear-gradient(180deg, rgba(255,111,0,.15) 0%, rgba(255,111,0,0) 100%);
        }
        .form-section-title {
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding-bottom: .85rem;
            border-bottom: 1px solid var(--border);
        }
        .form-section-title i {
            font-size: .9rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .form-section-title .icon-orange {
            background: rgba(255,111,0,.15);
            color: #ff6f00;
        }
        .form-section-title .icon-blue {
            background: rgba(2,136,209,.15);
            color: #0288d1;
        }
        .form-section-title .icon-green {
            background: rgba(46,125,50,.15);
            color: #2e7d32;
        }
        .form-section-title .icon-purple {
            background: rgba(123,31,162,.15);
            color: #ab47bc;
        }

        /* ─────── BADGE PILLS ─────── */
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .badge-primary { background: rgba(255,111,0,.12); color: #ff6f00; }
        .badge-success { background: rgba(46,125,50,.12); color: #43a047; }
        .badge-danger  { background: rgba(198,40,40,.12); color: #ef5350; }
        .badge-info    { background: rgba(2,136,209,.12); color: #0288d1; }

        /* ─────── SECTION TAG ─────── */
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            background: rgba(255,111,0,.1);
            color: #ff6f00;
            border: 1px solid rgba(255,111,0,.2);
            border-radius: 6px;
            padding: .2rem .65rem;
            margin-bottom: .5rem;
        }

        /* ─────── BTN PRIMARY & OUTLINE (Page-level) ─────── */
        .btn-primary {
            background: linear-gradient(135deg, #ff6f00, #ff8f00);
            color: #fff;
            box-shadow: 0 4px 14px rgba(255,111,0,.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #e65c00, #ff6f00);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,111,0,.4);
        }
        .btn-outline {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
        }
        .btn-outline:hover {
            border-color: rgba(255,111,0,.4);
            color: #ff6f00;
            background: rgba(255,111,0,.05);
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .main-area { margin-left: 0; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">✈</div>
        <div>
            <div class="sidebar-logo-text">Travel<span>Mate</span></div>
            <span class="sidebar-badge">ADMIN PANEL</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-item">
            <span class="nav-icon"><i class="fas fa-user"></i></span>
            User Dashboard
        </a>

        <div class="sidebar-section">Management</div>
        <a href="{{ route('admin.users') }}"
           class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            Users
        </a>
        <a href="{{ route('admin.guides') }}"
           class="nav-item {{ request()->routeIs('admin.guides*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user-tie"></i></span>
            Guide Requests
            @php $pendingGuides = \App\Models\User::where('role','guide')->where('guide_status','pending')->count(); @endphp
            @if($pendingGuides > 0)
            <span class="nav-badge">{{ $pendingGuides }}</span>
            @endif
        </a>
        <a href="{{ route('admin.bookings') }}"
           class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-ticket"></i></span>
            Bookings
        </a>
        <a href="{{ route('admin.packages') }}"
           class="nav-item {{ request()->routeIs('admin.packages*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-suitcase"></i></span>
            Packages
        </a>
        <a href="{{ route('admin.destinations') }}"
           class="nav-item {{ request()->routeIs('admin.destinations*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-globe"></i></span>
            Destinations
        </a>

        <div class="sidebar-section">Support</div>
        <a href="{{ route('admin.tickets') }}"
           class="nav-item {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-headset"></i></span>
            Tickets
            @php $openTickets = \App\Models\SupportTicket::where('status','open')->count(); @endphp
            @if($openTickets > 0)
            <span class="nav-badge">{{ $openTickets }}</span>
            @endif
        </a>
        <a href="{{ route('admin.reviews') }}"
           class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-flag"></i></span>
            Reviews
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <a href="{{ route('profile') }}" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;flex:1;min-width:0">
                <img src="{{ auth()->user()->avatar_url }}" alt="admin">
                <div style="min-width:0">
                    <div class="sidebar-user-name" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;flex-shrink:0">
                @csrf
                <button type="submit" class="sidebar-logout" title="Logout">
                    <i class="fas fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── MAIN AREA ── --}}
<div class="main-area">

    {{-- TOP BAR --}}
    <header class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        </div>
        <div class="topbar-spacer"></div>
        <div class="topbar-time" id="admin-live-clock">
            <i class="fas fa-clock" style="color:#ff6f00;margin-right:.3rem"></i>
            {{ now()->format('D, M d · H:i') }}
        </div>
        @if(session('success'))
        <div class="alert alert-success" style="margin:0;padding:.45rem 1rem;font-size:.78rem">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error" style="margin:0;padding:.45rem 1rem;font-size:.78rem">
            <i class="fas fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
        @endif
        <a href="javascript:void(0)" onclick="toggleNeuralDrawer()" class="topbar-notif">
            <i class="fas fa-bell"></i>
            <span class="dot"></span>
        </a>
        <a href="{{ route('profile') }}" title="My Profile" style="display:flex;align-items:center">
            <img src="{{ auth()->user()->avatar_url }}" alt="admin" class="topbar-avatar">
        </a>
    </header>

    {{-- PAGE CONTENT --}}
    <div class="page-content">
        @yield('content')
    </div>
</div>

<style>
/* Neural Center Off-Canvas Drawer */
.neural-drawer {
    position: fixed;
    top: 0;
    right: -420px;
    width: 400px;
    height: 100vh;
    background: rgba(10, 11, 26, 0.85);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-left: 1px solid rgba(255, 111, 0, 0.3);
    box-shadow: -15px 0 50px rgba(0, 0, 0, 0.8);
    z-index: 1000;
    transition: right 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}
.neural-drawer.open {
    right: 0;
}
.neural-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.neural-title {
    font-family: 'Orbitron', 'Outfit', sans-serif;
    font-size: 1.2rem;
    font-weight: 900;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.neural-title i { color: #00f2fe; }
.neural-close {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
}
.neural-close:hover {
    background: rgba(255,111,0,0.2);
    border-color: #ff6f00;
    color: #ff6f00;
}
.neural-body {
    padding: 1.5rem;
    flex: 1;
}
.neural-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}
.neural-feed-title {
    font-family: 'Orbitron', 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.neural-badge {
    background: rgba(0, 242, 254, 0.1);
    color: #00f2fe;
    border: 1px solid rgba(0, 242, 254, 0.25);
    font-size: 0.65rem;
    font-weight: 800;
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.neural-card {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: 0.3s;
}
.neural-card:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(0, 242, 254, 0.3);
}
.neural-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}
.neural-card-icon-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 800;
    font-size: 0.9rem;
    color: #fff;
}
.neural-card-time {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
}
.neural-card-desc {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.65);
    line-height: 1.5;
}
.neural-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.08);
}
.neural-stat-box {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 1rem;
}
.neural-stat-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}
.neural-stat-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.4rem;
    font-weight: 900;
    color: #fff;
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
}
.neural-stat-trend {
    font-size: 0.75rem;
    font-weight: 700;
}
.neural-stat-trend.green { color: #00ff87; }
.neural-stat-trend.cyan { color: #00f2fe; }
.neural-footer {
    padding: 1rem;
    text-align: center;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.3);
    border-top: 1px solid rgba(255,255,255,0.05);
    text-transform: uppercase;
}
/* Overlay */
.neural-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.4s;
}
.neural-overlay.show {
    opacity: 1;
    pointer-events: auto;
}
</style>

{{-- Neural Center Overlay & Drawer --}}
<div class="neural-overlay" id="neuralOverlay" onclick="toggleNeuralDrawer()"></div>

<div class="neural-drawer" id="neuralDrawer">
    <div class="neural-header">
        <div class="neural-title"><i class="fas fa-bell"></i> NEURAL CENTER</div>
        <div class="neural-close" onclick="toggleNeuralDrawer()"><i class="fas fa-chevron-right"></i></div>
    </div>

    <div class="neural-body">
        <div class="neural-section-head">
            <div class="neural-feed-title"><i class="fas fa-server" style="color: #00f2fe;"></i> LIVE NEURAL FEED</div>
            <span class="neural-badge">ENCRYPTED STREAM</span>
        </div>

        @php
            $adminNotifications = \App\Models\TravelNotification::where('user_id', auth()->id())->latest()->take(10)->get();
        @endphp
        @forelse($adminNotifications as $notif)
        <div class="neural-card">
            <div class="neural-card-top">
                <div class="neural-card-icon-title">
                    <i class="fas {{ $notif->type === 'new_contact_message' ? 'fa-envelope-open-text' : 'fa-bell' }}" style="color: #ffca28;"></i> {{ $notif->title }}
                </div>
                <div class="neural-card-time">{{ $notif->created_at->format('H:i') }}</div>
            </div>
            <div class="neural-card-desc" style="margin-bottom:0.75rem;">
                {{ $notif->message }}
            </div>
            @if($notif->link)
            <div>
                <a href="{{ $notif->link }}" class="btn btn-sm btn-orange" style="padding:0.25rem 0.65rem; font-size:0.75rem;"><i class="fas fa-reply"></i> Reply</a>
            </div>
            @endif
        </div>
        @empty
        <div class="neural-card">
            <div class="neural-card-top">
                <div class="neural-card-icon-title">
                    <i class="fas fa-clock" style="color: #ffca28;"></i> New Acquisition Staged
                </div>
                <div class="neural-card-time">19:10</div>
            </div>
            <div class="neural-card-desc">
                Operative Sai has staged a new acquisition manifest (#e8e205) for ₹2,500. Verification required.
            </div>
        </div>
        @endforelse

        {{-- Live Stats --}}
        <div class="neural-stats-grid">
            <div class="neural-stat-box">
                <div class="neural-stat-label">LIVE TRAFFIC</div>
                <div class="neural-stat-val">
                    <i class="fas fa-arrow-trend-up" style="color: #00ff87; font-size: 1rem;"></i> 1,402 <span class="neural-stat-trend green">+12%</span>
                </div>
            </div>
            <div class="neural-stat-box">
                <div class="neural-stat-label">SIGNAL LATENCY</div>
                <div class="neural-stat-val">
                    <i class="fas fa-wave-square" style="color: #00f2fe; font-size: 1rem;"></i> 24ms <span class="neural-stat-trend cyan">-4ms</span>
                </div>
            </div>
        </div>
    </div>

    <div class="neural-footer">
        END OF SECURE TRANSMISSION BUFFER
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateAdminClock() {
        const el = document.getElementById('admin-live-clock');
        if (el) {
            const now = new Date();
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            const dayName = days[now.getDay()];
            const monthName = months[now.getMonth()];
            const dayNum = now.getDate();
            
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12; // high-noon or midnight
            
            el.innerHTML = `<i class="fas fa-clock" style="color:#ff6f00;margin-right:.3rem"></i> ${dayName}, ${monthName} ${dayNum} · ${hours}:${minutes} ${ampm}`;
        }
    }
    updateAdminClock();
    setInterval(updateAdminClock, 30000); // update every 30s to stay light on performance
});

function toggleNeuralDrawer() {
    const drawer = document.getElementById('neuralDrawer');
    const overlay = document.getElementById('neuralOverlay');
    if (drawer && overlay) {
        drawer.classList.toggle('open');
        overlay.classList.toggle('show');
    }
}
</script>

@stack('scripts')
</body>
</html>
