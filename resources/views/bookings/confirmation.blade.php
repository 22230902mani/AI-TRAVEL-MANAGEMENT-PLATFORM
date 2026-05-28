@extends('layouts.app')
@section('title', 'Booking Confirmed! — TravelMate')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

.confirm-page {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0a0b0f 0%, #0f0f2e 50%, #0a1628 100%);
    min-height: 100vh;
    padding: 4rem 1rem 6rem;
}
.confirm-inner { max-width: 700px; margin: 0 auto; }

/* Confetti burst animation */
.confetti-wrap { position: relative; text-align: center; margin-bottom: 2rem; }
.success-ring {
    width: 100px; height: 100px; border-radius: 50%;
    background: rgba(0,212,170,0.15);
    border: 3px solid #00d4aa;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 3rem;
    animation: pulse-ring 2s ease infinite;
}
@keyframes pulse-ring {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0,212,170,0.4); }
    50%       { box-shadow: 0 0 0 20px rgba(0,212,170,0); }
}

.confirm-title {
    font-size: 2.5rem; font-weight: 900; color: #fff;
    margin-bottom: .5rem;
    background: linear-gradient(135deg, #00d4aa, #6c63ff);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}

/* Booking card */
.booking-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(0,212,170,0.25);
    border-radius: 24px; padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 40px rgba(0,212,170,0.05);
}
.ref-label { font-size: .8rem; color: #b0c4de; text-transform: uppercase; letter-spacing: 2px; margin-bottom: .25rem; }
.ref-number { font-size: 2rem; font-weight: 900; letter-spacing: .1em; color: #00d4aa; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 2rem; }
.info-item .label { font-size: .75rem; color: #b0c4de; text-transform: uppercase; letter-spacing: 1px; margin-bottom: .25rem; }
.info-item .value { font-weight: 700; color: #fff; font-size: .95rem; }

/* QR code block */
.qr-block {
    margin-top: 2rem; padding: 1.25rem;
    background: rgba(0,0,0,0.3); border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.06);
}
.qr-hash { font-family: monospace; font-size: .68rem; color: #6c63ff; word-break: break-all; line-height: 1.6; }

/* Action buttons */
.action-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin-top: 2rem;
    padding-bottom: 2rem;
}
.action-btn {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .5rem; padding: 1.2rem 1rem;
    border-radius: 16px; text-decoration: none; font-size: .85rem; font-weight: 600;
    transition: all .3s ease; border: 1px solid transparent;
}
.action-btn i { font-size: 1.4rem; }
.action-btn.primary {
    background: linear-gradient(135deg, #6c63ff, #00d4aa);
    color: #fff;
    box-shadow: 0 8px 25px rgba(108,99,255,0.35);
}
.action-btn.primary:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(108,99,255,0.5); }
.action-btn.outline {
    background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: #fff;
}
.action-btn.outline:hover { background: rgba(255,255,255,0.1); transform: translateY(-3px); }

@media(max-width:600px) {
    .info-grid { grid-template-columns: 1fr 1fr; }
    .action-grid { grid-template-columns: 1fr; }
    .confirm-title { font-size: 1.8rem; }
}
</style>

<div class="confirm-page">
<div class="confirm-inner">

    {{-- Header --}}
    <div class="confetti-wrap">
        <div class="success-ring">✅</div>
        <h1 class="confirm-title">Booking Confirmed!</h1>
        <p style="color:#b0c4de;font-size:1rem">Your adventure is officially booked. Check your email for the e-ticket.</p>
    </div>

    {{-- Booking Card --}}
    <div class="booking-card">
        <div style="text-align:center;margin-bottom:1.5rem">
            <div class="ref-label">Booking Reference</div>
            <div class="ref-number">{{ $booking->booking_reference }}</div>
        </div>

        {{-- Info Grid --}}
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Package</div>
                <div class="value">{{ $booking->package?->title ?? 'Travel Booking' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Destination</div>
                <div class="value">{{ $booking->package?->destination?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Travel Date</div>
                <div class="value">{{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('d M Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Travelers</div>
                <div class="value">{{ $booking->adults }} Adult{{ $booking->adults > 1 ? 's' : '' }}{{ $booking->children ? ', '.$booking->children.' Child' : '' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Payment Status</div>
                <div class="value" style="color:#00d4aa">✅ Paid</div>
            </div>
            <div class="info-item">
                <div class="label">Total Paid</div>
                <div class="value" style="font-size:1.2rem;color:#ffca28">₹{{ number_format($booking->total_amount) }}</div>
            </div>
        </div>

        {{-- QR Code --}}
        @if($booking->qr_code)
        <div class="qr-block">
            <div style="font-size:.78rem;color:#00d4aa;margin-bottom:.5rem;font-weight:600">
                <i class="fas fa-qrcode"></i> PKI-Signed QR Code (SHA-256)
            </div>
            <div class="qr-hash">{{ $booking->qr_code }}</div>
            <div style="margin-top:.75rem;font-size:.75rem;color:#b0c4de">
                <i class="fas fa-check-circle" style="color:#00d4aa"></i> Cryptographically signed · Tamper-proof e-ticket
            </div>
        </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="action-grid">
        <a href="{{ route('hotels.recommend', $booking) }}" class="action-btn primary">
            <i class="fas fa-hotel"></i>
            Find Hotels
        </a>
        <a href="{{ route('itineraries.create', ['booking_id' => $booking->id, 'destination' => $booking->package?->destination?->name, 'start_date' => $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d') : '', 'duration_days' => $booking->package?->duration_days]) }}" class="action-btn outline" style="border-color:#00d4aa; color:#00d4aa; background:rgba(0, 212, 170, 0.1);">
            <i class="fas fa-wand-magic-sparkles"></i>
            Get Free Trip Plan
        </a>
        <a href="{{ route('bookings.index') }}" class="action-btn outline">
            <i class="fas fa-ticket"></i>
            My Bookings
        </a>
    </div>

</div>
</div>

@endsection
