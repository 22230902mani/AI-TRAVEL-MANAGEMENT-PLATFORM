@extends('layouts.app')
@section('title', 'Recommended Hotels — TravelMate')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Poppins',sans-serif}
.hotels-page{background:linear-gradient(135deg,#0a0b0f 0%,#0f0f2e 50%,#0a1628 100%);min-height:100vh;padding:2rem 1rem 6rem}
.hotels-inner{max-width:1300px;margin:0 auto}
.hero-strip{background:rgba(255,255,255,.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:2rem;margin-bottom:3rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.badge-pill{background:rgba(0,212,170,.15);border:1px solid rgba(0,212,170,.3);color:#00d4aa;padding:.4rem 1rem;border-radius:50px;font-size:.8rem;font-weight:600}
.hotel-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:2rem}
.hotel-card{background:rgba(255,255,255,.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:24px;overflow:hidden;transition:all .4s cubic-bezier(.175,.885,.32,1.275)}
.hotel-card:hover{transform:translateY(-8px);box-shadow:0 20px 50px rgba(0,0,0,.5);border-color:rgba(0,212,170,.3)}
.hotel-img{position:relative;height:220px;overflow:hidden}
.hotel-img img{width:100%;height:100%;object-fit:cover;transition:transform .6s ease}
.hotel-card:hover .hotel-img img{transform:scale(1.08)}
.stars{color:#ffca28;font-size:.9rem}
.amenity-tag{background:rgba(108,99,255,.15);border:1px solid rgba(108,99,255,.2);color:#a29cf4;padding:.25rem .65rem;border-radius:6px;font-size:.72rem;font-weight:600}
.rec-badge{position:absolute;top:1rem;left:1rem;background:linear-gradient(135deg,#6c63ff,#00d4aa);color:#fff;font-size:.72rem;font-weight:700;padding:.3rem .8rem;border-radius:50px}
.price-tag{position:absolute;top:1rem;right:1rem;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);color:#fff;padding:.4rem .8rem;border-radius:10px;font-weight:800;font-size:.9rem;border:1px solid rgba(255,255,255,.1)}
.book-btn{display:block;width:100%;padding:.85rem;border:none;border-radius:12px;font-family:'Poppins',sans-serif;font-weight:700;font-size:.95rem;cursor:pointer;background:linear-gradient(135deg,#6c63ff,#00d4aa);color:#fff;transition:all .3s;text-align:center;text-decoration:none}
.book-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(108,99,255,.4);color:#fff}
.highlight-chip{display:inline-flex;align-items:center;gap:.3rem;background:rgba(255,202,40,.1);border:1px solid rgba(255,202,40,.2);color:#ffca28;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;margin:.15rem}
.section-title{font-size:2rem;font-weight:900;color:#fff;margin-bottom:.5rem}
.ai-banner{background:linear-gradient(135deg,rgba(108,99,255,.2),rgba(0,212,170,.1));border:1px solid rgba(108,99,255,.3);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:2.5rem;display:flex;align-items:center;gap:1rem}
</style>

<div class="hotels-page">
<div class="hotels-inner">

    {{-- Hero Strip --}}
    <div class="hero-strip">
        <div>
            <div style="font-size:.8rem;color:#b0c4de;margin-bottom:.25rem">Post-Booking Hotel Discovery</div>
            <div style="font-size:1.5rem;font-weight:900;color:#fff">🏨 Hotels Near {{ $destination?->name ?? 'Your Destination' }}</div>
            <div style="color:#b0c4de;font-size:.88rem;margin-top:.3rem">
                {{ $checkIn->format('d M') }} – {{ $checkOut->format('d M Y') }} · {{ $nights }} Night{{ $nights > 1 ? 's':'' }} · Booking <span style="color:#ffca28">{{ $booking->booking_reference }}</span>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap">
            <span class="badge-pill"><i class="fas fa-robot"></i> AI Recommended</span>
            <span class="badge-pill"><i class="fas fa-shield-alt"></i> Secure Booking</span>
        </div>
    </div>

    {{-- AI Banner --}}
    <div class="ai-banner">
        <div style="width:44px;height:44px;border-radius:50%;background:rgba(108,99,255,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem">🤖</div>
        <div>
            <div style="color:#fff;font-weight:700;margin-bottom:.2rem">AI Smart Recommendations</div>
            <div style="color:#b0c4de;font-size:.85rem">Based on your trip budget of ₹{{ number_format($booking->total_amount) }}, travel dates, and destination — we've curated the best hotels for you, ranked by value, ratings, and proximity.</div>
        </div>
    </div>

    {{-- Section header --}}
    <div style="margin-bottom:2rem">
        <div class="section-title">{{ $hotels->count() }} Hotels Found</div>
        <p style="color:#b0c4de">Sorted by AI recommendation score · All prices per night</p>
    </div>

    {{-- Hotel Grid --}}
    <div class="hotel-grid">
        @foreach($hotels as $hotel)
        <div class="hotel-card">
            {{-- Image --}}
            <div class="hotel-img">
                <img src="{{ $hotel->image_url ?? $hotel->image ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80' }}" alt="{{ $hotel->name }}">
                <div class="rec-badge">
                    @if(($hotel->recommendation_score ?? 0) >= 90) 🏆 Top Pick
                    @elseif(($hotel->recommendation_score ?? 0) >= 80) ⭐ Highly Rated
                    @else 💎 Great Value
                    @endif
                </div>
                <div class="price-tag">₹{{ number_format($hotel->price_per_night ?? 3000) }}<span style="font-size:.7rem;font-weight:400">/night</span></div>
            </div>

            {{-- Body --}}
            <div style="padding:1.5rem">
                {{-- Stars + Rating --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
                    <div class="stars">
                        @for($i=0;$i<($hotel->star_rating??4);$i++)★@endfor
                    </div>
                    <div style="display:flex;align-items:center;gap:.4rem;font-size:.85rem;color:#ffca28;font-weight:700">
                        <i class="fas fa-star"></i> {{ $hotel->avg_rating ?? '4.5' }}
                        <span style="color:#b0c4de;font-weight:400">({{ number_format($hotel->review_count ?? 200) }})</span>
                    </div>
                </div>

                <h3 style="color:#fff;font-size:1.1rem;font-weight:800;margin-bottom:.25rem">{{ $hotel->name }}</h3>
                <div style="color:#b0c4de;font-size:.8rem;margin-bottom:.75rem;display:flex;align-items:center;gap:.4rem">
                    <i class="fas fa-map-marker-alt" style="color:#6c63ff"></i>
                    {{ $hotel->address ?? ($destination?->name ?? 'Near destination') }}
                    @if($hotel->distance_km ?? false)
                    <span style="margin-left:.5rem;background:rgba(0,212,170,.1);color:#00d4aa;padding:.1rem .5rem;border-radius:6px;font-size:.72rem">{{ $hotel->distance_km }} km</span>
                    @endif
                </div>

                <p style="color:#9bb3cc;font-size:.82rem;line-height:1.6;margin-bottom:1rem">
                    {{ \Illuminate\Support\Str::limit($hotel->description ?? 'A premium hotel offering excellent comfort and services.', 100) }}
                </p>

                {{-- Amenities --}}
                @if(!empty($hotel->amenities))
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem">
                    @foreach(array_slice(is_array($hotel->amenities) ? $hotel->amenities : [], 0, 5) as $am)
                    <span class="amenity-tag"><i class="fas fa-check" style="font-size:.6rem"></i> {{ $am }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Highlights --}}
                @if(!empty($hotel->highlights))
                <div style="margin-bottom:1rem">
                    @foreach(array_slice(is_array($hotel->highlights) ? $hotel->highlights : [], 0, 3) as $h)
                    <span class="highlight-chip"><i class="fas fa-check-circle"></i> {{ $h }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Price + Nights Total --}}
                <div style="background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.15);border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center">
                    <div style="font-size:.8rem;color:#b0c4de">{{ $nights }} night{{ $nights>1?'s':'' }} total</div>
                    <div style="font-size:1.1rem;font-weight:900;color:#00d4aa">₹{{ number_format(($hotel->price_per_night ?? 3000) * $nights) }}</div>
                </div>

                {{-- Book Button --}}
                @if(isset($hotel->is_smart) && $hotel->is_smart)
                    {{-- Smart hotel: no DB record, go to a dummy book page with query params --}}
                    <a href="{{ route('hotels.recommend', $booking) }}#" class="book-btn"
                       onclick="alert('Smart hotel discovery! In production, this would create a hotel record and proceed to booking. This demo shows the full UI flow.'); return false;">
                        <i class="fas fa-hotel"></i> Select This Hotel
                    </a>
                @else
                    <a href="{{ route('hotels.show', $hotel) }}?check_in={{ $checkIn->format('Y-m-d') }}&check_out={{ $checkOut->format('Y-m-d') }}&booking_id={{ $booking->id }}" class="book-btn">
                        <i class="fas fa-hotel"></i> View Rooms & Book
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bottom CTA --}}
    <div style="text-align:center;margin-top:4rem;padding:3rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:20px">
        <div style="font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:.5rem">Can't find the right hotel?</div>
        <p style="color:#b0c4de;margin-bottom:1.5rem">Skip hotel booking and go straight to planning your trip itinerary</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('itineraries.create') }}" class="book-btn" style="width:auto;padding:.85rem 2rem">
                <i class="fas fa-wand-magic-sparkles"></i> Plan My Itinerary
            </a>
            <a href="{{ route('dashboard') }}" style="padding:.85rem 2rem;border-radius:12px;border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;font-weight:600">
                <i class="fas fa-gauge-high"></i> Dashboard
            </a>
        </div>
    </div>

</div>
</div>
@endsection
