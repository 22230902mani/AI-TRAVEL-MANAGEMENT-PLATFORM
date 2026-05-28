@extends('layouts.app')
@section('title','My Bookings')
@section('content')
<div style="background:linear-gradient(135deg,#0f0f2e,#0a1628);padding:4rem 2rem 2rem">
    <div style="max-width:1400px;margin:0 auto">
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900">My Bookings</h1>
        <p style="color:var(--muted)">{{ $bookings->total() }} total bookings</p>
    </div>
</div>
<section class="section" style="padding-top:2rem">
    <div class="section-inner">
        @php
            $premiumPlans = $bookings->filter(fn($b) => $b->booking_type === 'itinerary');
            $tripBookings = $bookings->filter(fn($b) => $b->booking_type !== 'itinerary');
        @endphp

        {{-- Premium Plans Section --}}
        <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:800;margin-bottom:1.5rem;color:var(--gold)"><i class="fas fa-crown"></i> Premium Itinerary Plans</h2>
        @forelse($premiumPlans as $booking)
        <div class="card" style="padding:1.5rem;margin-bottom:1rem;display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap;border-left:4px solid var(--gold)">
            <div style="flex:1">
                <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin-bottom:.25rem">
                    <span style="font-weight:700">{{ $booking->booking_reference }}</span>
                    <span class="badge-pill {{ $booking->booking_status=='confirmed'?'badge-success':($booking->booking_status=='cancelled'?'badge-danger':'badge-warning') }}" style="font-size:.75rem">{{ ucfirst($booking->booking_status) }}</span>
                    <span class="badge-pill badge-primary" style="font-size:.75rem">Premium Plan</span>
                </div>
                <div style="font-weight:600;margin-bottom:.2rem">{{ $booking->itinerary?->title ?? 'Custom Itinerary Booking' }}</div>
                <div style="font-size:.82rem;color:var(--muted)">
                    📅 {{ $booking->created_at?->format('M d, Y') }}
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:1.25rem;font-weight:800;color:var(--secondary)">₹{{ number_format($booking->total_amount) }}</div>
                <div style="font-size:.78rem;color:var(--muted);margin-bottom:.75rem">{{ ucfirst($booking->payment_status) }}</div>
                <div style="display:flex;gap:.5rem;justify-content:flex-end">
                    @if($booking->itinerary_id)
                    <a href="{{ route('itineraries.show', $booking->itinerary_id) }}" class="btn btn-outline btn-sm" style="border-color:var(--gold);color:var(--gold)"><i class="fas fa-map"></i> View Plan</a>
                    @endif
                    <a href="{{ route('bookings.show',$booking) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
                    @if($booking->booking_status==='confirmed')
                    <a href="{{ route('bookings.confirmation',$booking) }}" class="btn btn-primary btn-sm"><i class="fas fa-qrcode"></i> Receipt</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:var(--muted);background:rgba(255,255,255,0.02);border-radius:12px;border:1px dashed rgba(255,255,255,0.1);margin-bottom:2rem">
            No premium itinerary plans yet.
        </div>
        @endforelse

        {{-- Packages & Destinations Section --}}
        <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:800;margin:2.5rem 0 1.5rem;color:var(--primary)"><i class="fas fa-suitcase-rolling"></i> Packages & Destinations</h2>
        @forelse($tripBookings as $booking)
        <div class="card" style="padding:1.5rem;margin-bottom:1rem;display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap">
            @if($booking->package?->destination)
            <img src="{{ $booking->package->destination->image_url }}" style="width:100px;height:70px;object-fit:cover;border-radius:10px;flex-shrink:0">
            @endif
            <div style="flex:1">
                <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin-bottom:.25rem">
                    <span style="font-weight:700">{{ $booking->booking_reference }}</span>
                    <span class="badge-pill {{ $booking->booking_status=='confirmed'?'badge-success':($booking->booking_status=='cancelled'?'badge-danger':'badge-warning') }}" style="font-size:.75rem">{{ ucfirst($booking->booking_status) }}</span>
                    <span class="badge-pill badge-primary" style="font-size:.75rem">{{ ucfirst($booking->booking_type) }}</span>
                </div>
                <div style="font-weight:600;margin-bottom:.2rem">{{ $booking->package?->title ?? $booking->hotel?->name ?? 'Custom Booking' }}</div>
                <div style="font-size:.82rem;color:var(--muted)">
                    📍 {{ $booking->package?->destination?->name ?? $booking->hotel?->destination?->name ?? 'N/A' }}
                    &nbsp;•&nbsp; 📅 {{ $booking->check_in?->format('M d, Y') }}
                    &nbsp;•&nbsp; 👥 {{ $booking->adults }} adults{{ $booking->children ? ', '.$booking->children.' children' : '' }}
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:1.25rem;font-weight:800;color:var(--secondary)">₹{{ number_format($booking->total_amount) }}</div>
                <div style="font-size:.78rem;color:var(--muted);margin-bottom:.75rem">{{ ucfirst($booking->payment_status) }}</div>
                <div style="display:flex;gap:.5rem;justify-content:flex-end">
                    <a href="{{ route('itineraries.create') }}{{ $booking->package?->destination_id ? '?destination='.$booking->package->destination_id : '' }}" class="btn btn-outline btn-sm" style="border-color:#a855f7;color:#a855f7"><i class="fas fa-wand-magic-sparkles"></i> Free AI Trip Planner</a>
                    <a href="{{ route('bookings.show',$booking) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
                    @if($booking->booking_status==='confirmed')
                    <a href="{{ route('bookings.confirmation',$booking) }}" class="btn btn-primary btn-sm"><i class="fas fa-qrcode"></i> Ticket</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:4rem;color:var(--muted)">
            <i class="fas fa-ticket" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:.3"></i>
            No package or destination bookings yet. <a href="{{ route('packages.index') }}" style="color:var(--primary)">Browse packages</a>
        </div>
        @endforelse

        <div style="margin-top:2rem">{{ $bookings->links() }}</div>
    </div>
</section>
@endsection
