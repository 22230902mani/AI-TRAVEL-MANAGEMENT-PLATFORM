@extends('layouts.app')
@section('title','AI Travel Packages — TravelMate')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

.pkg-hero {
    background: linear-gradient(135deg, #0a0b1a 0%, #0f0f2e 50%, #0a1628 100%);
    padding: 5rem 2rem 3rem;
    position: relative;
    overflow: hidden;
}
.pkg-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(124,58,237,0.15), transparent 70%);
    animation: heroOrb1 12s infinite alternate ease-in-out;
}
.pkg-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,111,0,0.1), transparent 70%);
    animation: heroOrb2 10s infinite alternate ease-in-out;
}
@keyframes heroOrb1 { 0% { transform: translate(0,0); } 100% { transform: translate(80px, 40px); } }
@keyframes heroOrb2 { 0% { transform: translate(0,0); } 100% { transform: translate(-60px, -30px); } }

.pkg-hero-inner {
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

/* Filter Bar */
.pkg-filter-bar {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
.pkg-filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
    gap: .75rem;
    align-items: end;
}
.pkg-filter-grid label {
    display: block;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,0.5);
    margin-bottom: .35rem;
    font-family: 'Outfit', sans-serif;
}
.pkg-filter-grid input,
.pkg-filter-grid select {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: .7rem 1rem;
    color: #fff;
    font-family: 'Outfit', sans-serif;
    font-size: .88rem;
    transition: .3s;
}
.pkg-filter-grid input:focus,
.pkg-filter-grid select:focus {
    outline: none;
    border-color: rgba(124,58,237,0.5);
    background: rgba(124,58,237,0.08);
    box-shadow: 0 0 15px rgba(124,58,237,0.15);
}
.pkg-filter-grid select option { background: #111827; color: #fff; }
.filter-btn {
    padding: .7rem 1.5rem;
    background: linear-gradient(135deg, #7c3aed, #2563eb);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
    transition: .3s;
    font-family: 'Outfit';
    box-shadow: 0 4px 15px rgba(124,58,237,0.4);
}
.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124,58,237,0.6);
}

/* Package Cards Grid */
.pkg-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Package Card */
.pkg-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 24px;
    overflow: hidden;
    transition: all .4s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    animation: cardFadeIn .6s ease-out both;
}
.pkg-card:nth-child(1) { animation-delay: .1s; }
.pkg-card:nth-child(2) { animation-delay: .2s; }
.pkg-card:nth-child(3) { animation-delay: .3s; }
.pkg-card:nth-child(4) { animation-delay: .4s; }
.pkg-card:nth-child(5) { animation-delay: .5s; }
.pkg-card:nth-child(6) { animation-delay: .6s; }

@keyframes cardFadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.pkg-card:hover {
    transform: translateY(-8px);
    border-color: rgba(124,58,237,0.3);
    box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(124,58,237,0.1);
}

.pkg-card-img {
    position: relative;
    height: 200px;
    overflow: hidden;
}
.pkg-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .6s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.pkg-card:hover .pkg-card-img img {
    transform: scale(1.08);
}
.pkg-card-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(10,11,26,0.9) 0%, rgba(10,11,26,0.3) 40%, transparent 70%);
}

/* Offer Badge */
.offer-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: .35rem .85rem;
    background: linear-gradient(135deg, #ff6f00, #ff9100);
    color: #fff;
    font-family: 'Outfit';
    font-size: .7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(255,111,0,0.5);
    animation: badgePulse 2s infinite;
}
@keyframes badgePulse {
    0%, 100% { box-shadow: 0 4px 15px rgba(255,111,0,0.5); }
    50% { box-shadow: 0 4px 25px rgba(255,111,0,0.8); }
}

.discount-tag {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 5;
    padding: .3rem .7rem;
    background: rgba(220,38,38,0.9);
    color: #fff;
    font-family: 'Outfit';
    font-size: .72rem;
    font-weight: 700;
    border-radius: 50px;
    backdrop-filter: blur(10px);
}

.type-tag {
    position: absolute;
    bottom: .75rem;
    right: .75rem;
    z-index: 5;
    padding: .25rem .65rem;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(10px);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    border-radius: 50px;
    border: 1px solid rgba(255,255,255,0.15);
    text-transform: uppercase;
    letter-spacing: .05em;
}

.dest-tag {
    position: absolute;
    bottom: .75rem;
    left: .75rem;
    z-index: 5;
    color: #fff;
    font-size: .82rem;
    font-weight: 600;
}

/* Card Body */
.pkg-card-body {
    padding: 1.5rem;
}
.pkg-card-title {
    font-family: 'Outfit';
    font-weight: 800;
    font-size: 1.05rem;
    margin-bottom: .5rem;
    color: #fff;
}
.pkg-card-desc {
    color: rgba(255,255,255,0.5);
    font-size: .82rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.pkg-meta {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.pkg-meta-pill {
    padding: .25rem .65rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .03em;
}

.pkg-highlights {
    margin-bottom: 1rem;
}
.pkg-highlight-item {
    font-size: .78rem;
    color: rgba(255,255,255,0.55);
    margin-bottom: .25rem;
}
.pkg-highlight-item i {
    color: #00c853;
    width: 14px;
    margin-right: .3rem;
    font-size: .7rem;
}

/* Offer Text */
.offer-text-bar {
    background: linear-gradient(135deg, rgba(255,111,0,0.1), rgba(255,145,0,0.05));
    border: 1px solid rgba(255,111,0,0.2);
    border-radius: 10px;
    padding: .6rem .85rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .78rem;
    color: #ffca28;
    font-weight: 600;
}
.offer-text-bar i { color: #ff9100; }

/* Price & CTA */
.pkg-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.pkg-price-old {
    font-size: .75rem;
    color: rgba(255,255,255,0.4);
    text-decoration: line-through;
}
.pkg-price {
    font-family: 'Outfit';
    font-size: 1.3rem;
    font-weight: 900;
    color: #00c853;
}
.pkg-price span {
    font-size: .72rem;
    font-weight: 400;
    color: rgba(255,255,255,0.4);
}
.pkg-cta {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .6rem 1.3rem;
    background: linear-gradient(135deg, #7c3aed, #2563eb);
    color: #fff;
    border-radius: 50px;
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
    transition: all .3s;
    box-shadow: 0 4px 15px rgba(124,58,237,0.3);
    font-family: 'Outfit';
}
.pkg-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124,58,237,0.5);
}

/* Featured badge */
.featured-glow {
    border-color: rgba(255,215,0,0.2) !important;
    box-shadow: 0 0 20px rgba(255,215,0,0.05);
}
.featured-star {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 6;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #ffd700, #ff9100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    color: #000;
    box-shadow: 0 4px 12px rgba(255,215,0,0.4);
}

/* Empty State */
.pkg-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 5rem 2rem;
    color: rgba(255,255,255,0.4);
}

@media (max-width: 1024px) {
    .pkg-grid { grid-template-columns: repeat(2, 1fr); }
    .pkg-filter-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 640px) {
    .pkg-grid { grid-template-columns: 1fr; padding: 0 1rem; }
    .pkg-filter-grid { grid-template-columns: 1fr; }
}
</style>

{{-- Hero --}}
<div class="pkg-hero">
    <div class="pkg-hero-inner">
        <div class="section-tag" style="margin-bottom:.75rem">📦 AI-Curated Packages</div>
        <h1 style="font-family:'Outfit',serif;font-size:2.8rem;font-weight:900;margin-bottom:.5rem;color:#fff">
            Smart Travel <span style="background:linear-gradient(135deg,#a855f7,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Packages</span>
        </h1>
        <p style="color:rgba(255,255,255,0.6);font-size:1.05rem;max-width:600px;margin-bottom:2rem">
            {{ $packages->total() }} curated packages with AI-powered pricing, local travel guides & Razorpay-secured bookings.
        </p>

        {{-- Filter --}}
        <form method="GET" action="{{ route('packages.index') }}" class="pkg-filter-bar">
            <div class="pkg-filter-grid">
                <div>
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Package name or destination...">
                </div>
                <div>
                    <label><i class="fas fa-tag"></i> Type</label>
                    <select name="type">
                        <option value="">All Types</option>
                        @foreach(['adventure','cultural','beach','mountain','city','wildlife','cruise','honeymoon','family','budget','luxury'] as $t)
                        <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label><i class="fas fa-rupee-sign"></i> Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="e.g. 50000">
                </div>
                <div>
                    <label><i class="fas fa-calendar"></i> Max Days</label>
                    <input type="number" name="duration" value="{{ request('duration') }}" placeholder="e.g. 10">
                </div>
                <div>
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select name="sort">
                        <option value="">Featured</option>
                        <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Price ↑</option>
                        <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price ↓</option>
                        <option value="discount" {{ request('sort')=='discount'?'selected':'' }}>Best Discount</option>
                    </select>
                </div>
                <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Package Grid --}}
<section style="padding:3rem 0 5rem;background:#060713;">
    <div class="pkg-grid">
        @forelse($packages as $pkg)
        <div class="pkg-card {{ $pkg->is_featured ? 'featured-glow' : '' }}">
            {{-- Image --}}
            <div class="pkg-card-img">
                <img src="{{ $pkg->image ? $pkg->image_url : ($pkg->destination?->image_url ?? $pkg->image_url) }}" alt="{{ $pkg->title }}" loading="lazy">

                @if($pkg->is_featured && !$pkg->offer_badge)
                <div class="featured-star"><i class="fas fa-star"></i></div>
                @endif

                @if($pkg->offer_badge)
                <div class="offer-badge"><i class="fas fa-fire"></i> {{ $pkg->offer_badge }}</div>
                @endif

                @if($pkg->discount_percent > 0)
                <div class="discount-tag">-{{ $pkg->discount_percent }}% OFF</div>
                @endif

                <div class="type-tag">{{ ucfirst($pkg->package_type) }}</div>
                <div class="dest-tag">
                    <i class="fas fa-location-dot"></i> {{ $pkg->destination->name ?? 'N/A' }}, {{ $pkg->destination->country ?? '' }}
                </div>
            </div>

            {{-- Body --}}
            <div class="pkg-card-body">
                <div class="pkg-card-title">{{ $pkg->title }}</div>
                <div class="pkg-card-desc">{{ Str::limit($pkg->description, 90) }}</div>

                {{-- Meta Pills --}}
                <div class="pkg-meta">
                    <span class="pkg-meta-pill" style="background:rgba(124,58,237,.15);color:#c4b5fd;border:1px solid rgba(124,58,237,.3)">
                        <i class="fas fa-clock"></i> {{ $pkg->duration_days }} Days
                    </span>
                    <span class="pkg-meta-pill" style="background:rgba(0,200,83,.1);color:#6ee7b7;border:1px solid rgba(0,200,83,.3)">
                        <i class="fas fa-users"></i> Max {{ $pkg->max_group_size ?? '∞' }}
                    </span>
                    @if($pkg->difficulty_level)
                    <span class="pkg-meta-pill" style="background:rgba(255,215,0,.1);color:#fde68a;border:1px solid rgba(255,215,0,.3)">
                        {{ ucfirst($pkg->difficulty_level) }}
                    </span>
                    @endif
                </div>

                {{-- Highlights --}}
                @if($pkg->highlights)
                <div class="pkg-highlights">
                    @foreach(array_slice($pkg->highlights, 0, 2) as $hl)
                    <div class="pkg-highlight-item"><i class="fas fa-check"></i> {{ $hl }}</div>
                    @endforeach
                </div>
                @endif

                {{-- Offer Text --}}
                @if($pkg->offer_text)
                <div class="offer-text-bar">
                    <i class="fas fa-bolt"></i>
                    <span>{{ $pkg->offer_text }}</span>
                    @if($pkg->offer_expires_at)
                    <span style="margin-left:auto;font-size:.7rem;color:rgba(255,255,255,0.4)">
                        Ends {{ $pkg->offer_expires_at->format('M d') }}
                    </span>
                    @endif
                </div>
                @endif

                {{-- Price & CTA --}}
                <div class="pkg-card-footer">
                    <div>
                        @if($pkg->original_price > $pkg->price_per_person)
                        <div class="pkg-price-old">₹{{ number_format($pkg->original_price) }}</div>
                        @endif
                        <div class="pkg-price">₹{{ number_format($pkg->discounted_price) }} <span>/person</span></div>
                    </div>
                    <a href="{{ route('packages.show', $pkg) }}" class="pkg-cta">
                        <i class="fas fa-arrow-right"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="pkg-empty">
            <i class="fas fa-suitcase" style="font-size:3.5rem;display:block;margin-bottom:1.25rem;opacity:.2"></i>
            <h3 style="font-size:1.3rem;margin-bottom:.5rem;color:rgba(255,255,255,0.6)">No Packages Found</h3>
            <p style="font-size:.9rem">Try adjusting your filters or <a href="{{ route('packages.index') }}" style="color:#a855f7;font-weight:600">clear all filters</a>.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div style="max-width:1400px;margin:2.5rem auto 0;padding:0 2rem">{{ $packages->links() }}</div>

    @guest
    {{-- Premium Gated CTA Banner --}}
    <div style="max-width:1400px;margin:4rem auto 0;padding:0 2rem;">
        <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01)); backdrop-filter: blur(20px); border: 1px solid rgba(255, 111, 0, 0.3); border-radius: 28px; padding: 3.5rem 3rem; text-align: center; position: relative; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 30px rgba(255, 111, 0, 0.1);">
            <div style="position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 111, 0, 0.15) 0%, transparent 70%); pointer-events: none;"></div>
            
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(255,111,0,0.2), rgba(255,202,40,0.1)); border: 1px solid rgba(255,202,40,0.4); margin-bottom: 1.5rem; font-size: 2.8rem; color: #ffca28; animation: pulseGlow 2s infinite alternate;">
                <i class="fas fa-lock"></i>
            </div>

            <h2 style="font-family:'Outfit'; font-size: 2.4rem; font-weight: 900; color: #fff; margin-bottom: 1rem; letter-spacing: -0.01em;">
                Want to View 50+ Premium AI Travel Packages?
            </h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem; max-width: 680px; margin: 0 auto 2.5rem; line-height: 1.6;">
                You are currently previewing limited guest packages. Log in or create a free account to unlock exclusive international itineraries, real-time price drops, and member-only hotel booking discounts!
            </p>

            <div style="display: flex; gap: 1.25rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('register') }}" class="btn" style="background: linear-gradient(135deg, #ffca28, #ff6f00); color: #000; font-family:'Outfit'; font-weight: 900; font-size: 1.05rem; padding: 1rem 2.8rem; border-radius: 50px; box-shadow: 0 10px 25px rgba(255,111,0,0.4); transition: all 0.3s; text-decoration: none;">
                    <i class="fas fa-user-plus" style="margin-right: 0.4rem;"></i> Create Free Account
                </a>
                <a href="{{ route('login') }}" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-family:'Outfit'; font-weight: 700; font-size: 1.05rem; padding: 1rem 2.8rem; border-radius: 50px; transition: all 0.3s; text-decoration: none; backdrop-filter: blur(10px);">
                    <i class="fas fa-sign-in-alt" style="margin-right: 0.4rem;"></i> Member Log In
                </a>
            </div>
        </div>
    </div>
    <style>
    @keyframes pulseGlow {
        0% { box-shadow: 0 0 15px rgba(255,111,0,0.2); }
        100% { box-shadow: 0 0 30px rgba(255,111,0,0.6); }
    }
    </style>
    @endguest
</section>
@endsection
