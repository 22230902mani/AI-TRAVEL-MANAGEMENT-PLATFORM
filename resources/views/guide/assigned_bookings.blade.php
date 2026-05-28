@extends('layouts.app')
@section('title','Assigned Bookings — Guide Panel')
@section('content')

<style>
.bookings-hero {
    background: linear-gradient(135deg, #0a0b1a 0%, #0d1f3c 50%, #0a1628 100%);
    padding: 4.5rem 2rem 3.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    text-align: center;
}
.booking-card {
    background: linear-gradient(145deg, rgba(30,35,65,0.85) 0%, rgba(15,20,40,0.95) 100%);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 24px;
    padding: 0;
    margin-bottom: 2.5rem;
    transition: all .4s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(20px);
}
.booking-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.65), 0 0 0 1px rgba(20, 184, 166, 0.4);
}
.booking-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 5px;
    background: linear-gradient(90deg, #14b8a6, #818cf8, #db2777);
}
.client-info-section {
    background: rgba(255,255,255,0.015);
    border-right: 1px solid rgba(255,255,255,0.05);
    padding: 2.5rem 2rem;
}
.booking-details-section {
    padding: 2.5rem 2rem;
}
.avatar-large {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 3px solid #14b8a6;
    box-shadow: 0 0 25px rgba(20,184,166,0.35);
    object-fit: cover;
}
.status-pill {
    display: inline-block;
    padding: .35rem .95rem;
    border-radius: 50px;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
}
.status-confirmed { background: rgba(20,184,166,0.15); color: #14b8a6; border: 1px solid rgba(20,184,166,0.3); }
.status-pending   { background: rgba(245,158,11,0.15);  color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
.status-completed { background: rgba(99,102,241,0.15);  color: #818cf8; border: 1px solid rgba(99,102,241,0.3); }
.status-cancelled { background: rgba(239,68,68,0.15);   color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

.detail-badge {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.9);
    padding: .5rem 1rem;
    border-radius: 12px;
    font-size: .82rem;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
}
.interest-tag {
    background: rgba(20,184,166,0.1);
    border: 1px solid rgba(20,184,166,0.25);
    color: #14b8a6;
    padding: .25rem .65rem;
    border-radius: 6px;
    font-size: .72rem;
    font-weight: 700;
}
.special-request-box {
    background: rgba(245,158,11,0.06);
    border-left: 4px solid #f59e0b;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid rgba(245,158,11,0.1);
    border-bottom: 1px solid rgba(245,158,11,0.1);
    border-right: 1px solid rgba(245,158,11,0.1);
}
.pagination-container .pagination {
    display: flex;
    justify-content: center;
    gap: .5rem;
    list-style: none;
    padding: 0;
    margin-top: 2rem;
}
.pagination-container .page-item .page-link {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    color: #fff;
    padding: .6rem 1rem;
    border-radius: 8px;
    transition: all .2s;
}
.pagination-container .page-item.active .page-link {
    background: #14b8a6;
    border-color: #14b8a6;
    font-weight: bold;
}
.pagination-container .page-item.disabled .page-link {
    opacity: .4;
    cursor: not-allowed;
}
.pagination-container .page-item .page-link:hover:not(.disabled) {
    background: rgba(20,184,166,0.2);
    border-color: #14b8a6;
}
</style>

{{-- HERO HEADER --}}
<div class="bookings-hero">
    <div style="max-width:1400px;margin:0 auto">
        <span class="section-tag"><i class="fas fa-briefcase"></i> Service manifest</span>
        <h1 style="font-family:'Playfair Display',serif;font-size:2.65rem;font-weight:900;color:#fff;margin-top:.5rem">Assigned Client manifest</h1>
        <p style="color:var(--muted);max-width:600px;margin:1rem auto 0;font-size:1.05rem">
            View detailed passenger info, dynamic special requests, trip details, and booking status for all your assigned travelers.
        </p>
    </div>
</div>

<section class="section" style="padding-top:3rem">
    <div class="section-inner" style="max-width:1200px">
        
        @forelse($assignedBookings as $booking)
            @php 
                $client = $booking->user;
                $profile = $client?->profile;
            @endphp
            <div class="booking-card" id="booking-{{ $booking->id }}">
                <div style="display:grid;grid-template-columns:1.2fr 2fr;gap:0;align-items:stretch">
                    
                    {{-- CLIENT / TRAVELER PROFILE SECTION --}}
                    <div class="client-info-section">
                        <div style="text-align:center;margin-bottom:2rem">
                            <img src="{{ $client?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($client?->name ?? 'Guest').'&background=14b8a6&color=fff&size=128' }}" alt="avatar" class="avatar-large">
                            <h3 style="font-size:1.3rem;font-weight:700;color:#fff;margin-top:1rem">{{ $client?->name ?? 'Anonymous Traveler' }}</h3>
                            <span style="font-size:.78rem;color:rgba(255,255,255,0.45);text-transform:uppercase;font-weight:800;letter-spacing:.05em">Traveler Profile</span>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:1.2rem;font-size:.88rem;color:rgba(255,255,255,0.85)">
                            <div style="display:flex;align-items:center;gap:.75rem">
                                <i class="far fa-envelope" style="color:#14b8a6;width:18px;font-size:1.05rem"></i>
                                <span style="word-break:break-all">{{ $client?->email ?? 'N/A' }}</span>
                            </div>
                            
                            <div style="display:flex;align-items:center;gap:.75rem">
                                <i class="fas fa-phone-volume" style="color:#14b8a6;width:18px;font-size:1.05rem"></i>
                                <span>{{ $profile?->phone ?? 'No Phone Number' }}</span>
                            </div>

                            <div style="display:flex;align-items:center;gap:.75rem">
                                <i class="fas fa-globe" style="color:#14b8a6;width:18px;font-size:1.05rem"></i>
                                <span>Nationality: <strong>{{ $profile?->nationality ?? 'Not Specified' }}</strong></span>
                            </div>

                            @if($profile?->preferred_language)
                            <div style="display:flex;align-items:center;gap:.75rem">
                                <i class="far fa-comments" style="color:#14b8a6;width:18px;font-size:1.05rem"></i>
                                <span>Language: <strong>{{ $profile->preferred_language }}</strong></span>
                            </div>
                            @endif

                            {{-- CLIENT INTERESTS --}}
                            @if(!empty($profile?->travel_interests))
                            <div style="margin-top:.5rem">
                                <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,0.4);margin-bottom:.6rem">Travel Interests</div>
                                <div style="display:flex;flex-wrap:wrap;gap:.35rem">
                                    @foreach($profile->travel_interests as $interest)
                                        <span class="interest-tag">{{ $interest }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- TRIP & BOOKING DETAILS SECTION --}}
                    <div class="booking-details-section" style="display:flex;flex-direction:column;justify-content:space-between">
                        <div>
                            {{-- Header details --}}
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
                                <div>
                                    <span style="font-size:.75rem;font-weight:800;color:var(--muted);letter-spacing:1px">REF: {{ $booking->booking_reference ?? 'BK-'.$booking->id }}</span>
                                    <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:900;color:#fff;margin-top:.2rem">
                                        {{ $booking->package?->destination?->name ?? 'Custom Guided Trip' }}
                                    </h2>
                                </div>
                                <div>
                                    <span class="status-pill status-{{ $booking->booking_status }}">
                                        {{ ucfirst($booking->booking_status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Grid stats of booking --}}
                            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;margin-bottom:1.5rem">
                                <div class="detail-badge">
                                    <i class="far fa-calendar-alt" style="color:#14b8a6;font-size:1rem"></i>
                                    <span>Check In: <strong style="color:#fff">{{ $booking->check_in ? $booking->check_in->format('M d, Y') : 'N/A' }}</strong></span>
                                </div>
                                <div class="detail-badge">
                                    <i class="far fa-calendar-check" style="color:#818cf8;font-size:1rem"></i>
                                    <span>Check Out: <strong style="color:#fff">{{ $booking->check_out ? $booking->check_out->format('M d, Y') : 'N/A' }}</strong></span>
                                </div>
                                <div class="detail-badge">
                                    <i class="fas fa-users" style="color:#db2777;font-size:1rem"></i>
                                    <span>Passengers: <strong style="color:#fff">{{ $booking->passenger_summary }}</strong></span>
                                </div>
                                <div class="detail-badge">
                                    <i class="fas fa-wallet" style="color:#10b981;font-size:1rem"></i>
                                    <span>Total Price: <strong style="color:#10b981">₹{{ number_format($booking->total_amount) }}</strong></span>
                                </div>
                            </div>

                            {{-- Special Request alerts --}}
                            @if($booking->special_requests)
                            <div class="special-request-box">
                                <div style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;font-weight:800;color:#f59e0b;margin-bottom:.4rem;letter-spacing:.05em">
                                    <i class="fas fa-triangle-exclamation"></i> SPECIAL INSTRUCTIONS / REQUESTS
                                </div>
                                <p style="font-size:.88rem;color:rgba(255,255,255,0.9);margin:0;line-height:1.6;font-style:italic">
                                    "{{ $booking->special_requests }}"
                                </p>
                            </div>
                            @endif

                            {{-- Admin Guide Briefing --}}
                            <div style="background: rgba(20,184,166,0.06); border-left: 4px solid #14b8a6; border-radius: 12px; padding: 1.25rem 1.5rem; margin-top: 1.25rem; border-top: 1px solid rgba(20,184,166,0.1); border-bottom: 1px solid rgba(20,184,166,0.1); border-right: 1px solid rgba(20,184,166,0.1)">
                                <div style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;font-weight:800;color:#14b8a6;margin-bottom:.4rem;letter-spacing:.05em">
                                    <i class="fas fa-envelope-open-text"></i> IMPORTANT BRIEFING FROM ADMINISTRATION
                                </div>
                                <div style="font-size:.88rem;color:rgba(255,255,255,0.95);margin:0;line-height:1.6">
                                    <ul style="margin:.5rem 0 0 1.2rem; padding:0; font-size:.83rem; color:rgba(255,255,255,0.85); line-height:1.6">
                                        <li><strong>Traveler Details:</strong> {{ $client?->name ?? 'Anonymous' }} ({{ $profile?->phone ?? 'No Phone Provided' }} / {{ $client?->email ?? 'No Email' }})</li>
                                        <li><strong>Core Journey:</strong> {{ $booking->check_in ? $booking->check_in->format('M d, Y') : 'N/A' }} to {{ $booking->check_out ? $booking->check_out->format('M d, Y') : 'N/A' }}</li>
                                        <li><strong>Party Details:</strong> {{ $booking->passenger_summary }}</li>
                                        @if($booking->package_details_shared)
                                        <li style="margin-top: .4rem; padding-top: .4rem; border-top: 1px dashed rgba(255,255,255,0.1); color: #fff">
                                            <strong>Shared Trip Instructions:</strong> {{ $booking->package_details_shared }}
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            {{-- Addons --}}
                            @if(!empty($booking->complimentary_addons))
                            <div style="margin-top:1.5rem">
                                <div style="font-size:.75rem;font-weight:800;color:var(--muted);margin-bottom:.6rem;text-transform:uppercase;letter-spacing:.05em">Included Perks & Add-ons</div>
                                <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                                    @foreach($booking->complimentary_addons as $addon)
                                        <span style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);color:rgba(255,255,255,0.9);font-size:.75rem;padding:.3rem .8rem;border-radius:50px">
                                            <i class="fas fa-circle-check" style="color:#14b8a6;margin-right:4px"></i> {{ $addon }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div style="display:flex;gap:1rem;margin-top:2.5rem;flex-wrap:wrap">
                            <a href="mailto:{{ $client?->email }}" class="btn btn-primary btn-sm" style="box-shadow:none;padding:.7rem 1.5rem">
                                <i class="far fa-paper-plane"></i> Contact Client
                            </a>
                            @if($booking->package)
                            <a href="{{ route('packages.show', $booking->package->id) }}" class="btn btn-outline btn-sm" style="padding:.7rem 1.5rem">
                                <i class="fas fa-compass"></i> View Package Itinerary
                            </a>
                            @endif
                             @if($booking->itinerary_id)
                             <a href="{{ route('itineraries.show', $booking->itinerary_id) }}" class="btn btn-outline btn-sm" style="padding:.7rem 1.5rem;border-color:#14b8a6;color:#14b8a6">
                                 <i class="fas fa-route"></i> View Itinerary Plan
                             </a>
                             @endif
                            <a href="{{ route('guide.manifest-pdf', (string) $booking->id) }}" class="btn btn-outline btn-sm" style="border-color:rgba(255,255,255,0.1);padding:.7rem 1.5rem">
                                <i class="fas fa-file-pdf"></i> Print Manifest
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center;padding:6rem 2rem;color:var(--muted)">
                <i class="fas fa-folder-open" style="font-size:4rem;opacity:.25;margin-bottom:1.5rem;display:block"></i>
                <h3 style="color:#fff;font-weight:700;margin-bottom:.5rem">No Assigned Bookings</h3>
                <p style="font-size:.9rem;max-width:400px;margin:0 auto">You have no travel bookings assigned to you currently. Contact administration if this is an error.</p>
            </div>
        @endforelse

        {{-- PAGINATION --}}
        @if($assignedBookings->hasPages())
        <div class="pagination-container">
            {{ $assignedBookings->links() }}
        </div>
        @endif

    </div>
</section>

@endsection
