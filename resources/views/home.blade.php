@extends('layouts.app')
@section('title', 'AI-Powered Smart Travel Ecosystem — TravelMate')
@section('content')

{{-- Leaflet Maps Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- AOS Animation Library --}}
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Outfit',sans-serif;scroll-behavior:smooth}

.landing-container {
    background: #060713;
    color: #fff;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}

.inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Gradients & Neon Utilities */
.text-gradient {
    background: linear-gradient(135deg, #fff 30%, #ffca28 70%, #ff6f00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.neon-glow {
    box-shadow: 0 0 20px rgba(255, 111, 0, 0.15);
}

/* Glassmorphism system */
.glass-panel {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
}
.glass-panel-hover {
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.glass-panel-hover:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 111, 0, 0.3);
    background: rgba(255, 255, 255, 0.03);
    box-shadow: 0 15px 35px rgba(255, 111, 0, 0.1);
}

/* Section Header */
.section-header {
    text-align: center;
    margin-bottom: 3.5rem;
}
.section-header .tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 111, 0, 0.15);
    color: #ffca28;
    border: 1px solid rgba(255, 111, 0, 0.3);
    margin-bottom: 1rem;
}
.section-header h2 {
    font-size: 2.25rem;
    font-weight: 800;
    letter-spacing: -0.01em;
}

/* 1️⃣ Hero Section */
.hero-sec {
    position: relative;
    padding: 8rem 0 6rem;
    background: radial-gradient(circle at top, rgba(108, 99, 255, 0.12) 0%, transparent 60%);
    overflow: hidden;
}
.hero-media-bg {
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(6, 7, 19, 0.8), rgba(6, 7, 19, 0.95)), url('https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?q=80&w=1920');
    background-size: cover;
    background-position: center;
    z-index: 0;
    opacity: 0.4;
}

/* 2️⃣ AI Search Box & Autocomplete Suggestions */
.search-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 20px;
    background: rgba(10, 12, 30, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.search-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.search-field label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ff9100;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.search-field input, .search-field select {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 0.75rem;
    color: #fff;
    font-size: 0.9rem;
    outline: none;
    transition: 0.2s;
}
.search-field input:focus, .search-field select:focus {
    border-color: #ff6f00;
    background: rgba(255,255,255,0.08);
}
.search-field select option {
    background: #0d0f22;
    color: #fff;
}

.btn-ai-gen {
    background: linear-gradient(135deg, #3b82f6, #ec4899);
    color: #fff;
    border: none;
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
    padding: 1rem 3rem;
    font-size: 1.05rem;
    border-radius: 50px;
    font-weight: 800;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.btn-ai-gen:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 35px rgba(236, 72, 153, 0.6);
    filter: brightness(1.1);
}

.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #0d0f22;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    margin-top: 5px;
}
.autocomplete-suggestion {
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-size: 0.85rem;
    color: #b0c4de;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    text-align: left;
    transition: background 0.2s;
}
.autocomplete-suggestion:hover {
    background: rgba(255, 111, 0, 0.15);
    color: #fff;
}
.autocomplete-suggestion:last-child {
    border-bottom: none;
}

/* Progress Stepper */
.ai-synthesis-loader {
    display: none;
    background: rgba(10, 12, 30, 0.6);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 111, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    margin-top: 1.5rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
}
.synthesis-step {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    margin-bottom: 1rem;
    opacity: 0;
    transform: translateX(-20px);
    transition: all 0.4s ease;
}
.synthesis-step:last-child { margin-bottom: 0; }
.synthesis-step.active {
    opacity: 1;
    transform: translateX(0);
    border-color: rgba(255, 111, 0, 0.3);
    background: rgba(255, 111, 0, 0.05);
}
.synthesis-step.completed {
    opacity: 1;
    transform: translateX(0);
    border-color: rgba(0, 255, 102, 0.3);
    background: rgba(0, 255, 102, 0.05);
}
.step-icon-box {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #b0c4de;
    transition: 0.3s;
}
.synthesis-step.active .step-icon-box { color: #ff9100; background: rgba(255, 111, 0, 0.15); box-shadow: 0 0 15px rgba(255, 111, 0, 0.2); }
.synthesis-step.completed .step-icon-box { color: #00ff66; background: rgba(0, 255, 102, 0.15); box-shadow: 0 0 15px rgba(0, 255, 102, 0.2); }
.step-text { flex-grow: 1; text-align: left; }
.step-title { font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 0.25rem; }
.step-desc { font-size: 0.8rem; color: #b0c4de; }
.step-status { font-size: 1.2rem; color: #b0c4de; transition: 0.3s; }
.synthesis-step.active .step-status { color: #ff9100; animation: spin 1s linear infinite; }
.synthesis-step.completed .step-status { color: #00ff66; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* 3️⃣ Free Estimation Preview */
.preview-container {
    opacity: 0.5;
    transition: opacity 0.5s ease;
    pointer-events: none;
}
.preview-container.active {
    opacity: 1;
    pointer-events: auto;
}
.est-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    transition: 0.3s;
}
.est-card:hover {
    border-color: #ff6f00;
    background: rgba(255, 111, 0, 0.05);
}

/* 4️⃣ Feature Grid Animation */
.feature-marquee-wrapper {
    position: relative;
    width: 100%;
    overflow: hidden;
    padding: 2rem 0;
}
.feature-marquee-wrapper::before,
.feature-marquee-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 150px;
    z-index: 2;
    pointer-events: none;
}
.feature-marquee-wrapper::before {
    left: 0;
    background: linear-gradient(to right, #04050f, transparent);
}
.feature-marquee-wrapper::after {
    right: 0;
    background: linear-gradient(to left, #04050f, transparent);
}

.feature-grid {
    display: flex;
    overflow-x: auto;
    gap: 2rem;
    scroll-behavior: smooth;
    padding: 1rem;
    scroll-snap-type: x mandatory;
}
.feature-grid::-webkit-scrollbar {
    display: none;
}

.slide-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(10, 12, 30, 0.8);
    border: 1px solid rgba(255, 111, 0, 0.4);
    color: #ffca28;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    z-index: 10;
    transition: 0.3s;
    backdrop-filter: blur(5px);
}
.slide-control:hover {
    background: rgba(255, 111, 0, 0.2);
    color: #fff;
    box-shadow: 0 0 15px rgba(255, 111, 0, 0.4);
}
.slide-control.prev { left: 20px; }
.slide-control.next { right: 20px; }

.feature-card {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 2.5rem 2rem;
    flex: 0 0 320px;
    scroll-snap-align: center;
    position: relative;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, #ffca28, #ff6f00);
    opacity: 0;
    transition: 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: rgba(255, 111, 0, 0.3);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 20px rgba(255, 111, 0, 0.1);
    background: rgba(255, 255, 255, 0.04);
}

.feature-card:hover::before {
    opacity: 1;
}

.feature-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(255, 111, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffca28;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 20px rgba(255,111,0,0.15);
    border: 1px solid rgba(255, 111, 0, 0.2);
    transition: all 0.4s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
    background: rgba(255, 111, 0, 0.2);
    box-shadow: 0 12px 25px rgba(255,111,0,0.25);
    color: #fff;
}

/* 5️⃣ Analytics Section */
.analytics-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 1.5rem;
}
.stat-box {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
}

/* 6️⃣ Itinerary Timeline */
.timeline {
    position: relative;
    padding-left: 2rem;
    border-left: 2px solid rgba(255, 111, 0, 0.2);
    text-align: left;
}
.timeline-item {
    position: relative;
    margin-bottom: 2.5rem;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -2.6rem;
    top: 0.25rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #060713;
    border: 3px solid #ff6f00;
}

/* Gating Locker UI */
.gated-blur {
    filter: blur(5px);
    pointer-events: none;
    user-select: none;
    opacity: 0.3;
}
.gated-container {
    position: relative;
}
.gated-overlay {
    position: absolute;
    inset: 0;
    background: rgba(6, 7, 19, 0.7);
    backdrop-filter: blur(8px);
    z-index: 50;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 24px;
    padding: 2rem;
    border: 1px solid rgba(255, 111, 0, 0.2);
    text-align: center;
}

/* Leaflet Dark Theme Overrides */
.leaflet-container {
    background: #0d0f22 !important;
}
.leaflet-popup-content-wrapper {
    background: #111329 !important;
    color: #fff !important;
    border: 1px solid rgba(255,111,0,0.3) !important;
    border-radius: 8px !important;
}
.leaflet-popup-tip {
    background: #111329 !important;
}

/* Exclusive Travel Deals / Coupons */
.coupon-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}
.coupon-card {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 2.2rem 1.8rem;
    text-align: left;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Variant 1: Cyan / Neon Blue (Flights) */
.coupon-card.card-cyan:hover {
    border-color: rgba(0, 242, 254, 0.4);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 25px rgba(0, 242, 254, 0.2);
    background: rgba(255, 255, 255, 0.04);
}
.coupon-card.card-cyan .coupon-badge {
    background: linear-gradient(135deg, #00f2fe, #4facfe);
    color: #000;
}
.coupon-card.card-cyan .coupon-icon {
    background: rgba(0, 242, 254, 0.1);
    color: #00f2fe;
    border-color: rgba(0, 242, 254, 0.25);
}
.coupon-card.card-cyan .coupon-code-box {
    border-color: rgba(0, 242, 254, 0.4);
    color: #00f2fe;
}
.coupon-card.card-cyan .coupon-code-box:hover {
    background: rgba(0, 242, 254, 0.15);
}

/* Variant 2: Emerald / Mint (Trains) */
.coupon-card.card-emerald:hover {
    border-color: rgba(0, 255, 135, 0.4);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 25px rgba(0, 255, 135, 0.2);
    background: rgba(255, 255, 255, 0.04);
}
.coupon-card.card-emerald .coupon-badge {
    background: linear-gradient(135deg, #00ff87, #60efff);
    color: #000;
}
.coupon-card.card-emerald .coupon-icon {
    background: rgba(0, 255, 135, 0.1);
    color: #00ff87;
    border-color: rgba(0, 255, 135, 0.25);
}
.coupon-card.card-emerald .coupon-code-box {
    border-color: rgba(0, 255, 135, 0.4);
    color: #00ff87;
}
.coupon-card.card-emerald .coupon-code-box:hover {
    background: rgba(0, 255, 135, 0.15);
}

/* Variant 3: Crimson / Pink (Hotels) */
.coupon-card.card-pink:hover {
    border-color: rgba(255, 8, 68, 0.4);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 25px rgba(255, 8, 68, 0.2);
    background: rgba(255, 255, 255, 0.04);
}
.coupon-card.card-pink .coupon-badge {
    background: linear-gradient(135deg, #ff0844, #ffb199);
    color: #fff;
}
.coupon-card.card-pink .coupon-icon {
    background: rgba(255, 8, 68, 0.1);
    color: #ffb199;
    border-color: rgba(255, 8, 68, 0.25);
}
.coupon-card.card-pink .coupon-code-box {
    border-color: rgba(255, 177, 153, 0.4);
    color: #ffb199;
}
.coupon-card.card-pink .coupon-code-box:hover {
    background: rgba(255, 8, 68, 0.15);
}

/* Variant 4: Amber / Gold (Holiday Packages) */
.coupon-card.card-gold:hover {
    border-color: rgba(255, 175, 0, 0.4);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 25px rgba(255, 175, 0, 0.2);
    background: rgba(255, 255, 255, 0.04);
}
.coupon-card.card-gold .coupon-badge {
    background: linear-gradient(135deg, #ffca28, #ff6f00);
    color: #000;
}
.coupon-card.card-gold .coupon-icon {
    background: rgba(255, 111, 0, 0.1);
    color: #ffca28;
    border-color: rgba(255, 111, 0, 0.25);
}
.coupon-card.card-gold .coupon-code-box {
    border-color: rgba(255, 202, 40, 0.4);
    color: #ffca28;
}
.coupon-card.card-gold .coupon-code-box:hover {
    background: rgba(255, 111, 0, 0.15);
}

/* Base elements inside coupon card */
.coupon-badge {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    font-weight: 900;
    font-size: 0.75rem;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.coupon-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 1.5rem;
    border: 1px solid transparent;
}
.coupon-code-box {
    background: rgba(0, 0, 0, 0.4);
    border: 1px dashed transparent;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.8rem;
    font-family: monospace;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: 0.2s;
}

/* Responsive constraints */
@media(max-width: 1200px) {
    .coupon-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 992px) {
    .search-grid { grid-template-columns: 1fr; }
    .feature-grid { grid-template-columns: 1fr 1fr; }
    .analytics-grid { grid-template-columns: 1fr; }
    .dest-grid { grid-template-columns: 1fr 1fr; }
}
@media(max-width: 600px) {
    .feature-grid { grid-template-columns: 1fr; }
    .dest-grid { grid-template-columns: 1fr; }
    .coupon-grid { grid-template-columns: 1fr; }
}
</style>

<div class="landing-container">
    <div class="bg-orb" style="position: absolute; top: -10%; left: 50%; transform: translateX(-50%); width: 800px; height: 800px; background: radial-gradient(circle, rgba(255, 111, 0, 0.05) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

    {{-- ✅ 1️⃣ Hero Section --}}
    <header class="hero-sec">
        <div class="hero-media-bg"></div>
        <div class="inner" style="position: relative; z-index: 1;">
            <div style="text-align: center; max-width: 850px; margin: 0 auto;">
                <span class="section-header" style="margin-bottom:0;"><span class="tag" style="background: rgba(108, 99, 255, 0.15); color: #8f8aff; border-color: rgba(108,99,255,0.3);"><i class="fas fa-sparkles"></i> Plan Smarter. Travel Better.</span></span>
                <h1 style="font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 900; line-height: 1.1; margin-top: 1rem; margin-bottom: 1.25rem; letter-spacing: -0.02em;">
                    ✈️ AI-Powered <br>
                    <span style="background: linear-gradient(135deg, #fff 20%, #ffca28 70%, #ff6f00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Smart Travel Planning</span>
                </h1>
                <p style="color: #b0c4de; font-size: 1.15rem; line-height: 1.6; margin-bottom: 2.5rem;">
                    Automatically calculate travel costs, generate personalized itineraries, compare transport options, and unlock premium travel experiences in seconds.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="#ai-planner" class="btn btn-primary" style="padding: 0.9rem 2.2rem; font-size: 0.95rem; font-weight: 700; border-radius: 50px;">
                        <i class="fas fa-rocket"></i> Start Planning
                    </a>
                    <a href="#deals" class="btn btn-outline" style="padding: 0.9rem 2.2rem; font-size: 0.95rem; font-weight: 700; border-radius: 50px; border: 1px solid rgba(255,255,255,0.2); color: #fff;">
                        <i class="fas fa-tags"></i> Exclusive Travel Deals
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ✅ 2️⃣ AI Travel Search Box & ✅ 3️⃣ AI Estimation Preview --}}
    <section id="ai-planner" style="padding: 2rem 0 5rem; position: relative; z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
        <div class="inner">
            <div class="glass-panel" style="padding: 2.5rem; margin-bottom: 4rem;">
                <div style="margin-bottom: 2rem; text-align: left;">
                    <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.25rem;"><i class="fas fa-magic" style="color: #ff9100;"></i> Interactive AI Travel Synthesizer</h3>
                    <p style="color: #b0c4de; font-size: 0.88rem;">Search any worldwide city name dynamically using the live Map Autocomplete API.</p>
                </div>

                {{-- Search Box Fields --}}
                <div class="search-grid">
                    <div class="search-field">
                        <label><i class="fas fa-plane-departure"></i> From</label>
                        <div style="position: relative;">
                            <input type="text" id="route_from" value="" placeholder="Type city..." autocomplete="off" style="width:100%;">
                            <div id="from_suggestions" class="autocomplete-suggestions" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="search-field">
                        <label><i class="fas fa-plane-arrival"></i> To</label>
                        <div style="position: relative;">
                            <input type="text" id="route_to" value="" placeholder="Type city..." autocomplete="off" style="width:100%;">
                            <div id="to_suggestions" class="autocomplete-suggestions" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="search-field">
                        <label><i class="fas fa-calendar-day"></i> Days</label>
                        <input type="number" id="route_days" placeholder="e.g. 5" min="1" max="60" style="width:100%;">
                    </div>
                    <div class="search-field">
                        <label><i class="fas fa-indian-rupee-sign"></i> Budget Type</label>
                        <select id="route_budget" style="width:100%;">
                            <option value="Budget">Budget</option>
                            <option value="Standard" selected>Standard</option>
                            <option value="Luxury">Luxury</option>
                        </select>
                    </div>
                    <div class="search-field">
                        <label><i class="fas fa-users"></i> Travelers</label>
                        <input type="number" id="route_travelers" placeholder="e.g. 2" min="1" max="50" style="width:100%;">
                    </div>
                </div>

                <div style="margin-top: 1.5rem; text-align: center;">
                    <button type="button" class="btn-ai-gen" onclick="simulateAIEngine()">
                        <i class="fas fa-robot"></i> Generate AI Plan
                    </button>
                </div>

                {{-- Dynamic AI Synthesis Loader --}}
                <div id="ai_loader" class="ai-synthesis-loader"></div>
            </div>

            {{-- ✅ 3️⃣ Free AI Estimation Preview --}}
            <div id="preview_container" class="preview-container">
                <div class="section-header">
                    <span class="tag"><i class="fas fa-wallet"></i> Instant Calculations</span>
                    <h2>Free AI Estimation Preview</h2>
                    <p style="color: #b0c4de; font-size: 0.9rem; margin-top: 0.5rem;">Optimized routes and lodging estimates automatically processed by TravelMate.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem;" class="preview-cards">
                    {{-- Card 1 --}}
                    <div class="est-card">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">✈️</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: #ff9100; font-weight: 700; letter-spacing: 0.05em;">Flight Cost</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 0.25rem;" id="est_flight"><i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked</div>
                        <div style="font-size: 0.65rem; color: #8f8aff;" id="est_flight_desc">Login to View</div>
                    </div>
                    {{-- Card 2 --}}
                    <div class="est-card">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🚆</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: #ff9100; font-weight: 700; letter-spacing: 0.05em;">Train Express</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 0.25rem;" id="est_train"><i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked</div>
                        <div style="font-size: 0.65rem; color: #8f8aff;" id="est_train_desc">Login to View</div>
                    </div>
                    {{-- Card 3 --}}
                    <div class="est-card">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🏨</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: #ff9100; font-weight: 700; letter-spacing: 0.05em;">Luxe Lodging</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 0.25rem;" id="est_hotel"><i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked</div>
                        <div style="font-size: 0.65rem; color: #8f8aff;" id="est_hotel_desc">Login to View</div>
                    </div>
                    {{-- Card 4 --}}
                    <div class="est-card">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🍔</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: #ff9100; font-weight: 700; letter-spacing: 0.05em;">Dining & Local</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 0.25rem;" id="est_dining"><i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked</div>
                        <div style="font-size: 0.65rem; color: #8f8aff;" id="est_dining_desc">Login to View</div>
                    </div>
                    {{-- Card 5 --}}
                    <div class="est-card" style="border-color: rgba(255, 111, 0, 0.4); background: rgba(255, 111, 0, 0.04);">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">💰</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: #ffca28; font-weight: 800; letter-spacing: 0.05em;">Total Cost</div>
                        <div style="font-size: 1.4rem; font-weight: 900; color: #ffca28; margin-top: 0.25rem;" id="est_total">₹15,000</div>
                        <div style="font-size: 0.65rem; color: #8f8aff;">Optimized Estimate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ✅ 4️⃣ Why Choose TravelMate --}}
    <section style="padding: 5rem 0; background: #04050f;">
        <div class="inner">
            <div class="section-header">
                <span class="tag"><i class="fas fa-crown"></i> Platform Benefits</span>
                <h2>Why Choose TravelMate</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 600px; margin: 0.5rem auto 0;">Next-generation intelligent features engineered to revolutionize traditional trip planning.</p>
            </div>

            <div class="feature-marquee-wrapper">
                <button class="slide-control prev" onclick="slideFeatures(-1)"><i class="fas fa-chevron-left"></i></button>
                <div class="feature-grid" id="featureGrid">
                    {{-- Feature 1 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-brain"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">AI Trip Planning</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Automated prompting models customize dynamic, sequential day plans customized around local opening indices.</p>
                    </div>
                    {{-- Feature 2 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">Budget Optimization</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Evaluates and compares transit parameters dynamically across railway systems, road indices, and flight providers.</p>
                    </div>
                    {{-- Feature 3 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-hotel"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">Smart Hotel Discovery</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Pairs you with optimized local hotel recommendations categorized by proximity indices and verified traveler reviews.</p>
                    </div>
                    {{-- Feature 4 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-cloud-sun-rain"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">Weather Insights</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Aggregates local meteorology APIs to display dynamic real-time safety indices and localized warning bulletins.</p>
                    </div>
                    {{-- Feature 5 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">Expense Analytics</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Enterprise visual graphics tracking spend patterns, categorizing travel ledger transactions with interactive graphs.</p>
                    </div>
                    {{-- Feature 6 --}}
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.75rem; color: #fff;">Secure Payments</h3>
                        <p style="color: #b0c4de; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">Encrypted Razorpay checkout API validating payment payload hashing completely client and server side.</p>
                    </div>
                </div>
                <button class="slide-control next" onclick="slideFeatures(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <script>
        function slideFeatures(direction) {
            const grid = document.getElementById('featureGrid');
            const scrollAmount = 340; // width of card + gap approx
            grid.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
        }
    </script>

    {{-- ✅ 5️⃣ Analytics Dashboard Preview --}}
    <section id="analytics-preview" style="padding: 5rem 0;" data-aos="fade-up" data-aos-duration="1000">
        <div class="inner">
            <div class="section-header">
                <span class="tag" style="background: rgba(108, 99, 255, 0.15); color: #8f8aff; border-color: rgba(108,99,255,0.3);"><i class="fas fa-chart-pie"></i> Business Intelligence</span>
                <h2>Analytics Dashboard Preview</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 600px; margin: 0.5rem auto 0;">Visualize spend timelines and categorical distributions dynamically mapped by our execution engine.</p>
            </div>

            <div class="glass-panel analytics-grid" style="padding: 2.5rem; align-items: center;">
                {{-- Left Column: Line Chart --}}
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; color: #fff; margin-left: 0;"><i class="fas fa-chart-line" style="color: #8f8aff; margin-right: 0.5rem;"></i> Expenditure Projection Trend</h4>
                    <div style="height: 250px; position: relative;">
                        <canvas id="landingTrendChart"></canvas>
                    </div>
                </div>

                {{-- Right Column: Stats & Donut Chart --}}
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: #fff; margin-left: 0;"><i class="fas fa-pie-chart" style="color: #ff9100; margin-right: 0.5rem;"></i> Budget Allocation</h4>
                        <div style="height: 150px; position: relative;">
                            <canvas id="landingCatChart"></canvas>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="stat-box">
                            <div style="font-size: 0.7rem; color: #b0c4de; text-transform: uppercase;">Simulated Spending</div>
                            <div style="font-size: 1.25rem; font-weight: 800; color: #ffca28; margin-top: 0.25rem;">₹48,500</div>
                        </div>
                        <div class="stat-box">
                            <div style="font-size: 0.7rem; color: #b0c4de; text-transform: uppercase;">Saved Balance</div>
                            <div style="font-size: 1.25rem; font-weight: 800; color: #00ff66; margin-top: 0.25rem;">₹11,200</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ✅ 6️⃣ Gated AI Itinerary Showcase, Live Leaflet Maps, & ✅ 7️⃣ Premium Locker --}}
    <section id="itinerary_showcase_section" class="preview-container" style="padding: 5rem 0; background: #04050f;">
        <div class="inner">
            <div class="section-header">
                <span class="tag"><i class="fas fa-route"></i> Timeline Blueprint</span>
                <h2>AI Itinerary Showcase</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 600px; margin: 0.5rem auto 0;">A live generated preview of local timelines and scheduling synthesized by Google Gemini.</p>
            </div>

            <div class="gated-container">
                {{-- LOCK OVERLAY FOR GUEST USER --}}
                <div class="gated-overlay">
                    <div style="font-size: 3.5rem; margin-bottom: 1rem; animation: pulse-ring 2s infinite;"><i class="fas fa-lock" style="color: #ff9100;"></i></div>
                    <h3 style="font-size: 1.6rem; font-weight: 800; color: #fff; margin-bottom: 0.75rem;">🔒 Login to Unlock Full Features</h3>
                    <p style="color: #b0c4de; max-width: 500px; margin: 0 auto 1.5rem; font-size: 0.95rem; line-height: 1.6;">
                        Guest previews are limited. Register or Log In to access detailed day-by-day itineraries, exact hotel collections, live meteorological radars, and direct-booking platforms!
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 50px; font-weight: 800;">
                            <i class="fas fa-sign-in-alt"></i> Log In Now
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline" style="padding: 0.8rem 2rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.25); color: #fff; font-weight: 700;">
                            Create Free Account
                        </a>
                    </div>
                </div>

                {{-- GATED BLURRED WRAPPER --}}
                <div class="gated-blur" style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
                    {{-- Left: Timeline --}}
                    <div class="glass-panel" style="padding: 2.5rem;" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem; margin-bottom: 2rem;">
                            <div style="font-size: 1.15rem; font-weight: 800;"><i class="fas fa-map-location-dot" style="color: #ff9100; margin-right: 0.5rem;"></i> Generated Route Map</div>
                            <span id="itinerary_dest_badge" class="tag tag-indigo" style="background: rgba(255, 111, 0, 0.15); color: #ffca28; border-color: rgba(255,111,0,0.3);">Mumbai to Goa</span>
                        </div>

                        <div class="timeline">
                            {{-- Day 1 --}}
                            <div class="timeline-item">
                                <div style="font-size: 0.8rem; font-weight: 800; color: #ff6f00; text-transform: uppercase;">Day 1: Coastline Arrival & Fort Exploring</div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-top: 0.25rem; margin-bottom: 0.75rem;" id="day1_heading">Check-in at Beach Resort & Aguada Fort Exploration</h4>
                                <p style="color: #b0c4de; font-size: 0.88rem; line-height: 1.6;" id="day1_desc">
                                    Touchdown in North Goa. Enjoy check-in at a scenic oceanfront cottage. Drive to the 17th-century historic Fort Aguada to walk around the giant brick lighthouse and snap spectacular clifftop photos overlooking the vast Arabian Sea. End with coastal seafood curries at beach shacks.
                                </p>
                            </div>
                            {{-- Day 2 --}}
                            <div class="timeline-item">
                                <div style="font-size: 0.8rem; font-weight: 800; color: #ff6f00; text-transform: uppercase;">Day 2: Heritage Strolls & Spices</div>
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-top: 0.25rem; margin-bottom: 0.75rem;" id="day2_heading">Old Goa Portuguese Churches & Tropical Spice Farm</h4>
                                <p style="color: #b0c4de; font-size: 0.88rem; line-height: 1.6;" id="day2_desc">
                                    Stroll along historic Portuguese cobblestones at the Old Goa complex, photographing the majestic Basilica of Bom Jesus. In the afternoon, explore a fragrant spice garden plantation, enjoy a traditional wood-fired buffet lunch, and shop the lively local markets.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Leaflet Maps Radar + Premium Locker --}}
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        {{-- Live Telematics Map Card --}}
                        <div class="glass-panel" style="padding: 1.5rem; text-align: left;">
                            <div style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-map-location-dot" style="color: #ff9100;"></i> Live Telematics Map Radar
                            </div>
                            <div id="itineraryMap" style="height: 250px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; z-index: 1;"></div>
                        </div>

                        {{-- Premium Unlock Section --}}
                        <div class="glass-panel locker-card" style="padding: 2.5rem; display: flex; flex-direction: column; justify-content: center;">
                            <div style="text-align: center; margin-bottom: 2rem;">
                                <div style="font-size: 3rem; margin-bottom: 1rem; display: inline-flex; width: 80px; height: 80px; border-radius: 50%; align-items: center; justify-content: center; background: rgba(255,111,0,0.1);"><i class="fas fa-lock" style="color: #ffca28;"></i></div>
                                <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Unlock Complete Plan</h3>
                                <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.5;">Access absolute itinerary details, direct-bookings, and live travel companion tools instantly.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ✅ 8️⃣ Exclusive Travel Deals & Coupons --}}
    <section id="deals" style="padding: 5rem 0;" data-aos="fade-up" data-aos-duration="1000">
        <div class="inner">
            <div class="section-header">
                <span class="tag"><i class="fas fa-tags"></i> Exclusive Discounts</span>
                <h2>Grab Your Travel Coupons & Offers</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 600px; margin: 0.5rem auto 0;">Collect premium discount vouchers instantly for flight price-drops, train fare waivers, and hotel stays.</p>
            </div>

            <div class="coupon-grid">
                {{-- Deal 1: Flights --}}
                <div class="coupon-card card-cyan">
                    <span class="coupon-badge">25% OFF</span>
                    <div>
                        <div class="coupon-icon"><i class="fas fa-plane-departure"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Flight Tickets Discount</h3>
                        <p style="font-size: 0.88rem; color: #b0c4de; line-height: 1.6;">Instant price drops up to ₹2,500 on all domestic and international flights. Valid across major airline partners.</p>
                    </div>
                    <div class="coupon-code-box" onclick="collectCoupon('FLIGHTUP25', this)">
                        <span>FLIGHTUP25</span>
                        <i class="fas fa-copy" style="font-size: 0.9rem; color: inherit;"></i>
                    </div>
                </div>

                {{-- Deal 2: Trains --}}
                <div class="coupon-card card-emerald">
                    <span class="coupon-badge">₹500 OFF</span>
                    <div>
                        <div class="coupon-icon"><i class="fas fa-train"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Express Railway Waiver</h3>
                        <p style="font-size: 0.88rem; color: #b0c4de; line-height: 1.6;">Zero payment gateway fees plus direct ₹500 discount on AC 1-Tier and 2-Tier train reservations.</p>
                    </div>
                    <div class="coupon-code-box" onclick="collectCoupon('RAILPASS15', this)">
                        <span>RAILPASS15</span>
                        <i class="fas fa-copy" style="font-size: 0.9rem; color: inherit;"></i>
                    </div>
                </div>

                {{-- Deal 3: Hotels --}}
                <div class="coupon-card card-pink">
                    <span class="coupon-badge">30% OFF</span>
                    <div>
                        <div class="coupon-icon"><i class="fas fa-hotel"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Luxury Hotel Booking</h3>
                        <p style="font-size: 0.88rem; color: #b0c4de; line-height: 1.6;">Complimentary gourmet breakfast plus 30% off on premium ocean-view suites and heritage resorts.</p>
                    </div>
                    <div class="coupon-code-box" onclick="collectCoupon('STAYLUX30', this)">
                        <span>STAYLUX30</span>
                        <i class="fas fa-copy" style="font-size: 0.9rem; color: inherit;"></i>
                    </div>
                </div>

                {{-- Deal 4: Holiday Packages --}}
                <div class="coupon-card card-gold">
                    <span class="coupon-badge">₹5,000 OFF</span>
                    <div>
                        <div class="coupon-icon"><i class="fas fa-umbrella-beach"></i></div>
                        <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">AI Holiday Packages</h3>
                        <p style="font-size: 0.88rem; color: #b0c4de; line-height: 1.6;">Massive instant cashbacks on fully customized group itineraries and luxury vacation packages.</p>
                    </div>
                    <div class="coupon-code-box" onclick="collectCoupon('FESTIVE50', this)">
                        <span>FESTIVE50</span>
                        <i class="fas fa-copy" style="font-size: 0.9rem; color: inherit;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ✅ 9️⃣ Testimonials --}}
    <section style="padding: 5rem 0; background: #04050f;" data-aos="zoom-in" data-aos-duration="1000">
        <div class="inner">
            <div class="section-header">
                <span class="tag" style="background: rgba(108, 99, 255, 0.15); color: #8f8aff; border-color: rgba(108,99,255,0.3);"><i class="fas fa-quote-left"></i> Verified Reviews</span>
                <h2>What Travelers Say</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 600px; margin: 0.5rem auto 0;">Real feedback submitted by travelers in our database.</p>
            </div>

            <div class="feature-grid">
                {{-- Feed 1 --}}
                <div class="glass-panel" style="padding: 1.75rem; text-align: left;">
                    <div style="display: flex; gap: 0.5rem; color: #ffca28; margin-bottom: 1rem;">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p style="color: #b0c4de; font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem; font-style: italic;">
                        "AI planning saved my budget. It gave me flights, railways, and local hotel parameters instantly. Absolute lifesaver!"
                    </p>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #ff6f00; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">RK</div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 700;">Rohan K.</div>
                            <div style="font-size: 0.68rem; color: #00ff66;"><i class="fas fa-shield-alt"></i> Verified Traveler</div>
                        </div>
                    </div>
                </div>
                {{-- Feed 2 --}}
                <div class="glass-panel" style="padding: 1.75rem; text-align: left;">
                    <div style="display: flex; gap: 0.5rem; color: #ffca28; margin-bottom: 1rem;">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p style="color: #b0c4de; font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem; font-style: italic;">
                        "Best smart travel experience. Being able to compare different transport cost projections was perfect for our trip."
                    </p>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #6c63ff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">AP</div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 700;">Aanya P.</div>
                            <div style="font-size: 0.68rem; color: #00ff66;"><i class="fas fa-shield-alt"></i> Verified Traveler</div>
                        </div>
                    </div>
                </div>
                {{-- Feed 3 --}}
                <div class="glass-panel" style="padding: 1.75rem; text-align: left;">
                    <div style="display: flex; gap: 0.5rem; color: #ffca28; margin-bottom: 1rem;">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p style="color: #b0c4de; font-size: 0.88rem; line-height: 1.6; margin-bottom: 1.25rem; font-style: italic;">
                        "Easy and modern platform. The AI integrated routes and hotel suggestions in old architecture are extremely clean."
                    </p>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #00d4aa; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">DS</div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 700;">David S.</div>
                            <div style="font-size: 0.68rem; color: #00ff66;"><i class="fas fa-shield-alt"></i> Verified Traveler</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @auth
    {{-- ✅ 1️⃣0️⃣ Integrated Transit & Booking Ecosystem Hub (Available After Login) --}}
    <section id="transit-booking-hub" style="padding: 6rem 0; background: #060714; position: relative; z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
        <div class="inner">
            <div class="section-header">
                <span class="tag" style="background: rgba(0, 242, 254, 0.15); color: #00f2fe; border-color: rgba(0, 242, 254, 0.3);"><i class="fas fa-network-wired"></i> Verified Partner Integrations</span>
                <h2>Direct Booking & Transit Hub</h2>
                <p style="color: #b0c4de; font-size: 0.95rem; max-width: 680px; margin: 0.5rem auto 0;">You are logged into TravelMate. Seamlessly initiate real-time reservations, issue e-tickets, and book local logistics across our verified external transit networks.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                {{-- 1. Flight Booking --}}
                <div class="glass-panel glass-panel-hover" style="padding: 2.2rem 1.8rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #00f2fe;">
                    <div>
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(0, 242, 254, 0.1); color: #00f2fe; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; border: 1px solid rgba(0, 242, 254, 0.25);">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Flight Booking</h3>
                        <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.75rem;">Compare instant real-time fare drops across domestic & international flight providers.</p>
                    </div>
                    <a href="https://www.makemytrip.com/flights/" target="_blank" class="btn" style="background: rgba(0, 242, 254, 0.15); color: #00f2fe; border: 1px solid rgba(0, 242, 254, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                        Launch Portal <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                    </a>
                </div>

                {{-- 2. Train Reservations --}}
                <div class="glass-panel glass-panel-hover" style="padding: 2.2rem 1.8rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #00ff87;">
                    <div>
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(0, 255, 135, 0.1); color: #00ff87; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; border: 1px solid rgba(0, 255, 135, 0.25);">
                            <i class="fas fa-train-subway"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">IRCTC Railway</h3>
                        <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.75rem;">Instant Tatkal availability & live PNR tracking across Indian Railways express routes.</p>
                    </div>
                    <a href="https://www.irctc.co.in" target="_blank" class="btn" style="background: rgba(0, 255, 135, 0.15); color: #00ff87; border: 1px solid rgba(0, 255, 135, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                        Launch IRCTC <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                    </a>
                </div>

                {{-- 3. Bus Express --}}
                <div class="glass-panel glass-panel-hover" style="padding: 2.2rem 1.8rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ff9100;">
                    <div>
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255, 145, 0, 0.1); color: #ff9100; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; border: 1px solid rgba(255, 145, 0, 0.25);">
                            <i class="fas fa-bus-simple"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Bus Services</h3>
                        <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.75rem;">Book AC Volvo sleeper express transit across national inter-city highway networks.</p>
                    </div>
                    <a href="https://www.redbus.in" target="_blank" class="btn" style="background: rgba(255, 145, 0, 0.15); color: #ff9100; border: 1px solid rgba(255, 145, 0, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                        Launch redBus <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                    </a>
                </div>

                {{-- 4. Luxury Hotels --}}
                <div class="glass-panel glass-panel-hover" style="padding: 2.2rem 1.8rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ff0844;">
                    <div>
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255, 8, 68, 0.1); color: #ff0844; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; border: 1px solid rgba(255, 8, 68, 0.25);">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Luxe Hotels</h3>
                        <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.75rem;">Exclusive member discounts on verified 5-star oceanfront suites and city retreats.</p>
                    </div>
                    <a href="https://www.booking.com" target="_blank" class="btn" style="background: rgba(255, 8, 68, 0.15); color: #ff0844; border: 1px solid rgba(255, 8, 68, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                        Launch Agoda <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                    </a>
                </div>

                {{-- 5. Rapido / Uber --}}
                <div class="glass-panel glass-panel-hover" style="padding: 2.2rem 1.8rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ffca28;">
                    <div>
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255, 202, 40, 0.1); color: #ffca28; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; border: 1px solid rgba(255, 202, 40, 0.25);">
                            <i class="fas fa-taxi"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Rapido / Uber</h3>
                        <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.75rem;">Instant local cab & bike-taxi hailing for airport transfers and local sightseeing.</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="https://www.rapido.bike" target="_blank" class="btn" style="background: rgba(255, 202, 40, 0.15); color: #ffca28; border: 1px solid rgba(255, 202, 40, 0.4); font-weight: 700; font-size: 0.8rem; padding: 0.75rem 0.5rem; border-radius: 12px; text-align: center; text-decoration: none; flex: 1; transition: 0.3s;">
                            Rapido <i class="fas fa-arrow-up-right-from-square"></i>
                        </a>
                        <a href="https://www.uber.com" target="_blank" class="btn" style="background: rgba(255, 255, 255, 0.1); color: #fff; border: 1px solid rgba(255, 255, 255, 0.25); font-weight: 700; font-size: 0.8rem; padding: 0.75rem 0.5rem; border-radius: 12px; text-align: center; text-decoration: none; flex: 1; transition: 0.3s;">
                            Uber <i class="fas fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endauth

    {{-- ✅ 1️⃣0️⃣ Footer --}}
    <footer style="background: #03040a; border-top: 1px solid rgba(255,255,255,0.05); padding: 5rem 0 2rem; position: relative; z-index: 1;">
        <div class="inner">
            <div style="display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 3rem; margin-bottom: 4rem; text-align: left;">
                <div>
                    <h4 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin-bottom: 1rem;"><i class="fas fa-paper-plane" style="color: #ff6f00;"></i> TravelMate</h4>
                    <p style="color: #b0c4de; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem; max-width: 300px;">
                        Plan Smarter. Travel Better. The ultimate AI-powered ecosystem to build, predict, track, and secure global itineraries.
                    </p>
                    <div style="display: flex; gap: 1rem; color: #b0c4de; font-size: 1.1rem;">
                        <a href="#" style="transition:0.2s;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="transition:0.2s;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="transition:0.2s;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="transition:0.2s;"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div>
                    <h5 style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #ff6f00; letter-spacing: 0.05em; margin-bottom: 1.25rem;">Ecosystem</h5>
                    <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.82rem; color: #b0c4de;">
                        <li><a href="#ai-planner">AI Synthesizer</a></li>
                        <li><a href="{{ route('destinations.index') }}">Global Destinations</a></li>
                        <li><a href="{{ route('packages.index') }}">Travel Packages</a></li>
                    </ul>
                </div>
                <div>
                    <h5 style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #ff6f00; letter-spacing: 0.05em; margin-bottom: 1.25rem;">Project Info</h5>
                    <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.82rem; color: #b0c4de;">
                        <li><a href="{{ route('about') }}">About System</a></li>
                        <li><a href="{{ route('contact') }}">Contact Desk</a></li>
                    </ul>
                </div>
                <div>
                    <h5 style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #ff6f00; letter-spacing: 0.05em; margin-bottom: 1.25rem;">Legal</h5>
                    <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.82rem; color: #b0c4de;">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
            </div>

            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 2rem; text-align: center; color: #b0c4de; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>&copy; 2026 TravelMate Platform. Built for Final Year Project.</div>
                <div>Plan Smarter. Travel Better. &bull; Your AI-Powered Travel Companion.</div>
            </div>
        </div>
    </footer>
</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Live Map & Markers State
let itineraryMap = null;
let activeMarker = null;

// Initialize Leaflet Map
document.addEventListener('DOMContentLoaded', function() {
    // Center at Mumbai initially
    itineraryMap = L.map('itineraryMap', { zoomControl: true }).setView([19.0760, 72.8777], 5);
    
    // Load Dark Theme Tiles (fits your premium aesthetic perfectly)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(itineraryMap);

    // Initial marker at Mumbai
    activeMarker = L.marker([19.0760, 72.8777]).addTo(itineraryMap)
        .bindPopup('<b>Starting Location</b><br>Mumbai, India')
        .openPopup();
});

// Autocomplete Logic
function setupAutocomplete(inputID, suggestionsID) {
    const input = document.getElementById(inputID);
    const suggestions = document.getElementById(suggestionsID);
    let debounceTimer = null;

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = input.value.trim();
        
        if (query.length < 3) {
            suggestions.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (data.length === 0) {
                        suggestions.style.display = 'none';
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-suggestion';
                        div.textContent = item.display_name;
                        div.onclick = function() {
                            // Extract city/short name
                            const parts = item.display_name.split(',');
                            const shortName = parts[0].trim() + (parts.length > 1 ? ', ' + parts[parts.length - 1].trim() : '');
                            input.value = shortName;
                            suggestions.style.display = 'none';
                        };
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                })
                .catch(() => {});
        }, 300);
    });
}

// Wire up autocompletes
setupAutocomplete('route_from', 'from_suggestions');
setupAutocomplete('route_to', 'to_suggestions');

// Close suggestions on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#route_from') && !e.target.closest('#from_suggestions')) {
        document.getElementById('from_suggestions').style.display = 'none';
    }
    if (!e.target.closest('#route_to') && !e.target.closest('#to_suggestions')) {
        document.getElementById('to_suggestions').style.display = 'none';
    }
});

// Live Interactive AI Simulator
function simulateAIEngine() {
    const from = document.getElementById('route_from').value;
    const to = document.getElementById('route_to').value;
    const days = document.getElementById('route_days').value || '3';
    const budgetType = document.getElementById('route_budget').value || 'Standard';
    const travelers = document.getElementById('route_travelers').value || '2';

    const loader = document.getElementById('ai_loader');
    loader.style.display = 'block';
    loader.innerHTML = '';

    // Scroll to loader smoothly
    loader.scrollIntoView({ behavior: 'smooth', block: 'center' });

    const steps = [
        { icon: 'fa-globe-asia', title: 'Analyzing Geography', desc: `Mapping route from ${from || 'Origin'} to ${to || 'Destination'}...` },
        { icon: 'fa-plane-departure', title: 'Calculating Transit Metrics', desc: 'Querying live flight and railway pricing databases...' },
        { icon: 'fa-hotel', title: 'Curating Accommodations', desc: `Filtering optimized lodgings within ₹${budget} budget...` },
        { icon: 'fa-microchip', title: 'AI Orchestration', desc: 'Synthesizing day-by-day intelligent itinerary...' }
    ];

    let currentStep = 0;
    
    // Inject all steps initially
    steps.forEach((step, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'synthesis-step';
        stepDiv.id = `synth_step_${index}`;
        stepDiv.innerHTML = `
            <div class="step-icon-box"><i class="fas ${step.icon}"></i></div>
            <div class="step-text">
                <div class="step-title">${step.title}</div>
                <div class="step-desc">${step.desc}</div>
            </div>
            <div class="step-status" id="synth_status_${index}"><i class="fas fa-circle-notch"></i></div>
        `;
        loader.appendChild(stepDiv);
    });

    function processSteps() {
        if (currentStep < steps.length) {
            // Mark current as active
            const el = document.getElementById(`synth_step_${currentStep}`);
            el.classList.add('active');
            
            setTimeout(() => {
                // Mark current as completed
                el.classList.remove('active');
                el.classList.add('completed');
                document.getElementById(`synth_status_${currentStep}`).innerHTML = '<i class="fas fa-check-circle"></i>';
                
                currentStep++;
                setTimeout(processSteps, 300);
            }, 1000 + Math.random() * 500); // simulate processing time
        } else {
            // Geocode and move Leaflet Map dynamically!
            setTimeout(() => {
                geocodeAndFly(to || 'Goa, India');
                populateCalculations(from || 'Origin', to || 'Destination', days, budget, travelers);
            }, 500);
        }
    }
    
    // Start processing
    setTimeout(processSteps, 300);
}

function geocodeAndFly(locationName) {
    if (!itineraryMap) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationName)}&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);

                // Smooth fly animation across the globe
                itineraryMap.flyTo([lat, lon], 12, { animate: true, duration: 2.0 });

                // Update marker
                if (activeMarker) {
                    itineraryMap.removeLayer(activeMarker);
                }

                activeMarker = L.marker([lat, lon]).addTo(itineraryMap)
                    .bindPopup(`<b>${locationName}</b><br>AI Plan Synthesized Successfully!`)
                    .openPopup();
            }
        })
        .catch(() => {});
}

function populateCalculations(from, to, days, budget, travelers) {
    const previewContainer = document.getElementById('preview_container');
    const itinerarySection = document.getElementById('itinerary_showcase_section');

    // Dynamic cost calculator based on input
    const daysFactor = parseInt(days);
    const travelersFactor = parseInt(travelers);
    
    const flightCost = 2500 * travelersFactor;
    const trainCost = 600 * travelersFactor;
    const hotelCost = 2000 * (daysFactor - 1) * Math.ceil(travelersFactor/2);
    const diningCost = 700 * daysFactor * travelersFactor;
    const totalCost = flightCost + hotelCost + diningCost;

    document.getElementById('est_flight').innerHTML = '<i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked';
    document.getElementById('est_train').innerHTML = '<i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked';
    document.getElementById('est_hotel').innerHTML = '<i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked';
    document.getElementById('est_dining').innerHTML = '<i class="fas fa-lock" style="font-size: 0.9rem; color: #ff6f00;"></i> Locked';
    
    document.getElementById('est_flight_desc').textContent = 'Login to View';
    document.getElementById('est_train_desc').textContent = 'Login to View';
    document.getElementById('est_hotel_desc').textContent = 'Login to View';
    document.getElementById('est_dining_desc').textContent = 'Login to View';

    document.getElementById('est_total').textContent = '₹' + totalCost.toLocaleString();

    // Itinerary Text Modifications
    document.getElementById('itinerary_dest_badge').textContent = `${from} to ${to}`;
    document.getElementById('day1_heading').textContent = `Arrival in ${to} & Initial Explorations`;
    document.getElementById('day1_desc').textContent = `Departing from ${from}. Touchdown in beautiful ${to}. Check-in at your curated, budget-optimized hotel accommodations (Calculated index: ₹${hotelCost.toLocaleString()}). Explore highly rated sights nearby and enjoy local dinner dining.`;
    
    document.getElementById('day2_heading').textContent = `Historical Landmarks & Sightseeing Highlights`;
    document.getElementById('day2_desc').textContent = `Explore scenic local attractions. Sample traditional street food (Per diem budget: ₹${(diningCost/daysFactor).toLocaleString()}). Complete your tour with souvenir shopping at famous local markets and live-music cafés.`;

    // Make sections visible
    previewContainer.classList.add('active');
    itinerarySection.classList.add('active');

    // Scroll to results
    previewContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Invalidate map size so it fits perfectly (standard leaflet fix for hidden components)
    setTimeout(() => {
        if (itineraryMap) itineraryMap.invalidateSize();
    }, 200);
}

// ChartJS Rendering
document.addEventListener('DOMContentLoaded', function() {
    // Trend line Chart
    const trendCtx = document.getElementById('landingTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Projected Budget Limit (₹)',
                data: [15000, 15000, 15000, 20000, 20000, 25000],
                borderColor: '#6c63ff',
                backgroundColor: 'rgba(108, 99, 255, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }, {
                label: 'Actual Spent Log (₹)',
                data: [11200, 14200, 13100, 18500, 15600, 21800],
                borderColor: '#ff6f00',
                backgroundColor: 'transparent',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#ff6f00'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#b0c4de', font: { family: 'Outfit' } } }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#b0c4de' } },
                y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#b0c4de' } }
            }
        }
    });

    // Category Doughnut Chart
    const catCtx = document.getElementById('landingCatChart').getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: ['Transport', 'Hotel', 'Dining', 'Sights'],
            datasets: [{
                data: [35, 40, 15, 10],
                backgroundColor: [
                    '#ff6f00',
                    '#ffca28',
                    '#00ff66',
                    '#6c63ff'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: '#b0c4de', font: { family: 'Outfit' } }
                }
            }
        }
    });
});

// Collect Coupon Function
function collectCoupon(code, btn) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).catch(() => {});
    }
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span style="color: #00ff66;"><i class="fas fa-check-circle"></i> Collected</span>';
    btn.style.borderColor = '#00ff66';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.borderColor = '';
    }, 2500);
}

// Initialize AOS (Animate On Scroll)
AOS.init({
    once: true, // whether animation should happen only once - while scrolling down
    offset: 100, // offset (in px) from the original trigger point
});
</script>

@endsection
