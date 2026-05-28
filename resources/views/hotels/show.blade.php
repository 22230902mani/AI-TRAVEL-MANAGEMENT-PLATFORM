@extends('layouts.app')
@section('title', $hotel->name . ' — TravelMate Hotels')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Poppins',sans-serif}
.hotel-detail{background:linear-gradient(135deg,#0a0b0f 0%,#0f0f2e 50%,#0a1628 100%);min-height:100vh;padding-bottom:6rem}
.hotel-hero{position:relative;height:420px;overflow:hidden}
.hotel-hero img{width:100%;height:100%;object-fit:cover}
.hotel-hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(10,11,15,1) 0%,rgba(10,11,15,.3) 60%,transparent 100%)}
.hotel-hero-content{position:absolute;bottom:2rem;left:2rem;right:2rem}
.inner{max-width:1200px;margin:0 auto;padding:0 1.5rem}
.detail-grid{display:grid;grid-template-columns:1fr 380px;gap:2.5rem;padding-top:3rem;align-items:start}
.glass-card{background:rgba(255,255,255,.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:2rem}
.room-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:1.5rem;margin-bottom:1rem;transition:all .3s;cursor:pointer}
.room-card:hover,.room-card.selected{border-color:#6c63ff;background:rgba(108,99,255,.1)}
.amenity-chip{display:inline-flex;align-items:center;gap:.4rem;background:rgba(108,99,255,.12);border:1px solid rgba(108,99,255,.2);color:#a29cf4;padding:.3rem .8rem;border-radius:8px;font-size:.78rem;margin:.2rem}
.book-btn{display:block;width:100%;padding:1rem;border:none;border-radius:14px;font-family:'Poppins',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;background:linear-gradient(135deg,#6c63ff,#00d4aa);color:#fff;transition:all .3s;text-align:center;text-decoration:none}
.book-btn:hover{transform:translateY(-2px);box-shadow:0 10px 25px rgba(108,99,255,.4);color:#fff}
@media(max-width:900px){.detail-grid{grid-template-columns:1fr}}
</style>

<div class="hotel-detail">

{{-- Hero --}}
<div class="hotel-hero">
    <img src="{{ $hotel->image_url ?? $hotel->image ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80' }}" alt="{{ $hotel->name }}">
    <div class="hotel-hero-overlay"></div>
    <div class="hotel-hero-content">
        <div style="font-size:.8rem;color:#b0c4de;margin-bottom:.4rem">
            @for($i=0;$i<($hotel->star_rating??4);$i++)<span style="color:#ffca28">★</span>@endfor
        </div>
        <h1 style="font-size:2.5rem;font-weight:900;color:#fff;margin-bottom:.5rem">{{ $hotel->name }}</h1>
        <div style="color:#b0c4de;display:flex;align-items:center;gap:.5rem;font-size:.9rem">
            <i class="fas fa-map-marker-alt" style="color:#6c63ff"></i>
            {{ $hotel->address ?? ($hotel->destination?->name ?? 'Great location') }}
        </div>
    </div>
</div>

<div class="inner">
<div class="detail-grid">

    {{-- LEFT --}}
    <div>
        {{-- About --}}
        <div class="glass-card" style="margin-bottom:1.5rem">
            <h2 style="font-size:1.2rem;font-weight:800;color:#fff;margin-bottom:1rem"><i class="fas fa-hotel" style="color:#6c63ff"></i> About This Hotel</h2>
            <p style="color:#b0c4de;line-height:1.8;font-size:.9rem">{{ $hotel->description ?? 'A wonderful hotel offering premium comfort, excellent service, and an unforgettable stay experience.' }}</p>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:1.5rem">
                <div style="text-align:center;background:rgba(0,0,0,.2);border-radius:12px;padding:1rem">
                    <div style="font-size:1.5rem;color:#ffca28;font-weight:900">{{ $hotel->avg_rating ?? '4.5' }}</div>
                    <div style="font-size:.75rem;color:#b0c4de">Rating</div>
                </div>
                <div style="text-align:center;background:rgba(0,0,0,.2);border-radius:12px;padding:1rem">
                    <div style="font-size:1.5rem;color:#00d4aa;font-weight:900">{{ $hotel->star_rating ?? 4 }}★</div>
                    <div style="font-size:.75rem;color:#b0c4de">Star Hotel</div>
                </div>
                <div style="text-align:center;background:rgba(0,0,0,.2);border-radius:12px;padding:1rem">
                    <div style="font-size:1.5rem;color:#6c63ff;font-weight:900">{{ $hotel->review_count ?? '200' }}+</div>
                    <div style="font-size:.75rem;color:#b0c4de">Reviews</div>
                </div>
            </div>
        </div>

        {{-- Amenities --}}
        @if(!empty($hotel->amenities))
        <div class="glass-card" style="margin-bottom:1.5rem">
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1rem"><i class="fas fa-concierge-bell" style="color:#6c63ff"></i> Amenities</h2>
            <div>
                @foreach(is_array($hotel->amenities) ? $hotel->amenities : [] as $am)
                <span class="amenity-chip"><i class="fas fa-check-circle" style="color:#00d4aa"></i> {{ $am }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Room Types --}}
        <div class="glass-card" style="margin-bottom:1.5rem">
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.25rem"><i class="fas fa-bed" style="color:#6c63ff"></i> Available Rooms</h2>
            @foreach($roomTypes as $i => $room)
            <div class="room-card {{ $i===0 ? 'selected' : '' }}" onclick="selectRoom(this, '{{ $room['type'] }}', {{ $room['price'] }})">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:.5rem">
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:1rem">{{ $room['type'] }}</div>
                        <div style="color:#b0c4de;font-size:.8rem">{{ $room['beds'] ?? 1 }} Bed{{ ($room['beds']??1)>1?'s':'' }} · {{ $room['size'] ?? '250 sq ft' }}</div>
                    </div>
                    <div style="text-align:right">
                        <div style="color:#00d4aa;font-weight:900;font-size:1.1rem">₹{{ number_format($room['price']) }}</div>
                        <div style="font-size:.75rem;color:#b0c4de">/night</div>
                    </div>
                </div>
                @if(!empty($room['amenities']))
                <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.5rem">
                    @foreach(array_slice($room['amenities'], 0, 4) as $ra)
                    <span style="font-size:.72rem;color:#a29cf4;background:rgba(108,99,255,.1);padding:.15rem .5rem;border-radius:5px">{{ $ra }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Map --}}
        <div class="glass-card">
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1rem"><i class="fas fa-map-marked-alt" style="color:#6c63ff"></i> Location & Map</h2>
            <div style="border-radius:12px;overflow:hidden;height:200px;background:rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center">
                <iframe
                    width="100%" height="200" style="border:0" loading="lazy" allowfullscreen
                    src="https://maps.google.com/maps?q={{ urlencode($hotel->name . ' ' . ($hotel->destination?->name ?? '')) }}&output=embed">
                </iframe>
            </div>
            <a href="https://maps.google.com/?q={{ urlencode($hotel->name) }}" target="_blank" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:.75rem;color:#6c63ff;font-size:.85rem;text-decoration:none;font-weight:600">
                <i class="fas fa-directions"></i> Open in Google Maps
            </a>
        </div>
    </div>

    {{-- RIGHT: Booking Panel --}}
    <div class="glass-card" style="position:sticky;top:90px">
        <h3 style="color:#fff;font-weight:800;font-size:1.1rem;margin-bottom:1.5rem">
            <i class="fas fa-calendar-check" style="color:#00d4aa"></i> Book Your Stay
        </h3>

        <form action="{{ route('hotels.store', $hotel) }}" method="POST">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $bookingId }}">
            <input type="hidden" name="room_type"  id="selected_room_type"  value="{{ $roomTypes[0]['type'] ?? 'Standard Room' }}">
            <input type="hidden" name="room_price" id="selected_room_price" value="{{ $roomTypes[0]['price'] ?? $hotel->price_per_night }}">

            <div style="margin-bottom:1rem">
                <label style="font-size:.8rem;color:#b0c4de;display:block;margin-bottom:.4rem">Check-in</label>
                <input type="date" name="check_in" value="{{ $checkIn->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
                    style="width:100%;background:rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.15);color:#fff;padding:.75rem 1rem;border-radius:10px;font-family:'Poppins',sans-serif;outline:none"
                    onchange="updateTotal()">
            </div>
            <div style="margin-bottom:1rem">
                <label style="font-size:.8rem;color:#b0c4de;display:block;margin-bottom:.4rem">Check-out</label>
                <input type="date" name="check_out" value="{{ $checkOut->format('Y-m-d') }}" min="{{ $checkIn->addDay()->format('Y-m-d') }}"
                    style="width:100%;background:rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.15);color:#fff;padding:.75rem 1rem;border-radius:10px;font-family:'Poppins',sans-serif;outline:none"
                    onchange="updateTotal()">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem">
                <div>
                    <label style="font-size:.8rem;color:#b0c4de;display:block;margin-bottom:.4rem">Adults</label>
                    <input type="number" name="adults" value="2" min="1" max="10"
                        style="width:100%;background:rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.15);color:#fff;padding:.75rem;border-radius:10px;font-family:'Poppins',sans-serif;outline:none">
                </div>
                <div>
                    <label style="font-size:.8rem;color:#b0c4de;display:block;margin-bottom:.4rem">Children</label>
                    <input type="number" name="children" value="0" min="0" max="6"
                        style="width:100%;background:rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.15);color:#fff;padding:.75rem;border-radius:10px;font-family:'Poppins',sans-serif;outline:none">
                </div>
            </div>

            {{-- Selected Room --}}
            <div style="background:rgba(108,99,255,.1);border:1px solid rgba(108,99,255,.25);border-radius:12px;padding:1rem;margin-bottom:1.25rem">
                <div style="font-size:.78rem;color:#b0c4de;margin-bottom:.4rem">Selected Room</div>
                <div style="color:#fff;font-weight:700" id="display_room_type">{{ $roomTypes[0]['type'] ?? 'Standard Room' }}</div>
                <div style="color:#00d4aa;font-size:.85rem;margin-top:.25rem">₹<span id="display_price">{{ number_format($roomTypes[0]['price'] ?? $hotel->price_per_night) }}</span>/night</div>
            </div>

            {{-- Total --}}
            <div style="background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.2);border-radius:12px;padding:1rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div style="font-size:.78rem;color:#b0c4de">Total for {{ $nights }} nights</div>
                    <div id="total_display" style="font-size:1.4rem;font-weight:900;color:#00d4aa">₹{{ number_format(($roomTypes[0]['price'] ?? $hotel->price_per_night) * $nights) }}</div>
                </div>
                <div style="font-size:.75rem;color:#b0c4de;text-align:right">Taxes<br>included</div>
            </div>

            <button type="submit" class="book-btn">
                <i class="fas fa-lock"></i> Confirm & Pay
            </button>
        </form>

        <div style="margin-top:1rem;font-size:.75rem;color:#b0c4de;text-align:center">
            <i class="fas fa-shield-alt" style="color:#00d4aa"></i> Free cancellation · Secure payment via Razorpay
        </div>
    </div>
</div>
</div>
</div>

<script>
const nights = {{ $nights }};
function selectRoom(el, type, price) {
    document.querySelectorAll('.room-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selected_room_type').value = type;
    document.getElementById('selected_room_price').value = price;
    document.getElementById('display_room_type').textContent = type;
    document.getElementById('display_price').textContent = Math.round(price).toLocaleString('en-IN');
    document.getElementById('total_display').textContent = '₹' + Math.round(price * nights).toLocaleString('en-IN');
}
function updateTotal() {
    const price = parseFloat(document.getElementById('selected_room_price').value || 3000);
    const ci = new Date(document.querySelector('[name=check_in]').value);
    const co = new Date(document.querySelector('[name=check_out]').value);
    const n = Math.max(1, Math.round((co - ci) / (1000*60*60*24)));
    document.getElementById('total_display').textContent = '₹' + Math.round(price * n).toLocaleString('en-IN');
}
</script>
@endsection
