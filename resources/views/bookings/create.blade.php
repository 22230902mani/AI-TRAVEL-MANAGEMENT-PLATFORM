@extends('layouts.app')
@section('title','Book — '.$package->title)
@section('content')
<section class="section">
    <div class="section-inner" style="max-width:800px;margin:0 auto">
        <div style="margin-bottom:1.5rem">
            <a href="{{ route('packages.show',$package) }}" style="color:var(--muted);font-size:.88rem"><i class="fas fa-arrow-left"></i> Back to package</a>
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;margin-bottom:.5rem">Complete Your Booking</h1>
        <p style="color:var(--muted);margin-bottom:2rem">{{ $package->title }} · {{ $package->destination->name }}</p>

        <div style="display:grid;grid-template-columns:3fr 2fr;gap:1.5rem">
            <div class="card" style="padding:2rem">
                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="booking_type" value="package">
                    <div class="form-group">
                        <label class="form-label">Travel Date</label>
                        <input type="date" name="check_in" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="form-group">
                            <label class="form-label">Adults</label>
                            <input type="number" name="adults" class="form-control" min="1" max="20" value="1" id="adults" oninput="calcTotal()">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Children</label>
                            <input type="number" name="children" class="form-control" min="0" max="10" value="0" id="children" oninput="calcTotal()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Promo Code</label>
                        <div style="display:flex;gap:.5rem">
                            <input type="text" name="promo_code" id="promo_code" class="form-control" placeholder="e.g. WELCOME15">
                            <button type="button" onclick="applyPromo()" class="btn btn-outline btn-sm">Apply</button>
                        </div>
                        <div id="promo-msg" style="font-size:.82rem;margin-top:.4rem"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Complimentary Add-ons Included</label>
                        <div style="background:rgba(0, 212, 170, 0.05);border:1px solid rgba(0, 212, 170, 0.2);padding:1rem;border-radius:10px;display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                            <div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:#00d4aa;font-weight:600">
                                <i class="fas fa-check-circle"></i> Free Local Guide
                            </div>
                            <div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:#00d4aa;font-weight:600">
                                <i class="fas fa-check-circle"></i> Custom Trip Plan
                            </div>
                            <div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:#00d4aa;font-weight:600">
                                <i class="fas fa-check-circle"></i> Visa Assistance
                            </div>
                            <div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:#00d4aa;font-weight:600">
                                <i class="fas fa-check-circle"></i> Welcome Kit
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="3" placeholder="Any dietary requirements, accessibility needs..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.85rem;font-size:1rem">
                        <i class="fas fa-ticket"></i> Confirm Booking
                    </button>
                </form>
            </div>

            <div>
                <div class="card" style="padding:1.5rem;position:sticky;top:90px">
                    <h3 style="font-weight:700;margin-bottom:1rem">Order Summary</h3>
                    <img src="{{ $package->destination->image_url }}" style="width:100%;height:120px;object-fit:cover;border-radius:10px;margin-bottom:1rem">
                    <div style="font-weight:600;margin-bottom:.25rem">{{ $package->title }}</div>
                    <div style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">{{ $package->duration_days }} days · {{ $package->destination->name }}</div>
                    <div style="border-top:1px solid var(--border);padding-top:1rem">
                        <div style="display:flex;justify-content:space-between;font-size:.88rem;margin-bottom:.4rem">
                            <span style="color:var(--muted)">Price/person</span><span>₹{{ number_format($package->discounted_price) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.88rem;margin-bottom:.4rem">
                            <span style="color:var(--muted)">Adults</span><span id="sum-adults">1</span>
                        </div>
                        <div id="promo-row" style="display:none;justify-content:space-between;font-size:.88rem;margin-bottom:.4rem;color:var(--secondary)">
                            <span>Discount</span><span id="sum-discount"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.1rem;border-top:1px solid var(--border);padding-top:.75rem;margin-top:.5rem">
                            <span>Total</span><span id="sum-total" style="color:var(--secondary)">₹{{ number_format($package->discounted_price) }}</span>
                        </div>
                    </div>
                    <div style="margin-top:1rem;font-size:.78rem;color:var(--muted);text-align:center">
                        <i class="fas fa-shield-halved" style="color:var(--secondary)"></i> {{ $package->cancellation_policy }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
const price = {{ $package->discounted_price }};
let discountAmt = 0;
function calcTotal() {
    const adults = parseInt(document.getElementById('adults').value)||1;
    const children = parseInt(document.getElementById('children').value)||0;
    document.getElementById('sum-adults').textContent = adults;
    const subtotal = price*adults + price*0.5*children;
    const total = Math.max(0, subtotal - discountAmt);
    document.getElementById('sum-total').textContent = '₹' + total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
}
async function applyPromo() {
    const code = document.getElementById('promo_code').value;
    const adults = parseInt(document.getElementById('adults').value)||1;
    const children = parseInt(document.getElementById('children').value)||0;
    const amount = price*adults + price*0.5*children;
    const res = await fetch('{{ route("bookings.apply_promo") }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({promo_code:code, amount})
    });
    const data = await res.json();
    const msg = document.getElementById('promo-msg');
    if (res.ok) {
        discountAmt = data.discount;
        msg.style.color='var(--secondary)'; msg.textContent = data.message;
        document.getElementById('promo-row').style.display='flex';
        document.getElementById('sum-discount').textContent = '-$'+data.discount;
        calcTotal();
    } else { msg.style.color='var(--accent)'; msg.textContent = data.error; discountAmt=0; }
}
</script>
@endsection
