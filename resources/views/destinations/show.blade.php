@extends('layouts.app')
@section('title', $destination->name)
@section('content')
{{-- Hero Banner --}}
<div style="height:420px;position:relative;overflow:hidden">
    <img src="{{ $destination->image_url }}" alt="{{ $destination->name }}" style="width:100%;height:100%;object-fit:cover">
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.8) 0%,rgba(0,0,0,.3) 60%,transparent 100%)"></div>
    <div style="position:absolute;bottom:2.5rem;left:2rem;right:2rem;max-width:1400px;margin:0 auto">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div>
                <div style="margin-bottom:.5rem"><span class="badge-pill badge-primary">{{ ucfirst($destination->category) }}</span></div>
                <h1 style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:900;color:#fff">{{ $destination->name }}</h1>
                <div style="color:rgba(255,255,255,.8);font-size:1rem"><i class="fas fa-location-dot"></i> {{ $destination->city }}, {{ $destination->country }}</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.75rem">
                <div style="display:flex;align-items:center;gap:1rem">
                    <div style="text-align:center;background:rgba(255,255,255,.1);backdrop-filter:blur(10px);padding:.75rem 1.25rem;border-radius:12px">
                        <div class="stars" style="font-size:1.2rem">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star" style="color: {{ $i <= round($destination->avg_rating ?? 0) ? 'var(--gold)' : 'rgba(255,255,255,0.3)' }}"></i>
                            @endfor
                        </div>
                        <div style="color:#fff;font-weight:700">{{ number_format($destination->avg_rating ?? 0, 1) }}/5</div>
                        <div style="color:rgba(255,255,255,.6);font-size:.75rem">{{ number_format($destination->review_count ?? 0) }} reviews</div>
                    </div>
                </div>
                @auth
                <button onclick="toggleWishlistDest('{{ $destination->id }}', this)" class="btn btn-outline btn-sm" style="border-color:rgba(255,255,255,.3);color:#fff" id="wish-btn">
                    <i class="fas fa-heart" style="{{ $isWishlisted ? 'color:#ff6b6b' : '' }}"></i> {{ $isWishlisted ? 'Wishlisted' : 'Add to Wishlist' }}
                </button>
                @endauth
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="section-inner">
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:2.5rem;align-items:start">
            {{-- LEFT --}}
            <div>
                {{-- About --}}
                <div class="card" style="padding:2rem;margin-bottom:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-info-circle" style="color:var(--primary)"></i> About {{ $destination->name }}</h2>
                    <p style="color:var(--muted);line-height:1.8">{{ $destination->description }}</p>
                    
                    {{-- Google Maps Embed --}}
                    <div style="margin-top:1.5rem; border-radius:12px; overflow:hidden; border:1px solid var(--border)">
                        <iframe width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q={{ urlencode($destination->name . ' ' . $destination->country) }}&t=&z=13&ie=UTF8&iwloc=&output=embed" style="filter: contrast(1.1) brightness(0.9);"></iframe>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.5rem">
                        @if($destination->climate)<div style="background:var(--surface2);padding:1rem;border-radius:12px">
                            <div style="font-size:.78rem;color:var(--muted)">Climate</div>
                            <div style="font-weight:600">{{ ucfirst($destination->climate) }}</div>
                        </div>@endif
                        @if($destination->best_season)<div style="background:var(--surface2);padding:1rem;border-radius:12px">
                            <div style="font-size:.78rem;color:var(--muted)">Best Season</div>
                            <div style="font-weight:600">{{ $destination->best_season }}</div>
                        </div>@endif
                    </div>
                </div>

                {{-- Aspect Ratings --}}
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <h3 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-chart-bar" style="color:var(--secondary)"></i> Aspect Ratings</h3>
                    @foreach([['Food & Dining',$avgRatings['food'],'fa-utensils','#ff6b6b'],['Cleanliness',$avgRatings['cleanliness'],'fa-broom','#00d4aa'],['Safety',$avgRatings['safety'],'fa-shield','#ffd700'],['Value for Money',$avgRatings['value'],'fa-coins','#6c63ff']] as [$label,$val,$icon,$color])
                    <div style="margin-bottom:1rem">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;font-size:.88rem">
                            <span><i class="fas {{ $icon }}" style="color:{{ $color }}"></i> {{ $label }}</span>
                            <span style="font-weight:600">{{ $val ?? 'N/A' }}/5</span>
                        </div>
                        <div style="height:6px;background:var(--surface2);border-radius:6px"><div style="width:{{ (($val??0)/5)*100 }}%;height:100%;background:{{ $color }};border-radius:6px;transition:.5s"></div></div>
                    </div>
                    @endforeach
                </div>

                {{-- Safety Tips --}}
                @if($destination->safety_tips)
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <h3 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-shield-halved" style="color:var(--gold)"></i> Safety Tips</h3>
                    @foreach($destination->safety_tips as $tip)
                    <div style="display:flex;gap:.75rem;margin-bottom:.5rem;font-size:.88rem">
                        <i class="fas fa-check-circle" style="color:var(--secondary);margin-top:.15rem;flex-shrink:0"></i>
                        <span style="color:var(--muted)">{{ $tip }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Visa Info --}}
                @if($destination->visa_info)
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <h3 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-passport" style="color:var(--primary)"></i> Visa Information</h3>
                    @foreach($destination->visa_info as $vi)
                    <div style="display:flex;gap:.75rem;margin-bottom:.5rem;font-size:.88rem">
                        <i class="fas fa-circle-info" style="color:var(--primary);margin-top:.15rem;flex-shrink:0"></i>
                        <span style="color:var(--muted)">{{ $vi }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Reviews --}}
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                        <h3 style="font-weight:700"><i class="fas fa-star" style="color:var(--gold)"></i> Traveler Reviews</h3>
                        @auth<button onclick="document.getElementById('review-form').scrollIntoView({behavior:'smooth'})" class="btn btn-primary btn-sm">Write Review</button>@endauth
                    </div>
                    @forelse($reviews as $review)
                    <div style="padding:1rem 0;border-bottom:1px solid var(--border)">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.5rem">
                            <div style="display:flex;align-items:center;gap:.75rem">
                                <img src="{{ $review->user->avatar_url }}" style="width:36px;height:36px;border-radius:50%">
                                <div><div style="font-weight:600;font-size:.9rem">{{ $review->user->name }}</div>
                                    <div style="font-size:.75rem;color:var(--muted)">{{ $review->created_at->format('M j, Y') }}</div></div>
                            </div>
                            <div>
                                <div class="stars" style="font-size:.85rem">{{ str_repeat('★',$review->rating) }}</div>
                                @if($review->is_verified)<div style="font-size:.7rem;color:var(--secondary)"><i class="fas fa-shield-check"></i> Verified</div>@endif
                            </div>
                        </div>
                        @if($review->title)<div style="font-weight:600;margin-bottom:.3rem">{{ $review->title }}</div>@endif
                        <p style="color:var(--muted);font-size:.87rem">{{ $review->body }}</p>
                        <button onclick="markHelpful({{ $review->id }},this)" style="margin-top:.5rem;background:none;border:none;color:var(--muted);cursor:pointer;font-size:.78rem">
                            <i class="fas fa-thumbs-up"></i> Helpful (<span id="helpful-{{ $review->id }}">{{ $review->helpful_votes }}</span>)
                        </button>
                    </div>
                    @empty<p style="color:var(--muted)">No reviews yet. Be the first!</p>
                    @endforelse
                    <div style="margin-top:1rem">{{ $reviews->links() }}</div>
                </div>

                {{-- Write Review Form --}}
                @auth
                <div class="card" style="padding:1.5rem" id="review-form">
                    <h3 style="font-weight:700;margin-bottom:1.25rem">Write a Review</h3>
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
                        <input type="hidden" name="destination_id" value="{{ $destination->id }}">
                        <input type="hidden" name="reviewable_type" value="destination">
                        <div class="form-group">
                            <label class="form-label">Overall Rating</label>
                            <div style="display:flex;gap:.5rem" id="rating-stars">
                                @for($i=1;$i<=5;$i++)
                                <label class="star-label" data-value="{{ $i }}" style="cursor:pointer;font-size:1.5rem;color:var(--muted)">
                                    <input type="radio" name="rating" value="{{ $i }}" required style="display:none" onchange="updateStars({{ $i }})"> <i class="fas fa-star"></i>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Sum up your experience">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Your Review</label>
                            <textarea name="body" class="form-control" rows="4" placeholder="Share details of your experience..." required style="resize:vertical"></textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                            @foreach([['food_rating','Food'],['cleanliness_rating','Cleanliness'],['safety_rating','Safety'],['value_rating','Value']] as [$n,$l])
                            <div class="form-group"><label class="form-label">{{ $l }} (1-5)</label>
                                <input type="number" name="{{ $n }}" class="form-control" min="1" max="5" placeholder="1-5"></div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
                @endauth
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div style="position:sticky;top:90px">
                <style>
                .sidebar-card-pkg {
                    background: linear-gradient(135deg, rgba(0, 212, 170, 0.15), rgba(0, 212, 170, 0.03)) !important;
                    border: 1px solid rgba(0, 212, 170, 0.4) !important;
                    box-shadow: 0 10px 30px rgba(0, 212, 170, 0.1);
                    transition: all 0.3s ease;
                }
                .sidebar-card-pkg:hover {
                    transform: translateY(-4px);
                    border-color: rgba(0, 212, 170, 0.7) !important;
                    box-shadow: 0 15px 35px rgba(0, 212, 170, 0.2);
                }
                .btn-pkg {
                    background: linear-gradient(135deg, #00d4aa, #009678) !important;
                    color: #fff !important;
                    font-weight: 800 !important;
                    border: none !important;
                    box-shadow: 0 8px 20px rgba(0, 212, 170, 0.3) !important;
                    transition: all 0.3s ease !important;
                }
                .btn-pkg:hover {
                    transform: scale(1.03);
                    box-shadow: 0 12px 25px rgba(0, 212, 170, 0.5) !important;
                    filter: brightness(1.1);
                }

                .sidebar-card-ai {
                    background: linear-gradient(135deg, rgba(255, 111, 0, 0.15), rgba(255, 202, 40, 0.03)) !important;
                    border: 1px solid rgba(255, 111, 0, 0.4) !important;
                    box-shadow: 0 10px 30px rgba(255, 111, 0, 0.1);
                    transition: all 0.3s ease;
                }
                .sidebar-card-ai:hover {
                    transform: translateY(-4px);
                    border-color: rgba(255, 111, 0, 0.7) !important;
                    box-shadow: 0 15px 35px rgba(255, 111, 0, 0.2);
                }
                .btn-ai {
                    background: linear-gradient(135deg, #ffca28, #ff6f00) !important;
                    color: #000 !important;
                    font-weight: 900 !important;
                    border: none !important;
                    box-shadow: 0 8px 20px rgba(255, 111, 0, 0.3) !important;
                    transition: all 0.3s ease !important;
                }
                .btn-ai:hover {
                    transform: scale(1.03);
                    box-shadow: 0 12px 25px rgba(255, 111, 0, 0.5) !important;
                    filter: brightness(1.1);
                }

                .sidebar-card-tags {
                    background: linear-gradient(135deg, rgba(108, 99, 255, 0.12), rgba(236, 72, 153, 0.03)) !important;
                    border: 1px solid rgba(236, 72, 153, 0.3) !important;
                    box-shadow: 0 10px 30px rgba(236, 72, 153, 0.1);
                    transition: all 0.3s ease;
                }
                .sidebar-card-tags:hover {
                    transform: translateY(-4px);
                    border-color: rgba(236, 72, 153, 0.6) !important;
                }
                .badge-tag-custom {
                    background: linear-gradient(135deg, #6c63ff, #ec4899) !important;
                    color: #fff !important;
                    font-weight: 700 !important;
                    border: none !important;
                    padding: 0.4rem 0.8rem !important;
                    box-shadow: 0 4px 10px rgba(236,72,153,0.2) !important;
                }
                </style>

                {{-- Packages CTA --}}
                <div class="card sidebar-card-pkg" style="padding:1.5rem;margin-bottom:1.25rem;">
                    <h3 style="font-weight:800;margin-bottom:1rem;color:#00d4aa;">📦 Available Packages</h3>
                    @forelse($relatedPackages as $pkg)
                    <a href="{{ route('packages.show',$pkg) }}" style="display:block;padding:.75rem;background:var(--surface2);border-radius:10px;margin-bottom:.75rem;border:1px solid var(--border)">
                        <div style="font-weight:600;font-size:.9rem">{{ $pkg->title }}</div>
                        <div style="font-size:.8rem;color:var(--muted)">{{ $pkg->duration_days }} days</div>
                        <div style="font-size:.95rem;font-weight:700;color:#00d4aa;margin-top:.25rem">₹{{ number_format($pkg->discounted_price) }}/person</div>
                    </a>
                    @empty<p style="color:var(--muted);font-size:.88rem">No packages yet. Check back soon!</p>
                    @endforelse
                    <a href="{{ route('packages.index').'?destination='.$destination->name }}" class="btn btn-pkg" style="width:100%;justify-content:center;margin-top:.5rem">View All Packages</a>
                </div>

                {{-- Plan AI Itinerary --}}
                <div class="card sidebar-card-ai" style="padding:1.5rem;margin-bottom:1.25rem">
                    <h3 style="font-weight:800;margin-bottom:.75rem;color:#ffca28;">🤖 Generate AI Itinerary</h3>
                    <p style="color:var(--muted);font-size:.85rem;margin-bottom:1rem">Let our AI create a personalized day-by-day plan for {{ $destination->name }}</p>
                    @auth
                    <a href="{{ route('itineraries.create').'?destination='.$destination->id }}" class="btn btn-ai" style="width:100%;justify-content:center"><i class="fas fa-wand-magic-sparkles"></i> Generate Itinerary</a>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-ai" style="width:100%;justify-content:center"><i class="fas fa-rocket"></i> Sign Up Free</a>
                    @endauth
                </div>

                {{-- Tags --}}
                @if($destination->tags)
                <div class="card sidebar-card-tags" style="padding:1.5rem">
                    <h3 style="font-weight:800;margin-bottom:.75rem;color:#ec4899;">🏷️ Tags</h3>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                        @foreach($destination->tags as $tag)
                        <span class="badge-pill badge-tag-custom">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
<script>
async function toggleWishlistDest(id, btn) {
    const res = await fetch('{{ route("wishlist.toggle") }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ type:'destination', id })
    });
    const data = await res.json();
    btn.querySelector('i').style.color = data.wishlisted ? '#ff6b6b' : '';
    btn.innerHTML = `<i class="fas fa-heart" style="color:${data.wishlisted?'#ff6b6b':''}"></i> ${data.wishlisted?'Wishlisted':'Add to Wishlist'}`;
}
async function markHelpful(id, btn) {
    const res = await fetch(`/reviews/${id}/helpful`, {
        method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
    });
    const data = await res.json();
    document.getElementById('helpful-'+id).textContent = data.votes;
}

function updateStars(val) {
    document.querySelectorAll('#rating-stars .star-label').forEach(lbl => {
        lbl.style.color = lbl.dataset.value <= val ? 'var(--gold)' : 'var(--muted)';
    });
}
document.querySelectorAll('#rating-stars .star-label').forEach(lbl => {
    lbl.addEventListener('mouseover', () => {
        const hoverVal = lbl.dataset.value;
        document.querySelectorAll('#rating-stars .star-label').forEach(l => {
            l.style.color = l.dataset.value <= hoverVal ? 'var(--gold)' : 'var(--muted)';
        });
    });
    lbl.addEventListener('mouseout', () => {
        const checked = document.querySelector('#rating-stars input:checked');
        const val = checked ? checked.value : 0;
        updateStars(val);
    });
});
</script>
@endsection
