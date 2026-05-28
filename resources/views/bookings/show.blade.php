@extends('layouts.app')
@section('title','Booking #'.$booking->booking_reference)
@section('content')
<section class="section">
    <div class="section-inner" style="max-width:800px;margin:0 auto">
        <div style="margin-bottom:1rem"><a href="{{ route('bookings.index') }}" style="color:var(--muted);font-size:.88rem"><i class="fas fa-arrow-left"></i> My Bookings</a></div>
        <div class="card" style="padding:2rem;margin-bottom:1.5rem">
            <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
                <div>
                    <div style="font-size:.85rem;color:var(--muted)">Booking Reference</div>
                    <div style="font-size:1.6rem;font-weight:900;letter-spacing:.05em">{{ $booking->booking_reference }}</div>
                </div>
                <div style="text-align:right">
                    <span class="badge-pill {{ $booking->booking_status=='confirmed'?'badge-success':($booking->booking_status=='cancelled'?'badge-danger':'badge-warning') }}" style="font-size:.82rem;padding:.4rem 1rem">{{ ucfirst($booking->booking_status) }}</span>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
                @foreach([['Package',$booking->package?->title??'N/A'],['Destination',$booking->package?->destination?->name??'N/A'],['Travel Date',$booking->check_in?->format('M d, Y')??'—'],['Travelers',$booking->adults.' adults'.($booking->children?', '.$booking->children.' children':'')],['Total Amount','₹'.number_format($booking->total_amount)],['Payment',ucfirst($booking->payment_status)],['Transaction ID',$booking->transaction_id??'Pending'],['Booked On',$booking->created_at->format('M d, Y H:i')]] as [$l,$v])
                <div><div style="font-size:.78rem;color:var(--muted);margin-bottom:.15rem">{{ $l }}</div><div style="font-weight:600">{{ $v }}</div></div>
                @endforeach
            </div>
            @if($booking->qr_code)
            <div style="margin-top:1.5rem;display:flex;gap:1.5rem;align-items:center;background:var(--surface2);padding:1.5rem;border-radius:15px;border:1px solid var(--border)">
                <img src="{{ $booking->qr_code }}" alt="Booking QR" style="width:120px;height:120px;border:4px solid #fff;border-radius:8px">
                <div>
                    <div style="color:var(--secondary);font-weight:700;margin-bottom:.4rem"><i class="fas fa-qrcode"></i> Digital Travel Pass</div>
                    <p style="font-size:.82rem;color:var(--muted);margin:0">Scan this code at your destination for instant verification and hotel check-in.</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Guide & Assistance Section --}}
        @if($booking->guide_id)
        <div class="card" style="padding:2rem;margin-bottom:1.5rem;background:linear-gradient(135deg,rgba(0,200,83,.05),rgba(108,99,255,.05));border:1px solid rgba(0,200,83,.2)">
            <div style="display:flex;gap:1.5rem;align-items:center;margin-bottom:1.5rem">
                <img src="{{ $booking->guide->avatar_url }}" alt="Guide" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid var(--secondary)">
                <div>
                    <div class="section-tag" style="background:var(--secondary);color:#fff">Local Travel Manager Assigned</div>
                    <div style="font-size:1.2rem;font-weight:800;color:var(--text)">{{ $booking->guide->name }}</div>
                    <div style="font-size:.85rem;color:var(--muted)">Expert in {{ $booking->package?->destination?->name ?? 'local area' }}</div>
                </div>
            </div>

            @if($booking->package_details_shared)
            <div style="background:var(--surface);padding:1.5rem;border-radius:12px;border:1px solid var(--border);margin-bottom:1.5rem">
                <div style="font-weight:700;font-size:1rem;color:var(--secondary);margin-bottom:.5rem"><i class="fas fa-file-invoice"></i> Package & Itinerary Details</div>
                <div style="font-size:.88rem;color:var(--text);white-space:pre-wrap;line-height:1.6">{{ $booking->package_details_shared }}</div>
            </div>
            @endif

            @if($booking->complimentary_addons)
            <div style="margin-bottom:1.5rem">
                <h4 style="font-weight:700;font-size:.9rem;color:var(--muted);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em">Complimentary Add-ons Included</h4>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                    @foreach($booking->complimentary_addons as $addon)
                    <span style="background:rgba(0, 212, 170, 0.1);border:1px solid rgba(0, 212, 170, 0.3);color:#00d4aa;padding:.3rem .8rem;border-radius:50px;font-size:.75rem;font-weight:700"><i class="fas fa-check-circle" style="margin-right:.25rem"></i>{{ $addon }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <h3 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-headset" style="color:var(--secondary)"></i> Human Travel Assistance</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div style="background:var(--surface);padding:1rem;border-radius:12px;border:1px solid var(--border)">
                    <div style="font-weight:600;font-size:.9rem;margin-bottom:.25rem"><i class="fas fa-compass" style="color:var(--secondary)"></i> Navigation & Guidance</div>
                    <div style="font-size:.8rem;color:var(--muted)">Real-time local navigation support and attraction insights.</div>
                </div>
                <div style="background:var(--surface);padding:1rem;border-radius:12px;border:1px solid var(--border)">
                    <div style="font-weight:600;font-size:.9rem;margin-bottom:.25rem"><i class="fas fa-car-side" style="color:var(--secondary)"></i> Transport Coordination</div>
                    <div style="font-size:.8rem;color:var(--muted)">Assistance with Uber, Rapido, and local bus/train logistics.</div>
                </div>
                <div style="background:var(--surface);padding:1rem;border-radius:12px;border:1px solid var(--border)">
                    <div style="font-weight:600;font-size:.9rem;margin-bottom:.25rem"><i class="fas fa-bed" style="color:var(--secondary)"></i> Hotel Support</div>
                    <div style="font-size:.8rem;color:var(--muted)">Coordinating check-ins and special requests with hotel staff.</div>
                </div>
                <div style="background:var(--surface);padding:1rem;border-radius:12px;border:1px solid var(--border)">
                    <div style="font-weight:600;font-size:.9rem;margin-bottom:.25rem"><i class="fas fa-truck-medical" style="color:#ff6b6b"></i> Emergency Assistance</div>
                    <div style="font-size:.8rem;color:var(--muted)">24/7 emergency support and medical coordination if required.</div>
                </div>
            </div>
            <div style="margin-top:1.5rem">
                <a href="{{ route('chatbot.index') }}" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-comments"></i> Chat with {{ explode(' ', $booking->guide->name)[0] }}</a>
            </div>
        </div>
        @else
        <div class="card" style="padding:2rem;margin-bottom:1.5rem;background:rgba(108,99,255,0.02);border:1px dashed rgba(108,99,255,0.3)">
            <div style="text-align:center;margin-bottom:1.5rem">
                <i class="fas fa-user-clock" style="font-size:2.5rem;color:var(--muted);margin-bottom:.75rem"></i>
                <div style="font-weight:800;font-size:1.25rem;color:#fff">Guide Assignment Pending</div>
                <p style="font-size:.85rem;color:var(--muted);max-width:500px;margin:0.5rem auto 0">Our team is assigning a local travel manager. Or, you can **instant-book** a local guide right now!</p>
            </div>
            
            <form method="POST" action="{{ route('bookings.book-guide', $booking) }}" style="background:rgba(255,255,255,0.01);padding:1.5rem;border-radius:12px;border:1px solid var(--border)">
                @csrf
                <h4 style="font-weight:700;margin-bottom:1.25rem;color:#fff;display:flex;align-items:center;gap:0.5rem;"><i class="fas fa-user-plus" style="color:var(--secondary)"></i> Book a Local Guide</h4>
                <div style="margin-bottom:1rem">
                    <label style="font-size:.8rem;color:var(--muted);display:block;margin-bottom:.4rem;font-weight:600">Choose Language Preference</label>
                    <select name="language" class="form-control" style="background:var(--surface2);border-color:var(--border);color:#fff;width:100%;padding:0.6rem;border-radius:8px" required>
                        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Punjabi">Punjabi</option>
                        <option value="Local Dialect">Local Dialect</option>
                    </select>
                </div>
                <div style="margin-bottom:1.25rem">
                    <label style="font-size:.8rem;color:var(--muted);display:block;margin-bottom:.4rem;font-weight:600">Special Details/Interests for the Guide</label>
                    <textarea name="guide_details" class="form-control" rows="3" placeholder="Tell the guide about your travel style (e.g. food interests, budget shopping, historical sights)..." style="background:var(--surface2);border-color:var(--border);color:#fff;width:100%;padding:0.6rem;border-radius:8px" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem"><i class="fas fa-check-circle"></i> Confirm & Book Guide Now</button>
            </form>
        </div>
        @endif

        {{-- Event Log --}}
        @if($booking->event_log)
        <div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
            <h3 style="font-weight:700;margin-bottom:1rem"><i class="fas fa-timeline" style="color:var(--primary)"></i> Event History (Event Sourcing)</h3>
            @foreach(array_reverse($booking->event_log) as $event)
            <div style="display:flex;gap:.75rem;padding:.6rem 0;border-bottom:1px solid var(--border);font-size:.85rem">
                <i class="fas fa-circle" style="color:var(--primary);font-size:.4rem;margin-top:.45rem;flex-shrink:0"></i>
                <div><span style="font-weight:600">{{ str_replace('_',' ',ucfirst($event['event'])) }}</span>
                    <span style="color:var(--muted);margin-left:.5rem;font-size:.78rem">{{ \Carbon\Carbon::parse($event['timestamp'])->format('M d, Y H:i') }}</span></div>
            </div>
            @endforeach
        </div>
        @endif

        @if($booking->booking_status==='confirmed' && !$booking->isCancelled())
        <div class="card" style="padding:1.5rem;border-color:rgba(255,107,107,.3)">
            <h3 style="font-weight:700;color:var(--accent);margin-bottom:1rem"><i class="fas fa-ban"></i> Cancel Booking</h3>
            <form method="POST" action="{{ route('bookings.cancel',$booking) }}">
                @csrf
                <div class="form-group"><textarea name="reason" class="form-control" rows="2" placeholder="Reason for cancellation (min 10 chars)..." required></textarea></div>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</button>
            </form>
        </div>
        @endif
    </div>
</section>
@endsection
