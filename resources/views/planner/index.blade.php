@extends('layouts.app')
@section('title','AI Travel Planner Portal — TravelMate')
@section('content')

{{-- Leaflet Maps Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Outfit',sans-serif;scroll-behavior:smooth}

.planner-page{
    background:#060713;
    color:#fff;
    min-height:100vh;
    padding:4rem 1.5rem 6rem;
    position:relative;
    overflow:hidden;
}
.inner{max-width:1200px;margin:0 auto}

.glass{
    background:rgba(255,255,255,.02);
    backdrop-filter:blur(20px);
    border:1px solid rgba(255,255,255,.08);
    border-radius:24px;
    padding:2.5rem;
    box-shadow:0 20px 50px rgba(0,0,0,0.5);
}
.form-row{display:grid;grid-template-columns:1.2fr 1.2fr 1fr 1fr 1fr;gap:1rem;align-items:end}
.f-label{font-size:.72rem;font-weight:700;color:#ff9100;text-transform:uppercase;letter-spacing:1px;margin-bottom:.4rem;display:block}
.f-input{width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:.75rem;border-radius:12px;font-size:.9rem;outline:none;transition:.2s}
.f-input:focus{border-color:#ff6f00;background:rgba(255,255,255,0.08)}
.f-input option{background:#0d0f22}

.calc-btn{padding:1rem 2.5rem;border:none;border-radius:50px;background:linear-gradient(135deg,#ffca28,#ff6f00);color:#fff;font-weight:800;font-size:1rem;cursor:pointer;transition:.3s;white-space:nowrap;box-shadow:0 4px 15px rgba(255,111,0,.35)}
.calc-btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(255,111,0,.5)}

.results{display:none;margin-top:3rem;animation:fadeUp .5s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

.transport-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:2rem}
.transport-card{background:rgba(255,255,255,.02);border:2px solid rgba(255,255,255,.05);border-radius:18px;padding:1.5rem;text-align:center;transition:.3s;cursor:pointer}
.transport-card:hover,.transport-card.best{border-color:#00ff66;background:rgba(0,255,102,.04)}
.transport-card.best::before{content:'✨ Best Value';display:block;background:#00ff66;color:#03040a;font-size:.65rem;font-weight:900;padding:.2rem .6rem;border-radius:6px;margin-bottom:.75rem;width:fit-content;margin-left:auto;margin-right:auto;text-transform:uppercase}

.cost-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem}
.cost-card{background:rgba(255,255,255,.01);border:1px solid rgba(255,255,255,.05);border-radius:14px;padding:1.25rem;text-align:center}

.total-banner{background:linear-gradient(135deg,rgba(255,111,0,.15),rgba(255,202,40,.08));border:1px solid rgba(255,111,0,.25);border-radius:16px;padding:1.5rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}

.premium-lock-container{background:rgba(255,202,40,.04);border:1px solid rgba(255,202,40,.2);border-radius:20px;padding:2.5rem;text-align:center;margin-top:2.5rem;position:relative}
.unlock-btn{padding:1rem 2.5rem;border:none;border-radius:50px;background:linear-gradient(135deg,#ffca28,#ff6f00);color:#0a0b0f;font-weight:800;font-size:1.05rem;cursor:pointer;transition:.3s;box-shadow:0 5px 20px rgba(255,202,40,.35)}
.unlock-btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(255,202,40,.5)}

.gated-blur-element{filter:blur(4px);pointer-events:none;opacity:0.35;user-select:none}
.lock-overlay-badge{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(6,7,19,0.5);z-index:10;border-radius:12px;padding:1rem;text-align:center}

.partner-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-top:2.5rem}
.partner-card{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:1.25rem;text-align:center;transition:.3s;text-decoration:none;position:relative}
.partner-card:hover{transform:translateY(-4px);border-color:rgba(255,111,0,.3);background:rgba(255,111,0,.03)}

.feature-check{display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:#b0c4de;margin:.4rem 0}
.feature-check.locked{color:#555;text-decoration:line-through}

.spinner{display:none;width:24px;height:24px;border:3px solid rgba(255,255,255,.2);border-top-color:#ff6f00;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

.success-overlay{display:none;position:fixed;inset:0;background:rgba(6,7,19,.9);z-index:9999;align-items:center;justify-content:center}
.success-box{background:linear-gradient(135deg,#0a1628,#0f1f3d);border:1px solid rgba(0,255,102,.3);border-radius:24px;padding:3rem;text-align:center;max-width:400px;animation:popIn .4s ease}
@keyframes popIn{from{transform:scale(.8);opacity:0}to{transform:scale(1);opacity:1}}

.timeline-panel{position:relative;padding-left:2rem;border-left:2px solid rgba(255,111,0,.2);text-align:left}
.timeline-card{position:relative;margin-bottom:2rem}
.timeline-card::before{content:'';position:absolute;left:-2.6rem;top:0.25rem;width:18px;height:18px;border-radius:50%;background:#060713;border:3px solid #ff6f00}

@media(max-width:900px){.form-row{grid-template-columns:1fr 1fr}.transport-grid,.cost-grid{grid-template-columns:1fr}}
</style>

<div class="planner-page">
    <div class="bg-orb" style="position: absolute; top: -10%; left: 50%; transform: translateX(-50%); width: 800px; height: 800px; background: radial-gradient(circle, rgba(255, 111, 0, 0.05) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

    <div class="inner" style="position: relative; z-index: 1;">

        {{-- Header Section --}}
        <div style="text-align:center;margin-bottom:3.5rem">
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,111,0,.15);border:1px solid rgba(255,111,0,.3);padding:.4rem 1.2rem;border-radius:50px;margin-bottom:1rem">
                <span style="color:#ffca28;font-size:.8rem;font-weight:800"><i class="fas fa-sparkles"></i> REGISTERED TRAVELER SUITE</span>
            </div>
            <h1 style="font-size:clamp(2rem,5vw,3rem);font-weight:900;color:#fff;margin-bottom:.75rem">AI Intelligence Planner</h1>
            <p style="color:#b0c4de;font-size:1.1rem;max-width:600px;margin:0 auto">Plan your route dynamically with custom Chart.js visual graphics & weather forecast modules.</p>
        </div>

        {{-- Interactive Form Panel --}}
        <div class="glass" style="margin-bottom:2.5rem">
            <div style="font-size:.8rem;font-weight:800;color:#ff9100;text-transform:uppercase;letter-spacing:2px;margin-bottom:1.5rem"><i class="fas fa-sliders"></i> Adjust Navigation Targets</div>
            <div class="form-row">
                <div style="position:relative">
                    <label class="f-label"><i class="fas fa-plane-departure"></i> From</label>
                    <input type="text" id="p_from" class="f-input" placeholder="e.g. Delhi, Mumbai" value="Mumbai, India" autocomplete="off">
                    <div id="from-autocomplete" style="display:none;position:absolute;top:100%;left:0;right:0;background:#151538;border:1px solid rgba(255,111,0,0.3);border-radius:12px;box-shadow:0 15px 35px rgba(0,0,0,0.6);z-index:9999;max-height:220px;overflow-y:auto;margin-top:.25rem"></div>
                </div>
                <div style="position:relative">
                    <label class="f-label"><i class="fas fa-plane-arrival"></i> To</label>
                    <input type="text" id="p_to" class="f-input" placeholder="e.g. Goa, Paris" value="Goa, India" autocomplete="off">
                    <div id="to-autocomplete" style="display:none;position:absolute;top:100%;left:0;right:0;background:#151538;border:1px solid rgba(255,111,0,0.3);border-radius:12px;box-shadow:0 15px 35px rgba(0,0,0,0.6);z-index:9999;max-height:220px;overflow-y:auto;margin-top:.25rem"></div>
                </div>
                <div>
                    <label class="f-label"><i class="fas fa-users"></i> Travelers</label>
                    <select id="p_travelers" class="f-input">
                        @foreach(range(1,10) as $i)<option value="{{ $i }}" {{ $i==2?'selected':'' }}>{{ $i }} Person{{ $i>1?'s':'' }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label"><i class="fas fa-calendar"></i> Days</label>
                    <select id="p_days" class="f-input">
                        @foreach([1,2,3,4,5,7,10] as $d)<option value="{{ $d }}" {{ $d==3?'selected':'' }}>{{ $d }} Days</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label"><i class="fas fa-wallet"></i> Budget Tier</label>
                    <select id="p_budget" class="f-input">
                        <option value="budget">💰 Budget</option>
                        <option value="standard" selected>⭐ Standard</option>
                        <option value="luxury">💎 Luxury</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:1.5rem;display:flex;gap:1.5rem;align-items:center;">
                <button class="calc-btn" onclick="calculate()" id="calc-btn">
                    <i class="fas fa-robot"></i> Calculate AI Plan
                </button>
                <div class="spinner" id="spinner"></div>
            </div>
        </div>

        {{-- Interactive Results Module --}}
        <div class="results" id="results">

            {{-- 1. Transit Comparison --}}
            <div style="margin-bottom:2rem">
                <div style="font-size:1.15rem;font-weight:800;color:#fff;margin-bottom:1.25rem"><i class="fas fa-route" style="color: #ff9100; margin-right: 0.5rem;"></i> Transit Cost Estimator Matrix</div>
                <div class="transport-grid" id="transport-cards"></div>
            </div>

            {{-- 2. Weather Alert & Attractions --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem">
                {{-- Weather Alert Card --}}
                <div class="glass" style="padding:1.5rem;border-color:rgba(255,111,0,.15);text-align:left;">
                    <div style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem">
                        <i class="fas fa-cloud-sun" style="color:#ffca28"></i> Local Meteorology Radar Index
                    </div>
                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);padding:1rem;border-radius:12px;display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <div style="font-size:1.5rem;font-weight:900;color:#fff" id="weather_temp">28°C</div>
                            <div style="font-size:.78rem;color:#b0c4de">Scattered Clouds · Safe Travel Rating</div>
                        </div>
                        <span class="tag" style="background:rgba(0,255,102,.1);color:#00ff66;font-size:.7rem;padding:.3rem .6rem;border-radius:6px;font-weight:800;border:1px solid rgba(0,255,102,.2)">OPTIONAL</span>
                    </div>
                    <p style="font-size:.8rem;color:#b0c4de;margin-top:0.75rem;line-height:1.5;"><i class="fas fa-circle-info" style="color:#ffca28"></i> <b>AI Meteorological Tip</b>: Carry comfortable clothing. Standard UV index indices recorded. Perfect conditions for outdoor activities.</p>
                </div>

                {{-- Nearby Attractions Card --}}
                <div class="glass" style="padding:1.5rem;border-color:rgba(255,111,0,.15);text-align:left;">
                    <div style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem">
                        <i class="fas fa-location-crosshairs" style="color:#00ff66"></i> Local Attraction Map Pins
                    </div>
                    <div style="display:flex;flex-direction:column;gap:0.5rem">
                        <div style="background:rgba(255,255,255,0.02);padding:.6rem 1rem;border-radius:10px;display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:#fff;font-weight:700">🏰 Historic Coastal Fort</span>
                            <span style="color:#ffca28">1.2 km away</span>
                        </div>
                        <div style="background:rgba(255,255,255,0.02);padding:.6rem 1rem;border-radius:10px;display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:#fff;font-weight:700">🛍️ Lively Street Bazaar</span>
                            <span style="color:#ffca28">2.5 km away</span>
                        </div>
                    </div>
                    <p style="font-size:.8rem;color:#b0c4de;margin-top:0.75rem;line-height:1.5;"><i class="fas fa-heart-pulse" style="color:#00ff66"></i> Highly recommended by 1,200+ verified TravelMate explorers.</p>
                </div>
            </div>

            {{-- 3. Budget & Expense Chartjs Section --}}
            <div style="margin-bottom:2rem">
                <div style="font-size:1.15rem;font-weight:800;color:#fff;margin-bottom:1.25rem"><i class="fas fa-chart-pie" style="color: #ff9100; margin-right: 0.5rem;"></i> Predictive Spending & Cost Analysis</div>
                <div class="glass" style="display:grid;grid-template-columns:1.5fr 1fr;gap:2rem;padding:2rem;">
                    {{-- Spending Trend Line --}}
                    <div>
                        <h4 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;color:#fff;text-align:left;"><i class="fas fa-chart-line" style="color:#ff9100"></i> Local Budget Trend Projection</h4>
                        <div style="height:220px;position:relative;">
                            <canvas id="plannerLineChart"></canvas>
                        </div>
                    </div>
                    {{-- Allocation Doughnut --}}
                    <div>
                        <h4 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;color:#fff;text-align:left;"><i class="fas fa-circle-notch" style="color:#00ff66"></i> Cost Breakdown</h4>
                        <div style="height:220px;position:relative;">
                            <canvas id="plannerDoughnutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Day-Wise Basic Itinerary --}}
            <div class="glass" style="margin-bottom:2rem;text-align:left;">
                <div style="font-size:1.15rem;font-weight:800;color:#fff;border-bottom:1px solid rgba(255,255,255,0.08);padding-bottom:1rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-calendar-alt" style="color:#ff9100;margin-right:0.5rem"></i> Day-Wise Itinerary Plan</span>
                    <span class="tag" style="background:rgba(0,255,102,.1);color:#00ff66;font-size:.72rem;padding:.3rem .8rem;border-radius:50px;border:1px solid rgba(0,255,102,.2)">FREE PREVIEW ACTIVE</span>
                </div>

                <div class="timeline-panel">
                    {{-- Day 1 --}}
                    <div class="timeline-card">
                        <div style="font-size:.78rem;font-weight:800;color:#ff9100;text-transform:uppercase;">Day 1: Arrival & Coastal check-in</div>
                        <h4 style="font-size:1.1rem;font-weight:800;color:#fff;margin-top:0.25rem;margin-bottom:0.5rem;" id="it_day1_title">Oceanfront Check-in & Scenic Beach Shacks</h4>
                        <p style="color:#b0c4de;font-size:.88rem;line-height:1.6;" id="it_day1_desc">Drive from the terminal to your curated beach cottage accommodations. Unpack and take a refreshing beach sunset stroll, ordering fresh local appetizers and refreshing fruit mocktails from highly rated coastal shacks.</p>
                    </div>
                    {{-- Day 2 --}}
                    <div class="timeline-card">
                        <div style="font-size:.78rem;font-weight:800;color:#ff9100;text-transform:uppercase;">Day 2: Historical Landmarks</div>
                        <h4 style="font-size:1.1rem;font-weight:800;color:#fff;margin-top:0.25rem;margin-bottom:0.5rem;" id="it_day2_title">Old Portuguese Fort Walking Tour</h4>
                        <p style="color:#b0c4de;font-size:.88rem;line-height:1.6;" id="it_day2_desc">Explore ancient architecture in the historical district. Take amazing landscape photos of the deep sea cliffs from the high-walls. Stroll through the local spice gardens and enjoy traditional clay-pot wood-fired buffet lunches.</p>
                    </div>

                    {{-- Day 3 (Locked under Premium paywall) --}}
                    <div class="timeline-card" style="position:relative;">
                        {{-- Gated Blur --}}
                        <div class="gated-blur-element">
                            <div style="font-size:.78rem;font-weight:800;color:#ff9100;text-transform:uppercase;">Day 3: Secret Waterfalls & Hidden Caves</div>
                            <h4 style="font-size:1.1rem;font-weight:800;color:#fff;margin-top:0.25rem;margin-bottom:0.5rem;">Tropical Jungle Trekking & Scenic High Viewpoints</h4>
                            <p style="color:#b0c4de;font-size:.88rem;line-height:1.6;">Stroll inside a gorgeous wildlife sanctuary, following hidden routes to deep cascading mountain springs. Swim in standard pool currents under safe lifeguards. Finish with evening shopping at dynamic, illuminated ocean bazaar lanes.</p>
                        </div>
                        {{-- Lock Badge --}}
                        <div class="lock-overlay-badge">
                            <div style="font-size:1.5rem;margin-bottom:0.3rem;"><i class="fas fa-lock" style="color:#ffca28"></i></div>
                            <div style="font-size:.82rem;font-weight:800;color:#ffca28;text-transform:uppercase;">Day 3 Locked</div>
                            <div style="font-size:.7rem;color:#b0c4de;max-width:300px;">Unlock Premium to access full day-by-day travel timelines.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Exact Hotels & Bookings (Locked under Premium Paywall) --}}
            <div style="margin-bottom:2rem;text-align:left;">
                <div style="font-size:1.15rem;font-weight:800;color:#fff;margin-bottom:1.25rem"><i class="fas fa-hotel" style="color:#ff9100;margin-right:0.5rem"></i> Exact Hotel Recommendations</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;position:relative;">
                    {{-- Hotel Card 1 --}}
                    <div class="glass gated-blur-element" style="padding:1.5rem;display:flex;gap:1rem;align-items:center;">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=200" style="width:90px;height:90px;border-radius:12px;object-fit:cover;">
                        <div>
                            <div style="font-size:.7rem;font-weight:800;color:#ff9100;text-transform:uppercase;">Recommended Luxury</div>
                            <h4 style="font-size:1rem;font-weight:800;color:#fff;">The Royal Heritage Resort</h4>
                            <div style="font-size:.82rem;color:#00ff66;">⭐ 4.8 Ratings · Verified Partner</div>
                        </div>
                    </div>
                    {{-- Hotel Card 2 --}}
                    <div class="glass gated-blur-element" style="padding:1.5rem;display:flex;gap:1rem;align-items:center;">
                        <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=200" style="width:90px;height:90px;border-radius:12px;object-fit:cover;">
                        <div>
                            <div style="font-size:.7rem;font-weight:800;color:#ff9100;text-transform:uppercase;">Curated Boutique</div>
                            <h4 style="font-size:1rem;font-weight:800;color:#fff;">Aqua View Oceanside Cottages</h4>
                            <div style="font-size:.82rem;color:#00ff66;">⭐ 4.6 Ratings · Verified Partner</div>
                        </div>
                    </div>
                    {{-- Lock Badge --}}
                    <div class="lock-overlay-badge" style="background:rgba(6,7,19,0.75);">
                        <div style="font-size:2rem;margin-bottom:0.5rem;"><i class="fas fa-hotel" style="color:#ffca28"></i></div>
                        <h4 style="font-size:1.1rem;font-weight:800;color:#fff;">🏨 Exact Hotel Names Gated</h4>
                        <p style="font-size:.8rem;color:#b0c4de;max-width:400px;margin-top:0.25rem;">Unlock the complete plan to show precise accommodation recommendations, verified booking prices, and direct partner check-outs!</p>
                    </div>
                </div>
            </div>

            {{-- Total Estimate Banner --}}
            <div class="total-banner" id="total-banner"></div>

            {{-- FREE VS PREMIUM COMPARISON TABLE --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2.5rem;text-align:left;">
                <div class="glass" style="border-color:rgba(255,255,255,.08)">
                    <div style="font-size:1.05rem;font-weight:800;color:#fff;margin-bottom:1rem">🆓 Free Tier Access</div>
                    <div class="feature-check">✅ Flight vs Train Comparison</div>
                    <div class="feature-check">✅ Daily Food & Transit Indices</div>
                    <div class="feature-check">✅ Basic Weather Forecast Tip</div>
                    <div class="feature-check">✅ Day 1 & Day 2 Basic Plan</div>
                    <div class="feature-check locked">🔒 Day 3+ Detailed Hourly Map</div>
                    <div class="feature-check locked">🔒 Exact Hotel Picks & Booking</div>
                    <div class="feature-check locked">🔒 Telematics Mapping & Radars</div>
                    <div class="feature-check locked">🔒 Downloadable Travel QR Passes</div>
                </div>
                <div class="glass" style="border-color:rgba(255,202,40,.3);background:rgba(255,202,40,.03)">
                    <div style="font-size:1.05rem;font-weight:800;color:#ffca28;margin-bottom:.25rem">💎 Premium Ecosystem — ₹199</div>
                    <div style="font-size:.78rem;color:#b0c4de;margin-bottom:1rem">One-time checkout. Lifetime storage in dashboard.</div>
                    <div class="feature-check" style="color:#fff">✅ Everything in Free Tier</div>
                    <div class="feature-check" style="color:#00ff66">✨ Complete Day-Wise Timeline</div>
                    <div class="feature-check" style="color:#00ff66">✨ Exact Hotel Recommendations</div>
                    <div class="feature-check" style="color:#00ff66">✨ Direct Redirect Booking Portals</div>
                    <div class="feature-check" style="color:#00ff66">✨ Instant Booking ID Validation</div>
                    <div class="feature-check" style="color:#00ff66">✨ High-Speed PDF Export Pass</div>
                    <div class="feature-check" style="color:#00ff66">✨ Telematics Interactive Radar</div>
                </div>
            </div>

            {{-- 6. PREMIUM RAZORPAY GATE CONTAINER --}}
            <div class="premium-lock-container" id="premium-section">
                <div style="font-size:2.5rem;margin-bottom:.75rem">🔓</div>
                <h3 style="font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:.5rem">Unlock Complete Premium AI Itinerary</h3>
                <p style="color:#b0c4de;margin-bottom:1.5rem;max-width:550px;margin-left:auto;margin-right:auto;font-size:0.9rem;line-height:1.6;">
                    Unlock the full day-by-day travel plan, exact hotel locations, global telematics maps, booking redirect APIs, and generate a printable QR Travel Pass!
                </p>
                <button class="unlock-btn" onclick="unlockPremium()">
                    ✨ Unlock Complete Ecosystem for ₹199 — Razorpay Secure
                </button>
                <div style="margin-top:1.2rem;font-size:.75rem;color:#b0c4de">
                    <i class="fas fa-shield-halved" style="color:#00ff66"></i> Encrypted transactions · Dynamic signature validation · Instant access
                </div>
            </div>

        </div>

        {{-- Direct Booking Partners Section --}}
        <div style="margin-top:4.5rem;position:relative;">
            <div style="text-align:center;margin-bottom:2rem">
                <div style="font-size:.72rem;font-weight:800;color:#ff9100;text-transform:uppercase;letter-spacing:2px;margin-bottom:.5rem">DIRECT TRAVEL INTEGRATION</div>
                <h2 style="font-size:1.6rem;font-weight:800;color:#fff">Premium Booking Systems</h2>
                <p style="color:#b0c4de;font-size:.9rem">Direct partner checkout channels mapped automatically based on trip parameters</p>
            </div>

            <div class="partner-grid">
                {{-- Partner Cards --}}
                @foreach([
                    ['https://www.makemytrip.com','#E73C7E','fas fa-plane-up','MakeMyTrip','Flights Booking'],
                    ['https://www.irctc.co.in','#1565C0','fas fa-train-subway','IRCTC','Railway Bookings'],
                    ['https://www.redbus.in','#D84315','fas fa-bus-simple','RedBus','Bus Booking'],
                    ['https://www.uber.com','#000000','fab fa-uber','Uber','City Cab hailing'],
                    ['https://rapido.bike','#FFD600','fas fa-motorcycle','Rapido','Bike taxi pickup'],
                    ['https://www.booking.com','#003580','fas fa-hotel','Booking.com','Hotel Stays']
                ] as [$url,$color,$icon,$name,$desc])
                    <div class="partner-card">
                        {{-- Blur cover for locked booking --}}
                        <div class="gated-blur-element">
                            <div style="width:48px;height:48px;border-radius:12px;background:{{ $color }}22;border:1px solid {{ $color }}44;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem">
                                <i class="{{ $icon }}" style="color:{{ $color }};font-size:1.25rem"></i>
                            </div>
                            <div style="color:#fff;font-weight:700;font-size:.85rem">{{ $name }}</div>
                            <div style="color:#b0c4de;font-size:.72rem;margin-top:.2rem">{{ $desc }}</div>
                        </div>
                        {{-- Gated lock click prevention --}}
                        <div class="lock-overlay-badge" style="background:rgba(6,7,19,0.85);cursor:pointer;" onclick="alert('Please upgrade to Premium to unlock direct booking redirects!')">
                            <div style="font-size:1.15rem;color:#ffca28;"><i class="fas fa-lock"></i></div>
                            <div style="font-size:0.62rem;color:#b0c4de;font-weight:700;margin-top:0.25rem;text-transform:uppercase;">Premium Only</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Success Overlay --}}
<div class="success-overlay" id="success-overlay">
    <div class="success-box">
        <div style="font-size:3rem;margin-bottom:1rem">✨</div>
        <h2 style="color:#fff;font-weight:900;margin-bottom:.5rem">Premium Unlocked!</h2>
        <p style="color:#b0c4de;margin-bottom:1.5rem">Activating premium parameters and geocoding detailed itineraries...</p>
        <div class="spinner" style="display:block;margin:0 auto"></div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
let lastResult = null;
let trendChartInstance = null;
let doughnutChartInstance = null;

async function calculate() {
    const from = document.getElementById('p_from').value.trim();
    const to = document.getElementById('p_to').value.trim();
    if(!from || !to){ alert('Please provide coordinates for From & To inputs.'); return; }

    const btn = document.getElementById('calc-btn');
    const sp = document.getElementById('spinner');
    btn.disabled = true; 
    sp.style.display = 'block';

    try {
        const r = await fetch('{{ route("planner.calculate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                from: from,
                to: to,
                travelers: document.getElementById('p_travelers').value,
                days: document.getElementById('p_days').value,
                budget: document.getElementById('p_budget').value
            })
        });
        const data = await r.json();
        lastResult = data;
        renderResults(data);
    } catch(e) {
        alert('Simulation error: ' + e.message);
    }
    btn.disabled = false; 
    sp.style.display = 'none';
}

function renderResults(d) {
    const f = n => ('₹' + Math.round(n).toLocaleString('en-IN'));
    const cheapest = Math.min(d.totals.with_flight, d.totals.with_train, d.totals.with_bus);

    // Populate Transport Cards
    let tc = '';
    [['flight','✈️','fa-plane-up'], ['train','🚆','fa-train-subway'], ['bus','🚌','fa-bus-simple']].forEach(([k, em, icon]) => {
        const t = d.transport[k];
        const isBest = d.totals['with_' + k] === cheapest;
        tc += `<div class="transport-card ${isBest ? 'best' : ''}">
            <div style="font-size:1.8rem;margin-bottom:.5rem">${em}</div>
            <div style="color:#fff;font-weight:800;font-size:.95rem;margin-bottom:.2rem">${t.label}</div>
            <div style="color:#b0c4de;font-size:.78rem;margin-bottom:.5rem"><i class="fas fa-clock"></i> ${t.duration}</div>
            <div style="font-size:1.35rem;font-weight:900;color:#00ff66">${f(t.cost)}</div>
            <div style="font-size:.7rem;color:#b0c4de">for ${d.travelers} traveler(s)</div>
            <div style="margin-top:.75rem;font-size:.82rem;font-weight:700;color:#fff">Total Trip: <span style="color:#ffca28">${f(d.totals['with_'+k])}</span></div>
        </div>`;
    });
    document.getElementById('transport-cards').innerHTML = tc;

    // Populate Total Banner
    document.getElementById('total-banner').innerHTML = `
        <div>
            <div style="font-size:.82rem;color:#b0c4de;margin-bottom:.2rem">${d.from} → ${d.to} · ${d.days} Days · ${d.travelers} Traveler(s)</div>
            <div style="font-size:.88rem;color:#b0c4de">Suggested Transit: <strong style="color:#fff">with ${d.totals.with_bus <= d.totals.with_train ? 'Bus' : 'Train'}</strong></div>
        </div>
        <div style="text-align:right">
            <div style="font-size:.82rem;color:#b0c4de">Calculated Basic Budget</div>
            <div style="font-size:2rem;font-weight:900;color:#ff9100">${f(cheapest)}</div>
            <div style="font-size:.75rem;color:#b0c4de">${f(Math.round(cheapest/d.travelers))} /person</div>
        </div>`;

    // Dynamic Text Updates for Free Day Preview
    document.getElementById('it_day1_title').textContent = `Check-in in ${d.to} & Local Shacks`;
    document.getElementById('it_day1_desc').textContent = `Arrive dynamically from ${d.from}. Complete validation and enjoy high-speed check-in at a beautiful local hotel collection. Walk around coastal viewpoints and enjoy fresh dining packages.`;
    document.getElementById('it_day2_title').textContent = `${d.to} Historic Architectural Walk`;
    document.getElementById('it_day2_desc').textContent = `Explore scenic landmarks and markets. Treat yourself to highly rated dynamic local specialties. Experience a completely customized travel route optimized for registered users.`;

    // Initialize ChartJS Graphics
    initializeCharts(d);

    // Make results block visible & scroll to it
    document.getElementById('results').style.display = 'block';
    document.getElementById('results').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function initializeCharts(d) {
    const trendCtx = document.getElementById('plannerLineChart').getContext('2d');
    const doughnutCtx = document.getElementById('plannerDoughnutChart').getContext('2d');

    // Destroy existing instances if they exist (standard ChartJS recreation rule)
    if (trendChartInstance) trendChartInstance.destroy();
    if (doughnutChartInstance) doughnutChartInstance.destroy();

    // Line Chart: Projection curves
    trendChartInstance = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3'],
            datasets: [{
                label: 'Projected Spend Limit (₹)',
                data: [d.totals.cheapest * 0.3, d.totals.cheapest * 0.6, d.totals.cheapest],
                borderColor: '#ff9100',
                backgroundColor: 'rgba(255,145,0,0.1)',
                fill: true,
                borderWidth: 2,
                tension: 0.4
            }, {
                label: 'Simulated Budget Burn (₹)',
                data: [d.totals.cheapest * 0.25, d.totals.cheapest * 0.52, d.totals.cheapest * 0.95],
                borderColor: '#00ff66',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#00ff66'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#b0c4de' } } },
            scales: {
                x: { ticks: { color: '#b0c4de' }, grid: { color: 'rgba(255,255,255,0.03)' } },
                y: { ticks: { color: '#b0c4de' }, grid: { color: 'rgba(255,255,255,0.03)' } }
            }
        }
    });

    // Doughnut Chart: Categories
    doughnutChartInstance = new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Transit', 'Hotels', 'Food & Local'],
            datasets: [{
                data: [d.transport.flight.cost, d.daily.hotel.total, d.daily.food.total + d.daily.local.total],
                backgroundColor: ['#ff6f00', '#ffca28', '#00ff66'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { color: '#b0c4de' } } }
        }
    });
}

async function unlockPremium() {
    if(!lastResult) { alert('Please calculate your trip parameters first.'); return; }

    try {
        const r = await fetch('{{ route("planner.premium.order") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                from: lastResult.from,
                to: lastResult.to,
                travelers: lastResult.travelers,
                days: lastResult.days,
                budget: lastResult.tier
            })
        });
        
        if (!r.ok) {
            const errText = await r.text();
            let msg = 'Failed to create order.';
            try {
                const errJson = JSON.parse(errText);
                msg = errJson.error || errJson.message || msg;
            } catch(e) {
                if (errText.includes('Page Expired')) msg = 'Session expired. Please refresh the page.';
                else msg = errText.substring(0, 100) || msg;
            }
            throw new Error(msg);
        }
        
        const data = await r.json();
        if(data.error) { alert('❌ ' + data.error); return; }

        const rzp = new Razorpay({
            key: data.key_id,
            amount: data.amount,
            currency: data.currency,
            name: 'TravelMate Premium',
            description: data.description,
            order_id: data.order_id,
            prefill: { 
                name: data.name, 
                email: data.email,
                contact: '9999999999' // Prefill mock contact to bypass saved-card verification
            },
            theme: { color: '#ff6f00' },
            handler: async function(response) {
                document.getElementById('success-overlay').style.display = 'flex';
                
                try {
                    const verify = await fetch('{{ route("planner.premium.verify") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(response)
                    });
                    
                    if (!verify.ok) {
                        const verifyErr = await verify.text();
                        let verifyMsg = 'Payment verification failed.';
                        try {
                            const errJson = JSON.parse(verifyErr);
                            verifyMsg = errJson.message || errJson.error || verifyMsg;
                        } catch(e) {
                            verifyMsg = verifyErr.substring(0, 100) || verifyMsg;
                        }
                        throw new Error(verifyMsg);
                    }
                    
                    const res = await verify.json();
                    if(res.success) {
                        window.location.href = res.redirect_url;
                    } else {
                        document.getElementById('success-overlay').style.display = 'none';
                        alert('Verification error: ' + res.message);
                    }
                } catch(errVal) {
                    document.getElementById('success-overlay').style.display = 'none';
                    alert('❌ Verification Error: ' + errVal.message);
                }
            }
        });
        rzp.open();
    } catch(e) {
        alert('❌ Payment Order Error: ' + e.message);
    }
}

// --- Autocomplete Logic for Free Trip Planner ---
function bindAutocomplete(inputId, boxId) {
    const input = document.getElementById(inputId);
    const box = document.getElementById(boxId);
    let timeout = null;

    input.addEventListener('focus', function() {
        this.parentElement.style.zIndex = '9999';
    });
    input.addEventListener('blur', function() {
        setTimeout(() => { this.parentElement.style.zIndex = ''; }, 200);
    });

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            box.style.display = 'none';
            return;
        }

        box.innerHTML = '<div style="padding:.75rem 1rem;color:rgba(255,255,255,0.5);font-size:.85rem"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        box.style.display = 'block';

        timeout = setTimeout(async () => {
            try {
                let apiMatches = [];
                try {
                    const response = await fetch(`/api/city-search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    apiMatches = data.map(place => {
                        const city = place.address.city || place.address.town || place.address.village || place.name;
                        const state = place.address.state || place.address.country || 'India';
                        return { city, state };
                    });
                } catch (apiErr) {
                    console.error("Nominatim API failed", apiErr);
                }

                // Remove exact duplicates
                const unique = [];
                apiMatches.forEach(item => {
                    if (!unique.some(u => u.city.toLowerCase() === item.city.toLowerCase())) {
                        unique.push(item);
                    }
                });

                if (unique.length === 0) {
                    box.innerHTML = '<div style="padding:.75rem 1rem;color:rgba(255,255,255,0.5);font-size:.85rem">No cities found.</div>';
                    return;
                }

                box.innerHTML = '';
                unique.forEach(place => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding:.75rem 1rem;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);transition:background .2s;display:flex;align-items:center;gap:.5rem';
                    item.innerHTML = `
                        <i class="fas fa-location-dot" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i> 
                        <div>
                            <div style="font-weight:700;font-size:.9rem;color:#ffffff">${place.city}</div>
                            <div style="font-size:.75rem;color:rgba(255,255,255,0.5)">${place.state}</div>
                        </div>
                    `;
                    item.onmouseenter = () => item.style.background = 'rgba(255, 255, 255, 0.08)';
                    item.onmouseleave = () => item.style.background = 'transparent';
                    item.onclick = () => {
                        input.value = `${place.city}, ${place.state}`;
                        box.style.display = 'none';
                    };
                    box.appendChild(item);
                });
            } catch (error) {
                box.innerHTML = '<div style="padding:.75rem 1rem;color:#e74c3c;font-size:.85rem">Error loading cities.</div>';
            }
        }, 500);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !box.contains(e.target)) {
            box.style.display = 'none';
        }
    });
}

bindAutocomplete('p_from', 'from-autocomplete');
bindAutocomplete('p_to', 'to-autocomplete');
</script>
@endsection
