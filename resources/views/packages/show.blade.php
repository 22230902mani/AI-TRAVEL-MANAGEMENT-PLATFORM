@extends('layouts.app')
@section('title', $package->title)
@section('content')
<div style="height:380px;position:relative;overflow:hidden">
    <img src="{{ $package->image ? $package->image_url : ($package->destination?->image_url ?? $package->image_url) }}" alt="{{ $package->title }}" style="width:100%;height:100%;object-fit:cover">
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.85) 0%,rgba(0,0,0,.2) 70%)"></div>
    <div style="position:absolute;bottom:2rem;left:2rem;right:2rem;max-width:1400px;margin:0 auto">
        <span class="badge-pill badge-primary">{{ ucfirst($package->package_type) }}</span>
        <h1 style="font-family:'Playfair Display',serif;font-size:2.4rem;font-weight:900;color:#fff;margin:.5rem 0 .25rem">{{ $package->title }}</h1>
        <div style="color:rgba(255,255,255,.75)"><i class="fas fa-location-dot"></i> {{ $package->destination->name }}, {{ $package->destination->country }}
        &nbsp;•&nbsp; <i class="fas fa-clock"></i> {{ $package->duration_days }} days
        &nbsp;•&nbsp; <i class="fas fa-users"></i> Max {{ $package->max_group_size }} people</div>
    </div>
</div>

<section class="section">
    <div class="section-inner">
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:2.5rem;align-items:start">
            <div>
                {{-- Description --}}
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:1rem">About This Package</h2>
                    <p style="color:var(--muted);line-height:1.8">{{ $package->description }}</p>
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem">
                        @if($package->discount_percent>0)<span class="badge-pill badge-danger">-{{ $package->discount_percent }}% SALE</span>@endif
                        <span class="badge-pill badge-primary">{{ ucfirst($package->difficulty_level) }}</span>
                        <span class="badge-pill badge-success">{{ $package->cancellation_policy }}</span>
                    </div>
                </div>

                {{-- Highlights --}}
                @if($package->highlights)
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-star" style="color:var(--gold)"></i> Highlights</h2>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                        @foreach($package->highlights as $hl)
                        <div style="display:flex;gap:.5rem;align-items:flex-start;font-size:.9rem">
                            <i class="fas fa-check-circle" style="color:var(--secondary);margin-top:.15rem;flex-shrink:0"></i>
                            <span style="color:var(--muted)">{{ $hl }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- AI Travel Estimations --}}
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem;background:rgba(108,99,255,0.05);border:1px solid rgba(108,99,255,0.2)">
                    <h2 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-microchip" style="color:var(--secondary)"></i> AI-Powered Travel Estimations</h2>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem">
                        @php
                            $estimations = $package->ai_estimations ?? [
                                'transport' => '₹4,500 - ₹8,000',
                                'food'      => '₹1,200/day',
                                'hotel'     => '₹2,500 - ₹5,000/night',
                                'activities'=> '₹2,000 - ₹6,000',
                            ];
                        @endphp
                        <div style="background:var(--surface2);padding:1rem;border-radius:12px;text-align:center">
                            <i class="fas fa-plane" style="color:var(--secondary);font-size:1.2rem;margin-bottom:.5rem"></i>
                            <div style="font-size:.7rem;text-transform:uppercase;color:var(--muted)">Transport</div>
                            <div style="font-weight:700;font-size:.9rem">{{ $estimations['transport'] }}</div>
                        </div>
                        <div style="background:var(--surface2);padding:1rem;border-radius:12px;text-align:center">
                            <i class="fas fa-utensils" style="color:var(--gold);font-size:1.2rem;margin-bottom:.5rem"></i>
                            <div style="font-size:.7rem;text-transform:uppercase;color:var(--muted)">Food</div>
                            <div style="font-weight:700;font-size:.9rem">{{ $estimations['food'] }}</div>
                        </div>
                        <div style="background:var(--surface2);padding:1rem;border-radius:12px;text-align:center">
                            <i class="fas fa-hotel" style="color:var(--secondary);font-size:1.2rem;margin-bottom:.5rem"></i>
                            <div style="font-size:.7rem;text-transform:uppercase;color:var(--muted)">Hotel Stay</div>
                            <div style="font-weight:700;font-size:.9rem">{{ $estimations['hotel'] }}</div>
                        </div>
                        <div style="background:var(--surface2);padding:1rem;border-radius:12px;text-align:center">
                            <i class="fas fa-mountain-sun" style="color:#ff6b6b;font-size:1.2rem;margin-bottom:.5rem"></i>
                            <div style="font-size:.7rem;text-transform:uppercase;color:var(--muted)">Activities</div>
                            <div style="font-weight:700;font-size:.9rem">{{ $estimations['activities'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- External Service Integration --}}
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-link" style="color:var(--secondary)"></i> Booking Assistance & External Portals</h2>
                    <p style="color:var(--muted);font-size:.85rem;margin-bottom:1.25rem">Quickly navigate to verified portals for transit and logistics.</p>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap">
                        <a href="https://www.irctc.co.in" target="_blank" class="btn btn-outline btn-sm" style="background:#f1f5f9;color:#000"><i class="fas fa-train"></i> IRCTC</a>
                        <a href="https://www.makemytrip.com/flights/" target="_blank" class="btn btn-outline btn-sm" style="background:#f1f5f9;color:#000"><i class="fas fa-plane-departure"></i> Flight Portal</a>
                        <a href="https://www.redbus.in" target="_blank" class="btn btn-outline btn-sm" style="background:#f1f5f9;color:#000"><i class="fas fa-bus"></i> Bus Service</a>
                        <a href="https://www.uber.com" target="_blank" class="btn btn-outline btn-sm" style="background:#f1f5f9;color:#000"><i class="fas fa-car"></i> Uber/Rapido</a>
                    </div>
                </div>

                {{-- Weather Insights --}}
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem;background:linear-gradient(135deg,rgba(0,184,212,0.05),rgba(0,184,212,0.02));border:1px solid rgba(0,184,212,0.2)">
                    <h2 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-cloud-sun" style="color:#00b8d4"></i> Destination Weather Insights</h2>
                    <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap">
                        <div style="display:flex;align-items:center;gap:1rem">
                            <i class="fas fa-temperature-half" style="font-size:2rem;color:#ff9100"></i>
                            <div>
                                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase">Current Avg.</div>
                                <div style="font-size:1.4rem;font-weight:800">24°C - 30°C</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:1rem">
                            <i class="fas fa-umbrella" style="font-size:2rem;color:#00b8d4"></i>
                            <div>
                                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase">Precipitation</div>
                                <div style="font-size:1.4rem;font-weight:800">15% Chance</div>
                            </div>
                        </div>
                        <div style="margin-left:auto;padding:.75rem 1rem;background:rgba(255,255,255,0.05);border-radius:12px;font-size:.85rem;color:var(--muted);max-width:300px">
                            <i class="fas fa-circle-info" style="color:#00b8d4"></i> AI Recommendation: Best time to visit is from <strong>October to March</strong> for pleasant weather.
                        </div>
                    </div>
                </div>

                {{-- Inclusions/Exclusions --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
                    @if($package->inclusions)
                    <div class="card" style="padding:1.5rem">
                        <h3 style="font-weight:700;margin-bottom:.75rem;color:var(--secondary)"><i class="fas fa-circle-check"></i> What's Included</h3>
                        @foreach($package->inclusions as $inc)
                        <div style="font-size:.85rem;color:var(--muted);margin-bottom:.4rem"><i class="fas fa-check" style="color:var(--secondary);width:14px"></i> {{ $inc }}</div>
                        @endforeach
                    </div>
                    @endif
                    @if($package->exclusions)
                    <div class="card" style="padding:1.5rem">
                        <h3 style="font-weight:700;margin-bottom:.75rem;color:var(--accent)"><i class="fas fa-circle-xmark"></i> Not Included</h3>
                        @foreach($package->exclusions as $exc)
                        <div style="font-size:.85rem;color:var(--muted);margin-bottom:.4rem"><i class="fas fa-xmark" style="color:var(--accent);width:14px"></i> {{ $exc }}</div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Price History Chart --}}
                <div class="card" style="padding:1.75rem;margin-bottom:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:.5rem"><i class="fas fa-chart-line" style="color:var(--secondary)"></i> Price History — LSTM Prediction</h2>
                    <p style="color:var(--muted);font-size:.85rem;margin-bottom:1rem">30-day price trend. AI predicts best booking window.</p>
                    <canvas id="priceChart" height="80"></canvas>
                </div>

                {{-- Reviews --}}
                <div class="card" style="padding:1.5rem">
                    <h2 style="font-weight:700;margin-bottom:1.25rem">Reviews</h2>
                    @forelse($package->reviews as $review)
                    <div style="padding:.75rem 0;border-bottom:1px solid var(--border)">
                        <div style="display:flex;justify-content:space-between">
                            <div style="font-weight:600;font-size:.9rem">{{ $review->user->name }}</div>
                            <div class="stars" style="font-size:.85rem">{{ str_repeat('★',$review->rating) }}</div>
                        </div>
                        <p style="color:var(--muted);font-size:.85rem;margin-top:.3rem">{{ $review->body }}</p>
                    </div>
                    @empty<p style="color:var(--muted)">No reviews yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- BOOKING SIDEBAR --}}
            <div style="position:sticky;top:90px">
                <div class="card" style="padding:1.75rem;background:linear-gradient(135deg,rgba(108,99,255,.1),rgba(0,212,170,.07));border-color:rgba(108,99,255,.3)">
                    <div style="margin-bottom:1rem">
                        @if($package->original_price>$package->price_per_person)
                        <div style="font-size:.88rem;color:var(--muted);text-decoration:line-through">Was ₹{{ number_format($package->original_price) }}</div>
                        @endif
                        <div style="font-size:2rem;font-weight:900;color:var(--secondary)">₹{{ number_format($package->discounted_price) }}<span style="font-size:.9rem;font-weight:400;color:var(--muted)">/person</span></div>
                        @if($package->discount_percent>0)<span class="badge-pill badge-danger">You save ₹{{ number_format($package->original_price - $package->discounted_price) }}</span>@endif
                    </div>

                    <div style="background:var(--surface2);border-radius:12px;padding:1rem;margin-bottom:1rem;font-size:.85rem">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.4rem"><span style="color:var(--muted)">Duration</span><span>{{ $package->duration_days }} days</span></div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.4rem"><span style="color:var(--muted)">Max Group</span><span>{{ $package->max_group_size }} people</span></div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.4rem"><span style="color:var(--muted)">Difficulty</span><span>{{ ucfirst($package->difficulty_level) }}</span></div>
                        <div style="display:flex;justify-content:space-between"><span style="color:var(--muted)">Cancellation</span><span style="color:var(--secondary);font-size:.82rem">{{ $package->cancellation_policy }}</span></div>
                    </div>

                    @auth
                    <a href="{{ route('bookings.create').'?package_id='.$package->id }}" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.85rem">
                        <i class="fas fa-ticket"></i> Book Now
                    </a>
                    <button onclick="toggleWishlistPkg('{{ $package->id }}', this)" class="btn btn-outline btn-sm" id="wish-btn" style="width:100%;justify-content:center;margin-top:.75rem">
                        <i class="fas fa-heart" style="{{ $isWishlisted ? 'color:#ff6b6b' : '' }}"></i> {{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                    </button>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem">Sign Up to Book</a>
                    @endauth

                    <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border);font-size:.8rem;color:var(--muted);text-align:center">
                        <i class="fas fa-shield-halved" style="color:var(--secondary)"></i> Secure payment • QR e-ticket issued instantly
                    </div>
                </div>

                {{-- Related --}}
                @if($relatedPackages->count())
                <div class="card" style="padding:1.25rem;margin-top:1.25rem">
                    <h3 style="font-weight:700;margin-bottom:.75rem">Similar Packages</h3>
                    @foreach($relatedPackages as $rp)
                    <a href="{{ route('packages.show',$rp) }}" style="display:flex;gap:.75rem;padding:.6rem 0;border-bottom:1px solid var(--border)">
                        <div><div style="font-size:.88rem;font-weight:600">{{ $rp->title }}</div>
                        <div style="font-size:.78rem;color:var(--secondary)">₹{{ number_format($rp->discounted_price) }}/person</div></div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
const priceData = @json($priceHistory);
new Chart(document.getElementById('priceChart'), {
    type:'line', data:{
        labels: priceData.map(d=>d.date),
        datasets:[{label:'Price ($)', data:priceData.map(d=>d.price),
            borderColor:'#6c63ff',backgroundColor:'rgba(108,99,255,.1)',fill:true,
            tension:.4,pointRadius:2}]
    },
    options:{plugins:{legend:{display:false}},scales:{y:{ticks:{color:'#8892a4'},grid:{color:'rgba(255,255,255,.05)'}},x:{ticks:{color:'#8892a4',maxTicksLimit:8},grid:{display:false}}}}
});
async function toggleWishlistPkg(id, btn) {
    const res = await fetch('{{ route("wishlist.toggle") }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({type:'package', id})
    });
    const data = await res.json();
    btn.innerHTML = `<i class="fas fa-heart" style="color:${data.wishlisted?'#ff6b6b':''}"></i> ${data.wishlisted?'Remove from Wishlist':'Add to Wishlist'}`;
}
</script>
@endsection
