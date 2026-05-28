@extends('layouts.app')
@section('title','Guide Dashboard')
@section('content')

<style>
.guide-hero {
    background: linear-gradient(135deg, #0a0b1a 0%, #0d1f3c 50%, #0a1628 100%);
    padding: 3.5rem 2rem 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.guide-stat-card {
    background: linear-gradient(145deg, rgba(20,22,50,0.8), rgba(10,15,35,0.9));
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    padding: 1.5rem 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform .3s, box-shadow .3s;
    position: relative;
    overflow: hidden;
}
.guide-stat-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,0,0,0.4); }
.guide-stat-card::after {
    content: '';
    position: absolute;
    bottom: -25px; right: -25px;
    width: 90px; height: 90px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}
.gsc-icon {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.booking-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1rem;
    border-radius: 12px;
    transition: background .2s;
    gap: 1rem;
    flex-wrap: wrap;
}
.booking-row:hover { background: rgba(255,255,255,0.04); }
.upcoming-card {
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.07);
    border-left: 4px solid #14b8a6;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: .75rem;
    transition: .3s;
}
.upcoming-card:hover { background: rgba(20,184,166,0.07); }
.status-pill {
    display: inline-block;
    padding: .25rem .75rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
}
.status-confirmed { background: rgba(20,184,166,0.15); color: #14b8a6; }
.status-pending   { background: rgba(245,158,11,0.15);  color: #f59e0b; }
.status-completed { background: rgba(99,102,241,0.15);  color: #818cf8; }
.status-cancelled { background: rgba(239,68,68,0.15);   color: #ef4444; }
</style>

{{-- GUIDE HERO HEADER --}}
<div class="guide-hero">
    <div style="max-width:1400px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem">
        <div style="display:flex;align-items:center;gap:1.25rem">
            <img src="{{ $guide->avatar_url }}" alt="avatar"
                 style="width:72px;height:72px;border-radius:50%;border:3px solid #14b8a6;box-shadow:0 0 20px rgba(20,184,166,0.4)">
            <div>
                <div style="font-size:.82rem;color:rgba(255,255,255,0.5)">Welcome back, Guide</div>
                <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:900;color:#fff">{{ $guide->name }}</h1>
                <div style="display:flex;align-items:center;gap:.75rem;margin-top:.25rem;flex-wrap:wrap">
                    @if($guide->guide_specialty)
                    <span style="background:rgba(20,184,166,0.12);color:#14b8a6;font-size:.75rem;font-weight:700;padding:.2rem .75rem;border-radius:50px;border:1px solid rgba(20,184,166,0.25)">
                        <i class="fas fa-compass"></i> {{ $guide->guide_specialty }}
                    </span>
                    @endif
                    @if($guide->guide_experience)
                    <span style="background:rgba(99,102,241,0.12);color:#818cf8;font-size:.75rem;font-weight:700;padding:.2rem .75rem;border-radius:50px;border:1px solid rgba(99,102,241,0.25)">
                        <i class="fas fa-star"></i> {{ $guide->guide_experience }} yrs exp
                    </span>
                    @endif
                    <span style="background:rgba(16,185,129,0.1);color:#10b981;font-size:.75rem;font-weight:700;padding:.2rem .75rem;border-radius:50px;border:1px solid rgba(16,185,129,0.25)">
                        <i class="fas fa-shield-check"></i> Verified Guide
                    </span>
                </div>
            </div>
        </div>
        <div></div>
    </div>
</div>

<section class="section" style="padding-top:2rem">
    <div class="section-inner">

        {{-- STATS ROW --}}
        <div class="grid-4" style="margin-bottom:2rem">
            @php $statCards = [
                ['label'=>'Total Assigned','val'=>$stats['total_assigned'],'icon'=>'fa-briefcase','bg'=>'linear-gradient(135deg,#1e3a8a,#1d4ed8)','ibg'=>'rgba(59,130,246,0.2)','ic'=>'#93c5fd'],
                ['label'=>'Upcoming Trips','val'=>$stats['upcoming_count'],'icon'=>'fa-plane-departure','bg'=>'linear-gradient(135deg,#134e4a,#0d9488)','ibg'=>'rgba(20,184,166,0.2)','ic'=>'#5eead4'],
                ['label'=>'Confirmed','val'=>$stats['confirmed_trips'],'icon'=>'fa-circle-check','bg'=>'linear-gradient(135deg,#3b0764,#7c3aed)','ibg'=>'rgba(167,139,250,0.2)','ic'=>'#c4b5fd'],
                ['label'=>'Completed','val'=>$stats['completed_trips'],'icon'=>'fa-flag-checkered','bg'=>'linear-gradient(135deg,#7f1d1d,#b91c1c)','ibg'=>'rgba(252,165,165,0.2)','ic'=>'#fca5a5'],
            ]; @endphp
            @foreach($statCards as $c)
            <div class="guide-stat-card" style="background:{{ $c['bg'] }};border-color:rgba(255,255,255,0.1);">
                <div>
                    <div style="font-size:.7rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,0.55);margin-bottom:.5rem">{{ $c['label'] }}</div>
                    <div style="font-size:2.2rem;font-weight:900;color:#fff;font-family:'Playfair Display',serif;line-height:1">{{ $c['val'] }}</div>
                </div>
                <div class="gsc-icon" style="background:{{ $c['ibg'] }}">
                    <i class="fas {{ $c['icon'] }}" style="color:{{ $c['ic'] }}"></i>
                </div>
            </div>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
            {{-- ALL ASSIGNED BOOKINGS --}}
            <div>
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                        <h2 style="font-weight:700"><i class="fas fa-ticket" style="color:var(--primary)"></i> Assigned Bookings</h2>
                        <a href="{{ route('guide.assigned-bookings') }}" class="btn btn-primary btn-sm" style="box-shadow:none;border-radius:10px;padding:.4rem 1rem;font-size:.78rem">
                            <i class="fas fa-list-check" style="margin-right:4px"></i> Client Manifest
                        </a>
                    </div>
                    @forelse($assignedBookings as $b)
                    <a href="{{ route('guide.assigned-bookings') }}#booking-{{ $b->id }}" style="text-decoration:none;color:inherit;display:block">
                        <div class="booking-row" style="margin-bottom:.5rem">
                            <div style="flex:1;min-width:180px">
                                <div style="font-weight:600;font-size:.9rem;color:#fff">{{ $b->booking_reference ?? 'BK-'.$b->id }}</div>
                                <div style="font-size:.78rem;color:var(--muted);margin-top:.2rem">
                                    <i class="fas fa-map-marker-alt" style="color:#14b8a6"></i>
                                    {{ $b->package?->destination?->name ?? 'Custom Booking' }}
                                    @if($b->check_in) • {{ \Carbon\Carbon::parse($b->check_in)->format('M d, Y') }} @endif
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:.25rem">
                                <div style="font-weight:700;color:var(--secondary);font-size:.9rem">₹{{ number_format($b->total_amount) }}</div>
                                <div style="display:flex;align-items:center;gap:.5rem">
                                    <span class="status-pill status-{{ $b->booking_status }}" style="font-size:.68rem;padding:.15rem .5rem">{{ ucfirst($b->booking_status) }}</span>
                                    <span style="font-size:.75rem;color:rgba(20,184,166,0.85);font-weight:600"><i class="fas fa-chevron-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div style="text-align:center;padding:3rem 1rem;color:var(--muted)">
                        <i class="fas fa-inbox" style="font-size:2.5rem;opacity:.3;margin-bottom:1rem;display:block"></i>
                        No bookings assigned yet. Check back soon!
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div>
                {{-- UPCOMING TRIPS --}}
                <div class="card" style="padding:1.5rem;margin-bottom:1.25rem">
                    <h3 style="font-weight:700;font-size:1.05rem;margin:0 0 1.1rem 0">
                        <i class="fas fa-calendar-check" style="color:#14b8a6;margin-right:8px"></i> Upcoming Trips
                    </h3>
                    @forelse($upcomingTrips as $t)
                    <div class="upcoming-card">
                        <div style="font-weight:600;font-size:.88rem">{{ $t->package?->destination?->name ?? 'Trip' }}</div>
                        <div style="font-size:.77rem;color:var(--muted);margin-top:.3rem">
                            <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($t->check_in)->format('M d, Y') }}
                        </div>
                        <div style="font-size:.77rem;color:var(--muted);margin-top:.15rem">
                            Ref: {{ $t->booking_reference ?? 'N/A' }}
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem 1rem;color:var(--muted);font-size:.85rem">
                        <i class="fas fa-calendar-xmark" style="font-size:2rem;opacity:.25;margin-bottom:.75rem;display:block"></i>
                        No upcoming trips scheduled.
                    </div>
                    @endforelse
                </div>

                {{-- GUIDE INFO CARD --}}
                <div class="card" style="padding:1.5rem;background:linear-gradient(135deg,rgba(20,184,166,0.06),rgba(99,102,241,0.06));border-color:rgba(20,184,166,0.2)">
                    <h3 style="font-weight:700;font-size:1rem;margin:0 0 1rem 0">
                        <i class="fas fa-id-badge" style="color:#14b8a6;margin-right:8px"></i> Guide Profile
                    </h3>
                    <div style="display:flex;flex-direction:column;gap:.7rem;font-size:.85rem">
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Name</span>
                            <span style="font-weight:600">{{ $guide->name }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Email</span>
                            <span style="font-weight:600;font-size:.78rem">{{ $guide->email }}</span>
                        </div>
                        @if($guide->guide_phone)
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Phone</span>
                            <span style="font-weight:600">{{ $guide->guide_phone }}</span>
                        </div>
                        @endif
                        @if($guide->guide_specialty)
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Specialty</span>
                            <span style="font-weight:600">{{ $guide->guide_specialty }}</span>
                        </div>
                        @endif
                        @if($guide->guide_experience)
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Experience</span>
                            <span style="font-weight:600">{{ $guide->guide_experience }} years</span>
                        </div>
                        @endif
                        <div style="border-top:1px solid var(--border);padding-top:.7rem;display:flex;justify-content:space-between">
                            <span style="color:var(--muted)">Status</span>
                            <span class="status-pill status-confirmed">✓ Approved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
