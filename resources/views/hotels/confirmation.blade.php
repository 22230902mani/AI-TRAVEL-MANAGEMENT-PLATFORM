@extends('layouts.app')
@section('title', 'Hotel Booking Confirmed! — TravelMate')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Poppins',sans-serif}
.confirm-page{background:linear-gradient(135deg,#0a0b0f 0%,#0f0f2e 50%,#0a1628 100%);min-height:100vh;padding:3rem 1rem 7rem}
.confirm-inner{max-width:760px;margin:0 auto}
.pulse-ring{width:100px;height:100px;border-radius:50%;background:rgba(0,212,170,.15);border:3px solid #00d4aa;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:3rem;animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(0,212,170,.4)}50%{box-shadow:0 0 0 20px rgba(0,212,170,0)}}
.glass{background:rgba(255,255,255,.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:24px;padding:2.5rem;margin-bottom:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-top:1.5rem}
.info-item .label{font-size:.75rem;color:#b0c4de;text-transform:uppercase;letter-spacing:1px;margin-bottom:.25rem}
.info-item .value{font-weight:700;color:#fff;font-size:.95rem}
.action-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:2rem;padding-bottom:2rem}
.action-btn{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;padding:1.2rem 1rem;border-radius:16px;text-decoration:none;font-size:.85rem;font-weight:600;transition:all .3s;border:1px solid transparent}
.action-btn i{font-size:1.4rem}
.action-btn.primary{background:linear-gradient(135deg,#6c63ff,#00d4aa);color:#fff;box-shadow:0 8px 25px rgba(108,99,255,.35)}
.action-btn.primary:hover{transform:translateY(-3px);color:#fff}
.action-btn.outline{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#fff}
.action-btn.outline:hover{background:rgba(255,255,255,.1);transform:translateY(-3px)}
.qr-block{margin-top:1.5rem;padding:1.25rem;background:rgba(0,0,0,.3);border-radius:14px;border:1px solid rgba(255,255,255,.06)}
@media(max-width:600px){.info-grid{grid-template-columns:1fr 1fr}.action-grid{grid-template-columns:1fr}}
</style>

<div class="confirm-page">
<div class="confirm-inner">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:2.5rem">
        <div class="pulse-ring">🏨</div>
        <h1 style="font-size:2.5rem;font-weight:900;background:linear-gradient(135deg,#00d4aa,#6c63ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.5rem">Hotel Confirmed!</h1>
        <p style="color:#b0c4de;font-size:1rem">Your room is reserved. A digital e-ticket has been generated below.</p>
    </div>

    {{-- Booking Card --}}
    <div class="glass">
        {{-- Hotel header --}}
        @if($booking->hotel)
        <div style="display:flex;gap:1.25rem;align-items:center;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid rgba(255,255,255,.08)">
            <img src="{{ $booking->hotel->image_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80' }}" style="width:80px;height:80px;border-radius:14px;object-fit:cover;flex-shrink:0">
            <div>
                <div style="font-size:1.2rem;font-weight:900;color:#fff">{{ $booking->hotel->name }}</div>
                <div style="color:#b0c4de;font-size:.85rem;margin-top:.25rem">
                    <i class="fas fa-map-marker-alt" style="color:#6c63ff"></i> {{ $booking->hotel->address ?? ($booking->hotel->destination?->name ?? 'Hotel location') }}
                </div>
                <div style="margin-top:.4rem">
                    @for($i=0;$i<($booking->hotel->star_rating??4);$i++)<span style="color:#ffca28">★</span>@endfor
                </div>
            </div>
        </div>
        @endif

        {{-- Reference --}}
        <div style="text-align:center;margin-bottom:1.5rem">
            <div style="font-size:.8rem;color:#b0c4de;text-transform:uppercase;letter-spacing:2px">Booking Reference</div>
            <div style="font-size:2rem;font-weight:900;letter-spacing:.1em;color:#00d4aa">{{ $booking->booking_reference }}</div>
        </div>

        {{-- Info Grid --}}
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Room Type</div>
                <div class="value">{{ $booking->traveler_details['room_type'] ?? 'Standard Room' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Guests</div>
                <div class="value">{{ $booking->adults }} Adult{{ $booking->adults>1?'s':'' }}{{ $booking->children ? ', '.$booking->children.' Child':'' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Check-in</div>
                <div class="value">{{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('d M Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Check-out</div>
                <div class="value">{{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('d M Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Duration</div>
                <div class="value">{{ $booking->traveler_details['nights'] ?? 1 }} Night(s)</div>
            </div>
            <div class="info-item">
                <div class="label">Payment</div>
                <div class="value" style="color:#00d4aa">✅ Paid</div>
            </div>
            <div class="info-item">
                <div class="label">Total Paid</div>
                <div class="value" style="font-size:1.2rem;color:#ffca28">₹{{ number_format($booking->total_amount) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status</div>
                <div class="value" style="color:#00d4aa">Confirmed ✓</div>
            </div>
        </div>

        {{-- QR Code --}}
        @if($booking->qr_code)
        <div class="qr-block">
            <div style="font-size:.78rem;color:#00d4aa;margin-bottom:.5rem;font-weight:600"><i class="fas fa-qrcode"></i> Hotel E-Ticket QR (SHA-256 Signed)</div>
            <div style="font-family:monospace;font-size:.68rem;color:#6c63ff;word-break:break-all;line-height:1.6">{{ $booking->qr_code }}</div>
            <div style="margin-top:.75rem;font-size:.75rem;color:#b0c4de"><i class="fas fa-check-circle" style="color:#00d4aa"></i> Cryptographically signed · Present at hotel reception</div>
        </div>
        @endif

        {{-- Check-in/out timings --}}
        <div style="margin-top:1.5rem;background:rgba(255,202,40,.08);border:1px solid rgba(255,202,40,.2);border-radius:12px;padding:1rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div style="text-align:center">
                <div style="font-size:.75rem;color:#b0c4de">Check-in Time</div>
                <div style="font-size:1.5rem;font-weight:900;color:#ffca28">2:00 PM</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:.75rem;color:#b0c4de">Check-out Time</div>
                <div style="font-size:1.5rem;font-weight:900;color:#ffca28">12:00 PM</div>
            </div>
        </div>

        {{-- Hotel Contact --}}
        @if($booking->hotel && ($booking->hotel->contact_phone || $booking->hotel->contact_email))
        <div style="margin-top:1.25rem;padding:1rem;background:rgba(108,99,255,.08);border-radius:12px;display:flex;gap:2rem;flex-wrap:wrap">
            @if($booking->hotel->contact_phone)
            <div style="font-size:.85rem;color:#a29cf4"><i class="fas fa-phone"></i> {{ $booking->hotel->contact_phone }}</div>
            @endif
            @if($booking->hotel->contact_email)
            <div style="font-size:.85rem;color:#a29cf4"><i class="fas fa-envelope"></i> {{ $booking->hotel->contact_email }}</div>
            @endif
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="action-grid">
        <a href="{{ route('itineraries.create') }}" class="action-btn primary">
            <i class="fas fa-wand-magic-sparkles"></i>
            Plan Itinerary
        </a>
        <a href="{{ route('bookings.index') }}" class="action-btn outline">
            <i class="fas fa-ticket"></i>
            All Bookings
        </a>
        <a href="{{ route('dashboard') }}" class="action-btn outline">
            <i class="fas fa-gauge-high"></i>
            Dashboard
        </a>
    </div>

</div>
</div>
@endsection
