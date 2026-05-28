@extends('layouts.app')
@section('title','Premium Destinations')
@section('content')

{{-- Import Poppins Font --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

.poppins { font-family: 'Poppins', sans-serif !important; }

/* Advanced Styles & Animations */
.hero-gradient {
    background: linear-gradient(135deg, rgba(10,11,15,0.85) 0%, rgba(15,15,46,0.6) 50%, rgba(10,22,40,0.85) 100%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
}
.glow-on-hover:hover {
    box-shadow: 0 0 20px rgba(0, 212, 170, 0.4), 0 0 40px rgba(108, 99, 255, 0.2);
    border-color: rgba(0, 212, 170, 0.5);
}
.dest-card {
    border-radius: 24px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: var(--surface);
    border: 1px solid var(--border);
}
.dest-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}
.dest-card:hover .dest-img {
    transform: scale(1.1);
}
.dest-img {
    transition: transform 0.6s ease;
}
.btn-glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    transition: all 0.3s ease;
}
.btn-glass:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}
.fade-up {
    animation: fadeUp 0.8s ease forwards;
    opacity: 0;
    transform: translateY(30px);
}
@keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
}
.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }

.stat-glow {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>

{{-- HERO SECTION --}}
<section style="position:relative;height:70vh;min-height:500px;display:flex;align-items:center;justify-content:center;overflow:hidden">
    <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&q=80&w=2000" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;animation: zoomOut 20s infinite alternate" alt="Cinematic Travel">
    <div class="hero-gradient" style="position:absolute;inset:0;z-index:1"></div>
    
    <div style="position:relative;z-index:2;text-align:center;padding:0 2rem;max-width:1000px" class="fade-up">
        <span class="poppins" style="display:inline-block;padding:0.5rem 1.5rem;border-radius:50px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);color:#ffca28;font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1.5rem;border:1px solid rgba(255,202,40,0.3)">
            <i class="fas fa-plane-departure"></i> TravelMate Exclusive
        </span>
        <h1 class="poppins" style="font-size:clamp(2.5rem, 5vw, 4.5rem);font-weight:900;color:#fff;line-height:1.2;margin-bottom:1.5rem;text-shadow:0 10px 30px rgba(0,0,0,0.5)">
            Explore Amazing <span style="background:linear-gradient(135deg, #00d4aa, #6c63ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Destinations</span> Around the World
        </h1>
        <p class="poppins" style="font-size:1.1rem;color:rgba(255,255,255,0.8);max-width:700px;margin:0 auto;line-height:1.8">
            Discover breathtaking beaches, majestic mountains, thrilling adventures, and vibrant cultural wonders curated by our enterprise AI.
        </p>
    </div>
</section>

{{-- DARK THEME WRAPPER FOR CONTENT --}}
<div style="background: linear-gradient(135deg, #0a0b0f 0%, #0f0f2e 50%, #0a1628 100%); padding-bottom: 5rem;">
    <div class="section-inner poppins" style="max-width:1400px;margin:0 auto;position:relative;z-index:10; transform: translateY(-60px);">
        
        {{-- ADVANCED SEARCH & FILTER PANEL (Glassmorphism) --}}
    <form method="GET" action="{{ route('destinations.index') }}" class="glass-panel fade-up stagger-1" style="border-radius:24px;padding:2rem;margin-bottom:3rem">
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1.5rem;align-items:end">
            
            <div style="position:relative">
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-search" style="color:var(--primary)"></i> Destination</label>
                <input type="text" id="dest-search" name="search" value="{{ request('search') }}" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins'" placeholder="Search city or place...">
            </div>
            
            <div>
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-layer-group" style="color:var(--secondary)"></i> Category</label>
                <select name="category" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins';appearance:none">
                    <option value="" style="background:#0a1628">All Categories</option>
                    <option value="beaches" style="background:#0a1628">🏖️ Beaches</option>
                    <option value="mountains" style="background:#0a1628">⛰️ Mountains</option>
                    <option value="adventure" style="background:#0a1628">🧗 Adventure</option>
                    <option value="historical" style="background:#0a1628">🏛️ Historical</option>
                    <option value="luxury" style="background:#0a1628">💎 Luxury</option>
                    <option value="nature" style="background:#0a1628">🌿 Nature</option>
                    <option value="cultural" style="background:#0a1628">🎭 Cultural</option>
                    @foreach($categories as $cat)
                        @if(!in_array(strtolower($cat), ['beaches','mountains','adventure','historical','luxury','nature','cultural']))
                            <option value="{{ $cat }}" style="background:#0a1628" {{ request('category')==$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-globe-americas" style="color:var(--accent)"></i> Country</label>
                <select name="country" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins';appearance:none">
                    <option value="" style="background:#0a1628">All Countries</option>
                    @foreach($countries as $c)<option value="{{ $c }}" style="background:#0a1628" {{ request('country')==$c?'selected':'' }}>{{ $c }}</option>@endforeach
                </select>
            </div>
            
            <div>
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-sun" style="color:#ffca28"></i> Season</label>
                <select name="season" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins';appearance:none">
                    <option value="" style="background:#0a1628">Any Season</option>
                    <option value="Summer" style="background:#0a1628" {{ request('season')=='Summer'?'selected':'' }}>Summer</option>
                    <option value="Winter" style="background:#0a1628" {{ request('season')=='Winter'?'selected':'' }}>Winter</option>
                    <option value="Monsoon" style="background:#0a1628" {{ request('season')=='Monsoon'?'selected':'' }}>Monsoon</option>
                    <option value="Spring" style="background:#0a1628" {{ request('season')=='Spring'?'selected':'' }}>Spring</option>
                </select>
            </div>

            <div>
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-wallet" style="color:#00d4aa"></i> Max Budget (₹)</label>
                <input type="number" name="budget_max" value="{{ request('budget_max') }}" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins'" placeholder="e.g. 50000">
            </div>

            <div>
                <label style="display:block;font-size:0.85rem;color:#b0c4de;margin-bottom:0.5rem;font-weight:600"><i class="fas fa-sort-amount-down" style="color:#ffca28"></i> Sort By</label>
                <select name="sort" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.9rem 1.2rem;border-radius:12px;outline:none;font-family:'Poppins';appearance:none">
                    <option value="" style="background:#0a1628">Latest</option>
                    <option value="popular" style="background:#0a1628" {{ request('sort')=='popular'?'selected':'' }}>Most Popular</option>
                    <option value="rating" style="background:#0a1628" {{ request('sort')=='rating'?'selected':'' }}>Top Rated</option>
                    <option value="budget" style="background:#0a1628" {{ request('sort')=='budget'?'selected':'' }}>Budget Friendly</option>
                    <option value="trending" style="background:#0a1628" {{ request('sort')=='trending'?'selected':'' }}>Trending</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="height:52px;border-radius:12px;font-family:'Poppins';font-weight:700;font-size:1rem;display:flex;align-items:center;justify-content:center;gap:0.5rem;box-shadow:0 10px 20px rgba(108,99,255,0.3);transition:0.3s">
                <i class="fas fa-sliders-h"></i> Apply Filters
            </button>
        </div>
    </form>

    {{-- STATS SECTION --}}
    <div class="grid-4 fade-up stagger-2" style="margin-bottom:4rem;gap:2rem">
        @foreach([['500+','Destinations','fa-map-marked-alt'],['10K+','Happy Travelers','fa-user-check'],['2K+','Luxury Hotels','fa-hotel'],['4.9','User Ratings','fa-star']] as [$val,$lbl,$icon])
        <div class="glass-panel glow-on-hover" style="padding:2rem 1.5rem;border-radius:20px;text-align:center;transition:0.4s">
            <div style="font-size:2.5rem;margin-bottom:1rem;color:var(--primary)"><i class="fas {{ $icon }}"></i></div>
            <div class="stat-glow" style="font-size:2.5rem;font-weight:900;margin-bottom:0.5rem;line-height:1">{{ $val }}</div>
            <div style="color:#b0c4de;font-weight:500;text-transform:uppercase;letter-spacing:1px;font-size:0.85rem">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>

    {{-- RECOMMENDED SECTION --}}
    @if(isset($recommended) && $recommended->count() > 0)
    <div style="margin-bottom:5rem" class="fade-up stagger-3">
        <h2 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:0.5rem;display:flex;align-items:center;gap:1rem">
            @auth
                <i class="fas fa-magic" style="color:var(--primary)"></i> Recommended For You
            @else
                <i class="fas fa-fire" style="color:#ff6b6b"></i> Trending Destinations
            @endauth
        </h2>
        <p style="color:#b0c4de;margin-bottom:2rem">Intelligently curated based on {{ auth()->check() ? 'your preferences and seasonal trends' : 'global popularity and search frequency' }}.</p>
        
        <div class="grid-3" style="gap:2.5rem">
            @foreach($recommended as $dest)
                <a href="{{ route('destinations.show', $dest) }}" class="dest-card" style="display:block;text-decoration:none;position:relative;height:250px;border-radius:24px;overflow:hidden">
                    <img src="{{ $dest->image_url ?? asset('images/placeholder.png') }}" alt="{{ $dest->name }}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease" class="dest-img">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(10,11,15,0.9) 0%, transparent 50%)"></div>
                    <div style="position:absolute;top:1rem;left:1rem;background:rgba(255,202,40,0.9);color:#000;font-weight:800;font-size:0.75rem;padding:0.3rem 0.8rem;border-radius:50px;text-transform:uppercase">
                        <i class="fas fa-star"></i> {{ $dest->avg_rating }}
                    </div>
                    <div style="position:absolute;bottom:1.5rem;left:1.5rem;right:1.5rem">
                        <h3 style="color:#fff;font-size:1.5rem;font-weight:800;margin-bottom:0.3rem">{{ $dest->name }}</h3>
                        <div style="color:#b0c4de;font-size:0.9rem;display:flex;align-items:center;gap:0.5rem">
                            <i class="fas fa-map-marker-alt" style="color:var(--secondary)"></i> {{ $dest->country }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DESTINATIONS GRID --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem" class="fade-up stagger-3">
        <div>
            <h2 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:0.5rem">Featured Discoveries</h2>
            <p style="color:#b0c4de">Unveil the most breathtaking locations curated just for you.</p>
        </div>
        <div style="display:flex;gap:1rem">
            <button class="btn-glass" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-chevron-left"></i></button>
            <button class="btn-glass" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <div class="grid-3" style="gap:2.5rem;margin-bottom:4rem">
        @forelse($destinations as $dest)
        <div class="dest-card fade-up stagger-4" style="position:relative;display:flex;flex-direction:column">
            
            {{-- Image Header --}}
            <div style="position:relative;height:260px;overflow:hidden">
                <img src="{{ $dest->image_url ?? asset('images/placeholder.png') }}" alt="{{ $dest->name }}" class="dest-img" style="width:100%;height:100%;object-fit:cover">
                <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(10,11,15,1) 0%, transparent 60%)"></div>
                
                {{-- Top Badges --}}
                <div style="position:absolute;top:1rem;left:1rem;display:flex;gap:0.5rem">
                    <span style="background:rgba(108,99,255,0.8);backdrop-filter:blur(4px);color:#fff;padding:0.3rem 0.8rem;border-radius:50px;font-size:0.75rem;font-weight:700;text-transform:uppercase">
                        {{ $dest->category ?? 'Destination' }}
                    </span>
                </div>
                <div style="position:absolute;top:1rem;right:1rem">
                    @auth
                    <button class="wishlist-btn" data-type="destination" data-id="{{ $dest->id }}" onclick="toggleWishlist(this)" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.2);width:36px;height:36px;border-radius:50%;color:{{ auth()->user()->wishlists()->where('destination_id', $dest->id)->exists() ? '#ff6b6b' : '#fff' }};cursor:pointer;display:flex;align-items:center;justify-content:center;transition:0.3s">
                        <i class="fas fa-heart"></i>
                    </button>
                    @endauth
                </div>

                {{-- Title & Flag --}}
                <div style="position:absolute;bottom:1rem;left:1.5rem;right:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:flex-end">
                        <div>
                            <h3 style="color:#fff;font-size:1.4rem;font-weight:800;margin-bottom:0.2rem;line-height:1.2">{{ $dest->name }}</h3>
                            <div style="color:#b0c4de;font-size:0.85rem;display:flex;align-items:center;gap:0.4rem">
                                <i class="fas fa-map-marker-alt" style="color:var(--secondary)"></i> {{ $dest->country }}
                            </div>
                        </div>
                        <div style="background:rgba(255,202,40,0.2);border:1px solid rgba(255,202,40,0.4);padding:0.3rem 0.6rem;border-radius:8px;display:flex;align-items:center;gap:0.3rem;color:#ffca28;font-weight:700;font-size:0.85rem">
                            <i class="fas fa-star"></i> {{ $dest->avg_rating }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding:1.5rem;flex-grow:1;display:flex;flex-direction:column">
                <p style="color:#9bb3cc;font-size:0.85rem;line-height:1.6;margin-bottom:1.5rem;flex-grow:1">
                    {{ Str::limit($dest->description, 110) }}
                </p>

                {{-- Info Grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid rgba(255,255,255,0.05)">
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,212,170,0.1);display:flex;align-items:center;justify-content:center;color:var(--secondary)">
                            <i class="fas fa-cloud-sun"></i>
                        </div>
                        <div>
                            <div style="font-size:0.7rem;color:#b0c4de;text-transform:uppercase">Live Weather</div>
                            <div style="font-weight:700;font-size:0.9rem;color:#fff" class="live-weather" data-lat="{{ rand(-90,90) }}" data-lon="{{ rand(-180,180) }}">24°C Sunny</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(108,99,255,0.1);display:flex;align-items:center;justify-content:center;color:var(--primary)">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div style="font-size:0.7rem;color:#b0c4de;text-transform:uppercase">Est. Budget</div>
                            <div style="font-weight:700;font-size:0.9rem;color:#fff">₹{{ number_format($dest->base_price_economy ?? 15000) }}<span style="font-size:0.7rem;font-weight:400;color:#9bb3cc">/day</span></div>
                        </div>
                    </div>
                </div>

                {{-- Popularity & Breakdown Insights --}}
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                    <div style="font-size:0.8rem;color:#b0c4de;display:flex;align-items:center;gap:0.4rem;">
                        <i class="fas fa-fire" style="color:#ff6b6b"></i> Pop Score: <strong style="color:#fff">{{ number_format($dest->popularity_score ?? rand(100,1000)) }}</strong>
                    </div>
                    <div style="font-size:0.8rem;color:#00d4aa;cursor:pointer;display:flex;align-items:center;gap:0.4rem;" title="Accom.: ₹{{ number_format($dest->budget_breakdown['accommodation'] ?? 0) }} | Trans.: ₹{{ number_format($dest->budget_breakdown['transport'] ?? 0) }} | Food: ₹{{ number_format($dest->budget_breakdown['food'] ?? 0) }}">
                        <i class="fas fa-info-circle"></i> Budget Insights
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:0.5rem">
                    <a href="{{ route('destinations.show', $dest) }}" class="btn btn-primary" style="border-radius:10px;font-size:0.9rem;padding:0.6rem;display:flex;justify-content:center;align-items:center;font-weight:600">
                        Explore
                    </a>
                    <a href="{{ route('itineraries.create') }}?destination={{ urlencode($dest->name) }}" class="btn btn-outline" style="border-radius:10px;padding:0.6rem 1rem;background:rgba(255,202,40,0.1);border-color:rgba(255,202,40,0.3);color:#ffca28;display:flex;justify-content:center;align-items:center;font-weight:600">
                        <i class="fas fa-calendar-check" style="margin-right:0.3rem"></i> Plan Trip
                    </a>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem">
                    <a href="https://maps.google.com/maps?q={{ urlencode($dest->name . ' ' . $dest->country) }}" target="_blank" class="btn btn-outline" style="border-radius:10px;padding:0.6rem 1rem;border-color:rgba(255,255,255,0.2);display:flex;justify-content:center;align-items:center;font-size:0.85rem">
                        <i class="fas fa-map-marked-alt" style="margin-right:0.3rem"></i> View Map
                    </a>
                    <a href="{{ route('destinations.book', $dest) }}" class="btn btn-outline" style="border-radius:10px;padding:0.6rem 1rem;background:rgba(0,212,170,0.1);border-color:rgba(0,212,170,0.3);color:#00d4aa;display:flex;justify-content:center;align-items:center;font-size:0.85rem">
                        <i class="fas fa-ticket-alt" style="margin-right:0.3rem"></i> Book Now
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:5rem 2rem;background:rgba(255,255,255,0.02);border-radius:24px;border:1px dashed rgba(255,255,255,0.1)">
            <i class="fas fa-globe" style="font-size:4rem;color:rgba(255,255,255,0.1);margin-bottom:1.5rem;display:block"></i>
            <h3 style="font-size:1.5rem;font-weight:700;margin-bottom:0.5rem">No Destinations Found</h3>
            <p style="color:#b0c4de;margin-bottom:1.5rem">Try adjusting your advanced filters to find more amazing places.</p>
            <a href="{{ route('destinations.index') }}" class="btn btn-primary">Clear All Filters</a>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div style="display:flex;justify-content:center;margin-bottom:5rem">
        {{ $destinations->links() }}
    </div>

    {{-- TESTIMONIALS SECTION --}}
    <div class="glass-panel fade-up" style="border-radius:24px;padding:4rem 2rem;margin-bottom:4rem;text-align:center">
        <div class="section-tag" style="margin:0 auto 1rem">⭐ Traveler Stories</div>
        <h2 style="font-size:2.5rem;font-weight:800;color:#fff;margin-bottom:3rem">What Adventurers Are Saying</h2>
        
        <div class="grid-3" style="gap:2rem;text-align:left">
            @foreach([
                ['name'=>'Sarah Jenkins','img'=>'https://randomuser.me/api/portraits/women/44.jpg','loc'=>'Bali, Indonesia','text'=>'The AI planner perfectly curated our luxury retreat. The weather API predicted a storm, allowing us to seamlessly re-book our spa day!'],
                ['name'=>'David Chen','img'=>'https://randomuser.me/api/portraits/men/32.jpg','loc'=>'Swiss Alps','text'=>'TravelMate found us an unbelievable cabin in the mountains. The budget estimator was 99% accurate to our actual spend.'],
                ['name'=>'Elena Rodriguez','img'=>'https://randomuser.me/api/portraits/women/68.jpg','loc'=>'Kyoto, Japan','text'=>'Absolutely magical experience. The verified reviews gave me confidence, and the real-time Google Maps integration made navigating Kyoto a breeze.']
            ] as $review)
            <div style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.05);padding:2rem;border-radius:16px;position:relative">
                <i class="fas fa-quote-right" style="position:absolute;top:1.5rem;right:1.5rem;font-size:2rem;color:rgba(255,255,255,0.05)"></i>
                <div style="display:flex;gap:0.3rem;color:#ffca28;font-size:0.9rem;margin-bottom:1rem">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color:#b0c4de;font-size:0.95rem;line-height:1.7;margin-bottom:1.5rem;font-style:italic">"{{ $review['text'] }}"</p>
                <div style="display:flex;align-items:center;gap:1rem">
                    <img src="{{ $review['img'] }}" alt="User" style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid var(--primary)">
                    <div>
                        <div style="font-weight:700;color:#fff;font-size:0.95rem">{{ $review['name'] }}</div>
                        <div style="font-size:0.8rem;color:var(--secondary)">Traveled to {{ $review['loc'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    </div>
</div>

<script>
// Mock Real-Time Weather Fetcher for UI interactivity
document.addEventListener('DOMContentLoaded', () => {
    const weatherElements = document.querySelectorAll('.live-weather');
    const conditions = ['☀️ Sunny', '⛅ Partly Cloudy', '🌧️ Light Rain', '🌩️ Thunderstorms', '☁️ Overcast'];
    
    weatherElements.forEach(el => {
        // Randomize weather for demo purposes
        const temp = Math.floor(Math.random() * 15) + 15; // 15 to 30 C
        const cond = conditions[Math.floor(Math.random() * conditions.length)];
        el.textContent = `${temp}°C ${cond}`;
    });
});

async function toggleWishlist(btn) {
    const res = await fetch('{{ route("wishlist.toggle") }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ type: btn.dataset.type, id: btn.dataset.id })
    });
    const data = await res.json();
    btn.style.color = data.wishlisted ? '#ff6b6b' : '#fff';
}
</script>
@endsection
