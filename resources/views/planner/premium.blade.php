@extends('layouts.app')
@section('title','Premium AI Travel Ecosystem — TravelMate')
@section('content')

{{-- Leaflet Maps Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Outfit',sans-serif;scroll-behavior:smooth}

.prem-page{
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
    margin-bottom:2rem;
}

.section-head{
    font-size:1.25rem;
    font-weight:800;
    color:#fff;
    margin-bottom:1.5rem;
    display:flex;
    align-items:center;
    gap:.5rem;
    border-bottom:1px solid rgba(255,255,255,0.08);
    padding-bottom:0.75rem;
}

.day-card{
    background:rgba(255,255,255,.02);
    border:1px solid rgba(255,255,255,.05);
    border-radius:18px;
    padding:1.75rem;
    margin-bottom:1.5rem;
}

.activity-row{
    display:flex;
    gap:1.25rem;
    padding:1rem 0;
    border-bottom:1px solid rgba(255,255,255,.04);
}
.activity-row:last-child{border:none}

.time-badge{
    background:rgba(255,111,0,.15);
    color:#ffca28;
    padding:.3rem .75rem;
    border-radius:8px;
    font-size:.78rem;
    font-weight:800;
    white-space:nowrap;
    align-self:start;
    border:1px solid rgba(255,111,0,.25);
}

.hotel-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1.5rem;
    margin-bottom:1rem;
}
.hotel-card{
    background:rgba(255,255,255,.02);
    border:1px solid rgba(255,255,255,.05);
    border-radius:18px;
    padding:1.5rem;
    display:flex;
    gap:1.25rem;
    align-items:center;
    transition:0.25s;
    text-align:left;
}
.hotel-card:hover{
    transform:translateY(-3px);
    border-color:#ff6f00;
    background:rgba(255,111,0,0.03);
}

.tip-chip{
    background:rgba(0,255,102,.05);
    border:1px solid rgba(0,255,102,.15);
    color:#00ff66;
    padding:.5rem 1rem;
    border-radius:10px;
    font-size:.82rem;
    margin-bottom:0.5rem;
    text-align:left;
}

.success-badge{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    background:rgba(0,255,102,.15);
    border:1px solid rgba(0,255,102,.3);
    color:#00ff66;
    padding:.4rem 1.2rem;
    border-radius:50px;
    font-size:.8rem;
    font-weight:800;
    margin-bottom:1.25rem;
    text-transform:uppercase;
    letter-spacing:1px;
}

/* Printable QR Travel Pass */
.qr-pass-container{
    background:linear-gradient(135deg,#111329 0%,#090a16 100%);
    border:2px dashed rgba(255,111,0,0.3);
    border-radius:24px;
    padding:2.5rem;
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:2rem;
    align-items:center;
    text-align:left;
    position:relative;
    overflow:hidden;
}
.qr-pass-container::before{
    content:'PASS';
    position:absolute;
    bottom:-20px;
    right:-10px;
    font-size:8rem;
    font-weight:900;
    color:rgba(255,255,255,0.01);
}

.partner-link-grid{
    display:grid;
    grid-template-columns:repeat(6,1fr);
    gap:1rem;
}
.partner-link-card{
    background:rgba(255,255,255,.02);
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:1.25rem;
    text-align:center;
    text-decoration:none;
    transition:.3s;
    display:block;
}
.partner-link-card:hover{
    transform:translateY(-4px);
    border-color:#ff6f00;
    background:rgba(255,111,0,.04);
}

@media(max-width: 992px){
    .qr-pass-container { grid-template-columns: 1fr; text-align: center; }
    .partner-link-grid { grid-template-columns: repeat(3, 1fr); }
    .hotel-grid { grid-template-columns: 1fr; }
}
@media(max-width: 600px){
    .partner-link-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="prem-page">
    <div class="bg-orb" style="position: absolute; top: -10%; left: 50%; transform: translateX(-50%); width: 800px; height: 800px; background: radial-gradient(circle, rgba(255, 111, 0, 0.05) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

    <div class="inner" style="position: relative; z-index: 1;">

        {{-- Premium Header --}}
        <div style="text-align:center;margin-bottom:3.5rem">
            <div class="success-badge"><i class="fas fa-crown"></i> COMPLETE PREMIUM AI ECOSYSTEM UNLOCKED</div>
            <h1 style="font-size:clamp(2rem,5vw,3.25rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#ffca28 70%,#ff6f00 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.5rem">
                Your Optimized AI Travel Plan
            </h1>
            <p style="color:#b0c4de;font-size:1.1rem">
                {{ $planData['from'] ?? 'Origin' }} ➡️ {{ $planData['to'] ?? 'Destination' }} · 
                {{ $planData['days'] ?? '3' }} Days · {{ $planData['travelers'] ?? '2' }} Traveler(s) · {{ ucfirst($planData['budget'] ?? 'Standard') }} Budget
            </p>
        </div>

        {{-- ✅ PRINTABLE QR TRAVEL PASS --}}
        <div class="glass" style="padding:0;overflow:hidden;border-color:rgba(255,111,0,.25);">
            <div class="qr-pass-container">
                <div>
                    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,111,0,.15);border:1px solid rgba(255,111,0,.3);padding:.3rem .8rem;border-radius:50px;font-size:.7rem;font-weight:800;color:#ffca28;margin-bottom:1rem;text-transform:uppercase;">
                        <i class="fas fa-ticket-airline"></i> Verified Airport Boarding Pass
                    </div>
                    <h2 style="font-size:1.8rem;font-weight:900;color:#fff;margin-bottom:0.5rem;">TravelMate Boarding QR Pass</h2>
                    <p style="color:#b0c4de;font-size:.9rem;line-height:1.6;margin-bottom:1.5rem;max-width:550px;">
                        This digital pass serves as your verified itinerary ledger reference. Present this QR travel pass at partnered local terminals and lodgings to claim TravelMate loyalty perks.
                    </p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:0.85rem;color:#b0c4de;">
                        <div>
                            <span style="display:block;font-size:0.7rem;color:#ff9100;font-weight:800;text-transform:uppercase;">Booking ID</span>
                            <strong style="color:#fff;font-size:0.95rem;">TMT-{{ strtoupper(\Illuminate\Support\Str::random(6)) }}-PREM</strong>
                        </div>
                        <div>
                            <span style="display:block;font-size:0.7rem;color:#ff9100;font-weight:800;text-transform:uppercase;">Reference Token</span>
                            <strong style="color:#fff;font-size:0.95rem;">{{ $transaction }}</strong>
                        </div>
                    </div>
                </div>
                <div style="text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <div style="background:#fff;padding:1rem;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,0.5);display:inline-block;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TravelMate-Pass-{{ $transaction }}" style="width:130px;height:130px;display:block;" alt="Booking QR Pass">
                    </div>
                    <div style="font-size:.7rem;color:#ff9100;margin-top:0.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;"><i class="fas fa-print"></i> Scan for Terminal Check</div>
                </div>
            </div>
        </div>

        {{-- ✅ ROUTE OPTIMIZATION & TELEMATICS MAP RADAR --}}
        <div class="glass">
            <div class="section-head">
                <span><i class="fas fa-map-location-dot" style="color:#ff6f00;"></i></span> Interactive Route Optimization & Telematics Map
            </div>
            <p style="color:#b0c4de;font-size:0.9rem;text-align:left;margin-top:-0.5rem;margin-bottom:1.5rem;">
                Our active geocoding service has mapped optimal trajectories. Expand, zoom, and select markers to trace daily events live.
            </p>
            <div id="premiumMap" style="height:350px;border-radius:18px;border:1px solid rgba(255,255,255,0.1);z-index:1;overflow:hidden;"></div>
        </div>

        {{-- ✅ BOOKING SYSTEMS CHANNELS --}}
        <div class="glass" style="border-color:rgba(0,255,102,0.15);">
            <div class="section-head" style="border-color:rgba(0,255,102,0.1);color:#00ff66;">
                <span><i class="fas fa-circle-check"></i></span> Premium Booking Redirect Channels
            </div>
            <p style="color:#b0c4de;font-size:0.9rem;text-align:left;margin-top:-0.5rem;margin-bottom:1.5rem;">
                Your premium status is validated. Click on any channel below to auto-propagate coordinates directly into booking networks.
            </p>
            <div class="partner-link-grid">
                <a href="https://www.makemytrip.com" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#E73C7E;"><i class="fas fa-plane"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">MakeMyTrip</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Book Flights →</div>
                </a>
                <a href="https://www.irctc.co.in" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#1565C0;"><i class="fas fa-train"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">IRCTC</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Book Train →</div>
                </a>
                <a href="https://www.redbus.in" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#D84315;"><i class="fas fa-bus"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">redBus</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Book Bus →</div>
                </a>
                <a href="https://www.uber.com" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#fff;"><i class="fab fa-uber"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">Uber Cabs</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Call Uber →</div>
                </a>
                <a href="https://rapido.bike" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#FFD600;"><i class="fas fa-motorcycle"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">Rapido</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Book Bike →</div>
                </a>
                <a href="https://www.booking.com" target="_blank" class="partner-link-card">
                    <div style="font-size:2rem;margin-bottom:.4rem;color:#003580;"><i class="fas fa-hotel"></i></div>
                    <div style="color:#fff;font-weight:700;font-size:.85rem">Booking.com</div>
                    <div style="color:#00ff66;font-size:.7rem;font-weight:800;margin-top:0.25rem">Book Hotels →</div>
                </a>
            </div>
        </div>

        {{-- ✅ DAY-BY-DAY HOUR-BY-HOUR ITINERARY --}}
        <div class="glass">
            <div class="section-head" style="text-align:left;">
                <span><i class="fas fa-route" style="color:#ff6f00"></i></span> Full Hour-by-Hour Travel Itinerary
            </div>
            @foreach($aiPlan['days'] ?? [] as $day)
            <div class="day-card" style="text-align:left;">
                <div style="font-size:1.15rem;font-weight:800;color:#ff6f00;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;">
                    <span>{{ $day['theme'] ?? 'Day '.$day['day_number'] }}</span>
                    <span class="tag" style="background:rgba(255,111,0,0.15);color:#ffca28;font-size:0.7rem;padding:0.25rem 0.6rem;border-radius:6px;border:1px solid rgba(255,111,0,0.25);">DAY {{ $day['day_number'] }} SCHEDULE</span>
                </div>
                @foreach($day['activities'] ?? [] as $act)
                <div class="activity-row">
                    <span class="time-badge">{{ $act['time'] ?? '08:00 AM' }}</span>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:.95rem">{{ $act['activity'] ?? '' }}</div>
                        <div style="color:#b0c4de;font-size:.85rem;margin-top:.15rem;line-height:1.5;">{{ $act['description'] ?? '' }}</div>
                    </div>
                    @if(!empty($act['cost_inr']))
                    <div style="margin-left:auto;color:#00ff66;font-weight:800;font-size:.9rem;white-space:nowrap">₹{{ number_format($act['cost_inr']) }}</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- ✅ EXACT HOTEL RECOMMENDATIONS & DISCOVERY --}}
        @if(!empty($aiPlan['hotels']))
        <div class="glass">
            <div class="section-head" style="text-align:left;">
                <span><i class="fas fa-hotel" style="color:#ff6f00"></i></span> Premium Hotel Recommendations
            </div>
            <div class="hotel-grid">
                @foreach($aiPlan['hotels'] as $hotel)
                <div class="hotel-card">
                    <div style="width:64px;height:64px;border-radius:14px;background:rgba(255,111,0,.15);color:#ffca28;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.6rem;box-shadow:0 0 15px rgba(255,111,0,.15)"><i class="fas fa-hotel"></i></div>
                    <div style="flex:1">
                        <div style="color:#fff;font-weight:800;font-size:1.05rem;">{{ $hotel['name'] ?? 'Premium Hotel' }}</div>
                        <div style="color:#b0c4de;font-size:.82rem;margin-top:0.2rem;">{{ $hotel['type'] ?? 'Luxury Stay' }} · <span style="color:#ffca28;">⭐ {{ $hotel['rating'] ?? '4.8' }} Ratings</span></div>
                        @if(!empty($hotel['highlights']))
                        <div style="margin-top:.6rem">
                            @foreach(array_slice($hotel['highlights'],0,3) as $h)
                            <span style="font-size:.68rem;background:rgba(0,255,102,.1);color:#00ff66;padding:.2rem .6rem;border-radius:6px;margin-right:.25rem;border:1px solid rgba(0,255,102,.15);">{{ $h }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:1.25rem;font-weight:900;color:#00ff66">₹{{ number_format($hotel['price_per_night'] ?? 4500) }}</div>
                        <div style="font-size:.7rem;color:#b0c4de;text-transform:uppercase;font-weight:700;">/ night</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ✅ METEOROLOGICAL FORECAST & TRAVEL SAFETY TIPS --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;text-align:left;margin-bottom:2rem;">
            {{-- Weather Card --}}
            @if(!empty($aiPlan['weather']))
            <div class="glass" style="margin-bottom:0;">
                <div class="section-head" style="font-size:1.1rem;border-bottom:none;margin-bottom:0.75rem;">
                    <span><i class="fas fa-cloud-sun" style="color:#ffca28"></i></span> Weather Guide
                </div>
                <div style="color:#ffca28;font-weight:800;font-size:0.9rem;margin-bottom:.25rem;">Best time to travel</div>
                <div style="color:#fff;font-size:1rem;font-weight:700;margin-bottom:0.5rem;">{{ $aiPlan['weather']['best_time'] ?? 'October to April' }}</div>
                <div style="font-size:1.75rem;font-weight:900;color:#00ff66;margin-bottom:.5rem;">{{ $aiPlan['weather']['temperature'] ?? '26–30°C' }}</div>
                <div style="color:#b0c4de;font-size:.82rem;line-height:1.5;">{{ $aiPlan['weather']['tip'] ?? '' }}</div>
            </div>
            @endif

            {{-- Nearby Attractions --}}
            @if(!empty($aiPlan['nearby_attractions']))
            <div class="glass" style="margin-bottom:0;">
                <div class="section-head" style="font-size:1.1rem;border-bottom:none;margin-bottom:0.75rem;">
                    <span><i class="fas fa-location-crosshairs" style="color:#00ff66"></i></span> Nearby Discovery
                </div>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach($aiPlan['nearby_attractions'] as $att)
                    <div style="background:rgba(255,255,255,0.02);padding:0.6rem 0.8rem;border-radius:10px;display:flex;justify-content:space-between;align-items:center;font-size:.8rem;">
                        <div>
                            <div style="color:#fff;font-weight:700;">{{ $att['name'] }}</div>
                            <div style="color:#b0c4de;font-size:.7rem;">{{ $att['distance'] }} away</div>
                        </div>
                        <span style="color:#00ff66;font-weight:800;">{{ $att['entry_fee'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Travel Safety Tips --}}
            <div class="glass" style="margin-bottom:0;">
                <div class="section-head" style="font-size:1.1rem;border-bottom:none;margin-bottom:0.75rem;">
                    <span><i class="fas fa-shield-heart" style="color:#ff2a2a"></i></span> Travel Safety Tips
                </div>
                <div style="display:flex;flex-direction:column;gap:0.4rem;">
                    <div class="tip-chip"><i class="fas fa-circle-check"></i> Always keep digital offline backup of pass credentials.</div>
                    <div class="tip-chip"><i class="fas fa-circle-check"></i> Connect to verified local taxi networks only.</div>
                    <div class="tip-chip"><i class="fas fa-circle-check"></i> Keep local security hotline numbers pinned.</div>
                </div>
            </div>
        </div>

        {{-- Direct Action Buttons --}}
        <div style="display:flex;gap:1rem;justify-content:center;margin-top:2.5rem;flex-wrap:wrap;padding-bottom:2rem;">
            <a href="{{ route('planner.index') }}" class="btn btn-primary" style="padding:1rem 2.5rem;border-radius:50px;font-weight:800;background:linear-gradient(135deg,#ffca28,#ff6f00);box-shadow:0 4px 15px rgba(255,111,0,0.35);color:#fff;text-decoration:none;">
                <i class="fas fa-rocket"></i> Plan Another Route
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding:1rem 2.5rem;border-radius:50px;border:1px solid rgba(255,255,255,0.15);color:#fff;font-weight:700;text-decoration:none;">
                <i class="fas fa-gauge-high"></i> Travel Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-outline" style="padding:1rem 2.5rem;border-radius:50px;border:1px solid rgba(0,255,102,0.25);color:#00ff66;font-weight:700;background:rgba(0,255,102,0.02);">
                <i class="fas fa-print"></i> Print Itinerary Pass
            </button>
        </div>

    </div>
</div>

<script>
// Initialize Premium Routing Map
document.addEventListener('DOMContentLoaded', function() {
    const destinationCity = "{{ $planData['to'] ?? 'Goa, India' }}";
    
    // Create Map Instance centering dynamically
    const map = L.map('premiumMap', { zoomControl: true }).setView([15.2993, 74.1240], 6);

    // Render Premium Dark tiles matching luxury portal theme
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Geocode to center map dynamically on destination city
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(destinationCity)}&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);

                // Smoothly focus coordinates with Zoom Index 12
                map.setView([lat, lon], 12);

                // Add Premium popup pin marker
                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>${destinationCity}</b><br>Premium Route Optimization Active!`)
                    .openPopup();
            }
        })
        .catch(() => {});
});
</script>

@endsection
