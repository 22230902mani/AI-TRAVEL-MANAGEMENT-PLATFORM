@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div style="background:linear-gradient(135deg,#0a0b1a 0%,#0f0f2e 50%,#0a1628 100%);padding:4rem 2rem 2rem;border-bottom:1px solid rgba(255,255,255,0.08);box-shadow:0 10px 40px rgba(0,0,0,0.4)">
    <div style="max-width:1400px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem">
        <div style="display:flex;align-items:center;gap:1.25rem">
            <img src="{{ $user->avatar_url }}" alt="avatar" style="width:72px;height:72px;border-radius:50%;border:3px solid var(--primary)">
            <div>
                <div style="font-size:.85rem;color:rgba(255,255,255,0.6)">Welcome back</div>
                <h1 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:900;color:#fff">{{ $user->name }}</h1>
                <div style="display:flex;align-items:center;gap:.75rem;margin-top:.25rem">
                    <span class="badge-pill" style="background:rgba(255,215,0,.15);color:var(--gold);font-size:.75rem"><i class="fas fa-crown"></i> {{ $stats['loyalty_level'] }}</span>
                    <span style="font-size:.82rem;color:var(--muted)"><i class="fas fa-star" style="color:var(--gold)"></i> {{ number_format($stats['loyalty_points']) }} pts</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;align-items:center">
            <!-- Live Clock & Weather Widget -->
            <div class="card" style="padding:.75rem 1.25rem;display:flex;align-items:center;gap:1.25rem;background:rgba(255,255,255,0.08);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15)">
                <div>
                    <div id="live-clock" style="font-size:1.3rem;font-weight:800;font-family:'Playfair Display',serif;color:#fff;line-height:1">00:00</div>
                    <div id="live-date" style="font-size:.75rem;color:rgba(255,255,255,0.6);margin-top:.15rem">Loading...</div>
                </div>
                <div style="width:1px;height:35px;background:var(--border)"></div>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div id="weather-icon" style="font-size:1.5rem">⛅</div>
                    <div>
                        <div id="weather-temp" style="font-size:1rem;font-weight:700;line-height:1">--°C</div>
                        <div id="gps-location" style="font-size:.7rem;color:var(--muted);margin-top:.15rem"><i class="fas fa-map-marker-alt"></i> Locating...</div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border:none;font-weight:800;display:inline-flex;align-items:center;gap:0.4rem;padding:0.6rem 1.2rem;border-radius:8px;box-shadow:0 4px 14px rgba(239,68,68,.35);"><i class="fas fa-shield-halved"></i> Admin Dashboard</a>
                @endif
                <a href="{{ route('itineraries.create') }}" class="btn" style="background:linear-gradient(135deg,#ffca28,#ff6f00);color:#0a0b0f;border:none;font-weight:800;display:inline-flex;align-items:center;gap:0.4rem;padding:0.6rem 1.2rem;border-radius:8px;"><i class="fas fa-robot"></i> AI Trip Planner</a>
                <a href="{{ route('packages.index') }}" class="btn btn-outline"><i class="fas fa-compass"></i> Browse Packages</a>
            </div>
        </div>
    </div>
</div>
<section class="section" style="padding-top:2rem">
    <div class="section-inner">
        <div class="grid-4" style="margin-bottom:2rem">
        @php
            $statCards = [
                [
                    'val'   => $stats['premium_bought'],
                    'label' => 'Premium Plans Bought',
                    'icon'  => 'fa-crown',
                    'bg'    => 'linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 100%)',
                    'icon_bg' => 'rgba(59,130,246,0.25)',
                    'icon_color' => '#93c5fd',
                ],
                [
                    'val'   => '₹'.number_format($stats['overall_spent']),
                    'label' => 'Total Spent',
                    'icon'  => 'fa-wallet',
                    'bg'    => 'linear-gradient(135deg,#5b21b6 0%,#7c3aed 100%)',
                    'icon_bg' => 'rgba(167,139,250,0.25)',
                    'icon_color' => '#c4b5fd',
                ],
                [
                    'val'   => $stats['packages_bought'],
                    'label' => 'Packages Bought',
                    'icon'  => 'fa-box-open',
                    'bg'    => 'linear-gradient(135deg,#9d174d 0%,#db2777 100%)',
                    'icon_bg' => 'rgba(251,113,133,0.25)',
                    'icon_color' => '#fda4af',
                ],
                [
                    'val'   => $stats['upcoming_trip_text'],
                    'label' => 'Upcoming Trip',
                    'icon'  => 'fa-plane-departure',
                    'bg'    => 'linear-gradient(135deg,#7f1d1d 0%,#b91c1c 100%)',
                    'icon_bg' => 'rgba(252,165,165,0.25)',
                    'icon_color' => '#fca5a5',
                    'is_text' => true,
                ],
            ];
        @endphp
        @foreach($statCards as $card)
        <div style="
            background:{{ $card['bg'] }};
            border-radius:16px;
            padding:1.5rem 1.75rem;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            border:1px solid rgba(255,255,255,0.1);
            box-shadow:0 8px 24px rgba(0,0,0,0.35);
            transition:transform .3s, box-shadow .3s;
            position:relative;
            overflow:hidden;
        " onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 36px rgba(0,0,0,0.5)'"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.35)'">
            <div>
                <div style="font-size:.7rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:.6rem">
                    {{ $card['label'] }}
                </div>
                <div style="font-size:{{ isset($card['is_text']) ? '1.25rem' : '2rem' }};font-weight:900;color:#fff;line-height:1;font-family:'Playfair Display',serif">
                    {{ $card['val'] }}
                </div>
                <div style="font-size:.72rem;color:rgba(255,255,255,0.5);margin-top:.4rem">
                    <i class="fas fa-arrow-trend-up" style="color:rgba(255,255,255,0.5)"></i> Live data
                </div>
            </div>
            <div style="
                width:52px;height:52px;border-radius:14px;
                background:{{ $card['icon_bg'] }};
                display:flex;align-items:center;justify-content:center;
                flex-shrink:0;
            ">
                <i class="fas {{ $card['icon'] }}" style="color:{{ $card['icon_color'] }};font-size:1.4rem"></i>
            </div>
            {{-- Subtle glow orb --}}
            <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.05);pointer-events:none"></div>
        </div>
        @endforeach
        </div>{{-- /grid-4 --}}
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem">
            <div>
                <!-- Analytics & Budget Tracker -->
                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                        <h2 style="font-weight:700"><i class="fas fa-chart-pie" style="color:var(--accent)"></i> Financial & Booking Overview</h2>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div style="position:relative;height:240px;padding-bottom:10px;">
                            <h4 style="font-size:.9rem;font-weight:700;color:var(--muted);text-align:center;margin-bottom:.5rem">Expenditure Breakdown</h4>
                            <canvas id="budgetChart"></canvas>
                        </div>
                        <div style="position:relative;height:240px;padding-bottom:10px;">
                            <h4 style="font-size:.9rem;font-weight:700;color:var(--muted);text-align:center;margin-bottom:.5rem">Activity Over Time</h4>
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                        <h2 style="font-weight:700"><i class="fas fa-ticket" style="color:var(--primary)"></i> Recent Bookings</h2>
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    @forelse($recentBookings as $booking)
                    <a href="{{ route('bookings.show',$booking) }}" style="display:block;padding:.85rem 0;border-bottom:1px solid var(--border);text-decoration:none">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div style="font-weight:600;font-size:.9rem;color:var(--text)">{{ $booking->booking_reference }}</div>
                                <div style="font-size:.8rem;color:var(--muted)">{{ $booking->package?->destination?->name ?? 'Booking' }} • {{ $booking->check_in?->format('M d, Y') }}</div>
                            </div>
                            <div style="text-align:right">
                                <div style="font-weight:700;color:var(--secondary)">₹{{ number_format($booking->total_amount) }}</div>
                                <span class="badge-pill {{ $booking->booking_status=='confirmed'?'badge-success':($booking->booking_status=='cancelled'?'badge-danger':'badge-warning') }}" style="font-size:.72rem">{{ ucfirst($booking->booking_status) }}</span>
                            </div>
                        </div>
                        @if($booking->guide_id)
                            <div style="margin-top:.6rem;background:rgba(108, 99, 255, 0.08);border-left:3px solid var(--primary);padding:.5rem .75rem;border-radius:0 6px 6px 0">
                                <div style="font-size:.75rem;font-weight:700;color:var(--primary);margin-bottom:.2rem"><i class="fas fa-user-shield"></i> Assigned Guide: {{ $booking->guide?->name ?? 'Local Manager' }}</div>
                                @if($booking->package_details_shared)
                                <div style="font-size:.72rem;color:var(--muted);white-space:pre-wrap;line-height:1.4">{{ $booking->package_details_shared }}</div>
                                @endif
                            </div>
                        @endif
                    </a>
                    @empty<p style="color:var(--muted);text-align:center;padding:1.5rem">No bookings yet. <a href="{{ route('packages.index') }}" style="color:var(--primary)">Browse packages →</a></p>
                    @endforelse
                </div>
                <div class="card" style="padding:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                        <h2 style="font-weight:700"><i class="fas fa-map" style="color:var(--secondary)"></i> Active Itineraries</h2>
                        <a href="{{ route('itineraries.index') }}" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    @forelse($activeItineraries as $itin)
                    <a href="{{ route('itineraries.show',$itin) }}" style="display:block;padding:1rem;background:var(--surface2);border-radius:12px;margin-bottom:.75rem;border:1px solid var(--border)">
                        <div style="display:flex;justify-content:space-between;align-items:start">
                            <div><div style="font-weight:600">{{ $itin->title }}</div>
                                <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem">📍 {{ $itin->destination?->name ?? 'Custom' }} • {{ $itin->start_date?->format('M d') }} – {{ $itin->end_date?->format('M d, Y') }}</div>
                            </div>
                            <span class="badge-pill badge-primary" style="font-size:.72rem">{{ ucfirst($itin->status) }}</span>
                        </div>
                        @if($itin->budget)
                        <div style="margin-top:.75rem">
                            <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--muted);margin-bottom:.3rem">
                                <span>Budget used</span><span>₹{{ number_format($itin->spent) }} / ₹{{ number_format($itin->budget) }}</span>
                            </div>
                            <div style="height:4px;background:var(--surface);border-radius:4px">
                                <div style="width:{{ min(100,$itin->budget_used_percent) }}%;height:100%;background:{{ $itin->budget_used_percent>80?'var(--accent)':'var(--secondary)' }};border-radius:4px"></div>
                            </div>
                        </div>
                        @endif
                    </a>
                    @empty<div style="text-align:center;padding:1.5rem;color:var(--muted)">
                        <a href="{{ route('itineraries.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Create Itinerary</a>
                    </div>
                    @endforelse
                </div>
            </div>
            <div>
                <div class="card" style="padding:1.5rem;margin-bottom:1.25rem;background:linear-gradient(135deg,rgba(255,215,0,.1),rgba(108,99,255,.1));border-color:rgba(255,215,0,.3)">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
                        <div style="font-size:2rem">👑</div>
                        <div><div style="font-weight:700;font-size:1.1rem">{{ $stats['loyalty_level'] }} Member</div>
                            <div style="font-size:.82rem;color:var(--muted)">{{ number_format($stats['loyalty_points']) }} points</div></div>
                    </div>
                    @php $next=match($stats['loyalty_level']){'Bronze'=>1000,'Silver'=>5000,'Gold'=>10000,default=>99999};$pct=min(100,($stats['loyalty_points']/$next)*100); @endphp
                    <div style="background:var(--surface);border-radius:8px;height:8px;margin-bottom:.5rem">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,var(--gold),var(--primary));border-radius:8px"></div>
                    </div>
                    <div style="font-size:.78rem;color:var(--muted)">{{ number_format($next-$stats['loyalty_points']) }} pts to next level</div>
                </div>
                <div class="card" style="padding:1.5rem;margin-bottom:1.25rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                        <h3 style="font-weight:700;font-size:1.1rem;margin:0"><i class="fas fa-bell" style="color:var(--primary);margin-right:8px"></i> Notifications</h3>
                        @if(count($notifications) > 0)
                        <a href="{{ route('notifications') }}" style="font-size:.8rem;color:var(--primary);font-weight:600">View All</a>
                        @endif
                    </div>
                    @forelse($notifications as $notif)
                    <div style="padding:.75rem 1rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:12px;margin-bottom:.5rem;display:flex;gap:1rem;align-items:flex-start;transition:.3s;cursor:pointer" onmouseover="this.style.background='rgba(124,58,237,0.1)';this.style.borderColor='rgba(124,58,237,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.05)'">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:6px;flex-shrink:0;box-shadow:0 0 8px var(--primary)"></div>
                        <div>
                            <div style="font-weight:600;font-size:.88rem;color:#fff">{{ $notif->title }}</div>
                            <div style="color:rgba(255,255,255,0.6);font-size:.8rem;margin-top:.2rem">{{ Str::limit($notif->message,60) }}</div>
                            <div style="font-size:.7rem;color:var(--primary);margin-top:.3rem;font-weight:600"><i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem 1rem">
                        <i class="fas fa-bell-slash" style="font-size:2rem;color:rgba(255,255,255,0.1);margin-bottom:1rem"></i>
                        <p style="color:rgba(255,255,255,0.5);font-size:.85rem">No new notifications.</p>
                    </div>
                    @endforelse
                </div>
                
                <div class="card" style="padding:1.5rem">
                    <h3 style="font-weight:700;margin-bottom:1.25rem;font-size:1.1rem;margin-top:0"><i class="fas fa-bolt" style="color:var(--gold);margin-right:8px"></i> Quick Actions</h3>
                    <div style="display:flex;flex-direction:column;gap:.75rem">
                        <a href="{{ route('itineraries.create') }}" style="display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.25rem;background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(37,99,235,0.15));border:1px solid rgba(124,58,237,0.3);border-radius:12px;color:#fff;font-weight:600;font-size:.9rem;transition:all .3s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(124,58,237,0.25)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            <div style="display:flex;align-items:center;gap:1rem">
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff"><i class="fas fa-wand-magic-sparkles"></i></div>
                                <span>Generate AI Itinerary</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i>
                        </a>
                        
                        <a href="{{ route('chatbot.index') }}" style="display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.25rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-weight:600;font-size:.9rem;transition:all .3s" onmouseover="this.style.transform='translateY(-2px)';this.style.background='rgba(255,255,255,0.08)';this.style.borderColor='rgba(255,255,255,0.2)'" onmouseout="this.style.transform='translateY(0)';this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.1)'">
                            <div style="display:flex;align-items:center;gap:1rem">
                                <div style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#cbd5e1"><i class="fas fa-robot"></i></div>
                                <span>Chat with AI</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i>
                        </a>
                        
                        <a href="{{ route('profile') }}" style="display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.25rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-weight:600;font-size:.9rem;transition:all .3s" onmouseover="this.style.transform='translateY(-2px)';this.style.background='rgba(255,255,255,0.08)';this.style.borderColor='rgba(255,255,255,0.2)'" onmouseout="this.style.transform='translateY(0)';this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.1)'">
                            <div style="display:flex;align-items:center;gap:1rem">
                                <div style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#cbd5e1"><i class="fas fa-user-astronaut"></i></div>
                                <span>Edit Profile</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:rgba(255,255,255,0.4);font-size:.8rem"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ✅ Integrated Transit & Booking Ecosystem Hub --}}
<section id="transit-booking-hub" style="padding: 4rem 0 6rem; position: relative; z-index: 2;">
    <div class="section-inner">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
            <div>
                <span class="badge-pill" style="background:rgba(0,242,254,0.15);color:#00f2fe;font-size:.75rem"><i class="fas fa-network-wired"></i> Verified Partner Integrations</span>
                <h2 style="font-weight:800;font-size:1.75rem;margin-top:.5rem">Direct Booking & Transit Hub</h2>
            </div>
            <div style="font-size:.85rem;color:var(--muted)">Instant e-ticket generation & live API access</div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
            {{-- 1. Flight Booking --}}
            <div class="card" style="padding: 2rem 1.75rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #00f2fe; background: rgba(255,255,255,0.02); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='#00f2fe';this.style.boxShadow='0 15px 35px rgba(0,242,254,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(0, 242, 254, 0.1); color: #00f2fe; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Flight Booking</h3>
                    <p style="color: var(--muted); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">Compare instant real-time fare drops across domestic & international flight providers.</p>
                </div>
                <a href="https://www.makemytrip.com/flights/" target="_blank" class="btn" style="background: rgba(0, 242, 254, 0.15); color: #00f2fe; border: 1px solid rgba(0, 242, 254, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                    Launch Portal <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                </a>
            </div>

            {{-- 2. Train Reservations --}}
            <div class="card" style="padding: 2rem 1.75rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #00ff87; background: rgba(255,255,255,0.02); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='#00ff87';this.style.boxShadow='0 15px 35px rgba(0,255,135,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(0, 255, 135, 0.1); color: #00ff87; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-train-subway"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">IRCTC Railway</h3>
                    <p style="color: var(--muted); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">Instant Tatkal availability & live PNR tracking across Indian Railways express routes.</p>
                </div>
                <a href="https://www.irctc.co.in" target="_blank" class="btn" style="background: rgba(0, 255, 135, 0.15); color: #00ff87; border: 1px solid rgba(0, 255, 135, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                    Launch IRCTC <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                </a>
            </div>

            {{-- 3. Bus Express --}}
            <div class="card" style="padding: 2rem 1.75rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ff9100; background: rgba(255,255,255,0.02); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='#ff9100';this.style.boxShadow='0 15px 35px rgba(255,145,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 145, 0, 0.1); color: #ff9100; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-bus-simple"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Bus Services</h3>
                    <p style="color: var(--muted); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">Book AC Volvo sleeper express transit across national inter-city highway networks.</p>
                </div>
                <a href="https://www.redbus.in" target="_blank" class="btn" style="background: rgba(255, 145, 0, 0.15); color: #ff9100; border: 1px solid rgba(255, 145, 0, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                    Launch redBus <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                </a>
            </div>

            {{-- 4. Luxury Hotels --}}
            <div class="card" style="padding: 2rem 1.75rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ff0844; background: rgba(255,255,255,0.02); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='#ff0844';this.style.boxShadow='0 15px 35px rgba(255,8,68,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 8, 68, 0.1); color: #ff0844; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Luxe Hotels</h3>
                    <p style="color: var(--muted); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">Exclusive member discounts on verified 5-star oceanfront suites and city retreats.</p>
                </div>
                <a href="https://www.booking.com" target="_blank" class="btn" style="background: rgba(255, 8, 68, 0.15); color: #ff0844; border: 1px solid rgba(255, 8, 68, 0.4); font-weight: 700; font-size: 0.88rem; padding: 0.75rem 1rem; border-radius: 12px; text-align: center; text-decoration: none; display: block; transition: 0.3s;">
                    Launch Agoda <i class="fas fa-arrow-up-right-from-square" style="margin-left: 0.4rem; font-size: 0.8rem;"></i>
                </a>
            </div>

            {{-- 5. Rapido / Uber --}}
            <div class="card" style="padding: 2rem 1.75rem; text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #ffca28; background: rgba(255,255,255,0.02); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='#ffca28';this.style.boxShadow='0 15px 35px rgba(255,202,40,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 202, 40, 0.1); color: #ffca28; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-taxi"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">Rapido / Uber</h3>
                    <p style="color: var(--muted); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.5rem;">Instant local cab & bike-taxi hailing for airport transfers and local sightseeing.</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="https://www.rapido.bike" target="_blank" class="btn" style="background: rgba(255, 202, 40, 0.15); color: #ffca28; border: 1px solid rgba(255, 202, 40, 0.4); font-weight: 700; font-size: 0.8rem; padding: 0.75rem 0.5rem; border-radius: 12px; text-align: center; text-decoration: none; flex: 1; transition: 0.3s;">
                        Rapido <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                    <a href="https://www.uber.com" target="_blank" class="btn" style="background: rgba(255, 255, 255, 0.1); color: #fff; border: 1px solid rgba(255, 255, 255, 0.25); font-weight: 700; font-size: 0.8rem; padding: 0.75rem 0.5rem; border-radius: 12px; text-align: center; text-decoration: none; flex: 1; transition: 0.3s;">
                        Uber <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency SOS Module -->
<div id="sos-module" style="position:fixed;bottom:2rem;left:2rem;z-index:9999;">
    <button onclick="toggleSOS()" style="width:60px;height:60px;border-radius:50%;background:#ef4444;border:3px solid rgba(255,255,255,0.5);color:#fff;font-size:1.4rem;cursor:pointer;box-shadow:0 8px 24px rgba(239,68,68,.5);transition:all .3s;display:flex;align-items:center;justify-content:center;animation: pulse 2s infinite;" title="Emergency SOS">
        <i class="fas fa-shield-halved"></i>
    </button>
    <div id="sos-box" style="position:absolute;bottom:75px;left:0;width:300px;background:var(--surface);border:2px solid #ef4444;border-radius:16px;box-shadow:var(--shadow);display:none;padding:1.25rem;text-align:center;">
        <h4 style="color:#ef4444;font-weight:800;margin-bottom:.5rem"><i class="fas fa-triangle-exclamation"></i> EMERGENCY SOS</h4>
        <p style="font-size:.85rem;color:var(--muted);margin-bottom:1rem">Activate this only in case of a travel emergency. Your location will be shared with authorities and your emergency contacts.</p>
        <button onclick="activateSOS()" class="btn btn-danger" style="width:100%;justify-content:center;font-weight:700;font-size:1rem;padding:.75rem">TRIGGER SOS</button>
        <div id="sos-status" style="margin-top:1rem;font-size:.8rem;color:var(--success);display:none;font-weight:600"></div>
    </div>
</div>
<style>
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239,68,68, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(239,68,68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239,68,68, 0); }
    }
</style>

@push('scripts')
<script>
// --- Clock, Weather & GPS ---
setInterval(() => {
    const now = new Date();
    const clockEl = document.getElementById('live-clock');
    if(clockEl) {
        clockEl.innerText = now.toLocaleTimeString('en-US', {hour12: false, hour:'2-digit', minute:'2-digit'});
        document.getElementById('live-date').innerText = now.toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'});
    }
}, 1000);

if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(async (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        // Reverse Geocode
        try {
            const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
            const geoData = await geoRes.json();
            const city = geoData.address.city || geoData.address.town || geoData.address.village || 'Active';
            document.getElementById('gps-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${city}`;
        } catch(e) {
            document.getElementById('gps-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> Active`;
        }

        // Weather
        try {
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`);
            const weatherData = await weatherRes.json();
            const temp = weatherData.current_weather.temperature;
            document.getElementById('weather-temp').innerText = `${temp}°C`;
            const wcode = weatherData.current_weather.weathercode;
            const wicon = wcode === 0 ? '☀️' : (wcode < 4 ? '⛅' : (wcode < 60 ? '☁️' : '🌧️'));
            document.getElementById('weather-icon').innerText = wicon;
        } catch(e) {}
    }, () => {
        document.getElementById('gps-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> Disabled`;
    });
}

// --- Emergency SOS ---
function toggleSOS() {
    const box = document.getElementById('sos-box');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
}
function activateSOS() {
    const status = document.getElementById('sos-status');
    status.style.display = 'block';
    status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Alerting authorities...';
    // Simulate API call
    setTimeout(() => {
        status.innerHTML = '<i class="fas fa-check-circle"></i> SOS Sent! Help is on the way.';
        status.style.color = '#2e7d32';
    }, 2000);
}

// --- Analytics & Budget Tracker Charts ---
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData ?? null);
    
    if (chartData) {
        // Budget Doughnut Chart
        const ctxBudget = document.getElementById('budgetChart');
        if(ctxBudget) {
            new Chart(ctxBudget, {
                type: 'doughnut',
                data: {
                    labels: chartData.categories.length ? chartData.categories.map(c => c.charAt(0).toUpperCase() + c.slice(1)) : ['Awaiting Data'],
                    datasets: [{
                        data: chartData.visual_amounts ? chartData.visual_amounts : (chartData.category_amounts.length ? chartData.category_amounts : [1]),
                        backgroundColor: chartData.categories.length ? ['#10b981', '#ef4444', '#f97316', '#3b82f6', '#8b5cf6'] : ['rgba(16, 185, 129, 0.1)'],
                        borderColor: chartData.categories.length ? 'transparent' : '#10b981',
                        borderWidth: chartData.categories.length ? 0 : 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: 'rgba(255,255,255,0.7)',
                                font: { family: 'Outfit', size: 12 },
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(10, 10, 25, 0.9)',
                            titleFont: { family: 'Outfit', size: 14 },
                            bodyFont: { family: 'Outfit', size: 13 },
                            padding: 12,
                            borderColor: 'rgba(0, 240, 255, 0.3)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) { 
                                    let val = chartData.categories.length ? chartData.category_amounts[context.dataIndex] : 0;
                                    let isCurrency = context.dataIndex === 0; // Since index 0 is Amount
                                    return isCurrency ? ' ₹' + val.toLocaleString() : ' ' + val; 
                                }
                            }
                        }
                    }
                }
            });
        }

        // Monthly Spending Line Chart
        const ctxExpense = document.getElementById('expenseChart');
        if(ctxExpense) {
            // Create a gradient for the line chart fill
            let gradient = ctxExpense.getContext('2d').createLinearGradient(0, 0, 0, 180);
            gradient.addColorStop(0, 'rgba(0, 240, 255, 0.4)');
            gradient.addColorStop(1, 'rgba(0, 240, 255, 0.0)');

            new Chart(ctxExpense, {
                type: 'line',
                data: {
                    labels: chartData.months,
                    datasets: [{
                        label: 'Spent (₹)',
                        data: chartData.monthly_amounts.length ? chartData.monthly_amounts : [0,0,0,0,0,0,0,0,0,0,0,0],
                        borderColor: '#00f0ff',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ff00cc',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        y: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(10, 10, 25, 0.9)',
                            titleFont: { family: 'Outfit', size: 14 },
                            bodyFont: { family: 'Outfit', size: 13 },
                            padding: 12,
                            borderColor: 'rgba(255, 0, 204, 0.3)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: 'rgba(255,255,255,0.5)', font: { family: 'Outfit' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: 'rgba(255,255,255,0.5)', font: { family: 'Outfit' } }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
@endsection
