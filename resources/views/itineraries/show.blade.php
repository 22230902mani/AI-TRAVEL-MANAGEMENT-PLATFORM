@extends('layouts.app')
@section('title', $itinerary->title)
@section('content')
<div style="position:relative; background:linear-gradient(135deg, #1b0e02, #070300); padding: 5rem 2rem 3rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.4); border-bottom: 2px solid rgba(255,111,0,0.25);">
    <!-- Decorative background elements -->
    <div style="position:absolute; top:-50%; left:-10%; width:500px; height:500px; background:radial-gradient(circle, rgba(255,111,0,0.18) 0%, transparent 70%); border-radius:50%; z-index:0;"></div>
    <div style="position:absolute; bottom:-30%; right:-5%; width:400px; height:400px; background:radial-gradient(circle, rgba(255,170,0,0.12) 0%, transparent 70%); border-radius:50%; z-index:0;"></div>
    
    <div style="max-width:1400px; margin:0 auto; position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1.5rem;">
        <div style="animation: fadeInUp 0.6s ease-out;">
            <a href="{{ route('itineraries.index') }}" style="color:#b0c4de; font-size:0.9rem; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; display:inline-block; transition:0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#b0c4de'">
                <i class="fas fa-arrow-left" style="margin-right:0.5rem; color:#ffaa00;"></i> Back to Itineraries
            </a>
            <h1 style="font-family:'Playfair Display',serif; font-size:3.5rem; font-weight:900; color:#fff; line-height:1.2; margin-bottom:0.5rem; text-shadow: 2px 4px 10px rgba(0,0,0,0.5);">{{ $itinerary->title }}</h1>
            <div style="display:flex; gap:1.5rem; color:#cbd5e1; font-size:1rem; font-weight:500; align-items:center; flex-wrap:wrap;">
                <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-map-marker-alt" style="color:#ffaa00;"></i> {{ $itinerary->destination?->name ?? 'Custom Destination' }}</span>
                <span style="opacity:0.3; color:#fff;">|</span>
                <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-calendar-alt" style="color:#00c853;"></i> {{ $itinerary->start_date?->format('M d') }} – {{ $itinerary->end_date?->format('M d, Y') }}</span>
                <span style="opacity:0.3; color:#fff;">|</span>
                <span style="display:flex; align-items:center; gap:0.5rem; background:rgba(255,255,255,0.06); padding:0.2rem 0.8rem; border-radius:50px; border:1px solid rgba(255,255,255,0.12);"><i class="fas fa-clock" style="color:#ffd54f;"></i> {{ $itinerary->duration_days }} Days</span>
            </div>
        </div>
        @if($itinerary->is_paid)
        <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center; animation: fadeInUp 0.8s ease-out;">
            @if(!$itinerary->is_public)
            <form method="POST" action="{{ route('itineraries.update',$itinerary) }}" style="margin:0;">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $itinerary->title }}">
                <input type="hidden" name="status" value="{{ $itinerary->status }}">
                <input type="hidden" name="is_public" value="1">
                <button type="submit" class="btn btn-outline" style="background:rgba(255,255,255,0.05); backdrop-filter:blur(10px);"><i class="fas fa-share-alt"></i> Make Public</button>
            </form>
            @else
            <div style="background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); padding:0.6rem 1.2rem; border-radius:50px; border:1px solid rgba(255,255,255,0.2); color:#fff; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-globe" style="color:var(--secondary);"></i> Public Link: <a href="{{ route('itineraries.share',$itinerary->share_token) }}" style="color:var(--primary); font-weight:700; transition:0.2s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">/share/{{ substr($itinerary->share_token,0,8) }}</a>
            </div>
            @endif
            
            <a href="{{ route('itineraries.pdf', $itinerary) }}" class="btn btn-outline" style="background:rgba(255,255,255,0.05); backdrop-filter:blur(10px); display:inline-flex; align-items:center; gap:0.5rem; color:#fff; text-decoration:none; padding:0.6rem 1.2rem; border-radius:50px; border:1px solid rgba(255,255,255,0.2); transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                <i class="fas fa-file-pdf" style="color:#ff3d00;"></i> Download PDF
            </a>
        </div>
        @endif
    </div>
</div>

<section class="section" style="padding-top:3rem; background:var(--surface2);">
    <div class="section-inner">
        <!-- Budget Overview -->
        @if($itinerary->budget)
        <div class="card" style="margin-bottom:2.5rem; background:rgba(255,255,255,0.04); border-radius:24px; padding:2rem; position:relative; overflow:hidden; border:1px solid var(--border);">
            <div style="position:absolute; top:0; left:0; width:100%; height:6px; background:linear-gradient(90deg, var(--primary), var(--secondary), var(--gold));"></div>
            <h3 style="font-size:1.2rem; font-weight:800; color:var(--text); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem;"><i class="fas fa-chart-pie" style="color:var(--primary); font-size:1.5rem;"></i> Budget Overview</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:2rem;">
                @foreach([
                    ['Total Budget', '₹'.number_format($itinerary->budget), 'var(--primary)', 'fas fa-money-bill-wave'],
                    ['Total Spent', '₹'.number_format($itinerary->spent), 'var(--accent)', 'fas fa-receipt'],
                    ['Remaining', '₹'.number_format($itinerary->budget_remaining), 'var(--success)', 'fas fa-piggy-bank'],
                    ['Budget Used', number_format($itinerary->budget_used_percent).'%', 'var(--gold)', 'fas fa-chart-line']
                ] as [$l,$v,$c,$i])
                <div style="display:flex; align-items:center; gap:1.25rem; padding:1.25rem; background:rgba(255,255,255,0.03); border-radius:16px; border:1px solid var(--border); transition:transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width:50px; height:50px; border-radius:12px; background:{{ $c }}15; color:{{ $c }}; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
                        <i class="{{ $i }}"></i>
                    </div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">{{ $l }}</div>
                        <div style="font-size:1.6rem; font-weight:900; color:var(--text);">{{ $v }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Progress Bar -->
            <div style="margin-top:2rem; width:100%; height:12px; background:var(--surface2); border-radius:50px; overflow:hidden;">
                <div style="width:{{ min($itinerary->budget_used_percent, 100) }}%; height:100%; background:{{ $itinerary->budget_used_percent > 90 ? 'var(--accent)' : ($itinerary->budget_used_percent > 75 ? 'var(--gold)' : 'var(--success)') }}; border-radius:50px; transition:width 1s ease-in-out;"></div>
            </div>
        </div>
        @endif

        <!-- AI Financial Breakdown -->
        @php $fin = $itinerary->preferences['financials'] ?? null; @endphp
        @if($fin)
            @if($itinerary->is_paid)
            <div style="margin-bottom:3rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.5rem;">
                    <div>
                        <h3 style="font-size:1.5rem; font-weight:900; color:var(--text); display:flex; align-items:center; gap:0.75rem;"><i class="fas fa-robot" style="color:var(--primary);"></i> AI Financial Breakdown</h3>
                        <p style="color:var(--muted); font-size:0.95rem; margin-top:0.25rem;">Estimated costs generated by TravelMate AI for your {{ $itinerary->duration_days }}-day trip.</p>
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; margin-bottom:1.5rem;">
                    @foreach([
                        ['Flight (Round Trip)', $fin['travel_flight'] ?? 0, 'Est. airfare', 'fas fa-plane-departure', 'var(--primary)'],
                        ['Train (Round Trip)', $fin['travel_train'] ?? 0, 'Est. rail fare', 'fas fa-train', 'var(--secondary)'],
                        ['Room Rents', $fin['room_cost'] ?? 0, 'For '.($itinerary->duration_days - 1).' nights', 'fas fa-hotel', '#8e44ad'],
                        ['Activities', $fin['activity_cost'] ?? 0, 'Sightseeing', 'fas fa-camera-retro', '#e67e22'],
                        ['Food & Dining', $fin['food_cost'] ?? 0, 'For '.$itinerary->duration_days.' days', 'fas fa-utensils', '#e74c3c']
                    ] as [$title, $amount, $desc, $icon, $color])
                    @if($amount > 0)
                    <div class="card" style="background:rgba(255,255,255,0.04); border-radius:16px; padding:1.5rem; text-align:center; border:1px solid var(--border); position:relative; overflow:hidden; z-index:1;">
                        <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:{{ $color }}; opacity:0.04; z-index:-1; transform:rotate(-15deg);"><i class="{{ $icon }}"></i></div>
                        <div style="width:48px; height:48px; margin:0 auto 1rem; background:{{ $color }}15; color:{{ $color }}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem;"><i class="{{ $icon }}"></i></div>
                        <div style="font-size:0.8rem; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px; margin-bottom:0.25rem;">{{ $title }}</div>
                        <div style="font-size:1.5rem; font-weight:900; color:var(--text); margin-bottom:0.25rem;">₹{{ number_format($amount) }}</div>
                        <div style="font-size:0.75rem; color:var(--muted);">{{ $desc }}</div>
                    </div>
                    @endif
                    @endforeach
                </div>

                @if(!empty($fin['recommended_train']) || !empty($fin['recommended_flight']))
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">
                    @if(!empty($fin['recommended_train']))
                    <div class="card" style="background:linear-gradient(135deg, var(--secondary) 0%, #1565c0 100%); padding:1.5rem; border-radius:16px; color:#fff; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <div style="font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:0.5rem; text-transform:uppercase; letter-spacing:1px; opacity:0.9;"><i class="fas fa-train"></i> Train Journey Plan</div>
                            <div style="font-size:0.8rem; opacity:0.75; margin-top:0.25rem;">Recommended total including buffer.</div>
                        </div>
                        <div style="font-size:1.8rem; font-weight:900;">₹{{ number_format($fin['recommended_train']) }}</div>
                    </div>
                    @endif
                    @if(!empty($fin['recommended_flight']))
                    <div class="card" style="background:linear-gradient(135deg, var(--gold) 0%, #f39c12 100%); padding:1.5rem; border-radius:16px; color:#fff; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <div style="font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:0.5rem; text-transform:uppercase; letter-spacing:1px; opacity:0.9;"><i class="fas fa-plane"></i> Flight Journey Plan</div>
                            <div style="font-size:0.8rem; opacity:0.75; margin-top:0.25rem;">Recommended total including buffer.</div>
                        </div>
                        <div style="font-size:1.8rem; font-weight:900;">₹{{ number_format($fin['recommended_flight']) }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @else
            <!-- Blurred AI Financial Breakdown Preview -->
            <div style="margin-bottom:3rem; position:relative; overflow:hidden; border-radius:24px; border:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02); padding:2rem; filter:blur(4px); pointer-events:none; user-select:none;">
                <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.5rem;">
                    <div>
                        <h3 style="font-size:1.5rem; font-weight:900; color:var(--text); display:flex; align-items:center; gap:0.75rem;"><i class="fas fa-robot"></i> AI Financial Breakdown</h3>
                        <p style="color:var(--muted); font-size:0.95rem; margin-top:0.25rem;">Estimated costs generated by TravelMate AI for your {{ $itinerary->duration_days }}-day trip.</p>
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; margin-bottom:1.5rem;">
                    <div class="card" style="background:rgba(255,255,255,0.04); border-radius:16px; padding:1.5rem; text-align:center; border:1px solid var(--border);">
                        <div style="width:48px; height:48px; margin:0 auto 1rem; background:rgba(255,255,255,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem;"><i class="fas fa-plane-departure"></i></div>
                        <div style="font-size:0.8rem; color:var(--muted); text-transform:uppercase; font-weight:700;">Flight Cost</div>
                        <div style="font-size:1.5rem; font-weight:900; color:var(--text);">₹9,500</div>
                    </div>
                    <div class="card" style="background:rgba(255,255,255,0.04); border-radius:16px; padding:1.5rem; text-align:center; border:1px solid var(--border);">
                        <div style="width:48px; height:48px; margin:0 auto 1rem; background:rgba(255,255,255,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem;"><i class="fas fa-hotel"></i></div>
                        <div style="font-size:0.8rem; color:var(--muted); text-transform:uppercase; font-weight:700;">Hotel Rent</div>
                        <div style="font-size:1.5rem; font-weight:900; color:var(--text);">₹14,200</div>
                    </div>
                    <div class="card" style="background:rgba(255,255,255,0.04); border-radius:16px; padding:1.5rem; text-align:center; border:1px solid var(--border);">
                        <div style="width:48px; height:48px; margin:0 auto 1rem; background:rgba(255,255,255,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem;"><i class="fas fa-utensils"></i></div>
                        <div style="font-size:0.8rem; color:var(--muted); text-transform:uppercase; font-weight:700;">Food Cost</div>
                        <div style="font-size:1.5rem; font-weight:900; color:var(--text);">₹5,800</div>
                    </div>
                </div>
            </div>
            @endif
        @endif

        <!-- Day-by-Day Itinerary -->
        <h3 style="font-size:1.6rem; font-weight:900; color:#fff; display:flex; align-items:center; gap:0.75rem; margin-bottom:1.75rem;"><i class="fas fa-calendar-day" style="color:#ff7b00; text-shadow:0 0 10px rgba(255,123,0,0.3);"></i> Day-by-Day Plan</h3>
        
        @if($itinerary->days)
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            @foreach($itinerary->days as $index => $day)
                @if($itinerary->is_paid || $index === 0)
                <div class="card day-card" style="border-radius:20px; overflow:hidden; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); transition:all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); margin-bottom: 0.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div class="day-header" style="padding:1.5rem 1.75rem; background:{{ $index === 0 ? 'rgba(255,123,0,0.08)' : 'rgba(255,255,255,0.01)' }}; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleDay(this)">
                        <div style="display:flex; align-items:center; gap:1.25rem;">
                            <div style="width:42px; height:42px; background:linear-gradient(135deg, #ffaa00, #ff5500); color:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:1.15rem; box-shadow:0 4px 15px rgba(255,85,0,0.4);">{{ $index + 1 }}</div>
                            <span style="font-weight:800; font-size:1.2rem; color:#fff; letter-spacing:0.3px;">{{ $day['label'] }}</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:1.5rem;">
                            @if(isset($day['day_cost']) && $day['day_cost'] > 0)
                            <span style="background:rgba(0, 200, 83, 0.12); border:1px solid rgba(0, 200, 83, 0.3); color:#00ff66; padding:0.4rem 1rem; border-radius:50px; font-size:0.85rem; font-weight:800; letter-spacing:0.5px; box-shadow:0 0 10px rgba(0, 200, 83, 0.1);"><i class="fas fa-rupee-sign"></i> {{ number_format($day['day_cost']) }}</span>
                            @endif
                            <i class="fas fa-chevron-down toggle-icon" style="color:#a855f7; transition:transform 0.4s; font-size:1.1rem; transform:{{ $index === 0 ? 'rotate(180deg)' : 'rotate(0)' }};"></i>
                        </div>
                    </div>
                    
                    <div class="day-content" style="display:{{ $index === 0 ? 'block' : 'none' }}; background:rgba(0,0,0,0.15);">
                        @if(!empty($day['weather_tip']))
                        <div style="padding:1.2rem 1.75rem; background:rgba(255,191,0,0.06); border-bottom:1px solid rgba(255,255,255,0.04); display:flex; align-items:center; gap:0.75rem; color:#ffd54f; font-size:0.95rem; font-weight:600; border-left:4px solid #ffbf00;">
                            <i class="fas fa-sun" style="color:#ffbf00;"></i> {{ $day['weather_tip'] }}
                        </div>
                        @endif
                        
                        <div style="padding:0.75rem 1.75rem 2rem;">
                            @foreach($day['slots'] as $sIndex => $slot)
                            <div style="display:flex; gap:1.75rem; padding-top:2rem; position:relative;">
                                <!-- Timeline line -->
                                @if(!$loop->last)
                                <div style="position:absolute; left:45px; top:3.5rem; bottom:-2rem; width:2px; background:rgba(255,255,255,0.05);"></div>
                                @endif
                                
                                <div style="min-width:100px; text-align:right;">
                                    <div style="font-size:0.85rem; font-weight:800; color:#ff9e00; background:rgba(255,158,0,0.1); border:1px solid rgba(255,158,0,0.25); padding:0.35rem 0.75rem; border-radius:8px; display:inline-block; letter-spacing:0.5px; box-shadow:0 0 10px rgba(255,158,0,0.05);">{{ $slot['time'] }}</div>
                                </div>
                                
                                <!-- Timeline dot -->
                                <div style="position:relative; z-index:1; width:18px; height:18px; border-radius:50%; background:#111827; border:4px solid #ff7b00; margin-top:5px; box-shadow:0 0 0 5px rgba(255,123,0,0.15);"></div>
                                
                                <div style="flex:1; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); padding:1.5rem; border-radius:16px; transition:all 0.3s;" onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.2)'; this.style.borderColor='rgba(255,123,0,0.2)'; this.style.background='rgba(255,255,255,0.04)';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='rgba(255,255,255,0.05)'; this.style.background='rgba(255,255,255,0.02)';">
                                    <div style="font-weight:800; font-size:1.15rem; color:#fff; margin-bottom:0.5rem; letter-spacing:0.3px;">{{ $slot['activity'] }}</div>
                                    @if(!empty($slot['notes']))
                                    <div style="color:#b0c4de; font-size:0.95rem; line-height:1.6; margin-bottom:1rem; display:flex; align-items:start; gap:0.5rem; background:rgba(255,255,255,0.02); padding:0.75rem 1rem; border-radius:8px; border-left:3px solid #ff7b00;"><i class="fas fa-info-circle" style="color:#ff7b00; margin-top:0.2rem; opacity:0.8;"></i> <span>{{ $slot['notes'] }}</span></div>
                                    @endif
                                    @if(!empty($slot['est_cost']))
                                    <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.8rem; font-weight:800; color:#00ff66; background:rgba(0, 200, 83, 0.1); border:1px solid rgba(0, 200, 83, 0.2); padding:0.25rem 0.75rem; border-radius:6px;"><i class="fas fa-tag"></i> Est. ₹{{ $slot['est_cost'] }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <!-- Blurred Premium Gated Day Placeholder -->
                <div class="card day-card" style="border-radius:20px; overflow:hidden; background:rgba(255,255,255,0.01); border:1px dashed rgba(255,255,255,0.1); opacity:0.35; filter:blur(1px); pointer-events:none; user-select:none; transition:all 0.3s; margin-bottom:1.25rem;">
                    <div class="day-header" style="padding:1.5rem 1.75rem; display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.1);">
                        <div style="display:flex; align-items:center; gap:1.25rem;">
                            <div style="width:42px; height:42px; background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.3); border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.15rem;">{{ $index + 1 }}</div>
                            <span style="font-weight:800; font-size:1.2rem; color:rgba(255,255,255,0.4);">{{ $day['label'] }} — Premium Only</span>
                        </div>
                        <div>
                            <i class="fas fa-lock" style="color:#ff7b00; font-size:1.2rem;"></i>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if(!$itinerary->is_paid)
        <!-- Beautiful Premium Paywall Card overlay -->
        <div class="card" style="margin-top:2.5rem; padding:3rem 2rem; border-radius:24px; background:linear-gradient(135deg, #0e1e38 0%, #050e21 100%); border:1px solid rgba(255,111,0,0.3); text-align:center; position:relative; overflow:hidden; box-shadow:0 15px 35px rgba(0,0,0,0.4); z-index:2;">
            <div style="position:absolute; top:-20%; right:-10%; width:300px; height:300px; background:radial-gradient(circle, rgba(255,111,0,0.08) 0%, transparent 70%); border-radius:50%; pointer-events:none; z-index:0;"></div>
            <div style="position:absolute; bottom:-20%; left:-10%; width:300px; height:300px; background:radial-gradient(circle, rgba(41,182,246,0.08) 0%, transparent 70%); border-radius:50%; pointer-events:none; z-index:0;"></div>
            
            <div style="position:relative; z-index:1;">
                <div style="width:70px; height:70px; margin:0 auto 1.5rem; background:rgba(255,111,0,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,111,0,0.25); animation: pulse 2s infinite;">
                    <i class="fas fa-lock" style="color:#ff6f00; font-size:2rem;"></i>
                </div>
                <h3 style="font-family:'Playfair Display',serif; font-size:2.2rem; font-weight:900; color:#fff; margin-bottom:0.75rem; text-shadow:0 2px 10px rgba(0,0,0,0.2);">🔒 Premium Itinerary Features Locked</h3>
                <p style="color:#b0c4de; max-width:650px; margin:0 auto 2rem; font-size:1.05rem; line-height:1.7;">
                    To unlock the premium features like full daily schedules, timing slots, custom notes, and TravelMate AI's complete Financial Breakdown, make a secure one-time payment.
                </p>
                
                <div style="display:flex; justify-content:center; gap:2rem; flex-wrap:wrap; margin-bottom:2.5rem; font-size:0.95rem; color:#fff; opacity:0.9;">
                    <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-check-circle" style="color:#00ff66;"></i> Complete Daily Schedule</span>
                    <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-check-circle" style="color:#00ff66;"></i> Time Slots & Directions</span>
                    <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-check-circle" style="color:#00ff66;"></i> AI Financial Breakdown</span>
                </div>

                <button id="pay-btn" onclick="initiateRazorpayPayment()" class="btn btn-primary" style="padding:1.1rem 3rem; font-size:1.2rem; font-weight:800; border-radius:50px; border:none; box-shadow:0 8px 25px rgba(255,111,0,0.4); cursor:pointer; transition:transform 0.2s, box-shadow 0.2s; background: linear-gradient(135deg, #ffca28, #ff6f00); color: #fff;" onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 12px 30px rgba(255,111,0,0.6)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 25px rgba(255,111,0,0.4)';">
                    <span id="btn-spinner" style="display:none; margin-right:0.5rem;"><i class="fas fa-spinner fa-spin"></i></span>
                    <i id="btn-icon" class="fas fa-unlock" style="margin-right:0.5rem;"></i>
                    <span id="btn-text">To unlock the features make this payment</span>
                </button>

                <div style="margin-top:1.75rem; font-size:0.85rem; color:#8fa0c0; background:rgba(255, 255, 255, 0.04); padding:0.75rem 1.5rem; border-radius:8px; display:inline-block; border:1px solid rgba(255, 255, 255, 0.08);">
                    <i class="fas fa-info-circle" style="color:#ffca28; margin-right:0.25rem;"></i> <strong>Test Mode:</strong> Use card <code style="color:#00d4aa; background:rgba(0,0,0,0.25); padding:0.15rem 0.4rem; border-radius:4px;">4111 1111 1111 1111</code> to complete mock payment successfully.
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="card" style="text-align:center; padding:5rem 2rem; background:#fff; border-radius:24px; border:2px dashed rgba(0,0,0,0.1); box-shadow:none;">
            <div style="font-size:4rem; margin-bottom:1rem; opacity:0.5;">🏝️</div>
            <h4 style="font-size:1.5rem; font-weight:800; color:var(--text); margin-bottom:0.5rem;">No Itinerary Found</h4>
            <p style="color:var(--muted); max-width:400px; margin:0 auto 1.5rem;">Your day-by-day plan hasn't been generated yet. Let our AI build the perfect schedule for you!</p>
            <a href="{{ route('itineraries.create') }}" class="btn btn-primary btn-lg" style="padding:0.8rem 2rem; font-size:1rem; box-shadow:0 8px 25px rgba(255,111,0,0.3);"><i class="fas fa-magic"></i> Generate with AI</a>
        </div>
        @endif
    </div>
</section>

{{-- Success Overlay --}}
<div class="success-overlay" id="success-overlay">
    <div class="success-box">
        <div style="font-size:3rem;margin-bottom:1rem">✨</div>
        <h2 style="color:#fff;font-weight:900;margin-bottom:.5rem">Premium Unlocked!</h2>
        <p style="color:#b0c4de;margin-bottom:1.5rem">Activating premium parameters and loading complete itinerary...</p>
        <div class="spinner" style="display:block;margin:0 auto"></div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .day-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
        transform: translateY(-2px) !important;
    }
    .success-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(6, 7, 19, 0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .success-box {
        background: linear-gradient(135deg, #0a1628, #0f1f3d);
        border: 1px solid rgba(0, 255, 102, 0.3);
        border-radius: 24px;
        padding: 3rem;
        text-align: center;
        max-width: 400px;
        animation: popIn 0.4s ease;
    }
    .spinner {
        display: none;
        width: 24px;
        height: 24px;
        border: 3px solid rgba(255,255,255,.2);
        border-top-color: #ff6f00;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes popIn { from { transform: scale(.8); opacity:0; } to { transform: scale(1); opacity:1; } }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 111, 0, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 111, 0, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 111, 0, 0); }
    }
</style>
@endpush

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function toggleDay(element) {
        const content = element.nextElementSibling;
        const icon = element.querySelector('.toggle-icon');
        
        if(!content) return;
        
        if(content.style.display === 'none') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
            element.style.background = 'rgba(255,111,0,0.05)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0)';
            element.style.background = '';
        }
    }

    const ORDER_URL = '{{ route("itineraries.unlock-order", $itinerary) }}';
    const VERIFY_URL = '{{ route("itineraries.verify-payment", $itinerary) }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    async function initiateRazorpayPayment() {
        const btn = document.getElementById('pay-btn');
        const spinner = document.getElementById('btn-spinner');
        const icon = document.getElementById('btn-icon');
        const text = document.getElementById('btn-text');

        // Show loading state
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        icon.style.display = 'none';
        text.textContent = 'Creating secure order...';

        try {
            // Step 1: Create Razorpay order on server
            const res = await fetch(ORDER_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            });
            
            if (!res.ok) {
                const textErr = await res.text();
                let errText = 'Failed to create order.';
                try {
                    const errJson = JSON.parse(textErr);
                    errText = errJson.error || errJson.message || errText;
                } catch(e) {
                    if (textErr.includes('Page Expired')) errText = 'Session expired. Please refresh the page.';
                    else errText = textErr.substring(0, 100) || errText;
                }
                throw new Error(errText);
            }
            
            const data = await res.json();

            if (!data.order_id) throw new Error(data.error || 'Failed to create order.');

            // Reset button to active
            btn.disabled = false;
            spinner.style.display = 'none';
            icon.style.display = 'inline';
            text.textContent = 'To unlock the features make this payment';

            // Step 2: Open Razorpay checkout popup
            const options = {
                key: data.key_id,
                amount: data.amount,
                currency: data.currency,
                name: 'TravelMate',
                description: data.description,
                order_id: data.order_id,
                image: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=100&q=80',
                prefill: {
                    name: data.name,
                    email: data.email,
                    contact: '9999999999', // Prefill mock contact to bypass saved-card verification
                },
                theme: { color: '#ff6f00' },
                modal: {
                    ondismiss: function() {
                        btn.disabled = false;
                        text.textContent = 'To unlock the features make this payment';
                    }
                },
                handler: async function(response) {
                    // Step 3: Verify payment signature on server
                    document.getElementById('success-overlay').style.display = 'flex';

                    const verifyRes = await fetch(VERIFY_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                        }),
                    });
                    
                    if (!verifyRes.ok) {
                        const textVerifyErr = await verifyRes.text();
                        let errVerifyText = 'Payment verification failed.';
                        try {
                            const errJson = JSON.parse(textVerifyErr);
                            errVerifyText = errJson.message || errJson.error || errVerifyText;
                        } catch(e) {
                            errVerifyText = textVerifyErr.substring(0, 100) || errVerifyText;
                        }
                        throw new Error(errVerifyText);
                    }
                    
                    const result = await verifyRes.json();

                    if (result.success) {
                        window.location.href = result.redirect_url;
                    } else {
                        document.getElementById('success-overlay').style.display = 'none';
                        alert('❌ ' + (result.message || 'Payment verification failed.'));
                    }
                },
            };

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function(response) {
                document.getElementById('success-overlay').style.display = 'none';
                alert('❌ Payment failed: ' + response.error.description);
            });
            rzp.open();

        } catch (err) {
            btn.disabled = false;
            spinner.style.display = 'none';
            icon.style.display = 'inline';
            text.textContent = 'To unlock the features make this payment';
            alert('❌ Could not initiate payment: ' + err.message);
        }
    }

</script>
@endpush
@endsection
